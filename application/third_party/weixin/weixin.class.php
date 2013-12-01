<?php
	/**
	 * 微信客户端类
	 * @package weixin-client
	 */

	/**
	 * 微信客户端类
	 * @author scorpio
	 */
	class weChat{

		/**
		 * construct weChat api
		 */
		public function __construct(){

		}

		/**
		 * 服务器指令入口
		 * @param  string $method 命令
		 * @param  array $params  参数
		 * @return string         结果
		 */
		public function init($method, $params)
		{
			return $this->$method($params);
		}

		/**
		 * 微信第一次设置
		 * @return string 验证字符串
		 */
		public function initSet()
		{
			$echoStr = $_GET["echostr"];
			echo $echoStr;
		}

		/**
		 * 检查是否为微信服务器请求
		 * @return bool 微信服务器请求true,否则false
		 */
		public function checkSignature()
		{
			if($_GET){		
				$signature = isset($_GET["signature"])?$_GET["signature"]:'';
				$timestamp = isset($_GET["timestamp"])?$_GET["timestamp"]:'';
				$nonce =isset($_GET["nonce"])?$_GET["nonce"]:'';	
				$token = TOKEN;
				$tmpArr = array($token, $timestamp, $nonce);
				sort($tmpArr);
				$tmpStr = implode( $tmpArr );
				$tmpStr = sha1( $tmpStr );
				if( $tmpStr == $signature ){
					return true;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}

		/**
		 * 解析数据
		 * @return array 微信内容
		 */
		public function parseData(){
			$return = array();
			$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
			if (!empty($postStr)){
				$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
				$postObj = json_encode($postObj);
				$postObj = json_decode($postObj,1);
				return $postObj;
			}else {
				return $return;
			}
		}

		/**
		 * 检查是否登录成功
		 */
		public function check_login(){
			$http = new HTTPRequest('mp.weixin.qq.com', '/cgi-bin/getnewmsgnum?t=ajax-getmsgnum&lastmsgid=3000', 443);
			$http->setHeader('Cookie',$this->cookie);
			$http->setHeader('Referer',"https://mp.weixin.qq.com/cgi-bin/getmessage?t=wxm-message&token=".$this->token."&lang=zh_CN&count=50");
			$http->setType('POST');
			$post["token"] = $this->token;
			$post["ajax"] = 1;
			$http->setData($post);
			$http->setHeadOut();
			$http->execute();
			preg_match_all( "/Set-Cookie:(.*?)\r\n/" ,$http->getResponseText(),  $cookies );
			$cookie = "";
			foreach ($cookies[1] as $key => $value) {
				$value = str_replace("Path=/; Secure; HttpOnly","",$value);
				$cookie.= $value;
			}
			// var_dump(strpos($cookie,'slave_sid'));
			// var_dump($cookie);
			if(!strpos($cookie,'slave_sid=EXPIRED')){
				return true;
			}else{
				return false;
			}
		}

		/**
		 * 模拟登录获取cookie
		 * @param  string $imgcode 图片验证码
		 * @return string          cookie
		 */
		public function login($imgcode=''){
			$rand = rand();
			$http = new HTTPRequest('mp.weixin.qq.com', '/cgi-bin/login?lang=zh_CN', 443);
			$http->setType('POST');
			$post["username"] = ACCOUNT;
			$post["pwd"] = md5(PASSWORD);
			if($imgcode){
				$post["imgcode"] = $imgcode;
				$http->setHeader('Cookie',$this->read('login_cookie/login_cookie'.UID.'.data'));
			}
			$http->setHeader('Referer','https://mp.weixin.qq.com/');
			$post["f"] = "json";
			$http->setData($post);
			$http->setHeadOut();
			$http->execute();
			preg_match_all( "/Set-Cookie:(.*?)\r\n/" ,$http->getResponseText(),  $cookies );
			$cookie = "";
			foreach ($cookies[1] as $key => $value) {
				$value = str_replace("Path=/; Secure; HttpOnly","",$value);
				$cookie.= $value;
			}
			// var_dump($post);
			// var_dump($http);
			preg_match('/{[^{}]*}/', $http->getResponseText(), $matches);
			$return = json_decode($matches[0],1);
			$data = explode("token=", $return['ErrMsg']);
			if($cookie){
				$this->write('cookie/cookie'.UID.'.data',$cookie);
				$this->write('token/token'.UID.'.data',$data[1]);
			}
			return $cookie;
		}

		/**
		 * 获取cookie
		 * @return string 返回cookie
		 */
		public function getCookie(){
			$file = APPPATH."logs/data/cookie/cookie".UID.".data";
			if(file_exists($file)){
				// cookie600秒过期
				if( time() - filemtime($file) >= 60*60*24*365){
					$cookie = $this->login();
					$this->write('cookie/cookie'.UID.'.data',$cookie);
					return $cookie;
				}else{
					return $this->read('cookie/cookie'.UID.'.data');
				}
			}else{
				$cookie = $this->login();
				if($cookie){
					$this->write('cookie/cookie'.UID.'.data',$cookie);
				}
				return $cookie;
			}
		}

		/**
		 * 设置cookie
		 */
		public function setCookie(){
			$this->cookie = $this->getCookie();
			return $this->cookie;
		}

		/**
		 * 设置token
		 */
		public function setToken(){
			$this->token = $this->getToken();
			return $this->token;
		}

		/**
		 * 把内容写入文件
		 * @param  string $filename 文件名
		 * @param  string $content  文件内容
		 * @return null           无
		 */
		public function write($filename,$content){
			// $fp= fopen("./data/".$filename,"w");
			$fp= fopen(APPPATH."logs/data/".$filename,"w");
			fwrite($fp,$content);
			fclose($fp);
		}

		/**
		 * 读取文件内容
		 * @param  string $filename 文件名
		 * @return string           文件内容
		 */
		public function read($filename){
			if(file_exists(APPPATH."logs/data/".$filename)){
			// if(file_exists("./data/".$filename)){
				$data = '';
				$handle=fopen(APPPATH."logs/data/".$filename,'r');
				// $handle=fopen("./data/".$filename,'r');
				while (!feof($handle)){
					$data.=fgets($handle);
				}
				fclose($handle);
				return $data;
			}
		}

		/**
		 * 查看文件夹权限
		 * @return bool 是否有权限
		 */
		public function checkPermission(){
			$filename = rand();
			$fp= @fopen("./data/tmp/".$filename,"w");
			if($fp){
				return "ok";
			}else{
				return "no";
			}
		}

		/**
		 * 获取token
		 * @return string token
		 */
		public function getToken(){
			// $file = "./data/token/token".UID.".data";
			return $this->read('token/token'.UID.'.data');
		}

		/**
		 * 设置开发模式
		 */
		public function setDeverlop(){
			$http = new HTTPRequest('mp.weixin.qq.com', "/cgi-bin/operadvancedfunc?op=list&t=wxm-developer-index&token=".$this->token."&lang=zh_CN", 443);
			$http->setHeader('Cookie',$this->read('cookie/cookie'.UID.'.data'));
			$http->execute();
			$results = $http->getResponseText();
			$tmp = explode('<span class="nav_mod_tip wysiwyg" ><i class="add_on_icon32"></i>', $results);
			$tmp = explode("</span>", $tmp[1]);
			$edit = $tmp[0];
			$tmp = explode('<span class="nav_mod_tip dev"><i class="add_on_icon32"></i>', $results);
			$tmp = explode("</span>", $tmp[1]);
			$developer = $tmp[0];
			$result = array();
			$result['edit'] = $edit;
			$result['developer'] = $developer;
			// 如果编辑模式开着
			if($edit == '已开启'){
				$http = new HTTPRequest('mp.weixin.qq.com', "/cgi-bin/operadvancedfunc?op=switch&lang=zh_CN&token=".$this->token, 443);
				$http->setHeader('Cookie',$this->read('cookie/cookie'.UID.'.data'));
				$http->setHeader('Referer','http://mp.weixin.qq.com/cgi-bin/operadvancedfunc?op=list&t=wxm-developer-api&token='.$this->token.'&lang=zh_CN');
				$http->setType('POST');
				$post = array(
					'flag'=>0,
					'type'=>1,
					'token'=>$this->token,
					'ajax'=>0
				);
				$http->setData($post);
				$http->execute();
				$_tmp = json_decode($http->getResponseText(),1);
				if($_tmp['BizBaseRetResp']['Ret'] == 0){
					$result['edit'] = '已关闭';
				}
			}
			// 如果开发者模式关着
			if($developer == '已关闭'){
				$http = new HTTPRequest('mp.weixin.qq.com', "/cgi-bin/operadvancedfunc?op=switch&lang=zh_CN&token=".$this->token, 443);
				$http->setHeader('Cookie',$this->cookie);
				$http->setHeader('Referer','http://mp.weixin.qq.com/cgi-bin/operadvancedfunc?op=list&t=wxm-developer-api&token='.$this->token.'&lang=zh_CN');
				$http->setType('POST');
				$post = array(
					'flag'=>1,
					'type'=>2,
					'token'=>$this->token,
					'ajax'=>1
				);
				$http->setData($post);
				$http->execute();
				$_tmp = json_decode($http->getResponseText(),1);
				if($_tmp['BizBaseRetResp']['Ret'] == 0){
					$result['developer'] = '已开启';
				}
			}	
			return $result;		
		}

		/**
		 * 设置接口
		 * @param [type] $url   [description]
		 * @param [type] $token [description]
		 */
		public function setApi($params){
			$this->setDeverlop();
			$http = new HTTPRequest('mp.weixin.qq.com', "/cgi-bin/callbackprofile?t=ajax-response&lang=zh_CN", 443);
			$http->setHeader('Cookie',$this->cookie);
			$http->setHeader('Referer','http://mp.weixin.qq.com/cgi-bin/devapply?opcode=getinfo&t=wxm-developer-api-reg-port&token='.$this->token.'&lang=zh_CN');
			$http->setType('POST');
			$post = array(
				'url'=>$params["url"],
				'callback_token'=>$params["token"],
				'token'=>$this->token,
				'ajax'=>1
			);
			$http->setData($post);
			$http->execute();
			return json_decode($http->getResponseText(),1);
		}

		/**
		 * 主动发送信息
		 * @param  array $params 参数
		 * -id 	   integer 用户fakeid
		 * -type   integer 发送类型
		 * 		-1 文字消息
		 * 		-2 图片信息
		 * 		-3 语音信息
		 * 		-4 视频信息
		 * -fileid integer 资源id
		 * -fid    integer 资源id
		 * @return string        发送结果
		 */
		public function send($params){
			$http = new HTTPRequest('mp.weixin.qq.com', "/cgi-bin/singlesend?t=ajax-response&lang=zh_CN", 443);
			$http->setHeader('Cookie',$this->cookie);
			$http->setHeader('Referer',"https://mp.weixin.qq.com/cgi-bin/singlemsgpage?token=".$this->token."&fromfakeid=".$params["id"]."&msgid=&source=&count=20&t=wxm-singlechat&lang=zh_CN");
			$http->setType('POST');
			$post = array();
			$post['tofakeid'] = $params["id"];
			$post['type'] = $params['type'];
			if($params['type'] == 10){
				$post['fid'] = $params["fid"];
				$post['appmsgid'] = $params["appmsgid"];
			}
			if($params['type'] == 1){
				$post['content'] = $params["content"];
			}
			if( in_array($params['type'], array('2','3','4'))){			
				$post['fileid'] = $params["fileid"];
				$post['fid'] = $params["fid"];
			}
			$post['error'] = false;
			$post['ajax'] = 1;
			$post['token'] = $this->token;
			$http->setData($post);
			$http->execute();
			// var_dump($http);
			return json_decode($http->getResponseText(),1);
		}

		/**
		 * 发图扩展
		 * @return [type] [description]
		 */
		public function sendExten($params){
			$id = $this->upload($params['file']);
			$params['fileid'] = $id;
			$params['fid'] = $id;
			return $this->send($params);
		}

		/**
		 * 设置帐号密码token
		 * @param array $params 帐号密码token
		 */
		public function setAccount($params){
			$content  = '';
			$content .= '<?php';
			$content .= '	define("TOKEN", "'.$params['TOKEN'].'");';
			$content .= '	define("ACCOUNT", "'.$params['ACCOUNT'].'");';
			$content .= '	define("PASSWORD", "'.$params['PASSWORD'].'");';
			$this->write('inc/weixin.inc.'.UID.'.php',$content);
		}

		/**
		 * 获取登录者的fakeid
		 * @return integer fakeid
		 */
		function ownFakeId()
		{
			$http = new HTTPRequest('mp.weixin.qq.com', '/cgi-bin/indexpage?t=wxm-index&lang=zh_CN&token='.$this->token, 443);
			$http->setHeader('Cookie',$this->cookie);
			$http->execute();
			preg_match('/FakeID\s*:\s*"(\d+)"/', $http->getResponseText(), $matches);
			return $matches[1];
		}

		/**
		 * 获取登录者信息
		 */
		function ownInfo()
		{
			$http = new HTTPRequest('mp.weixin.qq.com', "/cgi-bin/userinfopage?t=wxm-setting&token=".$this->token."&lang=zh_CN", 443);
			$http->setHeader('Cookie',$this->cookie);
			$http->execute();
			$tmp = explode('<script type="json" id="json-setting">', $http->getResponseText());
			$tmp = explode('</script>', $tmp[1]);
			$tmp[0] = $this->deletehtml($tmp[0]);
			preg_match_all( '/NickName : "(.*?)",\r\n/' ,$http->getResponseText(),  $nickname );
			$return =  json_decode($tmp[0],1);
			$return['nickname'] = $nickname[1][0];
			return $return;
		}

		/**
		 * 延续cookie
		 */
		function extendCookie()
		{
			$http = new HTTPRequest('mp.weixin.qq.com', '/cgi-bin/home?t=home/index&lang=zh_CN&token='.$this->token, 443);
			$http->setHeader('Cookie',$this->cookie);
			$http->setHeadOut();
			// $post = array();
			// $post['token'] = $this->token;
			// $post['lang'] = 'zh_CN';
			// $post['t'] = 'ajax-getcontactinfo';
			// $post['fakeid'] = 1034585;
			// $http->setData($post);
			// $http->setHeader('Referer',"https://mp.weixin.qq.com/cgi-bin/message?t=message/list&count=20&day=7&token=".$this->token."&lang=zh_CN");
			$http->execute();
			// var_dump($http);
			// exit();
			// echo($http->getResponseText());
			preg_match_all( "/Set-Cookie:(.*?)\r\n/" ,$http->getResponseText(),  $cookies );
			$cookie = "";
			foreach ($cookies[1] as $key => $value) {
				$value = str_replace("Path=/; Secure; HttpOnly","",$value);
				$cookie.= $value;
			}
			// var_dump($cookie);
			return $cookie;
		}	

		/**
		 * 根据fakeid获取用户的信息
		 * @param  array $params 参数
		 * -id 用户的fakeid
		 * @return string        发送结果
		 */
		public function getInfo($params)
		{
			$http = new HTTPRequest('mp.weixin.qq.com', '/cgi-bin/getcontactinfo?t=ajax-getcontactinfo&lang=zh_CN&fakeid='.$params["fakeid"], 443);
			$http->setHeader('Cookie',$this->cookie);
			$http->setType('POST');
			$post = array(
				"token"	=>	$this->token,
				"ajax"	=>	1
			);
			$http->setData($post);
			$http->execute();
			return json_decode($http->getResponseText(),1);
		}

		/**
		 * 获取粉丝列表
		 * @param  array $params 参数
		 * -num 粉丝数目
		 * @return string        发送结果
		 */
		public function fans($params)
		{
			$http = new HTTPRequest('mp.weixin.qq.com', '/cgi-bin/contactmanage?t=user/index&token='.$this->token.'&lang=zh_CN&pagesize='.$params['num'].'&pageidx='.$params['index'].'&type=0&groupid='.$params['id'], 443);
			$http->setHeader('Cookie',$this->cookie);
			$http->execute();
			$tmp = explode('({"contacts":', $http->getResponseText());
			$tmp = explode('}).contacts,', $tmp[1]);
			return json_decode($tmp[0],1);
		}	

		/**
		 * 获取资源列表
		 * @param  array $params 参数
		 * -type 4:视频 3:语音 2:图片
		 * -num  数目
		 * @return string        发送结果
		 */
		public function sourceList($params)
		{
			$http = new HTTPRequest('mp.weixin.qq.com', '/cgi-bin/filemanagepage?t=wxm-file&lang=zh_CN&token='.$this->token.'&type='.$params["type"].'&pagesize='.$params["num"].'&pageidx='.$params["index"], 443);
			$http->setHeader('Cookie',$this->cookie);
			$http->execute();
			$tmp = explode('<script type="json" id="json-fileList">', $http->getResponseText());
			$tmp = explode('</script>', $tmp[1]);
			$tmp[0] = $this->deletehtml($tmp[0]);
			return json_decode($tmp[0],1);
		}

		/**
		 * 获取图文资源列表
		 * @param  array $params 参数
		 * -num 数目
		 * @return string        发送结果
		 */
		function mediaList($params)
		{
			// /cgi-bin/appmsg?begin=0&count=1&t=media/appmsg_list&type=10&action=list&token=813639401&lang=zh_CN
			// $http = new HTTPRequest('mp.weixin.qq.com', "/cgi-bin/operate_appmsg?token=".$this->token."&lang=zh_CN&sub=list&type=10&subtype=3&t=wxm-appmsgs-list-new&pagesize=".$params["num"]."&pageidx=".$params["index"]."&lang=zh_CN", 443);
			$http = new HTTPRequest('mp.weixin.qq.com', "/cgi-bin/appmsg?begin=0&count=1&t=media/appmsg_list&type=10&action=list&token=".$this->token."&lang=zh_CN", 443);
			$http->setHeader('Cookie',$this->cookie);
			$http->execute();
			// echo $http->getResponseText();
			$tmp = explode('"app_id":', $http->getResponseText());
			$tmp = explode(',"file_id"', $tmp[1]);
			$tmp[0] = $this->deletehtml($tmp[0]);
			// var_dump($tmp[0]);
			return $tmp[0];
		}

		/**
		 * 获取聊天记录
		 */
		public function getChatLog($params)
		{
			$http = new HTTPRequest('mp.weixin.qq.com', "/cgi-bin/message?t=message/list&count=20&day=7&token=".$this->token."&lang=zh_CN", 443);
			$http->setHeader('Cookie',$this->cookie);
			$http->execute();
			$tmp = explode('"msg_item":', $http->getResponseText());
			$tmp = explode('}).msg_item ', $tmp[1]);
			return json_decode($tmp[0],1);
		}

		/**
		 * 获取和某个人的聊天记录
		 */
		public function getChatLogByFakeId($params)
		{
			$http = new HTTPRequest('mp.weixin.qq.com', "/cgi-bin/singlemsgpage?token=".$this->token."&fromfakeid=".$params['fakeid']."&msgid=&source=&count=".$params["num"]."&t=wxm-singlechat&lang=zh_CN", 443);
			$http->setHeader('Cookie',$this->cookie);
			$http->execute();
			$tmp = explode('<script id="json-msgList" type="json">', $http->getResponseText());
			$tmp = explode('</script>', $tmp[1]);
			$tmp[0] = $this->deletehtml($tmp[0]);
			return json_decode($tmp[0],1);
		}

		/**
		 * 获取语音聊天记录
		 */
		public function getAudio($params)
		{
			$http = new HTTPRequest('mp.weixin.qq.com', "/cgi-bin/getvoicedata?token=".$this->token."&msgid=".$params['id']."&fileid=0", 443);
			$http->setHeader('Cookie',$this->cookie);
			$http->execute();
			return $http->getResponseText();
		}

		/**
		 * 移动分组
		 * @param  [type] $params [description]
		 * @return [type]         [description]
		 */
		public function modContact($params)
		{
			$http = new HTTPRequest('mp.weixin.qq.com', '/cgi-bin/modifycontacts?action=modifycontacts&t=ajax-putinto-group', 443);
			$http->setHeader('Cookie',$this->cookie);
			$http->setHeader('Referer',"https://mp.weixin.qq.com/cgi-bin/contactmanagepage?token=".$this->token."&t=wxm-friend&lang=zh_CN&pagesize=10&pageidx=0&type=0&groupid=0");
			$http->setType('POST');
			$post = array();
			$post['contacttype'] 	= $params['contacttype'];
			$post['tofakeidlist'] 	= $params['tofakeidlist'];
			$post['token'] 			= $this->token;
			$post['ajax'] 			= 1;
			$http->setData($post);
			$http->execute();
			return json_decode($http->getResponseText(),1);
		}

		/**
		 * 获取用户头像
		 * @param  [type] $params [description]
		 * @return [type]         [description]
		 */
		public function getAvatar($params)
		{
			$http = new HTTPRequest('mp.weixin.qq.com', "/cgi-bin/getheadimg?token=".$this->token."&fakeid=".$params['fakeid'], 443);
			$http->setHeader('Cookie',$this->cookie);
			$http->execute();
			return $http->getResponseText();
		}

		/**
		 * 保存头像
		 * @param  [type] $fakeid [description]
		 * @return [type]         [description]
		 */
		public function saveAvatar($fakeid){
			$fh = fopen(APPPATH."../data/avatar/".$fakeid.'.jpg', "w");
			fwrite($fh, $this->getAvatar(array('fakeid'=>$fakeid)));
			fclose($fh);
		}

		/**
		 * 查看聊天记录中的图片
		 */
		public function getChatImage($params){
			$http = new HTTPRequest('mp.weixin.qq.com', "/cgi-bin/getimgdata?token=".$this->token."&msgid=".$params['id']."&mode=".$params['mode']."&source=".$params['source']."&fileId=".$params['fid'], 443);
			$http->setHeader('Cookie',$this->cookie);
			$http->execute();
			// var_dump($http);
			return $http->getResponseText();
		}

		/**
		 * 获取公众账号二维码
		 * @param  [type] $params [description]
		 * @return [type]         [description]
		 */
		public function getQrcode($params)
		{
			$http = new HTTPRequest('mp.weixin.qq.com', "/cgi-bin/getqrcode?fakeid=".$params['fakeid']."&style=1&action=download&token=".$this->token, 443);
			$http->setHeader('Cookie',$this->cookie);
			$http->execute();
			echo $http->getResponseText();
		}

		/**
		 * 设置备注
		 */
		public function setRemark($params)
		{
			$http = new HTTPRequest('mp.weixin.qq.com', '/cgi-bin/modifycontacts?t=ajax-response&action=setremark', 443);
			$http->setHeader('Cookie',$this->cookie);
			$http->setHeader('Referer',"https://mp.weixin.qq.com/cgi-bin/contactmanagepage?token=".$this->token."&t=wxm-friend&lang=zh_CN&pagesize=10&pageidx=0&type=0&groupid=0");
			$http->setType('POST');
			$post = array();
			$post['remark'] = $params['remark'];
			$post['tofakeuin'] = $params['fakeid'];
			$post['token'] = $this->token;
			$post['ajax'] = 1;
			$http->setData($post);
			$http->execute();
			return json_decode($http->getResponseText(),1);
		}

		/**
		 * PHP 过滤HTML代码空格,回车换行符的函数
		 * @param  string $str 内容
		 * @return string      过滤之后的内容
		 */
		private function deletehtml($str)
		{
			$str = trim($str);
			$str=strip_tags($str,"");
			$str=preg_replace("{\t}","",$str);
			$str=preg_replace("{\r\n}","",$str);
			$str=preg_replace("{\r}","",$str);
			$str=preg_replace("{\n}","",$str);
			$str=preg_replace("{ }","",$str);
			return $str;
		}

		/**
		 * 获取图片验证码
		 * @return image 图片验证码
		 */
		public function imgcode(){
			$snoopy = new Snoopy; 
			$url = "http://mp.weixin.qq.com/cgi-bin/verifycode?username=".ACCOUNT;
			$snoopy->fetch($url); 
			$cookie = '';
			foreach ($snoopy->headers as $key => $value) {
				$value = trim($value);
				if(strpos($value,'Set-Cookie: ') || strpos($value,'Set-Cookie: ')===0){
					$tmp = str_replace("Set-Cookie: ","",$value);
					$tmp = str_replace("Path=/","",$tmp);
					$cookie.=$tmp;
				}
			}
			$this->write('login_cookie/login_cookie'.UID.'.data',$cookie);
			return $snoopy->results;  
		}

		/**
		 * 被动回复文本消息
		 * @param  string 	$fromUsername 接收方帐号(收到的OpenID)
		 * @param  string 	$toUsername   开发者微信号
		 * @param  string 	$content      回复的消息内容,长度不超过2048字节
		 * @param  integer 	$FuncFlag     1:星标刚才的消息,默认不星标
		 * @return string                 发送结果
		 */
		public function sendText($params)
		// public function sendText($fromUsername, $toUsername, $content, $FuncFlag=0)
		{
			$textTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[text]]></MsgType>
						<Content><![CDATA[%s]]></Content>
						<FuncFlag>%s</FuncFlag>
						</xml>";
			if(!isset($params['FuncFlag'])){
				$params["FuncFlag"] = 0;
			}  
			$resultStr = sprintf($textTpl, $params["FromUserName"], $params["ToUserName"], time(), $params["content"], $params["FuncFlag"]);
			return $resultStr;
		}

		/**
		 * 被动回复音乐消息
		 * @param  string  $fromUsername 接收方帐号(收到的OpenID)
		 * @param  string  $toUsername   开发者微信号
		 * @param  string  $title         音乐标题
		 * @param  string  $description  音乐描述
		 * @param  string  $musicurl     音乐链接
		 * @param  string  $hdmusicurl   高质量音乐链接，WIFI环境优先使用该链接播放音乐
		 * @param  integer $FuncFlag     1:星标刚才的消息,默认不星标
		 * @return string                发送结果
		 */
		// public function sendMusic($fromUsername, $toUsername, $title, $description, $musicurl, $hdmusicurl, $FuncFlag=0)
		public function sendMusic($params)
		{
			$textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[music]]></MsgType>
							<Music>
								<Title><![CDATA[%s]]></Title>
								<Description><![CDATA[%s]]></Description>
								<MusicUrl><![CDATA[%s]]></MusicUrl>
								<HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
							</Music>
							<FuncFlag>%s</FuncFlag>
						</xml>"; 
			if(!isset($params['FuncFlag'])){
				$params["FuncFlag"] = 0;
			}   
			$resultStr = sprintf(
				$textTpl, 
				$params["FromUserName"], 
				$params["ToUserName"], 
				time(), 
				$params["title"], 
				$params["description"], 
				$params["musicurl"], 
				$params["hdmusicurl"], 
				$params["FuncFlag"]
			);
			return $resultStr;
		}

		/**
		 * 被动回复图文消息(多图无描,单图有描述)
		 * @param  string $fromUsername 接收方帐号(收到的OpenID)
		 * @param  string $toUsername   开发者微信号
		 * @param  array  $articles     
		 *         -title 				图文消息标题
		 *         -description 		图文消息描述
		 *         -picurl 				图片链接，支持JPG、PNG格式
		 *         -url 				点击图文消息跳转链接	
		 * @param  string $FuncFlag     1:星标刚才的消息,默认不星标
		 * @return string               发送结果
		 */
		// public function sendArticle($fromUsername, $toUsername, $articles, $FuncFlag)
		public function sendArticle($params)
		{
			if(!isset($params['FuncFlag'])){
				$params["FuncFlag"] = 0;
			}   
			$xml  = '';
			$xml .= '<xml>';
			$xml .= '	<ToUserName><![CDATA['.$params["FromUserName"].']]></ToUserName>';
			$xml .= '	<FromUserName><![CDATA['.$params["ToUserName"].']]></FromUserName>';
			$xml .= '	<CreateTime>'.time().'</CreateTime>';
			$xml .= '	<MsgType><![CDATA[news]]></MsgType>';
			$xml .= '	<ArticleCount>'.count($params["articles"]).'</ArticleCount>';
			$xml .= '	<Articles>';
			foreach ($params["articles"] as $key => $value) {
				$xml .= '<item>';
				$xml .= '	<Title><![CDATA['.$value['title'].']]></Title> ';
				$xml .= '	<Description><![CDATA['.$value['description'].']]></Description>';
				$xml .= '	<PicUrl><![CDATA['.$value['picurl'].']]></PicUrl>';
				$xml .= '	<Url><![CDATA['.$value['url'].']]></Url>';
				$xml .= '</item>';
			}
			$xml .= "	</Articles>";
			$xml .= "	<FuncFlag>".$params["FuncFlag"]."</FuncFlag>";
			$xml .= "</xml>";
			return $xml;
		}

		/**
		 * 官方批量发送
		 */
		public function officialBatSend($params)
		{
			$send_snoopy = new Snoopy; 
			$post = array();
			$post['type'] 			= 1;
			$post['content'] 		= $params['content'];
			$post['error'] 			= false;
			$post['needcomment'] 	= 0;
			$post['groupid'] 		= -1;
			$post['sex'] 			= 0;
			$post['country'] 		= '';
			$post['city'] 			= '';
			$post['token'] 			= $this->token;
			$post['ajax'] 			= 1;
			$send_snoopy->referer = 'http://mp.weixin.qq.com/cgi-bin/masssendpage?t=wxm-send&token='.$this->token.'&lang=zh_CN';
			$submit = "http://mp.weixin.qq.com/cgi-bin/masssend?t=ajax-response";
			$send_snoopy->rawheaders['Cookie']= $this->cookie;
			$send_snoopy->submit($submit,$post);
		}

		/**
		 * 官方发送次数
		 */
		public function officialSendTime()
		{
			$http = new HTTPRequest('mp.weixin.qq.com', '/cgi-bin/masssendpage?t=wxm-send&token='.$this->token.'&lang=zh_CN', 443);
			$http->setHeader('Cookie',$this->cookie);
			$http->execute();
			return $http->getResponseText();
		}

		/**
		 * 上传资源
		 */
		public function upload($file)
		{
			$extra = array(
				'cookie'=>$this->cookie
			);

			// $finfo = finfo_open(FILEINFO_MIME, "/usr/share/misc/magic");
			// $post_data = array(
			//         'uploadfile'=>'@'.$file.';type='.finfo_file($finfo, $file)
			// );
			// finfo_close($finfo);

            $post_data = array(
                    'uploadfile'=>'@'.$file.';type='.mime_content_type($file)
            );


			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, 'https://mp.weixin.qq.com/cgi-bin/uploadmaterial?cgi=uploadmaterial&type=0&token='.$this->token.'&t=iframe-uploadfile&lang=zh_CN&formId=null');
			curl_setopt($curl, CURLOPT_POST, 1 );
			curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl,CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.64 Safari/537.31");
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);  
			$headers = array();
			foreach ($extra as $name => $val) {
				$headers[] = $name.': '.$val;
			}
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			$result = curl_exec($curl);
			// echo($result);
			$error = curl_error($curl);
			// echo $error;
			preg_match('/[0-9]{8,}/', $result, $matches);
			return $matches[0];
		}

		/**
		 * 图文预览
		 * @param  [type] $params [description]
		 * @return [type]         [description]
		 */
		public function mediaPreview($params){
			$http = new HTTPRequest('mp.weixin.qq.com', '/cgi-bin/operate_appmsg?sub=preview&t=ajax-appmsg-preview', 443);
			$http->setHeader('Cookie',$this->cookie);
			$http->setType('POST');
			$post = array();
			$post["error"] = false;
			$post["count"] = $params['count'];
			$post["AppMsgId"] = null;
			for ($i=0; $i < $params['count']; $i++) { 
				$fid = $this->upload($params['file'.$i]);
				$post["title".$i] = $params['title'.$i];
				$post["digest".$i] = $params['digest'.$i];
				$post["content".$i] = $params["content".$i];
				$post["fileid".$i] = $fid;
				$post["sourceurl".$i] = $params['sourceurl'.$i];
			}
			$post["preusername"] = $params['previewname'];
			$post["token"] = $this->token;
			$post["ajax"] = 1;
			$http->setData($post);
			$http->execute();
			return json_decode($http->getResponseText(),1);
		}

		/**
		 * 图文创建
		 */
		public function mediaCreate($params){
			$http = new HTTPRequest('mp.weixin.qq.com', '/cgi-bin/operate_appmsg?token='.$this->token.'&lang=zh_CN&t=ajax-response&sub=create', 443);
			$http->setHeader('Cookie',$this->cookie);
			$http->setType('POST');
				$post = array();
				$post["error"] = false;
				$post["count"] = $params['count'];
				$post["AppMsgId"] = null;
				for ($i=0; $i < $params['count']; $i++) { 
					if(!isset($params['fid'.$i])){
						$fid = $this->upload($params['file'.$i]);
					}else{
						$fid = $params['fid'.$i];
					}
					$post["title".$i] = $params['title'.$i];
					$post["digest".$i] = $params['digest'.$i];
					$post["content".$i] = $params["content".$i];
					$post["fileid".$i] = $fid;
					$post["sourceurl".$i] = $params['sourceurl'.$i];
				}
				$post["token"] = $this->token;
				$post["ajax"] = 1;
				$http->setData($post);
				$http->execute();
				// echo $http->getResponseText();
				$result = json_decode($http->getResponseText(),1);
				// var_dump($result);
				if($result["ret"] == 0){
					$data = array(
						'index'	=> 0,
						'num'	=> 1
					);
					$ret = $this->mediaList($data);
					// var_dump($ret);
					return $ret;
				}else{
					return 0;
				}
		}

		function saveMsgToFile($params)
		{
			$http = new HTTPRequest('mp.weixin.qq.com', '/cgi-bin/savemsgtofile?t=ajax-response', 443);
			$http->setHeader('Cookie',$this->cookie);
			$http->setHeader('Referer','https://mp.weixin.qq.com/cgi-bin/singlemsgpage?token='.$this->token.'&fromfakeid='.$params['fakeid'].'&msgid=&source=&count=20&t=wxm-singlechat&lang=zh_CN');
			$http->setType('POST');
			$post = array();
			$post["msgid"] = $params['msgid'];
			$post["filename"] = $params['filename'];
			$post["token"] = $this->token;
			$post["ajax"] = 1;
			$http->setData($post);
			$http->execute();
			return json_decode($http->getResponseText(),1);
		}
	}