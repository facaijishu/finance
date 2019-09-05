	$.intervalArr = [];
/*
 * Calculate nav height
 */
var calc_navbar_height = function() {
		var height = null;
	
		if ($('#header').length)
			height = $('#header').height();
	
		if (height === null)
			height = $('<div id="header"></div>').height();
	
		if (height === null)
			return 49;
		// default
		return height;
	},

	navbar_height = calc_navbar_height, 
/*
 * APP DOM REFERENCES
 * Description: Obj DOM reference, please try to avoid changing these
 */	
	shortcut_dropdown = $('#shortcut'),
	
	bread_crumb = $('#ribbon ol.breadcrumb'),

/*
 * Top menu on/off
 */
	topmenu = false,
/*
 * desktop or mobile
 */
	thisDevice = null,
/*
 * DETECT MOBILE DEVICES
 * Description: Detects mobile device - if any of the listed device is 
 * detected a class is inserted to $.root_ and the variable thisDevice 
 * is decleard. (so far this is covering most hand held devices)
 */	
	ismobile = (/iphone|ipad|ipod|android|blackberry|mini|windows\sce|palm/i.test(navigator.userAgent.toLowerCase())),
/*
 * JS ARRAY SCRIPT STORAGE
 * Description: used with loadScript to store script path and file name
 * so it will not load twice
 */	
	jsArray = {},
	cssArray = {},
/*
 * App Initialize
 * Description: Initializes the app with intApp();
 */	
	initApp = (function(app) {
		
		/*
		 * ADD DEVICE TYPE
		 * Detect if mobile or desktop
		 */		
		app.addDeviceType = function() {
			
			if (!ismobile) {
				// Desktop
				$.root_.addClass("desktop-detected");
				thisDevice = "desktop";
				return false; 
			} else {
				// Mobile
				$.root_.addClass("mobile-detected");
				thisDevice = "mobile";
				
				if (fastClick) {
					// Removes the tap delay in idevices
					// dependency: js/plugin/fastclick/fastclick.js 
					$.root_.addClass("needsclick");
					FastClick.attach(document.body); 
					return false; 
				}
				
			}
			
		};
		/* ~ END: ADD DEVICE TYPE */
		
		/*
		 * CHECK FOR MENU POSITION
		 * Scans localstroage for menu position (vertical or horizontal)
		 */
		app.menuPos = function() {
			
		 	if ($.root_.hasClass("menu-on-top") || localStorage.getItem('sm-setmenu')=='top' ) { 
		 		topmenu = true;
		 		$.root_.addClass("menu-on-top");
		 	}
		};
		/* ~ END: CHECK MOBILE DEVICE */

		/*
		 * SMART ACTIONS
		 */
		app.SmartActions = function(){
				
			var smartActions = {
			    
			    // LOGOUT MSG 
			    userLogout: function($this){
			
					// ask verification
					$.SmartMessageBox({
						title : "<i class='fa fa-sign-out txt-color-orangeDark'></i> 退出 <span class='txt-color-orangeDark'><strong>" + $('#show-shortcut').text() + "</strong></span> ?",
						content : $this.data('logout-msg') || "您即将退出系统！",
						buttons : '[取消][确定]'
			
					}, function(ButtonPressed) {
						if (ButtonPressed == "确定") {
							$.root_.addClass('animated fadeOutUp');
							setTimeout(logout, 1000);
						}
					});
					function logout() {
						window.location = $this.attr('href');
					}
			
				},
		
				// RESET WIDGETS
			    resetWidgets: function($this){
					
					$.SmartMessageBox({
                    title: "<i class='fa fa-refresh' style='color:green'></i> 清空本地存储",
                    content: $this.data('reset-msg') || "确认要清空本地存储?",
                    buttons: '[清空][取消]'
					}, function(ButtonPressed) {
						if (ButtonPressed == "Yes" && localStorage) {
							localStorage.clear();
							location.reload();
						}
			
					});
			    },
			    
			    // LAUNCH FULLSCREEN 
			    launchFullscreen: function(element){
			
					if (!$.root_.hasClass("full-screen")) {
				
						$.root_.addClass("full-screen");
				
						if (element.requestFullscreen) {
							element.requestFullscreen();
						} else if (element.mozRequestFullScreen) {
							element.mozRequestFullScreen();
						} else if (element.webkitRequestFullscreen) {
							element.webkitRequestFullscreen();
						} else if (element.msRequestFullscreen) {
							element.msRequestFullscreen();
						}
				
					} else {
						
						$.root_.removeClass("full-screen");
						
						if (document.exitFullscreen) {
							document.exitFullscreen();
						} else if (document.mozCancelFullScreen) {
							document.mozCancelFullScreen();
						} else if (document.webkitExitFullscreen) {
							document.webkitExitFullscreen();
						}
				
					}
			
			   },
			
			   // MINIFY MENU
			    minifyMenu: function($this){
			    	if (!$.root_.hasClass("menu-on-top")){
						$.root_.toggleClass("minified");
						$.root_.removeClass("hidden-menu");
						$('html').removeClass("hidden-menu-mobile-lock");
						$this.effect("highlight", {}, 500);
					}
			    },
			    
			    // TOGGLE MENU 
			    toggleMenu: function(){
			    	if (!$.root_.hasClass("menu-on-top")){
						$('html').toggleClass("hidden-menu-mobile-lock");
						$.root_.toggleClass("hidden-menu");
						$.root_.removeClass("minified");
			    	//} else if ( $.root_.hasClass("menu-on-top") && $.root_.hasClass("mobile-view-activated") ) {
			    	// suggested fix from Christian Jäger	
			    	} else if ( $.root_.hasClass("menu-on-top") && $(window).width() < 979 ) {	
			    		$('html').toggleClass("hidden-menu-mobile-lock");
						$.root_.toggleClass("hidden-menu");
						$.root_.removeClass("minified");
			    	}
			    },     
			
			    // TOGGLE SHORTCUT 
			    toggleShortcut: function(){
			    	
					if (shortcut_dropdown.is(":visible")) {
						shortcut_buttons_hide();
					} else {
						shortcut_buttons_show();
					}
		
					// SHORT CUT (buttons that appear when clicked on user name)
					shortcut_dropdown.find('a').click(function(e) {
						e.preventDefault();
						window.location = $(this).attr('href');
						setTimeout(shortcut_buttons_hide, 300);
				
					});
				
					// SHORTCUT buttons goes away if mouse is clicked outside of the area
					$(document).mouseup(function(e) {
						if (!shortcut_dropdown.is(e.target) && shortcut_dropdown.has(e.target).length === 0) {
							shortcut_buttons_hide();
						}
					});
					
					// SHORTCUT ANIMATE HIDE
					function shortcut_buttons_hide() {
						shortcut_dropdown.animate({
							height : "hide"
						}, 300, "easeOutCirc");
						$.root_.removeClass('shortcut-on');
				
					}
				
					// SHORTCUT ANIMATE SHOW
					function shortcut_buttons_show() {
						shortcut_dropdown.animate({
							height : "show"
						}, 200, "easeOutCirc");
						$.root_.addClass('shortcut-on');
					}
			
			    }  
			   
			};
				
			$.root_.on('click', '[data-action="userLogout"]', function(e) {
				var $this = $(this);
				smartActions.userLogout($this);
				e.preventDefault();
				
				//clear memory reference
				$this = null;
				
			}); 

			/*
			 * BUTTON ACTIONS 
			 */		
			$.root_.on('click', '[data-action="resetWidgets"]', function(e) {	
				var $this = $(this);
				smartActions.resetWidgets($this);
				e.preventDefault();
				
				//clear memory reference
				$this = null;
			});
			
			$.root_.on('click', '[data-action="launchFullscreen"]', function(e) {	
				smartActions.launchFullscreen(document.documentElement);
				e.preventDefault();
			}); 
			
			$.root_.on('click', '[data-action="minifyMenu"]', function(e) {
				var $this = $(this);
				smartActions.minifyMenu($this);
				e.preventDefault();
				
				//clear memory reference
				$this = null;
			}); 
			
			$.root_.on('click', '[data-action="toggleMenu"]', function(e) {	
				smartActions.toggleMenu();
				e.preventDefault();
			});  
		
			$.root_.on('click', '[data-action="toggleShortcut"]', function(e) {	
				smartActions.toggleShortcut();
				e.preventDefault();
			}); 
					
		};
		/* ~ END: SMART ACTIONS */
		
		/*
		 * ACTIVATE NAVIGATION
		 * Description: Activation will fail if top navigation is on
		 */
		app.leftNav = function(){
			
			// INITIALIZE LEFT NAV
			if (!topmenu) {
				if (!null) {
					$('nav ul').jarvismenu({
						accordion : menu_accordion || true,
						speed : menu_speed || true,
						closedSign : '<em class="fa fa-plus-square-o"></em>',
						openedSign : '<em class="fa fa-minus-square-o"></em>'
					});
				} else {
					alert("Error - menu anchor does not exist");
				}
			}
			
		};
		/* ~ END: ACTIVATE NAVIGATION */
		
		/*
		 * MISCELANEOUS DOM READY FUNCTIONS
		 * Description: fire with jQuery(document).ready...
		 */
		app.domReadyMisc = function() {
			
			/*
			 * FIRE TOOLTIPS
			 */
			if ($("[rel=tooltip]").length) {
				$("[rel=tooltip]").tooltip();
			}
		
			// SHOW & HIDE MOBILE SEARCH FIELD
			$('#search-mobile').click(function() {
				$.root_.addClass('search-mobile');
			});
		
			$('#cancel-search-js').click(function() {
				$.root_.removeClass('search-mobile');
			});
		
			// ACTIVITY
			// ajax drop
			$('#activity').click(function(e) {
				var $this = $(this);
		
				if ($this.find('.badge').hasClass('bg-color-red')) {
					$this.find('.badge').removeClassPrefix('bg-color-');
					$this.find('.badge').text("0");
				}
		
				if (!$this.next('.ajax-dropdown').is(':visible')) {
					$this.next('.ajax-dropdown').fadeIn(150);
					$this.addClass('active');
				} else {
					$this.next('.ajax-dropdown').fadeOut(150);
					$this.removeClass('active');
				}
		
				var theUrlVal = $this.next('.ajax-dropdown').find('.btn-group > .active > input').attr('id');
				
				//clear memory reference
				$this = null;
				theUrlVal = null;
						
				e.preventDefault();
			});
		
			$('input[name="activity"]').change(function() {
				var $this = $(this);
		
				url = $this.attr('id');
				container = $('.ajax-notifications');
				
				loadURL(url, container);
				
				//clear memory reference
				$this = null;		
			});
		
			// close dropdown if mouse is not inside the area of .ajax-dropdown
			$(document).mouseup(function(e) {
				if (!$('.ajax-dropdown').is(e.target) && $('.ajax-dropdown').has(e.target).length === 0) {
					$('.ajax-dropdown').fadeOut(150);
					$('.ajax-dropdown').prev().removeClass("active");
				}
			});
			
			// loading animation (demo purpose only)
			$('button[data-btn-loading]').on('click', function() {
				var btn = $(this);
				btn.button('loading');
				setTimeout(function() {
					btn.button('reset');
				}, 3000);
			});
		
			// NOTIFICATION IS PRESENT
			// Change color of lable once notification button is clicked

			$this = $('#activity > .badge');
	
			if (parseInt($this.text()) > 0) {
				$this.addClass("bg-color-red bounceIn animated");
				
				//clear memory reference
				$this = null;
			}

			
		};
		/* ~ END: MISCELANEOUS DOM */
	
		/*
		 * MISCELANEOUS DOM READY FUNCTIONS
		 * Description: fire with jQuery(document).ready...
		 */
		app.mobileCheckActivation = function(){
			
			if ($(window).width() < 979) {
				$.root_.addClass('mobile-view-activated');
				$.root_.removeClass('minified');
			} else if ($.root_.hasClass('mobile-view-activated')) {
				$.root_.removeClass('mobile-view-activated');
			}

			if (debugState){
				console.log("mobileCheckActivation");
			}
			
		} 
		/* ~ END: MISCELANEOUS DOM */

		return app;
		
	})({});

	initApp.addDeviceType();
	initApp.menuPos();

	/**
	 * 日期选择
	 */
	var bindDateTimePicker;
	var bindDateRangePicker;

	/**
	 *datatable初始化声明
	 */
	var dt_default_options,dtAutoOption,toEnableParams;

	/**
	 * ueditor初始化配置
	 */
	var ue_config = {
		toolbars: [[
			'source', 'undo', 'redo', '|',
			'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', '|',
			'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
			'customstyle', 'paragraph', 'fontfamily', 'fontsize', '|', 'indent', '|',
			'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'touppercase', 'tolowercase'
		]],
		zIndex: 1100
	};
