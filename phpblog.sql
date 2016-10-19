-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               5.5.50-log - MySQL Community Server (GPL)
-- ОС Сервера:                   Win64
-- HeidiSQL Версия:              9.3.0.4984
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Дамп структуры для таблица phpblog.blogs
CREATE TABLE IF NOT EXISTS `blogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parentid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `create` datetime NOT NULL,
  `text` varchar(16376) NOT NULL DEFAULT '0',
  `sort` int(11) DEFAULT NULL,
  KEY `Индекс 1` (`id`),
  KEY `FK__fos_user` (`userid`),
  CONSTRAINT `FK__fos_user` FOREIGN KEY (`userid`) REFERENCES `fos_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4;

-- Дамп данных таблицы phpblog.blogs: ~34 rows (приблизительно)
/*!40000 ALTER TABLE `blogs` DISABLE KEYS */;
INSERT INTO `blogs` (`id`, `parentid`, `userid`, `create`, `text`, `sort`) VALUES
	(15, 0, 1, '2016-10-18 02:14:37', 'nasgafghj', 15),
	(16, 15, 1, '2016-10-18 02:30:54', 'nnnnnn', 15),
	(17, 15, 1, '2016-10-18 02:31:17', 'ttttttt', 15),
	(18, 17, 1, '2016-10-18 03:15:00', 'упырь!', 15),
	(19, 18, 2, '2016-10-18 03:17:23', 'сам упырь', 15),
	(20, 0, 2, '2016-10-18 10:48:36', 'главный пост', 20),
	(21, 17, 2, '2016-10-18 11:14:01', 'да нет', 15),
	(22, 19, 1, '2016-10-19 02:18:08', 'реплика на реплику', 15),
	(23, 21, 1, '2016-10-19 02:18:39', 'нет да', 15),
	(24, 17, 1, '2016-10-19 02:25:06', 'а ты сам дурак', 15),
	(25, 22, 2, '2016-10-19 02:29:05', 'реплика на реплику а реплику', 15),
	(26, 22, 1, '2016-10-19 03:20:27', '+++++', 15),
	(27, 19, 1, '2016-10-19 03:20:56', '-------------', 15),
	(28, 0, 1, '2016-10-19 03:48:07', 'главный пост2', 28),
	(29, 0, 1, '2016-10-19 03:55:46', 'главный пост3', 29),
	(30, 0, 1, '2016-10-19 04:01:58', 'главный пост4', 30),
	(31, 0, 1, '2016-10-19 04:03:40', 'главный пост5', 31),
	(32, 31, 1, '2016-10-19 04:05:18', 'гвд', 31),
	(33, 32, 1, '2016-10-19 04:08:47', 'не гвд', 31),
	(34, 30, 1, '2016-10-19 04:17:30', 'udl', 30),
	(35, 29, 1, '2016-10-19 04:18:45', 'тест', 29),
	(36, 29, 1, '2016-10-19 04:19:01', 'тес т3', 29),
	(37, 29, 1, '2016-10-19 04:19:31', 'тест4', 29),
	(38, 30, 1, '2016-10-19 04:19:58', 'udl1', 30),
	(39, 34, 1, '2016-10-19 04:24:45', 'udl2', 30),
	(40, 34, 1, '2016-10-19 04:24:55', 'udl4', 30),
	(41, 38, 1, '2016-10-19 04:25:12', 'udl5', 30),
	(42, 34, 1, '2016-10-19 04:25:34', 'udl6', 30),
	(43, 28, 1, '2016-10-19 04:26:05', '1', 28),
	(44, 43, 1, '2016-10-19 04:26:13', '2', 28),
	(45, 43, 1, '2016-10-19 04:26:21', '3', 28),
	(46, 44, 1, '2016-10-19 04:26:30', '4', 28),
	(47, 46, 1, '2016-10-19 04:28:19', 'не гвд', 28),
	(48, 33, 2, '2016-10-19 04:34:48', 'ну и ну', 31);
/*!40000 ALTER TABLE `blogs` ENABLE KEYS */;


