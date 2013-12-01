<?php
/**
 * 500mi OpenAPI libirary
 * 网点相关api 获得网点列表、获得单个网点信息、添加网点、删除网点、编辑网点，判断输入地址是否在网点列表中----cp平台要求添加
 * 传入参数 $param
 * 返回值 $ret
 */
class Api_spot
{
	public function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->business('spot/spot_biz');
	}

	//获得网点列表,可设置type、city、district进行查询
	public function getSpotList($param)
	{
		$ret = array();
		isset($param['type'])?$searchParam['type']=$param['type']:null;
		isset($param['city'])?$searchParam['city']=$param['city']:null;
		isset($param['district'])?$searchParam['district']=$param['district']:null;
		$searchParam['status']=3;
		$db_ret = $this->ci->spot_biz->getSpotList($searchParam);
		for ($i=0; $i <count($db_ret) ; $i++) {
			$ret[$i]['type'] = $db_ret[$i]['type'];
			$ret[$i]['city'] = $db_ret[$i]['city'];
			$ret[$i]['district'] = $db_ret[$i]['district'];
			$ret[$i]['spot_name'] = $db_ret[$i]['spot_name'];
			$ret[$i]['address'] = $db_ret[$i]['address'];
			$ret[$i]['spot_code'] = $db_ret[$i]['spot_code'];
		}
		return $ret;
	}
	//添加网点
	public function addSpot($param)
	{
		$ret = array();
		if (isset($param['partner_id']) && isset($param['spot_name']) && isset($param['type']) && isset($param['city']) && isset($param['area_code'])) //验证必选参数是否全部设置
		{
			$insertParam['type'] = $param['type'];
			$area_code = $param['area_code'];
			$insertParam['spot_code'] = $this->autoCode($area_code, $param['type']);
			$insertParam['owner_id'] = $param['partner_id'];
			$insertParam['city'] = $param['city'];
		}else
		{
			return ERR_PARAM;
		}
		//设置可选参数
		isset($param['province']) ? $insertParam['province'] = $param['province'] : null;
		isset($param['address']) ? $insertParam['address'] = $param['address'] : null;
		isset($param['lng']) ? $insertParam['lng'] = $param['lng'] : null;
		isset($param['lat']) ? $insertParam['lat'] = $param['lat'] : null;
		$insertParam['status'] = '3';
		// die(var_dump($insertParam));
		$insertParam['cdate'] = date('Y-m-d H:i:s');
		$ret = $this->ci->spot_biz->addSpot($insertParam);
		if ($ret>0)
		{
			return $ret;
		}else
		{
			return ERR_DB_OPT;
		}
	}
	//自动编码，添加网点时调用
	private function autoCode($area_code, $type) {
		//获取该类最大编号.
		$code = $this->ci->spot_biz->getMaxCode($area_code, $type);
		return ++$code;
	}
	//删除网点，根据partner_id查到相关的网点信息进行删除
	public function delSpot($param)
	{
		$ret = array();
		isset($param['spot_code'])?$delParam['spot_code']=$param['spot_code']:null;
		$delParam['owner_id']=$param['partner_id'];
		$db_ret = $this->ci->spot_biz->getSpotBySpotCode($delParam['spot_code'],'id');
		// die(var_dump($db_ret));
		if ($db_ret>0) {
			$db_ret = $this->ci->spot_biz->deleteSpot($db_ret['id']);
			if ($db_ret>0) {
				return true;
			}else
			{
				return false;
			}
		}else
		{
			return "no auth to del";
		}
	}
	//编辑网点
	public function updateSpot($param)
	{
		$ret = array();
		$editParam['owner_id'] = $param['partner_id'];
		$editParam['spot_name'] = $param['spot_name'];
		$editParam['province'] = $param['province'];
		$editParam['city'] = $param['city'];
		$editParam['district'] = $param['district'];
		$editParam['address'] = $param['address'];
		$editParam['lng'] = $param['lng'];
		$editParam['lat'] = $param['lat'];
		$editParam['status'] = '3';

		isset($param['spot_code'])?$delParam['spot_code']=$param['spot_code']:null;
		$delParam['owner_id']=$param['partner_id'];
		$db_ret = $this->ci->spot_biz->getSpotBySpotCode($delParam['spot_code'],'id');
		if ($db_ret>0) {
			if(!empty($editParam['spot_name']) && !empty($editParam['city']))
			{
				$editParam['cdate'] = date('Y-m-d H:i:s');
				$ret = $this->ci->spot_biz->updateById($editParam,$db_ret['id']);
				if ($ret>0) {
					return $ret;
				}else
				{
					return false;
				}
			}
		}else
		{
			return "no auth to edit";
		}
	}
	//获得网点列表,可设置type(查询仓库或者网点)、必须输入user_id(查询该用户的仓库或网点)进行查询
	public function getMySpot($param)
	{
		$ret = array();
		isset($param['owner_id'])?$searchParam['user_id']=$param['user_id']:null;
		isset($param['type'])?$searchParam['type']=$param['type']:null;
		$searchParam['status']=3;
		$db_ret = $this->ci->spot_biz->getSpotList($searchParam);
		for ($i=0; $i <count($db_ret) ; $i++) {
			$ret[$i]['type'] = $db_ret[$i]['type'];
			$ret[$i]['city'] = $db_ret[$i]['city'];
			$ret[$i]['district'] = $db_ret[$i]['district'];
			$ret[$i]['spot_name'] = $db_ret[$i]['spot_name'];
			$ret[$i]['address'] = $db_ret[$i]['address'];
			$ret[$i]['spot_code'] = $db_ret[$i]['spot_code'];
		}
		return $ret;
	}
	//获得网点详细信息，传入网点id，返回详细信息
	public function getSpot($param)
	{
		$ret = array();
		isset($param['spot_code'])?$searchParam['spot_code']=$param['spot_code']:null;
		$searchParam['status']=3;
		$ret = $this->ci->spot_biz->getSpotBySpotCode($searchParam['spot_code']);
		return $ret;
	}
}