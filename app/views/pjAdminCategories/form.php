<?php
/* shared add / edit form; $is_update is set by the including view */
$arr = isset($tpl['arr']) ? $tpl['arr'] : array();
?>
<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
<div class="multilang"></div>
<?php endif; ?>

<div class="clear_both">
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCategories&amp;action=<?php echo $is_update ? 'pjActionUpdate' : 'pjActionCreate'; ?>" method="post" id="<?php echo $is_update ? 'frmUpdateCategory' : 'frmCreateCategory'; ?>" class="form pj-form pj-catalog-form" autocomplete="off">
		<input type="hidden" name="category_<?php echo $is_update ? 'update' : 'create'; ?>" value="1" />
		<input type="hidden" name="csrf_token" value="<?php echo pjAppController::getCsrfToken(); ?>" />
		<?php if ($is_update) : ?><input type="hidden" name="id" value="<?php echo (int) $arr['id']; ?>" /><?php endif; ?>
<?php
foreach ($tpl['lp_arr'] as $v)
{
	?>
	<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
		<label class="title"><?php __('lblTitle'); ?></label>
		<span class="inline_block">
			<input type="text" id="i18n_title_<?php echo $v['id'];?>" name="i18n[<?php echo $v['id']; ?>][title]" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" value="<?php echo pjSanitize::html(@$tpl['arr']['i18n'][$v['id']]['title']); ?>" lang="<?php echo $v['id']; ?>" data-msg-required="<?php __('pj_field_required');?>"/>
			<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
			<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
			<?php endif; ?>
		</span>
	</p>
	<?php
}
?>
		<p>
			<label class="title"><?php __('lblStatus'); ?></label>
			<span class="inline_block">
				<select name="status" id="status" class="pj-form-field required" data-msg-required="<?php __('pj_field_required');?>">
					<?php
					foreach (__('u_statarr', true) as $k => $v)
					{
						$selected = $is_update ? ($k == $arr['status']) : ($k == 'T');
						?><option value="<?php echo $k; ?>"<?php echo $selected ? ' selected="selected"' : NULL;?>><?php echo $v; ?></option><?php
					}
					?>
				</select>
			</span>
		</p>
		<p>
			<label class="title">&nbsp;</label>
			<span class="inline_block">
				<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
				<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminCategories&action=pjActionIndex';" />
			</span>
		</p>
	</form>
</div>

<script type="text/javascript">
var pjLocale = pjLocale || {};
var myLabel = myLabel || {};
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
			select: function (event, ui) {}
		});
	});
})(jQuery_1_8_2);
</script>
