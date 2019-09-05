/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : base_framework

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-09-28 23:27:28
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `rds_menu`
-- ----------------------------
DROP TABLE IF EXISTS `rds_menu`;
CREATE TABLE `rds_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '菜单名称',
  `icon` varchar(128) NOT NULL DEFAULT '' COMMENT '菜单图标',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '上级菜单id',
  `module` varchar(32) NOT NULL DEFAULT '' COMMENT '模块名称',
  `controller` varchar(32) NOT NULL DEFAULT '' COMMENT '控制器名称',
  `action` varchar(32) NOT NULL DEFAULT '' COMMENT '方法名称',
  `param` varchar(255) NOT NULL DEFAULT '' COMMENT '附加参数',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '类型,0:只作菜单；1:权限点+菜单',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否禁用',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `list_order` int(11) NOT NULL DEFAULT '100' COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `index_pid` (`pid`)
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8 COMMENT='后台菜单表';

-- ----------------------------
-- Records of rds_menu
-- ----------------------------
INSERT INTO `rds_menu` VALUES ('1', '缓存更新', 'fa-trash-o', '0', 'console', 'index', 'cache', '', '1', '0', '', '1');
INSERT INTO `rds_menu` VALUES ('2', '系统公告', 'fa-bullhorn', '4', 'console', 'index', 'index', '', '1', '1', '', '1');
INSERT INTO `rds_menu` VALUES ('3', '系统管理', 'fa-cogs', '0', 'console', 'role', 'index', '', '1', '1', '', '3');
INSERT INTO `rds_menu` VALUES ('4', '系统信息', 'fa-flag-o', '0', 'console', 'index', 'index', '', '0', '1', '', '2');
INSERT INTO `rds_menu` VALUES ('5', '菜单管理', 'fa-book', '3', 'console', 'menu', 'index', '', '0', '1', '', '1');
INSERT INTO `rds_menu` VALUES ('6', '角色管理', 'fa-group', '3', 'console', 'role', 'index', '', '0', '1', '', '2');
INSERT INTO `rds_menu` VALUES ('7', '用户管理', 'fa-user', '3', 'console', 'user', 'index', '', '1', '1', '', '3');
INSERT INTO `rds_menu` VALUES ('8', '添加菜单', 'fa-book', '5', 'console', 'menu', 'add', '', '1', '0', '', '1');
INSERT INTO `rds_menu` VALUES ('9', '编辑菜单', 'fa-book', '5', 'console', 'menu', 'edit', '', '1', '0', '', '2');
INSERT INTO `rds_menu` VALUES ('10', '删除菜单', 'fa-book', '5', 'console', 'menu', 'delete', '', '1', '0', '', '3');
INSERT INTO `rds_menu` VALUES ('11', '添加角色', 'fa-book', '6', 'console', 'role', 'add', '', '1', '0', '', '1');
INSERT INTO `rds_menu` VALUES ('12', '编辑角色', 'fa-book', '6', 'console', 'role', 'edit', '', '1', '0', '', '2');
INSERT INTO `rds_menu` VALUES ('13', '删除角色', 'fa-book', '6', 'console', 'role', 'delete', '', '1', '0', '', '3');
INSERT INTO `rds_menu` VALUES ('14', '权限设置', 'fa-book', '6', 'console', 'role', 'authorize', '', '1', '0', '', '4');
INSERT INTO `rds_menu` VALUES ('15', '添加用户', 'fa-book', '7', 'console', 'user', 'add', '', '1', '0', '', '1');
INSERT INTO `rds_menu` VALUES ('16', '编辑用户', 'fa-book', '7', 'console', 'user', 'edit', '', '1', '0', '', '2');
INSERT INTO `rds_menu` VALUES ('17', '删除用户', 'fa-book', '7', 'console', 'user', 'delete', '', '1', '0', '', '3');
INSERT INTO `rds_menu` VALUES ('20', '系统设置', 'fa-book', '3', 'console', 'system_config', 'index', '', '1', '1', '', '4');
INSERT INTO `rds_menu` VALUES ('94', '查看公司信息', '', '20', 'console', 'system_config', 'baseInfo', '', '1', '0', '', '1');
INSERT INTO `rds_menu` VALUES ('99', '菜单排序', '', '5', 'console', 'menu', 'order', '', '1', '0', '', '4');
INSERT INTO `rds_menu` VALUES ('100', '保存公司信息', '', '20', 'console', 'system_config', 'saveBaseInfo', '', '1', '0', '', '100');

-- ----------------------------
-- Table structure for `rds_migrations`
-- ----------------------------
DROP TABLE IF EXISTS `rds_migrations`;
CREATE TABLE `rds_migrations` (
  `version` bigint(20) NOT NULL,
  `migration_name` varchar(100) DEFAULT NULL,
  `start_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `end_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `breakpoint` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of rds_migrations
-- ----------------------------
INSERT INTO `rds_migrations` VALUES ('20170422163235', 'CreateMenu', '2017-06-10 16:05:59', '2017-06-10 16:06:00', '0');
INSERT INTO `rds_migrations` VALUES ('20170422163240', 'CreateUser', '2017-06-10 16:07:52', '2017-06-10 16:07:53', '0');
INSERT INTO `rds_migrations` VALUES ('20170422164000', 'CreateRole', '2017-07-15 17:13:41', '2017-07-15 17:13:42', '0');
INSERT INTO `rds_migrations` VALUES ('20170422164100', 'CreateRoleUser', '2017-07-15 17:13:42', '2017-07-15 17:13:42', '0');
INSERT INTO `rds_migrations` VALUES ('20170422164200', 'CreateRoleAccess', '2017-07-15 17:13:42', '2017-07-15 17:13:43', '0');
INSERT INTO `rds_migrations` VALUES ('20170423163240', 'CreateSystemConfig', '2017-06-14 00:12:59', '2017-06-14 00:13:00', '0');
INSERT INTO `rds_migrations` VALUES ('20170606024836', 'CreateCustomer', '2017-07-19 00:09:02', '2017-07-19 00:09:03', '0');
INSERT INTO `rds_migrations` VALUES ('20170607092131', 'CreateCustomerVisitLog', '2017-07-19 00:09:03', '2017-07-19 00:09:03', '0');
INSERT INTO `rds_migrations` VALUES ('20170608081900', 'CreateCustomerContact', '2017-07-19 00:09:03', '2017-07-19 00:09:04', '0');
INSERT INTO `rds_migrations` VALUES ('20170610031139', 'Storage', '2017-07-15 11:38:45', '2017-07-15 11:38:45', '0');
INSERT INTO `rds_migrations` VALUES ('20170610031148', 'Stock', '2017-07-15 11:38:45', '2017-07-15 11:38:46', '0');
INSERT INTO `rds_migrations` VALUES ('20170610041321', 'UpdateStorage', '2017-07-15 11:38:46', '2017-07-15 11:38:47', '0');
INSERT INTO `rds_migrations` VALUES ('20170610100630', 'CreateProduct', '2017-08-07 21:13:14', '2017-08-07 21:13:15', '0');
INSERT INTO `rds_migrations` VALUES ('20170612025016', 'ProductConfigList', '2017-07-15 11:38:47', '2017-07-15 11:38:47', '0');
INSERT INTO `rds_migrations` VALUES ('20170612032655', 'UpdateProductConfigList', '2017-07-15 11:38:48', '2017-07-15 11:38:48', '0');
INSERT INTO `rds_migrations` VALUES ('20170613072031', 'Supplier', '2017-07-15 11:38:48', '2017-07-15 11:38:49', '0');
INSERT INTO `rds_migrations` VALUES ('20170613084030', 'SupplierContact', '2017-07-15 11:38:49', '2017-07-15 11:38:49', '0');
INSERT INTO `rds_migrations` VALUES ('20170613161630', 'AddSystemConfigMenu', '2017-06-14 01:03:41', '2017-06-14 01:03:41', '0');
INSERT INTO `rds_migrations` VALUES ('20170614025925', 'EnquirySheet', '2017-07-15 11:38:49', '2017-07-15 11:38:50', '0');
INSERT INTO `rds_migrations` VALUES ('20170614035703', 'UpdateEnquirySheet', '2017-07-15 11:38:50', '2017-07-15 11:38:51', '0');
INSERT INTO `rds_migrations` VALUES ('20170614073412', 'UpdateProduct', '2017-07-15 11:38:51', '2017-07-15 11:38:52', '0');
INSERT INTO `rds_migrations` VALUES ('20170614101626', 'UpdateEnquirySheetTwo', '2017-07-15 11:38:52', '2017-07-15 11:38:56', '0');
INSERT INTO `rds_migrations` VALUES ('20170614154157', 'CreateProject', '2017-06-17 19:01:18', '2017-06-17 19:01:19', '0');
INSERT INTO `rds_migrations` VALUES ('20170614160616', 'CreateProjectMember', '2017-08-07 21:14:16', '2017-06-17 19:01:20', '0');
INSERT INTO `rds_migrations` VALUES ('20170614161738', 'CreateProjectTask', '2017-06-17 19:01:20', '2017-06-17 19:01:20', '0');
INSERT INTO `rds_migrations` VALUES ('20170614162258', 'CreateProjectLog', '2017-06-17 19:01:20', '2017-06-17 19:01:21', '0');
INSERT INTO `rds_migrations` VALUES ('20170614162649', 'CreateProjectTraceLog', '2017-06-17 19:01:21', '2017-06-17 19:01:21', '0');
INSERT INTO `rds_migrations` VALUES ('20170614163354', 'CreateProjectPurchasePlan', '2017-06-17 19:01:21', '2017-06-17 19:01:22', '0');
INSERT INTO `rds_migrations` VALUES ('20170615084501', 'CreateEnquiryProduct', '2017-07-15 17:13:43', '2017-07-15 17:13:43', '0');
INSERT INTO `rds_migrations` VALUES ('20170615141546', 'AddProjectMenu', '2017-06-16 02:02:01', '2017-06-16 02:02:01', '0');
INSERT INTO `rds_migrations` VALUES ('20170617105452', 'CreateProjectBudget', '2017-06-17 19:02:19', '2017-06-17 19:02:19', '0');
INSERT INTO `rds_migrations` VALUES ('20170619084111', 'CreateClauseModel', '2017-07-15 17:13:43', '2017-07-15 17:13:44', '0');
INSERT INTO `rds_migrations` VALUES ('20170620074034', 'CreatePurchaseContarctProduct', '2017-07-15 17:13:44', '2017-07-15 17:13:44', '0');
INSERT INTO `rds_migrations` VALUES ('20170622081138', 'CreateServiceExchange', '2017-07-15 17:13:44', '2017-07-15 17:13:45', '0');
INSERT INTO `rds_migrations` VALUES ('20170622081156', 'CreateServiceProduct', '2017-07-15 17:13:45', '2017-07-15 17:13:45', '0');
INSERT INTO `rds_migrations` VALUES ('20170624073904', 'CreateQuotationProduct', '2017-07-15 17:13:45', '2017-07-15 17:13:46', '0');
INSERT INTO `rds_migrations` VALUES ('20170626090936', 'CreateSalesProduct', '2017-07-15 17:13:46', '2017-07-15 17:13:46', '0');
INSERT INTO `rds_migrations` VALUES ('20170627032410', 'CreateSupplier', '2017-07-15 17:14:12', '2017-07-15 17:14:13', '0');
INSERT INTO `rds_migrations` VALUES ('20170627032439', 'CreateSupplierContact', '2017-07-15 17:14:24', '2017-07-15 17:14:24', '0');
INSERT INTO `rds_migrations` VALUES ('20170628154123', 'CreatePaymentManage', '2017-06-28 23:56:27', '2017-06-28 23:56:28', '0');
INSERT INTO `rds_migrations` VALUES ('20170629084040', 'CreateEnquirySheet', '2017-07-15 17:14:49', '2017-07-15 17:14:50', '0');
INSERT INTO `rds_migrations` VALUES ('20170629141911', 'UpdateProjectPurchasePlanV1', '2017-06-29 22:50:42', '2017-06-29 22:50:43', '0');
INSERT INTO `rds_migrations` VALUES ('20170629162331', 'UpdateProjectBudgetV1', '2017-06-30 00:26:11', '2017-06-30 00:26:12', '0');
INSERT INTO `rds_migrations` VALUES ('20170630052508', 'CreatePurchaseContarct', '2017-07-15 17:14:50', '2017-07-15 17:14:50', '0');
INSERT INTO `rds_migrations` VALUES ('20170701064633', 'CreateQuotation', '2017-07-15 17:14:50', '2017-07-15 17:14:51', '0');
INSERT INTO `rds_migrations` VALUES ('20170704060433', 'CreateSalesContarct', '2017-07-15 17:14:51', '2017-07-15 17:14:51', '0');
INSERT INTO `rds_migrations` VALUES ('20170704141534', 'AddFinancialMenu', '2017-07-05 23:17:18', '2017-07-05 23:17:18', '0');
INSERT INTO `rds_migrations` VALUES ('20170704142616', 'CreateFundFlowList', '2017-07-07 00:44:28', '2017-07-07 00:44:28', '0');
INSERT INTO `rds_migrations` VALUES ('20170705150129', 'UpdatePaymentManageV1', '2017-07-07 00:44:28', '2017-07-07 00:44:34', '0');
INSERT INTO `rds_migrations` VALUES ('20170707033756', 'CreateStockDetail', '2017-07-15 17:14:51', '2017-07-15 17:14:52', '0');
INSERT INTO `rds_migrations` VALUES ('20170707072718', 'CreateStorageOutInInfo', '2017-07-15 17:14:52', '2017-07-15 17:14:52', '0');
INSERT INTO `rds_migrations` VALUES ('20170710015715', 'CreateStock', '2017-07-15 17:15:03', '2017-07-15 17:15:03', '0');
INSERT INTO `rds_migrations` VALUES ('20170711163601', 'AddFinancialMenuInvoice', '2017-07-12 00:49:21', '2017-07-12 00:49:21', '0');
INSERT INTO `rds_migrations` VALUES ('20170711165104', 'CreateInvoiceManage', '2017-07-13 22:56:01', '2017-07-13 22:56:02', '0');
INSERT INTO `rds_migrations` VALUES ('20170711165112', 'CreateInvoiceFlow', '2017-07-13 22:56:02', '2017-07-13 22:56:02', '0');
INSERT INTO `rds_migrations` VALUES ('20170714024659', 'CreateStorageOutInProduct', '2017-07-15 17:15:03', '2017-07-15 17:15:04', '0');
INSERT INTO `rds_migrations` VALUES ('20170715092646', 'CreateArea', '2017-07-15 18:04:30', '2017-07-15 18:04:32', '0');
INSERT INTO `rds_migrations` VALUES ('20170716152922', 'UpdateInvoiceManageV1', '2017-07-17 00:29:05', '2017-07-17 00:29:06', '0');
INSERT INTO `rds_migrations` VALUES ('20170717031353', 'AddContactMenu', '2017-07-19 00:09:04', '2017-07-19 00:09:04', '0');
INSERT INTO `rds_migrations` VALUES ('20170717034833', 'AddCustomerSupplierMenu', '2017-07-19 00:09:04', '2017-07-19 00:09:04', '0');
INSERT INTO `rds_migrations` VALUES ('20170717035659', 'AddProductMenu', '2017-07-19 00:09:04', '2017-07-19 00:09:04', '0');
INSERT INTO `rds_migrations` VALUES ('20170717040241', 'AddStorageMenu', '2017-07-19 00:09:04', '2017-07-19 00:09:04', '0');
INSERT INTO `rds_migrations` VALUES ('20170717065134', 'AddSystemConfig', '2017-07-19 00:09:04', '2017-07-19 00:09:04', '0');
INSERT INTO `rds_migrations` VALUES ('20170717161509', 'AddExpenseManageMenu', '2017-07-19 00:22:07', '2017-07-19 00:22:07', '0');
INSERT INTO `rds_migrations` VALUES ('20170718144528', 'CreateExpenseManage', '2017-07-19 00:38:35', '2017-07-19 00:38:35', '0');
INSERT INTO `rds_migrations` VALUES ('20170718154509', 'CreateExpenseDetail', '2017-07-19 00:38:35', '2017-07-19 00:38:36', '0');
INSERT INTO `rds_migrations` VALUES ('20170718165611', 'CreatePersonalLoan', '2017-07-19 00:59:47', '2017-07-19 00:59:48', '0');
INSERT INTO `rds_migrations` VALUES ('20170720172635', 'UpdateExpenseManageV1', '2017-07-21 01:28:54', '2017-07-21 01:28:55', '0');
INSERT INTO `rds_migrations` VALUES ('20170721140217', 'UpdatePersonalLoanV1', '2017-07-21 22:04:11', '2017-07-21 22:04:11', '0');
INSERT INTO `rds_migrations` VALUES ('20170722063539', 'UpdateSystemConfigMenuV1', '2017-07-22 19:02:27', '2017-07-22 19:02:27', '0');
INSERT INTO `rds_migrations` VALUES ('20170722073912', 'UpdateSystemConfigV1', '2017-07-22 19:02:27', '2017-07-22 19:02:30', '0');
INSERT INTO `rds_migrations` VALUES ('20170722105042', 'UpdateSystemConfigV2', '2017-07-22 19:02:30', '2017-07-22 19:02:30', '0');
INSERT INTO `rds_migrations` VALUES ('20170722115153', 'CreateContractClause', '2017-08-07 23:59:54', '2017-07-23 23:54:28', '0');
INSERT INTO `rds_migrations` VALUES ('20170725132753', 'AddSystemNoticeAndContractClauseMenu', '2017-08-07 23:59:59', '2017-07-25 21:40:37', '0');
INSERT INTO `rds_migrations` VALUES ('20170725135420', 'CreateSystemNotice', '2017-07-25 22:27:19', '2017-07-25 22:27:20', '0');

-- ----------------------------
-- Table structure for `rds_role`
-- ----------------------------
DROP TABLE IF EXISTS `rds_role`;
CREATE TABLE `rds_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL DEFAULT '' COMMENT '角色名称',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '父角色ID',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `list_order` int(11) NOT NULL DEFAULT '100' COMMENT '排序字段',
  PRIMARY KEY (`id`),
  KEY `index_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='角色信息列表';

-- ----------------------------
-- Records of rds_role
-- ----------------------------
INSERT INTO `rds_role` VALUES ('1', '超级管理员', '0', '1', '拥有系统最高管理员权限！', '1329633709', '1329633709', '100');
INSERT INTO `rds_role` VALUES ('2', '权限测试账号', '1', '1', '', '1502037477', '1502037477', '100');

-- ----------------------------
-- Table structure for `rds_role_access`
-- ----------------------------
DROP TABLE IF EXISTS `rds_role_access`;
CREATE TABLE `rds_role_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL DEFAULT '0' COMMENT '角色id',
  `menu_id` int(11) NOT NULL DEFAULT '0' COMMENT '权限或菜单ID',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:菜单项；1:权限项',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=196 DEFAULT CHARSET=utf8 COMMENT='角色权限表';

