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
		if(isset($_GET['theme']) && in_array($_GET['theme'], array('theme1', 'theme2', 'theme3', 'theme4', 'theme5', 'theme6', 'theme7', 'theme8', 'theme9', 'theme10', 'theme11')))
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

		if ($theme === 'theme11')
		{
			$wrap = "pjWrapperServiceBooking_" . $theme;
			$colors = array(
				// general
				'header'        => '#e8836b',
				'page'          => '#fbf0ea',
				'card'          => '#f4dccf',
				// text / fonts
				'text_heading'  => '#4a2a22',
				'text_body'     => '#7b5b51',
				'text_accent'   => '#ae3c22',
				// buttons
				'button'        => '#c94e33',
				'button_hover'  => '#b73f24',
				// list (service cards, date/time cells) active vs inactive state
				'list_active'   => '#dc775f',
				'list_inactive' => '#f0d3c6'
			);
			foreach ($colors as $key => $default)
			{
				$opt_key = 'o_theme11_' . $key;
				if (isset($this->option_arr[$opt_key]) && preg_match('/^#[0-9a-fA-F]{3,8}$/', $this->option_arr[$opt_key]))
				{
					$colors[$key] = $this->option_arr[$opt_key];
				}
			}
			// "#abc" / "#aabbcc" -> "rgba(r,g,b,alpha)" for soft glows and tints that follow the chosen colours
			$rgba = function ($hex, $alpha) {
				$h = ltrim($hex, '#');
				if (strlen($h) == 3) { $h = $h[0].$h[0].$h[1].$h[1].$h[2].$h[2]; }
				if (strlen($h) < 6) { return 'transparent'; }
				return 'rgba(' . hexdec(substr($h, 0, 2)) . ',' . hexdec(substr($h, 2, 2)) . ',' . hexdec(substr($h, 4, 2)) . ',' . $alpha . ')';
			};
			// inline "%23rrggbb" for the theme line icons that are SVG data-URIs
			$svgColor = '%23' . ltrim(substr($colors['header'], 0, 7), '#');
			$rowIcons = array(
				2 => "%3Crect x='7' y='8' width='10' height='10' rx='2'/%3E%3Cpath d='M9 8V6a3 3 0 0 1 6 0v2'/%3E%3Cpath d='M4 12h3M17 12h3M7 16l-2 2M17 16l2 2M7 10l-2-2M17 10l2-2'/%3E",
				3 => "%3Crect x='6' y='4' width='12' height='16' rx='2'/%3E%3Cpath d='M9 4h6v2H9z'/%3E%3Ccircle cx='15' cy='16' r='3'/%3E%3Cpath d='M17.2 18.2 19 20'/%3E",
				4 => "%3Cpath d='M5 16l1.5-5A2 2 0 0 1 8.4 9.5h7.2a2 2 0 0 1 1.9 1.5L19 16'/%3E%3Crect x='3.5' y='16' width='17' height='4' rx='1.5'/%3E%3Ccircle cx='7.5' cy='18' r='0.6' fill='" . $svgColor . "'/%3E%3Ccircle cx='16.5' cy='18' r='0.6' fill='" . $svgColor . "'/%3E",
				5 => "%3Ccircle cx='12' cy='13' r='7'/%3E%3Cpath d='M12 13l3-3M9 5h6'/%3E",
				6 => "%3Ccircle cx='12' cy='13' r='8'/%3E%3Cpath d='M12 13l4-4M8 13h8'/%3E"
			);
			?>
/* general */
#<?php echo $wrap; ?> { background-color: <?php echo $colors['page']; ?>; }
#<?php echo $wrap; ?> .pjSbs-services,
#<?php echo $wrap; ?> .pjSbs11-success.pjSbs-services { background-color: <?php echo $colors['page']; ?>; }
#<?php echo $wrap; ?> .pjSbs-services-form { background-color: #fff; }

/* text / fonts */
#<?php echo $wrap; ?> a,
#<?php echo $wrap; ?> .pjSbs-service-utilities em { color: <?php echo $colors['text_accent']; ?>; }
#<?php echo $wrap; ?> .pjSbs-services-title,
#<?php echo $wrap; ?> .pjSbs-service-title,
#<?php echo $wrap; ?> .pjSbs-price,
#<?php echo $wrap; ?> .pjSbs11-success .pjSbs-services-title { color: <?php echo $colors['text_heading']; ?>; }
#<?php echo $wrap; ?> .pjSbs-service-desc,
#<?php echo $wrap; ?> .pjSbs-services-form-title,
#<?php echo $wrap; ?> .pjSbs11-step-label { color: <?php echo $colors['text_body']; ?>; }

/* buttons */
#<?php echo $wrap; ?> .btn-primary,
#<?php echo $wrap; ?> .pjSbsBtnStartOver.btn-default,
#<?php echo $wrap; ?> .pjSbs11-success .pjSbsBtnStartOver { background-color: <?php echo $colors['button']; ?>; border-color: <?php echo $colors['button']; ?>; }
#<?php echo $wrap; ?> .btn-primary:hover,
#<?php echo $wrap; ?> .pjSbsBtnStartOver.btn-default:hover,
#<?php echo $wrap; ?> .pjSbs11-success .pjSbsBtnStartOver:hover { background-color: <?php echo $colors['button_hover']; ?>; border-color: <?php echo $colors['button_hover']; ?>; }
#<?php echo $wrap; ?> .pjSbs-btn-back { color: <?php echo $colors['header']; ?>; }

/* header / accent: step tracker (done+current), back button, grid heading labels */
#<?php echo $wrap; ?> .pjSbs11-step-current .pjSbs11-step-bar,
#<?php echo $wrap; ?> .pjSbs11-step-done .pjSbs11-step-bar { background-color: <?php echo $colors['header']; ?>; }
/* step icon chips follow the same accent colour (current = filled chip, done = icon + check badge) */
#<?php echo $wrap; ?> .pjSbs11-step-current .pjSbs11-step-icon { background-color: <?php echo $colors['header']; ?>; }
#<?php echo $wrap; ?> .pjSbs11-step-done .pjSbs11-step-icon { color: <?php echo $colors['header']; ?>; }
#<?php echo $wrap; ?> .pjSbs11-step-done .pjSbs11-step-icon:after { background-color: <?php echo $colors['header']; ?>; border-color: <?php echo $colors['page']; ?>; }
#<?php echo $wrap; ?> .pjSbs-available-times table th { background-color: <?php echo $colors['header']; ?>; }
/* the Hour/Minutes grid headings are a small text label on the unified
   white card now (not a solid colour bar), so the "Header / accent
   colour" swatch tints the text instead of filling a background */
#<?php echo $wrap; ?> .pjSbs-time-grid .pjSbs-heading { color: <?php echo $colors['header']; ?>; }

/* list - inactive/default state (unselected cards, un-reached step bar segments) */
#<?php echo $wrap; ?> .pjSbs-service,
#<?php echo $wrap; ?> .pjSbs-service.active:hover,
#<?php echo $wrap; ?> .pjSbs-service.active,
#<?php echo $wrap; ?> .pjSbs-date,
#<?php echo $wrap; ?> .pjSbs-available-times table td:not(.pjSbs-meridium),
#<?php echo $wrap; ?> .pjSbs-time-grid .pjSbs-body .pjSbs-minute span,
#<?php echo $wrap; ?> .pjSbs-time-grid .pjSbs-body .pjSbs-hour-cell span { background-color: <?php echo $colors['card']; ?>; }
#<?php echo $wrap; ?> .pjSbs11-step-bar { background-color: <?php echo $colors['list_inactive']; ?>; }
#<?php echo $wrap; ?> .pjSbs-service .pjSbs-ico-check { background-color: <?php echo $colors['list_inactive']; ?>; color: <?php echo $colors['text_accent']; ?>; }
#<?php echo $wrap; ?> .pjSbs-service.active .pjSbs-ico-check { color: #fff; }

/* list - active/selected state (chosen service card, chosen date/hour/minute) */
#<?php echo $wrap; ?> .pjSbs-service.active .pjSbs-ico-check,
#<?php echo $wrap; ?> .pjSbs-service.active:after,
#<?php echo $wrap; ?> .pjSbs-date.active,
#<?php echo $wrap; ?> .pjSbs-available-times table td.active,
#<?php echo $wrap; ?> .pjSbs-available-times table td:not(.pjSbs-meridium):hover,
#<?php echo $wrap; ?> .pjSbs-time-grid .pjSbs-body .pjSbs-minute span.active,
#<?php echo $wrap; ?> .pjSbs-time-grid .pjSbs-body .pjSbs-minute span:hover,
#<?php echo $wrap; ?> .pjSbs-time-grid .pjSbs-body .pjSbs-hour-cell span.active,
#<?php echo $wrap; ?> .pjSbs-time-grid .pjSbs-body .pjSbs-hour-cell span:hover { background-color: <?php echo $colors['list_active']; ?>; }
#<?php echo $wrap; ?> .pjSbs-service.active { border-color: <?php echo $colors['list_active']; ?>; }

/* fine-tuning: the remaining accents (gradients, glows, borders, line icons) follow the chosen colours too */
#<?php echo $wrap; ?> .pjSbs-service:hover { background-color: <?php echo $colors['card']; ?>; border-color: <?php echo $colors['list_active']; ?>; }
#<?php echo $wrap; ?> .pjSbs-date.active { background: linear-gradient(135deg, <?php echo $colors['list_active']; ?>, <?php echo $colors['button']; ?>); box-shadow: 0 6px 14px <?php echo $rgba($colors['list_active'], 0.4); ?>; }
#<?php echo $wrap; ?> .pjSbs-time-grid .pjSbs-body .pjSbs-minute span.active,
#<?php echo $wrap; ?> .pjSbs-time-grid .pjSbs-body .pjSbs-minute span:hover,
#<?php echo $wrap; ?> .pjSbs-time-grid .pjSbs-body .pjSbs-hour-cell span.active,
#<?php echo $wrap; ?> .pjSbs-time-grid .pjSbs-body .pjSbs-hour-cell span:hover { box-shadow: 0 4px 10px <?php echo $rgba($colors['list_active'], 0.35); ?>; border-color: <?php echo $colors['list_active']; ?>; }
#<?php echo $wrap; ?> .pjSbs-calendar-pick-holder .pjSbs-owl-nav div:after,
#<?php echo $wrap; ?> .pjSbs-calendar-pick .owl-nav div:after { color: <?php echo $colors['header']; ?>; }
#<?php echo $wrap; ?> .form-control:focus { border-color: <?php echo $colors['header']; ?>; box-shadow: 0 0 0 3px <?php echo $rgba($colors['header'], 0.18); ?>; }
#<?php echo $wrap; ?> .pjSbs-services-form-checkbox input[type="checkbox"],
#<?php echo $wrap; ?> input[type="checkbox"].required { accent-color: <?php echo $colors['header']; ?>; }
#<?php echo $wrap; ?> .pjSbs-services-prices-row:not(.pjSbs-services-prices-total) .pjSbs-price { border-color: <?php echo $colors['header']; ?>; }
#<?php echo $wrap; ?> .pjSbs-services-prices-total { background: linear-gradient(160deg, <?php echo $rgba($colors['header'], 0.10); ?>, <?php echo $rgba($colors['header'], 0.22); ?>); }
#<?php echo $wrap; ?> .pjSbs-services-prices-total .pjSbs-service-title,
#<?php echo $wrap; ?> .pjSbs-services-prices-total .pjSbs-price { color: <?php echo $colors['text_body']; ?>; }
#<?php echo $wrap; ?> .pjSbs-services-prices-total .row:nth-of-type(3) .pjSbs-service-title { color: <?php echo $colors['text_heading']; ?>; }
#<?php echo $wrap; ?> .pjSbs-services-prices-total .row:nth-of-type(3) .pjSbs-price { color: <?php echo $colors['text_accent']; ?>; }
<?php foreach ($rowIcons as $pos => $body) { ?>
#<?php echo $wrap; ?> .pjSbs-services-prices-row:nth-of-type(5n+<?php echo $pos; ?>) .row:before { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='<?php echo $svgColor; ?>' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'<?php echo "%3E" . $body . "%3C/svg%3E"; ?>"); }
<?php } ?>
<?php
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