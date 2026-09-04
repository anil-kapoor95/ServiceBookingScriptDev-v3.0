var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		var $frmCreateService = $("#frmCreateService"),
			$frmUpdateService = $("#frmUpdateService"),
			dialog = ($.fn.dialog !== undefined),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined);
		
		$(".field-int").spinner({
			min: 0
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
				columns: [{text: myLabel.title, type: "text", sortable: true, editable: true, width: 220, editableWidth: 200},
				          {text: myLabel.price, type: "text", sortable: true, editable: false, width: 80},
				          {text: myLabel.duration, type: "text", sortable: true, editable: false, width: 100},
				          {text: myLabel.bookings, type: "text", sortable: true, editable: false, width: 80, align: 'center', renderer: formatBookings},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, width: 100, editableWidth: 80, options: [
			                                                                                     {label: myLabel.active, value: "T"}, 
			                                                                                     {label: myLabel.inactive, value: "F"}
			                                                                                     ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminServices&action=pjActionGetService" + pjGrid.queryString,
				dataType: "json",
				fields: ['title', 'price', 'duration', 'cnt_bookings', 'status'],
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
				q: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminServices&action=pjActionGetService", "title", "ASC", content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminServices&action=pjActionGetService", "title", "ASC", content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminServices&action=pjActionGetService", "title", "ASC", content.page, content.rowCount);
			return false;
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