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
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounts`
--

LOCK TABLES `accounts` WRITE;
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
INSERT INTO `accounts` VALUES (1,'الأصول','1000',NULL,'asset',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(2,'الالتزامات','2000',NULL,'liability',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(3,'الإيرادات','3000',NULL,'revenue',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(4,'المصاريف','4000',NULL,'expense',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(5,'رأس المال','5000',NULL,'equity',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(6,'النقدية','1100',1,'asset',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(7,'البنوك','1200',1,'asset',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(8,'حسابات العملاء','1300',1,'asset',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(9,'المخزون','1400',1,'asset',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(10,'الأصول الثابتة','1500',1,'asset',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(11,'صندوق رئيسي دينار','1101',6,'asset','debit',1,'IQD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(12,'بنك رئيسي دينار','1201',7,'asset','debit',0,'IQD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(13,'عميل رئيسي دينار','1301',8,'asset','debit',0,'IQD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(14,'حسابات الموردين','2100',2,'liability',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(15,'الضرائب المستحقة','2200',2,'liability',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(16,'قروض طويلة الأجل','2300',2,'liability',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(17,'مورد رئيسي دينار','2101',14,'liability','credit',0,'IQD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(18,'مبيعات المنتجات','3100',3,'revenue',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(19,'مبيعات الخدمات','3200',3,'revenue',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(20,'مبيعات نقدية','3101',18,'revenue','credit',0,'IQD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(21,'مبيعات بالتقسيط','3201',19,'revenue','credit',0,'IQD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(22,'رواتب الموظفين','4100',4,'expense',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(23,'مصاريف إيجار','4200',4,'expense',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(24,'مصروف رواتب دينار','4101',22,'expense','debit',0,'IQD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(25,'ذمم مستحقة للموظفين دينار','2102',14,'liability','credit',0,'IQD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(26,'خصومات رواتب دينار','2201',15,'liability','credit',0,'IQD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(27,'رأس مال المؤسسين','5100',5,'equity',NULL,0,'IQD',1,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(28,'رأس مال المالك','5101',5,'equity','credit',0,'IQD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(29,'صندوق رئيسي دولار','1102',6,'asset','debit',1,'USD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(30,'بنك رئيسي دولار','1202',7,'asset','debit',0,'USD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(31,'عميل رئيسي دولار','1302',8,'asset','debit',0,'USD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(32,'مورد رئيسي دولار','2103',14,'liability','credit',0,'USD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(33,'مصروف رواتب دولار','4102',22,'expense','debit',0,'USD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(34,'ذمم مستحقة للموظفين دولار','2104',14,'liability','credit',0,'USD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47'),(35,'خصومات رواتب دولار','2202',15,'liability','credit',0,'USD',0,'2025-05-02 20:41:47','2025-05-02 20:41:47');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_items`
--

LOCK TABLES `invoice_items` WRITE;
/*!40000 ALTER TABLE `invoice_items` DISABLE KEYS */;
INSERT INTO `invoice_items` VALUES (1,1,1,4,3444.00,13776.00,'2025-05-03 18:44:37','2025-05-03 18:44:37');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
INSERT INTO `invoices` VALUES (1,'INV-00001',1,'2025-05-03',13776.00,'IQD',1.000000,'paid',1,'2025-05-03 18:44:37','2025-05-03 18:49:21');
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
INSERT INTO `journal_entries` VALUES (1,'2025-01-31','قيد استحقاق رواتب شهر 2025-01','App\\Models\\SalaryBatch',1,1,'IQD',1.000000,5000.00,5000.00,'2025-05-03 18:41:16','2025-05-03 18:41:16'),(2,'2025-05-03','قيد سند مالي #VCH-00001','App\\Models\\Voucher',1,1,'IQD',1.000000,5000.00,5000.00,'2025-05-03 18:42:06','2025-05-03 18:42:06'),(3,'2025-05-03','دفع راتب شهر 2025-01 للموظف Mohammed hamdan','App\\Models\\Voucher',2,1,'IQD',1.000000,4800.00,4800.00,'2025-05-03 18:43:40','2025-05-03 18:43:40'),(4,'2025-05-03','قيد استحقاق فاتورة INV-00001','invoice',1,1,'IQD',1.000000,13776.00,13776.00,'2025-05-03 18:44:37','2025-05-03 18:44:37'),(5,'2025-05-03','قيد سداد فاتورة INV-00001','App\\Models\\Voucher',3,1,'IQD',1.000000,13776.00,13776.00,'2025-05-03 18:49:21','2025-05-03 18:49:21');
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `journal_entry_lines`
--

