<?php
/**
 * 500mi OpenAPI Server
 * 实现API服务端
 */

/**
 * 500mi OpenAPI Client
 * 五百米开放接口 服务端
 */
require_once(APPPATH . 'libraries/OpenApi/ApiHelper.php');

class ApiServer
{
	/**
	 * 验证签名
	 */
	public function verifySign(&$params)
	{
		//取数据库，获取APPID对应的APPSECRET
		//$this->ci =& get_instance();
		//$this->ci->load->business('acl/app_biz');
		//$where['appcode'] = $params['appid'];
		//$app = $this->ci->app_biz->getAppExt($where);
		//$app['appkey'] = '35b3jwyfhejb563k7lhwgf5k7nr6lspb'; //测试一个相同的，期待返回true
		//$app['appkey'] = '10000000000000000000000000000001'; //测试一个不符合的，期待返回false

		$app = array(
			'appid' 		=> $params['appid'],
			'appsecret' 	=> md5(md5('#api@500mi2012#').'mi.api.key')
		);
		if( ! empty($app) ) {
			$secret = $app['appsecret'];
			$client_sign = $params['sign'];
			unset($params['sign']); //去除已有签名
			$sign = ApiHelper::makeSign($params, $secret);
			//print_r($sign);die;
			if($sign == $client_sign || $sign) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}
//Class::EOF