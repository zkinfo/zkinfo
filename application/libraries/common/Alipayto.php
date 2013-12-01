<?php 

include_once(APPPATH.'./third_party/alipay/alipay_service.class.php');

class Alipayto
{
	function directpay($pay_param)
	{
		include_once(APPPATH.'./third_party/alipay/alipay.config.php');
		
		
		//构造要请求的参数数组
		$parameter = array(
				"service"			=> "create_direct_pay_by_user",
				"payment_type"		=> "1",
				
				"partner"			=> trim($aliapy_config['partner']),
				"_input_charset"	=> trim(strtolower($aliapy_config['input_charset'])),
		        "seller_email"		=> trim($aliapy_config['seller_email']),
		        "return_url"		=> trim($aliapy_config['return_url']),
		        "notify_url"		=> trim($aliapy_config['notify_url']),
				
				"out_trade_no"		=> $pay_param['out_trade_no'],
				"subject"			=> $pay_param['subject'],
				"body"				=> $pay_param['body'],
				"total_fee"			=> $pay_param['total_fee'],
				
				"paymethod"			=> $pay_param['paymethod'],
				"defaultbank"		=> $pay_param['defaultbank'],
				
				"anti_phishing_key"	=> $pay_param['anti_phishing_key'],
				"exter_invoke_ip"	=> $pay_param['exter_invoke_ip'],
				
				"show_url"			=> $pay_param['show_url'],
				"extra_common_param"=> $pay_param['extra_common_param'],
				
				"royalty_type"		=> $pay_param['royalty_type'],
				"royalty_parameters"=> $pay_param['royalty_parameters']
		);
		
		//构造即时到帐接口
		$alipayService = new AlipayService($aliapy_config);
		$html_text = $alipayService->create_direct_pay_by_user($parameter);
		return  $html_text;
	}
}
