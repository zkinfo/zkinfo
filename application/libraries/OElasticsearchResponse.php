<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * 搜索引擎客户端响应
 * @author danchex
 */
class OElasticsearchResponse
{
	protected $data;		//请求相应返回的原始数据
	protected $errno;			//请求状态
	protected $errmsg;			//请求错误消息
	
	public function init($data)
	{
		//print_r($data); die;
		$this->data = $data;
		return $this;
	}

	public function toArray()
	{
		return $this->data;
	}

	public function suggestions()
	{
		if ( ! isset($this->data['suggestions'])) return array();
		else return $this->data['suggestions'];
	}

	public function result()
	{
		if ( ! isset($this->data['hits']['hits'])) return array();
		foreach ($this->data['hits']['hits'] as $i => $_source) {
			$this->data['hits']['hits'][$i] = $_source['_source'];
		}
		return array(
			'total' 	=> $this->data['hits']['total'],
			'list' 		=> $this->data['hits']['hits']
		);
	}

	public function resultList()
	{
		if ( ! isset($this->data['hits']['hits'])) return array();
		foreach ($this->data['hits']['hits'] as $i => $_source) {
			$this->data['hits']['hits'][$i] = $_source['_source'];
		}
		return $this->data['hits']['hits'];
	}

	public function total()
	{
		if ( ! isset($this->data['hits']['total'])) return 0;
		else return $this->data['hits']['total'];
	}
}
//EOF;