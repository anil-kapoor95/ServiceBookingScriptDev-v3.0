<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFrontEnd extends pjFront
{
	public function __construct()
	{
		parent::__construct();
		$this->setAjax(true);
		$this->setLayout('pjActionEmpty');
	}

	public function pjActionLoad()
	{
		$this->setAjax(false);
		$this->setLayout('pjActionFront');
		
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
		$this->set('terms_conditions', $terms_conditions);
		
		ob_start();
		header("Content-Type: text/javascript; charset=utf-8");
	}
	
	public function pjActionLoadCss()
	{
	    $dm = new pjDependencyManager(PJ_INSTALL_PATH, PJ_THIRD_PARTY_PATH);
	    $dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
	
		$theme = $this->option_arr['o_theme'];
		$fonts = $this->option_arr['o_theme'];
		if(isset($_GET['theme']) && in_array($_GET['theme'], array('theme1', 'theme2', 'theme3', 'theme4', 'theme5', 'theme6', 'theme7', 'theme8', 'theme9', 'theme10')))
		{
			$theme = $_GET['theme'];
			$fonts = $_GET['theme'];
		}
		$arr = array(
				array('file' => "$fonts.css", 'path' => PJ_CSS_PATH . "fonts/"),
				array('file' => 'bootstrap-datetimepicker.min.css', 'path' => $dm->getPath('pj_bootstrap_datetimepicker')),
				array('file' => 'owl.carousel.min.css', 'path' => $dm->getPath('pj_owlcarousel')),
				array('file' => 'style.css', 'path' => PJ_CSS_PATH),
				array('file' => "$theme.css", 'path' => PJ_CSS_PATH . "themes/",
				array('file' => 'transitions.css', 'path' => PJ_CSS_PATH))
		);
		header("Content-Type: text/css; charset=utf-8");
		foreach ($arr as $item)
		{
			ob_start();
			@readfile($item['path'] . $item['file']);
			$string = ob_get_contents();
			ob_end_clean();
				
			if ($string !== FALSE)
			{
				echo str_replace(
						array('../fonts/glyphicons', "pjWrapper"),
						array(
								PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/fonts/glyphicons',
								"pjWrapperServiceBooking_" . $theme
						),
						$string
				) . "\n";
			}
		}
		exit;
	}
	
	public function pjActionCaptcha()
	{
		$Captcha = new pjCaptcha('app/web/obj/Anorexia.ttf', $this->defaultCaptcha, 6);
		$Captcha->setImage('app/web/img/button.png')->init(isset($_GET['rand']) ? $_GET['rand'] : null);
	}

	public function pjActionCheckCaptcha()
	{
		if (!isset($_GET['captcha']) || empty($_GET['captcha']) || strtoupper($_GET['captcha']) != $_SESSION[$this->defaultCaptcha]){
			echo 'false';
		}else{
			echo 'true';
		}
		exit;
	}
	
	public function pjActionAddService()
	{
		if($this->isXHR())
		{
			if($this->_is('service_id'))
			{
				$this->_unset('service_id');
				$this->_unset('duration');
			}
			if(isset($_POST['service_id']) && is_array($_POST['service_id']) && count($_POST['service_id']) > 0)
			{
				$this->_set('service_id', $_POST['service_id']);
				$total_duration = 0;
				foreach($_POST['service_id'] as $service_id => $duration)
				{
					$total_duration += $duration;
				}
				$this->_set('duration', $total_duration);
			}
				
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
		}
	}
	public function pjActionSetWeek()
	{
		if($this->isXHR())
		{
			if(isset($_GET['start_date']) && !empty($_GET['start_date']) && isset($_GET['end_date']) && !empty($_GET['end_date']))
			{
				$week_arr = array();
				$week_arr[0] = $_GET['start_date'];
				$week_arr[1] = $_GET['end_date'];
				$this->_set('week', $week_arr);
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
			}
		}
	}
	public function pjActionSetDate()
	{
		if($this->isXHR())
		{
			if(isset($_GET['date']) && !empty($_GET['date']))
			{
				if($this->_is('date_iso'))
				{
					$this->_unset('date_iso');
				}
				$this->_unset('hour_iso');
				$this->_unset('minute_iso');
				$this->_set('date_iso', $_GET['date']);
	
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
			}
		}
	}
	public function pjActionSetHour()
	{
		if($this->isXHR())
		{
			if(isset($_GET['hour']))
			{
				if($this->_is('hour_iso'))
				{
					$this->_unset('hour_iso');
				}
				$this->_unset('minute_iso');
				$this->_set('hour_iso', $_GET['hour']);
	
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
			}
		}
	}
	public function pjActionSetMinute()
	{
		if($this->isXHR())
		{
			if(isset($_GET['minute']))
			{
				if($this->_is('minute_iso'))
				{
					$this->_unset('minute_iso');
				}
				$this->_set('minute_iso', $_GET['minute']);
	
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
			}
		}
	}


	public function pjActionSaveBooking()
	{
		if ($this->isXHR())
		{
			if (!isset($_POST['sbs_preview']) || !isset($_SESSION[$this->defaultForm]) || empty($_SESSION[$this->defaultForm]) || !isset($_SESSION[$this->defaultStore]) || empty($_SESSION[$this->defaultStore]))
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 109));
			}
			if ((int) $this->option_arr['o_bf_include_captcha'] === 3 && (!isset($_SESSION[$this->defaultForm]['captcha']) ||
					!pjCaptcha::validate($_SESSION[$this->defaultForm]['captcha'], $_SESSION[$this->defaultCaptcha]) ))
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 110));
			}
	
			$STORE = @$_SESSION[$this->defaultStore];
			$FORM = @$_SESSION[$this->defaultForm];
	
			$data = array();
	
			$data['uuid'] = time();
			$data['start_dt'] = $STORE['date_iso'] . ' ' . $STORE['hour_iso'] . ':' . $STORE['minute_iso'] . ':00';
			$data['end_dt'] = date('Y-m-d H:i:s', strtotime($data['start_dt']) + $STORE['duration'] * 60);
			$data['duration'] = $STORE['duration'];
			$data['ip'] = pjUtil::getClientIp();
			$data['status'] = $this->option_arr['o_booking_status'];
			$data['created'] = date('Y-m-d H:i:s');
			$payment = ':NULL';
			if(isset($FORM['payment_method']))
			{
				if (isset($FORM['payment_method'])){
					$payment = $FORM['payment_method'];
				}
			}
			$pjBookingModel = pjBookingModel::factory();
			$id = $pjBookingModel->setAttributes(array_merge($FORM, $data))->insert()->getInsertId();
			if ($id !== false && (int) $id > 0)
			{
				if(isset($STORE['service_id']) && count($STORE['service_id']) > 0)
				{
					$pjBookingServiceModel = pjBookingServiceModel ::factory();
					foreach($STORE['service_id'] as $service_id => $something)
					{
						$pjBookingServiceModel
						->reset()
						->setAttributes(array(
								'booking_id' => $id,
								'service_id' => $service_id
						))->insert();
					}
				}
				$arr = $pjBookingModel->reset()->find($id)->getData();
	
				$pdata = array();
				$pdata['booking_id'] = $id;
				$pdata['payment_method'] = $payment;
				$pdata['payment_type'] = 'online';
				$pdata['amount'] = $arr['deposit'];
				$pdata['status'] = 'notpaid';
				pjBookingPaymentModel::factory()->setAttributes($pdata)->insert();
	
				pjFrontEnd::pjActionConfirmSend($this->option_arr, $id, PJ_SALT, 'confirm');
	
				unset($_SESSION[$this->defaultStore]);
				unset($_SESSION[$this->defaultForm]);
	
				$json = array('code' => 200, 'text' => '', 'booking_id' => $id, 'payment' => $payment);
				pjAppController::jsonResponse($json);
			}else {
				pjAppController::jsonResponse(array('code' => 'ERR', 'code' => 119));
			}
		}
	}

	public function pjActionConfirmAuthorize()
	{
		if (pjObject::getPlugin('pjAuthorize') === NULL)
		{
			$this->log('Authorize.NET plugin not installed');
			exit;
		}
		$pjBookingModel = pjBookingModel::factory();
	
		$booking_arr = $pjBookingModel
		->find($_POST['x_invoice_num'])
		->getData();
		if (count($booking_arr) == 0)
		{
			$this->log('No such booking');
			pjUtil::redirect($this->option_arr['o_thankyou_page']);
		}
	
		if (count($booking_arr) > 0)
		{
			$params = array(
					'transkey' => $this->option_arr['o_authorize_transkey'],
					'x_login' => $this->option_arr['o_authorize_merchant_id'],
					'md5_setting' => $this->option_arr['o_authorize_md5_hash'],
					'key' => md5($this->option_arr['private_key'] . PJ_SALT)
			);
	
			$response = $this->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
			if ($response !== FALSE && $response['status'] === 'OK')
			{
			    $pjBookingModel->reset()->set('id', $response['transaction_id'])
				->modify(array('status' => $this->option_arr['o_payment_status'], 'processed_on' => ':NOW()'));
	
				pjBookingPaymentModel::factory()->where('booking_id', $response['transaction_id'])->where('payment_type', 'online')->limit(1)->modifyAll(array('status' => 'paid'));
					
				pjFrontEnd::pjActionConfirmSend($this->option_arr, $booking_arr, PJ_SALT, 'payment');
	
			} elseif (!$response) {
				$this->log('Authorization failed');
			} else {
				$this->log('Booking not confirmed. ' . $response['response_reason_text']);
			}
			?>
				<script type="text/javascript">window.location.href="<?php echo $this->option_arr['o_thankyou_page']; ?>";</script>
			<?php
			return;
		}
	}
		
	public function pjActionConfirmPaypal()
	{
		if (pjObject::getPlugin('pjPaypal') === NULL)
		{
			$this->log('Paypal plugin not installed');
			pjAppController::jsonResponse(array('status' => 'ERR', 'text' => 'Paypal plugin not installed'));
		}
		$input = file_get_contents('php://input');
		$post = json_decode($input, true);
		if ($post) {
		    $_REQUEST = array_merge($_REQUEST, $post);
		}
		
		$pjBookingModel = pjBookingModel::factory();
	
		$booking_arr = $pjBookingModel
		->find($_REQUEST['custom'])
		->getData();
		if (count($booking_arr) == 0)
		{
			$this->log('No such booking');
			pjAppController::jsonResponse(array('status' => 'ERR', 'text' => 'No such booking'));
		}
	
		$params = array(
		    'request'		=> $_REQUEST,
		    'cancel_hash'	=> sha1($booking_arr['uuid'].strtotime($booking_arr['created']).PJ_SALT),
		    'txn_id' => @$booking_arr['txn_id'],
		    'paypal_address' => $this->option_arr['o_paypal_address'],
		    'deposit' => @$booking_arr['deposit'],
		    'currency' => $this->option_arr['o_currency'],
		    'key' => md5($this->option_arr['private_key'] . PJ_SALT)
		);
		$response = $this->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));	
		if ($response !== FALSE && $response['status'] === 'OK')
		{
			$this->log('Booking confirmed');
			$pjBookingModel->reset()->set('id', $booking_arr['id'])->modify(array(
					'status' => $this->option_arr['o_payment_status'],
					'txn_id' => $response['transaction_id'],
					'processed_on' => ':NOW()'
			));
			pjBookingPaymentModel::factory()->where('booking_id', $booking_arr['id'])->where('payment_type', 'online')->limit(1)->modifyAll(array('status' => 'paid'));
				
			pjFrontEnd::pjActionConfirmSend($this->option_arr, $booking_arr['id'], PJ_SALT, 'payment');
			pjAppController::jsonResponse(array('status' => 'OK', 'text' => 'Booking confirmed'));
		} elseif (!$response) {
			$this->log('Authorization failed');
			pjAppController::jsonResponse(array('status' => 'ERR', 'text' => 'Authorization failed'));
		} else {
			$this->log('Booking not confirmed');
			pjAppController::jsonResponse(array('status' => 'ERR', 'text' => 'Booking not confirmed'));
		}
	}
		
	public function pjActionCancel()
	{
		$this->setLayout('pjActionCancel');
	
		$pjBookingModel = pjBookingModel::factory();
	
		if (isset($_POST['booking_cancel']))
		{
			$booking_arr = $pjBookingModel->find($_POST['id'])->getData();
			if (count($booking_arr) > 0)
			{
				$sql = "UPDATE `".$pjBookingModel->getTable()."` SET status = 'cancelled' WHERE SHA1(CONCAT(`id`, `created`, '".PJ_SALT."')) = '" . $_POST['hash'] . "'";
	
				$pjBookingModel->reset()->execute($sql);
	
				$arr = $pjBookingModel->reset()->find($_POST['id'])->getData();
				pjFrontEnd::pjActionConfirmSend($this->option_arr, $arr['id'], PJ_SALT, 'cancel');
	
				pjUtil::redirect($_SERVER['PHP_SELF'] . '?controller=pjFrontEnd&action=pjActionCancel&err=200');
			}
		}else{
			if (isset($_GET['hash']) && isset($_GET['id']))
			{
				$arr = $pjBookingModel
					->reset()
					->select("t1.*, t2.content as country_title, AES_DECRYPT(t1.cc_type, '".PJ_SALT."') AS `cc_type`,
								AES_DECRYPT(t1.cc_num, '".PJ_SALT."') AS `cc_num`,
								AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') AS `cc_exp_month`,
								AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') AS `cc_exp_year`,
								AES_DECRYPT(t1.cc_code, '".PJ_SALT."') AS `cc_code`")
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.c_country AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->find($_GET['id'])
					->getData();
				if (count($arr) == 0)
				{
					$this->set('status', 2);
				}else{
					if ($arr['status'] == 'cancelled')
					{
						$this->set('status', 4);
					}else{
						$hash = sha1($arr['id'] . $arr['created'] . PJ_SALT);
						if ($_GET['hash'] != $hash)
						{
							$this->set('status', 3);
						}else{
	
							$temp_service_arr = pjBookingServiceModel::factory()
								->select('t1.*, t2.content as title, t3.price, t3.duration')
								->join('pjMultiLang', "t2.model='pjService' AND t2.foreign_id=t1.service_id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
								->join('pjService', 't3.id=t1.service_id', 'left outer')
								->where('t1.booking_id', $_GET['id'])
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
	
								$service_arr[] = $v['title'] . ' ('.pjUtil::formatCurrencySign($v['price'], $this->option_arr['o_currency']) . ' | '.join(' ', $duration_arr).')';
							}
							$services = join("<br/>", $service_arr);
								
							$this->set('arr', $arr);
							$this->set('services', $services);
						}
					}
				}
			}else if (!isset($_GET['err'])) {
				$this->set('status', 1);
			}
		}
	}

	public function pjActionConfirmSend($option_arr, $booking_id, $salt, $opt)
	{
		$Email = new pjEmail();
		if ($option_arr['o_send_email'] == 'smtp')
		{
			$Email
			->setTransport('smtp')
			->setSmtpHost($option_arr['o_smtp_host'])
			->setSmtpPort($option_arr['o_smtp_port'])
			->setSmtpUser($option_arr['o_smtp_user'])
			->setSmtpPass($option_arr['o_smtp_pass'])
			;
		}
		$Email->setContentType('text/html');
	
		$admin_email = $this->getAdminEmail();
		$admin_phone = $this->getAdminPhone();
		$from_email = $admin_email;
	
		$locale_id = $this->getLocaleId();
	
		$booking_arr = pjBookingModel::factory()->find($booking_id)->getData();
	
		$tokens = pjAppController::getTokens($booking_id, $option_arr, PJ_SALT, $locale_id);
	
		$pjMultiLangModel = pjMultiLangModel::factory();
	
		if ($option_arr['o_email_payment'] == 1 && $opt == 'payment')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_email_payment_message')
			->limit(0, 1)
			->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_email_payment_subject')
			->limit(0, 1)
			->findAll()->getData();
	
			if (count($lang_message) === 1 && count($lang_subject) === 1 && !empty($lang_subject[0]['content']) && !empty($lang_message[0]['content']))
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
	
				$Email
				->setTo($booking_arr['c_email'])
				->setFrom($from_email)
				->setSubject($lang_subject[0]['content'])
				->send($message);
			}
		}
		if ($option_arr['o_admin_email_payment'] == 1 && $opt == 'payment')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_admin_email_payment_message')
			->limit(0, 1)
			->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_admin_email_payment_subject')
			->limit(0, 1)
			->findAll()->getData();
	
			if (count($lang_message) === 1 && count($lang_subject) === 1 && !empty($lang_subject[0]['content']) && !empty($lang_message[0]['content']))
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
	
				$Email
				->setTo($admin_email)
				->setFrom($from_email)
				->setSubject($lang_subject[0]['content'])
				->send($message);
			}
		}
		if(!empty($admin_phone) && $opt == 'payment')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_admin_sms_payment_message')
			->limit(0, 1)
			->findAll()->getData();
			if (count($lang_message) === 1 && !empty($lang_message[0]['content']))
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
				if($message != '')
				{
					$params = array(
							'text' => $message,
							'type' => 'unicode',
							'key' => md5($option_arr['private_key'] . PJ_SALT)
					);
					$params['number'] = $admin_phone;
					$this->requestAction(array('controller' => 'pjSms', 'action' => 'pjActionSend', 'params' => $params), array('return'));
				}
			}
		}
	
		if ($option_arr['o_email_confirmation'] == 1 && $opt == 'confirm')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_email_confirmation_message')
			->limit(0, 1)
			->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_email_confirmation_subject')
			->limit(0, 1)
			->findAll()->getData();
	
			if (count($lang_message) === 1 && count($lang_subject) === 1 && !empty($lang_subject[0]['content']) && !empty($lang_message[0]['content']))
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
					
				$Email
				->setTo($booking_arr['c_email'])
				->setFrom($from_email)
				->setSubject($lang_subject[0]['content'])
				->send($message);
			}
		}
		if ($option_arr['o_admin_email_confirmation'] == 1 && $opt == 'confirm')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_admin_email_confirmation_message')
			->limit(0, 1)
			->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_admin_email_confirmation_subject')
			->limit(0, 1)
			->findAll()->getData();
	
			if (count($lang_message) === 1 && count($lang_subject) === 1 && !empty($lang_subject[0]['content']) && !empty($lang_message[0]['content']))
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
				$Email
				->setTo($admin_email)
				->setFrom($from_email)
				->setSubject($lang_subject[0]['content'])
				->send($message);
			}
		}
		if(!empty($booking_arr['c_phone']) && $opt == 'confirm')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_sms_confirmation_message')
			->limit(0, 1)
			->findAll()->getData();
			if (count($lang_message) === 1 && !empty($lang_message[0]['content']))
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
				if($message != '')
				{
					$params = array(
							'text' => $message,
							'type' => 'unicode',
							'key' => md5($option_arr['private_key'] . PJ_SALT)
					);
					$params['number'] = $booking_arr['c_phone'];
					$this->requestAction(array('controller' => 'pjSms', 'action' => 'pjActionSend', 'params' => $params), array('return'));
				}
			}
		}
		if(!empty($admin_phone) && $opt == 'confirm')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_admin_sms_confirmation_message')
			->limit(0, 1)
			->findAll()->getData();
			if (count($lang_message) === 1 && !empty($lang_message[0]['content']))
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
				if($message != '')
				{
					$params = array(
							'text' => $message,
							'type' => 'unicode',
							'key' => md5($option_arr['private_key'] . PJ_SALT)
					);
					$params['number'] = $admin_phone;
					$this->requestAction(array('controller' => 'pjSms', 'action' => 'pjActionSend', 'params' => $params), array('return'));
				}
			}
		}
	
		if ($option_arr['o_email_cancel'] == 1 && $opt == 'cancel')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_email_cancel_message')
			->limit(0, 1)
			->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_email_cancel_subject')
			->limit(0, 1)
			->findAll()->getData();
	
			if (count($lang_message) === 1 && count($lang_subject) === 1 && !empty($lang_subject[0]['content']) && !empty($lang_message[0]['content']))
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
	
				$Email
				->setTo($booking_arr['c_email'])
				->setFrom($from_email)
				->setSubject($lang_subject[0]['content'])
				->send($message);
			}
		}
		if ($option_arr['o_admin_email_cancel'] == 1 && $opt == 'cancel')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_admin_email_cancel_message')
			->limit(0, 1)
			->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_admin_email_cancel_subject')
			->limit(0, 1)
			->findAll()->getData();
	
			if (count($lang_message) === 1 && count($lang_subject) === 1 && !empty($lang_subject[0]['content']) && !empty($lang_message[0]['content']))
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
	
				$Email
				->setTo($admin_email)
				->setFrom($from_email)
				->setSubject($lang_subject[0]['content'])
				->send($message);
			}
		}
	}
}
?>