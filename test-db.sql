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
  PRIMARY KEY (`id`),
  KEY `action_question_id_foreign` (`question_id`),
  KEY `action_survey_id_foreign` (`survey_id`),
  KEY `action_interview_id_foreign` (`interview_id`),
  CONSTRAINT `action_interview_id_foreign` FOREIGN KEY (`interview_id`) REFERENCES `interview` (`id`),
  CONSTRAINT `action_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`),
  CONSTRAINT `action_survey_id_foreign` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action`
--

LOCK TABLES `action` WRITE;
/*!40000 ALTER TABLE `action` DISABLE KEYS */;
INSERT INTO `action` VALUES ('058e40df-61ea-4e80-921b-92c9511663b4',NULL,NULL,'2018-09-07 15:43:52',NULL,NULL,'next','b9021db1-2dc2-4b85-9866-d89d71c446f4',0,0),('3a0cf48f-c268-4517-8e50-06ea0c78cd69',NULL,'72c56240-aeb8-4068-9a8c-5ffa4ad99e18','2018-09-07 15:44:28',NULL,'{\"roster_id\":\"2978724b-c2a4-4598-93a5-520e42a8cd66\"}','add-roster-row','b9021db1-2dc2-4b85-9866-d89d71c446f4',0,0),('4077f001-a935-4dc5-a21a-4e0df3dd280f',NULL,'e103a32a-47bb-4fd1-b1c1-98dd8e522df0','2018-09-07 15:43:53',NULL,'{\"choice_id\":\"9efca378-a58c-4934-ad75-951e2310c797\",\"val\":\"one\"}','select-choice','b9021db1-2dc2-4b85-9866-d89d71c446f4',0,0),('6cf9f1b4-6d4b-4221-aafd-72e94f5a1595',NULL,NULL,'2018-09-07 15:44:12',NULL,NULL,'next','b9021db1-2dc2-4b85-9866-d89d71c446f4',0,0),('afcc625a-dd14-4bd4-b0d3-6f74b5227aeb',NULL,'e103a32a-47bb-4fd1-b1c1-98dd8e522df0','2018-09-07 15:43:54',NULL,'{\"choice_id\":\"036eacf6-4618-43ef-99f8-526043ac7c2d\",\"val\":\"two\"}','select-choice','b9021db1-2dc2-4b85-9866-d89d71c446f4',0,0);
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
INSERT INTO `assign_condition_tag` VALUES ('9b51a9cc-6cbc-43e2-a05c-510495924423','ad24d06a-b932-480e-9b8a-39d00cf88ae2','function (d) {\n   return d.pet_type == \'dog\'\n}','section','2018-09-07 15:00:56','2018-09-07 15:01:20',NULL),('b08174b3-9c59-4fea-9084-bb228a9b904a','9fbce8a9-6b37-4e4f-b059-ad4db6b5171f','function () { return false;}','respondent','2018-09-12 13:14:39','2018-09-12 13:15:13',NULL);
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
INSERT INTO `choice` VALUES ('02170a9c-139c-4cfc-ac35-57ef9c602396','4c160d54-8ea6-4ff0-8661-9351bfaf160b','brown','2018-09-07 14:59:56','2018-09-07 15:02:15',NULL),('036eacf6-4618-43ef-99f8-526043ac7c2d','d19f44ad-3241-4d31-88ba-51a9b7fc4f59','two','2018-09-07 14:54:31','2018-09-07 14:54:46',NULL),('3647af87-d56d-4a60-96e8-46391cbdd289','90862bbf-f083-4835-bbc4-444c8af232cd','horse','2018-09-07 14:58:54','2018-09-07 14:59:06',NULL),('404d56e3-4bb5-4139-a1c4-6f9d2b56d13a','ea4fedd7-7d20-4c6c-9a34-697f409ee306','yellow','2018-09-07 14:59:54','2018-09-07 15:02:13',NULL),('9efca378-a58c-4934-ad75-951e2310c797','0310ad5f-e129-49fa-a8ef-7c96dab847ce','one','2018-09-07 14:54:27','2018-09-07 14:54:41',NULL),('aac56b68-5f7c-4f4e-93e9-b7f58e7e40c7','53b61903-f13e-415c-a574-c892b8eea9e2','three','2018-09-07 14:54:48','2018-09-07 14:55:26',NULL),('cef52a1f-ed2e-472d-9975-0a12fdb2d005','286aa8c2-6d0b-4b35-a905-19cedc8640f9','dog','2018-09-07 14:56:53','2018-09-07 14:57:43',NULL),('cf1a9059-a8ab-4084-a426-2d520b962a5e','13cf2681-c623-4e49-ac4d-22bc866a3f1c','cat','2018-09-07 14:56:57','2018-09-07 14:57:46',NULL),('d82cc954-38e3-4386-8826-c9e4f443f4c0','1ee50b21-fabd-49e5-b6ca-d14aed147285','other','2018-09-07 14:58:22','2018-09-07 14:58:46',NULL),('f81bed36-4c11-443e-89a6-b847fb44fc78','e2e04746-5ded-4331-9029-a1bae0aff2f1','other','2018-09-07 15:00:20','2018-09-07 15:01:59',NULL);
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
INSERT INTO `condition_tag` VALUES ('1f0629de-e256-49d7-818b-67622c4f9c9a','is_human','2018-09-07 15:34:18','2018-09-07 15:34:18',NULL),('9fbce8a9-6b37-4e4f-b059-ad4db6b5171f','show_first_question','2018-09-12 13:14:39','2018-09-12 13:14:39',NULL),('ad24d06a-b932-480e-9b8a-39d00cf88ae2','is_dog','2018-09-07 15:00:56','2018-09-07 15:00:56',NULL),('c132adce-84ef-4be0-baca-b2b22bb7c28b','is_people','2018-09-07 15:34:57','2018-09-07 15:34:57',NULL);
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
INSERT INTO `edge` VALUES ('15177bdd-b5cf-11e8-8fd7-0800271047e3','6905448f-4356-40f0-b78d-0cc7c05acfdd','98926f3c-a9e2-4de5-8624-6ecf6ab128b6','2018-09-11 10:29:25','2018-09-11 10:29:25',NULL),('151785da-b5cf-11e8-8fd7-0800271047e3','6905448f-4356-40f0-b78d-0cc7c05acfdd','b4be2718-66c9-4d95-9518-d81ac7a29cbc','2018-09-11 10:29:25','2018-09-11 10:29:25',NULL);
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
INSERT INTO `form` VALUES ('bfc6270b-b6c8-4d9e-b26b-c2fc38b65a48','bfc6270b-b6c8-4d9e-b26b-c2fc38b65a48','6c058693-fddb-453f-8529-f0b55333a457',1,1,'2018-09-07 14:53:05','2018-09-07 14:53:21',NULL),('c98e78f8-1bcc-4afc-8e68-530374940213','c98e78f8-1bcc-4afc-8e68-530374940213','56f6924e-57c5-4261-a3cf-f026ef011603',1,1,'2018-09-07 14:52:56','2018-09-07 14:53:00',NULL),('cb801404-806f-4ed1-b5bd-88997ad81f80','cb801404-806f-4ed1-b5bd-88997ad81f80','d05f7efd-953a-465d-99da-ed382859ab53',1,1,'2018-09-12 13:11:04','2018-09-12 13:11:24',NULL);
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
INSERT INTO `form_section` VALUES ('05e2c90a-6b69-4c46-a0d6-66630dd54dfc','cb801404-806f-4ed1-b5bd-88997ad81f80','b01a541d-6518-4212-8d7f-c2cd7d663837',1,0,0,NULL,NULL,'2018-09-12 13:11:32','2018-09-12 13:11:32',NULL),('2f81a834-86a7-4efa-afef-cb95fbbacb48','c98e78f8-1bcc-4afc-8e68-530374940213','2494f66f-8c02-49f2-832b-e6c49156ee33',2,0,0,'717cf2e1-fc84-4c5a-8b56-ef47acfae561','72c56240-aeb8-4068-9a8c-5ffa4ad99e18','2018-09-07 14:56:08','2018-09-07 14:56:49',NULL),('4fca2881-ede5-4413-b5c6-5c272f6c1f99','c98e78f8-1bcc-4afc-8e68-530374940213','8a6b2fa6-55de-4839-b166-4fcb3b37daf9',1,0,0,NULL,NULL,'2018-09-07 14:53:32','2018-09-07 14:53:32',NULL),('8419c34d-5b19-4215-ba8b-6cfe1a81ac94','cb801404-806f-4ed1-b5bd-88997ad81f80','d506ad04-6db4-4a7b-abdd-7cc56c5a9b2e',2,0,0,NULL,NULL,'2018-09-12 13:13:48','2018-09-12 13:13:48',NULL);
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
INSERT INTO `key` VALUES ('1','X-Key','***REMOVED***','2018-09-07 14:52:03','2018-09-07 14:52:03',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2017_07_26_164254_create_tables',1),(2,'2017_07_26_174916_make_username_unique_in_user_table',1),(3,'2017_07_26_234640_add_read_only_to_datum_roster_table',1),(4,'2017_07_27_001945_change_condition_id_to_condition_tag_id_in_respondent_condition_tag_table',1),(5,'2017_08_19_212958_drop_datum_roster_table',1),(6,'2017_08_19_214425_add_follow_up_question_id_to_form_section_table',1),(7,'2017_08_19_215440_add_follow_up_datum_id_to_section_condition_tag_table',1),(8,'2017_10_04_023148_add_override_val_to_datum_choice_table',1),(9,'2017_10_10_004649_create_section_skip_table',1),(10,'2017_10_12_212518_add_opt_out_and_opt_out_val_to_datum_table',1),(11,'2017_11_08_145236_create_jobs_table',1),(12,'2017_11_09_151259_create_report_table',1),(13,'2017_11_09_161033_create_failed_jobs_table',1),(14,'2017_11_14_203453_create_report_file_table',1),(15,'2017_11_17_004021_add_can_enumerator_add_child_to_geo_type_table',1),(16,'2017_12_12_003130_create_study_parameter_table',1),(17,'2017_12_12_012623_create_preload_table',1),(18,'2017_12_12_014337_add_preload_id_make_survey_id_nullable_in_datum_table',1),(19,'2017_12_12_204619_create_respondent_fill_table',1),(20,'2018_03_12_185623_create_action_table',1),(21,'2018_03_23_143412_create_sync_table',1),(22,'2018_03_23_144708_create_snapshot_table',1),(23,'2018_04_12_140301_adding_roster_table',1),(24,'2018_04_16_144248_add_assigned_id_to_respondent',1),(25,'2018_04_18_171754_create_self_administered_survey_table',1),(26,'2018_06_07_182317_add_respondent_name_table',1),(27,'2018_06_08_202721_change_study_form_foreign_key',1),(28,'2018_06_21_152801_fix-action-datum-question-datum',1),(29,'2018_07_02_195903_add_respondent_geo_table',1),(30,'2018_07_03_145029_add_form_type_table',1),(31,'2018_07_03_170824_add_associated_respondent_to_respondent_table',1),(32,'2018_07_05_141349_add_census_type_table',1),(33,'2018_07_06_143228_add_zoom_level_to_geo_type',1),(34,'2018_07_17_210226_rename_can_enumerator_add_fields',1);
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
INSERT INTO `question` VALUES ('49ef53ee-bff2-4d66-9f1b-b816ae6a1e3d','0f76b96f-613a-4925-bacd-74db45368edb','6ee07d0f-d7f8-4947-9546-49cb6f6a32ca','ebf9af64-9c9e-4655-afbe-aa2c2a652ca9',1,'dog_type','2018-09-07 14:59:48','2018-09-07 14:59:48',NULL),('72c56240-aeb8-4068-9a8c-5ffa4ad99e18','5ae659b6-8945-4adc-86d5-a44b51531def','979ea7f2-c3e9-4115-aae6-accc43d5f155','ecc26ed8-5699-41ce-a675-b23f7df86a63',1,'pets','2018-09-07 14:55:56','2018-09-07 14:55:56',NULL),('7e02ce88-b095-491d-a40e-af0b099de1d0','cebe05f8-8e17-4c5c-a5fa-abc3a9c6c1f9','3cec547c-ec50-480c-b322-042de097b12d','937bbb86-6f63-4379-8ead-64957a35b8b5',1,'intro','2018-09-07 14:53:56','2018-09-07 14:53:56',NULL),('82a65354-e273-431d-9f44-7964a864fd20','2d3ff07a-5ab1-4da0-aa7f-440cf8cd0980','b4168caf-cd7f-45a8-aace-43c61eb97ef5','8a11e491-612a-4291-b270-24244d3da016',1,'skipped_question','2018-09-12 13:11:55','2018-09-12 13:11:55',NULL),('8ffe4518-5a9b-43a3-b1eb-6aa842e9d815','cebe05f8-8e17-4c5c-a5fa-abc3a9c6c1f9','18169029-6827-45f9-9da3-45879b05cd47','dc768d39-6479-4eaf-a62e-3f19db166941',1,'wow','2018-09-12 13:14:04','2018-09-12 13:14:04',NULL),('e103a32a-47bb-4fd1-b1c1-98dd8e522df0','0f76b96f-613a-4925-bacd-74db45368edb','ddee1291-042a-484c-b66f-7592cebe0038','e2247ebf-09dc-4d4e-a60e-11e80bb74555',1,'ms','2018-09-07 14:54:24','2018-09-07 14:54:24',NULL),('e978196d-95de-4137-b164-4e2e32f78cc6','b58f23fa-52c7-435e-9b31-5fb771e79f41','beb82ce1-67a5-4ac5-aaa8-ecbf0043faf4','27345e55-ed2e-4397-b492-808490ca21e0',1,'pet_type','2018-09-07 14:56:41','2018-09-07 14:56:41',NULL),('f7e46b66-5f41-46b6-9d69-74cefba4f844','0f76b96f-613a-4925-bacd-74db45368edb','8b68bc2f-5740-4a1d-834e-08bd75b7d0f2','ebf9af64-9c9e-4655-afbe-aa2c2a652ca9',2,'dog_type','2018-09-07 14:59:49','2018-09-07 15:02:24','2018-09-07 15:02:24');
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
INSERT INTO `question_assign_condition_tag` VALUES ('0d6199db-1d72-44e8-91f3-ded029949cf5','8ffe4518-5a9b-43a3-b1eb-6aa842e9d815','b08174b3-9c59-4fea-9084-bb228a9b904a','2018-09-12 13:14:39','2018-09-12 13:14:39',NULL),('b2bc5aa9-1dd5-4644-b8cf-1914455fa4da','e978196d-95de-4137-b164-4e2e32f78cc6','9b51a9cc-6cbc-43e2-a05c-510495924423','2018-09-07 15:00:56','2018-09-07 15:00:56',NULL);
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
INSERT INTO `question_choice` VALUES ('0490b034-b0a0-4e8c-89d1-a0836f4f887d','e103a32a-47bb-4fd1-b1c1-98dd8e522df0','036eacf6-4618-43ef-99f8-526043ac7c2d',2,'2018-09-07 14:54:31','2018-09-07 14:54:31',NULL),('1baacb5f-f129-49cd-9764-0d21339b44fb','49ef53ee-bff2-4d66-9f1b-b816ae6a1e3d','02170a9c-139c-4cfc-ac35-57ef9c602396',2,'2018-09-07 14:59:56','2018-09-07 14:59:56',NULL),('3be1c2db-94cd-45d0-932a-ce4fe248c460','e103a32a-47bb-4fd1-b1c1-98dd8e522df0','9efca378-a58c-4934-ad75-951e2310c797',1,'2018-09-07 14:54:27','2018-09-07 14:54:27',NULL),('4b34ec2f-7b8c-44fa-9b0c-8872ce8cbaff','e978196d-95de-4137-b164-4e2e32f78cc6','cf1a9059-a8ab-4084-a426-2d520b962a5e',2,'2018-09-07 14:56:57','2018-09-07 14:56:57',NULL),('779ff077-8dc4-4011-8453-0834c7de9ada','e978196d-95de-4137-b164-4e2e32f78cc6','cef52a1f-ed2e-472d-9975-0a12fdb2d005',1,'2018-09-07 14:56:53','2018-09-07 14:56:53',NULL),('7a62a759-aab5-43fb-b738-657798026870','e103a32a-47bb-4fd1-b1c1-98dd8e522df0','aac56b68-5f7c-4f4e-93e9-b7f58e7e40c7',3,'2018-09-07 14:54:48','2018-09-07 14:54:48',NULL),('9a2c2ceb-7dd0-48cb-8053-f136dc5ee5df','49ef53ee-bff2-4d66-9f1b-b816ae6a1e3d','404d56e3-4bb5-4139-a1c4-6f9d2b56d13a',1,'2018-09-07 14:59:54','2018-09-07 14:59:54',NULL),('d9eaae88-7070-4bc0-9bbe-afb2d71c9497','49ef53ee-bff2-4d66-9f1b-b816ae6a1e3d','f81bed36-4c11-443e-89a6-b847fb44fc78',3,'2018-09-07 15:00:20','2018-09-07 15:00:20',NULL),('eb16cf5c-991e-43e7-955f-b27da223fa59','e978196d-95de-4137-b164-4e2e32f78cc6','3647af87-d56d-4a60-96e8-46391cbdd289',4,'2018-09-07 14:58:54','2018-09-07 14:58:54',NULL),('f93e11d9-41d3-4a4e-ab33-afdc1bc13480','e978196d-95de-4137-b164-4e2e32f78cc6','d82cc954-38e3-4386-8826-c9e4f443f4c0',3,'2018-09-07 14:58:22','2018-09-07 14:58:22',NULL);
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
INSERT INTO `question_group` VALUES ('27345e55-ed2e-4397-b492-808490ca21e0','2018-09-07 14:56:12','2018-09-07 14:56:12',NULL),('8a11e491-612a-4291-b270-24244d3da016','2018-09-12 13:11:34','2018-09-12 13:11:34',NULL),('937bbb86-6f63-4379-8ead-64957a35b8b5','2018-09-07 14:53:35','2018-09-07 14:53:35',NULL),('dc768d39-6479-4eaf-a62e-3f19db166941','2018-09-12 13:13:50','2018-09-12 13:13:50',NULL),('e2247ebf-09dc-4d4e-a60e-11e80bb74555','2018-09-07 14:54:01','2018-09-07 14:54:01',NULL),('ebf9af64-9c9e-4655-afbe-aa2c2a652ca9','2018-09-07 14:59:26','2018-09-07 14:59:26',NULL),('ecc26ed8-5699-41ce-a675-b23f7df86a63','2018-09-07 14:55:33','2018-09-07 14:55:33',NULL);
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
INSERT INTO `question_group_skip` VALUES ('5272b384-0ab0-4fc7-8df0-19bca21d6e01','ebf9af64-9c9e-4655-afbe-aa2c2a652ca9','d3e8d3ff-277b-4f45-9186-fe38338f1d08','2018-09-07 15:01:44','2018-09-07 15:01:44',NULL),('9c3eacac-09a3-49af-bc7f-efd1cdd09a4d','8a11e491-612a-4291-b270-24244d3da016','06e38d4d-20fb-4bdd-877f-91546a8d0835','2018-09-12 13:13:08','2018-09-12 13:13:08',NULL);
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
INSERT INTO `respondent` VALUES ('6905448f-4356-40f0-b78d-0cc7c05acfdd',NULL,NULL,NULL,NULL,'Test respondent 3','2018-09-07 15:02:50','2018-09-07 15:02:50',NULL,NULL),('98926f3c-a9e2-4de5-8624-6ecf6ab128b6',NULL,NULL,NULL,NULL,'Test respondent 2','2018-09-07 15:02:43','2018-09-07 15:02:43',NULL,NULL),('b4be2718-66c9-4d95-9518-d81ac7a29cbc',NULL,NULL,NULL,NULL,'Test respondent 1','2018-09-07 15:02:37','2018-09-07 15:02:37',NULL,NULL);
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
INSERT INTO `respondent_name` VALUES ('0d7945b5-3f89-4cc6-a2b0-d278d7c96aa9',0,'Test respondent 1 nickname','b4be2718-66c9-4d95-9518-d81ac7a29cbc',NULL,NULL,'2018-09-07 15:34:34','2018-09-07 15:34:34',NULL),('148d2f88-f147-46aa-a4ac-76377bcd7cbd',1,'Test respondent 3','6905448f-4356-40f0-b78d-0cc7c05acfdd',NULL,NULL,'2018-09-07 15:02:50','2018-09-07 15:02:50',NULL),('66f69969-ebe0-4925-8690-a2056a0e2be2',1,'Test respondent 1','b4be2718-66c9-4d95-9518-d81ac7a29cbc',NULL,NULL,'2018-09-07 15:02:37','2018-09-07 15:02:37',NULL),('f92c0ed0-8d12-4887-8945-17588045773e',1,'Test respondent 2','98926f3c-a9e2-4de5-8624-6ecf6ab128b6',NULL,NULL,'2018-09-07 15:02:43','2018-09-07 15:02:43',NULL);
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
INSERT INTO `roster` VALUES ('1c257b6b-688b-4ec6-9804-12b0472eff59','not edited','0000-00-00 00:00:00','2018-09-10 17:17:55',NULL),('2978724b-c2a4-4598-93a5-520e42a8cd66','yogi','0000-00-00 00:00:00','0000-00-00 00:00:00',NULL);
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
INSERT INTO `section` VALUES ('2494f66f-8c02-49f2-832b-e6c49156ee33','a1329da2-981f-43e7-a6b7-9382546d0e7e','2018-09-07 14:56:08','2018-09-07 14:56:08',NULL),('8a6b2fa6-55de-4839-b166-4fcb3b37daf9','6847a5af-3557-48a3-8187-9be01ad17d6e','2018-09-07 14:53:32','2018-09-07 14:53:32',NULL),('b01a541d-6518-4212-8d7f-c2cd7d663837','09398af3-3b20-4427-8833-1391637bd241','2018-09-12 13:11:32','2018-09-12 13:11:32',NULL),('d506ad04-6db4-4a7b-abdd-7cc56c5a9b2e','5673c9c3-1d72-44fa-a04b-38800ff6b2d4','2018-09-12 13:13:48','2018-09-12 13:13:48',NULL);
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
INSERT INTO `section_question_group` VALUES ('1bfb799c-a307-447f-8223-49d6a8a050a8','b01a541d-6518-4212-8d7f-c2cd7d663837','8a11e491-612a-4291-b270-24244d3da016',1,'2018-09-12 13:11:34','2018-09-12 13:11:34',NULL),('2c015c37-cb7d-4e9a-9be8-05c0d1464c7b','8a6b2fa6-55de-4839-b166-4fcb3b37daf9','ecc26ed8-5699-41ce-a675-b23f7df86a63',3,'2018-09-07 14:55:33','2018-09-07 14:55:33',NULL),('34bb03d2-23de-486a-8d6b-7917f044a784','2494f66f-8c02-49f2-832b-e6c49156ee33','27345e55-ed2e-4397-b492-808490ca21e0',1,'2018-09-07 14:56:12','2018-09-07 14:56:12',NULL),('6ab39102-2310-463b-8421-c5a0a522c5f7','d506ad04-6db4-4a7b-abdd-7cc56c5a9b2e','dc768d39-6479-4eaf-a62e-3f19db166941',1,'2018-09-12 13:13:50','2018-09-12 13:13:50',NULL),('871b97d7-cf0e-4ccd-a537-1e9d094b0c0e','8a6b2fa6-55de-4839-b166-4fcb3b37daf9','937bbb86-6f63-4379-8ead-64957a35b8b5',1,'2018-09-07 14:53:35','2018-09-07 14:53:35',NULL),('cb36b119-76e4-4689-8439-b2ddfa0fc571','8a6b2fa6-55de-4839-b166-4fcb3b37daf9','e2247ebf-09dc-4d4e-a60e-11e80bb74555',2,'2018-09-07 14:54:01','2018-09-07 14:54:01',NULL),('d9723b8c-6e94-494d-960e-852a2a8821b3','2494f66f-8c02-49f2-832b-e6c49156ee33','ebf9af64-9c9e-4655-afbe-aa2c2a652ca9',2,'2018-09-07 14:59:26','2018-09-07 14:59:26',NULL);
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
INSERT INTO `skip` VALUES ('06e38d4d-20fb-4bdd-877f-91546a8d0835',1,0,0,'2018-09-12 13:13:08','2018-09-12 13:13:08',NULL),('d3e8d3ff-277b-4f45-9186-fe38338f1d08',1,1,0,'2018-09-07 15:01:44','2018-09-07 15:01:44',NULL);
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
INSERT INTO `skip_condition_tag` VALUES ('0d273994-ffd6-43a0-bcdf-f1aaacb4e2d7','d3e8d3ff-277b-4f45-9186-fe38338f1d08','2018-09-07 15:01:44','2018-09-07 15:01:44',NULL,'is_dog'),('81f2207c-910c-46cb-a111-0f1465bb5b35','06e38d4d-20fb-4bdd-877f-91546a8d0835','2018-09-12 13:14:53','2018-09-12 13:14:53',NULL,'show_first_question'),('cc19a32d-1f81-49e1-b22b-fc11e174b654','06e38d4d-20fb-4bdd-877f-91546a8d0835','2018-09-12 13:13:08','2018-09-12 13:14:53','2018-09-12 13:14:53','is_people');
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
INSERT INTO `snapshot` VALUES ('51c560ca-5f64-4e9f-befb-7d455246dfcd','51c560ca-5f64-4e9f-befb-7d455246dfcd.sqlite.sql.zip','5c96c15d20f91daf7f57a1a7e9cdd3b9','2018-09-10 17:45:18','2018-09-10 17:45:18',NULL);
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
INSERT INTO `study_form` VALUES ('a0574906-f486-40b7-b918-4079b38462ea','6a08c96a-fb80-4eae-9b2b-4d03d4b3235d','bfc6270b-b6c8-4d9e-b26b-c2fc38b65a48',2,'2018-09-07 14:53:05','2018-09-07 14:53:20',NULL,1,'06162912-8048-4978-a8d2-92b6dd0c2ed1'),('b0b31281-a48e-4063-b675-df2352f0c8ef','6a08c96a-fb80-4eae-9b2b-4d03d4b3235d','cb801404-806f-4ed1-b5bd-88997ad81f80',3,'2018-09-12 13:11:04','2018-09-12 13:11:04',NULL,0,NULL),('d7a60998-ab69-4693-abad-eb9f0e75add2','6a08c96a-fb80-4eae-9b2b-4d03d4b3235d','c98e78f8-1bcc-4afc-8e68-530374940213',1,'2018-09-07 14:52:56','2018-09-07 14:52:56',NULL,0,NULL);
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
INSERT INTO `study_respondent` VALUES ('08500877-f135-47e8-be85-5fbf355fa6fb','6a08c96a-fb80-4eae-9b2b-4d03d4b3235d','98926f3c-a9e2-4de5-8624-6ecf6ab128b6','2018-09-07 15:02:43','2018-09-07 15:02:43',NULL),('21584992-de3d-4556-83ca-252baa518bee','6a08c96a-fb80-4eae-9b2b-4d03d4b3235d','b4be2718-66c9-4d95-9518-d81ac7a29cbc','2018-09-07 15:02:37','2018-09-07 15:02:37',NULL),('c67d09ab-26f5-4973-ab12-52bcd5c31673','6a08c96a-fb80-4eae-9b2b-4d03d4b3235d','6905448f-4356-40f0-b78d-0cc7c05acfdd','2018-09-07 15:02:50','2018-09-07 15:02:50',NULL);
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
INSERT INTO `token` VALUES ('029e582d-1518-4291-8d8d-2e6dc234394a','c1f277ab-e181-11e5-84c9-a45e60f0e921','df62ac5c8aff657560427998267d1bd3f201e796b2b8dceeb8bb82b8d04058a900a7cd40bd4fba2ac1104198b85ba5ec034f5349b9a978f92fc225c78da1c880',0,'2018-09-11 10:04:19','2018-09-11 10:04:31',NULL),('05f2c53b-5d45-43a2-ab5a-ecfa7acdadd8','c1f277ab-e181-11e5-84c9-a45e60f0e921','3970c55f492791572f18820a353b3ec0d80f50763cbccf86ef9b6bd3c9dd08e36d888ab8cfd7d4b6c2eaaa011df280895cd5405e54cfaba20644e0510e5a72ba',0,'2018-09-11 09:47:52','2018-09-11 09:48:53',NULL),('23d9a693-9645-4cff-9dc9-efa772b1f91a','c1f277ab-e181-11e5-84c9-a45e60f0e921','fbb3c18691de0650dbcd8e12c83c87821c3dfbb869d210f24e25f2686634ac302e7f6f690a2e6ffc29058c03f03a00c2053d3e0b5e7843d3ee204487e790068b',0,'2018-09-11 10:00:20','2018-09-11 10:01:32',NULL),('2884a38a-9a53-405e-b72c-cd762aca5b71','c1f277ab-e181-11e5-84c9-a45e60f0e921','8f78e899afcd41299fde12743ac5c8fa81ec215935da7a9752785a29c83ed82951457073489025df260f9ca583b5f2585ab9a633e5e5faf99bbeed2761ed037c',0,'2018-09-11 10:06:42','2018-09-11 10:06:54',NULL),('2df5c7e3-5caf-44a5-a5a5-e7c1decb9f1e','c1f277ab-e181-11e5-84c9-a45e60f0e921','8b8cbbacf0a94d1808260b7ef731667b3368c583c41f1d19bed284156aa6e771c357c41069afeafc88aa08aded20801e268523b9629d7c53b173b4aad627b35b',0,'2018-09-11 09:56:17','2018-09-11 09:56:30',NULL),('35bf0222-3190-4568-bb57-409a37dd137b','c1f277ab-e181-11e5-84c9-a45e60f0e921','d87ba3b81166db763039d265f1332fb666f88a830582eb0b69774e6720890d2b20cbf8a03475cdffb32cc293b1ddf74541e360865f506ce04d79849509ef5708',0,'2018-09-11 10:09:27','2018-09-11 10:09:31',NULL),('3c17fbda-8a75-458d-a3ba-3366d9e4e7e8','c1f277ab-e181-11e5-84c9-a45e60f0e921','9060d0a716c35daf9bfbca4a5f8ef59f6886f7ed065895bf554940fe9e019968f66b2b504320a88f0bd0339a70419ee0c4102912364991f75cce628d40500c6d',0,'2018-09-11 09:53:13','2018-09-11 09:53:17',NULL),('4abee6f9-3ad0-4cbd-9935-4b9ebd696b01','c1f277ab-e181-11e5-84c9-a45e60f0e921','dc043fce81536e7e9fbfa0b59478566b256dffa02fcd2b0d606ff4d3e19fa1661f0a839e6a38720450b35ca5a39f72046e60b22f86a7342b7a09e55255bf32b2',0,'2018-09-12 13:10:50','2018-09-12 13:15:28',NULL),('5431fed7-46f2-4569-b56d-224b22a62b14','c1f277ab-e181-11e5-84c9-a45e60f0e921','fa41b85e6ee7638d08553fc5c5b78d4611745d8822339f1a378290a871793acab5d4544e571f24573a784cabff14344d52829d89410732ba07081034e6d3b65f',0,'2018-09-11 09:57:57','2018-09-11 09:58:03',NULL),('5ff02744-f053-4bac-b796-e2e8ba86af7e','c1f277ab-e181-11e5-84c9-a45e60f0e921','65d6e62d91a52fa5eaaa344f2a21bea32b5514f5a5845d7cfbad5309636be8f46158d9bd3bc15f28c3054ab14f60e9c9f7e306cf15fdb7482ee37611f069df06',0,'2018-09-10 17:49:01','2018-09-10 17:50:35',NULL),('694d7aa0-6ac6-4a45-8f0d-ff45d56b49f3','c1f277ab-e181-11e5-84c9-a45e60f0e921','c6f99d799980cadc4c4224238e443476c8d28bc4c2bbd3d9dc88d7b3a2b6c0a0f5e0b63a491779cd1b747de47c7d2e9b1a3bbf9ef6621b8029c7d143d303e986',0,'2018-09-11 10:10:24','2018-09-11 10:10:29',NULL),('6ca1546e-7eed-45e3-b22e-fd4031201ee2','c1f277ab-e181-11e5-84c9-a45e60f0e921','2ebb64f01f407da5ff01abeb7f8c3ebb00583d25120452523c474c514dce082c5132097b7ad7238a9c0692494ffc4ca53af5f60221b55b0fc5e8e68619f21905',0,'2018-09-12 13:16:21','2018-09-12 13:16:21',NULL),('6cb5f93e-5b8f-44fd-a0c5-c44159737be7','c1f277ab-e181-11e5-84c9-a45e60f0e921','1689787de4b847977906f6ac1efedb553b796ead356e0c719878c9cba00f50441fc6a81c9c3f5aefca65c2a895aa109480aad737ba6f06a69bb39a92df29a1af',0,'2018-09-10 17:52:33','2018-09-10 17:52:52',NULL),('736d0cf2-02ea-4ffa-aaad-4be817d80f5a','c1f277ab-e181-11e5-84c9-a45e60f0e921','ceedaf34414dbd43ee9b4f9e0a66ce18bf0559cc14d0eec1e467850fddf7e2ed9f6f5b03872b4935e9585e608e3772c2fe9ad051d8103bcca5a4b02ca0b1f283',0,'2018-09-11 10:07:38','2018-09-11 10:07:42',NULL),('75065304-4dd8-46f1-8443-ea2b9bcf92bc','c1f277ab-e181-11e5-84c9-a45e60f0e921','16880269dd5935d50c079190678a05e626b731bfdea900275478e1ad3195a1385d1b0041a58be94e9443d1e31a765489ae157d778728c1b80d0e3ee49c63f289',0,'2018-09-10 17:51:37','2018-09-10 17:51:41',NULL),('81706047-75d5-4f57-b4f7-d14f54d24eb5','c1f277ab-e181-11e5-84c9-a45e60f0e921','2fe77056558f3784182950174574080941e42d7f820adc45dfc5405fddf9e2693fb4cc751a66b7bf862119c6b927bba0d1df860ad1b593aedb8a8605165e7fdb',0,'2018-09-11 10:30:34','2018-09-11 10:30:44',NULL),('a7571d1f-f649-45db-8047-1e5e1f87d284','c1f277ab-e181-11e5-84c9-a45e60f0e921','d0753a9084041debc252b3f86e1d27f1352dac35c8abd00989f6d2b21c17a6f68d727d9a437e50ae3eba5010c0a2f72dec36e3d29a68786eb0cd745469e6478b',0,'2018-09-11 09:58:27','2018-09-11 09:58:30',NULL),('daeacbdd-3c99-45e0-8125-dd3a4c021f42','c1f277ab-e181-11e5-84c9-a45e60f0e921','0e68e063db66eb3b177f98a4dd7fa2a59d7d54dba3756490e280fff0d35d0f43f9f90b35c39b025f6a0fece117b42dd48f3c8d03e7d8b7b9edf2d5b46371f706',0,'2018-09-10 17:51:09','2018-09-10 17:51:13',NULL),('e1adf2eb-6f29-49b8-b580-0044c645bd7a','c1f277ab-e181-11e5-84c9-a45e60f0e921','c962b19cf12a9b1441a316a5dc5d3528475228fd3ddbe8aa239a8cda06821556ff2b8cd69094ace5d644b4285b5a10f293d2e5011eab75e4b40cf7e1dfbab8c1',0,'2018-09-11 10:01:45','2018-09-11 10:04:08',NULL),('e845398a-0fa6-445d-a92e-c6d48c9f234c','c1f277ab-e181-11e5-84c9-a45e60f0e921','fbf44bc5458f579b838dbcb07ae1589e496c73e800f4567926d2837bfac1dfd73073e55fd912b6db6a5036f25258429894ac2ef489ae380a88cbd1ef734f74c9',0,'2018-09-11 09:46:29','2018-09-11 09:47:30',NULL);
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
INSERT INTO `translation` VALUES ('0310ad5f-e129-49fa-a8ef-7c96dab847ce','2018-09-07 14:54:27','2018-09-07 14:54:27',NULL),('03bf7b25-0117-4de5-80ad-4c14bebfd9ac','2018-09-10 17:39:06','2018-09-10 17:39:06',NULL),('09398af3-3b20-4427-8833-1391637bd241','2018-09-12 13:11:32','2018-09-12 13:11:32',NULL),('13cf2681-c623-4e49-ac4d-22bc866a3f1c','2018-09-07 14:56:57','2018-09-07 14:56:57',NULL),('18169029-6827-45f9-9da3-45879b05cd47','2018-09-12 13:14:04','2018-09-12 13:14:04',NULL),('1ee50b21-fabd-49e5-b6ca-d14aed147285','2018-09-07 14:58:22','2018-09-07 14:58:22',NULL),('286aa8c2-6d0b-4b35-a905-19cedc8640f9','2018-09-07 14:56:53','2018-09-07 14:56:53',NULL),('3cec547c-ec50-480c-b322-042de097b12d','2018-09-07 14:53:56','2018-09-07 14:53:56',NULL),('4c160d54-8ea6-4ff0-8661-9351bfaf160b','2018-09-07 14:59:56','2018-09-07 14:59:56',NULL),('53b61903-f13e-415c-a574-c892b8eea9e2','2018-09-07 14:54:48','2018-09-07 14:54:48',NULL),('5673c9c3-1d72-44fa-a04b-38800ff6b2d4','2018-09-12 13:13:48','2018-09-12 13:13:48',NULL),('56f6924e-57c5-4261-a3cf-f026ef011603','2018-09-07 14:52:56','2018-09-07 14:52:56',NULL),('6847a5af-3557-48a3-8187-9be01ad17d6e','2018-09-07 14:53:32','2018-09-07 14:53:32',NULL),('6c058693-fddb-453f-8529-f0b55333a457','2018-09-07 14:53:05','2018-09-07 14:53:05',NULL),('6ee07d0f-d7f8-4947-9546-49cb6f6a32ca','2018-09-07 14:59:48','2018-09-07 14:59:48',NULL),('717cf2e1-fc84-4c5a-8b56-ef47acfae561','2018-09-07 14:56:45','2018-09-07 14:56:45',NULL),('8b68bc2f-5740-4a1d-834e-08bd75b7d0f2','2018-09-07 14:59:49','2018-09-07 14:59:49',NULL),('90862bbf-f083-4835-bbc4-444c8af232cd','2018-09-07 14:58:54','2018-09-07 14:58:54',NULL),('979ea7f2-c3e9-4115-aae6-accc43d5f155','2018-09-07 14:55:56','2018-09-07 14:55:56',NULL),('a1329da2-981f-43e7-a6b7-9382546d0e7e','2018-09-07 14:56:08','2018-09-07 14:56:08',NULL),('b4168caf-cd7f-45a8-aace-43c61eb97ef5','2018-09-12 13:11:55','2018-09-12 13:11:55',NULL),('beb82ce1-67a5-4ac5-aaa8-ecbf0043faf4','2018-09-07 14:56:41','2018-09-07 14:56:41',NULL),('c54f47c9-0c5e-4a1d-bbfd-cd210b44086f','2018-09-07 14:56:44','2018-09-07 14:56:44',NULL),('d05f7efd-953a-465d-99da-ed382859ab53','2018-09-12 13:11:04','2018-09-12 13:11:04',NULL),('d19f44ad-3241-4d31-88ba-51a9b7fc4f59','2018-09-07 14:54:31','2018-09-07 14:54:31',NULL),('ddee1291-042a-484c-b66f-7592cebe0038','2018-09-07 14:54:24','2018-09-07 14:54:24',NULL),('e2e04746-5ded-4331-9029-a1bae0aff2f1','2018-09-07 15:00:19','2018-09-07 15:00:19',NULL),('ea4fedd7-7d20-4c6c-9a34-697f409ee306','2018-09-07 14:59:54','2018-09-07 14:59:54',NULL),('f03bb2bc-2284-42c8-81a4-d52d64d9aa2e','2018-09-10 17:39:36','2018-09-10 17:39:36',NULL);
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
INSERT INTO `translation_text` VALUES ('016483fe-74bc-4f8a-aa27-0dc10113f226','b4168caf-cd7f-45a8-aace-43c61eb97ef5','48984fbe-84d4-11e5-ba05-0800279114ca','This should be skipped','2018-09-12 13:11:55','2018-09-12 13:11:55',NULL),('04433f3d-34fa-4161-bc4a-d002917ff50c','90862bbf-f083-4835-bbc4-444c8af232cd','4a1d88ab-84d4-11e5-ba05-0800279114ca','caballo','2018-09-07 14:59:16','2018-09-07 14:59:18',NULL),('09e36928-3326-4e2f-bf28-86826fccb0a7','13cf2681-c623-4e49-ac4d-22bc866a3f1c','48984fbe-84d4-11e5-ba05-0800279114ca','cat','2018-09-07 14:57:45','2018-09-07 14:57:45',NULL),('0e948d71-a135-4483-835a-ebaf816c51b5','18169029-6827-45f9-9da3-45879b05cd47','48984fbe-84d4-11e5-ba05-0800279114ca','ok','2018-09-12 13:14:04','2018-09-12 13:14:04',NULL),('13e7af1c-bcf5-4d53-9e57-97f9139624f1','03bf7b25-0117-4de5-80ad-4c14bebfd9ac','48984fbe-84d4-11e5-ba05-0800279114ca','Test State','2018-09-10 17:39:06','2018-09-10 17:39:06',NULL),('1565b7fd-bbd2-4129-8e71-91a6346a3427','f03bb2bc-2284-42c8-81a4-d52d64d9aa2e','48984fbe-84d4-11e5-ba05-0800279114ca','Test Village 1','2018-09-10 17:39:36','2018-09-10 17:39:36',NULL),('2b7d2196-924e-416e-99bd-9102939867b2','e2e04746-5ded-4331-9029-a1bae0aff2f1','4a1d88ab-84d4-11e5-ba05-0800279114ca','otro','2018-09-07 15:02:03','2018-09-07 15:02:06',NULL),('30891da4-6e5a-467e-b6e2-f99f7a4e2944','56f6924e-57c5-4261-a3cf-f026ef011603','48984fbe-84d4-11e5-ba05-0800279114ca','Repeated sections','2018-09-07 14:52:56','2018-09-12 13:11:02',NULL),('373c244b-63ec-4e52-82f0-a8639fed2ab4','53b61903-f13e-415c-a574-c892b8eea9e2','48984fbe-84d4-11e5-ba05-0800279114ca','Three','2018-09-07 14:55:15','2018-09-07 14:55:19',NULL),('398f37da-226f-4d6d-8e46-4cfffe089d23','1ee50b21-fabd-49e5-b6ca-d14aed147285','4a1d88ab-84d4-11e5-ba05-0800279114ca','otro','2018-09-07 14:58:49','2018-09-07 14:58:49',NULL),('40e22eda-4f62-48c4-aaab-62878d8e0d93','6ee07d0f-d7f8-4947-9546-49cb6f6a32ca','48984fbe-84d4-11e5-ba05-0800279114ca','What color of fur does [pets] have?','2018-09-07 14:59:48','2018-09-07 15:00:16',NULL),('46de8e00-4a87-4f8b-83cd-7f0453c35c10','8b68bc2f-5740-4a1d-834e-08bd75b7d0f2','48984fbe-84d4-11e5-ba05-0800279114ca','What kind of dog is [pets]?','2018-09-07 14:59:49','2018-09-07 14:59:49',NULL),('563ea99e-5545-4a9b-ae1f-5f5dc54af982','09398af3-3b20-4427-8833-1391637bd241','48984fbe-84d4-11e5-ba05-0800279114ca','One','2018-09-12 13:11:32','2018-09-12 13:11:32',NULL),('589e479c-9ee4-4936-bf62-c46048d7356e','d05f7efd-953a-465d-99da-ed382859ab53','48984fbe-84d4-11e5-ba05-0800279114ca','First question skipped','2018-09-12 13:11:04','2018-09-12 13:11:23',NULL),('5a908274-3e9c-4f5d-86dd-ba4a86f666f0','4c160d54-8ea6-4ff0-8661-9351bfaf160b','48984fbe-84d4-11e5-ba05-0800279114ca','brown','2018-09-07 15:01:56','2018-09-07 15:01:56',NULL),('63dbcd7c-397e-4cd3-8032-e722f06500bd','e2e04746-5ded-4331-9029-a1bae0aff2f1','48984fbe-84d4-11e5-ba05-0800279114ca','other','2018-09-07 15:01:58','2018-09-07 15:01:58',NULL),('6a0baf12-556a-450a-9579-0a88f28b3298','ddee1291-042a-484c-b66f-7592cebe0038','48984fbe-84d4-11e5-ba05-0800279114ca','Multiple select','2018-09-07 14:54:24','2018-09-07 14:54:24',NULL),('6d3e500a-5ca6-4d52-b3c7-efd915dddf33','d19f44ad-3241-4d31-88ba-51a9b7fc4f59','48984fbe-84d4-11e5-ba05-0800279114ca','Two','2018-09-07 14:54:44','2018-09-07 14:55:08',NULL),('7ee7b9ab-031f-4f95-ae2c-e7b1df654c2e','286aa8c2-6d0b-4b35-a905-19cedc8640f9','48984fbe-84d4-11e5-ba05-0800279114ca','dog','2018-09-07 14:57:43','2018-09-07 14:57:43',NULL),('8b32de48-ee09-487f-8e19-b2270dd8a817','13cf2681-c623-4e49-ac4d-22bc866a3f1c','4a1d88ab-84d4-11e5-ba05-0800279114ca','gato','2018-09-07 14:58:20','2018-09-07 14:58:21',NULL),('90dd82d5-8e99-4997-a8a9-6461093e8cc0','0310ad5f-e129-49fa-a8ef-7c96dab847ce','48984fbe-84d4-11e5-ba05-0800279114ca','One','2018-09-07 14:54:40','2018-09-07 14:54:40',NULL),('93324a19-4749-4621-8e90-c8a94b3e83ee','6847a5af-3557-48a3-8187-9be01ad17d6e','48984fbe-84d4-11e5-ba05-0800279114ca','One','2018-09-07 14:53:32','2018-09-07 14:53:32',NULL),('977aa89c-cb2e-46a6-846f-e12c746563e3','90862bbf-f083-4835-bbc4-444c8af232cd','48984fbe-84d4-11e5-ba05-0800279114ca','horse','2018-09-07 14:59:05','2018-09-07 14:59:05',NULL),('a3543611-30c6-4292-8520-f9281230ab0f','d19f44ad-3241-4d31-88ba-51a9b7fc4f59','48984fbe-84d4-11e5-ba05-0800279114ca','Two','2018-09-07 14:54:45','2018-09-07 14:54:45',NULL),('a9c26a70-b47d-40e3-8503-fa26b808e52b','d19f44ad-3241-4d31-88ba-51a9b7fc4f59','4a1d88ab-84d4-11e5-ba05-0800279114ca','Dos','2018-09-07 14:54:58','2018-09-07 14:54:58',NULL),('a9eaaada-261e-46d8-b304-0bff592787a3','5673c9c3-1d72-44fa-a04b-38800ff6b2d4','48984fbe-84d4-11e5-ba05-0800279114ca','empty','2018-09-12 13:13:48','2018-09-12 13:13:48',NULL),('b016ad17-387f-42dc-8b6b-b160635f9079','979ea7f2-c3e9-4115-aae6-accc43d5f155','48984fbe-84d4-11e5-ba05-0800279114ca','List your pets names','2018-09-07 14:55:56','2018-09-07 14:55:56',NULL),('bb7df38c-75a5-4785-bb59-b058eb04a7e4','3cec547c-ec50-480c-b322-042de097b12d','48984fbe-84d4-11e5-ba05-0800279114ca','An intro questoin','2018-09-07 14:53:56','2018-09-07 14:53:56',NULL),('dc6a1102-2918-49fa-9911-75afe9b3ed1f','286aa8c2-6d0b-4b35-a905-19cedc8640f9','4a1d88ab-84d4-11e5-ba05-0800279114ca','perro','2018-09-07 14:57:52','2018-09-07 14:57:52',NULL),('e3d13e28-6df9-4dbf-8936-357f7cc2797c','6c058693-fddb-453f-8529-f0b55333a457','48984fbe-84d4-11e5-ba05-0800279114ca','Add respondent form','2018-09-07 14:53:05','2018-09-07 14:53:17',NULL),('ee16ceff-b6c2-4b9a-80c2-b02d08ef963b','53b61903-f13e-415c-a574-c892b8eea9e2','4a1d88ab-84d4-11e5-ba05-0800279114ca','Tres','2018-09-07 14:55:23','2018-09-07 14:55:23',NULL),('f64799ec-0110-49dd-8cd6-e29aef9d8783','beb82ce1-67a5-4ac5-aaa8-ecbf0043faf4','48984fbe-84d4-11e5-ba05-0800279114ca','What kind of pet is [pets]?','2018-09-07 14:56:41','2018-09-07 14:56:41',NULL),('f6cff3d1-c70e-45ad-888a-4273b8147f9e','1ee50b21-fabd-49e5-b6ca-d14aed147285','48984fbe-84d4-11e5-ba05-0800279114ca','other','2018-09-07 14:58:34','2018-09-07 14:58:34',NULL),('fac6fff4-d419-4abf-8163-99faa71677b2','a1329da2-981f-43e7-a6b7-9382546d0e7e','48984fbe-84d4-11e5-ba05-0800279114ca','Pet info','2018-09-07 14:56:08','2018-09-07 14:56:08',NULL),('fbaf8280-039e-4220-85d9-e6ca42a215b2','ea4fedd7-7d20-4c6c-9a34-697f409ee306','48984fbe-84d4-11e5-ba05-0800279114ca','yellow','2018-09-07 15:01:53','2018-09-07 15:01:53',NULL),('fc89594e-e418-4889-8c09-47a7df30f622','0310ad5f-e129-49fa-a8ef-7c96dab847ce','4a1d88ab-84d4-11e5-ba05-0800279114ca','Uno','2018-09-07 14:54:55','2018-09-07 14:54:55',NULL);
/*!40000 ALTER TABLE `translation_text` ENABLE KEYS */;
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

-- Dump completed on 2018-09-12 13:17:16
