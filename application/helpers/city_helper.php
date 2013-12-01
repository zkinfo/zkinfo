<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 列出所有城市
 * Enter description here ...
 */
if ( ! function_exists('getallcity')){
	function getallcity()
	{	
		$CI =& get_instance();
		$CI->config->load('const_base',TRUE);
		return $CI->config->item('city_list','const_base');
	}
}
/**
 * 根据值遍历分析出城市列表
 * Enter description here ...
 * @param $city_value
 */
if ( ! function_exists('getcitybyvalue')){
	function getcitybyvalue($city_value)
	{	
		$arr = array();
		$CI =& get_instance();
		$CI->config->load('const_city',TRUE);
		$citylist = $CI->config->item('city_list','const_city');
		$i=0;
		foreach ($citylist as $key=>$value){
			$tmp = $city_value|$key;
			if($city_value==$tmp){
				$arr[$i] = $value;
				$i++;
			}
		}
		return $arr;
	}
}
if ( ! function_exists('getcityarr')){
	function getcityarr()
	{
		$arr = array();
		$CI =& get_instance();
		$CI->config->load('const_city',TRUE);
		$citylist = $CI->config->item('city_list','const_city');
		$i=0;
		foreach ($citylist as $key=>$value){
			$arr[$i]['city'] = $value;
			$arr[$i]['value'] = $key;
			$i++;
		}
		return $arr;	
	}
}