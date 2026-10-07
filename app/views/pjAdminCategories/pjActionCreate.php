<?php
if (isset($tpl['status']))
{
	$status = __('status', true);
	switch ($tpl['status'])
	{
		case 2:
			pjUtil::printNotice(NULL, $status[2]);
			break;
	}
} else {
	pjUtil::printNotice(__('infoAddCategoryTitle', true, false), __('infoAddCategoryDesc', true, false));
	$is_update = false;
	include dirname(__FILE__) . '/form.php';
}
?>