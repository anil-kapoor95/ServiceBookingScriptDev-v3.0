<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFront extends pjAppController
{	
	public $defaultCaptcha = 'pjSBS_Captcha';
	
	public $defaultLocale = 'pjSBS_LocaleId';
	
	public $defaultFrontUser = 'pjSBS_User';
	
	public $defaultLangMenu = 'pjSBS_LangMenu';
	
	public $defaultStore = 'pjSBS_Store';
	
	public $defaultForm = 'pjSBS_Form';
	
	public function __construct()
	{
		$this->setLayout('pjActionFront');
		
		self::allowCORS();
	}
	
	/**
	 * A verified (paid) Stripe payment arrived for a booking: store it, confirm the booking and send the payment emails.
	 * Safe to call twice - only the first call changes anything.
	 *
	 * @return bool|string TRUE confirmed now, FALSE already confirmed before, 'late' the booking was cancelled before the payment arrived
	 */
	protected function stripeConfirmPaid($booking_arr, $response)
	{
		$key = md5($this->option_arr['private_key'] . PJ_SALT);
		$log = array(
			'key' => $key,
			'foreign_id' => $booking_arr['id'],
			'session_id' => $response['session_id'],
			'payment_intent' => $response['transaction_id'],
			'amount' => $booking_arr['deposit'],
			'currency' => $this->option_arr['o_currency'],
			'email' => $response['email']
		);
		
		// an admin cancelled the booking before the payment arrived: do not re-confirm it,
		// the money has to be refunded / handled manually. The payment is kept in the Stripe log as "paid_late".
		$current = pjBookingModel::factory()->find($booking_arr['id'])->getData();
		if (!empty($current) && $current['status'] == 'cancelled')
		{
			$this->log('Stripe: payment ' . $response['transaction_id'] . ' for CANCELLED booking #' . $booking_arr['id'] . ' - needs a manual refund');
			$log['status'] = 'paid_late';
			$this->requestAction(array('controller' => 'pjStripe', 'action' => 'pjActionSave', 'params' => $log), array('return'));
			return 'late';
		}
		$log['status'] = 'paid';
		$this->requestAction(array('controller' => 'pjStripe', 'action' => 'pjActionSave', 'params' => $log), array('return'));
		
		$pjBookingPaymentModel = pjBookingPaymentModel::factory();
		$sql = "UPDATE `" . $pjBookingPaymentModel->getTable() . "` SET `status` = 'paid' WHERE `booking_id` = " . (int) $booking_arr['id'] . " AND `payment_type` = 'online' AND `status` <> 'paid' LIMIT 1";
		$pjBookingPaymentModel->reset()->execute($sql);
		if ((int) $pjBookingPaymentModel->getAffectedRows() < 1)
		{
			$online = $pjBookingPaymentModel->reset()->where('t1.booking_id', $booking_arr['id'])->where('t1.payment_type', 'online')->findCount()->getData();
			if ((int) $online > 0)
			{
				return false; // already paid
			}
		}
		
		pjBookingModel::factory()->set('id', $booking_arr['id'])->modify(array(
			'status' => $this->option_arr['o_payment_status'],
			'txn_id' => $response['transaction_id'],
			'processed_on' => ':NOW()'
		));
		$this->log('Stripe: booking #' . $booking_arr['id'] . ' confirmed');
		
		$mailer = ($this instanceof pjFrontEnd) ? $this : new pjFrontEnd();
		$mailer->option_arr = $this->option_arr;
		$mailer->pjActionConfirmSend($this->option_arr, $booking_arr['id'], PJ_SALT, 'payment');
		return true;
	}
	
	public function afterFilter()
	{		
		if (!isset($_GET['hide']) || (isset($_GET['hide']) && (int) $_GET['hide'] !== 1) &&
			in_array($_GET['action'], array('pjActionMain', 'pjActionTypes', 'pjActionLogin', 'pjActionVouchers', 'pjActionCheckout', 'pjActionPreview')))
		{
			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file, t2.title')
				->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
				->where('t2.file IS NOT NULL')
				->orderBy('t1.sort ASC')->findAll()->getData();
			
			$this->set('locale_arr', $locale_arr);
		}
	}
	
	public function beforeFilter()
	{
		$OptionModel = pjOptionModel::factory();
		$this->option_arr = $OptionModel->getPairs($this->getForeignId());
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
	
	public function beforeRender()
	{
		if (isset($_GET['iframe']))
		{
			$this->setLayout('pjActionIframe');
		}
	}
	
	public function pjActionLocale()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_GET['locale_id']))
			{
				$this->pjActionSetLocale($_GET['locale_id']);
				
				$this->loadSetFields(true);
				
				$day_names = __('day_names', true);
				ksort($day_names, SORT_NUMERIC);
				
				$months = __('months', true);
				ksort($months, SORT_NUMERIC);
				
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Locale have been changed.', 'opts' => array(
					'day_names' => array_values($day_names),
					'month_names' => array_values($months)
				)));
			}
		}
		exit;
	}
	
	public function pjActionGetLocale()
	{
		return isset($_SESSION[$this->defaultLocale]) && (int) $_SESSION[$this->defaultLocale] > 0 ? (int) $_SESSION[$this->defaultLocale] : FALSE;
	}
	
	public function isXHR()
	{
		return parent::isXHR() || isset($_SERVER['HTTP_ORIGIN']);
	}

	protected static function allowCORS()
	{
		$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
		header('P3P: CP="ALL DSP COR CUR ADM TAI OUR IND COM NAV INT"');
		header("Access-Control-Allow-Origin: $origin");
		header("Access-Control-Allow-Credentials: true");
		header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
		header("Access-Control-Allow-Headers: Origin, X-Requested-With");
	}
	protected function _get($key)
	{
		if ($this->_is($key))
		{
			return $_SESSION[$this->defaultStore][$key];
		}
		return false;
	}
	
	protected function _is($key)
	{
		return isset($_SESSION[$this->defaultStore]) && isset($_SESSION[$this->defaultStore][$key]);
	}
	
	protected function _set($key, $value)
	{
		$_SESSION[$this->defaultStore][$key] = $value;
		return $this;
	}
	
	protected function _unset($key)
	{
		if ($this->_is($key))
		{
			unset($_SESSION[$this->defaultStore][$key]);
		}
	}
	private function pjActionSetLocale($locale)
	{
		if ((int) $locale > 0)
		{
			$_SESSION[$this->defaultLocale] = (int) $locale;
		}
		return $this;
	}
}
?>