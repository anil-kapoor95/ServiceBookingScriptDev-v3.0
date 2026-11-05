var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
/*
 * Vouchers admin: the list (grid with inline edit) and the add / edit form.
 */
(function ($, undefined) {
	$(function () {
		"use strict";
		var $form = $(".pj-voucher-form"),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			$gridEl = $("#grid[data-entity='voucher']"),
			myLabel = window.myLabel || {};

		/* spaces around the text are removed when a field loses focus */
		$(document).on("blur", ".pj-voucher-form input[type=text]", function () {
			var v = this.value, t = v.replace(/^\s+|\s+$/g, "");
			if (v !== t) { this.value = t; }
		});

		/* ------------------------------------------------------------ list */
		if ($gridEl.length > 0 && datagrid) {
			var base = "index.php?controller=pjAdminVouchers&action=";
			var $grid = $gridEl.datagrid({
				buttons: [{type: "edit", url: base + "pjActionUpdate&id={:id}"},
				          {type: "delete", url: base + "pjActionDeleteVoucher&id={:id}"}],
				columns: [
					{text: myLabel.code, type: "text", sortable: true, editable: true, width: 200, editableWidth: 180},
					{text: myLabel.discount, type: "text", sortable: true, editable: true, width: 110, editableWidth: 80, align: "right",
						renderer: function (val, row) { return val + " " + (row && row.sign ? row.sign : ""); }},
					{text: myLabel.type, type: "text", sortable: true, editable: false, width: 110},
					{text: myLabel.valid, type: "text", sortable: true, editable: false, width: 110},
					{text: myLabel.services, type: "text", sortable: false, editable: false, width: 200}
				],
				dataUrl: base + "pjActionGetVoucher" + pjGrid.queryString,
				dataType: "json",
				fields: ['code', 'discount', 'type', 'valid', 'services'],
				paginator: {
					actions: [
						{text: myLabel.delete_selected, url: base + "pjActionDeleteVoucherBulk", render: true, confirmation: myLabel.delete_confirmation}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: base + "pjActionSaveVoucher&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});

			var reload = function (obj) {
				var content = $grid.datagrid("option", "content"),
					cache = $grid.datagrid("option", "cache");
				$.extend(cache, obj);
				$grid.datagrid("option", "cache", cache);
				$grid.datagrid("load", base + "pjActionGetVoucher", "code", "ASC", content.page, content.rowCount);
			};

			/* a refused inline edit (empty / duplicate code, bad discount): say why and show the saved value again */
			$(document).ajaxComplete(function (e, xhr, opts) {
				if (!opts || !opts.url || opts.url.indexOf("pjActionSaveVoucher") < 0 || !xhr.responseText) { return; }
				var res = null, msg = "";
				try { res = $.parseJSON(xhr.responseText); } catch (err) { res = null; }
				if (!res || res.status !== "ERR") { return; }
				if (res.code == 104) { msg = myLabel.discount_max; }
				else if (res.code == 106) { msg = myLabel.code_exists; }
				else if (res.code == 105) { msg = myLabel.field_required; }
				if (msg) {
					$("<div></div>").text(msg).dialog({
						modal: true, resizable: false, draggable: false, width: 380,
						buttons: [{text: "OK", click: function () { $(this).dialog("close"); }}],
						close: function () { $(this).dialog("destroy").remove(); }
					});
				}
				reload({});
			});

			$(document).on("submit", ".frm-filter", function (e) {
				if (e && e.preventDefault) { e.preventDefault(); }
				reload({q: $(this).find("input[name='q']").val()});
				return false;
			});
		}

		/* ------------------------------------------------------------ add / edit form */
		if ($form.length === 0) { return; }

		var jqDate = $("#voucher_date_format").val() || "dd.mm.yy",
			weekStart = parseInt($("#voucher_week_start").val(), 10) || 0,
			$valid = $("#valid"),
			$type = $("#type");

		if ($.fn.datepicker !== undefined) {
			$(".pj-voucher-date").datepicker({
				dateFormat: jqDate,
				firstDay: weekStart,
				showOn: "both",
				buttonImageOnly: false,
				onSelect: function () { checkRules(); }
			});
		}

		function showBox() {
			$(".valid-box").hide();
			if ($valid.val()) { $("#valid_" + $valid.val()).show(); }
			checkRules(true);
		}
		function updateSign() {
			$("#voucherSign").html($type.val() === "percent" ? "%" : $type.attr("data-currency")); // the sign comes from the server as an HTML entity
			if (validate && $("#discount").val() !== "") { $("#discount").valid(); }
		}

		/* minutes since midnight of an hour / minute / am-pm select group */
		function minutes(prefix) {
			var h = parseInt($("#" + prefix + "_hour").val(), 10) || 0,
				m = parseInt($("#" + prefix + "_minute").val(), 10) || 0,
				$ap = $("#" + prefix + "_ampm");
			if ($ap.length > 0) {
				var ap = String($ap.val()).toLowerCase();
				if (ap === "pm" && h < 12) { h += 12; }
				if (ap === "am" && h === 12) { h = 0; }
			}
			return h * 60 + m;
		}
		function dateOf(id) {
			try { return $.datepicker.parseDate(jqDate, $("#" + id).val()); } catch (err) { return null; }
		}
		/* end must be later than start; shows / hides the message under the "to" fields */
		function checkRules(silent) {
			var kind = $valid.val(), ok = true, $holder = null;
			$(".voucher-rule-msg").remove();
			if (kind === "fixed") {
				ok = minutes("f_to") > minutes("f_from");
				$holder = $("#validate_fixedtime");
			} else if (kind === "recurring") {
				ok = minutes("r_to") > minutes("r_from");
				$holder = $("#validate_time");
			} else if (kind === "period") {
				var d1 = dateOf("p_date_from"), d2 = dateOf("p_date_to");
				if (!d1 || !d2) {
					ok = false;
				} else {
					ok = (d2.getTime() + minutes("p_to") * 60000) > (d1.getTime() + minutes("p_from") * 60000);
				}
				$holder = $("#validate_datetime");
			}
			if (!ok && $holder && !silent) {
				$("<em class=\"voucher-rule-msg\"></em>").append($("<label class=\"err\"></label>").text($holder.attr("data-msg"))).insertAfter($holder.parent());
			}
			return ok;
		}

		/* services: all, or only the ticked ones (at least one) */
		function servicesSelected() {
			return $form.find("input[name='services_scope']:checked").val() === "selected";
		}
		/* the services are picked from a dropdown under the radio (it scrolls); it can be used only with "Only selected services" */
		function updateServicesLabel() {
			var names = [];
			$("#voucherServicesList input:checked").each(function () { names.push($(this).attr("data-title")); });
			var txt = names.length ? names.join(", ") : ($("#validate_services").attr("data-none") || "-- Select services --");
			$("#voucherServicesLabel").text(txt);
			$("#voucherServicesToggle").attr("title", txt);
		}
		function closeServices() {
			$("#voucherServicesList").hide();
		}
		function showServices() {
			var on = servicesSelected();
			$("#voucherServicesBox").toggleClass("voucher-services-off", !on).toggle(on);
			$("#voucherServicesList input").prop("disabled", !on);
			if (!on) { closeServices(); }
			updateServicesLabel();
			checkServices(true);
		}
		function checkServices(silent) {
			var ok = !servicesSelected() || $form.find("input[name='service_id[]']:checked").length > 0;
			$(".voucher-services-msg").remove();
			if (!ok && !silent) {
				$("<em class=\"voucher-services-msg\"></em>").css({"margin-left": 0, "width": "320px"}).append($("<label class=\"err\"></label>").text($("#validate_services").attr("data-msg"))).insertAfter($("#voucherServicesBox"));
			}
			return ok;
		}
		$form.on("change", "input[name='services_scope']", showServices);
		$form.on("change", "input[name='service_id[]']", function () { updateServicesLabel(); checkServices(); });
		$form.on("click", "#voucherServicesToggle", function (e) {
			e.preventDefault();
			if (!servicesSelected()) { return; }
			$("#voucherServicesList").toggle();
		});
		$(document).on("click", function (e) {
			if (!$(e.target).closest("#voucherServicesBox").length) { closeServices(); }
		});
		updateServicesLabel();

		$valid.on("change", showBox);
		$type.on("change", updateSign);
		$form.on("change", ".valid-box select", function () { checkRules(); });
		showBox();

		if (validate) {
			$form.validate({
				rules: {
					code: {
						required: true,
						remote: {
							url: "index.php?controller=pjAdminVouchers&action=pjActionCheckCode",
							type: "get",
							data: { id: function () { return $("#voucher_id").val() || 0; } }
						}
					},
					discount: {
						required: true,
						number: true,
						min: 0.01,
						max: function () { return $type.val() === "percent" ? 100 : 9999999; }
					},
					valid: { required: true }
				},
				messages: {
					discount: {
						min: myLabel.voucher_discount_min,
						max: function () { return $type.val() === "percent" ? myLabel.voucher_discount_max : myLabel.number_invalid; },
						number: myLabel.number_invalid
					}
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				errorPlacement: function (error, element) {
					error.insertAfter(element.closest(".inline_block"));
				},
				ignore: "",
				submitHandler: function (f) {
					if (!checkServices()) { return false; }
					if (!checkRules()) { return false; }
					f.submit();
				}
			});
		}
	});
})(jQuery_1_8_2);
