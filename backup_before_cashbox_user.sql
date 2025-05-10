-- MySQL dump 10.13  Distrib 8.0.42, for macos15 (x86_64)
--
-- Host: localhost    Database: accall
-- ------------------------------------------------------
-- Server version	8.0.42

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `account_balances`
--

DROP TABLE IF EXISTS `account_balances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `account_balances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `account_id` bigint unsigned NOT NULL,
  `currency_id` bigint unsigned NOT NULL,
  `balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_balances_account_id_currency_id_unique` (`account_id`,`currency_id`),
  KEY `account_balances_currency_id_foreign` (`currency_id`),
  CONSTRAINT `account_balances_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `account_balances_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account_balances`
--

LOCK TABLES `account_balances` WRITE;
/*!40000 ALTER TABLE `account_balances` DISABLE KEYS */;
/*!40000 ALTER TABLE `account_balances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `accounting_settings`
--

DROP TABLE IF EXISTS `accounting_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `accounting_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sales_account_id` bigint unsigned DEFAULT NULL,
  `purchases_account_id` bigint unsigned DEFAULT NULL,
  `receivables_account_id` bigint unsigned DEFAULT NULL,
  `payables_account_id` bigint unsigned DEFAULT NULL,
  `expenses_account_id` bigint unsigned DEFAULT NULL,
  `liabilities_account_id` bigint unsigned DEFAULT NULL,
  `deductions_account_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounting_settings`
--

LOCK TABLES `accounting_settings` WRITE;
/*!40000 ALTER TABLE `accounting_settings` DISABLE KEYS */;
INSERT INTO `accounting_settings` VALUES (1,'IQD',20,13,13,17,24,25,26,'2025-05-02 20:41:47','2025-05-02 20:43:13'),(2,'USD',31,31,31,32,33,34,35,'2025-05-02 20:41:47','2025-05-02 20:43:13');
/*!40000 ALTER TABLE `accounting_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `accounts`
--

DROP TABLE IF EXISTS `accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `type` enum('asset','liability','revenue','expense','equity') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nature` enum('debit','credit') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_cash_box` tinyint(1) NOT NULL DEFAULT '0',
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IQD',
  `is_group` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `accounts_code_unique` (`code`),
  KEY `accounts_parent_id_foreign` (`parent_id`),
  CONSTRAINT `accounts_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounts`
--

LOCK TABLES `accounts` WRITE;
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
INSERT INTO `accounts` VALUES (1,'الأصول','1000',NULL,'asset',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(2,'الالتزامات','2000',NULL,'liability',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(3,'الإيرادات','3000',NULL,'revenue',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(4,'المصاريف','4000',NULL,'expense',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(5,'رأس المال','5000',NULL,'equity',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(6,'النقدية','1100',1,'asset',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(7,'البنوك','1200',1,'asset',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(8,'حسابات العملاء','1300',1,'asset',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(9,'المخزون','1400',1,'asset',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(10,'الأصول الثابتة','1500',1,'asset',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(11,'صندوق رئيسي دينار','1101',6,'asset','debit',1,'IQD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(12,'بنك رئيسي دينار','1201',7,'asset','debit',0,'IQD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(13,'عميل رئيسي دينار','1301',8,'asset','debit',0,'IQD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(14,'حسابات الموردين','2100',2,'liability',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(15,'الضرائب المستحقة','2200',2,'liability',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(16,'قروض طويلة الأجل','2300',2,'liability',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(17,'مورد رئيسي دينار','2101',14,'liability','credit',0,'IQD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(18,'مبيعات المنتجات','3100',3,'revenue',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(19,'مبيعات الخدمات','3200',3,'revenue',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(20,'مبيعات نقدية','3101',18,'revenue','credit',0,'IQD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(21,'مبيعات بالتقسيط','3201',19,'revenue','credit',0,'IQD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(22,'رواتب الموظفين','4100',4,'expense',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(23,'مصاريف إيجار','4200',4,'expense',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(24,'مصروف رواتب دينار','4101',22,'expense','debit',0,'IQD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(25,'ذمم مستحقة للموظفين دينار','2102',14,'liability','credit',0,'IQD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(26,'خصومات رواتب دينار','2201',15,'liability','credit',0,'IQD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(27,'رأس مال المؤسسين','5100',5,'equity',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(28,'رأس مال المالك','5101',5,'equity','credit',0,'IQD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(29,'صندوق رئيسي دولار','1102',6,'asset','debit',1,'USD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(30,'بنك رئيسي دولار','1202',7,'asset','debit',0,'USD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(31,'عميل رئيسي دولار','1302',8,'asset','debit',0,'USD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(32,'مورد رئيسي دولار','2103',14,'liability','credit',0,'USD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(33,'مصروف رواتب دولار','4102',22,'expense','debit',0,'USD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(34,'ذمم مستحقة للموظفين دولار','2104',14,'liability','credit',0,'USD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(35,'خصومات رواتب دولار','2202',15,'liability','credit',0,'USD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(38,'دينار تجريبي','5102',1,'asset','debit',1,'IQD',0,'2025-05-04 20:51:10','2025-05-04 20:51:10'),(39,'دولار تجريبي','5103',1,'asset','debit',1,'USD',0,'2025-05-04 20:51:28','2025-05-04 20:51:28');
/*!40000 ALTER TABLE `accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `branches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branches`
--

