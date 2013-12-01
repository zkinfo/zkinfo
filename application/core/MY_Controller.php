<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/* The MX_Controller class is autoloaded as required */

class MY_Controller extends CI_Controller {
	/*
	 * 模板上下文
	 * danchex 注：默认变量，可在所有控制器中调用 $this->_data.
	 * 模板中使用的变量应该总是放入该变量.
	 * 除了全局变量，局部数组，对象都应该放入一个子索引 
	 * 如: 
	 *     $this->_data['siteurl'] 	= 'http://work.500mi.com' 	//全局的
	 *     $this->_data['list'] 	= array() 					//局部的
	 *     $this->_data['account'] 	= array()  					//局部的
	 */
	protected $_data = array();
	
	function __construct()
	{
		parent::__construct();
		$this->load->library('mi_common');
		$this->load->library('session');
		$this->load->library('auth');
		$this->load->helper('url');
		$this->load->helper('array');
		$this->load->helper('view');
		$is_weihu = $this->config->item('is_weihu');
		if ($is_weihu == TRUE && $this->router->fetch_class() != 'work' && $this->router->fetch_method() != 'system_maintenance') {
			redirect();
		} else if ($is_weihu == FALSE) {
			// 检查登录状态
			$this->check_auth();
		}
		$this->_data['role_id'] = $this->session->userdata('role_id');
	}

	/**
	 * 登录验证
	 */
    private function check_auth($userid=0)
    {
    	// 域名白名单，现在绑定3个：正式域名，线上域名，开发时域名 	[TODO:使用标准配置函数，统一管理]
		$allow_domain = array('tlcjw.com');
		if ( ! in_array(substr($_SERVER['SERVER_NAME'],-9), $allow_domain) ) {
			redirect('http://www.tlcjw.com/login');
			// [TODO:前后台、各个应用使用标准配置，而且互相兼容、冗余存储]
			return false;
		}
		// 登录验证的具体实现
		if ( ! $this->auth->check_auth() ) {
			redirect('http://www.tlcjw.com/login', 'refresh');
		} else {
			redirect('http://www.tlcjw.com/work', 'refresh');
		}

		/*// 检查ACL权限
		$this->load->library('acl');
        $this->_class = $this->router->fetch_class();
        $this->_method = $this->router->fetch_method(); // [TODO:类和方法名，放入控制器全局变量] 
        //var_dump($this->router->fetch_directory());      
        $url = '/'.str_replace('/index','',str_replace('../modules/','',$this->router->fetch_directory()).$this->_class.'/'.$this->_method);
        //$url = $this->uri->ruri_string();
        //var_dump($url);
        // 非法->跳转
		if( ! $this->acl->isAllowedUrl($url) ) {
			die('error:101<br /> permission denied. <br />无权限···'); //permission denied.
			redirect($_SERVER['HTTP_REFERER'], 'refresh');
			//echo '<script>window.location.href="/work/help/showmessage";</script>';
			return;
		} else {
			return true;
		}*/
		return true;
    }
}

/* End of file: MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */