<?php	if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mi_common {
	var $ci;

	function __construct()
	{
		$this->ci =& get_instance();
		$this->_class = $this->ci->router->fetch_class();
		$this->_method = $this->ci->router->fetch_method();
	}

	function create_app_and_menu()
	{
		$islogin = $this->ci->session->userdata('uid') > 0 ? TRUE : FALSE;
		$roleids = array();
		$loadedData = array();
		$user_id = 0;
		//获得当前页面的url地址：类似 /work/tenement/upexcel
		//var_dump($_SERVER);die;
		$this->ci->load->helper('url');
		$this->ci->load->library('session');
		$this->ci->load->business("cms/articles");
		$this->ci->load->business("cms/article_cates");
		$this->ci->load->business("item/category_biz");
		$this->ci->load->library('session');
		
		$url = '/'.str_replace('/index','',str_replace('../modules/','',$this->ci->router->fetch_directory()).$this->_class.'/'.$this->_method);
		$loadedData['nowurl'] =& $url;
		$loadedData['date'] = date('Y-m-d H:i:s');

		$loadedData['module'] 	= $this->ci->router->fetch_module();
		$loadedData['method'] 	= $this->ci->router->fetch_method();
		$loadedData['class'] 	= $this->ci->router->fetch_class();

		//取用户信息
		if($islogin){
			//公告
			// $cate = $this->ci->article_cates->getArticleCate(array('title'=>'系统信息'));
			// if($cate){
			//	 $loadedData["cate_id"] = $cate_id = $cate["id"];
			//	 $loadedData["article"] = $this->ci->articles->getArticleList(array('cate_id'=>$cate_id,"status"=>"9"));
			//	 $loadedData["cate_num"] = count($loadedData["article"]);
			// }else{
			//	 $loadedData["article"] = array();
			//	 $loadedData["cate_num"] = 0;
			// }
			//提示信息
			$login_info = $this->ci->session->userdata('login_info');
			if($login_info){
				// $loadedData['login_info'] = "你的帐号绑定了多个手机，请重新绑定";
				$loadedData['login_info'] = "";
			}else{
				$loadedData['login_info'] = "";
			}
			$client_ip = $this->ci->input->ip_address();
			$server_ip = $this->ci->input->server('SERVER_ADDR');

			$environment = '';
			// if ($server_ip == '60.190.240.74') {
			// 	$environment = '线上测试';
			// } else if (strpos($server_ip, '192.168') !== false) {
			// 	$environment = '线下测试';
			// } else if ($server_ip == '127.0.0.1') {
			// 	$environment = '开发版';
			// }

			$loadedData['supe_username']	= $this->ci->session->userdata('account');
			$loadedData['partner_name']		= $this->ci->session->userdata('partner_name');
			$loadedData['supe_uid']			= $this->ci->session->userdata('uid');
			$loadedData['ip']				= $client_ip;
			$loadedData['environment']		= $environment;
			$loadedData['code_env']			= ENVIRONMENT;		
			$loadedData['machine_name']		= $this->ci->config->item('machine_name');			
			$loadedData['dbname']		= $this->ci->db->database;
			$loadedData['dbserver']		= $this->ci->db->hostname;
			// if (ENVIRONMENT == 'development') {
			// 	$loadedData['branch']		= @file_get_contents(APPPATH . '../.git/HEAD');
			// 	$loadedData['branch']		= trim($loadedData['branch']);
			// 	$loadedData['branch']		= substr($loadedData['branch'], strrpos($loadedData['branch'],'/')+1);
			// } else {
			// 	$loadedData['branch']		= 'release';
			// }
			$loadedData['javaapiurl']		= array();
			$javaapiurl = $this->ci->config->item('javaAPI_url');
			foreach ($javaapiurl as $i => $u) {
				$loadedData['javaapiurl'][] = substr($u, 7, strpos($u, '/', 7)-7);
			}
			$loadedData['javaapiurl'][] = substr($this->ci->config->item('es_url'), 7, strpos($this->ci->config->item('es_url'), '/', 7)-7);
			$item = $this->ci->config->item('default');
			$loadedData['javaapiurl'][] = $item['host'].':'.$item['port'];
			$roleids = explode(',', $this->ci->session->userdata('role_ids'));
			$user_id = $this->ci->session->userdata('uid');

			$this->ci->load->business('acl/app_biz');
			$this->ci->load->business('acl/menu_biz');
			$this->ci->load->business('acl/resource_biz');
			require_once('Acl.php');
			$this->ci->acl = new Acl($type = '0');

			//获得app列表 && 主菜单
			$userapps = $this->ci->app_biz->loadApp($roleids, $url);

			// 用户APP & Menus
			$loadedData['userapps'] =& $userapps;
			$loadedData['userappcount'] = count($userapps);

			$loadedData['session'] = $this->ci->session;
		} else {
			$loadedData['ip'] = '';
			$loadedData['environment'] = '';
			$loadedData['userapps'] = array();
			$loadedData['userappcount'] = 0;
			
			$loadedData['session'] = $this->ci->session;
		}
		
		// 系统广告
		$this->ci->load->business('cms/articles');
		$articles = $this->ci->articles->getArticle(array('id' => 33));
		$loadedData['public_notice'] = $articles['content'];

		$articles = $this->ci->articles->getArticle(array('id' => 40));
		$loadedData['provide_notice'] = @$articles['content'];
		$loadedData['server_ip'] = $_SERVER['SERVER_ADDR']; 
		$cateTreeTmp = $this->ci->category_biz->getCategoryTree();
		$cateTree = array();
		foreach ($cateTreeTmp as $key => & $value) {
			if(($value['attribute'] & 1024) == 1024 && $value['status'] == 1 && $value['name'] != '进口食品'){
				array_push($cateTree, $value);
			}
		}

		$loadedData['cateTree'] = & $cateTree;

		$loadedData['mycash_menus'] = $this->get_mycash_menus();

		return $loadedData;
	}

	private function get_mycash_menus()
	{
		$this->ci->load->library('Acl');
		$list = array(
			array('url'=>'/finance/mycash','cname'=>'我的账户'),
			array('url'=>'/finance/mycash/accountDetail','cname'=>'账户明细'),
			array('url'=>'/finance/mycash/sellDetail','cname'=>'销售明细'),
			array('url'=>'/finance/mycash/topay','cname'=>'账户充值'),
			array('url'=>'/finance/mycash/getcash','cname'=>'申请提现'),
			array('url'=>'/finance/mycash/paylist','cname'=>'充提记录'),
			array('url'=>'/shop/topay/trade_pay_list','cname'=>'交易记录')
		);

		return $this->ci->acl->acl_load($list);
	}
}
?>
