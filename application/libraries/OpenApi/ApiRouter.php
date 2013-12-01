<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/************************************************************
 *
 * OpenApi 	路由信息，根据api_name 来确定所调用的函数和载入的lib库
 * @owner 	danchex
 * @author 	danchex, xudb
 *
*************************************************************/

$ApiRouter = array(
	//测试API
	'miapi.device.auth'	=> array(
		'lib' => 'Api_device', 
		'act' => 'auth'
	),

	//商品类目API
	'miapi.item.cate.list'	=> array(
		'lib' => 'Api_item_cate', 
		'act' => 'getCateList'
	),
	'miapi.item.cate.get'	=> array(
		'lib' => 'Api_item_cate', 
		'act' => 'getCate'
	),
	// 'miapi.item.cate.create'=> array(
	// 	'lib' => 'Api_item_cate', 
	// 	'act' => 'createCategory'
	// ),
	// 'miapi.item.cate.del'   => array(
	// 	'lib' => 'Api_item_cate', 
	// 	'act' => 'deleteCate'
	// ),
	// 'miapi.item.cate.getMyCate' => array(
	// 	'lib' => 'Api_item_cate', 
	// 	'act' => 'getMyCate'
	// ),
	// 'miapi.item.cate.updateCate' => array(
	// 	'lib' => 'Api_item_cate', 
	// 	'act' => 'updateCate'
	// ),

	//商品API
	'miapi.item.getItem' => array(
		'lib' => 'Api_item', 
		'act' => 'getItem'
	),
	'miapi.item.getItemList' => array(
		'lib' => 'Api_item', 
		'act' => 'getItemList'
	),
	'miapi.item.getItemCount' => array(
		'lib' => 'Api_item', 
		'act' => 'getItemCount'
	),
	'miapi.item.itemAdd' => array(
		'lib' => 'Api_item', 
		'act' => 'itemAdd'
	),
	'miapi.item.itemDel' => array(
		'lib' => 'Api_item', 
		'act' => 'itemDel'
	),
	'miapi.item.itemEdit' => array(
		'lib' => 'Api_item', 
		'act' => 'itemEdit'
	),

	//快递派收API
	'miapi.delivery.order.list' => array(
		'lib' => 'Api_delivery_order', 
		'act' => 'index'
	),
	'miapi.delivery.order.get' => array(
		'lib' => 'Api_delivery_order', 
		'act' => 'get'
	),

	//网点API
	'miapi.spot.getList' => array(
		'lib' => 'Api_spot', 
		'act' => 'getSpotList'
	),
	'miapi.spot.add' => array(
		'lib' => 'Api_spot', 
		'act' => 'addSpot'
	),
	'miapi.spot.del' => array(
		'lib' => 'Api_spot', 
		'act' => 'delSpot'
	),
	'miapi.spot.update' => array(
		'lib' => 'Api_spot', 
		'act' => 'updateSpot'
	),
	'miapi.spot.getMySpot' => array(
		'lib' => 'Api_spot',
		'act' => 'getMySpot'
	),
	'miapi.spot.getSpot' => array(
		'lib' => 'Api_spot', 
		'act' => 'getSpot'
	),

	//店铺API
	'miapi.shops.list' => array(
		'lib' => 'Api_shop',
		'act' => 'index'
	),
	'miapi.shop.get' => array(
		'lib' => 'Api_shop', 
		'act' => 'get'
	),
	'miapi.shop.follow' => array(
		'lib' => 'Api_shop', 
		'act' => 'follow'
	),
	'miapi.shop.follow.list' => array(
		'lib' => 'Api_shop', 
		'act' => 'follow_list'
	),
	'miapi.shop.unfollow' => array(
		'lib' => 'Api_shop', 
		'act' => 'unfollow'
	),
	'miapi.shop.items.list' => array(
		'lib' => 'Api_shop', 
		'act' => 'items'
	),

	//用户API
	'miapi.user.login' => array(
		'lib' => 'Api_user', 
		'act' => 'login'
	),
	'miapi.user.register' => array(
		'lib' => 'Api_user', 
		'act' => 'register'
	),
	'miapi.user.active' => array(
		'lib' => 'Api_user',
		'act' => 'active'
	),
	'miapi.user.bind' => array(
		'lib' => 'Api_user', 
		'act' => 'bind'
	),
	'miapi.user.get' => array(
		'lib' => 'Api_user', 
		'act' => 'get'
	),
	'miapi.user.address.get' => array(
		'lib' => 'Api_user_address', 
		'act' => 'get'
	),
	'miapi.user.address.add' => array(
		'lib' => 'Api_user_address', 
		'act' => 'add'
	),


	//交易API
	'miapi.trade.order.get' => array(
		'lib' => 'Api_trade_order', 
		'act' => 'get'
	),
	'miapi.trade.order.list' => array(
		'lib' => 'Api_trade_order', 
		'act' => 'index'
	),
	'miapi.trade.order.count' => array(
		'lib' => 'Api_trade_order', 
		'act' => 'count'
	),
	'miapi.trade.order.create' => array(
		'lib' => 'Api_trade_order', 
		'act' => 'create'
	),
	'miapi.trade.order.receive' => array(
		'lib' => 'Api_trade_order', 
		'act' => 'receive'
	),


	//优惠券API
	'miapi.coupons.imprest.get' => array(
		'lib' => 'Api_coupons_imprest', 
		'act' => 'get'
	),
	'miapi.coupons.imprest.items.get' => array(
		'lib' => 'Api_coupons_imprest', 
		'act' => 'getItems'
	),
	'miapi.coupons.imprest.list' => array(
		'lib' => 'Api_coupons_imprest', 
		'act' => 'index'
	),
	'miapi.coupons.imprest.count' => array(
		'lib' => 'Api_coupons_imprest', 
		'act' => 'count'
	),
	'miapi.coupons.imprest.delivery.request' => array(
		'lib' => 'Api_coupons_imprest', 
		'act' => 'delivery_request'
	),
	'miapi.coupons.imprest.receive' => array(
		'lib' => 'Api_coupons_imprest', 
		'act' => 'receive'
	),


	//测试API
	'miapi.developer.list'	=> array(
		'lib' => 'Api_developer', 
		'act' => 'index'
	),
	'miapi.developer.get'	=> array(
		'lib' => 'Api_developer', 
		'act' => 'get'
	),	
	'miapi.developer.add'	=> array(
		'lib' => 'Api_developer', 
		'act' => 'create'
	),		
	'miapi.developer.delete' => array(
		'lib' => 'Api_developer', 
		'act' => 'delete'
	),
	'miapi.developer.update' => array(
		'lib' => 'Api_developer', 
		'act' => 'update'
	)
);