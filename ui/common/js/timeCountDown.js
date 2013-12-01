/* by zhangxinxu 2010-07-27 
* http://www.zhangxinxu.com/
* 倒计时的实现
*/
var fnTimeCountDown = function(d, o, x){
	var xx = new Date();
	var shijicha = d - x;
	var bendiyuanlai = xx.getTime();
	var xianshangshiji = x;

	// var _phpnow = x;
	// console.log(new Date(_phpnow))
	// console.log(new Date(_jsnow));
	// console.log(new Date(_phpnow));
	// console.log("sum:"+sum);
	var f = {
		zero: function(n){
			var n = parseInt(n, 10);
			if(n > 0){
				if(n <= 9){
					n = "0" + n;	
				}
				return String(n);
			}else{
				return "00";	
			}
		},
		dv: function(){
			var xx = new Date();
			var _jsnow = xx.getTime();
			d = d || Date.UTC(2050, 0, 1); //如果未定义时间，则我们设定倒计时日期是2050年1月1日
			var xx = new Date();
			var now = x || xx.getTime();
			now = now-28800000+_jsnow-bendiyuanlai;
			var future = new Date(d);
			var now	   = new Date(now);
			//现在将来秒差值
			var dur = Math.round((future.getTime() - now.getTime()) / 1000) + future.getTimezoneOffset() * 60, pms = {
				sec: "00",
				mini: "00",
				hour: "00",
				day: "00",
				month: "00",
				year: "0"
			};
			// console.log(dur);
			if(dur > 0){
				pms.sec = f.zero(dur % 60);
				pms.mini = Math.floor((dur / 60)) > 0? f.zero(Math.floor((dur / 60)) % 60) : "00";
				pms.hour = Math.floor((dur / 3600)) > 0? f.zero(Math.floor((dur / 3600)) % 24) : "00";
				pms.day = Math.floor((dur / 86400)) > 0? f.zero(Math.floor((dur / 86400)) % 30) : "00";
				//月份，以实际平均每月秒数计算
				pms.month = Math.floor((dur / 2629744)) > 0? f.zero(Math.floor((dur / 2629744)) % 12) : "00";
				//年份，按按回归年365天5时48分46秒算
				pms.year = Math.floor((dur / 31556926)) > 0? Math.floor((dur / 31556926)) : "0";
			}
			return pms;
		},
		ui: function(){
			if(o.sec){
				o.sec.innerHTML = f.dv().sec;
			}
			if(o.mini){
				o.mini.innerHTML = f.dv().mini;
			}
			if(o.hour){
				o.hour.innerHTML = f.dv().hour;
			}
			if(o.day){
				o.day.innerHTML = f.dv().day;
			}
			if(o.month){
				o.month.innerHTML = f.dv().month;
			}
			if(o.year){
				o.year.innerHTML = f.dv().year;
			}
			// if( ! (o.sec == '00' && o.mini == '00' && o.hour == '00' && 
			// 	o.day == '00') ) {
			// 	$('.limit-buy .addCartBtn').attr('disabled','disabled').css('background-color','#999');
			// } else {
			// 	$('.limit-buy .addCartBtn').removeAttr('disabled').css('background-color','#F89406');
			// }
			setTimeout(f.ui, 1000);			
		}
	};	
	f.ui();
};