LOCK TABLES `branches` WRITE;
/*!40000 ALTER TABLE `branches` DISABLE KEYS */;
/*!40000 ALTER TABLE `branches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('laravel_cache_spatie.permission.cache','a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:70:{i:0;a:3:{s:1:\"a\";i:142;s:1:\"b\";s:27:\"عرض المستخدمين\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:143;s:1:\"b\";s:23:\"إضافة مستخدم\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:144;s:1:\"b\";s:23:\"تعديل مستخدم\";s:1:\"c\";s:3:\"web\";}i:3;a:3:{s:1:\"a\";i:145;s:1:\"b\";s:19:\"حذف مستخدم\";s:1:\"c\";s:3:\"web\";}i:4;a:3:{s:1:\"a\";i:146;s:1:\"b\";s:21:\"عرض الأدوار\";s:1:\"c\";s:3:\"web\";}i:5;a:3:{s:1:\"a\";i:147;s:1:\"b\";s:17:\"إضافة دور\";s:1:\"c\";s:3:\"web\";}i:6;a:3:{s:1:\"a\";i:148;s:1:\"b\";s:17:\"تعديل دور\";s:1:\"c\";s:3:\"web\";}i:7;a:3:{s:1:\"a\";i:149;s:1:\"b\";s:13:\"حذف دور\";s:1:\"c\";s:3:\"web\";}i:8;a:3:{s:1:\"a\";i:150;s:1:\"b\";s:25:\"عرض الصلاحيات\";s:1:\"c\";s:3:\"web\";}i:9;a:3:{s:1:\"a\";i:151;s:1:\"b\";s:23:\"إضافة صلاحية\";s:1:\"c\";s:3:\"web\";}i:10;a:3:{s:1:\"a\";i:152;s:1:\"b\";s:23:\"تعديل صلاحية\";s:1:\"c\";s:3:\"web\";}i:11;a:3:{s:1:\"a\";i:153;s:1:\"b\";s:19:\"حذف صلاحية\";s:1:\"c\";s:3:\"web\";}i:12;a:3:{s:1:\"a\";i:154;s:1:\"b\";s:23:\"عرض الحسابات\";s:1:\"c\";s:3:\"web\";}i:13;a:3:{s:1:\"a\";i:155;s:1:\"b\";s:19:\"إضافة حساب\";s:1:\"c\";s:3:\"web\";}i:14;a:3:{s:1:\"a\";i:156;s:1:\"b\";s:19:\"تعديل حساب\";s:1:\"c\";s:3:\"web\";}i:15;a:3:{s:1:\"a\";i:157;s:1:\"b\";s:15:\"حذف حساب\";s:1:\"c\";s:3:\"web\";}i:16;a:3:{s:1:\"a\";i:158;s:1:\"b\";s:23:\"عرض الفواتير\";s:1:\"c\";s:3:\"web\";}i:17;a:3:{s:1:\"a\";i:159;s:1:\"b\";s:23:\"إضافة فاتورة\";s:1:\"c\";s:3:\"web\";}i:18;a:3:{s:1:\"a\";i:160;s:1:\"b\";s:23:\"تعديل فاتورة\";s:1:\"c\";s:3:\"web\";}i:19;a:3:{s:1:\"a\";i:161;s:1:\"b\";s:19:\"حذف فاتورة\";s:1:\"c\";s:3:\"web\";}i:20;a:3:{s:1:\"a\";i:162;s:1:\"b\";s:23:\"تسديد فاتورة\";s:1:\"c\";s:3:\"web\";}i:21;a:3:{s:1:\"a\";i:163;s:1:\"b\";s:23:\"طباعة فاتورة\";s:1:\"c\";s:3:\"web\";}i:22;a:3:{s:1:\"a\";i:164;s:1:\"b\";s:21:\"عرض السندات\";s:1:\"c\";s:3:\"web\";}i:23;a:3:{s:1:\"a\";i:165;s:1:\"b\";s:17:\"إضافة سند\";s:1:\"c\";s:3:\"web\";}i:24;a:3:{s:1:\"a\";i:166;s:1:\"b\";s:17:\"تعديل سند\";s:1:\"c\";s:3:\"web\";}i:25;a:3:{s:1:\"a\";i:167;s:1:\"b\";s:13:\"حذف سند\";s:1:\"c\";s:3:\"web\";}i:26;a:3:{s:1:\"a\";i:168;s:1:\"b\";s:17:\"طباعة سند\";s:1:\"c\";s:3:\"web\";}i:27;a:3:{s:1:\"a\";i:169;s:1:\"b\";s:36:\"عرض الحركات المالية\";s:1:\"c\";s:3:\"web\";}i:28;a:3:{s:1:\"a\";i:170;s:1:\"b\";s:30:\"إضافة حركة مالية\";s:1:\"c\";s:3:\"web\";}i:29;a:3:{s:1:\"a\";i:171;s:1:\"b\";s:30:\"تعديل حركة مالية\";s:1:\"c\";s:3:\"web\";}i:30;a:3:{s:1:\"a\";i:172;s:1:\"b\";s:26:\"حذف حركة مالية\";s:1:\"c\";s:3:\"web\";}i:31;a:3:{s:1:\"a\";i:173;s:1:\"b\";s:21:\"عرض العملاء\";s:1:\"c\";s:3:\"web\";}i:32;a:3:{s:1:\"a\";i:174;s:1:\"b\";s:19:\"إضافة عميل\";s:1:\"c\";s:3:\"web\";}i:33;a:3:{s:1:\"a\";i:175;s:1:\"b\";s:19:\"تعديل عميل\";s:1:\"c\";s:3:\"web\";}i:34;a:3:{s:1:\"a\";i:176;s:1:\"b\";s:15:\"حذف عميل\";s:1:\"c\";s:3:\"web\";}i:35;a:4:{s:1:\"a\";i:177;s:1:\"b\";s:21:\"عرض العناصر\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:36;a:4:{s:1:\"a\";i:178;s:1:\"b\";s:19:\"إضافة عنصر\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:37;a:4:{s:1:\"a\";i:179;s:1:\"b\";s:19:\"تعديل عنصر\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:38;a:3:{s:1:\"a\";i:180;s:1:\"b\";s:15:\"حذف عنصر\";s:1:\"c\";s:3:\"web\";}i:39;a:3:{s:1:\"a\";i:181;s:1:\"b\";s:23:\"عرض الموظفين\";s:1:\"c\";s:3:\"web\";}i:40;a:3:{s:1:\"a\";i:182;s:1:\"b\";s:19:\"إضافة موظف\";s:1:\"c\";s:3:\"web\";}i:41;a:3:{s:1:\"a\";i:183;s:1:\"b\";s:19:\"تعديل موظف\";s:1:\"c\";s:3:\"web\";}i:42;a:3:{s:1:\"a\";i:184;s:1:\"b\";s:15:\"حذف موظف\";s:1:\"c\";s:3:\"web\";}i:43;a:3:{s:1:\"a\";i:185;s:1:\"b\";s:21:\"عرض الرواتب\";s:1:\"c\";s:3:\"web\";}i:44;a:3:{s:1:\"a\";i:186;s:1:\"b\";s:19:\"إضافة راتب\";s:1:\"c\";s:3:\"web\";}i:45;a:3:{s:1:\"a\";i:187;s:1:\"b\";s:19:\"تعديل راتب\";s:1:\"c\";s:3:\"web\";}i:46;a:3:{s:1:\"a\";i:188;s:1:\"b\";s:15:\"حذف راتب\";s:1:\"c\";s:3:\"web\";}i:47;a:3:{s:1:\"a\";i:189;s:1:\"b\";s:32:\"عرض دفعات الرواتب\";s:1:\"c\";s:3:\"web\";}i:48;a:3:{s:1:\"a\";i:190;s:1:\"b\";s:28:\"إضافة دفعة راتب\";s:1:\"c\";s:3:\"web\";}i:49;a:3:{s:1:\"a\";i:191;s:1:\"b\";s:28:\"تعديل دفعة راتب\";s:1:\"c\";s:3:\"web\";}i:50;a:3:{s:1:\"a\";i:192;s:1:\"b\";s:24:\"حذف دفعة راتب\";s:1:\"c\";s:3:\"web\";}i:51;a:3:{s:1:\"a\";i:193;s:1:\"b\";s:30:\"عرض كشوف الرواتب\";s:1:\"c\";s:3:\"web\";}i:52;a:3:{s:1:\"a\";i:194;s:1:\"b\";s:28:\"إضافة كشف رواتب\";s:1:\"c\";s:3:\"web\";}i:53;a:3:{s:1:\"a\";i:195;s:1:\"b\";s:28:\"تعديل كشف رواتب\";s:1:\"c\";s:3:\"web\";}i:54;a:3:{s:1:\"a\";i:196;s:1:\"b\";s:24:\"حذف كشف رواتب\";s:1:\"c\";s:3:\"web\";}i:55;a:3:{s:1:\"a\";i:197;s:1:\"b\";s:21:\"عرض العملات\";s:1:\"c\";s:3:\"web\";}i:56;a:3:{s:1:\"a\";i:198;s:1:\"b\";s:19:\"إضافة عملة\";s:1:\"c\";s:3:\"web\";}i:57;a:3:{s:1:\"a\";i:199;s:1:\"b\";s:19:\"تعديل عملة\";s:1:\"c\";s:3:\"web\";}i:58;a:3:{s:1:\"a\";i:200;s:1:\"b\";s:15:\"حذف عملة\";s:1:\"c\";s:3:\"web\";}i:59;a:3:{s:1:\"a\";i:201;s:1:\"b\";s:19:\"عرض الفروع\";s:1:\"c\";s:3:\"web\";}i:60;a:3:{s:1:\"a\";i:202;s:1:\"b\";s:17:\"إضافة فرع\";s:1:\"c\";s:3:\"web\";}i:61;a:3:{s:1:\"a\";i:203;s:1:\"b\";s:17:\"تعديل فرع\";s:1:\"c\";s:3:\"web\";}i:62;a:3:{s:1:\"a\";i:204;s:1:\"b\";s:13:\"حذف فرع\";s:1:\"c\";s:3:\"web\";}i:63;a:3:{s:1:\"a\";i:205;s:1:\"b\";s:25:\"عرض الإعدادات\";s:1:\"c\";s:3:\"web\";}i:64;a:3:{s:1:\"a\";i:206;s:1:\"b\";s:29:\"تعديل الإعدادات\";s:1:\"c\";s:3:\"web\";}i:65;a:3:{s:1:\"a\";i:207;s:1:\"b\";s:38:\"إدارة إعدادات النظام\";s:1:\"c\";s:3:\"web\";}i:66;a:3:{s:1:\"a\";i:208;s:1:\"b\";s:38:\"عرض القيود المحاسبية\";s:1:\"c\";s:3:\"web\";}i:67;a:3:{s:1:\"a\";i:209;s:1:\"b\";s:30:\"إضافة قيد محاسبي\";s:1:\"c\";s:3:\"web\";}i:68;a:3:{s:1:\"a\";i:210;s:1:\"b\";s:30:\"تعديل قيد محاسبي\";s:1:\"c\";s:3:\"web\";}i:69;a:3:{s:1:\"a\";i:211;s:1:\"b\";s:26:\"حذف قيد محاسبي\";s:1:\"c\";s:3:\"web\";}}s:5:\"roles\";a:1:{i:0;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:21:\"العناصر فقط\";s:1:\"c\";s:3:\"web\";}}}',1746920336);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `currencies`
--

DROP TABLE IF EXISTS `currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `currencies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbol` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exchange_rate` decimal(15,6) NOT NULL DEFAULT '1.000000',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `currencies`
--

LOCK TABLES `currencies` WRITE;
/*!40000 ALTER TABLE `currencies` DISABLE KEYS */;
INSERT INTO `currencies` VALUES (1,'دينار عراقي','IQD','د.ع',1.000000,1,'2025-05-02 20:38:34','2025-05-02 20:38:34'),(2,'دولار امريكي','USD','$',1500.000000,0,'2025-05-02 20:38:55','2025-05-02 20:38:55');
/*!40000 ALTER TABLE `currencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `account_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_email_unique` (`email`),
  KEY `customers_account_id_foreign` (`account_id`),
  CONSTRAINT `customers_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (1,'Mohammed hamdan','mohammed@shopini.com','07726164299','basrah\r\nbas',13,'2025-05-02 20:43:21','2025-05-02 20:43:21');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `employee_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `job_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `status` enum('active','inactive','terminated') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_employee_number_unique` (`employee_number`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (4,'Mohammed hamdan','1','التطوير','مبرمج','2025-04-01','active','IQD','2025-05-03 12:28:57','2025-05-03 12:28:57'),(5,'علي','5','التطوير','مبرمج','2025-03-01','active','IQD','2025-05-03 13:12:27','2025-05-03 13:12:27');
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_items`
--

DROP TABLE IF EXISTS `invoice_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `unit_price` decimal(15,2) NOT NULL,
  `line_total` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_items_invoice_id_foreign` (`invoice_id`),
  KEY `invoice_items_item_id_foreign` (`item_id`),
  CONSTRAINT `invoice_items_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoice_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_items`
--

LOCK TABLES `invoice_items` WRITE;
/*!40000 ALTER TABLE `invoice_items` DISABLE KEYS */;
INSERT INTO `invoice_items` VALUES (6,10,1,3,3444.00,10332.00,'2025-05-05 20:44:51','2025-05-05 20:44:51'),(7,11,1,5,3444.00,17220.00,'2025-05-07 12:34:16','2025-05-07 12:34:16'),(8,12,1,2,3444.00,6888.00,'2025-05-08 15:22:47','2025-05-08 15:22:47');
/*!40000 ALTER TABLE `invoice_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `total` decimal(15,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IQD',
  `exchange_rate` decimal(15,6) NOT NULL DEFAULT '1.000000',
  `status` enum('draft','unpaid','partial','paid','canceled') COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  KEY `invoices_created_by_foreign` (`created_by`),
  KEY `invoices_customer_id_foreign` (`customer_id`),
  CONSTRAINT `invoices_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoices_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
INSERT INTO `invoices` VALUES (10,'INV-00001',1,'2025-05-05',10332.00,'IQD',1.000000,'paid',1,'2025-05-05 20:44:51','2025-05-06 09:35:06'),(11,'INV-00011',1,'2025-05-07',17220.00,'IQD',1.000000,'paid',1,'2025-05-07 12:34:16','2025-05-09 21:07:09'),(12,'INV-00012',1,'2025-05-08',6888.00,'IQD',1.000000,'unpaid',1,'2025-05-08 15:22:47','2025-05-10 08:12:25');
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('product','service') COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` VALUES (1,'ree','product',3444.00,NULL,'2025-05-02 20:43:44','2025-05-02 20:43:44');
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `journal_entries`
--

DROP TABLE IF EXISTS `journal_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `journal_entries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_id` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IQD',
  `exchange_rate` decimal(15,6) NOT NULL DEFAULT '1.000000',
  `total_debit` decimal(18,2) NOT NULL DEFAULT '0.00',
  `total_credit` decimal(18,2) NOT NULL DEFAULT '0.00',
  `status` enum('active','canceled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `journal_entries_created_by_foreign` (`created_by`),
  CONSTRAINT `journal_entries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=130 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `journal_entries`
--

LOCK TABLES `journal_entries` WRITE;
/*!40000 ALTER TABLE `journal_entries` DISABLE KEYS */;
INSERT INTO `journal_entries` VALUES (28,'2025-05-04','قيد سند مالي #VCH-00001','App\\Models\\Voucher',11,1,'IQD',1.000000,8000000.00,8000000.00,'active','2025-05-04 20:27:06','2025-05-04 20:27:06'),(29,'2025-01-31','قيد استحقاق رواتب شهر 2025-01','App\\Models\\SalaryBatch',3,1,'IQD',1.000000,5000.00,5000.00,'active','2025-05-04 20:27:18','2025-05-04 20:27:18'),(30,'2025-05-04','دفع راتب شهر 2025-01 للموظف Mohammed hamdan','App\\Models\\Voucher',12,1,'IQD',1.000000,5000.00,5000.00,'active','2025-05-04 20:27:31','2025-05-04 20:27:31'),(32,'2025-05-04','قيد سند مالي #VCH-00013','App\\Models\\Voucher',13,1,'IQD',1.000000,200.00,200.00,'active','2025-05-04 20:34:19','2025-05-04 20:34:19'),(37,'2025-05-05','قيد تحويل بين الصناديق للسند #VCH-00014','App\\Models\\Voucher',18,1,'IQD',1.000000,0.00,0.00,'active','2025-05-04 21:39:48','2025-05-04 21:39:48'),(38,'2025-05-05','قيد تحويل بين الصناديق للسند #VCH-00019','App\\Models\\Voucher',19,1,'IQD',1.000000,0.00,0.00,'active','2025-05-05 07:38:10','2025-05-05 07:38:10'),(39,'2025-05-05','قيد تحويل بين الصناديق للسند #VCH-00020','App\\Models\\Voucher',20,1,'IQD',1.000000,0.00,0.00,'active','2025-05-05 07:38:40','2025-05-05 07:38:40'),(40,'2025-05-05','قيد سند مالي #VCH-00021','App\\Models\\Voucher',21,1,'IQD',1.000000,200.00,200.00,'active','2025-05-05 07:39:26','2025-05-05 07:39:26'),(41,'2025-05-05','قيد تحويل بين الصناديق للسند #VCH-00022','App\\Models\\Voucher',22,1,'IQD',1.000000,100.00,100.00,'active','2025-05-05 07:42:46','2025-05-05 07:42:46'),(42,'2025-05-05','قيد استحقاق فاتورة INV-00001','invoice',10,1,'IQD',1.000000,10332.00,10332.00,'active','2025-05-05 20:44:56','2025-05-05 20:44:56'),(43,'2025-05-06','قيد سند مالي #VCH-00023','App\\Models\\Voucher',23,1,'IQD',1.000000,333.00,333.00,'active','2025-05-05 21:01:36','2025-05-05 21:01:36'),(44,'2025-05-06','قيد سند مالي #VCH-00024','App\\Models\\Voucher',24,1,'IQD',1.000000,444.00,444.00,'active','2025-05-05 21:05:08','2025-05-05 21:05:08'),(45,'2025-05-06','قيد سند مالي #VCH-00025','App\\Models\\Voucher',25,1,'IQD',1.000000,666.00,666.00,'active','2025-05-05 21:22:10','2025-05-05 21:22:10'),(46,'2025-05-06','قيد سداد فاتورة INV-00001','App\\Models\\Voucher',26,1,'IQD',1.000000,10332.00,10332.00,'active','2025-05-06 09:35:06','2025-05-06 09:35:06'),(47,'2025-05-06','دفع راتب شهر 2025-01 للموظف Mohammed hamdan','App\\Models\\Voucher',27,1,'IQD',1.000000,5000.00,5000.00,'active','2025-05-06 09:55:03','2025-05-06 09:55:03'),(48,'2025-05-07','قيد استحقاق فاتورة INV-00011','invoice',11,1,'IQD',1.000000,17220.00,17220.00,'active','2025-05-07 12:34:22','2025-05-07 12:34:22'),(49,'2025-05-07','قيد تحويل بين الصناديق للسند #VCH-00028','App\\Models\\Voucher',28,1,'IQD',1.000000,50000.00,50000.00,'active','2025-05-07 12:35:09','2025-05-07 12:35:09'),(50,'2025-05-07','قيد تحويل بين الصناديق للسند #VCH-00029','App\\Models\\Voucher',29,1,'IQD',1.000000,3.00,3.00,'active','2025-05-07 12:43:11','2025-05-07 12:43:11'),(51,'2025-05-07','قيد تحويل بين الصناديق للسند #VCH-00030','App\\Models\\Voucher',30,1,'IQD',1.000000,7777.00,7777.00,'active','2025-05-07 12:48:10','2025-05-07 12:48:10'),(52,'2025-05-07','قيد تحويل بين الصناديق للسند #VCH-00031','App\\Models\\Voucher',31,1,'IQD',1.000000,5000.00,5000.00,'active','2025-05-07 12:58:07','2025-05-07 12:58:07'),(53,'2025-05-07','قيد تحويل بين الصناديق للسند #VCH-00032','App\\Models\\Voucher',32,1,'IQD',1.000000,2000.00,2000.00,'active','2025-05-07 13:06:26','2025-05-07 13:06:26'),(54,'2025-05-07','قيد تحويل بين الصناديق للسند #VCH-00033','App\\Models\\Voucher',33,1,'IQD',1.000000,3000.00,3000.00,'active','2025-05-07 14:48:01','2025-05-07 14:48:01'),(55,'2025-05-07','قيد تحويل بين الصناديق للسند #VCH-00034','App\\Models\\Voucher',34,1,'IQD',1.000000,3.85,5000.00,'active','2025-05-07 14:59:32','2025-05-07 14:59:32'),(56,'2025-05-07','قيد تحويل بين الصناديق للسند #VCH-00035','App\\Models\\Voucher',35,1,'IQD',1.000000,5555.00,5555.00,'active','2025-05-07 15:00:31','2025-05-07 15:00:31'),(57,'2025-05-07','قيد تحويل بين الصناديق للسند #VCH-00036','App\\Models\\Voucher',36,1,'IQD',1.000000,2.67,4000.00,'active','2025-05-07 15:04:23','2025-05-07 15:04:23'),(58,'2025-05-08','قيد استحقاق فاتورة INV-00012','invoice',12,1,'IQD',1.000000,6888.00,6888.00,'active','2025-05-08 15:22:51','2025-05-08 15:22:51'),(61,'2025-05-09',NULL,NULL,NULL,1,'USD',1500.000000,20.00,30000.00,'active','2025-05-09 06:05:45','2025-05-09 06:05:45'),(66,'2025-05-09','قيد عكسي لإلغاء السند #VCH-00001','App\\Models\\Voucher',11,1,'IQD',1.000000,8000000.00,8000000.00,'active','2025-05-09 06:23:24','2025-05-09 06:23:24'),(67,'2025-05-09','قيد عكسي لإلغاء القيد اليدوي #61','manual',61,1,'USD',1500.000000,20.00,30000.00,'active','2025-05-09 06:25:09','2025-05-09 06:25:09'),(68,'2025-05-09',NULL,NULL,NULL,1,'USD',1500.000000,20.00,30000.00,'active','2025-05-09 20:25:19','2025-05-09 20:25:19'),(69,'2025-05-09','قيد عكسي لإلغاء القيد اليدوي #68','manual',68,1,'USD',1500.000000,20.00,30000.00,'active','2025-05-09 20:25:31','2025-05-09 20:25:31'),(70,'2025-05-09','قيد سند مالي #VCH-00037','App\\Models\\Voucher',37,1,'USD',1500.000000,33.00,33.00,'active','2025-05-09 20:50:04','2025-05-09 20:50:04'),(71,'2025-02-28','قيد استحقاق رواتب شهر 2025-02','App\\Models\\SalaryBatch',5,1,'IQD',1.000000,14200.00,14200.00,'active','2025-05-09 20:56:50','2025-05-09 20:56:50'),(72,'2025-05-10','قيد سند مالي #VCH-00038','App\\Models\\Voucher',38,1,'IQD',1.000000,10000000.00,10000000.00,'active','2025-05-09 21:02:48','2025-05-09 21:02:48'),(73,'2025-05-10','دفع راتب شهر 2025-02 للموظف Mohammed hamdan','App\\Models\\Voucher',39,1,'IQD',1.000000,5000.00,5000.00,'active','2025-05-09 21:03:17','2025-05-09 21:03:17'),(74,'2025-05-10','دفع راتب شهر 2025-02 للموظف علي','App\\Models\\Voucher',40,1,'IQD',1.000000,9200.00,9200.00,'active','2025-05-09 21:03:33','2025-05-09 21:03:33'),(75,'2025-05-10','قيد عكسي لإلغاء السند #VCH-00027','App\\Models\\Voucher',27,1,'IQD',1.000000,5000.00,5000.00,'active','2025-05-09 21:05:02','2025-05-09 21:05:02'),(76,'2025-05-10','قيد عكسي لإلغاء السند #VCH-00039','App\\Models\\Voucher',39,1,'IQD',1.000000,5000.00,5000.00,'active','2025-05-09 21:05:56','2025-05-09 21:05:56'),(77,'2025-05-10','دفع راتب شهر 2025-01 للموظف Mohammed hamdan','App\\Models\\Voucher',41,1,'IQD',1.000000,5000.00,5000.00,'active','2025-05-09 21:06:16','2025-05-09 21:06:16'),(78,'2025-05-10','دفع راتب شهر 2025-02 للموظف Mohammed hamdan','App\\Models\\Voucher',42,1,'IQD',1.000000,5000.00,5000.00,'active','2025-05-09 21:06:25','2025-05-09 21:06:25'),(79,'2025-05-10','قيد عكسي لإلغاء السند #VCH-00042','App\\Models\\Voucher',42,1,'IQD',1.000000,5000.00,5000.00,'active','2025-05-09 21:06:37','2025-05-09 21:06:37'),(80,'2025-05-10','قيد سداد فاتورة INV-00011','App\\Models\\Voucher',43,1,'IQD',1.000000,17220.00,17220.00,'active','2025-05-09 21:07:09','2025-05-09 21:07:09'),(81,'2025-05-10','قيد سداد فاتورة INV-00012','App\\Models\\Voucher',44,1,'IQD',1.000000,2000.00,2000.00,'canceled','2025-05-09 21:07:23','2025-05-10 08:11:16'),(82,'2025-05-10','قيد سند مالي #VCH-00045','App\\Models\\Voucher',45,1,'IQD',1.000000,5000.00,5000.00,'active','2025-05-09 21:09:33','2025-05-09 21:09:33'),(83,'2025-05-10','teset',NULL,NULL,1,'USD',1500.000000,30.00,45000.00,'active','2025-05-09 21:27:54','2025-05-09 21:27:54'),(84,'2025-05-10','قيد عكسي لإلغاء القيد اليدوي #83','manual',83,1,'USD',1500.000000,30.00,45000.00,'active','2025-05-09 21:28:00','2025-05-09 21:28:00'),(85,'2025-05-10',NULL,NULL,NULL,1,'USD',1500.000000,20.00,30000.00,'active','2025-05-09 21:31:04','2025-05-09 21:31:04'),(86,'2025-05-10','قيد عكسي لإلغاء القيد اليدوي #85','manual',85,1,'USD',1500.000000,20.00,30000.00,'active','2025-05-09 21:31:11','2025-05-09 21:31:11'),(87,'2025-05-10',NULL,NULL,NULL,1,'IQD',1.000000,30000.00,30000.00,'active','2025-05-09 21:34:19','2025-05-09 21:34:19'),(88,'2025-05-10','قيد عكسي لإلغاء القيد اليدوي #87','manual',87,1,'IQD',1.000000,30000.00,30000.00,'active','2025-05-09 21:34:25','2025-05-09 21:34:25'),(89,'2025-05-10',NULL,NULL,NULL,1,'IQD',1.000000,30000.00,20.00,'active','2025-05-09 21:40:50','2025-05-09 21:40:50'),(90,'2025-05-10','قيد عكسي لإلغاء القيد اليدوي #89','manual',89,1,'IQD',1.000000,30000.00,20.00,'active','2025-05-09 21:40:55','2025-05-09 21:40:55'),(91,'2025-05-10',NULL,NULL,NULL,1,'IQD',1.000000,30000.00,20.00,'active','2025-05-09 21:42:59','2025-05-09 21:42:59'),(92,'2025-05-10','قيد عكسي لإلغاء القيد اليدوي #91','manual',91,1,'IQD',1.000000,30000.00,20.00,'active','2025-05-09 21:43:02','2025-05-09 21:43:02'),(93,'2025-05-10',NULL,NULL,NULL,1,'IQD',1.000000,30000.00,30000.00,'active','2025-05-09 21:46:54','2025-05-09 21:46:54'),(94,'2025-05-10','قيد عكسي لإلغاء القيد اليدوي #93','manual',93,1,'IQD',1.000000,30000.00,30000.00,'active','2025-05-09 21:47:28','2025-05-09 21:47:28'),(95,'2025-05-10',NULL,NULL,NULL,1,'IQD',1.000000,200.00,200.00,'active','2025-05-09 21:50:15','2025-05-09 21:50:15'),(96,'2025-05-10','قيد عكسي لإلغاء القيد اليدوي #95','manual',95,1,'IQD',1.000000,200.00,200.00,'active','2025-05-09 21:50:34','2025-05-09 21:50:34'),(97,'2025-05-10',NULL,NULL,NULL,1,'IQD',1.000000,200.00,200.00,'canceled','2025-05-09 21:53:49','2025-05-09 21:53:54'),(98,'2025-05-10','قيد عكسي لإلغاء القيد اليدوي #97','manual',97,1,'IQD',1.000000,200.00,200.00,'active','2025-05-09 21:53:54','2025-05-09 21:53:54'),(99,'2025-05-10','قيد سند مالي #VCH-00046','App\\Models\\Voucher',46,1,'IQD',1.000000,2000.00,2000.00,'active','2025-05-09 21:56:55','2025-05-09 21:56:55'),(100,'2025-05-10','قيد عكسي لإلغاء السند #VCH-00046','App\\Models\\Voucher',46,1,'IQD',1.000000,2000.00,2000.00,'active','2025-05-09 21:57:42','2025-05-09 21:57:42'),(101,'2025-05-10','قيد سند مالي #VCH-00047','App\\Models\\Voucher',47,1,'IQD',1.000000,2000.00,2000.00,'active','2025-05-09 22:04:43','2025-05-09 22:04:43'),(102,'2025-05-10','قيد عكسي لإلغاء السند #VCH-00047','App\\Models\\Voucher',47,1,'IQD',1.000000,2000.00,2000.00,'active','2025-05-09 22:05:24','2025-05-09 22:05:24'),(103,'2025-05-10','قيد عكسي لإلغاء السند #VCH-00041','App\\Models\\Voucher',41,1,'IQD',1.000000,5000.00,5000.00,'active','2025-05-09 22:07:39','2025-05-09 22:07:39'),(104,'2025-05-10','دفع راتب شهر 2025-02 للموظف Mohammed hamdan','App\\Models\\Voucher',48,1,'IQD',1.000000,5000.00,5000.00,'active','2025-05-09 22:08:40','2025-05-09 22:08:40'),(105,'2025-05-10','قيد عكسي لإلغاء السند #VCH-00048','App\\Models\\Voucher',48,1,'IQD',1.000000,5000.00,5000.00,'active','2025-05-09 22:09:10','2025-05-09 22:09:10'),(106,'2025-05-10','قيد سند مالي #VCH-00049','App\\Models\\Voucher',49,1,'IQD',1.000000,300.00,300.00,'canceled','2025-05-10 07:34:43','2025-05-10 07:36:08'),(107,'2025-05-10','قيد عكسي لإلغاء السند #VCH-00049','App\\Models\\Voucher',49,1,'IQD',1.000000,300.00,300.00,'active','2025-05-10 07:34:50','2025-05-10 07:34:50'),(108,'2025-05-10','قيد سند مالي #VCH-00050','App\\Models\\Voucher',50,1,'IQD',1.000000,3000.00,3000.00,'active','2025-05-10 07:43:05','2025-05-10 07:43:05'),(109,'2025-05-10','قيد عكسي لإلغاء السند #VCH-00050','App\\Models\\Voucher',50,1,'IQD',1.000000,3000.00,3000.00,'active','2025-05-10 07:43:14','2025-05-10 07:43:14'),(110,'2025-05-10','قيد سند مالي #VCH-00051','App\\Models\\Voucher',51,1,'IQD',1.000000,3200.00,3200.00,'active','2025-05-10 07:47:32','2025-05-10 07:47:32'),(111,'2025-05-10','قيد عكسي لإلغاء السند #VCH-00051','App\\Models\\Voucher',51,1,'IQD',1.000000,3200.00,3200.00,'active','2025-05-10 07:47:37','2025-05-10 07:47:37'),(112,'2025-05-10','قيد سند مالي #VCH-00052','App\\Models\\Voucher',52,1,'IQD',1.000000,1000.00,1000.00,'active','2025-05-10 07:50:33','2025-05-10 07:50:33'),(113,'2025-05-10','قيد عكسي لإلغاء السند #VCH-00052','App\\Models\\Voucher',52,1,'IQD',1.000000,1000.00,1000.00,'active','2025-05-10 07:50:39','2025-05-10 07:50:39'),(114,'2025-05-10','قيد سند مالي #VCH-00053','App\\Models\\Voucher',53,1,'IQD',1.000000,5433.00,5433.00,'active','2025-05-10 07:55:02','2025-05-10 07:55:02'),(115,'2025-05-10','قيد عكسي لإلغاء السند #VCH-00053','App\\Models\\Voucher',53,1,'IQD',1.000000,5433.00,5433.00,'active','2025-05-10 07:55:08','2025-05-10 07:55:08'),(116,'2025-05-10','قيد سند مالي #VCH-00054','App\\Models\\Voucher',54,1,'IQD',1.000000,3434.00,3434.00,'canceled','2025-05-10 08:06:53','2025-05-10 08:07:00'),(117,'2025-05-10','قيد عكسي لإلغاء السند #VCH-00054','App\\Models\\Voucher',54,1,'IQD',1.000000,3434.00,3434.00,'canceled','2025-05-10 08:07:00','2025-05-10 08:07:00'),(118,'2025-05-10','قيد عكسي لإلغاء السند #VCH-00044','App\\Models\\Voucher',44,1,'IQD',1.000000,2000.00,2000.00,'active','2025-05-10 08:11:16','2025-05-10 08:11:16'),(119,'2025-05-10','قيد سداد فاتورة INV-00012','App\\Models\\Voucher',55,1,'IQD',1.000000,1000.00,1000.00,'canceled','2025-05-10 08:12:17','2025-05-10 08:12:25'),(120,'2025-05-10','قيد عكسي لإلغاء السند #VCH-00055','App\\Models\\Voucher',55,1,'IQD',1.000000,1000.00,1000.00,'active','2025-05-10 08:12:25','2025-05-10 08:12:25'),(121,'2025-05-10',NULL,NULL,NULL,1,'IQD',1.000000,3000.00,3000.00,'canceled','2025-05-10 08:16:25','2025-05-10 08:16:38'),(122,'2025-05-10','قيد عكسي لإلغاء القيد #121','manual',121,1,'IQD',1.000000,3000.00,3000.00,'active','2025-05-10 08:16:38','2025-05-10 08:16:38'),(123,'2025-05-10',NULL,NULL,NULL,1,'IQD',1.000000,200.00,200.00,'canceled','2025-05-10 08:18:21','2025-05-10 08:18:25'),(124,'2025-05-10','قيد عكسي لإلغاء القيد #123','manual',123,1,'IQD',1.000000,200.00,200.00,'active','2025-05-10 08:18:25','2025-05-10 08:18:25'),(125,'2025-03-31','قيد استحقاق رواتب شهر 2025-03','App\\Models\\SalaryBatch',6,1,'IQD',1.000000,14200.00,14200.00,'active','2025-05-10 09:16:23','2025-05-10 09:16:23'),(126,'2025-05-10','قيد سند مالي #VCH-00056','App\\Models\\Voucher',56,1,'IQD',1.000000,45.00,45.00,'canceled','2025-05-10 09:18:16','2025-05-10 09:26:30'),(127,'2025-05-10','قيد سند مالي #VCH-00057','App\\Models\\Voucher',57,1,'IQD',1.000000,56.00,56.00,'active','2025-05-10 09:20:42','2025-05-10 09:20:42'),(128,'2025-05-10','قيد سند مالي #VCH-00058','App\\Models\\Voucher',58,1,'IQD',1.000000,43.00,43.00,'active','2025-05-10 09:23:42','2025-05-10 09:23:42'),(129,'2025-05-10','قيد عكسي لإلغاء السند #VCH-00056','App\\Models\\Voucher',56,1,'IQD',1.000000,45.00,45.00,'active','2025-05-10 09:26:30','2025-05-10 09:26:30');
/*!40000 ALTER TABLE `journal_entries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `journal_entry_lines`
--

DROP TABLE IF EXISTS `journal_entry_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `journal_entry_lines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `journal_entry_id` bigint unsigned NOT NULL,
  `account_id` bigint unsigned NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `debit` decimal(18,2) NOT NULL DEFAULT '0.00',
  `credit` decimal(18,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IQD',
  `exchange_rate` decimal(15,6) NOT NULL DEFAULT '1.000000',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `journal_entry_lines_journal_entry_id_foreign` (`journal_entry_id`),
  KEY `journal_entry_lines_account_id_foreign` (`account_id`),
  CONSTRAINT `journal_entry_lines_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `journal_entry_lines_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=244 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `journal_entry_lines`
--

LOCK TABLES `journal_entry_lines` WRITE;
/*!40000 ALTER TABLE `journal_entry_lines` DISABLE KEYS */;
INSERT INTO `journal_entry_lines` VALUES (39,28,11,NULL,8000000.00,0.00,'IQD',1.000000,'2025-05-04 20:27:06','2025-05-04 20:27:06'),(40,28,28,NULL,0.00,8000000.00,'IQD',1.000000,'2025-05-04 20:27:06','2025-05-04 20:27:06'),(41,29,24,'استحقاق رواتب شهر 2025-01',5000.00,0.00,'IQD',1.000000,'2025-05-04 20:27:18','2025-05-04 20:27:18'),(42,29,25,'ذمم مستحقة للموظفين عن رواتب شهر 2025-01',0.00,5000.00,'IQD',1.000000,'2025-05-04 20:27:18','2025-05-04 20:27:18'),(43,30,25,'صرف راتب للموظف Mohammed hamdan',5000.00,0.00,'IQD',1.000000,'2025-05-04 20:27:31','2025-05-04 20:27:31'),(44,30,11,'دفع راتب للموظف Mohammed hamdan',0.00,5000.00,'IQD',1.000000,'2025-05-04 20:27:31','2025-05-04 20:27:31'),(47,32,11,NULL,0.00,200.00,'IQD',1.000000,'2025-05-04 20:34:19','2025-05-04 20:34:19'),(48,32,28,NULL,200.00,0.00,'IQD',1.000000,'2025-05-04 20:34:19','2025-05-04 20:34:19'),(55,37,11,NULL,0.00,800.00,'IQD',1.000000,'2025-05-04 21:39:48','2025-05-04 21:39:48'),(56,37,38,NULL,800.00,0.00,'IQD',1.000000,'2025-05-04 21:39:48','2025-05-04 21:39:48'),(57,38,11,NULL,0.00,2000.00,'IQD',1.000000,'2025-05-05 07:38:10','2025-05-05 07:38:10'),(58,38,38,NULL,2000.00,0.00,'IQD',1.000000,'2025-05-05 07:38:10','2025-05-05 07:38:10'),(59,39,11,NULL,0.00,6000.00,'IQD',1.000000,'2025-05-05 07:38:40','2025-05-05 07:38:40'),(60,39,39,NULL,4.62,0.00,'USD',1.000000,'2025-05-05 07:38:40','2025-05-05 07:38:40'),(61,40,11,NULL,200.00,0.00,'IQD',1.000000,'2025-05-05 07:39:26','2025-05-05 07:39:26'),(62,40,28,NULL,0.00,200.00,'IQD',1.000000,'2025-05-05 07:39:26','2025-05-05 07:39:26'),(63,41,11,NULL,0.00,100.00,'IQD',1.000000,'2025-05-05 07:42:46','2025-05-05 07:42:46'),(64,41,38,NULL,100.00,0.00,'IQD',1.000000,'2025-05-05 07:42:46','2025-05-05 07:42:46'),(65,42,13,'استحقاق فاتورة INV-00001',10332.00,0.00,'IQD',1.000000,'2025-05-05 20:44:56','2025-05-05 20:44:56'),(66,42,20,'إيراد فاتورة INV-00001',0.00,10332.00,'IQD',1.000000,'2025-05-05 20:44:56','2025-05-05 20:44:56'),(67,43,11,NULL,333.00,0.00,'IQD',1.000000,'2025-05-05 21:01:36','2025-05-05 21:01:36'),(68,43,28,NULL,0.00,333.00,'IQD',1.000000,'2025-05-05 21:01:36','2025-05-05 21:01:36'),(69,44,11,NULL,444.00,0.00,'IQD',1.000000,'2025-05-05 21:05:08','2025-05-05 21:05:08'),(70,44,28,NULL,0.00,444.00,'IQD',1.000000,'2025-05-05 21:05:08','2025-05-05 21:05:08'),(71,45,11,NULL,666.00,0.00,'IQD',1.000000,'2025-05-05 21:22:10','2025-05-05 21:22:10'),(72,45,28,NULL,0.00,666.00,'IQD',1.000000,'2025-05-05 21:22:10','2025-05-05 21:22:10'),(73,46,11,'استلام نقد لفاتورة INV-00001',10332.00,0.00,'IQD',1.000000,'2025-05-06 09:35:06','2025-05-06 09:35:06'),(74,46,13,'تسوية فاتورة INV-00001',0.00,10332.00,'IQD',1.000000,'2025-05-06 09:35:06','2025-05-06 09:35:06'),(75,47,25,'صرف راتب للموظف Mohammed hamdan',5000.00,0.00,'IQD',1.000000,'2025-05-06 09:55:03','2025-05-06 09:55:03'),(76,47,11,'دفع راتب للموظف Mohammed hamdan',0.00,5000.00,'IQD',1.000000,'2025-05-06 09:55:03','2025-05-06 09:55:03'),(77,48,13,'استحقاق فاتورة INV-00011',17220.00,0.00,'IQD',1.000000,'2025-05-07 12:34:22','2025-05-07 12:34:22'),(78,48,20,'إيراد فاتورة INV-00011',0.00,17220.00,'IQD',1.000000,'2025-05-07 12:34:22','2025-05-07 12:34:22'),(79,49,11,NULL,0.00,50000.00,'IQD',1.000000,'2025-05-07 12:35:09','2025-05-07 12:35:09'),(80,49,29,NULL,50000.00,0.00,'USD',1.000000,'2025-05-07 12:35:09','2025-05-07 12:35:09'),(81,50,29,NULL,0.00,3.00,'USD',1.000000,'2025-05-07 12:43:11','2025-05-07 12:43:11'),(82,50,11,NULL,3.00,0.00,'IQD',1.000000,'2025-05-07 12:43:11','2025-05-07 12:43:11'),(83,51,11,NULL,0.00,7777.00,'IQD',1.000000,'2025-05-07 12:48:10','2025-05-07 12:48:10'),(84,51,29,NULL,7777.00,0.00,'USD',1.000000,'2025-05-07 12:48:10','2025-05-07 12:48:10'),(85,52,11,NULL,0.00,5000.00,'IQD',1.000000,'2025-05-07 12:58:07','2025-05-07 12:58:07'),(86,52,29,NULL,5000.00,0.00,'USD',1.000000,'2025-05-07 12:58:07','2025-05-07 12:58:07'),(87,53,11,NULL,0.00,2000.00,'IQD',1.000000,'2025-05-07 13:06:26','2025-05-07 13:06:26'),(88,53,29,NULL,2000.00,0.00,'USD',1.000000,'2025-05-07 13:06:26','2025-05-07 13:06:26'),(89,54,11,NULL,0.00,3000.00,'IQD',1.000000,'2025-05-07 14:48:01','2025-05-07 14:48:01'),(90,54,29,NULL,3000.00,0.00,'USD',1.000000,'2025-05-07 14:48:01','2025-05-07 14:48:01'),(91,55,11,NULL,0.00,5000.00,'IQD',1.000000,'2025-05-07 14:59:32','2025-05-07 14:59:32'),(92,55,29,NULL,3.85,0.00,'USD',1.000000,'2025-05-07 14:59:32','2025-05-07 14:59:32'),(93,56,11,NULL,0.00,5555.00,'IQD',1.000000,'2025-05-07 15:00:31','2025-05-07 15:00:31'),(94,56,38,NULL,5555.00,0.00,'IQD',1.000000,'2025-05-07 15:00:31','2025-05-07 15:00:31'),(95,57,11,NULL,0.00,4000.00,'IQD',1.000000,'2025-05-07 15:04:23','2025-05-07 15:04:23'),(96,57,29,NULL,2.67,0.00,'USD',1.000000,'2025-05-07 15:04:23','2025-05-07 15:04:23'),(97,58,13,'استحقاق فاتورة INV-00012',6888.00,0.00,'IQD',1.000000,'2025-05-08 15:22:51','2025-05-08 15:22:51'),(98,58,20,'إيراد فاتورة INV-00012',0.00,6888.00,'IQD',1.000000,'2025-05-08 15:22:51','2025-05-08 15:22:51'),(103,61,30,NULL,20.00,0.00,'USD',1500.000000,'2025-05-09 06:05:45','2025-05-09 06:05:45'),(104,61,12,NULL,0.00,30000.00,'IQD',1.000000,'2025-05-09 06:05:45','2025-05-09 06:05:45'),(113,66,11,'عكس: ',0.00,8000000.00,'IQD',1.000000,'2025-05-09 06:23:24','2025-05-09 06:23:24'),(114,66,28,'عكس: ',8000000.00,0.00,'IQD',1.000000,'2025-05-09 06:23:24','2025-05-09 06:23:24'),(115,67,30,'عكس: ',0.00,20.00,'USD',1500.000000,'2025-05-09 06:25:09','2025-05-09 06:25:09'),(116,67,12,'عكس: ',30000.00,0.00,'IQD',1.000000,'2025-05-09 06:25:09','2025-05-09 06:25:09'),(117,68,30,NULL,20.00,0.00,'USD',1500.000000,'2025-05-09 20:25:19','2025-05-09 20:25:19'),(118,68,17,NULL,0.00,30000.00,'IQD',1.000000,'2025-05-09 20:25:19','2025-05-09 20:25:19'),(119,69,30,'عكس: ',0.00,20.00,'USD',1500.000000,'2025-05-09 20:25:31','2025-05-09 20:25:31'),(120,69,17,'عكس: ',30000.00,0.00,'IQD',1.000000,'2025-05-09 20:25:31','2025-05-09 20:25:31'),(121,70,29,NULL,33.00,0.00,'USD',1500.000000,'2025-05-09 20:50:04','2025-05-09 20:50:04'),(122,70,32,NULL,0.00,33.00,'USD',1500.000000,'2025-05-09 20:50:04','2025-05-09 20:50:04'),(123,71,24,'استحقاق رواتب شهر 2025-02',14200.00,0.00,'IQD',1.000000,'2025-05-09 20:56:50','2025-05-09 20:56:50'),(124,71,25,'ذمم مستحقة للموظفين عن رواتب شهر 2025-02',0.00,14200.00,'IQD',1.000000,'2025-05-09 20:56:50','2025-05-09 20:56:50'),(125,72,11,NULL,10000000.00,0.00,'IQD',1.000000,'2025-05-09 21:02:48','2025-05-09 21:02:48'),(126,72,28,NULL,0.00,10000000.00,'IQD',1.000000,'2025-05-09 21:02:48','2025-05-09 21:02:48'),(127,73,25,'صرف راتب للموظف Mohammed hamdan',5000.00,0.00,'IQD',1.000000,'2025-05-09 21:03:17','2025-05-09 21:03:17'),(128,73,11,'دفع راتب للموظف Mohammed hamdan',0.00,5000.00,'IQD',1.000000,'2025-05-09 21:03:17','2025-05-09 21:03:17'),(129,74,25,'صرف راتب للموظف علي',9200.00,0.00,'IQD',1.000000,'2025-05-09 21:03:33','2025-05-09 21:03:33'),(130,74,11,'دفع راتب للموظف علي',0.00,9200.00,'IQD',1.000000,'2025-05-09 21:03:33','2025-05-09 21:03:33'),(131,75,25,'عكس: صرف راتب للموظف Mohammed hamdan',0.00,5000.00,'IQD',1.000000,'2025-05-09 21:05:02','2025-05-09 21:05:02'),(132,75,11,'عكس: دفع راتب للموظف Mohammed hamdan',5000.00,0.00,'IQD',1.000000,'2025-05-09 21:05:02','2025-05-09 21:05:02'),(133,76,25,'عكس: صرف راتب للموظف Mohammed hamdan',0.00,5000.00,'IQD',1.000000,'2025-05-09 21:05:56','2025-05-09 21:05:56'),(134,76,11,'عكس: دفع راتب للموظف Mohammed hamdan',5000.00,0.00,'IQD',1.000000,'2025-05-09 21:05:56','2025-05-09 21:05:56'),(135,77,25,'صرف راتب للموظف Mohammed hamdan',5000.00,0.00,'IQD',1.000000,'2025-05-09 21:06:16','2025-05-09 21:06:16'),(136,77,11,'دفع راتب للموظف Mohammed hamdan',0.00,5000.00,'IQD',1.000000,'2025-05-09 21:06:16','2025-05-09 21:06:16'),(137,78,25,'صرف راتب للموظف Mohammed hamdan',5000.00,0.00,'IQD',1.000000,'2025-05-09 21:06:25','2025-05-09 21:06:25'),(138,78,11,'دفع راتب للموظف Mohammed hamdan',0.00,5000.00,'IQD',1.000000,'2025-05-09 21:06:25','2025-05-09 21:06:25'),(139,79,25,'عكس: صرف راتب للموظف Mohammed hamdan',0.00,5000.00,'IQD',1.000000,'2025-05-09 21:06:37','2025-05-09 21:06:37'),(140,79,11,'عكس: دفع راتب للموظف Mohammed hamdan',5000.00,0.00,'IQD',1.000000,'2025-05-09 21:06:37','2025-05-09 21:06:37'),(141,80,11,'استلام نقد لفاتورة INV-00011',17220.00,0.00,'IQD',1.000000,'2025-05-09 21:07:09','2025-05-09 21:07:09'),(142,80,13,'تسوية فاتورة INV-00011',0.00,17220.00,'IQD',1.000000,'2025-05-09 21:07:09','2025-05-09 21:07:09'),(143,81,11,'استلام نقد لفاتورة INV-00012',2000.00,0.00,'IQD',1.000000,'2025-05-09 21:07:23','2025-05-09 21:07:23'),(144,81,13,'تسوية فاتورة INV-00012',0.00,2000.00,'IQD',1.000000,'2025-05-09 21:07:23','2025-05-09 21:07:23'),(145,82,11,NULL,3000.00,0.00,'IQD',1.000000,'2025-05-09 21:09:33','2025-05-09 21:09:33'),(146,82,28,NULL,0.00,3000.00,'IQD',1.000000,'2025-05-09 21:09:33','2025-05-09 21:09:33'),(147,82,11,NULL,2000.00,0.00,'IQD',1.000000,'2025-05-09 21:09:33','2025-05-09 21:09:33'),(148,82,21,NULL,0.00,2000.00,'IQD',1.000000,'2025-05-09 21:09:33','2025-05-09 21:09:33'),(149,83,31,NULL,30.00,0.00,'USD',1500.000000,'2025-05-09 21:27:54','2025-05-09 21:27:54'),(150,83,12,NULL,0.00,45000.00,'IQD',1.000000,'2025-05-09 21:27:54','2025-05-09 21:27:54'),(151,84,31,'عكس: ',0.00,30.00,'USD',1500.000000,'2025-05-09 21:28:00','2025-05-09 21:28:00'),(152,84,12,'عكس: ',45000.00,0.00,'IQD',1.000000,'2025-05-09 21:28:00','2025-05-09 21:28:00'),(153,85,30,NULL,20.00,0.00,'USD',1500.000000,'2025-05-09 21:31:04','2025-05-09 21:31:04'),(154,85,12,NULL,0.00,30000.00,'IQD',1.000000,'2025-05-09 21:31:04','2025-05-09 21:31:04'),(155,86,30,'عكس: ',0.00,20.00,'USD',1500.000000,'2025-05-09 21:31:11','2025-05-09 21:31:11'),(156,86,12,'عكس: ',30000.00,0.00,'IQD',1.000000,'2025-05-09 21:31:11','2025-05-09 21:31:11'),(157,87,11,NULL,30000.00,0.00,'IQD',1.000000,'2025-05-09 21:34:19','2025-05-09 21:34:19'),(158,87,12,NULL,0.00,30000.00,'IQD',1.000000,'2025-05-09 21:34:19','2025-05-09 21:34:19'),(159,88,11,'عكس: ',0.00,30000.00,'IQD',1.000000,'2025-05-09 21:34:25','2025-05-09 21:34:25'),(160,88,12,'عكس: ',30000.00,0.00,'IQD',1.000000,'2025-05-09 21:34:25','2025-05-09 21:34:25'),(161,89,12,NULL,30000.00,0.00,'IQD',1.000000,'2025-05-09 21:40:50','2025-05-09 21:40:50'),(162,89,32,NULL,0.00,20.00,'USD',1500.000000,'2025-05-09 21:40:50','2025-05-09 21:40:50'),(163,90,12,'عكس: ',0.00,30000.00,'IQD',1.000000,'2025-05-09 21:40:55','2025-05-09 21:40:55'),(164,90,32,'عكس: ',20.00,0.00,'USD',1500.000000,'2025-05-09 21:40:55','2025-05-09 21:40:55'),(165,91,11,NULL,30000.00,0.00,'IQD',1.000000,'2025-05-09 21:42:59','2025-05-09 21:42:59'),(166,91,31,NULL,0.00,20.00,'USD',1500.000000,'2025-05-09 21:42:59','2025-05-09 21:42:59'),(167,92,11,'عكس: ',0.00,30000.00,'IQD',1.000000,'2025-05-09 21:43:02','2025-05-09 21:43:02'),(168,92,31,'عكس: ',20.00,0.00,'USD',1500.000000,'2025-05-09 21:43:02','2025-05-09 21:43:02'),(169,93,11,NULL,30000.00,0.00,'IQD',1.000000,'2025-05-09 21:46:54','2025-05-09 21:46:54'),(170,93,12,NULL,0.00,30000.00,'IQD',1.000000,'2025-05-09 21:46:54','2025-05-09 21:46:54'),(171,94,11,'عكس: ',0.00,30000.00,'IQD',1.000000,'2025-05-09 21:47:28','2025-05-09 21:47:28'),(172,94,12,'عكس: ',30000.00,0.00,'IQD',1.000000,'2025-05-09 21:47:28','2025-05-09 21:47:28'),(173,95,11,NULL,200.00,0.00,'IQD',1.000000,'2025-05-09 21:50:15','2025-05-09 21:50:15'),(174,95,12,NULL,0.00,200.00,'IQD',1.000000,'2025-05-09 21:50:15','2025-05-09 21:50:15'),(175,96,11,'عكس: ',0.00,200.00,'IQD',1.000000,'2025-05-09 21:50:34','2025-05-09 21:50:34'),(176,96,12,'عكس: ',200.00,0.00,'IQD',1.000000,'2025-05-09 21:50:34','2025-05-09 21:50:34'),(177,97,11,NULL,200.00,0.00,'IQD',1.000000,'2025-05-09 21:53:49','2025-05-09 21:53:49'),(178,97,12,NULL,0.00,200.00,'IQD',1.000000,'2025-05-09 21:53:49','2025-05-09 21:53:49'),(179,98,11,'عكس: ',0.00,200.00,'IQD',1.000000,'2025-05-09 21:53:54','2025-05-09 21:53:54'),(180,98,12,'عكس: ',200.00,0.00,'IQD',1.000000,'2025-05-09 21:53:54','2025-05-09 21:53:54'),(181,99,11,NULL,0.00,2000.00,'IQD',1.000000,'2025-05-09 21:56:55','2025-05-09 21:56:55'),(182,99,28,NULL,2000.00,0.00,'IQD',1.000000,'2025-05-09 21:56:55','2025-05-09 21:56:55'),(183,100,11,'عكس: ',2000.00,0.00,'IQD',1.000000,'2025-05-09 21:57:42','2025-05-09 21:57:42'),(184,100,28,'عكس: ',0.00,2000.00,'IQD',1.000000,'2025-05-09 21:57:42','2025-05-09 21:57:42'),(185,101,38,NULL,2000.00,0.00,'IQD',1.000000,'2025-05-09 22:04:43','2025-05-09 22:04:43'),(186,101,12,NULL,0.00,2000.00,'IQD',1.000000,'2025-05-09 22:04:43','2025-05-09 22:04:43'),(187,102,38,'عكس: ',0.00,2000.00,'IQD',1.000000,'2025-05-09 22:05:24','2025-05-09 22:05:24'),(188,102,12,'عكس: ',2000.00,0.00,'IQD',1.000000,'2025-05-09 22:05:24','2025-05-09 22:05:24'),(189,103,25,'عكس: صرف راتب للموظف Mohammed hamdan',0.00,5000.00,'IQD',1.000000,'2025-05-09 22:07:39','2025-05-09 22:07:39'),(190,103,11,'عكس: دفع راتب للموظف Mohammed hamdan',5000.00,0.00,'IQD',1.000000,'2025-05-09 22:07:39','2025-05-09 22:07:39'),(191,104,25,'صرف راتب للموظف Mohammed hamdan',5000.00,0.00,'IQD',1.000000,'2025-05-09 22:08:40','2025-05-09 22:08:40'),(192,104,11,'دفع راتب للموظف Mohammed hamdan',0.00,5000.00,'IQD',1.000000,'2025-05-09 22:08:40','2025-05-09 22:08:40'),(193,105,25,'عكس: صرف راتب للموظف Mohammed hamdan',0.00,5000.00,'IQD',1.000000,'2025-05-09 22:09:10','2025-05-09 22:09:10'),(194,105,11,'عكس: دفع راتب للموظف Mohammed hamdan',5000.00,0.00,'IQD',1.000000,'2025-05-09 22:09:10','2025-05-09 22:09:10'),(195,106,11,NULL,300.00,0.00,'IQD',1.000000,'2025-05-10 07:34:43','2025-05-10 07:34:43'),(196,106,28,NULL,0.00,300.00,'IQD',1.000000,'2025-05-10 07:34:43','2025-05-10 07:34:43'),(197,107,11,'عكس: ',0.00,300.00,'IQD',1.000000,'2025-05-10 07:34:50','2025-05-10 07:34:50'),(198,107,28,'عكس: ',300.00,0.00,'IQD',1.000000,'2025-05-10 07:34:50','2025-05-10 07:34:50'),(199,108,11,NULL,3000.00,0.00,'IQD',1.000000,'2025-05-10 07:43:05','2025-05-10 07:43:05'),(200,108,26,NULL,0.00,3000.00,'IQD',1.000000,'2025-05-10 07:43:05','2025-05-10 07:43:05'),(201,109,11,'عكس: ',0.00,3000.00,'IQD',1.000000,'2025-05-10 07:43:14','2025-05-10 07:43:14'),(202,109,26,'عكس: ',3000.00,0.00,'IQD',1.000000,'2025-05-10 07:43:14','2025-05-10 07:43:14'),(203,110,11,NULL,3200.00,0.00,'IQD',1.000000,'2025-05-10 07:47:32','2025-05-10 07:47:32'),(204,110,28,NULL,0.00,3200.00,'IQD',1.000000,'2025-05-10 07:47:32','2025-05-10 07:47:32'),(205,111,11,'عكس: ',0.00,3200.00,'IQD',1.000000,'2025-05-10 07:47:37','2025-05-10 07:47:37'),(206,111,28,'عكس: ',3200.00,0.00,'IQD',1.000000,'2025-05-10 07:47:37','2025-05-10 07:47:37'),(207,112,11,NULL,1000.00,0.00,'IQD',1.000000,'2025-05-10 07:50:33','2025-05-10 07:50:33'),(208,112,28,NULL,0.00,1000.00,'IQD',1.000000,'2025-05-10 07:50:33','2025-05-10 07:50:33'),(209,113,11,'عكس: ',0.00,1000.00,'IQD',1.000000,'2025-05-10 07:50:39','2025-05-10 07:50:39'),(210,113,28,'عكس: ',1000.00,0.00,'IQD',1.000000,'2025-05-10 07:50:39','2025-05-10 07:50:39'),(211,114,11,NULL,5433.00,0.00,'IQD',1.000000,'2025-05-10 07:55:02','2025-05-10 07:55:02'),(212,114,28,NULL,0.00,5433.00,'IQD',1.000000,'2025-05-10 07:55:02','2025-05-10 07:55:02'),(213,115,11,'عكس: ',0.00,5433.00,'IQD',1.000000,'2025-05-10 07:55:08','2025-05-10 07:55:08'),(214,115,28,'عكس: ',5433.00,0.00,'IQD',1.000000,'2025-05-10 07:55:08','2025-05-10 07:55:08'),(215,116,11,NULL,3434.00,0.00,'IQD',1.000000,'2025-05-10 08:06:53','2025-05-10 08:06:53'),(216,116,28,NULL,0.00,3434.00,'IQD',1.000000,'2025-05-10 08:06:53','2025-05-10 08:06:53'),(217,117,11,'عكس: ',0.00,3434.00,'IQD',1.000000,'2025-05-10 08:07:00','2025-05-10 08:07:00'),(218,117,28,'عكس: ',3434.00,0.00,'IQD',1.000000,'2025-05-10 08:07:00','2025-05-10 08:07:00'),(219,118,11,'عكس: استلام نقد لفاتورة INV-00012',0.00,2000.00,'IQD',1.000000,'2025-05-10 08:11:16','2025-05-10 08:11:16'),(220,118,13,'عكس: تسوية فاتورة INV-00012',2000.00,0.00,'IQD',1.000000,'2025-05-10 08:11:16','2025-05-10 08:11:16'),(221,119,11,'استلام نقد لفاتورة INV-00012',1000.00,0.00,'IQD',1.000000,'2025-05-10 08:12:17','2025-05-10 08:12:17'),(222,119,13,'تسوية فاتورة INV-00012',0.00,1000.00,'IQD',1.000000,'2025-05-10 08:12:17','2025-05-10 08:12:17'),(223,120,11,'عكس: استلام نقد لفاتورة INV-00012',0.00,1000.00,'IQD',1.000000,'2025-05-10 08:12:25','2025-05-10 08:12:25'),(224,120,13,'عكس: تسوية فاتورة INV-00012',1000.00,0.00,'IQD',1.000000,'2025-05-10 08:12:25','2025-05-10 08:12:25'),(225,121,11,NULL,3000.00,0.00,'IQD',1.000000,'2025-05-10 08:16:25','2025-05-10 08:16:25'),(226,121,13,NULL,0.00,3000.00,'IQD',1.000000,'2025-05-10 08:16:25','2025-05-10 08:16:25'),(227,122,11,'عكس: ',0.00,3000.00,'IQD',1.000000,'2025-05-10 08:16:38','2025-05-10 08:16:38'),(228,122,13,'عكس: ',3000.00,0.00,'IQD',1.000000,'2025-05-10 08:16:38','2025-05-10 08:16:38'),(229,123,11,NULL,200.00,0.00,'IQD',1.000000,'2025-05-10 08:18:21','2025-05-10 08:18:21'),(230,123,12,NULL,0.00,200.00,'IQD',1.000000,'2025-05-10 08:18:21','2025-05-10 08:18:21'),(231,124,11,'عكس: ',0.00,200.00,'IQD',1.000000,'2025-05-10 08:18:25','2025-05-10 08:18:25'),(232,124,12,'عكس: ',200.00,0.00,'IQD',1.000000,'2025-05-10 08:18:25','2025-05-10 08:18:25'),(233,125,24,'استحقاق رواتب شهر 2025-03',14200.00,0.00,'IQD',1.000000,'2025-05-10 09:16:23','2025-05-10 09:16:23'),(234,125,25,'ذمم مستحقة للموظفين عن رواتب شهر 2025-03',0.00,13900.00,'IQD',1.000000,'2025-05-10 09:16:23','2025-05-10 09:16:23'),(235,125,26,'خصومات رواتب شهر 2025-03',0.00,300.00,'IQD',1.000000,'2025-05-10 09:16:23','2025-05-10 09:16:23'),(236,126,11,NULL,45.00,0.00,'IQD',1.000000,'2025-05-10 09:18:16','2025-05-10 09:18:16'),(237,126,28,NULL,0.00,45.00,'IQD',1.000000,'2025-05-10 09:18:16','2025-05-10 09:18:16'),(238,127,11,NULL,56.00,0.00,'IQD',1.000000,'2025-05-10 09:20:42','2025-05-10 09:20:42'),(239,127,28,NULL,0.00,56.00,'IQD',1.000000,'2025-05-10 09:20:42','2025-05-10 09:20:42'),(240,128,11,NULL,43.00,0.00,'IQD',1.000000,'2025-05-10 09:23:42','2025-05-10 09:23:42'),(241,128,25,NULL,0.00,43.00,'IQD',1.000000,'2025-05-10 09:23:42','2025-05-10 09:23:42'),(242,129,11,'عكس: ',0.00,45.00,'IQD',1.000000,'2025-05-10 09:26:30','2025-05-10 09:26:30'),(243,129,28,'عكس: ',45.00,0.00,'IQD',1.000000,'2025-05-10 09:26:30','2025-05-10 09:26:30');
/*!40000 ALTER TABLE `journal_entry_lines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2025_04_28_000001_create_users_table',1),(2,'2025_04_28_000002_create_branches_table',1),(3,'2025_04_28_000003_create_accounts_table',1),(4,'2025_04_28_000004_create_transactions_table',1),(5,'2025_04_28_000006_add_code_to_accounts_table',1),(6,'2025_04_28_143436_create_sessions_table',1),(7,'2025_04_28_143615_create_cache_table',1),(8,'2025_04_28_203550_create_vouchers_table',1),(9,'2025_04_28_203603_add_voucher_id_to_transactions_table',1),(10,'2025_04_28_220859_create_currencies_table',1),(11,'2025_04_28_223223_modify_type_column_in_transactions_table',1),(12,'2025_04_28_231449_add_is_cash_box_to_accounts_table',1),(13,'2025_05_01_110212_add_is_default_to_currencies_table',1),(14,'2025_05_01_110213_create_account_balances_table',1),(15,'2025_05_01_154726_add_currency_and_exchange_rate_to_vouchers_table',1),(16,'2025_05_01_232733_create_invoices_table',1),(17,'2025_05_02_000001_add_code_and_cashbox_to_accounts_table',1),(18,'2025_05_02_000138_create_items_table',1),(19,'2025_05_02_000139_create_customers_table',1),(20,'2025_05_02_000141_create_invoice_items_table',1),(21,'2025_05_02_001528_add_customer_id_to_invoices_table',1),(22,'2025_05_02_201011_create_salary_batches_table',1),(23,'2025_05_02_215900_create_accounting_settings_table',1),(24,'2025_05_02_223317_update_accounting_settings_for_currency_defaults',1),(25,'2025_05_03_000001_add_currency_to_accounts_table',1),(26,'2025_05_10_000000_add_invoice_id_to_vouchers_table',1),(27,'2025_05_10_000001_add_invoice_id_to_transactions_table',1),(28,'2025_05_10_100000_create_journal_entries_table',1),(29,'2025_05_10_100001_create_journal_entry_lines_table',1),(30,'2025_05_11_000001_create_employees_table',1),(31,'2025_05_11_000002_create_salaries_table',1),(32,'2025_05_11_000003_create_salary_payments_table',1),(33,'2025_05_03_231438_create_permission_tables',2),(34,'2025_05_04_123040_add_status_to_vouchers_table',3),(35,'2025_05_04_125328_change_date_column_type_in_vouchers_table',4),(36,'2025_05_04_131357_add_status_to_journal_entries_table',5),(37,'2025_05_04_133316_add_draft_status_to_invoices_table',6),(38,'2025_05_04_135401_add_canceled_status_to_invoices_table',7),(39,'2025_05_04_232821_make_payment_date_nullable_in_salary_payments_table',8),(40,'2025_05_05_003549_add_journal_entry_id_to_vouchers_table',9),(41,'2025_05_06_020000_create_settings_table',10);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (2,'App\\Models\\User',3);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=212 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (142,'عرض المستخدمين','web','2025-05-03 20:58:02','2025-05-03 20:58:02'),(143,'إضافة مستخدم','web','2025-05-03 20:58:02','2025-05-03 20:58:02'),(144,'تعديل مستخدم','web','2025-05-03 20:58:02','2025-05-03 20:58:02'),(145,'حذف مستخدم','web','2025-05-03 20:58:02','2025-05-03 20:58:02'),(146,'عرض الأدوار','web','2025-05-03 20:58:02','2025-05-03 20:58:02'),(147,'إضافة دور','web','2025-05-03 20:58:02','2025-05-03 20:58:02'),(148,'تعديل دور','web','2025-05-03 20:58:02','2025-05-03 20:58:02'),(149,'حذف دور','web','2025-05-03 20:58:02','2025-05-03 20:58:02'),(150,'عرض الصلاحيات','web','2025-05-03 20:58:02','2025-05-03 20:58:02'),(151,'إضافة صلاحية','web','2025-05-03 20:58:02','2025-05-03 20:58:02'),(152,'تعديل صلاحية','web','2025-05-03 20:58:02','2025-05-03 20:58:02'),(153,'حذف صلاحية','web','2025-05-03 20:58:02','2025-05-03 20:58:02'),(154,'عرض الحسابات','web','2025-05-03 20:58:02','2025-05-03 20:58:02'),(155,'إضافة حساب','web','2025-05-03 20:58:02','2025-05-03 20:58:02'),(156,'تعديل حساب','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(157,'حذف حساب','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(158,'عرض الفواتير','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(159,'إضافة فاتورة','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(160,'تعديل فاتورة','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(161,'حذف فاتورة','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(162,'تسديد فاتورة','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(163,'طباعة فاتورة','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(164,'عرض السندات','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(165,'إضافة سند','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(166,'تعديل سند','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(167,'حذف سند','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(168,'طباعة سند','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(169,'عرض الحركات المالية','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(170,'إضافة حركة مالية','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(171,'تعديل حركة مالية','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(172,'حذف حركة مالية','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(173,'عرض العملاء','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(174,'إضافة عميل','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(175,'تعديل عميل','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(176,'حذف عميل','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(177,'عرض العناصر','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(178,'إضافة عنصر','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(179,'تعديل عنصر','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(180,'حذف عنصر','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(181,'عرض الموظفين','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(182,'إضافة موظف','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(183,'تعديل موظف','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(184,'حذف موظف','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(185,'عرض الرواتب','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(186,'إضافة راتب','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(187,'تعديل راتب','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(188,'حذف راتب','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(189,'عرض دفعات الرواتب','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(190,'إضافة دفعة راتب','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(191,'تعديل دفعة راتب','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(192,'حذف دفعة راتب','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(193,'عرض كشوف الرواتب','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(194,'إضافة كشف رواتب','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(195,'تعديل كشف رواتب','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(196,'حذف كشف رواتب','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(197,'عرض العملات','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(198,'إضافة عملة','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(199,'تعديل عملة','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(200,'حذف عملة','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(201,'عرض الفروع','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(202,'إضافة فرع','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(203,'تعديل فرع','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(204,'حذف فرع','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(205,'عرض الإعدادات','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(206,'تعديل الإعدادات','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(207,'إدارة إعدادات النظام','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(208,'عرض القيود المحاسبية','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(209,'إضافة قيد محاسبي','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(210,'تعديل قيد محاسبي','web','2025-05-03 20:58:03','2025-05-03 20:58:03'),(211,'حذف قيد محاسبي','web','2025-05-03 20:58:03','2025-05-03 20:58:03');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (177,2),(178,2),(179,2);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'تجريبي','web','2025-05-03 20:46:40','2025-05-03 20:46:40'),(2,'العناصر فقط','web','2025-05-03 21:00:25','2025-05-03 21:00:25');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `salaries`
--

DROP TABLE IF EXISTS `salaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `salaries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint unsigned NOT NULL,
  `basic_salary` decimal(18,2) NOT NULL,
  `allowances` json DEFAULT NULL,
  `deductions` json DEFAULT NULL,
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `salaries_employee_id_foreign` (`employee_id`),
  CONSTRAINT `salaries_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salaries`
--

LOCK TABLES `salaries` WRITE;
/*!40000 ALTER TABLE `salaries` DISABLE KEYS */;
INSERT INTO `salaries` VALUES (1,4,5000.00,'[]','[]','2025-01-01',NULL,'2025-05-03 17:56:15','2025-05-03 17:56:15'),(2,5,9000.00,'[{\"name\": \"عمولات\", \"amount\": \"200\"}]','[]','2025-02-01',NULL,'2025-05-03 17:56:58','2025-05-03 17:56:58');
/*!40000 ALTER TABLE `salaries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `salary_batches`
--

DROP TABLE IF EXISTS `salary_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `salary_batches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `month` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','approved','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_by` bigint unsigned DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `salary_batches_created_by_foreign` (`created_by`),
  KEY `salary_batches_approved_by_foreign` (`approved_by`),
  CONSTRAINT `salary_batches_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `salary_batches_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salary_batches`
--

LOCK TABLES `salary_batches` WRITE;
/*!40000 ALTER TABLE `salary_batches` DISABLE KEYS */;
INSERT INTO `salary_batches` VALUES (3,'2025-01','approved',1,1,'2025-05-04 20:27:18','2025-05-04 20:27:15','2025-05-04 20:27:18'),(5,'2025-02','approved',1,1,'2025-05-09 20:56:50','2025-05-09 20:55:54','2025-05-09 20:56:50'),(6,'2025-03','approved',1,1,'2025-05-10 09:16:23','2025-05-09 21:00:28','2025-05-10 09:16:23');
/*!40000 ALTER TABLE `salary_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `salary_payments`
--

DROP TABLE IF EXISTS `salary_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `salary_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `salary_batch_id` bigint unsigned DEFAULT NULL,
  `employee_id` bigint unsigned NOT NULL,
  `salary_month` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gross_salary` decimal(18,2) NOT NULL,
  `total_allowances` decimal(18,2) NOT NULL DEFAULT '0.00',
  `total_deductions` decimal(18,2) NOT NULL DEFAULT '0.00',
  `net_salary` decimal(18,2) NOT NULL,
  `payment_date` date DEFAULT NULL,
  `status` enum('pending','paid','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `journal_entry_id` bigint unsigned DEFAULT NULL,
  `voucher_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `salary_payments_salary_batch_id_foreign` (`salary_batch_id`),
  KEY `salary_payments_employee_id_foreign` (`employee_id`),
  KEY `salary_payments_journal_entry_id_foreign` (`journal_entry_id`),
  KEY `salary_payments_voucher_id_foreign` (`voucher_id`),
  CONSTRAINT `salary_payments_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `salary_payments_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `salary_payments_salary_batch_id_foreign` FOREIGN KEY (`salary_batch_id`) REFERENCES `salary_batches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `salary_payments_voucher_id_foreign` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salary_payments`
--

LOCK TABLES `salary_payments` WRITE;
/*!40000 ALTER TABLE `salary_payments` DISABLE KEYS */;
INSERT INTO `salary_payments` VALUES (4,3,4,'2025-01',5000.00,0.00,0.00,5000.00,NULL,'pending',NULL,NULL,'2025-05-04 20:27:15','2025-05-09 22:07:39'),(7,5,4,'2025-02',5000.00,0.00,0.00,5000.00,NULL,'pending',NULL,NULL,'2025-05-09 20:55:54','2025-05-09 22:09:10'),(8,5,5,'2025-02',9000.00,200.00,0.00,9200.00,'2025-05-10','paid',74,40,'2025-05-09 20:55:54','2025-05-09 21:03:33'),(9,6,4,'2025-03',5000.00,0.00,300.00,4700.00,'2025-03-31','pending',NULL,NULL,'2025-05-09 21:00:28','2025-05-09 21:00:35'),(10,6,5,'2025-03',9000.00,200.00,0.00,9200.00,'2025-03-31','pending',NULL,NULL,'2025-05-09 21:00:28','2025-05-09 21:00:28');
/*!40000 ALTER TABLE `salary_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('5hu8UImDLENSqLcWidIDfnk6fKkCoyeUpHoDYbKF',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','YTo2OntzOjY6Il90b2tlbiI7czo0MDoidXN6ZWxqcUpNRGRqdDVCQlhyZ2FveGZqeEx1aXNTTExGazVhUFMyeSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjMzOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvdm91Y2hlcnMvMzYiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6NDoiYXV0aCI7YToxOntzOjIxOiJwYXNzd29yZF9jb25maXJtZWRfYXQiO2k6MTc0Njg3MjUyMzt9fQ==',1746880528);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'system_name','نظام الحسابات','2025-05-05 20:39:10','2025-05-05 20:39:10'),(2,'company_name','شركة التطوير','2025-05-05 20:39:10','2025-05-05 20:45:52'),(3,'company_logo','logos/yAylEWJgwInpIxPxIOb5ON5QpqVycHQtyvTKtcEz.jpg','2025-05-05 20:39:10','2025-05-05 20:39:10'),(4,'default_language','ar','2025-05-07 21:58:47','2025-05-09 20:50:52');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `voucher_id` bigint unsigned DEFAULT NULL,
  `invoice_id` bigint unsigned DEFAULT NULL,
  `date` date NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IQD',
  `exchange_rate` decimal(15,6) NOT NULL DEFAULT '1.000000',
  `account_id` bigint unsigned NOT NULL,
  `target_account_id` bigint unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transactions_account_id_foreign` (`account_id`),
  KEY `transactions_target_account_id_foreign` (`target_account_id`),
  KEY `transactions_user_id_foreign` (`user_id`),
  KEY `transactions_voucher_id_foreign` (`voucher_id`),
  KEY `transactions_invoice_id_foreign` (`invoice_id`),
  CONSTRAINT `transactions_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transactions_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transactions_target_account_id_foreign` FOREIGN KEY (`target_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transactions_voucher_id_foreign` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
INSERT INTO `transactions` VALUES (4,18,NULL,'2025-05-05','transfer',-800.00,'IQD',1.000000,11,NULL,'تحويل من الصندوق (سند تحويل #VCH-00014)',1,NULL,'2025-05-04 21:39:48','2025-05-04 21:39:48'),(5,18,NULL,'2025-05-05','transfer',800.00,'IQD',1.000000,38,NULL,'تحويل إلى الصندوق (سند تحويل #VCH-00014)',1,NULL,'2025-05-04 21:39:48','2025-05-04 21:39:48'),(6,19,NULL,'2025-05-05','transfer',-2000.00,'IQD',1.000000,11,NULL,'تحويل من الصندوق (سند تحويل #VCH-00019)',1,NULL,'2025-05-05 07:38:10','2025-05-05 07:38:10'),(7,19,NULL,'2025-05-05','transfer',2000.00,'IQD',1.000000,38,NULL,'تحويل إلى الصندوق (سند تحويل #VCH-00019)',1,NULL,'2025-05-05 07:38:10','2025-05-05 07:38:10'),(8,20,NULL,'2025-05-05','transfer',-6000.00,'IQD',1.000000,11,NULL,'تحويل من الصندوق (سند تحويل #VCH-00020)',1,NULL,'2025-05-05 07:38:40','2025-05-05 07:38:40'),(9,20,NULL,'2025-05-05','transfer',4.62,'USD',1.000000,39,NULL,'تحويل إلى الصندوق (سند تحويل #VCH-00020)',1,NULL,'2025-05-05 07:38:40','2025-05-05 07:38:40'),(10,22,NULL,'2025-05-05','transfer',-100.00,'IQD',1.000000,11,NULL,'تحويل من الصندوق (سند تحويل #VCH-00022)',1,NULL,'2025-05-05 07:42:46','2025-05-05 07:42:46'),(11,22,NULL,'2025-05-05','transfer',100.00,'IQD',1.000000,38,NULL,'تحويل إلى الصندوق (سند تحويل #VCH-00022)',1,NULL,'2025-05-05 07:42:46','2025-05-05 07:42:46'),(12,25,NULL,'2025-05-06','receipt',666.00,'IQD',1.000000,11,28,NULL,1,NULL,'2025-05-05 21:22:10','2025-05-05 21:22:10'),(13,26,10,'2025-05-06','receipt',10332.00,'IQD',1.000000,11,NULL,'سداد فاتورة INV-00001',1,NULL,'2025-05-06 09:35:06','2025-05-06 09:35:06'),(14,28,NULL,'2025-05-07','transfer',-50000.00,'IQD',1.000000,11,NULL,'تحويل من الصندوق (سند تحويل #VCH-00028)',1,NULL,'2025-05-07 12:35:09','2025-05-07 12:35:09'),(15,28,NULL,'2025-05-07','transfer',50000.00,'USD',1.000000,29,NULL,'تحويل إلى الصندوق (سند تحويل #VCH-00028)',1,NULL,'2025-05-07 12:35:09','2025-05-07 12:35:09'),(16,29,NULL,'2025-05-07','transfer',-3.00,'USD',1.000000,29,NULL,'تحويل من الصندوق (سند تحويل #VCH-00029)',1,NULL,'2025-05-07 12:43:11','2025-05-07 12:43:11'),(17,29,NULL,'2025-05-07','transfer',3.00,'IQD',1.000000,11,NULL,'تحويل إلى الصندوق (سند تحويل #VCH-00029)',1,NULL,'2025-05-07 12:43:11','2025-05-07 12:43:11'),(18,30,NULL,'2025-05-07','transfer',-7777.00,'IQD',1.000000,11,NULL,'تحويل من الصندوق (سند تحويل #VCH-00030)',1,NULL,'2025-05-07 12:48:10','2025-05-07 12:48:10'),(19,30,NULL,'2025-05-07','transfer',7777.00,'USD',1.000000,29,NULL,'تحويل إلى الصندوق (سند تحويل #VCH-00030)',1,NULL,'2025-05-07 12:48:10','2025-05-07 12:48:10'),(20,31,NULL,'2025-05-07','transfer',-5000.00,'IQD',1.000000,11,NULL,'تحويل من الصندوق (سند تحويل #VCH-00031)',1,NULL,'2025-05-07 12:58:07','2025-05-07 12:58:07'),(21,31,NULL,'2025-05-07','transfer',5000.00,'USD',1.000000,29,NULL,'تحويل إلى الصندوق (سند تحويل #VCH-00031)',1,NULL,'2025-05-07 12:58:07','2025-05-07 12:58:07'),(22,32,NULL,'2025-05-07','transfer',-2000.00,'IQD',1.000000,11,NULL,'تحويل من الصندوق (سند تحويل #VCH-00032)',1,NULL,'2025-05-07 13:06:26','2025-05-07 13:06:26'),(23,32,NULL,'2025-05-07','transfer',2000.00,'USD',1.000000,29,NULL,'تحويل إلى الصندوق (سند تحويل #VCH-00032)',1,NULL,'2025-05-07 13:06:26','2025-05-07 13:06:26'),(24,33,NULL,'2025-05-07','transfer',-3000.00,'IQD',1.000000,11,NULL,'تحويل من الصندوق (سند تحويل #VCH-00033)',1,NULL,'2025-05-07 14:48:01','2025-05-07 14:48:01'),(25,33,NULL,'2025-05-07','transfer',3000.00,'USD',1.000000,29,NULL,'تحويل إلى الصندوق (سند تحويل #VCH-00033)',1,NULL,'2025-05-07 14:48:01','2025-05-07 14:48:01'),(26,34,NULL,'2025-05-07','transfer',-5000.00,'IQD',1.000000,11,NULL,'تحويل من الصندوق (سند تحويل #VCH-00034)',1,NULL,'2025-05-07 14:59:32','2025-05-07 14:59:32'),(27,34,NULL,'2025-05-07','transfer',3.85,'USD',1.000000,29,NULL,'تحويل إلى الصندوق (سند تحويل #VCH-00034)',1,NULL,'2025-05-07 14:59:32','2025-05-07 14:59:32'),(28,35,NULL,'2025-05-07','transfer',-5555.00,'IQD',1.000000,11,NULL,'تحويل من الصندوق (سند تحويل #VCH-00035)',1,NULL,'2025-05-07 15:00:31','2025-05-07 15:00:31'),(29,35,NULL,'2025-05-07','transfer',5555.00,'IQD',1.000000,38,NULL,'تحويل إلى الصندوق (سند تحويل #VCH-00035)',1,NULL,'2025-05-07 15:00:31','2025-05-07 15:00:31'),(30,36,NULL,'2025-05-07','transfer',-4000.00,'IQD',1.000000,11,NULL,'تحويل من الصندوق (سند تحويل #VCH-00036)',1,NULL,'2025-05-07 15:04:23','2025-05-07 15:04:23'),(31,36,NULL,'2025-05-07','transfer',2.67,'USD',1.000000,29,NULL,'تحويل إلى الصندوق (سند تحويل #VCH-00036)',1,NULL,'2025-05-07 15:04:23','2025-05-07 15:04:23'),(32,37,NULL,'2025-05-09','receipt',33.00,'USD',1500.000000,29,32,NULL,1,NULL,'2025-05-09 20:50:04','2025-05-09 20:50:04'),(33,38,NULL,'2025-05-10','receipt',10000000.00,'IQD',1.000000,11,28,NULL,1,NULL,'2025-05-09 21:02:48','2025-05-09 21:02:48'),(34,43,11,'2025-05-10','receipt',17220.00,'IQD',1.000000,11,NULL,'سداد فاتورة INV-00011',1,NULL,'2025-05-09 21:07:09','2025-05-09 21:07:09'),(36,45,NULL,'2025-05-10','receipt',3000.00,'IQD',1.000000,11,28,NULL,1,NULL,'2025-05-09 21:09:33','2025-05-09 21:09:33'),(37,45,NULL,'2025-05-10','receipt',2000.00,'IQD',1.000000,11,21,NULL,1,NULL,'2025-05-09 21:09:33','2025-05-09 21:09:33'),(48,57,NULL,'2025-05-10','receipt',56.00,'IQD',1.000000,11,28,NULL,1,NULL,'2025-05-10 09:20:42','2025-05-10 09:20:42'),(49,58,NULL,'2025-05-10','receipt',43.00,'IQD',1.000000,11,25,NULL,1,NULL,'2025-05-10 09:23:42','2025-05-10 09:23:42');
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','admin@example.com',NULL,'$2y$12$SGmv5u4p79WGANqXfB54Befrb8iElvOT37O9bHe0lHP2kT2WBJNFu','35X2HAuicdBoRiQDNJcSidJucHHFhL1Ply7DvGPOcVLY6Kna3fCZagODc3uG','2025-05-02 20:31:37','2025-05-02 20:31:37'),(3,'علاوي','test1@shop.com',NULL,'$2y$12$8IamEdvb4T/Sg2/v1dDkKuCsQVuLaKwYBuj2Dp7gwKZrNd.raH5JG',NULL,'2025-05-03 21:05:35','2025-05-03 21:05:35');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vouchers`
--

DROP TABLE IF EXISTS `vouchers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vouchers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `voucher_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('receipt','payment','transfer') COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IQD',
  `exchange_rate` decimal(15,6) NOT NULL DEFAULT '1.000000',
  `date` datetime NOT NULL,
  `amount` double DEFAULT NULL,
  `account_id` bigint unsigned DEFAULT NULL,
  `target_account_id` bigint unsigned DEFAULT NULL,
  `journal_entry_id` bigint unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned NOT NULL,
  `recipient_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_id` bigint unsigned DEFAULT NULL,
  `status` enum('active','canceled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vouchers_voucher_number_unique` (`voucher_number`),
  KEY `vouchers_created_by_foreign` (`created_by`),
  KEY `vouchers_invoice_id_foreign` (`invoice_id`),
  CONSTRAINT `vouchers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `vouchers_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vouchers`
--

LOCK TABLES `vouchers` WRITE;
/*!40000 ALTER TABLE `vouchers` DISABLE KEYS */;
INSERT INTO `vouchers` VALUES (11,'VCH-00001','receipt','IQD',1.000000,'2025-05-04 23:26:00',NULL,NULL,NULL,NULL,NULL,1,'b',NULL,'canceled','2025-05-04 20:27:06','2025-05-09 06:23:24'),(12,'VCH-00012','payment','IQD',1.000000,'2025-05-04 00:00:00',NULL,NULL,NULL,NULL,'صرف راتب شهر 2025-01 للموظف Mohammed hamdan',1,'Mohammed hamdan',NULL,'canceled','2025-05-04 20:27:31','2025-05-04 20:29:25'),(13,'VCH-00013','transfer','IQD',1.000000,'2025-05-04 23:34:00',NULL,NULL,NULL,NULL,NULL,1,'test',NULL,'canceled','2025-05-04 20:34:19','2025-05-04 20:34:34'),(18,'VCH-00014','transfer','IQD',1.000000,'2025-05-05 00:39:00',800,11,38,37,NULL,1,NULL,NULL,'active','2025-05-04 21:39:48','2025-05-04 21:39:48'),(19,'VCH-00019','transfer','IQD',1.000000,'2025-05-05 10:37:00',2000,11,38,38,NULL,1,NULL,NULL,'active','2025-05-05 07:38:10','2025-05-05 07:38:10'),(20,'VCH-00020','transfer','IQD',1.000000,'2025-05-05 10:38:00',6000,11,39,39,NULL,1,NULL,NULL,'active','2025-05-05 07:38:40','2025-05-05 07:38:40'),(21,'VCH-00021','receipt','IQD',1.000000,'2025-05-05 10:39:00',NULL,NULL,NULL,NULL,NULL,1,'as',NULL,'active','2025-05-05 07:39:26','2025-05-05 07:39:26'),(22,'VCH-00022','transfer','IQD',1.000000,'2025-05-05 10:42:00',100,11,38,41,NULL,1,NULL,NULL,'active','2025-05-05 07:42:46','2025-05-05 07:42:46'),(23,'VCH-00023','receipt','IQD',1.000000,'2025-05-06 00:01:00',NULL,NULL,NULL,NULL,NULL,1,'ذذ',NULL,'active','2025-05-05 21:01:36','2025-05-05 21:01:36'),(24,'VCH-00024','receipt','IQD',1.000000,'2025-05-06 00:04:00',NULL,NULL,NULL,NULL,NULL,1,'يي',NULL,'active','2025-05-05 21:05:08','2025-05-05 21:05:08'),(25,'VCH-00025','receipt','IQD',1.000000,'2025-05-06 00:21:00',NULL,NULL,NULL,NULL,NULL,1,'سسس',NULL,'active','2025-05-05 21:22:10','2025-05-05 21:22:10'),(26,'VCH-00026','receipt','IQD',1.000000,'2025-05-06 00:00:00',NULL,NULL,NULL,NULL,'سداد فاتورة INV-00001',1,'INV-00001',10,'active','2025-05-06 09:35:06','2025-05-06 09:35:06'),(27,'VCH-00027','payment','IQD',1.000000,'2025-05-06 00:00:00',NULL,NULL,NULL,NULL,'صرف راتب شهر 2025-01 للموظف Mohammed hamdan',1,'Mohammed hamdan',NULL,'canceled','2025-05-06 09:55:03','2025-05-09 21:05:02'),(28,'VCH-00028','transfer','IQD',1.000000,'2025-05-07 15:34:00',50000,11,29,49,NULL,1,NULL,NULL,'active','2025-05-07 12:35:09','2025-05-07 12:35:09'),(29,'VCH-00029','transfer','USD',1.000000,'2025-05-07 15:42:00',3,29,11,50,NULL,1,NULL,NULL,'active','2025-05-07 12:43:11','2025-05-07 12:43:11'),(30,'VCH-00030','transfer','IQD',1.000000,'2025-05-07 15:47:00',7777,11,29,51,NULL,1,NULL,NULL,'active','2025-05-07 12:48:10','2025-05-07 12:48:10'),(31,'VCH-00031','transfer','IQD',1.000000,'2025-05-07 15:57:00',5000,11,29,52,NULL,1,NULL,NULL,'active','2025-05-07 12:58:07','2025-05-07 12:58:07'),(32,'VCH-00032','transfer','IQD',1.000000,'2025-05-07 16:06:00',2000,11,29,53,NULL,1,NULL,NULL,'active','2025-05-07 13:06:26','2025-05-07 13:06:26'),(33,'VCH-00033','transfer','IQD',1.000000,'2025-05-07 17:47:00',3000,11,29,54,NULL,1,NULL,NULL,'active','2025-05-07 14:48:01','2025-05-07 14:48:01'),(34,'VCH-00034','transfer','IQD',1.000000,'2025-05-07 17:58:00',5000,11,29,55,NULL,1,NULL,NULL,'active','2025-05-07 14:59:32','2025-05-07 14:59:32'),(35,'VCH-00035','transfer','IQD',1.000000,'2025-05-07 18:00:00',5555,11,38,56,NULL,1,NULL,NULL,'active','2025-05-07 15:00:31','2025-05-07 15:00:31'),(36,'VCH-00036','transfer','IQD',1.000000,'2025-05-07 18:04:00',4000,11,29,57,NULL,1,NULL,NULL,'active','2025-05-07 15:04:23','2025-05-07 15:04:23'),(37,'VCH-00037','receipt','USD',1.000000,'2025-05-09 23:49:00',NULL,NULL,NULL,NULL,NULL,1,'sdsdsd',NULL,'active','2025-05-09 20:50:04','2025-05-09 20:50:04'),(38,'VCH-00038','receipt','IQD',1.000000,'2025-05-10 00:02:00',NULL,NULL,NULL,NULL,NULL,1,'mmm',NULL,'active','2025-05-09 21:02:48','2025-05-09 21:02:48'),(39,'VCH-00039','payment','IQD',1.000000,'2025-05-10 00:00:00',NULL,NULL,NULL,NULL,'صرف راتب شهر 2025-02 للموظف Mohammed hamdan',1,'Mohammed hamdan',NULL,'canceled','2025-05-09 21:03:17','2025-05-09 21:05:56'),(40,'VCH-00040','payment','IQD',1.000000,'2025-05-10 00:00:00',NULL,NULL,NULL,NULL,'صرف راتب شهر 2025-02 للموظف علي',1,'علي',NULL,'active','2025-05-09 21:03:33','2025-05-09 21:03:33'),(41,'VCH-00041','payment','IQD',1.000000,'2025-05-10 00:00:00',NULL,NULL,NULL,NULL,'صرف راتب شهر 2025-01 للموظف Mohammed hamdan',1,'Mohammed hamdan',NULL,'canceled','2025-05-09 21:06:16','2025-05-09 22:07:39'),(42,'VCH-00042','payment','IQD',1.000000,'2025-05-10 00:00:00',NULL,NULL,NULL,NULL,'صرف راتب شهر 2025-02 للموظف Mohammed hamdan',1,'Mohammed hamdan',NULL,'canceled','2025-05-09 21:06:25','2025-05-09 21:06:37'),(43,'VCH-00043','receipt','IQD',1.000000,'2025-05-10 00:00:00',NULL,NULL,NULL,NULL,'سداد فاتورة INV-00011',1,'INV-00011',11,'active','2025-05-09 21:07:09','2025-05-09 21:07:09'),(44,'VCH-00044','receipt','IQD',1.000000,'2025-05-10 00:00:00',NULL,NULL,NULL,NULL,'سداد فاتورة INV-00012',1,'INV-00012',12,'canceled','2025-05-09 21:07:23','2025-05-10 08:11:16'),(45,'VCH-00045','receipt','IQD',1.000000,'2025-05-10 00:08:00',NULL,NULL,NULL,NULL,NULL,1,'asd',NULL,'active','2025-05-09 21:09:33','2025-05-09 21:09:33'),(46,'VCH-00046','payment','IQD',1.000000,'2025-05-10 00:56:00',NULL,NULL,NULL,NULL,NULL,1,'md',NULL,'canceled','2025-05-09 21:56:55','2025-05-09 21:57:42'),(47,'VCH-00047','receipt','IQD',1.000000,'2025-05-10 01:04:00',NULL,NULL,NULL,NULL,NULL,1,'ere',NULL,'canceled','2025-05-09 22:04:43','2025-05-09 22:05:24'),(48,'VCH-00048','payment','IQD',1.000000,'2025-05-10 00:00:00',NULL,NULL,NULL,NULL,'صرف راتب شهر 2025-02 للموظف Mohammed hamdan',1,'Mohammed hamdan',NULL,'canceled','2025-05-09 22:08:40','2025-05-09 22:09:10'),(49,'VCH-00049','receipt','IQD',1.000000,'2025-05-10 10:34:00',NULL,NULL,NULL,NULL,NULL,1,'aa',NULL,'canceled','2025-05-10 07:34:43','2025-05-10 07:34:50'),(50,'VCH-00050','receipt','IQD',1.000000,'2025-05-10 10:42:00',NULL,NULL,NULL,NULL,NULL,1,'ads',NULL,'canceled','2025-05-10 07:43:05','2025-05-10 07:43:14'),(51,'VCH-00051','receipt','IQD',1.000000,'2025-05-10 10:47:00',NULL,NULL,NULL,NULL,NULL,1,'awe',NULL,'canceled','2025-05-10 07:47:32','2025-05-10 07:47:37'),(52,'VCH-00052','receipt','IQD',1.000000,'2025-05-10 10:50:00',NULL,NULL,NULL,NULL,NULL,1,'mohammed',NULL,'canceled','2025-05-10 07:50:33','2025-05-10 07:50:39'),(53,'VCH-00053','receipt','IQD',1.000000,'2025-05-10 10:54:00',NULL,NULL,NULL,NULL,NULL,1,'d',NULL,'canceled','2025-05-10 07:55:02','2025-05-10 07:55:08'),(54,'VCH-00054','receipt','IQD',1.000000,'2025-05-10 11:06:00',NULL,NULL,NULL,NULL,NULL,1,'we',NULL,'canceled','2025-05-10 08:06:53','2025-05-10 08:07:00'),(55,'VCH-00055','receipt','IQD',1.000000,'2025-05-10 00:00:00',NULL,NULL,NULL,NULL,'سداد فاتورة INV-00012',1,'INV-00012',12,'canceled','2025-05-10 08:12:17','2025-05-10 08:12:25'),(56,'VCH-00056','receipt','IQD',1.000000,'2025-05-10 12:18:00',NULL,NULL,NULL,NULL,NULL,1,'hh',NULL,'canceled','2025-05-10 09:18:16','2025-05-10 09:26:30'),(57,'VCH-00057','receipt','IQD',1.000000,'2025-05-10 12:20:00',NULL,NULL,NULL,NULL,NULL,1,'arf',NULL,'active','2025-05-10 09:20:42','2025-05-10 09:20:42'),(58,'VCH-00058','receipt','IQD',1.000000,'2025-05-10 12:23:00',NULL,NULL,NULL,NULL,'trest',1,'ddd',NULL,'active','2025-05-10 09:23:42','2025-05-10 09:23:42');
/*!40000 ALTER TABLE `vouchers` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-10 15:39:29
