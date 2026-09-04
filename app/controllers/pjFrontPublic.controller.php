<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFrontPublic extends pjFront
{
	public function __construct()
	{
		parent::__construct();
		
		$this->setAjax(true);
		
		$this->setLayout('pjActionEmpty');
	}
	
	public function pjActionServices()
	{
		if($this->isXHR())
		{
			$pjServiceModel = pjServiceModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjService' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
				->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjService' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'description'", 'left');
				
			$column = 'title';
			$direction = 'ASC';
				
			$arr = $pjServiceModel
				->select("t1.*, t2.content as title, t3.content as description")
				->where('t1.status', 'T')
				->orderBy("$column $direction")
				->findAll()
				->getData();
				
			$this->set('arr', $arr);
		}
	}
	public function pjActionDateTime()
	{
		if($this->isXHR())
		{
			if (isset($_SESSION[$this->defaultStore]) &&
					count($_SESSION[$this->defaultStore]) > 0 &&
					isset($_SESSION[$this->defaultStore]['service_id']))
			{
				$pjWorkingTimeModel = pjWorkingTimeModel::factory();
				$current_date = date('Y-m-d');

				$week_arr = array();
				if(!$this->_is('week'))
				{
					$this->_set('week', pjUtil::getWeekRange($current_date, $this->option_arr['o_week_start']));
				}
				$temp_week_arr = $this->_get('week');
				
				$week_start_ts = strtotime($temp_week_arr[0]);
				$week_end_ts = strtotime($temp_week_arr[1]);
				for($i = $week_start_ts; $i <= $week_end_ts; $i+= 86400)
				{
					$date = date('Y-m-d', $i);
					$week_arr[$date] = $pjWorkingTimeModel->checkDateOff($date);
				}
				if(!$this->_is('date_iso'))
				{
					$wt_arr = $pjWorkingTimeModel->reset()->getWTime($current_date);
					$this->_set('date_iso', $current_date);
				}else{
					$wt_arr = $pjWorkingTimeModel->reset()->getWTime($this->_get('date_iso'));
				}
				$wt_arr['end_ts'] = (int) $wt_arr['end_ts'] - (60 * $this->_get('duration'));
				
				$service_id_arr = array_keys($_SESSION[$this->defaultStore]['service_id']);
				
				$booking_arr = pjBookingModel::factory()
				->where("(DATE(start_dt)='".$this->_get('date_iso')."')")
				->where('t1.status <>', 'cancelled')
				->where("(t1.id IN(SELECT `TBS`.`booking_id` FROM ".pjBookingServiceModel::factory()->getTable()." AS `TBS` WHERE `TBS`.`service_id` IN (".join(",", $service_id_arr).") ))")
				->orderBy('start_dt ASC')->findAll()->getData();
				
				$this->set('current_date', $current_date);
				$this->set('week_arr', $week_arr);
				$this->set('wt_arr', $wt_arr);
				$this->set('booking_arr', $booking_arr);
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
				exit;
			}
		}
	}
	public function pjActionCheckout()
	{
		if($this->isXHR())
		{
			if (isset($_SESSION[$this->defaultStore]) &&
					count($_SESSION[$this->defaultStore]) > 0 &&
					isset($_SESSION[$this->defaultStore]['service_id']) && 
					isset($_SESSION[$this->defaultStore]['date_iso']) &&
					isset($_SESSION[$this->defaultStore]['hour_iso']) &&
					isset($_SESSION[$this->defaultStore]['minute_iso']))
			{
				if(isset($_POST['sbs_checkout']))
				{
					$_SESSION[$this->defaultForm] = $_POST;
				
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200));
				}else{
					$arr = pjServiceModel::factory()
						->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjService' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
						->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjService' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'description'", 'left')
						->select("t1.*, t2.content as title, t3.content as description")
						->whereIn('t1.id', array_keys($_SESSION[$this->defaultStore]['service_id']))
						->orderBy("title ASC")
						->findAll()
						->getData();
					
					$price_arr = pjAppController::calculatePrices(array_keys($_SESSION[$this->defaultStore]['service_id']), $this->option_arr);
					
					$country_arr = pjCountryModel::factory()
						->select('t1.id, t2.content AS country_title')
						->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->orderBy('`country_title` ASC')
						->findAll()
						->getData();
					
					$_terms_conditions = pjMultiLangModel::factory()->select('t1.*')
					->where('t1.model','pjOption')
					->where('t1.locale', $this->getLocaleId())
					->where('t1.field', 'o_terms')
					->limit(0, 1)
					->findAll()->getData();
					$terms_conditions = '';
					if(!empty($_terms_conditions))
					{
						$terms_conditions = $_terms_conditions[0]['content'];
					}
					
					$this->set('arr', $arr);
					$this->set('price_arr', $price_arr);
					$this->set('country_arr', $country_arr);
					$this->set('terms_conditions', $terms_conditions);
				}
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
				exit;
			}
		}
	}
	public function pjActionPreview()
	{
		if($this->isXHR())
		{
			if (isset($_SESSION[$this->defaultStore]) &&
					count($_SESSION[$this->defaultStore]) > 0 &&
					isset($_SESSION[$this->defaultStore]['service_id']) && 
					isset($_SESSION[$this->defaultStore]['date_iso']) &&
					isset($_SESSION[$this->defaultStore]['hour_iso']) &&
					isset($_SESSION[$this->defaultStore]['minute_iso']))
			{
				$arr = pjServiceModel::factory()
					->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjService' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
					->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjService' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'description'", 'left')
					->select("t1.*, t2.content as title, t3.content as description")
					->whereIn('t1.id', array_keys($_SESSION[$this->defaultStore]['service_id']))
					->orderBy("title ASC")
					->findAll()
					->getData();

				$price_arr = pjAppController::calculatePrices(array_keys($_SESSION[$this->defaultStore]['service_id']), $this->option_arr);
					
				if(isset($_SESSION[$this->defaultForm]['c_country']) && (int) $_SESSION[$this->defaultForm]['c_country'] > 0)
				{
					$country_arr = pjCountryModel::factory()
						->select('t1.id, t2.content AS country_title')
						->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->find($_SESSION[$this->defaultForm]['c_country'])
						->getData();
					$this->set('country_arr', $country_arr);
				}
					
				$this->set('arr', $arr);
				$this->set('price_arr', $price_arr);
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
				exit;
			}
		}
	}
	
	public function pjActionGetPaymentForm()
	{
		if ($this->isXHR())
		{
			$arr = pjBookingModel::factory()->find($_GET['booking_id'])->getData();
				
			if (!empty($arr))
			{
				switch ($arr['payment_method'])
				{
					case 'paypal':
						$this->set('params', array(
							'name' => 'sbsPaypal',
							'id' => 'sbsPaypal',
							'business' => $this->option_arr['o_paypal_address'],
							'client_id' => $this->option_arr['o_paypal_client_id'],
							'client_secret' => $this->option_arr['o_paypal_client_secret'],
							'item_name' => pjSanitize::html($arr['uuid']),
							'custom' => $arr['id'],
							'amount' => $arr['deposit'],
							'currency_code' => $this->option_arr['o_currency'],
							'return' => $this->option_arr['o_thankyou_page'],
							'failure_url' => $this->option_arr['o_paypal_cancel_url'],
							'notify_url' => PJ_INSTALL_URL . 'index.php?controller=pjFrontEnd&action=pjActionConfirmPaypal',
							'target' => '_self',
							'charset' => 'utf-8'
						));
						break;
					case 'authorize':
						$this->set('params', array(
							'name' => 'sbsAuthorize',
							'id' => 'sbsAuthorize',
							'target' => '_self',
							'timezone' => $this->option_arr['o_authorize_timezone'],
							'transkey' => $this->option_arr['o_authorize_transkey'],
							'x_login' => $this->option_arr['o_authorize_merchant_id'],
							'x_description' => pjSanitize::html($arr['uuid']),
							'x_amount' => $arr['deposit'],
							'x_invoice_num' => $arr['id'],
							'x_receipt_link_url' => $this->option_arr['o_thankyou_page'],
							'x_relay_url' => PJ_INSTALL_URL . 'index.php?controller=pjFrontEnd&action=pjActionConfirmAuthorize'
						));
						break;
				}
			}
			$this->set('arr', $arr);
			$this->set('get', $_GET);
		}
	}
	
}
?>