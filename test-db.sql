-- MySQL dump 10.13  Distrib 5.7.23, for Linux (x86_64)
--
-- Host: localhost    Database: trellis_test
-- ------------------------------------------------------
-- Server version	5.7.23-0ubuntu0.16.04.1

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
-- Table structure for table `action`
--

DROP TABLE IF EXISTS `action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `survey_id` varchar(41) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `question_id` varchar(41) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `payload` text COLLATE utf8mb4_unicode_ci,
  `action_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `interview_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `section_follow_up_repetition` int(11) DEFAULT NULL,
  `section_repetition` int(11) DEFAULT NULL,
  `preload_action_id` varchar(41) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `action_question_id_foreign` (`question_id`),
  KEY `action_survey_id_foreign` (`survey_id`),
  KEY `action_interview_id_foreign` (`interview_id`),
  KEY `fk__preload_preload_action__idx` (`preload_action_id`),
  CONSTRAINT `action_interview_id_foreign` FOREIGN KEY (`interview_id`) REFERENCES `interview` (`id`),
  CONSTRAINT `action_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`),
  CONSTRAINT `action_survey_id_foreign` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`),
  CONSTRAINT `fk__preload_preload_action__idx` FOREIGN KEY (`preload_action_id`) REFERENCES `preload_action` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action`
--

LOCK TABLES `action` WRITE;
/*!40000 ALTER TABLE `action` DISABLE KEYS */;
/*!40000 ALTER TABLE `action` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assign_condition_tag`
--

DROP TABLE IF EXISTS `assign_condition_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assign_condition_tag` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `condition_tag_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logic` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `scope` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'RESPONDENT / SURVEY',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__assign_condition_tag__condition_idx` (`condition_tag_id`),
  CONSTRAINT `fk__assign_condition_tag__condition` FOREIGN KEY (`condition_tag_id`) REFERENCES `condition_tag` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assign_condition_tag`
--