/*
 * DOCUMENT LOADED EVENT
 * Description: Fire when DOM is ready
 */
	jQuery(document).ready(function() {
		
		initApp.SmartActions();
		initApp.leftNav();
		initApp.domReadyMisc();
	
	});
/*
 * RESIZER WITH THROTTLE
 * Source: http://benalman.com/code/projects/jquery-resize/examples/resize/
 */
	(function ($, window, undefined) {
	
	    var elems = $([]),
	        jq_resize = $.resize = $.extend($.resize, {}),
	        timeout_id, str_setTimeout = 'setTimeout',
	        str_resize = 'resize',
	        str_data = str_resize + '-special-event',
	        str_delay = 'delay',
	        str_throttle = 'throttleWindow';
	
	    jq_resize[str_delay] = throttle_delay;
	
	    jq_resize[str_throttle] = true;
	
	    $.event.special[str_resize] = {
	
	        setup: function () {
	            if (!jq_resize[str_throttle] && this[str_setTimeout]) {
	                return false;
	            }
	
	            var elem = $(this);
	            elems = elems.add(elem);
	            try {
	                $.data(this, str_data, {
	                    w: elem.width(),
	                    h: elem.height()
	                });
	            } catch (e) {
	                $.data(this, str_data, {
	                    w: elem.width, // elem.width();
	                    h: elem.height // elem.height();
	                });
	            }
	
	            if (elems.length === 1) {
	                loopy();
	            }
	        },
	        teardown: function () {
	            if (!jq_resize[str_throttle] && this[str_setTimeout]) {
	                return false;
	            }
	
	            var elem = $(this);
	            elems = elems.not(elem);
	            elem.removeData(str_data);
	            if (!elems.length) {
	                clearTimeout(timeout_id);
	            }
	        },
	
	        add: function (handleObj) {
	            if (!jq_resize[str_throttle] && this[str_setTimeout]) {
	                return false;
	            }
	            var old_handler;
	
	            function new_handler(e, w, h) {
	                var elem = $(this),
	                    data = $.data(this, str_data);
	                data.w = w !== undefined ? w : elem.width();
	                data.h = h !== undefined ? h : elem.height();
	
	                old_handler.apply(this, arguments);
	            }
	            if ($.isFunction(handleObj)) {
	                old_handler = handleObj;
	                return new_handler;
	            } else {
	                old_handler = handleObj.handler;
	                handleObj.handler = new_handler;
	            }
	        }
	    };
	
	    function loopy() {
	        timeout_id = window[str_setTimeout](function () {
	            elems.each(function () {
	                var width;
	                var height;
	
	                var elem = $(this),
	                    data = $.data(this, str_data); //width = elem.width(), height = elem.height();
	
	                // Highcharts fix
	                try {
	                    width = elem.width();
	                } catch (e) {
	                    width = elem.width;
	                }
	
	                try {
	                    height = elem.height();
	                } catch (e) {
	                    height = elem.height;
	                }
	                //fixed bug
	
	
	                if (width !== data.w || height !== data.h) {
	                    elem.trigger(str_resize, [data.w = width, data.h = height]);
	                }
	
	            });
	            loopy();
	
	        }, jq_resize[str_delay]);
	
	    }
	
	})(jQuery, this);
