<?php
/**
 * 500mi OpenAPI libirary
 * 用户操作相关api
 * 传入参数 $param 
 * 返回值 $ret
 */
require_once(APPPATH . 'libraries/OpenApi/ApiErrInfo.php');//加载错误信息配置文件
class Api_device
{
	public function __construct()
	{
		$this->ci =& get_instance();
	}

	private function keyEncode()
	{
		return md5(md5('#api@500mi2012#').'mi.api.key');
		//34aee295a97053a348644263217e2e5b
	}

	/**
	 *  设备授权，成功返回设备的授权码
	 */
	public function auth($request)
	{
		$this->ci->load->model('device_model');
		// $request = $this->request();
		// $request = array(
		// 	'appid' 	=> '500',
		// 	'device_id' => '5001',
		// 	'auth_time' => '20120910180000',
		// 	'sign' 		=> '4f1b828dc5c4aebd3f93d67d893db2a2'
		// );
		//29888C19F91B6966AC233EC8262573A8
		//die($this->keyEncode());
		if ( ! isset($request['appid'],$request['device_id']) ) {
			return array(
				'error' 	=> 1,
				'error_msg' => 'params error'
			);
		}

		//数据库操作

		$isDevice = $this->ci->device_model->getOne( array('appid' => $request['appid'], 'device_id' => $request['device_id']) );
		if ( empty($isDevice) ) {
			$device = array(
				'appid' 		=> $request['appid'], 
				'device_id' 	=> $request['device_id'],
				'mobile' 		=> isset($request['mobile']) ? $request['mobile'] : null,
				'auth_code' 	=> md5($sign . 'mi.api.auth'),
				'count' 		=> 1
			);
			if ( $this->ci->device_model->insert($device) ) {
				return array(
					'error' 	=> 0,
					'error_msg' => 'auth success',
					'data'		=> array('auth_code' => $device['auth_code'])
					//4f1b828dc5c4aebd3f93d67d893db2a2
				);					
			} else {
				return array(
					'error' 	=> 1,
					'error_msg' => 'auth save failed , please try again'
				);	
			}
		} else {
			//更新设备请求次数
			$device = array(
				'count' => $isDevice['count'] + 1 
			);
			$this->ci->device_model->update( $device, $isDevice['id'] );
			return array(
				'error' 	=> 0,
				'error_msg' => 'auth success',
				'data'		=> array('auth_code' => $isDevice['auth_code'])
			);
		}
	}

}