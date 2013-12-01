<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
require_once APPPATH .'libraries/OElasticsearchResponse.php';

/**
 * 搜索引擎客户端
 * @author danchex
 */
class OElasticsearchRequest
{
	protected $host;					//主机地址
	protected $index;					//索引（数据库）
	protected $type;					//类别（表、文档)
	protected $uri;						//资源位置
	protected $params;		//请求参数
	protected $method = 'GET';			//请求方式 REST
	protected $page = 1;					//分页页码
	
	/**
	 * 初始化
	 * 相当于构造连接
	 * @param $host
	 * @param $index
	 * @param $type
	 */
	public function init($host = null, $index = null, $type = null)
	{
		$this->host = $host;
		$this->index = $index;
		$this->type = $type;
		$this->uri = $this->host.$this->index.'/'.$this->type.'/';
		$this->params = array('from' => 0, 'size' => 20);
		return $this;
	}
	public function uri($uri)
	{
		$this->uri = $uri;
		return $this;
	}

	/**
	 * 搜索 - 执行命令
	 * @param $cmd
	 */
    public function exec($cmd = '_search')
	{		
		$this->uri .= $cmd;

		//var_dump($this->uri); print_r($this->params());
		$return = $this->curlrequest($this->uri, $this->args(), $this->method);
		$data = json_decode($return, true);
		$response = new OElasticsearchResponse;

		return $response->init($data);
	}

	/**
	 * 自定义参数
	 * 可以接受任意参数，使用前请参考elastic官方手册  --慎用！
	 */
	public function params($param = array())
	{
		if (empty($param)) return $this;
		$this->params = array_merge($this->params, $param);
		return $this;
	}

	/**
	 * 参数JSON序列化
	 * 执行搜索命令前序列化
	 */
	protected function args()
	{
		if ( ! isset($this->params['from'])) $this->params['from'] = 0;
		if ( ! isset($this->params['size'])) $this->params['size'] = 20; 
		// var_dump(json_encode($this->params)); die;
		return json_encode($this->params);
	}

