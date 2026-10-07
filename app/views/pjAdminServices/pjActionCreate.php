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
	
	pjUtil::printNotice(__('infoAddServiceTitle', true, false), __('infoAddServiceDesc', true, false)); 
	?>
	
	<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
	<div class="multilang"></div>
	<?php endif; ?>
	
	<div class="clear_both">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminServices&amp;action=pjActionCreate" method="post" id="frmCreateService" class="form pj-form" autocomplete="off" enctype="multipart/form-data">
			<input type="hidden" name="service_create" value="1" />
			<input type="hidden" name="csrf_token" value="<?php echo pjAppController::getCsrfToken(); ?>" />
			<?php
			foreach ($tpl['lp_arr'] as $v)
			{
			?>
				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
					<label class="title"><?php __('lblTitle'); ?></label>
					<span class="inline_block">
						<input type="text" id="i18n_title_<?php echo $v['id'];?>" name="i18n[<?php echo $v['id']; ?>][title]" class="pj-form-field w450<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" lang="<?php echo $v['id']; ?>" data-msg-required="<?php __('pj_field_required');?>"/>
						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
						<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						<?php endif; ?>
					</span>
				</p>
				<?php
			}
			
			foreach ($tpl['lp_arr'] as $v)
			{
			?>
				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
					<label class="title"><?php __('lblDescription'); ?></label>
					<span class="inline_block">
						<textarea id="i18n_description_<?php echo $v['id'];?>" name="i18n[<?php echo $v['id']; ?>][description]" class="pj-form-field w450 h100" lang="<?php echo $v['id']; ?>"></textarea>
						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
						<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						<?php endif; ?>
					</span>
				</p>
				<?php
			}
			?>
			<?php include dirname(__FILE__) . '/category_extras.php'; ?>
			<p>
				<label class="title"><?php __('lblPrice'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" id="price" name="price" class="pj-form-field number gtzero w80 required" data-msg-gtzero="<?php __('pj_greater_than_zero'); ?>" data-msg-number="<?php __('pj_number_validation');?>" data-msg-required="<?php __('pj_field_required');?>"/>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblDuration', false, true); ?></label>
				<span class="inline_block">
					<input type="text" id="duration" name="duration" class="pj-form-field field-int digits gtzero w80 required" data-msg-gtzero="<?php __('pj_greater_than_zero'); ?>" data-msg-number="<?php __('pj_digits_validation');?>" data-msg-required="<?php __('pj_field_required');?>"/>
					<?php __('lblMinutes');?>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblStatus'); ?></label>
				<span class="inline_block">
					<select name="status" id="status" class="pj-form-field required" data-msg-required="<?php __('pj_field_required');?>">
						<option value="">-- <?php __('lblChoose'); ?>--</option>
						<?php
						foreach (__('u_statarr', true) as $k => $v)
						{
							?><option value="<?php echo $k; ?>"<?php echo $k == 'T' ? ' selected="selected"' : null;?>><?php echo $v; ?></option><?php
						}
						?>
					</select>
				</span>
			</p>
			<p>
				<label class="title">&nbsp;</label>
				<span class="inline_block">
					<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
					<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminServices&action=pjActionIndex';" />
				</span>
			</p>
		</form>
	</div>
	
	<script type="text/javascript">
	var pjLocale = pjLocale || {};
	var myLabel = myLabel || {};
	myLabel.choose = "-- <?php __('lblChoose'); ?> --";
	var locale_array = new Array(); 
	pjLocale.langs = <?php echo $tpl['locale_str']; ?>;
	pjLocale.flagPath = "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/";
	
	myLabel.field_required = "<?php __('pj_field_required'); ?>";
	<?php
	foreach ($tpl['lp_arr'] as $v)
	{
		?>locale_array.push(<?php echo $v['id'];?>);<?php
	} 
	?>
	myLabel.locale_array = locale_array;
	(function ($) {
		$(function() {
			$(".multilang").multilang({
				langs: pjLocale.langs,
				flagPath: pjLocale.flagPath,
				select: function (event, ui) {
					
				}
			});
		});
	})(jQuery_1_8_2);
	</script>
	<?php
}
?>