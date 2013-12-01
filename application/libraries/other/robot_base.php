<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*******************************************************************************
 *
 * 类  名 : 五百米（采集机器人）
 * 功　能 : 机器采集
 * 操作表 : 
 *
*******************************************************************************/

class Robot_Base
{
	protected $ci;

	public function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->library('Logger');
	}

} //Class:EOF