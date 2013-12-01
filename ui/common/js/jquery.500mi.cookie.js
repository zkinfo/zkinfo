/****************************************************************
** CheckBox 操作方法
****************************************************************/
$(document).ready(function(){
    /*全选 反选 操作*/
    $('#selectall').click(function(){
        if($(this).attr("checked")){
            //全选，保存到cookie.
            $('.smsid').attr('checked','checked');
            $('.smsid').each(function(){
                var tempvalue = '["'
                + $(this).parents('tr').attr('id') + '","'
                + $(this).parents('tr').find('.owner_name').text() + '","'
                + $(this).parents('tr').find('.owner_mobile').text()  + '","'
                + $(this).parents('tr').find('.express').text() + '","'
                + $(this).parents('tr').find('.cdate').text()
                + '"]';
                setsmscookie(tempvalue,'add');
            });
        }else{
            //全不选，清空cookie，包括翻页数据.
            $.cookie('kuai_object',null,{path: '/'});
            $('.smsid').removeAttr('checked');
        }
    });
    /*单个操作*/
    $('.smsid').click(function(){
        var id = $(this).parents('tr').attr('id');
        var tempvalue = tempvalue = '["'
                + $(this).parents('tr').attr('id') + '","'
                + $(this).parents('tr').find('.owner_name').text() + '","'
                + $(this).parents('tr').find('.owner_mobile').text()  + '","'
                + $(this).parents('tr').find('.express').text() + '","'
                + $(this).parents('tr').find('.cdate').text()
                + '"]';
        if($(this).attr("checked")){
            //添加到cookie.
            setsmscookie(tempvalue,'add');
        }else{
            //从cookie删除.
            setsmscookie(tempvalue,'del');
        }
    });
    //页面load 时，载入cookie, 已经选择的打钩.
    var cookievalue = $.cookie('kuai_object');
    //alert(cookievalue);
    if(cookievalue==null||cookievalue.length==0) {
        $('.smsid').removeAttr('checked');
    } else {
        $('.smsid').removeAttr('checked');
        var cookiearr = eval('['+cookievalue+']');
        for(var i=0;i<cookiearr.length;i++)
        {
            $('#'+cookiearr[i][0] + ' .smsid').attr('checked','checked');
        }
    }
});

/*
** 设置cookie函数
** param string tempvalue cookie 字符串
** param string status = add|del 添加|删除
*/
function setsmscookie(tempvalue,status)
{
    var cookievalue = $.cookie('kuai_object');
    if(status=="add")
    {
        if(cookievalue==null||cookievalue.length==0){
            cookievalue =  tempvalue;
        }else{
            if(cookievalue.indexOf(tempvalue)==-1){
                cookievalue = cookievalue + ',' + tempvalue;
            }
        }
    }else if(status=="del")
    {
        cookievalue = cookievalue.replace(','+tempvalue,'');
        cookievalue = cookievalue.replace(tempvalue+',','');
        cookievalue = cookievalue.replace(tempvalue,'');
    }
    $.cookie('kuai_object',cookievalue,{path: '/'});
}