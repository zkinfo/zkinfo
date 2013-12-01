<?php
/**
 * 500mi OpenAPI Client
 * 实现客户端API连接
 *
 */

require_once(APPPATH . 'libraries/OpenApi/ApiConfig.php');
require_once(APPPATH . 'libraries/OpenApi/ApiHelper.php');
require_once(APPPATH . 'libraries/OpenApi/ApiRequest.php');

/**
 * 如果您的 PHP 没有安装 cURL 扩展，请先安装
 */
if (!function_exists('curl_init'))
{
	throw new Exception('OpenAPI needs the cURL PHP extension.');
}

/**
 * 如果您的 PHP 不支持JSON，请升级到 PHP 5.2.x 以上版本
 */
if (!function_exists('json_decode'))
{
	throw new Exception('OpenAPI needs the JSON PHP extension.');
}

/**
 * 错误码定义
 */
define('OPENAPI_ERROR_REQUIRED_PARAMETER_EMPTY', 2001); // 参数为空
define('OPENAPI_ERROR_REQUIRED_PARAMETER_INVALID', 2002); // 参数格式错误
define('OPENAPI_ERROR_RESPONSE_DATA_INVALID', 2003); // 返回包格式错误
define('OPENAPI_ERROR_CURL', 3000); // 网络错误, 偏移量3000, 详见 http://curl.haxx.se/libcurl/c/libcurl-errors.html

/**
 * 500mi OpenAPI Client
 * 五百米开放接口 客户端
 */
class ApiClient
{
	//这里是私有属性，同时也是一个config配置中心
	private $appid		 = '500';
	private $appkey		 = '34aee295a97053a348644263217e2e5b';
	private $device_id 	 = '344c48a18b9681e19827ed53f4e7b2d8';
	private $server_name = 'work.500mi.com';
	private $request_file= '/api/miapi';
	private $method		 = 'post';
	private $format		 = 'json';
	private $protocol	 = 'http';

	/**
	 * 构造函数
	 *
	 * @param int $appid 应用的ID
	 * @param string $appkey 应用的密钥
	 */
	function __construct()
	{
		//构造函数, 先留空
	}

	public function setServerName($server_name)
	{
		$this->server_name = $server_name;
	}

	/**
	 * 执行API调用，返回结果数组
	 *
	 * @param array $api_name 调用的API方法
	 * @param array $params 调用API时带的参数
	 * @param string $method 请求方法 post / get
	 * @param string $protocol 协议类型 http / https
	 * @return array 结果数组
	 */
	public function request($params)
	{
		// 检查 api_name
		if (!isset($params['api']) || empty($params['api']))
		{
			return array(
				'ret' => OPENAPI_ERROR_REQUIRED_PARAMETER_EMPTY,
				'msg' => 'api_name is empty'
			);
		}

		// 无需显式签名, 程序自动生成
		unset($params['sign']);

		// 添加一些参数
		$params['appid'] = $this->appid;
		$params['device_id'] = $this->device_id;
		! isset($params['method']) && $params['method'] = $this->method;
		! isset($params['format']) && $params['format'] = $this->format;
		! isset($params['request_time']) && $params['request_time'] = date('YmdHis');

		// 生成签名
		$secret = $this->appkey;
		$sign = ApiHelper::makeSign($params, $secret);
		$params['sign'] = $sign;
		$protocol = 'http';

		$url = $this->protocol . '://' . $this->server_name . $this->request_file ;
		$cookie = array();

		// 发起请求
		$ret = ApiRequest::makeRequest($url, $params, $cookie, $this->method, $this->protocol);

		if (false === $ret['result'])
		{
			return array(
				'ret' => OPENAPI_ERROR_CURL + $ret['errno'],
				'msg' => $ret['msg'],
			);
		}
		$result_array = json_decode($ret['msg'], true);

		// 远程返回的不是 json 格式, 说明返回包有问题
		if (is_null($result_array)) {
			return array(
				'ret' => OPENAPI_ERROR_RESPONSE_DATA_INVALID,
				'msg' => $ret['msg']);
		}
		return $result_array;
	}

}

// end of script
