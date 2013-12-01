/*
	ECBOSS GROUP(C) 2010-2011 Author:JETLEE
*/

var userAgent = navigator.userAgent.toLowerCase();
var is_opera = userAgent.indexOf('opera') != -1 && opera.version();
var is_moz = (navigator.product == 'Gecko') && userAgent.substr(userAgent.indexOf('firefox') + 8, 3);
var is_ie = (userAgent.indexOf('msie') != -1 && !is_opera) && userAgent.substr(userAgent.indexOf('msie') + 5, 3);
var is_safari = (userAgent.indexOf('webkit') != -1 || userAgent.indexOf('safari') != -1);
var note_step = 0;
var note_oldtitle = document.title;
var note_timer;

//iframe包含
if (top.location != location) {
	top.location.href = location.href;
}

function _fe(id) {
	return document.getElementById(id);
}

function addSort(obj) {
	if (obj.value == 'addoption') {
 	var newOptDiv = document.createElement('div')
 	newOptDiv.id = obj.id+'_menu';
 	newOptDiv.innerHTML = '<h1>添加</h1><a href="javascript:;" onclick="addOption(\'newsort\', \''+obj.id+'\')" class="float_del">删除</a><div class="popupmenu_inner" style="text-align: center;">名称：<input type="text" name="newsort" size="10" id="newsort" class="t_input" /><input type="button" name="addSubmit" value="创建" onclick="addOption(\'newsort\', \''+obj.id+'\')" class="button" /></div>';
 	newOptDiv.className = 'popupmenu_centerbox';
 	newOptDiv.style.cssText = 'position: absolute; left: 50%; top: 200px; width: 400px; margin-left: -200px;';
 	document.body.appendChild(newOptDiv);
 	_fe('newsort').focus();
 	}
}

function addOption(sid, aid) {
	var obj = _fe(aid);
	var newOption = _fe(sid).value;
	_fe(sid).value = "";
	if (newOption!=null && newOption!='') {
		var newOptionTag=document.createElement('option');
		newOptionTag.text=newOption;
		newOptionTag.value="new:" + newOption;
		try {
			obj.add(newOptionTag, obj.options[0]); // doesn't work in IE
		} catch(ex) {
			obj.add(newOptionTag, obj.selecedIndex); // IE only
		}
		obj.value="new:" + newOption;
	} else {
		obj.value=obj.options[0].value;
	}
	// Remove newOptDiv
	var newOptDiv = document.getElementById(aid+'_menu');
	var parent = newOptDiv.parentNode;
	var removedChild = parent.removeChild(newOptDiv);
}

function checkAll(form, name) {
	for(var i = 0; i < form.elements.length; i++) {
		var e = form.elements[i];
		if(e.name.match(name)) {
			e.checked = form.elements['chkall'].checked;
		}
	}
}

function cnCode(str) {
	return is_ie && document.charset == 'utf-8' ? encodeURIComponent(str) : str;
}

function isUndefined(variable) {
	return typeof variable == 'undefined' ? true : false;
}

function in_array(needle, haystack) {
	var ret = false;
	if(typeof needle == 'string' || typeof needle == 'number') {
		for(var i in haystack) {
			if(haystack[i] == needle) {
				ret = true;
				break;
			}
		}
	}
	return ret;
}

function strlen(str) {
	return (is_ie && str.indexOf('\n') != -1) ? str.replace(/\r?\n/g, '_').length : str.length;
}

function getExt(path) {
	return path.lastIndexOf('.') == -1 ? '' : path.substr(path.lastIndexOf('.') + 1, path.length).toLowerCase();
}

function doane(event) {
	e = event ? event : window.event;
	if(is_ie) {
		e.returnValue = false;
		e.cancelBubble = true;
	} else if(e) {
		e.stopPropagation();
		e.preventDefault();
	}
}

//Ctrl+Enter 发布
function ctrlEnter(event, btnId, onlyEnter) {
	if(isUndefined(onlyEnter)) onlyEnter = 0;
	if((event.ctrlKey || onlyEnter) && event.keyCode == 13) {
		_fe(btnId).click();
		return false;
	}
	return true;
}
//缩放Textarea
function zoomTextarea(id, zoom) {
	zoomSize = zoom ? 10 : -10;
	obj = _fe(id);
	if(obj.rows + zoomSize > 0 && obj.cols + zoomSize * 3 > 0) {
		obj.rows += zoomSize;
		obj.cols += zoomSize * 3;
	}
}

//复制URL地址
function setCopy(_sTxt){
	if(is_ie) {
		clipboardData.setData('Text',_sTxt);
		alert ("网址“"+_sTxt+"”\n已经复制到您的剪贴板中\n您可以使用Ctrl+V快捷键粘贴到需要的地方");
	} else {
		prompt("请复制网站地址:",_sTxt);
	}
}

