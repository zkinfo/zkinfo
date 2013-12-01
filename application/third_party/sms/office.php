<?php

/******************************************************************************
 * 第二办公室 短信通道 
 *
 ******************************************************************************/

require_once(APPPATH.'./third_party/sms/Interface.php');

Class Office implements Sms_Interface
{
	public function send($mobile, $content, $smsid)
	{
		require_once(APPPATH.'./third_party/sms/nusoap.php');
		$client = new nusoap_client("http://sms.2office.net/WebService/services/SmsService?wsdl", true);
		//设置编码，为GBK，以下三个设置不可缺少
		$client->soap_defencoding = 'GBK';
		$client->decode_utf8 = false;
		$client->xml_encoding = 'GBK';

		$ret = array();

		$err = $client->getError();
		if ($err) {
			$ret['error'] 		= 1;
			$ret['error_msg'] 	= '短信发送失败，连接通道失败';
			$ret['remark'] 		= $err;
			return $ret;
		}

		$content = iconv("utf-8", "GBK//IGNORE", $content);
		$password = md5("yesyes" . "cd694d6e8ae2987470e651e37688809d");

		$param = array(
				'account'	=> '2513816', 	//帐号
				'password'	=> $password, 	//密码
				'mobile'	=> $mobile, 	//发送手机
				'content'	=> $content, 	//发送内容
				'channel'	=> '2513816', 	//通道编号
				'smsid'		=> $smsid, 		//发送ID
				'sendType'	=> '1' 			//发送类别，意思不详
		);

		$result = $client->call('SendSms3', array('parameters' => $param), '', '', false, true, 'document', 'encoded');
		
		if ( strpos($result['out'], '成功') !== false ) {
		    //$this->ci->logger->log(6, $rs . ' : ' .  $mobile . ' : ' .  $content, 'sms-success');		
			$ret['error'] 		= 0;
			$ret['error_msg'] 	= '短信发送成功';
			$ret['remark'] 		= $result['out'];
		} else {
		    //$this->ci->logger->log(6, $rs . ' : ' .  $mobile . ' : ' .  $content, 'sms-error');
			$ret['error'] 		= 1;
			$ret['error_msg'] 	= '短信发送失败';
			$ret['remark'] 		= $result['out'];
		}
		return $ret;
	}
}
//Class::EOF