	/**
	 * 模拟请求，获得数据
	 * 
	 * @param $url
	 * @param $data
	 * @param $method
	 */
	public function curlrequest($url, $data = array(), $method = 'GET')
	{
		//print_r($url); print_r($data);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-HTTP-Method-Override: $method"));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}

	/**
	 * 游标起始位置 -- 弃用，请使用 page size 取代
	 * 
	 * @param $from
	 */
	public function fields($fields)
	{
		if (empty($fields)) return $this;
		if (is_array($fields)) {
			$this->params['fields'] = $fields;
		} else {
			$fields = preg_replace('/\s+/', '', $fields);
			$this->params['fields'] = explode(',', $fields);
		}
		return $this;
	}

	/**
	 * 游标起始位置 -- 弃用，请使用 page size 取代
	 * 
	 * @param $from
	 */
	protected function from($from)
	{
		if (empty($from)) return $this;
		$this->params['from'] = $from;
		return $this;
	}

	/**
	 * 分页每页数量
	 * 
	 * @param $size
	 */
	public function size($size)
	{
		if (empty($size)) return $this;
		$this->params['size'] = $size;
		$this->params['from'] = ($this->page - 1) * $this->params['size'];
		return $this;
	}

	/**
	 * 分页页码
	 * 
	 * @param $page
	 */
	public function page($page)
	{
		if (empty($page)) return $this;
		$this->page = $page > 1 ? $page : 1;
		$this->params['from'] = ($this->page - 1) * $this->params['size'];
		return $this;
	}

	/**
	 * 排序
	 * 
	 * @param $sort
	 */
	public function sort($sort)
	{
		if (empty($sort)) return $this;
		if ( ! isset($this->params['sort'])) $this->params['sort'] = array();
		if ( ! is_array($sort)) {
			$sort = explode(' ', $sort);
			if (count($sort) == 2) $sort = array($sort[0] => array('order' => $sort[1]));
				else $sort = array($sort[0] => array('order' => 'desc'));
		}
		//$this->params['sort'][] = array('order_num' => array('order' => 'desc'));
		$this->params['sort'][] = $sort;
		return $this;
	}

	/**
	 * 高亮
	 * 
	 * @param $field
	 */
	public function highlight($field)
	{
		if (empty($field)) return $this;
		$this->params['highlight'] = array(
			'fields' => array($field => (object) array())
		);
		return $this;
	}

	/**
	 * 区间查询
	 * 
	 * @param $range
	 */
	public function range($range)
	{
		if (empty($range)) return $this;
		if ( ! isset($this->params['query'])) $this->params['query'] = array();
		if ( ! isset($this->params['query']['bool'])) $this->params['query']['bool'] = array();
		if ( ! isset($this->params['query']['bool']['must'])) $this->params['query']['bool']['must'] = array();

		foreach ($range as $key => $value) {
			$_terms = array(
				'range' => array(
					$key => array(
						"from" 	=> $value['from'],
						"to" 	=> $value['to']
					)
				)
			);
			$this->params['query']['bool']['must'][] = $_terms;
		}
		return $this;
	}

	/**
	 * must  AND查询
	 * 
	 * @param $must
	 */
	public function must($must)
	{
		if (empty($must)) return $this;
		if ( ! isset($this->params['query'])) $this->params['query'] = array();
		if ( ! isset($this->params['query']['bool'])) $this->params['query']['bool'] = array();
		if ( ! isset($this->params['query']['bool']['must'])) $this->params['query']['bool']['must'] = array();
		
		foreach ($must as $key => $value) {
			$_terms = array();
			if ($key == 'item_name') {
				if (is_numeric($value)) {
					$_terms[] = array(
						'text' => array(
							"sin" => $value
						)
					);
				} else if ( ! preg_match('/[^\w]/', $value)) {
					$_terms[] = array(
						'text' => array(
							"item_name.pinyin" => $value
						)
					);					
				} else {
					$_terms[] = array(
						'text' => array(
							"item_name" => $value
						)
					);					
				}				
			} else {
				if (is_array($value)) {
					$_terms[] = array(
						'terms' => array(
							$key => $value
						)
					);
				} else if (strpos($value, ",")) {
					$_terms[] = array(
						'terms' => array(
							$key => explode(",", $value)
						)
					);
				} else {
					$_terms[] = array(
						'term' => array(
							$key => $value
						)
					);
				}
			}
			$this->params['query']['bool']['must'][] = $_terms;
		}
		return $this;
	}

	/**
	 * must  NOT 查询
	 * 
	 * @param $must_not
	 */
	public function must_not($must_not)
	{
		if (empty($must_not)) return $this;
		if ( ! isset($this->params['query'])) $this->params['query'] = array();
		if ( ! isset($this->params['query']['bool'])) $this->params['query']['bool'] = array();
		if ( ! isset($this->params['query']['bool']['must_not'])) $this->params['query']['bool']['must_not'] = array();
		
		foreach ($must_not as $key => $value) {
			$_terms = array();
			if ($key == 'item_name') {
				if (is_numeric($value)) {
					$_terms[] = array(
						'text' => array(
							"sin" => $value
						)
					);
				} else if ( ! preg_match('/[^\w]/', $value)) {
					$_terms[] = array(
						'text' => array(
							"item_name.pinyin" => $value
						)
					);					
				} else {
					$_terms[] = array(
						'text' => array(
							"item_name" => $value
						)
					);					
				}				
			} else {
				if (is_array($value)) {
					$_terms[] = array(
						'terms' => array(
							$key => $value
						)
					);					
				} else if (strpos($value, ",")) {
					$_terms[] = array(
						'terms' => array(
							$key => explode(",", $value)
						)
					);
				} else {
					$_terms[] = array(
						'term' => array(
							$key => $value
						)
					);
				}
			}
			$this->params['query']['bool']['must_not'][] = $_terms;
		}
		return $this;
	}

	/**
	 * should  OR查询
	 * 
	 * @param $must
	 */
	public function should($should)
	{
		if (empty($should)) return $this;
		if ( ! isset($this->params['query'])) $this->params['query'] = array();
		if ( ! isset($this->params['query']['bool'])) $this->params['query']['bool'] = array();
		if ( ! isset($this->params['query']['bool']['should'])) $this->params['query']['bool']['should'] = array();

		foreach ($should as $key => $value) {
			$_terms = array();
			if ($key == 'item_name') {
				if ( ! preg_match('/\d/', $value)) {
					$_terms[] = array(
						'text' => array(
							"item_name" => $value
						)
					);					
				} else if ( ! preg_match('/[^\d]/', $value)) {
					$_terms[] = array(
						'text' => array(
							"sin" => $value
						)
					);
				} else if ( ! preg_match('/[^\w]/', $value)) {
					$_terms[] = array(
						'text' => array(
							"item_name.pinyin" => $value
						)
					);					
				} else {
					$_terms[] = array(
						'text' => array(
							"item_name" => $value
						)
					);
				}
			} else {				
				$_terms = array(
					'terms' => array(
						$key => explode(",", $value),
						'minimum_match' => 1
					)
				);
			}
			$this->params['query']['bool']['should'][] = $_terms;
		}
		return $this;
	}

	/**
	 * facets  统计查询
	 * 
	 * @param $must
	 */
	public function facets($facets)
	{
		if (empty($facets)) return $this;
		if ( ! isset($this->params['facets'])) $this->params['facets'] = array();
		foreach ($facets as $key => $value) {
			$this->params['facets'][$key]['terms'] = array(
				'field'	=> $key,
				'size'	=> $value
			);
		}
		return $this;
	}
}
//EOF;