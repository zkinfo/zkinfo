<?php
/**
 * @author jetlee remark:danchex
 *--------------------------------------------------------------------------
 * 计划任务中单个任务的实现类
 *--------------------------------------------------------------------------
 * 通过系统任务调用类，调到这个类，默认执行invoke_task方法
 *
 */
class Sms_process
{
	public function __construct()
	{
		$this->ci =& get_instance();
		$this->config = $this->ci->config;
		$this->ci->load->library('logger');

		require_once APPPATH.'business/msg/message_biz.php';
		$this->msg = Message_biz::factory('sms');
	}

	/**
	 * 任务入口
	 * 计划任务调用该类时，主动调用此方法
	 */
	public function invoke_task($taskcode, $params){
		$this->send($params);

		$this->ci->load->library('task/task');
		$taskupdate = $this->ci->task->task_timeup($taskcode);
		return true;
    }

	/**
	 * 发送短信
	 * Enter description here ...
	 */
	function send($params)
	{
		$mod = explode('/', $params['mod']);
		$this->ci->logger->log(6, '短信发送计划任务'.$params['mod'].'开始', 'smstask');
		//$max = $this->msg->getMsgCount(array('status'=>0,'cdate >'=>$time_start));
		//一次最大发送上限
		$max = 20;
		$time_start = date("Y-m-d 00:00:00",strtotime('-3 day'));
		$sql = "select * from tt_msg where status = 0 and MOD(id, $mod[1]) = $mod[0] order by id desc limit 5";
		//$sendlist = $this->msg->getMsgList(array('status'=>0,'MOD(id, '.$mod[1].') ='=>$mod[0], 'cdate >'=>$time_start, 'retry <=' => 5), '*', 0, $max);
		//$sendlist = $this->msg->getMsgList(array('status'=>0,'MOD(id, '.$mod[1].') ='=>$mod[0], 'cdate <'=>$time_start), '*', 0, $max);
		
		//
		$sendlist = $this->msg->getMsgQuery($sql);
		$sendCount = count($sendlist);
		$sendSuccess = 0;
		for($i=0; $i<$sendCount; $i++)
		{
			$send = $this->msg->_send($sendlist[$i]);
			if($send['error']=="0") {
				$sendSuccess++;
			}
			// sleep(10);
		}
		$this->ci->logger->log(6, '短信发送任务'.$params['mod'].'：总量:'.$sendCount.', 成功:'.$sendSuccess, 'smstask');
		$this->ci->logger->log(6, '短信发送计划任务'.$params['mod'].'结束', 'smstask');
	}

	/**
	 * 短信回调
	 * Enter description here ...
	 */
	public function smsback($mobile, $smsid, $status)
	{
		$smsid = substr($smsid, 0, strlen($smsid)-1); //回调smsid去掉一位 13315 33861 35.84
		$isexist = $this->msg->getMsg(array('mobile'=>$mobile, 'out_smsid'=>$smsid));
		if( ! empty($isexist)){
			if($status=='DELIVRD'){
				$update = $this->msg->updateMsg(array('status'=>2,'rev_status'=>$status), $isexist['id']);
			}else{
				$update = $this->msg->updateMsg(array('status'=>3,'rev_status'=>$status), $isexist['id']);
			}
			if($update){
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 0;
		}
	}
}
//Class::EOF