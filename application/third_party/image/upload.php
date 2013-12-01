<?php
/**
* 
* 客户端类库-v2
* @author scorpio
* 
*/
class image_v2{
	var $file;
	var $path;
	const img_url = "http://res.500mi.com/";

	function __construct($config,$file){
		include_once("snoopy.php");
		$options = array(
			'save_path'            =>'tmp',// 存储文件夹名
			'time'                 =>time(),
			'save_key'             =>'random',// 文件存储方式(random & date),分别是当前文件夹随机文件名 & 当前文件夹/年/月/日/随机文件名
			'upload_type'          =>'image',
			'allow_file_type'      =>'jpg,jpeg,gif,png',// 控制文件上传的类型,可选
			'content_length_range' =>'0,1024000',// 限制文件大小,可选
			'image_width_range'    =>'20,1024000',// 限制图片宽度,可选
			'image_height_range'   =>'20,1024000',// 限制图片高度,可选
			// 'return_url'        =>'http://localhost/form-test/return.php',// 页面跳转型回调地址
			// 'notify_url'        =>'http://a.com/form-test/notify.php',// 服务端异步回调地址, 请注意该地址必须公网可以正常访问
		);
		$secret = "500mi_upload";
		$option = array_merge($options,$config);
		$this->policy = base64_encode(json_encode($option));
		$this->signature = md5($this->policy.'&'.$secret);
		$this->file   = $file;
		$this->snoopy = new Snoopy;
	}

	function config(){
		$data = array(
			"policy" 	=>	$this->policy,
			"signature" =>	$this->signature	
		);
		return $data;
	}

	function upload(){
		$all = array();
		$snoopy = $this->snoopy;
		$submit = self::img_url."main/api/v2_upload"; 
		$file = $this->file;
		$data["policy"] = $this->policy;
		$data["signature"] = $this->signature;
		foreach ($file as $key => $value) {
			if(!$value["error"]){			
				$child_file = array();
				$child_file['Filedata'] = $value;
				$data["type"] = $child_file['Filedata']['type'];
				if(in_array($data["type"], array("image/jpg","image/jpeg","image/gif","image/png")) === false){
					$tmp = explode(".", $child_file['Filedata']['name']);
					$data["type"] = "application/".$tmp[1];
				}
				$snoopy->_submit_type = "multipart/form-data";
				$snoopy->submit($submit,$data,$child_file); 
				$res = $snoopy->results; 
				$all[$key] = $res;
			}
		}
		return $all;
	}

	function move(){
		$all = array();
		$snoopy = $this->snoopy;
		$signature = $this->signature;
		$submit = self::img_url."main/api/move"; 
		$post["policy"] = $this->policy;
		$post["signature"] = $signature;
		$snoopy->submit($submit,$post);
		$res = $snoopy->results; 
		return $res;
	}

}