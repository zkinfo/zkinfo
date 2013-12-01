/**
 * Jquery 自定义扩展
 * 语法格式，符合Jquery风格的，才可以放在这里...
 */

/**
 * Jquery 批量取值
 * 原理：单个取值，然后使用逗号','拼接成字符串
 */
$.prototype.vars = function() {
	var vars = [];
	$(this).each(function(){
		vars.push($(this).val());
	});	
	return vars.join(',');
};

$().ready(function(){
	//输入搜索框
	$('input').each(function(){
		if ($(this).val() == '') {
			$(this).val($(this).data('placeholder')).css('color', 'rgb(173, 173, 173)');
		}
	});
});


$('input').live('blur', function(){
	if ($(this).val() == '') {
		$(this).val($(this).data('placeholder')).css('color', 'rgb(173, 173, 173)');
	} else {
		if ($(this).val() == $(this).data('placeholder')) {
			$(this).css('color', 'rgb(173, 173, 173)');
		} else {
			if ($(this).attr('type') != 'button' && $(this).attr('type') != 'submit') {
				$(this).css('color', '#000');
			}
		}		
	}
});

$('input').live('keydown focus click change', function(){
	if ($(this).val() == $(this).data('placeholder')) {
		$(this).val('').css('color', 'rgb(173, 173, 173)');
	} else {
		if ($(this).attr('type') != 'button' && $(this).attr('type') != 'submit') {
			$(this).css('color', '#000');
		}
	}
	// if ($(this).data('placeholder')) {
	// 	$(this).css('color', '#000');
	// }
});