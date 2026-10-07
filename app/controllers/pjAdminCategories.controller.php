<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
/**
 * Service categories (admin). Every service belongs to exactly one category;
 * the front end offers a category filter on the "Select services" step.
 */
class pjAdminCategories extends pjAdmin
{
	/**
	 * The default-language title must not be empty / spaces-only.
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
			if (isset($_POST['category_create']))
			{
				$_POST = pjAppController::trimDeep($_POST);
				if (!$this->isValidPost())
				{
					pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminCategories&action=pjActionCreate");
				}
				$id = pjServiceCategoryModel::factory()->setAttributes($_POST)->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					$err = 'AC03';
					if (isset($_POST['i18n']))
					{
						pjMultiLangModel::factory()->saveMultiLang($_POST['i18n'], $id, 'pjServiceCategory', 'data');
					}
				} else {
					$err = 'AC04';
				}
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminCategories&action=pjActionIndex&err=$err");
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
			if (isset($_POST['category_update']))
			{
				$_POST = pjAppController::trimDeep($_POST);
				if (!$this->isValidPost())
				{
					pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminCategories&action=pjActionUpdate&id=" . (int) @$_POST['id']);
				}
				$arr = pjServiceCategoryModel::factory()->find((int) $_POST['id'])->getData();
				if (empty($arr))
				{
					pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminCategories&action=pjActionIndex&err=AC08");
				}
				pjServiceCategoryModel::factory()->where('id', $arr['id'])->limit(1)->modifyAll(array('status' => $_POST['status']));
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $arr['id'], 'pjServiceCategory', 'data');
				}
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminCategories&action=pjActionIndex&err=AC01");
			} else {
				$arr = pjServiceCategoryModel::factory()->find((int) @$_GET['id'])->getData();
				if (empty($arr))
				{
					pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminCategories&action=pjActionIndex&err=AC08");
				}
				$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjServiceCategory');
				$this->set('arr', $arr);
				$this->setLocaleData();
			}
		} else {
			$this->set('status', 2);
		}
	}

	public function pjActionGetCategory()
	{
		$this->setAjax(true);

		if ($this->isXHR())
		{
			$pjCategoryModel = pjServiceCategoryModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjServiceCategory' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left');

			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjCategoryModel->where("(t2.content LIKE '%$q%')");
			}
			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('T', 'F')))
			{
				$pjCategoryModel->where('t1.status', $_GET['status']);
			}

			$column = 'title';
			$direction = 'ASC';
			$allowed_columns = array('title', 'cnt_services', 'status');
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array($_GET['column'], $allowed_columns) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjCategoryModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 20;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = $pjCategoryModel
				->select("t1.*, t2.content AS title, (SELECT COUNT(TS.id) FROM `".pjServiceModel::factory()->getTable()."` AS `TS` WHERE `TS`.category_id=t1.id) AS cnt_services")
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();

			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}

	public function pjActionSaveCategory()
	{
		$this->setAjax(true);

		if ($this->isXHR())
		{
			$pjCategoryModel = pjServiceCategoryModel::factory();
			if (!in_array($_POST['column'], $pjCategoryModel->getI18n()))
			{
				if ($_POST['column'] == 'status' && in_array($_POST['value'], array('T', 'F')))
				{
					$pjCategoryModel->where('id', (int) $_GET['id'])->limit(1)->modifyAll(array('status' => $_POST['value']));
				}
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), (int) $_GET['id'], 'pjServiceCategory', 'data');
			}
		}
		exit;
	}

	/**
	 * A category that still has services cannot be deleted (code 101, the grid shows a message).
	 */
	public function pjActionDeleteCategory()
	{
		$this->setAjax(true);

		if ($this->isXHR())
		{
			$response = array();
			$id = (int) $_GET['id'];
			$cnt = pjServiceModel::factory()->where('category_id', $id)->findCount()->getData();
			if ((int) $cnt > 0)
			{
				$response['code'] = 101;
			} elseif (pjServiceCategoryModel::factory()->setAttributes(array('id' => $id))->erase()->getAffectedRows() == 1)
			{
				pjMultiLangModel::factory()->where('model', 'pjServiceCategory')->where('foreign_id', $id)->eraseAll();
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}

	/**
	 * Bulk delete skips categories that still have services.
	 */
	public function pjActionDeleteCategoryBulk()
	{
		$this->setAjax(true);

		if ($this->isXHR())
		{
			if (isset($_POST['record']) && is_array($_POST['record']) && count($_POST['record']) > 0)
			{
				foreach ($_POST['record'] as $id)
				{
					$id = (int) $id;
					if ($id > 0 && (int) pjServiceModel::factory()->where('category_id', $id)->findCount()->getData() === 0)
					{
						pjServiceCategoryModel::factory()->where('id', $id)->limit(1)->eraseAll();
						pjMultiLangModel::factory()->where('model', 'pjServiceCategory')->where('foreign_id', $id)->eraseAll();
					}
				}
			}
		}
		exit;
	}
}
?>
