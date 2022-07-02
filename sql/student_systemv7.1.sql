-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2022-07-01 16:11:00
-- 服务器版本： 10.1.38-MariaDB
-- PHP 版本： 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `student_system`
--

-- --------------------------------------------------------

--
-- 表的结构 `department`
--

CREATE TABLE `department` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `department`
--

INSERT INTO `department` (`id`, `name`) VALUES
(1, '学院行政领导'),
(2, '2021级求知一苑'),
(3, '2021级求知二苑'),
(4, '2021级求知三苑'),
(5, '2021级博士博实苑'),
(6, '2021级英杰一苑'),
(7, '2021级英杰二苑'),
(8, '2021级英杰三苑'),
(9, '2021级英杰四苑'),
(10, '2021级博雅一苑'),
(11, '2021级博雅二苑'),
(12, '2021级博雅三苑'),
(13, '2021级博雅四苑'),
(14, '2021级未名一苑'),
(15, '2021级未名二苑'),
(16, '2021级未名三苑'),
(17, '2021级未名四苑'),
(18, '2021级燕南一苑'),
(19, '2021级燕南二苑'),
(20, '2021级燕南三苑'),
(21, '2021级燕南四苑'),
(22, '2021级MEM一苑'),
(23, '2021级MEM二苑'),
(24, '2021级MEM三苑'),
(25, '2021级MEM四苑'),
(26, '2021级MEM五苑'),
(27, '2020级求知一苑'),
(28, '2020级求知二苑'),
(29, '2020级求知三苑'),
(30, '2020级博士博实苑'),
(31, '2020级英杰一苑'),
(32, '2020级英杰二苑'),
(33, '2020级英杰三苑'),
(34, '2020级英杰四苑'),
(35, '2020级博雅一苑'),
(36, '2020级博雅二苑'),
(37, '2020级博雅三苑'),
(38, '2020级博雅四苑'),
(39, '2020级未名一苑'),
(40, '2020级未名二苑'),
(41, '2020级未名三苑'),
(42, '2020级未名四苑'),
(43, '2020级燕南一苑'),
(44, '2020级燕南二苑'),
(45, '2020级燕南三苑'),
(46, '2020级燕南四苑'),
(47, '2020级MEM一苑'),
(48, '2020级MEM二苑'),
(49, '2020级MEM三苑'),
(50, '2020级MEM四苑'),
(51, '2020级MEM五苑');

-- --------------------------------------------------------

--
-- 表的结构 `hesuan`
--