-- ----------------------------
-- Records of rds_role_access
-- ----------------------------
INSERT INTO `rds_role_access` VALUES ('179', '2', '3', '1');
INSERT INTO `rds_role_access` VALUES ('180', '2', '5', '0');
INSERT INTO `rds_role_access` VALUES ('181', '2', '9', '1');
INSERT INTO `rds_role_access` VALUES ('182', '2', '6', '0');
INSERT INTO `rds_role_access` VALUES ('183', '2', '14', '1');
INSERT INTO `rds_role_access` VALUES ('184', '2', '20', '1');
INSERT INTO `rds_role_access` VALUES ('185', '2', '94', '1');
INSERT INTO `rds_role_access` VALUES ('186', '2', '96', '1');
INSERT INTO `rds_role_access` VALUES ('187', '2', '101', '1');
INSERT INTO `rds_role_access` VALUES ('188', '2', '102', '1');
INSERT INTO `rds_role_access` VALUES ('189', '2', '4', '0');
INSERT INTO `rds_role_access` VALUES ('190', '2', '2', '1');
INSERT INTO `rds_role_access` VALUES ('191', '2', '74', '0');
INSERT INTO `rds_role_access` VALUES ('192', '2', '75', '1');
INSERT INTO `rds_role_access` VALUES ('193', '2', '105', '1');
INSERT INTO `rds_role_access` VALUES ('194', '2', '106', '1');
INSERT INTO `rds_role_access` VALUES ('195', '2', '109', '1');

