<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Mobile_Controller extends CI_Controller {
	
	protected $_data = array();
	
	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');
		$this->load->helper('view');
		$this->load->library('auth');
		$this->load->helper('array');
		// 检查是否登录
		$this->check_auth(); 
	}

	/**
	 * 登录验证
	 */
	private function check_auth($userid=0)
	{
		// 登录验证的具体实现
		if ( ! $this->auth->check_auth() ) {
			redirect('http://passport.500mi.com/login', 'refresh');
		}
    }
}