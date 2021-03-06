/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50726
Source Host           : localhost:3306
Source Database       : fysw

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2021-01-20 18:54:28
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for fysw_devices
-- ----------------------------
DROP TABLE IF EXISTS `fysw_devices`;
CREATE TABLE `fysw_devices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fd` varchar(255) DEFAULT NULL COMMENT '句柄资源',
  `device_no` varchar(255) DEFAULT NULL COMMENT '设备号',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '-1无效',
  `is_set` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1：已设置设备号',
  `is_alived` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1存活，-1离线（5分钟未发送心跳视为死亡）',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='设备的fd、设备号、以及设备存活状态';

-- ----------------------------
-- Records of fysw_devices
-- ----------------------------
