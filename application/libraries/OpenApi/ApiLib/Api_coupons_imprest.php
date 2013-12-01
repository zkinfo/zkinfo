<?php
/**
 * 优惠券
 */
class Api_coupons_imprest
{
	public function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->business('trade/trade_order_biz');
	}

	/**
	 *  获取用户的优惠券
	 *  主要参数 user_id
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

		$tdorders = $this->ci->trade_order_biz->getOrderList(array('type'=>'3', 'parent_id'=>0, 'user_id' => $params['user_id']), '*', $start, $pagesize, $by, $sort);
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
				'coupons' => $tdorders
			)
		);
	}

	/**
	 *  优惠券数量
	 */
	public function count($params = array())
	{
		$sql = "select count(*) as total from cp_trade_order where status != -1 and type = 3 and parent_id = 0 and user_id = $params[user_id] ";
		$tdorders = $this->ci->trade_order_biz->getOrderQuery($sql);
		return array(
			'error' 	=> 0,
			'error_msg' => 'Get success',
			'data' 		=> array(
				'coupons_count' => $tdorders
			)
		);
	}

	/**
	 *  根据ID获得优惠券详情
	 *
	 */
	public function get($params = array())
	{
		$tdorder = $this->ci->trade_order_biz->getOrder( array('type'=>'3', 'parent_id'=>0, 'tid' => $params['tid']) );
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
				'coupons' => $tdorder
			)
		);
	}

	/**
	 *  根据ID获得优惠券的商品列表
	 *
	 */
	public function getItems($params = array())
	{
		$tdorder = $this->ci->trade_order_biz->getOrder( array('type'=>'3', 'parent_id'=>$params['tid']) );
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
				'items' => $tdorder
			)
		);
	}

	/**
	 *  通知发货
	 *
	 */
	public function delivery_request($params = array())
	{
		$tdorder = $this->ci->trade_order_biz->getOrder( array('type'=>'3', 'tid' => $params['tid']) );
		if (empty($tdorder)) {
			return array(
				'error' 	=> 1,
				'error_msg' => '优惠券不存在',
				'data' 		=> array(
					'torder' => $tdorder
				)
			);
		} else if ($tdorder['type'] != 3) {
			return array(
				'error' 	=> 1,
				'error_msg' => '这个订单不是优惠券',
				'data' 		=> array(
					'torder' => $tdorder
				)
			);
		} else if ($tdorder['book_time']) {
			return array(
				'error' 	=> 1,
				'error_msg' => '已请求发货, 不能重复请求',
				'data' 		=> array(
					'torder' => $tdorder
				)
			);
		}

		$this->ci->trade_order_biz->updateOrder(array('book_time'=>$params['book_time']), $tdorder['id']);

		$tdorder = $this->ci->trade_order_biz->getOrder( array('type'=>'3', 'tid' => $params['tid']) );
		$fields = explode(',', 'id,tid,book_time,shop_id,spot_code,user_id,cdate,user_name,item_id,iname,spot_addr,user_addr,status,status_value,price_value,num,pay_value,pay_real_value,discount_value,parent_id,image_value,spec_value');
		foreach ($tdorder as $key => $val) {
			if ( ! in_array($key, $fields)) {
				unset($tdorder[$key]);
			}
		}

		return array(
			'error' 	=> 0,
			'error_msg' => '提货申请成功',
			'data' 		=> array(
				'torder' => $tdorder
			)
		);
	}

	/**
	 *  签收订单
	 *
	 */
	public function receive($params = array())
	{
		$receive = $this->ci->trade_order_biz->receive($params['tid']);

		$tdorder = $this->ci->trade_order_biz->getOrder( array('type'=>'3', 'tid' => $params['tid']) );
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
}