/*
* ADD CLASS WHEN BELOW CERTAIN WIDTH (MOBILE MENU)
* Description: tracks the page min-width of #CONTENT and NAV when navigation is resized.
* This is to counter bugs for minimum page width on many desktop and mobile devices.
* Note: This script utilizes JSthrottle script so don't worry about memory/CPU usage
*/
	$('#main').resize(function() {
		
		initApp.mobileCheckActivation();
		
	});

/* ~ END: NAV OR #LEFT-BAR RESIZE DETECT */

/*
 * DETECT IE VERSION
 * Description: A short snippet for detecting versions of IE in JavaScript
 * without resorting to user-agent sniffing
 * RETURNS:
 * If you're not in IE (or IE version is less than 5) then:
 * //ie === undefined
 *
 * If you're in IE (>=5) then you can determine which version:
 * // ie === 7; // IE7
 *
 * Thus, to detect IE:
 * // if (ie) {}
 *
 * And to detect the version:
 * ie === 6 // IE6
 * ie > 7 // IE8, IE9 ...
 * ie < 9 // Anything less than IE9
 */
// TODO: delete this function later on - no longer needed (?)
	var ie = ( function() {
	
		var undef, v = 3, div = document.createElement('div'), all = div.getElementsByTagName('i');
	
		while (div.innerHTML = '<!--[if gt IE ' + (++v) + ']><i></i><![endif]-->', all[0]);
	
		return v > 4 ? v : undef;
	
	}()); 
/* ~ END: DETECT IE VERSION */

/*
 * CUSTOM MENU PLUGIN
 */
	$.fn.extend({
	
		//pass the options variable to the function
		jarvismenu : function(options) {
	
			var defaults = {
				accordion : 'true',
				speed : 200,
				closedSign : '[+]',
				openedSign : '[-]'
			},
	
			// Extend our default options with those provided.
				opts = $.extend(defaults, options),
			//Assign current element to variable, in this case is UL element
				$this = $(this);
	
			//add a mark [+] to a multilevel menu
			$this.find("li").each(function() {
				if ($(this).find("ul").size() !== 0) {
					//add the multilevel sign next to the link
					$(this).find("a:first").append("<b class='collapse-sign'>" + opts.closedSign + "</b>");
	
					//avoid jumping to the top of the page when the href is an #
					if ($(this).find("a:first").attr('href') == "#") {
						$(this).find("a:first").click(function() {
							return false;
						});
					}
				}
			});
	
			//open active level
			$this.find("li.active").each(function() {
				$(this).parents("ul").slideDown(opts.speed);
				$(this).parents("ul").parent("li").find("b:first").html(opts.openedSign);
				$(this).parents("ul").parent("li").addClass("open");
			});
	
			$this.find("li a").click(function() {
	
				if ($(this).parent().find("ul").size() !== 0) {
	
					if (opts.accordion) {
						//Do nothing when the list is open
						if (!$(this).parent().find("ul").is(':visible')) {
							parents = $(this).parent().parents("ul");
							visible = $this.find("ul:visible");
							visible.each(function(visibleIndex) {
								var close = true;
								parents.each(function(parentIndex) {
									if (parents[parentIndex] == visible[visibleIndex]) {
										close = false;
										return false;
									}
								});
								if (close) {
									if ($(this).parent().find("ul") != visible[visibleIndex]) {
										$(visible[visibleIndex]).slideUp(opts.speed, function() {
											$(this).parent("li").find("b:first").html(opts.closedSign);
											$(this).parent("li").removeClass("open");
										});
	
									}
								}
							});
						}
					}// end if
					if ($(this).parent().find("ul:first").is(":visible") && !$(this).parent().find("ul:first").hasClass("active")) {
						$(this).parent().find("ul:first").slideUp(opts.speed, function() {
							$(this).parent("li").removeClass("open");
							$(this).parent("li").find("b:first").delay(opts.speed).html(opts.closedSign);
						});
	
					} else {
						$(this).parent().find("ul:first").slideDown(opts.speed, function() {
							/*$(this).effect("highlight", {color : '#616161'}, 500); - disabled due to CPU clocking on phones*/
							$(this).parent("li").addClass("open");
							$(this).parent("li").find("b:first").delay(opts.speed).html(opts.openedSign);
						});
					} // end else
				} // end if
			});
		} // end function
	});
/* ~ END: CUSTOM MENU PLUGIN */

/*
 * ELEMENT EXIST OR NOT
 * Description: returns true or false
 * Usage: $('#myDiv').doesExist();
 */
	jQuery.fn.doesExist = function() {
		return jQuery(this).length > 0;
	};
/* ~ END: ELEMENT EXIST OR NOT */

