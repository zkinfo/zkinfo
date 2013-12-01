<?php
class Reimage
{	
	public function __construct()
	{
		$this->ci =& get_instance();
		$this->config = $this->ci->config;
	
	}
	/**
	 * 分享图片裁剪
	 * Enter description here ...
	 * @param unknown_type $upurl
	 * @param unknown_type $filename
	 * @param unknown_type $caturl
	 */
	public function sharePicCrop($upurl,$filename,$caturl)
	{
		//240x160  50x50
		$this->ci->load->library('image_moo');
		$this->ci->image_moo->load($caturl)
			 ->resize_crop(92,58)->save($upurl.'92x58_'.$filename,true)
			 ->resize_crop(50,50)->save($upurl.'50x50_'.$filename,true)
			 ->resize_crop(240,160)->save($upurl.'240x160_'.$filename,true)
			 ->resize(540,320)->save($upurl.'540x320_'.$filename,true);
		//$this->picrename($upurl,'92x58_',$filename);
		//$this->picrename($upurl,'540x320_',$filename);	
	}
	
	/**
	 * 分享图片裁剪
	 * Enter description here ...
	 * @param unknown_type $upurl
	 * @param unknown_type $filename
	 * @param unknown_type $caturl
	 */
	public function actPicCrop($upurl,$filename,$caturl)
	{
		//240x160  50x50 460x340
		$this->ci->load->library('image_moo');
		$this->ci->image_moo->load($caturl)
			 ->resize_crop(92,58)->save($upurl.'92x58_'.$filename,true)
			 ->resize_crop(50,50)->save($upurl.'50x50_'.$filename,true)
			 ->resize(460,340)->save($upurl.'460x340_'.$filename,true);
		//$this->picrename($upurl,'92x58_',$filename);
		//$this->picrename($upurl,'540x320_',$filename);	
	}
	
	/**
	 * 修改原图后缀大小写
	 * Enter description here ...
	 * @param unknown_type $urlpath
	 * @param unknown_type $type
	 * @param unknown_type $filename
	 */
	public function repicrename($urlpath="",$type,$filename="")
	{
		$name = substr($filename,0,strrpos($filename, '.')); 
		$oldfile = $urlpath.$name.$type;
		$newfile = $urlpath.$name.strtolower($type);
		if(file_exists($oldfile)){
			rename($oldfile,$newfile);
		}
	}
	
}




?>