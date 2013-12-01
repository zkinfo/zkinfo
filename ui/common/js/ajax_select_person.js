/***************************************************************
 *  Name: Jquery ajax 选人插件
 *  Version : 0.0.1
 *  Create: danchex@gmail.com
 *  Date: 2012-2-16

 *  Depends(依赖) : jquery

 *  Modify: yourname@your.com
 *  Date: Y-m-d H:i:s
 *  Log: What is changed ?
****************************************************************/
$(document).ready(function(){
	//选单元
	$('#building').change(function(){
		var build = $(this).val();
		$.ajax({
			url:'/work/tenement/home/findUnit/',
			type:'GET',
			data:'build='+build,
			dataType:'json',
			timeout:5000,
			error:function(){
				//$('#unit').val('').attr('disabled',1);
				//$('#room').val('').attr('disabled',1);
				$('#person').hide();
				$('#owner_room').val('');
				$('#owner_name').val('');
				$('#owner_mobile').val('');
			},
			success:function(result){
				if(result.length > 0) {
					//alert('获取成功'+result.length);
					var html = '<option value="">--选择--</option>';
					for(var i=0; i<result.length; i++) {
						html += '<option value="'+result[i].unit+'">'+result[i].unit+'</option>';
					}
					$('#unit').html(html);
					//$('#unit').attr('disabled',0==1);
					$('#room').val('');//attr('disabled',1);
					$('#person').hide();
					$('.noPerson').parent('tr').hide();
				} else {
					//$('#unit').attr('disabled',1);
					//$('#room').attr('disabled',1);
					$('#person').hide();
					$('#owner_room').val('');
					$('#owner_name').val('');
					$('#owner_mobile').val('');
				}
			}
		});
	});

	//选Room
	$('#unit').change(function(){
		var build = $('#building').val();
		var unit = $(this).val();
		$.ajax({
			url:'/work/tenement/home/findRoom/',
			type:'GET',
			data:'build='+build+'&unit='+unit,
			dataType:'json',
			timeout:5000,
			error:function(){
				//$('#room').attr('disabled',1);
				$('#person').hide();
				$('#owner_room').val('');
				$('#owner_name').val('');
				$('#owner_mobile').val('');
			},
			success:function(result){
				if(result.length > 0) {
					//alert('获取成功'+result.length);
					var html = '<option value="">--选择--</option>';
					for(var i=0; i<result.length; i++) {
						html += '<option value="'+result[i].room+'">'+result[i].room+'</option>';
					}
					$('#room').html(html);
					//$('#room').attr('disabled',0==1);
					$('#person').hide();
					$('.noPerson').parent('tr').hide();
				} else {
					//$('#room').val('').attr('disabled',1);
					$('#person').hide();
					$('#owner_room').val('');
					$('#owner_name').val('');
					$('#owner_mobile').val('');
				}
			}
		});
	});

	//选人
	$('#room').change(function(){
		var build = $('#building').val();
		var unit = $('#unit').val();
		var room = $('#room').val();
		if(!$('#room').val()) {
			$('#person').hide();
			$('.noPerson').parent('tr').hide();
			return;
		}
		$.ajax({
			url:'/work/tenement/home/findPerson/',
			type:'GET',
			data:'build='+build+'&unit='+unit+'&room='+room,
			dataType:'json',
			timeout:5000,
			error:function(){
				$('#person').hide();
			},
			success:function(result){
				if(result.length > 1) {
					//生成姓名选单
					var html = '<option value="">--选择--</option>';
					for(var i=0; i<result.length; i++) {
						html += '<option value="'+result[i].mobile+'----'+result[i].user_id+'">'+result[i].realname+'</option>';
					}
					$('#person').html(html).show();
					$('.noPerson').parent('tr').hide();
				} else if(result.length == 1) {
					$('#person').hide();
					$('#owner_room').val($('#building').val()
						+'楼栋'+$('#unit').val()+'单元'+$('#room').val()
						+'室');
					$('#user_id').val(result[0].user_id);
					$('#owner_name').val(result[0].realname);
					$('#owner_mobile').val(result[0].mobile);
					$('.noPerson').parent('tr').hide();
				} else {
					$('#person').hide();
					$('.noPerson').html('<div class="noPerson">没有找到'+$('#building').val()
						+'楼栋'+$('#unit').val()+'单元'+$('#room').val()
						+'室住户，请手工添加姓名和手机信息...</div>');
					$('.noPerson').parent('tr').show();
					$('#owner_room').val('');
					$('#owner_name').val('');
					$('#owner_mobile').val('');
				}
			}
		});
	});

	$('#person').change(function(){
		$('.noPerson').parent('tr').hide();
		$('#owner_room').val($('#building').val()
			+'楼栋'+$('#unit').val()+'单元'+$('#room').val()
			+'室');
		var tt = $(this).val();
		var ttarr = tt.split("----");
		$('#owner_name').val($(this).children(':selected').text());
		$('#user_id').val(ttarr[1]);
		$('#owner_mobile').val(ttarr[0]);
	});
});