/*
 * INITIALIZE FORMS
 * Description: Select2, Masking, Datepicker, Autocomplete
 */	
	function runAllForms() {
	
		/*
		 * BOOTSTRAP SLIDER PLUGIN
		 * Usage:
		 * Dependency: js/plugin/bootstrap-slider
		 */
		if ($.fn.slider) {
			$('.slider').slider();
		}
	
		/*
		 * SELECT2 PLUGIN
		 * Usage:
		 * Dependency: js/plugin/select2/
		 */
		/*
		if ($.fn.select2) {
			$('select.select2').each(function () {
				var $this = $(this),
					width = $this.attr('data-select-width') || '100%';
				//, _showSearchInput = $this.attr('data-select-search') === 'true';
				$this.select2({
					//showSearchInput : _showSearchInput,
					allowClear: true,
					width: width
				});

				//clear memory reference
				$this = null;
			});
		}
		*/
	
		/*
		 * MASKING
		 * Dependency: js/plugin/masked-input/
		 */
		if ($.fn.mask) {
			$('[data-mask]').each(function() {
	
				var $this = $(this),
					mask = $this.attr('data-mask') || 'error...', mask_placeholder = $this.attr('data-mask-placeholder') || 'X';
	
				$this.mask(mask, {
					placeholder : mask_placeholder
				});
				
				//clear memory reference
				$this = null;
			});
		}
	
		/*
		 * AUTOCOMPLETE
		 * Dependency: js/jqui
		 */
		if ($.fn.autocomplete) {
			$('[data-autocomplete]').each(function() {
	
				var $this = $(this),
					availableTags = $this.data('autocomplete') || ["The", "Quick", "Brown", "Fox", "Jumps", "Over", "Three", "Lazy", "Dogs"];
	
				$this.autocomplete({
					source : availableTags
				});
				
				//clear memory reference
				$this = null;
			});
		}
	
		/*
		 * JQUERY UI DATE
		 * Dependency: js/libs/jquery-ui-1.10.3.min.js
		 * Usage: <input class="datepicker" />
		 */
        /*if ($.fn.datepicker) {
            $('.datepicker').each(function () {
                var $this = $(this),
                    dataDateFormat = $this.attr('data-dateformat') || 'yy-mm-dd';

                $this.datepicker({
                    dateFormat: dataDateFormat,
                    prevText: '<i class="fa fa-chevron-left"></i>',
                    nextText: '<i class="fa fa-chevron-right"></i>'
                });

                //clear memory reference
                $this = null;
            });
        }*/

		loadModule("datetimepicker", function(){
			//定义全局方法
			bindDateTimePicker = function (picker) {
				var minView = 0;
				var format = "yyyy-mm-dd";
				if(picker.hasClass("datepicker")){
					minView = 2;
				} else {
					format = "";
				}

				picker.datetimepicker({
					format: format,
					language: "zh-CN",
					autoclose: true,
					todayBtn: true,
					todayHighlight: true,
					startView: 2,
					minView: minView,
					forceParse: 0
				});
			}
			//全局绑定
			var datetimepickers = $(".datepicker,.datetimepicker");
			if(datetimepickers.length > 0) {
				datetimepickers.each(function(){
					var picker = $(this);
					bindDateTimePicker(picker);
				});
			}
		});

		loadModule("daterangepicker", function() {
			//定义全局方法
			bindDateRangePicker = function (dateRange) {
				var callback = arguments[1] || function () { };
				dateRange.daterangepicker({
					autoUpdateInput: false,
					autoUpdateInput: false,
					format: 'YYYY-MM-DD HH:mm:ss',
					locale: {
						applyLabel: '确 定',
						cancelLabel: '清 空',
						fromLabel: '起始时间',
						toLabel: '结束时间',
						customRangeLabel: '自定义',
						daysOfWeek: ['日', '一', '二', '三', '四', '五', '六'],
						monthNames: ['一月', '二月', '三月', '四月', '五月', '六月',
							'七月', '八月', '九月', '十月', '十一月', '十二月'],
						firstDay: 1
					},
					ranges: {
						//'今天': [moment(), moment()],
						//'昨天': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
						//'最近7天': [moment().subtract(6, 'days'), moment()],
						//'最近30天': [moment().subtract(29, 'days'), moment()],
						'本月': [moment().startOf('month'), moment().endOf('month')],
						//'上月': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
					}
				}).on('apply.daterangepicker', function (event, picker) {
					var start = picker.startDate.format('YYYY-MM-DD');
					var end = picker.endDate.format('YYYY-MM-DD');
					$(dateRange.attr("start") || "startDate").val(start);
					$(dateRange.attr("end") || "endDate").val(end);
					dateRange.val(start + "  至  " + end);
					callback && callback();
				}).on('cancel.daterangepicker', function (ev, picker) {
					$(dateRange.attr("start") || "startDate").val("");
					$(dateRange.attr("end") || "endDate").val("");
					dateRange.val("");
				});
			}

			//全局绑定
			/*var dateranges = $(".daterange");
			if (dateranges.length > 0) {
				dateranges.each(function () {
					var dateRange = $(this);
					bindDateRangePicker(dateRange);
				});
			}*/
		});
		/*
		 * AJAX BUTTON LOADING TEXT
		 * Usage: <button type="button" data-loading-text="Loading..." class="btn btn-xs btn-default ajax-refresh"> .. </button>
		 */
		$('button[data-loading-text]').on('click', function() {
			var btn = $(this);
			btn.button('loading');
			setTimeout(function() {
				btn.button('reset');
				//clear memory reference
				btn = null;
			}, 3000);

		});
	
	}
/* ~ END: INITIALIZE FORMS */

/*
 * INITIALIZE JARVIS WIDGETS
 * Setup Desktop Widgets
 */
	function setup_widgets_desktop() {
	
		if ($.fn.jarvisWidgets && enableJarvisWidgets) {
	
			$('#widget-grid').jarvisWidgets({
	
				grid : 'article',
				widgets : '.jarviswidget',
				localStorage : localStorageJarvisWidgets,
				deleteSettingsKey : '#deletesettingskey-options',
				settingsKeyLabel : 'Reset settings?',
				deletePositionKey : '#deletepositionkey-options',
				positionKeyLabel : 'Reset position?',
				sortable : sortableJarvisWidgets,
				buttonsHidden : false,
				// toggle button
				toggleButton : true,
				toggleClass : 'fa fa-minus | fa fa-plus',
				toggleSpeed : 200,
				onToggle : function() {
				},
				// delete btn
				deleteButton : true,
				deleteMsg:'Warning: This action cannot be undone!',
				deleteClass : 'fa fa-times',
				deleteSpeed : 200,
				onDelete : function() {
				},
				// edit btn
				editButton : true,
				editPlaceholder : '.jarviswidget-editbox',
				editClass : 'fa fa-cog | fa fa-save',
				editSpeed : 200,
				onEdit : function() {
				},
				// color button
				colorButton : true,
				// full screen
				fullscreenButton : true,
				fullscreenClass : 'fa fa-expand | fa fa-compress',
				fullscreenDiff : 3,
				onFullscreen : function() {
				},
				// custom btn
				customButton : false,
				customClass : 'folder-10 | next-10',
				customStart : function() {
					alert('Hello you, this is a custom button...');
				},
				customEnd : function() {
					alert('bye, till next time...');
				},
				// order
				buttonOrder : '%refresh% %custom% %edit% %toggle% %fullscreen% %delete%',
				opacity : 1.0,
				dragHandle : '> header',
				placeholderClass : 'jarviswidget-placeholder',
				indicator : true,
				indicatorTime : 600,
				ajax : true,
				timestampPlaceholder : '.jarviswidget-timestamp',
				timestampFormat : 'Last update: %m%/%d%/%y% %h%:%i%:%s%',
				refreshButton : true,
				refreshButtonClass : ' ',
				labelError : 'Sorry but there was a error:',
				labelUpdated : 'Last Update:',
				labelRefresh : 'Refresh',
				labelDelete : 'Delete widget:',
				afterLoad : function() {
				},
				rtl : false, // best not to toggle this!
				onChange : function() {
					
				},
				onSave : function() {
					
				},
				ajaxnav : $.navAsAjax // declears how the localstorage should be saved (HTML or AJAX Version)
	
			});
	
		}
	
	}
/*
 * SETUP DESKTOP WIDGET
 */
	function setup_widgets_mobile() {
	
		if (enableMobileWidgets && enableJarvisWidgets) {
			setup_widgets_desktop();
		}
	
	}
/* ~ END: INITIALIZE JARVIS WIDGETS */

