/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50714
Source Host           : localhost:3306
Source Database       : yiishop

Target Server Type    : MYSQL
Target Server Version : 50714
File Encoding         : 65001

Date: 2018-01-03 01:48:09
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for article
-- ----------------------------
DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '名称',
  `intro` text NOT NULL COMMENT '简介',
  `art_category_id` int(11) NOT NULL COMMENT '文章分类id',
  `sort` int(11) NOT NULL COMMENT '排序',
  `status` int(2) NOT NULL COMMENT '状态',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of article
-- ----------------------------
INSERT INTO `article` VALUES ('1', '123', '123', '1', '123', '1', '1513873138');
INSERT INTO `article` VALUES ('2', 'haha ', '111', '1', '1', '1', '1513873348');
INSERT INTO `article` VALUES ('3', '111', '111', '1', '1', '1', '1514879345');

-- ----------------------------
-- Table structure for article_category
-- ----------------------------
DROP TABLE IF EXISTS `article_category`;
CREATE TABLE `article_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '名称',
  `intro` text NOT NULL COMMENT '简介',
  `sort` int(11) NOT NULL COMMENT '排序',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of article_category
-- ----------------------------
INSERT INTO `article_category` VALUES ('1', '1', '1', '11', '1');
INSERT INTO `article_category` VALUES ('3', '2', '22', '22', '1');
INSERT INTO `article_category` VALUES ('4', '34', '4', '4', '1');

-- ----------------------------
-- Table structure for article_detail
-- ----------------------------
DROP TABLE IF EXISTS `article_detail`;
CREATE TABLE `article_detail` (
  `article_id` int(255) NOT NULL COMMENT '文章id',
  `content` text NOT NULL COMMENT '简介',
  PRIMARY KEY (`article_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of article_detail
-- ----------------------------
INSERT INTO `article_detail` VALUES ('0', '123');
INSERT INTO `article_detail` VALUES ('1', '123');
INSERT INTO `article_detail` VALUES ('2', 'hahahah');
INSERT INTO `article_detail` VALUES ('3', '<p>1111</p>');

-- ----------------------------
-- Table structure for auth_assignment
-- ----------------------------
DROP TABLE IF EXISTS `auth_assignment`;
CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of auth_assignment
-- ----------------------------
INSERT INTO `auth_assignment` VALUES ('商品管理员', '4', '1514712813');
INSERT INTO `auth_assignment` VALUES ('商品管理员', '6', '1514713706');
INSERT INTO `auth_assignment` VALUES ('文章管理员', '4', '1514712813');
INSERT INTO `auth_assignment` VALUES ('文章管理员', '5', '1514715733');
INSERT INTO `auth_assignment` VALUES ('文章管理员', '6', '1514713706');
INSERT INTO `auth_assignment` VALUES ('超级管理员', '6', '1514713706');
INSERT INTO `auth_assignment` VALUES ('超级管理员', '7', '1514716002');
INSERT INTO `auth_assignment` VALUES ('超级管理员', '8', '1514716357');

-- ----------------------------
-- Table structure for auth_item
-- ----------------------------
DROP TABLE IF EXISTS `auth_item`;
CREATE TABLE `auth_item` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` smallint(6) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` blob,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `idx-auth_item-type` (`type`),
  CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of auth_item
