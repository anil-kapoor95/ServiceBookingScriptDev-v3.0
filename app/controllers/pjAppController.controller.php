<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAppController extends pjController
{
	public $models = array();

	/**
	 * Recursively trim leading/trailing whitespace from every string in a value (e.g. $_POST).
	 */
	public static function trimDeep($value)
	{
		if (is_array($value))
		{
			foreach ($value as $k => $v)
			{
				$value[$k] = self::trimDeep($v);
			}
			return $value;
		}
		return is_string($value) ? trim($value) : $value;
	}

	/**
	 * Lenient phone check: an optional leading +, then digits, spaces, ( ) . - ; 5 to 15 digits in total.
	 * An empty value is "valid" here (use the required rule separately).
	 */
	public static function isValidPhone($phone)
	{
		$phone = trim((string) $phone);
		if ($phone === '')
		{
			return true;
		}
		$digits = preg_replace('/\D/', '', $phone);
		return (bool) preg_match('/^\+?[0-9()\s.\-]+$/', $phone) && strlen($digits) >= 5 && strlen($digits) <= 15;
	}

	/**
	 * Validate the client-details part of a booking/checkout POST against the "Booking form" options
	 * (o_bf_include_* : 1 = hidden, 2 = optional, 3 = required). Returns true when everything is fine.
	 */
	public static function validateClientDetails($post, $option_arr)
	{
		$fields = array('title', 'name', 'email', 'phone', 'notes', 'company', 'address', 'city', 'state', 'zip', 'country');
		foreach ($fields as $f)
		{
			$value = isset($post['c_' . $f]) ? trim((string) $post['c_' . $f]) : '';
			if (isset($option_arr['o_bf_include_' . $f]) && (int) $option_arr['o_bf_include_' . $f] === 3 && $value === '')
			{
				return false;
			}
		}
		if (isset($post['c_email']) && trim($post['c_email']) !== '' && !filter_var(trim($post['c_email']), FILTER_VALIDATE_EMAIL))
		{
			return false;
		}
		if (isset($post['c_phone']) && !self::isValidPhone($post['c_phone']))
		{
			return false;
		}
		return true;
	}

	/**
	 * Generate (or return existing) CSRF token for the current session.
	 */
	public static function getCsrfToken()
	{
		if (empty($_SESSION['csrf_token']))
		{
			$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
		}
		return $_SESSION['csrf_token'];
	}

	/**
	 * Validate CSRF token (admin POST; POST field or X-CSRF-Token header; no URL slug).
	 */
	public static function validateCsrfToken()
	{
		$token = '';
		if (!empty($_POST['csrf_token'])) {
			$token = $_POST['csrf_token'];
		} elseif (!empty($_SERVER['HTTP_X_CSRF_TOKEN'])) {
			$token = $_SERVER['HTTP_X_CSRF_TOKEN'];
		}
		if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], (string) $token))
		{
			header('HTTP/1.1 403 Forbidden');
			exit('Invalid CSRF token.');
		}
	}
	
	public $option_arr = array();
	
	public $defaultLocale = 'admin_locale_id';
  
	public $defaultFields = 'fields';
	
	public $defaultFieldsIndex = 'fields_index';
  
	protected function loadSetFields($force=FALSE, $locale_id=NULL, $fields=NULL)
	{
		if (is_null($locale_id))
		{
			$locale_id = $this->getLocaleId();
		}
		
		if (is_null($fields))
		{
			$fields = $this->defaultFields;
		}
		
		$registry = pjRegistry::getInstance();
		if ($force
				|| !isset($_SESSION[$this->defaultFieldsIndex])
				|| $_SESSION[$this->defaultFieldsIndex] != $this->option_arr['o_fields_index']
				|| !isset($_SESSION[$fields])
				|| empty($_SESSION[$fields]))
		{
			pjAppController::setFields($locale_id);
	
			# Update session
			if ($registry->is('fields'))
			{
				$_SESSION[$fields] = $registry->get('fields');
			}
			$_SESSION[$this->defaultFieldsIndex] = $this->option_arr['o_fields_index'];
		}
	
		if (isset($_SESSION[$fields]) && !empty($_SESSION[$fields]))
		{
			# Load fields from session
			$registry->set('fields', $_SESSION[$fields]);
		}
		
		return TRUE;
	}
	
	public function isCountryReady()
    {
    	return $this->isAdmin();
    }
    
	public function isOneAdminReady()
    {
    	return $this->isAdmin();
    }
	
	public static function setTimezone($timezone="UTC")
    {
    	if (in_array(version_compare(phpversion(), '5.1.0'), array(0,1)))
		{
			date_default_timezone_set($timezone);
		} else {
			$safe_mode = ini_get('safe_mode');
			if ($safe_mode)
			{
				putenv("TZ=".$timezone);
			}
		}
    }

	public static function setMySQLServerTime($offset="-0:00")
    {
		pjAppModel::factory()->prepare("SET SESSION time_zone = :offset;")->exec(compact('offset'));
    }
    
	public function setTime()
	{
		if (isset($this->option_arr['o_timezone']))
		{
			$offset = $this->option_arr['o_timezone'] / 3600;
			if ($offset > 0)
			{
				$offset = "-".$offset;
			} elseif ($offset < 0) {
				$offset = "+".abs($offset);
			} elseif ($offset === 0) {
				$offset = "+0";
			}
	
			pjAppController::setTimezone('Etc/GMT' . $offset);
			if (strpos($offset, '-') !== false)
			{
				$offset = str_replace('-', '+', $offset);
			} elseif (strpos($offset, '+') !== false) {
				$offset = str_replace('+', '-', $offset);
			}
			pjAppController::setMySQLServerTime($offset . ":00");
		}
	}
    
    public function beforeFilter()
    {
		if (strtoupper(@$_SERVER['REQUEST_METHOD']) === 'POST')
		{
			$_csrf_ctrl = isset($_GET['controller']) ? $_GET['controller'] : '';
			$_csrf_act = isset($_GET['action']) ? $_GET['action'] : '';
			if (strpos($_csrf_ctrl, 'pjAdmin') === 0
				&& !in_array($_csrf_act, array('pjActionLogin', 'pjActionForgot', 'pjActionLogout', 'pjActionPreview', 'pjActionExportFeed'), true)) {
				pjAppController::validateCsrfToken();
			}
		}

        $this->appendJs('jquery.min.js', PJ_THIRD_PARTY_PATH . 'jquery/');
        $baseDir = defined("PJ_INSTALL_PATH") ? PJ_INSTALL_PATH : null;
        $dm = new pjDependencyManager($baseDir, PJ_THIRD_PARTY_PATH);
        $dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
		$this->appendJs('jquery-migrate.min.js', $dm->getPath('jquery_migrate'), FALSE, FALSE);
		$this->appendJs('pjAdminCore.js');
		$this->appendCss('reset.css');
		 
		$this->appendJs('js/jquery-ui.custom.min.js', PJ_THIRD_PARTY_PATH . 'jquery_ui/');
		$this->appendCss('css/smoothness/jquery-ui.min.css', PJ_THIRD_PARTY_PATH . 'jquery_ui/');
				
		$this->appendCss('pj-all.css', PJ_FRAMEWORK_LIBS_PATH . 'pj/css/');
		$this->appendCss('admin.css');
		
    	if ($_GET['controller'] != 'pjInstaller')
		{
			$this->models['Option'] = pjOptionModel::factory();
			$this->option_arr = $this->models['Option']->getPairs($this->getForeignId());
			$this->set('option_arr', $this->option_arr);
			$this->setTime();
			
			if (!isset($_SESSION[$this->defaultLocale]))
			{
				$locale_arr = pjLocaleModel::factory()->where('is_default', 1)->limit(1)->findAll()->getData();
				if (count($locale_arr) === 1)
				{
					$this->setLocaleId($locale_arr[0]['id']);
				}
			}
			$this->loadSetFields();
		}
    }
    
    public function isEditor()
    {
    	return $this->getRoleId() == 2;
    }
    
    public function getForeignId()
    {
    	return 1;
    }
    
    public static function setFields($locale)
    {
    if(isset($_SESSION['lang_show_id']) && (int) $_SESSION['lang_show_id'] == 1)
		{
			$fields = pjMultiLangModel::factory()
				->select('CONCAT(t1.content, CONCAT(":", t2.id, ":")) AS content, t2.key')
				->join('pjField', "t2.id=t1.foreign_id", 'inner')
				->where('t1.locale', $locale)
				->where('t1.model', 'pjField')
				->where('t1.field', 'title')
				->findAll()
				->getDataPair('key', 'content');
		}else{
			$fields = pjMultiLangModel::factory()
				->select('t1.content, t2.key')
				->join('pjField', "t2.id=t1.foreign_id", 'inner')
				->where('t1.locale', $locale)
				->where('t1.model', 'pjField')
				->where('t1.field', 'title')
				->findAll()
				->getDataPair('key', 'content');
		}
		$registry = pjRegistry::getInstance();
		$tmp = array();
		if ($registry->is('fields'))
		{
			$tmp = $registry->get('fields');
		}
		$arrays = array();
		foreach ($fields as $key => $value)
		{
			if (strpos($key, '_ARRAY_') !== false)
			{
				list($prefix, $suffix) = explode("_ARRAY_", $key);
				if (!isset($arrays[$prefix]))
				{
					$arrays[$prefix] = array();
				}
				$arrays[$prefix][$suffix] = $value;
			}
		}
		require PJ_CONFIG_PATH . 'settings.inc.php';
		$fields = array_merge($tmp, $fields, $settings, $arrays);
		$registry->set('fields', $fields);
    }

    public static function jsonDecode($str)
	{
		$Services_JSON = new pjServices_JSON();
		return $Services_JSON->decode($str);
	}
	
	public static function jsonEncode($arr)
	{
		$Services_JSON = new pjServices_JSON();
		return $Services_JSON->encode($arr);
	}
	
	public static function jsonResponse($arr)
	{
		header("Content-Type: application/json; charset=utf-8");
		echo pjAppController::jsonEncode($arr);
		exit;
	}

	public function getLocaleId()
	{
		return isset($_SESSION[$this->defaultLocale]) && (int) $_SESSION[$this->defaultLocale] > 0 ? (int) $_SESSION[$this->defaultLocale] : false;
	}
	
	public function setLocaleId($locale_id)
	{
		$_SESSION[$this->defaultLocale] = (int) $locale_id;
	}
	
	public function pjActionCheckInstall()
	{
		$this->setLayout('pjActionEmpty');
		
		$result = array('status' => 'OK', 'code' => 200, 'text' => 'Operation succeeded', 'info' => array());
		$folders = array(
							'app/web/upload'
						);
		foreach ($folders as $dir)
		{
			if (!is_writable($dir))
			{
				$result['status'] = 'ERR';
				$result['code'] = 101;
				$result['text'] = 'Permission requirement';
				$result['info'][] = sprintf('Folder \'<span class="bold">%1$s</span>\' is not writable. You need to set write permissions (chmod 777) to directory located at \'<span class="bold">%1$s</span>\'', $dir);
			}
		}
		
		return $result;
	}
	
	public function friendlyURL($str, $divider='-')
	{
		$str = mb_strtolower($str, mb_detect_encoding($str));
		$str = trim($str);
		$str = preg_replace('/[_|\s]+/', $divider, $str);
		$str = preg_replace('/\x{00C5}/u', 'AA', $str);
		$str = preg_replace('/\x{00C6}/u', 'AE', $str);
		$str = preg_replace('/\x{00D8}/u', 'OE', $str);
		$str = preg_replace('/\x{00E5}/u', 'aa', $str);
		$str = preg_replace('/\x{00E6}/u', 'ae', $str);
		$str = preg_replace('/\x{00F8}/u', 'oe', $str);
		$str = preg_replace('/[^a-z\x{0400}-\x{04FF}0-9-]+/u', '', $str);
		$str = preg_replace('/[-]+/', $divider, $str);
		$str = preg_replace('/^-+|-+$/', '', $str);
		return $str;
	}
	
	public static function getTokens($booking_id, $option_arr, $salt, $locale_id)
	{
		$country = NULL;
		$date_time = NULL;
		$duration = NULL;
		$services = NULL;
	
		$data = pjBookingModel::factory()->find($booking_id)->getData();
	
		$personal_titles = __('personal_titles', true, false);
		$payment_methods = __('payment_methods', true, false);
	
		$title = $personal_titles[$data['c_title']];
		$payment_method = !empty($data['payment_method']) ? $payment_methods[$data['payment_method']]: NULL;
	
		$date_time = date($option_arr['o_date_format'], strtotime($data['start_dt'])) . ', ' . date($option_arr['o_time_format'], strtotime($data['start_dt']));
		
		$temp_arr = pjUtil::convertToHoursMins((int) $data['duration']);
		$duration_arr = array();
		if((int) $temp_arr['hours'] > 0)
		{
			$duration_arr[] = $temp_arr['hours']. ' ' . ($temp_arr['hours'] != 1 ? __('front_hours', true) : __('front_hour', true));
		}
		if((int) $temp_arr['minutes'] > 0)
		{
			$duration_arr[] = $temp_arr['minutes'] . ' '. ($temp_arr['minutes'] != 1 ? __('front_minutes', true) : __('front_minute', true));
		}
		$duration = join(' ', $duration_arr);
		
		$temp_service_arr = pjBookingServiceModel::factory()
			->select('t1.*, t2.content as title, t3.price, t3.duration')
			->join('pjMultiLang', "t2.model='pjService' AND t2.foreign_id=t1.service_id AND t2.field='title' AND t2.locale='".$locale_id."'", 'left outer')
			->join('pjService', 't3.id=t1.service_id', 'left outer')
			->where('t1.booking_id', $booking_id)
			->findAll()->getData();
		$service_arr = array();
		foreach($temp_service_arr as $k => $v)
		{
			$temp_arr = pjUtil::convertToHoursMins((int) $v['duration']);
			$duration_arr = array();
			if((int) $temp_arr['hours'] > 0)
			{
				$duration_arr[] = $temp_arr['hours']. ' ' . ($temp_arr['hours'] != 1 ? __('front_hours', true) : __('front_hour', true));
			}
			if((int) $temp_arr['minutes'] > 0)
			{
				$duration_arr[] = $temp_arr['minutes'] . ' '. ($temp_arr['minutes'] != 1 ? __('front_minutes', true) : __('front_minute', true));
			}
			
			$service_arr[] = $v['title'] . ' ('.pjUtil::formatCurrencySign($v['price'], $option_arr['o_currency']) . ' | '.join(' ', $duration_arr).')';
		}
		$services = join("<br/>", $service_arr);
	
		if (isset($data['c_country']) && !empty($data['c_country']))
		{
			if(isset($data['c_country']) && (int) $data['c_country'] > 0)
			{
				$country_arr = pjCountryModel::factory()
				->select('t1.id, t2.content AS country_title')
				->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$locale_id."'", 'left outer')
				->find($data['c_country'])
				->getData();
				if (!empty($country_arr))
				{
					$country = $country_arr['country_title'];
				}
			}
		}
		
		$subtotal = pjUtil::formatCurrencySign($data['subtotal'], $option_arr['o_currency']);
		$tax = pjUtil::formatCurrencySign($data['tax'], $option_arr['o_currency']);
		$deposit = pjUtil::formatCurrencySign($data['deposit'], $option_arr['o_currency']);
		$total = pjUtil::formatCurrencySign($data['total'], $option_arr['o_currency']);
	
		$cancelURL = PJ_INSTALL_URL . 'index.php?controller=pjFrontEnd&action=pjActionCancel&id='.@$data['id'].'&hash='.sha1(@$data['id'].@$data['created'].$salt);
		$cancelURL = '<a href="'.$cancelURL.'">' . $cancelURL . '</a>';
	
		$cc_exp = '';
		if(!empty($data['payment_method']) && $data['payment_method'] == 'creditcard')
		{
			$cc_exp = @$data['cc_exp_month'] . '-' . @$data['cc_exp_year'];
		}
	
		$search = array(
				'{DateTime}',
				'{Duration}',
				'{Services}',
				'{UniqueID}',
				'{Title}',
				'{Name}',
				'{Email}',
				'{Phone}',
				'{Country}',
				'{City}',
				'{State}',
				'{Zip}',
				'{Address}',
				'{Company}',
				'{SubTotal}',
				'{Total}',
				'{Tax}',
				'{Deposit}',
				'{PaymentMethod}',
				'{CCType}',
				'{CCNum}',
				'{CCExp}',
				'{CCSec}',
				'{CancelURL}'
		);
		$replace = array(
				$date_time,
				$duration,
				$services,
				@$data['uuid'],
				$title,
				@$data['c_name'],
				@$data['c_email'],
				@$data['c_phone'],
				$country,
				@$data['c_city'],
				@$data['c_state'],
				@$data['c_zip'],
				@$data['c_address'],
				@$data['c_company'],
				$subtotal,
				$total,
				$tax,
				$deposit,
				$payment_method,
				@$data['cc_type'],
				@$data['cc_num'],
				$cc_exp,
				@$data['cc_code'],
				$cancelURL
		);
		return compact('search', 'replace');
	}
	
	public function calculatePrices($service_id_arr, $option_arr)
	{
		$subtotal = 0;
		$tax = 0;
		$total = 0;
		$deposit = 0;
		
		$arr = pjServiceModel::factory()->whereIn('t1.id', $service_id_arr)->findAll()->getData();
		foreach($arr as $k => $v)
		{
			$subtotal += (float) $v['price'];
		}
		$tax = $subtotal * (float) $option_arr['o_tax_payment'] / 100;
		$total = $subtotal + $tax;
		$deposit = $total * (float) $option_arr['o_deposit_payment'] / 100;
		
		return compact('subtotal', 'tax', 'total', 'deposit');
	}
	
	public function getAdminEmail()
	{
		$arr = pjUserModel::factory()
			->findAll()
			->orderBy("t1.id ASC")
			->limit(1)
			->getData();
		return !empty($arr) ? $arr[0]['email'] : null;	
	}
	
	public function getAdminPhone()
	{
		$arr = pjUserModel::factory()
			->findAll()
			->orderBy("t1.id ASC")
			->limit(1)
			->getData();
		return !empty($arr) ? (!empty($arr[0]['phone']) ? $arr[0]['phone'] : null) : null;	
	}
	
	public function getWTimeData($selected_date_iso, $services_duration, $booking_id)
	{
		$pjWorkingTimeModel = pjWorkingTimeModel::factory();
		$wt_arr = $pjWorkingTimeModel->reset()->getWTime($selected_date_iso);
		
		if(!empty($wt_arr))
		{
			$wt_arr['end_ts'] = (int) $wt_arr['end_ts'] - (60 * $services_duration);
			if($booking_id > 0)
			{
				$booking_arr = pjBookingModel::factory()->where("(DATE(start_dt)='".$selected_date_iso."')")->where('t1.status <>', 'cancelled')->where('t1.id <>', $booking_id)->orderBy('start_dt ASC')->findAll()->getData();
			}else{
				$booking_arr = pjBookingModel::factory()->where("(DATE(start_dt)='".$selected_date_iso."')")->where('t1.status <>', 'cancelled')->orderBy('start_dt ASC')->findAll()->getData();
			}
		}else{
			$booking_arr= array();
		}
		
		return compact('wt_arr', 'booking_arr');
	}
}
?>