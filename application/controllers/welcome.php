<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends MY_Controller{

	protected  $_data;
	function __construct(){
		parent::__construct();
		$this->load->library('layout','moban01/layout_main');
	}

	public function index()
	{
		redirect('/work/work');
	}
}

?>