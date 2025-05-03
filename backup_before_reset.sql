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
  `sales_account_id` bigint unsigned DEFAULT NULL,
  `purchases_account_id` bigint unsigned DEFAULT NULL,
  `receivables_account_id` bigint unsigned DEFAULT NULL,
  `payables_account_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounting_settings`
--

LOCK TABLES `accounting_settings` WRITE;
/*!40000 ALTER TABLE `accounting_settings` DISABLE KEYS */;
INSERT INTO `accounting_settings` VALUES (1,21,NULL,9,NULL,'2025-05-02 19:09:34','2025-05-02 19:10:45');
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
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounts`
--

LOCK TABLES `accounts` WRITE;
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
INSERT INTO `accounts` VALUES (5,'الأصول','1000',NULL,'asset',NULL,0,'IQD',1,'2025-05-02 17:51:22','2025-05-02 17:51:22'),(6,'الأصول المتداولة','1100',5,'asset',NULL,0,'IQD',1,'2025-05-02 17:51:22','2025-05-02 17:51:22'),(7,'الصندوق الرئيسي','1101',6,'asset','debit',1,'IQD',0,'2025-05-02 17:51:22','2025-05-02 17:51:22'),(8,'البنك الرئيسي','1102',6,'asset','debit',0,'IQD',0,'2025-05-02 17:51:22','2025-05-02 17:51:22'),(9,'العملاء','1103',6,'asset','debit',0,'IQD',0,'2025-05-02 17:51:22','2025-05-02 17:51:22'),(10,'المخزون','1104',6,'asset','debit',0,'IQD',0,'2025-05-02 17:51:22','2025-05-02 17:51:22'),(11,'الأصول الثابتة','1200',5,'asset',NULL,0,'IQD',1,'2025-05-02 17:51:22','2025-05-02 17:51:22'),(12,'الأثاث','1201',11,'asset','debit',0,'IQD',0,'2025-05-02 17:51:22','2025-05-02 17:51:22'),(13,'المركبات','1202',11,'asset','debit',0,'IQD',0,'2025-05-02 17:51:22','2025-05-02 17:51:22'),(14,'الالتزامات','2000',NULL,'liability',NULL,0,'IQD',1,'2025-05-02 17:51:22','2025-05-02 17:51:22'),(15,'الالتزامات المتداولة','2100',14,'liability',NULL,0,'IQD',1,'2025-05-02 17:51:22','2025-05-02 17:51:22'),(16,'الموردون','2101',15,'liability','credit',0,'IQD',0,'2025-05-02 17:51:22','2025-05-02 17:51:22'),(17,'القروض قصيرة الأجل','2102',15,'liability','credit',0,'IQD',0,'2025-05-02 17:51:22','2025-05-02 17:51:22'),(18,'الالتزامات طويلة الأجل','2200',14,'liability',NULL,0,'IQD',1,'2025-05-02 17:51:22','2025-05-02 17:51:22'),(19,'قروض طويلة الأجل','2201',18,'liability','credit',0,'IQD',0,'2025-05-02 17:51:22','2025-05-02 17:51:22'),(20,'حقوق الملكية','3000',NULL,'equity',NULL,0,'IQD',1,'2025-05-02 17:51:22','2025-05-02 17:51:22'),(21,'رأس المال','3100',20,'equity','credit',0,'IQD',0,'2025-05-02 17:51:22','2025-05-02 17:51:22'),(22,'الأرباح المحتجزة','3200',20,'equity','credit',0,'IQD',0,'2025-05-02 17:51:22','2025-05-02 17:51:22'),(23,'الإيرادات','4000',NULL,'revenue',NULL,0,'IQD',1,'2025-05-02 17:51:22','2025-05-02 17:51:22'),(24,'المبيعات','4100',23,'revenue','credit',0,'IQD',0,'2025-05-02 17:51:22','2025-05-02 17:51:22'),(25,'إيرادات الخدمات','4200',23,'revenue','credit',0,'IQD',0,'2025-05-02 17:51:22','2025-05-02 17:51:22'),(26,'المصروفات','5000',NULL,'expense',NULL,0,'IQD',1,'2025-05-02 17:51:22','2025-05-02 17:51:22'),(27,'المصروفات التشغيلية','5100',26,'expense',NULL,0,'IQD',1,'2025-05-02 17:51:22','2025-05-02 17:51:22'),(28,'الرواتب','5101',27,'expense','debit',0,'IQD',0,'2025-05-02 17:51:22','2025-05-02 17:51:22'),(29,'الإيجار','5102',27,'expense','debit',0,'IQD',0,'2025-05-02 17:51:22','2025-05-02 17:51:22'),(30,'المصروفات الإدارية والعمومية','5200',26,'expense','debit',0,'IQD',0,'2025-05-02 17:51:22','2025-05-02 17:51:22'),(31,'مبيعات من اعدادات تجريبي','5201',23,'revenue','credit',0,'IQD',0,'2025-05-02 19:12:09','2025-05-02 19:12:09');
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
INSERT INTO `currencies` VALUES (1,'دينار عراقي','IQD','د.ع',1.000000,1,'2025-05-02 17:52:32','2025-05-02 17:52:32'),(2,'دولار امريكي','USD','ْ$',1500.000000,0,'2025-05-02 17:53:04','2025-05-02 17:53:04');
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
INSERT INTO `customers` VALUES (1,'Mohammed hamdan','mohammed@shopini.com','07726164299','basrah\r\nbas',9,'2025-05-02 17:53:34','2025-05-02 17:53:34');
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (2,'Mohammed hamdan','١','التطوير','مبرمج','2025-04-01','active','IQD','2025-05-02 18:07:03','2025-05-02 18:07:03'),(3,'Mohammed','2','التطوير','مبرمج','2025-03-25','active','IQD','2025-05-02 18:14:25','2025-05-02 18:14:25');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_items`
--

