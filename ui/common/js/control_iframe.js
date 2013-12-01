	window.onload=function()
	{
		var body = document.getElementsByTagName("body");

		var insert_div = document.createElement("div");
		insert_div.id = "insert_iframe";
		var object = body[0].appendChild(insert_div);
		var body = document.body,
    		html = document.documentElement;

		var b_height = Math.max( body.scrollHeight, body.offsetHeight, 
                       html.clientHeight, html.scrollHeight, html.offsetHeight );
		document.getElementById('insert_iframe').innerHTML='<iframe id="c_iframe" height="10" width="10" style="display:none" src="/app/app_load#860|'+b_height+'" ></iframe>';
	    runResize(b_height);
	}

	function runResize(height)
	{
		var body = document.body,
    		html = document.documentElement;
		var b_height = Math.max( body.scrollHeight, body.offsetHeight, 
                       html.clientHeight, html.scrollHeight, html.offsetHeight );
		if (b_height!=height) {
			height = b_height;
			document.getElementById('insert_iframe').innerHTML='<iframe id="c_iframe" height="10" width="10" style="display:none" src="/app/app_load#860|'+b_height+'" ></iframe>';
			setTimeout("runResize("+height+")",3000);
		}
	}

