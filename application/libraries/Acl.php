<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Ȩ�޿�����
 * @author danchex
 *
 * ���Class Method ���Ʒ���Ȩ��
 * Role -> Class::Method -> Permission
 * 4:�� 2:д 1:ɾ 7:All
 */
class Acl
{
	private $acl;

	public function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->business("user/user_biz");
		$this->ci->load->business('acl/app_biz');
		$this->ci->load->business('acl/menu_biz');
		$this->ci->load->business('acl/role_biz');
		$this->ci->load->business('acl/resource_biz');
		$this->ci->load->business('acl/permission_biz');
		$this->ci->load->library('session');

		$this->init();
	}

	/* ���Ȩ�� Acl
	 * @param int $role
	 * @param string $class
	 * @param string $method
	 * @return bool ����Ȩ��
	**/
	public function isAllowedUrl($url)
	{
		$role_ids = $this->ci->session->userdata('role_ids');
		return $this->isAllowed($role_ids, $url);
	}

	public function isAllowed($roleid, $resource, $action = null) 
	{
		//var_dump($resource);
		if ($resource == '') {
			return true;
		}

		if ( ! $this->acl->has(new Zend_Acl_Resource($resource))) {
			$resource = preg_replace('/\/[^\/]*$/', '', $resource);
			return $this->isAllowed($roleid, $resource, $action);
		}

		return $this->acl->isAllowed($roleid, $resource, $action);
	}

	public function acl_load($list)
	{
		$_list = array();
		foreach ($list as $row) {
			if ($this->isAllowedUrl($row['url'])) {
				$_list[] = $row;
			}
		}
		return $_list;
	}

	public function init() 
	{
		require_once('Zend/Acl.php');
		$this->ci->load->driver('cache', array('adapter' => 'file', 'backup' => 'file'));
		$acl = $this->ci->cache->get('acl.cache');		
		if ( $acl )
		{
			$this->acl = unserialize($acl);
		} else 
		{
			if ( !is_dir(APPPATH.'cache')) {
				mkdir(APPPATH.'cache');
			}
			//echo 'Saving to the cache!<br />';
			$this->set_acl();
			//$acl = serialize($this->acl);
			//$acl = $this->acl;

			// Save into the cache for 5 minutes
			$this->ci->cache->save('acl.cache', serialize($this->acl), 3600 * 24 * 365);
		}
		//var_dump(unserialize($acl)); die;
	}

	private function set_acl()
	{
		require_once('Zend/Acl.php');
		$this->acl = new Zend_Acl();

		$roles = $this->ci->role_biz->getRoleList();
		$resources = $this->ci->resource_biz->getList();
		$permissions = $this->ci->permission_biz->getList();

		//Add the roles to the ACL
		foreach($roles as $roles) {
			$role = new Zend_Acl_Role($roles['roleid']);
			$this->acl->addRole($role);
		}

		//Add the resources to the ACL
		foreach($resources as $resources) {
			$resource = new Zend_Acl_Resource($resources['url']);
			$this->acl->add($resource);
		}

		//Add the permissions to the ACL
		foreach($permissions as $perms) {
			if( empty($perms['roleid']) ) $perms['roleid'] = null;
			if( empty($perms['resource']) ) $perms['resource'] = null;
			if ($perms['permission'] == 7) {
				$this->acl->allow($perms['roleid'], $perms['resource']);
			} else {
				$this->acl->deny($perms['roleid'], $perms['resource']);
			}
		}
		//Change this to whatever id your adminstrators group is
		//$this->acl->allow('999');
		//print_r($this->acl); die;
	}
}

/* End of file: Acl.php */