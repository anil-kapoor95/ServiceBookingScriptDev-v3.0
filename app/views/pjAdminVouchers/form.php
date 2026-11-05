<?php
/* shared add / edit form; $is_update is set by the including view */
$arr = isset($tpl['arr']) ? $tpl['arr'] : array();
$opt = $tpl['option_arr'];
$jqDateFormat = pjUtil::jqDateFormat($opt['o_date_format']);
$week_start = isset($opt['o_week_start']) && in_array((int) $opt['o_week_start'], range(0, 6)) ? (int) $opt['o_week_start'] : 0;
$setup = pjAdminVouchers::getTimeSetup($opt);
$types = __('voucher_types', true);
$valids = __('voucher_valids', true);
$days = __('days', true);
$currency_sign = pjUtil::formatCurrencySign(NULL, $opt['o_currency'], "");

$valid_cur = isset($arr['valid']) ? $arr['valid'] : '';
$type_cur = isset($arr['type']) ? $arr['type'] : 'amount';
$today = date($opt['o_date_format']);
$tomorrow = date($opt['o_date_format'], time() + 86400);
$date_from_cur = !empty($arr['date_from']) ? date($opt['o_date_format'], strtotime($arr['date_from'])) : $today;
$date_to_cur = !empty($arr['date_to']) ? date($opt['o_date_format'], strtotime($arr['date_to'])) : $tomorrow;
$time_from_cur = !empty($arr['time_from']) ? $arr['time_from'] : '00:00:00';
$time_to_cur = !empty($arr['time_to']) ? $arr['time_to'] : '23:55:00';
$every_cur = !empty($arr['every']) ? $arr['every'] : 'monday';
$service_arr = isset($tpl['service_arr']) ? $tpl['service_arr'] : array();
$service_ids_cur = isset($tpl['voucher_service_ids']) ? $tpl['voucher_service_ids'] : array();
$services_selected = !empty($service_ids_cur);
$services_none = (string) __('lblVoucherServicesNone', true);
if (trim($services_none) === '' || $services_none === 'lblVoucherServicesNone')
{
	$services_none = '-- Select services --'; // label not installed yet
}

