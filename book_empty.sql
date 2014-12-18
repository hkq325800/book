/*
Navicat MySQL Data Transfer

Source Server         : apartment
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : book

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2014-12-18 16:06:26
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `bookactivity`
-- ----------------------------
DROP TABLE IF EXISTS `bookactivity`;
CREATE TABLE `bookactivity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `act_period` varchar(255) NOT NULL COMMENT '活动时间段',
  `act_budget` int(11) NOT NULL COMMENT '活动预算',
  `act_status` varchar(255) NOT NULL COMMENT '活动状态',
  `created_at` date NOT NULL COMMENT '创建日期',
  `updated_at` date NOT NULL COMMENT '更新时间',
  `act_cost` float DEFAULT NULL COMMENT '活动花费',
  `act_message` varchar(255) DEFAULT NULL COMMENT '活动公示',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of bookactivity
-- ----------------------------

-- ----------------------------
-- Table structure for `bookbasic`
-- ----------------------------
DROP TABLE IF EXISTS `bookbasic`;
CREATE TABLE `bookbasic` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '书类编号',
  `book_isbn` varchar(255) NOT NULL COMMENT '唯一isbn',
  `book_name` varchar(255) NOT NULL COMMENT '书本名称',
  `book_author` varchar(255) NOT NULL DEFAULT '未知' COMMENT '书本作者',
  `book_pub` varchar(255) DEFAULT NULL COMMENT '出版版次',
  `book_type` varchar(255) NOT NULL COMMENT '书本类型',
  `book_edit` varchar(255) DEFAULT NULL COMMENT '出版社团',
  `book_price` float(11,1) NOT NULL DEFAULT '0.0' COMMENT '书本价格',
  `book_pic` text NOT NULL COMMENT '书图片url',
  `book_link` text COMMENT '书相关url',
  `book_info` text COMMENT '书本简介',
  `favour` int(11) NOT NULL DEFAULT '0' COMMENT '点赞数目',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of bookbasic
-- ----------------------------

-- ----------------------------
-- Table structure for `bookcirculate`
-- ----------------------------
DROP TABLE IF EXISTS `bookcirculate`;
CREATE TABLE `bookcirculate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `book_id` int(11) NOT NULL COMMENT '与书关联',
  `user_id` int(11) NOT NULL COMMENT '用户关联',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of bookcirculate
-- ----------------------------

-- ----------------------------
-- Table structure for `booklike`
-- ----------------------------
DROP TABLE IF EXISTS `booklike`;
CREATE TABLE `booklike` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `book_kind` int(11) NOT NULL COMMENT '与类关联',
  `user_id` int(11) NOT NULL COMMENT '与人关联',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of booklike
-- ----------------------------

-- ----------------------------
-- Table structure for `booklist`
-- ----------------------------
DROP TABLE IF EXISTS `booklist`;
CREATE TABLE `booklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '清单编号',
  `book_kind` int(11) NOT NULL COMMENT '与类关联',
  `book_time` date NOT NULL DEFAULT '0000-00-00' COMMENT '录入时间',
  `book_status` varchar(255) NOT NULL DEFAULT '未被借' COMMENT '书本状态',
  `act_id` int(11) NOT NULL DEFAULT '0' COMMENT '活动相关',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of booklist
-- ----------------------------

-- ----------------------------
-- Table structure for `bookmessage`
-- ----------------------------
DROP TABLE IF EXISTS `bookmessage`;
CREATE TABLE `bookmessage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `book_kind` int(11) NOT NULL COMMENT '与类关联',
  `user_id` int(11) NOT NULL COMMENT '与人关联',
  `user_message` varchar(255) NOT NULL COMMENT '留言信息',
  `created_at` date NOT NULL COMMENT '创建日期',
  `updated_at` date NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of bookmessage
-- ----------------------------

-- ----------------------------
-- Table structure for `recommend`
-- ----------------------------
DROP TABLE IF EXISTS `recommend`;
CREATE TABLE `recommend` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '推荐编号',
  `user_id` int(11) NOT NULL COMMENT '与人关联',
  `book_kind` int(11) NOT NULL COMMENT '与类关联',
  `rec_reason` varchar(255) NOT NULL COMMENT '推荐理由',
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  `buy_link` text NOT NULL COMMENT '购买链接',
  `rec_type` varchar(255) NOT NULL COMMENT '推荐类别',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of recommend
-- ----------------------------

-- ----------------------------
-- Table structure for `user`
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户编号',
  `user_id` int(11) NOT NULL COMMENT '用户学号',
  `user_name` varchar(255) NOT NULL COMMENT '用户姓名',
  `user_password` varchar(255) NOT NULL COMMENT '用户密码',
  `user_rank` varchar(255) NOT NULL DEFAULT '普通用户' COMMENT '用户等级',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', '11101018', '李智', '1a48d1844d6ca6b456dd931d227f9843', '普通用户');
INSERT INTO `user` VALUES ('2', '12108129', '吴伟', '25946b36d3d2b1473b13492f4f3761b7', '普通用户');
INSERT INTO `user` VALUES ('3', '12108135', '张坚革', 'c52ab205539e00d8ac9e5117235fb6d3', '普通用户');
INSERT INTO `user` VALUES ('4', '12108139', '郑齐阳', '3bb9f156e763d3313a1527b1046cd99e', '普通用户');
INSERT INTO `user` VALUES ('5', '12108201', '白林林', '06267320e3540d239e2b14140b6cc67b', '普通用户');
INSERT INTO `user` VALUES ('6', '12108203', '彭雅丽', 'ca91f19d6d4f72aba7e085faf9a4caca', '普通用户');
INSERT INTO `user` VALUES ('7', '12108206', '边涛', 'c9b866aad15452c1b9901c2911ff5afb', '普通用户');
INSERT INTO `user` VALUES ('8', '12108208', '陈浩', 'e5d723350c7164a530769048b7547c96', '普通用户');
INSERT INTO `user` VALUES ('9', '12108209', '陈露耿', '41ca4c23cd320607c3fdfba9cc9ffdb5', '普通用户');
INSERT INTO `user` VALUES ('10', '12108210', '陈轲', 'eaa449211dd0cf977a026ebcdf2f3fe9', '普通用户');
INSERT INTO `user` VALUES ('11', '12108211', '段鹏飞', '2880067adbe0aca9e828e647b1569ddc', '普通用户');
INSERT INTO `user` VALUES ('12', '12108216', '李金祥', 'd6962e4c46547fc647cb2d9189e8b74c', '普通用户');
INSERT INTO `user` VALUES ('13', '12108222', '沈之川', 'ac677272ad2b6480bc834aa15a681e3c', '普通用户');
INSERT INTO `user` VALUES ('14', '12108227', '熊君睿', '0e44b4c6c34dc8ce18c01770219ca1e1', '普通用户');
INSERT INTO `user` VALUES ('15', '12108230', '章晨昱', '84b1df4354bdce1324009d97073e3856', '普通用户');
INSERT INTO `user` VALUES ('16', '12108234', '张旭', 'fec6b9b77656406c09ef0c06017f252a', '普通用户');
INSERT INTO `user` VALUES ('17', '12108238', '钟云昶', '6da788b5325d4e487f38930e2cd90c08', '图书管理');
INSERT INTO `user` VALUES ('18', '12108305', '王雪莹', '12bb5766cc55ef0e09aa196e20959729', '普通用户');
INSERT INTO `user` VALUES ('19', '12108306', '曹平涛', 'c304fb8318c7a552a4e241e1b3c5c2dc', '普通用户');
INSERT INTO `user` VALUES ('20', '12108309', '程彦', '2f7a10e0faa742d8f5f4665d6e4f3e05', '普通用户');
INSERT INTO `user` VALUES ('21', '12108315', '刘宏志', '416418370ccae48a1a8861a78860902f', '普通用户');
INSERT INTO `user` VALUES ('22', '12108316', '刘铁', '770653b67a74cf253287e0536970c779', '普通用户');
INSERT INTO `user` VALUES ('23', '12108318', '罗鹏展', '2733b73426c8d43db20f607f886f51f7', '普通用户');
INSERT INTO `user` VALUES ('24', '12108327', '谢俊杰', 'b6c63bc373200d84c0020d7f5b7c6ef2', '购书管理');
INSERT INTO `user` VALUES ('25', '12108331', '许鹏飞', '5370b00a2b3f1a75512ee6facf57127d', '普通用户');
INSERT INTO `user` VALUES ('26', '12108413', '黄可庆', 'ca8e4dea8b6c7e5dffb81548989ea0b2', '普通用户');