-- Дамп структуры для представление phpblog.blogssort
-- Создание временной таблицы для обработки ошибок зависимостей представлений
CREATE TABLE `blogssort` (
	`id` INT(11) NOT NULL,
	`parentid` INT(11) NOT NULL,
	`create` DATETIME NOT NULL,
	`text` VARCHAR(16376) NOT NULL COLLATE 'utf8mb4_general_ci',
	`userid` INT(11) NOT NULL,
	`user` VARCHAR(255) NULL COLLATE 'utf8mb4_general_ci',
	`sort` INT(11) NULL,
	`childid` INT(11) NULL,
	`childparentid` INT(11) NULL,
	`childcreate` DATETIME NULL,
	`childtext` VARCHAR(16376) NULL COLLATE 'utf8mb4_general_ci',
	`childuserid` INT(11) NULL,
	`childuser` VARCHAR(255) NULL COLLATE 'utf8mb4_general_ci',
	`childsort` INT(11) NULL
) ENGINE=MyISAM;


-- Дамп структуры для таблица phpblog.fos_user
CREATE TABLE IF NOT EXISTS `fos_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `u` varchar(255) NOT NULL DEFAULT '0',
  `p` varchar(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='пользователи системы';

-- Дамп данных таблицы phpblog.fos_user: ~2 rows (приблизительно)
/*!40000 ALTER TABLE `fos_user` DISABLE KEYS */;
INSERT INTO `fos_user` (`id`, `u`, `p`) VALUES
	(1, 'user', 'postman'),
	(2, 'denis', 'udl');
/*!40000 ALTER TABLE `fos_user` ENABLE KEYS */;


-- Дамп структуры для процедура phpblog.GET_USER_LOGIN_ATTEMPTS
DELIMITER //
CREATE DEFINER=`phpblog_admin`@`localhost` PROCEDURE `GET_USER_LOGIN_ATTEMPTS`(IN `MY_SESSION_ID` VARCHAR(50), IN `MY_PASSWORD` VARCHAR(32), OUT `RETVAL` INT)
BEGIN
IF (SELECT COUNT(TABLE_NAME) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME LIKE '%user_session_info%') IS NOT NULL THEN 
	IF (SELECT ID FROM user_session_info WHERE `login`=MY_SESSION_ID AND `password`=MY_PASSWORD) IS NOT NULL THEN
		SELECT Info INTO RETVAL FROM user_session_info WHERE login=MY_SESSION_ID AND password=MY_PASSWORD;
	ELSE
		SET RETVAL=0;
	END IF;
ELSE
	SET RETVAL=0;
END IF;
END//
DELIMITER ;


-- Дамп структуры для таблица phpblog.session
CREATE TABLE IF NOT EXISTS `session` (
  `session_id` varchar(32) NOT NULL,
  `session_start_time` datetime DEFAULT NULL,
  `session_end_time` datetime DEFAULT NULL,
  `voted` tinyint(4) DEFAULT '0',
  `session_expires` datetime NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Дамп данных таблицы phpblog.session: 0 rows
/*!40000 ALTER TABLE `session` DISABLE KEYS */;
/*!40000 ALTER TABLE `session` ENABLE KEYS */;


-- Дамп структуры для процедура phpblog.UPDATE_ROW_LOGGED_USER
DELIMITER //
CREATE DEFINER=`phpblog_admin`@`localhost` PROCEDURE `UPDATE_ROW_LOGGED_USER`(
IN MY_SESSION_ID VARCHAR(50),
IN MY_PASSWORD VARCHAR(32),
IN MY_INFO VARCHAR(250),
IN MY_DATE datetime,
OUT RETVAL INT)
BEGIN
IF (SELECT COUNT(TABLE_NAME) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME LIKE '%user_session_info%') IS NOT NULL THEN 
	IF (SELECT ID FROM user_session_info WHERE LOGIN=MY_SESSION_ID AND password=MY_PASSWORD) IS NOT NULL THEN
		UPDATE user_session_info SET Info=MY_INFO WHERE login=MY_SESSION_ID AND password=MY_PASSWORD;
	END IF;
	SET RETVAL=1;
ELSE
	SET RETVAL=0;
END IF;
END//
DELIMITER ;


-- Дамп структуры для таблица phpblog.user_session_info
CREATE TABLE IF NOT EXISTS `user_session_info` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL DEFAULT 'program error',
  `password` varchar(32) NOT NULL DEFAULT 'program error',
  `Info` varchar(250) NOT NULL DEFAULT 'program error',
  `session_id` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT 'program error',
  `Login_time` datetime NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `login` (`login`),
  KEY `password` (`password`),
  KEY `session_id` (`session_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1603 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы phpblog.user_session_info: 30 rows
