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

/*
 * Service categories + extras admin pages (add / edit forms and the two grids).
 */
(function ($, undefined) {
	$(function () {
		var $forms = $(".pj-catalog-form"),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			$grid = null;

		pjTrimOnBlur($forms);
		$(".field-int-zero").spinner({
			min: 0
		});
		if ($forms.length > 0 && validate) {
			$forms.each(function () {
				$(this).validate({
					errorPlacement: function (error, element) {
						if (element.attr('name') == 'duration') {
							error.insertAfter(element.parent().parent());
						} else {
							error.insertAfter(element.parent());
						}
					},
					onkeyup: false,
					errorClass: "err",
					wrapper: "em",
					ignore: "",
					invalidHandler: function (event, validator) {
						var localeId = $(validator.errorList[0].element, this).attr('lang');
						if (localeId != undefined) {
							$(".pj-multilang-wrap").each(function () {
								$(this).css('display', $(this).attr('data-index') == localeId ? 'block' : 'none');
							});
							$(".pj-form-langbar-item").each(function () {
								if ($(this).attr('data-index') == localeId) {
									$(this).addClass('pj-form-langbar-item-active');
								} else {
									$(this).removeClass('pj-form-langbar-item-active');
								}
							});
						}
					}
				});
			});
			if (myLabel.locale_array && myLabel.locale_array.length > 0) {
				for (var i = 0; i < myLabel.locale_array.length; i++) {
					$("#i18n_title_" + myLabel.locale_array[i]).rules('add', {
						messages: {required: myLabel.field_required}
					});
				}
			}
		}

		var $gridEl = $("#grid"),
			entity = $gridEl.attr("data-entity"),
			cfg = null;
		if (entity == "category") {
			cfg = {
				ctrl: "pjAdminCategories", id: "Category",
				fields: ['title', 'cnt_services', 'status'],
				columns: [{text: myLabel.title, type: "text", sortable: true, editable: true, width: 260, editableWidth: 240},
				          {text: myLabel.services, type: "text", sortable: true, editable: false, width: 90, align: 'center'},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, width: 100, editableWidth: 80, options: [
				        	  {label: myLabel.active, value: "T"}, {label: myLabel.inactive, value: "F"}], applyClass: "pj-status"}]
			};
		} else if (entity == "extra") {
			cfg = {
				ctrl: "pjAdminExtras", id: "Extra",
				fields: ['title', 'price', 'duration', 'cnt_services', 'status'],
				columns: [{text: myLabel.title, type: "text", sortable: true, editable: true, width: 220, editableWidth: 200},
				          {text: myLabel.price, type: "text", sortable: true, editable: false, width: 80},
				          {text: myLabel.duration, type: "text", sortable: true, editable: false, width: 100},
				          {text: myLabel.services, type: "text", sortable: true, editable: false, width: 90, align: 'center'},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, width: 100, editableWidth: 80, options: [
				        	  {label: myLabel.active, value: "T"}, {label: myLabel.inactive, value: "F"}], applyClass: "pj-status"}]
			};
		}
		if (cfg && datagrid) {
			var base = "index.php?controller=" + cfg.ctrl + "&action=";
			$grid = $gridEl.datagrid({
				buttons: [{type: "edit", url: base + "pjActionUpdate&id={:id}"},
				          {type: "delete", url: base + "pjActionDelete" + cfg.id + "&id={:id}"}],
				columns: cfg.columns,
				dataUrl: base + "pjActionGet" + cfg.id + pjGrid.queryString,
				dataType: "json",
				fields: cfg.fields,
				paginator: {
					actions: [
						{text: myLabel.delete_selected, url: base + "pjActionDelete" + cfg.id + "Bulk", render: true, confirmation: myLabel.delete_confirmation}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: base + "pjActionSave" + cfg.id + "&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
			/* a category that still has services is refused by the server (code 101) */
			$(document).ajaxComplete(function (e, xhr, opts) {
				if (opts && opts.url && opts.url.indexOf("pjActionDeleteCategory&") > -1 && xhr.responseText && xhr.responseText.indexOf('"code":101') > -1) {
					$(".ui-dialog-content:visible").dialog("close");
					// popup (same look as the delete confirmation) instead of a browser alert
					var $inUse = $("#pjCatalogInUseDialog"), btns = {};
					if ($inUse.length === 0) {
						$inUse = $('<div id="pjCatalogInUseDialog"><p></p></div>').appendTo("body");
					}
					$inUse.find("p").text(myLabel.category_in_use);
					btns[myLabel.ok || "OK"] = function () { $(this).dialog("close"); };
					$inUse.dialog({
						autoOpen: true,
						modal: true,
						resizable: false,
						draggable: false,
						width: 420,
						title: myLabel.cannot_delete || "",
						buttons: btns,
						close: function () { $(this).dialog("destroy"); }
					});
				}
			});
			var reload = function (obj) {
				var content = $grid.datagrid("option", "content"),
					cache = $grid.datagrid("option", "cache");
				$.extend(cache, obj);
				$grid.datagrid("option", "cache", cache);
				$grid.datagrid("load", base + "pjActionGet" + cfg.id, "title", "ASC", content.page, content.rowCount);
			};
			$(document).on("click", ".btn-all", function (e) {
				if (e && e.preventDefault) { e.preventDefault(); }
				$(this).addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
				reload({status: "", q: ""});
				return false;
			}).on("click", ".btn-filter", function (e) {
				if (e && e.preventDefault) { e.preventDefault(); }
				var $this = $(this), obj = {status: ""};
				$this.addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
				obj[$this.data("column")] = $this.data("value");
				reload(obj);
				return false;
			}).on("submit", ".frm-filter", function (e) {
				if (e && e.preventDefault) { e.preventDefault(); }
				reload({q: $(this).find("input[name='q']").val()});
				return false;
			});
		}
		$(document).on("keydown", "#price", function (e) {
			if (e.shiftKey == true) {
				e.preventDefault();
			}
			if ((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105) || e.keyCode == 8 || e.keyCode == 190 || e.keyCode == 9 || e.keyCode == 37 || e.keyCode == 39 || e.keyCode == 46) {
			} else {
				e.preventDefault();
			}
		});
	});
})(jQuery_1_8_2);