/*
 * GOOGLE MAPS
 * description: Append google maps to head dynamically (only execute for ajax version)
 * Loads at the begining for ajax pages
 */
	if ($.navAsAjax || $(".google_maps")){
		var gMapsLoaded = false;
		window.gMapsCallback = function() {
			gMapsLoaded = true;
			$(window).trigger('gMapsLoaded');
		};
		window.loadGoogleMaps = function() {
			if (gMapsLoaded)
				return window.gMapsCallback();
			var script_tag = document.createElement('script');
			script_tag.setAttribute("type", "text/javascript");
			script_tag.setAttribute("src", "http://maps.google.com/maps/api/js?sensor=false&callback=gMapsCallback");
			(document.getElementsByTagName("head")[0] || document.documentElement).appendChild(script_tag);
		};
	}
/* ~ END: GOOGLE MAPS */

/*
 * LOAD SCRIPTS
 * Usage:
 * Define function = myPrettyCode ()...
 * loadScript("js/my_lovely_script.js", myPrettyCode);
 */

var loadScript = function () {
	var arg = arguments[0];
	if (typeof arg == "string") {
		var callback = arguments[1] || function () {
			};
		if (jsArray[arg]) callback && callback();
		else {
			jsArray[arg] = !0;
			$.getScript(arg, function () {
				callback && callback()
			});
		}
	} else if ($.type(arg) == "array") { // array
		var root = "", callback;
		if (typeof arguments[1] == "function") {
			callback = arguments[1];
		} else {
			root = arguments[1] || "";
			callback = arguments[2];
		}
		var c = 0;
		var cb = function () {
			if (c++ == arg.length - 1) {
				callback && callback()
			} else l()
		}
		var l = function () {
			if (jsArray[root + arg[c]]) {
				cb()
			}
			else {
				jsArray[root + arg[c]] = !0;
				$.getScript(root + arg[c], cb)
			}
		}
		l();
	}
}
var loadCss = function (link) {
	!cssArray[link] && ($("head:first").append($('<link href="' + link + '" rel="stylesheet" type="text/css" />')), cssArray[link] = !0)
}

var loadModule = function(name, cb){
	var _loadModule = function(name, cb){
		var libs = [], scripts = [], type = $.type(name);
		if (type == "string") {
			libs = libs.concat(modules[name]);
		} else if(type == "array"){
			$.each(name, function(i){libs = libs.concat(modules[name[i]])});
		}
		$.each(libs, function(i){
			if(/^css!/.test(libs[i])){
				loadCss(window.modules_root + libs[i].substring(4));
			} else {
				scripts.push(window.modules_root + libs[i]);
			}
		});
		loadScript(scripts, cb);
	}
	if(!window.modules){
		loadScript(ctx + "/js/module.js", function(){
			_loadModule(name, cb);
		})
	} else {
		_loadModule(name, cb);
	}
}

/* ~ END: LOAD SCRIPTS */

/*
* APP AJAX REQUEST SETUP
* Description: Executes and fetches all ajax requests also
* updates naivgation elements to active
*/
	if($.navAsAjax) {
	    // fire this on page load if nav exists
	    if ($('nav').length) {
		    checkURL();
	    }
	
	    $(document).on('click', 'nav a[href!="#"]', function(e) {
		    e.preventDefault();
		    var $this = $(e.currentTarget);
	
		    // if parent is not active then get hash, or else page is assumed to be loaded
			if (!$this.parent().hasClass("active") && !$this.attr('target')) {
	
			    // update window with hash
			    // you could also do here:  thisDevice === "mobile" - and save a little more memory
	
			    if ($.root_.hasClass('mobile-view-activated')) {
				    $.root_.removeClass('hidden-menu');
				    $('html').removeClass("hidden-menu-mobile-lock");
				    window.setTimeout(function() {
						if (window.location.search) {
							window.location.href =
								window.location.href.replace(window.location.search, '')
									.replace(window.location.hash, '') + '#' + $this.attr('href');
						} else {
							window.location.hash = $this.attr('href');
						}
				    }, 150);
				    // it may not need this delay...
			    } else {
					if (window.location.search) {
						window.location.href =
							window.location.href.replace(window.location.search, '')
								.replace(window.location.hash, '') + '#' + $this.attr('href');
					} else {
						window.location.hash = $this.attr('href');
					}
			    }
			    
			    // clear DOM reference
			    // $this = null;
		    }
	
	    });
	
	    // fire links with targets on different window
	    $(document).on('click', 'nav a[target="_blank"]', function(e) {
		    e.preventDefault();
		    var $this = $(e.currentTarget);
	
		    window.open($this.attr('href'));
	    });
	
	    // fire links with targets on same window
	    $(document).on('click', 'nav a[target="_top"]', function(e) {
		    e.preventDefault();
		    var $this = $(e.currentTarget);
	
		    window.location = ($this.attr('href'));
	    });
	
	    // all links with hash tags are ignored
	    $(document).on('click', 'nav a[href="#"]', function(e) {
		    e.preventDefault();
	    });
	
	    // DO on hash change
	    $(window).on('hashchange', function() {
		    checkURL();
	    });
	}
/*
 * CHECK TO SEE IF URL EXISTS
 */
function checkURL() {

    //get the url by removing the hash
    //var url = location.hash.replace(/^#/, '');
    var url = location.href.split('#').splice(1).join('#');
    //BEGIN: IE11 Work Around
    if (!url) {
        try {
            var documentUrl = window.document.URL;
            if (documentUrl) {
                if (documentUrl.indexOf('#', 0) > 0 && documentUrl.indexOf('#', 0) < (documentUrl.length + 1)) {
                    url = documentUrl.substring(documentUrl.indexOf('#', 0) + 1);
                }
            }
        } catch (err) {
        }
    }
    //END: IE11 Work Around

    container = $('#content');
    // Do this if url exists (for page refresh, etc...)
    if (url) {
        // remove all active class
        $('nav li.active').removeClass("active");
        // match the url and add the active class
        $('nav li:has(a[href="' + url + '"])').addClass("active");
        var title = ($('nav a[href="' + url + '"]').attr('title'));

        // change page title from global var
        document.title = (title || document.title);

        // debugState
        if (debugState) {
            root.console.log("Page title: %c " + document.title, debugStyle_green);
        }

        // parse url to jquery
        loadURL(url + location.search, container);

    } else {

        // grab the first URL from nav
        var $this = $('nav > ul > li:first-child a[href!="#"]:first');
        $('nav li:has(a[href="' + $this.attr("href") + '"])').addClass("active");

        //update hash
        window.location.hash = $this.attr('href');

        //clear dom reference
        $this = null;

    }

}

/*
 * LOAD AJAX PAGES
 */