-- ----------------------------
-- Table structure for `rds_role_user`
-- ----------------------------
DROP TABLE IF EXISTS `rds_role_user`;
CREATE TABLE `rds_role_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `role_id` int(11) NOT NULL COMMENT '角色ID',
  `dept_id` int(11) NOT NULL COMMENT '部门ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COMMENT='角色信息列表';

-- ----------------------------
-- Records of rds_role_user
-- ----------------------------
INSERT INTO `rds_role_user` VALUES ('2', '3', '2', '0');
INSERT INTO `rds_role_user` VALUES ('15', '1', '1', '0');
INSERT INTO `rds_role_user` VALUES ('16', '6', '1', '0');
INSERT INTO `rds_role_user` VALUES ('24', '7', '2', '0');
INSERT INTO `rds_role_user` VALUES ('25', '8', '2', '0');

-- ----------------------------
-- Table structure for `rds_system_config`
-- ----------------------------
DROP TABLE IF EXISTS `rds_system_config`;
CREATE TABLE `rds_system_config` (
  `conf_id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(64) NOT NULL DEFAULT '' COMMENT '配置代码',
  `name` varchar(128) NOT NULL DEFAULT '' COMMENT '配置名称',
  `value` text NOT NULL COMMENT '配置值',
  `value_range` text NOT NULL COMMENT '可选值范围',
  `store_path` varchar(255) NOT NULL DEFAULT '' COMMENT '图片保存路径',
  `groups` varchar(32) NOT NULL DEFAULT '' COMMENT '配置分组，company：公司信息配置...后继根据需要添加',
  `type` varchar(32) NOT NULL DEFAULT '' COMMENT 'input类型',
  `list_order` int(11) NOT NULL DEFAULT '0' COMMENT '配置类型（company：公司信息配置...后继根据需要添加）',
  PRIMARY KEY (`conf_id`),
  KEY `index_name` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='系统配置表';

