var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var tabs = ($.fn.tabs !== undefined),
			$tabs = $("#tabs"),
			tOpt = {
				activate: function (event, ui) {
					$(":input[name='tab_id']").val(ui.newPanel.attr('id'));
				}
			};
		
		if ($tabs.length > 0 && tabs) {
			$tabs.tabs(tOpt);
		}
		$(".field-int").spinner({
			min: 0
		});
		
				
		$("#content").on("focusin", ".textarea_install", function (e) {
			$(this).select();
		}).on("change", "select[name='value-enum-o_send_email']", function (e) {
			switch ($("option:selected", this).val()) {
			case 'mail|smtp::mail':
				$(".boxSmtp").hide();
				break;
			case 'mail|smtp::smtp':
				$(".boxSmtp").show();
				break;
			}
		}).on("change", "select[name='value-enum-o_allow_paypal']", function (e) {
			switch ($("option:selected", this).val()) {
			case 'Yes|No::No':
				$(".boxPaypal").hide();
				break;
			case 'Yes|No::Yes':
				$(".boxPaypal").show();
				break;
			}
		}).on("change", "select[name='value-enum-o_allow_authorize']", function (e) {
			switch ($("option:selected", this).val()) {
			case 'Yes|No::No':
				$(".boxAuthorize").hide();
				break;
			case 'Yes|No::Yes':
				$(".boxAuthorize").show();
				break;
			}
		}).on("change", "select[name='value-enum-o_allow_bank']", function (e) {
			switch ($("option:selected", this).val()) {
			case 'Yes|No::No':
				$(".boxBankAccount").hide();
				break;
			case 'Yes|No::Yes':
				$(".boxBankAccount").show();
				break;
			}
		}).on("click", ".pj-use-theme", function (e) {
			var theme = $(this).attr('data-theme'),
				href = $('#pj_preview_install').attr('href');
			$('.pj-loader').css('display', 'block');
			$.ajax({
				type: "GET",
				async: false,
				url: 'index.php?controller=pjAdminOptions&action=pjActionUpdateTheme&theme=' + theme,
				success: function (data) {
					$('.theme-holder').html(data);
					$('.pj-loader').css('display', 'none');
				}
			});
		}).on("change", "#client_email_notify", function (e) {
			var value = $(this).val();
			$('.boxClient').hide();
			$('.boxClient' + value).show();
		}).on("change", "#client_sms_notify", function (e) {
			var value = $(this).val();
			$('.boxClientSms').hide();
			$('.boxClientSms' + value).show();
		}).on("change", "#admin_email_notify", function (e) {
			var value = $(this).val();
			$('.boxAdmin').hide();
			$('.boxAdmin' + value).show();
		}).on("change", "#admin_sms_notify", function (e) {
			var value = $(this).val();
			$('.boxAdminSms').hide();
			$('.boxAdminSms' + value).show();
		}).on("keydown", ".field-int", function (e) {
			if (e.shiftKey == true) {
                e.preventDefault();
            }
			if ((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105) || e.keyCode == 8 || e.keyCode == 9 || e.keyCode == 37 || e.keyCode == 39 || e.keyCode == 46) {
				
            } else {
            	e.preventDefault();
            } 
		}).on("keydown", "input[name='value-int-o_tax_payment'], input[name='value-int-o_deposit_payment']", function (e) {
			if (e.shiftKey == true) {
                e.preventDefault();
            }
			if ((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105) || e.keyCode == 8 || e.keyCode == 9 || e.keyCode == 190 || e.keyCode == 37 || e.keyCode == 39 || e.keyCode == 46) {
				
            } else {
            	e.preventDefault();
            } 
		});
		if ($('#frmNotification').length > 0) 
		{
			var value = $('#client_email_notify').val();
			$('.boxClient' + value).show();
			
			var value = $('#client_sms_notify').val();
			$('.boxClientSms' + value).show();
			
			var value = $('#admin_email_notify').val();
			$('.boxAdmin' + value).show();
			
			var value = $('#admin_sms_notify').val();
			$('.boxAdminSms' + value).show();
			
			tinymce.init({
			    selector: "textarea.mceEditor",
			    theme: "modern",
			    width: 500,
			    plugins: [
			         "advlist autolink link image lists charmap print preview hr anchor pagebreak",
			         "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
			         "save table contextmenu directionality emoticons template paste textcolor"
			   ],
			   toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l      ink image | print preview media fullpage | forecolor backcolor emoticons"
			 });
		}

		// ===== Email Settings: test connection, send test email, form check =====
		if ($('#frmEmailSettings').length > 0) {
			var L = window.pjEmailLabels || {};
			var lbl = function (key, def) {
				return (L && typeof L[key] === 'string' && L[key] !== '') ? L[key] : def;
			};
			var enumVal = function (v) {
				if (typeof v !== 'string') { return ''; }
				var i = v.indexOf('::');
				return i >= 0 ? v.substring(i + 2) : v;
			};
			var EMAIL_RE = /^[^@\s]+@[^@\s]+\.[^@\s]+$/,
				AJAX_TIMEOUT = 25000,
				baseUrl = window.location.pathname,
				dialogAvailable = ($.fn.dialog !== undefined),
				$emailTestDialog = $('#emailTestDialog'),
				$frmEmail = $('#frmEmailSettings');
			
			var paint = function ($box, kind, text) {
				if (!$box || $box.length === 0) { return; }
				var bg = '#eef', color = '#333', border = '#ccd';
				if (kind === 'success') { bg = '#e6f4ea'; color = '#1e7e34'; border = '#bfe3c9'; }
				else if (kind === 'error') { bg = '#fdecea'; color = '#b02a37'; border = '#f5c2c7'; }
				else if (kind === 'loading') { bg = '#eef2ff'; color = '#3b4a6b'; border = '#c9d4f0'; }
				$box.css({'background': bg, 'color': color, 'border': '1px solid ' + border}).html(text).show();
			};
			var showTestResult = function (kind, text) { paint($('#emailTestResult'), kind, text); };
			var collectEmailSettings = function () {
				var send = enumVal($('select[name="value-enum-o_send_email"]').val());
				var data = {
					send_email: send,
					from_email: $.trim($('input[name="value-string-o_from_email"]').val() || ''),
					from_name:  $.trim($('input[name="value-string-o_from_name"]').val() || '')
				};
				if (send === 'smtp') {
					data.smtp_host   = $.trim($('input[name="value-string-o_smtp_host"]').val() || '');
					data.smtp_port   = $.trim($('input[name="value-int-o_smtp_port"]').val() || '');
					data.smtp_secure = enumVal($('select[name="value-enum-o_smtp_secure"]').val());
					data.smtp_auth   = enumVal($('select[name="value-enum-o_smtp_auth"]').val());
					data.smtp_user   = $.trim($('input[name="value-string-o_smtp_user"]').val() || '');
					data.smtp_pass   = $('input[name="value-string-o_smtp_pass"]').val() || '';
				}
				return data;
			};
			var postTest = function (action, data, $box, $btn, loadingText, failText) {
				paint($box, 'loading', loadingText);
				if ($btn) { $btn.prop('disabled', true); }
				return $.ajax({
					type: 'POST',
					url: baseUrl + '?controller=pjAdminOptions&action=' + action,
					data: data,
					dataType: 'json',
					timeout: AJAX_TIMEOUT
				}).done(function (resp) {
					if (resp && resp.status === 'OK') { paint($box, 'success', resp.text); }
					else { paint($box, 'error', (resp && resp.text) ? resp.text : failText); }
				}).fail(function (xhr, textStatus) {
					paint($box, 'error', (textStatus === 'timeout') ? failText : lbl('unexpected', 'An unexpected error occurred.'));
				}).always(function () {
					if ($btn) { $btn.prop('disabled', false); }
				});
			};
			var doSendTestEmail = function (email, $msg) {
				email = $.trim(email || '');
				if (!email) { paint($msg, 'error', lbl('enterEmail', 'Please enter an email address.')); return; }
				if (!EMAIL_RE.test(email)) { paint($msg, 'error', lbl('validEmail', 'Please enter a valid email address.')); return; }
				var data = collectEmailSettings();
				data.email = email;
				postTest('pjActionAjaxSend', data, $msg, null,
					lbl('sending', 'Sending test email, please wait...'),
					lbl('sendFail', 'The test email could not be sent.'));
			};
			
			if ($emailTestDialog.length > 0 && dialogAvailable) {
				$emailTestDialog.dialog({
					modal: true, autoOpen: false, resizable: false, draggable: false, width: 480,
					buttons: [
						{text: lbl('cancel', 'Cancel'), click: function () { $(this).dialog('close'); }},
						{text: lbl('sendEmail', 'Send Email'), click: function () { doSendTestEmail($('#emailTestModalEmail').val(), $('#emailTestModalMsg')); }}
					]
				});
			}
			
			// SMTP host + port are required when SMTP is selected; sender must look like an email address
			$frmEmail.on('submit', function (e) {
				var data = collectEmailSettings();
				if (data.send_email === 'smtp' && (!data.smtp_host || !data.smtp_port || parseInt(data.smtp_port, 10) <= 0)) {
					e.preventDefault();
					showTestResult('error', lbl('enterHost', 'Please enter the SMTP host and port first.'));
					return false;
				}
				if (data.from_email && !EMAIL_RE.test(data.from_email)) {
					e.preventDefault();
					showTestResult('error', lbl('validEmail', 'Please enter a valid email address.'));
					return false;
				}
				return true;
			});
			
			$("#content")
				.on("click", "#btnTestConnection", function (e) {
					e.preventDefault();
					var data = collectEmailSettings();
					if (!data.smtp_host || !data.smtp_port) {
						showTestResult('error', lbl('enterHost', 'Please enter the SMTP host and port first.'));
						return;
					}
					postTest('pjActionAjaxSmtp', {
						smtp_host: data.smtp_host, smtp_port: data.smtp_port,
						smtp_secure: data.smtp_secure, smtp_auth: data.smtp_auth,
						smtp_user: data.smtp_user, smtp_pass: data.smtp_pass
					}, $('#emailTestResult'), $(this),
					lbl('testing', 'Testing SMTP connection, please wait...'),
					lbl('connFail', 'Connection failed. Please check your SMTP settings.'));
				})
				.on("click", "#btnSendTestEmail", function (e) {
					e.preventDefault();
					var data = collectEmailSettings();
					if (data.send_email === 'smtp' && (!data.smtp_host || !data.smtp_port)) {
						showTestResult('error', lbl('enterHost', 'Please enter the SMTP host and port first.'));
						return;
					}
					if ($emailTestDialog.length > 0 && dialogAvailable) {
						$('#emailTestModalMsg').hide().empty();
						$('#emailTestModalEmail').val(data.from_email || '');
						$emailTestDialog.dialog('open');
						$('#emailTestModalEmail').focus();
					} else {
						var email = window.prompt(lbl('promptEmail', 'Enter the email address to send the test email to:'), data.from_email || '');
						if (email !== null) { doSendTestEmail(email, $('#emailTestResult')); }
					}
				});
		}
	});
})(jQuery_1_8_2);