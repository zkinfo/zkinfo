<?php

require_once APPPATH . 'third_party/HTTPRequest/HTTPRequest.php';

class Pica 
{
	protected $protocol = 'http';
	protected $host 	= 'sms.pica.com';
	protected $uri 		= '/zqhdServer/sendSMS.jsp';
	protected $port 	= 8082;

	protected $username = 'ZXHD-CRM-0100-ZLKFCU'; 		//通道帐号
	protected $password = '28197249'; 		//通道密码
	protected $mobile; 				//目标手机号码，逗号分隔
	protected $mobileLimit; 		//最多同时发送手机数量
	protected $content; 			//发送内容
	protected $contentLimit; 		//字数限制

	protected $error;
	protected $error_msg;

	//查询参数
	protected $query = array();

	//返回值
	protected $returns = array(
		'0' 	=> '成功',
		'-1' 	=> '用户名或密码不正确',
		'-2'	=> '余额不够',
		'-3'	=> '帐号没有注册',
		'-4'	=> '内容超长',
		'-5'	=> '账号路由为空',
		'-6'	=> '手机号码大于100个',
		'-8'	=> '扩展号超长',
		'-13'	=> '定时时间错误或者小于当前系统时间',
		'-17'	=> '手机号码为空',
		'-19'	=> '短信内容为空',
		'-100' 	=> '其它故障'
	);
	

	/**
	 * 构造通道
	 */
	public function __construct() {
		$this->query = array(
			'regcode' 		=> $this->username,
			'pwd' 			=> md5($this->password),
			'phone' 		=> '',
			'content' 		=> '',
			'extnum' 		=> 11,
			'level' 		=> 1,
			'schtime' 		=> null,
			'reportflag' 	=> 1,
			'smstype' 		=> 0,
			'url' 			=> '',
			'key' 			=> 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'
		);
		return $this;
	}

	public function send($mobile = null, $content = null, $smsid = null) {
		if ( ! is_null($mobile)) {
			$this->mobile = $mobile;
			$this->query['phone'] = $mobile;
		}
		if ( ! is_null($content)) {
			$this->content = $content;
			$this->query['content'] = iconv('utf-8', 'GBK', $content);
		}

		return $this->execute()->returnArray();
	}

	public function setMobile($mobile) {
		$this->mobile = $mobile;
		return $this;
	}

	public function setContent($content) {
		$this->content = $content;
		return $this;
	}

	public function execute() {
		$http = new HTTPRequest($this->host, $this->uri, $this->port);
		$http->setQueryParams($this->query);
		$http->execute();
		$this->error = $http->getError();
		$xmlResult = new SimpleXMLElement($http->getResponseText());
		if ( isset($xmlResult->result) ) {
			$this->error = (int) $xmlResult->result;
			$key = $this->error;
			$row = $this->returns;
			$this->error_msg = isset($row[$key]) ? $row[$key] : $key;
		}
		return $this;
	}

	public function returnArray() {
		return array('error' => $this->error, 'error_msg' => $this->error_msg, 'remark' => $this->error_msg);
	}
}
