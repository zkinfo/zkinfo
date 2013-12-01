<?php

/******************************************************************************
 * 短信接口定义
 *
 * method : send($mobile, $content);
 *
 ******************************************************************************/

Interface Sms_Interface
{
	/**
	 * 发送短信
	 * @param $mobile
	 * @param $content
	 *
	 * @return array(
	 *     'errno', 		//错误
	 *     'error_msg', 	//错误信息
	 *     'remark' 		//通道返回原始值
	 * );
	 */
	public function send($mobile, $content, $smsid);
}
//Class:EOF