-- ----------------------------
INSERT INTO `auth_item` VALUES ('article-category/add', '2', '文章分类添加', null, null, '1514879464', '1514880295');
INSERT INTO `auth_item` VALUES ('article-category/del', '2', '文章分类删除', null, null, '1514879476', '1514880320');
INSERT INTO `auth_item` VALUES ('article-category/edit', '2', '文章分类编辑', null, null, '1514879486', '1514880313');
INSERT INTO `auth_item` VALUES ('article-category/index', '2', '文章分类列表', null, null, '1514879430', '1514880305');
INSERT INTO `auth_item` VALUES ('article/add', '2', '文章添加', null, null, '1514701075', '1514701075');
INSERT INTO `auth_item` VALUES ('article/del', '2', '文章删除', null, null, '1514701108', '1514701108');
INSERT INTO `auth_item` VALUES ('article/edit', '2', '文章修改', null, null, '1514701092', '1514701092');
INSERT INTO `auth_item` VALUES ('article/index', '2', '文章列表', null, null, '1514701122', '1514701122');
INSERT INTO `auth_item` VALUES ('brand/add', '2', '品牌添加', null, null, '1514700658', '1514700658');
INSERT INTO `auth_item` VALUES ('brand/del', '2', '品牌删除', null, null, '1514700703', '1514700703');
INSERT INTO `auth_item` VALUES ('brand/edit', '2', '品牌修改', null, null, '1514700678', '1514700678');
INSERT INTO `auth_item` VALUES ('brand/index', '2', '品牌列表', null, null, '1514700775', '1514700775');
INSERT INTO `auth_item` VALUES ('goods-category/add', '2', '商品分类添加', null, null, '1514701235', '1514701235');
INSERT INTO `auth_item` VALUES ('goods-category/del', '2', '商品分类删除', null, null, '1514701337', '1514701337');
INSERT INTO `auth_item` VALUES ('goods-category/index', '2', '商品分类列表', null, null, '1514701215', '1514701215');
INSERT INTO `auth_item` VALUES ('goods-category/update', '2', '商品分类修改', null, null, '1514701303', '1514701303');
INSERT INTO `auth_item` VALUES ('goods/add', '2', '商品添加', null, null, '1514700358', '1514700516');
INSERT INTO `auth_item` VALUES ('goods/del', '2', '商品删除', null, null, '1514700505', '1514700521');
INSERT INTO `auth_item` VALUES ('goods/edit', '2', '商品修改', null, null, '1514700487', '1514700487');
INSERT INTO `auth_item` VALUES ('goods/gallery', '2', '商品相册', null, null, '1514700640', '1514700640');
INSERT INTO `auth_item` VALUES ('goods/index', '2', '商品列表', null, null, '1514700340', '1514700340');
INSERT INTO `auth_item` VALUES ('goods/show', '2', '商品详情', null, null, '1514700562', '1514700562');
INSERT INTO `auth_item` VALUES ('menu/add', '2', '菜单添加', null, null, '1514879948', '1514879948');
INSERT INTO `auth_item` VALUES ('menu/del', '2', '菜单删除', null, null, '1514879993', '1514879993');
INSERT INTO `auth_item` VALUES ('menu/edit', '2', '菜单修改', null, null, '1514879979', '1514879979');
INSERT INTO `auth_item` VALUES ('menu/index', '2', '菜单列表', null, null, '1514879927', '1514879927');
INSERT INTO `auth_item` VALUES ('rbac/add-permission', '2', '添加权限', null, null, '1514706182', '1514706182');
INSERT INTO `auth_item` VALUES ('rbac/add-role', '2', '添加角色', null, null, '1514706417', '1514706417');
INSERT INTO `auth_item` VALUES ('rbac/del-permission', '2', '删除权限', null, null, '1514706335', '1514706335');
INSERT INTO `auth_item` VALUES ('rbac/del-role', '2', '删除角色', null, null, '1514706456', '1514706456');
INSERT INTO `auth_item` VALUES ('rbac/edit-permission', '2', '修改权限', null, null, '1514706303', '1514706303');
INSERT INTO `auth_item` VALUES ('rbac/edit-role', '2', '编辑角色', null, null, '1514706438', '1514706438');
INSERT INTO `auth_item` VALUES ('rbac/index', '2', '权限列表', null, null, '1514706204', '1514706204');
INSERT INTO `auth_item` VALUES ('rbac/index-role', '2', '角色列表', null, null, '1514706379', '1514706379');
INSERT INTO `auth_item` VALUES ('user/add', '2', '管理员添加', null, null, '1514699439', '1514700987');
INSERT INTO `auth_item` VALUES ('user/del', '2', '管理员删除', null, null, '1514700142', '1514700998');
INSERT INTO `auth_item` VALUES ('user/edit', '2', '管理员修改', null, null, '1514700159', '1514701009');
INSERT INTO `auth_item` VALUES ('user/index', '2', '管理员列表', null, null, '1514700193', '1514701020');
INSERT INTO `auth_item` VALUES ('商品管理员', '1', '拥有商品管理权限', null, null, '1514707966', '1514707966');
INSERT INTO `auth_item` VALUES ('文章管理员', '1', '所有文章权限', null, null, '1514708061', '1514879520');
INSERT INTO `auth_item` VALUES ('超级管理员', '1', '所有权限', null, null, '1514707924', '1514880085');

