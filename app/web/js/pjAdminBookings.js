var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
/*
 * Validation extras (jquery.validate 1.14 treats "   " as a filled-in value and has no ">0" / phone rule):
 *  - "required" now ignores leading/trailing spaces, so a spaces-only value is empty
 *  - gtzero : number must be greater than 0 (service price / duration)
 *  - phone  : 5-15 digits, may contain + ( ) . - and spaces
 */
(function ($) {
	if (!$ || !$.validator || $.validator.pjExtras) { return; }
	$.validator.pjExtras = true;
	var origRequired = $.validator.methods.required;
	$.validator.methods.required = function (value, element, param) {
		if (typeof value === "string" && element.nodeName.toLowerCase() !== "select" && !this.checkable(element)) { value = $.trim(value); }
		return origRequired.call(this, value, element, param);
	};
	$.validator.addMethod("gtzero", function (value, element) {
		return this.optional(element) || parseFloat($.trim(value).replace(/,/g, "")) > 0;
	}, "Please enter a value greater than 0.");
	$.validator.addMethod("phone", function (value, element) {
		if (this.optional(element)) { return true; }
		value = $.trim(value);
		var digits = value.replace(/\D/g, "");
		return /^\+?[0-9()\s.\-]+$/.test(value) && digits.length >= 5 && digits.length <= 15;
	}, "Please enter a valid phone number.");
	$.validator.addClassRules({gtzero: {gtzero: true}, phone: {phone: true}});
}(jQuery_1_8_2));
/* trim leading/trailing spaces when a text field loses focus */
function pjTrimOnBlur($forms) {
	$forms.on("blur", "input[type=text], textarea", function () {
		var v = this.value, t = v.replace(/^\s+|\s+$/g, "");
		if (v !== t) { this.value = t; }
	});
}