-- ----------------------------
-- Records of rds_system_config
-- ----------------------------
INSERT INTO `rds_system_config` VALUES ('1', 'company_name', '公司名称', '天津燃洁斯工业设备有限公司', '', '', 'company', '', '1');
INSERT INTO `rds_system_config` VALUES ('2', 'company_addr', '公司地址', '天津塘沽新北路4668号创新创业园21-南5010', '', '', 'company', '', '2');
INSERT INTO `rds_system_config` VALUES ('3', 'tel', '电话号码', '022-25820176', '', '', 'company', '', '3');
INSERT INTO `rds_system_config` VALUES ('4', 'fax', '传真号码', '022-25820276', '', '', 'company', '', '4');
INSERT INTO `rds_system_config` VALUES ('5', 'bank', '开户银行', '中国农业银行天津新洋支行', '', '', 'company', '', '5');
INSERT INTO `rds_system_config` VALUES ('6', 'account', '银行账号', '3432412344123451', '', '', 'company', '', '6');
INSERT INTO `rds_system_config` VALUES ('7', 'postcode', '邮政编码', '3004501', '', '', 'company', '', '7');
INSERT INTO `rds_system_config` VALUES ('8', 'email', '电子邮箱', 'randys@randys.com.cn', '', '', 'company', '', '8');
INSERT INTO `rds_system_config` VALUES ('9', 'url', '官方网址', 'http://www.randys.com.cn/', '', '', 'company', '', '9');
INSERT INTO `rds_system_config` VALUES ('10', 'tax_no', '公司税号', '4201076983479021', '', '', 'company', '', '10');

