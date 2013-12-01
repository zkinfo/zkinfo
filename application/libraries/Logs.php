<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 *******************************************************************************
 *
 * 系统日志类（数据库）
 * @author danchex
 *
 *******************************************************************************
 */
require_once(APPPATH.'./models/logs_model.php');
class Logs
{
	private function __construct()
	{		
		$this->ci =& get_instance();
	}

	public static function get_instance($object = null)
	{
		if (is_null($object)) {
			require_once(APPPATH.'./libraries/Logger.php');
			return new Logger;
		} else if ($object) {
			$object = ucfirst(strtolower($object)).'_log';
			require_once(APPPATH.'./libraries/logs/'.$object.'.php');
			return new $object;
		}
	}
}
/* End of file: Logs.php */