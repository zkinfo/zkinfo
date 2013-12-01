<?php

/******************************************************************************
 * Phpcms 短信通道 
 * sms.phpcms.cn
 * misms@500mi.com
 *
 ******************************************************************************/

require_once(APPPATH.'./third_party/sms/Interface.php');

Class Phpcms implements Sms_Interface
{
	private $sms_uid 	= '232556';
	private $sms_pid 	= '2333';
	private $sms_passwd = 'misms2012';
	private $url 		= "http://sms.phpcms.cn/api.php";

	/**
	 * 发送短信
	 */
	public function send($mobile, $content, $smsid = 0)
	{
		if ( is_array($mobile) ) {
			foreach ($mobile as $one_mobile) {
				$this->send($one_mobile, $content);
			}
		}

		$data = array(
			"op"			=> 'sms_service',
			"sms_uid"		=> $this->sms_uid,
			"sms_pid"		=> $this->sms_pid,
			"sms_passwd"	=> $this->sms_passwd,
			"mobile" 		=> $mobile,
			"send_txt" 		=> urlencode(iconv('utf-8','GBK',$content)),
			"charset" 		=> 'GBK'
		);

		$params = array();
		foreach ($data as $key => $val) {
			$params[$key] = $key . '=' . $val; 
		}
		$params = implode('&', $params);

		$rs = file_get_contents('http://sms.phpcms.cn/api.php?'.$params);

		$ret = array();
		if ( strpos($rs, '0#') !== false ) {
		    //$this->ci->logger->log(6, $rs . ' : ' .  $mobile . ' : ' .  $content, 'sms-success');		
			$ret['error'] 		= 0;
			$ret['error_msg'] 	= '短信发送成功';
			$ret['remark'] 		= $rs;
		} else {
		    //$this->ci->logger->log(6, $rs . ' : ' .  $mobile . ' : ' .  $content, 'sms-error');
			$ret['error'] 		= 1;
			$ret['error_msg'] 	= '短信发送失败';
			$ret['remark'] 		= $rs;
		}
		return $ret;
	}

	/**
	 * 获取短信余额
	 */
	public function surplus()
	{
		$url = 'http://sms.phpcms.cn/api.php?op=sms_get_info&sms_uid='.$this->sms_uid.'&sms_pid='.$this->sms_pid.'&sms_passwd='.$this->sms_passwd;
		$rs = file_get_contents($url);
		$result = json_decode($rs, true);
		return $result['surplus'];
	}
}
//Class::EOF