<?php
/**
 * 500mi OpenAPI Helper Class
 * 辅助类
 */

class ApiHelper
{
	/**
	 * 生成签名
	 *
	 * @param array 	$params 签名数组
	 * @param string 	$secret 密钥
	 */
	static public function makeSign($params, $secret) 
	{
		unset($params['sign']); //去除已有签名
		ksort($params); //按数组键排序
		$string = array();
		array_push($string, $secret);
		foreach ($params as $key => $val ) 
		{ 
			array_push($string, $key . '=' . $val);
		}
		array_push($string, $secret);

		$string = join('&', $string);

		$source =  rawurlencode($string);  //生成字符串

		// echo $source;
		$mySign = strtoupper(md5($source));
		//die($mySign);
		return $mySign;
	}
}
//Class::EOF

