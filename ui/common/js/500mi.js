// placeholder
 //(function($) {
	//if (!$.browser.msie || $.browser.version >8) return;
	$(function() {
		$(':input[placeholder]').each(function() {
			if (!$.browser.msie || $.browser.version >8) return;
			setupPasswords($(this))
		});
		$('form').submit(function(e) {
			if (!$.browser.msie || $.browser.version >8) return;
			clearPlaceholdersBeforeSubmit($(this))
		})
	});
	function setupPlaceholder(input) {
		var placeholderText = input.attr('placeholder');
		setPlaceholderOrFlagChanged(input, placeholderText);
		input.focus(function(e) {
			if (input.data('changed') === true) return;
			if (input.val() === placeholderText) input.val('')
		}).blur(function(e) {
			if (input.val() === '') input.val(placeholderText)
		}).change(function(e) {
			input.data('changed', input.val() !== '')
		})
	}
	function setPlaceholderOrFlagChanged(input, text) { (input.val() === '') ? input.val(text) : input.data('changed', true)
	}
	function setupPasswords(input) {
		var passwordPlaceholder = createPasswordPlaceholder(input);
		input.after(passwordPlaceholder); (input.val() === '') ? input.hide() : passwordPlaceholder.hide();
		$(input).blur(function(e) {
			if (input.val() !== '') return;
			input.hide();
			passwordPlaceholder.show()
		});
		$(passwordPlaceholder).focus(function(e) {
			input.show().focus();
			passwordPlaceholder.hide()
		})
	}
	function createPasswordPlaceholder(input) {
		return $('<input>').attr({
			placeholder: input.attr('placeholder'),
			value: input.attr('placeholder'),
			id: input.attr('id')
			// readonly: true
		}).addClass(input.attr('class'))
	}
	function clearPlaceholdersBeforeSubmit(form) {
		form.find(':input[placeholder]').each(function() {
			if ($(this).data('changed') === true) return;
			if ($(this).val() === $(this).attr('placeholder')) $(this).val('')
		})
	}
//})(jQuery);

// 分类过长,修正到html节点后端
function fix_category_li() {
	$('.category-list .per_category_li').each(function(){
		if($(this).height() > 22) {
			$(this).css('width', 798).appendTo('.category-list .all_category');
		} else {
			$(this).css('width', 399);
		}
	});
}

function countDown(time,day_elem,hour_elem,minute_elem,second_elem){
	//if(typeof end_time == "string")
	time = time.replace(/-/, '/');
	time = time.replace(/-/, '/');
	var end_time = new Date(time).getTime(),//月份是实际月份-1
	//current_time = new Date().getTime(),
	sys_second = (end_time-new Date().getTime())/1000;
	var timer = setInterval(function(){
		sys_second -= 1;
		if (sys_second >= 0) {
			var day = Math.floor((sys_second / 3600) / 24);
			var hour = Math.floor((sys_second / 3600) % 24);
			var minute = Math.floor((sys_second / 60) % 60);
			var second = Math.floor(sys_second % 60);
			day_elem && $(day_elem).text(day);//计算天
			$(hour_elem).text(hour<10?"0"+hour:hour);//计算小时
			$(minute_elem).text(minute<10?"0"+minute:minute);//计算分
			$(second_elem).text(second<10?"0"+second:second);// 计算秒
		} else { 
			clearInterval(timer);
		}
	}, 1000);
}
// 自适应
var $widthQuery = function(){
	var width = $(window).width();
	if (width > 1020) {
		$('html').addClass('w1000');
		$('.w1000 .use-w1000 .show-w1000').show();
	} else {
		$('html').removeClass('w1000');
		$('.show-w1000').hide();
	}
	if (width > 1220) {
		$('html').addClass('w1200');
		$('.w1200 .use-w1200 .show-w1200').show();
	} else {
		$('html').removeClass('w1200');
		$('.show-w1200').hide();
	}
	if (width > 1420) {
		$('html').addClass('w1400');
		$('.w1400 .use-w1400 .show-w1400').show();
	} else {
		$('html').removeClass('w1400');
		$('.show-w1400').hide();
	}
}
$widthQuery();
$(window).bind('resize', $widthQuery);

