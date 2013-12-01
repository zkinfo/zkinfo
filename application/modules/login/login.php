<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	function __construct()
	{
		parent::__construct();
		$this->ci =& get_instance();
		$this->ci->load->library('session');
		$this->ci->load->helper('cookie');
		$this->load->helper('url');
		$this->load->helper('array');
		$this->load->helper('view');
		$this->load->business('user/user_biz');
		$this->load->library('layout','layouts/login/layout_login');
	}

	public function index()
	{
		$this->layout->template('index');
	}

	public function auth()
	{		
		$account = $this->input->post('account');
		$password = $this->input->post('password');
		$params['account'] = $account;
		$user = $this->user_biz->getUser($params);
		if($user['password'] == md5(md5($password)) or $user['password'] == $password)
		{
			$_params = array();
			$_params['last_ip'] = $this->ci->input->ip_address();
			$_params['last_time'] = date("Y-m-d h:m:s");
			//var_dump($_params)
			$this->ci->user_biz->updateUser($_params, $user["id"]);
/*
			if ($user['status'] == 1)
			{
				$userSession['uid'] = $user['id'];
				$userSession['mobile'] = $user['mobile'];
				$this->setSession($userSession);
				return 5;//帐号待激活
			};*/
			//var_dump($this->ci->session->set_userdata(array("login_info"=>"0")));
			$this->ci->session->set_userdata(array("login_info"=>"0"));
			$userSession['account']		        = $user['account'];
			$userSession['user_name']	        = $user['user_name'];
			$userSession['user_id']			    = $user['id'];
			$userSession['role_id']	            = isset($user['role_id']) ? $user['role_id'] : null; 
			$userSession['attribute']	        = $user['attribute'];
			if($userSession['attribute'] & 1==1){
				$this->_data['islogin'] = 1;
			}else{
				$this->_data['islogin'] = 0;
			}
			$this->setSession($userSession);
			//var_dump($this->session->all_userdata());
			echo '1';
		}else{
			echo '3';//密码错误
		}
	}

	public  function  setSession($cookies){
		$session_data = array(
				'account' 		=> $cookies['account'],
				'uid'	  		=> $cookies['user_id'],
				'user_name'     => $cookies['user_name'],
				'role_id'  	    => $cookies['role_id'],
				'attribute'		=> $cookies['attribute'],
			);
		//$expi = $cookies['type'] == 3 ? 600 : 2*7*24*60*60;
		//$this->ci->session = new CI_Session(array('sess_expiration' => $expi)); //原生对象可以支持构造参数
		//$this->ci->load->library('session',array('sess_expiration' => 123400));  //Loader 不能很好支持构造参数
		$this->ci->session->set_userdata($session_data); //var_dump($this->ci->session); die;
		//$this->ci->load->library('logger');
		//$this->login_Log();
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */