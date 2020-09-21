/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50726
Source Host           : localhost:3306
Source Database       : stu-system

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2020-09-21 10:02:02
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for stu_t1
-- ----------------------------
DROP TABLE IF EXISTS `stu_t1`;
CREATE TABLE `stu_t1` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of stu_t1
-- ----------------------------
INSERT INTO `stu_t1` VALUES ('1', '2');
INSERT INTO `stu_t1` VALUES ('2', 'haha');
INSERT INTO `stu_t1` VALUES ('3', 'hehe');
INSERT INTO `stu_t1` VALUES ('4', 'haha');
INSERT INTO `stu_t1` VALUES ('5', 'hehe');
INSERT INTO `stu_t1` VALUES ('6', 'haha');
INSERT INTO `stu_t1` VALUES ('7', 'hehe');
