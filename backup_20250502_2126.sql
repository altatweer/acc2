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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account_balances`
--

LOCK TABLES `account_balances` WRITE;
/*!40000 ALTER TABLE `account_balances` DISABLE KEYS */;
INSERT INTO `account_balances` VALUES (1,1,1,0.00,'2025-05-02 14:07:00','2025-05-02 14:07:00'),(2,2,1,0.00,'2025-05-02 14:07:00','2025-05-02 14:07:00'),(3,3,1,0.00,'2025-05-02 14:07:00','2025-05-02 14:07:00'),(4,4,1,0.00,'2025-05-02 14:07:00','2025-05-02 14:07:00'),(5,5,1,0.00,'2025-05-02 14:07:01','2025-05-02 14:07:01'),(6,6,1,0.00,'2025-05-02 14:07:01','2025-05-02 14:07:01'),(7,7,1,0.00,'2025-05-02 14:07:01','2025-05-02 14:07:01'),(8,8,1,0.00,'2025-05-02 14:07:01','2025-05-02 14:07:01'),(9,9,1,0.00,'2025-05-02 14:07:01','2025-05-02 14:07:01'),(10,10,1,0.00,'2025-05-02 14:07:01','2025-05-02 14:07:01'),(11,11,1,0.00,'2025-05-02 14:07:01','2025-05-02 14:07:01'),(12,12,1,0.00,'2025-05-02 14:07:01','2025-05-02 14:07:01'),(13,13,1,0.00,'2025-05-02 14:07:01','2025-05-02 14:07:01'),(14,14,1,0.00,'2025-05-02 14:07:01','2025-05-02 14:07:01'),(15,15,1,0.00,'2025-05-02 14:07:01','2025-05-02 14:07:01'),(16,16,1,0.00,'2025-05-02 14:07:01','2025-05-02 14:07:01'),(17,17,1,0.00,'2025-05-02 14:07:01','2025-05-02 14:07:01'),(18,18,1,0.00,'2025-05-02 14:07:01','2025-05-02 14:07:01'),(19,19,1,0.00,'2025-05-02 14:07:01','2025-05-02 14:07:01'),(20,20,1,0.00,'2025-05-02 14:07:01','2025-05-02 14:07:01'),(21,21,1,0.00,'2025-05-02 14:07:01','2025-05-02 14:07:01'),(22,22,1,0.00,'2025-05-02 14:07:01','2025-05-02 14:07:01');
/*!40000 ALTER TABLE `account_balances` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounts`
--

