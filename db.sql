-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               5.5.41-log - MySQL Community Server (GPL)
-- Операционная система:         Win32
-- HeidiSQL Версия:              9.3.0.5055
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Дамп структуры для таблица e-shop1.bigSlider
CREATE TABLE IF NOT EXISTS `bigSlider` (
  `ID` int(5) NOT NULL AUTO_INCREMENT,
  `nomer` int(3) NOT NULL DEFAULT '0',
  `stranica` varchar(50) DEFAULT NULL,
  `photo` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='универсальная таблица для хранения картинок для слайдера';

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица e-shop1.categories
CREATE TABLE IF NOT EXISTS `categories` (
  `ID` int(5) NOT NULL AUTO_INCREMENT,
  `title` varchar(180) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.
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

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица e-shop1.products
CREATE TABLE IF NOT EXISTS `products` (
  `ID` int(5) NOT NULL AUTO_INCREMENT,
  `date` varchar(250) NOT NULL DEFAULT '0',
  `type` int(2) NOT NULL DEFAULT '0' COMMENT '1 - муж; 2 - жен;',
  `cat_id` int(5) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '0',
  `photo` varchar(250) NOT NULL DEFAULT '0',
  `price` varchar(250) NOT NULL DEFAULT '0',
  `price_2` varchar(250) NOT NULL DEFAULT '0' COMMENT 'цена со скидкой',
  `text` text,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