var __JQ_REQUEST_ = null;
function loadURL(url, container) {
	//判断是否浏览历史记录，如为历史记录，则不再入栈
	if('string' == typeof url) {
		var history = {url: url, type: 'history'};
		history_stack.unshift(history)
	}
	if('object' == typeof url && 'history' == url.type) {
		url = url.url
	}

	if($.trim(url) == "" || $.trim(url) == "#") return;
	container = container || $("#content");
	container.attr("url", url);
	// debugState
	if (debugState) {
		root.root.console.log("Loading URL: %c" + url, debugStyle);
	}
	__JQ_REQUEST_ = $.ajax({
		type: "GET",
		url: url,
		dataType: 'html',
		cache: true, // (warning: setting it to false will cause a timestamp and will call the request twice)
		beforeSend: function () {
			__JQ_REQUEST_ && __JQ_REQUEST_.abort();
			$.fn.bootstrapValidator && container.find("form.bv-form").length != 0 && container.find("form.bv-form").each(function(){
				$(this).bootstrapValidator("destroy");
			});



			//IE11 bug fix for googlemaps (delete all google map instances)
			//check if the page is ajax = true, has google map class and the container is #content
			if ($.navAsAjax && $(".google_maps")[0] && (container[0] == $("#content")[0])) {

				// target gmaps if any on page
				var collection = $(".google_maps"),
					i = 0;
				// run for each	map
				collection.each(function () {
					i++;
					// get map id from class elements
					var divDealerMap = document.getElementById(this.id);

					if (i == collection.length + 1) {
						// "callback"
					} else {
						// destroy every map found
						if (divDealerMap) divDealerMap.parentNode.removeChild(divDealerMap);

						// debugState
						if (debugState) {
							root.console.log("Destroying maps.........%c" + this.id, debugStyle_warning);
						}
					}
				});

				// debugState
				if (debugState) {
					root.console.log("✔ Google map instances nuked!!!");
				}

			} //end fix

			// destroy all datatable instances
			if ($.navAsAjax && $('.dataTables_wrapper')[0] && (container[0] == $("#content")[0])) {

				var tables = $.fn.dataTable.fnTables(true);
				$(tables).each(function () {

					if ($(this).find('.details-control').length != 0) {
						$(this).find('*').addBack().off().remove();
						$(this).dataTable().fnDestroy();
					} else {
						$(this).dataTable().fnDestroy();
					}

				});

				// debugState
				if (debugState) {
					root.console.log("✔ Datatable instances nuked!!!");
				}
			}
			// end destroy

			// pop intervals (destroys jarviswidget related intervals)
			if ($.navAsAjax && $.intervalArr.length > 0 && (container[0] == $("#content")[0]) && enableJarvisWidgets) {

				while ($.intervalArr.length > 0)
					clearInterval($.intervalArr.pop());
				// debugState
				if (debugState) {
					root.console.log("✔ All JarvisWidget intervals cleared");
				}

			}
			// end pop intervals

			// destroy all widget instances
			if ($.navAsAjax && (container[0] == $("#content")[0]) && enableJarvisWidgets && $("#widget-grid")[0]) {

				$("#widget-grid").jarvisWidgets('destroy');
				// debugState
				if (debugState) {
					root.console.log("✔ JarvisWidgets destroyed");
				}

			}
			// end destroy all widgets

			// cluster destroy: destroy other instances that could be on the page
			// this runs a script in the current loaded page before fetching the new page
			if ($.navAsAjax && (container[0] == $("#content")[0])) {

				/*
				 * The following elements should be removed, if they have been created:
				 *
				 *	colorList
				 *	icon
				 *	picker
				 *	inline
				 *	And unbind events from elements:
				 *
				 *	icon
				 *	picker
				 *	inline
				 *	especially $(document).on('mousedown')
				 *	It will be much easier to add namespace to plugin events and then unbind using selected namespace.
				 *
				 *	See also:
				 *
				 *	http://f6design.com/journal/2012/05/06/a-jquery-plugin-boilerplate/
				 *	http://keith-wood.name/pluginFramework.html
				 */

				// this function is below the pagefunction for all pages that has instances

				if (typeof pagedestroy == 'function') {

					try {
						pagedestroy();

						if (debugState) {
							root.console.log("✔ Pagedestroy()");
						}
					}
					catch (err) {
						pagedestroy = undefined;

						if (debugState) {
							root.console.log("! Pagedestroy() Catch Error");
						}
					}

				}

				// destroy all inline charts

				if ($.fn.sparkline && $("#content .sparkline")[0]) {
					$("#content .sparkline").sparkline('destroy');

					if (debugState) {
						root.console.log("✔ Sparkline Charts destroyed!");
					}
				}

				if ($.fn.easyPieChart && $("#content .easy-pie-chart")[0]) {
					$("#content .easy-pie-chart").easyPieChart('destroy');

					if (debugState) {
						root.console.log("✔ EasyPieChart Charts destroyed!");
					}
				}


				// end destory all inline charts

				// destroy form controls: Datepicker, select2, autocomplete, mask, bootstrap slider

				if ($.fn.select2 && $("#content select.select2")[0]) {
					$("#content select.select2").select2('destroy');

					if (debugState) {
						root.console.log("✔ Select2 destroyed!");
					}
				}

				if ($.fn.mask && $('#content [data-mask]')[0]) {
					$('#content [data-mask]').unmask();

					if (debugState) {
						root.console.log("✔ Input Mask destroyed!");
					}
				}

				if ($.fn.datepicker && $('#content .datepicker')[0]) {
					$('#content .datepicker').off();
					$('#content .datepicker').remove();

					if (debugState) {
						root.console.log("✔ Datepicker destroyed!");
					}
				}

				if ($.fn.slider && $('#content .slider')[0]) {
					$('#content .slider').off();
					$('#content .slider').remove();

					if (debugState) {
						root.console.log("✔ Bootstrap Slider destroyed!");
					}
				}

				// end destroy form controls


			}
			// end cluster destroy

			// empty container and var to start garbage collection (frees memory)
			pagefunction = null;
			container.removeData().html("");

			// place cog
			container.html('<h1 class="ajax-loading-animation"><i class="fa fa-cog fa-spin"></i> 加载中...</h1>');

			// Only draw breadcrumb if it is main content material
			if (container[0] == $("#content")[0]) {

				// clear everything else except these key DOM elements
				// we do this because sometime plugins will leave dynamic elements behind
				$('body').find('> *').filter(':not(' + ignore_key_elms + ')').empty().remove();

				// draw breadcrumb
				drawBreadCrumb();

				// scroll up
				$("html").animate({
					scrollTop: 0
				}, "fast");
			}
			// end if
		},
		success: function (data) {

			// dump data to container
			container.css({
				opacity: '0.0'
			}).html(data).delay(50).animate({
				opacity: '1.0'
			}, 300);

			$('[data-toggle="tooltip"]', container).tooltip();

			$(".js-status-update", container).on("click", "a", function () {
				var selText = $(this).text();
				var $this = $(this);
				$this.parents('.btn-group').find('.dropdown-toggle').html(selText + ' <span class="caret"></span>');
				$this.parents('.dropdown-menu').find('li').removeClass('active');
				$this.parent().addClass('active');
			});

			if(container[0] == $("#content")[0]){
				pageSetUp();
				var pageTitle = $("page-title", container),
					navs = $("nav li.active > a");
				var s = '<div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">';
				$.each(navs, function(i){
					var nav = $(navs[i]),
						text = $.trim(nav.clone().children(".badge").remove().end().text()),
						link = nav.attr("href");
					if(i == 0){
						s += '<h1 class="cursor-pointer page-title txt-color-blueDark">';
						s += '<i class="' + (nav.find(">i").attr("class")||"").replace("fa-lg", "") + '"></i>';
						s += '<a style="color:#000000;" onclick="loadURL(\'' + link + '\')">' + text + '</a>';
					} else {
						s += '&nbsp;<span onclick="loadURL(\'' + link + '\')">&gt;&nbsp;' + text + '</span>';
					}
				});
				var pTitle = $.trim(pageTitle.text());
				if(pTitle != ""){
					s += '&nbsp;<span onclick="refresh()">&gt;&nbsp;' + pTitle + '</span>';
					drawBreadCrumb([{text: pTitle, link: $("#content").attr("url")}]);
				}
				s += '</div>';
				pageTitle.before(s).remove();
			};

			// clear data var
			data = null;
			container = null;
		},
		error: function (xhr, status, thrownError, error) {
			// container.html('<h4 class="ajax-loading-error"><i class="fa fa-warning txt-color-orangeDark"></i> Error requesting <span class="txt-color-red">' + url + '</span>: ' + xhr.status + ' <span style="text-transform: capitalize;">' + thrownError + '</span></h4>');
			var msg = '请求失败 <span class="txt-color-red">' + error + '</span>：' + xhr.status + ' <span style="text-transform: capitalize;">' + thrownError + '</span>';
			try {
				var obj = JSON.parse(xhr.responseText);
				if(obj.msg){
					msg = '请求失败：<span class="txt-color-red">' + obj.msg + '</span>'
				}
			} catch(e){console.error("loadURL faild: " + xhr.responseText);}
			container.html('<h4 class="ajax-loading-error"><i class="fa fa-warning txt-color-red"></i>  ' + msg + '</h4>');
		},
		async: true
	});

}
/*
 * UPDATE BREADCRUMB
 */ 