LOCK TABLES `invoice_items` WRITE;
/*!40000 ALTER TABLE `invoice_items` DISABLE KEYS */;
INSERT INTO `invoice_items` VALUES (1,1,1,20,1234.00,24680.00,'2025-05-02 17:55:06','2025-05-02 17:55:06'),(2,2,1,7,1234.00,8638.00,'2025-05-02 19:11:00','2025-05-02 19:11:00');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
INSERT INTO `invoices` VALUES (1,'INV-00001',1,'2025-05-02',24680.00,'IQD',1.000000,'paid',1,'2025-05-02 17:55:06','2025-05-02 17:56:11'),(2,'INV-00002',1,'2025-05-02',8638.00,'IQD',1.000000,'unpaid',1,'2025-05-02 19:11:00','2025-05-02 19:11:00');
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
INSERT INTO `items` VALUES (1,'server','product',1234.00,NULL,'2025-05-02 17:53:55','2025-05-02 17:53:55');
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `journal_entries`
--

LOCK TABLES `journal_entries` WRITE;
/*!40000 ALTER TABLE `journal_entries` DISABLE KEYS */;
INSERT INTO `journal_entries` VALUES (1,'2025-05-02','قيد استحقاق فاتورة INV-00001','invoice',1,1,'IQD',1.000000,24680.00,24680.00,'2025-05-02 17:55:06','2025-05-02 17:55:06'),(2,'2025-05-02','قيد سداد فاتورة INV-00001','App\\Models\\Voucher',1,1,'IQD',1.000000,24680.00,24680.00,'2025-05-02 17:56:11','2025-05-02 17:56:11'),(3,'2025-05-02','قيد سند مالي #VCH-00002','App\\Models\\Voucher',2,1,'IQD',1.000000,7000.00,7000.00,'2025-05-02 18:18:37','2025-05-02 18:18:37'),(4,'2025-05-02','قيد سند مالي #VCH-00003','App\\Models\\Voucher',3,1,'IQD',1.000000,3000.00,3000.00,'2025-05-02 18:18:56','2025-05-02 18:18:56'),(5,'2025-05-02','قيد استحقاق فاتورة INV-00002','invoice',2,1,'IQD',1.000000,8638.00,8638.00,'2025-05-02 19:11:00','2025-05-02 19:11:00');
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `journal_entry_lines`
--