CREATE TABLE `hesuan` (
  `id` bigint(11) NOT NULL COMMENT '核酸数据id',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '填报时间',
  `user_id` int(11) NOT NULL COMMENT 'user_info表id',
  `cov_time` date NOT NULL COMMENT '检测时间',
  `cov_location` varchar(100) NOT NULL COMMENT '检测地点'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `hesuan`
--

INSERT INTO `hesuan` (`id`, `update_time`, `user_id`, `cov_time`, `cov_location`) VALUES
(1, '2022-06-06 06:24:03', 3, '2022-06-01', '北大（大兴）'),
(2, '2022-06-06 06:24:42', 3, '2022-06-02', '北大（其他校区）'),
(3, '2022-06-06 06:24:53', 3, '2022-06-04', '校外'),
(4, '2022-06-06 06:25:08', 5, '2022-06-01', '北大（大兴）');

-- --------------------------------------------------------

--
-- 表的结构 `log`
--

CREATE TABLE `log` (
  `log_id` bigint(20) NOT NULL COMMENT '日志编号',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '操作时间',
  `log_user_id0` int(11) NOT NULL COMMENT '操作人的校内id',
  `log_type` varchar(100) NOT NULL COMMENT '操作类型（登录、导入、导出、搜索、查询、修改、删除）',
  `log_user_id1` varchar(100) NOT NULL COMMENT '被操作人的校内id',
  `attribute_name` varchar(100) NOT NULL COMMENT '操作的字段名',
  `new_info` varchar(300) NOT NULL COMMENT '新的信息'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `log`
--

INSERT INTO `log` (`log_id`, `time`, `log_user_id0`, `log_type`, `log_user_id1`, `attribute_name`, `new_info`) VALUES
(1, '2022-06-11 13:53:27', 1, '登录', '1', '', 'chrome浏览器，ip地址，操作系统版本号'),
(2, '2022-06-11 13:53:27', 1, '查询', '1', '教师个人信息页面', ''),
(3, '2022-06-11 13:53:27', 1, '修改', '1', '手机号', '18598765432'),
(4, '2022-06-11 13:53:27', 1, '删除', '2', '', ''),
(5, '2022-06-11 13:53:27', 1, '导入', '1', '10个用户', '编号30 - 40'),
(6, '2022-06-11 13:53:27', 1, '修改', '2', '当前居住地址', '2021级求知一苑'),
(7, '2022-06-11 13:53:27', 1, '导出', '1', '学苑管理学生名单页面', '学生详细个人信息/学生学苑名单'),
(8, '2022-06-11 13:53:27', 1, '搜索', '1', '学苑管理学生名单页面', ''),
(9, '2022-06-30 18:03:47', 4, '登录', '4', '', '0.0.0.0, Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36'),
(10, '2022-06-30 18:17:59', 1, '登录', '1', '', '0.0.0.0, Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36'),
(11, '2022-06-30 18:30:59', 4, '登录', '4', '', '0.0.0.0, Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36'),
(12, '2022-06-30 18:58:17', 1, '登录', '1', '', '0.0.0.0, Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36'),
(13, '2022-06-30 21:25:25', 2, '登录', '2', '', '0.0.0.0, Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36'),
(14, '2022-06-30 21:39:20', 1, '登录', '1', '', '0.0.0.0, Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36');

-- --------------------------------------------------------

--
-- 表的结构 `major`
--

CREATE TABLE `major` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `major`
--

INSERT INTO `major` (`id`, `name`) VALUES
(1, '学院行政领导'),
(2, '软件工程与数据技术系'),
(3, '网络软件与系统安全系'),
(4, '集成电路与智能系统系'),
(5, '金融信息与工程管理系'),
(6, '数字艺术与技术传播系'),
(7, '工程博士教育中心'),
(8, '工程管理硕士教育中心'),
(9, '国际与港澳台教育中心'),
(10, '示范性微电子学院建设项目');

-- --------------------------------------------------------

--
-- 表的结构 `rbac_menu`
--

CREATE TABLE `rbac_menu` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `name` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT '菜单名',
  `icon` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT '图标',
  `controller` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT '控制器',
  `sort` int(11) NOT NULL COMMENT '从小到大排序'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统菜单';

--
-- 转存表中的数据 `rbac_menu`
--

INSERT INTO `rbac_menu` (`id`, `pid`, `name`, `icon`, `controller`, `sort`) VALUES
(1, 0, '首页', 'fa-home', 'main', 1),
(2, 0, '系统管理', 'fa-laptop', 'manage', 2),
(3, 2, '批量导入用户', '', 'manage.systemimport', 1),
(4, 2, '管理系统用户', '', 'manage.systemuser', 2),
(5, 2, '管理学苑权限', '', 'manage.systempermission', 3),
(6, 2, '查询系统日志', '', 'manage.systemlog', 4),
(7, 0, '我的学苑', 'fa-calendar', 'supervise', 3),
(8, 7, '学苑学生名单', '', 'supervise.checkstudent', 1),
(9, 7, '学苑管理员名单', '', 'supervise.checksupervisor', 2),
(10, 7, '学生联络名单', '', 'supervise.checkcontact', 3),
(11, 0, '个人信息', 'fa-credit-card\n', 'personal', 4),
(12, 11, '学生个人信息', '', 'personal.studentinfo', 1),
(13, 11, '教师个人信息', '', 'personal.teacherinfo', 2),
(14, 11, '更新学生联系方式', '', 'personal.updatestudentcontact', 3),
(15, 11, '更新个人当前状态', '', 'personal.updatestatus', 4),
(16, 11, '更新核酸疫苗状态', '', 'personal.updatehesuan', 5),
(17, 11, '更新教师联系方式', '', 'personal.updateteachercontact', 3);

-- --------------------------------------------------------

--
-- 表的结构 `rbac_permission`
--

CREATE TABLE `rbac_permission` (
  `id` int(11) NOT NULL,
  `name` varchar(11) NOT NULL COMMENT '权限名称',
  `controller` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统权限';

--
-- 转存表中的数据 `rbac_permission`
--

INSERT INTO `rbac_permission` (`id`, `name`, `controller`, `action`) VALUES
(1, '1', '*', '*'),
(2, '2', 'main', '*'),
(3, '3', 'manage', '*'),
(4, '4', 'manage.systemimport', '*'),
(5, '5', 'manage.systemuser', '*'),
(6, '6', 'manage.systempermission', '*'),
(7, '7', 'manage.systemlog', '*'),
(8, '8', 'supervise', '*'),
(9, '9', 'supervise.checkstudent', '*'),
(10, '10', 'supervise.checksupervisor', '*'),
(11, '11', 'supervise.checkcontact', '*'),
(12, '12', 'personal', '*'),
(13, '13', 'personal.studentinfo', '*'),
(14, '14', 'personal.teacherinfo', '*'),
(15, '15', 'personal.updatestudentcontact', '*'),
(16, '16', 'personal.updatestatus', '*'),
(17, '17', 'personal.updatehesuan', '*'),
(18, '18', 'modify', '*'),
(19, '19', 'personal.updateteachercontact', '*'),
(20, '20', 'index', '*');

-- --------------------------------------------------------

--
-- 表的结构 `rbac_role`
--

CREATE TABLE `rbac_role` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统角色';

--
-- 转存表中的数据 `rbac_role`
--

INSERT INTO `rbac_role` (`id`, `name`) VALUES
(1, '超级管理员'),
(2, '教师管理员'),
(3, '学生'),
(4, '学生管理员');

-- --------------------------------------------------------

--
-- 表的结构 `rbac_role_has_permissions`
--

CREATE TABLE `rbac_role_has_permissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL COMMENT '角色ID',
  `permission_id` int(11) NOT NULL COMMENT '权限ID',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='角色关联权限';

--
-- 转存表中的数据 `rbac_role_has_permissions`
--

INSERT INTO `rbac_role_has_permissions` (`id`, `role_id`, `permission_id`, `created_at`) VALUES
(2, 2, 2, '2022-04-05 08:08:55'),
(3, 2, 8, '2022-04-05 08:08:55'),
(4, 2, 9, '2022-04-05 08:08:55'),
(5, 2, 10, '2022-04-05 08:08:55'),
(6, 2, 12, '2022-04-05 08:08:55'),
(7, 2, 14, '2022-04-05 08:08:55'),
(8, 2, 19, '2022-04-05 08:08:55'),
(11, 2, 18, '2022-04-05 08:08:55'),
(12, 3, 2, '2022-04-05 08:08:55'),
(13, 3, 12, '2022-04-05 08:08:55'),
(14, 3, 13, '2022-04-05 08:08:55'),
(15, 3, 15, '2022-04-05 08:08:55'),
(16, 3, 16, '2022-04-05 08:08:55'),
(17, 3, 17, '2022-04-05 08:08:55'),
(18, 3, 18, '2022-04-05 08:08:55'),
(19, 4, 2, '2022-04-05 08:08:55'),
(20, 4, 8, '2022-04-05 08:08:55'),
(21, 4, 9, '2022-04-05 08:08:55'),
(22, 4, 10, '2022-04-05 08:08:55'),
(23, 4, 11, '2022-04-05 08:08:55'),
(24, 4, 12, '2022-04-05 08:08:55'),
(25, 4, 13, '2022-04-05 08:08:55'),
(26, 4, 15, '2022-04-05 08:08:55'),
(27, 4, 16, '2022-04-05 08:08:55'),
(28, 4, 17, '2022-04-05 08:08:55'),
(29, 4, 18, '2022-04-05 08:08:55'),
(30, 1, 2, '2022-04-05 08:08:55'),
(31, 1, 3, '2022-04-05 08:08:55'),
(32, 1, 4, '2022-04-05 08:08:55'),
(33, 1, 5, '2022-04-05 08:08:55'),
(34, 1, 6, '2022-04-05 08:08:55'),
(35, 1, 7, '2022-04-05 08:08:55'),
(36, 1, 8, '2022-04-05 08:08:55'),
(37, 1, 9, '2022-04-05 08:08:55'),
(38, 1, 10, '2022-04-05 08:08:55'),
(39, 1, 12, '2022-04-05 08:08:55'),
(40, 1, 14, '2022-04-05 08:08:55'),
(41, 1, 19, '2022-04-05 08:08:55'),
(42, 1, 18, '2022-04-05 08:08:55'),
(43, 1, 20, '2022-04-05 08:08:55'),
(44, 2, 20, '2022-04-05 08:08:55'),
(45, 3, 20, '2022-04-05 08:08:55'),
(46, 4, 20, '2022-04-05 08:08:55');

-- --------------------------------------------------------

--
-- 表的结构 `rbac_user`
--

CREATE TABLE `rbac_user` (
  `user_id` int(11) NOT NULL COMMENT 'user_info表id',
  `password` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT '密码',
  `modify` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否是初始密码（1:是，0:不是）',
  `salt` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '12332112##1%' COMMENT '密码MD5盐'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='仅该表中的用户可登录系统';

--
-- 转存表中的数据 `rbac_user`
--

INSERT INTO `rbac_user` (`user_id`, `password`, `modify`, `salt`) VALUES
(1, '173396b70f4aad597b4cfd9130f33f7b', 1, '12332112##1%'),
(2, '173396b70f4aad597b4cfd9130f33f7b', 1, '12332112##1%'),
(3, '173396b70f4aad597b4cfd9130f33f7b', 1, '12332112##1%'),
(4, '173396b70f4aad597b4cfd9130f33f7b', 1, '12332112##1%'),
(5, '72b6ca080b5781cdda501dad2743f45a', 1, 'eV4xKR33'),
(6, '661395e823fdb0f1fb52889d43ecd487', 1, 'qfu45bw'),
(7, '9c38a0f8205f3fff25ecffd9b9f4ecdf', 1, '5j9dQvxJ'),
(8, 'b634badcea2ed89d9452342020a706a5', 1, 'Smq6y3'),
(9, '0b0459ca993ff5709dfa1e67dccf95a2', 1, 'JD4rNxOw'),
(10, '50084f4676e1f260c423765596d79a4e', 1, 'CUcfzh'),
(11, 'ca01afac0963e9e8e3f0c62ed746ec3d', 1, 'xnV1bwI'),
(12, '01f343856baf150dc788a570c16b6f6e', 1, 'IakPjf'),
(13, 'd68ba9cdaa315c7187fc82bfacd8735b', 1, 'EPuqsu'),
(14, '9a6ac487bcec3db67ad1e5f9bb7157cf', 1, 'sPaR6yU'),
(15, 'fce4d6585338a68cda29f8c7a07c8509', 1, 'jK4yq4'),
(16, 'f12c77f86c1dd7607b1b1d11a1e80e66', 1, 'Snut4rP'),
(17, '35ffb5541cde06d493185a7d3e93de9e', 1, 'wGYzXJ'),
(18, 'b517f0fbfaf29ee438067c7fde3fc0b7', 1, 'JzKURRP9'),
(19, 'be2a9b2a775582f2b4a52767b44e7ea0', 1, 'T5d11I3h'),
(20, '1aa629d9693bbcd10d467fd6821cd0e9', 1, '.t4gjIx'),
(21, 'b8a552f8eaeb7e088cd94a3a70c36504', 1, 'soYtPrG'),
(22, '2e270ff02a77e2695d2269cc7a7f462c', 1, 'FT2gxZ'),
(23, '75021d036db846892dc45ea33332aadc', 1, 'y36TW2'),
(24, '64f600a8dc80c46a9e1bd2a2d03d6c64', 1, '1MZgTZ'),
(25, 'e8998237f40962d16daee91f7164b176', 1, 'M4Kq0zxv'),
(26, '4304fb665156d6117d13e731c80a7f43', 1, 'i49xVAAh'),
(27, 'bd216143f78b47ae78f826e01ea6d805', 1, 'HTprqb'),
(28, '42fbc3f66b42dd9cd547605782d6e766', 1, 'B2A7CK'),
(29, 'e2404cf37fa03b5ac3c16fbb8bf0eec2', 1, 'h0vk2HG1'),
(30, 'af435b002e43203a5b1055ad02df2d5a', 1, 'mjrPH/nn'),
(31, '0669812ff4b39a4d425238a00e9d7268', 1, 'XY9nBeE'),
(32, '80349dd844d2893eb1fba8a21776feda', 1, '2aLnHTQ'),
(33, '2b9b1f57356c8b3b015bd1576cdfb238', 1, 'dWhcL1k'),
(34, '9e370d79fde0e350d08d4226de3a033a', 1, 'eHWTsnW'),
(35, 'd90d23e2c3721800405368d90c0e6b11', 1, 'WC5tEd.'),
(36, 'd373f33774c63cd4fd9b1acd61527fec', 1, 'Aa8zcT/V'),
(37, '2ff9a7c87e61aa53f43d0ff8e2c69e11', 1, '3Y4sns1'),
(38, 'ee41e5fe62043f8b754bedc445c5d7a2', 1, 'h47gkMb'),
(39, '67605640e71c91bf5c5b7333256885f2', 1, '5ZZvwPKx'),
(40, 'c1f4ce14c8732bb9f06f8ce4f0e8f068', 1, '0ZEQLbQs'),
(41, '767da1f9496f5177b11acdb07b289739', 1, 'RAOtabZ'),
(42, '2d13feaeacec22b2d49b324822ef43bb', 1, '0jPtuRG'),
(43, '7118b36383b3a3afcafb5cc156340aab', 1, '0zN3TsI'),
(44, '4f456053e719220b28cf33f759a09805', 1, 'KT9EAC2'),
(45, 'b95d95728c1c7fbd090a4df4ec5983a4', 1, '4bU2dN'),
(46, '18357d323d43c138a4d728084c52ba50', 1, 'iA/FLqR5'),
(47, 'ea81a2a4de55e784c794e50d27635bf2', 1, 'fLVdhQ'),
(48, '8c790b8222a1b488b8d33868f64b2237', 1, 'ZUF2BP3'),
(49, '9d96dcf27c81a511d531aca1e4665329', 1, 'WvWMHx9s'),
(50, '7c9b1ea84662fcf5ecec05cbb5685601', 1, '8Kx7Ys'),
(51, '8d1244c3986572b652dc0a02750223d5', 1, '7pAfbNW'),
(52, '7a426ecba430d7cefe73a66631c62464', 1, 'rFugSP'),
(53, '70171242d8470d6fd8f3a2b32d7dc2c6', 1, 'DiwrcnJa'),
(54, 'cd445ad18c2abb9a8e6247754f1913dc', 1, 'FhPkYS'),
(55, '1662585dac446ac1c1a6c76ddbfb0f83', 1, 'A3wLGiI');

-- --------------------------------------------------------

--
-- 表的结构 `rbac_user_has_roles`
--

CREATE TABLE `rbac_user_has_roles` (
  `user_id` int(11) NOT NULL COMMENT 'user_info表id',
  `role_id` int(11) NOT NULL COMMENT 'rbac_role表id',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户权限关联表';

--
-- 转存表中的数据 `rbac_user_has_roles`
--

INSERT INTO `rbac_user_has_roles` (`user_id`, `role_id`, `created_at`) VALUES
(1, 1, '2022-04-05 07:27:52'),
(2, 2, '2022-04-05 07:28:03'),
(3, 4, '2022-04-05 07:28:09'),
(4, 3, '2022-05-26 10:17:26'),
(5, 3, '2022-06-09 10:36:17'),
(6, 3, '2022-06-09 10:36:24'),
(7, 2, '2022-06-21 15:54:46'),
(8, 2, '2022-06-21 15:54:46'),
(9, 2, '2022-06-21 15:54:46'),
(10, 2, '2022-06-21 15:54:46'),
(11, 2, '2022-06-21 15:54:46'),
(12, 2, '2022-06-21 15:54:46'),
(13, 2, '2022-06-21 15:54:46'),
(14, 2, '2022-06-21 15:54:46'),
(15, 2, '2022-06-21 15:54:46'),
(16, 2, '2022-06-21 15:54:46'),
(17, 3, '2022-06-21 15:54:46'),
(18, 3, '2022-06-21 15:54:46'),
(19, 3, '2022-06-21 15:54:46'),
(20, 3, '2022-06-21 15:54:46'),
(21, 3, '2022-06-21 15:54:46'),
(22, 3, '2022-06-21 15:54:46'),
(23, 3, '2022-06-21 15:54:46'),
(24, 3, '2022-06-21 15:54:46'),
(25, 3, '2022-06-21 15:54:46'),
(26, 3, '2022-06-21 15:54:46'),
(27, 3, '2022-06-21 15:54:46'),
(28, 3, '2022-06-21 15:54:46'),
(29, 3, '2022-06-21 15:54:46'),
(30, 3, '2022-06-21 15:54:46'),
(31, 3, '2022-06-21 15:54:46'),
(32, 3, '2022-06-21 15:54:46'),
(33, 3, '2022-06-21 15:54:46'),
(34, 3, '2022-06-21 15:54:46'),
(35, 3, '2022-06-21 15:54:46'),
(36, 3, '2022-06-21 15:54:46'),
(37, 3, '2022-06-21 15:54:46'),
(38, 3, '2022-06-21 15:54:46'),
(39, 3, '2022-06-21 15:54:46'),
(40, 3, '2022-06-21 15:54:46'),
(41, 3, '2022-06-21 15:54:46'),
(42, 3, '2022-06-21 15:54:46'),
(43, 3, '2022-06-21 15:54:46'),
(44, 3, '2022-06-21 15:54:46'),
(45, 3, '2022-06-21 15:54:46'),
(46, 3, '2022-06-21 15:54:46'),
(47, 3, '2022-06-21 15:54:46'),
(48, 3, '2022-06-21 15:54:46'),
(49, 3, '2022-06-21 15:54:46'),
(50, 3, '2022-06-21 15:54:46'),
(51, 3, '2022-06-21 15:54:46'),
(52, 3, '2022-06-21 15:54:46'),
(53, 3, '2022-06-21 15:54:46'),
(54, 3, '2022-06-21 15:54:46'),
(55, 3, '2022-06-21 15:54:46');

-- --------------------------------------------------------

--
-- 表的结构 `status`
--

CREATE TABLE `status` (
  `status_id` int(10) NOT NULL COMMENT '状态编号',
  `status_name` varchar(100) NOT NULL COMMENT '状态名称'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `status`
--

INSERT INTO `status` (`status_id`, `status_name`) VALUES
(1, '在校（大兴）'),
(2, '在校（非大兴校区）'),
(3, '居家'),
(4, '校外实习');

-- --------------------------------------------------------

--
-- 表的结构 `user_contact`
--

CREATE TABLE `user_contact` (
  `id` int(11) NOT NULL COMMENT '校内id',
  `phone` varchar(11) NOT NULL COMMENT '联系电话',
  `email` varchar(100) NOT NULL COMMENT '邮箱',
  `status` varchar(100) NOT NULL DEFAULT '在校' COMMENT '当前状态',
  `contact_pid` varchar(100) NOT NULL COMMENT '联络人的user_info表id',
  `address` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT '当前住址',
  `professor` varchar(100) NOT NULL COMMENT '导师姓名',
  `professor_phone` varchar(100) NOT NULL COMMENT '导师联系电话',
  `parent` varchar(100) NOT NULL COMMENT '紧急联系人',
  `parent_phone` varchar(100) NOT NULL COMMENT '紧急联系人电话'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='所有系统用户信息';

--
-- 转存表中的数据 `user_contact`
--

INSERT INTO `user_contact` (`id`, `phone`, `email`, `status`, `contact_pid`, `address`, `professor`, `professor_phone`, `parent`, `parent_phone`) VALUES
(1, '18511115555', '5555@qq.com', '1', '', '', '', '', '', ''),
(2, '18566669999', '123876@www.com', '1', '', '', '', '', '', ''),
(3, '18511110000', '123@qq.com', '2', '3', '北大软微16号楼1234', '张文', '18511110000', '丽丽', '18511112222'),
(4, '18511115555', '5555@stu.pku.edu.cn', '3', '3', '北大软微5号楼5555', '张一', '18511112222', '张五', '18511115555'),
(5, '18511116666', '123we@126.com', '2', '3', '北大软微13号楼1111', '张文', '18511110000', '张三', '18511110000'),
(6, '18511116666', '123we@126.com', '2', '3', '北大软微13号楼1111', '张文', '18511110000', '张三', '18511110000'),
(7, '', '', '1', '', '', '', '', '', ''),
(8, '', '', '1', '', '', '', '', '', ''),
(9, '', '', '1', '', '', '', '', '', ''),
(10, '', '', '1', '', '', '', '', '', ''),
(11, '', '', '1', '', '', '', '', '', ''),
(12, '', '', '1', '', '', '', '', '', ''),
(13, '', '', '1', '', '', '', '', '', ''),
(14, '', '', '1', '', '', '', '', '', ''),
(15, '', '', '1', '', '', '', '', '', ''),
(16, '', '', '1', '', '', '', '', '', ''),
(17, '', '', '1', '', '', '', '', '', ''),
(18, '', '', '1', '', '', '', '', '', ''),
(19, '', '', '1', '3', '', '', '', '', ''),
(20, '', '', '1', '', '', '', '', '', ''),
(21, '', '', '1', '', '', '', '', '', ''),
(22, '', '', '1', '', '', '', '', '', ''),
(23, '', '', '3', '', '', '', '', '', ''),
(24, '', '', '1', '', '', '', '', '', ''),
(25, '', '', '1', '', '', '', '', '', ''),
(26, '', '', '3', '', '', '', '', '', ''),
(27, '', '', '1', '', '', '', '', '', ''),
(28, '', '', '1', '3', '', '', '', '', ''),
(29, '', '', '1', '', '', '', '', '', ''),
(30, '18522222222', '222@qq.com', '1', '', '软微2号楼', '王二', '18522222222', '王二', '18522222222'),
(31, '', '', '1', '', '', '', '', '', ''),
(32, '', '', '1', '', '', '', '', '', ''),
(33, '', '', '1', '', '', '', '', '', ''),
(34, '', '', '1', '', '', '', '', '', ''),
(35, '', '', '1', '', '', '', '', '', ''),
(36, '', '', '1', '', '', '', '', '', ''),
(37, '', '', '1', '', '', '', '', '', ''),
(38, '', '', '1', '', '', '', '', '', ''),
(39, '', '', '1', '', '', '', '', '', ''),
(40, '', '', '1', '', '', '', '', '', ''),
(41, '', '', '1', '', '', '', '', '', ''),
(42, '', '', '1', '', '', '', '', '', ''),
(43, '', '', '1', '', '', '', '', '', ''),
(44, '', '', '1', '', '', '', '', '', ''),
(45, '', '', '1', '', '', '', '', '', ''),
(46, '', '', '1', '', '', '', '', '', ''),
(47, '', '', '1', '', '', '', '', '', ''),
(48, '', '', '1', '', '', '', '', '', ''),
(49, '', '', '1', '', '', '', '', '', ''),
(50, '', '', '1', '', '', '', '', '', ''),
(51, '', '', '1', '', '', '', '', '', ''),
(52, '', '', '1', '', '', '', '', '', ''),
(53, '', '', '1', '', '', '', '', '', ''),
(54, '', '', '1', '', '', '', '', '', ''),
(55, '', '', '1', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- 表的结构 `user_info`
--

CREATE TABLE `user_info` (
  `id` int(100) NOT NULL COMMENT '校内id',
  `no` varchar(30) CHARACTER SET utf8 NOT NULL COMMENT '工号/学号',
  `name` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT '姓名',
  `pinyin` varchar(20) CHARACTER SET utf8 NOT NULL COMMENT '姓名拼音',
  `sex` varchar(10) NOT NULL DEFAULT '男' COMMENT '性别',
  `birth` date NOT NULL COMMENT '出生日期',
  `major` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT '所属院系',
  `department` varchar(100) NOT NULL COMMENT '所属学苑',
  `vaccines` int(1) NOT NULL DEFAULT '0' COMMENT '疫苗接种剂量（1、2、3针）',
  `last_hesuan` date NOT NULL COMMENT '最近一次核酸日期',
  `del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:未删除，1:已删除'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='所有系统用户信息';

--
-- 转存表中的数据 `user_info`
--

INSERT INTO `user_info` (`id`, `no`, `name`, `pinyin`, `sex`, `birth`, `major`, `department`, `vaccines`, `last_hesuan`, `del`) VALUES
(1, '0001', '学工老师', 'xgls', '男', '2022-04-12', '1', '1', 1, '0000-00-00', 0),
(2, '0002', '张三', 'zs', '男', '2022-04-07', '2', '1', 0, '0000-00-00', 0),
(3, '2100000001', '李四', 'ls', '男', '2022-04-02', '2', '2', 0, '0000-00-00', 0),
(4, '2100000002', '王一', 'wy', '男', '2022-05-26', '3', '2', 0, '0000-00-00', 0),
(5, '2100000003', '王说', 'ws', '男', '2022-04-02', '3', '2', 0, '0000-00-00', 0),
(6, '2100000004', '李流', 'll', '女', '2022-04-02', '4', '2', 0, '0000-00-00', 0),
(7, '1000', '卫五', '', '女', '1998-03-12', '4', '1', 0, '0000-00-00', 0),
(8, '1001', '蒋日', '', '女', '1997-03-17', '8', '1', 0, '0000-00-00', 0),
(9, '1002', '褚五', '', '男', '1996-11-12', '7', '1', 0, '0000-00-00', 0),
(10, '1003', '赵三', '', '女', '2000-02-19', '10', '1', 0, '0000-00-00', 0),
(11, '1004', '钱四', '', '女', '1995-02-06', '9', '1', 0, '0000-00-00', 0),
(12, '1005', '卫三', '', '男', '1998-01-26', '3', '1', 0, '0000-00-00', 0),
(13, '1006', '钱五', '', '女', '1998-09-22', '4', '1', 0, '0000-00-00', 0),
(14, '1007', '赵三', '', '女', '1996-04-14', '5', '1', 0, '0000-00-00', 0),
(15, '1008', '李一', '', '男', '1995-01-16', '6', '1', 0, '0000-00-00', 0),
(16, '1009', '陈五', '', '男', '1998-11-24', '2', '1', 0, '0000-00-00', 0),
(17, '2100000010', '蒋日', 'jr', '男', '1996-10-10', '4', '3', 0, '0000-00-00', 0),
(18, '2100000011', '郑三', '', '女', '2000-12-18', '8', '3', 0, '0000-00-00', 0),
(19, '2100000012', '孙四', '', '女', '2000-09-02', '7', '2', 0, '0000-00-00', 0),
(20, '2100000013', '卫三', '', '女', '1995-02-16', '10', '3', 0, '0000-00-00', 0),
(21, '2100000014', '冯一', '', '男', '1997-05-11', '8', '3', 0, '0000-00-00', 0),
(22, '2100000015', '孙二', '', '女', '1998-08-18', '10', '3', 0, '0000-00-00', 0),
(23, '2100000016', '陈一', '', '男', '1995-06-22', '9', '3', 0, '0000-00-00', 0),
(24, '2100000017', '陈二', '', '女', '1997-08-10', '5', '2', 0, '0000-00-00', 0),
(25, '2100000018', '卫二', '', '女', '1996-12-15', '3', '3', 0, '0000-00-00', 0),
(26, '2100000019', '孙五', '', '女', '1995-09-06', '3', '2', 0, '0000-00-00', 0),
(27, '2100000020', '冯五', '', '男', '1996-11-29', '10', '2', 0, '0000-00-00', 0),
(28, '2100000021', '蒋一', '', '女', '1996-02-28', '4', '2', 0, '0000-00-00', 0),
(29, '2100000022', '卫六', '', '男', '1997-02-03', '5', '3', 0, '0000-00-00', 0),
(30, '2200000023', '褚二', '', '女', '1999-06-22', '5', '4', 2, '2022-02-02', 0),
(31, '2100000024', '陈五', '', '男', '1996-07-03', '6', '2', 0, '0000-00-00', 0),
(32, '2100000025', '冯二', '', '女', '1997-09-02', '6', '2', 0, '0000-00-00', 0),
(33, '2100000026', '郑日', '', '男', '2000-01-15', '2', '4', 0, '0000-00-00', 0),
(34, '2100000027', '钱一', '', '男', '1997-09-23', '3', '3', 0, '0000-00-00', 0),
(35, '2100000028', '陈六', '', '女', '1996-09-21', '3', '3', 0, '0000-00-00', 0),
(36, '2100000029', '郑四', '', '女', '1998-07-31', '5', '2', 0, '0000-00-00', 0),
(37, '2100000030', '赵六', '', '女', '1999-02-22', '5', '2', 0, '0000-00-00', 0),
(38, '2100000031', '陈六', '', '女', '1999-02-12', '4', '3', 0, '0000-00-00', 0),
(39, '2100000032', '李一', '', '男', '1999-01-21', '8', '3', 0, '0000-00-00', 0),
(40, '2100000033', '陈六', '', '男', '1998-03-06', '3', '2', 0, '0000-00-00', 0),
(41, '2100000034', '郑三', '', '女', '2000-08-02', '9', '3', 0, '0000-00-00', 0),
(42, '2100000035', '褚日', '', '男', '1996-09-09', '6', '3', 0, '0000-00-00', 0),
(43, '2100000036', '赵一', '', '男', '1995-09-10', '7', '2', 0, '0000-00-00', 0),
(44, '2100000037', '钱五', '', '女', '2000-06-06', '4', '3', 0, '0000-00-00', 0),
(45, '2100000038', '褚二', '', '男', '1999-02-01', '5', '3', 0, '0000-00-00', 0),
(46, '2100000039', '郑一', '', '男', '2000-09-20', '6', '3', 0, '0000-00-00', 0),
(47, '2100000040', '周五', '', '男', '1999-08-16', '5', '2', 0, '0000-00-00', 0),
(48, '2100000041', '褚三', '', '女', '1998-01-17', '9', '3', 0, '0000-00-00', 0),
(49, '2100000042', '褚日', '', '女', '1996-08-23', '6', '2', 0, '0000-00-00', 0),
(50, '2100000043', '王五', '', '女', '1995-10-24', '8', '2', 0, '0000-00-00', 0),
(51, '2100000044', '郑四', '', '男', '1998-04-03', '10', '4', 0, '0000-00-00', 0),
(52, '2100000045', '周四', '', '女', '1999-10-02', '9', '2', 0, '0000-00-00', 0),
(53, '2100000046', '赵二', '', '男', '1995-12-13', '2', '2', 0, '0000-00-00', 0),
(54, '2100000047', '王三', '', '女', '1996-10-21', '4', '2', 0, '0000-00-00', 0),
(55, '2100000048', '赵四', '', '女', '2000-06-01', '10', '2', 0, '0000-00-00', 0);

-- --------------------------------------------------------

--
-- 表的结构 `user_manage_department`
--

CREATE TABLE `user_manage_department` (
  `id` int(100) NOT NULL COMMENT '编号',
  `user_id` int(100) NOT NULL COMMENT '管理员的user_info表id',
  `department` int(100) NOT NULL COMMENT '管理的学苑id',
  `del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:未删除，1:已删除'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `user_manage_department`
--

INSERT INTO `user_manage_department` (`id`, `user_id`, `department`, `del`) VALUES
(1, 2, 2, 0),
(2, 2, 3, 0),
(3, 3, 2, 0),
(4, 2, 0, 1),
(5, 2, 0, 1),
(6, 2, 0, 1),
(7, 2, 4, 0),
(8, 2, 10, 1),
(9, 3, 3, 1),
(10, 2, 6, 1),
(11, 16, 2, 0),
(12, 8, 2, 0),
(13, 9, 3, 0),
(14, 7, 2, 0),
(15, 11, 4, 0),
(16, 10, 9, 0),
(17, 7, 4, 0),
(18, 8, 3, 0);

-- --------------------------------------------------------

--
-- 表的结构 `user_vaccines`
--

CREATE TABLE `user_vaccines` (
  `id` int(100) NOT NULL COMMENT '校内id',
  `date_1` varchar(100) NOT NULL COMMENT '第一针接种时间',
  `place_1` varchar(100) NOT NULL COMMENT '第一针接种地点',
  `date_2` varchar(100) NOT NULL COMMENT '第二针接种时间',
  `place_2` varchar(100) NOT NULL COMMENT '第二针接种地点',
  `date_3` varchar(100) NOT NULL COMMENT '第三针接种时间',
  `place_3` varchar(100) NOT NULL COMMENT '第三针接种地点'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `user_vaccines`
--

INSERT INTO `user_vaccines` (`id`, `date_1`, `place_1`, `date_2`, `place_2`, `date_3`, `place_3`) VALUES
(1, '2022-06-10', '京外', '', '', '', ''),
(2, '', '', '', '', '', ''),
(3, '', '', '', '', '', ''),
(4, '2022-06-10', '京外', '', '', '', ''),
(5, '', '', '', '', '', ''),
(6, '', '', '', '', '', ''),
(7, '', '', '', '', '', ''),
(8, '', '', '', '', '', ''),
(9, '', '', '', '', '', ''),
(10, '', '', '', '', '', ''),
(11, '', '', '', '', '', ''),
(12, '', '', '', '', '', ''),
(13, '', '', '', '', '', ''),
(14, '', '', '', '', '', ''),
(15, '', '', '', '', '', ''),
(16, '', '', '', '', '', ''),
(17, '', '', '', '', '', ''),
(18, '', '', '', '', '', ''),
(19, '', '', '', '', '', ''),
(20, '', '', '', '', '', ''),
(21, '', '', '', '', '', ''),
(22, '', '', '', '', '', ''),
(23, '', '', '', '', '', ''),
(24, '', '', '', '', '', ''),
(25, '', '', '', '', '', ''),
(26, '', '', '', '', '', ''),
(27, '', '', '', '', '', ''),
(28, '', '', '', '', '', ''),
(29, '', '', '', '', '', ''),
(30, '', '', '', '', '', ''),
(31, '', '', '', '', '', ''),
(32, '', '', '', '', '', ''),
(33, '', '', '', '', '', ''),
(34, '', '', '', '', '', ''),
(35, '', '', '', '', '', ''),
(36, '', '', '', '', '', ''),
(37, '', '', '', '', '', ''),
(38, '', '', '', '', '', ''),
(39, '', '', '', '', '', ''),
(40, '', '', '', '', '', ''),
(41, '', '', '', '', '', ''),
(42, '', '', '', '', '', ''),
(43, '', '', '', '', '', ''),
(44, '', '', '', '', '', ''),
(45, '', '', '', '', '', ''),
(46, '', '', '', '', '', ''),
(47, '', '', '', '', '', ''),
(48, '', '', '', '', '', ''),
(49, '', '', '', '', '', ''),
(50, '', '', '', '', '', ''),
(51, '', '', '', '', '', ''),
(52, '', '', '', '', '', ''),
(53, '', '', '', '', '', ''),
(54, '', '', '', '', '', ''),
(55, '', '', '', '', '', '');

--
-- 转储表的索引
--

--
-- 表的索引 `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `hesuan`
--
ALTER TABLE `hesuan`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`log_id`);

--
-- 表的索引 `major`
--
ALTER TABLE `major`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `rbac_menu`
--
ALTER TABLE `rbac_menu`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `rbac_permission`
--
ALTER TABLE `rbac_permission`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `rbac_role`
--
ALTER TABLE `rbac_role`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `rbac_role_has_permissions`
--
ALTER TABLE `rbac_role_has_permissions`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `rbac_user`
--
ALTER TABLE `rbac_user`
  ADD PRIMARY KEY (`user_id`);

--
-- 表的索引 `rbac_user_has_roles`
--
ALTER TABLE `rbac_user_has_roles`
  ADD PRIMARY KEY (`user_id`);

--
-- 表的索引 `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`status_id`);

--
-- 表的索引 `user_contact`
--
ALTER TABLE `user_contact`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `user_info`
--
ALTER TABLE `user_info`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `user_manage_department`
--
ALTER TABLE `user_manage_department`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `user_vaccines`
--
ALTER TABLE `user_vaccines`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `department`
--
ALTER TABLE `department`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- 使用表AUTO_INCREMENT `hesuan`
--
ALTER TABLE `hesuan`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT '核酸数据id', AUTO_INCREMENT=5;

--
-- 使用表AUTO_INCREMENT `log`
--
ALTER TABLE `log`
  MODIFY `log_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '日志编号', AUTO_INCREMENT=15;

--
-- 使用表AUTO_INCREMENT `major`
--
ALTER TABLE `major`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- 使用表AUTO_INCREMENT `rbac_menu`
--
ALTER TABLE `rbac_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- 使用表AUTO_INCREMENT `rbac_permission`
--
ALTER TABLE `rbac_permission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- 使用表AUTO_INCREMENT `rbac_role`
--
ALTER TABLE `rbac_role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 使用表AUTO_INCREMENT `rbac_role_has_permissions`
--
ALTER TABLE `rbac_role_has_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- 使用表AUTO_INCREMENT `rbac_user`
--
ALTER TABLE `rbac_user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'user_info表id', AUTO_INCREMENT=56;

--
-- 使用表AUTO_INCREMENT `rbac_user_has_roles`
--
ALTER TABLE `rbac_user_has_roles`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'user_info表id', AUTO_INCREMENT=56;

--
-- 使用表AUTO_INCREMENT `status`
--
ALTER TABLE `status`
  MODIFY `status_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '状态编号', AUTO_INCREMENT=5;

--
-- 使用表AUTO_INCREMENT `user_contact`
--
ALTER TABLE `user_contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '校内id', AUTO_INCREMENT=56;

--
-- 使用表AUTO_INCREMENT `user_info`
--
ALTER TABLE `user_info`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT COMMENT '校内id', AUTO_INCREMENT=56;

--
-- 使用表AUTO_INCREMENT `user_manage_department`
--
ALTER TABLE `user_manage_department`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT COMMENT '编号', AUTO_INCREMENT=19;

--
-- 使用表AUTO_INCREMENT `user_vaccines`
--
ALTER TABLE `user_vaccines`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT COMMENT '校内id', AUTO_INCREMENT=56;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
