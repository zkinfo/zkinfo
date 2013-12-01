//弹出窗口
var JqueryDialog = {
	
	//配置项
	//模态窗口背景色
	"cBackgroundColor"			:	"#F7F7F7",	
	//阴影距离(右)
	"cShdowRight"				:	5,
	//阴影距离(下)
	"cShdowDown"				:	5,	
	//边框尺寸(像素)
	"cBorderSize"				:	8,
	//边框颜色
	"cBorderColor"				:	"#CCCCCC",
	//Header背景色
	"cHeaderBackgroundColor"	:	"#F7F7F7",
	//右上角关闭显示文本
	"cCloseText"				:	"关闭",
	//鼠标移上去时的提示文字
	"cCloseTitle"				:	"关闭",	
	//拖拽效果
	"cDragTime"					:	"100",
	OpenAjax:function(iTitle,iframeSrc,isAjax){
		JqueryDialog.initAjax(iTitle,iframeSrc, 600, 200, isAjax, true);
	},
	initAjax:function(iTitle, iframeSrc, iframeWidth, iframeHeight,isAjax, isDrag){
		
		//常量
		//对话框阴影距离
		var _px_shadow = 5;
		//顶部高
		var _px_top = 30;
		//底部高
		var _px_bottom = 0;
		
		
		//获取客户端页面宽高
		var _client_width = document.body.clientWidth;
		var _client_height = document.documentElement.scrollHeight;

		//页面遮罩层
		if(typeof($("#jd_shadow")[0]) == "undefined"){
			//前置
			$("body").prepend("<div id='jd_shadow'>&nbsp;</div>");
			var _jd_shadow = $("#jd_shadow");
			_jd_shadow.css("width", _client_width + "px");
			_jd_shadow.css("height", _client_height + "px");
			_jd_shadow.hide();
		}
	
		//对话框主容器
		if(typeof($("#jd_dialog")[0]) != "undefined"){
			$("#jd_dialog").remove();
		}
		$("body").prepend("<div id='jd_dialog'></div>");
		$("select").hide();
		//dialog location
		//left 边框*2 阴影5
		//top 边框*2 阴影5 header30 bottom50
		
		var _jd_dialog = $("#jd_dialog");
		_jd_dialog.hide();
		var _left = (_client_width - (iframeWidth + JqueryDialog.cBorderSize * 2 + JqueryDialog.cShdowRight)) / 2;
		_jd_dialog.css("left", (_left < 0 ? 0 : _left) + document.documentElement.scrollLeft + "px");
		
		var _top = (document.documentElement.clientHeight - (iframeHeight + JqueryDialog.cBorderSize * 2 + _px_top + _px_bottom + JqueryDialog.cShdowDown)) / 2;
		_jd_dialog.css("top", (_top < 0 ? 0 : _top) + document.documentElement.scrollTop + "px");

		//对话框阴影
		

		//create dialog main
		_jd_dialog.append("<div id='jd_dialog_m' class='popupmenu_centerbox'></div>");
		var _jd_dialog_m = $("#jd_dialog_m");

		_jd_dialog_m.css("width", iframeWidth + "px").hide();

		_jd_dialog_m.append("<a class='float_del' title='关闭' href='javascript: JqueryDialog.Close();'>关闭</a>");
		_jd_dialog_m.append("<h1 id='jd_dialog_m_h'>"+iTitle+"</h1>");
		var _jd_dialog_m_h = $("#jd_dialog_m_h");

		if(iframeSrc.indexOf('?') != -1) {
			iframeSrc = iframeSrc + '&inajax=1';
		} else {
			iframeSrc = iframeSrc + '?inajax=1';
		}	
		if(isAjax==1){			
			$.ajax({
				url:iframeSrc,
				type:'POST',
				data:'',
				dataType:($.browser.msie) ? "text" : "xml",
				timeout:10000,
				error:function(){JqueryDialog.Close();alert('系统超时，请稍后再试！');},
				success:function(result){
					var xml;   
                    if (typeof result == "string") {   
                        xml = new ActiveXObject("Microsoft.XMLDOM");   
                        xml.async = false;   
                        xml.loadXML(result);   
                    } else {   
                        xml = result;   
                    }
					_jd_dialog_m.append($(xml).find("root").text());
					_jd_shadow.show();
					_jd_dialog.show();
					_jd_dialog_m.show();
				}
			});
		}else{
			_jd_dialog_m.append(iframeSrc);
			_jd_shadow.show();
			_jd_dialog.show();
			_jd_dialog_m.show();
		}
		if(isDrag){
			DragAndDrop.Register(_jd_dialog[0], _jd_dialog_m_h[0]);
		}
	},
	
	
	
	/// <summary>关闭模态窗口</summary>
	Close:function(){
		$("#jd_shadow").remove();
		$("#jd_dialog").remove();
		$("select").show();
	}
};