LOCK TABLES `journal_entry_lines` WRITE;
/*!40000 ALTER TABLE `journal_entry_lines` DISABLE KEYS */;
INSERT INTO `journal_entry_lines` VALUES (1,1,9,'استحقاق فاتورة INV-00001',24680.00,0.00,'IQD',1.000000,'2025-05-02 17:55:06','2025-05-02 17:55:06'),(2,1,24,'إيراد فاتورة INV-00001',0.00,24680.00,'IQD',1.000000,'2025-05-02 17:55:06','2025-05-02 17:55:06'),(3,2,7,'استلام نقد لفاتورة INV-00001',24680.00,0.00,'IQD',1.000000,'2025-05-02 17:56:11','2025-05-02 17:56:11'),(4,2,9,'تسوية فاتورة INV-00001',0.00,24680.00,'IQD',1.000000,'2025-05-02 17:56:11','2025-05-02 17:56:11'),(5,3,7,NULL,7000.00,0.00,'IQD',1.000000,'2025-05-02 18:18:37','2025-05-02 18:18:37'),(6,3,30,NULL,0.00,7000.00,'IQD',1.000000,'2025-05-02 18:18:37','2025-05-02 18:18:37'),(7,4,7,NULL,0.00,3000.00,'IQD',1.000000,'2025-05-02 18:18:56','2025-05-02 18:18:56'),(8,4,30,NULL,3000.00,0.00,'IQD',1.000000,'2025-05-02 18:18:56','2025-05-02 18:18:56'),(9,5,9,'استحقاق فاتورة INV-00002',8638.00,0.00,'IQD',1.000000,'2025-05-02 19:11:00','2025-05-02 19:11:00'),(10,5,24,'إيراد فاتورة INV-00002',0.00,8638.00,'IQD',1.000000,'2025-05-02 19:11:00','2025-05-02 19:11:00');
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
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2025_04_28_000001_create_users_table',1),(2,'2025_04_28_000002_create_branches_table',1),(3,'2025_04_28_000003_create_accounts_table',1),(4,'2025_04_28_000004_create_transactions_table',1),(5,'2025_04_28_000006_add_code_to_accounts_table',1),(6,'2025_04_28_143436_create_sessions_table',1),(7,'2025_04_28_143615_create_cache_table',1),(8,'2025_04_28_203550_create_vouchers_table',1),(9,'2025_04_28_203603_add_voucher_id_to_transactions_table',1),(10,'2025_04_28_220859_create_currencies_table',1),(11,'2025_04_28_223223_modify_type_column_in_transactions_table',1),(12,'2025_04_28_231449_add_is_cash_box_to_accounts_table',1),(13,'2025_05_01_110212_add_is_default_to_currencies_table',1),(14,'2025_05_01_110213_create_account_balances_table',1),(15,'2025_05_01_154726_add_currency_and_exchange_rate_to_vouchers_table',1),(16,'2025_05_01_232733_create_invoices_table',1),(17,'2025_05_02_000001_add_code_and_cashbox_to_accounts_table',1),(18,'2025_05_02_000138_create_items_table',1),(19,'2025_05_02_000139_create_customers_table',1),(20,'2025_05_02_000141_create_invoice_items_table',1),(21,'2025_05_02_001528_add_customer_id_to_invoices_table',1),(22,'2025_05_02_201011_create_salary_batches_table',1),(23,'2025_05_03_000001_add_currency_to_accounts_table',1),(24,'2025_05_10_000000_add_invoice_id_to_vouchers_table',1),(25,'2025_05_10_000001_add_invoice_id_to_transactions_table',1),(26,'2025_05_10_100000_create_journal_entries_table',1),(27,'2025_05_10_100001_create_journal_entry_lines_table',1),(28,'2025_05_11_000001_create_employees_table',1),(29,'2025_05_11_000002_create_salaries_table',1),(30,'2025_05_11_000003_create_salary_payments_table',1),(31,'2025_05_02_210314_alter_payment_date_nullable_on_salary_payments_table',2),(32,'2025_05_02_215900_create_accounting_settings_table',3),(33,'2025_05_02_220835_remove_cash_sales_account_id_from_accounting_settings_table',4);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salaries`
--

