<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 *******************************************************************************
 * DB 操作原型类
 * Model文件继承此类，即可使用通用DAO方法
 * 继承类请在构造函数最后加上：
 *     $this->table_name = '?';
 *******************************************************************************
 * @version 0.6.2 - 0.2.0
 */
class MY_Model extends CI_Model
{

	protected $table_name;

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	/** 
	 * 新增数据
	 *
	 * @param 	array 	$data
	 * @return 	int
	 */
	public function insert($data)
	{
		if ( ! isset($data['cdate'])) {
			$data['cdate'] = date('Y-m-d H:i:s');
		}
		$this->db->insert($this->table_name, $data);
		$id = $this->db->insert_id();
		if ($id) {
			return $id;
		} else {
			return $this->db->affected_rows();
		}
	}

	/** 
	 * 批量新增数据 (一般用于数据导入)
	 * 传入二维数组
	 *
	 * @param 	array 	$data
	 * @return 	bool
	 */
	public function insert_batch($data)
	{
		return $bool = $this->db->insert_batch($this->table_name, $data);
	}

	/**
	 * 更新数据
	 * 传入单个id, 或者一个数组条件
	 *
	 * @param 	array 	$data
	 * @param 	fixed 	$id
	 * @return 	bool
	 */
	public function update($data, $id)
	{
		if (is_array($id)) {
			$this->db->where($id);
		} else {
			$this->db->where('id', $id);
		}
		return $bool = $this->db->update($this->table_name, $data);
	}

	/**
	 * 删除数据, $real true 时 真删除
	 *
	 * @param 	fixed 	$id
	 * @param 	bool 	$real
	 * @return 	bool
	 */
	public function delete($id, $real = false)
	{
		$data = array(
			'status' => '-1'
		);
		return $bool = $this->update($data, $id);
	}

	/**
	 * 查询单个数据
	 *
	 * @param 	fixed 	$params
	 * @param 	string 	$fields
	 * @return 	array
	 */
	public function getOne($params = array(), $fields = '*')
	{		
		// if ( ! isset($params['status'])) {
		// 	$params['status <>'] = '-1';
		// }
		$q = $this->db->select($fields, false)->where($params)->get($this->table_name);
		return $row = $q->row_array();
	}

	/**
	 * 查询记录条数
	 *
	 * @param 	fixed 	$params
	 * @param 	array 	$like
	 * @return 	int
	 */
	public function getCount($params = array(), $like = array())
	{
		if ( ! isset($params['status'])) {
			$params['status <>'] = '-1';
		}
		return $count = $this->db->where($params)->or_like($like)->from($this->table_name)
								 ->count_all_results();
	}

	/**
	 * 查询列表
	 *
	 * @param 	array 	$params
	 * @param 	string 	$data
	 * @param 	int 	$start
	 * @param 	int 	$perpage
	 * @param 	string 	$order
	 * @param 	string 	$sort
	 * @param 	array 	$like
	 * @return 	array
	 */
	public function getList($params = array(), $fields = '*', $page = 1, $pagesize = 20, $order_by = null)
	{
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $pagesize;
		
		if ( ! isset($params['status'])) {
			$params['status <>'] = '-1';
		}
		if ($pagesize) {
			$this->db->limit($pagesize, $start);
		}
		if ($order_by) {
			$this->db->order_by($order_by, '');
		}
		$q = $this->db->select($fields, false)->where($params)->get($this->table_name);
		return $list = $q->result_array();
	}
}

/* End of file: MY_Model.php */