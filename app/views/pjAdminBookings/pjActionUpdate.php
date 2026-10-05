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
		$titles = __('error_titles', true);
		$bodies = __('error_bodies', true);
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	$jqTimeFormat = pjUtil::jqTimeFormat($tpl['option_arr']['o_time_format']);
	
	$selected_date = date($tpl['option_arr']['o_date_format'], strtotime($tpl['selected_date_iso']));
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionUpdate" method="post" class="form pj-form" id="frmUpdateBooking">
		<input type="hidden" name="booking_update" value="1"/>
		<input type="hidden" name="csrf_token" value="<?php echo pjAppController::getCsrfToken(); ?>" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id'];?>"/>
		<input type="hidden" id="duration" name="duration" value="<?php echo $tpl['arr']['duration'];?>"/>
		
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1"><?php __('tabBookingDetails');?></a></li>
				<li><a href="#tabs-2"><?php __('tabClientDetails');?></a></li>
			</ul>
			<div id="tabs-1">
				<?php pjUtil::printNotice(__('infoUpdateBookingDetailsTitle', true, false), __('infoUpdateBookingDetailsDesc', true, false)); ?>
				<div class="pj-loader-outer">
					<div class="pj-loader"></div>
					<fieldset class="fieldset white">
						<legend><?php __('legendDetails'); ?></legend>
						<div class="float_left w60p overflow">
							<p>
								<label class="title"><?php __('lblBookingID'); ?></label>
								<span class="inline_block t5"><?php echo pjSanitize::html($tpl['arr']['uuid']);?></span>
							</p>
							<p>
								<label class="title"><?php __('lblCreatedOn'); ?></label>
								<span class="inline_block t5"><?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['created'])) . ', ' . date($tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['created']));?></span>
							</p>
							<p>
								<label class="title"><?php __('lblIpAddress'); ?></label>
								<span class="inline_block t5"><?php echo pjSanitize::html($tpl['arr']['ip']);?></span>
							</p>
							
							<p>
								<label class="title"><?php __('lblDateTime'); ?></label>
								<span class="block overflow">
									<span class="block overflow float_left r5">
										<span class="pj-form-field-custom pj-form-field-custom-after">
											<input type="text" name="start_dt" class="pj-form-field pointer w80 required datetimepick" readonly="readonly" value="<?php echo $selected_date;?>" data-msg-required="<?php __('pj_field_required');?>" data-msg-remote="<?php __('lblWeAreClose');?>" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" lang="<?php echo $jqTimeFormat; ?>"/>
											<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
										</span>
									</span>
									<span id="hourWrapper" class="block float_left r5">
										<?php include_once dirname(__FILE__) . '/pjActionGetHour.php';?>
									</span>
									<span id="minuteWrapper" class="block float_left">
										<?php include_once dirname(__FILE__) . '/pjActionGetMinute.php';?>
									</span>
								</span>
								<em style="display: none;"><label id="pjSbsErrorCustom" class="errCustom"><?php __('lblSelectDateTimeHint');?></label></em>
							</p>
							<p>
								<label class="title"><?php __('lblStatus'); ?></label>
								<span class="inline_block">
									<select name="status" id="status" class="pj-form-field w150 required" data-msg-required="<?php __('pj_field_required');?>">
										<option value="">-- <?php __('lblChoose'); ?> --</option>
										<?php
										foreach (__('booking_statuses', true) as $k => $v)
										{
										    ?><option value="<?php echo $k; ?>"<?php echo $k == $tpl['arr']['status'] ? ' selected="selected"' : NULL;?>><?php echo pjSanitize::html($v); ?></option><?php
										}
										?>
									</select>
								</span>
							</p>
							<p>
								<label class="title"><?php __('lblPaymentMethod');?></label>
								<span class="inline-block">
									<select name="payment_method" id="payment_method" class="pj-form-field w150 required" data-msg-required="<?php __('pj_field_required');?>">
										<option value="">-- <?php __('lblChoose'); ?>--</option>
										<?php
										foreach (__('payment_methods', true, false) as $k => $v)
										{
										    ?><option value="<?php echo $k; ?>"<?php echo $k == $tpl['arr']['payment_method'] ? ' selected="selected"' : NULL;?>><?php echo pjSanitize::html($v); ?></option><?php
										}
										?>
									</select>
								</span>
							</p>
							<p>
								<label class="title">&nbsp;</label>
								<span class="inline_block">
									<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
									<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminBookings&action=pjActionIndex';" />
								</span>
							</p>
						</div>
						<div class="float_left w40p overflow">
							<p>
								<label class="title"><?php __('lblEndEmail'); ?></label>
								<span class="block t5 float_left">
									<a href="#" class="pjSbsSendConfirm block b10" data-id="<?php echo $tpl['arr']['id']?>"><?php __('lblConfirmation'); ?></a>
									<a href="#" class="pjSbsSendCancel" data-id="<?php echo $tpl['arr']['id']?>"><?php __('lblCancellation'); ?></a>
								</span>
							</p>
							<?php $isCC = $tpl['arr']['payment_method'] == 'creditcard'; ?>
							<p class="boxCC" style="display: <?php echo !$isCC ? 'none' : NULL; ?>;">
								<label class="title"><?php __('lblCCType'); ?></label>
								<span class="inline-block">
									<select name="cc_type" class="pj-form-field w150" data-msg-required="<?php __('pj_field_required');?>">
										<option value="">---</option>
										<?php
										foreach (__('cc_types', true, false) as $k => $v)
										{
										    ?><option value="<?php echo $k; ?>"<?php echo $tpl['arr']['cc_type'] == $k ? ' selected="selected"' : NULL;?>><?php echo pjSanitize::html($v); ?></option><?php
										}
										?>
									</select>
								</span>
							</p>
							<p class="boxCC" style="display: <?php echo !$isCC ? 'none' : NULL; ?>;">
								<label class="title"><?php __('lblCCNum'); ?></label>
								<span class="inline-block">
									<input type="text" name="cc_num" id="cc_num" class="pj-form-field w136" value="<?php echo pjSanitize::html($tpl['arr']['cc_num']); ?>" data-msg-required="<?php __('pj_field_required');?>"/>
								</span>
							</p>
							<p class="boxCC" style="display: <?php echo !$isCC ? 'none' : NULL; ?>;">
								<label class="title"><?php __('lblCCExp'); ?></label>
								<span class="inline-block">
									<select name="cc_exp_month" class="pj-form-field" data-msg-required="<?php __('pj_field_required');?>">
										<?php
										$month_arr = __('months', true, false);
										ksort($month_arr);
										foreach ($month_arr as $key => $val)
										{
											?><option value="<?php echo $key;?>"<?php echo $tpl['arr']['cc_exp_month'] == $key ? ' selected="selected"' : NULL;?>><?php echo $val;?></option><?php
										}
										?>
									</select>
									<select name="cc_exp_year" class="pj-form-field" data-msg-required="<?php __('pj_field_required');?>">
										<?php
										$y = (int) date('Y');
										for ($i = $y; $i <= $y + 10; $i++)
										{
											?><option value="<?php echo $i; ?>" <?php echo $tpl['arr']['cc_exp_year'] == $i ? ' selected="selected"' : NULL;?>><?php echo $i; ?></option><?php
										}
										?>
									</select>
								</span>
							</p>
							<p class="boxCC" style="display: <?php echo !$isCC ? 'none' : NULL; ?>">
								<label class="title"><?php __('lblCCCode'); ?></label>
								<span class="inline-block">
									<input type="text" name="cc_code" id="cc_code" class="pj-form-field w100" value="<?php echo pjSanitize::html($tpl['arr']['cc_code']); ?>" data-msg-required="<?php __('pj_field_required');?>"/>
								</span>
							</p>
							<p>
								<label class="title"><?php __('lblSubTotal'); ?></label>
								<span class="pj-form-field-custom pj-form-field-custom-before">
									<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
									<input type="text" id="subtotal" name="subtotal" class="pj-form-field number w80" value="<?php echo $tpl['arr']['subtotal']; ?>" readonly="readonly"/>
								</span>
							</p>
							<p>
								<label class="title"><?php __('lblTax'); ?></label>
								<span class="pj-form-field-custom pj-form-field-custom-before">
									<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
									<input type="text" id="tax" name="tax" class="pj-form-field number w80" value="<?php echo $tpl['arr']['tax']; ?>" data-tax="<?php echo $tpl['option_arr']['o_tax_payment'];?>" readonly="readonly"/>
								</span>
							</p>
							<p>
								<label class="title"><?php __('lblTotal'); ?></label>
								<span class="pj-form-field-custom pj-form-field-custom-before">
									<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
									<input type="text" id="total" name="total" class="pj-form-field number w80" value="<?php echo $tpl['arr']['total']; ?>" readonly="readonly"/>
								</span>
							</p>
							<p>
								<label class="title"><?php __('lblDeposit'); ?></label>
								<span class="pj-form-field-custom pj-form-field-custom-before">
									<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
									<input type="text" id="deposit" name="deposit" class="pj-form-field number w80" value="<?php echo $tpl['arr']['deposit']; ?>" data-deposit="<?php echo $tpl['option_arr']['o_deposit_payment'];?>" readonly="readonly"/>
								</span>
							</p>
						</div>
					</fieldset>
					<?php
					if(!empty($tpl['service_arr']))
					{
						?>
						<fieldset class="fieldset white serviceBox">
							<legend><?php __('legendServices'); ?></legend>
							<table class="pj-table" style="width: 100%; margin-bottom: 10px;">
								<thead>
									<tr>
										<th><?php __('lblTitle');?></th>
										<th><?php __('lblDuration');?></th>
										<th><?php __('lblPrice');?></th>
										<th>&nbsp;</th>
									</tr>
								</thead>
								<tbody>
									<?php
									foreach($tpl['service_arr'] as $k => $v)
									{
										?>
										<tr>
											<td><?php echo pjSanitize::html($v['title']);?></td>
											<td><?php echo pjSanitize::html($v['duration']) ?> <?php __('lblMinutes');?></td>
											<td><?php echo pjUtil::formatCurrencySign($v['price'], $tpl['option_arr']['o_currency']);?></td>
											<td><input type="checkbox" id="service_id_<?php echo $v['id'];?>" name="service_id[<?php echo $v['id'];?>]" value="<?php echo $v['price'];?>"<?php echo in_array($v['id'], $tpl['service_id_arr']) ? ' checked="checked"' : NULL;?> data-duration="<?php echo $v['duration'];?>" class="pjSbsServiceCheckbox"/></td>
										</tr>
										<?php
									} 
									?>
								</tbody>
							</table>
							<input type="hidden" id="hiddenValidateService" name="validate_service" value="1" class="required" data-msg-required="<?php __('lblPleaseSelectService');?>"/>
							<div class="overflow">
								<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
							</div>
						</fieldset>
						<?php
					} 
					?>
				</div>
			</div><!-- #tabs-1 -->
			<div id="tabs-2">
				<?php pjUtil::printNotice(__('infoUpdateClientDetailsTitle', true, false), __('infoUpdateClientDetailsDesc', true, false)); ?>
				
				<?php
				if (in_array((int) $tpl['option_arr']['o_bf_include_title'], array(2,3)))
				{
					?>
					<p>
						<label class="title"><?php __('lblResvTitle'); ?></label>
						<span class="inline-block">
							<select name="c_title" id="c_title" class="pj-form-field w150<?php echo $tpl['option_arr']['o_bf_include_title'] == 3 ? ' required' : NULL; ?>">
								<option value="">-- <?php __('lblChoose'); ?>--</option>
								<?php
								foreach ( __('personal_titles', true, false) as $k => $v)
								{
								    ?><option value="<?php echo $k; ?>"<?php echo $k == $tpl['arr']['c_title'] ? ' selected="selected"' : NULL;?>><?php echo pjSanitize::html($v); ?></option><?php
								}
								?>
							</select>
						</span>
					</p>
					<?php
				}
				if (in_array((int) $tpl['option_arr']['o_bf_include_name'], array(2,3)))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblResvName'); ?></label>
						<span class="inline-block">
							<input type="text" name="c_name" id="c_name" value="<?php echo pjSanitize::html($tpl['arr']['c_name']); ?>" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_name'] == 3 ? ' required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>"/>
						</span>
					</p>
					<?php
				}
				if (in_array((int) $tpl['option_arr']['o_bf_include_email'], array(2,3)))
				{
					?>
					<p>
						<label class="title"><?php __('lblResvEmail'); ?></label>
						<span class="inline-block">
							<input type="text" name="c_email" id="c_email" value="<?php echo pjSanitize::html($tpl['arr']['c_email']); ?>" class="pj-form-field email w400<?php echo $tpl['option_arr']['o_bf_include_email'] == 3 ? ' required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>" data-msg-email="<?php __('pj_email_validation');?>"/>
						</span>
					</p>
					<?php
				}
				if (in_array((int) $tpl['option_arr']['o_bf_include_phone'], array(2,3)))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblResvPhone'); ?></label>
						<span class="inline-block">
							<input type="text" name="c_phone" id="c_phone" value="<?php echo pjSanitize::html($tpl['arr']['c_phone']); ?>" class="pj-form-field phone w400<?php echo $tpl['option_arr']['o_bf_include_phone'] == 3 ? ' required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>" data-msg-phone="<?php __('pj_phone_validation'); ?>"/>
						</span>
					</p>
					<?php
				}
				if (in_array((int) $tpl['option_arr']['o_bf_include_notes'], array(2,3)))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblResvNotes'); ?></label>
						<span class="inline-block">
							<textarea name="c_notes" id="c_notes" class="pj-form-field w500 h120<?php echo $tpl['option_arr']['o_bf_include_notes'] == 3 ? ' required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>"><?php echo pjSanitize::html($tpl['arr']['c_notes']); ?></textarea>
						</span>
					</p>
					<?php
				}
				if (in_array((int) $tpl['option_arr']['o_bf_include_company'], array(2,3)))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblResvCompany'); ?></label>
						<span class="inline-block">
							<input type="text" name="c_company" id="c_company" value="<?php echo pjSanitize::html($tpl['arr']['c_company']); ?>" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_company'] == 3 ? ' required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>"/>
						</span>
					</p>
					<?php
				}
				if (in_array((int) $tpl['option_arr']['o_bf_include_address'], array(2,3)))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblResvAddress'); ?></label>
						<span class="inline-block">
							<input type="text" name="c_address" id="c_address" value="<?php echo pjSanitize::html($tpl['arr']['c_address']); ?>" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_address'] == 3 ? ' required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>"/>
						</span>
					</p>
					<?php
				}
				if (in_array((int) $tpl['option_arr']['o_bf_include_city'], array(2,3)))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblResvCity'); ?></label>
						<span class="inline-block">
							<input type="text" name="c_city" id="c_city" value="<?php echo pjSanitize::html($tpl['arr']['c_city']); ?>" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_city'] == 3 ? ' required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>"/>
						</span>
					</p>
					<?php
				}
				if (in_array((int) $tpl['option_arr']['o_bf_include_state'], array(2,3)))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblResvState'); ?></label>
						<span class="inline-block">
							<input type="text" name="c_state" id="c_state" value="<?php echo pjSanitize::html($tpl['arr']['c_state']); ?>" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_state'] == 3 ? ' required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>"/>
						</span>
					</p>
					<?php
				}
				if (in_array((int) $tpl['option_arr']['o_bf_include_zip'], array(2,3)))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblResvZip'); ?></label>
						<span class="inline-block">
							<input type="text" name="c_zip" id="c_zip" value="<?php echo pjSanitize::html($tpl['arr']['c_zip']); ?>" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_zip'] == 3 ? ' required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>"/>
						</span>
					</p>
					<?php
				}
				if (in_array((int) $tpl['option_arr']['o_bf_include_country'], array(2,3)))
				{ 
					?>
					<p>
						<label class="title"><?php __('lblResvCountry'); ?></label>
						<span class="inline-block">
							<select name="c_country" id="c_country" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_country'] == 3 ? ' required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>">
								<option value="">-- <?php __('lblChoose'); ?>--</option>
								<?php
								foreach ($tpl['country_arr'] as $v)
								{
								    ?><option value="<?php echo $v['id']; ?>"<?php echo $v['id'] == $tpl['arr']['c_country'] ? ' selected="selected"' : NULL;?>><?php echo pjSanitize::html($v['country_title']); ?></option><?php
								}
								?>
							</select>
						</span>
					</p>
					<?php
				}
				?>
				
				<p>
					<label class="title">&nbsp;</label>
					<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
					<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminBookings&action=pjActionIndex';" />
				</p>
			</div><!-- #tabs-2 -->
		</div>
		
	</form>
	
	<div id="dialogConfirmation" title="<?php __('booking_confirmation_title'); ?>" style="display: none"></div>
	
	<div id="dialogCancellation" title="<?php __('booking_cancellation_title'); ?>" style="display: none"></div>
	
	<div id="dialogDuplicate" title="<?php __('booking_duplicated_booking'); ?>" style="display: none"><p><?php __('lblDuplicatedBooking');?></p></div>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.currency = "<?php echo $tpl['option_arr']['o_currency'];?>";
	myLabel.choose = "-- <?php __('lblChoose'); ?> --";

	var disabledDates = [];
	var disabledWeekDays = [];
	var enabledDates = [];
	<?php
	foreach($tpl['date_arr'] as $k => $v)
	{
		if($v['is_dayoff'] == 'T')
		{
			?>disabledDates.push("<?php echo date('m-j-Y', strtotime($v['date']));?>");<?php
		}else{
			?>enabledDates.push("<?php echo date('m-j-Y', strtotime($v['date']));?>");<?php
		}
	}
	$week_arr = array('sunday'=>0,'monday'=>1,'tuesday'=>2,'wednesday'=>3,'thursday'=>4,'friday'=>5,'saturday'=>6);
	foreach($tpl['week_dayoff_arr'] as $k => $v)
	{
		?>disabledWeekDays.push(<?php echo $week_arr[$k];?>);<?php
	}  
	?>
	</script>
	<?php
}
?>