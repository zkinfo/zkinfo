<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 *******************************************************************************
 *
 * 用户
 * @author haibo danchex
 *
 *******************************************************************************
 * @version 0.6.2 - 0.2.0
 */
class User_biz
{
	private $const = 'const_user';

	public function __construct()
	{
		$this->ci =& get_instance();
		$this->config = $this->ci->config;
		$this->ci->load->model('user_model');
	}

    public function getUserById($id)
	{
		if ( ! is_numeric($id)) return array();
		return $this->ci->user_model->getOneById($id);
	}

	/**
	 * 通过Account用户信息
	 *
	 * @param 	string 	$account
	 * @return 	array
	 */
    public function getUser($params)
	{
		return $this->ci->user_model->getOne($params);
	}

	/**
	 * 用户列表
	 *
	 * @param 	array 	$params
	 * @param 	string 	$data
	 * @param 	int 	$start
	 * @param 	int 	$pagesize
	 * @param 	string 	$order
	 * @param 	string 	$sort
	 * @param 	array 	$like
	 * @return 	array
	 */
	public function getUserList($params = array(), $data = '*', $page = 1, $pagesize = 20, $orderby = 'id desc')
	{
		$result = $this->ci->user_model->getList($params, $data, $page, $pagesize, $orderby);
		return $result;
	}
	
	public function getUserListCount($params = array())
	{
		return $this->ci->user_model->getCount($params);
	}


	/**
	 * 查询ids用户列表
	 *
	 * @param 	string 	$user_ids
	 * @return 	array
	 */
	public function getUserListByIds($user_ids)
	{
		return $this->ci->user_model->getListByIds($user_ids);
	}	

	/**
	 * 查询role_ids用户列表
	 *
	 * @param 	string 	$user_ids
	 * @return 	array
	 */
	public function getUserListByRoleIds($role_ids, $data = '*', $page = 1, $pagesize = 20, $orderby = 'id desc')
	{
		return $this->ci->user_model->getListByRoleIds($role_ids, $data, $page, $pagesize, $orderby);
	}

	/**
	 * 已存在帐号?
	 *
	 * @param 	string 	$account
	 * @return 	array
	 */
	public function isAccount(& $data)
	{
		if ( ! isset($data['account']) ) return null;
		return $isAccount = $this->getUserByAccount($data['account']);
	}

	/**
	 * 已存在用户?
	 *
	 * @param 	array 	$data
	 * @return 	array
	 */
	public function isUser(& $data)
	{
		if ( ! isset($data['mobile']) || ! $data['mobile'] ) return null;
		return $isUser = $this->getUserByMobile($data['mobile']);
	}

	/**
	 * 新增用户
	 *
	 * @param 	array 	$data
	 * @return 	int
	 */
	public function addUser($data)
	{
		//检查是否已经存在
		$isAccount = $this->isAccount($data);
		$isUser = $this->isUser($data);
		if ( ! $data['mobile']) {
			unset($data['mobile']);
		}
		if ( empty($isAccount) && empty($isUser) ) {
			$data['ip'] = $this->ci->input->ip_address();
			if(!isset($data['type'])){
				$data["type"] = 1;
			}
			$data['attribute'] = ($data['type']-1) << 1 | 1;
			return $userId = $this->ci->user_model->insert($data);
		} else {
			return false; //已经存在用户
		}
	}

	/**
	 * 编辑用户
	 *
	 * @param 	array 	$data
	 * @param 	fixed 	$id
	 * @return 	bool
	 */
	public function updateUser($data, $id)
	{
		if ( ! $id) return false;
		return $bool = $this->ci->user_model->update($data, $id);
	}

	/**
	 * 删除用户
	 *
	 * @param 	fixed 	$id
	 * @param 	bool 	$real
	 * @return 	bool
	 */
	public function deleteUser($id)
	{
		return $bool = $this->ci->user_model->delete($id);
		 $ret;
	}

	/**
	 * 根据条件获得用户头像信息
	 * @param $getarr
	 */
    function getAvatar($user_id)
	{
		$user = $this->getUserById($user_id);
		$array = (isset($user['ext']) AND $user['ext'] != NULL) ? unserialize($user["ext"]) : array();
		if(is_array($array) AND array_key_exists("avatar", $array)){
			$result = $params["id"]."/"."150_".$array["avatar"];
		}else{
			$result = "150_avatar.jpg";
		}
		return $result;
	}

	/**
	 * 根据条件获得用户小头像信息
	 * @param $getarr
	 */
    function getSmallAvatar($user_id)
	{
		$user = $this->getUserById($user_id);
		if(!empty($user["ext"])){
			$array = unserialize($user["ext"]);
		}else{
			$array = "";
		}
		if(is_array($array)){
			if(array_key_exists("avatar",$array)){
				$result = $params["id"]."/"."50_".$array["avatar"];
			}else{
				$result = "50_avatar.jpg";
			}
		}else{
			$result = "50_avatar.jpg";
		}
		return 	$result;
	}

	public function newcomer(){
		$data = array();
		$data["status"] = "fail";
		$uid = $this->ci->session->userdata("uid");
		$account = $this->ci->session->userdata("account");
		$this->ci->load->business('trade/trade_order_biz');
		$this->ci->load->business('account/account_pay_biz');
		if($account != "ceshi" && $account != "xiaodian"){	
			$options = array(
				'id'		=>	$uid,
				'cdate >='	=>	"2012-12-01 00:00:00"
			);
			$user = $this->getUserById($uid);
			if($user && strtotime($user['cdate']) >= strtotime('2012-12-01 00:00:00')){
				$order_count = $this->ci->trade_order_biz->getOrderCount(array('puser_id'=>$uid, 'pay_status' => 1));
				if($order_count > 0){
					$data["message"] = "该活动只对从来没有交易过的用户开放";
				}else{
					// $count = $this->ci->account_pay_biz->getPayCount(array("uid"=>$uid,"type"=>1));
					// if($count >= 1){
						$data["status"] = "success";
					// }else{
					// 	$data["message"] = "请先充值";
					// }
				}
			}else{
				$data["message"] = "该活动只对12月1号之后注册的用户开放";
			}
		}else{
			$data['status'] = "success";
		}
		return $data;
	}
}
/* End of file: Users.php */