LOCK TABLES `journal_entry_lines` WRITE;
/*!40000 ALTER TABLE `journal_entry_lines` DISABLE KEYS */;
INSERT INTO `journal_entry_lines` VALUES (1,1,24,'استحقاق رواتب شهر 2025-01',5000.00,0.00,'IQD',1.000000,'2025-05-03 18:41:16','2025-05-03 18:41:16'),(2,1,25,'ذمم مستحقة للموظفين عن رواتب شهر 2025-01',0.00,4800.00,'IQD',1.000000,'2025-05-03 18:41:16','2025-05-03 18:41:16'),(3,1,26,'خصومات رواتب شهر 2025-01',0.00,200.00,'IQD',1.000000,'2025-05-03 18:41:16','2025-05-03 18:41:16'),(4,2,11,NULL,5000.00,0.00,'IQD',1.000000,'2025-05-03 18:42:06','2025-05-03 18:42:06'),(5,2,28,NULL,0.00,5000.00,'IQD',1.000000,'2025-05-03 18:42:06','2025-05-03 18:42:06'),(6,3,25,'صرف راتب للموظف Mohammed hamdan',4800.00,0.00,'IQD',1.000000,'2025-05-03 18:43:40','2025-05-03 18:43:40'),(7,3,11,'دفع راتب للموظف Mohammed hamdan',0.00,4800.00,'IQD',1.000000,'2025-05-03 18:43:40','2025-05-03 18:43:40'),(8,4,13,'استحقاق فاتورة INV-00001',13776.00,0.00,'IQD',1.000000,'2025-05-03 18:44:37','2025-05-03 18:44:37'),(9,4,20,'إيراد فاتورة INV-00001',0.00,13776.00,'IQD',1.000000,'2025-05-03 18:44:37','2025-05-03 18:44:37'),(10,5,11,'استلام نقد لفاتورة INV-00001',13776.00,0.00,'IQD',1.000000,'2025-05-03 18:49:21','2025-05-03 18:49:21'),(11,5,13,'تسوية فاتورة INV-00001',0.00,13776.00,'IQD',1.000000,'2025-05-03 18:49:21','2025-05-03 18:49:21');
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
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2025_04_28_000001_create_users_table',1),(2,'2025_04_28_000002_create_branches_table',1),(3,'2025_04_28_000003_create_accounts_table',1),(4,'2025_04_28_000004_create_transactions_table',1),(5,'2025_04_28_000006_add_code_to_accounts_table',1),(6,'2025_04_28_143436_create_sessions_table',1),(7,'2025_04_28_143615_create_cache_table',1),(8,'2025_04_28_203550_create_vouchers_table',1),(9,'2025_04_28_203603_add_voucher_id_to_transactions_table',1),(10,'2025_04_28_220859_create_currencies_table',1),(11,'2025_04_28_223223_modify_type_column_in_transactions_table',1),(12,'2025_04_28_231449_add_is_cash_box_to_accounts_table',1),(13,'2025_05_01_110212_add_is_default_to_currencies_table',1),(14,'2025_05_01_110213_create_account_balances_table',1),(15,'2025_05_01_154726_add_currency_and_exchange_rate_to_vouchers_table',1),(16,'2025_05_01_232733_create_invoices_table',1),(17,'2025_05_02_000001_add_code_and_cashbox_to_accounts_table',1),(18,'2025_05_02_000138_create_items_table',1),(19,'2025_05_02_000139_create_customers_table',1),(20,'2025_05_02_000141_create_invoice_items_table',1),(21,'2025_05_02_001528_add_customer_id_to_invoices_table',1),(22,'2025_05_02_201011_create_salary_batches_table',1),(23,'2025_05_02_215900_create_accounting_settings_table',1),(24,'2025_05_02_223317_update_accounting_settings_for_currency_defaults',1),(25,'2025_05_03_000001_add_currency_to_accounts_table',1),(26,'2025_05_10_000000_add_invoice_id_to_vouchers_table',1),(27,'2025_05_10_000001_add_invoice_id_to_transactions_table',1),(28,'2025_05_10_100000_create_journal_entries_table',1),(29,'2025_05_10_100001_create_journal_entry_lines_table',1),(30,'2025_05_11_000001_create_employees_table',1),(31,'2025_05_11_000002_create_salaries_table',1),(32,'2025_05_11_000003_create_salary_payments_table',1);
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salary_batches`
--

LOCK TABLES `salary_batches` WRITE;
/*!40000 ALTER TABLE `salary_batches` DISABLE KEYS */;
INSERT INTO `salary_batches` VALUES (1,'2025-01','approved',1,1,'2025-05-03 18:41:16','2025-05-03 18:40:21','2025-05-03 18:41:16');
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
  `payment_date` date NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salary_payments`
--

LOCK TABLES `salary_payments` WRITE;
/*!40000 ALTER TABLE `salary_payments` DISABLE KEYS */;
INSERT INTO `salary_payments` VALUES (1,1,4,'2025-01',5000.00,0.00,200.00,4800.00,'2025-05-03','paid',3,2,'2025-05-03 18:40:21','2025-05-03 18:43:40');
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
INSERT INTO `sessions` VALUES ('vCjl01Ka1FqnjtTeRPAE6cE2Xa8uzdMiFM8h5dxB',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36','YTo2OntzOjY6Il90b2tlbiI7czo0MDoidzJOcldocjdXQU5aS3hxbzdBY21ySFpVVGFtcmJYSjg1cXZJQlRzUSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvam91cm5hbC1lbnRyaWVzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjQ6ImF1dGgiO2E6MTp7czoyMToicGFzc3dvcmRfY29uZmlybWVkX2F0IjtpOjE3NDYzMDU3MzM7fX0=',1746309109);
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
INSERT INTO `users` VALUES (1,'admin','admin@example.com',NULL,'$2y$12$SGmv5u4p79WGANqXfB54Befrb8iElvOT37O9bHe0lHP2kT2WBJNFu','CkcQIjhoVO3Gv8AbuIHqaVdXeaaug7SRHghvTKWox1OKHIudebLa8GzwHVHP','2025-05-02 20:31:37','2025-05-02 20:31:37');
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
INSERT INTO `vouchers` VALUES (1,'VCH-00001','receipt','IQD',1.000000,'2025-05-03',NULL,1,'moh',NULL,'2025-05-03 18:42:06','2025-05-03 18:42:06'),(2,'VCH-00002','payment','IQD',1.000000,'2025-05-03','صرف راتب شهر 2025-01 للموظف Mohammed hamdan',1,'Mohammed hamdan',NULL,'2025-05-03 18:43:40','2025-05-03 18:43:40'),(3,'VCH-00003','receipt','IQD',1.000000,'2025-05-03','سداد فاتورة INV-00001',1,'INV-00001',1,'2025-05-03 18:49:21','2025-05-03 18:49:21');
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

-- Dump completed on 2025-05-04  0:53:06
