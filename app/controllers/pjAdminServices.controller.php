<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminServices extends pjAdmin
{
	/**
	 * Server-side guard for the add/edit service forms (the browser validates too): the default-language
	 * title must not be empty/spaces-only, and price and duration must be greater than 0.
	 */
	private function isValidServicePost()
	{
		$default_locale = pjLocaleModel::factory()->where('is_default', 1)->limit(1)->findAll()->getData();
		$default_id = !empty($default_locale) ? (int) $default_locale[0]['id'] : 0;
		if (isset($_POST['i18n']) && is_array($_POST['i18n']) && $default_id > 0)
		{
			if (!isset($_POST['i18n'][$default_id]['title']) || $_POST['i18n'][$default_id]['title'] === '')
			{
				return false;
			}
		}
		$price = isset($_POST['price']) ? str_replace(',', '', $_POST['price']) : '';
		if (!is_numeric($price) || (float) $price <= 0)
		{
			return false;
		}
		if (!isset($_POST['duration']) || !ctype_digit((string) $_POST['duration']) || (int) $_POST['duration'] <= 0)
		{
			return false;
		}
		return true;
	}

	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['service_create']))
			{
				$_POST = pjAppController::trimDeep($_POST);
				if (!$this->isValidServicePost())
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminServices&action=pjActionCreate");
				}
				$pjServiceModel = pjServiceModel::factory();
				
				$id = $pjServiceModel->setAttributes($_POST)->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					$err = 'AS03';
					if (isset($_POST['i18n']))
					{
						pjMultiLangModel::factory()->saveMultiLang($_POST['i18n'], $id, 'pjService', 'data');
					}
				} else {
					$err = 'AS04';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminServices&action=pjActionIndex&err=$err");
			} else {
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
		
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('pjAdminServices.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
		
	public function pjActionDeleteService()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			$pjServiceModel = pjServiceModel::factory();
			if ($pjServiceModel->reset()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				pjMultiLangModel::factory()->where('model', 'pjService')->where('foreign_id', $_GET['id'])->eraseAll();
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteServiceBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				$pjServiceModel = pjServiceModel::factory();
				$pjServiceModel->reset()->whereIn('id', $_POST['record'])->eraseAll();
				pjMultiLangModel::factory()->where('model', 'pjService')->whereIn('foreign_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionGetService()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjServiceModel = pjServiceModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjService' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
				->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjService' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'description'", 'left');
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjServiceModel->where("(t2.content LIKE '%$q%' OR t3.content LIKE '%$q%')");
			}
			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('T', 'F')))
			{
				$pjServiceModel->where('t1.status', $_GET['status']);
			}
			
			$column = 'title';
			$direction = 'ASC';
			$allowed_columns = array('title', 'price', 'duration', 'cnt_bookings', 'status');
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array($_GET['column'], $allowed_columns) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjServiceModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 20;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}
			
			$data = $pjServiceModel
				->select("t1.*, t2.content AS title, (SELECT COUNT(TBS.booking_id) FROM `".pjBookingServiceModel::factory()->getTable()."` AS `TBS` WHERE `TBS`.service_id=t1.id) AS cnt_bookings")
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();
			foreach($data as $k => $v)
			{
				$v['price'] = pjUtil::formatCurrencySign($v['price'], $this->option_arr['o_currency']);
				$v['duration'] = $v['duration'] . ' ' . __('lblMinutes', true);
				$data[$k] = $v;
			}
				
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
		
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminServices.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveService()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjServiceModel = pjServiceModel::factory();
			if (!in_array($_POST['column'], $pjServiceModel->getI18n()))
			{
				$pjServiceModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjService', 'data');
			}
		}
		exit;
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			if (isset($_POST['service_update']))
			{
				$_POST = pjAppController::trimDeep($_POST);
				if (!$this->isValidServicePost())
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminServices&action=pjActionUpdate&id=" . (int) @$_POST['id']);
				}
				$pjServiceModel = pjServiceModel::factory();
				
				$err = 'AS01';
				
				$arr = $pjServiceModel->find($_POST['id'])->getData();
				if (empty($arr))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBooths&action=pjActionIndex&err=AS08");
				}
				
				$data = array();
				
				pjServiceModel::factory()->where('id', $_POST['id'])->limit(1)->modifyAll(array_merge($_POST, $data));
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $_POST['id'], 'pjService', 'data');
				}

				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminServices&action=pjActionIndex&err=$err");
				
			} else {
				$arr = pjServiceModel::factory()->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminServices&action=pjActionIndex&err=AS08");
				}
				$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjService');
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
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('pjAdminServices.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
}
?>