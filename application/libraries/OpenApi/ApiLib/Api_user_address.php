<?php
/**
 * 500mi OpenAPI libirary
 * 用户操作相关api
 * 传入参数 $params 
 * 返回值 $ret
 */
require_once(APPPATH . 'libraries/OpenApi/ApiErrInfo.php');//加载错误信息配置文件
class Api_user_address
{
	public function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->business('user/consumer_biz');
		$this->ci->load->business('user/consumer_address_biz');
	}

	/**
	 * 获取收货地址
	 */
	public function index()
	{
		//$this->get($params);
	}

	public function get($params)
	{
		$address = $this->ci->consumer_address_biz->getAddressList( array('user_id' => $params['user_id']) );
		foreach ($address as $key => $value) {
			$address[$key]['address_id'] = $address[$key]['id'];
			unset($address[$key]['id']);
			foreach ($value as $k => $v) {
				if ( ! in_array($k, array('address_id','user_id','user_name','mobile','spot_code','lc_code','building','address')) ) {
					unset($address[$key][$k]);
				}
			}
		}
		return array(
			'error' 	=> 0,
			'error_msg' => 'Get success',
			'data' 		=> array(
				'address' => $address
			)
		);
	}

	/**
	 * 添加收货地址
	 */
	public function add($params)
	{
		
	}

}