LOCK TABLES `salaries` WRITE;
/*!40000 ALTER TABLE `salaries` DISABLE KEYS */;
INSERT INTO `salaries` VALUES (2,2,7000.00,'[]','[]','2025-04-01',NULL,'2025-05-02 18:07:30','2025-05-02 18:07:30'),(3,3,2000.00,'[]','[]','2025-04-01',NULL,'2025-05-02 18:14:40','2025-05-02 18:14:40');
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salary_batches`
--

LOCK TABLES `salary_batches` WRITE;
/*!40000 ALTER TABLE `salary_batches` DISABLE KEYS */;
INSERT INTO `salary_batches` VALUES (8,'2025-04','approved',1,1,'2025-05-02 18:32:57','2025-05-02 18:23:45','2025-05-02 18:32:57');
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salary_payments`
--

LOCK TABLES `salary_payments` WRITE;
/*!40000 ALTER TABLE `salary_payments` DISABLE KEYS */;
INSERT INTO `salary_payments` VALUES (3,8,2,'2025-04',7000.00,0.00,0.00,7000.00,NULL,'pending',NULL,NULL,'2025-05-02 18:23:45','2025-05-02 18:23:45'),(4,8,3,'2025-04',2000.00,0.00,0.00,2000.00,NULL,'pending',NULL,NULL,'2025-05-02 18:23:45','2025-05-02 18:23:45');
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
INSERT INTO `sessions` VALUES ('Bi4qp578JDHxBpYh7wKYaZX4UYqX9tfuEqFG8b9Z',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoieHVNMEtMMUl6Zlp6T2dMUTkzM2NZUmhBelc2NjEyWmN3MTJkTUl5MCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9qb3VybmFsLWVudHJpZXMvY3JlYXRlIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjQ6ImF1dGgiO2E6MTp7czoyMToicGFzc3dvcmRfY29uZmlybWVkX2F0IjtpOjE3NDYyMTgwNjI7fX0=',1746224105);
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','admin@example.com',NULL,'$2y$12$1ZzNloJwaR/k/TU1m52nLOkLFhHf7ogctEXttYs2D6.hqDbtIRYtS',NULL,'2025-05-02 17:34:07','2025-05-02 17:34:07');
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vouchers`
--

LOCK TABLES `vouchers` WRITE;
/*!40000 ALTER TABLE `vouchers` DISABLE KEYS */;
INSERT INTO `vouchers` VALUES (1,'VCH-00001','receipt','IQD',1.000000,'2025-05-02','سداد فاتورة INV-00001',1,'INV-00001',1,'2025-05-02 17:56:11','2025-05-02 17:56:11'),(2,'VCH-00002','receipt','IQD',1.000000,'2025-05-02','ممم',1,'محمد',NULL,'2025-05-02 18:18:37','2025-05-02 18:18:37'),(3,'VCH-00003','payment','IQD',1.000000,'2025-05-02','gggg',1,'ggg',NULL,'2025-05-02 18:18:56','2025-05-02 18:18:56');
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

-- Dump completed on 2025-05-03  1:31:40
