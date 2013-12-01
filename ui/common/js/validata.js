function submitCallback(){
	try{
		$("input[validate=num]").each(valiNumCB);
		$("input[validate=money]").each(valiMoneyCB);
		$("input[validate=null]").each(valiNullCB);
		$("input[validate=email]").each(valiEmailCB);
		$("input[validate=mobile]").each(valiMobileCB);
		$("select[validate=null]").each(valiSelectNull);

	}catch(e){
		//alert(e.descrption);
		return false;
	}
	return true;
}


function valiSelectNull(_index,_ele){
	if(_ele.disabled) return;
	if($(_ele).val() == ""){
		alert(_ele.getAttribute('msgTitle') + "不能为空");
		_ele.focus();

		throw("");
	}
	
}

function valiNumCB(_index,_ele){
	if(_ele.disabled) return;
	
	var reg = new RegExp('[0-9]{1}');
	var _r = reg.test(_ele.value);
	if(_r){
		//验证没问题
		return;
	}
	alert(_ele.getAttribute('msgTitle') + "必须全部为数字");
	_ele.focus();
	throw("");
}

function valiMoneyCB(_index,_ele){
	if(_ele.disabled) return;
	
	var reg = new RegExp('^\d+(.\d{1,3})?$');
	var _r = reg.test(_ele.value);
	if(_r){
		//验证没问题
		return;
	}
	alert(_ele.getAttribute('msgTitle') + "请输入正确整数或者小数");
	_ele.focus();
	throw("");
}

function valiNullCB(_index,_ele){
	if(_ele.disabled) return;

	
	var _r;
	var reg = new RegExp("[ ]+");
	
	if(_ele.value == ""){
		_r = false;
	}else{
		if(reg.test(_ele.value))
		_r = false;
		else
			_r = true;
	}
	
	if(_r){
		//验证没问题
		return;
	}
	alert(_ele.getAttribute('msgTitle') + "不能为空");
	_ele.focus();
	throw("");
}


function valiEmailCB(_index,_ele){
	//
	if(_ele.disabled) return;

	var reg = new  RegExp('\\w+@\\w+\\.{1}\\w+');
	var _r = reg.test(_ele.value);
	if(_r){
		//验证没问题
		return;
	}
	alert(_ele.getAttribute('msgTitle') + "格式不正确。电子邮件正确格式样例：bobo@qq.com");
	_ele.focus();
	throw("");
	
	
}
function valiMobileCB(_index,_ele){
	//
	if(_ele.disabled) return;

	var reg = /^(13[0-9]|14[0-9]|15[0-9]|18[0-9])\d{8}$/;
	var _r = reg.test(_ele.value);
	if(_r){
		//验证没问题
		return;
	}
	alert(_ele.getAttribute('msgTitle') + "格式不正确。");
	_ele.focus();
	throw("");
	
	
}