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
			'AE03' => array('infoExtraAddedTitle', 'infoExtraAddedDesc'),
			'AE01' => array('infoExtraUpdatedTitle', 'infoExtraUpdatedDesc'),
			'AE04' => array('infoExtraFailedTitle', 'infoExtraFailedDesc'),
			'AE08' => array('infoExtraFailedTitle', 'infoExtraFailedDesc')
		);
		if (isset($msg[$_GET['err']]))
		{
			pjUtil::printNotice(__($msg[$_GET['err']][0], true, false), __($msg[$_GET['err']][1], true, false));
		}
	}
	$filter = __('filter', true, false);

	pjUtil::printNotice(__('infoExtrasTitle', true, false), __('infoExtrasDesc', true, false));
	?>

	<div class="b10">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="float_left pj-form r10">
			<input type="hidden" name="controller" value="pjAdminExtras" />
			<input type="hidden" name="action" value="pjActionCreate" />
			<input type="submit" class="pj-button" value="<?php __('btnAddExtra'); ?>" />
		</form>
		<form action="" method="get" class="float_left pj-form frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w150" placeholder="<?php __('btnSearch'); ?>" />
		</form>

		<div class="float_right t5">
			<a href="#" class="pj-button btn-all"><?php __('lblAll'); ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="T"><?php echo $filter['active']; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="F"><?php echo $filter['inactive']; ?></a>
		</div>
		<br class="clear_both" />
	</div>

	<div id="grid" data-entity="extra"></div>

	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.queryString = "";

	var myLabel = myLabel || {};
	myLabel.title = "<?php __('lblTitle'); ?>";
	myLabel.price = "<?php __('lblPrice'); ?>";
	myLabel.duration = "<?php __('lblDuration'); ?>";
	myLabel.services = "<?php __('lblServicesCount'); ?>";
	myLabel.status = "<?php __('lblStatus'); ?>";
	myLabel.active = "<?php echo $filter['active']; ?>";
	myLabel.inactive = "<?php echo $filter['inactive']; ?>";
	myLabel.delete_selected = "<?php __('delete_selected'); ?>";
	myLabel.delete_confirmation = "<?php __('delete_confirmation'); ?>";
	myLabel.category_in_use = "<?php echo addslashes(__('msgCategoryInUse', true)); ?>";
	</script>
	<?php
}
?>