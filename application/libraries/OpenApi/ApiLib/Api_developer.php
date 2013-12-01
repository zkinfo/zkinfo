<?php
/**
 * 500mi OpenAPI libirary
 * 派收快递单
 * 传入参数 $param
 * 返回值 $ret
 */
class Api_developer
{
	function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->business('user/user_biz');
	}

	//快递单列表
	public function index()
	{
		return array('error' => 0, 'error_msg' => 'success', 'data' => $this->ci->user_biz->getUserList(array('account' => '%五百米%'), '*', 1, 10) );
	}

	public function create($params)
	{
		if (!isset($params['mobile']) || !isset($params['name'])) return array('error' => 1, 'error_msg' => '添加失败, 数据不完整', 'data' => array() );
		return array('error' => 0, 'error_msg' => '创建成功，ID = 100001', 'data' => array('user_id' => '100001', 'name' => $params['name'], 'mobile' => $params['mobile']) );
	}

	public function get($params)
	{
		if (!isset($params['id'])) return array('error' => 1, 'error_msg' => '请求失败, id不能为空', 'data' => array() );
		$request['id'] = $params['id'];
		$item = $this->ci->user_biz->getUserById($request['id']);
		if ( empty($item) ) return array('error' => 2, 'error_msg' => '请求失败, 用户不存在', 'data' => array() );
		return array('error' => 0, 'error_msg' => 'success', 'data' => $this->ci->user_biz->getUserById($request['id']) );
	}

	public function delete($params)
	{
		if (!isset($params['id'])) return array('error' => 1, 'error_msg' => '请求失败, id不能为空', 'data' => array() );
		$request['id'] = $params['id'];
		$request["account like '%五百米%'"] = null;
		$item = $this->ci->user_biz->getUserById($request['id']);
		if ( empty($item) ) return array('error' => 2, 'error_msg' => '请求失败, 用户不存在', 'data' => array() );
		return array('error' => 0, 'error_msg' => '删除成功');
	}


	public function update($params)
	{
		if (!isset($params['id'])) return array('error' => 1, 'error_msg' => '请求失败, id不能为空', 'data' => array() );
		$request['id'] = $params['id'];
		$request["account like '%五百米%'"] = null;
		$item = $this->ci->user_biz->getUserById($request['id']);
		if ( empty($item) ) return array('error' => 2, 'error_msg' => '请求失败, 用户不存在', 'data' => array() );
		return array('error' => 0, 'error_msg' => '更新成功');
	}	
}