-- ----------------------------
-- Table structure for auth_item_child
-- ----------------------------
DROP TABLE IF EXISTS `auth_item_child`;
CREATE TABLE `auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`),
  CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of auth_item_child
-- ----------------------------
INSERT INTO `auth_item_child` VALUES ('文章管理员', 'article-category/add');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'article-category/add');
INSERT INTO `auth_item_child` VALUES ('文章管理员', 'article-category/del');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'article-category/del');
INSERT INTO `auth_item_child` VALUES ('文章管理员', 'article-category/edit');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'article-category/edit');
INSERT INTO `auth_item_child` VALUES ('文章管理员', 'article-category/index');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'article-category/index');
INSERT INTO `auth_item_child` VALUES ('文章管理员', 'article/add');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'article/add');
INSERT INTO `auth_item_child` VALUES ('文章管理员', 'article/del');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'article/del');
INSERT INTO `auth_item_child` VALUES ('文章管理员', 'article/edit');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'article/edit');
INSERT INTO `auth_item_child` VALUES ('文章管理员', 'article/index');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'article/index');
INSERT INTO `auth_item_child` VALUES ('商品管理员', 'brand/add');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'brand/add');
INSERT INTO `auth_item_child` VALUES ('商品管理员', 'brand/del');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'brand/del');
INSERT INTO `auth_item_child` VALUES ('商品管理员', 'brand/edit');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'brand/edit');
INSERT INTO `auth_item_child` VALUES ('商品管理员', 'brand/index');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'brand/index');
INSERT INTO `auth_item_child` VALUES ('商品管理员', 'goods-category/add');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'goods-category/add');
INSERT INTO `auth_item_child` VALUES ('商品管理员', 'goods-category/del');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'goods-category/del');
INSERT INTO `auth_item_child` VALUES ('商品管理员', 'goods-category/index');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'goods-category/index');
INSERT INTO `auth_item_child` VALUES ('商品管理员', 'goods-category/update');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'goods-category/update');
INSERT INTO `auth_item_child` VALUES ('商品管理员', 'goods/add');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'goods/add');
INSERT INTO `auth_item_child` VALUES ('商品管理员', 'goods/del');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'goods/del');
INSERT INTO `auth_item_child` VALUES ('商品管理员', 'goods/edit');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'goods/edit');
INSERT INTO `auth_item_child` VALUES ('商品管理员', 'goods/gallery');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'goods/gallery');
INSERT INTO `auth_item_child` VALUES ('商品管理员', 'goods/index');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'goods/index');
INSERT INTO `auth_item_child` VALUES ('商品管理员', 'goods/show');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'goods/show');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'menu/add');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'menu/del');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'menu/edit');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'menu/index');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'rbac/add-permission');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'rbac/add-role');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'rbac/del-permission');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'rbac/del-role');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'rbac/edit-permission');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'rbac/edit-role');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'rbac/index');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'rbac/index-role');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'user/add');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'user/del');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'user/edit');
INSERT INTO `auth_item_child` VALUES ('超级管理员', 'user/index');

-- ----------------------------
-- Table structure for auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `auth_rule`;
CREATE TABLE `auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` blob,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of auth_rule
-- ----------------------------

-- ----------------------------
-- Table structure for brand
-- ----------------------------
DROP TABLE IF EXISTS `brand`;
CREATE TABLE `brand` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '名称',
  `intro` text NOT NULL COMMENT '简介',
  `logo` varchar(255) DEFAULT NULL COMMENT '图片',
  `sort` int(11) NOT NULL COMMENT '排序',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of brand
