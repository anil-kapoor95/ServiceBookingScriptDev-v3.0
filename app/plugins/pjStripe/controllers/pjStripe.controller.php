<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
/**
 * Stripe Checkout (hosted payment page) plugin.
 *
 * Flow:
 *  1. pjActionCreateSession  - creates a Stripe Checkout Session and returns its hosted page URL
 *  2. the customer pays on Stripe's page and comes back to the script (success_url)
 *  3. pjActionConfirm        - asks Stripe for the session and verifies "paid", amount and currency
 *
 * No Stripe SDK is needed, only PHP cURL (or allow_url_fopen).
 */
class pjStripe extends pjStripeAppController
{
	protected static $logPrefix = "Payments | pjStripe plugin<br>";
	
	/** currencies Stripe treats as having no minor unit */
	private static $zeroDecimal = array('BIF','CLP','DJF','GNF','JPY','KMF','KRW','MGA','PYG','RWF','UGX','VND','VUV','XAF','XOF','XPF');
	/** currencies Stripe treats as having 3 decimals */
	private static $threeDecimal = array('BHD','IQD','JOD','KWD','LYD','OMR','TND');
	
	/**
	 * Convert a decimal amount (e.g. 12.50) to Stripe's smallest currency unit (e.g. 1250).
	 */
	public static function toMinorUnits($amount, $currency)
	{
		$currency = strtoupper($currency);
		$amount = (float) $amount;
		if (in_array($currency, self::$zeroDecimal))
		{
			return (int) round($amount);
		}
		if (in_array($currency, self::$threeDecimal))
		{
			return (int) (round($amount * 100) * 10);
		}
		return (int) round($amount * 100);
	}
	
	private function checkKey($params)
	{
		return isset($params['key']) && $params['key'] == md5($this->option_arr['private_key'] . PJ_SALT);
	}
	
