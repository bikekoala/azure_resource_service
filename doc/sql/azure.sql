-- MySQL dump 10.13  Distrib 5.5.39, for Linux (x86_64)
--
-- Host: localhost    Database: ucw_cmdb_dev
-- ------------------------------------------------------
-- Server version	5.5.39-cll-lve

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `azure_res_item`
--

DROP TABLE IF EXISTS `azure_res_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `azure_res_item` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `op_id` int(11) unsigned NOT NULL COMMENT '操作表ID',
  `data` text NOT NULL COMMENT '条目数据',
  `service_name` varchar(50) NOT NULL COMMENT '服务名称',
  `phase_name` varchar(50) NOT NULL COMMENT '当前执行阶段名称',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '执行状态(-1失败0已接受1处理中2成功）',
  `message` text COMMENT '状态消息',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `op_id` (`op_id`) USING BTREE,
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Azure资源条目表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `azure_res_item_cs`
--

DROP TABLE IF EXISTS `azure_res_item_cs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `azure_res_item_cs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `item_id` int(11) unsigned NOT NULL COMMENT '条目表ID',
  `name` varchar(63) NOT NULL COMMENT '云服务名称',
  `label` varchar(100) NOT NULL COMMENT '云服务base64编码的标识符',
  `location` varchar(50) NOT NULL COMMENT '创建云服务的位置',
  `request_id` char(32) DEFAULT NULL COMMENT '响应ID',
  `is_deleted` tinyint(1) unsigned NOT NULL COMMENT '是否已删除',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE,
  KEY `item_id` (`item_id`) USING BTREE,
  KEY `request_id` (`request_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Azure资源条目云服务表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `azure_res_item_sa`
--

