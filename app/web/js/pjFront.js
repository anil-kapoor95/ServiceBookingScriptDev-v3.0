(function (window, undefined){
	"use strict";
	pjQ.$.ajaxSetup({
		xhrFields: {
			withCredentials: true
		}
	});
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
	}(pjQ.$));
	/* trim leading/trailing spaces when a text field loses focus */
	function pjTrimOnBlur($forms) {
		$forms.on("blur", "input[type=text], textarea", function () {
			var v = this.value, t = v.replace(/^\s+|\s+$/g, "");
			if (v !== t) { this.value = t; }
		});
	}

	var document = window.document,
		validate = (pjQ.$.fn.validate !== undefined),
		routes = [
		          	{pattern: /^#!\/loadServices$/, eventName: "loadServices"},
		          	{pattern: /^#!\/loadDateTime$/, eventName: "loadDateTime"},
					{pattern: /^#!\/loadCheckout$/, eventName: "loadCheckout"},
		          	{pattern: /^#!\/loadPreview$/, eventName: "loadPreview"},
		         ];
	
	function log() {
		if (window.console && window.console.log) {
			for (var x in arguments) {
				if (arguments.hasOwnProperty(x)) {
					window.console.log(arguments[x]);
				}
			}
		}
	}
	
	function assert() {
		if (window && window.console && window.console.assert) {
			window.console.assert.apply(window.console, arguments);
		}
	}
	
	function hashBang(value) {
		if (value !== undefined && value.match(/^#!\//) !== null) {
			if (window.location.hash == value) {
				return false;
			}
			window.location.hash = value;
			return true;
		}
		
		return false;
	}
	
	function onHashChange() {
		var i, iCnt, m;
		for (i = 0, iCnt = routes.length; i < iCnt; i++) {
			m = window.location.hash.match(routes[i].pattern);
			if (m !== null) {
				pjQ.$(window).trigger(routes[i].eventName, m.slice(1));
				break;
			}
		}
		if (m === null) {
			pjQ.$(window).trigger("loadServices");
		}
	}
	pjQ.$(window).on("hashchange", function (e) {
    	onHashChange.call(null);
    });
	
	function ServiceBooking(opts) {
		if (!(this instanceof ServiceBooking)) {
			return new ServiceBooking(opts);
		}
				
		this.reset.call(this);
		this.init.call(this, opts);
		
		return this;
	}
	function refreshFirstLastVisible(event) {
		var $items = pjQ.$(event.target).find('.owl-item');
		$items.removeClass('owl-item-first-visible owl-item-last-visible');
		
		$items.eq(event.item.index).addClass('owl-item-first-visible');
		$items.eq(event.item.index + event.page.size - 1).addClass('owl-item-last-visible');
	}
	ServiceBooking.inObject = function (val, obj) {
		var key;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) {
				if (obj[key] == val) {
					return true;
				}
			}
		}
		return false;
	};
	
	ServiceBooking.size = function(obj) {
		var key,
			size = 0;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) {
				size += 1;
			}
		}
		return size;
	};
	
	ServiceBooking.prototype = {
		reset: function () {
			this.$container = null;			
			this.container = null;
			this.opts = {};
			
			return this;
		},
		
		disableButtons: function () {
			this.$container.find(".btn").each(function (i, el) {
				pjQ.$(el).attr("disabled", "disabled");
			});
		},
		enableButtons: function () {
			this.$container.find(".btn").removeAttr("disabled");
		},
		
		init: function (opts) {
			var self = this;
			this.opts = opts;
			this.container = document.getElementById("pjSbsContainer_" + self.opts.index);
						
			self.$container = pjQ.$(self.container);
			
			this.$container.on("click.sbs", ".pjSbsBackToServices", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if (!hashBang("#!/loadServices")) 
				{
					self.loadServices.call(self);
				}
				return false;
			}).on("click.sbs", ".pjSbsBackToDateTime", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if (!hashBang("#!/loadDateTime")) 
				{
					self.loadDateTime.call(self);
				}
				return false;
			}).on("click.sbs", ".pjSbsBackToCheckout", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if (!hashBang("#!/loadCheckout")) 
				{
					self.loadCheckout.call(self);
				}
				return false;
			}).on("click.sbs", ".pjSbsSetTime", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if(!pjQ.$(this).hasClass('pjSbs-disabled'))
				{
					var ts = pjQ.$(this).attr('data-ts');
					self.setTime.call(self, ts);
				}
				return false;
			}).on("click.sbs", ".pjSbsSetHour", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if(!pjQ.$(this).hasClass('pjSbs-disabled'))
				{
					pjQ.$('.pjSbsSetHour').removeClass('active');
					pjQ.$(this).addClass('active');
					var hour = pjQ.$(this).attr('data-hour');
					self.setHour.call(self, hour);
				}
				return false;
			}).on("click.sbs", ".pjSbsSetMinute", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if(!pjQ.$(this).hasClass('pjSbs-disabled'))
				{
					pjQ.$('.pjSbsSetMinute').removeClass('active');
					pjQ.$(this).addClass('active');
					var minute = pjQ.$(this).attr('data-minute');
					self.setMinute.call(self, minute);
				}
				return false;
			}).on("change.sbs", "select[name='payment_method']", function () {
				self.$container.find(".pjSbsCcWrap").hide();
				self.$container.find(".pjSbsBankWrap").hide();
				switch (pjQ.$("option:selected", this).val()) {
				case 'creditcard':
					self.$container.find(".pjSbsCcWrap").show();
					break;
				case 'bank':
					self.$container.find(".pjSbsBankWrap").show();
					break;
				}
			}).on("click.sbs", ".pjSbsBtnStartOver", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if (!hashBang("#!/loadServices")) 
				{
					self.loadServices.call(self);
				}
				return false;
			});
			
			pjQ.$(window).on("loadServices", this.$container, function (e) {
				self.loadServices.call(self);
			}).on("loadDateTime", this.$container, function (e) {
				self.loadDateTime.call(self);
			}).on("loadCheckout", this.$container, function (e) {
				self.loadCheckout.call(self);
			}).on("loadPreview", this.$container, function (e) {
				self.loadPreview.call(self);
			});
			
			if (window.location.hash.length === 0) {
				this.loadServices.call(this);
			} else {
				onHashChange.call(null);
			}
			
			pjQ.$(document).on("click.sbs", 'button[data-dismiss="modal"]', function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $modal = pjQ.$(this).closest('.modal');
				if ($modal !== undefined && $modal.length > 0) {
					$modal.modal('hide');
					pjQ.$('body').removeClass('modal-open');
				}
				return false;
			});
		},
		setWeek: function (start_date, end_date) {
			var self = this,
				index = this.opts.index,
				params = {};
			params.locale = this.opts.locale;
			params.index = this.opts.index;
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			params.start_date = start_date;
			params.end_date = end_date;
			self.disableButtons.call(self);
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFrontEnd&action=pjActionSetWeek"].join(""), params).done(function (data) {
				if (!hashBang("#!/loadDateTime")) 
				{
					self.loadDateTime.call(self);
				}
			}).fail(function () {
				
			});
		},
		setDate: function (date) {
			var self = this,
				index = this.opts.index,
				params = {};
			params.locale = this.opts.locale;
			params.index = this.opts.index;
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			params.date = date;
			self.disableButtons.call(self);
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFrontEnd&action=pjActionSetDate"].join(""), params).done(function (data) {
				if (!hashBang("#!/loadDateTime")) 
				{
					self.loadDateTime.call(self);
				}
			}).fail(function () {
				
			});
		},
		setHour: function (hour) {
			var self = this,
				index = this.opts.index,
				params = {};
			params.locale = this.opts.locale;
			params.index = this.opts.index;
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			params.hour = hour;
			self.disableButtons.call(self);
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFrontEnd&action=pjActionSetHour"].join(""), params).done(function (data) {
				if (!hashBang("#!/loadDateTime")) 
				{
					self.loadDateTime.call(self);
				}
			}).fail(function () {
				
			});
		},
		setMinute: function (minute) {
			var self = this,
				index = this.opts.index,
				params = {};
			params.locale = this.opts.locale;
			params.index = this.opts.index;
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			params.minute = minute;
			self.disableButtons.call(self);
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFrontEnd&action=pjActionSetMinute"].join(""), params).done(function (data) {
				if (!hashBang("#!/loadDateTime")) 
				{
					self.loadDateTime.call(self);
				}
			}).fail(function () {
				
			});
		},
		loadServices: function () {
			var self = this,
				index = this.opts.index,
				params = {};
			params.locale = this.opts.locale;
			params.index = this.opts.index;
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFrontPublic&action=pjActionServices"].join(""), params).done(function (data) {
				self.$container.html(data);
				self.bindServices();
				pjQ.$('html, body').animate({
			        scrollTop: self.$container.offset().top
			    }, 500);
			}).fail(function () {
				
			});
		},
		bindServices: function(){
			var self = this,
				index = this.opts.index;
		
			var $form = pjQ.$('#pjSbsServiceForm_' + index);
			if($form.length > 0)
			{
				var ajax_url = [self.opts.folder, "index.php?controller=pjFrontEnd&action=pjActionAddService"].join("");
				if(self.opts.session_id != '')
				{
					ajax_url += "&session_id=" + self.opts.session_id;
				}
				pjQ.$('.pjSbs-service').on('click', function(e){
					e.stopPropagation();
					e.preventDefault();
					
					pjQ.$(this).toggleClass('active');
					if(pjQ.$(this).hasClass('active'))
					{
						pjQ.$(this).find('input:checkbox').attr('checked', 'checked');
					}else{
						pjQ.$(this).find('input:checkbox').removeAttr('checked');
					}
					self.disableButtons.call(self);
					pjQ.$.post(ajax_url, $form.serialize()).done(function (data) {
						if (!hashBang("#!/loadServices")) 
						{
							self.loadServices.call(self);
						}
					}).fail(function () {
						self.enableButtons.call(self);
					});
				});
				$form.validate({
					submitHandler: function (form) {
						self.disableButtons.call(self);
						if (!hashBang("#!/loadDateTime")) 
						{
							self.loadDateTime.call(self);
						}
						return false;
					}
				});
			}
		},
		loadDateTime: function () {
			var self = this,
				index = this.opts.index,
				params = {};
			params.locale = this.opts.locale;
			params.index = this.opts.index;
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFrontPublic&action=pjActionDateTime"].join(""), params).done(function (data) {
				if (data.code != undefined && data.status == 'ERR') {
					if (!hashBang("#!/loadServices")) 
					{
						self.loadServices.call(self);
					}
				}else{
					self.$container.html(data);
					self.bindDateTime.call(self);
					pjQ.$('html, body').animate({
				        scrollTop: self.$container.offset().top
				    }, 500);
				}
			}).fail(function () {
				
			});
		},
		bindDateTime: function(){
			var self = this,
				index = this.opts.index;
		
			var $owl = pjQ.$('.owl-carousel').owlCarousel({
			    loop:false,
			    margin:5,
			    responsiveClass:true,
			    responsive:{
			        0:{
			            items:3,
			            nav:true
			        },
			        600:{
			            items:5,
			            nav:false
			        },
			        1000:{
			            items:7,
			            nav:true,
			            loop:false
			        }
			    },
			    onInitialized: function (event) {
			        refreshFirstLastVisible(event);
			    },
			    onChanged: function (event) {
			        refreshFirstLastVisible(event);
			    }
			});
			pjQ.$('.pjSbs-owl-prev').click(function(e) {
			    if (e && e.preventDefault) {
					e.preventDefault();
				}
				if(pjQ.$('#pjSbsDate_1').parent().hasClass('owl-item-first-visible'))
				{
					var $prev_data = pjQ.$('#pjSbsWeekPrev_' + self.opts.index);
					var start_date = $prev_data.attr('data-start_iso');
					var end_date = $prev_data.attr('data-end_iso');
					self.setWeek.call(self, start_date, end_date);
				}else{
					$owl.trigger('prev.owl.carousel');
				}
				return false;
			});
			pjQ.$('.pjSbs-owl-next').click(function(e) {
			    
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if(pjQ.$('#pjSbsDate_7').parent().hasClass('owl-item-last-visible'))
				{
					var $next_data = pjQ.$('#pjSbsWeekNext_' + self.opts.index);
					var start_date = $next_data.attr('data-start_iso');
					var end_date = $next_data.attr('data-end_iso');
					self.setWeek.call(self, start_date, end_date);
				}else{
					$owl.trigger('next.owl.carousel');
				}
				return false;
			});
			pjQ.$('.pjSbs-date:not(.disabled)').on('click',function(){
				pjQ.$('.pjSbs-date').removeClass('active');

				pjQ.$('.pjSbs-not-available').hide();

				pjQ.$('.pjSbs-services-footer, .pjSbs-available-times').show();

				pjQ.$(this).toggleClass('active');
				if(pjQ.$(this).hasClass('active'))
				{
					var date = pjQ.$(this).attr('data-date');
					self.setDate.call(self, date);
				}
			});
			var $form = pjQ.$('#pjSbsDateTimeForm_' + index);
			if($form.length > 0)
			{
				$form.validate({
					submitHandler: function (form) {
						self.disableButtons.call(self);
						if (!hashBang("#!/loadCheckout")) 
						{
							self.loadCheckout.call(self);
						}
						return false;
					}
				});
			}
			
			var $periodWrapper = pjQ.$('#pjSbsPeriod_' + self.opts.index);
			if($periodWrapper.length > 0)
			{
				var height = $periodWrapper.parent().height();
				var span_height = parseInt(height/2, 10) - 2;
				$periodWrapper.find('span').css('line-height', span_height + 'px');
			}
			pjQ.$(window).on('resize', function() {
				var height = $periodWrapper.parent().height();
				var span_height = parseInt(height/2, 10) - 2;
				$periodWrapper.find('span').css('line-height', span_height + 'px');
			});
		},
		loadCheckout: function () {
			var self = this,
				index = this.opts.index,
				params = {};
			params.locale = this.opts.locale;
			params.index = this.opts.index;
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFrontPublic&action=pjActionCheckout"].join(""), params).done(function (data) {
				if (data.code != undefined && data.status == 'ERR') {
					if (!hashBang("#!/loadServices")) 
					{
						self.loadServices.call(self);
					}
				}else{
					self.$container.html(data);
					self.bindCheckout.call(self);
					pjQ.$('html, body').animate({
				        scrollTop: self.$container.offset().top
				    }, 500);
				}
			}).fail(function () {
				
			});
		},
		bindCheckout: function(){
			var self = this,
				index = this.opts.index;
		
			pjQ.$('.modal-dialog').css("z-index", "9999"); 
			
			if (validate) 
			{
				var $form = pjQ.$('#pjSbsCheckoutForm_'+ self.opts.index);
				pjTrimOnBlur($form);
				var remote_url = self.opts.folder + "index.php?controller=pjFrontEnd&action=pjActionCheckCaptcha";
				if(self.opts.session_id != '')
				{
					remote_url += "&session_id=" + self.opts.session_id;
				}
				$form.validate({
					rules: {
						"captcha": {
							remote: remote_url
						}
					},
					onkeyup: false,
					errorElement: 'li',
					errorPlacement: function (error, element) {
						if(element.attr('name') == 'terms')
						{
							error.appendTo(element.siblings().find('ul'));
						}else{
							error.appendTo(element.next().find('ul'));
						}
					},
		            highlight: function(ele, errorClass, validClass) {
		            	var element = pjQ.$(ele);
		            	if(element.attr('name') == 'captcha')
						{
							element.parent().parent().parent().addClass('has-error');
						}else{
							element.parent().addClass('has-error');
						}
		            },
		            unhighlight: function(ele, errorClass, validClass) {
		            	var element = pjQ.$(ele);
		            	if(element.attr('name') == 'captcha')
						{
							element.parent().parent().parent().removeClass('has-error').addClass('has-success');
						}else{
							element.parent().removeClass('has-error').addClass('has-success');
						}
		            },
					submitHandler: function (form) {
						self.disableButtons.call(self);
						var $form = pjQ.$(form);
						pjQ.$.post([self.opts.folder, "index.php?controller=pjFrontPublic&action=pjActionCheckout"].join(""), $form.serialize()).done(function (data) {
							if (data.status == "OK") {
								if (!hashBang("#!/loadPreview")) 
								{
									self.loadPreview.call(self);
								}
							} else {
								self.enableButtons.call(self);
							}
						}).fail(function () {
							self.enableButtons.call(self);
						});
						return false;
					}
				});
			}
		},
		loadPreview: function () {
			var self = this,
				index = this.opts.index,
				params = {};
			params.locale = self.opts.locale;
			params.index = self.opts.index;
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFrontPublic&action=pjActionPreview", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
				if (data.code != undefined && data.status == 'ERR') {
					if (!hashBang("#!/loadServices")) 
					{
						self.loadServices.call(self);
					}
				}else{
					self.$container.html(data);
					self.bindPreview.call(self);
					pjQ.$('html, body').animate({
				        scrollTop: self.$container.offset().top
				    }, 500);
				}
			}).fail(function () {
				
			});
		},
		bindPreview: function(){
			var self = this,
				index = this.opts.index;
		
			if (validate) 
			{
				var $form = pjQ.$('#pjSbsPreviewForm_'+ self.opts.index);
				$form.validate({
					submitHandler: function (form) {
						self.disableButtons.call(self);
						var $form = pjQ.$(form);
						pjQ.$.post([self.opts.folder, "index.php?controller=pjFrontEnd&action=pjActionSaveBooking"].join(""), $form.serialize()).done(function (data) {
							if (data.code == "200") {
								self.getPaymentForm.call(self, data);
							} else if (data.code == "119") {
								self.enableButtons.call(self);
							}
						}).fail(function () {
							self.enableButtons.call(self);
						});
						return false;
						return false;
					}
				});
			}
		},
		getPaymentForm: function(obj){
			var self = this,
				index = this.opts.index;
			var	params = {};
			params.locale = self.opts.locale;
			params.index = self.opts.index;
			params.booking_id =  obj.booking_id;
			params.payment_method = obj.payment;
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFrontPublic&action=pjActionGetPaymentForm"].join(""), params).done(function (data) {
				self.$container.html(data);
				switch (obj.payment) {
					case 'paypal':
						self.$container.find("form[name='sbsPaypal']").trigger('submit');
						break;
					case 'authorize':
						self.$container.find("form[name='sbsAuthorize']").trigger('submit');
						break;
					case 'creditcard':
					case 'bank':
					case 'cash':
						break;
				}
				pjQ.$('html, body').animate({
			        scrollTop: self.$container.offset().top
			    }, 500);
			}).fail(function () {
				log("Deferred is rejected");
			});
		}
	};
	
	window.ServiceBooking = ServiceBooking;	
})(window);