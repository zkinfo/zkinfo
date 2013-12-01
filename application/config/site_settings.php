<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| site_settings
|--------------------------------------------------------------------------
|
| 原来老的系统的一些全局变量改成配置，使用还是很方便的
| 设置站点全局需要的一些配置
|
*/
// 系统维护页面
$config['is_weihu']		= FALSE; 

//配置参数
$config['machine_name'] = '';  //机器名，用于日志前缀、环境识别

$config['siteurl'] 		= 'http://work.tlcjw.com';
$config['base_url'] 	= 'http://work.tlcjw.com';
$config['work_url'] 	= 'http://work.tlcjw.com';
$config['boss_url'] 	= 'http://boss.tlcjw.com';
$config['passport_url'] = 'http://boss.tlcjw.com';
$config['analysis_url'] = 'http://fenxi.tlcjw.com';
$config['cp_url'] 		= 'http://www.tlcjw.com';
$config['redis_url']	= '';

$config['sitename'] 	= '成绩网';
$config['adminemail'] 	= '';
$config['img_url'] 		= '';
$config['res_url'] 		= '';

//开发环境搜索引擎地址
if (ENVIRONMENT == 'production') {
	$config['es_url'] 		= '';
} else if (ENVIRONMENT == 'development') {
	$config['es_url'] 		= '';
}

//系统管理员手机，用于接收系统警报等信息
$config['admin_mobile'] = '';
$config['caiwu_mobile'] = '';

?>
