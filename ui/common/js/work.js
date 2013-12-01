/***************************************************************
 *  Javascript 主文件
 *  @五百米·网络
 *  @www.500mi.com
 *  Depends(依赖) : jquery
****************************************************************/

$(document).ready(function(){
	//二级菜单弹出回缩
	$(".iconNav").toggle(function(){
		$('.iconSub').show();
	},function(){
		$('.iconSub').hide();
	});

	//快递下拉菜单 动态填入文本框
	$('#select_express').change(function(){
		var express = $(this).val();
		$('#express').val(express);
	});

	//快递删除
	$('.delivDel').click(function(){
		if(confirm('确定删除?')) {
			var id = $(this).parents('tr').attr('id');
			$.ajax({
				url:'http://www.500mi.com/work/delivery/delivDel/'+id,
				type:'POST',
				data:'',
				dataType:'json',
				timeout:5000,
				error:function(){alert('系统超时，请稍后再试！');},
				success:function(result){
					if(result == 1) {
						$('#'+id).remove();
					} else if(result == '2'){
						alert('无此权限！');
					} else {
						alert('操作失败');
					}
				}
			});
		}
	});

	//房号下拉菜单 动态填入文本框
	$('.select_room').change(function(){
		var build = $('#building').val();
		var unit = $('#unit').val();
		var room = $('#room').val();
		build = build == '楼号' ? '' : build;
		unit = unit == '单元' ? '' : unit;
		room = room == '房号' ? '' : room;
		$('#owner_room').val(build+' '+unit+' '+room);
		$('#owner_name').val('姓名');
		$('#owner_mobile').val('18000000000');
	});

	//费用删除
	$('.feeDel').click(function(){
		if(confirm('确定删除?')) {
			var id = $(this).parents('tr').attr('id');
			$.ajax({
				url:'http://www.500mi.com/work/tenement/fee/feeDel/'+id,
				type:'POST',
				data:'',
				dataType:'json',
				timeout:5000,
				error:function(){alert('无此权限！');},
				success:function(result){
					if(result == 1) {
						$('#'+id).remove();
					} else {
						alert('无此权限！');
					}
				}
			});
		}
	});

	//选单元
	$('#building').change(function(){
		var build = $(this).val();
		$.ajax({
			url:'http://www.500mi.com/work/tenement/home/findUnit/',
			type:'POST',
			data:'build='+build,
			dataType:'json',
			timeout:5000,
			error:function(){
				$('#unit').val('').attr('disabled',1);
				$('#room').val('').attr('disabled',1);
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
					$('#unit').attr('disabled',0==1);
					$('#room').val('').attr('disabled',1);
					$('#person').hide();
					$('.noPerson').parent('tr').hide();
				} else {
					$('#unit').attr('disabled',1);
					$('#room').attr('disabled',1);
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
			url:'http://www.500mi.com/work/tenement/home/findRoom/',
			type:'POST',
			data:'build='+build+'&unit='+unit,
			dataType:'json',
			timeout:5000,
			error:function(){
				$('#room').attr('disabled',1);
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
					$('#room').attr('disabled',0==1);
					$('#person').hide();
					$('.noPerson').parent('tr').hide();
				} else {
					$('#room').val('').attr('disabled',1);
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
			url:'http://www.500mi.com/work/tenement/home/findPerson/',
			type:'POST',
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

function MenuNav(o) {
	//alert(o);
	if(o.children('.u2').css('display') == 'none') {
		o.children('.u2').show();
	} else {
		o.children('.u2').hide();
	}
}

function showMobile(o) {
	//alert(o);
	$('#owner_mobile').val(o);
}