	/**
	 * Call the Stripe REST API.
	 *
	 * @return array array('code' => HTTP status (0 on transport error), 'body' => decoded JSON|NULL, 'error' => string)
	 */
	private function api($secret, $method, $path, $data = array(), $timeout = 30)
	{
		$url = rtrim(PJ_STRIPE_API_BASE, '/') . $path;
		$method = strtoupper($method);
		$body = ($method === 'POST') ? http_build_query($data, '', '&') : '';
		if ($method === 'GET' && !empty($data))
		{
			$url .= '?' . http_build_query($data, '', '&');
		}
		$headers = array(
			'Authorization: Bearer ' . $secret,
			'Content-Type: application/x-www-form-urlencoded',
			'Stripe-Version: 2022-11-15'
		);
		
		$raw = false;
		$code = 0;
		$error = '';
		if (function_exists('curl_init'))
		{
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_USERAGENT, 'ServiceBookingScript-pjStripe/1.0');
			if ($method === 'POST')
			{
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
			}
			$raw = curl_exec($ch);
			if ($raw === false)
			{
				$error = curl_error($ch);
			}
			$code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
		} else {
			$opts = array('http' => array(
				'method' => $method,
				'header' => implode("\r\n", $headers),
				'content' => $body,
				'timeout' => $timeout,
				'ignore_errors' => true
			));
			$raw = @file_get_contents($url, false, stream_context_create($opts));
			if ($raw === false)
			{
				$error = 'HTTP request failed (enable PHP cURL or allow_url_fopen).';
			}
			if (isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $m))
			{
				$code = (int) $m[1];
			}
		}
		
		$decoded = ($raw !== false) ? json_decode($raw, true) : NULL;
		if ($raw !== false && !is_array($decoded))
		{
			$error = 'Invalid response from Stripe.';
		}
		if (is_array($decoded) && isset($decoded['error']['message']))
		{
			$error = $decoded['error']['message'];
		}
		return array('code' => $code, 'body' => $decoded, 'error' => $error);
	}
	
	/**
	 * Create a Stripe Checkout Session.
	 *
	 * Params: key, secret_key, booking_id, uuid, amount, currency, description, email,
	 *         success_url, cancel_url, locale
	 *
	 * @return array array('status' => 'OK', 'url' => ..., 'session_id' => ...) or array('status' => 'ERR', 'text' => ...)
	 */
	public function pjActionCreateSession()
	{
		$this->setAjax(true);
		$this->setLayout('pjActionEmpty');
		
		$params = $this->getParams();
		if (!$this->checkKey($params))
		{
			$this->log(self::$logPrefix . "Missing or invalid 'key' parameter.");
			return array('status' => 'ERR', 'text' => 'Invalid request.');
		}
		
		$secret = isset($params['secret_key']) ? trim($params['secret_key']) : '';
		if ($secret === '' || strpos($secret, 'sk_') !== 0 && strpos($secret, 'rk_') !== 0)
		{
			$this->log(self::$logPrefix . "Stripe secret key is missing or invalid (must start with sk_ or rk_).");
			return array('status' => 'ERR', 'text' => 'Stripe is not configured.');
		}
		
		$currency = strtolower($params['currency']);
		$amount = self::toMinorUnits($params['amount'], $currency);
		if ($amount <= 0)
		{
			$this->log(self::$logPrefix . "Invalid amount: " . $params['amount']);
			return array('status' => 'ERR', 'text' => 'Invalid amount.');
		}
		
		$name = !empty($params['description']) ? $params['description'] : ('Booking #' . $params['booking_id']);
		$data = array(
			'mode' => 'payment',
			'success_url' => $params['success_url'],
			'cancel_url' => $params['cancel_url'],
			'client_reference_id' => (string) $params['booking_id'],
			'line_items' => array(
				array(
					'quantity' => 1,
					'price_data' => array(
						'currency' => $currency,
						'unit_amount' => $amount,
						'product_data' => array('name' => mb_substr($name, 0, 250))
					)
				)
			),
			'metadata' => array(
				'booking_id' => (string) $params['booking_id'],
				'uuid' => isset($params['uuid']) ? (string) $params['uuid'] : ''
			),
			'payment_intent_data' => array(
				'description' => mb_substr($name, 0, 250),
				'metadata' => array('booking_id' => (string) $params['booking_id'])
			)
		);
		if (!empty($params['email']) && filter_var($params['email'], FILTER_VALIDATE_EMAIL))
		{
			$data['customer_email'] = $params['email'];
		}
		$data['locale'] = (!empty($params['locale']) && preg_match('/^[a-z]{2}(-[A-Z]{2})?$/', $params['locale'])) ? $params['locale'] : 'auto';
		
		$resp = $this->api($secret, 'POST', '/v1/checkout/sessions', $data);
		if ($resp['code'] >= 200 && $resp['code'] < 300 && !empty($resp['body']['id']))
		{
			$this->log(self::$logPrefix . "Checkout session created: " . $resp['body']['id'] . " (booking #" . $params['booking_id'] . ")");
			return array('status' => 'OK', 'url' => !empty($resp['body']['url']) ? $resp['body']['url'] : '', 'session_id' => $resp['body']['id']);
		}
		$this->log(self::$logPrefix . "Could not create Checkout session (HTTP " . $resp['code'] . "): " . $resp['error']);
		return array('status' => 'ERR', 'text' => $resp['error'] !== '' ? $resp['error'] : 'Could not contact Stripe.');
	}
	
	/**
	 * Ask Stripe for a Checkout Session and verify that it is really paid for this booking.
	 *
	 * Params: key, secret_key, session_id, booking_id, amount, currency
	 *
	 * @return array status: OK (paid) | PENDING (not paid yet) | FAIL ; OK also returns txn_id, transaction_id, email
	 */
	public function pjActionConfirm()
	{
		$this->setAjax(true);
		$this->setLayout('pjActionEmpty');
		
		$params = $this->getParams();
		$response = array('status' => 'FAIL', 'redirect' => false);
		if (!$this->checkKey($params))
		{
			$this->log(self::$logPrefix . "Missing or invalid 'key' parameter.");
			return $response;
		}
		if (empty($params['session_id']) || !preg_match('/^cs_[A-Za-z0-9_]+$/', $params['session_id']))
		{
			$this->log(self::$logPrefix . "Missing or invalid 'session_id' parameter.");
			return $response;
		}
		
		$resp = $this->api(trim($params['secret_key']), 'GET', '/v1/checkout/sessions/' . $params['session_id']);
		if ($resp['code'] != 200 || !is_array($resp['body']))
		{
			$this->log(self::$logPrefix . "Could not retrieve session {$params['session_id']} (HTTP {$resp['code']}): " . $resp['error']);
			return $response;
		}
		return $this->verifySession($resp['body'], $params);
	}
	
	/**
	 * Verify a Checkout Session object (from the API) against the booking.
	 *
	 * Params: booking_id, amount, currency
	 */
	protected function verifySession($session, $params)
	{
		$response = array('status' => 'FAIL', 'redirect' => false);
		$sid = isset($session['id']) ? $session['id'] : '';
		
		$ref = isset($session['client_reference_id']) ? $session['client_reference_id'] : (isset($session['metadata']['booking_id']) ? $session['metadata']['booking_id'] : '');
		if ((string) $ref !== (string) $params['booking_id'])
		{
			$this->log(self::$logPrefix . "Session {$sid}: booking reference mismatch.");
			return $response;
		}
		if (!isset($session['payment_status']) || $session['payment_status'] !== 'paid')
		{
			$this->log(self::$logPrefix . "Session {$sid}: payment status is '" . (isset($session['payment_status']) ? $session['payment_status'] : 'unknown') . "' (not paid yet).");
			$response['status'] = 'PENDING';
			return $response;
		}
		if (!isset($session['currency']) || strtoupper($session['currency']) !== strtoupper($params['currency']))
		{
			$this->log(self::$logPrefix . "Session {$sid}: CURRENCY didn't match.");
			return $response;
		}
		$expected = self::toMinorUnits($params['amount'], $params['currency']);
		if (!isset($session['amount_total']) || (int) $session['amount_total'] !== $expected)
		{
			$this->log(self::$logPrefix . "Session {$sid}: AMOUNT didn't match (expected {$expected}, got " . (isset($session['amount_total']) ? $session['amount_total'] : 'n/a') . ").");
			return $response;
		}
		
		$txn_id = !empty($session['payment_intent']) && is_string($session['payment_intent']) ? $session['payment_intent'] : $sid;
		$this->log(self::$logPrefix . "Session {$sid}: payment was successful. TXN ID: {$txn_id}.");
		$response['status'] = 'OK';
		$response['txn_id'] = $txn_id;
		$response['transaction_id'] = $txn_id;
		$response['session_id'] = $sid;
		$response['email'] = isset($session['customer_details']['email']) ? $session['customer_details']['email'] : (isset($session['customer_email']) ? $session['customer_email'] : '');
		return $response;
	}
	
	/**
	 * Store (or update) a payment record. Returns TRUE when this call changed the record to a paid state for the first time.
	 *
	 * Params: key, foreign_id, session_id, payment_intent, event_id, amount, currency, status, email
	 */
	public function pjActionSave()
	{
		$this->setLayout('pjActionEmpty');
		
		$params = $this->getParams();
		if (!$this->checkKey($params))
		{
			return false;
		}
		$pjStripeModel = pjStripeModel::factory();
		$arr = $pjStripeModel->where('t1.session_id', $params['session_id'])->limit(1)->findAll()->getData();
		$data = array(
			'foreign_id' => $params['foreign_id'],
			'session_id' => $params['session_id'],
			'payment_intent' => @$params['payment_intent'],
			'event_id' => @$params['event_id'],
			'amount' => $params['amount'],
			'currency' => $params['currency'],
			'status' => $params['status'],
			'email' => @$params['email'],
			'dt' => date('Y-m-d H:i:s')
		);
		if (count($arr) === 1)
		{
			$pjStripeModel->reset()->set('id', $arr[0]['id'])->modify($data);
		} else {
			$pjStripeModel->reset()->setAttributes($data)->insert();
		}
		return true;
	}
	
	/**
	 * Output the "redirecting to Stripe" block.
	 * Params: url (Stripe hosted page, may be empty), error (message when the session could not be created)
	 */
	public function pjActionForm()
	{
		$this->setLayout('pjActionEmpty');
		$this->setAjax(true);
		$this->set('arr', $this->getParams());
	}
}
?>
