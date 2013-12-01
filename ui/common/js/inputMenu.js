/***************************************************************
 *  Name: Javascript 主文件
 *  Version : 0.0.1
 *  Create: danchex@gmail.com
 *  Date: 2012-2-16

 *  Depends(依赖) : jquery

 *  Modify: yourname@your.com
 *  Date: Y-m-d H:i:s
 *  Log: What is changed ?
****************************************************************/
$(document).ready(function(){
	//CheckBox 下拉菜单
	$('.icon li.sup').click(function(){
		$(this).children('ul').slideToggle(500);
	});

	/*
	$('.xinYezhu a').click(function(){
		//$('.contentArea .listTable').html('登记入口');
	});
	*/

	//$('.NavArea li ul li').click(function(){
		//$(this).chidren('ul').show();
	//});
});
