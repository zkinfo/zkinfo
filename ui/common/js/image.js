var Image = {
	init: function (config) {
		Image.layout();
		$(".close").live("click",function(){
			Image.del($(this));
		})
		var _options = {
			limit 	: "5MB",
			desc 	: "请上传图片",
			exts 	: "*.gif; *.jpg; *.png;*.jpeg",
			multi 	: "true",
			height 	: "20",
			width 	: "80",
			text 	: "浏览",
			imgurl 	: "http://res.500mi.com/",
			path 	: "tmp",
			filename: ""
		};
		for (var i in config) {
			_options[i] = config[i];
		}
		var json_data = {
			path 	: _options.path,
			filename: _options.filename
		}
		$("#file_upload").uploadify({
			// 'debug'		: true,
			'auto'			: true,
			'fileSizeLimit'	: _options.limit,
			'fileTypeDesc'	: _options.desc,
			'fileTypeExts'	: _options.exts,
			'method'		: 'get',
			'multi'			: _options.multi,
			'height'		: _options.height,   
			'width'			: _options.width,    
			'buttonText'	: _options.text,
			'swf'			: '/ui/common/upload/uploadify.swf',
			'uploader'		: '/api/image/upload',
			'cancelImg'		: '/ui/common/images/uploadify-cancel.png',
			'removeCompleted':true,
			'formData'		: json_data,
			'onUploadStart'	: function(file) {
				$("#file_upload").uploadify("settings","formData", json_data);
			},
			'onUploadSuccess'	: function(file,data,response) {
				var data = JSON.parse(data);
				var data = JSON.parse(data.Filedata);
				console.log(data);
				var origin = $('.pic').val().split(',');
				origin.push(data.filename);
				$(".pic").val(origin.join(','));
				var html = 	[
					'<li>',
					'	<img data-file="'+data.filename+'" src="'+data.img_server_url+'!100x100">',
					'	<span class="close">x</span>',							
					'</li>'
				].join('\n');
				$(".pic_list > ul").append(html);
			}
		});
	},
	del: function(_self){
		_self.parent("li").remove();
		var tmp = [];
		$(".pic_list ul li img").each(function(){
			var file = $(this).data("file");
			tmp.push(file);
		})
		$(".pic").val(tmp.join(','));
		console.log($(".pic").val());
	},
	layout: function(){
		var pic_list = $(".pic").val().split(',');
		var spot_code = $(".pic").data("spot_code");
		var tmp = '';
		for(i in pic_list){
			var html = 	[
				'<li>',
				'	<img data-file="'+pic_list[i]+'" src='+MIAdmin._Domains.resUrl+'"/data/shop/'+pic_list[i]+'!100x100">',
				'	<span class="close">x</span>',							
				'</li>'
			].join('\n');
			tmp+=html;
		}
		$(".pic_list > ul").append(tmp);
	}
}