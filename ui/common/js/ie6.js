$(function(){
	var html = "";
	html+='<div class="notify-bar fixed">';
	html+='	<span>';
	html+='		你正在使用低级浏览器,为了获取更好的用户体验,点击右侧浏览器图标升级';
	html+='	</span>';
	html+='	<a href="http://download.500mi.com/Firefox_14.0.1.exe">';
	html+='		<img src="/ui/common/images/ff-25.png" />';
	html+='	</a>';
	html+='	<a href="http://download.500mi.com/IE8-WindowsXP-x86-CHS.exe">';
	html+='		<img src="/ui/common/images/ie8-25.png" />';
	html+='	</a>			';
	html+='	<a href="http://download.500mi.com/chrome_dev_22.0.1207.1.exe">';
	html+='		<img src="/ui/common/images/chrome-25.png" />';
	html+='	</a>';
	html+='	<a href="http://download.500mi.com/360cse_5.5.0.632.exe">';
	html+='		<img src="/ui/common/images/360chrome-25.png" />';
	html+='	</a>';
	html+='</div>';
	$("body").append(html);
	$("#all_wrap").css({"margin-top":"30px"})
})
function correctPNG() // correctly handle PNG transparency in Win IE 5.5 & 6.
{
	var arVersion = navigator.appVersion.split("MSIE")
	var version = parseFloat(arVersion[1])
	if ((version >= 5.5) && (document.body.filters)){
		for(var j=0; j<document.images.length; j++){
			var img = document.images[j]
			var imgName = img.src.toUpperCase()
			if (imgName.substring(imgName.length-3, imgName.length) == "PNG"){
				var imgID = (img.id) ? "id='" + img.id + "' " : ""
				var imgClass = (img.className) ? "class='" + img.className + "' " : ""
				var imgTitle = (img.title) ? "title='" + img.title + "' " : "title='" + img.alt + "' "
				var imgStyle = "display:inline-block;" + img.style.cssText 
				if (img.align == "left") imgStyle = "float:left;" + imgStyle
				if (img.align == "right") imgStyle = "float:right;" + imgStyle
				if (img.parentElement.href) imgStyle = "cursor:hand;" + imgStyle
				var strNewHTML = "<span " + imgID + imgClass + imgTitle
				+ " style=\"" + "width:" + img.width + "px; height:" + img.height + "px;" + imgStyle + ";"
				+ "filter:progid:DXImageTransform.Microsoft.AlphaImageLoader"
				+ "(src=\'" + img.src + "\', sizingMethod='scale');\"></span>" 
				img.outerHTML = strNewHTML
				j = j-1
			}
		}
	}    
}
window.attachEvent("onload", correctPNG);