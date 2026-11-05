<?php
/**
 * Voucher box of the checkout step.
 * Expects $tpl['voucher_arr'] (the applied voucher row, or empty) set by pjFrontPublic::pjActionCheckout().
 * The "Have a voucher code?" link is Theme 11 only (the other themes always show the box).
 * Texts come from the label system (Options > Languages); the fallbacks only matter before the SQL update is run.
 */
$pjSbsLbl = function ($key, $default)
{
	$v = __($key, true);
	return (is_string($v) && $v !== '' && $v !== $key) ? $v : $default;
};
$pjSbsVoucherApplied = !empty($tpl['voucher_arr']);
$pjSbsVoucherCode = $pjSbsVoucherApplied && isset($tpl['voucher_arr']['code']) ? $tpl['voucher_arr']['code'] : '';
$pjSbsVoucherMsgs = __('apply_voucher_msg', true);
$pjSbsVoucherOk = is_array($pjSbsVoucherMsgs) && !empty($pjSbsVoucherMsgs[200]) ? $pjSbsVoucherMsgs[200] : 'Voucher code is applied.';
?>
<a href="#" class="pjSbs11-voucher-toggle pjSbsVoucherToggle"<?php echo $pjSbsVoucherApplied ? ' style="display: none;"' : NULL; ?>><span class="glyphicon glyphicon-tag"></span> <?php echo pjSanitize::html($pjSbsLbl('front_voucher_toggle', 'Have a voucher code?')); ?></a>
<div class="pjSbs-voucher<?php echo $pjSbsVoucherApplied ? '' : ' pjSbs-voucher-collapsed'; ?>">
	<div class="pjSbs-services-form-title"><?php echo pjSanitize::html($pjSbsLbl('front_voucher', 'Voucher')); ?></div><!-- /.pjSbs-services-form-title -->

	<div class="row">
		<div class="col-sm-8 col-xs-12">
			<div class="form-group">
				<label for="pjSbsVoucherCodeField"><?php echo pjSanitize::html($pjSbsLbl('front_voucher_code', 'Voucher code')); ?></label>

				<input type="text" id="pjSbsVoucherCodeField" class="form-control<?php echo $pjSbsVoucherApplied ? ' pjSbsLocked' : NULL; ?>"<?php echo $pjSbsVoucherApplied ? ' readonly="readonly"' : NULL; ?> maxlength="255" autocomplete="off" value="<?php echo pjSanitize::html($pjSbsVoucherCode); ?>" />
			</div><!-- /.form-group -->
		</div><!-- /.col-sm-8 -->

		<div class="col-sm-4 col-xs-12">
			<div class="form-group">
				<label class="pjSbs-voucher-spacer">&nbsp;</label>

				<a href="#" class="btn btn-primary btn-block pjSbsApplyCode" style="display: <?php echo $pjSbsVoucherApplied ? 'none' : ''; ?>;"><?php echo pjSanitize::html($pjSbsLbl('front_btn_apply', 'Apply')); ?></a>
				<a href="#" class="btn btn-primary btn-block pjSbsRemoveCode" style="display: <?php echo $pjSbsVoucherApplied ? '' : 'none'; ?>;"><?php echo pjSanitize::html($pjSbsLbl('front_btn_remove', 'Remove')); ?></a>
			</div><!-- /.form-group -->
		</div><!-- /.col-sm-4 -->
	</div><!-- /.row -->

	<div id="pjSbsVoucherMessage" class="alert<?php echo $pjSbsVoucherApplied ? ' alert-info' : NULL; ?>" style="display: <?php echo $pjSbsVoucherApplied ? '' : 'none'; ?>;"><?php echo $pjSbsVoucherApplied ? pjSanitize::html($pjSbsVoucherOk) : ''; ?></div>
</div><!-- /.pjSbs-voucher -->
