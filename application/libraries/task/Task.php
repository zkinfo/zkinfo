<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author jetlee
 *--------------------------------------------------------------------------
 * 定时任务的逻辑代码
 *--------------------------------------------------------------------------
 * 帮助需要定时运行的业务逻辑解决通用的任务创建、运行、结束的策略
 * 业务逻辑不必关心任务如何运行的，只需要保证本身业务的正确性
 * 
 * 
 *
*/	

class Task
{
	
	public function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->library('logger');
		$this->ci->load->model("common_task_model");
	}

	/**
	 * 查询任务
	 * Enter description here ...
	 * @param string $task_code
	 */	
	function get_task($task_code)
	{
		$task = $this->ci->common_task_model->getOne( array('task_code' => $task_code) );
		if($task){
			$task['params'] = unserialize($task['params']);
		}
		return $task;
	}	
	
	/**
	 * 任务拼装方法 invoke_task($task_code, $params)
	 * 调用需要的任务程序类，执行类的方法
	 * @param array $task
	 */
	function invoke($task)
	{
		$class = $task['class_name'];
		$task_code = $task['task_code'];
		$params = $task['params'];
		
		$this->ci->load->library($class, null, "class_task");
		$ret = $this->ci->class_task->invoke_task($task_code, $params);
		return $ret;
	}

	/**
	 * 更新任务最新执行时间
	 * Enter description here ...
	 * @param string $task_code
	 */
	function task_timeup($task_code){
		$task = $this->get_task($task_code);
		return $this->ci->common_task_model->update(array('edate'=>date('Y-m-d H:i:s')), $task['id']);
	}

	/*
	 *--------------------------------------------------------------------------
	 * 以下方法 500mi 和 balunche 工程暂未使用,功能未明,保留待用
	 *--------------------------------------------------------------------------
	 * 
	 */	

	/**
	 * 创建TASK：如果任务不存在，则创建任务；如果任务存在，更新任务参数到初始状态。
	 * 此时任务状态status为0,默认参数'page_no','page_size','total','last_sync_id'为0
	 * start_sync_time 为当前时间
	 * @param unknown_type $task_code 以$task_type：为前缀
	 * @param unknown_type $class_name 任务调用的业务逻辑类名
	 * @param unknown_type $params：数组形式，默认会存储'page_no','page_size','total','last_sync_id'
	 */
	function create_task($task_code,$params=array(),$class_name){

		$task_type = $this->get_task_type($task_code);
		/*
		foreach (array('page_no','page_size','total','last_sync_id') as $val)
		{
			if(!isset($params[$val])){
				$params[$val] = 0;
			}
		}
		*/
		//$params_data = $this->_serialize($params);
		return $this->ci->common_task_model->create_task($task_code,$task_type,0,$params,$class_name);
	}
	
	/**
	 * 刷新任务,如果任务不存在，则创建任务；如果任务存在，默认更新任务为处理中。
	 * 此时任务状态status为1,处理中
	 * @param unknown_type $task_code
	 * @param unknown_type $type
	 * @param unknown_type $status
	 */
	function refresh_task($task_code,$params=array(),$status=1){
		$task_type = $this->get_task_type($task_code);
		/*
		foreach (array('page_no','page_size','total','last_sync_id') as $val)
		{
			if(!isset($params[$val])){
				$params[$val] = 0;
			}
		}
		*/
		return $this->ci->common_task_model->create_task($task_code,$task_type,1,$params);
	}
	
	/**
	 * 初始任务,如果任务不存在，则创建任务；如果任务存在，默认更新任务,默认处理中。
	 * 此时任务状态status为1,处理中
	 * @param unknown_type $task_code
	 * @param unknown_type $type
	 * @param unknown_type $status
	 */

	/*
	function init_task($task_code,$params=array(),$status=1,$class_name=''){
		$task_type = $this->get_task_type($task_code);
		return $this->ci->common_task_model->create_task($task_code,$task_type,$status,$params,$class_name);
	}
	*/
	
	/**
	 * 结束任务
	 * 此时任务状态status默认为9,处理结束；
	 * @param $task_code
	 * @param $status
	 */
	function end_task($task_code){
		$this->ci->common_task_model->end_task($task_code,9);
	}
	
	/**
	 * 结束任务并重新初始化任务，用于循环跑的任务
	 * 此时任务状态status默认为0,恢复到初始状态；
	 * @param $task_code
	 * @param $status
	 */
	function end_task_init($task_code,$params){
		$this->ci->common_task_model->end_task($task_code,0,$params);
	}

	function get_task_type($task_code){
		$arr = explode(':',$task_code);
		return $arr[0];
	}
	
}
//Class::EOF