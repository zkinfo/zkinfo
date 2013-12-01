$('input, select').live('focus change blur',function(){
	//if ($(this).attr(validate)) {
		validate($(this));
	//}
});

function validate(input) {
	var msg = '';
	switch(input.attr('validate')) {
		case 'num':
			if(input.val() && /^\d+$/.test(input.val())) {
				notice('输入正确', input, 1);
				return true;
			}
			else {
				notice('请输入数字', input);
				return false;
			}
			break;
		case 'money':
			if(input.val() && /^(\d{1,3},?)+(\.\d+)?$/.test(input.val())) {
				notice('输入正确', input, 1);
				return true;
			}
			else {
				notice('请输入数字', input);
				return false;
			}
			break;
		case 'null':
			if(input.val()) {
				notice('输入正确', input, 1);
				return true;
			}
			else {
				notice('不能为空', input);
				return false;
			}
			break;
		case 'order_id':
			if(input.val() && input.val().length > 6) {
				notice('输入正确', input, 1);
				return true;
			}
			else {
				notice('请输入快递单号, 6位以上数字或字母', input);
				return false;
			}
			break;
		case 'email':
			if(input.val() && input.val().length > 6) {
				notice('输入正确', input, 1);
				return true;
			}
			else {
				notice('请输入邮箱地址', input);
				return false;
			}
			break;
		case 'mobile':
			if(input.val() && /^1\d{10}$/.test(input.val())) {
				notice('输入正确', input, 1);
				return true;
			}
			else {
				notice('请输入手机号码', input);
				return false;
			}
			break;
		case 'phone':
			if(input.val() && /^(\d{2,4})?-?\d{7,8}$/.test(input.val())) {
				notice('输入正确', input, 1);
				return true;
			}
			else {
				notice('请输入电话号码', input);
				return false;
			}
			break;
		case 'zip':
			if(input.val() && /^\d{6}$/.test(input.val())) {
				notice('输入正确', input, 1);
				return true;
			}
			else {
				notice('请输入邮政编码, 6位数字', input);
				return false;
			}
			break;
		case 'name':
		case 'realname':
			if(input.val() && input.val().length >= 2) {
				notice('输入正确', input, 1);
				return true;
			}
			else {
				notice('请输入姓名', input);
				return false;
			}
			break;
		case 'account':
			if(input.val() && input.val().length >= 2) {
				//checkAccount(input);
				//notice('输入正确', input, 1);
				checkAccount(input);
				return true;
			}
			else {
				notice('请输入', input);
				return false;
			}
			break;
		case 'check_mobile':
			if(input.val() && /^1\d{10}$/.test(input.val())) {
				checkMobile(input);
				return true;
			}
			else {
				notice('请输入手机号码', input);
				return false;
			}
			break;
		case 'password':
			if(input.val() && input.val().length >= 6) {
				notice('输入正确', input, 1);
				return true;
			}
			else {
				notice('请输入6位以上密码', input);
				return false;
			}
			break;
		default:
			//default
			return true;
	}
}

function notice(msg, input, status) {
	if(! input.siblings().is('.notice')) {
		input.parent().append('<span class="notice" style="margin-left:10px;"></span>');
	}
	if(input.attr('valigroup')) {
		var groupName = input.attr('valigroup');
		var count = $('input[valigroup='+groupName+']').length;
		//msg += '。 '+ input.attr('valigroup') + count + '个必填一个';
	}
	if(input.attr('valiconfirm')) {
		var confirm = input.attr('valiconfirm');
		var count = $('input[valiconfirm='+confirm+']').length;
		msg += '。 '+ input.attr('valiconfirm') + count + '个要填写一致';
	}
	if(status == 1) {
		input.siblings('.notice').text(msg).css('color','green');
	} else if(status == 2){
		input.siblings('.notice').text(msg).css('color','#09F');
	} else {
		input.siblings('.notice').text(msg).css('color','red');
	}
}

function submitCallback(){
	var r = true;
	var g = false;
	var ig = false;
	$("input, select").each(function(){
		if($(this).attr('valigroup')) {
			//组合验证
			ig = true;
			if(validate($(this))) {
				g = true;
			}
		} else if($(this).attr('valiconfirm')) {
			//相同检查
			r = valiconfirm($(this).attr('valiconfirm'));
		} else if($(this).attr('valionly')) {
			//仅验证
		} else {
			if(! validate($(this))) {
				r = false;
			}
		}
	});
	return r && (! ig || (ig && g));
}

function valiconfirm(name) {
	var value = '';
	var r = false;
	var i = 0;
	$('input[valiconfirm='+name+']').each(function() {
		if(validate($(this))) {
			if(i == 0) {
				value = $(this).val();
				++i;
			} else {
				r = (value == $(this).val());
			}
		}
	});
	return r;
}

function checkAccount(input)
{
   	if( ! input.siblings().is('.notice')) {
		input.parent().append('<span class="notice" style="margin-left:10px;"></span>');
	}
	var spanNotice = input.siblings('.notice');
    var account = input.val();
    if (null==account || ''==account) {
    	spanNotice.text('不能为空').css('color','red');
				return false;
    };
    $.ajax(
    {
	url:'/passport/user/checkAccount/',
	dataType:'json',
	data:'account='+encodeURIComponent(account),
	success:function(result)
		{
			if (result==true) {
				spanNotice.text('帐号可用').css('color','green');
				return true;
			} else {
				spanNotice.text('帐号已被注册').css('color','red');
				return false;				
			}
		}
	});
}

function checkMobile(input)
{
   	if( ! input.siblings().is('.notice')) {
		input.parent().append('<span class="notice" style="margin-left:10px;"></span>');
	}
	var spanNotice = input.siblings('.notice');
    var account = input.val();
    if (null==account || ''==account) {
    	spanNotice.text('不能为空').css('color','red');
				return false;
    };
    $.ajax(
    {
	url:'/passport/user/checkMobile/',
	dataType:'json',
	data:'mobile='+encodeURIComponent(account),
	success:function(result)
		{
			if (result==true) {
				spanNotice.text('手机可用').css('color','green');
				return true;
			} else {
				spanNotice.text('手机已被注册').css('color','red');
				return false;				
			}
		}
	});
}