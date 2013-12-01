<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * redis初始化配置
 *
 */
class Predis
{

	public function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->redis = new Redis();
		if (ENVIRONMENT == 'production') {
			$this->ci->redis->connect('redis.api.500mi.com', 6379, 3);
		}else{
			$this->ci->redis->connect('redis.api.500mi.com', 6379, 5);
		}
	}

	function  init(){

	}

	/**
	 * $cache_name   	cache名称
	 * $source  		redis的数据源查找条件
	 * $value_is_json  	value的值是不是json
	 */
	function getCacheByString($cache_name = null, $source = null, $value_is_json = false)
	{
		$this->ci->load->driver('cache', array('adapter' => 'file', 'backup' => 'file'));
		$cache_file = 'redis.'.$cache_name.'.cache';
		$resource = $this->ci->cache->get($cache_file);

		if ($resource) {
			return json_decode($resource, true);
		} else {
			$source = empty($source) ? $cache_name : $source;

			$resource_keys = $this->ci->redis->keys($source);

			$resource = array();
			foreach ($resource_keys as $resource_key) {
				$data = $this->ci->redis->get($resource_key);
				$resource[] = $value_is_json ? json_decode($data, true) : $data;
			}
			$this->ci->cache->save($cache_file, json_encode($resource), 3600 * 24);
			return $resource;
		}
	}

	/**
	 * $cache_name   	cache名称
	 * $value_is_json  	value的值是不是json
	 */
	function getCacheByList($cache_name = null, $value_is_json = false)
	{
		$this->ci->load->driver('cache', array('adapter' => 'file', 'backup' => 'file'));
		$cache_file = 'redis.'.$cache_name.'.cache';
		$resource = $this->ci->cache->get($cache_file);

		if ($resource) {
			return json_decode($resource, true);
		} else {
			$list_size = $this->ci->redis->lSize($cache_name);
			$list_size = 3;

			$resource = array();
			for ($i = 0; $i < $list_size; $i++) { 
				$data = $this->ci->redis->lGet($cache_name, $i);
				$resource[] = $value_is_json ? json_decode($data, true) : $data;
			}

			$this->ci->cache->save($cache_file, json_encode($resource), 3600 * 24);
			return $resource;
		}
	}

	/**
	 * $cache_name   	cache名称
	 * $value_is_json  	value的值是不是json
	 */
	function getCacheByHash($cache_name = null, $value_is_json = false)
	{
		$this->ci->load->driver('cache', array('adapter' => 'file', 'backup' => 'file'));
		$cache_file = 'redis.'.$cache_name.'.cache';
		$resource = $this->ci->cache->get($cache_file);

		if ($resource) {
			return json_decode($resource, true);
		} else {
			$resource = $data = $this->ci->redis->hGetAll($cache_name);
			if ($value_is_json) {
				foreach ($resource as $key => $value) {
					$resource[$key] = json_decode($value, true);
				}
			}
			
			$this->ci->cache->save($cache_file, json_encode($resource), 3600 * 24);
			return $resource;
		}
	}

	function deleteCache($table)
	{
		$this->ci->load->driver('cache', array('adapter' => 'file', 'backup' => 'file'));
		$cache_name = 'redis.'.$table.'.cache';

		if ($this->ci->cache->get($cache_name)) {
			$this->ci->cache->delete($cache_name);
		}
	}
}


?>
