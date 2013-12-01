<?php
/**
 * 500mi OpenAPI libirary
 * 派收快递单
 * 传入参数 $param
 * 返回值 $ret
 */
class Api_delivery_order
{
	function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->business('delivery/dispatch_order_biz');
	}

	//快递单列表
	public function index()
	{
		return $this->ci->dispatch_order_biz->getOrderList();
	}

	public function create()
	{

	}

	public function get(&$params)
	{
		$request['id'] = $params['order_id'];
		return $this->ci->dispatch_order_biz->getOrder($request);
	}

	public function delete()
	{

	}
}