/*!40000 ALTER TABLE `user_session_info` DISABLE KEYS */;
INSERT INTO `user_session_info` (`ID`, `login`, `password`, `Info`, `session_id`, `Login_time`) VALUES
	(1573, 'demmtkqrmsi7cqqo0e0lu6g9u0', 'demmtkqrmsi7cqqo0e0lu6g9u0', '112', 'demmtkqrmsi7cqqo0e0lu6g9u0', '2016-10-08 17:34:35'),
	(1574, '', '', '509', '', '2016-10-09 22:31:15'),
	(1575, 's3kf312e62ofncgcrrpllvbj93', 's3kf312e62ofncgcrrpllvbj93', '9', 's3kf312e62ofncgcrrpllvbj93', '2016-10-16 02:07:26'),
	(1576, '6snq6e320j8ih2vc80hr1c5c86', '6snq6e320j8ih2vc80hr1c5c86', '2', '6snq6e320j8ih2vc80hr1c5c86', '2016-10-16 12:53:57'),
	(1577, 'd9m3mv2vhb2o69d2neml9qvi17', 'd9m3mv2vhb2o69d2neml9qvi17', '28', 'd9m3mv2vhb2o69d2neml9qvi17', '2016-10-16 19:18:27'),
	(1578, 'vfur4dr4dkdf6fsoe8070gc893', 'vfur4dr4dkdf6fsoe8070gc893', '1', 'vfur4dr4dkdf6fsoe8070gc893', '2016-10-16 21:42:17'),
	(1579, 'hjf9ad6pdgh4h1qjolfd0u4lk7', 'hjf9ad6pdgh4h1qjolfd0u4lk7', '1', 'hjf9ad6pdgh4h1qjolfd0u4lk7', '2016-10-16 21:42:42'),
	(1580, 'fscslbvrg9fi1v1fue9t1udkn3', 'fscslbvrg9fi1v1fue9t1udkn3', '12', 'fscslbvrg9fi1v1fue9t1udkn3', '2016-10-16 21:42:47'),
	(1581, 'm7rsrvtslot7dcct7glh1qf3l1', 'm7rsrvtslot7dcct7glh1qf3l1', '24', 'm7rsrvtslot7dcct7glh1qf3l1', '2016-10-17 00:37:11'),
	(1582, 'roa4d736j5rba7q6n67p8kfvi5', 'roa4d736j5rba7q6n67p8kfvi5', '10', 'roa4d736j5rba7q6n67p8kfvi5', '2016-10-17 12:22:09'),
	(1583, 'ae8vd9pqikrt414fcheal00ut1', 'ae8vd9pqikrt414fcheal00ut1', '3', 'ae8vd9pqikrt414fcheal00ut1', '2016-10-17 12:24:01'),
	(1584, '0jngheh3o5ci3b0n7um1sg25e6', '0jngheh3o5ci3b0n7um1sg25e6', '1', '0jngheh3o5ci3b0n7um1sg25e6', '2016-10-17 12:34:21'),
	(1585, 'nnjcvup6h05of9jt0dh9e540h6', 'nnjcvup6h05of9jt0dh9e540h6', '3', 'nnjcvup6h05of9jt0dh9e540h6', '2016-10-17 12:34:59'),
	(1586, 'q5te0c7cald0mdkei2mehhc9d7', 'q5te0c7cald0mdkei2mehhc9d7', '2', 'q5te0c7cald0mdkei2mehhc9d7', '2016-10-17 12:48:39'),
	(1587, 'rkrg4co2s2gm0dt8tshtb3p0j3', 'rkrg4co2s2gm0dt8tshtb3p0j3', '7', 'rkrg4co2s2gm0dt8tshtb3p0j3', '2016-10-17 12:52:27'),
	(1588, 'i8ssg7dgtbgvtees5lf7h0iil3', 'i8ssg7dgtbgvtees5lf7h0iil3', '1', 'i8ssg7dgtbgvtees5lf7h0iil3', '2016-10-17 13:59:54'),
	(1589, 'kgqjcurpgi8p1b8cimgesoj6d0', 'kgqjcurpgi8p1b8cimgesoj6d0', '1', 'kgqjcurpgi8p1b8cimgesoj6d0', '2016-10-17 14:00:19'),
	(1590, 'tsar68foc9vpng19q44du1hs50', 'tsar68foc9vpng19q44du1hs50', '3', 'tsar68foc9vpng19q44du1hs50', '2016-10-17 14:00:32'),
	(1591, 'uh0dv1hkurf38kps3fntfu0826', 'uh0dv1hkurf38kps3fntfu0826', '2', 'uh0dv1hkurf38kps3fntfu0826', '2016-10-17 14:07:26'),
	(1592, 'gqs10b9brl0742tm5mcl56bo97', 'gqs10b9brl0742tm5mcl56bo97', '6', 'gqs10b9brl0742tm5mcl56bo97', '2016-10-17 14:07:56'),
	(1593, 'vqlcrlj6b6rs15aoethup9api0', 'vqlcrlj6b6rs15aoethup9api0', '29', 'vqlcrlj6b6rs15aoethup9api0', '2016-10-17 15:44:17'),
	(1594, 'vmbm9hl199neujkade8s7747e2', 'vmbm9hl199neujkade8s7747e2', '2', 'vmbm9hl199neujkade8s7747e2', '2016-10-17 22:29:34'),
	(1595, '162la7i97mf060hhm0b4d59i20', '162la7i97mf060hhm0b4d59i20', '2', '162la7i97mf060hhm0b4d59i20', '2016-10-17 22:33:10'),
	(1596, 'oip9sfc02k09a5815ok5gs5du5', 'oip9sfc02k09a5815ok5gs5du5', '1', 'oip9sfc02k09a5815ok5gs5du5', '2016-10-17 22:43:05'),
	(1597, '624pluohcc5gvqs6ir99frgdo6', '624pluohcc5gvqs6ir99frgdo6', '2', '624pluohcc5gvqs6ir99frgdo6', '2016-10-17 22:43:18'),
	(1598, 'ep7sspe5e38j05bkp1cdq554u6', 'ep7sspe5e38j05bkp1cdq554u6', '2', 'ep7sspe5e38j05bkp1cdq554u6', '2016-10-17 22:44:02'),
	(1599, '52pgrvq6e4p5ejpa4pqlim24h7', '52pgrvq6e4p5ejpa4pqlim24h7', '103', '52pgrvq6e4p5ejpa4pqlim24h7', '2016-10-17 22:52:37'),
	(1600, 'u9e6bq31bj72l3jd5kipspp6p0', 'u9e6bq31bj72l3jd5kipspp6p0', '155', 'u9e6bq31bj72l3jd5kipspp6p0', '2016-10-17 22:55:06'),
	(1601, 'se9gv4db7thnjfqapmv1bjc577', 'se9gv4db7thnjfqapmv1bjc577', '17', 'se9gv4db7thnjfqapmv1bjc577', '2016-10-18 22:33:23'),
	(1602, '4d2fg1j2ltqq9h4cd79elqe2o7', '4d2fg1j2ltqq9h4cd79elqe2o7', '30', '4d2fg1j2ltqq9h4cd79elqe2o7', '2016-10-18 23:34:50');
/*!40000 ALTER TABLE `user_session_info` ENABLE KEYS */;


-- Дамп структуры для представление phpblog.blogssort
-- Удаление временной таблицы и создание окончательной структуры представления
DROP TABLE IF EXISTS `blogssort`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` VIEW `blogssort` AS select b.id,
b.parentid,
b.`create`,
b.text,
b.userid,
(select u from fos_user where id=b.userid) user,
b.`sort`,
t.id childid,
t.parentid childparentid,
t.`create` childcreate,
t.text childtext,
t.userid childuserid,
(select u from fos_user where id=t.userid) childuser,
t.`sort` `childsort`
FROM blogs b 
left join blogs t on t.parentid=b.id 
left join fos_user u on b.userid=u.id and t.userid=u.id 
where (b.parentid=0 and b.id<>0 and t.id is not null and t.parentid is not null and t.text is not null) 
or (b.parentid=0 and b.id<>0 and t.id is null and t.parentid is null and t.text is null) 
or (b.parentid<>0 and b.id<>0 and t.id is not null and t.parentid is not null and t.text is not null)
order by b.sort,b.parentid,b.id ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
