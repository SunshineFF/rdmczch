/*
SQLyog Ultimate v12.08 (64 bit)
MySQL - 5.7.14 : Database - rdmczch
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`rdmczch` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `rdmczch`;

/*Table structure for table `ty_user_address` */

DROP TABLE IF EXISTS `ty_user_address`;

CREATE TABLE `ty_user_address` (
  `address_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `consignee` varchar(60) NOT NULL DEFAULT '' COMMENT '收货人',
  `email` varchar(60) NOT NULL DEFAULT '' COMMENT '邮箱地址',
  `country` int(11) NOT NULL DEFAULT '0' COMMENT '国家',
  `province` int(11) NOT NULL DEFAULT '0' COMMENT '省份',
  `city` int(11) NOT NULL DEFAULT '0' COMMENT '城市',
  `district` int(11) NOT NULL DEFAULT '0' COMMENT '地区',
  `twon` int(11) DEFAULT '0' COMMENT '乡镇',
  `address` varchar(250) NOT NULL DEFAULT '' COMMENT '地址',
  `zipcode` varchar(60) NOT NULL DEFAULT '' COMMENT '邮政编码',
  `mobile` varchar(60) NOT NULL DEFAULT '' COMMENT '手机',
  `is_default` tinyint(1) DEFAULT '0' COMMENT '默认收货地址',
  `is_pickup` int(11) DEFAULT '0',
  PRIMARY KEY (`address_id`),
  KEY `user_id` (`user_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=137 DEFAULT CHARSET=utf8;

/*Data for the table `ty_user_address` */

insert  into `ty_user_address`(`address_id`,`user_id`,`consignee`,`email`,`country`,`province`,`city`,`district`,`twon`,`address`,`zipcode`,`mobile`,`is_default`,`is_pickup`) values (35,1,'张谷泉','',0,5827,6088,6099,6104,'南山区西丽镇留仙大道1001号','518000','15872123653',1,0),(22,35,'张谷泉','',0,5827,6384,6386,6390,'南山区西丽镇留仙大道1001号','518000','15872123653',1,0),(8,20,'某大街某号 2','',0,19,241,2370,0,' 某街道办 .','','13554754891',0,0),(9,20,'某大街某号 2','',0,1,36,397,0,' ujiuo;iiergf','','13554745866',0,0),(10,21,'哈哈','',0,2,37,401,0,'记录贴','518116','13800138000',0,0),(11,23,' 测试','',0,1,2,3,0,'测试','','13012345678',0,0),(12,24,'吴亚朋','',0,28240,28558,28571,28574,'南山区西丽镇留仙大道1001号','518000','13410170107',0,0),(13,24,'吴亚朋','wyp001@163.com',0,28240,28558,28581,0,'南山区西丽镇留仙大道1001号','','13410170107',1,0),(14,25,'吴亚朋','',1,28240,28558,28581,28584,'南山区西丽镇留仙大道1001号','518000','13410170107',0,0),(15,25,'吴亚朋','',1,28240,28558,28560,28564,'南山区西丽镇留仙大道1001号','518000','13410170107',0,0),(16,26,'吴亚朋','',1,28240,28558,28581,28582,'南山区西丽镇留仙大道1001号','518000','13410170107',1,0),(17,26,'吴亚朋','',1,28240,28241,28266,28272,'南山区西丽镇留仙大道1001号','518055','13410170107',0,0),(18,26,'吴亚朋','',1,28240,28241,28266,28272,'南山区西丽镇留仙大道1001号','518055','13410170107',0,0),(20,34,'靠你妹','',1,636,936,937,0,'北京朝阳区来广营','334345','13800138068',1,0),(36,58,'吕束俊','',1,636,1188,1208,0,'吕束俊','224312','18251122249',1,0),(38,61,'test','',1,1,2,3,0,'test','520000','18776885525',1,0),(39,72,'史强','',1,45753,45754,45813,0,'果园路','','15209583253',1,0),(40,69,'11','',1,636,1188,1208,0,'ll','222','18251122249',0,0),(41,78,'呵呵','',1,41877,41878,41880,0,'妮子洗衣','','18993254658',1,0),(42,81,'bu ','',1,8558,9222,9230,0,'哦啦啦咯我','848000','13209037788',1,0),(43,69,'111','',1,338,569,586,0,'吕阿森','1111','18251122249',1,0),(44,79,'小明','',1,28240,28241,28308,0,'天河客运站附近','','13544041573',1,0),(45,85,'好几块','',1,1,2,3,0,'发货快','','15306428096',1,0),(46,89,'体力','',1,17359,17894,17896,0,'国际经济寂寞','','18319366666',1,0),(47,94,'是啊','',1,636,1188,1208,0,'时候','111111','18500269999',1,0),(48,101,'邱','',1,12596,13890,13892,0,'测试','','15866666666',1,0),(49,159,'您','',1,636,1188,1208,0,'牛会吐','82626','13456782345',1,0),(50,168,'吕布','',1,338,569,570,0,'去了','245212','18251122249',1,0),(51,207,'测试人','',1,10543,10544,10560,0,'石龙路','','15655454544',1,0),(52,244,'刘','',1,33007,33008,33058,0,'天府软件园E区','','18108089922',1,0),(53,246,'我','',1,1,300,301,0,'我','610000','18030653344',1,0),(54,264,'那么','',1,338,569,586,0,'那么','123444','13304050607',1,0),(55,263,'李建杰','',1,338,339,347,0,'啊考虑考虑','','18535644725',1,0),(56,266,'测试','',1,46047,46957,46958,0,'测试测试','','18935952111',1,0),(57,215,'尹子','',1,5827,5828,5830,0,'南头古城','','13659863652',1,0),(58,294,'是的','',1,4670,4874,4896,0,'信息','','18888888888',1,0),(59,293,'张云鹏','',1,7531,7706,7792,0,'啦啦啦','132','15257101990',1,0),(60,293,'1','',1,636,936,965,0,'1','1','15257101990',0,0),(61,315,'哦哦','',1,636,1291,1329,0,'咯哦哦哦YY','','15288888888',1,0),(62,301,'好','',1,338,569,608,0,'123','','13576033389',1,0),(63,318,'OK','',1,1,300,301,0,'OK','123','13366666666',1,0),(64,390,'该喝喝','',1,1,2,14,0,'太古uvgjkj','7852542','15222222222',1,0),(65,412,'小','',1,1,2,3,0,'开发人挺好合伙人','277588','13365552356',1,0),(66,434,'李辉','',1,28240,28241,28359,0,'光明北路585','','13924200563',1,0),(67,472,'测试','',1,338,339,340,0,'测试','','13800138000',1,0),(68,103,'周晓涛','',1,28240,28737,28744,0,'富伟大厦903','','13022311523',0,0),(69,103,'周晓涛','',1,28240,28737,28744,0,'富伟大厦903','52800','13022311523',0,0),(70,103,'周晓涛','',1,28240,28737,28744,0,'富伟大厦903','52800','13022311523',0,0),(71,103,'周晓涛','',1,28240,28737,28744,0,'富伟大厦903','52800','13022311523',1,0),(72,507,'天堂','',1,636,1291,1319,0,'甘','56886','13336558888',1,0),(73,409,'测试','',1,3102,3379,3388,0,'13078866969','65655','13866655566',1,0),(74,87,'吴生','',1,1,2,3,0,'大家都觉得','','13266845879',1,0),(75,513,'爱喝茶','',1,4670,4874,4896,0,'来了来了','136449','13466661113',0,0),(76,513,'爱喝茶','',1,4670,4874,4896,0,'来了来了','136449','13466661113',1,0),(77,513,'爱喝茶','',1,4670,4874,4896,0,'来了来了','136449','13466661113',0,0),(78,513,'爱喝茶','',1,4670,4874,4896,0,'来了来了','136449','13466661113',0,0),(79,513,'爱喝茶','',1,4670,4874,4896,0,'来了来了','136449','13466661113',0,0),(80,522,'哦哦哦','',1,1,2,22,0,'哦民工','','13366664444',1,0),(81,550,'66','',1,636,1554,1572,0,'666','','13655555555',1,0),(82,552,'段','',1,1,2,3,0,'北京','745400','18691819005',1,0),(83,575,'语音','',1,4670,4759,4775,0,'杭州','','15523456789',1,0),(84,536,'来了','',1,8558,8884,8894,0,'，哈哈哈','250000','15555555555',1,0),(87,611,'哈哈哈','',1,1,300,322,0,'朝阳','','13103815097',1,0),(86,198,'12','',1,1,2,3,0,'咖啡','255555','17096522222',0,0),(88,198,'李继武','',1,3102,3431,3454,0,'你给我打电话的东西啊','','17865122794',1,0),(89,635,'锈银','',1,19280,19281,19283,0,'历下','','15562517965',0,0),(90,635,'锈银','',1,19280,19281,19283,0,'历下','','15562517965',0,0),(91,635,'锈银','',1,19280,19281,19283,0,'历下','','15562517965',1,0),(92,613,'qq','',1,4670,4849,4851,0,'ww','','18211445623',0,0),(93,613,'qq','',1,4670,4849,4851,0,'ww','','18211445623',1,0),(94,605,'陈家','',1,1,2,14,0,'你的好好啊？','','15815059939',1,0),(95,678,'陈微','',1,21387,21388,21435,0,'白庙小区','','15583012020',1,0),(96,750,'qqq','',1,1,2,3,0,'黄河鬼棺','','15241232145',1,0),(97,767,'1111','',1,338,569,586,0,'111','111','18251122249',1,0),(98,782,'deba','',1,636,1291,1319,0,'了解了拉长身材和容貌','','14725836982',1,0),(99,611,'哈哈哈哈','',1,1,2,3,0,'高新区','','13100000000',0,0),(100,785,'all','',1,636,1188,1208,0,'酷睿','','15978965478',0,0),(101,785,'all','',1,636,1188,1208,0,'酷睿','450000','15978965478',1,0),(102,816,'Aaa','',1,1,2,3,0,'AAA','000000','13100000000',1,0),(103,792,'套路','',1,1,2,3,0,'解决','','18600161602',1,0),(112,794,'孙双洋','',1,338,569,586,0,'111','054700','18831913100',0,0),(105,846,'艾虎','',1,33007,33008,33189,0,'1111','','18999016279',1,0),(106,853,'陈','',1,636,1188,1218,0,'打开上课上课考试','','13888888888',1,0),(107,544,'test','',1,1,2,3,0,'123','','13900459998',1,0),(108,332,'唐小姐','',1,28240,28737,28739,0,'古新路','','13380514775',1,0),(109,889,'11','',1,338,569,586,0,'11','111','13111111111',1,0),(110,798,'的啊','',1,338,569,586,0,'考虑考虑','111333','13323232223',1,0),(111,777,'次','',1,43776,43904,43905,0,'哦','','15894924561',1,0),(113,1010,'测试','',1,636,1291,1319,0,'111','','13302658657',1,0),(116,1029,'刘德华','',1,636,1291,1319,0,'好快看看','','18877986689',1,0),(115,794,'李威','',1,1,2,3,0,'融泽嘉园1号园','100000','18811766076',1,0),(117,1030,'测试','',1,1,300,322,0,'啦啦啦','','13255668899',1,0),(118,1057,'张三丰','',1,33007,35288,35290,0,'三苏路100','','13096228888',1,0),(119,949,'谢玉振','',1,14234,15194,15264,0,'刚好哈哈哈哈','','17681101110',1,0),(120,1012,'1','',1,338,569,586,0,'1','1','13243188220',0,0),(121,1012,'1','',1,338,569,586,0,'1','1','13243188220',1,0),(122,1083,'邓小春','',1,33007,33008,33042,0,'华侨城','','18589290319',0,0),(123,1083,'李蔡屹','',1,1,2,3,0,'北京','','18589290319',1,0),(124,808,'11','',1,338,339,340,0,'11','1','13243188220',1,0),(125,772,'111','',1,338,569,586,0,'11','11','18251122249',1,0),(126,1109,'小七','',1,16068,16572,16574,0,'池峰路8号','','15712345000',1,0),(127,1179,'一个','',1,28240,29855,47499,0,'vf','','13876565755',1,0),(128,752,'想想','',1,10543,10544,10612,0,'花园路号','','18616372760',1,0),(129,1219,'刘大帅','',1,31929,31930,31931,0,'渝中区春申路','','15723382318',1,0),(130,977,'一美女','',1,338,339,340,0,'个性卫衣','','13828554809',1,0),(131,223,'天天','',1,338,569,586,0,'黄家驹','','18813444465',1,0),(132,1254,'该吃吃','',1,338,569,586,0,'吃饭电饭锅','','17702909999',1,0),(133,1286,'liu shukai','',1,1,2,3,4,'Muscat Grand Mall Dohat Al Adab street','00000','18613045257',1,0),(134,1295,'mei','',1,1,2,3,6,'17824 SW 154th Ct','33187','18613045257',1,0),(135,1298,'milu','',1,1,2,3,4,'Francisco de Zela 2118','15073','18613045257',1,0),(136,1306,'dsad','',1,1,2,3,4,'asdas','00000','18888881234',1,0);

/*Table structure for table `ty_user_level` */

DROP TABLE IF EXISTS `ty_user_level`;

CREATE TABLE `ty_user_level` (
  `level_id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `level_name` varchar(30) DEFAULT NULL COMMENT '头衔名称',
  `amount` decimal(10,2) DEFAULT NULL COMMENT '等级必要金额',
  `discount` smallint(4) DEFAULT NULL COMMENT '折扣',
  `describe` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`level_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Data for the table `ty_user_level` */

insert  into `ty_user_level`(`level_id`,`level_name`,`amount`,`discount`,`describe`) values (1,'普通会员','0.00',100,'会员'),(2,'群主','10000.00',98,'群主'),(3,'小区代理','30000.00',95,'小区代理'),(4,'大区代理','50000.00',92,'大区代理');

/*Table structure for table `ty_user_message` */

DROP TABLE IF EXISTS `ty_user_message`;

CREATE TABLE `ty_user_message` (
  `rec_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `message_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '消息id',
  `category` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '系统消息0，活动消息',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '查看状态：0未查看，1已查看',
  PRIMARY KEY (`rec_id`),
  KEY `user_id` (`user_id`) USING BTREE,
  KEY `message_id` (`message_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

/*Data for the table `ty_user_message` */

insert  into `ty_user_message`(`rec_id`,`user_id`,`message_id`,`category`,`status`) values (1,1,1,0,1),(2,1,2,0,1),(3,1,3,0,1),(4,1,4,0,1),(5,1,5,0,1),(6,1,6,0,0),(7,40,7,0,0),(8,1,8,0,0);

/*Table structure for table `ty_users` */

DROP TABLE IF EXISTS `ty_users`;

CREATE TABLE `ty_users` (
  `user_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id',
  `email` varchar(60) DEFAULT '' COMMENT '邮件',
  `password` varchar(32) DEFAULT '' COMMENT '密码',
  `sex` tinyint(1) unsigned DEFAULT '0' COMMENT '0 保密 1 男 2 女',
  `birthday` int(11) DEFAULT '0' COMMENT '生日',
  `user_money` decimal(10,2) DEFAULT '0.00' COMMENT '用户金额',
  `frozen_money` decimal(10,2) DEFAULT '0.00' COMMENT '冻结金额',
  `distribut_money` decimal(10,2) DEFAULT '0.00' COMMENT '累积分佣金额',
  `pay_points` int(10) unsigned DEFAULT '0' COMMENT '消费积分',
  `address_id` mediumint(8) unsigned DEFAULT '0' COMMENT '默认收货地址',
  `reg_time` int(10) unsigned DEFAULT '0' COMMENT '注册时间',
  `last_login` int(11) unsigned DEFAULT '0' COMMENT '最后登录时间',
  `last_ip` varchar(15) DEFAULT '' COMMENT '最后登录ip',
  `qq` varchar(20) DEFAULT NULL COMMENT 'QQ',
  `mobile` varchar(20) DEFAULT NULL COMMENT '手机号码',
  `mobile_validated` tinyint(3) unsigned DEFAULT '0' COMMENT '是否验证手机',
  `oauth` varchar(10) DEFAULT '' COMMENT '第三方来源 wx weibo alipay',
  `openid` varchar(100) DEFAULT NULL COMMENT '第三方唯一标示',
  `unionid` varchar(100) DEFAULT NULL,
  `head_pic` varchar(255) DEFAULT NULL COMMENT '头像',
  `province` int(6) DEFAULT '0' COMMENT '省份',
  `city` int(6) DEFAULT '0' COMMENT '市区',
  `district` int(6) DEFAULT '0' COMMENT '县',
  `email_validated` tinyint(1) unsigned DEFAULT '0' COMMENT '是否验证电子邮箱',
  `nickname` varchar(50) DEFAULT NULL COMMENT '第三方返回昵称',
  `level` tinyint(1) DEFAULT '1' COMMENT '会员等级',
  `discount` decimal(10,2) DEFAULT '1.00' COMMENT '会员折扣，默认1不享受',
  `total_amount` decimal(10,2) DEFAULT '0.00' COMMENT '消费累计额度',
  `is_lock` tinyint(1) DEFAULT '0' COMMENT '是否被锁定冻结',
  `is_distribut` tinyint(1) DEFAULT '0' COMMENT '是否为分销商 0 否 1 是',
  `first_leader` int(11) DEFAULT '0' COMMENT '第一个上级',
  `second_leader` int(11) DEFAULT '0' COMMENT '第二个上级',
  `third_leader` int(11) DEFAULT '0' COMMENT '第三个上级',
  `token` varchar(64) DEFAULT '' COMMENT '用于app 授权类似于session_id',
  `open_id` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `nick_name` varchar(255) DEFAULT NULL,
  `seat` int(255) DEFAULT '0',
  `call_cate` int(11) DEFAULT '0',
  `call_service` int(11) DEFAULT '0',
  `call_mf` int(11) DEFAULT '0',
  `call_s` int(11) DEFAULT '0',
  `wait_num` int(11) DEFAULT '0',
  `wait_type` int(255) DEFAULT '0',
  `distribution` int(11) DEFAULT '0' COMMENT '分销商',
  `card_name` varchar(100) DEFAULT NULL,
  `card_num` varchar(100) DEFAULT NULL,
  `card_brank` varchar(100) DEFAULT NULL,
  `qrcode` varchar(100) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL COMMENT '父级ID',
  `parent_path` text COMMENT '父级path',
  `total_a` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT 'A分支的投资总额',
  `total_b` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT 'B分支的投资总额',
  `count_a` int(11) NOT NULL DEFAULT '0' COMMENT 'A分支会员的总数',
  `count_b` int(11) NOT NULL DEFAULT '0' COMMENT 'B分支会员的总数',
  `zhitui_id` int(11) DEFAULT NULL COMMENT '直推ID',
  `tou_zi` decimal(11,2) DEFAULT '0.00' COMMENT '当前用户投资金额',
  `all_child` text COMMENT '所有子集的关系表',
  `user_type` tinyint(1) DEFAULT '1' COMMENT '用户分支类型',
  `all_touzi` text COMMENT '关联的所有用户的投资',
  `one_day_time_jifen` int(11) DEFAULT NULL COMMENT '积分领取时间',
  `one_day_time_dong` int(11) DEFAULT NULL COMMENT '动态奖励领取时间',
  `invite_code` varchar(50) DEFAULT NULL COMMENT '推荐码',
  `qr_code` varchar(255) DEFAULT NULL COMMENT '二维码',
  PRIMARY KEY (`user_id`),
  KEY `email` (`email`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1316 DEFAULT CHARSET=utf8;

/*Data for the table `ty_users` */

insert  into `ty_users`(`user_id`,`email`,`password`,`sex`,`birthday`,`user_money`,`frozen_money`,`distribut_money`,`pay_points`,`address_id`,`reg_time`,`last_login`,`last_ip`,`qq`,`mobile`,`mobile_validated`,`oauth`,`openid`,`unionid`,`head_pic`,`province`,`city`,`district`,`email_validated`,`nickname`,`level`,`discount`,`total_amount`,`is_lock`,`is_distribut`,`first_leader`,`second_leader`,`third_leader`,`token`,`open_id`,`country`,`gender`,`nick_name`,`seat`,`call_cate`,`call_service`,`call_mf`,`call_s`,`wait_num`,`wait_type`,`distribution`,`card_name`,`card_num`,`card_brank`,`qrcode`,`parent_id`,`parent_path`,`total_a`,`total_b`,`count_a`,`count_b`,`zhitui_id`,`tou_zi`,`all_child`,`user_type`,`all_touzi`,`one_day_time_jifen`,`one_day_time_dong`,`invite_code`,`qr_code`) values (1315,'','519475228fe35ad067744465c42a19b2',0,0,'900.00','0.00','0.00',0,0,1558516098,0,'',NULL,'18812345660',1,'',NULL,NULL,NULL,0,0,0,0,'18812345660',1,'1.00','0.00',0,1,0,0,0,'',NULL,NULL,NULL,NULL,0,0,0,0,0,0,0,0,NULL,NULL,NULL,NULL,1314,'1309,1310,1312,1313,1314','0.00','0.00',0,0,1310,'0.00',NULL,1,NULL,NULL,NULL,NULL,NULL),(1314,'','519475228fe35ad067744465c42a19b2',0,0,'0.00','0.00','0.00',0,0,1558515950,0,'',NULL,'18812345679',1,'',NULL,NULL,NULL,0,0,0,0,'18812345679',1,'1.00','0.00',0,1,0,0,0,'',NULL,NULL,NULL,NULL,0,0,0,0,0,0,0,0,NULL,NULL,NULL,NULL,1313,'1309,1310,1312,1313','0.00','0.00',0,0,1310,'0.00',NULL,1,NULL,NULL,NULL,NULL,NULL),(1313,'','519475228fe35ad067744465c42a19b2',0,0,'0.00','0.00','0.00',0,0,1558515766,0,'',NULL,'18812345676',1,'',NULL,NULL,NULL,0,0,0,0,'18812345676',1,'1.00','0.00',0,1,0,0,0,'',NULL,NULL,NULL,NULL,0,0,0,0,0,0,0,0,NULL,NULL,NULL,NULL,1312,'1309,1310,1312','0.00','0.00',0,0,1310,'0.00',NULL,1,NULL,NULL,NULL,NULL,NULL),(1312,'','519475228fe35ad067744465c42a19b2',0,0,'0.00','0.00','0.00',0,0,1558514457,0,'',NULL,'18812345675',1,'',NULL,NULL,NULL,0,0,0,0,'18812345675',1,'1.00','0.00',0,1,0,0,0,'',NULL,NULL,NULL,NULL,0,0,0,0,0,0,0,0,NULL,NULL,NULL,NULL,1310,'1309,1310','0.00','0.00',0,0,1310,'0.00',NULL,1,NULL,NULL,NULL,NULL,NULL),(1311,'','519475228fe35ad067744465c42a19b2',0,0,'0.00','0.00','0.00',0,0,1558513781,0,'',NULL,'18812345672',1,'',NULL,NULL,NULL,0,0,0,0,'18812345672',1,'1.00','0.00',0,1,0,0,0,'',NULL,NULL,NULL,NULL,0,0,0,0,0,0,0,0,NULL,NULL,NULL,NULL,1309,'1309','0.00','0.00',0,0,1310,'0.00',NULL,2,NULL,NULL,NULL,NULL,NULL),(1310,'','519475228fe35ad067744465c42a19b2',0,0,'0.00','0.00','0.00',500,0,1558513754,0,'',NULL,'18812345671',1,'',NULL,NULL,NULL,0,0,0,0,'18812345671',1,'1.00','0.00',0,1,0,0,0,'',NULL,NULL,NULL,NULL,0,0,0,0,0,0,0,0,NULL,NULL,NULL,NULL,1309,'1309','0.00','0.00',0,0,1309,'50000.00',NULL,1,NULL,NULL,NULL,'13101370','Public/upload/2019_05/23/1558593415.png'),(1309,'','519475228fe35ad067744465c42a19b2',0,0,'0.00','0.00','0.00',400,0,1558513541,0,'',NULL,'18812345670',1,'',NULL,NULL,NULL,0,0,0,0,'18812345670',1,'1.00','0.00',0,1,0,0,0,'',NULL,NULL,NULL,NULL,0,0,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,'0.00','0.00',0,0,NULL,'0.00','a:5:{i:1309;a:2:{i:1;s:4:\"1310\";i:2;s:4:\"1311\";}i:1310;a:1:{i:1;s:4:\"1312\";}i:1312;a:1:{i:1;s:4:\"1313\";}i:1313;a:1:{i:1;s:4:\"1314\";}i:1314;a:1:{i:1;s:4:\"1315\";}}',1,NULL,NULL,NULL,NULL,NULL);

/*Table structure for table `ty_wx_user` */

DROP TABLE IF EXISTS `ty_wx_user`;

CREATE TABLE `ty_wx_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '表id',
  `uid` int(11) NOT NULL COMMENT 'uid',
  `wxname` varchar(60) NOT NULL COMMENT '公众号名称',
  `aeskey` varchar(256) NOT NULL DEFAULT '' COMMENT 'aeskey',
  `encode` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'encode',
  `appid` varchar(50) NOT NULL DEFAULT '' COMMENT 'appid',
  `appsecret` varchar(50) NOT NULL DEFAULT '' COMMENT 'appsecret',
  `wxid` varchar(64) NOT NULL COMMENT '公众号原始ID',
  `weixin` char(64) NOT NULL COMMENT '微信号',
  `headerpic` char(255) NOT NULL COMMENT '头像地址',
  `token` char(255) NOT NULL COMMENT 'token',
  `w_token` varchar(150) NOT NULL DEFAULT '' COMMENT '微信对接token',
  `create_time` int(11) NOT NULL COMMENT 'create_time',
  `updatetime` int(11) NOT NULL COMMENT 'updatetime',
  `tplcontentid` varchar(2) NOT NULL COMMENT '内容模版ID',
  `share_ticket` varchar(150) NOT NULL COMMENT '分享ticket',
  `share_dated` char(15) NOT NULL COMMENT 'share_dated',
  `authorizer_access_token` varchar(200) NOT NULL COMMENT 'authorizer_access_token',
  `authorizer_refresh_token` varchar(200) NOT NULL COMMENT 'authorizer_refresh_token',
  `authorizer_expires` char(10) NOT NULL COMMENT 'authorizer_expires',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '类型',
  `web_access_token` varchar(200) NOT NULL COMMENT ' 网页授权token',
  `web_refresh_token` varchar(200) NOT NULL COMMENT 'web_refresh_token',
  `web_expires` int(11) NOT NULL COMMENT '过期时间',
  `qr` varchar(200) NOT NULL COMMENT 'qr',
  `menu_config` text COMMENT '菜单',
  `wait_access` tinyint(1) DEFAULT '0' COMMENT '微信接入状态,0待接入1已接入',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE,
  KEY `uid_2` (`uid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='微信公共帐号';

/*Data for the table `ty_wx_user` */

insert  into `ty_wx_user`(`id`,`uid`,`wxname`,`aeskey`,`encode`,`appid`,`appsecret`,`wxid`,`weixin`,`headerpic`,`token`,`w_token`,`create_time`,`updatetime`,`tplcontentid`,`share_ticket`,`share_dated`,`authorizer_access_token`,`authorizer_refresh_token`,`authorizer_expires`,`type`,`web_access_token`,`web_refresh_token`,`web_expires`,`qr`,`menu_config`,`wait_access`) values (16,14,'搜豹公司','jjpjvIMsKvowPvDlWEDmvCiVCCMiBnTjGdvOadPSGcp',0,'wxe7abd31','535','gh_b','wyp58','./tpl/User/default/common/images/portrait.jpg','vjotae1439949952','so',1439950067,1439950507,'1','','','','','',2,'','',1490636250,'http://wd.imakebe.com/uploads/a/admin/d/d/9/4/thumb_55d3e4ea93538.png',NULL,1);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
