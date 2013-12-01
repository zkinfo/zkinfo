<?php
/**
 * 500mi OpenAPI libirary
 * 用户操作相关api
 * 传入参数 $param 
 * 返回值 $ret
 */
require_once(APPPATH . 'libraries/OpenApi/ApiErrInfo.php');//加载错误信息配置文件
class Api_user
{
	public function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->business(array("user/passport/passport_biz","user/passport/validate_biz","user/user_biz","user/consumer_biz"));
	}

	/**
	 * 注册
	 */
	public function register($param)
	{

		$param["name"] = isset($param["name"]) ? $param["name"] : $param["mobile"];
		$param["account"] = isset($param["account"]) ? $param["account"] : $param["mobile"];
		if (isset($param['mobile'])){
			if ($param["name"] and ! $this->ci->validate_biz->isName($param["name"])){
				return array('error' => 'ERR_NAME', 'error_msg' => ERR_NAME);
			}elseif(!$this->ci->validate_biz->isMobile($param["mobile"])){
				return array('error' => 'ERR_MOBILE', 'error_msg' => ERR_MOBILE);
			}elseif ($this->ci->validate_biz->isUniqueMobile($param["mobile"])){
				return array('error' => 'ERR_NOT_UNIQUE', 'error_msg' => ERR_NOT_UNIQUE);
			}elseif ($this->ci->validate_biz->isAccount($param["account"]) == "false") {
				return array('error' => 'ERR_ACCOUNT', 'error_msg' => ERR_ACCOUNT);
			}else{
				$rand = isset($param["password"]) ? $param["password"] : rand(100000,999999);
				$params["name"] = isset($param["name"]) ? $param["name"] : '';
				$params["mobile"] = $param["mobile"];
				$params["account"] = isset($param["account"]) ? $param["account"] : $param["mobile"];
				$params["password"] =  md5(md5($rand));
				$params["type"] = "1";
				$params["status"] = 0;
				$params["role_ids"] = "100";
				// 添加user
				if($id = $this->ci->user_biz->addUser($params)){
					$consumer["user_id"] = $id;
					$consumer["realname"] = $param["name"];
					$consumer["mobile"] = $param["mobile"];
					// 添加consumer
					if($this->ci->consumer_biz->addConsumer($consumer)){
						log_message('error', 'consumer添加失败');
					}
					$msg_content = $this->_sendMes("jh_time",$rand,$params["mobile"]);
					$user = $this->ci->user_biz->getUserByMobile($param["mobile"]);
					$res = array('error' => 0, 'error_msg' => 'Register success', 'data' => array('user' => $user, 'smscode' => $rand) ) ;
					return $res;
				}else{
					return array('error' => 'ERR_DB_OPT', 'error_msg' => ERR_DB_OPT);
				}
			}
		}else{
			return array('error' => 'ERR_PARAM', 'error_msg' => ERR_PARAM);
		}
	}

	/**
	 * 登录
	 */
	public function _login($param)
	{
		//return $this->token($param);

		if (isset($param['account']) && isset($param['password']) ){
			$user = $this->ci->user_biz->getUserByAccount($param['account']);
			$mobile_user = $this->ci->user_biz->getUserList(array('mobile' => $param['account']));
			if (empty($user) && empty($mobile_user)) return ERR_INVALID_ACCOUNT;//帐号不存在
			$n = count($mobile_user);
			if($n>1){
				return ERR_UNIQUE_MOBILE;//手机绑定多个帐号
			}else{
				if($mobile_user){
					$mobile_user = $mobile_user[0];
				}
			}

			if (empty($user)){
				$user = $mobile_user;
			}

			if ($user['status'] == -1) return ERR_ACCOUNT;//帐号删除

			if ($user['status'] == 2) return ERR_ACCOUNT;//帐号没有网点开通

			if ($user['status'] == 1){
				return ERR_ACTIVED_ACCOUNT;//帐号待激活
			};
			if($user['password'] == md5($param['password']) || $user['password'] == $param['password'] || $user['password'] == md5(md5($param['password']))){
				
				return array('user' => $user);
			}else{
				return ERR_PASSWORD;//密码错误
			}
		}else{
			return ERR_PARAM;
		}	
	}


	/**
	 *  建立会话，成功返回会话和用户对象
	 */
	public function login($param)
	{
		$request = $param;
		//var_dump($param);die;
		// $request = array(
		// 	'appid' 	=> '500',
		// 	'device_id' => '5001',
		// 	'auth_code' => '4f1b828dc5c4aebd3f93d67d893db2a2',
		// 	//'account' 	=> 'apiAccount',
		// 	'mobile' 	=> '18072705348',
		// 	'pwd' 		=> '10001',
		// 	'token_time'=> '20120910190000',
		// 	'sign' 		=> 'c3d08b02256e40519818988d6437dc84'
		// );
		//91B082588E12306CF4964F2341D87CBB
		if ( ! isset($request['appid'],$request['device_id'],$request['password']) ) {
			return array(
				'error' 	=> 1,
				'error_msg' => 'params error'
			);
		}

		//数据库操作

		$this->ci->load->model('token_model');
		$isParams = array( 'appid' => $request['appid'], 'device_id' => $request['device_id'] );
		$isUser = array();
		if (isset($request['mobile'])) {
			$isUser['mobile'] = $request['mobile'];
		} else if (isset($request['account'])) {
			$isUser['account'] = $request['account'];
		} else {
			return array(
				'error' 	=> 1,
				'error_msg' => 'Login failed, you must give a mobile or an account'
			);	
		}

		//用户是否存在
		$isUser['password'] = md5($request['password']);
		$isUser['status !='] = 1;
		$user = $this->ci->user_model->getOne($isUser, 'id, account, name, mobile');
		if ( empty($user) ) {
			return array(
				'error' 	=> 1,
				'error_msg' => 'User or password is not right'
			);		
		}

		$isParams['uid'] = $user['id'];
		$isParams['token'] = md5($user['id'] . '@mi.api.token');
		//会话是否存在
		$isLogin = $this->ci->token_model->getOne( $isParams );
		if ( empty($isToken) ) {
			$token = array(
				'appid' 		=> $request['appid'], 
				'device_id' 	=> $request['device_id'],
				'uid' 			=> $isParams['uid'],
				'token' 		=> md5($user['id'] . '@mi.api.token'),
				'life' 			=> date('Y-m-d H:i:s', strtotime('+1 day')),
				'count' 		=> 1
			);
			if ( $this->ci->token_model->insert($token) ) {
				return array(
					'error' 	=> 0,
					'error_msg' => 'Login success',
					'data' 		=> array(
						'token' => $token['token'],
						'user' 	=> $this->_return_value($user)
						//4f1b828dc5c4aebd3f93d67d893db2a2
					)
				);						
			} else {
				return array(
					'error' 	=> 1,
					'error_msg' => 'Login save failed , please try again'
				);	
			}
		} else {
			//更新会话使用次数
			$token = array( 'count' => $isToken['count'] + 1 );
			if ( strtotime($isToken['life']) <= time() ) {
				$token['life'] = date('Y-m-d H:i:s', strtotime('+1 day'));
			}
			$this->ci->token_model->update( $token, $isToken['id'] );
			return array(
				'error' 	=> 0,
				'error_msg' => 'Login success',
				'token' 	=> $isToken['token'],
				'data' 		=> array('user' => $this->_return_value($user))
			);
		}

	}	

	/**
	 * 激活
	 */
	public function active($params)
	{
		if (isset($params['uid']) && isset($params['code']) && isset($params['password'])){
			$res = array();
			$uid = $params["uid"];
			$code = $params["code"];
			$password = $params["password"];
			$user = $this->ci->user_biz->getUserById($uid);
			if($user){
				if($user["status"] == "1"){			
					if($user["password"] == md5(md5($code)) ){
						if($password){
							$data = $this->ci->user_biz->updateUser(array('password' => $password),'status'=>'0'),$uid);
							if($data){
								return array("success");
							}else{
								return array("fail");
							}
						}
						$data = $this->ci->user_biz->updateUser(array('status'=>'0'),$uid);
						if($data){
							return array("success");
						}else{
							return array("fail");
						}
					}else{
						return INVALID_CODE;
					}
				}else{
					return ACTIVED_ACCOUNT;
				}
			}else{
				return ERR_INVALID_ACCOUNT;
			}
		}else{
			return ERR_PARAM;
		}	
	}

	private function _return_value($user)
	{
		$user = array_merge(array('user_id'=>$user['id']), $user);
		unset($user['id']);
		return $user;
	}

	/**
	 * 绑定
	 */
	public function bind($params)
	{
		if (isset($params['uid']) && isset($params['platform']) && isset($params['wid'])){
			$uid = $params["uid"];
			$platform = $params["platform"];
			$wid = $params["wid"];
			$user = $this->ci->user_biz->getUserById($uid);
			if($user){
				if($platform == "sina"){
					if($user["sina_a"]){
						return ERR_BIND;
					}else{
						$data = $this->ci->user_biz->updateUser(array('sina_a' => $wid),$uid);
						if($data){
							return array("success");
						}else{
							return array("fail");
						}
					}
				}
				if($platform == "tencent"){
					if($user["qq_a"]){
						return ERR_BIND;
					}else{
						$data = $this->ci->user_biz->updateUser(array('qq_a' => $wid),$uid);
						if($data){
							return array("success");
						}else{
							return array("fail");
						}
					}
				}
			}else{
				return ERR_INVALID_ACCOUNT;
			}
		}else{
			return ERR_PARAM;
		}
	}

	/**
	 * 查询接口
	 */
	public function check($params)
	{
		if (isset($params['platform']) && isset($params['wid'])){
			$platform = $params["platform"];
			$wid = $params["wid"];
			if($platform == "sina"){
				$userList = $this->ci->user_biz->getUserList(array('sina_a' => $wid));
				$user = isset($userList[0]) ? $userList[0] : array();
			}
			if($platform == "tencent"){
				$userList = $this->ci->user_biz->getUserList(array('qq_a' => $wid));
				$user = isset($userList[0]) ? $userList[0] : array();
			}
			if($user){
				$array = array(
					"uid" => $user["id"]
				);
				return $array;
			}else{
				return ERR_ACCOUNT;
			}
		}else{
			return ERR_PARAM;
		}
	}
	

	/**
	 * 发送短信
	 */
	public function _sendMes($method="",$yzCode="",$mobile="")
	{
		require_once APPPATH.'business/msg/message_biz.php';
		$this->msg = Message_biz::factory('sms', array('level' => 1));
		if(!$yzCode){
			$yzCode = rand(100000,999999);	
		}
		$content = "欢迎访问500mi网站，您的验证码为".$yzCode."，如非本人操作，请忽略此短信，详情见 www.500mi.com";
		if($method == "jh_time"){
			$content = "欢迎访问五百米网站，您的验证码为".$yzCode."，如非本人操作，请忽略此短信，详情见 www.500mi.com";
			$message["smstype"] = "active";
		}
		$user = $this->ci->user_biz->getUserByMobile($mobile);
		$uid = $user["id"];
		// $this->session->set_userdata('uid', $uid);
		$array = unserialize($user["ext"]);
		$now_date = date("ymd");
		if ($this->ci->validate_biz->isMobile($mobile)) {
			if($array[$method][$now_date]<=2){
				$curTime = date("U");
				$message["partner_id"] = 0;
				$message["type"] = "1";
				$message["from_uid"] = 0;
				$message["to_uid"] = $uid;
				$message["relation_id"] = 0;
				$message["subject"] = $user["name"];
				$message["contact"] = $mobile;
				$message["content"] = $content;
				$sendTime = $this->ci->session->userdata("sendTime");
				if(!$this->ci->session->userdata('mesTime')){
					$preTime = $curTime;
					$this->msg->task($message);
					return "direct";
					$array[$method][$now_date] = $array[$method][$now_date]+1;
					$list["ext"] = serialize($array);
					$this->ci->user_biz->updateUser($list,$uid);
					$this->ci->session->set_userdata('mesTime', $curTime);
					$this->ci->session->set_userdata('yzCode', $yzCode);
					exit;
				}else{
					$preTime = $this->ci->session->userdata('mesTime');
				}
				$subtime = $curTime-$preTime;
				if($subtime<60){
					return array("short");
				}else{
					$this->msg->task($message);
					$array[$method][$now_date] = $array[$method][$now_date]+1;
					$list["ext"] = serialize($array);
					$this->ci->user_biz->updateUser($list,$uid);
					$this->ci->session->set_userdata('yzCode', $yzCode);
					$this->ci->session->set_userdata('mesTime', $curTime);
					return $message["content"];
				}
			}else{
				return array("much");
			}
		} else {
			return array("invalid mobile");
		}
	}
}