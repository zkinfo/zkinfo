<?php
/**
 * 500mi OpenAPI libirary
 * 获得商品类目列表、获得单个商品类目信息，获得产品信息
 * 传入参数 $param 
 * 返回值 $ret
 */
require_once(APPPATH . 'libraries/OpenApi/ApiErrInfo.php');//加载错误信息配置文件
class Api_item_cate
{
	public function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->business('item/category_biz');
	}
	//得到类目列表,返回类目列表,根据用户的partner_id获得其所创建的类目，partner_id为0为公共类目
	public function getCateList($param)
	{
		if (isset($param['partner_id'])) //验证必选参数是否全部设置
		{
			$partner_id=$param['partner_id'];
		}else
		{
			return ERR_PARAM;
		}

		$result = $this->ci->category_biz->getCategoryByPartnerIds(implode(',', array($partner_id,0)));//执行数据库操作
		$cate_list = $result['list'];//执行数据库操作

		$ret = null;//控制返回值
		if (!$cate_list) {
			return $ret;
		}
		for ($i=0; $i < count($cate_list); $i++) 
		{ 
			$ret[$i]['cate_name'] = $cate_list[$i]['name'];
			$ret[$i]['cate_id'] = $cate_list[$i]['id'];
		}
		return $ret;
	}
	//查询某个类目
	public function getCate($param)
	{
		if (isset($param['partner_id']) && isset($param['cate_id'])) //验证必选参数是否全部设置
		{
			$partner_id=$param['partner_id'];
			$cate_id=$param['cate_id'];
		}else
		{
			return ERR_PARAM;
		}

		$cate = $this->ci->category_biz->getCategoryById($cate_id);

		$ret = null;
		if (!$cate || $cate['partner_id'] != $partner_id) 
		{
			return $ret;
		}
		$ret['cate_name'] = $cate['name'];
		$ret['cate_id'] = $cate['id'];
		$ret['creator'] = $cate['creator'];
		return $ret;
	}
	
/**
 * 华丽的分割线，下面的函数未使用
 */
	//查询partner所创建的类目
	public function getMyCate($param)
	{
		$cate_list = $this->ci->category_biz->getChildCategoryList($param['partner_id']);
		$ret = null;
		if (!empty($cate_list)) 
		{
			return $ret;
		}
		for ($i=0; $i < count($cate_list); $i++)
		{
			$ret[$i]['cate_name'] = $cate_list[$i]['name'];
			$ret[$i]['cate_id'] = $cate_list[$i]['id'];
			$ret[$i]['creator'] = $cate_list[$i]['creator'];
		}
		return $ret;
	}
	//添加类目列表，参数传入cate_name，添加时取得partner_id，加入到cp_catagory表中，返回id
	public function addCate($param)
	{
		$ret = $this->ci->category_biz->addCategory(array('name' => $param['cate_name'] ,'partner_id' => $param['partner_id'] , 'props' => '999'));
		if(!empty($ret))
		{
			return false;
		}
		return $ret;
	}

	//根据传入的类目id和 partner_id删除相应类目
	public function deleteCate($param)
	{
		$cate = $this->ci->category_biz->getCategoryById($param['cate_id']);
		if(!empty($cate))
		{
			$ret = $this->ci->category_biz->deleteCategory($param['cate_id']);
			return $ret;
		}else if (empty($cate) || $cate['partner_id'] != $param['partner_id'])
		{
			return "no auth to delete this category";
		}
	}
	public function updateCate($param)
	{
		$cate = $this->ci->category_biz->getCategoryById($param['cate_id']);
		if(!empty($cate))
		{
			$ret = $this->ci->category_biz->updateCategory(array('name' => $param['cate_name']),$param['cate_id']);
			return $ret;
		}else if (empty($cate) || $cate['partner_id'] != $param['partner_id'])
		{
			return "no auth to edit this category";
		}
	}
}
