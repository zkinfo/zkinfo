<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/************************************************************
 *
 * OpenApi 错误信息，定义api错误常量
 * @author xudb
 *
*************************************************************/

define("ERR_PARTER_CONFIRM", 	"读取partner_id失败，所提供appcode错误");
define("ERR_PARAM", 			"api调用参数错误，请确认参数类型和数量");
define("ERR_SIGN", 				"验证app签名失败");
define("ERR_API_ROUTE", 		"api路由查找失败");
define("ERR_LOAD_LIB", 			"api 库加载失败");
define("ERR_DB_SEARCH", 		"数据库查找失败");
define("ERR_DB_OPT", 			"数据库操作失败");
define("ERR_ITEM_AUTH", 		"应用权限验证失败，没有权限操作该项");
define("ERR_NAME", 				"用户名错误");
define("ERR_MOBILE", 			"手机号码错误");
define("ERR_NOT_UNIQUE", 		"手机号码已经被注册");
define("ERR_ACCOUNT", 			"帐号格式错误");
define("ERR_INVALID_ACCOUNT", 	"帐号不存在");
define("ERR_UNIQUE_MOBILE", 	"手机绑定多个帐号");
define("ERR_ACTIVED_ACCOUNT", 	"帐号待激活");
define("ERR_PASSWORD", 			"密码错误");
define("INVALID_CODE", 			"激活码错误");
define("ACTIVED_ACCOUNT", 		"帐号已激活");
define("ERR_BIND", 				"已经绑定");