$(function() {
	$('.lazyimg').lazyload({
		threshold: 200,
		effect : 'fadeIn',
		data_attribute: 'lazysrc'
	});
	$('.timeDown').each(function(){
		countDown($(this).data('endtime'), $(this).find('.day'), $(this).find('.hour'), $(this).find('.minute'), $(this).find('.second'));
	});

	$(".search-btn").live("click",function(){
		if (location.pathname == "/shop/stock" || ((location.pathname == "/channel/jiushuiyinliao" || location.pathname == "/channel/jinkoulingshi") && $(this).data('value') != 'all_site')) {
			$(".nav-item.active").removeClass("active").find("a").remove();
			$(".third-cate-list,.brand-list,.partner-list").remove();
			// Item.renderItem('',{item_name:$(".search").val()});
			return false;
		} else {
			window.location = "/shop/stock?item_name="+ encodeURI($(".search").val());
		}
	})
	$(".ac-item-search").keydown(function(event){
		if (event.keyCode == 13) {
			if (location.pathname == "/channel/jiushuiyinliao" || location.pathname == "/channel/jinkoulingshi") {		
				$('.search-chn-btn').click();
				return false;
			} else {
				$('.search-site-btn').click();
				return false;
			}
		}
	});
	$(".hot-search a").click(function(){
		$(".search").val($(this).text());
		$(".search-btn").click();
	});

	// 头部关闭按钮
	$(".head-close").live("click",function(){
		$(this).parents(".add-item-table").hide();
	});
	// 监听input变化
	if(document.all){
	    $('input[type="text"]').each(function() {
	        var that=this;

	        if(this.attachEvent) {
	            this.attachEvent('onpropertychange',function(e) {
	                if(e.propertyName!='value') return;
	                $(that).trigger('input');
	            });
	        }
	    });
	};
	// 操作按钮的点击用hover替代
	$('.btn-group').live('hover', function(){
        $(this).children('ul').toggle();
     });

	// navbar菜单hover切换
	$('.top-nav .nav-item').hover(function(){
		if ($(this).children('.nav-subbox').length > 0) {
			$(this).toggleClass('nav-item-hover').children('.trig').toggleClass('trig-top trig-bottom');
		}
	});
	// idtabs
	$('.ui-box .ui-box-title .idTabs a').click(function(){
		$(this).parent().parent().children().removeClass('ui-tab-trigger-item-current');
		$(this).parent().addClass('ui-tab-trigger-item-current');
	});
	fix_category_li();
	if ($.browser.msie && parseInt($.browser.version, 10) === 6) {
		$('.row div[class^="span"]:last-child').addClass('last-child');
        // $('[class*="span"]').addClass('margin-left-20');
        $(':button[class="btn"], :reset[class="btn"], :submit[class="btn"], input[type="button"]').addClass('button-reset');
        $(':checkbox').addClass('input-checkbox');
        $('[class^="icon-"], [class*=" icon-"]').addClass('icon-sprite');
        // $('.pagination li:first-child a').addClass('pagination-first-child');
	}

	var beta_v20121030 = store.get('beta_v201211261741');
	if(!beta_v20121030){
		store.clear();
		store.set('beta_v201211261741','beta_v201211261741');
	}

	$.fn.smartFloat = function() {
		var position = function(element) {
			var top = element.position().top, pos = element.css("position");
			$(window).scroll(function() {
				var scrolls = $(this).scrollTop();
				if (scrolls > top && $('.e-side-static').attr('t') != 1) {
					if (window.XMLHttpRequest) {
						element.css({
							position: "fixed",
							top: 0
						});	
					} else {
						element.css({
							top: scrolls
						});	
					}
				}else {
					element.css({
					position: pos,
					top: top
					});	
				}
			});
		};
		return $(this).each(function() {
			position($(this));
		});	
	}

	$.fn.navFixed = function() {
		var position = function(element) {
			var top = element.position().top,
				height = element.height(),
				pos = element.css("position"),
				margin_top = $(element).next().css('marginTop');
				margin_top = parseInt(margin_top);

			$(window).scroll(function() {
				var scrolls = $(this).scrollTop();
				if (scrolls > top) {
					if (window.XMLHttpRequest) {
						element.css({
							position: "fixed",
							top: 0
						});	
					} else {
						element.css({
							top: scrolls
						});	
					}

					$(element).next().css('margin-top',  margin_top + height);
				}else {
					element.css({
						position: pos,
						top: top
					});	
					$(element).next().css('margin-top', margin_top);
				}
			});
		};
		return $(this).each(function() {
			position($(this));
		});	
	}

	// 点close关闭
	$(".close").live("click",function(){
		$(this).parent(".alert").remove();
	});
	
	// 获取系统消息
	if (location.href.indexOf('work.500mi.com') > 0) {
		$.get("/boss/sms/sms_notify",function(data){
			if(data > 0){
				$('.top-msg-num').text(data);
				$('.top-msg').addClass('top-msg-flashed');
				msg_flash_clock = setInterval('msg_flash()', 1000);
			}
		});
	}

	// 商品服务属性tip
	// $('.item-service-list span').poshytip({
	// 	className: 'wk-tip',
	// 	fade: false,
	// 	slide: false
	// });
});

// 系统消息闪烁效果
function msg_flash(){
	$('.top-msg-flashed').fadeIn(function(){
		$(this).fadeOut();
	}).fadeOut(function(){
		$(this).fadeIn();
	});
}
function Trim(str,is_global) { 
	var result; 
	result = str.replace(/(^\s+)|(\s+$)/g,""); 
	if(is_global.toLowerCase()=="g") 
	result = result.replace(/\s/g,""); 
	return result; 
}

$.fn.outerHtml = function(){
    // IE, Chrome & Safari will comply with the non-standard outerHTML, all others (FF) will have a fall-back for cloning
    return (!this.length) ? this : (this[0].outerHTML || (
      function(el){
          var div = document.createElement('div');
          div.appendChild(el.cloneNode(true));
          var contents = div.innerHTML;
          div = null;
          return contents;
    })(this[0]));
}
