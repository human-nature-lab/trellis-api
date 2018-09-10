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
INSERT INTO `assign_condition_tag` VALUES ('9b51a9cc-6cbc-43e2-a05c-510495924423','ad24d06a-b932-480e-9b8a-39d00cf88ae2','function (d) {\n   return d.pet_type == \'dog\'\n}','section','2018-09-07 15:00:56','2018-09-07 15:01:20',NULL);
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
INSERT INTO `condition_tag` VALUES ('1f0629de-e256-49d7-818b-67622c4f9c9a','is_human','2018-09-07 15:34:18','2018-09-07 15:34:18',NULL),('ad24d06a-b932-480e-9b8a-39d00cf88ae2','is_dog','2018-09-07 15:00:56','2018-09-07 15:00:56',NULL),('c132adce-84ef-4be0-baca-b2b22bb7c28b','is_people','2018-09-07 15:34:57','2018-09-07 15:34:57',NULL);
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
INSERT INTO `form` VALUES ('bfc6270b-b6c8-4d9e-b26b-c2fc38b65a48','bfc6270b-b6c8-4d9e-b26b-c2fc38b65a48','6c058693-fddb-453f-8529-f0b55333a457',1,1,'2018-09-07 14:53:05','2018-09-07 14:53:21',NULL),('c98e78f8-1bcc-4afc-8e68-530374940213','c98e78f8-1bcc-4afc-8e68-530374940213','56f6924e-57c5-4261-a3cf-f026ef011603',1,1,'2018-09-07 14:52:56','2018-09-07 14:53:00',NULL);
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
INSERT INTO `form_section` VALUES ('2f81a834-86a7-4efa-afef-cb95fbbacb48','c98e78f8-1bcc-4afc-8e68-530374940213','2494f66f-8c02-49f2-832b-e6c49156ee33',2,0,0,'717cf2e1-fc84-4c5a-8b56-ef47acfae561','72c56240-aeb8-4068-9a8c-5ffa4ad99e18','2018-09-07 14:56:08','2018-09-07 14:56:49',NULL),('4fca2881-ede5-4413-b5c6-5c272f6c1f99','c98e78f8-1bcc-4afc-8e68-530374940213','8a6b2fa6-55de-4839-b166-4fcb3b37daf9',1,0,0,NULL,NULL,'2018-09-07 14:53:32','2018-09-07 14:53:32',NULL);
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
INSERT INTO `question` VALUES ('49ef53ee-bff2-4d66-9f1b-b816ae6a1e3d','0f76b96f-613a-4925-bacd-74db45368edb','6ee07d0f-d7f8-4947-9546-49cb6f6a32ca','ebf9af64-9c9e-4655-afbe-aa2c2a652ca9',1,'dog_type','2018-09-07 14:59:48','2018-09-07 14:59:48',NULL),('72c56240-aeb8-4068-9a8c-5ffa4ad99e18','5ae659b6-8945-4adc-86d5-a44b51531def','979ea7f2-c3e9-4115-aae6-accc43d5f155','ecc26ed8-5699-41ce-a675-b23f7df86a63',1,'pets','2018-09-07 14:55:56','2018-09-07 14:55:56',NULL),('7e02ce88-b095-491d-a40e-af0b099de1d0','cebe05f8-8e17-4c5c-a5fa-abc3a9c6c1f9','3cec547c-ec50-480c-b322-042de097b12d','937bbb86-6f63-4379-8ead-64957a35b8b5',1,'intro','2018-09-07 14:53:56','2018-09-07 14:53:56',NULL),('e103a32a-47bb-4fd1-b1c1-98dd8e522df0','0f76b96f-613a-4925-bacd-74db45368edb','ddee1291-042a-484c-b66f-7592cebe0038','e2247ebf-09dc-4d4e-a60e-11e80bb74555',1,'ms','2018-09-07 14:54:24','2018-09-07 14:54:24',NULL),('e978196d-95de-4137-b164-4e2e32f78cc6','b58f23fa-52c7-435e-9b31-5fb771e79f41','beb82ce1-67a5-4ac5-aaa8-ecbf0043faf4','27345e55-ed2e-4397-b492-808490ca21e0',1,'pet_type','2018-09-07 14:56:41','2018-09-07 14:56:41',NULL),('f7e46b66-5f41-46b6-9d69-74cefba4f844','0f76b96f-613a-4925-bacd-74db45368edb','8b68bc2f-5740-4a1d-834e-08bd75b7d0f2','ebf9af64-9c9e-4655-afbe-aa2c2a652ca9',2,'dog_type','2018-09-07 14:59:49','2018-09-07 15:02:24','2018-09-07 15:02:24');
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
INSERT INTO `question_assign_condition_tag` VALUES ('b2bc5aa9-1dd5-4644-b8cf-1914455fa4da','e978196d-95de-4137-b164-4e2e32f78cc6','9b51a9cc-6cbc-43e2-a05c-510495924423','2018-09-07 15:00:56','2018-09-07 15:00:56',NULL);
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
INSERT INTO `question_group` VALUES ('27345e55-ed2e-4397-b492-808490ca21e0','2018-09-07 14:56:12','2018-09-07 14:56:12',NULL),('937bbb86-6f63-4379-8ead-64957a35b8b5','2018-09-07 14:53:35','2018-09-07 14:53:35',NULL),('e2247ebf-09dc-4d4e-a60e-11e80bb74555','2018-09-07 14:54:01','2018-09-07 14:54:01',NULL),('ebf9af64-9c9e-4655-afbe-aa2c2a652ca9','2018-09-07 14:59:26','2018-09-07 14:59:26',NULL),('ecc26ed8-5699-41ce-a675-b23f7df86a63','2018-09-07 14:55:33','2018-09-07 14:55:33',NULL);
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
INSERT INTO `question_group_skip` VALUES ('5272b384-0ab0-4fc7-8df0-19bca21d6e01','ebf9af64-9c9e-4655-afbe-aa2c2a652ca9','d3e8d3ff-277b-4f45-9186-fe38338f1d08','2018-09-07 15:01:44','2018-09-07 15:01:44',NULL);
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
INSERT INTO `roster` VALUES ('2978724b-c2a4-4598-93a5-520e42a8cd66','yogi','0000-00-00 00:00:00','0000-00-00 00:00:00',NULL);
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
INSERT INTO `section` VALUES ('2494f66f-8c02-49f2-832b-e6c49156ee33','a1329da2-981f-43e7-a6b7-9382546d0e7e','2018-09-07 14:56:08','2018-09-07 14:56:08',NULL),('8a6b2fa6-55de-4839-b166-4fcb3b37daf9','6847a5af-3557-48a3-8187-9be01ad17d6e','2018-09-07 14:53:32','2018-09-07 14:53:32',NULL);
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
INSERT INTO `section_question_group` VALUES ('2c015c37-cb7d-4e9a-9be8-05c0d1464c7b','8a6b2fa6-55de-4839-b166-4fcb3b37daf9','ecc26ed8-5699-41ce-a675-b23f7df86a63',3,'2018-09-07 14:55:33','2018-09-07 14:55:33',NULL),('34bb03d2-23de-486a-8d6b-7917f044a784','2494f66f-8c02-49f2-832b-e6c49156ee33','27345e55-ed2e-4397-b492-808490ca21e0',1,'2018-09-07 14:56:12','2018-09-07 14:56:12',NULL),('871b97d7-cf0e-4ccd-a537-1e9d094b0c0e','8a6b2fa6-55de-4839-b166-4fcb3b37daf9','937bbb86-6f63-4379-8ead-64957a35b8b5',1,'2018-09-07 14:53:35','2018-09-07 14:53:35',NULL),('cb36b119-76e4-4689-8439-b2ddfa0fc571','8a6b2fa6-55de-4839-b166-4fcb3b37daf9','e2247ebf-09dc-4d4e-a60e-11e80bb74555',2,'2018-09-07 14:54:01','2018-09-07 14:54:01',NULL),('d9723b8c-6e94-494d-960e-852a2a8821b3','2494f66f-8c02-49f2-832b-e6c49156ee33','ebf9af64-9c9e-4655-afbe-aa2c2a652ca9',2,'2018-09-07 14:59:26','2018-09-07 14:59:26',NULL);
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
INSERT INTO `skip` VALUES ('d3e8d3ff-277b-4f45-9186-fe38338f1d08',1,1,0,'2018-09-07 15:01:44','2018-09-07 15:01:44',NULL);
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
INSERT INTO `skip_condition_tag` VALUES ('0d273994-ffd6-43a0-bcdf-f1aaacb4e2d7','d3e8d3ff-277b-4f45-9186-fe38338f1d08','2018-09-07 15:01:44','2018-09-07 15:01:44',NULL,'is_dog');
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
INSERT INTO `snapshot` VALUES ('04e9261c-8f14-4d5f-a00a-cba3fe621e55','04e9261c-8f14-4d5f-a00a-cba3fe621e55.sqlite.sql.zip','f758488c52e3cc32cb2d07556d1c8b65','2018-09-07 15:40:12','2018-09-07 15:40:12',NULL),('60bcd897-dfbb-40f0-a2a9-571a4583b946','60bcd897-dfbb-40f0-a2a9-571a4583b946.sqlite.sql.zip','58166734971f08fc805652c76cc171c7','2018-09-07 15:44:51','2018-09-07 15:44:51',NULL);
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
INSERT INTO `study_form` VALUES ('a0574906-f486-40b7-b918-4079b38462ea','6a08c96a-fb80-4eae-9b2b-4d03d4b3235d','bfc6270b-b6c8-4d9e-b26b-c2fc38b65a48',2,'2018-09-07 14:53:05','2018-09-07 14:53:20',NULL,1,'06162912-8048-4978-a8d2-92b6dd0c2ed1'),('d7a60998-ab69-4693-abad-eb9f0e75add2','6a08c96a-fb80-4eae-9b2b-4d03d4b3235d','c98e78f8-1bcc-4afc-8e68-530374940213',1,'2018-09-07 14:52:56','2018-09-07 14:52:56',NULL,0,NULL);
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
INSERT INTO `token` VALUES ('08d1a3a5-8532-4244-b35a-c7570d071c08','c1f277ab-e181-11e5-84c9-a45e60f0e921','0990ae5978aadf51c05b7d580c06352b1306d49d9072436d035c34cedbbfebb9b7962dcf1f8105b73a5942e9b2e12fb7beab532394c22806e07aefa5a439cb8e',0,'2018-09-07 15:51:00','2018-09-07 15:51:05',NULL),('0a85a7e2-778c-44bd-bec9-ab279aa03ccd','c1f277ab-e181-11e5-84c9-a45e60f0e921','1b6842f112d400bf38cff6dae8b71c568c0f15394fc98b32cea45f758636fe29aed349666f70a2e533203cf87509d82a6e71db904f0980032ec3ee978e304dce',0,'2018-09-07 16:50:01','2018-09-07 16:51:52',NULL),('14c4582e-e318-4620-93e0-226bb8a888c8','c1f277ab-e181-11e5-84c9-a45e60f0e921','e715fb7e5e7161081a3f6e4c01b2471739453c5a45c72b01f9be073d9b6a45e90e93c327f603e8f72325b6f948a126eb919da7d8fb33c320b2725409c884de71',0,'2018-09-07 21:34:56','2018-09-07 21:35:00',NULL),('1d8ca0b2-ae06-4f30-8d5a-ba585bd7852e','c1f277ab-e181-11e5-84c9-a45e60f0e921','4aaf36b3af943be984a81807613bac26e3a4d7c574afc3ca4da9b425f57a7c0fb02ab536636eadc15a8a0061c223f1d4a487d404ec6c409e0d28a319d7e292a3',0,'2018-09-07 17:12:04','2018-09-07 17:12:22',NULL),('1eec3e7e-c1a3-4be2-bfab-0695745f68ea','c1f277ab-e181-11e5-84c9-a45e60f0e921','33688fa5c6d404d2b5dd05a2025b330616033dcd39a7c47d0b4f5733fbe566a3342e84ea929bd30c645d7fd164ef606dd06331cd0a17014e191f3d161b793a2b',0,'2018-09-07 15:56:50','2018-09-07 15:57:00',NULL),('23b618dd-8ff0-4eae-a2bc-4732dada9683','c1f277ab-e181-11e5-84c9-a45e60f0e921','c91b3bb1819b37299abb7ba3e2e2dea701307a267ca3af1624d6709a226bf4e4317190d0eeae7b34df5a45db88c38d4d31ecd62497ca9269e02394858c529fb6',0,'2018-09-07 21:17:41','2018-09-07 21:17:46',NULL),('258be0b6-cee4-445a-9ba8-83df3603ff6a','c1f277ab-e181-11e5-84c9-a45e60f0e921','dd27bf482974bc9bcc9f5a46b5f074d6a19eccec4ea211ccd399b020bc86df39f1288c5673c35cc794d86141c25ac2a77504c23592bae991acc289652e8d3d9f',0,'2018-09-07 15:33:41','2018-09-07 15:50:46',NULL),('2a0cfa9c-ebc6-45a9-84fe-851e34ea8359','c1f277ab-e181-11e5-84c9-a45e60f0e921','f4b9eaab56dcedbc69a5cd5f51417da1d686f9573f904abf16154380352d2c955c7e2b4c9c3537fcd64a0d607d5b2360d1beb3a691fc3c169458bacd3a2fd282',0,'2018-09-07 16:10:06','2018-09-07 16:10:21',NULL),('31cc9a77-8f1e-4b9e-a043-cc47b9e6cf55','c1f277ab-e181-11e5-84c9-a45e60f0e921','b936a113ee50fde6216badd1bac5b68925d78ed20d3769ed7159889203b1b74ee8979c8ac1bceac8bab03c29a402a739b61d5ee8e7e0dc656f7c6067c989173a',0,'2018-09-07 21:31:57','2018-09-07 21:32:01',NULL),('32f72b0f-d972-43ec-b180-b928c4fd1a7d','c1f277ab-e181-11e5-84c9-a45e60f0e921','ea641c2bd4688d2180c35d9e1fda50a6a99488c866af8bcb0e26df318bb7decc1521fce7411e6590a9ada2deb488f646daf2867ca910b18208fb96d78e171e85',0,'2018-09-07 16:52:09','2018-09-07 16:52:51',NULL),('3461548f-e17c-4fe6-9206-6e9ecb7251ff','c1f277ab-e181-11e5-84c9-a45e60f0e921','8843db5a5123cf50574480cccafc2ed3ab95a1543163116317fd53096e7ff176a0981152a3c7ae765fd35ba1c1e60275fa8df0c76d436f35b5b9d14185ac4f81',0,'2018-09-07 16:43:14','2018-09-07 16:44:27',NULL),('360250a4-8ce7-4cc6-b99e-1a643ce2ba30','c1f277ab-e181-11e5-84c9-a45e60f0e921','f7051697bfdeea63f6672cdbb1935ca7f23528651d20dff75945ec2bbcf4c69cc7e4ffd33971b922411885a29c2a93bc1d22ecdf98cdfc23dd9d17b1f81ab08f',0,'2018-09-07 16:54:15','2018-09-07 16:54:29',NULL),('375adccb-ce7a-41c4-a9c7-8d6245f9df98','c1f277ab-e181-11e5-84c9-a45e60f0e921','129a00ebafb4187b3aad97cd5a5e4b1a2fded397b5d8dd09eddd281042d6e81d366fadd64b602db4f0665149b3300b4ed2126a030b77cfba91e7424ab540c034',0,'2018-09-07 16:01:28','2018-09-07 16:03:27',NULL),('3f9a500b-1190-4019-86d9-35c3c660200c','c1f277ab-e181-11e5-84c9-a45e60f0e921','281e115543ed8a17ec702d585aa2c9bb1bb959fb928a65eef2ccbae4f093dad8fab711a1b148d53da293916ba821f1200448fd7b64427255d5f327667a501bc0',0,'2018-09-07 15:46:16','2018-09-07 15:46:20',NULL),('531bb3e2-260e-4680-8fef-71ab58f6e76f','c1f277ab-e181-11e5-84c9-a45e60f0e921','2ce67dcfd5c9b040bf60831b7b72548590ad93270017470f7289854b778c1f3e565184443e12ab5a43f00bf4d662d151c75eb3e0e77810d664d939cf844d3add',0,'2018-09-07 15:59:48','2018-09-07 15:59:57',NULL),('647113d1-e7be-4f04-ace0-24f05bc07d2b','c1f277ab-e181-11e5-84c9-a45e60f0e921','96bfc7f81f1547a57ed3cfbbe5214acd85b84c56f23ef79d1985de243fddbe0d8dcac3c763c13ff8afc48d5506ce476f639b0568046a0b84103ad78031a332f0',0,'2018-09-07 17:10:54','2018-09-07 17:11:08',NULL),('68623762-982f-4b92-9a2b-db2e048eacd1','c1f277ab-e181-11e5-84c9-a45e60f0e921','4734ee85c372a30ed2f083fde8dc3bb8e18ed8b6f89d7b4c99437eb98ab71d81f98d86bf181cd9d39e3544a00ccb99ac3371b85b38e0fdf55594a2543db3db03',0,'2018-09-07 21:29:36','2018-09-07 21:29:41',NULL),('6e26c46f-43dd-478e-b08e-b4aeea467caf','c1f277ab-e181-11e5-84c9-a45e60f0e921','813e6f1c82b573104e7aa8eb8a031a569d26144b37483cc9ad49b5641a5810c8bfb0c95cdf0dd053ccc038db279fd52b78be296a7037f05198a651830cd38856',0,'2018-09-07 21:44:46','2018-09-07 21:44:50',NULL),('7c4adf5d-461e-4cff-9bba-53a9c46542e0','c1f277ab-e181-11e5-84c9-a45e60f0e921','9a95df9aeaa094df2d81496edba3454d67fb0c6bb5c14cb2bbb67384a8911ae24f7f763fc66979921365aa26c220e94c40c4ce025f22e15148e1b0e0c3661ad2',0,'2018-09-07 21:46:08','2018-09-07 21:46:14',NULL),('81cd2c3a-34b8-426d-ab7d-8357e8f78dba','c1f277ab-e181-11e5-84c9-a45e60f0e921','123a0b18c2aff895755858bd185a13b63690137d7cec3299de0eb1a02b2e8ede6cdc87f8c00aaf4432bb1e777d574b2a2b4d417cef4f419f539e14f998039755',0,'2018-09-07 21:24:10','2018-09-07 21:24:14',NULL),('8b2d7056-b786-4775-94c1-86a67de62bd1','c1f277ab-e181-11e5-84c9-a45e60f0e921','cdef79ad45e7975d1e1fc0a5deab667b8aea9a2b12d19092cde4ba7a96f153f0caa6ea87b47bd9c0f4964e4a67e9a61c67e821c28cdd514e36384d8c9e2f31d6',0,'2018-09-07 16:48:58','2018-09-07 16:49:11',NULL),('9d2c634b-772e-46e6-8492-ab94c51ef875','c1f277ab-e181-11e5-84c9-a45e60f0e921','db6831c1d8f24f2acdcc01c96ca3007fad3afe68c5bf862e0792a85682c0bab0e172d609d36920fa70fccb095bc1c97661809a34ba534a21d1a9ca889e53e696',0,'2018-09-07 21:17:15','2018-09-07 21:17:18',NULL),('a88343c6-cd61-4e1b-aea0-2f6397dea430','c1f277ab-e181-11e5-84c9-a45e60f0e921','1ef4eb87192b766467fd27fcae43ac68be890dfe570484f07feb82a474032d4c80b72051714735d5a3ad5b8b1c8dbc5d3a5f98d000d249f944786c7ce8d8fda0',0,'2018-09-07 15:49:46','2018-09-07 15:49:52',NULL),('ab2832c5-8185-415e-8480-ea7fae14c538','c1f277ab-e181-11e5-84c9-a45e60f0e921','1e8156ab60ee6354af43c3a61ec9bad07762a3871bad833caf3f8a5412544c7213480e5367c80c49dc6762fa5a56364e96a2b67c6887c59486cbe3dc4f495b7d',0,'2018-09-07 16:03:53','2018-09-07 16:04:06',NULL),('adaa31a9-94e7-4fc2-8dd8-349d1f6372fc','c1f277ab-e181-11e5-84c9-a45e60f0e921','f7d2d4c8268ec62f7b86e2d9e548b57800a7cd2fc7ad86981954d7de844b982b055d5b72b9028cbaa80f4762bf276d0553a438e05bb94062189947b62bcf02c3',0,'2018-09-07 15:42:55','2018-09-07 15:43:00',NULL),('b166d91e-9016-4c4e-bdb1-5d25115ff4f8','c1f277ab-e181-11e5-84c9-a45e60f0e921','93d9c729eb51825cb9df6fcef7dad36170354389aef15661b724fecfd5f7001cb8fe26610d514c5a84911c2ba6fc7dc5aafd2801eb0071eb410a475f143a83ee',0,'2018-09-07 15:45:34','2018-09-07 15:45:39',NULL),('b1d3661e-a64d-4019-8df6-08c32ad55141','c1f277ab-e181-11e5-84c9-a45e60f0e921','97dd43c4479cf2d1bc056871d954c99fb3bf191495f2c7ba5fd5b9a87990ad4997fb615f6c312569fb8eecf0c6f60440a2ff672ca42c1baa292b3a0961628c60',0,'2018-09-07 15:05:59','2018-09-07 15:06:01',NULL),('b4cd4555-2226-4060-9fd8-c338c53b5375','c1f277ab-e181-11e5-84c9-a45e60f0e921','80e718a325360d34f5a1c5321748231013242c0b2d5bfa84d9b68ae7ee6dc0f4413b3fa66507e5e14c50ca532e033ddde282e5ed02413bdaaa3988be1294c184',0,'2018-09-07 21:46:47','2018-09-07 21:46:57',NULL),('c8365e2b-d71c-44cc-bcdb-f1c5cb05e2a3','c1f277ab-e181-11e5-84c9-a45e60f0e921','766ada003215fb23e70b224ec77ee2dd7ae0cffbfc6f531fc98faacc8c26a01e51b7eb915da709256365ff8d1f4720ee35255145918dde108e730807d90fe3c2',0,'2018-09-07 16:05:07','2018-09-07 16:08:19',NULL),('cec5b57a-66e2-4d91-9eb2-1f2f6f15365a','c1f277ab-e181-11e5-84c9-a45e60f0e921','2186d99e41512f1e85cb0b694e9a65f34a18b83838c2947c95c74a9fd2682229717ce0e5569a6fb638725f701a3ccabfce6a5c372695202d65814ef0e11c05df',0,'2018-09-07 17:01:39','2018-09-07 17:01:53',NULL),('cfa191ae-700e-47db-9ca0-ab59b1311454','c1f277ab-e181-11e5-84c9-a45e60f0e921','34b06d350854a67c64f76f565bc1a10db67e73ecefe128359415cbc34283fa57447b96339c68d79b9dae3fed952191746f02fb86a3d1ad7c4d2fbb706bdfab65',0,'2018-09-07 15:48:49','2018-09-07 15:48:53',NULL),('d1d0d126-8511-4385-8333-38caf77c9364','c1f277ab-e181-11e5-84c9-a45e60f0e921','46c0b51a5559193e7f4a5a4a0e9643daa9830e940cdecedb377c3912575d7c5014759891959ec6dff915a497b5dcab3839f6440e8ec4ff1a168731f0912ab3ec',0,'2018-09-07 21:32:38','2018-09-07 21:32:42',NULL),('d6fff010-9452-4a73-bc53-d07e0c9f5c1c','c1f277ab-e181-11e5-84c9-a45e60f0e921','5e491043f1bad0ed67a748316eb7818cdbeecdc00fcbcac597d92714bf3bcb8ee50c150b6844b3c6a2a139d9f58e2c245353d4182492dbfe4470d3df511f458f',0,'2018-09-07 16:58:50','2018-09-07 16:59:04',NULL),('dbc5ae41-6dcd-4af8-b0f3-0bb8ab86c94f','c1f277ab-e181-11e5-84c9-a45e60f0e921','1b1e252d4781a796188de852242b342cbd3855d172b8ceb0405e8d545eac83827b73cf695dd43c3d218564eff8ea9deb8d69969f76f5d940095269f4c13e1abb',0,'2018-09-07 16:39:42','2018-09-07 16:41:15',NULL),('e11630e3-cdc6-4093-8563-35295bf0dd22','c1f277ab-e181-11e5-84c9-a45e60f0e921','726838094c30eb7ab5f4454b720f18395150a0d9f9250e403b03dcd2529de13f70d86d2255531b4358b980413252cd2ecca3f840b4d16235306c6cb0013bf371',0,'2018-09-07 16:57:52','2018-09-07 16:58:06',NULL),('e895916c-0b6a-4979-aad2-ee52b77bf853','c1f277ab-e181-11e5-84c9-a45e60f0e921','c78a17565ada77ffe4e5c543586f36c548a9921e533cfa32b0419c62ffb851a52191411e96513501f04698ec1da11c6cc1f1668a1a1a1c1fac3f43de4f01b1b6',0,'2018-09-07 16:32:24','2018-09-07 16:32:38',NULL),('e9d67a9b-9a66-4df7-a3a4-010b0ec1834f','c1f277ab-e181-11e5-84c9-a45e60f0e921','ba8683d05877b239350dcc97d16d9bd0e0543d2d2169d4295318b942be7c28f3fb984632e77e79e229c81ac0336301573066ee56918fba0a2b22d670054bdfb0',0,'2018-09-07 15:53:28','2018-09-07 15:53:32',NULL),('f2c11f75-a4ca-4092-8e11-5770406144fe','c1f277ab-e181-11e5-84c9-a45e60f0e921','42fc71e9e81e6d9e880febba1bbdf6b4a2c068310e4ef93a7a53104827356c8c4a0459dcc53611513563797f9c6b0540591105cf55ec3192d629d0c7711e4187',0,'2018-09-07 16:45:17','2018-09-07 16:48:37',NULL),('f727d901-3f36-489d-9981-059ada1ee165','c1f277ab-e181-11e5-84c9-a45e60f0e921','e4bd1f16cd4ea46d7309837629cba18a86a50924efea43407c458282658a7999b11b2a5b390bd65e8817a8f26b97216a652af11794093dad1423f74b5ab07c43',0,'2018-09-07 14:52:31','2018-09-07 15:39:43',NULL),('fae1bc38-f416-4967-86c8-15296d35d46c','c1f277ab-e181-11e5-84c9-a45e60f0e921','82eeeddc3aa25194879c3349a45610f8ce775a7e055121111b06bb38de33a0ecf709cbc82541021fa86d54fd921609a183eccbfd0dc9fde283021eb257d6de61',0,'2018-09-07 21:28:11','2018-09-07 21:28:15',NULL),('fddd68e5-547e-4629-a79a-ba7fb0562f9a','c1f277ab-e181-11e5-84c9-a45e60f0e921','9e2b1c1d28114c37053469d10c3b004c53ba57719ff1039ec3124c4fe6f1b02e31979e4fd37428ddc9bab986828911118fe530a8a469825b64c920f3f4255e2b',0,'2018-09-07 17:00:09','2018-09-07 17:00:22',NULL);
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
INSERT INTO `translation` VALUES ('0310ad5f-e129-49fa-a8ef-7c96dab847ce','2018-09-07 14:54:27','2018-09-07 14:54:27',NULL),('13cf2681-c623-4e49-ac4d-22bc866a3f1c','2018-09-07 14:56:57','2018-09-07 14:56:57',NULL),('1ee50b21-fabd-49e5-b6ca-d14aed147285','2018-09-07 14:58:22','2018-09-07 14:58:22',NULL),('286aa8c2-6d0b-4b35-a905-19cedc8640f9','2018-09-07 14:56:53','2018-09-07 14:56:53',NULL),('3cec547c-ec50-480c-b322-042de097b12d','2018-09-07 14:53:56','2018-09-07 14:53:56',NULL),('4c160d54-8ea6-4ff0-8661-9351bfaf160b','2018-09-07 14:59:56','2018-09-07 14:59:56',NULL),('53b61903-f13e-415c-a574-c892b8eea9e2','2018-09-07 14:54:48','2018-09-07 14:54:48',NULL),('56f6924e-57c5-4261-a3cf-f026ef011603','2018-09-07 14:52:56','2018-09-07 14:52:56',NULL),('6847a5af-3557-48a3-8187-9be01ad17d6e','2018-09-07 14:53:32','2018-09-07 14:53:32',NULL),('6c058693-fddb-453f-8529-f0b55333a457','2018-09-07 14:53:05','2018-09-07 14:53:05',NULL),('6ee07d0f-d7f8-4947-9546-49cb6f6a32ca','2018-09-07 14:59:48','2018-09-07 14:59:48',NULL),('717cf2e1-fc84-4c5a-8b56-ef47acfae561','2018-09-07 14:56:45','2018-09-07 14:56:45',NULL),('8b68bc2f-5740-4a1d-834e-08bd75b7d0f2','2018-09-07 14:59:49','2018-09-07 14:59:49',NULL),('90862bbf-f083-4835-bbc4-444c8af232cd','2018-09-07 14:58:54','2018-09-07 14:58:54',NULL),('979ea7f2-c3e9-4115-aae6-accc43d5f155','2018-09-07 14:55:56','2018-09-07 14:55:56',NULL),('a1329da2-981f-43e7-a6b7-9382546d0e7e','2018-09-07 14:56:08','2018-09-07 14:56:08',NULL),('beb82ce1-67a5-4ac5-aaa8-ecbf0043faf4','2018-09-07 14:56:41','2018-09-07 14:56:41',NULL),('c54f47c9-0c5e-4a1d-bbfd-cd210b44086f','2018-09-07 14:56:44','2018-09-07 14:56:44',NULL),('d19f44ad-3241-4d31-88ba-51a9b7fc4f59','2018-09-07 14:54:31','2018-09-07 14:54:31',NULL),('ddee1291-042a-484c-b66f-7592cebe0038','2018-09-07 14:54:24','2018-09-07 14:54:24',NULL),('e2e04746-5ded-4331-9029-a1bae0aff2f1','2018-09-07 15:00:19','2018-09-07 15:00:19',NULL),('ea4fedd7-7d20-4c6c-9a34-697f409ee306','2018-09-07 14:59:54','2018-09-07 14:59:54',NULL);
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
INSERT INTO `translation_text` VALUES ('04433f3d-34fa-4161-bc4a-d002917ff50c','90862bbf-f083-4835-bbc4-444c8af232cd','4a1d88ab-84d4-11e5-ba05-0800279114ca','caballo','2018-09-07 14:59:16','2018-09-07 14:59:18',NULL),('09e36928-3326-4e2f-bf28-86826fccb0a7','13cf2681-c623-4e49-ac4d-22bc866a3f1c','48984fbe-84d4-11e5-ba05-0800279114ca','cat','2018-09-07 14:57:45','2018-09-07 14:57:45',NULL),('2b7d2196-924e-416e-99bd-9102939867b2','e2e04746-5ded-4331-9029-a1bae0aff2f1','4a1d88ab-84d4-11e5-ba05-0800279114ca','otro','2018-09-07 15:02:03','2018-09-07 15:02:06',NULL),('30891da4-6e5a-467e-b6e2-f99f7a4e2944','56f6924e-57c5-4261-a3cf-f026ef011603','48984fbe-84d4-11e5-ba05-0800279114ca','Test form 1','2018-09-07 14:52:56','2018-09-07 14:53:00',NULL),('373c244b-63ec-4e52-82f0-a8639fed2ab4','53b61903-f13e-415c-a574-c892b8eea9e2','48984fbe-84d4-11e5-ba05-0800279114ca','Three','2018-09-07 14:55:15','2018-09-07 14:55:19',NULL),('398f37da-226f-4d6d-8e46-4cfffe089d23','1ee50b21-fabd-49e5-b6ca-d14aed147285','4a1d88ab-84d4-11e5-ba05-0800279114ca','otro','2018-09-07 14:58:49','2018-09-07 14:58:49',NULL),('40e22eda-4f62-48c4-aaab-62878d8e0d93','6ee07d0f-d7f8-4947-9546-49cb6f6a32ca','48984fbe-84d4-11e5-ba05-0800279114ca','What color of fur does [pets] have?','2018-09-07 14:59:48','2018-09-07 15:00:16',NULL),('46de8e00-4a87-4f8b-83cd-7f0453c35c10','8b68bc2f-5740-4a1d-834e-08bd75b7d0f2','48984fbe-84d4-11e5-ba05-0800279114ca','What kind of dog is [pets]?','2018-09-07 14:59:49','2018-09-07 14:59:49',NULL),('5a908274-3e9c-4f5d-86dd-ba4a86f666f0','4c160d54-8ea6-4ff0-8661-9351bfaf160b','48984fbe-84d4-11e5-ba05-0800279114ca','brown','2018-09-07 15:01:56','2018-09-07 15:01:56',NULL),('63dbcd7c-397e-4cd3-8032-e722f06500bd','e2e04746-5ded-4331-9029-a1bae0aff2f1','48984fbe-84d4-11e5-ba05-0800279114ca','other','2018-09-07 15:01:58','2018-09-07 15:01:58',NULL),('6a0baf12-556a-450a-9579-0a88f28b3298','ddee1291-042a-484c-b66f-7592cebe0038','48984fbe-84d4-11e5-ba05-0800279114ca','Multiple select','2018-09-07 14:54:24','2018-09-07 14:54:24',NULL),('6d3e500a-5ca6-4d52-b3c7-efd915dddf33','d19f44ad-3241-4d31-88ba-51a9b7fc4f59','48984fbe-84d4-11e5-ba05-0800279114ca','Two','2018-09-07 14:54:44','2018-09-07 14:55:08',NULL),('7ee7b9ab-031f-4f95-ae2c-e7b1df654c2e','286aa8c2-6d0b-4b35-a905-19cedc8640f9','48984fbe-84d4-11e5-ba05-0800279114ca','dog','2018-09-07 14:57:43','2018-09-07 14:57:43',NULL),('8b32de48-ee09-487f-8e19-b2270dd8a817','13cf2681-c623-4e49-ac4d-22bc866a3f1c','4a1d88ab-84d4-11e5-ba05-0800279114ca','gato','2018-09-07 14:58:20','2018-09-07 14:58:21',NULL),('90dd82d5-8e99-4997-a8a9-6461093e8cc0','0310ad5f-e129-49fa-a8ef-7c96dab847ce','48984fbe-84d4-11e5-ba05-0800279114ca','One','2018-09-07 14:54:40','2018-09-07 14:54:40',NULL),('93324a19-4749-4621-8e90-c8a94b3e83ee','6847a5af-3557-48a3-8187-9be01ad17d6e','48984fbe-84d4-11e5-ba05-0800279114ca','One','2018-09-07 14:53:32','2018-09-07 14:53:32',NULL),('977aa89c-cb2e-46a6-846f-e12c746563e3','90862bbf-f083-4835-bbc4-444c8af232cd','48984fbe-84d4-11e5-ba05-0800279114ca','horse','2018-09-07 14:59:05','2018-09-07 14:59:05',NULL),('a3543611-30c6-4292-8520-f9281230ab0f','d19f44ad-3241-4d31-88ba-51a9b7fc4f59','48984fbe-84d4-11e5-ba05-0800279114ca','Two','2018-09-07 14:54:45','2018-09-07 14:54:45',NULL),('a9c26a70-b47d-40e3-8503-fa26b808e52b','d19f44ad-3241-4d31-88ba-51a9b7fc4f59','4a1d88ab-84d4-11e5-ba05-0800279114ca','Dos','2018-09-07 14:54:58','2018-09-07 14:54:58',NULL),('b016ad17-387f-42dc-8b6b-b160635f9079','979ea7f2-c3e9-4115-aae6-accc43d5f155','48984fbe-84d4-11e5-ba05-0800279114ca','List your pets names','2018-09-07 14:55:56','2018-09-07 14:55:56',NULL),('bb7df38c-75a5-4785-bb59-b058eb04a7e4','3cec547c-ec50-480c-b322-042de097b12d','48984fbe-84d4-11e5-ba05-0800279114ca','An intro questoin','2018-09-07 14:53:56','2018-09-07 14:53:56',NULL),('dc6a1102-2918-49fa-9911-75afe9b3ed1f','286aa8c2-6d0b-4b35-a905-19cedc8640f9','4a1d88ab-84d4-11e5-ba05-0800279114ca','perro','2018-09-07 14:57:52','2018-09-07 14:57:52',NULL),('e3d13e28-6df9-4dbf-8936-357f7cc2797c','6c058693-fddb-453f-8529-f0b55333a457','48984fbe-84d4-11e5-ba05-0800279114ca','Add respondent form','2018-09-07 14:53:05','2018-09-07 14:53:17',NULL),('ee16ceff-b6c2-4b9a-80c2-b02d08ef963b','53b61903-f13e-415c-a574-c892b8eea9e2','4a1d88ab-84d4-11e5-ba05-0800279114ca','Tres','2018-09-07 14:55:23','2018-09-07 14:55:23',NULL),('f64799ec-0110-49dd-8cd6-e29aef9d8783','beb82ce1-67a5-4ac5-aaa8-ecbf0043faf4','48984fbe-84d4-11e5-ba05-0800279114ca','What kind of pet is [pets]?','2018-09-07 14:56:41','2018-09-07 14:56:41',NULL),('f6cff3d1-c70e-45ad-888a-4273b8147f9e','1ee50b21-fabd-49e5-b6ca-d14aed147285','48984fbe-84d4-11e5-ba05-0800279114ca','other','2018-09-07 14:58:34','2018-09-07 14:58:34',NULL),('fac6fff4-d419-4abf-8163-99faa71677b2','a1329da2-981f-43e7-a6b7-9382546d0e7e','48984fbe-84d4-11e5-ba05-0800279114ca','Pet info','2018-09-07 14:56:08','2018-09-07 14:56:08',NULL),('fbaf8280-039e-4220-85d9-e6ca42a215b2','ea4fedd7-7d20-4c6c-9a34-697f409ee306','48984fbe-84d4-11e5-ba05-0800279114ca','yellow','2018-09-07 15:01:53','2018-09-07 15:01:53',NULL),('fc89594e-e418-4889-8c09-47a7df30f622','0310ad5f-e129-49fa-a8ef-7c96dab847ce','4a1d88ab-84d4-11e5-ba05-0800279114ca','Uno','2018-09-07 14:54:55','2018-09-07 14:54:55',NULL);
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

-- Dump completed on 2018-09-07 21:54:50
