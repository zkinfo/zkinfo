<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function getallunit()
{	
	$CI =& get_instance();
	$CI->load->conster('const_unit',TRUE);
	return $CI->conster->item('unit','const_unit');
}

function getunit($unitid)
{	
	$CI =& get_instance();
	$CI->load->conster('const_unit',TRUE);
	$unitlist = $CI->conster->item('unit','const_unit');

	return isset($unitlist[$unitid]) ? $unitlist[$unitid] : '';
}