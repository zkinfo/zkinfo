<?php

/**
 * PHP 手机类
 * 实现逻辑：构造一个有效手机，判断是否有效手机，判断手机运营商，判断手机号码地区
 */

class Mobile {
	private $number;

	public static function isMobile($mobile = null)
	{
		if (is_null($mobile)) $mobile = $this->number;
		if (preg_match('/1[3458]{1}\d{9}/', $mobile)) {
			return TRUE;
		} else {
			return FALSE;
		}

	}
}

