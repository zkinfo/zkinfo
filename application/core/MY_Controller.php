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
		
		// 维护页面开启/关闭
		$is_weihu = $this->config->item('is_weihu');
		if ($is_weihu == TRUE && $this->router->fetch_class() != 'work' && $this->router->fetch_method() != 'system_maintenance') {
			redirect();
		} else if ($is_weihu == FALSE) {
			// 检查登录状态
			$this->check_auth();
		}
		
		// 获取位置，ip定位;  	#TODO:手机版、智能浏览器将考虑只能定位
		$this->set_locate();

		// 全局回调变量 	#TODO:定义全局数组，整理子类的全局变量
		$this->_data['siteurl'] 	= $this->config->item('base_url');
		$this->_data['imgurl'] 		= $this->config->item('img_url');
		$this->_data['resurl'] 		= $this->config->item('res_url');
		$this->_data['user_name'] 	= $this->session->userdata('user_name');
		$this->_data['partner_name']= $this->session->userdata('partner_name');
		$this->_data['uid'] 		= $this->session->userdata('uid');
		$this->_data["date"] 		= date('Y-m-d H:i:s');

		// 请求回调变量
		$this->_data['get'] 		= $this->input->get();
		$this->_data['__get'] 		= $this->input->get();
		$this->_data['post'] 		= $this->input->post();
		$this->_data['request'] 	= $this->request();
		$this->_data['cookie'] 		= $this->session->userdata;
		$this->_data['session'] 	= & $this->_data['cookie'];
		$this->_data['server'] 		= $_SERVER;

		// 分页回调变量
		$this->_data['paging'] 		= array(
			'page' 		=> $this->input->get_post('page') < 1 ? 1 : 
						   $this->input->get_post('page'),
			'perpage' 	=> 10
		);

		// js和css预定义变量
		$this->_data['styles'] = "";
		$this->_data['scripts'] = "";

		// 帐号手机重复 	#TODO@scropio: Layout那边重复代码干掉]
		if ( $this->session->userdata('login_info' ) ) {
			$this->_data['login_info'] = "你的帐号绑定了多个手机，请重新绑定";
		}else{
			$this->_data['login_info'] = "";
		}

		// 非ajax操作才执行菜单控制 **警告：不要乱动这段代码位置啊 By Nation
		if (!$this->input->is_ajax_request()) {
			$loadedData = $this->mi_common->create_app_and_menu();
			$this->_data = array_merge($this->_data, $loadedData);
		}

		// 史前的东西，不知道干什么滴
		$sections = array(
			'benchmarks'		=> FALSE,
			'config'			=> FALSE,
			'controller_info'	=> FALSE,
			'get'				=> FALSE,
			'http_headers'		=> FALSE,
			'memory_usage'		=> FALSE,
			'post'				=> FALSE,
			'queries'			=> TRUE,
			'uri_string'		=> FALSE
		);
		// 史前的东西，config log_threshold = 2 的时候，显示Debug信息
		if ($this->config->item('log_threshold') == 2) {
			$this->output->set_profiler_sections($sections);
			$this->output->enable_profiler(TRUE);
		}
		
		$this->_data['role_ids'] = $this->session->userdata('role_ids');
	}

	/**
	 * 计算分页
	 */
	protected function pagingExec()
	{
		$page = $this->_data['paging']['page'];
		$perpage = $this->_data['paging']['perpage'];
		$start = ($page - 1) * $perpage;
		$count = $this->_data['paging']['count'];
		ckstart($start, $perpage);
		$url = $this->_data['server']['url'] = current_url();
		$request = $this->_data['request'];
		if (isset($request['page'])) unset($request['page']);
		$query = $this->_data['server']['query'] = $url.'?'.geturl($request);
		$this->_data['paging']['multi'] = str_replace('??','?',multi($count, $perpage, $page, $query));
	}

	/**
	 * 合并GET, POST请求数组
	 */
	private function request()
	{
		if ( $this->input->get() ) {
			if ( $this->input->post() ) {
				return array_merge($this->input->get(), $this->input->post());
			} else {
				return $this->input->get();
			}
		} else {
			return $this->input->post();
		}
	}

	/**
	 * 登录验证
	 */
    private function check_auth($userid=0)
    {
    	// 域名白名单，现在绑定3个：正式域名，线上域名，开发时域名 	[TODO:使用标准配置函数，统一管理]
		$allow_domain = array('500mi.com', '500mi.org');
		if ( ! in_array(substr($_SERVER['SERVER_NAME'],-9), $allow_domain) ) {
			redirect('http://passport.500mi.com/login');
			// [TODO:前后台、各个应用使用标准配置，而且互相兼容、冗余存储]
			return false;
		}
		// 登录验证的具体实现
		if ( ! $this->auth->check_auth() ) {
			redirect('http://passport.500mi.com/login', 'refresh');
		}

		// 检查ACL权限
		$this->load->library('acl');
        $this->_class = $this->router->fetch_class();
        $this->_method = $this->router->fetch_method(); // [TODO:类和方法名，放入控制器全局变量]        
        $url = substr($this->router->fetch_directory(),10).$this->_class.'/'.$this->_method;
        // var_dump($url);
        // 非法->跳转
		if( ! $this->acl->check_acl($url) ) {
			die('permission denied. <br />无权限···'); //permission denied.
			redirect($_SERVER['HTTP_REFERER'], 'refresh');
			//echo '<script>window.location.href="/work/help/showmessage";</script>';
			return;
		}
    }

    /**
     * 设置用户所在省市
     */
    private function set_locate()
    {
    	$locate = $this->session->userdata('locate');
    	$province = "浙江";
    	$city = "杭州";
    	if (!$locate)
    	{
    		require APPPATH."third_party/ip/ip.php";	
    		$ip = $_SERVER["REMOTE_ADDR"];
    		$idADDR = new IpLocation();
    		$res = $idADDR->getlocation($ip);
    		$data = eval('return '.iconv('gbk','utf-8',var_export($res,true)).';');
    		$flag = stripos($data["country"],"省");
    		
    		if ($flag) {
    			$tmp = explode("省", $data["country"]);
    			$province = $tmp[0];
    			$list = explode("市",$tmp[1]);
    			$city = $list[0];
    		}
    		
    		$locate = $province."-".$city;
    		$this->session->set_userdata('locate', $locate);
    	}

    	// 获取城市ID
    	$area = $this->session->userdata('area');
    	if (!$area) {
    		$this->load->helper('andor');
    		$this->session->set_userdata('area', 1);
    		//$this->session->set_userdata('area', getCityIdByTitle($city));
    	}
    	
    	return $locate;
    }
}

/* End of file: MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */