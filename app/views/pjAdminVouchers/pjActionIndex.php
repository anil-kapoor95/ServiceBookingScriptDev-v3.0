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
	if (isset($_GET['err']))
	{
		$msg = array(
			'AV03' => array('infoVoucherAddedTitle', 'infoVoucherAddedDesc'),
			'AV01' => array('infoVoucherUpdatedTitle', 'infoVoucherUpdatedDesc'),
			'AV04' => array('infoVoucherFailedTitle', 'infoVoucherFailedDesc'),
			'AV09' => array('infoVoucherFailedTitle', 'infoVoucherFailedDesc'),
			'AV10' => array('infoVoucherExistsTitle', 'infoVoucherExistsDesc'),
			'AV08' => array('infoVoucherNotFoundTitle', 'infoVoucherNotFoundDesc')
		);
		if (isset($msg[$_GET['err']]))
		{
			pjUtil::printNotice(__($msg[$_GET['err']][0], true, false), __($msg[$_GET['err']][1], true, false));
		}
	}

	pjUtil::printNotice(__('infoVouchersTitle', true, false), __('infoVouchersDesc', true, false));
	?>

	<div class="b10">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="float_left pj-form r10">
			<input type="hidden" name="controller" value="pjAdminVouchers" />
			<input type="hidden" name="action" value="pjActionCreate" />
			<input type="submit" class="pj-button" value="<?php __('btnAddVoucher'); ?>" />
		</form>
		<form action="" method="get" class="float_left pj-form frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w150" placeholder="<?php __('btnSearch'); ?>" />
		</form>
		<br class="clear_both" />
	</div>

	<div id="grid" data-entity="voucher"></div>

	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.queryString = "";

	var myLabel = myLabel || {};
	myLabel.code = "<?php echo addslashes(__('lblVoucherCode', true)); ?>";
	myLabel.discount = "<?php echo addslashes(__('lblVoucherDiscount', true)); ?>";
	myLabel.type = "<?php echo addslashes(__('lblVoucherType', true)); ?>";
	myLabel.valid = "<?php echo addslashes(__('lblVoucherValid', true)); ?>";
	myLabel.services = "<?php echo addslashes(__('lblVoucherServices', true)); ?>";
	myLabel.discount_max = "<?php echo addslashes(__('lblVoucherDiscountInvalid', true)); ?>";
	myLabel.code_exists = "<?php echo addslashes(__('lblVoucherCodeExists', true)); ?>";
	myLabel.field_required = "<?php echo addslashes(__('pj_field_required', true)); ?>";
	myLabel.delete_selected = "<?php __('delete_selected'); ?>";
	myLabel.delete_confirmation = "<?php __('delete_confirmation'); ?>";
	</script>
	<?php
}
?>
