<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminOptions extends pjAdmin
{
	public function pjActionIndex()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			$arr = pjOptionModel::factory()
				->where('t1.foreign_id', $this->getForeignId())
				->orderBy('t1.order ASC')
				->findAll()
				->getData();
			
			$this->set('arr', $arr);
			
			$this->appendJs('pjAdminOptions.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			if (isset($_POST['options_update']))
			{
				if (isset($_POST['next_action']) && $_POST['next_action'] === 'pjActionEmailSettings')
				{
					// trim every text value of the Email Settings form (spaces-only counts as empty)
					foreach (array('value-string-o_smtp_host', 'value-int-o_smtp_port', 'value-string-o_smtp_user', 'value-string-o_from_email', 'value-string-o_from_name') as $f)
					{
						if (isset($_POST[$f])) { $_POST[$f] = trim($_POST[$f]); }
					}
					if (isset($_POST['value-string-o_smtp_host']))
					{
						$_POST['value-string-o_smtp_host'] = str_ireplace(array('ssl://', 'tls://'), array('', ''), $_POST['value-string-o_smtp_host']);
					}
					$method = isset($_POST['value-enum-o_send_email']) ? $_POST['value-enum-o_send_email'] : '';
					$is_smtp = (substr($method, -6) === '::smtp');
					$bad_from = !empty($_POST['value-string-o_from_email']) && !filter_var($_POST['value-string-o_from_email'], FILTER_VALIDATE_EMAIL);
					if ($bad_from || ($is_smtp && (empty($_POST['value-string-o_smtp_host']) || empty($_POST['value-int-o_smtp_port']) || (int) $_POST['value-int-o_smtp_port'] <= 0)))
					{
						pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOptions&action=pjActionEmailSettings&err=AO08");
					}
				}
				
				$OptionModel = new pjOptionModel();
			
				foreach ($_POST as $key => $value)
				{
					if (preg_match('/value-(string|text|int|float|enum|bool|color)-(.*)/', $key) === 1)
					{
						list(, $type, $k) = explode("-", $key);
						if (!empty($k))
						{
							$OptionModel
								->reset()
								->where('foreign_id', $this->getForeignId())
								->where('`key`', $k)
								->limit(1)
								->modifyAll(array('value' => $value));
						}
					}
				}
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], 1, 'pjOption', 'data');
				}
				
				if (isset($_POST['next_action']))
				{
					switch ($_POST['next_action'])
					{
						case 'pjActionIndex':
							$err = 'AO01';
							break;
						case 'pjActionBooking':
							$err = 'AO02';
							break;
						case 'pjActionNotification':
							$err = 'AO03&tab_id=' . $_POST['tab_id'];
							break;
						case 'pjActionBookingForm':
							$err = 'AO04';
							break;
						case 'pjActionTerm':
							$err = 'AO05';
							break;
						case 'pjActionPreview':
							$err = 'AO06';
							break;
						case 'pjActionEmailSettings':
							$err = 'AO07';
							break;
					}
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOptions&action=" . @$_POST['next_action'] . "&err=$err");
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	/**
	 * Email Settings tab: mail transport (PHP mail() / SMTP), SMTP security + authentication,
	 * sender address / name, "Test connection" and "Send test email".
	 */
	public function pjActionEmailSettings()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$this->appendJs('pjAdminOptions.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	/**
	 * AJAX: test the SMTP connection with the values currently typed in the form (nothing is saved).
	 */
	public function pjActionAjaxSmtp()
	{
		$this->setAjax(true);
		
		if (!$this->isXHR())
		{
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Invalid request.'));
		}
		if (!(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST'))
		{
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Invalid request method.'));
		}
		if (!$this->isLoged() || !$this->isAdmin())
		{
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Forbidden.'));
		}
		
		$host   = isset($_POST['smtp_host'])   ? trim($_POST['smtp_host'])   : '';
		$port   = isset($_POST['smtp_port'])   ? trim($_POST['smtp_port'])   : '';
		$user   = isset($_POST['smtp_user'])   ? trim($_POST['smtp_user'])   : '';
		$pass   = isset($_POST['smtp_pass'])   ? (string) $_POST['smtp_pass'] : '';
		$secure = isset($_POST['smtp_secure']) ? trim($_POST['smtp_secure']) : '';
		$auth   = isset($_POST['smtp_auth'])   ? trim($_POST['smtp_auth'])   : 'LOGIN';
		
		if ($host === '' || $port === '')
		{
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => __('emailMsgEnterHostPort', true)));
		}
		
		$message = '';
		$result  = false;
		try {
			$mail = new pjPHPMailer(true);
			$mail->isSMTP();
			$mail->Timeout = 10;
			$mail->Host = str_ireplace(array('ssl://', 'tls://'), array('', ''), $host);
			$mail->Port = (int) $port;
			if (in_array($secure, array('ssl', 'tls')))
			{
				$mail->SMTPSecure = $secure;
			}
			if ($user !== '')
			{
				$mail->SMTPAuth = true;
				$mail->AuthType = in_array($auth, array('CRAM-MD5', 'LOGIN', 'PLAIN')) ? $auth : 'LOGIN';
				$mail->Username = $user;
				$mail->Password = $pass;
			}
			$result = $mail->smtpConnect();
			if (!$result)
			{
				$message = trim((string) $mail->ErrorInfo);
				if ($message === '')
				{
					$smtp_err = $mail->getSMTPInstance()->getError();
					$message = !empty($smtp_err['error']) ? trim($smtp_err['error'] . (!empty($smtp_err['detail']) ? ' ' . $smtp_err['detail'] : '')) : '';
					if ($message === '')
					{
						$message = $mail->Host . ':' . $mail->Port;
					}
				}
			}
			$mail->smtpClose();
		} catch (Exception $e) {
			$result  = false;
			$message = $e->getMessage();
		}
		
		if ($result)
		{
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => __('emailMsgConnOk', true)));
		}
		pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 104, 'text' => __('emailMsgConnFail', true) . ($message !== '' ? ' (' . pjSanitize::html($message) . ')' : '')));
	}
	
	/**
	 * AJAX: send a test email with the values currently typed in the form (nothing is saved).
	 */
	public function pjActionAjaxSend()
	{
		$this->setAjax(true);
		
		if (!$this->isXHR())
		{
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Invalid request.'));
		}
		if (!(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST'))
		{
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Invalid request method.'));
		}
		if (!$this->isLoged() || !$this->isAdmin())
		{
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Forbidden.'));
		}
		
		$email = isset($_POST['email']) ? trim($_POST['email']) : '';
		if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => __('emailMsgEnterValidEmail', true)));
		}
		
		$post_opt = array(
			'o_send_email'  => (isset($_POST['send_email']) && $_POST['send_email'] === 'smtp') ? 'smtp' : 'mail',
			'o_smtp_host'   => isset($_POST['smtp_host'])   ? trim($_POST['smtp_host'])   : '',
			'o_smtp_port'   => isset($_POST['smtp_port'])   ? trim($_POST['smtp_port'])   : '',
			'o_smtp_user'   => isset($_POST['smtp_user'])   ? trim($_POST['smtp_user'])   : '',
			'o_smtp_pass'   => isset($_POST['smtp_pass'])   ? (string) $_POST['smtp_pass'] : '',
			'o_smtp_secure' => isset($_POST['smtp_secure']) ? trim($_POST['smtp_secure']) : '',
			'o_smtp_auth'   => isset($_POST['smtp_auth'])   ? trim($_POST['smtp_auth'])   : 'LOGIN'
		);
		$from_name  = isset($_POST['from_name']) ? trim($_POST['from_name']) : '';
		$from_email = (isset($_POST['from_email']) && trim($_POST['from_email']) !== '' && filter_var(trim($_POST['from_email']), FILTER_VALIDATE_EMAIL))
			? trim($_POST['from_email'])
			: $this->getAdminEmail();
		if (empty($from_email))
		{
			$from_email = $email;
		}
		
		$pjEmail = new pjEmail();
		pjAppController::applyEmailTransport($pjEmail, $post_opt);
		$pjEmail->setContentType('text/html');
		
		$ok = $pjEmail
			->setFrom($from_email, $from_name)
			->setTo($email)
			->setSubject(__('emailTestSubject', true))
			->send(__('emailTestBody', true));
		
		if ($ok)
		{
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => str_replace('{EMAIL}', pjSanitize::html($email), __('emailMsgSentOk', true)) . ' ' . __('emailMsgCheckInbox', true)));
		}
		
		$detail = $pjEmail->getErrorDetail();
		if ($detail === '')
		{
			$detail = (string) $pjEmail->getErrorMessage();
		}
		pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 104, 'text' => __('emailMsgSendFail', true) . ($detail !== '' ? ' (' . pjSanitize::html($detail) . ')' : '')));
	}
	
	public function pjActionBooking()
	{
		$this->checkLogin();
	
		if ($this->isAdmin())
		{
			$pjOptionModel = pjOptionModel::factory()
				->where('t1.foreign_id', $this->getForeignId())
				->orderBy('t1.order ASC')
				->findAll();
	
			$this->set('arr', $pjOptionModel->getData());
			$this->set('o_arr', $pjOptionModel->getDataPair('key'));
	
			$this->appendJs('pjAdminOptions.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionNotification()
	{
		$this->checkLogin();
	
		if ($this->isAdmin())
		{
	
			$arr = pjOptionModel::factory()
				->where('t1.foreign_id', $this->getForeignId())
				->orderBy('t1.order ASC')
				->findAll()
				->getData();
	
			$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang(1, 'pjOption');
	
			$this->set('arr', $arr);
	
			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
				->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
				->where('t2.file IS NOT NULL')
				->orderBy('t1.sort ASC')->findAll()->getData();
	
			$lp_arr = array();
			foreach ($locale_arr as $item)
			{
				$lp_arr[$item['id']."_"] = $item['file'];
			}
			$this->set('lp_arr', $locale_arr);
			$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
	
			$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
			$this->appendJs('pjAdminOptions.js');
		}
	}
	
	public function pjActionBookingForm()
	{
		$this->checkLogin();
	
		if ($this->isAdmin())
		{
			$arr = pjOptionModel::factory()
				->where('t1.foreign_id', $this->getForeignId())
				->orderBy('t1.order ASC')
				->findAll()
				->getData();
	
			$this->set('arr', $arr);
			$this->appendJs('pjAdminOptions.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionTerm()
	{
		$this->checkLogin();
	
		if ($this->isAdmin())
		{
	
			$arr = pjOptionModel::factory()
				->where('t1.foreign_id', $this->getForeignId())
				->orderBy('t1.order ASC')
				->findAll()
				->getData();
	
			$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang(1, 'pjOption');
	
			$this->set('arr', $arr);
	
			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
			->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
			->where('t2.file IS NOT NULL')
			->orderBy('t1.sort ASC')->findAll()->getData();
	
			$lp_arr = array();
			foreach ($locale_arr as $item)
			{
				$lp_arr[$item['id']."_"] = $item['file'];
			}
			$this->set('lp_arr', $locale_arr);
			$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
	
			$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendJs('pjAdminOptions.js');
	
		}
	}
	
	public function pjActionInstall()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$this->appendJs('pjAdminOptions.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionPreview()
	{
		$this->checkLogin();
	
		if ($this->isAdmin())
		{
			$this->appendJs('pjAdminOptions.js');
		} else {
			$this->set('status', 2);
		}
	}

	public function pjActionUpdateTheme()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			pjOptionModel::factory()
				->where('foreign_id', $this->getForeignId())
				->where('`key`', 'o_theme')
				->limit(1)
				->modifyAll(array('value' => 'theme1|theme2|theme3|theme4|theme5|theme6|theme7|theme8|theme9|theme10|theme11::theme' . $_GET['theme']));
			
		}
	}
}
?>