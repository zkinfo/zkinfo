<?php
/**
 * @author jetlee remark:danchex
 *--------------------------------------------------------------------------
 * 短信发送类 - 发送网关
 *--------------------------------------------------------------------------
 * 在这里调用短信通道，真正发送给用户手机
 * 异步通道商的回调，得到发送状态（成功、失败）
 *
 */
class Smslib
{
	public function __construct()
	{
		$this->ci =& get_instance();
		$this->config = $this->ci->config;
		$this->ci->load->library('logger');
	}

	/**
	 * 短信发送网关  Sina
	 * Enter description here ...
	 * @param int $mobile
	 * @param string $content
	 * @param int $smsid
	 */
	public function Pica_sendSMS($mobile, $content, $smsid = 0)
	{					
		require_once(APPPATH.'./third_party/sms/pica.php');
		$sms = new Pica();
		$content = rtrim($content, '【500mi】') . '【500mi】';
		$send = $sms->send($mobile, $content, $smsid);
		if ( ! $send['error']) {
			return $send;
		} else {			
			return $send;
		}
	}

	/**
	 * 短信发送网关  Sina
	 * Enter description here ...
	 * @param int $mobile
	 * @param string $content
	 * @param int $smsid
	 */
	public function _sendSMS($mobile, $content, $smsid = 0)
	{
		$send = $this->Pica_sendSMS($mobile, $content, $smsid);
		if ( ! $send['error']) {
			return $send;
		} else {			
			return $this->PHPCMS_sendSMS($mobile, $content, $smsid);
		}
	}
	
	/**
	 * 短信发送网关  Sina
	 * Enter description here ...
	 * @param int $mobile
	 * @param string $content
	 * @param int $smsid
	 */
	public function Sina_sendSMS($mobile, $content, $smsid = 0)
	{		
		require_once(APPPATH.'./third_party/sms/sina.php');
		$sms = new Sina();
		$content = rtrim($content, '【500mi】') . '【500mi】';
		$send = $sms->send($mobile, $content, $smsid);
		if ( ! $send['error']) {
			return $send;
		} else {			
			//return $this->Office_sendSMS($mobile, $content, $smsid);
			return $send;
		}
	}
	
	/**
	 * 短信发送网关  PHPCMS
	 * Enter description here ...
	 * @param int $mobile
	 * @param string $content
	 * @param int $smsid
	 */
	public function PHPCMS_sendSMS($mobile, $content, $smsid = 0)
	{		
		require_once(APPPATH.'./third_party/sms/phpcms.php');
		$sms = new Phpcms();
		$content = rtrim($content, '【500mi】') . '【500mi】';
		$send = $sms->send($mobile, $content, $smsid);
		if ( ! $send['error']) {
			return $send;
		} else {			
			//return $this->Office_sendSMS($mobile, $content, $smsid);
			return $send;
		}
	}
}
//Class::EOF