function drawBreadCrumb(opt_breadCrumbs) {
	var a = $("nav li.active > a"),
		b = a.length;

	bread_crumb.empty(),
	bread_crumb.append($("<li>Home</li>")), a.each(function() {
        bread_crumb.append($("<li></li>").html('<a href="javascript:void(0);" onclick="loadURL(\'' + $(this).attr("href") + '\')">' + $.trim($(this).clone().children(".badge").remove().end().text()) + '</a>')), --b || (document.title = bread_crumb.find("li:last-child").text())
	});

	// Push breadcrumb manually -> drawBreadCrumb(["Users", "John Doe"]);
	// Credits: Philip Whitt | philip.whitt@sbcglobal.net
	if (opt_breadCrumbs != undefined) {
        $.each(opt_breadCrumbs, function (index, nav) {
            bread_crumb.append($("<li></li>").html('<a href="javascript:void(0);" onclick="loadURL(\'' + nav.link + '\')">' + nav.text + '</a>'));
			document.title = bread_crumb.find("li:last-child").text();
		});
	}
}
/* ~ END: APP AJAX REQUEST SETUP */

function datatableInit() {
	/* datatable默认初始化设置 */
	dt_default_options = {
		dom: "<'dt-toolbar custom-toolbar'>t<'dt-toolbar-footer'<'col-sm-6 hidden-xs'i><'col-sm-6 col-xs-12'p>>",
		language: {
			dom: "",
			processing: "处理中...",
			lengthMenu: "每页 _MENU_ 项",
			zeroRecords: "没有匹配结果",
			info: "显示第 _START_ 至 _END_ 项，共 _TOTAL_ 项",
			infoEmpty: "没有可显示项",
			infoFiltered: "(由 _MAX_ 项过滤)",
			infoPostFix: "",
			search: "搜索:",
			url: "",
			emptyTable: "暂未查询到相关数据",
			loadingRecords: "载入中...",
			infoThousands: ",",
			paginate: {
				first: "首页",
				previous: "上一页",
				next: "下一页",
				last: "尾页"
			},
			aria: {
				sortAscending: ": 以升序排列此列",
				sortDescending: ": 以降序排列此列"
			}
		},
		searching: false,
		lengthChange: true,
		serverSide: true,
		retrieve: true,
		bProcessing: true,
		aoColumnDefs: [{sDefaultContent: '', aTargets: [ '_all' ]}],
	};

	dtAutoOption = function(table, pointDefine){
		var responsiveHelper_dt_basic = undefined;
		var breakpointDefinition = {
			tablet : 1024,
			phone : 480
		};
		return $.extend({}, dt_default_options, {
			sDom: "<'dt-toolbar custom-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r>t<'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'pl>>",
			autoWidth : true,
			preDrawCallback : function() {
				// Initialize the responsive datatables helper once.
				if (!responsiveHelper_dt_basic) {
					responsiveHelper_dt_basic = new ResponsiveDatatablesHelper(table, pointDefine || breakpointDefinition);
				}
			},
			rowCallback : function(nRow) {
				responsiveHelper_dt_basic.createExpandIcon(nRow);
			},
			drawCallback : function(oSettings) {
				responsiveHelper_dt_basic.respond();
			}
		})
	};

	//将datatable请求参数简化
	toEnableParams = function (data) {
		var params = 'object' === typeof arguments[1] ? arguments[1] : {};
		var _params = {};
		_params.length = data.length;
		_params.start = data.start;
		_params.order = new Array();
		if(data.order.length > 0) {
			for (var i in data.order) {
				var _order = {};
				_order.column = data.columns[data.order[i].column].data;
				_order.dir = data.order[i].dir;
				_params.order[i] = _order;
			}
		}
		return $.extend(true, _params, params);
	};
	/* datatable默认初始化设置 */
}

/*
 * PAGE SETUP
 * Description: fire certain scripts that run through the page
 * to check for form elements, tooltip activation, popovers, etc...
 */
function pageSetUp() {

	if (thisDevice === "desktop"){
		// is desktop

		// activate tooltips
		$("[rel=tooltip], [data-rel=tooltip]").tooltip();

		// activate popovers
		$("[rel=popover], [data-rel=popover]").popover();

		// activate popovers with hover states
		$("[rel=popover-hover], [data-rel=popover-hover]").popover({
			trigger : "hover"
		});

		// setup widgets
		setup_widgets_desktop();

		//datatable init
		datatableInit()

		// run form elements
		runAllForms();


	} else {

		// is mobile

		// activate popovers
		$("[rel=popover], [data-rel=popover]").popover();

		// activate popovers with hover states
		$("[rel=popover-hover], [data-rel=popover-hover]").popover({
			trigger : "hover"
		});

		// setup widgets
		setup_widgets_mobile();

		//datatable init
		datatableInit()

		// run form elements
		runAllForms();
	}

}
/* ~ END: PAGE SETUP */

/*
 * ONE POP OVER THEORY
 * Keep only 1 active popover per trigger - also check and hide active popover if user clicks on document
 */
$('body').on('click', function(e) {
	$('[rel="popover"], [data-rel="popover"]').each(function() {
		//the 'is' for buttons that trigger popups
		//the 'has' for icons within a button that triggers a popup
		if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
			$(this).popover('hide');
		}
	});
});
/* ~ END: ONE POP OVER THEORY */

/*
 * DELETE MODEL DATA ON HIDDEN
 * Clears the model data once it is hidden, this way you do not create duplicated data on multiple modals
 */
$('body').on('hidden.bs.modal', '.modal', function () {
  $(this).removeData('bs.modal');
});
/* ~ END: DELETE MODEL DATA ON HIDDEN */

/*
 * HELPFUL FUNCTIONS
 * We have included some functions below that can be resued on various occasions
 * 
 * Get param value
 * example: alert( getParam( 'param' ) );
 */
function getParam(name) {
	name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
	var regexS = "[\\?&]" + name + "=([^&#]*)";
	var regex = new RegExp(regexS);
	var results = regex.exec(window.location.href);
	if (results == null)
		return "";
	else
		return results[1];
}