(function ($, undefined) {
	$(function () {
		"use strict";
		var validator,
			$frmCreateBooking = $('#frmCreateBooking'),
			$frmUpdateBooking = $('#frmUpdateBooking'),
			$dialogConfirmation = $("#dialogConfirmation"),
			$dialogCancellation = $("#dialogCancellation"),
			$dialogDuplicate = $("#dialogDuplicate"),
			dialog = ($.fn.dialog !== undefined),
			datepicker = ($.fn.datepicker !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			validate = ($.fn.validate !== undefined),
			multiselect = ($.fn.multiselect !== undefined),
			chosen = ($.fn.chosen !== undefined),
			spinner = ($.fn.spinner !== undefined),
			tabs = ($.fn.tabs !== undefined),
			$tabs = $("#tabs"),
			tOpt = {
				activate: function (event, ui) {
					$(":input[name='tab_id']").val($(ui.newPanel).prop('id'));
				}
			};
	
		if ($tabs.length > 0 && tabs) {
			$tabs.tabs(tOpt);
		}
		$(".field-int").spinner({
			min: 1
		});
		if (chosen) {
			$("#c_country").chosen();
		}
		if (multiselect) {
			
		}
		if ($frmCreateBooking.length > 0 || $frmUpdateBooking.length > 0) 
		{
			pjTrimOnBlur($frmCreateBooking.add($frmUpdateBooking));
			$frmCreateBooking.validate({
				rules: {
					"cc_type":{
						required: function(){
							if($('#payment_method').val() == 'creditcard')
							{
								return true;
							}else{
								return false;
							}
						}
					},
					"cc_num":{
						required: function(){
							if($('#payment_method').val() == 'creditcard')
							{
								return true;
							}else{
								return false;
							}
						}
					},
					"cc_code":{
						required: function(){
							if($('#payment_method').val() == 'creditcard')
							{
								return true;
							}else{
								return false;
							}
						}
					}
				},
				errorPlacement: function (error, element) {
					if(element.attr('name') == 'validate_service')
					{
						error.insertAfter(element);
					}else{
						error.insertAfter(element.parent());
					}
				},
				errorClass: "err",
				wrapper: "em",
				ignore: "",
				invalidHandler: function (event, validator) {
				    if (validator.numberOfInvalids()) {
				    	var index = $(validator.errorList[0].element, this).closest("div[id^='tabs-']").index();
				    	if ($tabs.length > 0 && tabs && index !== -1) {
				    		$tabs.tabs(tOpt).tabs("option", "active", index-1);
				    	}
				    };
				},
				submitHandler: function(form){
					if($frmCreateBooking.find('select[name="hour_iso"]').val() != '' && $frmCreateBooking.find('select[name="minute_iso"]').val() != '')
					{
						form.submit();
					}else{
						$tabs.tabs(tOpt).tabs("option", "active", 0);
						$('#pjSbsErrorCustom').parent().show();
					}
					return false;
				}
			});
			$frmUpdateBooking.validate({
				rules: {
					"cc_type":{
						required: function(){
							if($('#payment_method').val() == 'creditcard')
							{
								return true;
							}else{
								return false;
							}
						}
					},
					"cc_num":{
						required: function(){
							if($('#payment_method').val() == 'creditcard')
							{
								return true;
							}else{
								return false;
							}
						}
					},
					"cc_code":{
						required: function(){
							if($('#payment_method').val() == 'creditcard')
							{
								return true;
							}else{
								return false;
							}
						}
					}
				},
				errorPlacement: function (error, element) {
					if(element.attr('name') == 'validate_service')
					{
						error.insertAfter(element);
					}else{
						error.insertAfter(element.parent());
					}
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: "",
				invalidHandler: function (event, validator) {
				    if (validator.numberOfInvalids()) {
				    	var index = $(validator.errorList[0].element, this).closest("div[id^='tabs-']").index();
				    	if ($tabs.length > 0 && tabs && index !== -1) {
				    		$tabs.tabs(tOpt).tabs("option", "active", index-1);
				    	}
				    };
				},
				submitHandler: function(form){
					if($frmUpdateBooking.find('select[name="hour_iso"]').val() != '' && $frmUpdateBooking.find('select[name="minute_iso"]').val() != '')
					{
						$.post("index.php?controller=pjAdminBookings&action=pjActionDoubleCheck", $frmUpdateBooking.serialize()).done(function (data) {
							if(data == 'false')
							{
								$dialogDuplicate.dialog('open');
							}else{
								form.submit();
							}
						});
					}else{
						$tabs.tabs(tOpt).tabs("option", "active", 0);
						$('#pjSbsErrorCustom').parent().show();
					}
					return false;
				}
			});
			
		}
		if ($("#grid").length > 0 && datagrid) {
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminBookings&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminBookings&action=pjActionDeleteBooking&id={:id}"}
						 ],
				columns: [{text: myLabel.date_time, type: "text", sortable: true, editable: false, width:130},
				          {text: myLabel.name, type: "text", sortable: true, editable: false, width:160},
				          {text: myLabel.services, type: "text", sortable: false, editable: false, width:200},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, width: 100, options: [
				                                                                                     {label: myLabel.pending, value: "pending"}, 
				                                                                                     {label: myLabel.confirmed, value: "confirmed"},
				                                                                                     {label: myLabel.cancelled, value: "cancelled"}
				                                                                                     ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminBookings&action=pjActionGetBooking" + pjGrid.queryString,
				dataType: "json",
				fields: ['start_dt', 'c_name', 'services','status'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminBookings&action=pjActionDeleteBookingBulk", render: true, confirmation: myLabel.delete_confirmation}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminBookings&action=pjActionSaveBooking&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
		function noSunday(date){ 
	         return [date.getDay() != 0, ''];
		};
		function DisableSpecificDates(date) 
		{
			var m = date.getMonth();
			var d = date.getDate();
			var y = date.getFullYear();
			var day = date.getDay();
			var currentdate = (m + 1) + '-' + d + '-' + y ;
			if ($.inArray(currentdate, disabledDates) != -1 ) {
				return [false];
			}
			if ($.inArray(currentdate, enabledDates) != -1 ) {
				return [true];
			}
			if ($.inArray(day, disabledWeekDays) != -1 ) {
				return [false];
			}
			return [true];
		}
		$(document).on("focusin", ".datepick", function (e) {
			var minDate, maxDate,
				$this = $(this),
				custom = {},
				o = {
					firstDay: $this.attr("rel"),
					dateFormat: $this.attr("rev"),
				};
			switch ($this.attr("name")) {
				case "start_date":
					if($(".datepick[name='end_date']").val() != '')
					{
						maxDate = $(".datepick[name='end_date']").datepicker({
							firstDay: $this.attr("rel"),
							dateFormat: $this.attr("rev")
						}).datepicker("getDate");
						$(".datepick[name='end_date']").datepicker("destroy").removeAttr("id");
						if (maxDate !== null) {
							custom.maxDate = maxDate;
						}
					}
					break;
				case "end_date":
					if($(".datepick[name='start_date']").val() != '')
					{
						minDate = $(".datepick[name='start_date']").datepicker({
							firstDay: $this.attr("rel"),
							dateFormat: $this.attr("rev")
						}).datepicker("getDate");
						$(".datepick[name='start_date']").datepicker("destroy").removeAttr("id");
						if (minDate !== null) {
							custom.minDate = minDate;
						}
					}
					break;
			}
			$this.datepicker($.extend(o, custom));
		}).on("focusin", ".datetimepick", function (e) {
			var $this = $(this),
				custom = {},
				o = {
					firstDay: $this.attr("rel"),
					dateFormat: $this.attr("rev"),
					beforeShowDay: DisableSpecificDates, 
					onClose: function (dateText){
						getHour(0);
					}
			};
			if ($frmCreateBooking.length > 0) 
			{
				custom.minDate = 0;
			}
			$(this).datepicker($.extend(o, custom));
			
		}).on("click", ".pj-form-field-icon-date", function (e) {
			var $dp = $(this).parent().siblings("input[type='text']");
			if ($dp.hasClass("hasDatepicker")) {
				$dp.datepicker("show");
			} else {
				$dp.trigger("focusin").datepicker("show");
			}
			
		}).on("click", ".btn-all", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				status: "",
				q: "",
				uuid: "",
				client: "",
				service_id: "",
				start_date: "",
				end_date: "",
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking", "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("click", ".btn-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache"),
				obj = {};
			$this.addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			obj.status = "";
			obj[$this.data("column")] = $this.data("value");
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking", "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("submit", ".frm-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				q: $this.find("input[name='q']").val(),
				uuid: "",
				client: "",
				service_id: "",
				start_date: "",
				end_date: "",
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking", "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("click", ".pj-button-detailed, .pj-button-detailed-arrow", function (e) {
			e.stopPropagation();
			$(".pj-form-filter-advanced").toggle();
		}).on("submit", ".frm-filter-advanced", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var obj = {},
				$this = $(this),
				arr = $this.serializeArray(),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			for (var i = 0, iCnt = arr.length; i < iCnt; i++) {
				obj[arr[i].name] = arr[i].value;
			}
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking", "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("reset", ".frm-filter-advanced", function (e) {
			$(".pj-button-detailed").trigger("click");
			$("#client").val('');
			$("#service_id").val('');
			$("#uuid").val('');
			$("#start_date").val('');
			$("#end_date").val('');
		}).on("change", "#payment_method", function (e) {
			switch ($("option:selected", this).val()) {
				case 'creditcard':
					$(".boxCC").show();
					break;
				default:
					$(".boxCC").hide();
			}
		}).on("click", ".pjSbsSendConfirm", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$dialogConfirmation.data('id', $(this).attr('data-id')).dialog('open');
		}).on("click", ".pjSbsSendCancel", function (e) {
			if (e && e.pjSbsSendCancel) {
				e.preventDefault();
			}
			$dialogCancellation.data('id', $(this).attr('data-id')).dialog('open');
		}).on("click", ".pjSbsExtraCheckbox", function (e) {
			calculatePrice();
		}).on("click", ".pjSbsServiceCheckbox", function (e) {
			var checked = false;
			$('.pjSbsServiceCheckbox').each(function(e){
				if($(this).is(':checked'))
				{
					checked = true;
				}
			});
			if(checked == false)
			{
				$('#hiddenValidateService').val("");
			}else{
				$('#hiddenValidateService').val("1").valid();
			}
			calculatePrice();
		}).on("change", "#export_period", function (e) {
			var period = $(this).val();
			if(period == 'last')
			{
				$('#last_label').show();
				$('#next_label').hide();
			}else{
				$('#last_label').hide();
				$('#next_label').show();
			}
		}).on("click", "#file", function (e) {
			$('#cpSubmitButton').val(myLabel.btn_export);
			$('.cpFeedContainer').hide();
			$('.cpPassowrdContainer').hide();
		}).on("click", "#feed", function (e) {
			$('.cpPassowrdContainer').show();
			$('#cpSubmitButton').val(myLabel.btn_get_url);
		}).on("focus", "#bookings_feed", function (e) {
			$(this).select();
		}).on("change", "select[name='hour_iso']", function (e) {
			getMinute();
		}).on("change", "select[name='minute_iso']", function (e) {
			var $frm = null
			if ($frmCreateBooking.length > 0) 
			{
				$frm = $frmCreateBooking;
			}
			if ($frmUpdateBooking.length > 0) 
			{
				$frm = $frmUpdateBooking;
			}
			if($frm.find('select[name="hour_iso"]').val() != '' && $frm.find('select[name="minute_iso"]').val() != '')
			{
				$('#pjSbsErrorCustom').parent().hide();
			}
		});
		
		if ($dialogDuplicate.length > 0 && dialog) {
			$dialogDuplicate.dialog({
				autoOpen: false,
				draggable: false,
				resizable: false,
				modal: true,
				width: 450,
				buttons: (function () {
					var buttons = {};
					buttons[sbsApp.locale.button.ok] = function () {
						$dialogDuplicate.dialog("close");
					};
					
					return buttons;
				})()
			});
		}
		if ($dialogConfirmation.length > 0 && dialog) {
			$dialogConfirmation.dialog({
				autoOpen: false,
				draggable: false,
				resizable: false,
				modal: true,
				width: 645,
				open: function () {
					$dialogConfirmation.html("");
					$.get("index.php?controller=pjAdminBookings&action=pjActionConfirmation", {
						"booking_id": $dialogConfirmation.data('id')
					}).done(function (data) {
						$dialogConfirmation.html(data);
						validator = $dialogConfirmation.find("form").validate({
							
						});
						$dialogConfirmation.dialog("option", "position", "center");
						attachTinyMce.call(null);
					});
				},
				buttons: (function () {
					var buttons = {};
					buttons[sbsApp.locale.button.send] = function () {
						tinymce.get("confirm_message").save();	
						if (validator.form()) {
							$.post("index.php?controller=pjAdminBookings&action=pjActionConfirmation", $dialogConfirmation.find("form").serialize()).done(function (data) {
								$dialogConfirmation.dialog("close");
							})
						}
					};
					buttons[sbsApp.locale.button.cancel] = function () {
						$dialogConfirmation.dialog("close");
					};
					
					return buttons;
				})()
			});
		}
		if ($dialogCancellation.length > 0 && dialog) {
			$dialogCancellation.dialog({
				autoOpen: false,
				draggable: false,
				resizable: false,
				modal: true,
				width: 645,
				open: function () {
					$dialogConfirmation.html("");
					$.get("index.php?controller=pjAdminBookings&action=pjActionCancellation", {
						"booking_id": $dialogCancellation.data('id')
					}).done(function (data) {
						$dialogCancellation.html(data);
						validator = $dialogCancellation.find("form").validate({
							
						});
						$dialogCancellation.dialog("option", "position", "center");
						attachTinyMce.call(null);
					});
				},
				buttons: (function () {
					var buttons = {};
					buttons[sbsApp.locale.button.send] = function () {
						tinymce.get("confirm_message").save();	
						if (validator.form()) {
							$.post("index.php?controller=pjAdminBookings&action=pjActionCancellation", $dialogCancellation.find("form").serialize()).done(function (data) {
								$dialogCancellation.dialog("close");
							})
						}
					};
					buttons[sbsApp.locale.button.cancel] = function () {
						$dialogCancellation.dialog("close");
					};
					
					return buttons;
				})()
			});
		}
		
		function getHour(loadMinute)
		{
			var $frm = null
			if ($frmCreateBooking.length > 0) 
			{
				$frm = $frmCreateBooking;
			}
			if ($frmUpdateBooking.length > 0) 
			{
				$frm = $frmUpdateBooking;
			}
			$.post("index.php?controller=pjAdminBookings&action=pjActionGetHour", $frm.serialize()).done(function (data) {
				$('#hourWrapper').html(data);
				if(loadMinute == 1)
				{
					getMinute();
				}
				if($frm.find('select[name="hour_iso"]').val() != '' && $frm.find('select[name="minute_iso"]').val() != '')
				{
					$('#pjSbsErrorCustom').parent().hide();
				}
			});
		}
		function getMinute()
		{
			var $frm = null
			if ($frmCreateBooking.length > 0) 
			{
				$frm = $frmCreateBooking;
			}
			if ($frmUpdateBooking.length > 0) 
			{
				$frm = $frmUpdateBooking;
			}
			$.post("index.php?controller=pjAdminBookings&action=pjActionGetMinute", $frm.serialize()).done(function (data) {
				$('#minuteWrapper').html(data);
				if($frm.find('select[name="hour_iso"]').val() != '' && $frm.find('select[name="minute_iso"]').val() != '')
				{
					$('#pjSbsErrorCustom').parent().hide();
				}
			});
		}
		
		function getTime()
		{
			var $frm = null
			if ($frmCreateBooking.length > 0) 
			{
				$frm = $frmCreateBooking;
			}
			if ($frmUpdateBooking.length > 0) 
			{
				$frm = $frmUpdateBooking;
			}
			$.post("index.php?controller=pjAdminBookings&action=pjActionGetTime", $frm.serialize()).done(function (data) {
				$('#timeWrapper').html(data);
				if($frm.find('select[name="time_iso"]').val() != '')
				{
					$('#pjSbsErrorCustom').parent().hide();
				}
			});
		}

		function attachTinyMce(options) {
			if (window.tinymce !== undefined) {
				tinymce.EditorManager.editors = [];
				var defaults = {
					selector: "textarea.mceEditor",
					theme: "modern",
					width: 610,
					height: 330,
					plugins: [
				         "advlist autolink link image lists charmap print preview hr anchor pagebreak",
				         "searchreplace visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
				         "save table contextmenu directionality emoticons template paste textcolor"
				    ],
				    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons"
				};
				
				var settings = $.extend({}, defaults, options);
				
				tinymce.init(settings);
			}
		}
		function calculatePrice()
		{
			var subtotal = 0;
			var tax = 0;
			var total = 0;
			var deposit = 0;
			var duration = 0;
			
			$('.pjSbsServiceCheckbox, .pjSbsExtraCheckbox').each(function(e){
				if($(this).is(':checked'))
				{
					subtotal += parseFloat($(this).val());
					duration += parseInt($(this).attr('data-duration'), 10);
				}
			});
			
			if(subtotal > 0)
			{
				var tax_percentage = parseFloat($('#tax').attr('data-tax'));
				var deposit_percentage = parseFloat($('#deposit').attr('data-deposit'));
				tax = (subtotal * tax_percentage) / 100;
				total = subtotal + tax;
				deposit = (total * deposit_percentage) / 100;
				$('#subtotal').val(subtotal.toFixed(2));
				$('#tax').val(tax.toFixed(2));
				$('#total').val(total.toFixed(2));
				$('#deposit').val(deposit.toFixed(2));
				$('#duration').val(duration);
			}else{
				$('#subtotal').val('');
				$('#tax').val('');
				$('#total').val('');
				$('#deposit').val('');
				$('#duration').val('');
			}
			getHour(1);
		}
		function formatCurrency(price, currency, separator)
		{
			var format = '---';
			switch (currency)
			{
				case 'USD':
					format = "$" + separator + price.toFixed(2);
					break;
				case 'GBP':
					format = "&pound;" + separator  + price.toFixed(2);
					break;
				case 'EUR':
					format = "&euro;" + separator  + price.toFixed(2);
					break;
				case 'JPY':
					format = "&yen;" + separator  + price.toFixed(2);
					break;
				case 'AUD':
				case 'CAD':
				case 'NZD':
				case 'CHF':
				case 'HKD':
				case 'SGD':
				case 'SEK':
				case 'DKK':
				case 'PLN':
					format = price.toFixed(2) + separator  + currency;
					break;
				case 'NOK':
				case 'HUF':
				case 'CZK':
				case 'ILS':
				case 'MXN':
					format = currency + separator  + price.toFixed(2);
					break;
				default:
					format = price.toFixed(2) + separator  + currency;
					break;
			}
			return format;
		}
	});
})(jQuery_1_8_2);