//验证是否有选择记录
function ischeck(id, prefix) {
	form = document.getElementById(id);
	for(var i = 0; i < form.elements.length; i++) {
		var e = form.elements[i];
		if(e.name.match(prefix) && e.checked) {
			if(confirm("您确定要执行本操作吗？")) {
				return true;
			} else {
				return false;
			}
		}
	}
	alert('请选择要操作的对象');
	return false;
}
function showPreview(val, id) {
	var showObj = _fe(id);
	if(typeof showObj == 'object') {
		showObj.innerHTML = val.replace(/\n/ig, "<br />");
	}
}

function getEvent() {
	if (document.all) return window.event;
	func = getEvent.caller;
	while (func != null) {
		var arg0 = func.arguments[0];
		if (arg0) {
			if((arg0.constructor==Event || arg0.constructor ==MouseEvent) || (typeof(arg0)=="object" && arg0.preventDefault && arg0.stopPropagation)) {
				return arg0;
			}
		}
		func=func.caller;
	}
	return null;
}

function copyRow(tbody) {
	var add = false;
	var newnode;
	if(_fe(tbody).rows.length == 1 && _fe(tbody).rows[0].style.display == 'none') {
		_fe(tbody).rows[0].style.display = '';
		newnode = _fe(tbody).rows[0];
	} else {
		newnode = _fe(tbody).rows[0].cloneNode(true);
		add = true;
	}
	tags = newnode.getElementsByTagName('input');
	for(i in tags) {
		if(tags[i].name == 'pics[]') {
			tags[i].value = 'http://';
		}
	}
	if(add) {
		_fe(tbody).appendChild(newnode);
	}
}

function delRow(obj, tbody) {
	if(_fe(tbody).rows.length == 1) {
		var trobj = obj.parentNode.parentNode;
		tags = trobj.getElementsByTagName('input');
		for(i in tags) {
			if(tags[i].name == 'pics[]') {
				tags[i].value = 'http://';
			}
		}
		trobj.style.display='none';
	} else {
		_fe(tbody).removeChild(obj.parentNode.parentNode);
	}
}

function insertWebImg(obj) {
	if(checkImage(obj.value)) {
		insertImage(obj.value);
		obj.value = 'http://';
	} else {
		alert('图片地址不正确');
	}
}

function checkFocus(target) {
	var obj = _fe(target);
	if(!obj.hasfocus) {
		obj.focus();
	}
}
function insertImage(text) {
	text = "\n[img]" + text + "[/img]\n";
	insertContent('message', text)
}

function insertContent(target, text) {
	var obj = _fe(target);
	selection = document.selection;
	checkFocus(target);
	if(!isUndefined(obj.selectionStart)) {
		var opn = obj.selectionStart + 0;
		obj.value = obj.value.substr(0, obj.selectionStart) + text + obj.value.substr(obj.selectionEnd);
	} else if(selection && selection.createRange) {
		var sel = selection.createRange();
		sel.text = text;
		sel.moveStart('character', -strlen(text));
	} else {
		obj.value += text;
	}
}

function checkImage(url) {
	var re = /^http\:\/\/.{5,200}\.(jpg|gif|png)$/i
	return url.match(re);
}

function quick_validate(obj) {
    if(_fe('seccode')) {
		var code = _fe('seccode').value;
		var x = new Ajax();
		x.get('cp.php?ac=common&op=seccode&code=' + code, function(s){
			s = trim(s);
			if(s != 'succeed') {
				alert(s);
				_fe('seccode').focus();
           		return false;
			} else {
				obj.form.submit();
				return true;
			}
		});
    } else {
    	obj.form.submit();
    	return true;
    }
}

function trim(str) {
	var re = /\s*(\S[^\0]*\S)\s*/;
	re.exec(str);
	return RegExp.$1;
}


//滚动
function startMarquee(h, speed, delay, sid) {
	var t = null;
	var p = false;
	var o = _fe(sid);
	o.innerHTML += o.innerHTML;
	o.onmouseover = function() {p = true}
	o.onmouseout = function() {p = false}
	o.scrollTop = 0;
	function start() {
	    t = setInterval(scrolling, speed);
	    if(!p) {
			o.scrollTop += 2;
		}
	}
	function scrolling() {
	    if(p) return;
		if(o.scrollTop % h != 0) {
	        o.scrollTop += 2;
	        if(o.scrollTop >= o.scrollHeight/2) o.scrollTop = 0;
	    } else {
	        clearInterval(t);
	        setTimeout(start, delay);
	    }
	}
	setTimeout(start, delay);
}

function readfeed(obj, id) {
	if(Cookie.get("read_feed_ids")) {
		var fcookie = Cookie.get("read_feed_ids");
		fcookie = id + ',' + fcookie;
	} else {
		var fcookie = id;
	}
	Cookie.set("read_feed_ids", fcookie, 48);
	obj.className = 'feedread';
}

