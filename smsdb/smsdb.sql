/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50611
Source Host           : localhost:3306
Source Database       : smsdb

Target Server Type    : MYSQL
Target Server Version : 50611
File Encoding         : 65001

Date: 2013-11-06 10:49:04
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for as_properties
-- ----------------------------
DROP TABLE IF EXISTS `as_properties`;
CREATE TABLE `as_properties` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `as_id` bigint(20) NOT NULL COMMENT '主帐号ID',
  `as_code` varchar(20) DEFAULT '0' COMMENT '行政编码',
  `position` varchar(20) DEFAULT NULL COMMENT '职务',
  `agency_type` tinyint(4) DEFAULT '0' COMMENT '订单状态 ，0 默认有效 1待审核 -1 失效',
  `status` tinyint(4) DEFAULT '0' COMMENT '订单状态 ，0 默认有效 1待审核 -1 失效',
  `cdate` datetime DEFAULT NULL,
  `edate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='行政信息变更表';

-- ----------------------------
-- Table structure for sms_administrative
-- ----------------------------
DROP TABLE IF EXISTS `sms_administrative`;
CREATE TABLE `sms_administrative` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL COMMENT '主帐号ID',
  `as_name` varchar(100) DEFAULT NULL COMMENT '行政姓名',
  `address` varchar(200) DEFAULT NULL COMMENT '具体地址',
  `contact_phone` varchar(20) DEFAULT NULL COMMENT '联系电话',
  `contact_mobile` varchar(11) DEFAULT NULL COMMENT '手机',
  `status` tinyint(4) DEFAULT '0' COMMENT '订单状态 ，0 默认有效 1待审核 -1 失效',
  `employ_date` datetime DEFAULT NULL COMMENT '入职时间，用来计算工龄',
  `birthday` datetime DEFAULT NULL,
  `cdate` datetime DEFAULT NULL,
  `edate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='行政信息表';

