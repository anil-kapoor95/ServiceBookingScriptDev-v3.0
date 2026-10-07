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
	pjUtil::printNotice(__('infoAddExtraTitle', true, false), __('infoAddExtraDesc', true, false));
	$is_update = false;
	include dirname(__FILE__) . '/form.php';
}
?>