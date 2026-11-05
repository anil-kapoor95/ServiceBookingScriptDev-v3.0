<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
/**
 * Vouchers (admin): discount codes customers can type on the booking form.
 * Same fields and rules as the voucher section of the Time Slots Booking Calendar.
 */
class pjAdminVouchers extends pjAdmin
{
	/**
	 * Services posted with the form (empty = the voucher is valid for every service); set by getPostedData().
	 */
	private $service_ids = array();
	
	/**
	 * All services (id, title in the admin language) for the "Services" box of the form and the list.
	 */
	private function getServiceList()
	{
		return pjServiceModel::factory()
			->select('t1.id, t2.content AS title')
			->join('pjMultiLang', "t2.model='pjService' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='" . $this->getLocaleId() . "'", 'left outer')
			->orderBy("t1.sort_order ASC, t1.id ASC")
			->findAll()
			->getData();
	}
	
	private function saveServices($voucher_id)
	{
		$pjVoucherServiceModel = pjVoucherServiceModel::factory();
		$pjVoucherServiceModel->where('voucher_id', (int) $voucher_id)->eraseAll();
		foreach ($this->service_ids as $service_id)
		{
			$pjVoucherServiceModel->reset()->setAttributes(array('voucher_id' => (int) $voucher_id, 'service_id' => (int) $service_id))->insert();
		}
	}
	
	/**
	 * Time format of the site (o_time_format) -> how the hour / minute / am-pm selects are built and read.
	 */
	public static function getTimeSetup($option_arr)
	{
		$fmt = isset($option_arr['o_time_format']) ? $option_arr['o_time_format'] : 'H:i';
		$ampm = 0;
		if (strpos($fmt, 'a') !== false)
		{
			$ampm = 1; // lower case am / pm
		} elseif (strpos($fmt, 'A') !== false) {
			$ampm = 2; // upper case AM / PM
		}
		return array('ampm' => $ampm, 'h12' => ($ampm > 0 || strpos($fmt, 'h') !== false || strpos($fmt, 'g') !== false));
	}
	
	/**
	 * "hour / minute / am-pm" posted with a prefix (f_from, p_to, r_from ...) -> H:i:s
	 */
	private function postedTime($prefix)
	{
		$h = isset($_POST[$prefix . '_hour']) ? (int) $_POST[$prefix . '_hour'] : 0;
		$m = isset($_POST[$prefix . '_minute']) ? (int) $_POST[$prefix . '_minute'] : 0;
		$setup = self::getTimeSetup($this->option_arr);
		if ($setup['ampm'] > 0)
		{
			$ap = isset($_POST[$prefix . '_ampm']) ? strtolower($_POST[$prefix . '_ampm']) : 'am';
			if ($ap == 'pm' && $h < 12)
			{
				$h += 12;
			} elseif ($ap == 'am' && $h == 12) {
				$h = 0;
			}
		}
		if ($h < 0 || $h > 23 || $m < 0 || $m > 59)
		{
			return false;
		}
		return sprintf('%02d:%02d:00', $h, $m);
	}
	
	private function postedDate($key)
	{
		if (!isset($_POST[$key]) || trim($_POST[$key]) === '')
		{
			return false;
		}
		$iso = pjUtil::formatDate($_POST[$key], $this->option_arr['o_date_format']);
		if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', (string) $iso, $m) || !checkdate((int) $m[2], (int) $m[3], (int) $m[1]))
		{
			return false;
		}
		return $iso;
	}
	
