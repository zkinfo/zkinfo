<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 *--------------------------------------------------------------------------
 * 时间程序运行相关逻辑
 *--------------------------------------------------------------------------
 * 运行各种定时任务的逻辑
 * 主要有通用任务逻辑、
 * 特殊任务逻辑：订单处理、新浪粉丝同步
 * 不同类型的任务会有不同的调度策略和频率
 *
 */

class Timetask_process
{	
	public function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->library('logger');
	}
	
	/*
	 *--------------------------------------------------------------------------
	 * 通用任务处理
	 *--------------------------------------------------------------------------
	 * 获取状态为0的任务，运行任务
	 */	
	public function task_process($task_type)
	{
		$this->ci->logger->log(6, '任务处理主程序开始', 'task');
		$this->ci->load->model("common_task_model");
		$this->ci->load->library('task/task');
		
		$sync_num = 50;//一次同步任务数
		$params = array(
			'task_type'	=> $task_type,
			'status'	=> 0
		);
		$tasks = $this->ci->common_task_model->getList($params, '*', 0, $sync_num);
		$tnum = count($tasks);
		$success_num = 0;
		foreach ($tasks as $task){
			$task['params'] = unserialize($task['params']);
			$ret = $this->ci->task->invoke($task);
			
			if($ret == TRUE) $success_num = $success_num+1;
		}
		$this->ci->logger->log(6, '任务类型：'.$task_type.' 累计获取任务数:'.$tnum.', 处理成功:'.$success_num, 'task');	
		$this->ci->logger->log(6, '任务处理主程序结束', 'task');
		
	}
	
	/*
	 *--------------------------------------------------------------------------
	 * 以下方法 500mi 和 balunche 工程暂未使用,功能未明,保留待用
	 *--------------------------------------------------------------------------
	 * 
	 */		
	function init_task($param){		
		$tasks = array(
			'SMSSEND' =>array('code'=>'SMSSEND','class'=>'sms_process','page_no'=>1,'page_size'=>50,'status'=>0)    
		);
		//创建分页补偿处理任务
		$task_type_pre = $this->ci->config->item('TASK_SMSSEND');//'';
		foreach($tasks as $task){
			$task_type = $task_type_pre;
			$task_code = $task_type;
			$params = array(
				'page_no' => $task['page_no'],
				'page_size' => $task['page_size']
			);
			$class = $task['class'];
			$status = $task['status'];
			$this->ci->load->library('task/task');
			$ret = $this->ci->task->create_task($task_code,$params,'task/'.$class);			
		}		
	}

}
//Class::EOF