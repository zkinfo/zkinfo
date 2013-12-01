<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/* load the MX_Loader class */
require APPPATH."third_party/MX/Loader.php";

/**
 * 自定义加载类，扩展了MX_Load
 */
class MY_Loader extends MX_Loader 
{
	/**
	 * 加载JavaAPI，获得接口对象
	 * @param string $api 接口名字
	 * @param string $object_name 返回对象名称
	 * @param array $params 构造参数
	 * @return void
	 */	
	public function javaAPI($api, $object_name = null) 
	{
		if (is_array($api)) {
			foreach ($api as $_api) return $this->javaAPI($_api);	
		}		

		$service_array = $this->config->item('javaAPI_service');
		if (!array_key_exists($api, $service_array)) return null;

		$url = $service_array[$api]['url'];
		$session = $service_array[$api]['session'];

		//API名字
		$class = strtr(strtolower(basename($api)), array('.'=>'_'));

		//对象存在，直接返回实例
		if (isset($this->_ci_classes[$class]) AND $_alias = $this->_ci_classes[$class]) {
			return CI::$APP->$_alias;
		}

		//否则，创建一个以API名为名字的对象	
		($_alias = strtolower($object_name)) OR $_alias = $class;
		
		//载入JavaAPI访问器，访问对应API
		
		include_once(APPPATH . 'libraries/JavaAPI.php');
		CI::$APP->$_alias = new JavaAPI($api, $url, $session);
		
		$this->_ci_classes[$class] = $_alias;
		
		return CI::$APP->$_alias;
	}

	/**
	 * 新的Model加载，判断是否支持javaAPI
	 * 默认javaAPI优先，否则加载旧的Model
	 * @param string $api 接口名字
	 * @param string $object_name 返回对象名称
	 * @param array $params 构造参数
	 * @return void
	 */	
	public function model($model, $object_name = NULL, $connect = FALSE) 
	{

		if (is_array($model))
		{
			foreach ($model as $babe)
			{
				$this->model($babe);
			}
			return;
		}

		return parent::model($model, $object_name, $connect);
	}

	public function dos($do, $arr = array()) 
	{
		require_once APPPATH . 'models/dos/'.$do.'.php';
		preg_match('/[A-Za-z]+$/', $do, $matches);
		$obj = new $matches[0]($arr);
		return $obj;
	}
}