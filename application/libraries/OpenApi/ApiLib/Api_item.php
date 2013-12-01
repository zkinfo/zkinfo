<?php
/**
 * 500mi OpenAPI libirary
 * 获得商品 列表、获得单个商品信息、添加商品、删除商品、编辑商品
 * 传入参数 $param 
 * 返回值 $ret
 */
require_once(APPPATH . 'libraries/OpenApi/ApiErrInfo.php');//加载错误信息配置文件
class Api_item
{
	public function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->business('item/item_biz');
	}
	//根据商品id获得item
	public function getItem($param)
	{
		if (isset($param['item_id'])) //验证必选参数是否全部设置
		{
			$item_id=$param['item_id'];
		}else
		{
			return ERR_PARAM;
		}
		$item = $this->ci->item_biz->getItemById($item_id);
		if(!empty($item)) 
		{
			unset($item['partner_id']);
			unset($item['puser_id']);
			return $item;
		}else 
		{
			return ERR_DB_SEARCH;
		}
	}
	//根据用户的partner_id获得商品信息列表，返回id和item_name
	public function getItemlist($param)
	{
		if (isset($param['partner_id'])) //验证必选参数是否全部设置
		{
			$partner_id=$param['partner_id'];
		}else
		{
			return ERR_PARAM;
		}
		$items = $this->ci->item_biz->getItemListByPartnerIds($partner_id,'id,item_name');	
		return $items;
	}
	//根据用户的partner_id获得商品数量，可以实现比较快速的获得发布商品数量
	public function getItemCount($param)
	{
		if (isset($param['partner_id'])) //验证必选参数是否全部设置
		{
			$partner_id=$param['partner_id'];
		}else
		{
			return ERR_PARAM;
		}
		$result = $this->item_biz->getPartnerItemList($partner_id);
		$items = $result['total'];
		return $items;
	}
	//根据用户传入参数，添加商品
	public function itemAdd($param)
	{
		if (isset($param['partner_id']) && isset($param['category_id']) && isset($param['item_name']) && isset($param['price']) ) //验证必选参数是否全部设置
		{
			$addData['partner_id'] = $param['partner_id'];
			$addData['category_id'] = $param['category_id'];
			$addData['item_name'] = $param['item_name'];
			$addData['price'] = $param['price'];
		}else
		{
			return ERR_PARAM;
		}
		isset($param['description']) ? $addData['description'] = $param['description']:null;
		isset($param['url']) ? $addData['url'] = $param['url']:null;
		$ret = $this->ci->item_biz->addItem($addData);
		if(!empty($ret)) {
			return $ret;
		} else {
			return ERR_DB_OPT;
		}
	}
	//根据item_id删除商品，删除前先根据用户partner_id get数据是否能得到，能得到说明是该用户的商品，可以删除
	public function itemDel($param)
	{
		isset($param['partner_id']) ? $addData['partner_id'] = $param['partner_id']:null;
		$item = $this->ci->item_biz->getItemById($param['item_id']);
		if(!empty($item)) 
		{
			$ret = $this->ci->item_biz->deleteItem($param['item_id']);
			return $ret;
		} else {
			return ERR_ITEM_AUTH;
		}
	}
	//根据用户传入参数，添加商品
	public function itemEdit($param)
	{
		isset($param['partner_id']) ? $addData['partner_id'] = $param['partner_id']:null;
		$item = $this->ci->item_biz->getItemById($param['item_id']);
		if(!empty($item)) 
		{
			if (isset($param['partner_id']) && isset($param['category_id']) && isset($param['item_name']) && isset($param['price']) ) //验证必选参数是否全部设置
			{
				$addData['partner_id'] = $param['partner_id'];
				$addData['category_id'] = $param['category_id'];
				$addData['item_name'] = $param['item_name'];
				$addData['price'] = $param['price'];
			}else
			{
				return ERR_PARAM;
			}
			isset($param['description']) ? $addData['description'] = $param['description']:null;
			isset($param['url']) ? $addData['url'] = $param['url']:null;
			$ret = $this->ci->item_biz->updateItem($editData,$param['item_id']);
			if(!empty($ret)) {
				return $ret;
			} else {
				return "no items found";
			}
		}else
		{
			return ERR_ITEM_AUTH;
		}
	}
}