-- ----------------------------
INSERT INTO `brand` VALUES ('8', '小米', '为发烧而生', 'http://p1aylb874.bkt.clouddn.com//upload/5a4b9300681f2.jpg', '1', '1');

-- ----------------------------
-- Table structure for goods
-- ----------------------------
DROP TABLE IF EXISTS `goods`;
CREATE TABLE `goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL COMMENT '商品名称',
  `sn` varchar(20) NOT NULL COMMENT '货号',
  `logo` varchar(255) NOT NULL COMMENT 'LOGO图片',
  `goods_category_id` int(11) NOT NULL COMMENT '商品分类id',
  `brand_id` int(11) NOT NULL COMMENT '品牌分类',
  `market_price` decimal(10,2) NOT NULL COMMENT '市场价格',
  `shop_price` decimal(10,2) NOT NULL COMMENT '商品价格',
  `stock` int(11) NOT NULL COMMENT '库存',
  `is_on_sale` int(1) NOT NULL DEFAULT '0' COMMENT '是否在售(1在售 0下架)',
  `status` int(1) NOT NULL COMMENT '状态(1正常 0回收站)',
  `sort` int(11) NOT NULL COMMENT '排序',
  `create_time` int(11) NOT NULL COMMENT '添加时间',
  `view_times` int(11) NOT NULL COMMENT '浏览次数',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of goods
-- ----------------------------
INSERT INTO `goods` VALUES ('1', '小米5', '2017123000001', 'http://p1aylb874.bkt.clouddn.com//upload/goods/5a4b93312e1da.jpg', '27', '8', '1999.00', '1999.00', '111', '1', '0', '123', '1514629066', '0');
INSERT INTO `goods` VALUES ('2', '小米6', '2017123000001', 'http://p1aylb874.bkt.clouddn.com//upload/goods/5a47680fbc471.jpg', '27', '8', '123.00', '1123.00', '123', '0', '0', '123', '1514629139', '0');
INSERT INTO `goods` VALUES ('3', '小米 mix2', '2017123000001', 'http://p1aylb874.bkt.clouddn.com//upload/goods/5a476a8477bf6.jpg', '27', '8', '123.00', '312.00', '123', '1', '0', '312', '1514629766', '0');
INSERT INTO `goods` VALUES ('4', '111', '2017123000001', 'http://p1aylb874.bkt.clouddn.com//upload/goods/5a47954d18a8a.jpg', '1', '6', '11.00', '11.00', '1', '1', '2', '1', '1514640730', '0');

-- ----------------------------
-- Table structure for goods_category
-- ----------------------------
DROP TABLE IF EXISTS `goods_category`;
CREATE TABLE `goods_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tree` int(11) DEFAULT NULL COMMENT '树id',
  `lft` int(11) NOT NULL COMMENT '左值',
  `rgt` int(11) NOT NULL COMMENT '右值',
  `depth` int(11) NOT NULL COMMENT '层级',
  `name` varchar(50) NOT NULL COMMENT '名称',
  `parent_id` int(11) NOT NULL COMMENT '上级分类id',
  `intro` text COMMENT '简介',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of goods_category
-- ----------------------------
INSERT INTO `goods_category` VALUES ('1', '1', '1', '36', '0', '手机/运营商/数码', '0', '1');
INSERT INTO `goods_category` VALUES ('2', '2', '1', '12', '0', '家用电器', '0', '1');
INSERT INTO `goods_category` VALUES ('3', '3', '1', '8', '0', '电脑/办公', '0', '1');
INSERT INTO `goods_category` VALUES ('4', '4', '1', '8', '0', '家具/家居/家装/厨具', '0', '1');
INSERT INTO `goods_category` VALUES ('5', '5', '1', '8', '0', '男装/女装/童装/内页', '0', '1');
INSERT INTO `goods_category` VALUES ('6', '6', '1', '6', '0', '美妆个护/宠物', '0', '1');
INSERT INTO `goods_category` VALUES ('7', '2', '2', '3', '1', '电视', '2', '2');
INSERT INTO `goods_category` VALUES ('8', '2', '4', '5', '1', '空调', '2', '2');
INSERT INTO `goods_category` VALUES ('9', '2', '6', '7', '1', '洗衣机', '2', '2');
INSERT INTO `goods_category` VALUES ('10', '2', '8', '9', '1', '冰箱', '2', '2');
INSERT INTO `goods_category` VALUES ('11', '2', '10', '11', '1', '厨卫大电', '2', '2');
INSERT INTO `goods_category` VALUES ('12', '1', '2', '11', '1', '手机通讯', '1', '2');
INSERT INTO `goods_category` VALUES ('13', '1', '12', '19', '1', '运营商', '1', '2');
INSERT INTO `goods_category` VALUES ('14', '1', '20', '27', '1', '手机配件', '1', '2');
INSERT INTO `goods_category` VALUES ('15', '1', '28', '35', '1', '摄影摄像', '1', '2');
INSERT INTO `goods_category` VALUES ('16', '4', '2', '3', '1', '厨具', '4', '2');
INSERT INTO `goods_category` VALUES ('17', '4', '4', '5', '1', '家纺', '4', '2');
INSERT INTO `goods_category` VALUES ('18', '4', '6', '7', '1', '生活日用', '4', '2');
INSERT INTO `goods_category` VALUES ('19', '3', '2', '3', '1', '电脑整机', '3', '2');
INSERT INTO `goods_category` VALUES ('20', '3', '4', '5', '1', '电脑外设', '3', '2');
INSERT INTO `goods_category` VALUES ('21', '3', '6', '7', '1', '电脑配件', '3', '2');
INSERT INTO `goods_category` VALUES ('22', '5', '2', '3', '1', '女装', '5', '2');
INSERT INTO `goods_category` VALUES ('23', '5', '4', '5', '1', '男装', '5', '2');
INSERT INTO `goods_category` VALUES ('24', '5', '6', '7', '1', '睡衣', '5', '2');
INSERT INTO `goods_category` VALUES ('25', '6', '2', '3', '1', '面部护肤', '6', '2');
INSERT INTO `goods_category` VALUES ('26', '6', '4', '5', '1', '洗发护发', '6', '2');
INSERT INTO `goods_category` VALUES ('27', '1', '3', '4', '2', '手机', '12', '3');
INSERT INTO `goods_category` VALUES ('28', '1', '5', '6', '2', '对讲机', '12', '');
INSERT INTO `goods_category` VALUES ('29', '1', '7', '8', '2', '以旧换新', '12', '');
INSERT INTO `goods_category` VALUES ('30', '1', '9', '10', '2', '手机维修', '12', '');
INSERT INTO `goods_category` VALUES ('31', '1', '13', '14', '2', '合约机', '13', '');
INSERT INTO `goods_category` VALUES ('32', '1', '15', '16', '2', '选号码', '13', '');
INSERT INTO `goods_category` VALUES ('33', '1', '17', '18', '2', '固话宽带', '13', '');
INSERT INTO `goods_category` VALUES ('34', '1', '21', '22', '2', '手机壳', '14', '');
INSERT INTO `goods_category` VALUES ('35', '1', '23', '24', '2', '贴膜', '14', '');
INSERT INTO `goods_category` VALUES ('36', '1', '25', '26', '2', '手机存储卡', '14', '');
INSERT INTO `goods_category` VALUES ('37', '1', '29', '30', '2', '数码相机', '15', '');
INSERT INTO `goods_category` VALUES ('38', '1', '31', '32', '2', '拍立得', '15', '');
INSERT INTO `goods_category` VALUES ('39', '1', '33', '34', '2', '单反相机', '15', '');

-- ----------------------------
-- Table structure for goods_day_count
-- ----------------------------
DROP TABLE IF EXISTS `goods_day_count`;
CREATE TABLE `goods_day_count` (
  `day` date NOT NULL COMMENT '日期',
  `count` int(11) NOT NULL COMMENT '商品数'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of goods_day_count
-- ----------------------------

-- ----------------------------
-- Table structure for goods_gallery
-- ----------------------------
DROP TABLE IF EXISTS `goods_gallery`;
CREATE TABLE `goods_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL COMMENT '商品id',
  `path` varchar(255) DEFAULT NULL COMMENT '图片地址',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of goods_gallery