function showreward() {
	if(Cookie.get('reward_notice_disable')) {
		return false;
	}
	var x = new Ajax();
	x.get('do.php?ac=ajax&op=getreward', function(s){
		if(s) {
			msgwin(s, 2000);
		}
	});
}

function msgwin(s, t) {

	var msgWinObj = _fe('msgwin');
	if(!msgWinObj) {
		var msgWinObj = document.createElement("div");
		msgWinObj.id = 'msgwin';
		msgWinObj.style.display = 'none';
		msgWinObj.style.position = 'absolute';
		msgWinObj.style.zIndex = '100000';
		_fe('append_parent').appendChild(msgWinObj);
	}
	msgWinObj.innerHTML = s;
	msgWinObj.style.display = '';
	msgWinObj.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=0)';
	msgWinObj.style.opacity = 0;
	var sTop = document.documentElement && document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop;
	pbegin = sTop + (document.documentElement.clientHeight / 2);
	pend = sTop + (document.documentElement.clientHeight / 5);
	setTimeout(function () {showmsgwin(pbegin, pend, 0, t)}, 10);
	msgWinObj.style.left = ((document.documentElement.clientWidth - msgWinObj.clientWidth) / 2) + 'px';
	msgWinObj.style.top = pbegin + 'px';
}

function showmsgwin(b, e, a, t) {
	step = (b - e) / 10;
	var msgWinObj = _fe('msgwin');
	newp = (parseInt(msgWinObj.style.top) - step);
	if(newp > e) {
		msgWinObj.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + a + ')';
		msgWinObj.style.opacity = a / 100;
		msgWinObj.style.top = newp + 'px';
		setTimeout(function () {showmsgwin(b, e, a += 10, t)}, 10);
	} else {
		msgWinObj.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=100)';
		msgWinObj.style.opacity = 1;
		setTimeout('displayOpacity(\'msgwin\', 100)', t);
	}
}

function displayOpacity(id, n) {
	if(!_fe(id)) {
		return;
	}
	if(n >= 0) {
		n -= 10;
		_fe(id).style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + n + ')';
		_fe(id).style.opacity = n / 100;
		setTimeout('displayOpacity(\'' + id + '\',' + n + ')', 50);
	} else {
		_fe(id).style.display = 'none';
		_fe(id).style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=100)';
		_fe(id).style.opacity = 1;
	}
}

function display(id) {
	var obj = _fe(id);
	obj.style.display = obj.style.display == '' ? 'none' : '';
}

function urlto(url) {
	window.location.href = url;
}

function explode(sep, string) {
	return string.split(sep);
}

function selector(pattern, context) {
	var re = new RegExp('([a-z]*)([\.#:]*)(.*|$)', 'ig');
	var match = re.exec(pattern);
	var conditions = [];
	if (match[2] == '#')	conditions.push(['id', match[3]]);
	else if(match[2] == '.')	conditions.push(['className', match[3]]);
	else if(match[2] == ':')	conditions.push(['type', match[3]]);
	var s = match[3].replace(/\[(.*)\]/g,'$1').split('@');
	for(var i=0; i<s.length; i++) {
		var cc = null;
		if (cc = /([\w]+)([=^%!$~]+)(.*)$/.exec(s[i])){
			conditions.push([cc[1], cc[2], cc[3]]);
		}
	}
	var list = (context || document).getElementsByTagName(match[1] || "*");
	if(conditions) {
		var elements = [];
		var attrMapping = {'for': 'htmlFor', 'class': 'className'};
		for(var i=0; i<list.length; i++) {
			var pass = true;
			for(var j=0; j<conditions.length; j++) {
				var attr = attrMapping[conditions[j][0]] || conditions[j][0];
				var val = list[i][attr] || (list[i].getAttribute ? list[i].getAttribute(attr) : '');
				var pattern = null;
				if(conditions[j][1] == '=') {
					pattern = new RegExp('^'+conditions[j][2]+'$', 'i');
				} else if(conditions[j][1] == '^=') {
					pattern = new RegExp('^' + conditions[j][2], 'i');
				} else if(conditions[j][1] == '$=') {
					pattern = new RegExp(conditions[j][2] + '$', 'i');
				} else if(conditions[j][1] == '%=') {
					pattern = new RegExp(conditions[j][2], 'i');
				} else if(conditions[j][1] == '~=') {
					pattern = new RegExp('(^|[ ])' + conditions[j][2] + '([ ]|$)', 'i');
				}
				if(pattern && !pattern.test(val)) {
					pass = false;
					break;
				}
			}
			if(pass) elements.push(list[i]);
		}
		return elements;
	} else {
		return list;
	}
}
