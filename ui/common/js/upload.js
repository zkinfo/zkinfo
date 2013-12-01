$(function() {
	/*
	 * 上传图片
	 */
	var btnUpload = $('#upload_pic');
	var status = $('#pic_status');
	var tmp = btnUpload.attr("class");
	var tmp = tmp.split(" ");
	var partner_id = tmp[0];
	var url = status.attr("class");
	var pic_input = $('#ptcon_pic');
	if(btnUpload.length > 0) {
		new AjaxUpload(btnUpload, {
			action : url+'/boss/market/ptcon/uploadFile/?partnerid='+partner_id,
			name : 'uploadfile',
			onSubmit : function(file, ext) {
				if(!(ext && /^(jpg|png|jpeg|gif)$/.test(ext))) {
					status.text('只能是.jpg,.png,.gif后缀结尾的图片!');
					return false;
				}
				status.text('正在上传...');
			},
			onComplete : function(file, response) {
				status.text('');
				if(response.split("?")[1]) {
					$('#pic_show').after('<p class="pic_del" id="'+response.split("?")[1]+'"><img src="' + url + '/data/contract/' + partner_id + '/' + response.split("?")[1] + '" />删除</p>');
					if(pic_input.val()==""){
						pic_input.val(response.split("?")[1])
					}else{
						pic_input.val(pic_input.val()+","+response.split("?")[1])
					}
					status.text('上传成功');
				} else {
					status.text('上传失败');
				}
			}
		});
	}
	$(".pic_del").live("click",function(){
		var id = $(this).attr("id");
		var aim = ","+id;
		$(this).remove();
		$.get(url+"/boss/market/ptcon/picDel/", {
			id 		   : id,
			partner_id : partner_id
		}, function(data) {
			if(data=="1"){
				status.text('删除成功');
				console.log(pic_input.val());
				if(pic_input.val().indexOf(aim)>0){
					str = pic_input.val().replace(aim, "");
				}else{
					str = pic_input.val().replace(id, "");
				}
				console.log(str);
				pic_input.val(str);
			}else{
				status.text('删除失败');
			}
		})
	})
})