-- ----------------------------
-- Table structure for `rds_user`
-- ----------------------------
DROP TABLE IF EXISTS `rds_user`;
CREATE TABLE `rds_user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `login_name` varchar(128) NOT NULL DEFAULT '' COMMENT '用户名称',
  `login_pwd` varchar(255) NOT NULL DEFAULT '' COMMENT '用户登录密码',
  `salt` varchar(32) NOT NULL DEFAULT '0' COMMENT '密码加盐值',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT '用户邮箱',
  `mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '用户手机号',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `nickname` varchar(128) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `real_name` varchar(128) NOT NULL DEFAULT '0' COMMENT '真实姓名',
  `remark` varchar(255) NOT NULL DEFAULT '0' COMMENT '备注',
  `user_type` varchar(32) NOT NULL DEFAULT '' COMMENT '用户类型',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '用户状态。0：禁止；1：正常',
  `create_ip` varchar(16) NOT NULL DEFAULT '' COMMENT '用户创建IP',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '用户创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '用户更新时间',
  `last_login_ip` varchar(16) NOT NULL DEFAULT '' COMMENT '上次登录IP',
  `last_login_time` int(11) NOT NULL DEFAULT '0' COMMENT '上次登录时间',
  `login_count` int(11) NOT NULL DEFAULT '0' COMMENT '登录次数',
  `default_role_id` int(11) NOT NULL DEFAULT '0' COMMENT '默认角色ID',
  PRIMARY KEY (`uid`),
  KEY `index_login_name` (`login_name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
-- Records of rds_user
-- ----------------------------
INSERT INTO `rds_user` VALUES ('1', 'admin', '8edb32b78b15504d625352316e28600f', 'alFQAF', '', '13661916491', '', '', '管理员', '', '', '1', '127.0.0.1', '0', '1505232592', '2130706433', '1506609525', '172', '0');
INSERT INTO `rds_user` VALUES ('3', 'wluabc', '3cdf1629afa6fe4f2d8c4bb493121d3f', '55E4n3', '', '17196432627', '', '', '王一', 'asdf', '', '1', '', '1498144096', '1502038171', '2130706433', '1502458554', '9', '0');
INSERT INTO `rds_user` VALUES ('6', 'wangxiao22', '78bf0abcde1ac9a34b448cae26317a8b', 'gZR2Jd', '111@111.com', '', '', '', '王xiao超级管理员', '大佬说卡；懒得开萨拉；代课老师aa', '', '1', '', '1504939836', '1505232611', '1699868713', '1504940964', '4', '0');
INSERT INTO `rds_user` VALUES ('7', 'aaaaa', '12cd6b5ad89e2b42edcd3cec8b0ad559', 'Sd5db6', '', '', '', '', 'asdfasfbbb', 'asdf', '', '0', '', '1505921895', '1505922180', '', '0', '0', '0');
INSERT INTO `rds_user` VALUES ('8', 'wx222', 'a33bef722ebcfd18c7f10806bbf79b9d', 'rRHzFB', 'a@bbbb.com', '', '', '', '王潇', '测试', '', '0', '', '1505975602', '1505975602', '', '0', '0', '0');
