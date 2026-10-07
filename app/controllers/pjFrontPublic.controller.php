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
				->orderBy("t1.sort_order ASC, $column $direction")
				->findAll()
				->getData();

			// categories that still have at least one active service (front-end filter)
			$category_arr = pjServiceCategoryModel::factory()
				->select("t1.id, t2.content AS title")
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjServiceCategory' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
				->where('t1.status', 'T')
				->where("(t1.id IN (SELECT `TS`.category_id FROM `".pjServiceModel::factory()->getTable()."` AS `TS` WHERE `TS`.status = 'T'))")
				->orderBy("title ASC")
				->findAll()
				->getData();

			// extras are chosen on their own step; only tell the view whether the selected services have any
			$extra_arr = $this->getSelectedExtras();

			$this->set('arr', $arr);
			$this->set('category_arr', $category_arr);
			$this->set('extra_arr', $extra_arr);
		}
	}
	/**
	 * Active extras offered with the services currently selected in the session.
	 */
	private function getSelectedExtras()
	{
		$extra_arr = array();
		if (isset($_SESSION[$this->defaultStore]['service_id']) && count($_SESSION[$this->defaultStore]['service_id']) > 0)
		{
			$extra_arr = pjExtraModel::factory()
				->select("t1.*, t2.content AS title, t3.content AS description")
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjExtra' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
				->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjExtra' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'description'", 'left')
				->where('t1.status', 'T')
				->where("(t1.id IN (SELECT `TSE`.extra_id FROM `".pjServiceExtraModel::factory()->getTable()."` AS `TSE` WHERE `TSE`.service_id IN (".join(',', array_map('intval', array_keys($_SESSION[$this->defaultStore]['service_id'])))."))) ")
				->orderBy("title ASC")
				->findAll()
				->getData();
		}
		return $extra_arr;
	}
	/**
	 * Extras step: the extras that belong to the selected services.
	 */
	public function pjActionExtras()
	{
		if($this->isXHR())
		{
			if (isset($_SESSION[$this->defaultStore]['service_id']) && count($_SESSION[$this->defaultStore]['service_id']) > 0)
			{
				$service_arr = pjServiceModel::factory()
					->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjService' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
					->select("t1.*, t2.content AS title")
					->where('t1.status', 'T')
					->whereIn('t1.id', array_map('intval', array_keys($_SESSION[$this->defaultStore]['service_id'])))
					->findAll()
					->getData();
				$this->set('service_arr', $service_arr);
				$this->set('extra_arr', $this->getSelectedExtras());
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
			}
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
				$this->set('has_extras', count($this->getSelectedExtras()) > 0);
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
					$_POST = pjAppController::trimDeep($_POST);
					if (!pjAppController::validateClientDetails($_POST, $this->option_arr))
					{
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => ''));
						exit;
					}
					$_SESSION[$this->defaultForm] = $_POST;
				
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200));
				}else{
					$arr = pjServiceModel::factory()
						->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjService' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
						->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjService' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'description'", 'left')
						->select("t1.*, t2.content as title, t3.content as description")
						->whereIn('t1.id', array_keys($_SESSION[$this->defaultStore]['service_id']))
						->orderBy("t1.sort_order ASC, title ASC")
						->findAll()
						->getData();
					
					$extra_id_arr = isset($_SESSION[$this->defaultStore]['extra_id']) ? array_keys($_SESSION[$this->defaultStore]['extra_id']) : array();
					$extra_arr = array();
					if (!empty($extra_id_arr))
					{
						$extra_arr = pjExtraModel::factory()
							->select("t1.*, t2.content AS title")
							->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjExtra' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
							->whereIn('t1.id', $extra_id_arr)
							->orderBy("title ASC")
							->findAll()
							->getData();
					}
					$price_arr = pjAppController::calculatePrices(array_keys($_SESSION[$this->defaultStore]['service_id']), $this->option_arr, $extra_id_arr);
					$this->set('extra_arr', $extra_arr);
					
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
					->orderBy("t1.sort_order ASC, title ASC")
					->findAll()
					->getData();

				$extra_id_arr = isset($_SESSION[$this->defaultStore]['extra_id']) ? array_keys($_SESSION[$this->defaultStore]['extra_id']) : array();
				$extra_arr = array();
				if (!empty($extra_id_arr))
				{
					$extra_arr = pjExtraModel::factory()
						->select("t1.*, t2.content AS title")
						->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjExtra' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
						->whereIn('t1.id', $extra_id_arr)
						->orderBy("title ASC")
						->findAll()
						->getData();
				}
				$price_arr = pjAppController::calculatePrices(array_keys($_SESSION[$this->defaultStore]['service_id']), $this->option_arr, $extra_id_arr);
				$this->set('extra_arr', $extra_arr);
					
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
					case 'stripe':
						$stripe = array('url' => '', 'error' => '');
						if (pjObject::getPlugin('pjStripe') !== NULL)
						{
							$hash = sha1($arr['id'] . $arr['created'] . PJ_SALT);
							// customer cancels on Stripe -> back to the booking page (or the thank-you page)
							$cancel_url = (isset($_GET['return_url']) && preg_match('#^https?://#i', $_GET['return_url'])) ? $_GET['return_url'] : $this->option_arr['o_thankyou_page'];
							$items = pjBookingServiceModel::factory()
								->select('t2.content AS title')
								->join('pjMultiLang', "t2.model='pjService' AND t2.foreign_id=t1.service_id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
								->where('t1.booking_id', $arr['id'])
								->findAll()->getData();
							$titles = array();
							foreach ($items as $item)
							{
								if (!empty($item['title'])) { $titles[] = $item['title']; }
							}
							$description = 'Booking #' . $arr['uuid'] . (count($titles) > 0 ? ' - ' . join(', ', $titles) : '');
							$response = $this->requestAction(array('controller' => 'pjStripe', 'action' => 'pjActionCreateSession', 'params' => array(
								'key' => md5($this->option_arr['private_key'] . PJ_SALT),
								'secret_key' => $this->option_arr['o_stripe_secret_key'],
								'booking_id' => $arr['id'],
								'uuid' => $arr['uuid'],
								'amount' => $arr['deposit'],
								'currency' => $this->option_arr['o_currency'],
								'description' => $description,
								'email' => $arr['c_email'],
								'success_url' => PJ_INSTALL_URL . 'index.php?controller=pjFrontEnd&action=pjActionConfirmStripe&booking_id=' . $arr['id'] . '&hash=' . $hash . '&stripe_sid={CHECKOUT_SESSION_ID}',
								'cancel_url' => $cancel_url
							)), array('return'));
							if (is_array($response) && $response['status'] === 'OK')
							{
								$stripe['ok'] = true;
								$stripe['url'] = $response['url'];
								$stripe['session_id'] = $response['session_id'];
								$stripe['api_key'] = trim($this->option_arr['o_stripe_api_key']);
								// remember the Checkout Session id (replaced by the payment id once paid)
								pjBookingModel::factory()->set('id', $arr['id'])->modify(array('txn_id' => $response['session_id']));
							} else {
								$stripe['error'] = is_array($response) && isset($response['text']) ? $response['text'] : '';
								$this->log('Stripe: could not start Checkout for booking #' . $arr['id'] . ' - ' . $stripe['error']);
							}
						} else {
							$this->log('Stripe plugin not installed');
						}
						$this->set('params', $stripe);
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