	/**
	 * Validates the posted form. Returns the row to save, or an error code:
	 * 'blank' | 'discount' | 'valid' | 'services' | 'time' | 'exists'
	 */
	private function getPostedData($id = 0)
	{
		$code = isset($_POST['code']) ? trim((string) $_POST['code']) : '';
		$discount = isset($_POST['discount']) ? str_replace(',', '', trim((string) $_POST['discount'])) : '';
		if ($code === '' || $discount === '' || strlen($code) > 255)
		{
			return 'blank';
		}
		$type = isset($_POST['type']) && in_array($_POST['type'], array('amount', 'percent')) ? $_POST['type'] : false;
		if ($type === false || !is_numeric($discount) || (float) $discount <= 0 || (float) $discount > 9999999 || ($type == 'percent' && (float) $discount > 100))
		{
			return 'discount';
		}
		$valid = isset($_POST['valid']) && in_array($_POST['valid'], array('fixed', 'period', 'recurring')) ? $_POST['valid'] : false;
		if ($valid === false)
		{
			return 'valid';
		}
		// services: "all" (nothing stored) or a real selection; "selected" without any service is refused,
		// it must never turn into "all services" silently
		$this->service_ids = array();
		if (isset($_POST['services_scope']) && $_POST['services_scope'] == 'selected')
		{
			$existing = array();
			foreach ($this->getServiceList() as $service)
			{
				$existing[] = (int) $service['id'];
			}
			$posted = isset($_POST['service_id']) && is_array($_POST['service_id']) ? $_POST['service_id'] : array();
			foreach ($posted as $service_id)
			{
				if (in_array((int) $service_id, $existing, true))
				{
					$this->service_ids[] = (int) $service_id;
				}
			}
			$this->service_ids = array_values(array_unique($this->service_ids));
			if (empty($this->service_ids))
			{
				return 'services';
			}
		}
		$data = array(
			'code' => $code,
			'type' => $type,
			'discount' => round((float) $discount, 2),
			'valid' => $valid,
			'date_from' => ':NULL', 'date_to' => ':NULL', 'every' => ':NULL'
		);
		switch ($valid)
		{
			case 'fixed':
				$date = $this->postedDate('f_date');
				$from = $this->postedTime('f_from');
				$to = $this->postedTime('f_to');
				if ($date === false || $from === false || $to === false || $to <= $from)
				{
					return 'time';
				}
				$data['date_from'] = $date;
				$data['date_to'] = $date;
				$data['time_from'] = $from;
				$data['time_to'] = $to;
				break;
			case 'period':
				$date_from = $this->postedDate('p_date_from');
				$date_to = $this->postedDate('p_date_to');
				$from = $this->postedTime('p_from');
				$to = $this->postedTime('p_to');
				if ($date_from === false || $date_to === false || $from === false || $to === false || strtotime($date_to . ' ' . $to) <= strtotime($date_from . ' ' . $from))
				{
					return 'time';
				}
				$data['date_from'] = $date_from;
				$data['date_to'] = $date_to;
				$data['time_from'] = $from;
				$data['time_to'] = $to;
				break;
			case 'recurring':
				$every = isset($_POST['r_every']) && in_array($_POST['r_every'], array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')) ? $_POST['r_every'] : false;
				$from = $this->postedTime('r_from');
				$to = $this->postedTime('r_to');
				if ($every === false || $from === false || $to === false || $to <= $from)
				{
					return 'time';
				}
				$data['every'] = $every;
				$data['time_from'] = $from;
				$data['time_to'] = $to;
				break;
		}
		$pjVoucherModel = pjVoucherModel::factory()->where('t1.code', $code);
		if ((int) $id > 0)
		{
			$pjVoucherModel->where('t1.id !=', (int) $id);
		}
		if ((int) $pjVoucherModel->findCount()->getData() > 0)
		{
			return 'exists';
		}
		return $data;
	}
	
	private function errorToRedirectCode($result)
	{
		return $result === 'exists' ? 'AV10' : 'AV09';
	}
	
	private function setFormData()
	{
		$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
		$this->appendJs('pjAdminVouchers.js');
		$this->set('service_arr', $this->getServiceList());
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminVouchers.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['voucher_create']))
			{
				$_POST = pjAppController::trimDeep($_POST);
				$data = $this->getPostedData();
				if (!is_array($data))
				{
					pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminVouchers&action=pjActionIndex&err=" . $this->errorToRedirectCode($data));
				}
				$id = pjVoucherModel::factory()->setAttributes($data)->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					$this->saveServices($id);
				}
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminVouchers&action=pjActionIndex&err=" . ($id !== false && (int) $id > 0 ? 'AV03' : 'AV04'));
			} else {
				$this->setFormData();
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['voucher_update']))
			{
				$_POST = pjAppController::trimDeep($_POST);
				$arr = pjVoucherModel::factory()->find((int) @$_POST['id'])->getData();
				if (empty($arr))
				{
					pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminVouchers&action=pjActionIndex&err=AV08");
				}
				$data = $this->getPostedData($arr['id']);
				if (!is_array($data))
				{
					pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminVouchers&action=pjActionIndex&err=" . $this->errorToRedirectCode($data));
				}
				pjVoucherModel::factory()->where('id', $arr['id'])->limit(1)->modifyAll($data);
				$this->saveServices($arr['id']);
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminVouchers&action=pjActionIndex&err=AV01");
			} else {
				$arr = pjVoucherModel::factory()->find((int) @$_GET['id'])->getData();
				if (empty($arr))
				{
					pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminVouchers&action=pjActionIndex&err=AV08");
				}
				$this->set('arr', $arr);
				$this->set('voucher_service_ids', pjVoucherModel::getServiceIds($arr['id']));
				$this->setFormData();
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	/**
	 * jQuery validate "remote" rule: prints true when the code is free.
	 */
	public function pjActionCheckCode()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$code = isset($_GET['code']) ? trim((string) $_GET['code']) : '';
			if ($code === '')
			{
				echo 'false';
				exit;
			}
			$pjVoucherModel = pjVoucherModel::factory()->where('t1.code', $code);
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$pjVoucherModel->where('t1.id !=', (int) $_GET['id']);
			}
			echo (int) $pjVoucherModel->findCount()->getData() === 0 ? 'true' : 'false';
		}
		exit;
	}
	
	public function pjActionGetVoucher()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$pjVoucherModel = pjVoucherModel::factory();
			
			if (isset($_GET['q']) && trim($_GET['q']) !== '')
			{
				$q = pjObject::escapeString(str_replace(array('%', '_'), array('\%', '\_'), trim($_GET['q'])));
				$pjVoucherModel->where("t1.code LIKE '%$q%'");
			}
			
			$column = 'code';
			$direction = 'ASC';
			$allowed_columns = array('code', 'discount', 'type', 'valid');
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array($_GET['column'], $allowed_columns) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}
			
			$total = $pjVoucherModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 20;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}
			
			$data = $pjVoucherModel
				->select('t1.id, t1.code, t1.discount, t1.type, t1.valid')
				->orderBy("`$column` $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();
			
			$types = __('voucher_types', true);
			$valids = __('voucher_valids', true);
			$currency_sign = pjUtil::formatCurrencySign(NULL, $this->option_arr['o_currency'], "");
			// services of the vouchers on this page
			$voucher_services = array();
			$voucher_ids = array();
			foreach ($data as $v)
			{
				$voucher_ids[] = (int) $v['id'];
			}
			if (!empty($voucher_ids))
			{
				$rows = pjVoucherServiceModel::factory()->whereIn('t1.voucher_id', $voucher_ids)->findAll()->getData();
				foreach ($rows as $row)
				{
					$voucher_services[(int) $row['voucher_id']][] = (int) $row['service_id'];
				}
			}
			$service_titles = array();
			foreach ($this->getServiceList() as $service)
			{
				$service_titles[(int) $service['id']] = $service['title'];
			}
			foreach ($data as $k => $v)
			{
				if (empty($voucher_services[(int) $v['id']]))
				{
					$data[$k]['services'] = __('lblVoucherServicesAll', true);
				} else {
					$names = array();
					foreach ($voucher_services[(int) $v['id']] as $service_id)
					{
						if (isset($service_titles[$service_id]))
						{
							$names[] = $service_titles[$service_id];
						}
					}
					$data[$k]['services'] = pjSanitize::html(implode(', ', $names));
				}
				// the sign is shown after the discount in the grid; the cell itself still edits the plain number
				$data[$k]['sign'] = $v['type'] == 'percent' ? '%' : $currency_sign;
				$data[$k]['type'] = isset($types[$v['type']]) ? $types[$v['type']] : $v['type'];
				$data[$k]['valid'] = isset($valids[$v['valid']]) ? $valids[$v['valid']] : $v['valid'];
			}
			
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	/**
	 * Inline edit in the grid (code and discount).
	 */
	public function pjActionSaveVoucher()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$arr = pjVoucherModel::factory()->find(isset($_GET['id']) ? (int) $_GET['id'] : 0)->getData();
			if (!$this->isAdmin() || empty($arr) || !isset($_POST['column']) || !isset($_POST['value']))
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
			}
			$value = trim((string) $_POST['value']);
			if ($_POST['column'] == 'code')
			{
				if ($value === '' || strlen($value) > 255)
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 105, 'text' => ''));
				}
				if ((int) pjVoucherModel::factory()->where('t1.code', $value)->where('t1.id !=', $arr['id'])->findCount()->getData() > 0)
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 106, 'text' => ''));
				}
				pjVoucherModel::factory()->where('id', $arr['id'])->limit(1)->modifyAll(array('code' => $value));
			} elseif ($_POST['column'] == 'discount') {
				$value = str_replace(',', '', $value);
				if ($value === '' || !is_numeric($value) || (float) $value <= 0 || (float) $value > 9999999)
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 105, 'text' => ''));
				}
				if ($arr['type'] == 'percent' && (float) $value > 100)
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 104, 'text' => ''));
				}
				pjVoucherModel::factory()->where('id', $arr['id'])->limit(1)->modifyAll(array('discount' => round((float) $value, 2)));
			} else {
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
			}
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 201, 'text' => ''));
		}
		exit;
	}
	
	public function pjActionDeleteVoucher()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$response = array();
			$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
			if ($this->isAdmin() && $id > 0 && pjVoucherModel::factory()->find($id)->getData())
			{
				pjVoucherModel::factory()->where('id', $id)->limit(1)->eraseAll();
				pjVoucherServiceModel::factory()->where('voucher_id', $id)->eraseAll();
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteVoucherBulk()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			if ($this->isAdmin() && isset($_POST['record']) && is_array($_POST['record']) && count($_POST['record']) > 0)
			{
				$ids = array_filter(array_map('intval', $_POST['record']));
				if (!empty($ids))
				{
					pjVoucherModel::factory()->whereIn('id', $ids)->eraseAll();
					pjVoucherServiceModel::factory()->whereIn('voucher_id', $ids)->eraseAll();
				}
			}
		}
		exit;
	}
}
?>
