<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH."libraries/Views.php";

class Layout extends Views{
	//private $obj;
	private $layout;
	
	function __construct($layout = "default/layout_main")
	{
		parent::__construct();
		//$this->obj =& get_instance();
		$this->layout = $layout;
		$this->load->config('site_settings', TRUE);
		$this->siteconfig = $this->config->item('site_settings');
		$this->siteconfig['charset'] = $this->config->item('charset');
	}

	function setLayout($layout)
	{
	  $this->layout = $layout;
	}

	function view($view, $data=null, $return=false)
	{
		$loadedData = array();
		$loadedData['content_for_layout'] = $this->load->view($view,$data,true);

		if($return)
		{
			$output = $this->load->view($this->layout, $loadedData, true);
			return $output;
		}
		else
		{
			$this->load->view($this->layout, $loadedData, false);
		}
	}
	
	function template($template, $data=null, $return=false)
	{
		$loadedData = array();
		$loadedData['content_for_layout'] = $this->load->template($template,$data,true);
		$loadedData['_SC'] =  $this->siteconfig;

		//dump 数据， 开发调试用
		if ($this->input->get('xdump') !== false) {
			echo '<pre>';			
			print_r($data); 
			echo '</pre>';	
			die;
		}
		//export 导出数据， 只能导出 list 变量
		if ($this->input->get('xexport') !== false) {
			if ( ! isset($data['list'])) die('只能导出list变量');
			$name = $_SERVER['REDIRECT_URL'];
			$name = str_replace('/', '_', substr($name, 1)).'-'.date('Y-m-d').'.csv';
			header("Content-type:text/csv;charset=gbk");
			header("Content-Disposition:attachment;filename=".$name);
			header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
			header('Expires:0');
			header('Pragma:public');
			$str_row = array();
			foreach ($data['list'] as $i => $row) {
				if ( ! isset($str_row[0])) $str_row[0] = implode(',', array_keys($row));
				$str_row[] = iconv('utf-8', 'GBK', implode(',', $row));
			}
			echo implode("\n", $str_row);
			die;
		}

		// 
		$this->load->library('assets');
		$this->assets->set_header_start();

		if($return)
		{
			$output = $this->load->template($this->layout, $loadedData, true);
			return $output;
		}
		else
		{
			$this->load->template($this->layout, $loadedData, false);
		}
	}
}

/* End of file: Layout.php */