-- ----------------------------
-- Table structure for sms_class
-- ----------------------------
DROP TABLE IF EXISTS `sms_class`;
CREATE TABLE `sms_class` (
  `class_id` int(11) NOT NULL AUTO_INCREMENT,
  `grade_id` int(11) DEFAULT NULL,
  `class_name` varchar(50) DEFAULT NULL,
  `sum_student` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for sms_class_score
-- ----------------------------
DROP TABLE IF EXISTS `sms_class_score`;
CREATE TABLE `sms_class_score` (
  `class_score_id` int(11) NOT NULL AUTO_INCREMENT,
  `class_id` int(11) DEFAULT NULL,
  `grade_id` int(11) DEFAULT NULL,
  `exam_id` varchar(255) DEFAULT NULL,
  `chinese` tinyint(4) DEFAULT NULL,
  `math` tinyint(4) DEFAULT NULL,
  `english` tinyint(4) DEFAULT NULL,
  `chemistry` tinyint(4) DEFAULT NULL,
  `physical` tinyint(4) DEFAULT NULL,
  `biological` tinyint(4) DEFAULT NULL,
  `science` tinyint(4) DEFAULT NULL,
  `society` tinyint(4) DEFAULT NULL,
  `political` tinyint(4) DEFAULT NULL,
  `history` tinyint(4) DEFAULT NULL,
  `geography` tinyint(4) DEFAULT NULL,
  `cdate` datetime DEFAULT NULL,
  `edate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`class_score_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for sms_extra_exam
-- ----------------------------
DROP TABLE IF EXISTS `sms_extra_exam`;
CREATE TABLE `sms_extra_exam` (
  `extra_id` int(11) NOT NULL AUTO_INCREMENT,
  `stu_id` int(11) DEFAULT NULL,
  `extra_title` varchar(200) DEFAULT NULL,
  `extra_score` varchar(255) DEFAULT NULL,
  `exam_date` datetime DEFAULT NULL,
  `sum_employee` tinyint(4) DEFAULT NULL,
  `cdate` datetime DEFAULT NULL,
  `edate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`extra_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for sms_grade
-- ----------------------------
DROP TABLE IF EXISTS `sms_grade`;
CREATE TABLE `sms_grade` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `school_id` bigint(20) DEFAULT NULL,
  `grade_name` varchar(50) DEFAULT NULL,
  `cdate` datetime DEFAULT NULL,
  `edate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`grade_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for sms_purview
-- ----------------------------
DROP TABLE IF EXISTS `sms_purview`;
CREATE TABLE `sms_purview` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) DEFAULT '0' COMMENT '0默认 1学生 2教师 3行政人员 100管理员',
  `role_attribute` bigint(20) DEFAULT '0' COMMENT '角色属性，标记，按位与，1,2,4 ----1024系统保留权限',
  `as_url` varchar(20) DEFAULT NULL COMMENT '能访问路径',
  `status` tinyint(4) DEFAULT '0' COMMENT '订单状态 ，0 默认有效 1待审核 -1 失效',
  `cdate` datetime DEFAULT NULL,
  `edate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='权限控制表';

-- ----------------------------
-- Table structure for sms_school
-- ----------------------------
DROP TABLE IF EXISTS `sms_school`;
CREATE TABLE `sms_school` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `school_code` int(11) DEFAULT '0' COMMENT '0默认 1学生 2教师 3行政人员 100管理员',
  `school_name` varchar(80) DEFAULT NULL COMMENT '职务',
  `sum_employee` int(11) DEFAULT '0' COMMENT '教师人数',
  `address` varchar(200) DEFAULT NULL COMMENT '具体地址',
  `status` tinyint(4) DEFAULT '0' COMMENT '订单状态 ，0 默认有效 1待审核 -1 失效',
  `cdate` datetime DEFAULT NULL,
  `edate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='学校信息表';

-- ----------------------------
-- Table structure for sms_student
-- ----------------------------
DROP TABLE IF EXISTS `sms_student`;
CREATE TABLE `sms_student` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL COMMENT '主帐号ID',
  `std_name` varchar(100) DEFAULT NULL COMMENT '学生姓名',
  `address` varchar(200) DEFAULT NULL COMMENT '具体地址',
  `contact_phone` varchar(20) DEFAULT NULL COMMENT '联系电话',
  `contact_mobile` varchar(11) DEFAULT NULL COMMENT '手机',
  `status` tinyint(4) DEFAULT '0' COMMENT '订单状态 ，0 默认有效 1待审核 -1 失效',
  `enrollment_date` datetime DEFAULT NULL COMMENT '入学时间',
  `birthday` datetime DEFAULT NULL COMMENT '用来计算学生年龄',
  `cdate` datetime DEFAULT NULL,
  `edate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='学生信息表';

-- ----------------------------
-- Table structure for sms_teacher
-- ----------------------------
DROP TABLE IF EXISTS `sms_teacher`;
CREATE TABLE `sms_teacher` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL COMMENT '主帐号ID',
  `tch_name` varchar(100) DEFAULT NULL COMMENT '教师姓名',
  `address` varchar(200) DEFAULT NULL COMMENT '具体地址',
  `contact_phone` varchar(20) DEFAULT NULL COMMENT '联系电话',
  `contact_mobile` varchar(11) DEFAULT NULL COMMENT '手机',
  `status` tinyint(4) DEFAULT '0' COMMENT '订单状态 ，0 默认有效 1待审核 -1 失效',
  `employ_date` datetime DEFAULT NULL COMMENT '入职时间，用来计算教师的教龄',
  `birthday` datetime DEFAULT NULL,
  `cdate` datetime DEFAULT NULL,
  `edate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='教师信息表';

-- ----------------------------
-- Table structure for sms_user
-- ----------------------------
DROP TABLE IF EXISTS `sms_user`;
CREATE TABLE `sms_user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `account` varchar(50) DEFAULT NULL COMMENT '用户登录帐号：默认根据xx_code',
  `password` varchar(64) DEFAULT NULL COMMENT '默认密码：学号后6的md5码',
  `user_name` varchar(50) DEFAULT NULL COMMENT '名称：用户真实姓名',
  `role_id` int(11) DEFAULT '0' COMMENT '1学生 2教师 3行政人员 100管理员',
  `role_attribute` bigint(20) DEFAULT '0' COMMENT '角色权限属性，标记，按位与，每一位代表一种权限',
  `ip` varchar(24) DEFAULT NULL COMMENT 'ip',
  `last_ip` varchar(24) DEFAULT NULL COMMENT 'ip',
  `status` tinyint(3) DEFAULT '0' COMMENT '用户状态，0默认有效，-1删除，1待激活 9待审核',
  `active_type` tinyint(3) DEFAULT '0' COMMENT '激活状态:0未激活，1已激活',
  `active_time` datetime DEFAULT NULL COMMENT '帐号激活时间',
  `last_time` datetime DEFAULT NULL COMMENT '最后登录时间',
  `cdate` datetime DEFAULT NULL,
  `edate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8 COMMENT='用户帐号表';

-- ----------------------------
-- Table structure for std_examination
-- ----------------------------
DROP TABLE IF EXISTS `std_examination`;
CREATE TABLE `std_examination` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `school_id` bigint(20) DEFAULT '0' COMMENT '考试学校id',
  `grade_id` bigint(20) DEFAULT '0' COMMENT '年级',
  `examination_type` varchar(80) DEFAULT NULL COMMENT '考试类型',
  `Examination_name` varchar(200) DEFAULT NULL COMMENT '考试名称',
  `initiate_agency` varchar(80) DEFAULT NULL COMMENT '发起者',
  `examination_date` datetime DEFAULT NULL,
  `cdate` datetime DEFAULT NULL,
  `edate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=37135 DEFAULT CHARSET=utf8 COMMENT='学生考试信息表';

-- ----------------------------
-- Table structure for std_properties
-- ----------------------------
DROP TABLE IF EXISTS `std_properties`;
CREATE TABLE `std_properties` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `std_id` bigint(20) NOT NULL COMMENT '主帐号ID',
  `std_code` int(11) DEFAULT '0' COMMENT '学号',
  `grade_id` bigint(20) DEFAULT '0' COMMENT '年级',
  `class_id` bigint(20) DEFAULT NULL COMMENT '班级',
  `school_id` bigint(20) DEFAULT '0' COMMENT '订单状态 ，0 默认有效 1待审核 -1 失效',
  `school_code` varchar(20) DEFAULT NULL COMMENT '学校编码，前4位代表区域',
  `agency_type` tinyint(4) DEFAULT '0' COMMENT '订单状态 ，0 默认有效 1待审核 -1 失效',
  `cdate` datetime DEFAULT NULL,
  `edate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='学生信息变更表';

-- ----------------------------
-- Table structure for std_sorce
-- ----------------------------
DROP TABLE IF EXISTS `std_sorce`;
CREATE TABLE `std_sorce` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `std_id` bigint(20) NOT NULL COMMENT '学生id',
  `exam_id` bigint(20) NOT NULL COMMENT '考试id',
  `chinese` tinyint(4) DEFAULT '0' COMMENT '成绩',
  `math` tinyint(4) DEFAULT '0' COMMENT '成绩',
  `english` tinyint(4) DEFAULT '0' COMMENT '成绩',
  `chemistry` tinyint(4) DEFAULT '0' COMMENT '成绩',
  `physical` tinyint(4) DEFAULT '0' COMMENT '成绩',
  `biological` tinyint(4) DEFAULT '0' COMMENT '成绩',
  `science` tinyint(4) DEFAULT '0' COMMENT '成绩',
  `society` tinyint(4) DEFAULT '0' COMMENT '成绩',
  `political` tinyint(4) DEFAULT '0' COMMENT '成绩',
  `history` tinyint(4) DEFAULT '0' COMMENT '成绩',
  `geography` tinyint(4) DEFAULT '0' COMMENT '成绩',
  `total_sorce` tinyint(4) DEFAULT '0' COMMENT '成绩',
  `check_type` tinyint(4) DEFAULT '0' COMMENT '登记状体',
  `check_time` datetime DEFAULT NULL,
  `cdate` datetime DEFAULT NULL,
  `edate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=37135 DEFAULT CHARSET=utf8 COMMENT='学生成绩表';

-- ----------------------------
-- Table structure for tch_properties
-- ----------------------------
DROP TABLE IF EXISTS `tch_properties`;
CREATE TABLE `tch_properties` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tch_id` bigint(20) NOT NULL COMMENT '主帐号ID',
  `tch_code` varchar(20) DEFAULT '0' COMMENT '教师编码',
  `grade_id` bigint(20) DEFAULT '0' COMMENT '年级',
  `class_id` bigint(20) DEFAULT NULL COMMENT '班级',
  `school_id` bigint(20) DEFAULT '0' COMMENT '订单状态 ，0 默认有效 1待审核 -1 失效',
  `school_code` varchar(20) DEFAULT NULL COMMENT '学校编码，前4位代表区域',
  `agency_type` tinyint(4) DEFAULT '0' COMMENT '订单状态 ，0 默认有效 1待审核 -1 失效',
  `position` varchar(20) DEFAULT NULL COMMENT '职务',
  `status` tinyint(4) DEFAULT '0' COMMENT '订单状态 ，0 默认有效 1待审核 -1 失效',
  `cdate` datetime DEFAULT NULL,
  `edate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='教师信息变更表';
