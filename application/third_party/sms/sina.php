<?php

/******************************************************************************
 * Sina短信通道 
 * sae.sina.com.cn
 * misms@500mi.com
 *
 ******************************************************************************/

require_once(APPPATH.'./third_party/sms/Interface.php');

Class Sina implements Sms_Interface
{
	private $uid = 'z051415x14';
	private $pwd = 'xjlzlj3y44k252534xx0w51j414wixi020il1iyx';
	private $url = "http://misms.sinaapp.com/";

	public function send($mobile, $content, $smsid = 0)
	{
		if ( is_array($mobile) ) {
			foreach ($mobile as $one_mobile) {
				$this->send($one_mobile, $content);
			}
		}

		$data = array(		
			"uid"		=> $this->uid,
			"pwd"		=> $this->pwd,
			"mobile"	=> $mobile,
			"msg"		=> $content
		);

		$rs = $this->posttohosts($this->url, $data);

		$ret = array();
		if (strpos($rs, 'status') !== false) {
		    //$this->ci->logger->log(6, $rs . ' : ' .  $mobile . ' : ' .  $content, 'sms-success');		
			$ret['error'] 		= 0;
			$ret['error_msg'] 	= '短信发送成功';
			$ret['remark'] 		= '短信发送成功';
		} else {
		    //$this->ci->logger->log(6, $rs . ' : ' .  $mobile . ' : ' .  $content, 'sms-error');
			$ret['error'] 		= 1;
			$ret['error_msg'] 	= '短信发送失败';
			$ret['remark'] 		= $rs;
		}
		return $ret;
	}

	public static function posttohosts($url, $data)
	{
		$url = parse_url($url);
		if (!$url) return "couldn't parse url";
		if (!isset($url['port'])) { $url['port'] = ""; }
		if (!isset($url['query'])) { $url['query'] = ""; }
		$encoded = "";
		while (list($k,$v) = each($data))
		{
				$encoded .= ($encoded ? "&" : "");
				$encoded .= rawurlencode($k)."=".rawurlencode($v);
		}
		$fp = fsockopen($url['host'], $url['port'] ? $url['port'] : 80);
		if (!$fp) return "Failed to open socket to $url[host]";
		fputs($fp, sprintf("POST %s%s%s HTTP/1.0\n", $url['path'], $url['query'] ? "?" : "", $url['query']));
		fputs($fp, "Host: $url[host]\n");
		fputs($fp, "Content-type: application/x-www-form-urlencoded\n");
		fputs($fp, "Content-length: " . strlen($encoded) . "\n");
		fputs($fp, "Connection: close\n\n");
		fputs($fp, "$encoded\n");
		$line = fgets($fp,1024);
		// if (!preg_match("/^HTTP/1\.. 200/", $line)) return;
		$results = "";
		$inheader = 1;
		while(!feof($fp))
		{
				$line = fgets($fp,1024);
				if ($inheader && ($line == "\n" || $line == "\r\n"))
				{
						$inheader = 0;
				}
				elseif (!$inheader)
				{
						$results .= $line;
				}
		}
		fclose($fp);
		return $results;
	}
}
//Class::EOF