-- ----------------------------
INSERT INTO `goods_gallery` VALUES ('3', '1', 'http://p1aylb874.bkt.clouddn.com//upload/GoodsGallery/5a4b3783e2116.jpg');
INSERT INTO `goods_gallery` VALUES ('4', '1', 'http://p1aylb874.bkt.clouddn.com//upload/GoodsGallery/5a4b378a6a459.jpg');
INSERT INTO `goods_gallery` VALUES ('5', '1', 'http://p1aylb874.bkt.clouddn.com//upload/GoodsGallery/5a4b37d619c8c.jpg');
INSERT INTO `goods_gallery` VALUES ('6', '1', 'http://p1aylb874.bkt.clouddn.com//upload/GoodsGallery/5a4b3854878b1.jpg');
INSERT INTO `goods_gallery` VALUES ('7', '1', 'http://p1aylb874.bkt.clouddn.com//upload/GoodsGallery/5a4b387c66e28.jpg');
INSERT INTO `goods_gallery` VALUES ('8', '1', 'http://p1aylb874.bkt.clouddn.com//upload/GoodsGallery/5a4b38d769da5.jpg');
INSERT INTO `goods_gallery` VALUES ('10', '2', 'http://p1aylb874.bkt.clouddn.com//upload/GoodsGallery/5a4b393000150.jpg');

