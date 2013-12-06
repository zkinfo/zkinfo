<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 认证授权,各种应用平台认证授权的统一控制
 *
 */
class Auth
{

	/**
	 * 根据访问的应用模块校验应用授权
	 * 1 嵌入其他平台的前台应用模块在MODULES/IMPLANT模块下
	 * 1.1 taobao/follow,guide
	 * 1.2 sina
	 * 路由规则
	 * $route["tb"] = "implant/taobao";
	 * $route["sina"] = "implant/sina";
	 *
	 * 2 后台应用只有一个,在MODULES/!Implant目录下
	 */

	private $apps;

	public function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->library('session');
		$this->ci->load->helper('cookie');
	}

	/**
	 * 通过获取本地的SESSION，验证用户是否登录
	 */
	function  check_auth(){
		$islogin = 0;

		$identity = 'account';
		//var_dump($this->ci->session->all_userdata());
		var_dump($this->ci->session->userdata('account'));
		$islogin = (bool) $this->ci->session->userdata($identity);
		//var_dump($islogin);die('111');

		if($this->ci->session->userdata($identity)) {
			$islogin = 1;
		}else{
			$this->ci->session->sess_destroy();
		}
		return $islogin;
	}

	/*function login($account,$password,$remember="")
	{
		$this->ci->load->business("partner/partner_biz");
		$this->ci->load->business("user/consumer_biz");

		//判断是什么类型传入 帐号，手机，邮箱等 -danchex
		include_once APPPATH . 'libraries/Mobile.php';
		$userSession['remember'] = $remember;
		if (Mobile::isMobile($account)) {
			$result = $this->ci->user_biz->getUserList(array('mobile' => $account));
			if ($result['total'] > 1) {
				return 8; //手机绑定多个帐号
			} else {
				$user = $result['list'][0];
			}
		} else {
			$user = $this->ci->user_biz->getUserByAccount($account);
		}

		if (empty($user)) {
			return 4; //帐号不存在
		}

		if ($user['status'] == -1) return 2;//帐号删除

		// if ($user['status'] == 2) return 6;//帐号没有网点开通
		// var_dump($user);
		if (empty($user['open_codes'])) {
			return 9;
		}
		$open_biz = $this->ci->db->where(array('open_code'=>$user['open_codes']))->get('tt_biz')->row_array();
		if (empty($open_biz)) {
			return 9;
		}

		if($user['password'] == md5($password) || $user['password'] == $password || $user['password'] == md5(md5($password)))
		{		
			$_params = array();
			$_params['last_ip'] = $this->ci->input->ip_address();
			$_params['last_time'] = date("Y-m-d h:m:s");
			$this->ci->user_biz->updateUser($_params, $user["id"]);

			if ($user['status'] == 1)
			{
				$userSession['uid'] = $user['id'];
				$userSession['mobile'] = $user['mobile'];
				$this->setSession($userSession);
				return 5;//帐号待激活
			};
			$this->ci->session->set_userdata(array("login_info"=>"0"));

			$userSession['open_code']		    = $user['open_codes'];
			$userSession['biz_name']		    = $open_biz['biz_name'];
			$userSession['account']		        = $user['account'];
			$userSession['user_name']	        = $user['name'];
			$userSession['uid']			        = $user['id'];
			$userSession['type']		        = $user['type'];
			$userSession['role_ids']	        = isset($user['role_ids']) ? $user['role_ids'] : null; 
			$userSession['parent_id']	        = isset($user['parent_id']) ? $user['parent_id'] : null; 
			$userSession['attribute']	        = $user['attribute'];
			if($userSession['attribute'] & 1==1){
				$this->_data['islogin'] = 1;
			}else{
				$this->_data['islogin'] = 0;
			}
			
			$role_ids = isset($user['role_ids']) ? $user['role_ids'] : null; 
			$parent_id = isset($user['parent_id']) ? $user['parent_id'] : null;
			
			//查询是否有子帐号，如存在则user_id用","分隔存入$userSession
			//$t_user_ids = $this->ci->user_biz->getUserList(array('parent_id'=>$user['id']));
			//$t_user_ids = $t_user_ids['list'];
			$t_user_ids = array();
			$user_ids = '';			
			if( ! empty($t_user_ids) AND count( $t_user_ids ) > 0){
				$t_ids = array();
				foreach($t_user_ids as $tt_ids){
					$t_ids[] = $tt_ids['id'];
				}
				$user_ids = implode(',', $t_ids);
			}
			$userSession['child_ids'] = $user_ids;
			
			//判断帐号类型是否为消费者
			if($user['type'] == 1){
				$consumer = $this->ci->consumer_biz->getConsumerByUserId($user['id']);
				$userSession['partner_id']		= '';
				$userSession['puser_id']		= '';
				$userSession['partner_name']	= '';
			}else{
				$userSession['partner_name']	= '';
				$userSession['puser_id']		= '';
				$userSession['partner_id']		= '';
			}
			
			//判断帐号类型是否为合伙伙伴
			if($user['type'] == 2){
				//根据当前uid来获得父uid 获得partner信息
				$partner_uid = 0;//指小区物业或遍历点的userid	
				if($parent_id == 0){//合作伙伴登录时判断是否存在子帐号
					$partner_uid = $user['id'];
				}else{
					//存在子帐号
					$islastchild = $this->ci->user_biz->getUserById($user['parent_id']);//获得父uid的user信息
					
					//判断是否还存在子帐号
					if($islastchild['parent_id']==0){
						$partner_uid = $user['id'];
					}else{
						$partner_uid = $islastchild['id'];
					}
				}
				$partner = $this->ci->partner_biz->getPartnerByUserId($partner_uid);
				$userSession['partner_id']		= $partner['id'];
				$userSession['puser_id']		= $partner['user_id'];
				$userSession['partner_name']	= $partner['partner_name'];
				$userSession['spot_code']		= $partner['spot_code'];
			}else{
				$userSession['partner_name']	= '';
				$userSession['puser_id']		= '';
				$userSession['partner_id']		= '';
				$userSession['spot_code']		= '';
			}
			$this->setSession($userSession);
			return 1;
		}else{
			return 3;//密码错误
		}
	}
	function  logout(){
		$this->ci->session->unset_userdata('user_name');
		$this->ci->session->unset_userdata('uid');
		$this->ci->session->unset_userdata('cid');
		$this->ci->session->unset_userdata('session_key');
		$this->ci->session->unset_userdata('login_info');
		$this->ci->session->unset_userdata('attribute');

		if (get_cookie('loginuser'))
		{
			delete_cookie('loginuser');
		}
		if (get_cookie('auth'))
		{
			delete_cookie('auth');
		}

		$this->ci->session->sess_destroy();
	}

	public  function  setSession($cookies){
		$session_data = array(
				'open_code'		=> $cookies['open_code'],
				'biz_name'		=> $cookies['biz_name'],
				'account' 		=> $cookies['account'],
				'uid'	  		=> $cookies['uid'],
				'type'    		=> $cookies['type'],
				'role_ids'  	=> $cookies['role_ids'],
				'partner_id'	=> $cookies['partner_id'],
				'parent_id'		=> $cookies['parent_id'],
				'child_ids'		=> $cookies['child_ids'],
				'puser_id'		=> $cookies['puser_id'],
				'partner_name'	=> $cookies['partner_name'],
				'spot_code'		=> $cookies['spot_code'],
				'attribute'		=> $cookies['attribute'],
				'remember'		=> $cookies['remember']
			);
		//$expi = $cookies['type'] == 3 ? 600 : 2*7*24*60*60;
		//$this->ci->session = new CI_Session(array('sess_expiration' => $expi)); //原生对象可以支持构造参数
		//$this->ci->load->library('session',array('sess_expiration' => 123400));  //Loader 不能很好支持构造参数
		$this->ci->session->set_userdata($session_data); //var_dump($this->ci->session); die;
		$this->ci->load->library('logger');
		$this->login_Log();
	}

	// 添加登录log
	public function login_Log(){
		$this->ci->load->library('logger');
		$data = array();
		$data["date"] = date("Y-m-d H:i:s");
		$data["uid"] = $this->ci->session->userdata("uid");
		$data["ip"] = $this->ci->session->userdata("ip_address");
		$data["locate"] = $this->ci->session->userdata("locate");
		$data["browser"] = strtr($this->ci->session->userdata("user_agent"), array(","=>'-'));
		$this->ci->logger->write('INFO/login/'.date("m"), implode(",", $data), 'login');
	}*/

	//字符串解密加密
/*	private function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

		$ckey_length = 4;	// 随机密钥长度 取值 0-32;
					// 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
					// 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
					// 当此值为 0 时，则不产生随机密钥

		$key = md5($key ? $key : '');
		$keya = md5(substr($key, 0, 16));
		$keyb = md5(substr($key, 16, 16));
		$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

		$cryptkey = $keya.md5($keya.$keyc);
		$key_length = strlen($cryptkey);

		$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
		$string_length = strlen($string);

		$result = '';
		$box = range(0, 255);

		$rndkey = array();
		for($i = 0; $i <= 255; $i++) {
			$rndkey[$i] = ord($cryptkey[$i % $key_length]);
		}

		for($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}

		for($a = $j = $i = 0; $i < $string_length; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}

		if($operation == 'DECODE') {
			if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
				return substr($result, 26);
			} else {
				return '';
			}
		} else {
			return $keyc.str_replace('=', '', base64_encode($result));
		}
	}*/


}


?>
