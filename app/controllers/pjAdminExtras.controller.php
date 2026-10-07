<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
/**
 * Extras / add-ons (admin). An extra is created once and then ticked on every
 * service it can be added to; customers pick them on the "Select services" step
 * and their price and duration are added to the booking.
 */
class pjAdminExtras extends pjAdmin
{
	/**
	 * Default-language title required; price must be a number >= 0; duration a whole number >= 0 (minutes).
	 */
	private function isValidPost()
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
		if (!is_numeric($price) || (float) $price < 0)
		{
			return false;
		}
		if (!isset($_POST['duration']) || !ctype_digit((string) $_POST['duration']))
		{
			return false;
		}
		return isset($_POST['status']) && in_array($_POST['status'], array('T', 'F'));
	}

	private function setLocaleData()
	{
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
		$this->appendJs('pjAdminCatalog.js');
	}

	public function pjActionIndex()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminCatalog.js');
		} else {
			$this->set('status', 2);
		}
	}

	public function pjActionCreate()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			if (isset($_POST['extra_create']))
			{
				$_POST = pjAppController::trimDeep($_POST);
				if (!$this->isValidPost())
				{
					pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminExtras&action=pjActionCreate");
				}
				$_POST['price'] = str_replace(',', '', $_POST['price']);
				$id = pjExtraModel::factory()->setAttributes($_POST)->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					$err = 'AE03';
					if (isset($_POST['i18n']))
					{
						pjMultiLangModel::factory()->saveMultiLang($_POST['i18n'], $id, 'pjExtra', 'data');
					}
				} else {
					$err = 'AE04';
				}
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminExtras&action=pjActionIndex&err=$err");
			} else {
				$this->setLocaleData();
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
			if (isset($_POST['extra_update']))
			{
				$_POST = pjAppController::trimDeep($_POST);
				if (!$this->isValidPost())
				{
					pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminExtras&action=pjActionUpdate&id=" . (int) @$_POST['id']);
				}
				$arr = pjExtraModel::factory()->find((int) $_POST['id'])->getData();
				if (empty($arr))
				{
					pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminExtras&action=pjActionIndex&err=AE08");
				}
				pjExtraModel::factory()->where('id', $arr['id'])->limit(1)->modifyAll(array(
					'price' => str_replace(',', '', $_POST['price']),
					'duration' => (int) $_POST['duration'],
					'status' => $_POST['status']
				));
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $arr['id'], 'pjExtra', 'data');
				}
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminExtras&action=pjActionIndex&err=AE01");
			} else {
				$arr = pjExtraModel::factory()->find((int) @$_GET['id'])->getData();
				if (empty($arr))
				{
					pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminExtras&action=pjActionIndex&err=AE08");
				}
				$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjExtra');
				$this->set('arr', $arr);
				$this->setLocaleData();
			}
		} else {
			$this->set('status', 2);
		}
	}

	public function pjActionGetExtra()
	{
		$this->setAjax(true);

		if ($this->isXHR())
		{
			$pjExtraModel = pjExtraModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjExtra' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
				->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjExtra' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'description'", 'left');

			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjExtraModel->where("(t2.content LIKE '%$q%' OR t3.content LIKE '%$q%')");
			}
			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('T', 'F')))
			{
				$pjExtraModel->where('t1.status', $_GET['status']);
			}

			$column = 'title';
			$direction = 'ASC';
			$allowed_columns = array('title', 'price', 'duration', 'cnt_services', 'status');
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array($_GET['column'], $allowed_columns) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjExtraModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 20;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = $pjExtraModel
				->select("t1.*, t2.content AS title, (SELECT COUNT(TSE.id) FROM `".pjServiceExtraModel::factory()->getTable()."` AS `TSE` WHERE `TSE`.extra_id=t1.id) AS cnt_services")
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();
			foreach ($data as $k => $v)
			{
				$v['price'] = pjUtil::formatCurrencySign($v['price'], $this->option_arr['o_currency']);
				$v['duration'] = $v['duration'] . ' ' . __('lblMinutes', true);
				$data[$k] = $v;
			}

			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}

	public function pjActionSaveExtra()
	{
		$this->setAjax(true);

		if ($this->isXHR())
		{
			$pjExtraModel = pjExtraModel::factory();
			if (!in_array($_POST['column'], $pjExtraModel->getI18n()))
			{
				if ($_POST['column'] == 'status' && in_array($_POST['value'], array('T', 'F')))
				{
					$pjExtraModel->where('id', (int) $_GET['id'])->limit(1)->modifyAll(array('status' => $_POST['value']));
				}
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), (int) $_GET['id'], 'pjExtra', 'data');
			}
		}
		exit;
	}

	private function eraseExtra($id)
	{
		pjExtraModel::factory()->where('id', $id)->limit(1)->eraseAll();
		pjMultiLangModel::factory()->where('model', 'pjExtra')->where('foreign_id', $id)->eraseAll();
		pjServiceExtraModel::factory()->where('extra_id', $id)->eraseAll();
	}

	public function pjActionDeleteExtra()
	{
		$this->setAjax(true);

		if ($this->isXHR())
		{
			$response = array();
			$id = (int) $_GET['id'];
			if ($id > 0 && pjExtraModel::factory()->find($id)->getData())
			{
				$this->eraseExtra($id);
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}

	public function pjActionDeleteExtraBulk()
	{
		$this->setAjax(true);

		if ($this->isXHR())
		{
			if (isset($_POST['record']) && is_array($_POST['record']) && count($_POST['record']) > 0)
			{
				foreach ($_POST['record'] as $id)
				{
					if ((int) $id > 0)
					{
						$this->eraseExtra((int) $id);
					}
				}
			}
		}
		exit;
	}
}
?>
