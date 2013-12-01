<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Assets {
	
	var $javascript = array();
	var $header_js_start = 0;
	var $css = array();
	var $header_css_start = 0;
	
	private $assets_folder;
	private $assets_path;
	private $assets_url;
	private $asset_types;
	private $default_level;
	
	function __construct($config=array())
	{
		$this->ci =& get_instance();
		$this->ci->load->config("assets");
		$this->assets_folder = $this->ci->config->item("assets_folder");
		$this->assets_path = $this->ci->config->item("assets_path");
		$this->assets_url = $this->ci->config->item("assets_url");
		if(empty($this->assets_url)){
			$this->assets_url = $this->ci->site_url();
		}
		$this->asset_types = $this->ci->config->item("asset_types");
		$this->default_level = $this->ci->config->item("default_level");
		$this->assets_version = $this->ci->config->item("assets_version");
		$this->ci->load->helper("url");
	}
	
	function Assets($config=array()){
		// Get CI instance
		$this->ci =& get_instance();
		$this->ci->load->config("assets");
		$this->assets_folder = $this->ci->config->item("assets_folder");
		$this->assets_path = $this->ci->config->item("assets_path");
		$this->assets_url = $this->ci->config->item("assets_url");
		$this->asset_types = $this->ci->config->item("asset_types");
		$this->default_level = $this->ci->config->item("default_level");
		$this->assets_version = $this->ci->config->item("assets_version");
	/*if(!empty($config)){
			foreach($config as $k => $v){
				$this->$k = $v;
			}
		}else{
			show_error("Assets Config File is Missing or is Empty");
		}*/
	}
	
	function load($file,$level="",$params=array()){
		if(!$level){
			$level = $this->default_level;
		}
		$type = strtolower(substr(strrchr($file, '.'), 1));
		if($type=="jpg" || $type=="jpeg" || $type=="png" || $type=="gif"){
			// Generate Image Link
			$image_link = $this->assets_url."/$this->assets_folder/$level/images/".$file;
			// Generate the Paramaters
			$image_params = $this->generate_params($params);
			// Create Image Tag
			$output = "<img src='$image_link'$image_params />";
			return $output;
		}elseif($type=="js"){
			$file = "/$level/$type/$file";
			if($params != NULL && array_key_exists("extra",$params) && $params["extra"] != ""){
				$file .= "/".$params["extra"];
				unset($params["extra"]);
			}
			$js_link = $this->assets_url."/$this->assets_folder".$file;
			if (!empty($this->assets_version)) {
				$js_link .= "?v=".$this->assets_version;
			}

			$js_params = $this->generate_params($params);

			$this->javascript[] = array('url' => $js_link, 'params' => $js_params);
			// $output = "<script type='text/javascript' src='".$js_link."'$js_params></script>\n";
			// return $output;
		}elseif($type=="css"){
			$file = "/$level/$type/$file";
			if($params != NULL && array_key_exists("extra",$params) && $params["extra"] != ""){
				$file .= "/".$params["extra"];
				unset($params["extra"]);
			}
			$css_link = $this->assets_url."/$this->assets_folder".$file;
			if (!empty($this->assets_version)) {
				$css_link .= "?v=".$this->assets_version;
			}

			$css_params = $this->generate_params($params);
			$this->css[] = array('url' => $css_link, 'params' => $css_params);
			// $output = "<link type='text/css' rel='stylesheet' href='".$css_link."'$css_params />\n";
			// return $output;
		}else{
			return false;
		}
	}
	
	function url($file,$level=""){
		if(!$level){
			$level = $this->default_level;
		}
		$type = strtolower(substr(strrchr($file, '.'), 1));
		if(array_key_exists($type,$this->asset_types)){
			foreach($this->asset_types as $asset_type => $folder){
				if($type == $asset_type){
					$output = $this->assets_url."/$this->assets_folder/$level/$folder/".$file;
					return $output;
					break;
				}
			}
		}else{
			show_error("$type is not a valid asset type");
		}
	}
		
	function generate_params($params){
		$output = '';
		if(!empty($params)){
			foreach($params as $k => $v){
				$output .= ' '.$k.'="'.$v.'"';
			}
		}
		return $output;
	}

	function set_header_start(){
		$this->header_js_start = count($this->javascript);
		$this->header_css_start = count($this->css);
	}
	
	function display_header_assets(){
		$output = '';

		$header_css = '';
		$content_css = '';
		foreach($this->css as $k => $css){
			$css_string = "<link type='text/css' rel='stylesheet' href='".$css['url']."'".$css['params']." />\n";
			if ($k < $this->header_css_start) {
				$content_css .= $css_string;
			} else {
				$header_css .= $css_string;
			}
			// $output .= "<link type='text/css' rel='stylesheet' href='".$this->assets_url."/$this->assets_folder".str_replace(".css","",$file)."/' />\n";
		}
		$output .= $header_css.$content_css;

		$header_js = '';
		$content_js = '';
		foreach($this->javascript as $k => $js){
			$js_string= "<script type='text/javascript' src='".$js['url']."'".$js['params']."></script>\n";
			if ($k < $this->header_js_start) {
				$content_js .= $js_string;
			} else {
				$header_js .= $js_string;
			}
			// $output .= "<script type='text/javascript' src='".$this->assets_url."/$this->assets_folder".str_replace(".js","",$file)."/'></script>\n";
		}
		$output .= $header_js.$content_js;
		
		return $output;
	}
}

?>