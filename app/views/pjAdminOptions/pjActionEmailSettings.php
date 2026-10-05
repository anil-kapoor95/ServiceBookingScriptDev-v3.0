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
	include_once PJ_VIEWS_PATH . 'pjLayouts/elements/optmenu.php';
	
	pjUtil::printNotice(__('infoEmailSettingsTitle', true), __('infoEmailSettingsBody', true));
	
	$o = isset($tpl['option_arr']) && is_array($tpl['option_arr']) ? $tpl['option_arr'] : array();
	
	$send_email  = isset($o['o_send_email'])  && $o['o_send_email'] === 'smtp' ? 'smtp' : 'mail';
	$smtp_host   = isset($o['o_smtp_host'])   ? $o['o_smtp_host']   : '';
	$smtp_port   = isset($o['o_smtp_port'])   && $o['o_smtp_port'] !== '' ? $o['o_smtp_port'] : '25';
	$smtp_user   = isset($o['o_smtp_user'])   ? $o['o_smtp_user']   : '';
	$smtp_pass   = isset($o['o_smtp_pass'])   ? $o['o_smtp_pass']   : '';
	$smtp_secure = isset($o['o_smtp_secure']) && in_array($o['o_smtp_secure'], array('ssl', 'tls')) ? $o['o_smtp_secure'] : '';
	$smtp_auth   = isset($o['o_smtp_auth'])   && in_array($o['o_smtp_auth'], array('LOGIN', 'PLAIN', 'CRAM-MD5')) ? $o['o_smtp_auth'] : 'LOGIN';
	$from_email  = isset($o['o_from_email'])  ? $o['o_from_email']  : '';
	$from_name   = isset($o['o_from_name'])   ? $o['o_from_name']   : '';
	
	$smtpRowStyle = $send_email === 'smtp' ? '' : 'display: none;';
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionUpdate" method="post" class="form pj-form" id="frmEmailSettings">
		<input type="hidden" name="options_update" value="1" />
		<input type="hidden" name="csrf_token" value="<?php echo pjAppController::getCsrfToken(); ?>" />
		<input type="hidden" name="next_action" value="pjActionEmailSettings" />
		
		<table class="pj-table" cellpadding="0" cellspacing="0" style="width: 100%">
			<thead>
				<tr>
					<th style="width: 260px;"><?php __('lblOption'); ?></th>
					<th><?php __('lblValue'); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr class="pj-table-row-odd">
					<td><?php __('opt_o_send_email'); ?></td>
					<td>
						<select name="value-enum-o_send_email" id="o_send_email" class="pj-form-field">
							<option value="mail|smtp::mail"<?php echo $send_email === 'mail' ? ' selected="selected"' : ''; ?>>PHP mail()</option>
							<option value="mail|smtp::smtp"<?php echo $send_email === 'smtp' ? ' selected="selected"' : ''; ?>>SMTP</option>
						</select>
					</td>
				</tr>
				
				<tr class="pj-table-row-odd boxSmtp" style="<?php echo $smtpRowStyle; ?>">
					<td><?php __('opt_o_smtp_host'); ?></td>
					<td><input type="text" name="value-string-o_smtp_host" id="o_smtp_host" class="pj-form-field w200" value="<?php echo pjSanitize::html($smtp_host); ?>" autocomplete="off" /></td>
				</tr>
				
				<tr class="pj-table-row-odd boxSmtp" style="<?php echo $smtpRowStyle; ?>">
					<td><?php __('opt_o_smtp_port'); ?></td>
					<td>
						<input type="text" name="value-int-o_smtp_port" id="o_smtp_port" class="pj-form-field field-int w60" value="<?php echo pjSanitize::html($smtp_port); ?>" />
						<span class="gray" style="margin-left: 8px;"><?php __('emailSmtpPortHint'); ?></span>
					</td>
				</tr>
				
				<tr class="pj-table-row-odd boxSmtp" style="<?php echo $smtpRowStyle; ?>">
					<td><?php __('opt_o_smtp_secure'); ?></td>
					<td>
						<select name="value-enum-o_smtp_secure" class="pj-form-field">
							<option value="|ssl|tls::"<?php echo $smtp_secure === '' ? ' selected="selected"' : ''; ?>><?php __('lblNone'); ?></option>
							<option value="|ssl|tls::ssl"<?php echo $smtp_secure === 'ssl' ? ' selected="selected"' : ''; ?>>SSL</option>
							<option value="|ssl|tls::tls"<?php echo $smtp_secure === 'tls' ? ' selected="selected"' : ''; ?>>TLS</option>
						</select>
					</td>
				</tr>
				
				<tr class="pj-table-row-odd boxSmtp" style="<?php echo $smtpRowStyle; ?>">
					<td><?php __('opt_o_smtp_auth'); ?></td>
					<td>
						<select name="value-enum-o_smtp_auth" class="pj-form-field">
							<option value="LOGIN|PLAIN|CRAM-MD5::LOGIN"<?php echo $smtp_auth === 'LOGIN' ? ' selected="selected"' : ''; ?>>LOGIN</option>
							<option value="LOGIN|PLAIN|CRAM-MD5::PLAIN"<?php echo $smtp_auth === 'PLAIN' ? ' selected="selected"' : ''; ?>>PLAIN</option>
							<option value="LOGIN|PLAIN|CRAM-MD5::CRAM-MD5"<?php echo $smtp_auth === 'CRAM-MD5' ? ' selected="selected"' : ''; ?>>CRAM-MD5</option>
						</select>
					</td>
				</tr>
				
				<tr class="pj-table-row-odd boxSmtp" style="<?php echo $smtpRowStyle; ?>">
					<td><?php __('opt_o_smtp_user'); ?></td>
					<td><input type="text" name="value-string-o_smtp_user" class="pj-form-field w200" value="<?php echo pjSanitize::html($smtp_user); ?>" autocomplete="off" /></td>
				</tr>
				
				<tr class="pj-table-row-odd boxSmtp" style="<?php echo $smtpRowStyle; ?>">
					<td><?php __('opt_o_smtp_pass'); ?></td>
					<td><input type="password" name="value-string-o_smtp_pass" class="pj-form-field w200" value="<?php echo pjSanitize::html($smtp_pass); ?>" autocomplete="new-password" /></td>
				</tr>
				
				<tr class="pj-table-row-odd">
					<td><?php __('opt_o_from_email'); ?></td>
					<td><input type="text" name="value-string-o_from_email" id="o_from_email" class="pj-form-field w200" value="<?php echo pjSanitize::html($from_email); ?>" /></td>
				</tr>
				
				<tr class="pj-table-row-odd">
					<td><?php __('opt_o_from_name'); ?></td>
					<td><input type="text" name="value-string-o_from_name" class="pj-form-field w200" value="<?php echo pjSanitize::html($from_name); ?>" /></td>
				</tr>
			</tbody>
		</table>
		
		<p style="margin-top: 12px;">
			<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
			<input type="button" id="btnTestConnection" value="<?php __('btnTestConnection', false, true); ?>" class="pj-button boxSmtp" style="<?php echo $smtpRowStyle; ?>" />
			<input type="button" id="btnSendTestEmail" value="<?php __('btnSendTestEmail', false, true); ?>" class="pj-button" />
		</p>
		
		<div id="emailTestResult" style="display: none; margin-top: 10px; padding: 10px 12px; border-radius: 3px; max-width: 640px;"></div>
	</form>
	
	<!-- Send test email dialog -->
	<div id="emailTestDialog" title="<?php __('emailTestModalTitle', false, true); ?>" style="display: none;">
		<p style="margin: 0 0 14px; color: #666;"><?php __('emailTestModalDesc'); ?></p>
		<div class="form-group">
			<label for="emailTestModalEmail" style="display: block; font-weight: bold; margin-bottom: 8px;"><?php __('lblEmailAddress'); ?></label>
			<input type="text" id="emailTestModalEmail" class="pj-form-field" style="width: 100%; box-sizing: border-box; padding: 13px 12px; font-size: 16px; line-height: 1.4;" />
		</div>
		<div id="emailTestModalMsg" style="display: none; margin-top: 12px; padding: 8px 10px; border-radius: 3px;"></div>
	</div>
	
	<script type="text/javascript">
	var pjEmailLabels = {
		enterEmail:  <?php echo pjAppController::jsonEncode(__('emailMsgEnterEmail', true)); ?>,
		validEmail:  <?php echo pjAppController::jsonEncode(__('emailMsgEnterValidEmail', true)); ?>,
		enterHost:   <?php echo pjAppController::jsonEncode(__('emailMsgEnterHostPort', true)); ?>,
		testing:     <?php echo pjAppController::jsonEncode(__('emailMsgTesting', true)); ?>,
		sending:     <?php echo pjAppController::jsonEncode(__('emailMsgSending', true)); ?>,
		connFail:    <?php echo pjAppController::jsonEncode(__('emailMsgConnFail', true)); ?>,
		sendFail:    <?php echo pjAppController::jsonEncode(__('emailMsgSendFail', true)); ?>,
		unexpected:  <?php echo pjAppController::jsonEncode(__('emailMsgUnexpected', true)); ?>,
		promptEmail: <?php echo pjAppController::jsonEncode(__('emailMsgPrompt', true)); ?>,
		cancel:      <?php echo pjAppController::jsonEncode(__('btnCancel', true)); ?>,
		sendEmail:   <?php echo pjAppController::jsonEncode(__('btnSendEmail', true)); ?>
	};
	</script>
	<?php
}
?>
