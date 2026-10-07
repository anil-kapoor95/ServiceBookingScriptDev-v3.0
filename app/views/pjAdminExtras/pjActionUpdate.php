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
	pjUtil::printNotice(__('infoUpdateExtraTitle', true, false), __('infoUpdateExtraDesc', true, false));
	$is_update = true;
	include dirname(__FILE__) . '/form.php';
}
?>