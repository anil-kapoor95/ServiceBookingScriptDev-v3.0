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
		var $frmCreateService = $("#frmCreateService"),
			$frmUpdateService = $("#frmUpdateService"),
			dialog = ($.fn.dialog !== undefined),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined);
		
		pjTrimOnBlur($frmCreateService.add($frmUpdateService));
		
		/* extras: compact dropdown with checkboxes (selected names + count shown on the button) */
		$(".pj-ms").each(function () {
			var $ms = $(this),
				$toggle = $ms.find(".pj-ms-toggle"),
				$text = $ms.find(".pj-ms-text"),
				$count = $ms.find(".pj-ms-count"),
				refresh = function () {
					var names = [];
					$ms.find("input:checked").each(function () {
						names.push($(this).attr("data-title"));
					});
					if (names.length > 0) {
						$text.removeClass("pj-ms-placeholder").text(names.join(", "));
						$count.text(names.length).show();
					} else {
						$text.addClass("pj-ms-placeholder").text($text.attr("data-placeholder"));
						$count.hide();
					}
					$toggle.attr("title", names.join(", "));
				},
				close = function () {
					$ms.removeClass("pj-ms-open");
					$toggle.attr("aria-expanded", "false");
				};
			$toggle.on("click", function (e) {
				e.preventDefault();
				var open = !$ms.hasClass("pj-ms-open");
				$(".pj-ms").removeClass("pj-ms-open");
				if (open) {
					$ms.addClass("pj-ms-open");
					$toggle.attr("aria-expanded", "true");
				}
			});
			$ms.find("input:checkbox").on("change", refresh);
			$(document).on("click", function (e) {
				if (!$(e.target).closest(".pj-ms").length) {
					close();
				}
			}).on("keydown", function (e) {
				if (e.which === 27) {
					close();
				}
			});
			refresh();
		});
		$(".field-int").spinner({
			min: 1
		});
		if ($frmCreateService.length > 0 && validate) {
			$frmCreateService.validate({
				errorPlacement: function (error, element) {					
					if(element.attr('name') == 'duration')
					{
						error.insertAfter(element.parent().parent());
					}else{
						error.insertAfter(element.parent());
					}
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: "",
				invalidHandler: function (event, validator) {
					var localeId = $(validator.errorList[0].element, this).attr('lang');
					if(localeId != undefined)
					{
						$(".pj-multilang-wrap").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).css('display','block');
							}else{
								$(this).css('display','none');
							}
						});
						$(".pj-form-langbar-item").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).addClass('pj-form-langbar-item-active');
							}else{
								$(this).removeClass('pj-form-langbar-item-active');
							}
						});
					}
				}
			});
		}
		if ($frmUpdateService.length > 0 && validate) {
			$frmUpdateService.validate({
				errorPlacement: function (error, element) {
					if(element.attr('name') == 'duration')
					{
						error.insertAfter(element.parent().parent());
					}else{
						error.insertAfter(element.parent());
					}
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: "",
				invalidHandler: function (event, validator) {
					var localeId = $(validator.errorList[0].element, this).attr('lang');
					if(localeId != undefined)
					{
						$(".pj-multilang-wrap").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).css('display','block');
							}else{
								$(this).css('display','none');
							}
						});
						$(".pj-form-langbar-item").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).addClass('pj-form-langbar-item-active');
							}else{
								$(this).removeClass('pj-form-langbar-item-active');
							}
						});
					}
				}
			});
		}
		if ($frmCreateService.length > 0 || $frmUpdateService.length > 0) 
		{
			if(myLabel.locale_array.length > 0)
			{
				var locale_array = myLabel.locale_array;
				for(var i = 0; i < locale_array.length; i++)
				{
					var element = $("#i18n_title_" + locale_array[i]);
					element.rules('add', {
						messages: {
					    	required: myLabel.field_required
					    }
					});
				}
			}
		}
		function formatBookings (str, obj) {
			if (parseInt(obj.cnt_bookings, 10) > 0) {
				return '<a href="index.php?controller=pjAdminBookings&action=pjActionIndex&service_id='+obj.id+'">'+str+'</a>';
			} else {
				return 0;
			}
		}
		if ($("#grid").length > 0 && datagrid) {
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminServices&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminServices&action=pjActionDeleteService&id={:id}"}
				          ],
				columns: [{text: myLabel.order, type: "text", sortable: true, editable: true, width: 60, editableWidth: 44},
				          {text: myLabel.title, type: "text", sortable: true, editable: true, width: 150, editableWidth: 130},
				          {text: myLabel.category, type: "text", sortable: true, editable: false, width: 110},
				          {text: myLabel.price, type: "text", sortable: true, editable: false, width: 70},
				          {text: myLabel.duration, type: "text", sortable: true, editable: false, width: 90},
				          {text: myLabel.bookings, type: "text", sortable: true, editable: false, width: 70, align: 'center', renderer: formatBookings},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, width: 90, editableWidth: 76, options: [
			                                                                                     {label: myLabel.active, value: "T"}, 
			                                                                                     {label: myLabel.inactive, value: "F"}
			                                                                                     ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminServices&action=pjActionGetService" + pjGrid.queryString,
				dataType: "json",
				fields: ['sort_order', 'title', 'category', 'price', 'duration', 'cnt_bookings', 'status'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminServices&action=pjActionDeleteServiceBulk", render: true, confirmation: myLabel.delete_confirmation}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminServices&action=pjActionSaveService&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
		
		$(document).on("click", ".btn-all", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				status: "",
				q: "",
				category_id: ""
			});
			$("#filter_category_id").val("");
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminServices&action=pjActionGetService", "sort_order", "ASC", content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminServices&action=pjActionGetService", "sort_order", "ASC", content.page, content.rowCount);
			return false;
		}).on("submit", ".frm-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				q: $this.find("input[name='q']").val()
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminServices&action=pjActionGetService", "sort_order", "ASC", content.page, content.rowCount);
			return false;
		}).on("change", "#filter_category_id", function (e) {
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				category_id: $(this).val()
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminServices&action=pjActionGetService", "sort_order", "ASC", content.page, content.rowCount);
		}).on("click", ".pj-delete-image", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$dialogDelete.data('href', $(this).data('href')).dialog("open");
		}).on("keydown", "#price", function (e) {
			if (e.shiftKey == true) {
                e.preventDefault();
            }
			if ((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105) || e.keyCode == 8 || e.keyCode == 190 || e.keyCode == 9 ||e.keyCode == 37 || e.keyCode == 39 || e.keyCode == 46) {
				
            } else {
            	e.preventDefault();
            } 
		});
	});
})(jQuery_1_8_2);