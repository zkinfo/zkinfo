<?php
/**
 * 500mi OpenAPI libirary
 * 网点相关api 获得网点列表、获得单个网点信息、添加网点、删除网点、编辑网点，判断输入地址是否在网点列表中----cp平台要求添加
 * 传入参数 $param
 * 返回值 $ret
 */
class Api_shop
{
	public function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->business('shop/shop_biz');
	}

	/**
	 *  根据坐标获得网点列表
	 *  $coords 坐标
	 *  $name 名字
	 */
	public function index($params = array())
	{
		$this->ci->load->business('spot/spot_biz');
		$params['fields'] = isset($params['fields']) ? $params['fields'] 
			: ' id, shop_name, `desc`, address, remark, tag, 
				level, contact, contact_phone, spot_code, shop_item, logo, time_open, time_close ';
		if ( ! isset($params['limit']) ) {
			$params['limit'] = 10;	
		}

		//按坐标搜索
		if ( isset($params['coords']) && $params['coords'] ) {
			$coords = explode(',',  $params['coords']);
			if ( ! isset($coords[0], $coords[1])) {
				return array('error'=>1, 'error_msg'=>'Coords wrong', 'data' => array());
			}
			$shop = $this->ci->shop_biz->getShopByCoords( $coords[0], $coords[1],'*',1,$params['limit'] );
			// var_dump($shop);
			$num = $shop['total'];
			for ($i=0; $i < $num; $i++) { 			
				$spot = $this->ci->spot_biz->getSpotBySpotCode($shop[$i]["spot_code"]);
				//$shop[$i]["spot"] = $spot["spot_name"];
				//$shop[$i]["shop_id"] = $shop[$i]["id"];
				$shop[$i]["coords"] = $spot["lng"].','.$spot["lat"];
				$shop[$i] = array_merge(array('shop_id'=>$shop[$i]['id']), $shop[$i]);
				$shop[$i]["logo"] = $shop[$i]["logo_value"];
				unset($shop[$i]['logo_value']);
				unset($shop[$i]['id']);
				unset($shop[$i]['lng']);
				unset($shop[$i]['lat']);
			}
			return array('error'=>0, 'error_msg'=>'Get Success', 'data' => array( 'shops' => $shop));
		}

		//按名称搜索
		if ( isset($params['shop_name']) && $params['shop_name'] ) {
			$sql = " select a.id, a.shop_name, a.address, a.shop_item, a.contact, a.contact_phone, a.spot_code, b.lng, b.lat, a.shop_item, a.logo from cp_shop a inner join tt_spot b on a.spot_code = b.spot_code 
					where a.status != -1 and b.status = 3 and b.lng > 0 and b.lat > 0 and a.shop_name like '%$params[shop_name]%'
					";
			$sql .= ' limit ' . $params['limit'];
			$shop = $this->ci->db->query($sql)->result_array();
			$num = count($shop);
			for ($i=0; $i < $num; $i++) { 			
				$spot = $this->ci->spot_biz->getSpotBySpotCode($shop[$i]["spot_code"]);
				//$shop[$i]["spot"] = $spot["spot_name"];
				//$shop[$i]["shop_id"] = $shop[$i]["id"];
				$shop[$i]["coords"] = $spot["lng"].','.$spot["lat"];
				$shop[$i] = array_merge(array('shop_id'=>$shop[$i]['id']), $shop[$i]);
				$shop[$i]["logo"] = $shop[$i]["logo_value"];
				unset($shop[$i]['logo_value']);
				unset($shop[$i]['id']);
				unset($shop[$i]['lng']);
				unset($shop[$i]['lat']);
			}
			return array('error'=>0, 'error_msg'=>'Get Success', 'data' => array( 'shops' => $shop));
		}

		//全部网点
		if ( 1 ) {
			$sql = " select a.id, a.shop_name, a.address, a.shop_item, a.contact, a.contact_phone, a.spot_code, b.lng, b.lat, a.shop_item, a.logo from cp_shop a inner join tt_spot b on a.spot_code = b.spot_code 
					where a.status != -1 and b.status = 3 and b.lng > 0 and b.lat > 0 
					";
			$sql .= ' limit ' . $params['limit'];
			$shop = $this->ci->db->query($sql)->result_array();
			$num = count($shop);
			for ($i=0; $i < $num; $i++) { 			
				$spot = $this->ci->spot_biz->getSpotBySpotCode($shop[$i]["spot_code"]);
				//$shop[$i]["spot"] = $spot["spot_name"];
				//$shop[$i]["shop_id"] = $shop[$i]["id"];
				$shop[$i]["coords"] = $spot["lng"].','.$spot["lat"];
				$shop[$i] = array_merge(array('shop_id'=>$shop[$i]['id']), $shop[$i]);
				$shop[$i]["logo"] = $shop[$i]["logo_value"];
				unset($shop[$i]['logo_value']);
				unset($shop[$i]['id']);
				unset($shop[$i]['lng']);
				unset($shop[$i]['lat']);
			}
			return array('error'=>0, 'error_msg'=>'Get Success', 'data' => array( 'shops' => $shop));
		}
	}

	/**
	 *  根据ID获得网点
	 *
	 *
	 */
	public function get($params = array())
	{
		$this->ci->load->business('spot/spot_biz');
		$params['fields'] = isset($params['fields']) ? $params['fields'] 
			: ' id, shop_name, address, shop_item, contact, contact_phone, spot_code, logo ';
		if ( ! isset($params['limit']) || $params['limit'] <= 0 || $params['limit'] > 10) {
			$params['limit'] = 3;	
		}

		//按坐标搜索
		if ( isset($params['shop_id']) && $params['shop_id'] ) {
			$shop = $this->ci->shop_biz->getShopById($params['shop_id'], $params['fields'] );
			
			if ( !empty($shop) ) {
				$spot = $this->ci->spot_biz->getSpotBySpotCode($shop[$i]["spot_code"]);
				//$shop["spot"] = $spot["spot_name"];
				$shop["shop_id"] = $shop["id"];
				$shop["coords"] = $spot["lng"].','.$spot["lat"];
				$shop = array_merge(array('shop_id'=>$shop['id']), $shop);
				$shop["logo"] = $shop["logo_value"];
				unset($shop['logo_value']);
				unset($shop['id']);
			
				return array('error'=>0, 'error_msg'=>'Get Success', 'data' => array( 'shop' => $shop));
			} else {
				return array('error'=>1, 'error_msg'=>'No this shop', 'data' => array());
			}
				
		} else {
			return array('error'=>1, 'error_msg'=>'shop_id must', 'data' => array());
		}
	}

	/**
	 *  关注店铺
	 *
	 *
	 */
	public function follow($params = array())
	{
		$this->ci->load->model('shop_follow_model');
		$this->ci->load->business('user/consumer_biz');
		$this->ci->load->business('user/consumer_address_biz');
		$this->ci->load->business('user/user_biz');

		//有用户
		$user = $this->ci->user_biz->getUserById($params['user_id']);
		if ( empty($user) ) {
			return array('error'=>1, 'error_msg'=>'没有这个用户');
		}

		//有店铺
		$shop = $this->ci->shop_biz->getShopById($params['shop_id']);
		if ( empty($shop) || $shop['status'] == -1) {
			return array('error'=>1, 'error_msg'=>'没有这个店铺');
		}

		//已经关注
		$isFollowed = $this->ci->shop_follow_model->getCount(array('shop_id' => $params['shop_id'], 'user_id' => $params['user_id'], 'status !=' => '-1'));
		if ( $isFollowed ) {
			return array('error'=>1, 'error_msg'=>'已经关注过了');
		}

		//关注逻辑
		$follow_data = array(
			'user_id'		=> $params['user_id'],
			'shop_id'		=> $params['shop_id'],
			'spot_code'		=> $shop['spot_code'],
			'shop_name'		=> $shop['shop_name'],
			'lc_code'		=> $shop['lc_code'],
			'follow_from'	=> 'IPHONE',
			'follow_time'	=> date('Y-m-d H:i:s')
		);
		$doFollow = $this->ci->shop_follow_model->insert($follow_data);
		if ($doFollow) {
			$hasAddress = $this->ci->consumer_address_biz->getAddressList(array('user_id'=>$params['user_id'], 'spot_code'=>$shop['spot_code'], 'status !='=>'-1'));
			if ( empty($hasAddress) ) {
				$address_data = array(
					'user_id'		=> $params['user_id'],
					'mobile'		=> $user['mobile'],
					'user_name'		=> $user['name'],
					'spot_code'		=> $shop['spot_code'],
					'lc_code'		=> $shop['lc_code'],
					'address'		=> $shop['address']
				);
				if ($this->ci->consumer_address_biz->addAddress($address_data)){
					return array('error'=>0, 'error_msg'=>'关注成功, 同时添加收货地址');
				} else {
					return array('error'=>0, 'error_msg'=>'关注成功, 收货地址未添加');
				}
			} else {				
				return array('error'=>0, 'error_msg'=>'关注成功');
			}
		} else {
			return array('error'=>1, 'error_msg'=>'关注失败');
		}
	}

	/**
	 *  取消关注店铺
	 *
	 */
	public function unfollow($params = array())
	{
		$this->ci->load->model('shop_follow_model');
		$this->ci->load->business('user/consumer_biz');
		$this->ci->load->business('user/consumer_address_biz');
		$this->ci->load->business('user/user_biz');

		//有用户
		$user = $this->ci->user_biz->getUserById($params['user_id']);
		if ( empty($user) ) {
			return array('error'=>1, 'error_msg'=>'没有这个用户');
		}

		//有店铺
		$shop = $this->ci->shop_biz->getShopById($params['shop_id']);
		if ( empty($shop) || $shop['status'] == -1) {
			return array('error'=>1, 'error_msg'=>'没有这个店铺');
		}

		//已经关注
		$isFollowed = $this->ci->shop_follow_model->getOne(array('shop_id' => $params['shop_id'], 'user_id' => $params['user_id'], 'status !=' => '-1'));
		if ( empty($isFollowed) ) {
			return array('error'=>1, 'error_msg'=>'还没关注过这个店铺');
		}

		//取消关注
		$doFollow = $this->ci->shop_follow_model->delete($isFollowed['id']);

		if ($doFollow) {
			$hasAddress = $this->ci->consumer_address_biz->getAddressList(array('user_id'=>$params['user_id'], 'spot_code'=>$shop['spot_code'], 'status !='=>'-1'));
			
			if ( ! empty($hasAddress) && isset($params['delete_address']) && $params['delete_address']) {
				if ($this->ci->consumer_address_biz->deleteAddress($hasAddress['id'])){
					return array('error'=>0, 'error_msg'=>'取消关注成功, 同时取消收货地址');
				} else {
					return array('error'=>0, 'error_msg'=>'取消关注成功, 收货地址取消失败');
				}
			} else {				
				return array('error'=>0, 'error_msg'=>'取消关注成功');
			}
		} else {
			return array('error'=>1, 'error_msg'=>'取消关注失败');
		}
	}

	/**
	 *  我的关注店铺
	 *
	 *
	 */
	public function follow_list($params = array())
	{
		$this->ci->load->model('shop_follow_model');
		$this->ci->load->business('user/consumer_biz');
		$this->ci->load->business('spot/spot_biz');
		$this->ci->load->business('user/user_biz');

		if ( ! isset($params['limit']) ) {
			$params['limit'] = 10;	
		}

		//有用户
		$user = $this->ci->user_biz->getUserById($params['user_id']);
		if ( empty($user) ) {
			return array('error'=>1, 'error_msg'=>'没有这个用户');
		}

		//关注列表
		//$follow_list = $this->ci->shop_follow_model->getList(array('user_id' => $params['user_id'], 'status !=' => '-1'));
		//if ( $follow_list ) {
			//return array('error'=>0, 'error_msg'=>'已经关注过了', 'data' => array('shops' => $follow_list));
		//} else {
			//return array('error'=>1, 'error_msg'=>'还没有关注的店铺', 'data' => array());
		//}

		if ( isset($params['user_id']) ) {
			$sql = " select c.user_id, a.id, a.shop_name, a.address, a.shop_item, a.contact, a.contact_phone, a.spot_code, b.lng, b.lat, a.shop_item, a.logo from cp_shop a inner join tt_spot b on a.spot_code = b.spot_code inner join cp_shop_follow c on c.spot_code = b.spot_code 
					where a.status != -1 and b.status = 3 and b.lng > 0 and b.lat > 0 and c.user_id = $params[user_id]
					";
			$sql .= ' limit ' . $params['limit'];
			$shop = $this->ci->db->query($sql)->result_array();
			$num = count($shop);
			for ($i=0; $i < $num; $i++) { 			
				$spot = $this->ci->spot_biz->getSpotBySpotCode($shop[$i]["spot_code"]);
				//$shop[$i]["spot"] = $spot["spot_name"];
				//$shop[$i]["shop_id"] = $shop[$i]["id"];
				$shop[$i]["coords"] = $spot["lng"].','.$spot["lat"];
				$shop[$i] = array_merge(array('shop_id'=>$shop[$i]['id']), $shop[$i]);
				$shop[$i]["logo"] = $shop[$i]["logo_value"];
				unset($shop[$i]['logo_value']);
				unset($shop[$i]['id']);
				unset($shop[$i]['lng']);
				unset($shop[$i]['lat']);
			}
			return array('error'=>0, 'error_msg'=>'Get Success', 'data' => array( 'shops' => $shop));
		}
	}

	/**
	 *  获得店铺在卖商品
	 *  $name 名字
	 */
	public function items($params = array())
	{
		if ( ! isset($params['shop_id']) ) {
			return array('error'=>1, 'error_msg'=>'shop_id undefined', 'data' => array());
		}

		$shop = $this->ci->shop_biz->getShopById($params['shop_id']);
		
		if ( empty($shop) ) {
			return array('error'=>1, 'error_msg'=>'No this shop', 'data' => array());
		}

		$this->ci->load->business('partner/shop_item_biz');
		$params['fields'] = isset($params['fields']) ? $params['fields'] 
			: ' * ';
		if ( ! isset($params['limit']) ) {
			$params['limit'] = 10;	
		}
		$fields = explode(',', str_replace(' ','',$params['fields']));
		foreach($fields as $k => $val){
			$fields[$k] = 'a.' . $val;
		}
		$fields = implode(',', $fields);
		//全部网点
		if ( isset($params['shop_id']) && $params['shop_id'] ) {			
			$sql = " select a.* from cp_item a left join cp_shop b on a.partner_id = b.pid 
					where a.status = 1 and (a.attribute & 1024 = 1024) or (a.attribute & 512 = 512 and a.partner_id = b.pid and b.id = $params[shop_id])";
			$sql .= ' limit ' . $params['limit'];
			$items = $this->ci->shop_item_biz->getItemQuery($sql);
			if ( ! empty($items) ) {
				foreach ($items as $i => $item) {
					$items[$i]['image'] = $items[$i]['image_value'];
					
					$items[$i] = array_merge(array('item_id'=>$items[$i]['id']), $items[$i]);
					unset($items[$i]['id']);
					
					foreach ($item as $j => $row) {
						$fields = 'item_id,category_id,item_name,item_code,price_value,price_real,
						unit_value,storage,spec_value,image,status,status_value';
						if ( ! in_array( $j, explode(',', $fields) )) {
							unset($items[$i][$j]);
						}
					}
				}
				return array('error'=>0, 'error_msg'=>'Get Success', 'data' => array( 'items' => $items));
			} else {			
				return array('error'=>1, 'error_msg'=>'No items', 'data' => array( 'items' => array()));
			}
		} else {
			return array('error'=>1, 'error_msg'=>'shop_id must', 'data' => array());
		}
	}	
}