LOCK TABLES `accounts` WRITE;
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
INSERT INTO `accounts` VALUES (1,'الأصول','1000',NULL,'asset',NULL,0,'IQD',1,'2025-05-02 14:07:00','2025-05-02 14:07:00'),(2,'الالتزامات','2000',NULL,'liability',NULL,0,'IQD',1,'2025-05-02 14:07:00','2025-05-02 14:07:00'),(3,'الإيرادات','3000',NULL,'revenue',NULL,0,'IQD',1,'2025-05-02 14:07:00','2025-05-02 14:07:00'),(4,'المصاريف','4000',NULL,'expense',NULL,0,'IQD',1,'2025-05-02 14:07:00','2025-05-02 14:07:00'),(5,'رأس المال','5000',NULL,'equity',NULL,0,'IQD',1,'2025-05-02 14:07:00','2025-05-02 14:07:00'),(6,'النقدية','1100',1,'asset',NULL,0,'IQD',1,'2025-05-02 14:07:00','2025-05-02 14:07:00'),(7,'حسابات العملاء','1200',1,'asset',NULL,0,'IQD',1,'2025-05-02 14:07:00','2025-05-02 14:07:00'),(8,'أصول ثابتة','1300',1,'asset',NULL,0,'IQD',1,'2025-05-02 14:07:00','2025-05-02 14:07:00'),(9,'صندوق رئيسي دينار','1101',6,'asset','debit',1,'IQD',0,'2025-05-02 14:07:00','2025-05-02 14:17:35'),(10,'حساب بنك رئيسي','1102',6,'asset','debit',1,'USD',0,'2025-05-02 14:07:00','2025-05-02 14:17:43'),(11,'حسابات الموردين','2100',2,'liability',NULL,0,'IQD',1,'2025-05-02 14:07:00','2025-05-02 14:07:00'),(12,'مورد رئيسي','2101',11,'liability','credit',0,'IQD',0,'2025-05-02 14:07:00','2025-05-02 14:07:00'),(13,'مبيعات المنتجات','3100',3,'revenue',NULL,0,'IQD',1,'2025-05-02 14:07:00','2025-05-02 14:07:00'),(14,'مبيعات الخدمات','3200',3,'revenue',NULL,0,'IQD',1,'2025-05-02 14:07:00','2025-05-02 14:07:00'),(15,'مبيعات نقدية','3101',13,'revenue','credit',0,'IQD',0,'2025-05-02 14:07:00','2025-05-02 14:07:00'),(16,'مبيعات بالتقسيط','3201',14,'revenue','credit',0,'IQD',0,'2025-05-02 14:07:00','2025-05-02 14:07:00'),(17,'رواتب الموظفين','4100',4,'expense',NULL,0,'IQD',1,'2025-05-02 14:07:00','2025-05-02 14:07:00'),(18,'مصاريف إيجار','4200',4,'expense',NULL,0,'IQD',1,'2025-05-02 14:07:00','2025-05-02 14:07:00'),(19,'راتب موظف رئيسي','4101',17,'expense','debit',0,'IQD',0,'2025-05-02 14:07:00','2025-05-02 14:07:00'),(20,'فاتورة إيجار مكتب','4201',18,'expense','debit',0,'IQD',0,'2025-05-02 14:07:00','2025-05-02 14:07:00'),(21,'رأس مال المؤسسين','5100',5,'equity',NULL,0,'IQD',1,'2025-05-02 14:07:00','2025-05-02 14:07:00'),(22,'رأس مال المالك','5101',5,'equity','credit',0,'IQD',0,'2025-05-02 14:07:00','2025-05-02 14:07:00');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `currencies`
--

