/*
 Navicat MySQL Data Transfer

 Source Server         : localhost
 Source Server Version : 50144
 Source Host           : localhost
 Source Database       : smsdb

 Target Server Version : 50144
 File Encoding         : utf-8

 Date: 11/10/2013 15:34:39 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `as_property`
-- ----------------------------
DROP TABLE IF EXISTS `as_property`;
CREATE TABLE `as_property` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `as_id` bigint(20) NOT NULL COMMENT '���ʺ�ID',
  `as_code` varchar(20) DEFAULT '0' COMMENT '��������',
  `position` varchar(20) DEFAULT NULL COMMENT 'ְ��',
  `agency_type` tinyint(4) DEFAULT '0' COMMENT '����״̬ ��0 Ĭ����Ч 1����� -1 ʧЧ',
  `status` tinyint(4) DEFAULT '0' COMMENT '����״̬ ��0 Ĭ����Ч 1����� -1 ʧЧ',
  `cdate` datetime DEFAULT NULL,
  `edate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='������Ϣ�����';

-- ----------------------------
--  Table structure for `sms_administrative`
-- ----------------------------
DROP TABLE IF EXISTS `sms_administrative`;
CREATE TABLE `sms_administrative` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL COMMENT '���ʺ�ID',
  `as_name` varchar(100) DEFAULT NULL COMMENT '��������',
  `address` varchar(200) DEFAULT NULL COMMENT '�����ַ',
  `contact_phone` varchar(20) DEFAULT NULL COMMENT '��ϵ�绰',
  `contact_mobile` varchar(11) DEFAULT NULL COMMENT '�ֻ�',
  `status` tinyint(4) DEFAULT '0' COMMENT '����״̬ ��0 Ĭ����Ч 1����� -1 ʧЧ',
  `employ_date` datetime DEFAULT NULL COMMENT '��ְʱ�䣬�������㹤��',
  `birthday` datetime DEFAULT NULL,
  `cdate` datetime DEFAULT NULL,
  `edate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='������Ϣ��';

-- ----------------------------
--  Table structure for `sms_class`
-- ----------------------------
DROP TABLE IF EXISTS `sms_class`;
CREATE TABLE `sms_class` (
  `class_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `grade_id` bigint(20) DEFAULT NULL,
  `class_name` varchar(50) DEFAULT NULL,
  `sum_student` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `sms_class_score`
-- ----------------------------
DROP TABLE IF EXISTS `sms_class_score`;
CREATE TABLE `sms_class_score` (
  `class_score_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `class_id` bigint(20) DEFAULT NULL,
  `grade_id` bigint(20) DEFAULT NULL,
  `exam_id` bigint(20) DEFAULT NULL,
  `chinese` varchar(20) DEFAULT NULL,
  `math` varchar(20) DEFAULT NULL,
  `english` varchar(20) DEFAULT NULL COMMENT 'Ӣ��',
  `chemistry` varchar(20) DEFAULT NULL COMMENT '��ѧ',
  `physical` varchar(20) DEFAULT NULL COMMENT '����',
  `biological` varchar(20) DEFAULT NULL COMMENT '����',
  `science` varchar(20) DEFAULT NULL COMMENT '��ѧ',
  `society` varchar(20) DEFAULT NULL COMMENT '���',
  `political` varchar(20) DEFAULT NULL COMMENT '����',
  `history` varchar(20) DEFAULT NULL COMMENT '��ʷ',
  `geography` varchar(20) DEFAULT NULL COMMENT '����',
  `wenzong` varchar(20) DEFAULT NULL,
  `lizong` varchar(20) DEFAULT NULL,
  `cdate` datetime DEFAULT NULL,
  `edate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`class_score_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `sms_grade`
-- ----------------------------
DROP TABLE IF EXISTS `sms_grade`;
CREATE TABLE `sms_grade` (
  `grade_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `school_id` bigint(20) DEFAULT NULL,
  `grade_name` varchar(50) DEFAULT NULL,
  `cdate` datetime DEFAULT NULL,
  `edate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`grade_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `sms_purview`
-- ----------------------------
DROP TABLE IF EXISTS `sms_purview`;
CREATE TABLE `sms_purview` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) DEFAULT '0' COMMENT '0Ĭ�� 1ѧ�� 2��ʦ 3������Ա 100����Ա',
  `role_attribute` bigint(20) DEFAULT '0' COMMENT '��ɫ���ԣ���ǣ���λ�룬1,2,4 ----1024ϵͳ����Ȩ��',
  `as_url` varchar(20) DEFAULT NULL COMMENT '�ܷ���·��',
  `status` tinyint(4) DEFAULT '0' COMMENT '����״̬ ��0 Ĭ����Ч 1����� -1 ʧЧ',
  `cdate` datetime DEFAULT NULL,
  `edate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Ȩ�޿��Ʊ�';

-- ----------------------------
--  Table structure for `sms_school`
-- ----------------------------
DROP TABLE IF EXISTS `sms_school`;
CREATE TABLE `sms_school` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `school_code` int(11) DEFAULT '0' COMMENT '0Ĭ�� 1ѧ�� 2��ʦ 3������Ա 100����Ա',
  `school_name` varchar(80) DEFAULT NULL COMMENT 'ְ��',
  `sum_employee` int(11) DEFAULT '0' COMMENT '��ʦ����',
  `address` varchar(200) DEFAULT NULL COMMENT '�����ַ',
  `status` tinyint(4) DEFAULT '0' COMMENT '����״̬ ��0 Ĭ����Ч 1����� -1 ʧЧ',
  `cdate` datetime DEFAULT NULL,
  `edate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='ѧУ��Ϣ��';

-- ----------------------------
--  Table structure for `sms_student`
-- ----------------------------
DROP TABLE IF EXISTS `sms_student`;
CREATE TABLE `sms_student` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL COMMENT '���ʺ�ID',
  `std_name` varchar(100) DEFAULT NULL COMMENT 'ѧ������',
  `address` varchar(200) DEFAULT NULL COMMENT '�����ַ',
  `contact_phone` varchar(20) DEFAULT NULL COMMENT '��ϵ�绰',
  `contact_mobile` varchar(11) DEFAULT NULL COMMENT '�ֻ�',
  `status` tinyint(4) DEFAULT '0' COMMENT '����״̬ ��0 Ĭ����Ч 1����� -1 ʧЧ',
  `enrollment_date` datetime DEFAULT NULL COMMENT '��ѧʱ��',
  `birthday` datetime DEFAULT NULL COMMENT '��������ѧ������',
  `cdate` datetime DEFAULT NULL,
  `edate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='ѧ����Ϣ��';

-- ----------------------------
--  Table structure for `sms_teacher`
-- ----------------------------
DROP TABLE IF EXISTS `sms_teacher`;
CREATE TABLE `sms_teacher` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL COMMENT '���ʺ�ID',
  `tch_name` varchar(100) DEFAULT NULL COMMENT '��ʦ����',
  `address` varchar(200) DEFAULT NULL COMMENT '�����ַ',
  `contact_phone` varchar(20) DEFAULT NULL COMMENT '��ϵ�绰',
  `contact_mobile` varchar(11) DEFAULT NULL COMMENT '�ֻ�',
  `status` tinyint(4) DEFAULT '0' COMMENT '����״̬ ��0 Ĭ����Ч 1����� -1 ʧЧ',
  `employ_date` datetime DEFAULT NULL COMMENT '��ְʱ�䣬���������ʦ�Ľ���',
  `birthday` datetime DEFAULT NULL,
  `cdate` datetime DEFAULT NULL,
  `edate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='��ʦ��Ϣ��';

-- ----------------------------
--  Table structure for `sms_user`
-- ----------------------------
DROP TABLE IF EXISTS `sms_user`;
CREATE TABLE `sms_user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `account` varchar(50) DEFAULT NULL COMMENT '�û���¼�ʺţ�Ĭ�ϸ���xx_code',
  `password` varchar(64) DEFAULT NULL COMMENT 'Ĭ�����룺ѧ�ź�6��md5��',
  `user_name` varchar(50) DEFAULT NULL COMMENT '���ƣ��û���ʵ����',
  `role_id` int(11) DEFAULT '0' COMMENT '1ѧ�� 2��ʦ 3������Ա 100����Ա',
  `attribute` tinyint(3) DEFAULT '0' COMMENT '0δ��½��1�ѵ�¼',
  `ip` varchar(24) DEFAULT NULL COMMENT 'ip',
  `last_ip` varchar(24) DEFAULT NULL COMMENT 'ip',
  `status` tinyint(3) DEFAULT '0' COMMENT '�û�״̬��0Ĭ����Ч��-1ɾ����1������ 9�����',
  `active_type` tinyint(3) DEFAULT '0' COMMENT '����״̬:0δ���1�Ѽ���',
  `active_time` datetime DEFAULT NULL COMMENT '�ʺż���ʱ��',
  `last_time` datetime DEFAULT NULL COMMENT '����¼ʱ��',
  `cdate` datetime DEFAULT NULL,
  `edate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8 COMMENT='�û��ʺű�';

-- ----------------------------
--  Table structure for `std_examination`
-- ----------------------------
DROP TABLE IF EXISTS `std_examination`;
CREATE TABLE `std_examination` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `school_id` bigint(20) DEFAULT '0' COMMENT '����ѧУid',
  `grade_id` bigint(20) DEFAULT '0' COMMENT '�꼶',
  `examination_type` varchar(80) DEFAULT NULL COMMENT '��������',
  `Examination_name` varchar(200) DEFAULT NULL COMMENT '��������',
  `initiate_agency` varchar(80) DEFAULT NULL COMMENT '������',
  `examination_date` datetime DEFAULT NULL,
  `cdate` datetime DEFAULT NULL,
  `edate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=37136 DEFAULT CHARSET=utf8 COMMENT='ѧ��������Ϣ��';

-- ----------------------------
--  Table structure for `std_extra_exam`
-- ----------------------------
DROP TABLE IF EXISTS `std_extra_exam`;
CREATE TABLE `std_extra_exam` (
  `extra_id` int(11) NOT NULL AUTO_INCREMENT,
  `stu_id` int(11) DEFAULT NULL,
  `exam_id` int(11) DEFAULT NULL,
  `extra_score` tinyint(6) DEFAULT NULL,
  `exam_date` datetime DEFAULT NULL,
  `sum_employee` tinyint(4) DEFAULT NULL,
  `cdate` datetime DEFAULT NULL,
  `edate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`extra_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `std_property`
-- ----------------------------
DROP TABLE IF EXISTS `std_property`;
CREATE TABLE `std_property` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `std_id` bigint(20) NOT NULL COMMENT '���ʺ�ID',
  `std_code` int(11) DEFAULT '0' COMMENT 'ѧ��',
  `grade_id` bigint(20) DEFAULT '0' COMMENT '�꼶',
  `class_id` bigint(20) DEFAULT NULL COMMENT '�༶',
  `school_id` bigint(20) DEFAULT '0' COMMENT '����״̬ ��0 Ĭ����Ч 1����� -1 ʧЧ',
  `school_code` varchar(20) DEFAULT NULL COMMENT 'ѧУ���룬ǰ4λ��������',
  `agency_type` tinyint(4) DEFAULT '0' COMMENT '����״̬ ��0 Ĭ����Ч 1����� -1 ʧЧ',
  `cdate` datetime DEFAULT NULL,
  `edate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='ѧ����Ϣ�����';

-- ----------------------------
--  Table structure for `std_sorce`
-- ----------------------------
DROP TABLE IF EXISTS `std_sorce`;
CREATE TABLE `std_sorce` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `std_id` bigint(20) NOT NULL COMMENT 'ѧ��id',
  `exam_id` bigint(20) NOT NULL COMMENT '����id',
  `chinese` varchar(20) DEFAULT '0' COMMENT '�ɼ�',
  `math` varchar(20) DEFAULT '0' COMMENT '�ɼ�',
  `english` varchar(20) DEFAULT '0' COMMENT '�ɼ�',
  `chemistry` varchar(20) DEFAULT '0' COMMENT '��ѧ',
  `physical` varchar(20) DEFAULT '0' COMMENT '����',
  `biological` varchar(20) DEFAULT '0' COMMENT '����',
  `science` varchar(20) DEFAULT '0' COMMENT '��ѧ',
  `society` varchar(20) DEFAULT '0' COMMENT '���',
  `political` varchar(20) DEFAULT '0' COMMENT '����',
  `history` varchar(20) DEFAULT '0' COMMENT '��ʷ',
  `geography` varchar(20) DEFAULT '0' COMMENT '����',
  `wenzong` varchar(20) DEFAULT NULL,
  `lizong` varchar(20) DEFAULT NULL,
  `total_sorce` varchar(20) DEFAULT '0' COMMENT '�ɼ�',
  `check_status` tinyint(4) DEFAULT '0' COMMENT '�Ǽ�״��',
  `check_time` datetime DEFAULT NULL,
  `cdate` datetime DEFAULT NULL,
  `edate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=37138 DEFAULT CHARSET=utf8 COMMENT='ѧ���ɼ���';

-- ----------------------------
--  Table structure for `tch_property`
-- ----------------------------
DROP TABLE IF EXISTS `tch_property`;
CREATE TABLE `tch_property` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tch_id` bigint(20) NOT NULL COMMENT '���ʺ�ID',
  `tch_code` varchar(20) DEFAULT '0' COMMENT '��ʦ����',
  `grade_id` bigint(20) DEFAULT '0' COMMENT '�꼶',
  `class_id` bigint(20) DEFAULT NULL COMMENT '�༶',
  `school_id` bigint(20) DEFAULT '0' COMMENT '����״̬ ��0 Ĭ����Ч 1����� -1 ʧЧ',
  `school_code` varchar(20) DEFAULT NULL COMMENT 'ѧУ���룬ǰ4λ��������',
  `agency_type` tinyint(4) DEFAULT '0' COMMENT '����״̬ ��0 Ĭ����Ч 1����� -1 ʧЧ',
  `position` varchar(20) DEFAULT NULL COMMENT 'ְ��',
  `status` tinyint(4) DEFAULT '0' COMMENT '����״̬ ��0 Ĭ����Ч 1����� -1 ʧЧ',
  `cdate` datetime DEFAULT NULL,
  `edate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='��ʦ��Ϣ�����';

-- ----------------------------
--  View structure for `students_score`
-- ----------------------------
DROP VIEW IF EXISTS `students_score`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `students_score` AS select `std_sorce`.`std_id` AS `std_id`,`sms_student`.`std_name` AS `std_name`,`std_sorce`.`exam_id` AS `exam_id`,`std_examination`.`Examination_name` AS `Examination_name`,`std_sorce`.`chinese` AS `chinese`,`std_sorce`.`math` AS `math`,`std_sorce`.`english` AS `english`,`std_sorce`.`chemistry` AS `chemistry`,`std_sorce`.`biological` AS `biological`,`std_sorce`.`science` AS `science`,`std_sorce`.`physical` AS `physical`,`std_sorce`.`society` AS `society`,`std_sorce`.`political` AS `political`,`std_sorce`.`history` AS `history`,`std_sorce`.`geography` AS `geography`,`std_sorce`.`total_sorce` AS `total_sorce`,`std_examination`.`cdate` AS `cdate` from ((`std_sorce` join `std_examination`) join `sms_student`);

SET FOREIGN_KEY_CHECKS = 1;
