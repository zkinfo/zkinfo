<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 * Feeds
 * @author GongNation
 */
Class User_model extends MY_Model
{
	function __construct()
	{
		parent::__construct();
		$this->table_name = 'sms_user';
	}
}
