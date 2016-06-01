-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               5.5.41-log - MySQL Community Server (GPL)
-- Операционная система:         Win32
-- HeidiSQL Версия:              9.3.0.5049
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Дамп структуры для таблица e-shop1.bigSlider
CREATE TABLE IF NOT EXISTS `bigSlider` (
  `ID` int(5) NOT NULL AUTO_INCREMENT,
  `stranica` varchar(50) DEFAULT NULL,
  `photo` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='универсальная таблица для хранения картинок для слайдера';

-- Дамп данных таблицы e-shop1.bigSlider: ~0 rows (приблизительно)
/*!40000 ALTER TABLE `bigSlider` DISABLE KEYS */;
/*!40000 ALTER TABLE `bigSlider` ENABLE KEYS */;

-- Дамп структуры для таблица e-shop1.categories
CREATE TABLE IF NOT EXISTS `categories` (
  `ID` int(5) NOT NULL AUTO_INCREMENT,
  `title` varchar(180) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы e-shop1.categories: ~4 rows (приблизительно)
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
REPLACE INTO `categories` (`ID`, `title`) VALUES
	(1, 'штаны'),
	(3, 'Носки'),
	(4, 'Курточки'),
	(5, 'Шапки');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;

-- Дамп структуры для таблица e-shop1.page_settings
CREATE TABLE IF NOT EXISTS `page_settings` (
  `ID` int(5) NOT NULL AUTO_INCREMENT,
  `stranica` varchar(180) NOT NULL DEFAULT '0',
  `title` varchar(180) NOT NULL DEFAULT '0',
  `btn_title` varchar(180) NOT NULL DEFAULT '0',
  `meta` text NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `stranica` (`stranica`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы e-shop1.page_settings: ~4 rows (приблизительно)
/*!40000 ALTER TABLE `page_settings` DISABLE KEYS */;
REPLACE INTO `page_settings` (`ID`, `stranica`, `title`, `btn_title`, `meta`, `text`) VALUES
	(6, 'main', '', 'fdsfsdfds', '{"title":"\\u043c\\u0435\\u0442\\u0430 \\u0437\\u0430\\u0433\\u043e\\u043b\\u043e\\u0432\\u043e\\u043a","desc":"\\u043a\\u0440\\u0430\\u0442\\u043a\\u043e\\u0435 \\u043e\\u043f\\u0438\\u0441\\u0430\\u043d\\u0438\\u0435","keywords":"\\u043a\\u043b\\u044e\\u0447\\u0435\\u0432\\u044b\\u0435, \\u0441\\u043b\\u043e\\u0432\\u0430"}', 'sdsds sd sd sdsdsds ds ds d'),
	(7, 'dsdssdsss', 'dsfdsfds', 'fdsfsdfds', '{"title":"fdsfds","desc":"fdsfds","keywords":"fdsfdsfds"}', 'fhfghfgdfgdfg'),
	(8, 'df df dfdf d', 'f df df', ' df d', '{"title":"f df df ","desc":"df df","keywords":"df df d"}', 'f d df df df '),
	(9, '', 'Штаны', '', '{"title":"","desc":"","keywords":""}', '');
/*!40000 ALTER TABLE `page_settings` ENABLE KEYS */;

-- Дамп структуры для таблица e-shop1.users
CREATE TABLE IF NOT EXISTS `users` (
  `ID` int(5) NOT NULL AUTO_INCREMENT,
  `email` varchar(250) NOT NULL DEFAULT '0',
  `pass` varchar(250) NOT NULL DEFAULT '0',
  `nickname` varchar(250) NOT NULL DEFAULT '0',
  `date` bigint(11) NOT NULL DEFAULT '0',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '1 - просто пользователь; 2 - модератор; 3 - админ;',
  `avatar` varchar(250) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы e-shop1.users: ~1 rows (приблизительно)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
REPLACE INTO `users` (`ID`, `email`, `pass`, `nickname`, `date`, `status`, `avatar`) VALUES
	(10, 'sht_job@ukr.net', '202cb962ac59075b964b07152d234b70', 'Вася', 1464109435, 3, 'ed4ffccdbdc7793b292bf19ad6a96059.png');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
