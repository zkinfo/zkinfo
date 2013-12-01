<?php
/**
 * 交易订单类
 */
class Api_trade_order
{
	public function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->business('trade/trade_order_biz');
	}

	/**
	 *  根据坐标获得网点列表
	 *  $coords 坐标
	 *  $name 名字
	 */
	public function index($params = array())
	{
		$params['limit'] = isset($params['limit']) ? $params['limit'] : '0, 10';
		$params['limit'] = strtr($params['limit'], array(' '=>''));
		$limit = explode(',', $params['limit']);
		$start = $pagesize = 0;
		if (count($limit) == 1) {
			$pagesize = $limit[0];
		} else {
			$start = $limit[0];
			$pagesize = $limit[1];
		}

		$params['order_by'] = isset($params['order_by']) ? $params['order_by'] : 'cdate, desc';
		$params['order_by'] = strtr($params['order_by'], array(' '=>''));
		$order_by = explode(',', $params['order_by']);
		$by = $sort = null;
		if (count($order_by) == 1) {
			$by = $order_by[0];
		} else {
			$by = $order_by[0];
			$sort = $order_by[1];
		}

		$tdorders = $this->ci->trade_order_biz->getOrderList(array('user_id' => $params['user_id']), '*', $start, $pagesize, $by, $sort);
		// foreach ($torders as $i => $torder) {
		// 	foreach ($torder as $j => $row) {
		// 		$fields = 'item_id,category_id,item_name,item_code,price_value,price_real,
		// 		unit_value,storage,spec_value,';

		// 		if ( ! in_array( $j, explode(',', $fields) )) {
		// 			unset($torders[$i][$j]);
		// 		}
		// 	}
		// }
		foreach ($tdorders as $i => $tdorder) {
			$fields = explode(',', 'id,tid,shop_id,spot_code,user_id,cdate,user_name,item_id,iname,spot_addr,user_addr,status,status_value,price_value,num,pay_value,pay_real_value,discount_value,parent_id,image_value,spec_value');
			foreach ($tdorder as $key => $val) {
				if ( ! in_array($key, $fields)) {
					unset($tdorders[$i][$key]);
				}
			}
		}
		return array(
			'error' 	=> 0,
			'error_msg' => 'Get success',
			'data' 		=> array(
				'torder' => $tdorders
			)
		);
	}

	/**
	 *  订单统计
	 */
	public function count($params = array())
	{
		$sql = "select status, count(*) as count from cp_trade_order where status != -1 and item_id > 0 group by status";
		$tdorders = $this->ci->trade_order_biz->getOrderQuery($sql);
		// foreach ($tdorders as $i => $tdorder) {
		// 	$fields = explode(',', 'id,tid,shop_id,spot_code,user_id,cdate,user_name,item_id,iname,spot_addr,user_addr,status,status_value,price_value,num,pay_value,pay_real_value,discount_value,parent_id,image_value,spec_value');
		// 	foreach ($tdorder as $key => $val) {
		// 		if ( ! in_array($key, $fields)) {
		// 			unset($tdorders[$i][$key]);
		// 		}
		// 	}
		// }
		return array(
			'error' 	=> 0,
			'error_msg' => 'Get success',
			'data' 		=> array(
				'order_count' => $tdorders
			)
		);
	}

	/**
	 *  根据ID获得网点
	 *
	 *
	 */
	public function get($params = array())
	{
		$tdorder = $this->ci->trade_order_biz->getOrder( array('tid' => $params['tid']) );
		$fields = explode(',', 'id,tid,shop_id,spot_code,user_id,cdate,user_name,item_id,iname,spot_addr,user_addr,status,status_value,price_value,num,pay_value,pay_real_value,discount_value,parent_id,image_value,spec_value');
		foreach ($tdorder as $key => $val) {
			if ( ! in_array($key, $fields)) {
				unset($tdorder[$key]);
			}
		}

		return array(
			'error' 	=> 0,
			'error_msg' => 'Get success',
			'data' 		=> array(
				'torder' => $tdorder
			)
		);
	}

	/**
	 *  签收订单
	 *
	 *
	 */
	public function receive($params = array())
	{
		$receive = $this->ci->trade_order_biz->receive($params['tid']);

		$tdorder = $this->ci->trade_order_biz->getOrder( array('tid' => $params['tid']) );
		$fields = explode(',', 'id,tid,shop_id,spot_code,user_id,cdate,user_name,item_id,iname,spot_addr,user_addr,status,status_value,price_value,num,pay_value,pay_real_value,discount_value,parent_id,image_value,spec_value');
		foreach ($tdorder as $key => $val) {
			if ( ! in_array($key, $fields)) {
				unset($tdorder[$key]);
			}
		}

		return array(
			'error' 	=> $receive->error,
			'error_msg' => $receive->error_msg,
			'data' 		=> array(
				'torder' => $tdorder
			)
		);
	}

	/**
	 *  获得店铺在卖商品
	 *  $name 名字
	 */
	public function create($params = array())
	{
		$this->ci->load->business('trade/mi_create_order_biz');

		$items = json_decode($params['items'], 1);
		$order = $params;

		$create = $this->ci->mi_create_order_biz->create($items, $order);
		//var_dump($create);die;

		if ($create['status_code'] == 1) {
			$tdorder = $this->ci->trade_order_biz->getOrder( array('tid' => $create['tid']) );
			$fields = explode(',', 'id,tid,shop_id,spot_code,user_id,cdate,user_name,item_id,iname,spot_addr,user_addr,status,status_value,price_value,num,pay_value,pay_real_value,discount_value,parent_id,image_value,spec_value');
			foreach ($tdorder as $key => $val) {
				if ( ! in_array($key, $fields)) {
					unset($tdorder[$key]);
				}
			}
			return array(
				'error' 	=> 0,
				'error_msg' => $create['msg'],
				'data' 		=> array(
					'TDOrder' => $tdorder
				)
			);			
		} else {			
			return array(
				'error' 	=> 1,
				'error_msg' => $create['msg'],
				'data' 		=> array(
					'TDOrder' => array()
				)
			);
		}
	}	
}