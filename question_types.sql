# ************************************************************
# Sequel Pro SQL dump
# Version 4499
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.6.27-0ubuntu0.14.04.1)
# Database: trellis
# Generation Time: 2015-11-24 21:55:14 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table question_type
# ------------------------------------------------------------

DROP TABLE IF EXISTS `question_type`;

CREATE TABLE `question_type` (
  `id` varchar(41) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `question_type` WRITE;
/*!40000 ALTER TABLE `question_type` DISABLE KEYS */;

INSERT INTO `question_type` (`id`, `name`, `created_at`, `updated_at`, `deleted_at`)
VALUES
	('06162912-8048-4978-a8d2-92b6dd0c2ed1','time','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),
	('0f76b96f-613a-4925-bacd-74db45368edb','multiple_select','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),
	('1e9e577d-524c-4af1-bd70-26b561e14710','image','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),
	('2ab4a309-5c65-4eec-a044-c75a89ba25f1','relationship','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),
	('2d3ff07a-5ab1-4da0-aa7f-440cf8cd0980','integer','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),
	('312533dd-5957-453c-ab00-691f869d257f','decimal','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),
	('49c03474-cbe8-4f4c-ab10-6491f936338f','group','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),
	('948ffae0-bfb3-4cf1-a3e9-b4845181cb61','text','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),
	('99e769a7-c2b3-41ae-98a3-9b7afbfc4a45','text_area','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),
	('b58f23fa-52c7-435e-9b31-5fb771e79f41','multiple_choice','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),
	('c35db71d-cb10-49c7-909c-e67a9a29e736','geo','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),
	('cebe05f8-8e17-4c5c-a5fa-abc3a9c6c1f9','intro','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),
	('d566e086-c95e-45aa-9b3f-e88cb1802081','year','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),
	('d840f8cb-b68b-432a-9a47-2b0b5dc65377','year_month','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),
	('efbafb7c-62ca-4ed9-92df-7d171e855650','year_month_day','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),
	('effab4ce-df07-459d-a2a4-25be77bcca1b','year_month_day_time','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL);

/*!40000 ALTER TABLE `question_type` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