/**
 * 重新包装弹框，对话框等
 * @type {{warn: Dialog.warn, success: Dialog.success, error: Dialog.error, info: Dialog.info, __smallBox: Dialog.__smallBox, alert: Dialog.alert, confirm: Dialog.confirm, crop: Dialog.crop, modal: Dialog.modal, prompt: Dialog.prompt}}
 */
var Dialog = {
	warn : function(){
		Dialog.__smallBox.call(this,"warn",arguments);
	},
	success : function(){
		Dialog.__smallBox.call(this,"success",arguments);
	},
	error : function(){
		Dialog.__smallBox.call(this,"error",arguments)
	},
	info : function(){
		Dialog.__smallBox.call(this, "info", arguments);
	},
	__smallBox : function(level,args){
		var title,content="",callback=null,time=3000,style,color;
		if("warn"==level){
			title="警告";
			style="fa-warning";
			color="#9b711a";
		}
		if("success"==level){
			title="操作成功";
			color="#5b835b";
			style="fa-check-circle";
		}
		if("info"==level) {
			title="信息";
			color="#275b89";
		}
		if("error"==level) {
			title="错误";
			style="fa-times-circle";
			color="#77021d";
		}
		if(args.length == 1){
			content=args[0]
		} else if(args.length == 2) {
			title = args[0]||title;
			content = args[1];
		} else if(args.length == 3){
			title = args[0]||title;
			content = args[1];
			time = args[2]||time;
		}else {
			title = args[0]||title;
			content = args[1];
			time = args[2]||time;
			callback = args[3];
		}
		$.smallBox({
			title : title||"信息提示",
			content : content||"未知错误",
			color : color,
			timeout: time,
			icon : "fa " + (style||"fa-info-circle") + " swing animated"
		}, callback);
	},
	alert : function(info, content, callback){
		$.SmartMessageBox({
			title : info,
			content : content||'',
			buttons : '[确定]'
		}, function(){
			var name = arguments[0];
			if('确定' == name){
				if( callback ){callback();}
			}
		});
	},
	confirm : function(title, content, callback){
		if($.type(content) == 'function'){callback = content;content=null;}
		$.SmartMessageBox({
			title : title,
			content : content||'',
			buttons : '[取消][确定]'
		}, function(){
			var name = arguments[0];
			if('确定' == name){
				if( callback ){callback();}
			}
		});
	},
	crop : function(src, op, cb, no){
		var cropImg = null;
		var image = $(new Image());
		$(document.body).append(image);
		image.css("display", "none");
		image.attr("src", src);
		image.load(function(){
			var width = image.width(), height = image.height();
			var canvas = document.createElement("canvas");
			canvas.width = width, canvas.height = height;
			canvas.getContext("2d").drawImage(image[0], 0, 0, width, height);
			var url = canvas.toDataURL("image/jpg");
			image.remove();
			var modal = Dialog.modal({
				buttons: {
					"取 消": {
						icon: "glyphicon glyphicon-remove",
						close: true,
						callback: no
					},
					"裁 剪": {
						icon: "glyphicon glyphicon-ok",
						'class': "btn-primary",
						close: true,
						callback: function(){
							var data = cropImg.cropper('getData');
							var cropCanvas = cropImg.cropper("getCroppedCanvas");
							var cropUrl = cropCanvas.toDataURL("image/jpg");
							if(op.preview){
								var preview = $(op.preview);
								var width = cropCanvas.width;
								if(preview.hasClass("file-preview-frame")){
									width = preview.find("img:first").width();
								}
								preview.html("").append("<img src='" + cropUrl+ "' width='" + width + "'/>");
							}
							cb&&cb({x:parseInt(data.x),y:parseInt(data.y),width:parseInt(data.width),height:parseInt(data.height)});
						}
					}
				},
				content: "<div><img src='" + url + "' style='width:100%;height:100%;'></div>",
				title: "裁剪图片",
				modalSize: "modal-lg",
				backdrop: "static",
				show: true
			}).on("shown.bs.modal", function(){
				cropImg = modal.find(".modal-body>div>img")
				loadCss("/assets/js/plugin/cropper/cropper.min.css");
				loadScript("/assets/js/plugin/cropper/cropper.min.js", function(){
					cropImg.cropper($.extend({
						autoCropArea: 1,
						viewMode: 1
					}, op, {preview:null}));
				});
			});
		});
	},
	modal: function(options){
		options = $.extend({
			buttons: {
				"关 闭": {
					icon: "glyphicon glyphicon-remove",
					close: true
				}
			},
			modalSize: "modal-md",
			title: "",
			content: "",
			show: true,
			modalClass: "fade"
		}, options);
		var tpl = "<div class='modal @{modalClass}'><div class='modal-dialog @{modalSize}'><div class ='modal-content'>{@if title}<div class ='modal-header'><button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button> <h4 class='modal-title'>@{title}</h4></div>{@/if}<div class='modal-body' style='min-height: 30px;'> @@{ content } </div>{@if buttons}<div class ='modal-footer'> {@each buttons as b,i} <button type='button' class='btn @{b.class}' {@if b.close} data-dismiss='modal'{@/if}>{@if b.icon}<i class='@{b.icon}'></i>{@/if}&nbsp; @{i}</button>{@/each}</div>{@/if}</div></div></div>";
		var m = $(juicer(tpl, options));
		m.appendTo(document.body)
			.modal(options);
		m.on("click",".modal-footer button", function(){
			var bt = options.buttons[$.trim($(this).text())];
			try {
				bt&&bt.callback&&bt.callback();
			} catch(e){console.error(e);}
		}).on("hidden.bs.modal", function(){
			m.remove();
		});
		return m;
	},
	prompt: function(i,yes,no,ipt){
		try {
			ipt = ipt || {};
			ipt.type = ipt.type || "text";
			ipt.value = ipt.value || "";
			ipt.placeholder = ipt.placeholder || "";
			var inputId = ("_input_" + Math.random() + "_").replace(".", "") ;
			this.modal({
				buttons: {
					"取 消": {
						icon: "glyphicon glyphicon-remove",
						close: true,
						callback: no
					},
					"确 定": {
						icon: "glyphicon glyphicon-ok",
						'class': "btn-primary",
						close: true,
						callback: function(){yes&&yes($("#" + inputId).val())}
					}
				},
				content: "<input type=" + (ipt.type) + " id='" + inputId + "' class='form-control' value='" + (ipt.value) + "' placeholder='" + (ipt.placeholder) + "'/>",
				title: i,
				modalSize: "modal-sm"
			});
		} catch(e){
			var r = window.prompt(i);
			c&&c(r);
		}
	}
};

var goBack = function(){
	var history = history_stack.shift();
	if(history == undefined){
		return;
	}
	var current = $('#content').attr('url');
	if(current == history.url){
		history = history_stack.shift();
	}
	loadURL(history);
	return;
};
var refresh = function(){loadURL($("#content").attr("url"),$("#content"));}
var setSubmit = function(){
    try {$("#content form").data("bootstrapValidator").validate();}catch(e){console.error(e);}
}
/* ~ END: HELPFUL FUNCTIONS */
    $.ajaxSetup({
        error : function(resp) {
            console.log(resp)
        }
    })
    
    $('nav > ul a[href!="#"]').click(function(){location.href.split("#").splice(1).join("#") == $(this).attr("href") && checkURL();});