/* hour / minute / am-pm selects that follow the "time format" option of the site */
$timeSelects = function ($prefix, $hms) use ($setup)
{
	$ts = strtotime('2000-01-01 ' . $hms);
	$h24 = (int) date('G', $ts);
	$m = (int) date('i', $ts);
	$html = '<select name="' . $prefix . '_hour" id="' . $prefix . '_hour" class="pj-form-field w60">';
	if ($setup['h12'])
	{
		$h12 = $h24 % 12 == 0 ? 12 : $h24 % 12;
		for ($i = 1; $i <= 12; $i++)
		{
			$html .= '<option value="' . $i . '"' . ($i == $h12 ? ' selected="selected"' : '') . '>' . sprintf('%02d', $i) . '</option>';
		}
	} else {
		for ($i = 0; $i <= 23; $i++)
		{
			$html .= '<option value="' . $i . '"' . ($i == $h24 ? ' selected="selected"' : '') . '>' . sprintf('%02d', $i) . '</option>';
		}
	}
	$html .= '</select> <select name="' . $prefix . '_minute" id="' . $prefix . '_minute" class="pj-form-field w60">';
	for ($i = 0; $i <= 59; $i += 5)
	{
		$html .= '<option value="' . $i . '"' . ($i == $m - ($m % 5) ? ' selected="selected"' : '') . '>' . sprintf('%02d', $i) . '</option>';
	}
	$html .= '</select>';
	if ($setup['ampm'] > 0)
	{
		$am = $setup['ampm'] == 2 ? 'AM' : 'am';
		$pm = $setup['ampm'] == 2 ? 'PM' : 'pm';
		$html .= ' <select name="' . $prefix . '_ampm" id="' . $prefix . '_ampm" class="pj-form-field w60">'
			. '<option value="' . $am . '"' . ($h24 < 12 ? ' selected="selected"' : '') . '>' . $am . '</option>'
			. '<option value="' . $pm . '"' . ($h24 >= 12 ? ' selected="selected"' : '') . '>' . $pm . '</option></select>';
	}
	return $html;
};
?>
<div class="clear_both">
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVouchers&amp;action=<?php echo $is_update ? 'pjActionUpdate' : 'pjActionCreate'; ?>" method="post" id="<?php echo $is_update ? 'frmUpdateVoucher' : 'frmCreateVoucher'; ?>" class="form pj-form pj-voucher-form" autocomplete="off">
		<input type="hidden" name="voucher_<?php echo $is_update ? 'update' : 'create'; ?>" value="1" />
		<input type="hidden" name="csrf_token" value="<?php echo pjAppController::getCsrfToken(); ?>" />
		<?php if ($is_update) : ?><input type="hidden" name="id" id="voucher_id" value="<?php echo (int) $arr['id']; ?>" /><?php endif; ?>
		<input type="hidden" id="voucher_date_format" value="<?php echo pjSanitize::html($jqDateFormat); ?>" />
		<input type="hidden" id="voucher_week_start" value="<?php echo $week_start; ?>" />

		<p>
			<label class="title"><?php __('lblVoucherCode'); ?></label>
			<span class="inline_block">
				<input type="text" name="code" id="code" maxlength="255" class="pj-form-field w300 required" value="<?php echo isset($arr['code']) ? pjSanitize::html($arr['code']) : ''; ?>" data-msg-required="<?php __('pj_field_required'); ?>" data-msg-remote="<?php __('lblVoucherCodeExists'); ?>" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblVoucherDiscount'); ?></label>
			<span class="inline_block">
				<input type="text" name="discount" id="discount" class="pj-form-field w80 required" value="<?php echo isset($arr['discount']) ? pjSanitize::html($arr['discount']) : ''; ?>" data-msg-required="<?php __('pj_field_required'); ?>" data-msg-number="<?php __('pj_number_validation'); ?>" data-msg-min="<?php __('lblVoucherDiscountMin'); ?>" data-msg-max="<?php __('lblVoucherDiscountInvalid'); ?>" />
				<select name="type" id="type" class="pj-form-field w100" data-currency="<?php echo pjSanitize::html($currency_sign); ?>">
					<?php foreach ($types as $k => $v) { ?>
					<option value="<?php echo $k; ?>"<?php echo $type_cur == $k ? ' selected="selected"' : ''; ?>><?php echo pjSanitize::html($v); ?></option>
					<?php } ?>
				</select>
				<span id="voucherSign" class="l5"><?php echo $type_cur == 'percent' ? '%' : $currency_sign; // already an HTML entity (e.g. &euro;) ?></span>
			</span>
		</p>
		<p style="display: flow-root; overflow: visible;">
			<label class="title"><?php __('lblVoucherServices'); ?></label>
			<span class="inline_block" style="max-width: 380px; vertical-align: top;">
				<label style="display: block; margin: 2px 0 4px;"><input type="radio" name="services_scope" value="all"<?php echo !$services_selected ? ' checked="checked"' : ''; ?> /> <?php __('lblVoucherServicesAll'); ?></label>
				<label style="display: block; margin: 0 0 4px;"><input type="radio" name="services_scope" value="selected"<?php echo $services_selected ? ' checked="checked"' : ''; ?> /> <?php __('lblVoucherServicesSelected'); ?></label>
				<span id="voucherServicesBox" class="<?php echo $services_selected ? '' : 'voucher-services-off'; ?>" style="display: <?php echo $services_selected ? 'block' : 'none'; ?>; position: relative; width: 320px; margin-top: 2px;">
					<a href="#" id="voucherServicesToggle" class="voucher-services-toggle" style="display: block; box-sizing: border-box; width: 320px; padding: 6px 28px 6px 10px; min-height: 18px; line-height: 18px; border: 1px solid #ccc; background: #fff; color: #333; text-decoration: none; position: relative; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><span id="voucherServicesLabel"></span><span style="position: absolute; right: 10px; top: 7px; font-size: 10px; color: #777;">&#9660;</span></a>
					<span id="voucherServicesList" style="display: none; position: absolute; z-index: 50; left: 0; top: 100%; box-sizing: border-box; width: 320px; max-height: 200px; overflow-y: auto; padding: 6px 10px; border: 1px solid #ccc; background: #fff; box-shadow: 0 4px 10px rgba(0,0,0,.18);">
						<?php foreach ($service_arr as $service) { ?>
						<label style="display: block; padding: 2px 0;"><input type="checkbox" name="service_id[]" value="<?php echo (int) $service['id']; ?>" data-title="<?php echo pjSanitize::html($service['title']); ?>"<?php echo in_array((int) $service['id'], $service_ids_cur, true) ? ' checked="checked"' : ''; ?><?php echo $services_selected ? '' : ' disabled="disabled"'; ?> /> <?php echo pjSanitize::html($service['title']); ?></label>
						<?php } ?>
					</span>
				</span>
				<span id="validate_services" data-msg="<?php echo pjSanitize::html(__('lblVoucherServicesRequired', true)); ?>" data-none="<?php echo pjSanitize::html($services_none); ?>" style="display: none;"></span>
				<span style="display: block; max-width: 380px; margin-top: 4px; color: #777; font-size: 12px; white-space: normal;"><?php __('lblVoucherServicesHint'); ?></span>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblVoucherValid'); ?></label>
			<span class="inline_block">
				<select name="valid" id="valid" class="pj-form-field w150 required" data-msg-required="<?php __('pj_field_required'); ?>">
					<option value=""><?php __('lblVoucherChoose'); ?></option>
					<?php foreach ($valids as $k => $v) { ?>
					<option value="<?php echo $k; ?>"<?php echo $valid_cur == $k ? ' selected="selected"' : ''; ?>><?php echo pjSanitize::html($v); ?></option>
					<?php } ?>
				</select>
			</span>
		</p>

		<div id="valid_fixed" class="valid-box" style="display: none;">
			<p>
				<label class="title"><?php __('lblVoucherDate'); ?></label>
				<span class="inline_block">
					<input type="text" name="f_date" id="f_date" class="pj-form-field w100 pj-voucher-date" readonly="readonly" value="<?php echo $date_from_cur; ?>" data-msg-required="<?php __('pj_field_required'); ?>" />
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblVoucherTimeFrom'); ?></label>
				<span class="inline_block"><?php echo $timeSelects('f_from', $time_from_cur); ?></span>
			</p>
			<p>
				<label class="title"><?php __('lblVoucherTimeTo'); ?></label>
				<span class="inline_block"><?php echo $timeSelects('f_to', $time_to_cur); ?><input type="hidden" id="validate_fixedtime" value="" data-msg="<?php __('lblVoucherValidateTime'); ?>" /></span>
			</p>
		</div>

		<div id="valid_period" class="valid-box" style="display: none;">
			<p>
				<label class="title"><?php __('lblVoucherDateFrom'); ?></label>
				<span class="inline_block">
					<input type="text" name="p_date_from" id="p_date_from" class="pj-form-field w100 pj-voucher-date" readonly="readonly" value="<?php echo $date_from_cur; ?>" />
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblVoucherTimeFrom'); ?></label>
				<span class="inline_block"><?php echo $timeSelects('p_from', $time_from_cur); ?></span>
			</p>
			<p>
				<label class="title"><?php __('lblVoucherDateTo'); ?></label>
				<span class="inline_block">
					<input type="text" name="p_date_to" id="p_date_to" class="pj-form-field w100 pj-voucher-date" readonly="readonly" value="<?php echo $date_to_cur; ?>" />
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblVoucherTimeTo'); ?></label>
				<span class="inline_block"><?php echo $timeSelects('p_to', $time_to_cur); ?><input type="hidden" id="validate_datetime" value="" data-msg="<?php __('lblVoucherValidateDateTime'); ?>" /></span>
			</p>
		</div>

		<div id="valid_recurring" class="valid-box" style="display: none;">
			<p>
				<label class="title"><?php __('lblVoucherEvery'); ?></label>
				<span class="inline_block">
					<select name="r_every" id="r_every" class="pj-form-field w150">
						<?php
						$order = array(1 => 'monday', 2 => 'tuesday', 3 => 'wednesday', 4 => 'thursday', 5 => 'friday', 6 => 'saturday', 0 => 'sunday');
						$ordered = array();
						for ($i = 0; $i < 7; $i++)
						{
							$n = ($week_start + $i) % 7;
							$ordered[$n] = $order[$n];
						}
						foreach ($ordered as $n => $day_key)
						{
							?><option value="<?php echo $day_key; ?>"<?php echo $every_cur == $day_key ? ' selected="selected"' : ''; ?>><?php echo pjSanitize::html(isset($days[$n]) ? $days[$n] : ucfirst($day_key)); ?></option><?php
						}
						?>
					</select>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblVoucherStartTime'); ?></label>
				<span class="inline_block"><?php echo $timeSelects('r_from', $time_from_cur); ?></span>
			</p>
			<p>
				<label class="title"><?php __('lblVoucherEndTime'); ?></label>
				<span class="inline_block"><?php echo $timeSelects('r_to', $time_to_cur); ?><input type="hidden" id="validate_time" value="" data-msg="<?php __('lblVoucherValidateTime'); ?>" /></span>
			</p>
		</div>

		<p>
			<label class="title">&nbsp;</label>
			<span class="inline_block">
				<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
				<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminVouchers&action=pjActionIndex';" />
			</span>
		</p>
	</form>
</div>

<script type="text/javascript">
var myLabel = myLabel || {};
myLabel.voucher_discount_max = "<?php echo addslashes(__('lblVoucherDiscountInvalid', true)); ?>";
myLabel.voucher_code_exists = "<?php echo addslashes(__('lblVoucherCodeExists', true)); ?>";
myLabel.voucher_discount_min = "<?php echo addslashes(__('lblVoucherDiscountMin', true)); ?>";
myLabel.field_required = "<?php echo addslashes(__('pj_field_required', true)); ?>";
myLabel.number_invalid = "<?php echo addslashes(__('pj_number_validation', true)); ?>";
</script>