LOCK TABLES `currencies` WRITE;
/*!40000 ALTER TABLE `currencies` DISABLE KEYS */;
INSERT INTO `currencies` VALUES (1,'دينار عراقي','IQD','د.ع',1.000000,1,'2025-05-02 14:07:00','2025-05-02 14:07:00'),(2,'دولار أمريكي','USD','$',1500.000000,0,'2025-05-02 14:07:00','2025-05-02 14:07:00');
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
INSERT INTO `customers` VALUES (1,'Mohammed hamdan','mohammed@shopini.com','07726164299','basrah\r\nbas',16,'2025-05-02 14:17:26','2025-05-02 14:17:26');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
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
INSERT INTO `invoice_items` VALUES (1,1,1,1,9000.00,9000.00,'2025-05-02 14:18:14','2025-05-02 14:18:14'),(2,2,1,2,9000.00,18000.00,'2025-05-02 14:27:34','2025-05-02 14:27:34'),(3,3,1,4,9000.00,36000.00,'2025-05-02 14:36:09','2025-05-02 14:36:09'),(4,4,1,1,9000.00,9000.00,'2025-05-02 14:39:59','2025-05-02 14:39:59'),(5,4,1,6,9000.00,54000.00,'2025-05-02 14:39:59','2025-05-02 14:39:59'),(6,5,1,8,9000.00,72000.00,'2025-05-02 14:44:38','2025-05-02 14:44:38'),(7,6,1,16,9000.00,144000.00,'2025-05-02 15:14:00','2025-05-02 15:14:00'),(8,7,1,1,9000.00,9000.00,'2025-05-02 15:18:57','2025-05-02 15:18:57');
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
  `status` enum('unpaid','partial','paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  KEY `invoices_created_by_foreign` (`created_by`),
  KEY `invoices_customer_id_foreign` (`customer_id`),
  CONSTRAINT `invoices_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoices_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
INSERT INTO `invoices` VALUES (1,'INV-00001',1,'2025-05-02',9000.00,'IQD',1.000000,'paid',3,'2025-05-02 14:18:14','2025-05-02 14:18:39'),(2,'INV-00002',1,'2025-05-02',18000.00,'IQD',1.000000,'paid',3,'2025-05-02 14:27:34','2025-05-02 14:27:40'),(3,'INV-00003',1,'2025-05-02',36000.00,'IQD',1.000000,'paid',3,'2025-05-02 14:36:09','2025-05-02 14:36:28'),(4,'INV-00004',1,'2025-05-02',63000.00,'IQD',1.000000,'paid',3,'2025-05-02 14:39:59','2025-05-02 14:41:54'),(5,'INV-00005',1,'2025-05-02',72000.00,'IQD',1.000000,'paid',3,'2025-05-02 14:44:38','2025-05-02 14:45:25'),(6,'INV-00006',1,'2025-05-02',144000.00,'IQD',1.000000,'paid',3,'2025-05-02 15:14:00','2025-05-02 15:15:07'),(7,'INV-00007',1,'2025-05-02',9000.00,'IQD',1.000000,'paid',3,'2025-05-02 15:18:57','2025-05-02 15:19:01');
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
INSERT INTO `items` VALUES (1,'الأصول','product',9000.00,NULL,'2025-05-02 14:18:03','2025-05-02 14:18:03');
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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `journal_entries_created_by_foreign` (`created_by`),
  CONSTRAINT `journal_entries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `journal_entries`
--

LOCK TABLES `journal_entries` WRITE;
/*!40000 ALTER TABLE `journal_entries` DISABLE KEYS */;
INSERT INTO `journal_entries` VALUES (1,'2025-05-02','قيد استحقاق فاتورة INV-00001','invoice',1,3,'IQD',1.000000,9000.00,9000.00,'2025-05-02 14:18:14','2025-05-02 14:18:14'),(2,'2025-05-02','قيد سداد فاتورة INV-00001','voucher',1,3,'IQD',1.000000,9000.00,9000.00,'2025-05-02 14:18:39','2025-05-02 14:18:39'),(3,'2025-05-02','قيد استحقاق فاتورة INV-00002','invoice',2,3,'IQD',1.000000,18000.00,18000.00,'2025-05-02 14:27:34','2025-05-02 14:27:34'),(4,'2025-05-02','قيد سداد فاتورة INV-00002','voucher',2,3,'IQD',1.000000,18000.00,18000.00,'2025-05-02 14:27:40','2025-05-02 14:27:40'),(5,'2025-05-02','قيد استحقاق فاتورة INV-00003','invoice',3,3,'IQD',1.000000,36000.00,36000.00,'2025-05-02 14:36:09','2025-05-02 14:36:09'),(6,'2025-05-02','قيد سداد فاتورة INV-00003','voucher',3,3,'IQD',1.000000,36000.00,36000.00,'2025-05-02 14:36:28','2025-05-02 14:36:28'),(7,'2025-05-02','قيد استحقاق فاتورة INV-00004','invoice',4,3,'IQD',1.000000,63000.00,63000.00,'2025-05-02 14:39:59','2025-05-02 14:39:59'),(8,'2025-05-02','قيد سداد فاتورة INV-00004','voucher',4,3,'IQD',1.000000,63000.00,63000.00,'2025-05-02 14:41:54','2025-05-02 14:41:54'),(9,'2025-05-02','قيد استحقاق فاتورة INV-00005','invoice',5,3,'IQD',1.000000,72000.00,72000.00,'2025-05-02 14:44:38','2025-05-02 14:44:38'),(10,'2025-05-02','قيد سداد فاتورة INV-00005','voucher',5,3,'IQD',1.000000,72000.00,72000.00,'2025-05-02 14:45:25','2025-05-02 14:45:25'),(11,'2025-05-02','قيد سند مالي #VCH-00006','voucher',6,3,'IQD',1.000000,50000.00,50000.00,'2025-05-02 14:57:02','2025-05-02 14:57:02'),(12,'2025-05-02','قيد سند مالي #VCH-00007','voucher',7,3,'IQD',1.000000,7000.00,7000.00,'2025-05-02 15:03:56','2025-05-02 15:03:56'),(13,'2025-05-02','قيد سند مالي #VCH-00008','voucher',8,3,'IQD',1.000000,30000.00,30000.00,'2025-05-02 15:10:38','2025-05-02 15:10:38'),(14,'2025-05-02','قيد سند مالي #VCH-00009','App\\Models\\Voucher',9,3,'IQD',1.000000,5555.00,5555.00,'2025-05-02 15:13:37','2025-05-02 15:13:37'),(15,'2025-05-02','قيد استحقاق فاتورة INV-00006','invoice',6,3,'IQD',1.000000,144000.00,144000.00,'2025-05-02 15:14:00','2025-05-02 15:14:00'),(16,'2025-05-02','قيد سداد فاتورة INV-00006','voucher',10,3,'IQD',1.000000,144000.00,144000.00,'2025-05-02 15:15:07','2025-05-02 15:15:07'),(17,'2025-05-02','قيد استحقاق فاتورة INV-00007','invoice',7,3,'IQD',1.000000,9000.00,9000.00,'2025-05-02 15:18:57','2025-05-02 15:18:57'),(18,'2025-05-02','قيد سداد فاتورة INV-00007','App\\Models\\Voucher',11,3,'IQD',1.000000,9000.00,9000.00,'2025-05-02 15:19:01','2025-05-02 15:19:01');
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
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `journal_entry_lines`
--

LOCK TABLES `journal_entry_lines` WRITE;
/*!40000 ALTER TABLE `journal_entry_lines` DISABLE KEYS */;
INSERT INTO `journal_entry_lines` VALUES (1,1,16,'استحقاق فاتورة INV-00001',9000.00,0.00,'IQD',1.000000,'2025-05-02 14:18:14','2025-05-02 14:18:14'),(2,1,15,'إيراد فاتورة INV-00001',0.00,9000.00,'IQD',1.000000,'2025-05-02 14:18:14','2025-05-02 14:18:14'),(3,2,9,'استلام نقد لفاتورة INV-00001',9000.00,0.00,'IQD',1.000000,'2025-05-02 14:18:39','2025-05-02 14:18:39'),(4,2,16,'تسوية فاتورة INV-00001',0.00,9000.00,'IQD',1.000000,'2025-05-02 14:18:39','2025-05-02 14:18:39'),(5,3,16,'استحقاق فاتورة INV-00002',18000.00,0.00,'IQD',1.000000,'2025-05-02 14:27:34','2025-05-02 14:27:34'),(6,3,15,'إيراد فاتورة INV-00002',0.00,18000.00,'IQD',1.000000,'2025-05-02 14:27:34','2025-05-02 14:27:34'),(7,4,9,'استلام نقد لفاتورة INV-00002',18000.00,0.00,'IQD',1.000000,'2025-05-02 14:27:40','2025-05-02 14:27:40'),(8,4,16,'تسوية فاتورة INV-00002',0.00,18000.00,'IQD',1.000000,'2025-05-02 14:27:40','2025-05-02 14:27:40'),(9,5,16,'استحقاق فاتورة INV-00003',36000.00,0.00,'IQD',1.000000,'2025-05-02 14:36:09','2025-05-02 14:36:09'),(10,5,15,'إيراد فاتورة INV-00003',0.00,36000.00,'IQD',1.000000,'2025-05-02 14:36:09','2025-05-02 14:36:09'),(11,6,9,'استلام نقد لفاتورة INV-00003',36000.00,0.00,'IQD',1.000000,'2025-05-02 14:36:28','2025-05-02 14:36:28'),(12,6,16,'تسوية فاتورة INV-00003',0.00,36000.00,'IQD',1.000000,'2025-05-02 14:36:28','2025-05-02 14:36:28'),(13,7,16,'استحقاق فاتورة INV-00004',63000.00,0.00,'IQD',1.000000,'2025-05-02 14:39:59','2025-05-02 14:39:59'),(14,7,15,'إيراد فاتورة INV-00004',0.00,63000.00,'IQD',1.000000,'2025-05-02 14:39:59','2025-05-02 14:39:59'),(15,8,9,'استلام نقد لفاتورة INV-00004',63000.00,0.00,'IQD',1.000000,'2025-05-02 14:41:54','2025-05-02 14:41:54'),(16,8,16,'تسوية فاتورة INV-00004',0.00,63000.00,'IQD',1.000000,'2025-05-02 14:41:54','2025-05-02 14:41:54'),(17,9,16,'استحقاق فاتورة INV-00005',72000.00,0.00,'IQD',1.000000,'2025-05-02 14:44:38','2025-05-02 14:44:38'),(18,9,15,'إيراد فاتورة INV-00005',0.00,72000.00,'IQD',1.000000,'2025-05-02 14:44:38','2025-05-02 14:44:38'),(19,10,9,'استلام نقد لفاتورة INV-00005',72000.00,0.00,'IQD',1.000000,'2025-05-02 14:45:25','2025-05-02 14:45:25'),(20,10,16,'تسوية فاتورة INV-00005',0.00,72000.00,'IQD',1.000000,'2025-05-02 14:45:25','2025-05-02 14:45:25'),(21,11,9,NULL,50000.00,0.00,'IQD',1.000000,'2025-05-02 14:57:02','2025-05-02 14:57:02'),(22,11,12,NULL,0.00,50000.00,'IQD',1.000000,'2025-05-02 14:57:02','2025-05-02 14:57:02'),(23,12,9,NULL,7000.00,0.00,'IQD',1.000000,'2025-05-02 15:03:56','2025-05-02 15:03:56'),(24,12,12,NULL,0.00,7000.00,'IQD',1.000000,'2025-05-02 15:03:56','2025-05-02 15:03:56'),(25,13,9,NULL,30000.00,0.00,'IQD',1.000000,'2025-05-02 15:10:38','2025-05-02 15:10:38'),(26,13,20,NULL,0.00,30000.00,'IQD',1.000000,'2025-05-02 15:10:38','2025-05-02 15:10:38'),(27,14,9,NULL,5555.00,0.00,'IQD',1.000000,'2025-05-02 15:13:37','2025-05-02 15:13:37'),(28,14,20,NULL,0.00,5555.00,'IQD',1.000000,'2025-05-02 15:13:37','2025-05-02 15:13:37'),(29,15,16,'استحقاق فاتورة INV-00006',144000.00,0.00,'IQD',1.000000,'2025-05-02 15:14:00','2025-05-02 15:14:00'),(30,15,15,'إيراد فاتورة INV-00006',0.00,144000.00,'IQD',1.000000,'2025-05-02 15:14:00','2025-05-02 15:14:00'),(31,16,9,'استلام نقد لفاتورة INV-00006',144000.00,0.00,'IQD',1.000000,'2025-05-02 15:15:07','2025-05-02 15:15:07'),(32,16,16,'تسوية فاتورة INV-00006',0.00,144000.00,'IQD',1.000000,'2025-05-02 15:15:07','2025-05-02 15:15:07'),(33,17,16,'استحقاق فاتورة INV-00007',9000.00,0.00,'IQD',1.000000,'2025-05-02 15:18:57','2025-05-02 15:18:57'),(34,17,15,'إيراد فاتورة INV-00007',0.00,9000.00,'IQD',1.000000,'2025-05-02 15:18:57','2025-05-02 15:18:57'),(35,18,9,'استلام نقد لفاتورة INV-00007',9000.00,0.00,'IQD',1.000000,'2025-05-02 15:19:01','2025-05-02 15:19:01'),(36,18,16,'تسوية فاتورة INV-00007',0.00,9000.00,'IQD',1.000000,'2025-05-02 15:19:01','2025-05-02 15:19:01');
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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2025_04_28_000001_create_users_table',1),(2,'2025_04_28_000002_create_branches_table',1),(3,'2025_04_28_000003_create_accounts_table',1),(4,'2025_04_28_000004_create_transactions_table',1),(5,'2025_04_28_000006_add_code_to_accounts_table',1),(6,'2025_04_28_143436_create_sessions_table',1),(7,'2025_04_28_143615_create_cache_table',1),(8,'2025_04_28_203550_create_vouchers_table',1),(9,'2025_04_28_203603_add_voucher_id_to_transactions_table',1),(10,'2025_04_28_220859_create_currencies_table',1),(11,'2025_04_28_223223_modify_type_column_in_transactions_table',1),(12,'2025_04_28_231449_add_is_cash_box_to_accounts_table',1),(13,'2025_05_01_110212_add_is_default_to_currencies_table',1),(14,'2025_05_01_110213_create_account_balances_table',1),(15,'2025_05_01_154726_add_currency_and_exchange_rate_to_vouchers_table',1),(16,'2025_05_01_232733_create_invoices_table',1),(17,'2025_05_02_000001_add_code_and_cashbox_to_accounts_table',1),(18,'2025_05_02_000138_create_items_table',1),(19,'2025_05_02_000139_create_customers_table',1),(20,'2025_05_02_000141_create_invoice_items_table',1),(21,'2025_05_02_001528_add_customer_id_to_invoices_table',1),(22,'2025_05_03_000001_add_currency_to_accounts_table',1),(23,'2025_05_10_000000_add_invoice_id_to_vouchers_table',1),(24,'2025_05_10_000001_add_invoice_id_to_transactions_table',1),(25,'2025_05_10_100000_create_journal_entries_table',1),(26,'2025_05_10_100001_create_journal_entry_lines_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
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
INSERT INTO `sessions` VALUES ('HWVYxSbcscYlxKWtIK3CKHTAab5yIJhPzayL1QHn',3,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','YTo2OntzOjY6Il90b2tlbiI7czo0MDoiMVZ4YXl3YUZlUHVXV0JMdkdJYmJaZkhQN05YS2N2Z3pRcWJMU0Q1NyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MztzOjQ6ImF1dGgiO2E6MTp7czoyMToicGFzc3dvcmRfY29uZmlybWVkX2F0IjtpOjE3NDYyMDYyMTM7fX0=',1746210002);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
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
INSERT INTO `users` VALUES (3,'Admin','admin@example.com',NULL,'$2y$12$Kby/ZtQE3iEEktrOAKKFW.wkxTc46vbZh97k2QJy8KwyQe6ST4ZEa',NULL,'2025-05-02 14:16:40','2025-05-02 14:16:40');
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
  `date` date NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned NOT NULL,
  `recipient_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vouchers_voucher_number_unique` (`voucher_number`),
  KEY `vouchers_created_by_foreign` (`created_by`),
  KEY `vouchers_invoice_id_foreign` (`invoice_id`),
  CONSTRAINT `vouchers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `vouchers_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vouchers`
--

LOCK TABLES `vouchers` WRITE;
/*!40000 ALTER TABLE `vouchers` DISABLE KEYS */;
INSERT INTO `vouchers` VALUES (1,'VCH-00001','receipt','IQD',1.000000,'2025-05-02','سداد فاتورة INV-00001',3,'INV-00001',1,'2025-05-02 14:18:39','2025-05-02 14:18:39'),(2,'VCH-00002','receipt','IQD',1.000000,'2025-05-02','سداد فاتورة INV-00002',3,'INV-00002',2,'2025-05-02 14:27:40','2025-05-02 14:27:40'),(3,'VCH-00003','receipt','IQD',1.000000,'2025-05-02','سداد فاتورة INV-00003',3,'INV-00003',3,'2025-05-02 14:36:28','2025-05-02 14:36:28'),(4,'VCH-00004','receipt','IQD',1.000000,'2025-05-02','سداد فاتورة INV-00004',3,'INV-00004',4,'2025-05-02 14:41:54','2025-05-02 14:41:54'),(5,'VCH-00005','receipt','IQD',1.000000,'2025-05-02','سداد فاتورة INV-00005',3,'INV-00005',5,'2025-05-02 14:45:25','2025-05-02 14:45:25'),(6,'VCH-00006','receipt','IQD',1.000000,'2025-05-02',NULL,3,'moh',NULL,'2025-05-02 14:57:02','2025-05-02 14:57:02'),(7,'VCH-00007','receipt','IQD',1.000000,'2025-05-02',NULL,3,'test',NULL,'2025-05-02 15:03:56','2025-05-02 15:03:56'),(8,'VCH-00008','receipt','IQD',1.000000,'2025-05-02',NULL,3,'mkkk',NULL,'2025-05-02 15:10:38','2025-05-02 15:10:38'),(9,'VCH-00009','receipt','IQD',1.000000,'2025-05-02',NULL,3,'aa',NULL,'2025-05-02 15:13:37','2025-05-02 15:13:37'),(10,'VCH-00010','receipt','IQD',1.000000,'2025-05-02','سداد فاتورة INV-00006',3,'INV-00006',6,'2025-05-02 15:15:07','2025-05-02 15:15:07'),(11,'VCH-00011','receipt','IQD',1.000000,'2025-05-02','سداد فاتورة INV-00007',3,'INV-00007',7,'2025-05-02 15:19:01','2025-05-02 15:19:01');
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

-- Dump completed on 2025-05-02 21:26:43