DROP TABLE IF EXISTS `azure_res_item_sa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `azure_res_item_sa` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `item_id` int(11) unsigned NOT NULL COMMENT '条目表ID',
  `sub_id` char(36) NOT NULL COMMENT '订阅ID',
  `name` varchar(63) NOT NULL COMMENT '名称',
  `label` varchar(100) NOT NULL COMMENT '标签',
  `location` varchar(50) NOT NULL COMMENT '地域',
  `disk_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '账户内磁盘数',
  `request_id` char(32) NOT NULL COMMENT '请求ID',
  `is_created` tinyint(1) unsigned NOT NULL COMMENT '是否已创建',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `item_id` (`item_id`),
  KEY `sub_id` (`sub_id`),
  KEY `request_id` (`request_id`) USING BTREE,
  KEY `location` (`location`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Azure资源条目存储账户表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `azure_res_item_vmd`
--

DROP TABLE IF EXISTS `azure_res_item_vmd`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `azure_res_item_vmd` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `item_id` int(11) unsigned NOT NULL COMMENT '条目表ID',
  `sa_id` int(11) unsigned NOT NULL COMMENT '存储账户ID',
  `vn_id` int(11) unsigned NOT NULL COMMENT '虚拟网络表ID',
  `cs_id` int(11) unsigned NOT NULL COMMENT '云服务表ID',
  `name` varchar(63) NOT NULL COMMENT '部署名称',
  `label` varchar(255) NOT NULL COMMENT '标签',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Azure资源条目虚拟机部部署表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `azure_res_item_vmd_role`
--

DROP TABLE IF EXISTS `azure_res_item_vmd_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `azure_res_item_vmd_role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `vmd_id` int(11) unsigned NOT NULL COMMENT '虚拟机部署表ID',
  `size_id` int(11) unsigned NOT NULL COMMENT '虚拟机尺寸表ID',
  `image_id` int(11) unsigned NOT NULL COMMENT '虚拟机镜像表ID',
  `os_media_link` varchar(255) NOT NULL COMMENT '系统盘文件链接',
  `data_media_link` varchar(255) NOT NULL COMMENT '数据盘文件链接',
  `data_disk_size` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '数据盘容量（单位GB）',
  `data_disk_label` varchar(100) NOT NULL COMMENT '数据盘base64编码的标识符',
  `internal_ip` varchar(15) NOT NULL COMMENT '内部IP',
  `host_name` varchar(63) NOT NULL COMMENT '虚拟机名称',
  `user_name` varchar(255) NOT NULL COMMENT '用户名',
  `user_password` varchar(123) NOT NULL COMMENT '用户密码',
  `request_id` char(32) DEFAULT NULL COMMENT '请求ID',
  `is_deleted` tinyint(1) unsigned NOT NULL COMMENT '是否已删除',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `vmd_id_host_name` (`vmd_id`,`host_name`) USING BTREE,
  KEY `request_id` (`request_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Azure资源条目虚拟机部署角色表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `azure_res_item_vn`
--

DROP TABLE IF EXISTS `azure_res_item_vn`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `azure_res_item_vn` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `item_id` int(11) unsigned NOT NULL COMMENT '条目表ID',
  `sub_id` char(36) NOT NULL COMMENT '订阅ID',
  `name` varchar(63) NOT NULL COMMENT '虚拟网络名称',
  `location` varchar(50) NOT NULL COMMENT '虚拟网络地域',
  `address_prefix` varchar(18) NOT NULL COMMENT '虚拟网络地址前缀',
  `request_id` char(32) DEFAULT NULL COMMENT '请求ID',
  `is_created` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否创建',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sub_id_name` (`sub_id`,`name`) USING BTREE,
  KEY `item_id` (`item_id`),
  KEY `location` (`location`),
  KEY `request_id` (`request_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Azure资源条目虚拟网络表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `azure_res_item_vn_subnet`
--

DROP TABLE IF EXISTS `azure_res_item_vn_subnet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `azure_res_item_vn_subnet` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `vn_id` int(11) unsigned NOT NULL COMMENT '虚拟网络表ID',
  `name` varchar(63) NOT NULL COMMENT '子网名称',
  `address_prefix` varchar(18) NOT NULL COMMENT '子网地址前缀',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `vn_id` (`vn_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Azure资源条目虚拟网络子网表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `azure_res_op`
--

DROP TABLE IF EXISTS `azure_res_op`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `azure_res_op` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `sub_id` char(36) NOT NULL COMMENT '订阅ID',
  `callback_url` varchar(255) NOT NULL COMMENT '回调URL',
  `api_name` varchar(50) NOT NULL COMMENT 'API名称',
  `api_data` text NOT NULL COMMENT 'API数据',
  `callback_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'callback状态',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '操作状态',
  `message` varchar(255) NOT NULL DEFAULT '' COMMENT '操作信息',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Azure资源操作表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `azure_res_status`
--

DROP TABLE IF EXISTS `azure_res_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `azure_res_status` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `request_id` char(32) NOT NULL COMMENT '请求ID',
  `status` varchar(20) NOT NULL COMMENT '响应状态',
  `error_code` varchar(50) NOT NULL COMMENT '响应错误码',
  `error_message` text NOT NULL COMMENT '响应错误信息',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `request_id` (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Azure资源异步操作状态表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `azure_vm_image`
--

DROP TABLE IF EXISTS `azure_vm_image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `azure_vm_image` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `display_index` int(11) unsigned NOT NULL COMMENT '镜像显示顺序索引',
  `display_name` varchar(255) NOT NULL COMMENT '镜像显示名称',
  `label` varchar(255) NOT NULL COMMENT '镜像标签（对应API的Label）',
  `source_name` varchar(255) NOT NULL COMMENT '镜像源名称（对应API的Name）',
  `os_name` varchar(50) NOT NULL COMMENT '系统名称（对应API的OS）',
  `is_disabled` tinyint(1) unsigned NOT NULL COMMENT '是否禁用',
  PRIMARY KEY (`id`),
  KEY `source_name` (`source_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Azure虚拟机镜像表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `azure_vm_size`
--

DROP TABLE IF EXISTS `azure_vm_size`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `azure_vm_size` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号ID',
  `name` varchar(32) NOT NULL COMMENT '名称',
  `core` int(10) unsigned NOT NULL COMMENT '内核数',
  `memory` decimal(11,2) unsigned NOT NULL COMMENT '内存数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Azure虚拟机尺寸表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `azure_subscription`
--

DROP TABLE IF EXISTS `azure_subscription`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `azure_subscription` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `guid` char(36) NOT NULL COMMENT '订阅GUID',
  `name` varchar(36) NOT NULL COMMENT '订阅名称',
  `cert` text NOT NULL COMMENT '管理证书私钥',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 -1禁用0未分配1已使用',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `guid` (`guid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Azure订阅表';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-04-11 11:24:50