-- ----------------------------
-- Table structure for goods_intro
-- ----------------------------
DROP TABLE IF EXISTS `goods_intro`;
CREATE TABLE `goods_intro` (
  `goods_id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text COMMENT '商品描述',
  PRIMARY KEY (`goods_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of goods_intro
-- ----------------------------
INSERT INTO `goods_intro` VALUES ('1', '<p>123</p>');
INSERT INTO `goods_intro` VALUES ('2', '<p>123</p>');
INSERT INTO `goods_intro` VALUES ('3', '<p>3123</p>');
INSERT INTO `goods_intro` VALUES ('4', '<p>111</p>');

-- ----------------------------
-- Table structure for member
-- ----------------------------
DROP TABLE IF EXISTS `member`;
CREATE TABLE `member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL COMMENT '用户名',
  `auth_key` varchar(100) NOT NULL,
  `password_hash` varchar(100) NOT NULL COMMENT '密码（密文）',
  `email` varchar(100) NOT NULL COMMENT '邮箱',
  `tel` char(11) NOT NULL COMMENT '电话',
  `last_login_time` int(11) DEFAULT NULL COMMENT '最后登录时间',
  `last_login_ip` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of member
-- ----------------------------
INSERT INTO `member` VALUES ('1', '111111', 'MLSCH867n604EVaaLpey0sizqr98QCKZ', '$2y$13$H3G/giOTCZBn./uT3XmYWuSP.pDYaFQ6F5/azrjTWPTdN7UfM/Hw6', '11@qq.com', '11111111111', null, null, '1', '1514914130', null);

-- ----------------------------
-- Table structure for menu
-- ----------------------------
DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '菜单名称',
  `parent_id` int(11) NOT NULL COMMENT '上级菜单',
  `route` varchar(50) DEFAULT NULL COMMENT '路由地址',
  `sort` int(11) NOT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=60 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of menu
-- ----------------------------
INSERT INTO `menu` VALUES ('8', '1级菜单1', '8', '', '123');
INSERT INTO `menu` VALUES ('17', '商品管理', '0', '', '1');
INSERT INTO `menu` VALUES ('4', '11111', '2', 'goods/update', '1');
INSERT INTO `menu` VALUES ('18', '商品添加', '17', 'goods/add', '1');
INSERT INTO `menu` VALUES ('6', '2级菜单', '5', '', '1');
INSERT INTO `menu` VALUES ('9', '123', '8', 'goods/delete', '123');
INSERT INTO `menu` VALUES ('21', '商品列表', '17', 'goods/index', '1');
INSERT INTO `menu` VALUES ('13', '二级菜单1', '10', '', '1');
INSERT INTO `menu` VALUES ('24', '品牌管理', '0', '', '1');
INSERT INTO `menu` VALUES ('25', '品牌添加', '24', 'brand/add', '1');
INSERT INTO `menu` VALUES ('28', '品牌列表', '24', 'brand/index', '1');
INSERT INTO `menu` VALUES ('29', '商品分类添加', '17', 'goods-category/add', '1');
INSERT INTO `menu` VALUES ('32', '商品分类列表', '17', 'goods-category/index', '1');
INSERT INTO `menu` VALUES ('33', '文章管理', '0', '', '1');
INSERT INTO `menu` VALUES ('34', '文章添加', '33', 'article/add', '1');
INSERT INTO `menu` VALUES ('37', '文章列表', '33', 'article/index', '1');
INSERT INTO `menu` VALUES ('38', '管理员管理', '0', '', '1');
INSERT INTO `menu` VALUES ('39', '管理员添加', '38', 'user/add', '1');
INSERT INTO `menu` VALUES ('42', '管理员列表', '38', 'user/index', '1');
INSERT INTO `menu` VALUES ('43', '权限控制', '0', '', '1');
INSERT INTO `menu` VALUES ('44', '权限添加', '43', 'rbac/add-permission', '1');
INSERT INTO `menu` VALUES ('57', '菜单列表', '55', 'menu/index', '1');
INSERT INTO `menu` VALUES ('48', '权限列表', '43', 'rbac/index', '1');
INSERT INTO `menu` VALUES ('49', '角色添加', '43', 'rbac/add-role', '1');
INSERT INTO `menu` VALUES ('56', '菜单添加', '55', 'menu/add', '1');
INSERT INTO `menu` VALUES ('55', '菜单管理', '0', '', '1');
INSERT INTO `menu` VALUES ('52', '角色列表', '43', 'rbac/index-role', '1');
INSERT INTO `menu` VALUES ('59', '文章类别添加', '33', 'article-category/add', '1');
INSERT INTO `menu` VALUES ('58', '文章类别列表', '33', 'article-category/index', '1');

-- ----------------------------
-- Table structure for migration
-- ----------------------------
DROP TABLE IF EXISTS `migration`;
CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of migration
-- ----------------------------
INSERT INTO `migration` VALUES ('m000000_000000_base', '1513754652');
INSERT INTO `migration` VALUES ('m130524_201442_init', '1513754655');
INSERT INTO `migration` VALUES ('m171220_071627_create_brand_table', '1513754655');
INSERT INTO `migration` VALUES ('m171220_082634_create_article_category_table', '1513758605');
INSERT INTO `migration` VALUES ('m171221_103532_create_goods_category_table', '1514217854');
INSERT INTO `migration` VALUES ('m171222_005850_create_goods_day_count_table', '1514622404');
INSERT INTO `migration` VALUES ('m171222_010026_create_goods_table', '1514622404');
INSERT INTO `migration` VALUES ('m171222_010039_create_goods_intro_table', '1514622404');
INSERT INTO `migration` VALUES ('m171222_010102_create_goods_gallery_table', '1514622404');
INSERT INTO `migration` VALUES ('m140506_102106_rbac_init', '1514698482');

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `last_login_time` int(11) DEFAULT NULL,
  `last_login_ip` char(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `head` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `password_reset_token` (`password_reset_token`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('8', '111', '5a48bcc5189f1', '$2y$13$MIjMEvwX.srkpFxWedY6pegWURP1cZrk6RYcd6H2zFOLN9.9HzY.u', null, '111', '1', '1514716357', null, '1514879545', '127.0.0.1', 'http://p1aylb874.bkt.clouddn.com//upload/goods/5a48bcc022df1.jpg');