LOCK TABLES `assign_condition_tag` WRITE;
/*!40000 ALTER TABLE `assign_condition_tag` DISABLE KEYS */;
INSERT INTO `assign_condition_tag` VALUES ('067c4ee7-9553-43d6-8768-347387dfdf89','b936d784-28b4-4c5c-b334-41fa63071488','function (d) {return parseInt(d.assigner, 10) === 2}','form','2018-09-12 16:38:12','2018-09-12 16:38:37',NULL),('1654f44f-fe57-4170-afcd-dc2adeb55574','eb511781-8c0d-449b-9710-aa22f3cc9a67','function (d) { return parseInt(d.assigner, 10) === 1 }','form','2018-09-12 16:36:25','2018-09-12 16:37:22',NULL),('9b51a9cc-6cbc-43e2-a05c-510495924423','ad24d06a-b932-480e-9b8a-39d00cf88ae2','function (d) {\n   return d.pet_type == \'dog\'\n}','section','2018-09-07 15:00:56','2018-09-07 15:01:20',NULL),('b08174b3-9c59-4fea-9084-bb228a9b904a','9fbce8a9-6b37-4e4f-b059-ad4db6b5171f','function () { return false;}','respondent','2018-09-12 13:14:39','2018-09-12 13:15:13',NULL),('dfc8a8c4-fcae-4ba0-9b34-8e9af5a5e82d','529afb22-fcac-4d35-a405-d420ffe1fd9a','function (d) {return true;}','form','2018-09-12 16:41:08','2018-09-12 16:41:26',NULL);
/*!40000 ALTER TABLE `assign_condition_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `census_type`
--

DROP TABLE IF EXISTS `census_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `census_type` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `census_type`
--

LOCK TABLES `census_type` WRITE;
/*!40000 ALTER TABLE `census_type` DISABLE KEYS */;
INSERT INTO `census_type` VALUES ('06162912-8048-4978-a8d2-92b6dd0c2ed1','add respondent'),('0f76b96f-613a-4925-bacd-74db45368edb','add geo'),('1e9e577d-524c-4af1-bd70-26b561e14710','move respondent'),('957757f8-5952-482b-b753-2e686409e573','rename respondent'),('bd4f005e-1443-44e3-9d45-cbee6c5b9a18','add other respondent');
/*!40000 ALTER TABLE `census_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `choice`
--

DROP TABLE IF EXISTS `choice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `choice` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `choice_translation_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `val` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__choice__translation_idx` (`choice_translation_id`),
  CONSTRAINT `fk__choice__translation` FOREIGN KEY (`choice_translation_id`) REFERENCES `translation` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `choice`
--

LOCK TABLES `choice` WRITE;
/*!40000 ALTER TABLE `choice` DISABLE KEYS */;
INSERT INTO `choice` VALUES ('02170a9c-139c-4cfc-ac35-57ef9c602396','4c160d54-8ea6-4ff0-8661-9351bfaf160b','brown','2018-09-07 14:59:56','2018-09-07 15:02:15',NULL),('036eacf6-4618-43ef-99f8-526043ac7c2d','d19f44ad-3241-4d31-88ba-51a9b7fc4f59','two','2018-09-07 14:54:31','2018-09-07 14:54:46',NULL),('055f0dc5-3d61-442c-b002-1c8bfff70fa5','617bb75c-a46f-49e0-a4ac-560cba1dc5be','one','2018-09-12 16:38:02','2018-09-12 16:40:01',NULL),('3647af87-d56d-4a60-96e8-46391cbdd289','90862bbf-f083-4835-bbc4-444c8af232cd','horse','2018-09-07 14:58:54','2018-09-07 14:59:06',NULL),('404d56e3-4bb5-4139-a1c4-6f9d2b56d13a','ea4fedd7-7d20-4c6c-9a34-697f409ee306','yellow','2018-09-07 14:59:54','2018-09-07 15:02:13',NULL),('539b94da-fbbd-42fc-b840-c18aefeb05ce','3bb3e23f-2bd0-4767-b02d-ea32812035cf','two','2018-09-12 16:38:03','2018-09-12 16:40:04',NULL),('7027e80e-cffa-412f-a905-0dde32d2a2ef','14279205-cf14-497c-9ce7-cb50eb32ef1d','yes','2018-09-17 16:35:16','2018-09-17 16:35:26',NULL),('7b5ef540-ec76-4adc-acb5-11d40d802eb1','df7527cd-c903-4068-826c-98ee3e9a57a0','1','2018-09-12 16:35:46','2018-09-12 16:36:09',NULL),('9efca378-a58c-4934-ad75-951e2310c797','0310ad5f-e129-49fa-a8ef-7c96dab847ce','one','2018-09-07 14:54:27','2018-09-07 14:54:41',NULL),('aac56b68-5f7c-4f4e-93e9-b7f58e7e40c7','53b61903-f13e-415c-a574-c892b8eea9e2','three','2018-09-07 14:54:48','2018-09-07 14:55:26',NULL),('cef52a1f-ed2e-472d-9975-0a12fdb2d005','286aa8c2-6d0b-4b35-a905-19cedc8640f9','dog','2018-09-07 14:56:53','2018-09-07 14:57:43',NULL),('cf1a9059-a8ab-4084-a426-2d520b962a5e','13cf2681-c623-4e49-ac4d-22bc866a3f1c','cat','2018-09-07 14:56:57','2018-09-07 14:57:46',NULL),('d47d622b-061a-4d14-9c47-db251f7ea953','27f7c054-1e24-4dd4-8991-b6cdab557417','2','2018-09-12 16:35:48','2018-09-12 16:36:12',NULL),('d82cc954-38e3-4386-8826-c9e4f443f4c0','1ee50b21-fabd-49e5-b6ca-d14aed147285','other','2018-09-07 14:58:22','2018-09-07 14:58:46',NULL),('f81bed36-4c11-443e-89a6-b847fb44fc78','e2e04746-5ded-4331-9029-a1bae0aff2f1','other','2018-09-07 15:00:20','2018-09-07 15:01:59',NULL);
/*!40000 ALTER TABLE `choice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `condition_tag`
--

DROP TABLE IF EXISTS `condition_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `condition_tag` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '0 = show_if, 1 = hide_if, 2-255 reserved',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `condition_tag`
--

LOCK TABLES `condition_tag` WRITE;
/*!40000 ALTER TABLE `condition_tag` DISABLE KEYS */;
INSERT INTO `condition_tag` VALUES ('1f0629de-e256-49d7-818b-67622c4f9c9a','is_human','2018-09-07 15:34:18','2018-09-07 15:34:18',NULL),('529afb22-fcac-4d35-a405-d420ffe1fd9a','skipped_maybe_was_evaluated','2018-09-12 16:41:08','2018-09-12 16:41:08',NULL),('9fbce8a9-6b37-4e4f-b059-ad4db6b5171f','show_first_question','2018-09-12 13:14:39','2018-09-12 13:14:39',NULL),('ad24d06a-b932-480e-9b8a-39d00cf88ae2','is_dog','2018-09-07 15:00:56','2018-09-07 15:00:56',NULL),('b936d784-28b4-4c5c-b334-41fa63071488','is_two','2018-09-12 16:38:12','2018-09-12 16:38:12',NULL),('c132adce-84ef-4be0-baca-b2b22bb7c28b','is_people','2018-09-07 15:34:57','2018-09-07 15:34:57',NULL),('eb511781-8c0d-449b-9710-aa22f3cc9a67','is_one','2018-09-12 16:36:25','2018-09-12 16:36:25',NULL);
/*!40000 ALTER TABLE `condition_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `datum`
--

DROP TABLE IF EXISTS `datum`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datum` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `val` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `choice_id` varchar(41) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `survey_id` varchar(41) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `question_id` varchar(41) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_datum_id` varchar(41) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `datum_type_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `sort_order` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `roster_id` varchar(41) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_order` smallint(6) NOT NULL,
  `question_datum_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `geo_id` varchar(41) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `edge_id` varchar(41) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo_id` varchar(41) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__datum__choice_idx` (`choice_id`),
  KEY `fk__datum__survey_idx` (`survey_id`),
  KEY `fk__datum__question_idx` (`question_id`),
  KEY `fk__parent_datum_id__datum_idx` (`parent_datum_id`),
  KEY `fk__datum__datum_type_idx` (`datum_type_id`),
  KEY `datum_roster_id_foreign` (`roster_id`),
  KEY `datum_question_datum_id_foreign` (`question_datum_id`),
  KEY `datum_geo_id_foreign` (`geo_id`),
  KEY `datum_edge_id_foreign` (`edge_id`),
  KEY `datum_photo_id_foreign` (`photo_id`),
  CONSTRAINT `datum_edge_id_foreign` FOREIGN KEY (`edge_id`) REFERENCES `edge` (`id`),
  CONSTRAINT `datum_geo_id_foreign` FOREIGN KEY (`geo_id`) REFERENCES `geo` (`id`),
  CONSTRAINT `datum_photo_id_foreign` FOREIGN KEY (`photo_id`) REFERENCES `photo` (`id`),
  CONSTRAINT `datum_question_datum_id_foreign` FOREIGN KEY (`question_datum_id`) REFERENCES `question_datum` (`id`),
  CONSTRAINT `datum_roster_id_foreign` FOREIGN KEY (`roster_id`) REFERENCES `roster` (`id`),
  CONSTRAINT `fk__datum__choice` FOREIGN KEY (`choice_id`) REFERENCES `choice` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  CONSTRAINT `fk__datum__datum_type` FOREIGN KEY (`datum_type_id`) REFERENCES `datum_type` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__datum__question` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  CONSTRAINT `fk__datum__survey` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__parent_datum_id__datum` FOREIGN KEY (`parent_datum_id`) REFERENCES `datum` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `datum`
--

LOCK TABLES `datum` WRITE;
/*!40000 ALTER TABLE `datum` DISABLE KEYS */;
INSERT INTO `datum` VALUES ('74351709-8fb7-471b-b5c5-a43717dee24a','','two','036eacf6-4618-43ef-99f8-526043ac7c2d','012aef54-40bc-4a99-9bdd-d0bf20fcc72c',NULL,NULL,'0',NULL,'2018-09-07 15:43:54','2018-09-07 15:43:54',NULL,NULL,1,'a58e9a73-b386-4cb6-88be-08a10df3f987',NULL,NULL,NULL),('99672184-667c-4bd2-a935-51a56c1dd932','','one','9efca378-a58c-4934-ad75-951e2310c797','012aef54-40bc-4a99-9bdd-d0bf20fcc72c',NULL,NULL,'0',NULL,'2018-09-07 15:43:53','2018-09-07 15:43:53',NULL,NULL,0,'a58e9a73-b386-4cb6-88be-08a10df3f987',NULL,NULL,NULL),('a3064223-0064-4fbf-be75-aa9c9ed81810','','',NULL,'012aef54-40bc-4a99-9bdd-d0bf20fcc72c',NULL,NULL,'0',NULL,'2018-09-07 15:44:28','2018-09-07 15:44:28',NULL,'2978724b-c2a4-4598-93a5-520e42a8cd66',0,'8d570cfd-c0a0-44d4-9719-f0713cdee186',NULL,NULL,NULL);
/*!40000 ALTER TABLE `datum` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `datum_choice`
--

DROP TABLE IF EXISTS `datum_choice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datum_choice` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `datum_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `choice_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `override_val` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK__datum_choice__datum_idx` (`datum_id`),
  KEY `FK__datum_choice__choice_idx` (`choice_id`),
  CONSTRAINT `FK__datum_choice__choice` FOREIGN KEY (`choice_id`) REFERENCES `choice` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `FK__datum_choice__datum` FOREIGN KEY (`datum_id`) REFERENCES `datum` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `datum_choice`
--

LOCK TABLES `datum_choice` WRITE;
/*!40000 ALTER TABLE `datum_choice` DISABLE KEYS */;
/*!40000 ALTER TABLE `datum_choice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `datum_geo`
--

DROP TABLE IF EXISTS `datum_geo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datum_geo` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `datum_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `geo_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK__datum_geo__datum_idx` (`datum_id`),
  KEY `FK__datum_geo__geo_idx` (`geo_id`),
  CONSTRAINT `FK__datum_geo__datum` FOREIGN KEY (`datum_id`) REFERENCES `datum` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `FK__datum_geo__geo` FOREIGN KEY (`geo_id`) REFERENCES `geo` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `datum_geo`
--

LOCK TABLES `datum_geo` WRITE;
/*!40000 ALTER TABLE `datum_geo` DISABLE KEYS */;
/*!40000 ALTER TABLE `datum_geo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `datum_group_tag`
--

DROP TABLE IF EXISTS `datum_group_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datum_group_tag` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `datum_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_tag_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK__datum_group_tag__datum_idx` (`datum_id`),
  KEY `FK__datum_group_tag__group_tag_idx` (`group_tag_id`),
  CONSTRAINT `FK__datum_group_tag__datum` FOREIGN KEY (`datum_id`) REFERENCES `datum` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `FK__datum_group_tag__group_tag` FOREIGN KEY (`group_tag_id`) REFERENCES `group_tag` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `datum_group_tag`
--

LOCK TABLES `datum_group_tag` WRITE;
/*!40000 ALTER TABLE `datum_group_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `datum_group_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `datum_photo`
--

DROP TABLE IF EXISTS `datum_photo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datum_photo` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `datum_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__datum_photo__datum_idx` (`datum_id`),
  KEY `fk__datum_photo__photo_idx` (`photo_id`),
  CONSTRAINT `fk__datum_photo__datum` FOREIGN KEY (`datum_id`) REFERENCES `datum` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__datum_photo__photo` FOREIGN KEY (`photo_id`) REFERENCES `photo` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `datum_photo`
--

LOCK TABLES `datum_photo` WRITE;
/*!40000 ALTER TABLE `datum_photo` DISABLE KEYS */;
/*!40000 ALTER TABLE `datum_photo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `datum_type`
--

DROP TABLE IF EXISTS `datum_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datum_type` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `datum_type`
--

LOCK TABLES `datum_type` WRITE;
/*!40000 ALTER TABLE `datum_type` DISABLE KEYS */;
INSERT INTO `datum_type` VALUES ('0','default','2017-02-28 15:05:32','2017-02-28 15:05:32',NULL);
/*!40000 ALTER TABLE `datum_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `device`
--

DROP TABLE IF EXISTS `device`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `device` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `device`
--

LOCK TABLES `device` WRITE;
/*!40000 ALTER TABLE `device` DISABLE KEYS */;
INSERT INTO `device` VALUES ('55e03080-8931-49c0-9f17-acb4d6e3754b','82f102e4f7226ba6','Beast Emulator','2018-09-07 15:39:20','2018-09-07 15:39:20',NULL),('69369866-629f-4fee-a19d-3bb9a05e7db1','3c586040f3f7a483','Wyatt nexus 7','2018-09-07 15:39:43','2018-09-07 15:39:43',NULL);
/*!40000 ALTER TABLE `device` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `edge`
--

DROP TABLE IF EXISTS `edge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `edge` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_respondent_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_respondent_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__edge_source_respondent_id__respondent_idx` (`source_respondent_id`),
  KEY `fk__edge_target_respondent_id__respondent_idx` (`target_respondent_id`),
  CONSTRAINT `fk__edge_list_source__respondent` FOREIGN KEY (`source_respondent_id`) REFERENCES `respondent` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__edge_list_target__respondent` FOREIGN KEY (`target_respondent_id`) REFERENCES `respondent` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `edge`
--

LOCK TABLES `edge` WRITE;
/*!40000 ALTER TABLE `edge` DISABLE KEYS */;
INSERT INTO `edge` VALUES ('15177bdd-b5cf-11e8-8fd7-0800271047e3','6905448f-4356-40f0-b78d-0cc7c05acfdd','98926f3c-a9e2-4de5-8624-6ecf6ab128b6','2018-09-11 10:29:25','2018-09-11 10:29:25',NULL),('151785da-b5cf-11e8-8fd7-0800271047e3','6905448f-4356-40f0-b78d-0cc7c05acfdd','b4be2718-66c9-4d95-9518-d81ac7a29cbc','2018-09-11 10:29:25','2018-09-11 10:29:25',NULL),('4e1233a9-f9ae-4ba0-b5c0-dfd2123b66b8','b4be2718-66c9-4d95-9518-d81ac7a29cbc','98926f3c-a9e2-4de5-8624-6ecf6ab128b6','0000-00-00 00:00:00','0000-00-00 00:00:00',NULL),('c27de1f2-174d-481c-b62f-85b2fb6dc1ab','b4be2718-66c9-4d95-9518-d81ac7a29cbc','98926f3c-a9e2-4de5-8624-6ecf6ab128b6','0000-00-00 00:00:00','0000-00-00 00:00:00',NULL);
/*!40000 ALTER TABLE `edge` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `edge_datum`
--

DROP TABLE IF EXISTS `edge_datum`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `edge_datum` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `edge_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `datum_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__edge_datum__edge_idx` (`edge_id`),
  KEY `fk__edge_datum__datum_idx` (`datum_id`),
  CONSTRAINT `fk__connection_datum__connection` FOREIGN KEY (`edge_id`) REFERENCES `edge` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__connection_datum__datum` FOREIGN KEY (`datum_id`) REFERENCES `datum` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `edge_datum`
--

LOCK TABLES `edge_datum` WRITE;
/*!40000 ALTER TABLE `edge_datum` DISABLE KEYS */;
/*!40000 ALTER TABLE `edge_datum` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `form`
--

DROP TABLE IF EXISTS `form`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `form` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `form_master_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_translation_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `version` int(10) unsigned NOT NULL DEFAULT '0',
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx__form_master_id` (`form_master_id`),
  KEY `fk__form_name__translation_idx` (`name_translation_id`),
  CONSTRAINT `fk__form_name__translation` FOREIGN KEY (`name_translation_id`) REFERENCES `translation` (`id`) ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `form`
--

LOCK TABLES `form` WRITE;
/*!40000 ALTER TABLE `form` DISABLE KEYS */;
INSERT INTO `form` VALUES ('00be5ff5-f49b-4bd4-af0c-eae392819a69','00be5ff5-f49b-4bd4-af0c-eae392819a69','28ccfbb3-8864-4cb4-baf4-93fafe96f64f',1,1,'2018-09-12 16:34:58','2018-09-12 16:35:09',NULL),('01a36e93-32e0-4161-b1d5-6458c21d9edc','01a36e93-32e0-4161-b1d5-6458c21d9edc','3360ed21-dde8-4e72-913f-5cef09d67d8d',1,1,'2018-09-13 16:40:35','2018-09-13 16:40:41',NULL),('bfc6270b-b6c8-4d9e-b26b-c2fc38b65a48','bfc6270b-b6c8-4d9e-b26b-c2fc38b65a48','6c058693-fddb-453f-8529-f0b55333a457',1,1,'2018-09-07 14:53:05','2018-09-07 14:53:21',NULL),('c98e78f8-1bcc-4afc-8e68-530374940213','c98e78f8-1bcc-4afc-8e68-530374940213','56f6924e-57c5-4261-a3cf-f026ef011603',1,1,'2018-09-07 14:52:56','2018-09-07 14:53:00',NULL),('cb801404-806f-4ed1-b5bd-88997ad81f80','cb801404-806f-4ed1-b5bd-88997ad81f80','d05f7efd-953a-465d-99da-ed382859ab53',1,1,'2018-09-12 13:11:04','2018-09-12 13:11:24',NULL),('cea71d10-5423-4e87-ade6-5e4a4ce01090','cea71d10-5423-4e87-ade6-5e4a4ce01090','c6bda83a-2fac-46bf-b2c0-4c6eb8721ffa',1,1,'2018-09-17 16:33:28','2018-09-17 16:33:39',NULL);
/*!40000 ALTER TABLE `form` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `form_section`
--

DROP TABLE IF EXISTS `form_section`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `form_section` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `form_id` varchar(41) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `section_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_repeatable` tinyint(1) NOT NULL DEFAULT '0',
  `max_repetitions` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Max number of repetitions allowed, 0 = no limit',
  `repeat_prompt_translation_id` varchar(41) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `follow_up_question_id` varchar(41) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__form_section__form_idx` (`form_id`),
  KEY `fk__form_section__section_idx` (`section_id`),
  KEY `fk__form_section_repeat_prompt__translation_idx` (`repeat_prompt_translation_id`),
  KEY `fk__follow_up_question__question` (`follow_up_question_id`),
  CONSTRAINT `fk__follow_up_question__question` FOREIGN KEY (`follow_up_question_id`) REFERENCES `question` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  CONSTRAINT `fk__form_section__form` FOREIGN KEY (`form_id`) REFERENCES `form` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__form_section__section` FOREIGN KEY (`section_id`) REFERENCES `section` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__form_section_repeat_prompt__translation` FOREIGN KEY (`repeat_prompt_translation_id`) REFERENCES `translation` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `form_section`
--

LOCK TABLES `form_section` WRITE;
/*!40000 ALTER TABLE `form_section` DISABLE KEYS */;
INSERT INTO `form_section` VALUES ('05e2c90a-6b69-4c46-a0d6-66630dd54dfc','cb801404-806f-4ed1-b5bd-88997ad81f80','b01a541d-6518-4212-8d7f-c2cd7d663837',1,0,0,NULL,NULL,'2018-09-12 13:11:32','2018-09-12 13:11:32',NULL),('0b8aa3fa-66ab-49f0-a4a8-05a76d71d981','cea71d10-5423-4e87-ade6-5e4a4ce01090','832269fa-2c3b-4d28-b6ab-4252d4e644ae',1,0,0,NULL,NULL,'2018-09-17 16:33:45','2018-09-17 16:33:45',NULL),('2f81a834-86a7-4efa-afef-cb95fbbacb48','c98e78f8-1bcc-4afc-8e68-530374940213','2494f66f-8c02-49f2-832b-e6c49156ee33',2,0,0,'717cf2e1-fc84-4c5a-8b56-ef47acfae561','72c56240-aeb8-4068-9a8c-5ffa4ad99e18','2018-09-07 14:56:08','2018-09-07 14:56:49',NULL),('3608ffc6-de60-41fa-b0cc-638e09ba8721','00be5ff5-f49b-4bd4-af0c-eae392819a69','a55fd93c-ad35-4abf-9d10-987b82d68dd6',1,0,0,NULL,NULL,'2018-09-12 16:35:14','2018-09-12 16:35:14',NULL),('4fca2881-ede5-4413-b5c6-5c272f6c1f99','c98e78f8-1bcc-4afc-8e68-530374940213','8a6b2fa6-55de-4839-b166-4fcb3b37daf9',1,0,0,NULL,NULL,'2018-09-07 14:53:32','2018-09-07 14:53:32',NULL),('7796c5af-2899-417e-85b0-d7bf0e2c0ee7','01a36e93-32e0-4161-b1d5-6458c21d9edc','74d21b5c-4a12-46fc-9521-0dc5faf89adf',1,0,0,NULL,NULL,'2018-09-13 16:40:46','2018-09-13 16:40:46',NULL),('8419c34d-5b19-4215-ba8b-6cfe1a81ac94','cb801404-806f-4ed1-b5bd-88997ad81f80','d506ad04-6db4-4a7b-abdd-7cc56c5a9b2e',2,0,0,NULL,NULL,'2018-09-12 13:13:48','2018-09-12 13:13:48',NULL),('cad9dd72-207b-48ae-be49-eb3814fe6058','01a36e93-32e0-4161-b1d5-6458c21d9edc','6e42360e-67c5-4639-849b-993eefd15c98',2,0,0,NULL,NULL,'2018-09-13 16:41:20','2018-09-13 16:41:20',NULL);
/*!40000 ALTER TABLE `form_section` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `form_skip`
--

DROP TABLE IF EXISTS `form_skip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `form_skip` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `form_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `skip_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__form_skip__form_idx` (`form_id`),
  KEY `fk__form_skip__skip_idx` (`skip_id`),
  CONSTRAINT `fk__form_skip__form` FOREIGN KEY (`form_id`) REFERENCES `form` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__form_skip__skip` FOREIGN KEY (`skip_id`) REFERENCES `skip` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `form_skip`
--

LOCK TABLES `form_skip` WRITE;
/*!40000 ALTER TABLE `form_skip` DISABLE KEYS */;
/*!40000 ALTER TABLE `form_skip` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `form_type`
--

DROP TABLE IF EXISTS `form_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `form_type` (
  `id` tinyint(4) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `form_type`
--

LOCK TABLES `form_type` WRITE;
/*!40000 ALTER TABLE `form_type` DISABLE KEYS */;
INSERT INTO `form_type` VALUES (0,'data collection form'),(1,'census'),(2,'default census');
/*!40000 ALTER TABLE `form_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `geo`
--

DROP TABLE IF EXISTS `geo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geo` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `geo_type_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` varchar(41) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `altitude` double DEFAULT NULL,
  `name_translation_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__geo__geo_type_idx` (`geo_type_id`),
  KEY `fk__geo__parent_geo_idx` (`parent_id`),
  KEY `fk__name_translation__translation_idx` (`name_translation_id`),
  CONSTRAINT `fk__geo__geo_type` FOREIGN KEY (`geo_type_id`) REFERENCES `geo_type` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__geo__parent_geo` FOREIGN KEY (`parent_id`) REFERENCES `geo` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__geo_name__translation` FOREIGN KEY (`name_translation_id`) REFERENCES `translation` (`id`) ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `geo`
--

LOCK TABLES `geo` WRITE;
/*!40000 ALTER TABLE `geo` DISABLE KEYS */;
INSERT INTO `geo` VALUES ('02c9de74-cd46-4444-a76c-c492241f705d','fc114e35-0f08-48a4-92fc-a82d9a336b3a',NULL,12,43,11234,'03bf7b25-0117-4de5-80ad-4c14bebfd9ac','2018-09-10 17:39:06','2018-09-10 17:39:06',NULL),('3a1dc8cb-b49a-459b-b023-68240597ed4e','c1cb42b4-9178-46c9-89e3-e88b01c9ae75','02c9de74-cd46-4444-a76c-c492241f705d',123,431,312634,'f03bb2bc-2284-42c8-81a4-d52d64d9aa2e','2018-09-10 17:39:36','2018-09-10 17:39:36',NULL);
/*!40000 ALTER TABLE `geo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `geo_photo`
--

DROP TABLE IF EXISTS `geo_photo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geo_photo` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `geo_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` tinyint(3) unsigned NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__geo_photo__geo_idx` (`geo_id`),
  KEY `fk__geo_photo__photo_idx` (`photo_id`),
  CONSTRAINT `fk__geo_photo__geo` FOREIGN KEY (`geo_id`) REFERENCES `geo` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__geo_photo__photo` FOREIGN KEY (`photo_id`) REFERENCES `photo` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `geo_photo`
--

LOCK TABLES `geo_photo` WRITE;
/*!40000 ALTER TABLE `geo_photo` DISABLE KEYS */;
/*!40000 ALTER TABLE `geo_photo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `geo_type`
--

DROP TABLE IF EXISTS `geo_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geo_type` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` varchar(41) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `study_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `can_user_add` tinyint(1) NOT NULL DEFAULT '0',
  `can_user_add_child` tinyint(1) NOT NULL DEFAULT '0',
  `can_contain_respondent` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `zoom_level` decimal(6,3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK__geo_type__study_idx` (`study_id`),
  CONSTRAINT `FK__geo_type__study` FOREIGN KEY (`study_id`) REFERENCES `study` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `geo_type`
--

LOCK TABLES `geo_type` WRITE;
/*!40000 ALTER TABLE `geo_type` DISABLE KEYS */;
INSERT INTO `geo_type` VALUES ('c1cb42b4-9178-46c9-89e3-e88b01c9ae75','fc114e35-0f08-48a4-92fc-a82d9a336b3a','6a08c96a-fb80-4eae-9b2b-4d03d4b3235d','Village',1,0,1,'2018-09-10 17:38:25','2018-09-10 17:38:25',NULL,NULL),('fc114e35-0f08-48a4-92fc-a82d9a336b3a',NULL,'6a08c96a-fb80-4eae-9b2b-4d03d4b3235d','State',1,0,1,'2018-09-10 17:38:02','2018-09-10 17:38:02',NULL,NULL);
/*!40000 ALTER TABLE `geo_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `group_tag`
--

DROP TABLE IF EXISTS `group_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group_tag` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_tag_type_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__group__group_type_idx` (`group_tag_type_id`),
  CONSTRAINT `fk__group_tag__group_tag_type` FOREIGN KEY (`group_tag_type_id`) REFERENCES `group_tag_type` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_tag`
--

LOCK TABLES `group_tag` WRITE;
/*!40000 ALTER TABLE `group_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `group_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `group_tag_type`
--

DROP TABLE IF EXISTS `group_tag_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group_tag_type` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_tag_type`
--

LOCK TABLES `group_tag_type` WRITE;
/*!40000 ALTER TABLE `group_tag_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `group_tag_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `interview`
--

DROP TABLE IF EXISTS `interview`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `interview` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `survey_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(41) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL,
  `latitude` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `longitude` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `altitude` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__survey_session__survey_idx` (`survey_id`),
  KEY `fk__survey_session__user_idx` (`user_id`),
  CONSTRAINT `fk__survey_session__survey` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__survey_session__user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `interview`
--

LOCK TABLES `interview` WRITE;
/*!40000 ALTER TABLE `interview` DISABLE KEYS */;
INSERT INTO `interview` VALUES ('8d032e56-25a5-4f85-8a88-f015d059ff08','012aef54-40bc-4a99-9bdd-d0bf20fcc72c','c1f277ab-e181-11e5-84c9-a45e60f0e921','2018-09-07 15:35:54','2018-09-07 15:37:39','41.311436799999996','-72.9284608',NULL,'2018-09-07 15:35:54','2018-09-07 15:37:39',NULL),('b9021db1-2dc2-4b85-9866-d89d71c446f4','012aef54-40bc-4a99-9bdd-d0bf20fcc72c','c1f277ab-e181-11e5-84c9-a45e60f0e921','2018-09-07 15:43:37','2018-09-07 15:50:46','41.311436799999996','-72.9284608',NULL,'2018-09-07 15:43:37','2018-09-07 15:50:46',NULL);
/*!40000 ALTER TABLE `interview` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `interview_question`
--

DROP TABLE IF EXISTS `interview_question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `interview_question` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `interview_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enter_date` datetime DEFAULT NULL,
  `answer_date` datetime DEFAULT NULL,
  `leave_date` datetime DEFAULT NULL,
  `elapsed_time` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__interview_question__survey_session_idx` (`interview_id`),
  KEY `fk__interview_question__question_idx` (`question_id`),
  CONSTRAINT `fk__interview_question__interview` FOREIGN KEY (`interview_id`) REFERENCES `interview` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__interview_question__question` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `interview_question`
--

LOCK TABLES `interview_question` WRITE;
/*!40000 ALTER TABLE `interview_question` DISABLE KEYS */;
/*!40000 ALTER TABLE `interview_question` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_reserved_reserved_at_index` (`queue`,`reserved`,`reserved_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `key`
--

DROP TABLE IF EXISTS `key`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `key` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `hash` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_name_UNIQUE` (`name`),
  UNIQUE KEY `key_hash_UNIQUE` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `key`
--

LOCK TABLES `key` WRITE;
/*!40000 ALTER TABLE `key` DISABLE KEYS */;
INSERT INTO `key` VALUES ('1','X-Key','rXghvr7C1Q8dRmhX2Lyl3wC62TyoAr95','2018-09-07 14:52:03','2018-09-07 14:52:03',NULL);
/*!40000 ALTER TABLE `key` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `locale`
--

DROP TABLE IF EXISTS `locale`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `locale` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `language_tag` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language_name` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'The English name of the language.',
  `language_native` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'The name of the language in the language itself.',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `locale`
--

LOCK TABLES `locale` WRITE;
/*!40000 ALTER TABLE `locale` DISABLE KEYS */;
INSERT INTO `locale` VALUES ('47df4404-84d4-11e5-ba05-0800279114ca','ab','Abkhaz','аҧсуа бызшәа, аҧсшәа','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('47e14aef-84d4-11e5-ba05-0800279114ca','aa','Afar','Afaraf','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('47e3d348-84d4-11e5-ba05-0800279114ca','af','Afrikaans','Afrikaans','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('47e67b00-84d4-11e5-ba05-0800279114ca','ak','Akan','Akan','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('47e8d79e-84d4-11e5-ba05-0800279114ca','sq','Albanian','Shqip','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('47eb55ef-84d4-11e5-ba05-0800279114ca','am','Amharic','አማርኛ','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('47f07b08-84d4-11e5-ba05-0800279114ca','ar','Arabic','العربية','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('47f31306-84d4-11e5-ba05-0800279114ca','an','Aragonese','aragonés','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('47f831dd-84d4-11e5-ba05-0800279114ca','hy','Armenian','Հայերեն','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('47fad7f9-84d4-11e5-ba05-0800279114ca','as','Assamese','অসমীয়া','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('47ffcb4b-84d4-11e5-ba05-0800279114ca','av','Avaric','авар мацӀ, магӀарул мацӀ','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4802535f-84d4-11e5-ba05-0800279114ca','ae','Avestan','avesta','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48078038-84d4-11e5-ba05-0800279114ca','ay','Aymara','aymar aru','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4809d6e6-84d4-11e5-ba05-0800279114ca','az','Azerbaijani','azərbaycan dili','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('480f139b-84d4-11e5-ba05-0800279114ca','bm','Bambara','bamanankan','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4811a05d-84d4-11e5-ba05-0800279114ca','ba','Bashkir','башҡорт теле','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4816a29d-84d4-11e5-ba05-0800279114ca','eu','Basque','euskara, euskera','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4827c398-84d4-11e5-ba05-0800279114ca','be','Belarusian','беларуская мова','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('482b2d50-84d4-11e5-ba05-0800279114ca','bn','Bengali, Bangla','বাংলা','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48300d40-84d4-11e5-ba05-0800279114ca','bh','Bihari','भोजपुरी','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4832a1d5-84d4-11e5-ba05-0800279114ca','bi','Bislama','Bislama','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('483800ac-84d4-11e5-ba05-0800279114ca','bs','Bosnian','bosanski jezik','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('483cc9e2-84d4-11e5-ba05-0800279114ca','br','Breton','brezhoneg','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4841fff8-84d4-11e5-ba05-0800279114ca','bg','Bulgarian','български език','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4846fac0-84d4-11e5-ba05-0800279114ca','my','Burmese','ဗမာစာ','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('484c0f95-84d4-11e5-ba05-0800279114ca','ca','Catalan','català','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('485162da-84d4-11e5-ba05-0800279114ca','ch','Chamorro','Chamoru','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48563005-84d4-11e5-ba05-0800279114ca','ce','Chechen','нохчийн мотт','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('485b5e07-84d4-11e5-ba05-0800279114ca','ny','Chichewa, Chewa, Nyanja','chiCheŵa, chinyanja','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4860acc2-84d4-11e5-ba05-0800279114ca','zh','Chinese','中文 (Zhōngwén), 汉语, 漢語','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4865899d-84d4-11e5-ba05-0800279114ca','cv','Chuvash','чӑваш чӗлхи','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('486aa4d1-84d4-11e5-ba05-0800279114ca','kw','Cornish','Kernewek','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('486fa170-84d4-11e5-ba05-0800279114ca','co','Corsican','corsu, lingua corsa','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4874aea2-84d4-11e5-ba05-0800279114ca','cr','Cree','ᓀᐦᐃᔭᐍᐏᐣ','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4879eb59-84d4-11e5-ba05-0800279114ca','hr','Croatian','hrvatski jezik','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('487eeed2-84d4-11e5-ba05-0800279114ca','cs','Czech','čeština, český jazyk','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4883e59b-84d4-11e5-ba05-0800279114ca','da','Danish','dansk','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48892cdc-84d4-11e5-ba05-0800279114ca','dv','Divehi, Dhivehi, Maldivian','ދިވެހި','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('488b9f6e-84d4-11e5-ba05-0800279114ca','nl','Dutch','Nederlands, Vlaams','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48911159-84d4-11e5-ba05-0800279114ca','dz','Dzongkha','རྫོང་ཁ','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48984fbe-84d4-11e5-ba05-0800279114ca','en','English','English','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('489b0858-84d4-11e5-ba05-0800279114ca','eo','Esperanto','Esperanto','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('489ffe36-84d4-11e5-ba05-0800279114ca','et','Estonian','eesti, eesti keel','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48a285b0-84d4-11e5-ba05-0800279114ca','ee','Ewe','Eʋegbe','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48a7b34a-84d4-11e5-ba05-0800279114ca','fo','Faroese','føroyskt','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48aa3281-84d4-11e5-ba05-0800279114ca','fj','Fijian','vosa Vakaviti','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48af7563-84d4-11e5-ba05-0800279114ca','fi','Finnish','suomi, suomen kieli','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48b1f4da-84d4-11e5-ba05-0800279114ca','fr','French','français, langue française','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48b45b72-84d4-11e5-ba05-0800279114ca','ff','Fula, Fulah, Pulaar, Pular','Fulfulde, Pulaar, Pular','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48b6dba3-84d4-11e5-ba05-0800279114ca','gl','Galician','galego','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48b9924f-84d4-11e5-ba05-0800279114ca','ka','Georgian','ქართული','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48be843a-84d4-11e5-ba05-0800279114ca','de','German','Deutsch','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48c11dba-84d4-11e5-ba05-0800279114ca','el','Greek (modern)','ελληνικά','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48c618ff-84d4-11e5-ba05-0800279114ca','gn','Guaraní','Avañe\'ẽ','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48c89f38-84d4-11e5-ba05-0800279114ca','gu','Gujarati','ગુજરાતી','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48cdd411-84d4-11e5-ba05-0800279114ca','ht','Haitian, Haitian Creole','Kreyòl ayisyen','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48d04156-84d4-11e5-ba05-0800279114ca','ha','Hausa','(Hausa) هَوُسَ','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48d2eee7-84d4-11e5-ba05-0800279114ca','he','Hebrew (modern)','עברית','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48d56c06-84d4-11e5-ba05-0800279114ca','hz','Herero','Otjiherero','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48d7f74a-84d4-11e5-ba05-0800279114ca','hi','Hindi','हिन्दी, हिंदी','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48dd1e6b-84d4-11e5-ba05-0800279114ca','ho','Hiri Motu','Hiri Motu','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48dfda49-84d4-11e5-ba05-0800279114ca','hu','Hungarian','magyar','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48e4a715-84d4-11e5-ba05-0800279114ca','ia','Interlingua','Interlingua','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48e7477e-84d4-11e5-ba05-0800279114ca','id','Indonesian','Bahasa Indonesia','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48ec532f-84d4-11e5-ba05-0800279114ca','ie','Interlingue','Originally called Occidental; then Interlingue after WWII','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48eecffb-84d4-11e5-ba05-0800279114ca','ga','Irish','Gaeilge','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48f3e7ae-84d4-11e5-ba05-0800279114ca','ig','Igbo','Asụsụ Igbo','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48f66d14-84d4-11e5-ba05-0800279114ca','ik','Inupiaq','Iñupiaq, Iñupiatun','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48fbbe19-84d4-11e5-ba05-0800279114ca','io','Ido','Ido','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('48fe2328-84d4-11e5-ba05-0800279114ca','is','Icelandic','Íslenska','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49035fd7-84d4-11e5-ba05-0800279114ca','it','Italian','italiano','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4905dfb1-84d4-11e5-ba05-0800279114ca','iu','Inuktitut','ᐃᓄᒃᑎᑐᑦ','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49083651-84d4-11e5-ba05-0800279114ca','ja','Japanese','日本語 (にほんご)','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('490ad79c-84d4-11e5-ba05-0800279114ca','jv','Javanese','basa Jawa','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('490d79c7-84d4-11e5-ba05-0800279114ca','kl','Kalaallisut, Greenlandic','kalaallisut, kalaallit oqaasii','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49127b62-84d4-11e5-ba05-0800279114ca','kn','Kannada','ಕನ್ನಡ','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4914f32b-84d4-11e5-ba05-0800279114ca','kr','Kanuri','Kanuri','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('491a1575-84d4-11e5-ba05-0800279114ca','ks','Kashmiri','कश्मीरी, كشميري‎','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('491c9eee-84d4-11e5-ba05-0800279114ca','kk','Kazakh','қазақ тілі','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4921b170-84d4-11e5-ba05-0800279114ca','km','Khmer','ខ្មែរ, ខេមរភាសា, ភាសាខ្មែរ','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4924318c-84d4-11e5-ba05-0800279114ca','ki','Kikuyu, Gikuyu','Gĩkũyũ','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49297cdc-84d4-11e5-ba05-0800279114ca','rw','Kinyarwanda','Ikinyarwanda','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('492be381-84d4-11e5-ba05-0800279114ca','ky','Kyrgyz','Кыргызча, Кыргыз тили','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49310fcb-84d4-11e5-ba05-0800279114ca','kv','Komi','коми кыв','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4933c260-84d4-11e5-ba05-0800279114ca','kg','Kongo','Kikongo','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49389297-84d4-11e5-ba05-0800279114ca','ko','Korean','한국어, 조선어','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('493b1eae-84d4-11e5-ba05-0800279114ca','ku','Kurdish','Kurdî, كوردی‎','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49404c40-84d4-11e5-ba05-0800279114ca','kj','Kwanyama, Kuanyama','Kuanyama','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4942b5ad-84d4-11e5-ba05-0800279114ca','la','Latin','latine, lingua latina','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4947e8b7-84d4-11e5-ba05-0800279114ca','lb','Luxembourgish, Letzeburgesch','Lëtzebuergesch','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('494a67e8-84d4-11e5-ba05-0800279114ca','lg','Ganda','Luganda','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('494f7e38-84d4-11e5-ba05-0800279114ca','li','Limburgish, Limburgan, Limburger','Limburgs','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49525bc3-84d4-11e5-ba05-0800279114ca','ln','Lingala','Lingála','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49572737-84d4-11e5-ba05-0800279114ca','lo','Lao','ພາສາລາວ','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4959c548-84d4-11e5-ba05-0800279114ca','lt','Lithuanian','lietuvių kalba','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('495ecdeb-84d4-11e5-ba05-0800279114ca','lu','Luba-Katanga','Tshiluba','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49613eea-84d4-11e5-ba05-0800279114ca','lv','Latvian','latviešu valoda','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4966673b-84d4-11e5-ba05-0800279114ca','gv','Manx','Gaelg, Gailck','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4968f172-84d4-11e5-ba05-0800279114ca','mk','Macedonian','македонски јазик','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('496e0048-84d4-11e5-ba05-0800279114ca','mg','Malagasy','fiteny malagasy','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49708b6f-84d4-11e5-ba05-0800279114ca','ms','Malay','bahasa Melayu, بهاس ملايو‎','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4975a32c-84d4-11e5-ba05-0800279114ca','ml','Malayalam','മലയാളം','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49783f11-84d4-11e5-ba05-0800279114ca','mt','Maltese','Malti','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('497d52cf-84d4-11e5-ba05-0800279114ca','mi','Māori','te reo Māori','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('497fd798-84d4-11e5-ba05-0800279114ca','mr','Marathi (Marāṭhī)','मराठी','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49850710-84d4-11e5-ba05-0800279114ca','mh','Marshallese','Kajin M̧ajeļ','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49876247-84d4-11e5-ba05-0800279114ca','mn','Mongolian','Монгол хэл','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('498cab01-84d4-11e5-ba05-0800279114ca','na','Nauru','Ekakairũ Naoero','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('498f352c-84d4-11e5-ba05-0800279114ca','nv','Navajo, Navaho','Diné bizaad','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4991f504-84d4-11e5-ba05-0800279114ca','nd','Northern Ndebele','isiNdebele','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('499467b2-84d4-11e5-ba05-0800279114ca','ne','Nepali','नेपाली','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('499967a8-84d4-11e5-ba05-0800279114ca','ng','Ndonga','Owambo','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('499be937-84d4-11e5-ba05-0800279114ca','nb','Norwegian Bokmål','Norsk bokmål','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('499e4fe6-84d4-11e5-ba05-0800279114ca','nn','Norwegian Nynorsk','Norsk nynorsk','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49a38e6b-84d4-11e5-ba05-0800279114ca','no','Norwegian','Norsk','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49a615e7-84d4-11e5-ba05-0800279114ca','ii','Nuosu','ꆈꌠ꒿ Nuosuhxop','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49ab2012-84d4-11e5-ba05-0800279114ca','nr','Southern Ndebele','isiNdebele','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49ad9b78-84d4-11e5-ba05-0800279114ca','oc','Occitan','occitan, lenga d\'òc','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49b2b8a0-84d4-11e5-ba05-0800279114ca','oj','Ojibwe, Ojibwa','ᐊᓂᔑᓈᐯᒧᐎᓐ','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49b53a4c-84d4-11e5-ba05-0800279114ca','cu','Old Church Slavonic, Church Slavonic, Old Bulgarian','ѩзыкъ словѣньскъ','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49ba7ad2-84d4-11e5-ba05-0800279114ca','om','Oromo','Afaan Oromoo','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49bce466-84d4-11e5-ba05-0800279114ca','or','Oriya','ଓଡ଼ିଆ','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49c214f7-84d4-11e5-ba05-0800279114ca','os','Ossetian, Ossetic','ирон æвзаг','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49c4c272-84d4-11e5-ba05-0800279114ca','pa','Panjabi, Punjabi','ਪੰਜਾਬੀ, پنجابی‎','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49c72c45-84d4-11e5-ba05-0800279114ca','pi','Pāli','पाऴि','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49c9aee0-84d4-11e5-ba05-0800279114ca','fa','Persian (Farsi)','فارسی','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49cc3312-84d4-11e5-ba05-0800279114ca','pl','Polish','język polski, polszczyzna','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49cecfdc-84d4-11e5-ba05-0800279114ca','ps','Pashto, Pushto','پښتو','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49d15339-84d4-11e5-ba05-0800279114ca','pt','Portuguese','português','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49d3ef13-84d4-11e5-ba05-0800279114ca','qu','Quechua','Runa Simi, Kichwa','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49d8d721-84d4-11e5-ba05-0800279114ca','rm','Romansh','rumantsch grischun','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49db8787-84d4-11e5-ba05-0800279114ca','rn','Kirundi','Ikirundi','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49e09a53-84d4-11e5-ba05-0800279114ca','ro','Romanian','limba română','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49e315a1-84d4-11e5-ba05-0800279114ca','ru','Russian','Русский','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49e83495-84d4-11e5-ba05-0800279114ca','sa','Sanskrit (Saṁskṛta)','संस्कृतम्','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49eae685-84d4-11e5-ba05-0800279114ca','sc','Sardinian','sardu','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49efcf95-84d4-11e5-ba05-0800279114ca','sd','Sindhi','सिन्धी, سنڌي، سندھی‎','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49f22fdb-84d4-11e5-ba05-0800279114ca','se','Northern Sami','Davvisámegiella','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49f78159-84d4-11e5-ba05-0800279114ca','sm','Samoan','gagana fa\'a Samoa','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49f9efdc-84d4-11e5-ba05-0800279114ca','sg','Sango','yângâ tî sängö','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('49fefae5-84d4-11e5-ba05-0800279114ca','sr','Serbian','српски језик','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a0190a0-84d4-11e5-ba05-0800279114ca','gd','Scottish Gaelic, Gaelic','Gàidhlig','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a06c62b-84d4-11e5-ba05-0800279114ca','sn','Shona','chiShona','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a0bb63a-84d4-11e5-ba05-0800279114ca','si','Sinhala, Sinhalese','සිංහල','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a0e326f-84d4-11e5-ba05-0800279114ca','sk','Slovak','slovenčina, slovenský jazyk','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a10dadb-84d4-11e5-ba05-0800279114ca','sl','Slovene','slovenski jezik, slovenščina','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a15d7cf-84d4-11e5-ba05-0800279114ca','so','Somali','Soomaaliga, af Soomaali','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a185937-84d4-11e5-ba05-0800279114ca','st','Southern Sotho','Sesotho','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a1d88ab-84d4-11e5-ba05-0800279114ca','es','Spanish','español','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a20325b-84d4-11e5-ba05-0800279114ca','su','Sundanese','Basa Sunda','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a255041-84d4-11e5-ba05-0800279114ca','sw','Swahili','Kiswahili','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a27b2bf-84d4-11e5-ba05-0800279114ca','ss','Swati','SiSwati','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a2cf8c4-84d4-11e5-ba05-0800279114ca','sv','Swedish','svenska','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a2f7267-84d4-11e5-ba05-0800279114ca','ta','Tamil','தமிழ்','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a321ce1-84d4-11e5-ba05-0800279114ca','te','Telugu','తెలుగు','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a348bc8-84d4-11e5-ba05-0800279114ca','tg','Tajik','тоҷикӣ, toçikī, تاجیکی‎','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a370562-84d4-11e5-ba05-0800279114ca','th','Thai','ไทย','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a39891c-84d4-11e5-ba05-0800279114ca','ti','Tigrinya','ትግርኛ','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a3bfccb-84d4-11e5-ba05-0800279114ca','bo','Tibetan Standard, Tibetan, Central','བོད་ཡིག','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a3e91b6-84d4-11e5-ba05-0800279114ca','tk','Turkmen','Türkmen, Түркмен','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a43bdfa-84d4-11e5-ba05-0800279114ca','tl','Tagalog','Wikang Tagalog, ᜏᜒᜃᜅ᜔ ᜆᜄᜎᜓᜄ᜔','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a463381-84d4-11e5-ba05-0800279114ca','tn','Tswana','Setswana','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a4b3850-84d4-11e5-ba05-0800279114ca','to','Tonga (Tonga Islands)','faka Tonga','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a4dca8c-84d4-11e5-ba05-0800279114ca','tr','Turkish','Türkçe','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a530300-84d4-11e5-ba05-0800279114ca','ts','Tsonga','Xitsonga','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a55a848-84d4-11e5-ba05-0800279114ca','tt','Tatar','татар теле, tatar tele','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a5aaae9-84d4-11e5-ba05-0800279114ca','tw','Twi','Twi','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a5d1b76-84d4-11e5-ba05-0800279114ca','ty','Tahitian','Reo Tahiti','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a6222a2-84d4-11e5-ba05-0800279114ca','ug','Uyghur','ئۇيغۇرچە‎, Uyghurche','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a64b27b-84d4-11e5-ba05-0800279114ca','uk','Ukrainian','українська мова','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a69b728-84d4-11e5-ba05-0800279114ca','ur','Urdu','اردو','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a6c6308-84d4-11e5-ba05-0800279114ca','uz','Uzbek','Oʻzbek, Ўзбек, أۇزبېك‎','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a71994a-84d4-11e5-ba05-0800279114ca','ve','Venda','Tshivenḓa','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a73f903-84d4-11e5-ba05-0800279114ca','vi','Vietnamese','Tiếng Việt','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a78fec7-84d4-11e5-ba05-0800279114ca','vo','Volapük','Volapük','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a7b8a26-84d4-11e5-ba05-0800279114ca','wa','Walloon','walon','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a80b9aa-84d4-11e5-ba05-0800279114ca','cy','Welsh','Cymraeg','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a8351d7-84d4-11e5-ba05-0800279114ca','wo','Wolof','Wollof','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a8875d8-84d4-11e5-ba05-0800279114ca','fy','Western Frisian','Frysk','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a8ade9b-84d4-11e5-ba05-0800279114ca','xh','Xhosa','isiXhosa','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a8fe430-84d4-11e5-ba05-0800279114ca','yi','Yiddish','ייִדיש','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a92c3da-84d4-11e5-ba05-0800279114ca','yo','Yoruba','Yorùbá','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a97cdad-84d4-11e5-ba05-0800279114ca','za','Zhuang, Chuang','Saɯ cueŋƅ, Saw cuengh','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4a9a14d2-84d4-11e5-ba05-0800279114ca','zu','Zulu','isiZulu','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL);
/*!40000 ALTER TABLE `locale` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `actor_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `row_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `table_name` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `operation` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `previous_row` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log`
--

LOCK TABLES `log` WRITE;
/*!40000 ALTER TABLE `log` DISABLE KEYS */;
/*!40000 ALTER TABLE `log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2017_07_26_164254_create_tables',1),(2,'2017_07_26_174916_make_username_unique_in_user_table',1),(3,'2017_07_26_234640_add_read_only_to_datum_roster_table',1),(4,'2017_07_27_001945_change_condition_id_to_condition_tag_id_in_respondent_condition_tag_table',1),(5,'2017_08_19_212958_drop_datum_roster_table',1),(6,'2017_08_19_214425_add_follow_up_question_id_to_form_section_table',1),(7,'2017_08_19_215440_add_follow_up_datum_id_to_section_condition_tag_table',1),(8,'2017_10_04_023148_add_override_val_to_datum_choice_table',1),(9,'2017_10_10_004649_create_section_skip_table',1),(10,'2017_10_12_212518_add_opt_out_and_opt_out_val_to_datum_table',1),(11,'2017_11_08_145236_create_jobs_table',1),(12,'2017_11_09_151259_create_report_table',1),(13,'2017_11_09_161033_create_failed_jobs_table',1),(14,'2017_11_14_203453_create_report_file_table',1),(15,'2017_11_17_004021_add_can_enumerator_add_child_to_geo_type_table',1),(16,'2017_12_12_003130_create_study_parameter_table',1),(17,'2017_12_12_012623_create_preload_table',1),(18,'2017_12_12_014337_add_preload_id_make_survey_id_nullable_in_datum_table',1),(19,'2017_12_12_204619_create_respondent_fill_table',1),(20,'2018_03_12_185623_create_action_table',1),(21,'2018_03_23_143412_create_sync_table',1),(22,'2018_03_23_144708_create_snapshot_table',1),(23,'2018_04_12_140301_adding_roster_table',1),(24,'2018_04_16_144248_add_assigned_id_to_respondent',1),(25,'2018_04_18_171754_create_self_administered_survey_table',1),(26,'2018_06_07_182317_add_respondent_name_table',1),(27,'2018_06_08_202721_change_study_form_foreign_key',1),(28,'2018_06_21_152801_fix-action-datum-question-datum',1),(29,'2018_07_02_195903_add_respondent_geo_table',1),(30,'2018_07_03_145029_add_form_type_table',1),(31,'2018_07_03_170824_add_associated_respondent_to_respondent_table',1),(32,'2018_07_05_141349_add_census_type_table',1),(33,'2018_07_06_143228_add_zoom_level_to_geo_type',1),(34,'2018_07_17_210226_rename_can_enumerator_add_fields',1),(35,'2018_09_07_183010_create_upload_table',2),(36,'2018_09_11_192423_create_preload_action_table',2),(37,'2018_09_12_132431_add_preload_action_id_to_action_table',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parameter`
--

DROP TABLE IF EXISTS `parameter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parameter` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parameter`
--

LOCK TABLES `parameter` WRITE;
/*!40000 ALTER TABLE `parameter` DISABLE KEYS */;
INSERT INTO `parameter` VALUES ('1','min','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('10','min_relationships','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('11','max_relationships','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('12','min_geos','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('13','max_geos','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('14','min_roster','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('15','max_roster','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('16','exclusive','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('17','can_add_respondent','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('2','max','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('3','other','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('4','none','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('5','read_only','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('6','show_dk','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('7','show_rf','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('8','is_required','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL),('9','geo_type','2017-08-07 20:12:53','2017-08-07 20:12:53',NULL);
/*!40000 ALTER TABLE `parameter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `photo`
--

DROP TABLE IF EXISTS `photo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `photo` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `photo`
--

LOCK TABLES `photo` WRITE;
/*!40000 ALTER TABLE `photo` DISABLE KEYS */;
/*!40000 ALTER TABLE `photo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `photo_tag`
--

DROP TABLE IF EXISTS `photo_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `photo_tag` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tag_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__photo_tag__photo_idx` (`photo_id`),
  KEY `fk__photo_tag__tag_idx` (`tag_id`),
  CONSTRAINT `fk__photo_tag__photo` FOREIGN KEY (`photo_id`) REFERENCES `photo` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__photo_tag__tag` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `photo_tag`
--

LOCK TABLES `photo_tag` WRITE;
/*!40000 ALTER TABLE `photo_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `photo_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `preload`
--

DROP TABLE IF EXISTS `preload`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `preload` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `respondent_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `form_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `study_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_question_id` varchar(41) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `preload_respondent_id_foreign` (`respondent_id`),
  KEY `preload_form_id_foreign` (`form_id`),
  KEY `preload_study_id_foreign` (`study_id`),
  KEY `preload_last_question_id_foreign` (`last_question_id`),
  CONSTRAINT `preload_form_id_foreign` FOREIGN KEY (`form_id`) REFERENCES `form` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `preload_last_question_id_foreign` FOREIGN KEY (`last_question_id`) REFERENCES `question` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  CONSTRAINT `preload_respondent_id_foreign` FOREIGN KEY (`respondent_id`) REFERENCES `respondent` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `preload_study_id_foreign` FOREIGN KEY (`study_id`) REFERENCES `study` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `preload`
--

LOCK TABLES `preload` WRITE;
/*!40000 ALTER TABLE `preload` DISABLE KEYS */;
/*!40000 ALTER TABLE `preload` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `preload_action`
--

DROP TABLE IF EXISTS `preload_action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `preload_action` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` text COLLATE utf8mb4_unicode_ci,
  `respondent_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `preload_action_respondent_id_foreign` (`respondent_id`),
  KEY `preload_action_question_id_foreign` (`question_id`),
  CONSTRAINT `preload_action_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`),
  CONSTRAINT `preload_action_respondent_id_foreign` FOREIGN KEY (`respondent_id`) REFERENCES `respondent` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `preload_action`
--

LOCK TABLES `preload_action` WRITE;
/*!40000 ALTER TABLE `preload_action` DISABLE KEYS */;
INSERT INTO `preload_action` VALUES ('0ddaf5fe-5de6-47e8-8660-54b8c16315bc','add-roster-row','{\"roster_id\":\"768bc97f-9783-4a5a-b401-8ab9b8734336\"}','380cb5c8-10fe-43e5-a927-97f62180db5f','37f6f943-6972-4cdd-8bd5-ae9599034f44','2018-09-18 12:07:07',NULL),('11018e4c-6043-4c03-b41c-f5e8f654b5ba','add-roster-row','{\"roster_id\":\"cc4bfdd3-e977-4508-8bf3-0dcf7e400a7b\"}','380cb5c8-10fe-43e5-a927-97f62180db5f','4cfd9f05-3c74-41eb-8ae5-fe688ab142b5','2018-09-18 12:07:07',NULL),('33d867b9-acaa-472d-96fa-4e7a8024d1b4','add-roster-row','{\"roster_id\":\"e0a0a76d-b7e6-4c81-b1f7-5a3f54f87ef2\"}','380cb5c8-10fe-43e5-a927-97f62180db5f','4cfd9f05-3c74-41eb-8ae5-fe688ab142b5','2018-09-18 12:07:07',NULL),('629ca60e-810d-46ae-9fe7-ffb4e41efa97','add-roster-row','{\"roster_id\":\"b591b522-653e-42fc-8b79-4afd35ca6395\"}','380cb5c8-10fe-43e5-a927-97f62180db5f','37f6f943-6972-4cdd-8bd5-ae9599034f44','2018-09-18 12:07:07',NULL),('729ab127-6e79-4562-8152-fc269fd2118d','add-roster-row','{\"roster_id\":\"f10ef564-f425-475a-9d25-b750c8c695e3\"}','380cb5c8-10fe-43e5-a927-97f62180db5f','4cfd9f05-3c74-41eb-8ae5-fe688ab142b5','2018-09-18 12:07:07',NULL),('acea8c34-4cbe-4498-902a-5754214c60a8','add-roster-row','{\"roster_id\":\"fc8b035f-0b6e-4a6b-8c69-b69beabb7f76\"}','380cb5c8-10fe-43e5-a927-97f62180db5f','343d09a4-5a08-4e41-9dae-630b55bb1bf5','2018-09-18 12:07:07',NULL),('b1d47033-edf7-4b5c-955d-71ee1877d0fd','add-roster-row','{\"roster_id\":\"97e2b5d6-1916-455f-88c6-e75ead1cfdf8\"}','380cb5c8-10fe-43e5-a927-97f62180db5f','37f6f943-6972-4cdd-8bd5-ae9599034f44','2018-09-18 12:07:07',NULL),('da2a7122-dd33-4c62-bb91-5e6aec7f2234','add-roster-row','{\"roster_id\":\"ff7ac9ca-bf86-46ca-b2bc-53d7b721e20a\"}','380cb5c8-10fe-43e5-a927-97f62180db5f','343d09a4-5a08-4e41-9dae-630b55bb1bf5','2018-09-18 12:07:07',NULL),('e01792f3-aaaf-4617-9718-0a46fb8d1e2b','add-roster-row','{\"roster_id\":\"5ca96a1d-e47f-4fcb-90ed-b9955798412c\"}','380cb5c8-10fe-43e5-a927-97f62180db5f','343d09a4-5a08-4e41-9dae-630b55bb1bf5','2018-09-18 12:07:07',NULL);
/*!40000 ALTER TABLE `preload_action` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question`
--

DROP TABLE IF EXISTS `question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_type_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_translation_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_group_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `var_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__question__question_type_idx` (`question_type_id`),
  KEY `fk__question__translation_idx` (`question_translation_id`),
  KEY `fk__question__question_group_idx` (`question_group_id`),
  CONSTRAINT `fk__question__question_group` FOREIGN KEY (`question_group_id`) REFERENCES `question_group` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__question__question_type` FOREIGN KEY (`question_type_id`) REFERENCES `question_type` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__question__translation` FOREIGN KEY (`question_translation_id`) REFERENCES `translation` (`id`) ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question`
--

LOCK TABLES `question` WRITE;
/*!40000 ALTER TABLE `question` DISABLE KEYS */;
INSERT INTO `question` VALUES ('0806c768-aaa1-4fea-bfb4-07598545a9af','cebe05f8-8e17-4c5c-a5fa-abc3a9c6c1f9','7d065622-3109-4758-9d1c-ef061a5a3b72','84068749-31a3-44f4-9f08-19c593fd77d0',2,'q4','2018-09-13 16:41:41','2018-09-13 16:41:41',NULL),('1efaeb1b-1de1-4434-92b9-a7d5eac2e5a1','b58f23fa-52c7-435e-9b31-5fb771e79f41','4626bd46-3bd5-4889-b7b4-8608c2067c5b','ddf1310a-2993-4ef1-9517-16ba9441aac3',1,'yes','2018-09-17 16:35:14','2018-09-17 16:35:14',NULL),('1f7a3147-90f1-43fa-8e96-17f55528ca9d','cebe05f8-8e17-4c5c-a5fa-abc3a9c6c1f9','545d5f8a-3d66-42a6-aefe-1f5b19b50477','af0460fb-f9b0-4f4b-861d-88b4c5f1206d',1,'q1','2018-09-13 16:41:01','2018-09-13 16:41:01',NULL),('343d09a4-5a08-4e41-9dae-630b55bb1bf5','5ae659b6-8945-4adc-86d5-a44b51531def','eb950f9c-dc28-4f70-9785-da6c141b3952','ba6eeec4-e84e-45b2-919f-88df3cbc1ff2',1,'prefill_first_question','2018-09-17 16:34:06','2018-09-17 16:34:06',NULL),('37f6f943-6972-4cdd-8bd5-ae9599034f44','5ae659b6-8945-4adc-86d5-a44b51531def','f3d6aa21-c9fb-4871-9ab5-c9d5792234dc','4bed36a2-09cd-43e1-a766-a9d8d592bbe3',1,'last_question','2018-09-17 16:35:48','2018-09-17 16:35:48',NULL),('49ef53ee-bff2-4d66-9f1b-b816ae6a1e3d','0f76b96f-613a-4925-bacd-74db45368edb','6ee07d0f-d7f8-4947-9546-49cb6f6a32ca','ebf9af64-9c9e-4655-afbe-aa2c2a652ca9',1,'dog_type','2018-09-07 14:59:48','2018-09-07 14:59:48',NULL),('4cfd9f05-3c74-41eb-8ae5-fe688ab142b5','5ae659b6-8945-4adc-86d5-a44b51531def','3f1ba7b0-50be-46d7-a45b-ffb50d796cbd','6ec54af7-4ca7-44fe-a993-618893d97e81',1,'Middle question roster prefill test','2018-09-17 16:34:57','2018-09-17 16:34:57',NULL),('72c56240-aeb8-4068-9a8c-5ffa4ad99e18','5ae659b6-8945-4adc-86d5-a44b51531def','979ea7f2-c3e9-4115-aae6-accc43d5f155','ecc26ed8-5699-41ce-a675-b23f7df86a63',1,'pets','2018-09-07 14:55:56','2018-09-07 14:55:56',NULL),('7e02ce88-b095-491d-a40e-af0b099de1d0','cebe05f8-8e17-4c5c-a5fa-abc3a9c6c1f9','3cec547c-ec50-480c-b322-042de097b12d','937bbb86-6f63-4379-8ead-64957a35b8b5',1,'intro','2018-09-07 14:53:56','2018-09-07 14:53:56',NULL),('7fb928ab-73a6-48ec-935d-dc7920a50c8f','cebe05f8-8e17-4c5c-a5fa-abc3a9c6c1f9','6f193843-926e-4898-9a26-7ac2f9046ea5','57b30a00-f6e7-46f2-affc-1a74efc28118',1,'q5','2018-09-13 16:42:16','2018-09-13 16:42:16',NULL),('82a65354-e273-431d-9f44-7964a864fd20','2d3ff07a-5ab1-4da0-aa7f-440cf8cd0980','b4168caf-cd7f-45a8-aace-43c61eb97ef5','8a11e491-612a-4291-b270-24244d3da016',1,'skipped_question','2018-09-12 13:11:55','2018-09-12 13:11:55',NULL),('8a737a88-1dbb-433b-a2bd-ad094b1b2112','cebe05f8-8e17-4c5c-a5fa-abc3a9c6c1f9','bb6b251e-2a7e-48a3-9a68-c9fa7f4568d2','3a847ce1-c12f-459e-b7d9-70212542994b',1,'q2','2018-09-13 16:41:13','2018-09-13 16:41:13',NULL),('8ffe4518-5a9b-43a3-b1eb-6aa842e9d815','cebe05f8-8e17-4c5c-a5fa-abc3a9c6c1f9','18169029-6827-45f9-9da3-45879b05cd47','dc768d39-6479-4eaf-a62e-3f19db166941',1,'wow','2018-09-12 13:14:04','2018-09-12 13:14:04',NULL),('9e466c1d-eb37-4846-975b-355b667163f5','cebe05f8-8e17-4c5c-a5fa-abc3a9c6c1f9','f834fd24-848e-4d81-a7f2-6808989687c5','28cc048b-8579-4235-91a4-1ccd99d72d49',1,'adslkjf','2018-09-17 16:34:24','2018-09-17 16:34:34',NULL),('ac218c92-70ca-474c-8d5f-a25d429cc8d4','b58f23fa-52c7-435e-9b31-5fb771e79f41','4df2cff0-9cae-471e-a8f7-52b7d8124f0c','7c4e6cc3-d9e2-4d8b-8064-dd91ac6c7a77',1,'assigner','2018-09-12 16:35:44','2018-09-12 16:36:43',NULL),('b4fe5f93-67f7-45d7-9cb3-38f5e5be4663','cebe05f8-8e17-4c5c-a5fa-abc3a9c6c1f9','f99d59f6-e633-4905-8402-0ef14a7d4f5d','84068749-31a3-44f4-9f08-19c593fd77d0',1,'q3','2018-09-13 16:41:33','2018-09-13 16:41:33',NULL),('bd5aa795-b6b4-419d-b50e-b036fc55408c','b58f23fa-52c7-435e-9b31-5fb771e79f41','003ab718-6d41-45b6-bc7f-4d6de1bb12bc','e5f5ddbb-57fc-48d3-b9c5-e9bf3937139d',1,'skipped_maybe','2018-09-12 16:37:59','2018-09-12 16:37:59',NULL),('e103a32a-47bb-4fd1-b1c1-98dd8e522df0','0f76b96f-613a-4925-bacd-74db45368edb','ddee1291-042a-484c-b66f-7592cebe0038','e2247ebf-09dc-4d4e-a60e-11e80bb74555',1,'ms','2018-09-07 14:54:24','2018-09-07 14:54:24',NULL),('e10f842f-9513-4be0-89e6-6c9f871f2d35','cebe05f8-8e17-4c5c-a5fa-abc3a9c6c1f9','7bd311c9-c945-4911-be7e-ed81b61fddf7','f0f6109c-8e47-4e80-a081-b7d0a58e33c8',1,'last one','2018-09-12 16:40:21','2018-09-12 16:40:21',NULL),('e978196d-95de-4137-b164-4e2e32f78cc6','b58f23fa-52c7-435e-9b31-5fb771e79f41','beb82ce1-67a5-4ac5-aaa8-ecbf0043faf4','27345e55-ed2e-4397-b492-808490ca21e0',1,'pet_type','2018-09-07 14:56:41','2018-09-07 14:56:41',NULL),('f7e46b66-5f41-46b6-9d69-74cefba4f844','0f76b96f-613a-4925-bacd-74db45368edb','8b68bc2f-5740-4a1d-834e-08bd75b7d0f2','ebf9af64-9c9e-4655-afbe-aa2c2a652ca9',2,'dog_type','2018-09-07 14:59:49','2018-09-07 15:02:24','2018-09-07 15:02:24');
/*!40000 ALTER TABLE `question` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question_assign_condition_tag`
--

DROP TABLE IF EXISTS `question_assign_condition_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question_assign_condition_tag` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `assign_condition_tag_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__question_assign_condition_tag__question_idx` (`question_id`),
  KEY `fk__question_assign_condition_tag__assign_condition_tag_idx` (`assign_condition_tag_id`),
  CONSTRAINT `fk__question_assign_condition_tag__assign_condition_tag` FOREIGN KEY (`assign_condition_tag_id`) REFERENCES `assign_condition_tag` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__question_assign_condition_tag__question` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_assign_condition_tag`
--

LOCK TABLES `question_assign_condition_tag` WRITE;
/*!40000 ALTER TABLE `question_assign_condition_tag` DISABLE KEYS */;
INSERT INTO `question_assign_condition_tag` VALUES ('0d6199db-1d72-44e8-91f3-ded029949cf5','8ffe4518-5a9b-43a3-b1eb-6aa842e9d815','b08174b3-9c59-4fea-9084-bb228a9b904a','2018-09-12 13:14:39','2018-09-12 13:14:39',NULL),('20a6c8ab-eaae-4a82-be60-a0d58f8d7645','ac218c92-70ca-474c-8d5f-a25d429cc8d4','1654f44f-fe57-4170-afcd-dc2adeb55574','2018-09-12 16:36:25','2018-09-12 16:36:25',NULL),('4dcc683b-8d28-4ce6-8fe8-b463d56636ac','bd5aa795-b6b4-419d-b50e-b036fc55408c','dfc8a8c4-fcae-4ba0-9b34-8e9af5a5e82d','2018-09-12 16:41:08','2018-09-12 16:41:08',NULL),('b2bc5aa9-1dd5-4644-b8cf-1914455fa4da','e978196d-95de-4137-b164-4e2e32f78cc6','9b51a9cc-6cbc-43e2-a05c-510495924423','2018-09-07 15:00:56','2018-09-07 15:00:56',NULL),('bbd6904f-374b-46e8-90b0-693eda0ac930','ac218c92-70ca-474c-8d5f-a25d429cc8d4','067c4ee7-9553-43d6-8768-347387dfdf89','2018-09-12 16:38:12','2018-09-12 16:38:12',NULL);
/*!40000 ALTER TABLE `question_assign_condition_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question_choice`
--

DROP TABLE IF EXISTS `question_choice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question_choice` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `choice_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__question_choice__question_idx` (`question_id`),
  KEY `fk__question_choice__choice_idx` (`choice_id`),
  CONSTRAINT `fk__question_choice__choice` FOREIGN KEY (`choice_id`) REFERENCES `choice` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__question_choice__question` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_choice`
--

LOCK TABLES `question_choice` WRITE;
/*!40000 ALTER TABLE `question_choice` DISABLE KEYS */;
INSERT INTO `question_choice` VALUES ('0490b034-b0a0-4e8c-89d1-a0836f4f887d','e103a32a-47bb-4fd1-b1c1-98dd8e522df0','036eacf6-4618-43ef-99f8-526043ac7c2d',2,'2018-09-07 14:54:31','2018-09-07 14:54:31',NULL),('1baacb5f-f129-49cd-9764-0d21339b44fb','49ef53ee-bff2-4d66-9f1b-b816ae6a1e3d','02170a9c-139c-4cfc-ac35-57ef9c602396',2,'2018-09-07 14:59:56','2018-09-07 14:59:56',NULL),('3be1c2db-94cd-45d0-932a-ce4fe248c460','e103a32a-47bb-4fd1-b1c1-98dd8e522df0','9efca378-a58c-4934-ad75-951e2310c797',1,'2018-09-07 14:54:27','2018-09-07 14:54:27',NULL),('4b34ec2f-7b8c-44fa-9b0c-8872ce8cbaff','e978196d-95de-4137-b164-4e2e32f78cc6','cf1a9059-a8ab-4084-a426-2d520b962a5e',2,'2018-09-07 14:56:57','2018-09-07 14:56:57',NULL),('779ff077-8dc4-4011-8453-0834c7de9ada','e978196d-95de-4137-b164-4e2e32f78cc6','cef52a1f-ed2e-472d-9975-0a12fdb2d005',1,'2018-09-07 14:56:53','2018-09-07 14:56:53',NULL),('7a62a759-aab5-43fb-b738-657798026870','e103a32a-47bb-4fd1-b1c1-98dd8e522df0','aac56b68-5f7c-4f4e-93e9-b7f58e7e40c7',3,'2018-09-07 14:54:48','2018-09-07 14:54:48',NULL),('91f0fb9e-4553-43ba-9625-f62227bd00c7','ac218c92-70ca-474c-8d5f-a25d429cc8d4','7b5ef540-ec76-4adc-acb5-11d40d802eb1',1,'2018-09-12 16:35:46','2018-09-12 16:35:46',NULL),('9a2c2ceb-7dd0-48cb-8053-f136dc5ee5df','49ef53ee-bff2-4d66-9f1b-b816ae6a1e3d','404d56e3-4bb5-4139-a1c4-6f9d2b56d13a',1,'2018-09-07 14:59:54','2018-09-07 14:59:54',NULL),('9b22680a-626b-47e5-b92d-d96cccd14f68','bd5aa795-b6b4-419d-b50e-b036fc55408c','539b94da-fbbd-42fc-b840-c18aefeb05ce',2,'2018-09-12 16:38:03','2018-09-12 16:38:03',NULL),('c9deeb5e-c8fd-4af0-a6c2-4c442c2c0ead','1efaeb1b-1de1-4434-92b9-a7d5eac2e5a1','7027e80e-cffa-412f-a905-0dde32d2a2ef',1,'2018-09-17 16:35:16','2018-09-17 16:35:16',NULL),('d17765ba-5a02-4b40-b938-22afa3c33519','bd5aa795-b6b4-419d-b50e-b036fc55408c','055f0dc5-3d61-442c-b002-1c8bfff70fa5',1,'2018-09-12 16:38:02','2018-09-12 16:38:02',NULL),('d9eaae88-7070-4bc0-9bbe-afb2d71c9497','49ef53ee-bff2-4d66-9f1b-b816ae6a1e3d','f81bed36-4c11-443e-89a6-b847fb44fc78',3,'2018-09-07 15:00:20','2018-09-07 15:00:20',NULL),('e9d10bf6-52c6-47af-8d93-87865dfd2d8a','ac218c92-70ca-474c-8d5f-a25d429cc8d4','d47d622b-061a-4d14-9c47-db251f7ea953',2,'2018-09-12 16:35:48','2018-09-12 16:35:48',NULL),('eb16cf5c-991e-43e7-955f-b27da223fa59','e978196d-95de-4137-b164-4e2e32f78cc6','3647af87-d56d-4a60-96e8-46391cbdd289',4,'2018-09-07 14:58:54','2018-09-07 14:58:54',NULL),('f93e11d9-41d3-4a4e-ab33-afdc1bc13480','e978196d-95de-4137-b164-4e2e32f78cc6','d82cc954-38e3-4386-8826-c9e4f443f4c0',3,'2018-09-07 14:58:22','2018-09-07 14:58:22',NULL);
/*!40000 ALTER TABLE `question_choice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question_datum`
--

DROP TABLE IF EXISTS `question_datum`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question_datum` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `section_repetition` int(11) NOT NULL,
  `follow_up_datum_id` varchar(41) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `question_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `survey_id` varchar(41) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `answered_at` datetime DEFAULT NULL,
  `skipped_at` datetime DEFAULT NULL,
  `dk_rf` tinyint(1) DEFAULT NULL,
  `dk_rf_val` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__question_datum__datum_idx` (`follow_up_datum_id`),
  KEY `fk__question_datum__question_idx` (`question_id`),
  KEY `fk__question_datum__survey_idx` (`survey_id`),
  CONSTRAINT `question_datum_follow_up_datum_id_foreign` FOREIGN KEY (`follow_up_datum_id`) REFERENCES `datum` (`id`),
  CONSTRAINT `question_datum_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`),
  CONSTRAINT `question_datum_survey_id_foreign` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_datum`
--

LOCK TABLES `question_datum` WRITE;
/*!40000 ALTER TABLE `question_datum` DISABLE KEYS */;
INSERT INTO `question_datum` VALUES ('4016e60f-1a70-4b7c-8181-63f0d9971d31',0,NULL,'7e02ce88-b095-491d-a40e-af0b099de1d0','012aef54-40bc-4a99-9bdd-d0bf20fcc72c','2018-09-07 15:36:04','2018-09-07 15:36:04',NULL,NULL,'2018-09-07 15:36:04','2018-09-07 15:36:04',NULL),('8d570cfd-c0a0-44d4-9719-f0713cdee186',0,NULL,'72c56240-aeb8-4068-9a8c-5ffa4ad99e18','012aef54-40bc-4a99-9bdd-d0bf20fcc72c','2018-09-07 15:44:12','2018-09-07 15:44:12',NULL,NULL,'2018-09-07 15:44:12','2018-09-07 15:44:12',NULL),('a58e9a73-b386-4cb6-88be-08a10df3f987',0,NULL,'e103a32a-47bb-4fd1-b1c1-98dd8e522df0','012aef54-40bc-4a99-9bdd-d0bf20fcc72c','2018-09-07 15:43:52','2018-09-07 15:43:52',NULL,NULL,'2018-09-07 15:43:52','2018-09-07 15:43:52',NULL);
/*!40000 ALTER TABLE `question_datum` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question_group`
--

DROP TABLE IF EXISTS `question_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question_group` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_group`
--

LOCK TABLES `question_group` WRITE;
/*!40000 ALTER TABLE `question_group` DISABLE KEYS */;
INSERT INTO `question_group` VALUES ('27345e55-ed2e-4397-b492-808490ca21e0','2018-09-07 14:56:12','2018-09-07 14:56:12',NULL),('28cc048b-8579-4235-91a4-1ccd99d72d49','2018-09-17 16:34:12','2018-09-17 16:34:12',NULL),('3a847ce1-c12f-459e-b7d9-70212542994b','2018-09-13 16:41:04','2018-09-13 16:41:04',NULL),('4bed36a2-09cd-43e1-a766-a9d8d592bbe3','2018-09-17 16:35:29','2018-09-17 16:35:29',NULL),('57b30a00-f6e7-46f2-affc-1a74efc28118','2018-09-13 16:42:04','2018-09-13 16:42:04',NULL),('6ec54af7-4ca7-44fe-a993-618893d97e81','2018-09-17 16:34:35','2018-09-17 16:34:35',NULL),('7c4e6cc3-d9e2-4d8b-8064-dd91ac6c7a77','2018-09-12 16:35:16','2018-09-12 16:35:16',NULL),('84068749-31a3-44f4-9f08-19c593fd77d0','2018-09-13 16:41:23','2018-09-13 16:41:23',NULL),('8a11e491-612a-4291-b270-24244d3da016','2018-09-12 13:11:34','2018-09-12 13:11:34',NULL),('937bbb86-6f63-4379-8ead-64957a35b8b5','2018-09-07 14:53:35','2018-09-07 14:53:35',NULL),('af0460fb-f9b0-4f4b-861d-88b4c5f1206d','2018-09-13 16:40:48','2018-09-13 16:40:48',NULL),('ba6eeec4-e84e-45b2-919f-88df3cbc1ff2','2018-09-17 16:33:47','2018-09-17 16:33:47',NULL),('dc768d39-6479-4eaf-a62e-3f19db166941','2018-09-12 13:13:50','2018-09-12 13:13:50',NULL),('ddf1310a-2993-4ef1-9517-16ba9441aac3','2018-09-17 16:34:59','2018-09-17 16:34:59',NULL),('e2247ebf-09dc-4d4e-a60e-11e80bb74555','2018-09-07 14:54:01','2018-09-07 14:54:01',NULL),('e5f5ddbb-57fc-48d3-b9c5-e9bf3937139d','2018-09-12 16:37:31','2018-09-12 16:37:31',NULL),('ebf9af64-9c9e-4655-afbe-aa2c2a652ca9','2018-09-07 14:59:26','2018-09-07 14:59:26',NULL),('ecc26ed8-5699-41ce-a675-b23f7df86a63','2018-09-07 14:55:33','2018-09-07 14:55:33',NULL),('f0f6109c-8e47-4e80-a081-b7d0a58e33c8','2018-09-12 16:40:10','2018-09-12 16:40:10',NULL);
/*!40000 ALTER TABLE `question_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question_group_skip`
--

DROP TABLE IF EXISTS `question_group_skip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question_group_skip` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_group_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `skip_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__question_group_skip__question_group_idx` (`question_group_id`),
  KEY `fk__form_skip__skip_idx` (`skip_id`),
  CONSTRAINT `fk__question_group_skip__question_group` FOREIGN KEY (`question_group_id`) REFERENCES `question_group` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__question_group_skip__skip` FOREIGN KEY (`skip_id`) REFERENCES `skip` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_group_skip`
--

LOCK TABLES `question_group_skip` WRITE;
/*!40000 ALTER TABLE `question_group_skip` DISABLE KEYS */;
INSERT INTO `question_group_skip` VALUES ('5272b384-0ab0-4fc7-8df0-19bca21d6e01','ebf9af64-9c9e-4655-afbe-aa2c2a652ca9','d3e8d3ff-277b-4f45-9186-fe38338f1d08','2018-09-07 15:01:44','2018-09-07 15:01:44',NULL),('6f2c6b7d-a719-4449-b194-9befdfe721f3','e5f5ddbb-57fc-48d3-b9c5-e9bf3937139d','f97e8c83-f2be-4042-8f5f-d570beeb48cc','2018-09-12 16:40:38','2018-09-12 16:40:38',NULL),('9c3eacac-09a3-49af-bc7f-efd1cdd09a4d','8a11e491-612a-4291-b270-24244d3da016','06e38d4d-20fb-4bdd-877f-91546a8d0835','2018-09-12 13:13:08','2018-09-12 13:13:08',NULL);
/*!40000 ALTER TABLE `question_group_skip` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question_parameter`
--

DROP TABLE IF EXISTS `question_parameter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question_parameter` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parameter_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `val` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__question_parameter__question_idx` (`question_id`),
  KEY `fk__question_parameter__parameter_idx` (`parameter_id`),
  CONSTRAINT `fk__question_parameter__parameter` FOREIGN KEY (`parameter_id`) REFERENCES `parameter` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__question_parameter__question` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_parameter`
--

LOCK TABLES `question_parameter` WRITE;
/*!40000 ALTER TABLE `question_parameter` DISABLE KEYS */;
INSERT INTO `question_parameter` VALUES ('697589aa-e2da-456e-b90e-bebb062c807c','e978196d-95de-4137-b164-4e2e32f78cc6','3','other','2018-09-07 14:57:29','2018-09-07 14:58:39',NULL),('854ed5bd-132c-4b71-90f7-158d3bb4cd15','49ef53ee-bff2-4d66-9f1b-b816ae6a1e3d','16','other','2018-09-07 15:00:35','2018-09-07 15:00:35',NULL),('d176a65a-13bf-41c1-a4aa-45202d9b4f3f','49ef53ee-bff2-4d66-9f1b-b816ae6a1e3d','3','other','2018-09-07 15:00:31','2018-09-07 15:00:31',NULL),('e8fe4ee7-62a7-4cdd-8e0a-f26940e72008','e978196d-95de-4137-b164-4e2e32f78cc6','16','1','2018-09-07 14:57:16','2018-09-07 14:58:43','2018-09-07 14:58:43');
/*!40000 ALTER TABLE `question_parameter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question_type`
--

DROP TABLE IF EXISTS `question_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question_type` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_type`
--

LOCK TABLES `question_type` WRITE;
/*!40000 ALTER TABLE `question_type` DISABLE KEYS */;
INSERT INTO `question_type` VALUES ('06162912-8048-4978-a8d2-92b6dd0c2ed1','time','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),('0f76b96f-613a-4925-bacd-74db45368edb','multiple_select','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),('1e9e577d-524c-4af1-bd70-26b561e14710','image','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),('2ab4a309-5c65-4eec-a044-c75a89ba25f1','relationship','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),('2d3ff07a-5ab1-4da0-aa7f-440cf8cd0980','integer','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),('312533dd-5957-453c-ab00-691f869d257f','decimal','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),('49c03474-cbe8-4f4c-ab10-6491f936338f','group','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),('5ae659b6-8945-4adc-86d5-a44b51531def','roster','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),('948ffae0-bfb3-4cf1-a3e9-b4845181cb61','text','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),('99e769a7-c2b3-41ae-98a3-9b7afbfc4a45','text_area','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),('b58f23fa-52c7-435e-9b31-5fb771e79f41','multiple_choice','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),('c35db71d-cb10-49c7-909c-e67a9a29e736','geo','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),('cebe05f8-8e17-4c5c-a5fa-abc3a9c6c1f9','intro','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),('d566e086-c95e-45aa-9b3f-e88cb1802081','year','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),('d840f8cb-b68b-432a-9a47-2b0b5dc65377','year_month','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),('db1192c9-a850-4427-ad67-388f6325fd23','respondent_geo','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),('efbafb7c-62ca-4ed9-92df-7d171e855650','year_month_day','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL),('effab4ce-df07-459d-a2a4-25be77bcca1b','year_month_day_time','2015-11-24 12:01:48','2015-11-24 12:01:48',NULL);
/*!40000 ALTER TABLE `question_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report`
--

DROP TABLE IF EXISTS `report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `report_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report`
--

LOCK TABLES `report` WRITE;
/*!40000 ALTER TABLE `report` DISABLE KEYS */;
/*!40000 ALTER TABLE `report` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_file`
--

DROP TABLE IF EXISTS `report_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_file` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `report_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `report_file_report_id_foreign` (`report_id`),
  CONSTRAINT `report_file_report_id_foreign` FOREIGN KEY (`report_id`) REFERENCES `report` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_file`
--

LOCK TABLES `report_file` WRITE;
/*!40000 ALTER TABLE `report_file` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `respondent`
--

DROP TABLE IF EXISTS `respondent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `respondent` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `assigned_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `geo_id` varchar(41) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `geo_notes` text COLLATE utf8mb4_unicode_ci,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `associated_respondent_id` varchar(41) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__respondent__geo_idx` (`geo_id`),
  KEY `fk__respondent_associated_respondent__idx` (`associated_respondent_id`),
  CONSTRAINT `fk__respondent__geo` FOREIGN KEY (`geo_id`) REFERENCES `geo` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  CONSTRAINT `fk__respondent_associated_respondent__idx` FOREIGN KEY (`associated_respondent_id`) REFERENCES `respondent` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `respondent`
--

LOCK TABLES `respondent` WRITE;
/*!40000 ALTER TABLE `respondent` DISABLE KEYS */;
INSERT INTO `respondent` VALUES ('380cb5c8-10fe-43e5-a927-97f62180db5f',NULL,NULL,NULL,NULL,'Test Prefill Respondent 1','2018-09-17 16:31:32','2018-09-17 16:31:32',NULL,NULL),('6905448f-4356-40f0-b78d-0cc7c05acfdd',NULL,NULL,NULL,NULL,'Test respondent 3','2018-09-07 15:02:50','2018-09-07 15:02:50',NULL,NULL),('98926f3c-a9e2-4de5-8624-6ecf6ab128b6',NULL,NULL,NULL,NULL,'Test respondent 2','2018-09-07 15:02:43','2018-09-07 15:02:43',NULL,NULL),('b4be2718-66c9-4d95-9518-d81ac7a29cbc',NULL,NULL,NULL,NULL,'Test respondent 1','2018-09-07 15:02:37','2018-09-07 15:02:37',NULL,NULL);
/*!40000 ALTER TABLE `respondent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `respondent_condition_tag`
--

DROP TABLE IF EXISTS `respondent_condition_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `respondent_condition_tag` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `respondent_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `condition_tag_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__respondent_condition__respondent_idx` (`respondent_id`),
  KEY `fk__respondent_condition_tag__condition_tag` (`condition_tag_id`),
  CONSTRAINT `fk__respondent_condition_tag__condition_tag` FOREIGN KEY (`condition_tag_id`) REFERENCES `condition_tag` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__respondent_condition_tag__respondent` FOREIGN KEY (`respondent_id`) REFERENCES `respondent` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `respondent_condition_tag`
--

LOCK TABLES `respondent_condition_tag` WRITE;
/*!40000 ALTER TABLE `respondent_condition_tag` DISABLE KEYS */;
INSERT INTO `respondent_condition_tag` VALUES ('8b7ee1cf-2a05-43db-8b60-70a4abf31081','b4be2718-66c9-4d95-9518-d81ac7a29cbc','2018-09-07 15:35:00','2018-09-07 15:35:00',NULL,'c132adce-84ef-4be0-baca-b2b22bb7c28b'),('ec9efacb-72a2-418b-9cf9-cb2be93de59e','b4be2718-66c9-4d95-9518-d81ac7a29cbc','2018-09-07 15:34:21','2018-09-07 15:34:21',NULL,'1f0629de-e256-49d7-818b-67622c4f9c9a');
/*!40000 ALTER TABLE `respondent_condition_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `respondent_fill`
--

DROP TABLE IF EXISTS `respondent_fill`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `respondent_fill` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `respondent_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `val` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `respondent_fill_respondent_id_foreign` (`respondent_id`),
  CONSTRAINT `respondent_fill_respondent_id_foreign` FOREIGN KEY (`respondent_id`) REFERENCES `respondent` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `respondent_fill`
--

LOCK TABLES `respondent_fill` WRITE;
/*!40000 ALTER TABLE `respondent_fill` DISABLE KEYS */;
/*!40000 ALTER TABLE `respondent_fill` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `respondent_geo`
--

DROP TABLE IF EXISTS `respondent_geo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `respondent_geo` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `geo_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `respondent_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `previous_respondent_geo_id` varchar(41) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_current` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `respondent_geo_geo_id_foreign` (`geo_id`),
  KEY `respondent_geo_respondent_id_foreign` (`respondent_id`),
  KEY `respondent_geo_previous_respondent_geo_id_foreign` (`previous_respondent_geo_id`),
  CONSTRAINT `respondent_geo_geo_id_foreign` FOREIGN KEY (`geo_id`) REFERENCES `geo` (`id`),
  CONSTRAINT `respondent_geo_previous_respondent_geo_id_foreign` FOREIGN KEY (`previous_respondent_geo_id`) REFERENCES `respondent_geo` (`id`),
  CONSTRAINT `respondent_geo_respondent_id_foreign` FOREIGN KEY (`respondent_id`) REFERENCES `respondent` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `respondent_geo`
--

LOCK TABLES `respondent_geo` WRITE;
/*!40000 ALTER TABLE `respondent_geo` DISABLE KEYS */;
INSERT INTO `respondent_geo` VALUES ('559f5a71-f094-43b0-bc09-71def5a76c16','3a1dc8cb-b49a-459b-b023-68240597ed4e','b4be2718-66c9-4d95-9518-d81ac7a29cbc',NULL,NULL,0,NULL,'2018-09-14 14:12:57','2018-09-14 14:12:57');
/*!40000 ALTER TABLE `respondent_geo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `respondent_group_tag`
--

DROP TABLE IF EXISTS `respondent_group_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `respondent_group_tag` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `respondent_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_tag_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__respondent_group__respondent_idx` (`respondent_id`),
  KEY `fk__respondent_group__group_idx` (`group_tag_id`),
  CONSTRAINT `fk__respondent_group_tag__group_tag` FOREIGN KEY (`group_tag_id`) REFERENCES `group_tag` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__respondent_group_tag__respondent` FOREIGN KEY (`respondent_id`) REFERENCES `respondent` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `respondent_group_tag`
--

LOCK TABLES `respondent_group_tag` WRITE;
/*!40000 ALTER TABLE `respondent_group_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `respondent_group_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `respondent_name`
--

DROP TABLE IF EXISTS `respondent_name`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `respondent_name` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_display_name` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `respondent_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `locale_id` varchar(41) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `previous_respondent_name_id` varchar(41) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `respondent_name_respondent_id_foreign` (`respondent_id`),
  KEY `respondent_name_locale_id_foreign` (`locale_id`),
  KEY `respondent_name_previous_respondent_name_id_foreign` (`previous_respondent_name_id`),
  CONSTRAINT `respondent_name_locale_id_foreign` FOREIGN KEY (`locale_id`) REFERENCES `locale` (`id`),
  CONSTRAINT `respondent_name_previous_respondent_name_id_foreign` FOREIGN KEY (`previous_respondent_name_id`) REFERENCES `respondent_name` (`id`),
  CONSTRAINT `respondent_name_respondent_id_foreign` FOREIGN KEY (`respondent_id`) REFERENCES `respondent` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `respondent_name`
--

LOCK TABLES `respondent_name` WRITE;
/*!40000 ALTER TABLE `respondent_name` DISABLE KEYS */;
INSERT INTO `respondent_name` VALUES ('0d7945b5-3f89-4cc6-a2b0-d278d7c96aa9',0,'Test respondent 1 nickname','b4be2718-66c9-4d95-9518-d81ac7a29cbc',NULL,NULL,'2018-09-07 15:34:34','2018-09-07 15:34:34',NULL),('148d2f88-f147-46aa-a4ac-76377bcd7cbd',1,'Test respondent 3','6905448f-4356-40f0-b78d-0cc7c05acfdd',NULL,NULL,'2018-09-07 15:02:50','2018-09-07 15:02:50',NULL),('66f69969-ebe0-4925-8690-a2056a0e2be2',1,'Test respondent 1','b4be2718-66c9-4d95-9518-d81ac7a29cbc',NULL,NULL,'2018-09-07 15:02:37','2018-09-07 15:02:37',NULL),('f4d422e2-3108-45c7-8d19-07f8e35a729b',1,'Test Prefill Respondent 1','380cb5c8-10fe-43e5-a927-97f62180db5f',NULL,NULL,'2018-09-17 16:31:32','2018-09-17 16:31:32',NULL),('f92c0ed0-8d12-4887-8945-17588045773e',1,'Test respondent 2','98926f3c-a9e2-4de5-8624-6ecf6ab128b6',NULL,NULL,'2018-09-07 15:02:43','2018-09-07 15:02:43',NULL);
/*!40000 ALTER TABLE `respondent_name` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `respondent_photo`
--

DROP TABLE IF EXISTS `respondent_photo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `respondent_photo` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `respondent_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` tinyint(3) unsigned NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__respondent_photo__respondent_idx` (`respondent_id`),
  KEY `fk__respondent_photo__photo_idx` (`photo_id`),
  CONSTRAINT `fk__respondent_photo__photo` FOREIGN KEY (`photo_id`) REFERENCES `photo` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__respondent_photo__respondent` FOREIGN KEY (`respondent_id`) REFERENCES `respondent` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `respondent_photo`
--

LOCK TABLES `respondent_photo` WRITE;
/*!40000 ALTER TABLE `respondent_photo` DISABLE KEYS */;
/*!40000 ALTER TABLE `respondent_photo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roster`
--

DROP TABLE IF EXISTS `roster`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roster` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `val` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roster`
--

LOCK TABLES `roster` WRITE;
/*!40000 ALTER TABLE `roster` DISABLE KEYS */;
INSERT INTO `roster` VALUES ('03f80960-c777-4222-a10b-2bcd8876d936','second','0000-00-00 00:00:00','0000-00-00 00:00:00',NULL),('11180881-6920-4104-9be9-a57800e4b2f6','first','0000-00-00 00:00:00','0000-00-00 00:00:00',NULL),('1c257b6b-688b-4ec6-9804-12b0472eff59','not edited','0000-00-00 00:00:00','2018-09-14 14:13:06',NULL),('2978724b-c2a4-4598-93a5-520e42a8cd66','yogi','0000-00-00 00:00:00','0000-00-00 00:00:00',NULL),('5ca96a1d-e47f-4fcb-90ed-b9955798412c','intial_prefill_2','2018-09-18 12:07:07','2018-09-18 12:07:07',NULL),('5e9aa6ca-e89d-4c4f-aa74-3a6a84d08248','third','0000-00-00 00:00:00','0000-00-00 00:00:00',NULL),('768bc97f-9783-4a5a-b401-8ab9b8734336','last_prefill_3','2018-09-18 12:07:07','2018-09-18 12:07:07',NULL),('97e2b5d6-1916-455f-88c6-e75ead1cfdf8','last_prefill_1','2018-09-18 12:07:07','2018-09-18 12:07:07',NULL),('b591b522-653e-42fc-8b79-4afd35ca6395','last_prefill_2','2018-09-18 12:07:07','2018-09-18 12:07:07',NULL),('cc4bfdd3-e977-4508-8bf3-0dcf7e400a7b','middle_prefill_3','2018-09-18 12:07:07','2018-09-18 12:07:07',NULL),('e0a0a76d-b7e6-4c81-b1f7-5a3f54f87ef2','middle_prefill_2','2018-09-18 12:07:07','2018-09-18 12:07:07',NULL),('f10ef564-f425-475a-9d25-b750c8c695e3','middle_prefill_1','2018-09-18 12:07:07','2018-09-18 12:07:07',NULL),('fc8b035f-0b6e-4a6b-8c69-b69beabb7f76','intial_prefill_3','2018-09-18 12:07:07','2018-09-18 12:07:07',NULL),('ff7ac9ca-bf86-46ca-b2bc-53d7b721e20a','intial_prefill_1','2018-09-18 12:07:07','2018-09-18 12:07:07',NULL);
/*!40000 ALTER TABLE `roster` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `section`
--

DROP TABLE IF EXISTS `section`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `section` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_translation_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__section_name__translation_idx` (`name_translation_id`),
  CONSTRAINT `fk__section_name__translation` FOREIGN KEY (`name_translation_id`) REFERENCES `translation` (`id`) ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `section`
--

LOCK TABLES `section` WRITE;
/*!40000 ALTER TABLE `section` DISABLE KEYS */;
INSERT INTO `section` VALUES ('2494f66f-8c02-49f2-832b-e6c49156ee33','a1329da2-981f-43e7-a6b7-9382546d0e7e','2018-09-07 14:56:08','2018-09-07 14:56:08',NULL),('6e42360e-67c5-4639-849b-993eefd15c98','28e8482f-37fe-4819-ac56-7be1dfe285bd','2018-09-13 16:41:20','2018-09-13 16:41:20',NULL),('74d21b5c-4a12-46fc-9521-0dc5faf89adf','f0dc1589-badd-45d8-b16f-d75e4df9d16a','2018-09-13 16:40:46','2018-09-13 16:40:46',NULL),('832269fa-2c3b-4d28-b6ab-4252d4e644ae','314f676d-e904-404f-918e-e6cbf4baf9ce','2018-09-17 16:33:45','2018-09-17 16:33:45',NULL),('8a6b2fa6-55de-4839-b166-4fcb3b37daf9','6847a5af-3557-48a3-8187-9be01ad17d6e','2018-09-07 14:53:32','2018-09-07 14:53:32',NULL),('a55fd93c-ad35-4abf-9d10-987b82d68dd6','14e5f69f-f615-477a-a4d8-62519a4e53f0','2018-09-12 16:35:14','2018-09-12 16:35:14',NULL),('b01a541d-6518-4212-8d7f-c2cd7d663837','09398af3-3b20-4427-8833-1391637bd241','2018-09-12 13:11:32','2018-09-12 13:11:32',NULL),('d506ad04-6db4-4a7b-abdd-7cc56c5a9b2e','5673c9c3-1d72-44fa-a04b-38800ff6b2d4','2018-09-12 13:13:48','2018-09-12 13:13:48',NULL);
/*!40000 ALTER TABLE `section` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `section_condition_tag`
--

DROP TABLE IF EXISTS `section_condition_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `section_condition_tag` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `section_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `condition_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `survey_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `repetition` int(10) unsigned NOT NULL DEFAULT '0',
  `follow_up_datum_id` varchar(41) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__section_condition__condition_tag_idx` (`condition_id`),
  KEY `fk__section_condition_tag__section` (`section_id`),
  KEY `fk__section_condition_tag__survey` (`survey_id`),
  KEY `fk__follow_up_datum__datum` (`follow_up_datum_id`),
  CONSTRAINT `fk__follow_up_datum__datum` FOREIGN KEY (`follow_up_datum_id`) REFERENCES `datum` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  CONSTRAINT `fk__section_condition_tag__condition_tag` FOREIGN KEY (`condition_id`) REFERENCES `condition_tag` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__section_condition_tag__section` FOREIGN KEY (`section_id`) REFERENCES `section` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__section_condition_tag__survey` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `section_condition_tag`
--

LOCK TABLES `section_condition_tag` WRITE;
/*!40000 ALTER TABLE `section_condition_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `section_condition_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `section_question_group`
--

DROP TABLE IF EXISTS `section_question_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `section_question_group` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `section_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_group_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_group_order` int(10) unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__form_question__form_idx` (`section_id`),
  KEY `fk__section_question_group__question_group_idx` (`question_group_id`),
  CONSTRAINT `fk__section_question_group__question_group` FOREIGN KEY (`question_group_id`) REFERENCES `question_group` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__section_question_group__section` FOREIGN KEY (`section_id`) REFERENCES `section` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `section_question_group`
--

LOCK TABLES `section_question_group` WRITE;
/*!40000 ALTER TABLE `section_question_group` DISABLE KEYS */;
INSERT INTO `section_question_group` VALUES ('0ba5df05-3f9d-4ca9-a7af-67a11e19233f','74d21b5c-4a12-46fc-9521-0dc5faf89adf','3a847ce1-c12f-459e-b7d9-70212542994b',2,'2018-09-13 16:41:04','2018-09-13 16:41:04',NULL),('0d65a382-9148-42d1-ba3d-d513cbd1d9e9','6e42360e-67c5-4639-849b-993eefd15c98','84068749-31a3-44f4-9f08-19c593fd77d0',1,'2018-09-13 16:41:23','2018-09-13 16:41:23',NULL),('116066a9-80e6-4cb1-a434-03648e776925','832269fa-2c3b-4d28-b6ab-4252d4e644ae','28cc048b-8579-4235-91a4-1ccd99d72d49',2,'2018-09-17 16:34:12','2018-09-17 16:34:12',NULL),('1bfb799c-a307-447f-8223-49d6a8a050a8','b01a541d-6518-4212-8d7f-c2cd7d663837','8a11e491-612a-4291-b270-24244d3da016',1,'2018-09-12 13:11:34','2018-09-12 13:11:34',NULL),('2c015c37-cb7d-4e9a-9be8-05c0d1464c7b','8a6b2fa6-55de-4839-b166-4fcb3b37daf9','ecc26ed8-5699-41ce-a675-b23f7df86a63',3,'2018-09-07 14:55:33','2018-09-07 14:55:33',NULL),('34bb03d2-23de-486a-8d6b-7917f044a784','2494f66f-8c02-49f2-832b-e6c49156ee33','27345e55-ed2e-4397-b492-808490ca21e0',1,'2018-09-07 14:56:12','2018-09-07 14:56:12',NULL),('442c2654-4a13-4be6-aa37-58ef203609f9','832269fa-2c3b-4d28-b6ab-4252d4e644ae','4bed36a2-09cd-43e1-a766-a9d8d592bbe3',5,'2018-09-17 16:35:29','2018-09-17 16:35:29',NULL),('6ab39102-2310-463b-8421-c5a0a522c5f7','d506ad04-6db4-4a7b-abdd-7cc56c5a9b2e','dc768d39-6479-4eaf-a62e-3f19db166941',1,'2018-09-12 13:13:50','2018-09-12 13:13:50',NULL),('8009c630-eb87-43d1-bb39-80b098362b29','a55fd93c-ad35-4abf-9d10-987b82d68dd6','e5f5ddbb-57fc-48d3-b9c5-e9bf3937139d',2,'2018-09-12 16:37:31','2018-09-12 16:37:31',NULL),('84c11ee3-aaab-4948-8039-f0b2187ae105','832269fa-2c3b-4d28-b6ab-4252d4e644ae','ddf1310a-2993-4ef1-9517-16ba9441aac3',4,'2018-09-17 16:34:59','2018-09-17 16:34:59',NULL),('871b97d7-cf0e-4ccd-a537-1e9d094b0c0e','8a6b2fa6-55de-4839-b166-4fcb3b37daf9','937bbb86-6f63-4379-8ead-64957a35b8b5',1,'2018-09-07 14:53:35','2018-09-07 14:53:35',NULL),('99eb6f19-a95c-4683-94a1-11ef8f344a9b','a55fd93c-ad35-4abf-9d10-987b82d68dd6','7c4e6cc3-d9e2-4d8b-8064-dd91ac6c7a77',1,'2018-09-12 16:35:16','2018-09-12 16:35:16',NULL),('ad01799e-4151-41d8-9bba-fe5ee58ac626','6e42360e-67c5-4639-849b-993eefd15c98','57b30a00-f6e7-46f2-affc-1a74efc28118',2,'2018-09-13 16:42:04','2018-09-13 16:42:04',NULL),('ba16431c-42f9-452e-8ddb-5d303e2367a6','832269fa-2c3b-4d28-b6ab-4252d4e644ae','6ec54af7-4ca7-44fe-a993-618893d97e81',3,'2018-09-17 16:34:35','2018-09-17 16:34:35',NULL),('cb36b119-76e4-4689-8439-b2ddfa0fc571','8a6b2fa6-55de-4839-b166-4fcb3b37daf9','e2247ebf-09dc-4d4e-a60e-11e80bb74555',2,'2018-09-07 14:54:01','2018-09-07 14:54:01',NULL),('d7d1ed56-acb9-499e-8c3d-1224441c8c2f','a55fd93c-ad35-4abf-9d10-987b82d68dd6','f0f6109c-8e47-4e80-a081-b7d0a58e33c8',3,'2018-09-12 16:40:10','2018-09-12 16:40:10',NULL),('d9723b8c-6e94-494d-960e-852a2a8821b3','2494f66f-8c02-49f2-832b-e6c49156ee33','ebf9af64-9c9e-4655-afbe-aa2c2a652ca9',2,'2018-09-07 14:59:26','2018-09-07 14:59:26',NULL),('df76b81f-f373-486e-bac6-a74f8d0ac261','832269fa-2c3b-4d28-b6ab-4252d4e644ae','ba6eeec4-e84e-45b2-919f-88df3cbc1ff2',1,'2018-09-17 16:33:47','2018-09-17 16:33:47',NULL),('f43cf637-fcdd-4934-af41-50e9b6abb59e','74d21b5c-4a12-46fc-9521-0dc5faf89adf','af0460fb-f9b0-4f4b-861d-88b4c5f1206d',1,'2018-09-13 16:40:48','2018-09-13 16:40:48',NULL);
/*!40000 ALTER TABLE `section_question_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `section_skip`
--

DROP TABLE IF EXISTS `section_skip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `section_skip` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `section_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `skip_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `section_skip_section_id_foreign` (`section_id`),
  KEY `section_skip_skip_id_foreign` (`skip_id`),
  CONSTRAINT `section_skip_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `section` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `section_skip_skip_id_foreign` FOREIGN KEY (`skip_id`) REFERENCES `skip` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `section_skip`
--

LOCK TABLES `section_skip` WRITE;
/*!40000 ALTER TABLE `section_skip` DISABLE KEYS */;
/*!40000 ALTER TABLE `section_skip` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `self_administered_survey`
--

DROP TABLE IF EXISTS `self_administered_survey`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `self_administered_survey` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `survey_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login_type` enum('url','password_only','id_password','hash') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'id_password',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `self_administered_survey_survey_id_foreign` (`survey_id`),
  CONSTRAINT `self_administered_survey_survey_id_foreign` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `self_administered_survey`
--

LOCK TABLES `self_administered_survey` WRITE;
/*!40000 ALTER TABLE `self_administered_survey` DISABLE KEYS */;
/*!40000 ALTER TABLE `self_administered_survey` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `skip`
--

DROP TABLE IF EXISTS `skip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `skip` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `show_hide` tinyint(3) unsigned NOT NULL COMMENT '0 = show_if, 1 = hide_if, 2-255 reserved',
  `any_all` tinyint(3) unsigned NOT NULL COMMENT '0 = Any, 1 = All',
  `precedence` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `skip`
--

LOCK TABLES `skip` WRITE;
/*!40000 ALTER TABLE `skip` DISABLE KEYS */;
INSERT INTO `skip` VALUES ('06e38d4d-20fb-4bdd-877f-91546a8d0835',1,0,0,'2018-09-12 13:13:08','2018-09-12 13:13:08',NULL),('d3e8d3ff-277b-4f45-9186-fe38338f1d08',1,1,0,'2018-09-07 15:01:44','2018-09-07 15:01:44',NULL),('f97e8c83-f2be-4042-8f5f-d570beeb48cc',0,0,0,'2018-09-12 16:40:38','2018-09-12 16:40:38',NULL);
/*!40000 ALTER TABLE `skip` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `skip_condition_tag`
--

DROP TABLE IF EXISTS `skip_condition_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `skip_condition_tag` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `skip_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '0 = show_if, 1 = hide_if, 2-255 reserved',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `condition_tag_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__skip_condition_tag__skip_idx` (`skip_id`),
  CONSTRAINT `fk__skip_condition_tag__skip` FOREIGN KEY (`skip_id`) REFERENCES `skip` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `skip_condition_tag`
--

LOCK TABLES `skip_condition_tag` WRITE;
/*!40000 ALTER TABLE `skip_condition_tag` DISABLE KEYS */;
INSERT INTO `skip_condition_tag` VALUES ('0d273994-ffd6-43a0-bcdf-f1aaacb4e2d7','d3e8d3ff-277b-4f45-9186-fe38338f1d08','2018-09-07 15:01:44','2018-09-07 15:01:44',NULL,'is_dog'),('60c7e86f-43a2-47aa-a55c-9e9e43f8ce75','f97e8c83-f2be-4042-8f5f-d570beeb48cc','2018-09-12 16:40:38','2018-09-12 16:40:38',NULL,'is_one'),('81f2207c-910c-46cb-a111-0f1465bb5b35','06e38d4d-20fb-4bdd-877f-91546a8d0835','2018-09-12 13:14:53','2018-09-12 13:14:53',NULL,'show_first_question'),('cc19a32d-1f81-49e1-b22b-fc11e174b654','06e38d4d-20fb-4bdd-877f-91546a8d0835','2018-09-12 13:13:08','2018-09-12 13:14:53','2018-09-12 13:14:53','is_people');
/*!40000 ALTER TABLE `skip_condition_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `snapshot`
--

DROP TABLE IF EXISTS `snapshot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `snapshot` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hash` varchar(63) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `snapshot`
--

LOCK TABLES `snapshot` WRITE;
/*!40000 ALTER TABLE `snapshot` DISABLE KEYS */;
INSERT INTO `snapshot` VALUES ('13dfe78b-59c3-4dd4-a0c1-a76159606225','13dfe78b-59c3-4dd4-a0c1-a76159606225.sqlite.sql.zip','6d5eec254861416c0faa451aa7637fb7','2018-09-12 16:41:48','2018-09-12 16:41:48',NULL),('3d9fa025-98b0-4a83-ac9b-6eb69e33ef45','3d9fa025-98b0-4a83-ac9b-6eb69e33ef45.sqlite.sql.zip','f2870bcb6f571f55c574e554cf18bb59','2018-09-18 12:07:35','2018-09-18 12:07:35',NULL),('5073f4db-726a-4c8b-b81e-4b1ab2bb970f','5073f4db-726a-4c8b-b81e-4b1ab2bb970f.sqlite.sql.zip','9abfd7edf75ad23f6ab4c04f41fd6cd6','2018-09-12 13:17:21','2018-09-12 13:17:21',NULL),('51c560ca-5f64-4e9f-befb-7d455246dfcd','51c560ca-5f64-4e9f-befb-7d455246dfcd.sqlite.sql.zip','5c96c15d20f91daf7f57a1a7e9cdd3b9','2018-09-10 17:45:18','2018-09-10 17:45:18',NULL),('5f0920ce-052d-45e7-8c79-de4502de8029','5f0920ce-052d-45e7-8c79-de4502de8029.sqlite.sql.zip','dd80e2a7f5258b373e08e92f04f8e63c','2018-09-13 16:42:45','2018-09-13 16:42:45',NULL);
/*!40000 ALTER TABLE `snapshot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `study`
--

DROP TABLE IF EXISTS `study`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `study` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo_quality` tinyint(3) unsigned NOT NULL DEFAULT '60',
  `default_locale_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__study__default_locale_idx` (`default_locale_id`),
  CONSTRAINT `fk__study__default_locale` FOREIGN KEY (`default_locale_id`) REFERENCES `locale` (`id`) ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `study`
--

LOCK TABLES `study` WRITE;
/*!40000 ALTER TABLE `study` DISABLE KEYS */;
INSERT INTO `study` VALUES ('6a08c96a-fb80-4eae-9b2b-4d03d4b3235d','Test Study',70,'48984fbe-84d4-11e5-ba05-0800279114ca','2018-09-07 14:52:48','2018-09-07 14:52:48',NULL);
/*!40000 ALTER TABLE `study` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `study_form`
--

DROP TABLE IF EXISTS `study_form`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `study_form` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `study_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `form_master_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `form_type_id` tinyint(4) NOT NULL,
  `census_type_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__study_form__study_idx` (`study_id`),
  KEY `fk__study_form__form_idx` (`form_master_id`),
  KEY `fk__form_study_form_type_idx` (`form_type_id`),
  KEY `fk__study_form_census_type__idx` (`census_type_id`),
  CONSTRAINT `fk__form_study_form_type_idx` FOREIGN KEY (`form_type_id`) REFERENCES `form_type` (`id`),
  CONSTRAINT `fk__study_form__form` FOREIGN KEY (`form_master_id`) REFERENCES `form` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk__study_form__study` FOREIGN KEY (`study_id`) REFERENCES `study` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__study_form_census_type__idx` FOREIGN KEY (`census_type_id`) REFERENCES `census_type` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `study_form`
--

LOCK TABLES `study_form` WRITE;
/*!40000 ALTER TABLE `study_form` DISABLE KEYS */;
INSERT INTO `study_form` VALUES ('2a0abc74-f9b9-4cc6-8ab8-8a7967e2aa9d','6a08c96a-fb80-4eae-9b2b-4d03d4b3235d','01a36e93-32e0-4161-b1d5-6458c21d9edc',5,'2018-09-13 16:40:35','2018-09-13 16:40:35',NULL,0,NULL),('2e595a71-a992-47eb-bb6f-2e2da4ce2ac1','6a08c96a-fb80-4eae-9b2b-4d03d4b3235d','00be5ff5-f49b-4bd4-af0c-eae392819a69',4,'2018-09-12 16:34:58','2018-09-12 16:34:58',NULL,0,NULL),('820cb4b7-0a0f-4cc5-b052-530e1e0f3cb5','6a08c96a-fb80-4eae-9b2b-4d03d4b3235d','cea71d10-5423-4e87-ade6-5e4a4ce01090',6,'2018-09-17 16:33:28','2018-09-17 16:33:28',NULL,0,NULL),('a0574906-f486-40b7-b918-4079b38462ea','6a08c96a-fb80-4eae-9b2b-4d03d4b3235d','bfc6270b-b6c8-4d9e-b26b-c2fc38b65a48',2,'2018-09-07 14:53:05','2018-09-07 14:53:20',NULL,1,'06162912-8048-4978-a8d2-92b6dd0c2ed1'),('b0b31281-a48e-4063-b675-df2352f0c8ef','6a08c96a-fb80-4eae-9b2b-4d03d4b3235d','cb801404-806f-4ed1-b5bd-88997ad81f80',3,'2018-09-12 13:11:04','2018-09-12 13:11:04',NULL,0,NULL),('d7a60998-ab69-4693-abad-eb9f0e75add2','6a08c96a-fb80-4eae-9b2b-4d03d4b3235d','c98e78f8-1bcc-4afc-8e68-530374940213',1,'2018-09-07 14:52:56','2018-09-07 14:52:56',NULL,0,NULL);
/*!40000 ALTER TABLE `study_form` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `study_locale`
--

DROP TABLE IF EXISTS `study_locale`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `study_locale` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `study_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `locale_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__study_locale__study_idx` (`study_id`),
  KEY `fk__study_locale__locale_idx` (`locale_id`),
  CONSTRAINT `fk__study_locale__locale` FOREIGN KEY (`locale_id`) REFERENCES `locale` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__study_locale__study` FOREIGN KEY (`study_id`) REFERENCES `study` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `study_locale`
--

LOCK TABLES `study_locale` WRITE;
/*!40000 ALTER TABLE `study_locale` DISABLE KEYS */;
INSERT INTO `study_locale` VALUES ('5cca5685-5d3b-4aec-8484-beebc262d8b3','6a08c96a-fb80-4eae-9b2b-4d03d4b3235d','4a1d88ab-84d4-11e5-ba05-0800279114ca','2018-09-07 14:52:52','2018-09-07 14:52:52',NULL),('7f14ba4e-d6de-4b44-acd9-c82d89751aa5','6a08c96a-fb80-4eae-9b2b-4d03d4b3235d','48984fbe-84d4-11e5-ba05-0800279114ca','2018-09-07 14:52:48','2018-09-07 14:52:48',NULL);
/*!40000 ALTER TABLE `study_locale` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `study_parameter`
--

DROP TABLE IF EXISTS `study_parameter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `study_parameter` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `study_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parameter_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `val` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `study_parameter_study_id_foreign` (`study_id`),
  KEY `study_parameter_parameter_id_foreign` (`parameter_id`),
  CONSTRAINT `study_parameter_parameter_id_foreign` FOREIGN KEY (`parameter_id`) REFERENCES `parameter` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `study_parameter_study_id_foreign` FOREIGN KEY (`study_id`) REFERENCES `study` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `study_parameter`
--

LOCK TABLES `study_parameter` WRITE;
/*!40000 ALTER TABLE `study_parameter` DISABLE KEYS */;
/*!40000 ALTER TABLE `study_parameter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `study_respondent`
--

DROP TABLE IF EXISTS `study_respondent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `study_respondent` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `study_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `respondent_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__study_respondent__study_idx` (`study_id`),
  KEY `fk__study_respondent__respondent_idx` (`respondent_id`),
  CONSTRAINT `fk__study_respondent__respondent` FOREIGN KEY (`respondent_id`) REFERENCES `respondent` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__study_respondent__study` FOREIGN KEY (`study_id`) REFERENCES `study` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `study_respondent`
--

LOCK TABLES `study_respondent` WRITE;
/*!40000 ALTER TABLE `study_respondent` DISABLE KEYS */;
INSERT INTO `study_respondent` VALUES ('08500877-f135-47e8-be85-5fbf355fa6fb','6a08c96a-fb80-4eae-9b2b-4d03d4b3235d','98926f3c-a9e2-4de5-8624-6ecf6ab128b6','2018-09-07 15:02:43','2018-09-07 15:02:43',NULL),('21584992-de3d-4556-83ca-252baa518bee','6a08c96a-fb80-4eae-9b2b-4d03d4b3235d','b4be2718-66c9-4d95-9518-d81ac7a29cbc','2018-09-07 15:02:37','2018-09-07 15:02:37',NULL),('c67d09ab-26f5-4973-ab12-52bcd5c31673','6a08c96a-fb80-4eae-9b2b-4d03d4b3235d','6905448f-4356-40f0-b78d-0cc7c05acfdd','2018-09-07 15:02:50','2018-09-07 15:02:50',NULL),('e6f8a8db-7339-4b94-af99-128128e94982','6a08c96a-fb80-4eae-9b2b-4d03d4b3235d','380cb5c8-10fe-43e5-a927-97f62180db5f','2018-09-17 16:31:32','2018-09-17 16:31:32',NULL);
/*!40000 ALTER TABLE `study_respondent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `survey`
--

DROP TABLE IF EXISTS `survey`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `survey` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `respondent_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `form_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `study_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_question_id` varchar(41) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__survey__respondent_idx` (`respondent_id`),
  KEY `fk__survey__form_idx` (`form_id`),
  KEY `fk__survey__study_idx` (`study_id`),
  KEY `fk__survey__last_question_idx` (`last_question_id`),
  CONSTRAINT `fk__survey__form` FOREIGN KEY (`form_id`) REFERENCES `form` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__survey__last_question` FOREIGN KEY (`last_question_id`) REFERENCES `question` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  CONSTRAINT `fk__survey__respondent` FOREIGN KEY (`respondent_id`) REFERENCES `respondent` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__survey__study` FOREIGN KEY (`study_id`) REFERENCES `study` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `survey`
--

LOCK TABLES `survey` WRITE;
/*!40000 ALTER TABLE `survey` DISABLE KEYS */;
INSERT INTO `survey` VALUES ('012aef54-40bc-4a99-9bdd-d0bf20fcc72c','b4be2718-66c9-4d95-9518-d81ac7a29cbc','c98e78f8-1bcc-4afc-8e68-530374940213','6a08c96a-fb80-4eae-9b2b-4d03d4b3235d',NULL,'2018-09-07 15:35:21','2018-09-07 15:35:21',NULL,NULL);
/*!40000 ALTER TABLE `survey` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `survey_condition_tag`
--

DROP TABLE IF EXISTS `survey_condition_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `survey_condition_tag` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `survey_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `condition_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__survey_condition_tag__survey_idx` (`survey_id`),
  KEY `fk__interview_condition__condition_idx` (`condition_id`),
  CONSTRAINT `fk__survey_condition_tag__condition_tag` FOREIGN KEY (`condition_id`) REFERENCES `condition_tag` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__survey_condition_tag__survey` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `survey_condition_tag`
--

LOCK TABLES `survey_condition_tag` WRITE;
/*!40000 ALTER TABLE `survey_condition_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `survey_condition_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sync`
--

DROP TABLE IF EXISTS `sync`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sync` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `snapshot_id` varchar(41) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'download only',
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'upload only',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'upload / download',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'pending / in_progress / completed / error',
  `error_message` text COLLATE utf8mb4_unicode_ci COMMENT 'reason why sync failed',
  `warning_message` text COLLATE utf8mb4_unicode_ci COMMENT 'sync was successful but there was a warning, e.g. low disk space',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sync`
--

LOCK TABLES `sync` WRITE;
/*!40000 ALTER TABLE `sync` DISABLE KEYS */;
/*!40000 ALTER TABLE `sync` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tag`
--

DROP TABLE IF EXISTS `tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(63) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tag`
--

LOCK TABLES `tag` WRITE;
/*!40000 ALTER TABLE `tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `token`
--

DROP TABLE IF EXISTS `token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `token` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token_hash` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key_id` bigint(20) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token_hash_UNIQUE` (`token_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `token`
--

LOCK TABLES `token` WRITE;
/*!40000 ALTER TABLE `token` DISABLE KEYS */;
INSERT INTO `token` VALUES ('0069f494-28c2-4cb3-a877-bc4e1fd0d2bb','c1f277ab-e181-11e5-84c9-a45e60f0e921','7b8042829e5518b35929c8f4c5cefbb9dfc0811f2a8a1520a7f09bad6cb9bf890c8a30ed745543fad2c23cc934373100787579b2fc572170cd178f670ebe6dc9',0,'2018-09-13 15:12:03','2018-09-13 15:12:04',NULL),('015e6cda-8320-48dc-b229-7a9a71ef7108','c1f277ab-e181-11e5-84c9-a45e60f0e921','6fbcdf6901301d09f13d3f0484a02b2a6d38276b68a9e5663096d0bdf6307c27a3c1e56b613330c1ddac3aba0baa7f73955518653c757514728d98ad02a10955',0,'2018-09-17 10:56:38','2018-09-17 10:56:39',NULL),('01c71287-fb19-4dd4-a674-d45834e50ad7','c1f277ab-e181-11e5-84c9-a45e60f0e921','7144823e39fc3b8dde37d353a34be93863a8ebb2ad4852690c17c5dd0fe4cf4eb9cbde7a495cfcd1721c8f0deb988c74387b92db9fdeed6b77fe7338f869add7',0,'2018-09-14 12:58:09','2018-09-14 12:58:09',NULL),('029e582d-1518-4291-8d8d-2e6dc234394a','c1f277ab-e181-11e5-84c9-a45e60f0e921','df62ac5c8aff657560427998267d1bd3f201e796b2b8dceeb8bb82b8d04058a900a7cd40bd4fba2ac1104198b85ba5ec034f5349b9a978f92fc225c78da1c880',0,'2018-09-11 10:04:19','2018-09-11 10:04:31',NULL),('030ed610-8a72-45d6-8275-f3d04f4121c4','c1f277ab-e181-11e5-84c9-a45e60f0e921','11d7277ac79d29dc53182c76a22024dac43c3a6a2ca2ba7ec50a10bde82ffa5de482f757cf03b0d6e7baa88e0be77a708a269b868ad0b13fe0445de96598877a',0,'2018-09-13 16:26:08','2018-09-13 16:26:08',NULL),('05f2c53b-5d45-43a2-ab5a-ecfa7acdadd8','c1f277ab-e181-11e5-84c9-a45e60f0e921','3970c55f492791572f18820a353b3ec0d80f50763cbccf86ef9b6bd3c9dd08e36d888ab8cfd7d4b6c2eaaa011df280895cd5405e54cfaba20644e0510e5a72ba',0,'2018-09-11 09:47:52','2018-09-11 09:48:53',NULL),('060bc875-2d59-4ec7-aefb-b0be503c293c','c1f277ab-e181-11e5-84c9-a45e60f0e921','93447f041257e3ee8363a00e936e3291d5c142b564d91a7bd3f36b18a8031466e50aab0f30359b6fa3f4a0e34c747b7c57bd437f1d390e70c7e27b69e26cf1c8',0,'2018-09-12 15:00:39','2018-09-12 15:00:40',NULL),('06894b11-ea2c-4aad-a9c7-166beb12f06c','c1f277ab-e181-11e5-84c9-a45e60f0e921','173cc395bb1bd75bbaae3fb52026fb9c220d6813d98ca45896fe43fed894b7b2470c9f19dda33b8ea79e5260b0f1cc9292ec2351024737c2b2d524d3cb8d0785',0,'2018-09-17 14:38:44','2018-09-17 14:38:45',NULL),('06b3eafd-b060-4830-8372-b1634e853d08','c1f277ab-e181-11e5-84c9-a45e60f0e921','ced41befd2088e953837c577505c7ca800c3890777654e262c985b87e06237fe514f847b7de7dbb42f612cf6bbe5cf3b758122519fbd82536fcc2c7bc4cca81d',0,'2018-09-14 14:34:21','2018-09-14 14:34:21',NULL),('07a42415-e01d-4491-9526-7a5f6542bfd3','c1f277ab-e181-11e5-84c9-a45e60f0e921','139a1d084c0b2e7aa688f10d652537e54f250f4a5bf530a95c4c803d34cc339cf8b256f13f4b6847b2c444b80fc4b65e607adc5627e69396ab52058ec1196530',0,'2018-09-12 16:15:48','2018-09-12 16:15:48',NULL),('085fd5ce-9454-4a8d-8739-fd78f5825eb9','c1f277ab-e181-11e5-84c9-a45e60f0e921','88d8cac57247c47dcb98326202c5d5d18f90ec44c0d593cae1260e92e6ad8919ac36dbfbea3310663a47771afdbc14bf64eca8ba5758849664d9e9dadb38be06',0,'2018-09-14 14:58:12','2018-09-14 14:58:13',NULL),('0deefa25-b924-4dc0-af37-c47335e513bb','c1f277ab-e181-11e5-84c9-a45e60f0e921','f2594bcc1a40769c0545adb240d67f791fcf6b5cdef2226e9bf3b6dfc8f6a0ef4c945321cf89fd4987764f63e70debedf88c1c9710a4aa6cc54ad47af4d2c53b',0,'2018-09-17 16:49:44','2018-09-17 16:49:45',NULL),('0dfeb9d3-d57c-42b8-96e0-d107df7fdf77','c1f277ab-e181-11e5-84c9-a45e60f0e921','51d3882809dfc3b7b3b9ce684ec70038acf688bbeb75582da2c8190b43effa7e80008f2924309255576c53e89235c287ecf183f7c9d50c7ef0dbb0301eac1c57',0,'2018-09-12 15:33:04','2018-09-12 15:33:05',NULL),('11d48301-9f43-4369-8b7f-f5e06bb8847e','c1f277ab-e181-11e5-84c9-a45e60f0e921','3c00339096c2644c4a441b8ab3ad947cef989e320cefe2b938e0f5d4da1c111cda0f9d8c17ab24a55429e51ac9ea3621884069e78fc6dbcccf3cbee50527431a',0,'2018-09-14 10:40:42','2018-09-14 10:40:42',NULL),('12db670b-c152-478f-b01a-a0435bbbf6ea','c1f277ab-e181-11e5-84c9-a45e60f0e921','aaeb3541f34c3cf4ac28fcc4fbaa26a97b58020515aec26d133855113ebd90bd822a4447b7f15d233e19c05630bb252ccc2d26d4ba12ef9ea242089020edc4ab',0,'2018-09-12 16:19:21','2018-09-12 16:19:21',NULL),('130fc57e-3fee-4d88-8b92-ee98c1576010','c1f277ab-e181-11e5-84c9-a45e60f0e921','af761129aa45899a878ad56c1690c7dbd70badc76b38c56e7f1c301f1ca45aabe1faacff8c5a4360b5d46c81fc91c0a1fd80d05178df4ff167463b73e5340047',0,'2018-09-17 15:49:11','2018-09-17 15:49:12',NULL),('131a2cde-dd63-4f92-a8a3-c52b3cf7a87f','c1f277ab-e181-11e5-84c9-a45e60f0e921','8af304bb8a6ef9f4459f25f345bfde8f5584923cc6b66560b3b76d91b81200a6d0cc14c28f4b32efd95c06464f0c5338f0b638489573405944d159c8191d020c',0,'2018-09-13 16:45:25','2018-09-13 16:45:25',NULL),('132126cf-4534-40d1-893c-01f68c2240c1','c1f277ab-e181-11e5-84c9-a45e60f0e921','2afce41ab19b819e366023892863f53c01dcffba5150bb631d00515b6d02f69d20be82082becde48496203c4670e1426168e7eebb33f2058ca849d65ddc51ae6',0,'2018-09-17 10:41:56','2018-09-17 10:41:57',NULL),('182da36e-7b9d-46b4-aa72-1cfb0a9a0ad4','c1f277ab-e181-11e5-84c9-a45e60f0e921','d940fc983c7f00c8fe33cefa8f6bc1a53c3558e8e4b78ffedc437824e8d7a4079c9e919c776aa21859e65e55ed398ab906593022fa5f122e8d770f9e9e0afc15',0,'2018-09-13 16:16:09','2018-09-13 16:16:09',NULL),('18c6c5c6-73d1-48c1-a892-f69e2ecae607','c1f277ab-e181-11e5-84c9-a45e60f0e921','460c374759fffc25ac2ecbca673379888c68c4aa18799b23128aa4edb5e8360e142ecd772bca8de10b1b684788044abd7c5e3aa6217e9bce018c44f6f1085f2e',0,'2018-09-14 12:15:24','2018-09-14 12:15:25',NULL),('1a375435-43ef-470f-9d16-31d218d4e620','c1f277ab-e181-11e5-84c9-a45e60f0e921','f731d0124a379c8fc6a155eb8683a875f4c5147c5b9c96efa3452a39caadf71e4d25065ab0d1132de574fdd765a59b0f93e9a5d665bd7ff58618664d580c2dd8',0,'2018-09-17 14:12:06','2018-09-17 14:12:07',NULL),('1a9fe643-da13-4740-8265-df06459078aa','c1f277ab-e181-11e5-84c9-a45e60f0e921','ea44f07cb027bdf8600dfd942f33f3fe18719c3012cd9ab1d3a3a5cc850d27801669418489ac2549017f4bdcd38982e0a47a982b0ac5d3713aac74e7a6caf2e9',0,'2018-09-12 16:13:42','2018-09-12 16:13:42',NULL),('1da783e1-959d-42f9-b08e-ebafff02b69d','c1f277ab-e181-11e5-84c9-a45e60f0e921','4296c94e13698e4bf1b5977f60d5d92457a8b59f58ae0cfec6b8524ca5b2459afe0ce9220e0a1b7c352ea7e159d8781e746c63d4bf62b7f67f46621eaf01e306',0,'2018-09-14 12:13:05','2018-09-14 12:13:06',NULL),('1e1d1662-6f58-4d52-b3bb-f457a2397bfb','c1f277ab-e181-11e5-84c9-a45e60f0e921','63d934ffef501e7ab6d722c5865ddb31a206e0ca5d3a13975095335c8235c0e2bcbccecdd3d947b311b17a45bfd1697bb18286a08a093fc8a2446d8ca820033f',0,'2018-09-17 15:00:44','2018-09-17 15:00:44',NULL),('1f1653d8-ec86-43a3-88e9-138ab7a2a376','c1f277ab-e181-11e5-84c9-a45e60f0e921','5d0e5c7d4f77ec3261dffc2885709ed3b89ad96fba92f3c88b0a369c206a4fa0fffcd75ef0015392bd7717740df387d625765610bf41bca087c4be398f865cb2',0,'2018-09-13 15:45:08','2018-09-13 15:45:08',NULL),('20571bb1-6fec-4bee-825a-5f41ef30b03f','c1f277ab-e181-11e5-84c9-a45e60f0e921','49d9911716c05c062f0dea1439e77a79dd7b864caeb333694f5a5d35dc3067b4e0e24b3446f6d2911573593687e892a8178f651e37c2344b2f5cdce2ecb66478',0,'2018-09-17 15:27:10','2018-09-17 15:27:10',NULL),('220c3bb6-d3ba-4483-8d0f-389bec974522','c1f277ab-e181-11e5-84c9-a45e60f0e921','be0e741980a7124c91daf0160c75d5b6e2f594b2306af0f82e0dde3a58163c89f96e38585fc71142f490e5617ed3c51458394bb7d27d5e4831e13bd28bf5507b',0,'2018-09-17 15:02:08','2018-09-17 15:02:09',NULL),('2221f02c-ed33-40c9-a708-1c4835befcdc','c1f277ab-e181-11e5-84c9-a45e60f0e921','83241f7247ae34cf046de66dab01ff525aa3f396de47c6637e4217a6ce28da4c83575ee321c2e4f3e41dc4b325054c0f4ca74dac0ced279dad5657a6a899ba17',0,'2018-09-13 15:46:29','2018-09-13 15:46:30',NULL),('222ea1af-d3f0-4ac3-9cbf-9dff51c6f8f7','c1f277ab-e181-11e5-84c9-a45e60f0e921','a8d40795fbbf20e07b541d99ecab3039c6343f41ffe2c18523850b4afa6855bfbbb725011a95451ef39c57b30c6f4590ca1f58197e2d664c5935c83295bfcad2',0,'2018-09-12 15:03:56','2018-09-12 15:03:56',NULL),('23d9a693-9645-4cff-9dc9-efa772b1f91a','c1f277ab-e181-11e5-84c9-a45e60f0e921','fbb3c18691de0650dbcd8e12c83c87821c3dfbb869d210f24e25f2686634ac302e7f6f690a2e6ffc29058c03f03a00c2053d3e0b5e7843d3ee204487e790068b',0,'2018-09-11 10:00:20','2018-09-11 10:01:32',NULL),('24264533-2eab-49e0-9dec-f571b0fed69a','c1f277ab-e181-11e5-84c9-a45e60f0e921','ed6457fe29aac557c99496e30596157f65aa9d1acd62796eeedb22f5ebb51bf89dd7450a12eb43b28056dd68c7495c9e61db2e1aa0122d73e78afd546cc15b91',0,'2018-09-14 10:44:29','2018-09-14 10:44:29',NULL),('2820e9a8-ba65-4eab-b6c7-67acf60228c5','c1f277ab-e181-11e5-84c9-a45e60f0e921','11e586f8c870dc0ed18805b7380ba9982a6523737df96ea94ace995a887ada9203f8992731e88257e40fe784d24417541fde4e327b1c68ffc9d34bbd5d92de1a',0,'2018-09-17 14:07:01','2018-09-17 14:07:02',NULL),('2884a38a-9a53-405e-b72c-cd762aca5b71','c1f277ab-e181-11e5-84c9-a45e60f0e921','8f78e899afcd41299fde12743ac5c8fa81ec215935da7a9752785a29c83ed82951457073489025df260f9ca583b5f2585ab9a633e5e5faf99bbeed2761ed037c',0,'2018-09-11 10:06:42','2018-09-11 10:06:54',NULL),('2983ce6a-c81c-4228-bd6d-03402f8eb380','c1f277ab-e181-11e5-84c9-a45e60f0e921','d16c7eb7bdb75a897784e8d34214064208abdf55c247f4b0068c87fdf51e292eebed73e00d1a0d2d03e903b06d534d77105f0e5d82d8432fed4353314aa9fab5',0,'2018-09-17 12:11:16','2018-09-17 12:11:16',NULL),('2aa43c16-27cd-4314-b7f6-1329e416b1cf','c1f277ab-e181-11e5-84c9-a45e60f0e921','3b446b5e338f956acf5e6c450d05aae4dbc639fe19ba0108b1af47e496603b77061683ddd61595bd517d46706b7d9c4c623ad8af43defaa011811e413a537d6e',0,'2018-09-14 10:32:21','2018-09-14 10:32:21',NULL),('2df5c7e3-5caf-44a5-a5a5-e7c1decb9f1e','c1f277ab-e181-11e5-84c9-a45e60f0e921','8b8cbbacf0a94d1808260b7ef731667b3368c583c41f1d19bed284156aa6e771c357c41069afeafc88aa08aded20801e268523b9629d7c53b173b4aad627b35b',0,'2018-09-11 09:56:17','2018-09-11 09:56:30',NULL),('2ebaed05-c707-4f65-ad85-b032b7a8b017','c1f277ab-e181-11e5-84c9-a45e60f0e921','290e3f8136d216d92e715c1f5675963473ce082e790740f94132cfaa4e31eaa03ca00da08b69f0725a83eacbd856523eb3e26ff7abed56d7dd79de25ae81a94c',0,'2018-09-17 15:32:58','2018-09-17 15:32:59',NULL),('2ed0e4e2-e78a-43ff-8358-cbee08e0aadc','c1f277ab-e181-11e5-84c9-a45e60f0e921','8963b12eadf426a617cc5a93da47221991c336515c774846d20d1717b2a7b3d3dca8dbc1cbbadf95e62aeb11d9a1a6ae966e1ea3f82456f3be5189ef99a39aa2',0,'2018-09-17 11:00:17','2018-09-17 11:00:17',NULL),('32b2f117-a6d0-4603-986e-ab72949c6827','c1f277ab-e181-11e5-84c9-a45e60f0e921','600237b2a8a48dde457e01652f9b28a9e35ef5ad4ad7e3bd0264d3d49c7ee069714456e1d8941507f32635fee25152c21523a4f891a17dde868982bcb7f15b6c',0,'2018-09-12 15:43:42','2018-09-12 15:43:42',NULL),('34d474ae-134b-4675-85f1-12729b509584','c1f277ab-e181-11e5-84c9-a45e60f0e921','f82ab8c54ec662bca2ebf65dd7cbca8b171cf5205574969cdfbb13f372a87c1fb21467c271e1f30ac4e060ceb36cc2e394708082410a9cb1a674f045c58a3d38',0,'2018-09-17 12:33:01','2018-09-17 12:33:01',NULL),('35bf0222-3190-4568-bb57-409a37dd137b','c1f277ab-e181-11e5-84c9-a45e60f0e921','d87ba3b81166db763039d265f1332fb666f88a830582eb0b69774e6720890d2b20cbf8a03475cdffb32cc293b1ddf74541e360865f506ce04d79849509ef5708',0,'2018-09-11 10:09:27','2018-09-11 10:09:31',NULL),('39af7595-b034-4256-950d-8d5bf0ac3df9','c1f277ab-e181-11e5-84c9-a45e60f0e921','ab717ba3211f21cb23783e29fddccfbd238bb0059d05020fb8d164aedaf630b88b105215d9df1a919b53ef8df6c6a1df4d47530489baed9d870c2c072989c3ac',0,'2018-09-17 14:07:36','2018-09-17 14:07:36',NULL),('3a3a8afe-0a2f-4137-97cc-f8103c8c3c83','c1f277ab-e181-11e5-84c9-a45e60f0e921','7e3211dddffeb46a639a99b493f33f1662973362a86b9dfb55d458a1e2abf3cdad8584d1d2d3a356ed7261b28a47f4eac3c79db0d5c77e85602b0c60c5d2fa52',0,'2018-09-13 12:05:22','2018-09-13 12:05:23',NULL),('3c17fbda-8a75-458d-a3ba-3366d9e4e7e8','c1f277ab-e181-11e5-84c9-a45e60f0e921','9060d0a716c35daf9bfbca4a5f8ef59f6886f7ed065895bf554940fe9e019968f66b2b504320a88f0bd0339a70419ee0c4102912364991f75cce628d40500c6d',0,'2018-09-11 09:53:13','2018-09-11 09:53:17',NULL),('3ca19066-9e14-4067-92fb-4b9f739d7413','c1f277ab-e181-11e5-84c9-a45e60f0e921','f3d115a2666f46c2911953b3b91c31618a860bfbbf80c8c3af9f5496dda577c6f2967c03fb9cec31c5c058d3197f7ae93d57b17e0d56b8f63a15407f38f23657',0,'2018-09-14 11:57:10','2018-09-14 11:57:11',NULL),('3dd188fa-6969-4828-ace4-e6d6fd575ff1','c1f277ab-e181-11e5-84c9-a45e60f0e921','0d34e52004efaea6edc81e45e9fcff6ca2ca43fe0bab71b07cbc62b2e2b50734c655871cbd1ae6706a50089a0f7f2462eb4b1784233fb5e33cf1c376239a38ae',0,'2018-09-13 15:40:46','2018-09-13 15:40:46',NULL),('3eea02ab-d542-4a0f-9ba8-8aa85989ef04','c1f277ab-e181-11e5-84c9-a45e60f0e921','978af4dfc551437ce0ca85eb2b3b91ac75558abad146707d062e701aeb482063b27923e4b2dca8b135a966da2c1247b9392f6721de79107185f645dd2de4df67',0,'2018-09-17 15:36:12','2018-09-17 16:44:04',NULL),('41a6662b-6f34-4b8c-bc5b-27370c67e8c0','c1f277ab-e181-11e5-84c9-a45e60f0e921','c654563274ee85e1b4968f8f1775bfd2e773722e02524f08ea92e2616cbfd8b3bd5d2ebb21db7efc663ec36fda5f9025b6eedf4d88519892395124e5fcebe5b0',0,'2018-09-12 15:50:32','2018-09-12 15:50:33',NULL),('4316ce82-4d11-4e6f-ae23-48088c101964','c1f277ab-e181-11e5-84c9-a45e60f0e921','4712a8100a80187ae901f4908d76082ff00afba0508b1230f44cdef6bd6267cea00367f85dd991bc8f4450868e5fd74af9851f3c5bd5d954c434ae97f465e6d8',0,'2018-09-17 15:16:11','2018-09-17 15:16:12',NULL),('4320d1cc-85b8-4529-b13a-4f3db2a07165','c1f277ab-e181-11e5-84c9-a45e60f0e921','1fe81ac710747f4a3cf78157204210721c91ae552b83d5f7c3253bcefb50e8d10195de6efaaebe704c1ba86b85ca4e4357931b181d1b0c345eaacb8773f8e3ab',0,'2018-09-14 12:07:19','2018-09-14 12:07:19',NULL),('435ef417-8e67-401d-abe9-c09bfe5cdca5','c1f277ab-e181-11e5-84c9-a45e60f0e921','966fb11f7368282148c6a163814a446a2d8684b306fcc59fe91df4995642f66bf125aac86100815171556a23c34592a37fe8bccadd70d2886068c3c814b3f6c6',0,'2018-09-17 14:57:40','2018-09-17 14:57:41',NULL),('448544bc-a016-49f9-bd87-f111ec557f6b','c1f277ab-e181-11e5-84c9-a45e60f0e921','7ed924ea01fc89418777cfc4897bc92fb7efaf09d0a1df4d464c0cb6f601fb952e596902432bade51d85fe477fb5da6fbd331790e3f2631e319b2c3991cbe1ab',0,'2018-09-14 11:03:08','2018-09-14 11:03:09',NULL),('44e5b098-4266-40ed-9e3a-4b30a9f482c9','c1f277ab-e181-11e5-84c9-a45e60f0e921','27030563db8a9fabcf0336992b51bc423b26aa3bf34289de996ba6d555b22919f4bf9e66b8db4cd05e0168c1c7b6ef490f179abe882344a55ad3f0c6a2ad5ea9',0,'2018-09-14 10:52:59','2018-09-14 10:53:00',NULL),('45147f1b-9752-4fdb-bd59-8a01e0ef6699','c1f277ab-e181-11e5-84c9-a45e60f0e921','b3bb7787aed61d687c03ff9a1b9d6847d68fcc2fdd90f9cc6f49d8d1fd95895e4eab453281157499fd5ade2a07ba9563387fb4ee0441e2a5a5f31e9c9b182c8c',0,'2018-09-12 16:17:46','2018-09-12 16:17:47',NULL),('4542c297-a020-421e-8b1c-f818a9ad41e1','c1f277ab-e181-11e5-84c9-a45e60f0e921','96d75e0e3aa9899f8dc5ce29a6fbbc59f43cefe9131908b3ca200c25b20c4d3f29503c6c09c8072e89e195a5c9b3424751007088d8253d0f48f84a3d4343652f',0,'2018-09-13 16:29:31','2018-09-13 16:29:32',NULL),('46daf805-0671-4e4a-9c40-639b42cbabc2','c1f277ab-e181-11e5-84c9-a45e60f0e921','016841bef0cfa188844ea71c531e8ad0b9c58dfd2b2eb603657991cc8090aad7476302cff3c647a85b6b8aac4d76aa96d3e9f2566a9402c6404c3f91215ac4e4',0,'2018-09-17 12:19:00','2018-09-17 12:19:01',NULL),('481a525f-68c8-498d-81ee-31160d0716e8','c1f277ab-e181-11e5-84c9-a45e60f0e921','3f28011098c90f78fc5a07dc41a719f4799f72b11e807b0070bcbb602a7e1a5cd906f18b4bd21e8561a2d87f3070df38f0d0b619eda4b92c83de20a30989f9ac',0,'2018-09-14 12:30:52','2018-09-14 12:30:53',NULL),('49866d71-3652-4218-a29f-26bb69fa2243','c1f277ab-e181-11e5-84c9-a45e60f0e921','cfa501553213e14b1452b9502d98582db7707673195a52be9970161223b895f7e524fae74d71e43440265524c0ee7b30de9bfd1288aa329433fb68bd65a9eece',0,'2018-09-12 15:47:24','2018-09-12 15:47:24',NULL),('4abee6f9-3ad0-4cbd-9935-4b9ebd696b01','c1f277ab-e181-11e5-84c9-a45e60f0e921','dc043fce81536e7e9fbfa0b59478566b256dffa02fcd2b0d606ff4d3e19fa1661f0a839e6a38720450b35ca5a39f72046e60b22f86a7342b7a09e55255bf32b2',0,'2018-09-12 13:10:50','2018-09-12 13:15:28',NULL),('4b42eb78-6d28-4825-8dff-1aec37a4d9b0','c1f277ab-e181-11e5-84c9-a45e60f0e921','743e3de9ce098e9b7ee9d753725c192ad67827c013871c71acf02eeff5cfb14d2dde24261e83e7eee8b76d61374144ecb60fa67c5d909b036bd1bfd0b57d17cd',0,'2018-09-17 14:59:06','2018-09-17 14:59:06',NULL),('4b686602-5177-4e09-ad4e-9c2fa105cf42','c1f277ab-e181-11e5-84c9-a45e60f0e921','3704cf64f048a2409698027f8b8ea5796049ea299360553efdd7a8ab986804398269b4e64b0e16be4d7504e9751e3e2ea1a2ec58a698103319a7de3d7ddf8639',0,'2018-09-18 12:06:29','2018-09-18 12:07:07',NULL),('4d6720ac-0920-4e92-bcba-023160c16c3d','c1f277ab-e181-11e5-84c9-a45e60f0e921','71f4505f84f501244b233fd0bfe01d073b755c39f4db6b191e9efbb7cfbe2709a5997c54c476d9deee0e7df95c9cb1f4d10c35f3b219ad4d22c246edf0c0e9dc',0,'2018-09-17 10:16:57','2018-09-17 10:16:57',NULL),('4ed91bd4-6f98-4d9b-938d-c8d584835f51','c1f277ab-e181-11e5-84c9-a45e60f0e921','b3fa61476b49987588a5c5939af6ec61f21216b8cb6579cd9653e65fca8d803bb05f70275112df14db4ac6f084f00ca362ea42b129c68b39231d82f7743d18b6',0,'2018-09-14 12:44:04','2018-09-14 12:44:04',NULL),('4f69e6a3-3f44-445b-90e7-c4c5320256ab','c1f277ab-e181-11e5-84c9-a45e60f0e921','47fd3ceb95bcf0b1f73c5cb86ab9ab23d10e4a868fd3948e3541e5529442aaeb5bb5ab58ce023cae82f6a12be7836b12884cc0853526993d4302651830c6f559',0,'2018-09-13 13:56:07','2018-09-13 13:56:07',NULL),('50c28607-fcd2-427e-87ce-2686ea52b5b1','c1f277ab-e181-11e5-84c9-a45e60f0e921','73535382e2d9d0dbfaad48ba20afe3fa8f30bc13c6cbd110f8ec7f762c0d09d6a62c887dadf2ab8af341825b9dffb9e96036ab4b440ab6e9fa71b9cc7c9be882',0,'2018-09-14 12:02:38','2018-09-14 12:02:39',NULL),('51130d74-7c57-4c9c-b996-340623dbca7f','c1f277ab-e181-11e5-84c9-a45e60f0e921','8bf18a410f77006b6144287e7ae09b50a7908a092ae3697dcba3ecb156e779803c4db0fe7230880bd70753e8027d80adbd0796c6bd35677b595726aa859d2a09',0,'2018-09-18 12:10:40','2018-09-18 12:10:40',NULL),('513ab378-b05b-4017-82f7-983a4a963c5f','c1f277ab-e181-11e5-84c9-a45e60f0e921','c3e673995a3034956f9132038bc777131e4795db861957aa6629eff6eabb169ce8cab7a21cb5baa9c50b3e2e12002e9690861822e3f03eabd9958cbab860c2e7',0,'2018-09-13 12:04:51','2018-09-13 12:04:51',NULL),('523d3f82-835b-4963-9c4c-5d30e531e5da','c1f277ab-e181-11e5-84c9-a45e60f0e921','ba07676522b5ebf1b09d5f9befb3607602968c9bf9a55dc6d18ebc306af88af5ee89be298ddbbacc2babbe45c045cc0b035d2f09ca2ad68b7b0120835be2580d',0,'2018-09-17 11:58:45','2018-09-17 11:58:46',NULL),('532d2232-cd7d-4316-8fb5-cf018908785a','c1f277ab-e181-11e5-84c9-a45e60f0e921','d011f12ce226cf8fcafb6f99d68d8dcbd45d3813338a45edcca6c644e70105bd890f3a34879d1e31fc30333558650c8bd37c080e4766abd29b73728ff122374a',0,'2018-09-14 11:33:57','2018-09-14 11:33:58',NULL),('53e0ea84-4698-4b8a-a48f-b457f4d4516f','c1f277ab-e181-11e5-84c9-a45e60f0e921','8b03c658b39398ef75adbd71b232c7a7cabe5a3cd2d43ae15beb33a7a2cf29ab06fa3ebe424c857f2662ade0e8b52b2b163a475ff1f67bc7dbcd6366ff571ea6',0,'2018-09-17 14:29:34','2018-09-17 14:29:34',NULL),('5431fed7-46f2-4569-b56d-224b22a62b14','c1f277ab-e181-11e5-84c9-a45e60f0e921','fa41b85e6ee7638d08553fc5c5b78d4611745d8822339f1a378290a871793acab5d4544e571f24573a784cabff14344d52829d89410732ba07081034e6d3b65f',0,'2018-09-11 09:57:57','2018-09-11 09:58:03',NULL),('574a4c19-afbe-4002-a1ad-a12baaa95ce5','c1f277ab-e181-11e5-84c9-a45e60f0e921','8a6fa21203125a2101ad453668787f020cb9ff7f75021b35823a20c6c6df4d881f01d2cee9aaffefa7545a9d55756dca143923060afaf46e4d14007e8cdb7ea6',0,'2018-09-13 17:06:46','2018-09-13 17:06:47',NULL),('579bc569-4414-42b2-a522-efae159ec8f8','c1f277ab-e181-11e5-84c9-a45e60f0e921','2b75e51ecf7aac106f90d009829e1bd1552809ec30471eea86b6a05438250142cdb3e154a17d079a3d7cd3d3aeb108e003e58ed7181d250fe87df57d6ef9e81d',0,'2018-09-13 16:46:49','2018-09-13 16:46:50',NULL),('57c03a37-6d0c-45e2-81da-5cbe99115942','c1f277ab-e181-11e5-84c9-a45e60f0e921','58d779dbf7639526c6e8b54b330a7f69c23f09ba01c60fa7ad6996abed76084f5841ab48cef046b93e2d1842a59dda128f5e8a1333ff5fd7ce27199e6ddfa9bd',0,'2018-09-17 15:58:14','2018-09-17 15:58:14',NULL),('5910684e-6fbc-42ca-bb0f-2569c9192931','c1f277ab-e181-11e5-84c9-a45e60f0e921','34ad06b1fc1e60aaf558a275d850c99532a71bb59d9c15acaf6fda2257b2745d81bfe2147c0ff7de30e49b8f4662318af8e3e8c81da1362900dd4743da96f782',0,'2018-09-17 12:40:45','2018-09-17 12:40:45',NULL),('594aa7b7-ca81-4cdc-bec4-b3ee582cba05','c1f277ab-e181-11e5-84c9-a45e60f0e921','4a6e40e264efc98c9e0132ccd02d218af1979d7af1329620f999d04c3ae604ae7aafac4b3be6aa2ea685718119517606ad9e6fea6407ae545c7164ef6239a735',0,'2018-09-17 15:08:14','2018-09-17 15:08:15',NULL),('5c3df826-8b5d-447a-8893-12008ffae26c','c1f277ab-e181-11e5-84c9-a45e60f0e921','e55b597071e569a73bf8c5d2b4816f1a63fabb07426ca8506108533cb005aeadeb7c7030b54c61a4098e59f5c8fa984689094365d9ca81e19c03a14d63194c4a',0,'2018-09-13 16:49:37','2018-09-13 16:49:37',NULL),('5caa441c-c63d-40a4-b1aa-1db6fe702207','c1f277ab-e181-11e5-84c9-a45e60f0e921','bb840e42e2a904b22c5a8751bcb0aeaa184063dbaf5f81e18b3bfa30845815db6561aee43ce0f83fae8f316cefcd4719ea742a98f4347fa54b47adf61367fb10',0,'2018-09-17 11:03:50','2018-09-17 11:03:51',NULL),('5ef22f13-3367-4a15-bf72-34f51a534bb7','c1f277ab-e181-11e5-84c9-a45e60f0e921','0b191e72f3167a58e3e4be2655281990388986971a717c2c093b72f971e9a8034c625fe90f589c21197c3ce92e72024bd5bc3a5a94682d6905d572c24a91e486',0,'2018-09-14 10:42:04','2018-09-14 10:42:04',NULL),('5f157f1d-b8d7-4b35-ba89-409243f031ab','c1f277ab-e181-11e5-84c9-a45e60f0e921','0ffbb51b9894c37b14a8269b45cbf602d421b14b9b985c74cc04c90d39a19215d1f3e5128dd43a0925dbdb13271a7dfea01c2576fdf5b0eb024a73de9594d355',0,'2018-09-14 12:17:02','2018-09-14 12:17:02',NULL),('5f72bc29-163d-4453-857c-558d966c0dba','c1f277ab-e181-11e5-84c9-a45e60f0e921','506bff0c9b94c6dbddc6df69c57ce17b5f6104db0a1b82c82a121133fcc3603ba8aaa4c99a9958eb315eca22cd7f996a50b85acca81df8fb5bfa9f59859f38bf',0,'2018-09-14 10:42:37','2018-09-14 11:29:19',NULL),('5fd3cbb9-c7ec-4c33-b479-b2b95fdfc8cd','c1f277ab-e181-11e5-84c9-a45e60f0e921','a29dc958961a62d4232643b47cec0328c0b48e8f3ce8a72d0ee806bed3db743575c66d37979d5e5b42370498f8aba79f7516ceb07d459ae779a90a8cc03168ce',0,'2018-09-13 17:09:42','2018-09-13 17:09:43',NULL),('5ff02744-f053-4bac-b796-e2e8ba86af7e','c1f277ab-e181-11e5-84c9-a45e60f0e921','65d6e62d91a52fa5eaaa344f2a21bea32b5514f5a5845d7cfbad5309636be8f46158d9bd3bc15f28c3054ab14f60e9c9f7e306cf15fdb7482ee37611f069df06',0,'2018-09-10 17:49:01','2018-09-10 17:50:35',NULL),('60200d99-302b-496f-9e95-632889e5b0e7','c1f277ab-e181-11e5-84c9-a45e60f0e921','7f0b92e94213e68d864d792ca99cb046860b28dd38ae86e70d6d49a8fffa48eb42398d880976b354f1704295ddb7fa3fd85490c78d3e9be1489f7a33a5b01a5e',0,'2018-09-14 12:05:11','2018-09-14 12:05:11',NULL),('60efa5ee-4c15-41c1-9518-a47a73362bf3','c1f277ab-e181-11e5-84c9-a45e60f0e921','f03aa470e80236f04947fcd0f494f26c75113a1f190145125d942cdb54a909c8f910e886573ca1b7c27ce3563858e55bddff14b55074576004cc1d2515d96347',0,'2018-09-17 12:20:58','2018-09-17 12:20:58',NULL),('63c4b109-9927-4073-be3a-cb0daa7179bc','c1f277ab-e181-11e5-84c9-a45e60f0e921','3b72f23d47e54e8ef289f083a3d4f8ef261963c306c7c704de31e5ed1167b238f7c922d5108831add74bc68964a5740e453176bea253cad735e850585fe531e9',0,'2018-09-12 15:45:17','2018-09-12 15:45:18',NULL),('658ae8e0-b2bf-4970-9e77-8c9149b724c4','c1f277ab-e181-11e5-84c9-a45e60f0e921','0151370421ae1007120263a2c989a86e42bb2ab0511d0e8abfe956ce4758a1556c349f0e5bde2cd3e7abcf60f00735b3ad6845b250dff58ea6ece9bbc5a6f54c',0,'2018-09-14 11:43:09','2018-09-14 11:43:10',NULL),('65aa31a1-db4a-4ba8-8155-a5874ba1cfa7','c1f277ab-e181-11e5-84c9-a45e60f0e921','9080909c62b9eab492312efcbc04623d22ba5fd541e8bfb56f70922016bd94ec446015860b3a4ffa40cf90cc413535c43f820a22853f75545ee375b16fced3b1',0,'2018-09-17 12:48:47','2018-09-17 12:48:47',NULL),('671f3459-c441-4761-b8a4-202ac6e07b1f','c1f277ab-e181-11e5-84c9-a45e60f0e921','5842f8af6f94ebaad78a9fe09f76a4e2a4b42b2da997ee722895c07103c26d483f6ee215e6c4b690b5bd5cf1ae996a9956884214da9d20ded269e67d6c709752',0,'2018-09-13 15:51:23','2018-09-13 15:51:23',NULL),('67a5a916-dc74-4f6e-8963-f8039b3fb922','c1f277ab-e181-11e5-84c9-a45e60f0e921','43f389667b04c19962b0094782db7e8cda6fb6bac6de6ed425afc48aeef1a5033627161b603389313f1c908d73d7f08d5abe87a2616bc3012df27b8964943f5e',0,'2018-09-17 15:17:49','2018-09-17 15:17:50',NULL),('694d7aa0-6ac6-4a45-8f0d-ff45d56b49f3','c1f277ab-e181-11e5-84c9-a45e60f0e921','c6f99d799980cadc4c4224238e443476c8d28bc4c2bbd3d9dc88d7b3a2b6c0a0f5e0b63a491779cd1b747de47c7d2e9b1a3bbf9ef6621b8029c7d143d303e986',0,'2018-09-11 10:10:24','2018-09-11 10:10:29',NULL),('6a9a1325-47d6-4e83-8319-4667ac47ce0d','c1f277ab-e181-11e5-84c9-a45e60f0e921','c289f70640fbb6b98f4bd27d277a7668c63458e18a66e11b6a58944724f7661a8b927ecce139a4a35db2f78fec9a8b261f49a4bba0765272d4cd03d55b257c77',0,'2018-09-14 12:42:01','2018-09-14 12:42:02',NULL),('6adae2ae-2375-4edf-b5ee-5fdfbeb5db05','c1f277ab-e181-11e5-84c9-a45e60f0e921','9e8b451f1450cff2e93c4f694efa8adcce161aaddca123d7e1bab2c085ae4aec85973807f5cf19c26733fb904842c8f0d889fdbf58bd4fa69d6c6b9f71afa92a',0,'2018-09-14 12:35:36','2018-09-14 12:35:36',NULL),('6bf86149-e22b-4bfb-aca2-f0e4c6074dfd','c1f277ab-e181-11e5-84c9-a45e60f0e921','9718bececfb0331d4068b4dc55828b995c64c1a10d5ae41b73cf0b89119b06c89ef43bf20d9ef8642dce2ed7887f3c70be989c7c9a42f7df377c1c63442ac4cf',0,'2018-09-13 11:48:33','2018-09-13 11:48:33',NULL),('6ca1546e-7eed-45e3-b22e-fd4031201ee2','c1f277ab-e181-11e5-84c9-a45e60f0e921','2ebb64f01f407da5ff01abeb7f8c3ebb00583d25120452523c474c514dce082c5132097b7ad7238a9c0692494ffc4ca53af5f60221b55b0fc5e8e68619f21905',0,'2018-09-12 13:16:21','2018-09-12 13:16:21',NULL),('6cb5f93e-5b8f-44fd-a0c5-c44159737be7','c1f277ab-e181-11e5-84c9-a45e60f0e921','1689787de4b847977906f6ac1efedb553b796ead356e0c719878c9cba00f50441fc6a81c9c3f5aefca65c2a895aa109480aad737ba6f06a69bb39a92df29a1af',0,'2018-09-10 17:52:33','2018-09-10 17:52:52',NULL),('6e79efc0-4a03-4db7-8402-d022b6643fd5','c1f277ab-e181-11e5-84c9-a45e60f0e921','d50f6cacfe53b01b8b6f3c977da867ff1d3ebf20ad6dfe34836970a5b3ec33d6aee796300f9a997f7077819e482581247419ffe7fd47bbc74fa9fd49f912f3c9',0,'2018-09-17 16:17:48','2018-09-17 16:17:49',NULL),('736d0cf2-02ea-4ffa-aaad-4be817d80f5a','c1f277ab-e181-11e5-84c9-a45e60f0e921','ceedaf34414dbd43ee9b4f9e0a66ce18bf0559cc14d0eec1e467850fddf7e2ed9f6f5b03872b4935e9585e608e3772c2fe9ad051d8103bcca5a4b02ca0b1f283',0,'2018-09-11 10:07:38','2018-09-11 10:07:42',NULL),('74220e5e-dfab-4785-9447-daf280dd9452','c1f277ab-e181-11e5-84c9-a45e60f0e921','a0701239c2c84e5581086946934484b292dfb66775321a2d28a10ae57f31877aca3de424a065c298f8ea53e5cac8677012e4b05942d0b0829364dd4665533b5f',0,'2018-09-17 12:26:11','2018-09-17 12:26:11',NULL),('75065304-4dd8-46f1-8443-ea2b9bcf92bc','c1f277ab-e181-11e5-84c9-a45e60f0e921','16880269dd5935d50c079190678a05e626b731bfdea900275478e1ad3195a1385d1b0041a58be94e9443d1e31a765489ae157d778728c1b80d0e3ee49c63f289',0,'2018-09-10 17:51:37','2018-09-10 17:51:41',NULL),('779f6a0b-8b26-4412-b611-bb2363eca2f0','c1f277ab-e181-11e5-84c9-a45e60f0e921','f3495c50e1901afd689244ac845318bc56b7a658f3820b5d0f9adb192e95a3c9faf7255d3bbab3a05d7845f201caf04137b2d788e52f8b20702d8d97ed257151',0,'2018-09-17 13:56:26','2018-09-17 13:56:27',NULL),('78a82827-71d0-48d9-8a61-25a86cab8fa7','c1f277ab-e181-11e5-84c9-a45e60f0e921','5655b7690818873a8973d12d7b6009dac61a59eddbb8a7cac1f2bc5666b20a6058e29c8bb56e33a0d36d9cccd3f26108aede5ffac93fe16e5779983de17613d1',0,'2018-09-13 16:35:36','2018-09-13 16:35:37',NULL),('7a6b61b2-bf73-4b98-aafa-f9d09f862c89','c1f277ab-e181-11e5-84c9-a45e60f0e921','9c7adeb5638d0eafefabc1aaddab4331ba3b5199daf2e5490b2a4221d81fb5cb25be6da4db7a51c1b489c9dad01930b25e1d1f4a6bac3b29cd11f12245bbcbc2',0,'2018-09-14 12:48:51','2018-09-14 12:48:52',NULL),('7cc9bf0e-42e4-4ded-965d-89a947a62d28','c1f277ab-e181-11e5-84c9-a45e60f0e921','ab37c79cabdb656aa96161224cd8d8240ded820c7c43742d44362231a9f4d650f712e6429f1d9659c46af3450d06ede2e72f1248ac006a43188ddfe9a6bc6e59',0,'2018-09-14 14:55:02','2018-09-14 14:55:02',NULL),('7cd758af-2922-45c0-abe8-736b26216cf3','c1f277ab-e181-11e5-84c9-a45e60f0e921','20822b7ea15250aa4f9fdd60e5a74966c3ba2ce420dd82c276b66f08f51dbf5ddd43171a894b04eb2c6dbbb792b06a1e31ecb92c775895390d51390561c50f26',0,'2018-09-13 16:36:56','2018-09-13 16:36:56',NULL),('7dd46d27-13a8-4cef-af52-4e57d89d45e1','c1f277ab-e181-11e5-84c9-a45e60f0e921','978340fbed9798ca39803b274a401cf10cf03eac2d06d511b2101c04cdc5a4532e7c6649a264673600969cd82a8d65f3fe0392d375de2e45ad7c0c45401e1898',0,'2018-09-13 15:11:32','2018-09-13 15:11:33',NULL),('7ed19375-7287-4bb4-82fb-becd64d78a28','c1f277ab-e181-11e5-84c9-a45e60f0e921','d1adbcedb0179e54e18960690ef4229d3237d28e9e2fc3b2352767365195890cac80fb055282a6310cc80992d52dc6e546f4cab8e1fa20e6872c022ccda0fec3',0,'2018-09-14 11:44:08','2018-09-14 11:44:09',NULL),('7f64662f-5383-4b13-81b3-f8e5c7ceddec','c1f277ab-e181-11e5-84c9-a45e60f0e921','7a32d56887dbfbac276083c8e32c8b0654f9d105cdb01306b6fb1f78a7a47381a80480481b4dace94875845b3f345b863480c451e9546e7bebdb4275a88f9718',0,'2018-09-17 14:50:26','2018-09-17 14:50:27',NULL),('81706047-75d5-4f57-b4f7-d14f54d24eb5','c1f277ab-e181-11e5-84c9-a45e60f0e921','2fe77056558f3784182950174574080941e42d7f820adc45dfc5405fddf9e2693fb4cc751a66b7bf862119c6b927bba0d1df860ad1b593aedb8a8605165e7fdb',0,'2018-09-11 10:30:34','2018-09-11 10:30:44',NULL),('81bb4507-cb21-49a2-8382-d04dc5c646c0','c1f277ab-e181-11e5-84c9-a45e60f0e921','6dc7b59a2b8ee2a4cf04ece2fb9d0a4d58b8d37514434a240cc3fcab9f9faa7e701baf3a4e8f531837f4cdbd7b843242a1cfe03d27727687794d9b8ee04abe6a',0,'2018-09-12 16:16:06','2018-09-12 16:16:06',NULL),('82a69d30-d248-4bc9-89cd-3e24dddb8d1e','c1f277ab-e181-11e5-84c9-a45e60f0e921','3802e7daaa34fb1005e1f8847027237e77f08cb9cdf41e0c7b203766a9954cddef14342d91a84654a42482c39eff69458f78114e877a6058bceb0c2b3c012b8f',0,'2018-09-14 12:24:33','2018-09-14 12:24:34',NULL),('83aa8e44-6d7b-4652-b177-074df65a2f4d','c1f277ab-e181-11e5-84c9-a45e60f0e921','91df0b8f5572237c0fc6336602262fb1c73acb91bd88a77684d9b38f533d81fbe35a0c85f3d418b882e185f989cafa342480ee61fffc1a79ad3adcbcd3455d2a',0,'2018-09-13 15:17:04','2018-09-13 15:17:04',NULL),('83cecc72-596f-4998-8327-799c46681124','c1f277ab-e181-11e5-84c9-a45e60f0e921','b370a2701b6639bd7c27395bc02c00fceeeed40c8b2375891e109552934db7fb5137f47d8857100a4e97d5217b4c2aec08b4634951273c638f0c22119397180b',0,'2018-09-14 10:35:12','2018-09-14 10:35:13',NULL),('84bcda00-143c-4812-b3f5-1ca828e5c038','c1f277ab-e181-11e5-84c9-a45e60f0e921','356ec3eded13891569be52964dbec42fcbc4f45d1c025e9ac632c6d35d1080ad09e5cf00656bee33f9087f995dd3bff16e1669a1348b251dfdae52396e0c5b86',0,'2018-09-13 15:08:07','2018-09-13 15:08:07',NULL),('84cc41a2-5659-4ed9-944b-4ef2d14bd293','c1f277ab-e181-11e5-84c9-a45e60f0e921','0f0e7ffe73ff4d22ff2c911bae84cb96d4708019fb45a38a68cd6b775c023c43cc0ddce7ff8c7bf9f6e7b7f57a025348355655331009d5e22c2f4424c4daa32f',0,'2018-09-17 12:12:55','2018-09-17 12:12:56',NULL),('853c4732-563c-415d-9d2e-f67991a52970','c1f277ab-e181-11e5-84c9-a45e60f0e921','c9990807a37e6104c7e39611c3d328a139eee8fedf28f60c92b657b3fbcafcca84abf053feacd3625fabcd1eecf43d028f40d27897b9582aeb13d213d1b2e6ab',0,'2018-09-14 14:09:57','2018-09-14 14:59:16',NULL),('85c96c88-d5dc-4348-8c94-e1396935e63e','c1f277ab-e181-11e5-84c9-a45e60f0e921','c87a336140baa65d1ed150ec11a2590ba117281baeda3899f94a3a18b0bdadb9db76da43368aa0d41bf0bb736e3562a16d9c2a6117924d354a5feb35319b1063',0,'2018-09-13 17:12:18','2018-09-13 17:12:18',NULL),('86ac8c2b-2496-4a9e-bb1a-d31c176d2860','c1f277ab-e181-11e5-84c9-a45e60f0e921','399724d4c08c39420b4ae8a0865d8dd05d6a500507ee502530940377256e6fbb8a126f4bd3350fda221eb86ebca64dce25051af8f5896d130cf81f5fc65811e7',0,'2018-09-14 12:17:57','2018-09-14 12:17:57',NULL),('87566d84-187a-4488-971f-04339c2b3db4','c1f277ab-e181-11e5-84c9-a45e60f0e921','8dd50ca0bd3b4e680219c8341ec908f951e9fd3ab765445bde13b3e7fb582adba4468e7c6ecc62e9415e18c012bf770af4892960d0c1d0527cd42688271250a1',0,'2018-09-17 14:09:46','2018-09-17 14:09:46',NULL),('880619ba-e058-4387-ad58-63c1e9d5587f','c1f277ab-e181-11e5-84c9-a45e60f0e921','7c916bef81079eaadf5c891f5d98614162161079e78c456135bfe135d05ec53e18715d411eaf3c0e721aad07d7b8a6f07be825686d021b4c75c6319a673f6f83',0,'2018-09-14 11:28:42','2018-09-14 11:28:43',NULL),('898160da-7870-482e-af6a-66a210ff3690','c1f277ab-e181-11e5-84c9-a45e60f0e921','39c03f195e689538257b63a1b4b3a5b733f93adbcbe81e2dafbd493254bd7ed63b5bcb879c91553b73b867617fa42376254adf2ec5b231f6557df7e76b6626ad',0,'2018-09-14 14:37:37','2018-09-14 14:37:38',NULL),('8b0e23d9-fcea-4807-ae10-a38c5edb5f58','c1f277ab-e181-11e5-84c9-a45e60f0e921','1400dee2bcf9ffcb75c287b705ec78ed04dd974f26faf7a30ef628762080cc8715d2d52248fbb9246799f49e7702c03d8c078dde5e936010b9edfb253661e1d0',0,'2018-09-17 12:40:22','2018-09-17 12:40:23',NULL),('8b3237e1-c8f2-4a4e-8b5a-7355983e7666','c1f277ab-e181-11e5-84c9-a45e60f0e921','e42eeb61287c95b918c4e3e27604bc2eb03bbd77bc12c0684c1a5620e6ba6957561de3e7d6e5ca6e73ea024472b891d9723a60fe3fb0d00544db21fff02af465',0,'2018-09-12 15:48:59','2018-09-12 15:49:00',NULL),('8c198d2c-9110-4a4a-b0ea-bf902e9e4d51','c1f277ab-e181-11e5-84c9-a45e60f0e921','9a4ce37c1ad475fa4b6a2a566d69d9765ec4a90ac9d2f23b7f39f73838bebbaae3becd439194202a957e19a08104f4b523ffb0a7cc134ff26787e4f3e804ff99',0,'2018-09-13 12:14:23','2018-09-13 12:14:23',NULL),('8c7ef27b-6e2f-4ba5-a74d-541a27c37e75','c1f277ab-e181-11e5-84c9-a45e60f0e921','603d6a27069f6874f65f2a44aa33bd78acdd7d41248c85078cec9db1ad8ccd7bf9d65b2b8c7061e7313163a4c8eaa29dcf953bd42a20a04a2c4ea7c7e02148e7',0,'2018-09-14 12:51:07','2018-09-14 12:51:07',NULL),('8d52384a-f7a4-4555-9464-1ecb60496a39','c1f277ab-e181-11e5-84c9-a45e60f0e921','473440c1dbd3916084747943df7956135b2865047edb8891630cab74959fa578c0b9f6cfb176ccf2e0879ce2a67874a4e88c9831ee81adaa7aeac2df639657f5',0,'2018-09-14 12:06:22','2018-09-14 12:06:23',NULL),('906cdbdf-3265-4a24-a5e9-f6470b09bdcf','c1f277ab-e181-11e5-84c9-a45e60f0e921','798e006849b10dc0e959eaa7ee5c7d7d0fd0db3ae6de735c62c3e9a33acde0cd517c6c0417d1488c2e70d12d3363e99c7c418655e0f2ee421bed8ffaf4b9a1d6',0,'2018-09-17 14:10:55','2018-09-17 14:10:56',NULL),('91bcf9b7-e721-4a04-81cb-1ab3efc1d3a5','c1f277ab-e181-11e5-84c9-a45e60f0e921','45492d1bd74620ced3d962dab47aa0032149151a456da73c20e9ea5f0a21810bfb25ac5c870e475bfa0a029f6b0f89a2b62639c893bf635d96d08fdceef67ee2',0,'2018-09-14 10:38:37','2018-09-14 10:38:37',NULL),('92c68b8f-281d-4b98-884a-cd461e64baa8','c1f277ab-e181-11e5-84c9-a45e60f0e921','3a86353338101e0ef2412736af4c0bccebb3b2065ae676221da6fa263f20b16c42738f7c9305ad8f8520ebc2291ca24d81bfd3824d55ab9cf8ebaa6da09efc6b',0,'2018-09-13 15:37:24','2018-09-13 15:37:24',NULL),('93c1d2f1-413d-420a-a468-bc57b47329ef','c1f277ab-e181-11e5-84c9-a45e60f0e921','db061a453f1884cc261e00326b9f394550a768c81fab35be961d95ac4a7db5d9720acf5996288e367017a68a4577e9127e07b69561a60c2547f4ea009fa98f11',0,'2018-09-14 11:00:09','2018-09-14 11:00:10',NULL),('951dbd7a-0021-4ecf-b0b8-5d17b81982d2','c1f277ab-e181-11e5-84c9-a45e60f0e921','9a77d209b62b97683bf93b7f3c8abae553eea43aac50398cb2a732ea5e45ebd3f3988088bf9fc195cb6ec04b9000cda12349b8b0e55e9304e7d66f20a43d8836',0,'2018-09-14 12:11:05','2018-09-14 12:11:05',NULL),('96267d1f-744b-4eff-904c-1c3d411ecd45','c1f277ab-e181-11e5-84c9-a45e60f0e921','d1bb3f9436359769045b63c6c306a2bed602cc38df0ad7e7b88214edd97bca4d88bb7260c253b33d92a9f2179197d9ae30cfe32ece7c377d672189f73757521e',0,'2018-09-14 12:49:49','2018-09-14 12:49:50',NULL),('97cc76de-ee8d-40b2-ab76-2aff3b303e34','c1f277ab-e181-11e5-84c9-a45e60f0e921','099bba44410f91f892609d253fd1f99cffe5fd02fbba9e511ea5e3297993e53e4faa8d9d33827e5bbdd49ebe1891e22ccc1ed6b29bc99449f327febfc1e926e4',0,'2018-09-13 15:15:22','2018-09-13 15:15:22',NULL),('984dbd2c-a732-4a7f-b464-4f7554f7eb46','c1f277ab-e181-11e5-84c9-a45e60f0e921','51e1d592027c82b93cda310df499af5082b6b4f3f8bd755c2cc9b9e7ed02ab57e0fd9d68db93bccbfadd44f943f5623c6ac34f72cb283d87cb81c26e61f62a1c',0,'2018-09-17 15:50:29','2018-09-17 15:50:29',NULL),('9c058752-cda9-4375-9924-96a59f65a41a','c1f277ab-e181-11e5-84c9-a45e60f0e921','3115bf9a88f0155c16819fe015f64b66d7775f946ca720c99e89b677ac130adffbf387456ffe9ca9f60e8dd919920404ce4ed643cbb69d1748e209512d8c216e',0,'2018-09-17 15:52:42','2018-09-17 15:52:42',NULL),('9c18fb92-0b15-4ffb-a802-d1e3b6364a68','c1f277ab-e181-11e5-84c9-a45e60f0e921','ba6a01348401a49156763594656473c93402b64492f060be0e344c9782af40523dee2ebb40095ddb9281efc22e441167b609dd75943e5c453c353739c73a629d',0,'2018-09-14 12:32:12','2018-09-14 12:32:12',NULL),('9c54626d-c09c-4f93-ac8a-decddf178076','c1f277ab-e181-11e5-84c9-a45e60f0e921','06d309df96c9fb1a2ed87730fb0cb68a5b8e1c2cb1e27ea00839a458ac4222e890a317b9d0e7eed91be7697240f441ba4d0e14d6512db4cef89b4300ece6b122',0,'2018-09-13 15:54:55','2018-09-13 15:54:55',NULL),('9d1d1629-5e6b-47db-8ab6-87757e6242e7','c1f277ab-e181-11e5-84c9-a45e60f0e921','98f951dc9996c8edc30e4d69aac00256b06bfd8277e6673d24c25afc4f82c20fc56f6a0ca0893db9d5eb179511b8377a4743c9751e4c21f72ecd08d36ee1e7da',0,'2018-09-17 10:55:01','2018-09-17 10:55:02',NULL),('a091a95f-696c-478f-98d3-7a9c93469e32','c1f277ab-e181-11e5-84c9-a45e60f0e921','0f1d971c0a8a585aef28e2cff55d6390d0642ab71a0d9c2e4fcd5aa663d668df9bb7adab5aa904d7e68f221226cabce6bcab7338a8b7acdfc5f192b6be672045',0,'2018-09-17 16:22:37','2018-09-17 16:22:38',NULL),('a4ac5bee-f376-4dcd-b37a-14524cc80ce7','c1f277ab-e181-11e5-84c9-a45e60f0e921','6a95ba6e59a2dc951864bf23a092225366b7d6334a36e72ec6e7db8ab26f942b8a4dac503c3ae2d4a12c52cadc5a4cfcecb26afa2585e155e180c67fd339100c',0,'2018-09-17 15:53:44','2018-09-17 15:53:45',NULL),('a612d7cb-e1e3-4990-9391-a3a272404bbd','c1f277ab-e181-11e5-84c9-a45e60f0e921','2d235986a550351f177977fd6beb93c33010a036a7cc830cf45842edb6671c8f9d53ec133c155187652e7cbb45abb82a5509efd5d434e4cc87a629aa35c93476',0,'2018-09-17 12:13:46','2018-09-17 12:13:46',NULL),('a6c16ca1-605d-433a-abb4-4f862839d034','c1f277ab-e181-11e5-84c9-a45e60f0e921','a6133dface701d99b0bbe579bc69dec698172fff6f57c34e3288a7307619ae5631f73fa047e152cb3435c790debf4d21a811d1ae60b86b1572c302250ef20ffc',0,'2018-09-13 11:09:51','2018-09-13 11:12:00',NULL),('a7571d1f-f649-45db-8047-1e5e1f87d284','c1f277ab-e181-11e5-84c9-a45e60f0e921','d0753a9084041debc252b3f86e1d27f1352dac35c8abd00989f6d2b21c17a6f68d727d9a437e50ae3eba5010c0a2f72dec36e3d29a68786eb0cd745469e6478b',0,'2018-09-11 09:58:27','2018-09-11 09:58:30',NULL),('a7eb9a2c-18a6-4fc8-9c35-ca7922a6bc74','c1f277ab-e181-11e5-84c9-a45e60f0e921','4659b4afcb7507a4cacd8c07878c20647d5ec776c183f8b0de619bf3643452a145c88a8b98a3ad913032fce844b25b72571fb07583bbb690b200df9fc4136097',0,'2018-09-14 14:46:16','2018-09-14 14:46:16',NULL),('a8d06b39-bb03-443c-8150-1c0c1a97dfcc','c1f277ab-e181-11e5-84c9-a45e60f0e921','e5426b2b7cf53417b2d156b6cb96ff932b3eef28ef0115bdcf2f7c11ebe7e8a86f46a87121878813b0793962b3a3ed5ab54d49495597f833252ab91a1df90c62',0,'2018-09-13 16:13:59','2018-09-13 16:14:00',NULL),('ab330405-7f6c-4165-85d3-72e92aab4606','c1f277ab-e181-11e5-84c9-a45e60f0e921','ae8a22a4060b3263b8274097b52bba44afabed3d59915a117aebe46080c16dd89fe3a18dc19add557a601b4775c58886e3bf1f200d799a02424363a5858a6a94',0,'2018-09-12 14:39:30','2018-09-12 14:39:30',NULL),('abe8f625-64e8-4b4a-8c06-b0479d1e132b','c1f277ab-e181-11e5-84c9-a45e60f0e921','095506555c96180400572f5144051c84994c3889d65105246e41518d21745d685e1199aabbd46e700f6be4871fd14c24690698184e62bb6064929cc5c9fa4077',0,'2018-09-17 15:20:10','2018-09-17 15:20:11',NULL),('b084a02d-d223-41a2-8da7-64a4281c5605','c1f277ab-e181-11e5-84c9-a45e60f0e921','1f79b359c7494775587ad794035069313f00eb34ed18a6eb2c8865bebe6452ce54638ac924565bece7eb4e9d780b7bd7386a5400af4a7518b6e1264d59437fbc',0,'2018-09-17 14:08:52','2018-09-17 14:08:52',NULL),('b1530414-8a7d-4913-afcf-118610eb7e20','c1f277ab-e181-11e5-84c9-a45e60f0e921','3221a999be20a3e785ae6d606abd195ff9270b57f61d3a76cdedf0cbaeafa3c3cb4c7cff77d23abf1e1998b5018adebda438b62d0d4b6f5593800c43c3e2f103',0,'2018-09-17 10:39:00','2018-09-17 10:39:01',NULL),('b231a443-057f-40e4-b2bc-e7425c790435','c1f277ab-e181-11e5-84c9-a45e60f0e921','37df607b8d0f10346ef9da76736b4333fc77610d11320c6294ce00295895cc5fc927f59c312c38201999123f1ccdfaacf0269b0841f2bcafc2889191646c64de',0,'2018-09-17 11:01:09','2018-09-17 11:01:10',NULL),('b321c228-d219-4a64-a564-1eb63253c981','c1f277ab-e181-11e5-84c9-a45e60f0e921','f9ff297fa6d963a6450413d28a90ff87f923e7ae4b7cac04d28bd783c121035a8f09b9965fcf52f26a47eb99c4e59291daa9a51954a674a89be8e1180f44af80',0,'2018-09-13 16:40:30','2018-09-13 16:42:21',NULL),('b3a54ec7-e95a-4899-9207-18c78ecbfe14','c1f277ab-e181-11e5-84c9-a45e60f0e921','42161afeff8380b1fd6cf02c46c8186cf8674e5526e018c096ab80aa17249fef038e768eac84b08616a6b2d83d132f058a6e9e9b2389cdc79298e82af049cf3f',0,'2018-09-14 12:44:50','2018-09-14 12:44:51',NULL),('b517b7e1-821d-42b6-9371-80479a558cb7','c1f277ab-e181-11e5-84c9-a45e60f0e921','70335c7d4cda4fad89dd1055405a27d28fe1a2bb68ff3376975108006626a4b16c88101421196b6035c86d2e7e696d10fc3fde3851f02c8310d779463bf2274f',0,'2018-09-12 15:31:45','2018-09-12 15:31:46',NULL),('b5ab8349-2190-4cdd-b940-eeb431813439','c1f277ab-e181-11e5-84c9-a45e60f0e921','da7f482a1994775ccac1cce1e23bb92d8f6f1bdf9b111bd85a983ef564eb4bf8e882f67d0a8c2e630e796ecb524976f75406cae5016f15cb08772b52e2551d4b',0,'2018-09-12 16:22:04','2018-09-12 16:22:05',NULL),('b62ddf6b-ee4b-427c-99d6-96ee9946a00d','c1f277ab-e181-11e5-84c9-a45e60f0e921','3346822847ddc07df6d5d7bbeeecc9b14b5f10fbaf94f968192a4446eeb69e7ce46eeb78928496f6b6f30f307fdb936d27d2a51eb1be907b9d7ecd34738c5cdc',0,'2018-09-14 11:31:23','2018-09-14 11:31:24',NULL),('b67e158d-302a-4dd4-b6fc-265ba4ad87fb','c1f277ab-e181-11e5-84c9-a45e60f0e921','189faafe9e58717cef519b35c0830713719bc66ebbe6d27eaa9219bad2891f79e9033fd177c70389d142b52e66cff9b40209132b512efeb4e64085fc0f7054ea',0,'2018-09-13 16:15:07','2018-09-13 16:15:08',NULL),('b6a492c6-3340-432d-8261-1c78adb4d4ff','c1f277ab-e181-11e5-84c9-a45e60f0e921','8d0fa3b6cd7ba0fef7be966437e7a93f5ac4e319defd7a2154dfdf8ca19e72598f44b344fc966b46c987ecdaa97f8b6e80dc40b43a2c4ccb94632a4bfa81d4de',0,'2018-09-17 12:34:45','2018-09-17 12:34:45',NULL),('b7e69290-5f59-4f8a-ae86-8cb4d6ed6974','c1f277ab-e181-11e5-84c9-a45e60f0e921','31c0be607e787b8c358b02e01c99d66643b6b25140854afae843ee8cfbd0b5658f454c6428652bad27cb527cda084d89cb6d68bf8cd1817e9bc0cf2badec5062',0,'2018-09-14 14:49:29','2018-09-14 14:49:29',NULL),('b870dcaa-53e2-410d-a006-5788e0cedc12','c1f277ab-e181-11e5-84c9-a45e60f0e921','bae32d96c2c46b568c6d24457468679858425b7625d072752222812d1c6dc44944eada70116a906811bd12316c2d7cccbedeb6c820c4e62dcc0d0a0bec294eab',0,'2018-09-14 11:59:57','2018-09-14 11:59:57',NULL),('ba8aa827-395b-4352-a681-eefd29f365cb','c1f277ab-e181-11e5-84c9-a45e60f0e921','239cf233add9f4340ff14ac9ffe6bff3ad6ff6124d4ef647e8983630ae1498048394f44022c2a3970e62ba5ad21092494d55ccd553c264472d1bda1730ff972d',0,'2018-09-17 12:36:14','2018-09-17 12:36:14',NULL),('be6df80d-f0ff-4ea1-8341-52860058f72d','c1f277ab-e181-11e5-84c9-a45e60f0e921','cdaa4b0569c04bd61b459635408dba7fca7902cf5bf071c6700a85140480a4c6d8707ba99b4e6c054eb24a76e823f0005903be7d4b08d8865813dcdbf3cdfc0c',0,'2018-09-17 10:56:03','2018-09-17 10:56:03',NULL),('beca1687-6330-48a5-8a2d-20f33ce5ff64','c1f277ab-e181-11e5-84c9-a45e60f0e921','488451cba0e25e65b607182bbc22d46c9a5233e1da0b77f795abe9b2f289e9a1e103830ba3692bc5e05fed92773066056f0f1efdc1a54e3fb0f1076adb7131b0',0,'2018-09-17 14:13:33','2018-09-17 14:13:33',NULL),('beddf8ed-d0e6-4db1-85f5-785813a28583','c1f277ab-e181-11e5-84c9-a45e60f0e921','e7f11d13de3ab278ea14a1222668f1107559271e6ec278a947a9389352d6e7af67a94ce71bd65fc9f11ba03795f01a219445bbd9dc7c70412c3fc9b3cd180051',0,'2018-09-14 10:53:55','2018-09-14 10:53:55',NULL),('c12edfda-5ecc-4ce7-8c72-e9ee3363dd90','c1f277ab-e181-11e5-84c9-a45e60f0e921','b670d81ab53b9d2e020ce3bf37df228bd7450dfad6c8e609c10c83b204fba14ad23852f1f1906a8f7570ef6ea88aa932b54ee972b74b93d4c086d9b8ab2474c6',0,'2018-09-12 16:10:26','2018-09-12 16:10:27',NULL),('c1b244f6-d1a0-40e7-a0bf-dbba41252279','c1f277ab-e181-11e5-84c9-a45e60f0e921','92fb4322bf01a8ff346852e91b24ea04110b32b70f06aba9eefeb96a25ed1b3a17b4920de7d4a5795c20ff3676ae76a69d9e187299300f45020abbf4e48305b9',0,'2018-09-13 16:38:52','2018-09-13 16:38:53',NULL),('c23135fb-6e98-44b2-9bf4-04aa50777822','c1f277ab-e181-11e5-84c9-a45e60f0e921','a78fb4b871053f3365845fadd5700a102e7aa3ff156b1e797d34ceada4066ab4f56dce586312af266241d2fdc2cfc02d7fa68c6a55ecd6b32f89091adf2d9219',0,'2018-09-13 16:56:29','2018-09-13 16:56:29',NULL),('c3506b72-8be6-4e3a-aaba-0a3819fb6827','c1f277ab-e181-11e5-84c9-a45e60f0e921','a781cfd3805f95e9954c2c0042676620386095427ca5c179aee59617927d9f72f54abf0580ab1109a04b4bbd69ea5fde2ef72a0010be006686ecc8b5de67c541',0,'2018-09-17 15:29:45','2018-09-17 15:29:45',NULL),('c46f06af-cfe8-4cf5-8406-0e17d5b6370c','c1f277ab-e181-11e5-84c9-a45e60f0e921','16edb1b04c96c1f46081b771ff2d2fdb227e94140021b3cd97e20cf74c2ff9ff746bda444f2d0990e0bea3b64e01a62b0b0d2d221a0eb3c43ea9a9ba3959663a',0,'2018-09-13 17:11:46','2018-09-13 17:11:46',NULL),('c470f4d1-2884-4461-822d-2046c29d6d69','c1f277ab-e181-11e5-84c9-a45e60f0e921','a99fb9204f22dcbb42934c01b390dce0617df8e4284d7316045250d7aca0cca7f0e7b175ecae6db4a730494a7b0fbf89d8dd81baa27ce6c086d2e875a45519b0',0,'2018-09-17 11:05:50','2018-09-17 11:05:51',NULL),('c5864d44-d4a8-4897-970e-11740a45ea6c','c1f277ab-e181-11e5-84c9-a45e60f0e921','32ca8f121336b1e7a49c1ea3d4b9a4a802f69db1512da2b924d41396a86c7c7b0a175a53f132429bd4a1e0d861f2c60ae7b390149a7f68a1a948e2a7e1898a23',0,'2018-09-17 12:11:59','2018-09-17 12:12:00',NULL),('c7168244-c27a-4e0e-8e25-e91209fe2654','c1f277ab-e181-11e5-84c9-a45e60f0e921','e2e7f6a77999fb22be989d43ee9e0d9d13b1e06088dba1c2253ebb522b43c37301dd17ce881540df1acc588e0cb425389d3d53b386f58b8ccccfaeea7f326862',0,'2018-09-17 11:40:51','2018-09-17 11:40:51',NULL),('c78c05be-e12b-4f7f-8bab-0f32ed23f47e','c1f277ab-e181-11e5-84c9-a45e60f0e921','597f37e8241990f11d0e8884936d42ee51c148c534f8669eab6d5463e2facf34f70c3d2c298882d386d249d506e006907b70162c7046ec52d1d453f1c0e45667',0,'2018-09-17 14:54:28','2018-09-17 14:54:29',NULL),('c7dffac5-1053-4dc8-b646-89c6d2d67312','c1f277ab-e181-11e5-84c9-a45e60f0e921','40bcca1479cc0e581d3a808bfd4c82eb9e3e3c6ef95e5977763f259f2c52951c74c3e8f24451648cc40959d5a4d28bedae6be454d00d155c484ebe7868ad8228',0,'2018-09-13 16:52:24','2018-09-13 16:52:24',NULL),('c8c3d6ba-a6f2-44ce-a01b-1526c807b656','c1f277ab-e181-11e5-84c9-a45e60f0e921','fbc2a6131b153addd0eceb6d59cb701ec0addad20bfec4c726ffc8159fbb24e1a7305f007182e0b54bcadc8d4a3f2cd80dee18d214c0c676743c93eb64d8fbec',0,'2018-09-17 11:36:05','2018-09-17 11:36:05',NULL),('c91c8dfa-0242-4f6c-88ff-b06903f9f173','c1f277ab-e181-11e5-84c9-a45e60f0e921','94ef3bc13e5a660007136880ae555218c4ad1c5764a0a157dd4bb4e22db144fa95b80ebb91fc7803ce99eb1760baf553cb9fa0d4d3ffc4e694f66f67629d11e8',0,'2018-09-17 15:44:03','2018-09-17 15:44:04',NULL),('c96c38c6-5e5b-480b-ae81-64f3595545fe','c1f277ab-e181-11e5-84c9-a45e60f0e921','4682f55ac300570670916dc9a2d498c4e1c0b31869cf8bb019023203a992ad061c0633f15ac42802416f035d47f13e971bc7dc42b888f3815a37271a2db89c4b',0,'2018-09-14 11:54:04','2018-09-14 11:54:04',NULL),('c9969ed9-dcb9-4e2a-b175-f81b83c0589e','c1f277ab-e181-11e5-84c9-a45e60f0e921','6c510b3b5b4fe8afdf54f508ef6686140d83baf09c11e6e7d9f02a12553373289588903c9e4a62b440320eac570a9b37c00de16a03c9dbc92241612ce364f303',0,'2018-09-14 10:43:04','2018-09-14 10:43:04',NULL),('ca08b24a-7290-4a1f-ab12-f9581276b895','c1f277ab-e181-11e5-84c9-a45e60f0e921','751938bb749c864a88eb3847b9d6004cb544db1e2c89f015309fb2a7f1e4cfeece20059dcff4a733eac8d27226e0ce50b474a174f92e1f37b421534465652e6b',0,'2018-09-13 16:30:26','2018-09-13 16:30:26',NULL),('cb22651e-1d6d-4e46-b235-e9c9a065a952','c1f277ab-e181-11e5-84c9-a45e60f0e921','d774a8cbe51bc9ce13d7d70aeebc793833681373648f579510da098c539194f7afb0988b8c83d3d7b601f827757eb6b1a1ca2493fcea939efc0700a68862c299',0,'2018-09-13 12:01:15','2018-09-13 12:01:15',NULL),('cbb08326-ceda-4995-b0e5-16db0ecf2029','c1f277ab-e181-11e5-84c9-a45e60f0e921','a534ef9f1d89a3c5510b92b4424c6fccdb7f424d71892fc04a3c0c87c720207fc5b05bce27dd293de0df5cd15c26c01a37ef4d42ba7f8fcbd72d70b41218ca05',0,'2018-09-13 15:41:47','2018-09-13 15:41:47',NULL),('cf88b0ec-beae-4e92-ba6c-3360a0003274','c1f277ab-e181-11e5-84c9-a45e60f0e921','6494260a1a2d1aeac94a940fd8361abc01cc465620dd935c67f64db5f458379693a6e80f7c4f1b036dabd84a819b2330341b365c73c9cdb8d5ff84c5d40c3daf',0,'2018-09-17 14:21:04','2018-09-17 14:21:05',NULL),('cfb746b5-cb7f-4164-a126-2e319b7ed26d','c1f277ab-e181-11e5-84c9-a45e60f0e921','8c6c6e45106888eb6785d45f33f9533f48178bf59e08e509dba9fdc371831170ff914144cb721881400530fb9dceb565dc3b89b071cc120053300cf3031a980a',0,'2018-09-17 11:24:39','2018-09-17 11:24:39',NULL),('d2495a2d-2e3a-46ed-b7dd-e245e19c09a2','c1f277ab-e181-11e5-84c9-a45e60f0e921','d0511d3f6586293d03ef35f60caf58afbada8dcf6103d59b20b4792d3fa434cd27240383cd13cb0f6e005a4b37fa6db27f085da554e81a0b9fa88c2e811a1894',0,'2018-09-14 11:29:36','2018-09-14 11:29:37',NULL),('d844e418-c74f-4213-b5c4-81b62f6de8bf','c1f277ab-e181-11e5-84c9-a45e60f0e921','3e51f18ce7830c82e270592df1eca2bc4f9678b6ddfd3bc0c5c121fc6bf26228430fb38290b84db3839e0f00558d1ce30ccb38dcc53581fd72eb25c3284ffa1f',0,'2018-09-13 16:51:16','2018-09-13 16:51:17',NULL),('da4c1702-c17c-4835-83c8-5437a05a4acd','c1f277ab-e181-11e5-84c9-a45e60f0e921','f16656a581f53a630a63ee3754fb1e7d7f00a233b1462425ccbfef3cfd77e7f841761d48d399c0c24885403614c231641260de2f36f9557ad7475655bbcf97cc',0,'2018-09-13 17:01:17','2018-09-13 17:01:17',NULL),('da5ef189-02e0-45c1-b0b9-b8041cea9595','c1f277ab-e181-11e5-84c9-a45e60f0e921','58146633791316f25e32ecb6696f9a36f2650bfe1e9c666228987dbbd5cc218af8816f243ae727e87426e0face92ae5205856a9e61766abac37aef199ef924f5',0,'2018-09-17 10:49:17','2018-09-17 10:49:18',NULL),('daeacbdd-3c99-45e0-8125-dd3a4c021f42','c1f277ab-e181-11e5-84c9-a45e60f0e921','0e68e063db66eb3b177f98a4dd7fa2a59d7d54dba3756490e280fff0d35d0f43f9f90b35c39b025f6a0fece117b42dd48f3c8d03e7d8b7b9edf2d5b46371f706',0,'2018-09-10 17:51:09','2018-09-10 17:51:13',NULL),('dbc2049b-87e8-479e-9a11-873c4f64bebd','c1f277ab-e181-11e5-84c9-a45e60f0e921','894e4f15d25acf3949d2e5406f727a08f6f6e8573a0f177a946ac184403aeac1e04c513e23b5ff9a7fce536e6abbaa214b289b87678912e95c2c71c41a303955',0,'2018-09-13 17:12:55','2018-09-13 17:12:55',NULL),('ddb0895a-1fbf-4c43-b563-6094b97f4aa9','c1f277ab-e181-11e5-84c9-a45e60f0e921','ab34c430e8e442c7eb1760f913121959471f98ee8d8c5c0d7962f8489b663aa15d8b7e4bfde7a0145a5ce5d4297e32fbc46619fcfc73695b20ecc593aecf35be',0,'2018-09-12 15:42:44','2018-09-12 15:42:45',NULL),('de8fdeb8-20a9-4ddd-a9cc-8bae70a397a1','c1f277ab-e181-11e5-84c9-a45e60f0e921','543ff04f4d9b31572d0cac8e79ff9e5acac625818b5c3b5b1d384aa1d52982d876028c3fe8cd09299a19e33c349761ffacc997d60c29b1a6538bfb0c629c8e39',0,'2018-09-14 12:27:00','2018-09-14 12:27:00',NULL),('e1adf2eb-6f29-49b8-b580-0044c645bd7a','c1f277ab-e181-11e5-84c9-a45e60f0e921','c962b19cf12a9b1441a316a5dc5d3528475228fd3ddbe8aa239a8cda06821556ff2b8cd69094ace5d644b4285b5a10f293d2e5011eab75e4b40cf7e1dfbab8c1',0,'2018-09-11 10:01:45','2018-09-11 10:04:08',NULL),('e261907d-f053-46d3-aba2-07fcc80836c7','c1f277ab-e181-11e5-84c9-a45e60f0e921','43d726ca3320b3c98d03b239621e964a84cbaa2027faff2f9c5efde3be7c3cf8b225284f1484021db1595e7513855539f0eb26d8f706a71bd08e283920c364ee',0,'2018-09-17 15:23:37','2018-09-17 15:23:38',NULL),('e5d5787f-909b-4d5f-b690-00f8c2471c90','c1f277ab-e181-11e5-84c9-a45e60f0e921','624a338dd8fd52225e7b0c90ba48377e5a61a69be21bae4eac04677e66b710f8de4c9c71dc64c75c09893726ca042f7c8bf452af5c039914bfdc1d2fae20d3a3',0,'2018-09-17 15:57:06','2018-09-17 15:57:06',NULL),('e8047263-c655-44c9-ad27-a3357f75e8fa','c1f277ab-e181-11e5-84c9-a45e60f0e921','dc35dbf527e428097d90d11c586c2ab627ad247a44a5b1afd57df930084f8941649fd52ff2690739344da65f2738e62d531860fc369a2adf5c4fcb8cb0bfce6d',0,'2018-09-17 11:21:26','2018-09-17 11:21:27',NULL),('e845398a-0fa6-445d-a92e-c6d48c9f234c','c1f277ab-e181-11e5-84c9-a45e60f0e921','fbf44bc5458f579b838dbcb07ae1589e496c73e800f4567926d2837bfac1dfd73073e55fd912b6db6a5036f25258429894ac2ef489ae380a88cbd1ef734f74c9',0,'2018-09-11 09:46:29','2018-09-11 09:47:30',NULL),('e8bc36da-a227-4319-b052-cf7535aa10ca','c1f277ab-e181-11e5-84c9-a45e60f0e921','0ded692bd5d4fe9654b9094a45502d34b8aa5dddff0115418a5a17b49357a0174af72efcbef6fcdc87d8ee4fdeb8aa88bee13d5d0478270a200c9c0ef9843dda',0,'2018-09-14 12:13:49','2018-09-14 12:13:49',NULL),('ef14e7e7-2215-457e-8c83-1b7260c96b11','c1f277ab-e181-11e5-84c9-a45e60f0e921','8972858b1c14ba74b49ff4f769ff4f2145450e052feaa0643907f6f29d1f02b050c7ac3c4767507766683d960344fc396cdf05a8aa81517c13fde922269b0a2b',0,'2018-09-17 14:15:59','2018-09-17 14:16:00',NULL),('f52469fe-6dee-406d-9186-e0e2255c0abe','c1f277ab-e181-11e5-84c9-a45e60f0e921','52f85543e6e3f78767c653a6f6dfa1ed6f5c09a4a48c0266fda69045acf3da68e3e3f2ff48c3f96e2436aa52c941750c69c3ace24340c6347c228db9cd33f222',0,'2018-09-14 14:36:53','2018-09-14 14:36:53',NULL),('f6623092-0833-4e31-a153-35474e1b7ec5','c1f277ab-e181-11e5-84c9-a45e60f0e921','dcf5269df403c123330296cf26c33e91737d69ee5e1824079e45cb77bf91df4eb0f879f048c684fa500d006ac1489fc15ed5eeebf5bb2bf32a6c10bc2b317ef3',0,'2018-09-13 13:52:37','2018-09-13 13:52:37',NULL),('fa2c59dc-0548-494d-97c5-c6de5bc81dd8','c1f277ab-e181-11e5-84c9-a45e60f0e921','8b971629aaa72dc7731c7b8444e19cad8453f9781c229fa16b9822a4c4217c6f3c5a0a6656afb0774c948ed9b38f29ae65e586f6760598da9ee049df6b51253b',0,'2018-09-14 14:12:27','2018-09-14 14:13:17',NULL),('fa9e1c42-4237-4dfb-83c3-a0c3cb0f7a42','c1f277ab-e181-11e5-84c9-a45e60f0e921','9a9700ff1ca4a6829cf29ea2a5223b601eb69bd4c95790872c3e82aacfd10bc55731b01f96a78434a8906c0eb93284633be3834b6f8d39a34cca4d12a9cb2093',0,'2018-09-12 16:34:50','2018-09-12 16:41:26',NULL),('fc8cd31b-420b-4b84-b4a8-98ad0dc75afb','c1f277ab-e181-11e5-84c9-a45e60f0e921','db78333369c29e51b6250951182a5424e2ebeb242ebb97c0e2450e7ea74d15031e13d279e62df84690712e991c2388250e500767131d0f0bf10b65deb30f99b6',0,'2018-09-17 11:10:32','2018-09-17 11:10:32',NULL),('fdd08ab5-13c4-4bea-82b7-ce1c35b16f00','c1f277ab-e181-11e5-84c9-a45e60f0e921','efdcbf83512f66a25659bc04c2407ca060c92de77c8a8fa119d4d35e499a4411967b07567d4ad19b9fddd31a224aab2397d59b576239cdf1cad731759e10b1a2',0,'2018-09-17 10:46:12','2018-09-17 10:46:13',NULL);
/*!40000 ALTER TABLE `token` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `translation`
--

DROP TABLE IF EXISTS `translation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `translation` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `translation`
--

LOCK TABLES `translation` WRITE;
/*!40000 ALTER TABLE `translation` DISABLE KEYS */;
INSERT INTO `translation` VALUES ('003ab718-6d41-45b6-bc7f-4d6de1bb12bc','2018-09-12 16:37:59','2018-09-12 16:37:59',NULL),('0310ad5f-e129-49fa-a8ef-7c96dab847ce','2018-09-07 14:54:27','2018-09-07 14:54:27',NULL),('03bf7b25-0117-4de5-80ad-4c14bebfd9ac','2018-09-10 17:39:06','2018-09-10 17:39:06',NULL),('09398af3-3b20-4427-8833-1391637bd241','2018-09-12 13:11:32','2018-09-12 13:11:32',NULL),('13cf2681-c623-4e49-ac4d-22bc866a3f1c','2018-09-07 14:56:57','2018-09-07 14:56:57',NULL),('14279205-cf14-497c-9ce7-cb50eb32ef1d','2018-09-17 16:35:16','2018-09-17 16:35:16',NULL),('14e5f69f-f615-477a-a4d8-62519a4e53f0','2018-09-12 16:35:14','2018-09-12 16:35:14',NULL),('18169029-6827-45f9-9da3-45879b05cd47','2018-09-12 13:14:04','2018-09-12 13:14:04',NULL),('1ee50b21-fabd-49e5-b6ca-d14aed147285','2018-09-07 14:58:22','2018-09-07 14:58:22',NULL),('27f7c054-1e24-4dd4-8991-b6cdab557417','2018-09-12 16:35:48','2018-09-12 16:35:48',NULL),('286aa8c2-6d0b-4b35-a905-19cedc8640f9','2018-09-07 14:56:53','2018-09-07 14:56:53',NULL),('28ccfbb3-8864-4cb4-baf4-93fafe96f64f','2018-09-12 16:34:58','2018-09-12 16:34:58',NULL),('28e8482f-37fe-4819-ac56-7be1dfe285bd','2018-09-13 16:41:20','2018-09-13 16:41:20',NULL),('314f676d-e904-404f-918e-e6cbf4baf9ce','2018-09-17 16:33:45','2018-09-17 16:33:45',NULL),('3360ed21-dde8-4e72-913f-5cef09d67d8d','2018-09-13 16:40:35','2018-09-13 16:40:35',NULL),('3bb3e23f-2bd0-4767-b02d-ea32812035cf','2018-09-12 16:38:03','2018-09-12 16:38:03',NULL),('3cec547c-ec50-480c-b322-042de097b12d','2018-09-07 14:53:56','2018-09-07 14:53:56',NULL),('3f1ba7b0-50be-46d7-a45b-ffb50d796cbd','2018-09-17 16:34:57','2018-09-17 16:34:57',NULL),('4626bd46-3bd5-4889-b7b4-8608c2067c5b','2018-09-17 16:35:14','2018-09-17 16:35:14',NULL),('4c160d54-8ea6-4ff0-8661-9351bfaf160b','2018-09-07 14:59:56','2018-09-07 14:59:56',NULL),('4df2cff0-9cae-471e-a8f7-52b7d8124f0c','2018-09-12 16:35:44','2018-09-12 16:35:44',NULL),('53b61903-f13e-415c-a574-c892b8eea9e2','2018-09-07 14:54:48','2018-09-07 14:54:48',NULL),('545d5f8a-3d66-42a6-aefe-1f5b19b50477','2018-09-13 16:41:01','2018-09-13 16:41:01',NULL),('5673c9c3-1d72-44fa-a04b-38800ff6b2d4','2018-09-12 13:13:48','2018-09-12 13:13:48',NULL),('56f6924e-57c5-4261-a3cf-f026ef011603','2018-09-07 14:52:56','2018-09-07 14:52:56',NULL),('617bb75c-a46f-49e0-a4ac-560cba1dc5be','2018-09-12 16:38:02','2018-09-12 16:38:02',NULL),('6847a5af-3557-48a3-8187-9be01ad17d6e','2018-09-07 14:53:32','2018-09-07 14:53:32',NULL),('6c058693-fddb-453f-8529-f0b55333a457','2018-09-07 14:53:05','2018-09-07 14:53:05',NULL),('6ee07d0f-d7f8-4947-9546-49cb6f6a32ca','2018-09-07 14:59:48','2018-09-07 14:59:48',NULL),('6f193843-926e-4898-9a26-7ac2f9046ea5','2018-09-13 16:42:16','2018-09-13 16:42:16',NULL),('717cf2e1-fc84-4c5a-8b56-ef47acfae561','2018-09-07 14:56:45','2018-09-07 14:56:45',NULL),('7bd311c9-c945-4911-be7e-ed81b61fddf7','2018-09-12 16:40:21','2018-09-12 16:40:21',NULL),('7d065622-3109-4758-9d1c-ef061a5a3b72','2018-09-13 16:41:41','2018-09-13 16:41:41',NULL),('8b68bc2f-5740-4a1d-834e-08bd75b7d0f2','2018-09-07 14:59:49','2018-09-07 14:59:49',NULL),('90862bbf-f083-4835-bbc4-444c8af232cd','2018-09-07 14:58:54','2018-09-07 14:58:54',NULL),('979ea7f2-c3e9-4115-aae6-accc43d5f155','2018-09-07 14:55:56','2018-09-07 14:55:56',NULL),('a1329da2-981f-43e7-a6b7-9382546d0e7e','2018-09-07 14:56:08','2018-09-07 14:56:08',NULL),('b4168caf-cd7f-45a8-aace-43c61eb97ef5','2018-09-12 13:11:55','2018-09-12 13:11:55',NULL),('bb6b251e-2a7e-48a3-9a68-c9fa7f4568d2','2018-09-13 16:41:13','2018-09-13 16:41:13',NULL),('beb82ce1-67a5-4ac5-aaa8-ecbf0043faf4','2018-09-07 14:56:41','2018-09-07 14:56:41',NULL),('c54f47c9-0c5e-4a1d-bbfd-cd210b44086f','2018-09-07 14:56:44','2018-09-07 14:56:44',NULL),('c6bda83a-2fac-46bf-b2c0-4c6eb8721ffa','2018-09-17 16:33:28','2018-09-17 16:33:28',NULL),('d05f7efd-953a-465d-99da-ed382859ab53','2018-09-12 13:11:04','2018-09-12 13:11:04',NULL),('d19f44ad-3241-4d31-88ba-51a9b7fc4f59','2018-09-07 14:54:31','2018-09-07 14:54:31',NULL),('ddee1291-042a-484c-b66f-7592cebe0038','2018-09-07 14:54:24','2018-09-07 14:54:24',NULL),('df7527cd-c903-4068-826c-98ee3e9a57a0','2018-09-12 16:35:46','2018-09-12 16:35:46',NULL),('e2e04746-5ded-4331-9029-a1bae0aff2f1','2018-09-07 15:00:19','2018-09-07 15:00:19',NULL),('ea4fedd7-7d20-4c6c-9a34-697f409ee306','2018-09-07 14:59:54','2018-09-07 14:59:54',NULL),('eb950f9c-dc28-4f70-9785-da6c141b3952','2018-09-17 16:34:06','2018-09-17 16:34:06',NULL),('f03bb2bc-2284-42c8-81a4-d52d64d9aa2e','2018-09-10 17:39:36','2018-09-10 17:39:36',NULL),('f0dc1589-badd-45d8-b16f-d75e4df9d16a','2018-09-13 16:40:46','2018-09-13 16:40:46',NULL),('f3d6aa21-c9fb-4871-9ab5-c9d5792234dc','2018-09-17 16:35:48','2018-09-17 16:35:48',NULL),('f834fd24-848e-4d81-a7f2-6808989687c5','2018-09-17 16:34:24','2018-09-17 16:34:24',NULL),('f99d59f6-e633-4905-8402-0ef14a7d4f5d','2018-09-13 16:41:33','2018-09-13 16:41:33',NULL);
/*!40000 ALTER TABLE `translation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `translation_text`
--

DROP TABLE IF EXISTS `translation_text`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `translation_text` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `translation_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `locale_id` varchar(41) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `translated_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__translation_text__translation_idx` (`translation_id`),
  KEY `fk__translation_text__locale_idx` (`locale_id`),
  CONSTRAINT `fk__translation_text__locale` FOREIGN KEY (`locale_id`) REFERENCES `locale` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__translation_text__translation` FOREIGN KEY (`translation_id`) REFERENCES `translation` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `translation_text`
--

LOCK TABLES `translation_text` WRITE;
/*!40000 ALTER TABLE `translation_text` DISABLE KEYS */;
INSERT INTO `translation_text` VALUES ('016483fe-74bc-4f8a-aa27-0dc10113f226','b4168caf-cd7f-45a8-aace-43c61eb97ef5','48984fbe-84d4-11e5-ba05-0800279114ca','This should be skipped','2018-09-12 13:11:55','2018-09-12 13:11:55',NULL),('04433f3d-34fa-4161-bc4a-d002917ff50c','90862bbf-f083-4835-bbc4-444c8af232cd','4a1d88ab-84d4-11e5-ba05-0800279114ca','caballo','2018-09-07 14:59:16','2018-09-07 14:59:18',NULL),('0844390d-59bd-4fe4-8e7d-d93f5e7a3644','6f193843-926e-4898-9a26-7ac2f9046ea5','48984fbe-84d4-11e5-ba05-0800279114ca','q5','2018-09-13 16:42:16','2018-09-13 16:42:16',NULL),('09e36928-3326-4e2f-bf28-86826fccb0a7','13cf2681-c623-4e49-ac4d-22bc866a3f1c','48984fbe-84d4-11e5-ba05-0800279114ca','cat','2018-09-07 14:57:45','2018-09-07 14:57:45',NULL),('0b38614c-7d7e-4272-be5b-35e5171c5dd9','f3d6aa21-c9fb-4871-9ab5-c9d5792234dc','48984fbe-84d4-11e5-ba05-0800279114ca','Last question roster prefill test','2018-09-17 16:35:48','2018-09-17 16:35:48',NULL),('0c1c84ab-7e10-4229-94e1-27e030bba26b','314f676d-e904-404f-918e-e6cbf4baf9ce','48984fbe-84d4-11e5-ba05-0800279114ca','one','2018-09-17 16:33:45','2018-09-17 16:33:45',NULL),('0e948d71-a135-4483-835a-ebaf816c51b5','18169029-6827-45f9-9da3-45879b05cd47','48984fbe-84d4-11e5-ba05-0800279114ca','ok','2018-09-12 13:14:04','2018-09-12 13:14:04',NULL),('10f9ecde-c538-4751-9259-f02d5ea8c13c','f0dc1589-badd-45d8-b16f-d75e4df9d16a','48984fbe-84d4-11e5-ba05-0800279114ca','one','2018-09-13 16:40:46','2018-09-13 16:40:46',NULL),('13e7af1c-bcf5-4d53-9e57-97f9139624f1','03bf7b25-0117-4de5-80ad-4c14bebfd9ac','48984fbe-84d4-11e5-ba05-0800279114ca','Test State','2018-09-10 17:39:06','2018-09-10 17:39:06',NULL),('1565b7fd-bbd2-4129-8e71-91a6346a3427','f03bb2bc-2284-42c8-81a4-d52d64d9aa2e','48984fbe-84d4-11e5-ba05-0800279114ca','Test Village 1','2018-09-10 17:39:36','2018-09-10 17:39:36',NULL),('1d9cb436-7378-4698-8446-18e00fca3326','003ab718-6d41-45b6-bc7f-4d6de1bb12bc','48984fbe-84d4-11e5-ba05-0800279114ca','A skipped question','2018-09-12 16:37:59','2018-09-12 16:37:59',NULL),('29255711-1f66-4729-b51b-3b5897165f37','4626bd46-3bd5-4889-b7b4-8608c2067c5b','48984fbe-84d4-11e5-ba05-0800279114ca','yes','2018-09-17 16:35:14','2018-09-17 16:35:14',NULL),('2b7d2196-924e-416e-99bd-9102939867b2','e2e04746-5ded-4331-9029-a1bae0aff2f1','4a1d88ab-84d4-11e5-ba05-0800279114ca','otro','2018-09-07 15:02:03','2018-09-07 15:02:06',NULL),('30891da4-6e5a-467e-b6e2-f99f7a4e2944','56f6924e-57c5-4261-a3cf-f026ef011603','48984fbe-84d4-11e5-ba05-0800279114ca','Repeated sections','2018-09-07 14:52:56','2018-09-12 13:11:02',NULL),('373c244b-63ec-4e52-82f0-a8639fed2ab4','53b61903-f13e-415c-a574-c892b8eea9e2','48984fbe-84d4-11e5-ba05-0800279114ca','Three','2018-09-07 14:55:15','2018-09-07 14:55:19',NULL),('398f37da-226f-4d6d-8e46-4cfffe089d23','1ee50b21-fabd-49e5-b6ca-d14aed147285','4a1d88ab-84d4-11e5-ba05-0800279114ca','otro','2018-09-07 14:58:49','2018-09-07 14:58:49',NULL),('40e22eda-4f62-48c4-aaab-62878d8e0d93','6ee07d0f-d7f8-4947-9546-49cb6f6a32ca','48984fbe-84d4-11e5-ba05-0800279114ca','What color of fur does [pets] have?','2018-09-07 14:59:48','2018-09-07 15:00:16',NULL),('4252bade-34eb-4a44-a450-1257cd7487a6','c6bda83a-2fac-46bf-b2c0-4c6eb8721ffa','48984fbe-84d4-11e5-ba05-0800279114ca','Roster prefill test','2018-09-17 16:33:28','2018-09-17 16:33:37',NULL),('43c52ab2-295e-4147-9760-e86eee9f01c4','3f1ba7b0-50be-46d7-a45b-ffb50d796cbd','48984fbe-84d4-11e5-ba05-0800279114ca','prefill_middle_question','2018-09-17 16:34:57','2018-09-17 16:34:57',NULL),('46de8e00-4a87-4f8b-83cd-7f0453c35c10','8b68bc2f-5740-4a1d-834e-08bd75b7d0f2','48984fbe-84d4-11e5-ba05-0800279114ca','What kind of dog is [pets]?','2018-09-07 14:59:49','2018-09-07 14:59:49',NULL),('4cf079ff-eaf9-40a5-b2a9-da5080a184e0','f834fd24-848e-4d81-a7f2-6808989687c5','48984fbe-84d4-11e5-ba05-0800279114ca','random','2018-09-17 16:34:24','2018-09-17 16:34:24',NULL),('4fa8b53e-0389-4162-80a4-024fa543c9ef','617bb75c-a46f-49e0-a4ac-560cba1dc5be','48984fbe-84d4-11e5-ba05-0800279114ca','one','2018-09-12 16:39:57','2018-09-12 16:39:57',NULL),('563ea99e-5545-4a9b-ae1f-5f5dc54af982','09398af3-3b20-4427-8833-1391637bd241','48984fbe-84d4-11e5-ba05-0800279114ca','One','2018-09-12 13:11:32','2018-09-12 13:11:32',NULL),('589e479c-9ee4-4936-bf62-c46048d7356e','d05f7efd-953a-465d-99da-ed382859ab53','48984fbe-84d4-11e5-ba05-0800279114ca','First question skipped','2018-09-12 13:11:04','2018-09-12 13:11:23',NULL),('5a908274-3e9c-4f5d-86dd-ba4a86f666f0','4c160d54-8ea6-4ff0-8661-9351bfaf160b','48984fbe-84d4-11e5-ba05-0800279114ca','brown','2018-09-07 15:01:56','2018-09-07 15:01:56',NULL),('63dbcd7c-397e-4cd3-8032-e722f06500bd','e2e04746-5ded-4331-9029-a1bae0aff2f1','48984fbe-84d4-11e5-ba05-0800279114ca','other','2018-09-07 15:01:58','2018-09-07 15:01:58',NULL),('69611117-307c-4c29-8d22-fc84bd8d92e0','3bb3e23f-2bd0-4767-b02d-ea32812035cf','48984fbe-84d4-11e5-ba05-0800279114ca','two','2018-09-12 16:40:02','2018-09-12 16:40:02',NULL),('6a0baf12-556a-450a-9579-0a88f28b3298','ddee1291-042a-484c-b66f-7592cebe0038','48984fbe-84d4-11e5-ba05-0800279114ca','Multiple select','2018-09-07 14:54:24','2018-09-07 14:54:24',NULL),('6d3e500a-5ca6-4d52-b3c7-efd915dddf33','d19f44ad-3241-4d31-88ba-51a9b7fc4f59','48984fbe-84d4-11e5-ba05-0800279114ca','Two','2018-09-07 14:54:44','2018-09-07 14:55:08',NULL),('73437af9-5c7d-4545-af3c-125f81b8371d','27f7c054-1e24-4dd4-8991-b6cdab557417','48984fbe-84d4-11e5-ba05-0800279114ca','two','2018-09-12 16:36:11','2018-09-12 16:36:11',NULL),('765558b5-2a09-4e14-b4bb-33a6198a14b2','14279205-cf14-497c-9ce7-cb50eb32ef1d','48984fbe-84d4-11e5-ba05-0800279114ca','yes','2018-09-17 16:35:27','2018-09-17 16:35:27',NULL),('7ee7b9ab-031f-4f95-ae2c-e7b1df654c2e','286aa8c2-6d0b-4b35-a905-19cedc8640f9','48984fbe-84d4-11e5-ba05-0800279114ca','dog','2018-09-07 14:57:43','2018-09-07 14:57:43',NULL),('8a556e7b-e89b-47b2-9786-ab653d3c7d4c','bb6b251e-2a7e-48a3-9a68-c9fa7f4568d2','48984fbe-84d4-11e5-ba05-0800279114ca','q2','2018-09-13 16:41:13','2018-09-13 16:41:13',NULL),('8b32de48-ee09-487f-8e19-b2270dd8a817','13cf2681-c623-4e49-ac4d-22bc866a3f1c','4a1d88ab-84d4-11e5-ba05-0800279114ca','gato','2018-09-07 14:58:20','2018-09-07 14:58:21',NULL),('90dd82d5-8e99-4997-a8a9-6461093e8cc0','0310ad5f-e129-49fa-a8ef-7c96dab847ce','48984fbe-84d4-11e5-ba05-0800279114ca','One','2018-09-07 14:54:40','2018-09-07 14:54:40',NULL),('90e24cbf-f58d-4f17-ac0f-84bd766547f8','14e5f69f-f615-477a-a4d8-62519a4e53f0','48984fbe-84d4-11e5-ba05-0800279114ca','One','2018-09-12 16:35:14','2018-09-12 16:35:14',NULL),('93324a19-4749-4621-8e90-c8a94b3e83ee','6847a5af-3557-48a3-8187-9be01ad17d6e','48984fbe-84d4-11e5-ba05-0800279114ca','One','2018-09-07 14:53:32','2018-09-07 14:53:32',NULL),('977aa89c-cb2e-46a6-846f-e12c746563e3','90862bbf-f083-4835-bbc4-444c8af232cd','48984fbe-84d4-11e5-ba05-0800279114ca','horse','2018-09-07 14:59:05','2018-09-07 14:59:05',NULL),('9ef0b038-b4ea-425b-9161-d7c328fd6c21','f99d59f6-e633-4905-8402-0ef14a7d4f5d','48984fbe-84d4-11e5-ba05-0800279114ca','q3','2018-09-13 16:41:33','2018-09-13 16:41:33',NULL),('9fc49f5f-0d2d-4847-89ce-f50f55ea9384','4df2cff0-9cae-471e-a8f7-52b7d8124f0c','48984fbe-84d4-11e5-ba05-0800279114ca','Assign conditoin','2018-09-12 16:35:44','2018-09-12 16:35:44',NULL),('a3543611-30c6-4292-8520-f9281230ab0f','d19f44ad-3241-4d31-88ba-51a9b7fc4f59','48984fbe-84d4-11e5-ba05-0800279114ca','Two','2018-09-07 14:54:45','2018-09-07 14:54:45',NULL),('a63ffa24-9491-474a-888e-6742c07a937d','28ccfbb3-8864-4cb4-baf4-93fafe96f64f','48984fbe-84d4-11e5-ba05-0800279114ca','Condition Assignment','2018-09-12 16:34:58','2018-09-12 16:35:07',NULL),('a9c26a70-b47d-40e3-8503-fa26b808e52b','d19f44ad-3241-4d31-88ba-51a9b7fc4f59','4a1d88ab-84d4-11e5-ba05-0800279114ca','Dos','2018-09-07 14:54:58','2018-09-07 14:54:58',NULL),('a9eaaada-261e-46d8-b304-0bff592787a3','5673c9c3-1d72-44fa-a04b-38800ff6b2d4','48984fbe-84d4-11e5-ba05-0800279114ca','empty','2018-09-12 13:13:48','2018-09-12 13:13:48',NULL),('b016ad17-387f-42dc-8b6b-b160635f9079','979ea7f2-c3e9-4115-aae6-accc43d5f155','48984fbe-84d4-11e5-ba05-0800279114ca','List your pets names','2018-09-07 14:55:56','2018-09-07 14:55:56',NULL),('b0e5714b-2f89-4b4c-a29e-ac30dc6d0924','eb950f9c-dc28-4f70-9785-da6c141b3952','48984fbe-84d4-11e5-ba05-0800279114ca','Initial roster prefill test','2018-09-17 16:34:06','2018-09-17 16:34:06',NULL),('b36e03c3-e8c4-4b07-80f2-4c5c24abd5f5','545d5f8a-3d66-42a6-aefe-1f5b19b50477','48984fbe-84d4-11e5-ba05-0800279114ca','q1','2018-09-13 16:41:01','2018-09-13 16:41:01',NULL),('bb7df38c-75a5-4785-bb59-b058eb04a7e4','3cec547c-ec50-480c-b322-042de097b12d','48984fbe-84d4-11e5-ba05-0800279114ca','An intro questoin','2018-09-07 14:53:56','2018-09-07 14:53:56',NULL),('ca08245c-b395-4073-a676-c2cf06654685','3360ed21-dde8-4e72-913f-5cef09d67d8d','48984fbe-84d4-11e5-ba05-0800279114ca','Basic navigation','2018-09-13 16:40:35','2018-09-13 16:40:39',NULL),('dc6a1102-2918-49fa-9911-75afe9b3ed1f','286aa8c2-6d0b-4b35-a905-19cedc8640f9','4a1d88ab-84d4-11e5-ba05-0800279114ca','perro','2018-09-07 14:57:52','2018-09-07 14:57:52',NULL),('e3d13e28-6df9-4dbf-8936-357f7cc2797c','6c058693-fddb-453f-8529-f0b55333a457','48984fbe-84d4-11e5-ba05-0800279114ca','Add respondent form','2018-09-07 14:53:05','2018-09-07 14:53:17',NULL),('e782e426-412c-4c06-a88d-0c17b0f78beb','7bd311c9-c945-4911-be7e-ed81b61fddf7','48984fbe-84d4-11e5-ba05-0800279114ca','last','2018-09-12 16:40:21','2018-09-12 16:40:21',NULL),('ee16ceff-b6c2-4b9a-80c2-b02d08ef963b','53b61903-f13e-415c-a574-c892b8eea9e2','4a1d88ab-84d4-11e5-ba05-0800279114ca','Tres','2018-09-07 14:55:23','2018-09-07 14:55:23',NULL),('ee6deeca-f9a1-436d-9239-af3c44395072','df7527cd-c903-4068-826c-98ee3e9a57a0','48984fbe-84d4-11e5-ba05-0800279114ca','one','2018-09-12 16:36:07','2018-09-12 16:36:07',NULL),('f1657570-b0dd-4a61-89f6-5eb168951a8f','28e8482f-37fe-4819-ac56-7be1dfe285bd','48984fbe-84d4-11e5-ba05-0800279114ca','two','2018-09-13 16:41:20','2018-09-13 16:41:20',NULL),('f2c7ec5a-6fbb-48ca-a5fc-e2439b38fb7a','7d065622-3109-4758-9d1c-ef061a5a3b72','48984fbe-84d4-11e5-ba05-0800279114ca','q4','2018-09-13 16:41:41','2018-09-13 16:41:41',NULL),('f64799ec-0110-49dd-8cd6-e29aef9d8783','beb82ce1-67a5-4ac5-aaa8-ecbf0043faf4','48984fbe-84d4-11e5-ba05-0800279114ca','What kind of pet is [pets]?','2018-09-07 14:56:41','2018-09-07 14:56:41',NULL),('f6cff3d1-c70e-45ad-888a-4273b8147f9e','1ee50b21-fabd-49e5-b6ca-d14aed147285','48984fbe-84d4-11e5-ba05-0800279114ca','other','2018-09-07 14:58:34','2018-09-07 14:58:34',NULL),('fac6fff4-d419-4abf-8163-99faa71677b2','a1329da2-981f-43e7-a6b7-9382546d0e7e','48984fbe-84d4-11e5-ba05-0800279114ca','Pet info','2018-09-07 14:56:08','2018-09-07 14:56:08',NULL),('fbaf8280-039e-4220-85d9-e6ca42a215b2','ea4fedd7-7d20-4c6c-9a34-697f409ee306','48984fbe-84d4-11e5-ba05-0800279114ca','yellow','2018-09-07 15:01:53','2018-09-07 15:01:53',NULL),('fc89594e-e418-4889-8c09-47a7df30f622','0310ad5f-e129-49fa-a8ef-7c96dab847ce','4a1d88ab-84d4-11e5-ba05-0800279114ca','Uno','2018-09-07 14:54:55','2018-09-07 14:54:55',NULL);
/*!40000 ALTER TABLE `translation_text` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `upload`
--

DROP TABLE IF EXISTS `upload`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `upload` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `upload`
--

LOCK TABLES `upload` WRITE;
/*!40000 ALTER TABLE `upload` DISABLE KEYS */;
INSERT INTO `upload` VALUES ('2bf89723-0605-405f-816d-1e0a830142f5','82f102e4f7226ba6','79852c5b-7a9a-43bd-a262-d70c71620937.json.zip','b693cf129593618987fc4d41ade1d128','PENDING','2018-09-18 12:08:29','2018-09-18 12:08:29',NULL),('8d229187-8b4e-4961-be36-c447cf599607','82f102e4f7226ba6','783d6d31-e9bc-4f17-8aff-fa5af5183a86.json.zip','69fbd88ad16635e0468fd73550fc014d','PENDING','2018-09-13 16:43:20','2018-09-13 16:43:20',NULL),('d3549635-63d1-448b-8409-c727b66fe709','82f102e4f7226ba6','eccad012-2062-4fc4-be5b-348771c752d2.json.zip','701dbdaf1a18e65fc5822dff0890a6ab','DONE','2018-09-13 11:42:16','2018-09-13 11:42:16',NULL),('eee14e60-dbc4-42da-8c44-f4aeebc5ff2c','82f102e4f7226ba6','f4eb8bc7-327c-4917-ade8-309f92994cfb.json.zip','2cb74fd80fd079adf143526f8e97e10d','PENDING','2018-09-14 10:10:53','2018-09-14 10:10:53',NULL),('f7503c15-069a-4429-84e6-464b34c9c5e0','82f102e4f7226ba6','405dec31-fd79-44cc-88c9-66b614744833.json.zip','6327453e45b46481f7d38ad7c09e57d7','PENDING','2018-09-14 12:47:19','2018-09-14 12:47:19',NULL);
/*!40000 ALTER TABLE `upload` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(63) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(63) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ADMIN / SURVEYOR',
  `selected_study_id` varchar(41) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_username_unique` (`username`),
  UNIQUE KEY `idx__username__deleted_at` (`username`,`deleted_at`),
  KEY `fk__user_selected_study__study_idx` (`selected_study_id`),
  CONSTRAINT `fk__user_selected_study__study` FOREIGN KEY (`selected_study_id`) REFERENCES `study` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES ('c1f277ab-e181-11e5-84c9-a45e60f0e921','Default Admin','admin','$2y$10$hutpzXA9dYJeBFx7BUUJkufMaS7.95YAmyMN5ixFz1T4iOqi3iT3y','ADMIN',NULL,'2018-09-07 14:52:03','2018-09-07 14:52:03',NULL);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_study`
--

DROP TABLE IF EXISTS `user_study`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_study` (
  `id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `study_id` varchar(41) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk__user_study__user_idx` (`user_id`),
  KEY `fk__user_study__study_idx` (`study_id`),
  CONSTRAINT `fk__user_study__study` FOREIGN KEY (`study_id`) REFERENCES `study` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk__user_study__user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_study`
--

LOCK TABLES `user_study` WRITE;
/*!40000 ALTER TABLE `user_study` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_study` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-09-18 12:19:53
