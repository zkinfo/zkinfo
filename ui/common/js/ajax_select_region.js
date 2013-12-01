$('.selectRegion').live('click',function(){
	id = $(this).attr('id');
	type = $(this).attr('itype');
	code = $(this).attr('code');
	name = $(this).text();
	if(type == '3') {
		//alert(id+type+code);
		$('#district').val(name);
		//$('.area_code').hide();
		//$('.region').html('<span class="area_code">'+name+'邮政编号：'+code+'</span>');
		$('.region').html('<input type="hidden" class="area_code" name="area_code" value="'+code+'" />');
		$('#area_code, #zip').val(code);
		$('.region').hide();
		$('.region').parent().hide();
	} else {
		$.ajax({
			url:'/work/region/getRegion/' + id,
			type:'POST',
			data:'',
			dataType:'json',
			timeout:3000,
			error:function(){alert('系统超时，请稍后再试！');},
			success:function(result){
				//alert('获取成功'+result.length);
				var html = '<ul>';
				for(var i=0; i<result.length; i++) {
					html += '<li id="'+result[i].area_code+'" class="selectRegion" itype="'+result[i].area_type+'" code="'+result[i].area_code+'">'+result[i].area_name+'</li>';
				}
				html += '</ul>';
				$('.region').html(html).show();
			}
		});
		if(type == '1') {
			$('#province').val(name);
			$('#city').val('');
			$('#district').val('');
		} else if(type == '2') {
			$('#city').val(name);
			$('#district').val('');
		}
	}
});