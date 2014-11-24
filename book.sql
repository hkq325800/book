/*
Navicat MySQL Data Transfer

Source Server         : apartment
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : book

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2014-11-24 15:59:41
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `bookactivity`
-- ----------------------------
DROP TABLE IF EXISTS `bookactivity`;
CREATE TABLE `bookactivity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `act_period` varchar(255) DEFAULT NULL COMMENT '活动时间段',
  `act_budget` int(11) DEFAULT NULL COMMENT '活动预算',
  `act_status` int(11) NOT NULL COMMENT '活动状态',
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  `act_cost` float DEFAULT NULL COMMENT '活动花费',
  `act_message` varchar(255) DEFAULT NULL COMMENT '活动公示',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of bookactivity
-- ----------------------------
INSERT INTO `bookactivity` VALUES ('1', '2014.9.1-2014.10.1', null, '0', '0000-00-00', '0000-00-00', null, null);

-- ----------------------------
-- Table structure for `bookbasic`
-- ----------------------------
DROP TABLE IF EXISTS `bookbasic`;
CREATE TABLE `bookbasic` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'book_id',
  `act_id` int(11) NOT NULL DEFAULT '0' COMMENT '活动相关',
  `book_name` varchar(255) NOT NULL COMMENT '书名',
  `book_author` varchar(255) NOT NULL DEFAULT '未知' COMMENT '作者',
  `book_pub` varchar(255) DEFAULT NULL COMMENT '版次',
  `book_type` varchar(255) NOT NULL COMMENT '书类别',
  `book_edit` varchar(255) DEFAULT NULL COMMENT '出版社',
  `book_price` float NOT NULL DEFAULT '0' COMMENT '价格',
  `book_status` varchar(255) NOT NULL DEFAULT '未被借' COMMENT '书的状态',
  `book_info` text COMMENT '图书简介',
  `favour` int(11) NOT NULL DEFAULT '0' COMMENT '点赞',
  `dislike` int(11) NOT NULL DEFAULT '0' COMMENT '踩',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of bookbasic
-- ----------------------------
INSERT INTO `bookbasic` VALUES ('1', '1', 'Android应用UI设计模式', 'Greg Nudelman', null, '移动端', '人民邮电', '0', '已被借', null, '2', '0');
INSERT INTO `bookbasic` VALUES ('2', '1', 'Android和PHP开发最佳实践', '黄隽实', null, '移动端', '机械工业', '0', '已被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('3', '1', '深入浅出PhoneGap', '饶侠', null, '移动端', '人民邮电', '0', '未购买', null, '1', '0');
INSERT INTO `bookbasic` VALUES ('4', '1', '第一行代码Android', '郭霖', null, '移动端', '人民邮电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('5', '1', 'Java程序设计经典300例', '李源', null, 'JAVA', '电子工业', '0', '已超期', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('6', '1', 'Java常用算法手册', '徐明远', null, 'JAVA', '中国铁道', '0', '未被借', null, '1', '0');
INSERT INTO `bookbasic` VALUES ('7', '1', 'Web3.0与Semantic Web编程', 'John Hebeler', null, 'JAVA', '清华大学', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('8', '1', 'Java Web整合开发王者归来', '刘京华', null, 'JAVA', '清华大学', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('9', '1', 'Java编程思想', 'Bruce Eckel', null, 'JAVA', '机械工业', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('10', '1', 'Java网络编程', 'Elliotte Rusty', null, 'JAVA', '中国电力', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('11', '1', 'HTML5WebSocket权威指南', 'Vanessa Wang', null, 'Web小技术', '机械工程', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('12', '1', '图解HTTP', '上野宣', null, 'Web小技术', '人民邮电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('13', '1', '网站性能检测与优化', 'Alistair Croll', null, 'Web小技术', '人民邮电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('14', '1', 'Web技术——HTTP到服务器端', '小泉修', null, 'Web小技术', '科学', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('15', '1', 'RESTfulWeb APIs', 'Leonard Richardson', null, 'Web小技术', '电子工业', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('16', '1', 'Thinking in UML', '谭云杰', null, 'UML', '中国水利水电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('17', '1', 'UML面向对象建模基础', '徐峰', null, 'UML', '中国水利水电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('18', '1', '深入浅出JS编程', 'Eric Freeman', null, 'JS', '东南大学', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('19', '1', 'JQuery技术内幕', '高云', null, 'JS', '机械工业', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('20', '1', '精通Jquery', 'Adam Freeman', null, 'JS', '人民邮电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('21', '1', 'JS权威指南', 'David Flanagan', null, 'JS', '机械工业', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('22', '1', '单页Web应用', 'Michael S.', null, 'JS', '人民邮电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('23', '1', '扩展及应用开发Chrome', '李喆', null, 'JS', '人民邮电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('24', '1', 'JavaScript DOM编程艺术', 'Jeremy Keith', null, 'JS', '人民邮电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('25', '1', 'Ruby基础教程', '高桥征义', null, '小众后端', '人民邮电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('26', '1', '面向对象设计实践指南Ruby语言描述', 'Sandi Metz', null, '小众后端', '人民邮电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('27', '1', 'Go语言程序设计', 'Mark Summerfield', null, '小众后端', '人民邮电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('28', '1', '深入浅出nodjs', '朴灵', null, '小众后端', '人民邮电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('29', '1', 'The Ruby Way', 'Hal Fulton', null, '小众后端', '人民邮电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('30', '1', 'Ruby Cookbook', 'Lucas Carlson', null, '小众后端', '清华大学', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('31', '1', 'Programming Ruby', 'Dave Thomas', null, '小众后端', '电子工业', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('32', '1', '只是为了好玩', 'Linus Torvalds', null, '操作系统', '人民邮电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('33', '1', '理解Unix进程', 'Jesse Storimer', null, '操作系统', '人民邮电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('34', '1', '深入Linux内核架构', 'Wolfgang Mauerer', null, '操作系统', '人民邮电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('35', '1', 'Unix网络编程卷一', 'W.Richard Stevens', null, '操作系统', '人民邮电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('36', '1', 'Unix网络编程卷二', 'W.Richard Stevens', null, '操作系统', '人民邮电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('37', '1', '精通Unix shell脚本编程', 'Randal K.', null, '操作系统', '清华大学', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('38', '1', '操作系统之哲学原理', '邹恒明', null, '操作系统', '机械工业', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('39', '1', 'PHP编程实战', 'Peter Maclntyre', null, 'PHP', '人民邮电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('40', '1', '细说PHP', 'LAMP兄弟连', null, 'PHP', '电子工业', '0', '已被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('41', '1', 'PHP核心技术与最佳实践', '陈文', null, 'PHP', '机械工业', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('42', '1', 'PHP和MySQL Web开发', 'Luke Welling', null, 'PHP', '机械工业', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('43', '1', 'PHP与MySQL程序设计', 'W.Jason Gilmore', null, 'PHP', '人民邮电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('44', '1', '利用Python进行数据分析', 'Wes McKinney', null, '数据挖掘', '机械工业', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('45', '1', 'Hadoop权威指南', 'Tom White', null, '数据挖掘', '清华大学', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('46', '1', '数据挖掘', 'Jiawei Han', null, '数据挖掘', '机械工业', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('47', '1', 'HTML5实战', '陶国荣', null, '前端', '机械工业', '0', '已被借', null, '1', '0');
INSERT INTO `bookbasic` VALUES ('48', '1', 'Web前端黑客技术揭秘', '钟晨鸣', null, '前端', '电子工业', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('49', '1', 'HTML5与CSS3权威指南', '陆凌牛', null, '前端', '机械工业', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('50', '1', 'CSS高效开发实战', '谢郁', null, '前端', '电子工业', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('51', '1', 'C++PrimerPlus', 'Stephen Prata', null, 'C++', '人民邮电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('52', '1', '高效程序员的45个习惯——敏捷开发修炼之道', 'Venkat Subramaniam', null, '思维锻炼', '人民邮电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('53', '1', '统计思维 程序员数学之概率统计', 'Allen B.Downey', null, '思维锻炼', '人民邮电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('54', '1', '研磨设计模式', '陈臣', null, '思维锻炼', '清华大学', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('55', '1', '大设计', '霍金', null, '思维锻炼', '湖南科学技术', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('56', '1', 'Scrum要素', 'Chris Slims', null, '思维锻炼', '人民邮电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('57', '1', 'Scrum实战', 'Mitch Lacey', null, '思维锻炼', '清华大学', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('58', '1', 'Javascript编程精解', 'Marijn Haverbeke', null, 'JS', '机械工业', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('59', '1', '数据库系统概念', 'Abraham Silberschatz', null, '数据库', '机械工业', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('60', '1', '数据库查询优化器', '李海翔', null, '数据库', '机械工业', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('61', '1', 'Linux教程', 'Syed Mansoor Sarwar', null, '操作系统', '清华大学', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('62', '1', '编写可维护的javaScript', 'Nicholas C.Zakas', null, 'JS', '人民邮电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('63', '1', '基于MVC的javaScript Web 富应用开发', 'Alex MacCaw', null, 'JS', '电子工业', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('64', '1', '给大家看的Web设计书', 'Robin Williams', null, 'Web小技术', '人民邮电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('65', '1', 'Sass与Compass实战', 'Wynn Netherland', null, 'Web小技术', '人民邮电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('66', '1', '黑客与画家', 'Paul Graham', null, '其他', '人民邮电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('67', '1', '霍金传', 'Michael White', null, '其他', '湖南科学技术', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('68', '1', '你的灯亮着吗？', '唐纳德·高斯', null, '其他', '清华大学', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('69', '1', '逻辑的力量', 'C.Stephen Layman', null, '其他', '中国人民大学', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('70', '1', '数字文明物理学和计算机', '郝柏林', null, '其他', '科学', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('71', '1', '不插电的计算机科学', 'Tim Bell', null, '其他', '华中科技大学', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('72', '1', '白', '原研哉', null, '其他', '广西师范大学', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('73', '1', '摇摆难以抗拒的非理性诱惑', '奥瑞·布莱福曼', null, '其他', '中信', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('74', '1', '程序开发心理学', 'Gerald M.Weinberg', null, '其他', '清华大学', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('75', '1', '通灵芯片', 'W.Daniel Hillis', null, '其他', '上海世纪', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('76', '1', '和谐社会之数学表达', '赵克', null, '其他', '广东人民', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('77', '1', '互联网：碎片化生存', '段永朝', null, '其他', '中信', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('78', '1', 'SEO实战密码', 'Zac', null, '其他', '电子工业', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('79', '1', '蚁群优化', 'Marco Dorigo', null, '其他', '清华大学', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('80', '1', '复杂网络理论及其应用', '汪小帆', null, '其他', '清华大学', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('81', '1', '链接网络新科学', '艾伯特-拉斯洛·巴拉巴西', null, '其他', '湖南科学技术', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('82', '1', '人机工程与工业设计', '张宇红', null, '其他', '中国水利水电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('83', '1', '链接分析：信息科学的研究方法', 'Mike Thelwall', null, '其他', '东南大学', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('84', '1', '嵌入式系统高能效软件技术及应用', '赵霞', null, '其他', '清华大学', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('85', '1', '如何求解问题', 'Zbigniew Michalewicz', null, '其他', '中国水利水电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('86', '1', '数据库原理与实现', '高屹', null, '数据库', '清华大学', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('87', '1', '可穿戴设备', '陈根', null, '交互', '机械工业', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('88', '1', 'Kinect应用开发实战', '余涛', null, '交互', '机械工业', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('89', '1', '虚拟现实技术', '陈怀友', null, '交互', '清华大学', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('90', '1', '色彩设计的原理', '伊达千代', null, '前端', '中信', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('91', '1', '文字的设计原理', '伊达千代', null, '前端', '中信', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('92', '1', '版面设计的原理', '伊达千代', null, '前端', '中信', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('93', '1', '笔式用户界面', '戴国忠', null, '交互', '中国科学技术', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('94', '1', '信息可视化交互设计', 'Robert Spence', null, '交互', '机械工业', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('95', '1', '关键设计报告', '比尔·莫格里奇', null, '前端', '中信', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('96', '1', '物联网导论', '刘云浩', null, '其他', '科学', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('97', '1', '用AngularJS开发下一代Web应用', 'Brad Green', null, '前端', '电子工业', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('98', '1', 'CSS设计指南', 'Charles Wyke-Smith', null, '前端', '人民邮电', '0', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('99', '1', 'new book', '123', null, '46', '90', '12', '未被借', null, '0', '0');
INSERT INTO `bookbasic` VALUES ('100', '1', '1', '2', null, '3', '5', '3', '6', null, '0', '0');

-- ----------------------------
-- Table structure for `bookcirculate`
-- ----------------------------
DROP TABLE IF EXISTS `bookcirculate`;
CREATE TABLE `bookcirculate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `book_id` int(11) NOT NULL COMMENT '与书关联',
  `user_id` int(11) NOT NULL COMMENT '用户关联',
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of bookcirculate
-- ----------------------------
INSERT INTO `bookcirculate` VALUES ('1', '47', '12108413', '2014-10-13', '2014-11-12');
INSERT INTO `bookcirculate` VALUES ('2', '40', '12108206', '2014-10-22', '0000-00-00');
INSERT INTO `bookcirculate` VALUES ('3', '1', '12108413', '2014-11-05', '0000-00-00');
INSERT INTO `bookcirculate` VALUES ('4', '47', '12108438', '2014-11-25', '0000-00-00');
INSERT INTO `bookcirculate` VALUES ('5', '2', '12108413', '2014-11-10', '0000-00-00');

-- ----------------------------
-- Table structure for `bookdetail`
-- ----------------------------
DROP TABLE IF EXISTS `bookdetail`;
CREATE TABLE `bookdetail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `book_id` int(11) NOT NULL COMMENT '与书关联',
  `buy_time` varchar(255) DEFAULT '0' COMMENT '购书时间',
  `book_pic` text COMMENT 'picurl',
  `book_link` text COMMENT 'linkurl',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of bookdetail
-- ----------------------------
INSERT INTO `bookdetail` VALUES ('1', '1', '0', 'https://avatars0.githubusercontent.com/u/8351862?v=2&s=460', 'http://www.uisdc.com/android-ui-mode');
INSERT INTO `bookdetail` VALUES ('2', '2', '0', null, null);
INSERT INTO `bookdetail` VALUES ('3', '3', '0', null, null);
INSERT INTO `bookdetail` VALUES ('4', '4', '0', null, null);
INSERT INTO `bookdetail` VALUES ('5', '5', '0', null, null);
INSERT INTO `bookdetail` VALUES ('6', '6', '0', null, null);
INSERT INTO `bookdetail` VALUES ('7', '7', '0', null, null);
INSERT INTO `bookdetail` VALUES ('8', '8', '0', null, null);
INSERT INTO `bookdetail` VALUES ('9', '9', '0', null, null);
INSERT INTO `bookdetail` VALUES ('10', '10', '0', null, null);
INSERT INTO `bookdetail` VALUES ('11', '11', '0', null, null);
INSERT INTO `bookdetail` VALUES ('12', '12', '0', null, null);
INSERT INTO `bookdetail` VALUES ('13', '13', '0', null, null);
INSERT INTO `bookdetail` VALUES ('14', '14', '0', null, null);
INSERT INTO `bookdetail` VALUES ('15', '15', '0', null, null);
INSERT INTO `bookdetail` VALUES ('16', '16', '0', null, null);
INSERT INTO `bookdetail` VALUES ('17', '17', '0', null, null);
INSERT INTO `bookdetail` VALUES ('18', '18', '0', null, null);
INSERT INTO `bookdetail` VALUES ('19', '19', '0', null, null);
INSERT INTO `bookdetail` VALUES ('20', '20', '0', null, null);
INSERT INTO `bookdetail` VALUES ('21', '21', '0', null, null);
INSERT INTO `bookdetail` VALUES ('22', '22', '0', null, null);
INSERT INTO `bookdetail` VALUES ('23', '23', '0', null, null);
INSERT INTO `bookdetail` VALUES ('24', '24', '0', null, null);
INSERT INTO `bookdetail` VALUES ('25', '25', '0', null, null);
INSERT INTO `bookdetail` VALUES ('26', '26', '0', null, null);
INSERT INTO `bookdetail` VALUES ('27', '27', '0', null, null);
INSERT INTO `bookdetail` VALUES ('28', '28', '0', null, null);
INSERT INTO `bookdetail` VALUES ('29', '29', '0', null, null);
INSERT INTO `bookdetail` VALUES ('30', '30', '0', null, null);
INSERT INTO `bookdetail` VALUES ('31', '31', '0', null, null);
INSERT INTO `bookdetail` VALUES ('32', '32', '0', null, null);
INSERT INTO `bookdetail` VALUES ('33', '33', '0', null, null);
INSERT INTO `bookdetail` VALUES ('34', '34', '0', null, null);
INSERT INTO `bookdetail` VALUES ('35', '35', '0', null, null);
INSERT INTO `bookdetail` VALUES ('36', '36', '0', null, null);
INSERT INTO `bookdetail` VALUES ('37', '37', '0', null, null);
INSERT INTO `bookdetail` VALUES ('38', '38', '0', null, null);
INSERT INTO `bookdetail` VALUES ('39', '39', '0', null, null);
INSERT INTO `bookdetail` VALUES ('40', '40', '0', null, null);
INSERT INTO `bookdetail` VALUES ('41', '41', '0', null, null);
INSERT INTO `bookdetail` VALUES ('42', '42', '0', null, null);
INSERT INTO `bookdetail` VALUES ('43', '43', '0', null, null);
INSERT INTO `bookdetail` VALUES ('44', '44', '0', null, null);
INSERT INTO `bookdetail` VALUES ('45', '45', '0', null, null);
INSERT INTO `bookdetail` VALUES ('46', '46', '0', null, null);
INSERT INTO `bookdetail` VALUES ('47', '47', '0', null, null);
INSERT INTO `bookdetail` VALUES ('48', '48', '0', null, null);
INSERT INTO `bookdetail` VALUES ('49', '49', '0', null, null);
INSERT INTO `bookdetail` VALUES ('50', '50', '0', null, null);
INSERT INTO `bookdetail` VALUES ('51', '51', '0', null, null);
INSERT INTO `bookdetail` VALUES ('52', '52', '0', null, null);
INSERT INTO `bookdetail` VALUES ('53', '53', '0', null, null);
INSERT INTO `bookdetail` VALUES ('54', '54', '0', null, null);
INSERT INTO `bookdetail` VALUES ('55', '55', '0', null, null);
INSERT INTO `bookdetail` VALUES ('56', '56', '0', null, null);
INSERT INTO `bookdetail` VALUES ('57', '57', '0', null, null);
INSERT INTO `bookdetail` VALUES ('58', '58', '0', null, null);
INSERT INTO `bookdetail` VALUES ('59', '59', '0', null, null);
INSERT INTO `bookdetail` VALUES ('60', '60', '0', null, null);
INSERT INTO `bookdetail` VALUES ('61', '61', '0', null, null);
INSERT INTO `bookdetail` VALUES ('62', '62', '0', null, null);
INSERT INTO `bookdetail` VALUES ('63', '63', '0', null, null);
INSERT INTO `bookdetail` VALUES ('64', '64', '0', null, null);
INSERT INTO `bookdetail` VALUES ('65', '65', '0', null, null);
INSERT INTO `bookdetail` VALUES ('66', '66', '0', null, null);
INSERT INTO `bookdetail` VALUES ('67', '67', '0', null, null);
INSERT INTO `bookdetail` VALUES ('68', '68', '0', null, null);
INSERT INTO `bookdetail` VALUES ('69', '69', '0', null, null);
INSERT INTO `bookdetail` VALUES ('70', '70', '0', null, null);
INSERT INTO `bookdetail` VALUES ('71', '71', '0', null, null);
INSERT INTO `bookdetail` VALUES ('72', '72', '0', null, null);
INSERT INTO `bookdetail` VALUES ('73', '73', '0', null, null);
INSERT INTO `bookdetail` VALUES ('74', '74', '0', null, null);
INSERT INTO `bookdetail` VALUES ('75', '75', '0', null, null);
INSERT INTO `bookdetail` VALUES ('76', '76', '0', null, null);
INSERT INTO `bookdetail` VALUES ('77', '77', '0', null, null);
INSERT INTO `bookdetail` VALUES ('78', '78', '0', null, null);
INSERT INTO `bookdetail` VALUES ('79', '79', '0', null, null);
INSERT INTO `bookdetail` VALUES ('80', '80', '0', null, null);
INSERT INTO `bookdetail` VALUES ('81', '81', '0', null, null);
INSERT INTO `bookdetail` VALUES ('82', '82', '0', null, null);
INSERT INTO `bookdetail` VALUES ('83', '83', '0', null, null);
INSERT INTO `bookdetail` VALUES ('84', '84', '0', null, null);
INSERT INTO `bookdetail` VALUES ('85', '85', '0', null, null);
INSERT INTO `bookdetail` VALUES ('86', '86', '0', null, null);
INSERT INTO `bookdetail` VALUES ('87', '87', '0', null, null);
INSERT INTO `bookdetail` VALUES ('88', '88', '0', null, null);
INSERT INTO `bookdetail` VALUES ('89', '89', '0', null, null);
INSERT INTO `bookdetail` VALUES ('90', '90', '0', null, null);
INSERT INTO `bookdetail` VALUES ('91', '91', '0', null, null);
INSERT INTO `bookdetail` VALUES ('92', '92', '0', null, null);
INSERT INTO `bookdetail` VALUES ('93', '93', '0', null, null);
INSERT INTO `bookdetail` VALUES ('94', '94', '0', null, null);
INSERT INTO `bookdetail` VALUES ('95', '95', '0', null, null);
INSERT INTO `bookdetail` VALUES ('96', '96', '0', null, null);
INSERT INTO `bookdetail` VALUES ('97', '97', '0', null, null);
INSERT INTO `bookdetail` VALUES ('98', '98', '0', null, null);
INSERT INTO `bookdetail` VALUES ('103', '106', '0', null, null);
INSERT INTO `bookdetail` VALUES ('109', '116', '0', '4', null);

-- ----------------------------
-- Table structure for `booklike`
-- ----------------------------
DROP TABLE IF EXISTS `booklike`;
CREATE TABLE `booklike` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `book_id` int(11) NOT NULL COMMENT '与书关联',
  `user_id` int(11) NOT NULL COMMENT '与人关联',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of booklike
-- ----------------------------
INSERT INTO `booklike` VALUES ('1', '1', '12108413');
INSERT INTO `booklike` VALUES ('2', '3', '12108413');
INSERT INTO `booklike` VALUES ('3', '1', '12108238');
INSERT INTO `booklike` VALUES ('4', '6', '12108238');

-- ----------------------------
-- Table structure for `bookmessage`
-- ----------------------------
DROP TABLE IF EXISTS `bookmessage`;
CREATE TABLE `bookmessage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `book_id` int(11) NOT NULL COMMENT '与书关联',
  `user_id` int(11) NOT NULL COMMENT '用户关联',
  `user_message` varchar(255) NOT NULL COMMENT '用户留言',
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of bookmessage
-- ----------------------------
INSERT INTO `bookmessage` VALUES ('1', '1', '1', '我看过这本', '2014-10-27', '0000-00-00');

-- ----------------------------
-- Table structure for `recommend`
-- ----------------------------
DROP TABLE IF EXISTS `recommend`;
CREATE TABLE `recommend` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '与人关联',
  `book_id` int(11) NOT NULL COMMENT '与书关联',
  `rec_reason` varchar(255) NOT NULL COMMENT '推荐理由',
  `updated_at` date NOT NULL,
  `created_at` date NOT NULL,
  `buy_link` text NOT NULL COMMENT '购买链接',
  `rec_type` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of recommend
-- ----------------------------
INSERT INTO `recommend` VALUES ('1', '1', '1', '这书不错', '0000-00-00', '2014-10-26', '', '自选入库');

-- ----------------------------
-- Table structure for `user`
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `user_name` varchar(255) NOT NULL COMMENT '用户姓名',
  `user_password` varchar(255) DEFAULT NULL COMMENT '用户密码',
  `user_rank` varchar(255) NOT NULL DEFAULT '普通用户' COMMENT '用户等级',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

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