var DragAndDrop = function(){
	
	//客户端当前屏幕尺寸(忽略滚动条)
	var _clientWidth;
	var _clientHeight;
		
	//拖拽控制区
	var _controlObj;
	//拖拽对象
	var _dragObj;
	//拖动状态
	var _flag = false;
	
	//拖拽对象的当前位置
	var _dragObjCurrentLocation;
	
	//鼠标最后位置
	var _mouseLastLocation;
	
	//使用异步的Javascript使拖拽效果更为流畅
	//var _timer;
	
	//定时移动，由_timer定时调用
	//var intervalMove = function(){
	//	$(_dragObj).css("left", _dragObjCurrentLocation.x + "px");
	//	$(_dragObj).css("top", _dragObjCurrentLocation.y + "px");
	//};
	
	var getElementDocument = function(element){
		return element.ownerDocument || element.document;
	};
	
	//鼠标按下
	var dragMouseDownHandler = function(evt){

		if(_dragObj){
			
			evt = evt || window.event;
			
			//获取客户端屏幕尺寸
			_clientWidth = document.body.clientWidth;
			_clientHeight = document.documentElement.scrollHeight;
			
			//iframe遮罩
			//$("#jd_dialog_m_h").css("display", "");
						
			//标记
			_flag = true;
			
			//拖拽对象位置初始化
			_dragObjCurrentLocation = {
				x : $(_dragObj).offset().left,
				y : $(_dragObj).offset().top
			};
	
			//鼠标最后位置初始化
			_mouseLastLocation = {
				x : evt.screenX,
				y : evt.screenY
			};
			
			//注：mousemove与mouseup下件均针对document注册，以解决鼠标离开_controlObj时事件丢失问题
			//注册事件(鼠标移动)			
			$(document).bind("mousemove", dragMouseMoveHandler);
			//注册事件(鼠标松开)
			$(document).bind("mouseup", dragMouseUpHandler);
			
			//取消事件的默认动作
			if(evt.preventDefault)
				evt.preventDefault();
			else
				evt.returnValue = false;
			
			//开启异步移动
			//_timer = setInterval(intervalMove, 10);
		}
	};
	
	//鼠标移动
	var dragMouseMoveHandler = function(evt){
		if(_flag){

			evt = evt || window.event;
			
			//当前鼠标的x,y座标
			var _mouseCurrentLocation = {
				x : evt.screenX,
				y : evt.screenY
			};
			
			//拖拽对象座标更新(变量)
			_dragObjCurrentLocation.x = _dragObjCurrentLocation.x + (_mouseCurrentLocation.x - _mouseLastLocation.x);
			_dragObjCurrentLocation.y = _dragObjCurrentLocation.y + (_mouseCurrentLocation.y - _mouseLastLocation.y);
			
			//将鼠标最后位置赋值为当前位置
			_mouseLastLocation = _mouseCurrentLocation;
			
			//拖拽对象座标更新(位置)
			$(_dragObj).css("left", _dragObjCurrentLocation.x + "px");
			$(_dragObj).css("top", _dragObjCurrentLocation.y + "px");
			
			//取消事件的默认动作
			if(evt.preventDefault)
				evt.preventDefault();
			else
				evt.returnValue = false;
		}
	};
	
	//鼠标松开
	var dragMouseUpHandler = function(evt){
		if(_flag){
			evt = evt || window.event;
			
			//取消iframe遮罩
			//$("#jd_dialog_m_h").css("display", "none");
			
			//注销鼠标事件(mousemove mouseup)
			cleanMouseHandlers();
			
			//标记
			_flag = false;
			
			//清除异步移动
			//if(_timer){
			//	clearInterval(_timer);
			//	_timer = null;
			//}
		}
	};
	
	//注销鼠标事件(mousemove mouseup)
	var cleanMouseHandlers = function(){
		if(_controlObj){
			$(_controlObj.document).unbind("mousemove");
			$(_controlObj.document).unbind("mouseup");
		}
	};
	
	return {
		//注册拖拽(参数为dom对象)
		Register : function(dragObj, controlObj){
			//赋值
			_dragObj = dragObj;
			_controlObj = controlObj;
			//注册事件(鼠标按下)
			$(_controlObj).bind("mousedown", dragMouseDownHandler);			
		}
	}

}();