-- MySQL dump 10.13  Distrib 8.0.42, for Linux (x86_64)
--
-- Host: localhost    Database: accaltatweertech_m2
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
  `tenant_id` bigint unsigned DEFAULT NULL,
  `account_id` bigint unsigned NOT NULL,
  `currency_id` bigint unsigned NOT NULL,
  `balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_balances_account_id_currency_id_unique` (`account_id`,`currency_id`),
  KEY `account_balances_currency_id_foreign` (`currency_id`),
  KEY `account_balances_tenant_id_index` (`tenant_id`),
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
-- Table structure for table `account_user`
--

DROP TABLE IF EXISTS `account_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `account_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `account_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tenant_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_user_user_id_account_id_unique` (`user_id`,`account_id`),
  KEY `account_user_account_id_foreign` (`account_id`),
  KEY `account_user_tenant_id_index` (`tenant_id`),
  CONSTRAINT `account_user_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `account_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account_user`
--

LOCK TABLES `account_user` WRITE;
/*!40000 ALTER TABLE `account_user` DISABLE KEYS */;
INSERT INTO `account_user` (`id`, `user_id`, `account_id`, `created_at`, `updated_at`, `tenant_id`) VALUES (1,2,3,NULL,NULL,NULL),(2,2,51,NULL,NULL,NULL);
/*!40000 ALTER TABLE `account_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `accounting_settings`
--

DROP TABLE IF EXISTS `accounting_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `accounting_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned DEFAULT NULL,
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `accounting_settings_tenant_id_index` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounting_settings`
--

LOCK TABLES `accounting_settings` WRITE;
/*!40000 ALTER TABLE `accounting_settings` DISABLE KEYS */;
INSERT INTO `accounting_settings` (`id`, `tenant_id`, `key`, `value`, `currency`, `created_at`, `updated_at`) VALUES (1,1,'default_sales_account','31','USD','2025-05-23 22:47:26','2025-05-23 22:47:26'),(2,1,'default_purchases_account','32','USD','2025-05-23 22:47:26','2025-05-23 22:47:26'),(3,1,'default_customers_account','7','USD','2025-05-23 22:47:26','2025-05-23 22:47:26'),(4,1,'default_suppliers_account','19','USD','2025-05-23 22:47:26','2025-05-23 22:47:26'),(5,1,'salary_expense_account','37','USD','2025-05-23 22:47:26','2025-05-23 22:47:26'),(6,1,'employee_payables_account','24','USD','2025-05-23 22:47:26','2025-05-23 22:47:26'),(7,1,'deductions_account','25','USD','2025-05-23 22:47:26','2025-05-23 22:47:26'),(8,1,'tax_account','47','USD','2025-05-23 22:47:26','2025-05-23 22:47:26'),(9,1,'inventory_account','8','USD','2025-05-23 22:47:26','2025-05-23 22:47:26'),(10,1,'main_bank_account','5','USD','2025-05-23 22:47:26','2025-05-23 22:47:26'),(11,1,'main_cash_account','2','USD','2025-05-23 22:47:26','2025-05-23 22:47:26'),(12,1,'default_sales_account','79','IQD','2025-05-23 22:47:27','2025-05-23 22:47:27'),(13,1,'default_purchases_account','80','IQD','2025-05-23 22:47:27','2025-05-23 22:47:27'),(14,1,'default_customers_account','55','IQD','2025-05-23 22:47:27','2025-05-23 22:47:27'),(15,1,'default_suppliers_account','67','IQD','2025-05-23 22:47:27','2025-05-23 22:47:27'),(16,1,'salary_expense_account','85','IQD','2025-05-23 22:47:27','2025-05-23 22:47:27'),(17,1,'employee_payables_account','72','IQD','2025-05-23 22:47:27','2025-05-23 22:47:27'),(18,1,'deductions_account','73','IQD','2025-05-23 22:47:27','2025-05-23 22:47:27'),(19,1,'tax_account','95','IQD','2025-05-23 22:47:27','2025-05-23 22:47:27'),(20,1,'inventory_account','56','IQD','2025-05-23 22:47:27','2025-05-23 22:47:27'),(21,1,'main_bank_account','53','IQD','2025-05-23 22:47:27','2025-05-23 22:47:27'),(22,1,'main_cash_account','50','IQD','2025-05-23 22:47:27','2025-05-23 22:47:27');
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
  `tenant_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `type` enum('asset','liability','revenue','expense','equity') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nature` enum('debit','credit') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_cash_box` tinyint(1) NOT NULL DEFAULT '0',
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_group` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `accounts_parent_id_foreign` (`parent_id`),
  KEY `accounts_tenant_id_index` (`tenant_id`),
  KEY `idx_tenant_group` (`tenant_id`,`is_group`),
  KEY `idx_parent_tenant` (`parent_id`,`tenant_id`),
  KEY `idx_code_tenant` (`code`,`tenant_id`),
  CONSTRAINT `accounts_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=162 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounts`
--

LOCK TABLES `accounts` WRITE;
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
INSERT INTO `accounts` (`id`, `tenant_id`, `name`, `code`, `parent_id`, `type`, `nature`, `is_cash_box`, `currency`, `is_group`, `created_at`, `updated_at`) VALUES (1,1,'الأصول المتداولة','1100',NULL,'asset',NULL,0,NULL,1,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(2,1,'الصندوق الرئيسي','11011',106,'asset','debit',1,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(3,1,'الصناديق الفرعية','11021',106,'asset','debit',1,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(4,1,'البنوك','1200',NULL,'asset',NULL,0,NULL,1,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(5,1,'البنك الرئيسي','12011',110,'asset','debit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(6,1,'حسابات بنكية أخرى','12021',110,'asset','debit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(7,1,'العملاء','13011',107,'asset','debit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(8,1,'المخزون','14011',108,'asset','debit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(9,1,'أوراق القبض','15011',107,'asset','debit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(10,1,'مصروفات مدفوعة مقدماً','16011',109,'asset','debit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(11,1,'الأصول الثابتة','1700',NULL,'asset',NULL,0,NULL,1,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(12,1,'الأراضي','17011',111,'asset','debit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(13,1,'المباني','17021',111,'asset','debit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(14,1,'الأثاث','17031',112,'asset','debit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(15,1,'المركبات','17041',112,'asset','debit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(16,1,'أجهزة ومعدات','17051',112,'asset','debit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(17,1,'مجمع إهلاك الأصول','17061',113,'asset','credit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(18,1,'الالتزامات المتداولة','2100',NULL,'liability',NULL,0,NULL,1,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(19,1,'الموردون','21011',114,'liability','credit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(20,1,'أوراق الدفع','21021',114,'liability','credit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(21,1,'قروض قصيرة الأجل','21031',116,'liability','credit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(22,1,'مصروفات مستحقة','21041',115,'liability','credit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(23,1,'ضرائب مستحقة','21051',115,'liability','credit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(24,1,'رواتب مستحقة الدفع','21061',115,'liability','credit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(25,1,'خصومات مستحقة للموظفين','22011',117,'liability','credit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(26,1,'الالتزامات طويلة الأجل','2300',NULL,'liability',NULL,0,NULL,1,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(27,1,'قروض طويلة الأجل','23011',118,'liability','credit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(28,1,'رأس المال','31001',121,'equity','credit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(29,1,'الأرباح المحتجزة','32001',122,'equity','credit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(30,1,'مسحوبات شخصية','33001',122,'equity','debit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(31,1,'المبيعات','41001',125,'revenue','credit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(32,1,'إيرادات الخدمات','42001',126,'revenue','credit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(33,1,'خصم مسموح به','43001',125,'revenue','debit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(34,1,'مردودات المبيعات','44001',125,'revenue','debit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(35,1,'تكلفة البضاعة المباعة','51001',130,'expense','debit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(36,1,'المصروفات التشغيلية','5200',NULL,'expense',NULL,0,NULL,1,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(37,1,'الرواتب والأجور','41011',127,'expense','debit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(38,1,'الإيجار','52011',128,'expense','debit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(39,1,'الكهرباء والماء','52021',128,'expense','debit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(40,1,'مصروفات إدارية وعمومية','52031',128,'expense','debit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(41,1,'مصروفات تسويق','52041',129,'expense','debit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(42,1,'مصروفات صيانة','52051',129,'expense','debit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(43,1,'مصروفات نقل','52061',129,'expense','debit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(44,1,'مصروفات اتصالات','52071',129,'expense','debit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(45,1,'مصروفات بنكية','52081',129,'expense','debit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(46,1,'مصروفات أخرى','52091',129,'expense','debit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(47,1,'ضريبة القيمة المضافة','53001',131,'expense','credit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(48,1,'ضريبة الدخل','53011',131,'expense','credit',0,'USD',0,'2025-05-23 22:47:25','2025-06-07 21:42:27'),(50,1,'الصندوق الرئيسي','1101',106,'asset','debit',1,'IQD',0,'2025-05-23 22:47:26','2025-06-07 21:42:27'),(51,1,'الصناديق الفرعية','1102',106,'asset','debit',1,'IQD',0,'2025-05-23 22:47:26','2025-06-07 21:42:27'),(53,1,'البنك الرئيسي','1201',110,'asset','debit',0,'IQD',0,'2025-05-23 22:47:26','2025-06-07 21:42:27'),(54,1,'حسابات بنكية أخرى','1202',110,'asset','debit',0,'IQD',0,'2025-05-23 22:47:26','2025-06-07 21:42:27'),(55,1,'العملاء','1301',107,'asset','debit',0,'IQD',0,'2025-05-23 22:47:26','2025-05-23 22:47:26'),(56,1,'المخزون','1401',108,'asset','debit',0,'IQD',0,'2025-05-23 22:47:26','2025-05-23 22:47:26'),(57,1,'أوراق القبض','1501',107,'asset','debit',0,'IQD',0,'2025-05-23 22:47:26','2025-05-23 22:47:26'),(58,1,'مصروفات مدفوعة مقدماً','1601',109,'asset','debit',0,'IQD',0,'2025-05-23 22:47:26','2025-05-23 22:47:26'),(60,1,'الأراضي','1701',111,'asset','debit',0,'IQD',0,'2025-05-23 22:47:26','2025-06-07 21:42:27'),(61,1,'المباني','1702',111,'asset','debit',0,'IQD',0,'2025-05-23 22:47:26','2025-06-07 21:42:27'),(62,1,'الأثاث','1703',112,'asset','debit',0,'IQD',0,'2025-05-23 22:47:26','2025-06-07 21:42:27'),(63,1,'المركبات','1704',112,'asset','debit',0,'IQD',0,'2025-05-23 22:47:26','2025-06-07 21:42:27'),(64,1,'أجهزة ومعدات','1705',112,'asset','debit',0,'IQD',0,'2025-05-23 22:47:26','2025-06-07 21:42:27'),(65,1,'مجمع إهلاك الأصول','1706',113,'asset','credit',0,'IQD',0,'2025-05-23 22:47:26','2025-06-07 21:42:27'),(67,1,'الموردون','2101',114,'liability','credit',0,'IQD',0,'2025-05-23 22:47:26','2025-06-07 21:42:27'),(68,1,'أوراق الدفع','2102',114,'liability','credit',0,'IQD',0,'2025-05-23 22:47:26','2025-06-07 21:42:27'),(69,1,'قروض قصيرة الأجل','2103',116,'liability','credit',0,'IQD',0,'2025-05-23 22:47:26','2025-06-07 21:42:27'),(70,1,'مصروفات مستحقة','2104',115,'liability','credit',0,'IQD',0,'2025-05-23 22:47:26','2025-06-07 21:42:27'),(71,1,'ضرائب مستحقة','2105',115,'liability','credit',0,'IQD',0,'2025-05-23 22:47:26','2025-06-07 21:42:27'),(72,1,'رواتب مستحقة الدفع','2106',115,'liability','credit',0,'IQD',0,'2025-05-23 22:47:26','2025-06-07 21:42:27'),(73,1,'خصومات مستحقة للموظفين','2201',117,'liability','credit',0,'IQD',0,'2025-05-23 22:47:26','2025-05-23 22:47:26'),(75,1,'قروض طويلة الأجل','2301',118,'liability','credit',0,'IQD',0,'2025-05-23 22:47:26','2025-06-07 21:42:27'),(76,1,'رأس المال','3100',121,'equity','credit',0,'IQD',0,'2025-05-23 22:47:26','2025-06-08 00:19:28'),(77,1,'الأرباح المحتجزة','3200',122,'equity','credit',0,'IQD',0,'2025-05-23 22:47:26','2025-05-23 22:47:26'),(78,1,'مسحوبات شخصية','3300',122,'equity','debit',0,'IQD',0,'2025-05-23 22:47:26','2025-06-08 00:20:49'),(79,1,'المبيعات','4100',125,'revenue','credit',0,'IQD',0,'2025-05-23 22:47:26','2025-05-23 22:47:26'),(80,1,'إيرادات الخدمات','4200',126,'revenue','credit',0,'IQD',0,'2025-05-23 22:47:26','2025-05-23 22:47:26'),(81,1,'خصم مسموح به','4300',125,'revenue','debit',0,'IQD',0,'2025-05-23 22:47:26','2025-05-23 22:47:26'),(82,1,'مردودات المبيعات','4400',125,'revenue','debit',0,'IQD',0,'2025-05-23 22:47:26','2025-05-23 22:47:26'),(83,1,'تكلفة البضاعة المباعة','5100',130,'expense','debit',0,'IQD',0,'2025-05-23 22:47:26','2025-05-23 22:47:26'),(85,1,'الرواتب والأجور','4101',127,'expense','debit',0,'IQD',0,'2025-05-23 22:47:26','2025-06-07 21:42:27'),(86,1,'الإيجار','5201',128,'expense','debit',0,'IQD',0,'2025-05-23 22:47:26','2025-06-07 21:42:27'),(87,1,'الكهرباء والماء','5202',128,'expense','debit',0,'IQD',0,'2025-05-23 22:47:26','2025-06-07 21:42:27'),(88,1,'مصروفات إدارية وعمومية','5203',128,'expense','debit',0,'IQD',0,'2025-05-23 22:47:26','2025-06-07 21:42:27'),(89,1,'مصروفات تسويق','5204',129,'expense','debit',0,'IQD',0,'2025-05-23 22:47:26','2025-06-07 21:42:27'),(90,1,'مصروفات صيانة','5205',129,'expense','debit',0,'IQD',0,'2025-05-23 22:47:26','2025-06-07 21:42:27'),(91,1,'مصروفات نقل','5206',129,'expense','debit',0,'IQD',0,'2025-05-23 22:47:26','2025-06-07 21:42:27'),(92,1,'مصروفات اتصالات','5207',129,'expense','debit',0,'IQD',0,'2025-05-23 22:47:26','2025-06-07 21:42:27'),(93,1,'مصروفات بنكية','5208',129,'expense','debit',0,'IQD',0,'2025-05-23 22:47:26','2025-06-07 21:42:27'),(94,1,'مصروفات أخرى','5209',129,'expense','debit',0,'IQD',0,'2025-05-23 22:47:26','2025-06-07 21:42:27'),(95,1,'ضريبة القيمة المضافة','5300',131,'expense','credit',0,'IQD',0,'2025-05-23 22:47:26','2025-05-23 22:47:26'),(96,1,'ضريبة الدخل','5301',131,'expense','credit',0,'IQD',0,'2025-05-23 22:47:26','2025-05-23 22:47:26'),(97,1,'محمد حمدان','2107',115,'liability','credit',0,'IQD',0,'2025-05-24 11:54:22','2025-06-07 21:42:27'),(99,1,'تسويات  حقوق ملكية ٢٠٢٥','5001',124,'equity','credit',0,'IQD',0,'2025-06-06 23:17:21','2025-06-06 23:17:21'),(100,1,'ارباح مرحلة','5002',124,'equity','credit',0,'IQD',0,'2025-06-07 00:46:16','2025-06-07 00:46:16'),(102,1,'سلف حجي عمار','5101',119,'liability','credit',0,'IQD',0,'2025-06-07 01:00:37','2025-06-07 01:14:45'),(103,1,'سلف محمد حمدان','5102',119,'liability','credit',0,'IQD',0,'2025-06-07 01:01:00','2025-06-07 01:15:02'),(104,1,'الإيرادات','4000',NULL,'revenue','credit',0,NULL,1,'2025-06-07 22:56:10','2025-06-07 22:56:10'),(105,1,'حقوق الملكية','3000',NULL,'equity','credit',0,NULL,1,'2025-06-07 22:56:10','2025-06-07 22:56:10'),(106,1,'الصناديق النقدية','1110',1,'asset','debit',0,NULL,1,'2025-06-07 22:56:10','2025-06-07 22:56:10'),(107,1,'العملاء والذمم المدينة','1120',1,'asset','debit',0,NULL,1,'2025-06-07 22:56:10','2025-06-07 22:56:10'),(108,1,'المخزون والبضائع','1130',1,'asset','debit',0,NULL,1,'2025-06-07 22:56:10','2025-06-07 22:56:10'),(109,1,'المدفوعات مقدماً','1140',1,'asset','debit',0,NULL,1,'2025-06-07 22:56:10','2025-06-07 22:56:10'),(110,1,'الحسابات البنكية','1210',4,'asset','debit',0,NULL,1,'2025-06-07 22:56:10','2025-06-07 22:56:10'),(111,1,'الأراضي والمباني','1710',11,'asset','debit',0,NULL,1,'2025-06-07 22:56:10','2025-06-07 22:56:10'),(112,1,'الأثاث والمعدات','1720',11,'asset','debit',0,NULL,1,'2025-06-07 22:56:10','2025-06-07 22:56:10'),(113,1,'الإهلاك المتراكم','1730',11,'asset','credit',0,NULL,1,'2025-06-07 22:56:10','2025-06-07 22:56:10'),(114,1,'الموردون والدائنون','2110',18,'liability','credit',0,NULL,1,'2025-06-07 22:56:10','2025-06-07 22:56:10'),(115,1,'الالتزامات المستحقة','2120',18,'liability','credit',0,NULL,1,'2025-06-07 22:56:10','2025-06-07 22:56:10'),(116,1,'القروض قصيرة الأجل','2130',18,'liability','credit',0,NULL,1,'2025-06-07 22:56:10','2025-06-07 22:56:10'),(117,1,'خصومات الموظفين','2140',18,'liability','credit',0,NULL,1,'2025-06-07 22:56:10','2025-06-07 22:56:10'),(118,1,'القروض طويلة الأجل','2310',26,'liability','credit',0,NULL,1,'2025-06-07 22:56:10','2025-06-07 22:56:10'),(119,1,'السلف والقروض الشخصية','2320',26,'liability','credit',0,NULL,1,'2025-06-07 22:56:10','2025-06-07 22:56:10'),(120,1,'رأس المال','3110',105,'equity','credit',0,NULL,1,'2025-06-07 22:56:10','2025-06-07 22:56:10'),(121,1,'رأس المال المدفوع','3111',120,'equity','credit',0,NULL,1,'2025-06-07 22:56:10','2025-06-07 22:56:10'),(122,1,'الأرباح والمسحوبات','3120',120,'equity','credit',0,NULL,1,'2025-06-07 22:56:10','2025-06-07 22:56:10'),(124,1,'التسويات والأرباح المرحلة','3311',105,'equity',NULL,0,NULL,1,'2025-06-07 22:56:10','2025-06-08 00:14:10'),(125,1,'إيرادات المبيعات','4010',104,'revenue','credit',0,NULL,1,'2025-06-07 22:56:10','2025-06-07 22:56:10'),(126,1,'إيرادات الخدمات','4020',104,'revenue','credit',0,NULL,1,'2025-06-07 22:56:10','2025-06-07 22:56:10'),(127,1,'الرواتب والأجور','5210',36,'expense','debit',0,NULL,1,'2025-06-07 22:56:10','2025-06-07 22:56:10'),(128,1,'المصروفات الإدارية','5220',36,'expense','debit',0,NULL,1,'2025-06-07 22:56:10','2025-06-07 22:56:10'),(129,1,'مصروفات التشغيل','5230',36,'expense','debit',0,NULL,1,'2025-06-07 22:56:10','2025-06-07 22:56:10'),(130,1,'تكلفة البضاعة المباعة','5240',36,'expense','debit',0,NULL,1,'2025-06-07 22:56:10','2025-06-07 22:56:10'),(131,1,'الضرائب والرسوم','5250',36,'expense','debit',0,NULL,1,'2025-06-07 22:56:10','2025-06-07 22:56:10'),(160,NULL,'سيرفرات وخدمات اونلاين','52092',129,'expense','debit',0,'IQD',0,'2025-06-08 09:26:17','2025-06-08 09:26:17'),(161,NULL,'سيرفرات وخدمات اونلاين','52093',129,'expense','debit',0,'USD',0,'2025-06-08 09:27:08','2025-06-08 09:27:08');
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
  `tenant_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `branches_tenant_id_index` (`tenant_id`)
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
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES ('accountingsystem_cache_admin@example.com|217.142.22.193','i:1;',1750078025),('accountingsystem_cache_admin@example.com|217.142.22.193:timer','i:1750078025;',1750078025),('accountingsystem_cache_currencies','O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:2:{i:0;O:19:\"App\\Models\\Currency\":31:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:10:\"currencies\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:1;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:9:{s:2:\"id\";i:1;s:9:\"tenant_id\";i:1;s:4:\"name\";s:23:\"دولار أمريكي\";s:4:\"code\";s:3:\"USD\";s:6:\"symbol\";s:1:\"$\";s:13:\"exchange_rate\";s:11:\"1412.500000\";s:10:\"is_default\";i:0;s:10:\"created_at\";s:19:\"2025-05-24 01:47:21\";s:10:\"updated_at\";s:19:\"2025-05-31 13:00:50\";}s:11:\"\0*\0original\";a:9:{s:2:\"id\";i:1;s:9:\"tenant_id\";i:1;s:4:\"name\";s:23:\"دولار أمريكي\";s:4:\"code\";s:3:\"USD\";s:6:\"symbol\";s:1:\"$\";s:13:\"exchange_rate\";s:11:\"1412.500000\";s:10:\"is_default\";i:0;s:10:\"created_at\";s:19:\"2025-05-24 01:47:21\";s:10:\"updated_at\";s:19:\"2025-05-31 13:00:50\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:10:\"is_default\";s:7:\"boolean\";s:13:\"exchange_rate\";s:9:\"decimal:6\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:5:{i:0;s:4:\"name\";i:1;s:4:\"code\";i:2;s:6:\"symbol\";i:3;s:13:\"exchange_rate\";i:4;s:10:\"is_default\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:1;O:19:\"App\\Models\\Currency\":31:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:10:\"currencies\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:1;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:9:{s:2:\"id\";i:2;s:9:\"tenant_id\";i:1;s:4:\"name\";s:21:\"دينار عراقي\";s:4:\"code\";s:3:\"IQD\";s:6:\"symbol\";s:3:\"IQD\";s:13:\"exchange_rate\";s:8:\"1.000000\";s:10:\"is_default\";i:1;s:10:\"created_at\";s:19:\"2025-05-24 01:47:21\";s:10:\"updated_at\";s:19:\"2025-05-24 01:47:21\";}s:11:\"\0*\0original\";a:9:{s:2:\"id\";i:2;s:9:\"tenant_id\";i:1;s:4:\"name\";s:21:\"دينار عراقي\";s:4:\"code\";s:3:\"IQD\";s:6:\"symbol\";s:3:\"IQD\";s:13:\"exchange_rate\";s:8:\"1.000000\";s:10:\"is_default\";i:1;s:10:\"created_at\";s:19:\"2025-05-24 01:47:21\";s:10:\"updated_at\";s:19:\"2025-05-24 01:47:21\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:10:\"is_default\";s:7:\"boolean\";s:13:\"exchange_rate\";s:9:\"decimal:6\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:5:{i:0;s:4:\"name\";i:1;s:4:\"code\";i:2;s:6:\"symbol\";i:3;s:13:\"exchange_rate\";i:4;s:10:\"is_default\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}',1749342339),('accountingsystem_cache_spatie.permission.cache','a:3:{s:5:\"alias\";a:5:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:9:\"tenant_id\";s:1:\"c\";s:4:\"name\";s:1:\"d\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:74:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";N;s:1:\"c\";s:10:\"view_users\";s:1:\"d\";s:3:\"web\";}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";N;s:1:\"c\";s:8:\"add_user\";s:1:\"d\";s:3:\"web\";}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";N;s:1:\"c\";s:9:\"edit_user\";s:1:\"d\";s:3:\"web\";}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";N;s:1:\"c\";s:11:\"delete_user\";s:1:\"d\";s:3:\"web\";}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";N;s:1:\"c\";s:10:\"view_roles\";s:1:\"d\";s:3:\"web\";}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";N;s:1:\"c\";s:8:\"add_role\";s:1:\"d\";s:3:\"web\";}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";N;s:1:\"c\";s:9:\"edit_role\";s:1:\"d\";s:3:\"web\";}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";N;s:1:\"c\";s:11:\"delete_role\";s:1:\"d\";s:3:\"web\";}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";N;s:1:\"c\";s:16:\"view_permissions\";s:1:\"d\";s:3:\"web\";}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";N;s:1:\"c\";s:14:\"add_permission\";s:1:\"d\";s:3:\"web\";}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";N;s:1:\"c\";s:15:\"edit_permission\";s:1:\"d\";s:3:\"web\";}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";N;s:1:\"c\";s:17:\"delete_permission\";s:1:\"d\";s:3:\"web\";}i:12;a:5:{s:1:\"a\";i:13;s:1:\"b\";N;s:1:\"c\";s:13:\"view_accounts\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";N;s:1:\"c\";s:11:\"add_account\";s:1:\"d\";s:3:\"web\";}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";N;s:1:\"c\";s:12:\"edit_account\";s:1:\"d\";s:3:\"web\";}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";N;s:1:\"c\";s:14:\"delete_account\";s:1:\"d\";s:3:\"web\";}i:16;a:5:{s:1:\"a\";i:17;s:1:\"b\";N;s:1:\"c\";s:13:\"view_invoices\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:17;a:5:{s:1:\"a\";i:18;s:1:\"b\";N;s:1:\"c\";s:11:\"add_invoice\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:18;a:5:{s:1:\"a\";i:19;s:1:\"b\";N;s:1:\"c\";s:12:\"edit_invoice\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:19;a:5:{s:1:\"a\";i:20;s:1:\"b\";N;s:1:\"c\";s:14:\"delete_invoice\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:20;a:5:{s:1:\"a\";i:21;s:1:\"b\";N;s:1:\"c\";s:11:\"pay_invoice\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:21;a:5:{s:1:\"a\";i:22;s:1:\"b\";N;s:1:\"c\";s:13:\"print_invoice\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:22;a:5:{s:1:\"a\";i:23;s:1:\"b\";N;s:1:\"c\";s:13:\"view_vouchers\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:23;a:5:{s:1:\"a\";i:24;s:1:\"b\";N;s:1:\"c\";s:11:\"add_voucher\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:24;a:4:{s:1:\"a\";i:25;s:1:\"b\";N;s:1:\"c\";s:12:\"edit_voucher\";s:1:\"d\";s:3:\"web\";}i:25;a:4:{s:1:\"a\";i:26;s:1:\"b\";N;s:1:\"c\";s:14:\"delete_voucher\";s:1:\"d\";s:3:\"web\";}i:26;a:5:{s:1:\"a\";i:27;s:1:\"b\";N;s:1:\"c\";s:13:\"print_voucher\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:27;a:4:{s:1:\"a\";i:28;s:1:\"b\";N;s:1:\"c\";s:17:\"view_all_vouchers\";s:1:\"d\";s:3:\"web\";}i:28;a:5:{s:1:\"a\";i:29;s:1:\"b\";N;s:1:\"c\";s:15:\"cancel_vouchers\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:29;a:5:{s:1:\"a\";i:30;s:1:\"b\";N;s:1:\"c\";s:17:\"view_transactions\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:30;a:4:{s:1:\"a\";i:31;s:1:\"b\";N;s:1:\"c\";s:15:\"add_transaction\";s:1:\"d\";s:3:\"web\";}i:31;a:4:{s:1:\"a\";i:32;s:1:\"b\";N;s:1:\"c\";s:16:\"edit_transaction\";s:1:\"d\";s:3:\"web\";}i:32;a:4:{s:1:\"a\";i:33;s:1:\"b\";N;s:1:\"c\";s:18:\"delete_transaction\";s:1:\"d\";s:3:\"web\";}i:33;a:5:{s:1:\"a\";i:34;s:1:\"b\";N;s:1:\"c\";s:14:\"view_customers\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:34;a:5:{s:1:\"a\";i:35;s:1:\"b\";N;s:1:\"c\";s:12:\"add_customer\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:35;a:5:{s:1:\"a\";i:36;s:1:\"b\";N;s:1:\"c\";s:13:\"edit_customer\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:36;a:5:{s:1:\"a\";i:37;s:1:\"b\";N;s:1:\"c\";s:15:\"delete_customer\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:37;a:5:{s:1:\"a\";i:38;s:1:\"b\";N;s:1:\"c\";s:10:\"view_items\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:38;a:5:{s:1:\"a\";i:39;s:1:\"b\";N;s:1:\"c\";s:8:\"add_item\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:39;a:5:{s:1:\"a\";i:40;s:1:\"b\";N;s:1:\"c\";s:9:\"edit_item\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:40;a:5:{s:1:\"a\";i:41;s:1:\"b\";N;s:1:\"c\";s:11:\"delete_item\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:41;a:5:{s:1:\"a\";i:42;s:1:\"b\";N;s:1:\"c\";s:14:\"view_employees\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:42;a:5:{s:1:\"a\";i:43;s:1:\"b\";N;s:1:\"c\";s:12:\"add_employee\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:43;a:5:{s:1:\"a\";i:44;s:1:\"b\";N;s:1:\"c\";s:13:\"edit_employee\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:44;a:5:{s:1:\"a\";i:45;s:1:\"b\";N;s:1:\"c\";s:15:\"delete_employee\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:45;a:5:{s:1:\"a\";i:46;s:1:\"b\";N;s:1:\"c\";s:13:\"view_salaries\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:46;a:5:{s:1:\"a\";i:47;s:1:\"b\";N;s:1:\"c\";s:10:\"add_salary\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:47;a:5:{s:1:\"a\";i:48;s:1:\"b\";N;s:1:\"c\";s:11:\"edit_salary\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:48;a:5:{s:1:\"a\";i:49;s:1:\"b\";N;s:1:\"c\";s:13:\"delete_salary\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:49;a:5:{s:1:\"a\";i:50;s:1:\"b\";N;s:1:\"c\";s:20:\"view_salary_payments\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:50;a:5:{s:1:\"a\";i:51;s:1:\"b\";N;s:1:\"c\";s:18:\"add_salary_payment\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:51;a:5:{s:1:\"a\";i:52;s:1:\"b\";N;s:1:\"c\";s:19:\"edit_salary_payment\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:52;a:5:{s:1:\"a\";i:53;s:1:\"b\";N;s:1:\"c\";s:21:\"delete_salary_payment\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:53;a:5:{s:1:\"a\";i:54;s:1:\"b\";N;s:1:\"c\";s:19:\"view_salary_batches\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:54;a:5:{s:1:\"a\";i:55;s:1:\"b\";N;s:1:\"c\";s:16:\"add_salary_batch\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:55;a:5:{s:1:\"a\";i:56;s:1:\"b\";N;s:1:\"c\";s:17:\"edit_salary_batch\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:56;a:5:{s:1:\"a\";i:57;s:1:\"b\";N;s:1:\"c\";s:19:\"delete_salary_batch\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:57;a:5:{s:1:\"a\";i:58;s:1:\"b\";N;s:1:\"c\";s:15:\"view_currencies\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:58;a:4:{s:1:\"a\";i:59;s:1:\"b\";N;s:1:\"c\";s:12:\"add_currency\";s:1:\"d\";s:3:\"web\";}i:59;a:5:{s:1:\"a\";i:60;s:1:\"b\";N;s:1:\"c\";s:13:\"edit_currency\";s:1:\"d\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:60;a:4:{s:1:\"a\";i:61;s:1:\"b\";N;s:1:\"c\";s:15:\"delete_currency\";s:1:\"d\";s:3:\"web\";}i:61;a:4:{s:1:\"a\";i:62;s:1:\"b\";N;s:1:\"c\";s:13:\"view_branches\";s:1:\"d\";s:3:\"web\";}i:62;a:4:{s:1:\"a\";i:63;s:1:\"b\";N;s:1:\"c\";s:10:\"add_branch\";s:1:\"d\";s:3:\"web\";}i:63;a:4:{s:1:\"a\";i:64;s:1:\"b\";N;s:1:\"c\";s:11:\"edit_branch\";s:1:\"d\";s:3:\"web\";}i:64;a:4:{s:1:\"a\";i:65;s:1:\"b\";N;s:1:\"c\";s:13:\"delete_branch\";s:1:\"d\";s:3:\"web\";}i:65;a:4:{s:1:\"a\";i:66;s:1:\"b\";N;s:1:\"c\";s:13:\"view_settings\";s:1:\"d\";s:3:\"web\";}i:66;a:4:{s:1:\"a\";i:67;s:1:\"b\";N;s:1:\"c\";s:13:\"edit_settings\";s:1:\"d\";s:3:\"web\";}i:67;a:4:{s:1:\"a\";i:68;s:1:\"b\";N;s:1:\"c\";s:15:\"manage_settings\";s:1:\"d\";s:3:\"web\";}i:68;a:4:{s:1:\"a\";i:69;s:1:\"b\";N;s:1:\"c\";s:20:\"view_journal_entries\";s:1:\"d\";s:3:\"web\";}i:69;a:4:{s:1:\"a\";i:70;s:1:\"b\";N;s:1:\"c\";s:17:\"add_journal_entry\";s:1:\"d\";s:3:\"web\";}i:70;a:4:{s:1:\"a\";i:71;s:1:\"b\";N;s:1:\"c\";s:18:\"edit_journal_entry\";s:1:\"d\";s:3:\"web\";}i:71;a:4:{s:1:\"a\";i:72;s:1:\"b\";N;s:1:\"c\";s:20:\"delete_journal_entry\";s:1:\"d\";s:3:\"web\";}i:72;a:4:{s:1:\"a\";i:73;s:1:\"b\";N;s:1:\"c\";s:24:\"view_all_journal_entries\";s:1:\"d\";s:3:\"web\";}i:73;a:4:{s:1:\"a\";i:74;s:1:\"b\";N;s:1:\"c\";s:22:\"cancel_journal_entries\";s:1:\"d\";s:3:\"web\";}}s:5:\"roles\";a:1:{i:0;a:4:{s:1:\"a\";i:2;s:1:\"b\";N;s:1:\"c\";s:12:\"Fatema saleh\";s:1:\"d\";s:3:\"web\";}}}',1752233670);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `tenant_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbol` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exchange_rate` decimal(15,6) NOT NULL DEFAULT '1.000000',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `currencies_code_unique` (`code`),
  KEY `currencies_tenant_id_index` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `currencies`
--

LOCK TABLES `currencies` WRITE;
/*!40000 ALTER TABLE `currencies` DISABLE KEYS */;
INSERT INTO `currencies` (`id`, `tenant_id`, `name`, `code`, `symbol`, `exchange_rate`, `is_default`, `created_at`, `updated_at`) VALUES (1,1,'دولار أمريكي','USD','$',1420.000000,0,'2025-05-23 22:47:21','2025-07-09 11:27:55'),(2,1,'دينار عراقي','IQD','IQD',1.000000,1,'2025-05-23 22:47:21','2025-07-09 11:31:47');
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
  `tenant_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `account_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_email_unique` (`email`),
  KEY `customers_account_id_foreign` (`account_id`),
  KEY `customers_tenant_id_index` (`tenant_id`),
  CONSTRAINT `customers_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` (`id`, `tenant_id`, `name`, `email`, `phone`, `address`, `account_id`, `created_at`, `updated_at`) VALUES (1,1,'fusteka Group','mohammed@test.com',NULL,NULL,55,'2025-05-24 07:26:38','2025-05-24 07:26:38'),(2,1,'Afieat','afieat@altatweer',NULL,NULL,55,'2025-05-24 07:26:52','2025-05-24 07:26:52'),(3,1,'shopini stor','shopinisto@altatweer.com',NULL,NULL,55,'2025-05-24 07:27:05','2025-05-24 07:27:05'),(4,1,'shoppini Express','info@shopiniexpress.com',NULL,NULL,55,'2025-05-24 07:27:17','2025-05-24 07:27:17'),(5,1,'Basra Amazon','mohamed.thamer1990@gmail.com',NULL,'العراق/ البصرة',55,'2025-05-24 07:28:00','2025-05-24 07:28:00'),(6,1,'Recycling','hassan@baadya.com',NULL,NULL,55,'2025-05-24 07:47:14','2025-05-24 07:47:14'),(7,1,'Healthy kitchen','ridhafusteka@gmail.com',NULL,NULL,55,'2025-05-24 07:48:17','2025-05-24 07:48:17');
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
  `tenant_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `employee_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `job_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `status` enum('active','inactive','terminated') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_employee_number_unique` (`employee_number`),
  KEY `employees_user_id_foreign` (`user_id`),
  KEY `employees_tenant_id_index` (`tenant_id`),
  CONSTRAINT `employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` (`id`, `tenant_id`, `name`, `employee_number`, `department`, `job_title`, `hire_date`, `status`, `currency`, `user_id`, `created_at`, `updated_at`) VALUES (1,1,'zainab moayed','1','project management','coordinator','2024-11-01','active','IQD',NULL,'2025-05-24 07:07:53','2025-05-25 08:44:58'),(2,1,'fatmasaleh','2','administrative','administrative','2024-05-19','active','IQD',NULL,'2025-05-24 07:08:36','2025-05-25 08:45:18'),(3,1,'nabaa','3','project management','coordinator','2025-02-20','active','IQD',NULL,'2025-05-24 07:10:00','2025-05-25 08:46:06'),(4,1,'mustafa waheed','4','project management','coordinator','2023-10-23','active','IQD',NULL,'2025-05-24 07:10:48','2025-05-25 08:46:38'),(5,1,'nafea amaad','5','project management','coordinator','2024-05-19','active','IQD',NULL,'2025-05-24 07:11:32','2025-05-25 08:47:03'),(6,1,'umer','6','Developer','Developer','2000-02-01','active','USD',NULL,'2025-05-24 07:12:15','2025-05-24 07:12:15'),(7,1,'usama','7','Developer','Developer','2022-11-13','active','USD',NULL,'2025-05-24 07:13:47','2025-05-25 09:23:58'),(8,1,'sadqain','8','Developer','Developer','2021-09-15','active','USD',NULL,'2025-05-24 07:18:21','2025-05-25 10:27:19'),(9,1,'siraj','9','Developer','Developer','2023-11-26','active','USD',NULL,'2025-05-24 07:18:56','2025-05-25 09:18:14'),(10,1,'firas','10','Developer','Developer','2023-12-23','active','USD',NULL,'2025-05-24 07:19:24','2025-05-25 08:50:06'),(11,1,'tareq','11','Developer','Developer','2023-03-08','active','USD',NULL,'2025-05-24 07:19:46','2025-05-25 08:59:05'),(12,1,'naveed','12','Developer','Developer','2022-05-06','active','USD',NULL,'2025-05-24 07:20:11','2025-05-25 09:16:26'),(13,1,'Mohammad hassnain','13','Developer','Developer','2023-04-01','active','USD',NULL,'2025-05-24 07:21:33','2025-05-25 10:26:36'),(14,1,'Mohamed jaber','14','Developer','Developer','2025-05-01','active','USD',NULL,'2025-05-24 07:22:10','2025-05-25 08:58:37'),(15,1,'Hothaifa Jaber','15','Developer','Developer','2023-08-01','active','USD',NULL,'2025-05-24 07:22:29','2025-05-25 08:57:31'),(16,1,'Hisham','16','Developer','Developer','2025-05-01','active','USD',NULL,'2025-05-24 07:22:54','2025-05-25 08:57:58'),(17,1,'ateeb','17','Developer','Developer','2000-01-01','active','USD',NULL,'2025-05-24 07:24:13','2025-05-24 07:24:13'),(18,1,'arkan','18','Developer','Developer','2025-05-01','active','USD',NULL,'2025-05-24 07:24:28','2025-06-11 11:42:33'),(19,1,'kaly','19','Developer','Developer','2022-09-21','active','USD',NULL,'2025-05-24 07:24:45','2025-05-25 08:51:38'),(20,1,'ibraham','20','Developer','Developer','2022-08-10','active','USD',NULL,'2025-05-24 07:25:21','2025-05-25 08:52:00'),(21,1,'علي صادق','21','service employee','service employee','2025-06-01','active','IQD',NULL,'2025-06-04 11:51:55','2025-06-22 06:19:03'),(22,1,'فرهاد حسين','22','Housekeeper','Housekeeper','2025-06-01','active','IQD',NULL,'2025-06-04 11:53:18','2025-06-22 06:49:23'),(23,1,'zahraa  Jumaah','23','project management','coordinator','2025-06-01','active','IQD',NULL,'2025-06-04 11:57:52','2025-06-04 11:58:02');
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
  `tenant_id` bigint unsigned DEFAULT NULL,
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
  KEY `invoice_items_tenant_id_index` (`tenant_id`),
  CONSTRAINT `invoice_items_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoice_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_items`
--

LOCK TABLES `invoice_items` WRITE;
/*!40000 ALTER TABLE `invoice_items` DISABLE KEYS */;
INSERT INTO `invoice_items` (`id`, `tenant_id`, `invoice_id`, `item_id`, `quantity`, `unit_price`, `line_total`, `created_at`, `updated_at`) VALUES (1,1,1,1,1,10361.00,10361.00,'2025-05-29 12:36:52','2025-05-29 12:36:52'),(2,1,2,1,2,2500.00,5000.00,'2025-06-07 20:45:49','2025-06-07 20:45:49'),(3,1,2,2,2,1500.00,3000.00,'2025-06-07 20:45:49','2025-06-07 20:45:49'),(4,NULL,3,2,1,16500.00,16500.00,'2025-06-10 09:53:16','2025-06-10 09:53:16'),(5,NULL,4,2,1,3000.00,3000.00,'2025-06-11 11:51:20','2025-06-11 11:51:20'),(6,NULL,5,2,1,500.00,500.00,'2025-06-12 11:43:45','2025-06-12 11:43:45'),(7,NULL,6,3,1,1587.00,1587.00,'2025-06-25 12:51:48','2025-06-25 12:51:48'),(8,NULL,7,1,1,10361.00,10361.00,'2025-06-30 11:47:31','2025-06-30 11:47:31'),(9,NULL,8,2,1,13500.00,13500.00,'2025-06-30 11:49:21','2025-06-30 11:49:21');
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
  `tenant_id` bigint unsigned DEFAULT NULL,
  `invoice_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `total` decimal(15,2) NOT NULL,
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IQD',
  `exchange_rate` decimal(15,6) NOT NULL DEFAULT '1.000000',
  `status` enum('draft','unpaid','partial','paid','canceled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  KEY `invoices_created_by_foreign` (`created_by`),
  KEY `invoices_customer_id_foreign` (`customer_id`),
  KEY `invoices_tenant_id_index` (`tenant_id`),
  CONSTRAINT `invoices_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoices_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
INSERT INTO `invoices` (`id`, `tenant_id`, `invoice_number`, `customer_id`, `date`, `total`, `currency`, `exchange_rate`, `status`, `created_by`, `created_at`, `updated_at`) VALUES (1,1,'INV-00001',1,'2025-05-29',10361.00,'USD',1412.500000,'paid',2,'2025-05-29 12:36:52','2025-05-29 12:37:24'),(2,1,'INV-00002',4,'2025-05-31',8500.00,'USD',1412.500000,'paid',1,'2025-06-07 20:45:49','2025-06-07 20:47:10'),(3,NULL,'INV-00003',1,'2025-05-25',16500.00,'USD',1412.500000,'paid',2,'2025-06-10 09:53:16','2025-06-10 10:20:10'),(4,NULL,'INV-00004',2,'2025-06-11',3000.00,'USD',1420.000000,'paid',2,'2025-06-11 11:51:20','2025-06-11 11:51:50'),(5,NULL,'INV-00005',3,'2025-06-01',500.00,'USD',1420.000000,'paid',2,'2025-06-12 11:43:45','2025-06-12 11:45:41'),(6,NULL,'INV-00006',1,'2025-05-31',1587.00,'USD',1420.000000,'paid',2,'2025-06-25 12:51:48','2025-06-25 12:52:02'),(7,NULL,'INV-00007',1,'2025-06-30',10361.00,'USD',1415.000000,'paid',2,'2025-06-30 11:47:31','2025-06-30 11:48:14'),(8,NULL,'INV-00008',1,'2025-06-30',13500.00,'USD',1415.000000,'paid',2,'2025-06-30 11:49:21','2025-06-30 11:49:43');
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
  `tenant_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('product','service') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `items_tenant_id_index` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` (`id`, `tenant_id`, `name`, `type`, `unit_price`, `description`, `created_at`, `updated_at`) VALUES (1,1,'سيرفرات','product',1500.00,NULL,'2025-05-24 07:29:37','2025-05-24 07:29:37'),(2,1,'تطوير انظمه','product',1500.00,NULL,'2025-05-24 07:30:03','2025-06-10 09:58:48'),(3,NULL,'خدمات اون لاين','service',1500.00,NULL,'2025-06-25 12:49:48','2025-06-25 12:49:48');
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
  `tenant_id` bigint unsigned DEFAULT NULL,
  `date` date NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_id` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IQD',
  `exchange_rate` decimal(15,6) NOT NULL DEFAULT '1.000000',
  `is_multi_currency` tinyint(1) NOT NULL DEFAULT '0',
  `total_debit` decimal(18,2) NOT NULL DEFAULT '0.00',
  `total_credit` decimal(18,2) NOT NULL DEFAULT '0.00',
  `status` enum('active','canceled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `journal_entries_created_by_foreign` (`created_by`),
  KEY `journal_entries_tenant_id_index` (`tenant_id`),
  CONSTRAINT `journal_entries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=194 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `journal_entries`
--

LOCK TABLES `journal_entries` WRITE;
/*!40000 ALTER TABLE `journal_entries` DISABLE KEYS */;
INSERT INTO `journal_entries` (`id`, `tenant_id`, `date`, `description`, `source_type`, `source_id`, `created_by`, `currency`, `exchange_rate`, `is_multi_currency`, `total_debit`, `total_credit`, `status`, `created_at`, `updated_at`) VALUES (1,1,'2025-05-24','قيد سند مالي #VCH-00001','App\\Models\\Voucher',1,2,'IQD',1.000000,0,300000.00,300000.00,'active','2025-05-24 12:01:33','2025-05-24 12:01:33'),(2,1,'2025-05-25','قيد سند مالي #VCH-00002','App\\Models\\Voucher',2,2,'IQD',1.000000,0,150000.00,150000.00,'active','2025-05-25 06:53:14','2025-05-25 06:53:14'),(3,1,'2025-05-25','قيد سند مالي #VCH-00003','App\\Models\\Voucher',3,2,'IQD',1.000000,0,50000.00,50000.00,'active','2025-05-25 07:49:22','2025-05-25 07:49:22'),(4,1,'2025-05-25','قيد سند مالي #VCH-00004','App\\Models\\Voucher',4,2,'IQD',1.000000,0,10000.00,10000.00,'active','2025-05-25 09:38:00','2025-05-25 09:38:00'),(5,1,'2025-05-26','قيد سند مالي #VCH-00005','App\\Models\\Voucher',5,2,'IQD',1.000000,0,5000.00,5000.00,'active','2025-05-26 09:55:30','2025-05-26 09:55:30'),(6,1,'2025-05-28','قيد سند مالي #VCH-00006','App\\Models\\Voucher',6,2,'IQD',1.000000,0,1500.00,1500.00,'active','2025-05-28 08:55:47','2025-05-28 08:55:47'),(7,1,'2025-05-28','قيد سند مالي #VCH-00007','App\\Models\\Voucher',7,2,'IQD',1.000000,0,10000.00,10000.00,'active','2025-05-28 10:22:29','2025-05-28 10:22:29'),(8,1,'2025-05-29','قيد سند مالي #VCH-00008','App\\Models\\Voucher',8,2,'IQD',1.000000,0,10000.00,10000.00,'active','2025-05-29 07:47:31','2025-05-29 07:47:31'),(9,1,'2025-05-29','قيد سند مالي #VCH-00009','App\\Models\\Voucher',9,2,'IQD',1.000000,0,5000.00,5000.00,'active','2025-05-29 08:33:46','2025-05-29 08:33:46'),(10,1,'2025-05-29','قيد سند مالي #VCH-00010','App\\Models\\Voucher',10,2,'IQD',1.000000,0,5000.00,5000.00,'active','2025-05-29 09:22:39','2025-05-29 09:22:39'),(11,1,'2025-05-29','قيد سند مالي #VCH-00011','App\\Models\\Voucher',11,2,'IQD',1.000000,0,15000.00,15000.00,'active','2025-05-29 09:58:44','2025-05-29 09:58:44'),(12,1,'2025-05-29','قيد استحقاق فاتورة INV-00001','invoice',1,2,'USD',1412.500000,0,10361.00,10361.00,'active','2025-05-29 12:37:11','2025-05-29 12:37:11'),(13,1,'2025-05-29','قيد سداد فاتورة INV-00001','App\\Models\\Voucher',12,2,'USD',1412.500000,0,10361.00,10361.00,'active','2025-05-29 12:37:24','2025-05-29 12:37:24'),(14,1,'2025-05-31','قيد سند مالي #VCH-00013','App\\Models\\Voucher',13,2,'IQD',1.000000,0,15000.00,15000.00,'active','2025-05-31 06:34:23','2025-05-31 06:34:23'),(15,1,'2025-05-31','قيد تحويل بين الصناديق للسند #VCH-00014','App\\Models\\Voucher',14,2,'IQD',1.000000,0,14801428.57,10361.00,'canceled','2025-05-31 10:04:10','2025-05-31 10:37:29'),(16,1,'2025-05-31','قيد عكسي لإلغاء السند #VCH-00014','App\\Models\\Voucher',14,2,'IQD',1.000000,0,14801428.57,10361.00,'active','2025-05-31 10:37:29','2025-05-31 10:37:29'),(17,1,'2025-05-31','قيد تحويل بين الصناديق للسند #VCH-00015','App\\Models\\Voucher',15,2,'IQD',1.000000,0,14634912.50,10361.00,'active','2025-05-31 10:43:53','2025-05-31 10:43:53'),(18,1,'2025-05-31','قيد سند مالي #VCH-00016','App\\Models\\Voucher',16,2,'IQD',1.000000,0,150000.00,150000.00,'active','2025-05-31 10:46:32','2025-05-31 10:46:32'),(19,1,'2025-05-31','قيد سند مالي #VCH-00017','App\\Models\\Voucher',17,2,'IQD',1.000000,0,115000.00,115000.00,'active','2025-05-31 10:48:53','2025-05-31 10:48:53'),(20,1,'2025-05-31','قيد سند مالي #VCH-00018','App\\Models\\Voucher',18,2,'IQD',1.000000,0,150000.00,150000.00,'active','2025-05-31 10:58:41','2025-05-31 10:58:41'),(21,1,'2025-05-31','قيد تحويل بين الصناديق للسند #VCH-00019','App\\Models\\Voucher',19,2,'IQD',1.000000,0,4000000.00,4000000.00,'active','2025-05-31 11:54:11','2025-05-31 11:54:11'),(22,1,'2025-06-01','قيد سند مالي #VCH-00020','App\\Models\\Voucher',20,2,'IQD',1.000000,0,100000.00,100000.00,'active','2025-06-01 07:18:30','2025-06-01 07:18:30'),(23,1,'2025-06-01','قيد سند مالي #VCH-00021','App\\Models\\Voucher',21,2,'IQD',1.000000,0,40000.00,40000.00,'active','2025-06-01 07:35:40','2025-06-01 07:35:40'),(24,1,'2025-06-01','قيد سند مالي #VCH-00022','App\\Models\\Voucher',22,2,'IQD',1.000000,0,10000.00,10000.00,'active','2025-06-01 07:36:49','2025-06-01 07:36:49'),(25,1,'2025-06-01','قيد سند مالي #VCH-00023','App\\Models\\Voucher',23,2,'IQD',1.000000,0,5000.00,5000.00,'active','2025-06-01 09:30:03','2025-06-01 09:30:03'),(26,1,'2025-06-01','قيد سند مالي #VCH-00024','App\\Models\\Voucher',24,2,'IQD',1.000000,0,200000.00,200000.00,'active','2025-06-01 10:40:06','2025-06-01 10:40:06'),(27,1,'2025-06-01','قيد سند مالي #VCH-00025','App\\Models\\Voucher',25,2,'IQD',1.000000,0,250000.00,250000.00,'active','2025-06-01 11:33:18','2025-06-01 11:33:18'),(28,1,'2025-05-31','قيد استحقاق رواتب شهر 2025-05','App\\Models\\SalaryBatch',1,1,'IQD',1.000000,1,26002500.00,26002500.00,'active','2025-06-01 11:48:34','2025-06-01 11:48:34'),(29,1,'2025-06-01','دفع راتب شهر 2025-05 للموظف fatmasaleh','App\\Models\\Voucher',26,2,'IQD',1.000000,0,800000.00,800000.00,'active','2025-06-01 12:16:15','2025-06-01 12:16:15'),(30,1,'2025-06-01','دفع راتب شهر 2025-05 للموظف nabaa','App\\Models\\Voucher',27,2,'IQD',1.000000,0,750000.00,750000.00,'active','2025-06-01 12:17:54','2025-06-01 12:17:54'),(31,1,'2025-06-01','دفع راتب شهر 2025-05 للموظف mustafa waheed','App\\Models\\Voucher',28,2,'IQD',1.000000,0,1000000.00,1000000.00,'active','2025-06-01 12:18:25','2025-06-01 12:18:25'),(32,1,'2025-06-01','دفع راتب شهر 2025-05 للموظف nafea amaad','App\\Models\\Voucher',29,2,'IQD',1.000000,0,800000.00,800000.00,'active','2025-06-01 12:18:43','2025-06-01 12:18:43'),(33,1,'2025-06-01','دفع راتب شهر 2025-05 للموظف zainab moayed','App\\Models\\Voucher',30,2,'IQD',1.000000,0,900000.00,900000.00,'active','2025-06-01 12:19:14','2025-06-01 12:19:14'),(34,1,'2025-06-01','قيد سند مالي #VCH-00031','App\\Models\\Voucher',31,2,'IQD',1.000000,0,750000.00,750000.00,'active','2025-06-01 12:20:32','2025-06-01 12:20:32'),(35,1,'2025-06-01','قيد سند مالي #VCH-00032','App\\Models\\Voucher',32,2,'IQD',1.000000,0,25000.00,25000.00,'active','2025-06-01 12:37:08','2025-06-01 12:37:08'),(36,1,'2025-06-02','قيد سند مالي #VCH-00033','App\\Models\\Voucher',33,2,'IQD',1.000000,0,5000.00,5000.00,'active','2025-06-02 09:53:32','2025-06-02 09:53:32'),(37,1,'2025-06-03','قيد سند مالي #VCH-00034','App\\Models\\Voucher',34,2,'IQD',1.000000,0,5000.00,5000.00,'active','2025-06-03 09:36:33','2025-06-03 09:36:33'),(38,1,'2025-06-03','قيد سند مالي #VCH-00035','App\\Models\\Voucher',35,2,'IQD',1.000000,0,10000.00,10000.00,'active','2025-06-03 12:43:14','2025-06-03 12:43:14'),(39,1,'2025-06-03','قيد سند مالي #VCH-00036','App\\Models\\Voucher',36,2,'IQD',1.000000,0,40000.00,40000.00,'active','2025-06-03 12:56:08','2025-06-03 12:56:08'),(40,1,'2025-06-04','قيد سند مالي #VCH-00037','App\\Models\\Voucher',37,2,'IQD',1.000000,0,300000.00,300000.00,'active','2025-06-04 08:37:47','2025-06-04 08:37:47'),(41,1,'2025-06-04','قيد سند مالي #VCH-00038','App\\Models\\Voucher',38,2,'IQD',1.000000,0,75000.00,75000.00,'active','2025-06-04 08:40:15','2025-06-04 08:40:15'),(42,1,'2025-06-04','قيد سند مالي #VCH-00039','App\\Models\\Voucher',39,2,'IQD',1.000000,0,1175000.00,1175000.00,'canceled','2025-06-04 09:10:15','2025-06-04 09:31:57'),(43,1,'2025-06-04','قيد عكسي لإلغاء السند #VCH-00039','App\\Models\\Voucher',39,2,'IQD',1.000000,0,1175000.00,1175000.00,'active','2025-06-04 09:31:57','2025-06-04 09:31:57'),(44,1,'2025-06-04','قيد سند مالي #VCH-00040','App\\Models\\Voucher',40,1,'IQD',1.000000,0,4000000.00,4000000.00,'canceled','2025-06-04 09:34:47','2025-06-07 20:49:04'),(45,1,'2025-06-04','قيد سند مالي #VCH-00041','App\\Models\\Voucher',41,2,'IQD',1.000000,0,2175000.00,2175000.00,'active','2025-06-04 09:36:07','2025-06-04 09:36:07'),(46,1,'2025-06-04','قيد سند مالي #VCH-00042','App\\Models\\Voucher',42,2,'IQD',1.000000,0,5000.00,5000.00,'active','2025-06-04 09:48:54','2025-06-04 09:48:54'),(47,1,'2025-06-04','قيد سند مالي #VCH-00043','App\\Models\\Voucher',43,2,'IQD',1.000000,0,10000.00,10000.00,'active','2025-06-04 09:50:07','2025-06-04 09:50:07'),(48,1,'2025-06-04','قيد سند مالي #VCH-00044','App\\Models\\Voucher',44,2,'IQD',1.000000,0,5000.00,5000.00,'active','2025-06-04 10:00:36','2025-06-04 10:00:36'),(49,1,'2025-06-04','قيد سند مالي #VCH-00045','App\\Models\\Voucher',45,2,'IQD',1.000000,0,10000.00,10000.00,'active','2025-06-04 10:06:52','2025-06-04 10:06:52'),(50,1,'2025-05-31','شهر ٥ سنة ٢٠٢٥',NULL,NULL,1,'IQD',1.000000,0,9940000.00,9940000.00,'active','2025-06-04 22:31:37','2025-06-04 22:31:37'),(51,1,'2025-06-06',NULL,NULL,NULL,1,'IQD',1.000000,0,14610000.00,14610000.00,'canceled','2025-06-06 17:52:40','2025-06-06 17:55:12'),(52,1,'2025-06-06','قيد عكسي لإلغاء القيد اليدوي #51','manual',51,1,'IQD',1.000000,0,14610000.00,14610000.00,'active','2025-06-06 17:55:12','2025-06-06 17:55:12'),(53,1,'2025-01-31','مصاريف ورواتب شهر ١ قبل نظام مجموع مصاريف ٩٧٤٠ والرواتب ٢٥١٤٨',NULL,NULL,1,'IQD',1.000000,0,52332000.00,52332000.00,'active','2025-06-06 23:05:53','2025-06-06 23:05:53'),(54,1,'2025-01-31','ايرادات شهر ١ سنة ٢٠٢٥ قبل نظام',NULL,NULL,1,'IQD',1.000000,0,52804500.00,52804500.00,'active','2025-06-06 23:19:49','2025-06-06 23:19:49'),(55,1,'2025-02-28','ايرادات شهر ٢ سنة ٢٠٢٥ قبل نظام',NULL,NULL,1,'IQD',1.000000,0,48735000.00,48735000.00,'active','2025-06-06 23:40:22','2025-06-06 23:40:22'),(56,1,'2025-02-28','مصاريف ورواتب شهر ٢ قبل نظام مجموع مصاريف ٨٠٠٠ والرواتب ٢٥٦٤٨',NULL,NULL,1,'IQD',1.000000,0,50472000.00,50472000.00,'active','2025-06-06 23:47:16','2025-06-06 23:47:16'),(57,1,'2025-03-31','ايرادات شهر ٣ سنة ٢٠٢٥ قبل نظام',NULL,NULL,1,'IQD',1.000000,0,46500000.00,46500000.00,'active','2025-06-07 00:07:24','2025-06-07 00:07:24'),(58,1,'2025-03-31','مصاريف ورواتب شهر ٣ قبل نظام مجموع مصاريف ٤٩٠٠ والرواتب ٢٥٦٤٨',NULL,NULL,1,'IQD',1.000000,0,45822000.00,45822000.00,'active','2025-06-07 00:09:27','2025-06-07 00:09:27'),(59,1,'2025-04-30','ايرادات شهر ٤ سنة ٢٠٢٥ قبل نظام',NULL,NULL,1,'IQD',1.000000,0,54000000.00,54000000.00,'active','2025-06-07 00:24:45','2025-06-07 00:24:45'),(60,1,'2025-04-30','مصاريف ورواتب شهر ٤ قبل نظام مجموع مصاريف ٧٩٠٠ والرواتب ٢٥١١٥',NULL,NULL,1,'IQD',1.000000,0,49522500.00,49522500.00,'active','2025-06-07 00:27:37','2025-06-07 00:27:37'),(61,1,'2025-06-07','العجز التراكي الى شهر ٤ سنة ٢٠٢٥',NULL,NULL,1,'IQD',1.000000,0,53355000.00,53355000.00,'active','2025-06-07 00:47:48','2025-06-07 00:47:48'),(62,1,'2025-04-30','سلف مدفوعة لتشغيل مشروع',NULL,NULL,1,'IQD',1.000000,0,121901700.00,121901700.00,'active','2025-06-07 01:17:50','2025-06-07 01:17:50'),(63,1,'2025-04-30','تسوية مصاريف 4 أشهر قبل تطبيق النظام',NULL,NULL,1,'IQD',1.000000,0,198148500.00,198148500.00,'active','2025-06-07 13:22:10','2025-06-07 13:22:10'),(64,1,'2025-05-31','قيد استحقاق فاتورة INV-00002','invoice',2,1,'USD',1412.500000,0,8500.00,8500.00,'active','2025-06-07 20:45:59','2025-06-07 20:45:59'),(65,1,'2025-06-07','قيد سداد فاتورة INV-00002','App\\Models\\Voucher',46,1,'USD',1412.500000,0,8500.00,8500.00,'active','2025-06-07 20:47:10','2025-06-07 20:47:10'),(66,1,'2025-06-07','قيد عكسي لإلغاء السند #VCH-00040','App\\Models\\Voucher',40,1,'IQD',1.000000,0,4000000.00,4000000.00,'active','2025-06-07 20:49:04','2025-06-07 20:49:04'),(67,NULL,'2025-05-31','قيد سند مالي #VCH-00047','App\\Models\\Voucher',47,1,'IQD',1.000000,0,4000000.00,4000000.00,'active','2025-06-08 00:24:01','2025-06-08 00:24:01'),(68,NULL,'2025-05-31','قيد سند مالي #VCH-00048','App\\Models\\Voucher',48,1,'USD',1412.500000,0,8500.00,8500.00,'active','2025-06-08 09:28:38','2025-06-08 09:28:38'),(69,NULL,'2025-06-10','قيد سند مالي #VCH-00049','App\\Models\\Voucher',49,2,'IQD',1.000000,0,15000.00,15000.00,'active','2025-06-10 09:36:57','2025-06-10 09:36:57'),(70,NULL,'2025-05-25','قيد استحقاق فاتورة INV-00003','invoice',3,2,'USD',1412.500000,0,16500.00,16500.00,'active','2025-06-10 09:53:47','2025-06-10 09:53:47'),(71,NULL,'2025-06-10','قيد سداد فاتورة INV-00003','App\\Models\\Voucher',50,2,'USD',1412.500000,0,16500.00,16500.00,'active','2025-06-10 10:20:10','2025-06-10 10:20:10'),(72,NULL,'2025-06-10','قيد سند مالي #VCH-00051','App\\Models\\Voucher',51,2,'IQD',1.000000,0,10000.00,10000.00,'active','2025-06-10 12:38:16','2025-06-10 12:38:16'),(73,NULL,'2025-06-10','قيد سند مالي #VCH-00052','App\\Models\\Voucher',52,2,'IQD',1.000000,0,3000.00,3000.00,'active','2025-06-10 12:43:23','2025-06-10 12:43:23'),(74,NULL,'2025-06-10','قيد سند مالي #VCH-00053','App\\Models\\Voucher',53,2,'IQD',1.000000,0,200000.00,200000.00,'canceled','2025-06-10 12:45:35','2025-06-11 09:28:34'),(75,NULL,'2025-06-11','قيد سند مالي #VCH-00054','App\\Models\\Voucher',54,2,'IQD',1.000000,0,500000.00,500000.00,'canceled','2025-06-11 05:39:37','2025-06-11 09:28:18'),(76,NULL,'2025-06-11','قيد سند مالي #VCH-00055','App\\Models\\Voucher',55,2,'IQD',1.000000,0,20000.00,20000.00,'active','2025-06-11 07:25:25','2025-06-11 07:25:25'),(77,NULL,'2025-06-11','قيد سند مالي #VCH-00056','App\\Models\\Voucher',56,2,'IQD',1.000000,0,150000.00,150000.00,'active','2025-06-11 07:43:03','2025-06-11 07:43:03'),(78,NULL,'2025-06-11','قيد عكسي لإلغاء السند #VCH-00054','App\\Models\\Voucher',54,2,'IQD',1.000000,0,500000.00,500000.00,'active','2025-06-11 09:28:18','2025-06-11 09:28:18'),(79,NULL,'2025-06-11','قيد عكسي لإلغاء السند #VCH-00053','App\\Models\\Voucher',53,2,'IQD',1.000000,0,200000.00,200000.00,'active','2025-06-11 09:28:34','2025-06-11 09:28:34'),(80,NULL,'2025-06-11','دفع راتب شهر 2025-05 للموظف ibraham','App\\Models\\Voucher',57,2,'USD',1420.000000,0,1900.00,1900.00,'active','2025-06-11 09:30:13','2025-06-11 09:30:13'),(81,NULL,'2025-06-11','قيد سند مالي #VCH-00058','App\\Models\\Voucher',60,2,'IQD',1.000000,0,5000.00,5000.00,'active','2025-06-11 09:42:45','2025-06-11 09:42:45'),(82,NULL,'2025-06-11','قيد سند مالي #VCH-00061','App\\Models\\Voucher',61,2,'IQD',1.000000,0,700000.00,700000.00,'active','2025-06-11 09:48:01','2025-06-11 09:48:01'),(83,NULL,'2025-06-11','دفع راتب شهر 2025-05 للموظف kaly','App\\Models\\Voucher',62,2,'USD',1420.000000,0,1400.00,1400.00,'active','2025-06-11 09:49:23','2025-06-11 09:49:23'),(84,NULL,'2025-06-11','قيد استحقاق فاتورة INV-00004','invoice',4,2,'USD',1420.000000,0,3000.00,3000.00,'active','2025-06-11 11:51:42','2025-06-11 11:51:42'),(85,NULL,'2025-06-11','قيد سداد فاتورة INV-00004','App\\Models\\Voucher',71,2,'USD',1420.000000,0,3000.00,3000.00,'active','2025-06-11 11:51:50','2025-06-11 11:51:50'),(86,NULL,'2025-06-11','قيد سند مالي #VCH-00072','App\\Models\\Voucher',72,2,'IQD',1.000000,0,31000.00,31000.00,'active','2025-06-11 12:02:54','2025-06-11 12:02:54'),(87,NULL,'2025-06-11','قيد سند مالي #VCH-00073','App\\Models\\Voucher',73,2,'IQD',1.000000,0,25000.00,25000.00,'active','2025-06-11 12:10:54','2025-06-11 12:10:54'),(88,NULL,'2025-06-11','قيد سند مالي #VCH-00074','App\\Models\\Voucher',74,2,'IQD',1.000000,0,5000.00,5000.00,'active','2025-06-11 12:12:02','2025-06-11 12:12:02'),(89,NULL,'2025-06-11','قيد سند مالي #VCH-00075','App\\Models\\Voucher',75,2,'USD',1420.000000,0,2454.00,2454.00,'active','2025-06-11 12:38:03','2025-06-11 12:38:03'),(90,NULL,'2025-06-11','دفع راتب شهر 2025-05 للموظف arkan','App\\Models\\Voucher',76,1,'USD',1420.000000,0,1900.00,1900.00,'active','2025-06-11 13:53:15','2025-06-11 13:53:15'),(91,NULL,'2025-06-12','قيد سند مالي #VCH-00077','App\\Models\\Voucher',77,2,'IQD',1.000000,0,300000.00,300000.00,'active','2025-06-12 10:27:59','2025-06-12 10:27:59'),(92,NULL,'2025-06-12','قيد سند مالي #VCH-00078','App\\Models\\Voucher',78,2,'IQD',1.000000,0,30000.00,30000.00,'active','2025-06-12 10:28:57','2025-06-12 10:28:57'),(93,NULL,'2025-06-12','قيد سند مالي #VCH-00079','App\\Models\\Voucher',79,2,'IQD',1.000000,0,18000.00,18000.00,'active','2025-06-12 10:29:46','2025-06-12 10:29:46'),(94,NULL,'2025-06-12','قيد سند مالي #VCH-00080','App\\Models\\Voucher',80,2,'IQD',1.000000,0,5000.00,5000.00,'active','2025-06-12 10:30:17','2025-06-12 10:30:17'),(95,NULL,'2025-06-12','قيد سند مالي #VCH-00081','App\\Models\\Voucher',81,2,'IQD',1.000000,0,10000.00,10000.00,'active','2025-06-12 10:31:31','2025-06-12 10:31:31'),(96,NULL,'2025-06-12','قيد سند مالي #VCH-00082','App\\Models\\Voucher',82,2,'IQD',1.000000,0,3000.00,3000.00,'active','2025-06-12 10:33:49','2025-06-12 10:33:49'),(97,NULL,'2025-06-12','قيد سند مالي #VCH-00083','App\\Models\\Voucher',83,2,'IQD',1.000000,0,10000.00,10000.00,'active','2025-06-12 10:36:46','2025-06-12 10:36:46'),(98,NULL,'2025-06-01','قيد استحقاق فاتورة INV-00005','invoice',5,2,'USD',1420.000000,0,500.00,500.00,'active','2025-06-12 11:44:30','2025-06-12 11:44:30'),(99,NULL,'2025-06-12','قيد سداد فاتورة INV-00005','App\\Models\\Voucher',84,2,'USD',1420.000000,0,500.00,500.00,'active','2025-06-12 11:45:41','2025-06-12 11:45:41'),(100,NULL,'2025-06-12','قيد تحويل بين الصناديق للسند #VCH-00085','App\\Models\\Voucher',85,2,'IQD',1.000000,0,710000.00,500.00,'active','2025-06-12 11:51:28','2025-06-12 11:51:28'),(101,NULL,'2025-06-12','قيد سند مالي #VCH-00086','App\\Models\\Voucher',86,2,'IQD',1.000000,0,710000.00,710000.00,'active','2025-06-12 11:53:38','2025-06-12 11:53:38'),(102,NULL,'2025-06-14','دفع راتب شهر 2025-05 للموظف umer','App\\Models\\Voucher',87,2,'USD',1420.000000,0,1000.00,1000.00,'active','2025-06-14 09:51:34','2025-06-14 09:51:34'),(103,NULL,'2025-06-14','دفع راتب شهر 2025-05 للموظف tareq','App\\Models\\Voucher',104,2,'USD',1420.000000,0,750.00,750.00,'active','2025-06-14 10:39:46','2025-06-14 10:39:46'),(104,NULL,'2025-06-14','دفع راتب شهر 2025-05 للموظف ateeb','App\\Models\\Voucher',105,2,'USD',1420.000000,0,750.00,750.00,'active','2025-06-14 10:40:41','2025-06-14 10:40:41'),(105,NULL,'2025-06-14','دفع راتب شهر 2025-05 للموظف Hisham','App\\Models\\Voucher',106,2,'USD',1420.000000,0,500.00,500.00,'active','2025-06-14 10:40:50','2025-06-14 10:40:50'),(106,NULL,'2025-06-14','دفع راتب شهر 2025-05 للموظف Hothaifa Jaber','App\\Models\\Voucher',107,2,'USD',1420.000000,0,1400.00,1400.00,'active','2025-06-14 10:40:58','2025-06-14 10:40:58'),(107,NULL,'2025-06-14','دفع راتب شهر 2025-05 للموظف Mohamed jaber','App\\Models\\Voucher',108,2,'USD',1420.000000,0,500.00,500.00,'active','2025-06-14 10:41:07','2025-06-14 10:41:07'),(108,NULL,'2025-06-14','دفع راتب شهر 2025-05 للموظف Mohammad hassnain','App\\Models\\Voucher',109,2,'USD',1420.000000,0,750.00,750.00,'active','2025-06-14 10:41:16','2025-06-14 10:41:16'),(109,NULL,'2025-06-14','دفع راتب شهر 2025-05 للموظف naveed','App\\Models\\Voucher',110,2,'USD',1420.000000,0,1150.00,1150.00,'active','2025-06-14 10:41:25','2025-06-14 10:41:25'),(110,NULL,'2025-06-14','دفع راتب شهر 2025-05 للموظف firas','App\\Models\\Voucher',111,2,'USD',1420.000000,0,500.00,500.00,'active','2025-06-14 10:41:33','2025-06-14 10:41:33'),(111,NULL,'2025-06-14','دفع راتب شهر 2025-05 للموظف siraj','App\\Models\\Voucher',112,2,'USD',1420.000000,0,800.00,800.00,'active','2025-06-14 10:41:41','2025-06-14 10:41:41'),(112,NULL,'2025-06-14','دفع راتب شهر 2025-05 للموظف sadqain','App\\Models\\Voucher',113,2,'USD',1420.000000,0,1500.00,1500.00,'active','2025-06-14 10:41:56','2025-06-14 10:41:56'),(113,NULL,'2025-06-14','دفع راتب شهر 2025-05 للموظف usama','App\\Models\\Voucher',114,2,'USD',1420.000000,0,600.00,600.00,'active','2025-06-14 10:42:07','2025-06-14 10:42:07'),(114,NULL,'2025-06-14','قيد سند مالي #VCH-00115','App\\Models\\Voucher',115,2,'USD',1420.000000,0,700.00,700.00,'active','2025-06-14 10:44:59','2025-06-14 10:44:59'),(115,NULL,'2025-06-14','قيد تحويل بين الصناديق للسند #VCH-00116','App\\Models\\Voucher',116,2,'IQD',1.000000,0,775320.00,546.00,'active','2025-06-14 11:37:21','2025-06-14 11:37:21'),(116,NULL,'2025-06-15','قيد سند مالي #VCH-00117','App\\Models\\Voucher',117,2,'USD',1420.000000,0,400.00,400.00,'active','2025-06-15 06:10:49','2025-06-15 06:10:49'),(117,NULL,'2025-06-15','قيد سند مالي #VCH-00118','App\\Models\\Voucher',118,2,'IQD',1.000000,0,750000.00,750000.00,'active','2025-06-15 06:22:02','2025-06-15 06:22:02'),(118,NULL,'2025-06-15','قيد سند مالي #VCH-00119','App\\Models\\Voucher',119,2,'IQD',1.000000,0,375000.00,375000.00,'active','2025-06-15 11:59:25','2025-06-15 11:59:25'),(119,NULL,'2025-06-15','قيد سند مالي #VCH-00120','App\\Models\\Voucher',120,2,'IQD',1.000000,0,700000.00,700000.00,'active','2025-06-15 12:38:19','2025-06-15 12:38:19'),(120,NULL,'2025-06-16','قيد سند مالي #VCH-00121','App\\Models\\Voucher',121,2,'IQD',1.000000,0,10000.00,10000.00,'active','2025-06-16 05:24:05','2025-06-16 05:24:05'),(121,NULL,'2025-06-16','قيد سند مالي #VCH-00122','App\\Models\\Voucher',122,2,'IQD',1.000000,0,5000.00,5000.00,'active','2025-06-16 09:26:42','2025-06-16 09:26:42'),(122,NULL,'2025-06-16','قيد سند مالي #VCH-00123','App\\Models\\Voucher',123,2,'IQD',1.000000,0,30000.00,30000.00,'active','2025-06-16 13:00:57','2025-06-16 13:00:57'),(123,NULL,'2025-06-17','قيد سند مالي #VCH-00124','App\\Models\\Voucher',124,2,'IQD',1.000000,0,5000.00,5000.00,'active','2025-06-17 12:30:25','2025-06-17 12:30:25'),(124,NULL,'2025-06-18','قيد سند مالي #VCH-00125','App\\Models\\Voucher',125,2,'IQD',1.000000,0,5000.00,5000.00,'active','2025-06-18 11:08:36','2025-06-18 11:08:36'),(125,NULL,'2025-06-18','قيد سند مالي #VCH-00126','App\\Models\\Voucher',126,2,'IQD',1.000000,0,500000.00,500000.00,'active','2025-06-18 11:18:06','2025-06-18 11:18:06'),(126,NULL,'2025-06-18','قيد سند مالي #VCH-00127','App\\Models\\Voucher',127,2,'IQD',1.000000,0,180000.00,180000.00,'active','2025-06-18 11:27:35','2025-06-18 11:27:35'),(127,NULL,'2025-06-18','قيد سند مالي #VCH-00128','App\\Models\\Voucher',128,2,'IQD',1.000000,0,320000.00,320000.00,'active','2025-06-18 11:31:16','2025-06-18 11:31:16'),(128,NULL,'2025-06-19','قيد سند مالي #VCH-00129','App\\Models\\Voucher',129,2,'IQD',1.000000,0,36000.00,36000.00,'active','2025-06-19 07:05:08','2025-06-19 07:05:08'),(129,NULL,'2025-06-19','قيد سند مالي #VCH-00130','App\\Models\\Voucher',130,2,'IQD',1.000000,0,190000.00,190000.00,'active','2025-06-19 09:42:22','2025-06-19 09:42:22'),(130,NULL,'2025-06-19','قيد سند مالي #VCH-00131','App\\Models\\Voucher',131,2,'IQD',1.000000,0,5000.00,5000.00,'active','2025-06-19 09:48:16','2025-06-19 09:48:16'),(131,NULL,'2025-06-22','قيد سند مالي #VCH-00132','App\\Models\\Voucher',132,2,'IQD',1.000000,0,5000.00,5000.00,'active','2025-06-22 11:43:28','2025-06-22 11:43:28'),(132,NULL,'2025-06-23','قيد سند مالي #VCH-00133','App\\Models\\Voucher',133,2,'IQD',1.000000,0,14000.00,14000.00,'active','2025-06-23 07:21:39','2025-06-23 07:21:39'),(133,NULL,'2025-06-23','قيد سند مالي #VCH-00134','App\\Models\\Voucher',134,2,'IQD',1.000000,0,5000.00,5000.00,'active','2025-06-23 09:43:36','2025-06-23 09:43:36'),(134,NULL,'2025-06-24','قيد سند مالي #VCH-00135','App\\Models\\Voucher',135,2,'IQD',1.000000,0,5000.00,5000.00,'active','2025-06-24 10:53:02','2025-06-24 10:53:02'),(135,NULL,'2025-06-24','قيد سند مالي #VCH-00136','App\\Models\\Voucher',136,2,'IQD',1.000000,0,10000.00,10000.00,'active','2025-06-24 11:30:39','2025-06-24 11:30:39'),(136,NULL,'2025-06-24','قيد سند مالي #VCH-00137','App\\Models\\Voucher',137,2,'IQD',1.000000,0,305000.00,305000.00,'active','2025-06-24 11:53:16','2025-06-24 11:53:16'),(137,NULL,'2025-06-24','قيد سند مالي #VCH-00138','App\\Models\\Voucher',138,2,'IQD',1.000000,0,305000.00,305000.00,'active','2025-06-24 11:56:21','2025-06-24 11:56:21'),(138,NULL,'2025-06-24','قيد سند مالي #VCH-00139','App\\Models\\Voucher',139,2,'IQD',1.000000,0,50000.00,50000.00,'active','2025-06-24 11:58:20','2025-06-24 11:58:20'),(139,NULL,'2025-06-25','قيد سند مالي #VCH-00140','App\\Models\\Voucher',140,2,'IQD',1.000000,0,5000.00,5000.00,'active','2025-06-25 06:45:46','2025-06-25 06:45:46'),(140,NULL,'2025-06-25','قيد سند مالي #VCH-00141','App\\Models\\Voucher',141,2,'IQD',1.000000,0,25000.00,25000.00,'active','2025-06-25 12:17:56','2025-06-25 12:17:56'),(141,NULL,'2025-05-31','قيد استحقاق فاتورة INV-00006','invoice',6,2,'USD',1420.000000,0,1587.00,1587.00,'active','2025-06-25 12:51:56','2025-06-25 12:51:56'),(142,NULL,'2025-06-25','قيد سداد فاتورة INV-00006','App\\Models\\Voucher',142,2,'USD',1420.000000,0,1587.00,1587.00,'active','2025-06-25 12:52:02','2025-06-25 12:52:02'),(143,NULL,'2025-06-26','قيد تحويل بين الصناديق للسند #VCH-00143','App\\Models\\Voucher',143,2,'IQD',1.000000,0,1587.00,1587.00,'canceled','2025-06-26 06:12:31','2025-06-26 09:17:59'),(144,NULL,'2025-06-26','قيد عكسي لإلغاء السند #VCH-00143','App\\Models\\Voucher',143,2,'IQD',1.000000,0,1587.00,1587.00,'active','2025-06-26 09:17:59','2025-06-26 09:17:59'),(145,NULL,'2025-06-26','قيد تحويل بين الصناديق للسند #VCH-00144','App\\Models\\Voucher',144,2,'IQD',1.000000,0,1587.00,1587.00,'canceled','2025-06-26 09:23:17','2025-06-26 09:24:53'),(146,NULL,'2025-06-26','قيد عكسي لإلغاء السند #VCH-00144','App\\Models\\Voucher',144,2,'IQD',1.000000,0,1587.00,1587.00,'active','2025-06-26 09:24:53','2025-06-26 09:24:53'),(147,NULL,'2025-06-26','قيد تحويل بين الصناديق للسند #VCH-00145','App\\Models\\Voucher',145,2,'IQD',1.000000,0,2253540.00,1587.00,'active','2025-06-26 10:20:00','2025-06-26 10:20:00'),(148,NULL,'2025-06-26','قيد تحويل بين الصناديق للسند #VCH-00146','App\\Models\\Voucher',146,2,'IQD',1.000000,0,2000000.00,2000000.00,'active','2025-06-26 10:21:13','2025-06-26 10:21:13'),(149,NULL,'2025-06-28','قيد سند مالي #VCH-00147','App\\Models\\Voucher',147,2,'IQD',1.000000,0,150000.00,150000.00,'active','2025-06-28 07:18:12','2025-06-28 07:18:12'),(150,NULL,'2025-06-28','قيد سند مالي #VCH-00148','App\\Models\\Voucher',148,2,'IQD',1.000000,0,5000.00,5000.00,'active','2025-06-28 07:20:13','2025-06-28 07:20:13'),(151,NULL,'2025-06-29','قيد سند مالي #VCH-00149','App\\Models\\Voucher',149,2,'IQD',1.000000,0,10000.00,10000.00,'active','2025-06-29 08:55:09','2025-06-29 08:55:09'),(152,NULL,'2025-06-29','قيد سند مالي #VCH-00150','App\\Models\\Voucher',150,2,'IQD',1.000000,0,15000.00,15000.00,'active','2025-06-29 11:03:23','2025-06-29 11:03:23'),(153,NULL,'2025-06-30','قيد سند مالي #VCH-00151','App\\Models\\Voucher',151,2,'IQD',1.000000,0,50000.00,50000.00,'active','2025-06-30 08:33:19','2025-06-30 08:33:19'),(154,NULL,'2025-06-30','قيد سند مالي #VCH-00152','App\\Models\\Voucher',152,2,'IQD',1.000000,0,15000.00,15000.00,'active','2025-06-30 09:05:00','2025-06-30 09:05:00'),(155,NULL,'2025-06-30','قيد سند مالي #VCH-00153','App\\Models\\Voucher',153,2,'IQD',1.000000,0,10000.00,10000.00,'active','2025-06-30 09:17:28','2025-06-30 09:17:28'),(156,NULL,'2025-06-30','قيد سند مالي #VCH-00154','App\\Models\\Voucher',154,1,'IQD',1.000000,0,2000000.00,2000000.00,'active','2025-06-30 10:47:50','2025-06-30 10:47:50'),(157,NULL,'2025-06-30','قيد استحقاق فاتورة INV-00007','invoice',7,2,'USD',1415.000000,0,10361.00,10361.00,'active','2025-06-30 11:47:45','2025-06-30 11:47:45'),(158,NULL,'2025-06-30','قيد سداد فاتورة INV-00007','App\\Models\\Voucher',155,2,'USD',1415.000000,0,10361.00,10361.00,'active','2025-06-30 11:48:14','2025-06-30 11:48:14'),(159,NULL,'2025-06-30','قيد استحقاق فاتورة INV-00008','invoice',8,2,'USD',1415.000000,0,13500.00,13500.00,'active','2025-06-30 11:49:29','2025-06-30 11:49:29'),(160,NULL,'2025-06-30','قيد سداد فاتورة INV-00008','App\\Models\\Voucher',156,2,'USD',1415.000000,0,13500.00,13500.00,'active','2025-06-30 11:49:43','2025-06-30 11:49:43'),(161,NULL,'2025-06-30','قيد تحويل بين الصناديق للسند #VCH-00157','App\\Models\\Voucher',157,2,'IQD',1.000000,0,33763315.00,23861.00,'active','2025-06-30 12:01:30','2025-06-30 12:01:30'),(162,NULL,'2025-06-30','قيد سند مالي #VCH-00158','App\\Models\\Voucher',158,2,'IQD',1.000000,0,5015000.00,5015000.00,'active','2025-06-30 12:32:01','2025-06-30 12:32:01'),(163,NULL,'2025-06-30','قيد سند مالي #VCH-00159','App\\Models\\Voucher',159,2,'IQD',1.000000,0,5015000.00,5015000.00,'active','2025-06-30 12:34:03','2025-06-30 12:34:03'),(164,NULL,'2025-06-30','قيد سند مالي #VCH-00160','App\\Models\\Voucher',160,2,'IQD',1.000000,0,10000.00,10000.00,'active','2025-06-30 12:34:51','2025-06-30 12:34:51'),(165,NULL,'2025-06-30','قيد سند مالي #VCH-00161','App\\Models\\Voucher',161,2,'IQD',1.000000,0,235000.00,235000.00,'active','2025-06-30 12:53:38','2025-06-30 12:53:38'),(166,NULL,'2025-07-01','قيد سند مالي #VCH-00162','App\\Models\\Voucher',162,2,'IQD',1.000000,0,8000.00,8000.00,'active','2025-07-01 08:14:28','2025-07-01 08:14:28'),(167,NULL,'2025-06-30','قيد استحقاق رواتب شهر 2025-06','App\\Models\\SalaryBatch',2,2,'IQD',1.000000,1,27649500.00,27649500.00,'active','2025-07-01 08:28:30','2025-07-01 08:28:30'),(168,NULL,'2025-07-01','دفع راتب شهر 2025-06 للموظف علي صادق','App\\Models\\Voucher',163,2,'IQD',1.000000,0,150000.00,150000.00,'active','2025-07-01 09:25:47','2025-07-01 09:25:47'),(169,NULL,'2025-07-01','دفع راتب شهر 2025-06 للموظف فرهاد حسين','App\\Models\\Voucher',164,2,'IQD',1.000000,0,750000.00,750000.00,'active','2025-07-01 09:26:53','2025-07-01 09:26:53'),(170,NULL,'2025-07-01','دفع راتب شهر 2025-06 للموظف mustafa waheed','App\\Models\\Voucher',165,2,'IQD',1.000000,0,1100000.00,1100000.00,'active','2025-07-01 09:28:21','2025-07-01 09:28:21'),(171,NULL,'2025-07-01','دفع راتب شهر 2025-06 للموظف nabaa','App\\Models\\Voucher',166,2,'IQD',1.000000,0,850000.00,850000.00,'active','2025-07-01 09:32:01','2025-07-01 09:32:01'),(172,NULL,'2025-07-01','دفع راتب شهر 2025-06 للموظف zainab moayed','App\\Models\\Voucher',167,2,'IQD',1.000000,0,900000.00,900000.00,'active','2025-07-01 09:33:58','2025-07-01 09:33:58'),(173,NULL,'2025-07-01','دفع راتب شهر 2025-06 للموظف zahraa  Jumaah','App\\Models\\Voucher',168,2,'IQD',1.000000,0,650000.00,650000.00,'active','2025-07-01 09:36:14','2025-07-01 09:36:14'),(174,NULL,'2025-07-01','دفع راتب شهر 2025-06 للموظف nafea amaad','App\\Models\\Voucher',169,2,'IQD',1.000000,0,800000.00,800000.00,'active','2025-07-01 09:38:54','2025-07-01 09:38:54'),(175,NULL,'2025-07-01','دفع راتب شهر 2025-06 للموظف fatmasaleh','App\\Models\\Voucher',170,1,'IQD',1.000000,0,800000.00,800000.00,'active','2025-07-01 09:55:42','2025-07-01 09:55:42'),(176,NULL,'2025-07-01','قيد سند مالي #VCH-00171','App\\Models\\Voucher',171,2,'IQD',1.000000,0,70000.00,70000.00,'active','2025-07-01 10:04:36','2025-07-01 10:04:36'),(177,NULL,'2025-07-01','قيد سند مالي #VCH-00172','App\\Models\\Voucher',172,2,'IQD',1.000000,0,814000.00,814000.00,'active','2025-07-01 11:38:29','2025-07-01 11:38:29'),(178,NULL,'2025-07-02','قيد سند مالي #VCH-00173','App\\Models\\Voucher',173,2,'IQD',1.000000,0,1610000.00,1610000.00,'active','2025-07-02 07:59:08','2025-07-02 07:59:08'),(179,NULL,'2025-07-02','قيد سند مالي #VCH-00174','App\\Models\\Voucher',174,2,'IQD',1.000000,0,728000.00,728000.00,'active','2025-07-02 08:23:20','2025-07-02 08:23:20'),(180,NULL,'2025-07-02','قيد سند مالي #VCH-00175','App\\Models\\Voucher',175,2,'IQD',1.000000,0,15000.00,15000.00,'active','2025-07-02 08:25:41','2025-07-02 08:25:41'),(181,NULL,'2025-07-02','قيد سند مالي #VCH-00176','App\\Models\\Voucher',176,2,'IQD',1.000000,0,5000.00,5000.00,'active','2025-07-02 08:26:37','2025-07-02 08:26:37'),(182,NULL,'2025-07-02','قيد سند مالي #VCH-00177','App\\Models\\Voucher',177,2,'IQD',1.000000,0,1000.00,1000.00,'active','2025-07-02 08:34:04','2025-07-02 08:34:04'),(183,NULL,'2025-07-02','قيد سند مالي #VCH-00178','App\\Models\\Voucher',178,2,'IQD',1.000000,0,5000.00,5000.00,'active','2025-07-02 08:59:27','2025-07-02 08:59:27'),(184,NULL,'2025-07-03','قيد سند مالي #VCH-00179','App\\Models\\Voucher',179,2,'IQD',1.000000,0,150000.00,150000.00,'active','2025-07-03 08:42:59','2025-07-03 08:42:59'),(185,NULL,'2025-07-03','قيد سند مالي #VCH-00180','App\\Models\\Voucher',180,2,'IQD',1.000000,0,5000.00,5000.00,'active','2025-07-03 08:46:06','2025-07-03 08:46:06'),(186,NULL,'2025-07-07','قيد سند مالي #VCH-00181','App\\Models\\Voucher',181,2,'IQD',1.000000,0,9000.00,9000.00,'active','2025-07-07 07:08:47','2025-07-07 07:08:47'),(187,NULL,'2025-07-07','قيد سند مالي #VCH-00182','App\\Models\\Voucher',182,2,'IQD',1.000000,0,500000.00,500000.00,'active','2025-07-07 08:00:19','2025-07-07 08:00:19'),(188,NULL,'2025-07-07','قيد سند مالي #VCH-00183','App\\Models\\Voucher',183,2,'IQD',1.000000,0,5000.00,5000.00,'active','2025-07-07 09:14:00','2025-07-07 09:14:00'),(189,NULL,'2025-06-30','اجور الادارة شهر ٦',NULL,NULL,1,'IQD',1.000000,0,9940000.00,9940000.00,'active','2025-07-08 12:57:15','2025-07-08 12:57:15'),(190,NULL,'2025-07-09','قيد تحويل بين الصناديق للسند #VCH-00184','App\\Models\\Voucher',184,2,'IQD',1.000000,0,5168.80,7384000.00,'active','2025-07-09 12:28:21','2025-07-09 12:28:21'),(191,NULL,'2025-07-09','قيد سند مالي #VCH-00185','App\\Models\\Voucher',185,2,'IQD',1.000000,0,61000.00,61000.00,'active','2025-07-09 12:55:05','2025-07-09 12:55:05'),(192,NULL,'2025-07-10','قيد سند مالي #VCH-00186','App\\Models\\Voucher',186,2,'IQD',1.000000,0,369000.00,369000.00,'active','2025-07-10 07:31:13','2025-07-10 07:31:13'),(193,NULL,'2025-07-10','قيد سند مالي #VCH-00187','App\\Models\\Voucher',187,2,'IQD',1.000000,0,10000.00,10000.00,'active','2025-07-10 09:18:38','2025-07-10 09:18:38');
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
  `tenant_id` bigint unsigned DEFAULT NULL,
  `journal_entry_id` bigint unsigned NOT NULL,
  `account_id` bigint unsigned NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `debit` decimal(18,2) NOT NULL DEFAULT '0.00',
  `credit` decimal(18,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IQD',
  `exchange_rate` decimal(15,6) NOT NULL DEFAULT '1.000000',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `journal_entry_lines_journal_entry_id_foreign` (`journal_entry_id`),
  KEY `journal_entry_lines_account_id_foreign` (`account_id`),
  KEY `journal_entry_lines_tenant_id_index` (`tenant_id`),
  CONSTRAINT `journal_entry_lines_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `journal_entry_lines_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=392 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `journal_entry_lines`
--

LOCK TABLES `journal_entry_lines` WRITE;
/*!40000 ALTER TABLE `journal_entry_lines` DISABLE KEYS */;
INSERT INTO `journal_entry_lines` (`id`, `tenant_id`, `journal_entry_id`, `account_id`, `description`, `debit`, `credit`, `currency`, `exchange_rate`, `created_at`, `updated_at`) VALUES (1,1,1,51,NULL,300000.00,0.00,'IQD',1.000000,'2025-05-24 12:01:33','2025-05-24 12:01:33'),(2,1,1,97,NULL,0.00,300000.00,'IQD',1.000000,'2025-05-24 12:01:33','2025-05-24 12:01:33'),(3,1,2,51,NULL,0.00,150000.00,'IQD',1.000000,'2025-05-25 06:53:14','2025-05-25 06:53:14'),(4,1,2,94,NULL,150000.00,0.00,'IQD',1.000000,'2025-05-25 06:53:14','2025-05-25 06:53:14'),(5,1,3,51,NULL,0.00,50000.00,'IQD',1.000000,'2025-05-25 07:49:22','2025-05-25 07:49:22'),(6,1,3,94,NULL,50000.00,0.00,'IQD',1.000000,'2025-05-25 07:49:22','2025-05-25 07:49:22'),(7,1,4,51,NULL,0.00,10000.00,'IQD',1.000000,'2025-05-25 09:38:00','2025-05-25 09:38:00'),(8,1,4,91,NULL,10000.00,0.00,'IQD',1.000000,'2025-05-25 09:38:00','2025-05-25 09:38:00'),(9,1,5,51,NULL,0.00,5000.00,'IQD',1.000000,'2025-05-26 09:55:30','2025-05-26 09:55:30'),(10,1,5,91,NULL,5000.00,0.00,'IQD',1.000000,'2025-05-26 09:55:30','2025-05-26 09:55:30'),(11,1,6,51,NULL,0.00,1500.00,'IQD',1.000000,'2025-05-28 08:55:47','2025-05-28 08:55:47'),(12,1,6,94,NULL,1500.00,0.00,'IQD',1.000000,'2025-05-28 08:55:47','2025-05-28 08:55:47'),(13,1,7,51,NULL,0.00,10000.00,'IQD',1.000000,'2025-05-28 10:22:29','2025-05-28 10:22:29'),(14,1,7,91,NULL,10000.00,0.00,'IQD',1.000000,'2025-05-28 10:22:29','2025-05-28 10:22:29'),(15,1,8,51,NULL,0.00,10000.00,'IQD',1.000000,'2025-05-29 07:47:31','2025-05-29 07:47:31'),(16,1,8,91,NULL,10000.00,0.00,'IQD',1.000000,'2025-05-29 07:47:31','2025-05-29 07:47:31'),(17,1,9,51,NULL,0.00,5000.00,'IQD',1.000000,'2025-05-29 08:33:46','2025-05-29 08:33:46'),(18,1,9,92,NULL,5000.00,0.00,'IQD',1.000000,'2025-05-29 08:33:46','2025-05-29 08:33:46'),(19,1,10,51,NULL,0.00,5000.00,'IQD',1.000000,'2025-05-29 09:22:39','2025-05-29 09:22:39'),(20,1,10,91,NULL,5000.00,0.00,'IQD',1.000000,'2025-05-29 09:22:39','2025-05-29 09:22:39'),(21,1,11,51,NULL,0.00,15000.00,'IQD',1.000000,'2025-05-29 09:58:44','2025-05-29 09:58:44'),(22,1,11,91,NULL,15000.00,0.00,'IQD',1.000000,'2025-05-29 09:58:44','2025-05-29 09:58:44'),(23,1,12,7,'استحقاق فاتورة INV-00001',10361.00,0.00,'USD',1412.500000,'2025-05-29 12:37:11','2025-05-29 12:37:11'),(24,1,12,31,'إيراد فاتورة INV-00001',0.00,10361.00,'USD',1412.500000,'2025-05-29 12:37:11','2025-05-29 12:37:11'),(25,1,13,3,'استلام نقد لفاتورة INV-00001',10361.00,0.00,'USD',1412.500000,'2025-05-29 12:37:24','2025-05-29 12:37:24'),(26,1,13,7,'تسوية فاتورة INV-00001',0.00,10361.00,'USD',1412.500000,'2025-05-29 12:37:24','2025-05-29 12:37:24'),(27,1,14,51,NULL,0.00,15000.00,'IQD',1.000000,'2025-05-31 06:34:23','2025-05-31 06:34:23'),(28,1,14,88,NULL,15000.00,0.00,'IQD',1.000000,'2025-05-31 06:34:23','2025-05-31 06:34:23'),(29,1,15,3,NULL,0.00,10361.00,'USD',1412.500000,'2025-05-31 10:04:10','2025-05-31 10:04:10'),(30,1,15,51,NULL,14801428.57,0.00,'IQD',1.000000,'2025-05-31 10:04:10','2025-05-31 10:04:10'),(31,1,16,3,'عكس: ',10361.00,0.00,'USD',1412.500000,'2025-05-31 10:37:29','2025-05-31 10:37:29'),(32,1,16,51,'عكس: ',0.00,14801428.57,'IQD',1.000000,'2025-05-31 10:37:29','2025-05-31 10:37:29'),(33,1,17,3,NULL,0.00,10361.00,'USD',1412.500000,'2025-05-31 10:43:53','2025-05-31 10:43:53'),(34,1,17,51,NULL,14634912.50,0.00,'IQD',1.000000,'2025-05-31 10:43:53','2025-05-31 10:43:53'),(35,1,18,51,NULL,0.00,150000.00,'IQD',1.000000,'2025-05-31 10:46:32','2025-05-31 10:46:32'),(36,1,18,88,NULL,150000.00,0.00,'IQD',1.000000,'2025-05-31 10:46:32','2025-05-31 10:46:32'),(37,1,19,51,NULL,0.00,115000.00,'IQD',1.000000,'2025-05-31 10:48:53','2025-05-31 10:48:53'),(38,1,19,90,NULL,115000.00,0.00,'IQD',1.000000,'2025-05-31 10:48:53','2025-05-31 10:48:53'),(39,1,20,51,NULL,0.00,150000.00,'IQD',1.000000,'2025-05-31 10:58:41','2025-05-31 10:58:41'),(40,1,20,85,NULL,150000.00,0.00,'IQD',1.000000,'2025-05-31 10:58:41','2025-05-31 10:58:41'),(41,1,21,51,NULL,0.00,4000000.00,'IQD',1.000000,'2025-05-31 11:54:11','2025-05-31 11:54:11'),(42,1,21,50,NULL,4000000.00,0.00,'IQD',1.000000,'2025-05-31 11:54:11','2025-05-31 11:54:11'),(43,1,22,51,NULL,0.00,100000.00,'IQD',1.000000,'2025-06-01 07:18:30','2025-06-01 07:18:30'),(44,1,22,87,NULL,100000.00,0.00,'IQD',1.000000,'2025-06-01 07:18:30','2025-06-01 07:18:30'),(45,1,23,51,NULL,0.00,40000.00,'IQD',1.000000,'2025-06-01 07:35:40','2025-06-01 07:35:40'),(46,1,23,93,NULL,40000.00,0.00,'IQD',1.000000,'2025-06-01 07:35:40','2025-06-01 07:35:40'),(47,1,24,51,NULL,0.00,10000.00,'IQD',1.000000,'2025-06-01 07:36:49','2025-06-01 07:36:49'),(48,1,24,91,NULL,10000.00,0.00,'IQD',1.000000,'2025-06-01 07:36:50','2025-06-01 07:36:50'),(49,1,25,51,NULL,0.00,5000.00,'IQD',1.000000,'2025-06-01 09:30:03','2025-06-01 09:30:03'),(50,1,25,91,NULL,5000.00,0.00,'IQD',1.000000,'2025-06-01 09:30:03','2025-06-01 09:30:03'),(51,1,26,51,NULL,0.00,200000.00,'IQD',1.000000,'2025-06-01 10:40:06','2025-06-01 10:40:06'),(52,1,26,97,NULL,200000.00,0.00,'IQD',1.000000,'2025-06-01 10:40:07','2025-06-01 10:40:07'),(53,1,27,51,NULL,0.00,250000.00,'IQD',1.000000,'2025-06-01 11:33:18','2025-06-01 11:33:18'),(54,1,27,88,NULL,250000.00,0.00,'IQD',1.000000,'2025-06-01 11:33:18','2025-06-01 11:33:18'),(55,1,28,85,'استحقاق رواتب شهر 2025-05',4250000.00,0.00,'IQD',1.000000,'2025-06-01 11:48:34','2025-06-01 11:48:34'),(56,1,28,72,'ذمم مستحقة للموظفين عن رواتب شهر 2025-05',0.00,4250000.00,'IQD',1.000000,'2025-06-01 11:48:34','2025-06-01 11:48:34'),(57,1,28,37,'استحقاق رواتب شهر 2025-05',15400.00,0.00,'USD',1412.500000,'2025-06-01 11:48:34','2025-06-01 11:48:34'),(58,1,28,24,'ذمم مستحقة للموظفين عن رواتب شهر 2025-05',0.00,15400.00,'USD',1412.500000,'2025-06-01 11:48:34','2025-06-01 11:48:34'),(59,1,29,72,'صرف راتب للموظف fatmasaleh',800000.00,0.00,'IQD',1.000000,'2025-06-01 12:16:15','2025-06-01 12:16:15'),(60,1,29,51,'صرف راتب للموظف fatmasaleh',0.00,800000.00,'IQD',1.000000,'2025-06-01 12:16:15','2025-06-01 12:16:15'),(61,1,30,72,'صرف راتب للموظف nabaa',750000.00,0.00,'IQD',1.000000,'2025-06-01 12:17:54','2025-06-01 12:17:54'),(62,1,30,51,'صرف راتب للموظف nabaa',0.00,750000.00,'IQD',1.000000,'2025-06-01 12:17:54','2025-06-01 12:17:54'),(63,1,31,72,'صرف راتب للموظف mustafa waheed',1000000.00,0.00,'IQD',1.000000,'2025-06-01 12:18:25','2025-06-01 12:18:25'),(64,1,31,51,'صرف راتب للموظف mustafa waheed',0.00,1000000.00,'IQD',1.000000,'2025-06-01 12:18:25','2025-06-01 12:18:25'),(65,1,32,72,'صرف راتب للموظف nafea amaad',800000.00,0.00,'IQD',1.000000,'2025-06-01 12:18:43','2025-06-01 12:18:43'),(66,1,32,51,'صرف راتب للموظف nafea amaad',0.00,800000.00,'IQD',1.000000,'2025-06-01 12:18:43','2025-06-01 12:18:43'),(67,1,33,72,'صرف راتب للموظف zainab moayed',900000.00,0.00,'IQD',1.000000,'2025-06-01 12:19:14','2025-06-01 12:19:14'),(68,1,33,51,'صرف راتب للموظف zainab moayed',0.00,900000.00,'IQD',1.000000,'2025-06-01 12:19:14','2025-06-01 12:19:14'),(69,1,34,51,NULL,0.00,750000.00,'IQD',1.000000,'2025-06-01 12:20:32','2025-06-01 12:20:32'),(70,1,34,85,NULL,750000.00,0.00,'IQD',1.000000,'2025-06-01 12:20:32','2025-06-01 12:20:32'),(71,1,35,51,NULL,0.00,25000.00,'IQD',1.000000,'2025-06-01 12:37:08','2025-06-01 12:37:08'),(72,1,35,88,NULL,25000.00,0.00,'IQD',1.000000,'2025-06-01 12:37:08','2025-06-01 12:37:08'),(73,1,36,51,NULL,0.00,5000.00,'IQD',1.000000,'2025-06-02 09:53:32','2025-06-02 09:53:32'),(74,1,36,91,NULL,5000.00,0.00,'IQD',1.000000,'2025-06-02 09:53:32','2025-06-02 09:53:32'),(75,1,37,51,NULL,0.00,5000.00,'IQD',1.000000,'2025-06-03 09:36:33','2025-06-03 09:36:33'),(76,1,37,91,NULL,5000.00,0.00,'IQD',1.000000,'2025-06-03 09:36:33','2025-06-03 09:36:33'),(77,1,38,51,NULL,0.00,10000.00,'IQD',1.000000,'2025-06-03 12:43:14','2025-06-03 12:43:14'),(78,1,38,88,NULL,10000.00,0.00,'IQD',1.000000,'2025-06-03 12:43:14','2025-06-03 12:43:14'),(79,1,39,51,NULL,0.00,40000.00,'IQD',1.000000,'2025-06-03 12:56:08','2025-06-03 12:56:08'),(80,1,39,97,NULL,40000.00,0.00,'IQD',1.000000,'2025-06-03 12:56:08','2025-06-03 12:56:08'),(81,1,40,51,NULL,0.00,300000.00,'IQD',1.000000,'2025-06-04 08:37:47','2025-06-04 08:37:47'),(82,1,40,97,NULL,300000.00,0.00,'IQD',1.000000,'2025-06-04 08:37:47','2025-06-04 08:37:47'),(83,1,41,51,NULL,0.00,75000.00,'IQD',1.000000,'2025-06-04 08:40:15','2025-06-04 08:40:15'),(84,1,41,97,NULL,75000.00,0.00,'IQD',1.000000,'2025-06-04 08:40:15','2025-06-04 08:40:15'),(85,1,42,51,NULL,0.00,1175000.00,'IQD',1.000000,'2025-06-04 09:10:15','2025-06-04 09:10:15'),(86,1,42,88,NULL,1175000.00,0.00,'IQD',1.000000,'2025-06-04 09:10:15','2025-06-04 09:10:15'),(87,1,43,51,'عكس: ',1175000.00,0.00,'IQD',1.000000,'2025-06-04 09:31:57','2025-06-04 09:31:57'),(88,1,43,88,'عكس: ',0.00,1175000.00,'IQD',1.000000,'2025-06-04 09:31:57','2025-06-04 09:31:57'),(89,1,44,50,NULL,0.00,4000000.00,'IQD',1.000000,'2025-06-04 09:34:47','2025-06-04 09:34:47'),(90,1,44,94,NULL,4000000.00,0.00,'IQD',1.000000,'2025-06-04 09:34:47','2025-06-04 09:34:47'),(91,1,45,51,NULL,0.00,2175000.00,'IQD',1.000000,'2025-06-04 09:36:07','2025-06-04 09:36:07'),(92,1,45,88,NULL,2175000.00,0.00,'IQD',1.000000,'2025-06-04 09:36:07','2025-06-04 09:36:07'),(93,1,46,51,NULL,0.00,5000.00,'IQD',1.000000,'2025-06-04 09:48:54','2025-06-04 09:48:54'),(94,1,46,91,NULL,5000.00,0.00,'IQD',1.000000,'2025-06-04 09:48:54','2025-06-04 09:48:54'),(95,1,47,51,NULL,0.00,10000.00,'IQD',1.000000,'2025-06-04 09:50:08','2025-06-04 09:50:08'),(96,1,47,91,NULL,10000.00,0.00,'IQD',1.000000,'2025-06-04 09:50:08','2025-06-04 09:50:08'),(97,1,48,51,NULL,0.00,5000.00,'IQD',1.000000,'2025-06-04 10:00:36','2025-06-04 10:00:36'),(98,1,48,91,NULL,5000.00,0.00,'IQD',1.000000,'2025-06-04 10:00:36','2025-06-04 10:00:36'),(99,1,49,51,NULL,0.00,10000.00,'IQD',1.000000,'2025-06-04 10:06:52','2025-06-04 10:06:52'),(100,1,49,88,NULL,10000.00,0.00,'IQD',1.000000,'2025-06-04 10:06:52','2025-06-04 10:06:52'),(101,1,50,85,NULL,9940000.00,0.00,'IQD',1.000000,'2025-06-04 22:31:37','2025-06-04 22:31:37'),(102,1,50,97,NULL,0.00,9940000.00,'IQD',1.000000,'2025-06-04 22:31:37','2025-06-04 22:31:37'),(103,1,51,88,NULL,0.00,14610000.00,'IQD',1.000000,'2025-06-06 17:52:40','2025-06-06 17:52:40'),(104,1,51,58,NULL,14610000.00,0.00,'IQD',1.000000,'2025-06-06 17:52:40','2025-06-06 17:52:40'),(105,1,52,88,'عكس: ',14610000.00,0.00,'IQD',1.000000,'2025-06-06 17:55:12','2025-06-06 17:55:12'),(106,1,52,58,'عكس: ',0.00,14610000.00,'IQD',1.000000,'2025-06-06 17:55:12','2025-06-06 17:55:12'),(107,1,53,88,'مصاريف ورواتب شهر ١ قبل نظام مجموع مصاريف ٩٧٤٠ والرواتب ٢٥١٤٨',52332000.00,0.00,'IQD',1.000000,'2025-06-06 23:05:53','2025-06-06 23:05:53'),(108,1,53,58,'مصاريف ورواتب شهر ١ قبل نظام مجموع مصاريف ٩٧٤٠ والرواتب ٢٥١٤٨',0.00,52332000.00,'IQD',1.000000,'2025-06-06 23:05:53','2025-06-06 23:05:53'),(109,1,54,99,'ايرادات شهر ١ سنة ٢٠٢٥ قبل نظام',52804500.00,0.00,'IQD',1.000000,'2025-06-06 23:19:49','2025-06-06 23:19:49'),(110,1,54,79,'ايرادات شهر ١ سنة ٢٠٢٥ قبل نظام',0.00,52804500.00,'IQD',1.000000,'2025-06-06 23:19:49','2025-06-06 23:19:49'),(111,1,55,99,NULL,48735000.00,0.00,'IQD',1.000000,'2025-06-06 23:40:22','2025-06-06 23:40:22'),(112,1,55,79,NULL,0.00,48735000.00,'IQD',1.000000,'2025-06-06 23:40:22','2025-06-06 23:40:22'),(113,1,56,88,NULL,50472000.00,0.00,'IQD',1.000000,'2025-06-06 23:47:16','2025-06-06 23:47:16'),(114,1,56,58,NULL,0.00,50472000.00,'IQD',1.000000,'2025-06-06 23:47:16','2025-06-06 23:47:16'),(115,1,57,99,'ايرادات شهر ٣ سنة ٢٠٢٥ قبل نظام',46500000.00,0.00,'IQD',1.000000,'2025-06-07 00:07:24','2025-06-07 00:07:24'),(116,1,57,79,'ايرادات شهر ٣ سنة ٢٠٢٥ قبل نظام',0.00,46500000.00,'IQD',1.000000,'2025-06-07 00:07:24','2025-06-07 00:07:24'),(117,1,58,88,'مصاريف ورواتب شهر ٣ قبل نظام مجموع مصاريف ٤٩٠٠ والرواتب ٢٥٦٤٨',45822000.00,0.00,'IQD',1.000000,'2025-06-07 00:09:27','2025-06-07 00:09:27'),(118,1,58,58,'مصاريف ورواتب شهر ٣ قبل نظام مجموع مصاريف ٤٩٠٠ والرواتب ٢٥٦٤٨',0.00,45822000.00,'IQD',1.000000,'2025-06-07 00:09:27','2025-06-07 00:09:27'),(119,1,59,99,'ايرادات شهر ٤ سنة ٢٠٢٥ قبل نظام',54000000.00,0.00,'IQD',1.000000,'2025-06-07 00:24:45','2025-06-07 00:24:45'),(120,1,59,79,'ايرادات شهر ٤ سنة ٢٠٢٥ قبل نظام',0.00,54000000.00,'IQD',1.000000,'2025-06-07 00:24:45','2025-06-07 00:24:45'),(121,1,60,88,'مصاريف ورواتب شهر ٤ قبل نظام مجموع مصاريف ٧٩٠٠ والرواتب ٢٥١١٥',49522500.00,0.00,'IQD',1.000000,'2025-06-07 00:27:37','2025-06-07 00:27:37'),(122,1,60,58,'مصاريف ورواتب شهر ٤ قبل نظام مجموع مصاريف ٧٩٠٠ والرواتب ٢٥١١٥',0.00,49522500.00,'IQD',1.000000,'2025-06-07 00:27:37','2025-06-07 00:27:37'),(123,1,61,100,'العجز التراكي الى شهر ٤ سنة ٢٠٢٥',53355000.00,0.00,'IQD',1.000000,'2025-06-07 00:47:48','2025-06-07 00:47:48'),(124,1,61,99,'العجز التراكي الى شهر ٤ سنة ٢٠٢٥',0.00,53355000.00,'IQD',1.000000,'2025-06-07 00:47:48','2025-06-07 00:47:48'),(125,1,62,99,'سلف مدفوعة لتشغيل مشروع',121901700.00,0.00,'IQD',1.000000,'2025-06-07 01:17:50','2025-06-07 01:17:50'),(126,1,62,102,'سلف مدفوعة لتشغيل مشروع حجي عمار',0.00,95029500.00,'IQD',1.000000,'2025-06-07 01:17:50','2025-06-07 01:17:50'),(127,1,62,103,'سلف مدفوعة لتشغيل مشروع محمد حمدان',0.00,26872200.00,'IQD',1.000000,'2025-06-07 01:17:50','2025-06-07 01:17:50'),(128,1,63,58,'تسوية مصاريف 4 أشهر قبل تطبيق النظام',198148500.00,0.00,'IQD',1.000000,'2025-06-07 13:22:10','2025-06-07 13:22:10'),(129,1,63,99,'تسوية مصاريف 4 أشهر قبل تطبيق النظام',0.00,198148500.00,'IQD',1.000000,'2025-06-07 13:22:10','2025-06-07 13:22:10'),(130,1,64,7,'استحقاق فاتورة INV-00002',8500.00,0.00,'USD',1412.500000,'2025-06-07 20:45:59','2025-06-07 20:45:59'),(131,1,64,31,'إيراد فاتورة INV-00002',0.00,8500.00,'USD',1412.500000,'2025-06-07 20:45:59','2025-06-07 20:45:59'),(132,1,65,2,'استلام نقد لفاتورة INV-00002',8500.00,0.00,'USD',1412.500000,'2025-06-07 20:47:10','2025-06-07 20:47:10'),(133,1,65,7,'تسوية فاتورة INV-00002',0.00,8500.00,'USD',1412.500000,'2025-06-07 20:47:10','2025-06-07 20:47:10'),(134,1,66,50,'عكس: ',4000000.00,0.00,'IQD',1.000000,'2025-06-07 20:49:04','2025-06-07 20:49:04'),(135,1,66,94,'عكس: ',0.00,4000000.00,'IQD',1.000000,'2025-06-07 20:49:04','2025-06-07 20:49:04'),(136,NULL,67,50,NULL,0.00,4000000.00,'IQD',1.000000,'2025-06-08 00:24:01','2025-06-08 00:24:01'),(137,NULL,67,92,NULL,4000000.00,0.00,'IQD',1.000000,'2025-06-08 00:24:01','2025-06-08 00:24:01'),(138,NULL,68,2,NULL,0.00,8500.00,'USD',1412.500000,'2025-06-08 09:28:38','2025-06-08 09:28:38'),(139,NULL,68,161,NULL,8500.00,0.00,'USD',1412.500000,'2025-06-08 09:28:38','2025-06-08 09:28:38'),(140,NULL,69,51,NULL,0.00,15000.00,'IQD',1.000000,'2025-06-10 09:36:57','2025-06-10 09:36:57'),(141,NULL,69,91,NULL,15000.00,0.00,'IQD',1.000000,'2025-06-10 09:36:57','2025-06-10 09:36:57'),(142,NULL,70,7,'استحقاق فاتورة INV-00003',16500.00,0.00,'USD',1412.500000,'2025-06-10 09:53:47','2025-06-10 09:53:47'),(143,NULL,70,31,'إيراد فاتورة INV-00003',0.00,16500.00,'USD',1412.500000,'2025-06-10 09:53:47','2025-06-10 09:53:47'),(144,NULL,71,3,'استلام نقد لفاتورة INV-00003',16500.00,0.00,'USD',1412.500000,'2025-06-10 10:20:10','2025-06-10 10:20:10'),(145,NULL,71,7,'تسوية فاتورة INV-00003',0.00,16500.00,'USD',1412.500000,'2025-06-10 10:20:10','2025-06-10 10:20:10'),(146,NULL,72,51,NULL,0.00,10000.00,'IQD',1.000000,'2025-06-10 12:38:16','2025-06-10 12:38:16'),(147,NULL,72,91,NULL,10000.00,0.00,'IQD',1.000000,'2025-06-10 12:38:16','2025-06-10 12:38:16'),(148,NULL,73,51,NULL,0.00,3000.00,'IQD',1.000000,'2025-06-10 12:43:23','2025-06-10 12:43:23'),(149,NULL,73,93,NULL,3000.00,0.00,'IQD',1.000000,'2025-06-10 12:43:23','2025-06-10 12:43:23'),(150,NULL,74,51,NULL,0.00,200000.00,'IQD',1.000000,'2025-06-10 12:45:35','2025-06-10 12:45:35'),(151,NULL,74,103,NULL,200000.00,0.00,'IQD',1.000000,'2025-06-10 12:45:35','2025-06-10 12:45:35'),(152,NULL,75,51,NULL,0.00,500000.00,'IQD',1.000000,'2025-06-11 05:39:37','2025-06-11 05:39:37'),(153,NULL,75,103,NULL,500000.00,0.00,'IQD',1.000000,'2025-06-11 05:39:37','2025-06-11 05:39:37'),(154,NULL,76,51,NULL,0.00,20000.00,'IQD',1.000000,'2025-06-11 07:25:25','2025-06-11 07:25:25'),(155,NULL,76,91,NULL,20000.00,0.00,'IQD',1.000000,'2025-06-11 07:25:25','2025-06-11 07:25:25'),(156,NULL,77,51,NULL,0.00,150000.00,'IQD',1.000000,'2025-06-11 07:43:03','2025-06-11 07:43:03'),(157,NULL,77,88,NULL,150000.00,0.00,'IQD',1.000000,'2025-06-11 07:43:03','2025-06-11 07:43:03'),(158,NULL,78,51,'عكس: ',500000.00,0.00,'IQD',1.000000,'2025-06-11 09:28:18','2025-06-11 09:28:18'),(159,NULL,78,103,'عكس: ',0.00,500000.00,'IQD',1.000000,'2025-06-11 09:28:18','2025-06-11 09:28:18'),(160,NULL,79,51,'عكس: ',200000.00,0.00,'IQD',1.000000,'2025-06-11 09:28:34','2025-06-11 09:28:34'),(161,NULL,79,103,'عكس: ',0.00,200000.00,'IQD',1.000000,'2025-06-11 09:28:34','2025-06-11 09:28:34'),(162,NULL,80,24,'صرف راتب للموظف ibraham',1900.00,0.00,'USD',1420.000000,'2025-06-11 09:30:13','2025-06-11 09:30:13'),(163,NULL,80,3,'صرف راتب للموظف ibraham',0.00,1900.00,'USD',1420.000000,'2025-06-11 09:30:13','2025-06-11 09:30:13'),(164,NULL,81,51,NULL,0.00,5000.00,'IQD',1.000000,'2025-06-11 09:42:45','2025-06-11 09:42:45'),(165,NULL,81,91,NULL,5000.00,0.00,'IQD',1.000000,'2025-06-11 09:42:45','2025-06-11 09:42:45'),(166,NULL,82,51,NULL,0.00,700000.00,'IQD',1.000000,'2025-06-11 09:48:01','2025-06-11 09:48:01'),(167,NULL,82,97,NULL,700000.00,0.00,'IQD',1.000000,'2025-06-11 09:48:01','2025-06-11 09:48:01'),(168,NULL,83,24,'صرف راتب للموظف kaly',1400.00,0.00,'USD',1420.000000,'2025-06-11 09:49:23','2025-06-11 09:49:23'),(169,NULL,83,3,'صرف راتب للموظف kaly',0.00,1400.00,'USD',1420.000000,'2025-06-11 09:49:23','2025-06-11 09:49:23'),(170,NULL,84,7,'استحقاق فاتورة INV-00004',3000.00,0.00,'USD',1420.000000,'2025-06-11 11:51:42','2025-06-11 11:51:42'),(171,NULL,84,31,'إيراد فاتورة INV-00004',0.00,3000.00,'USD',1420.000000,'2025-06-11 11:51:42','2025-06-11 11:51:42'),(172,NULL,85,3,'استلام نقد لفاتورة INV-00004',3000.00,0.00,'USD',1420.000000,'2025-06-11 11:51:50','2025-06-11 11:51:50'),(173,NULL,85,7,'تسوية فاتورة INV-00004',0.00,3000.00,'USD',1420.000000,'2025-06-11 11:51:50','2025-06-11 11:51:50'),(174,NULL,86,51,NULL,0.00,31000.00,'IQD',1.000000,'2025-06-11 12:02:54','2025-06-11 12:02:54'),(175,NULL,86,88,NULL,31000.00,0.00,'IQD',1.000000,'2025-06-11 12:02:54','2025-06-11 12:02:54'),(176,NULL,87,51,NULL,0.00,25000.00,'IQD',1.000000,'2025-06-11 12:10:54','2025-06-11 12:10:54'),(177,NULL,87,90,NULL,25000.00,0.00,'IQD',1.000000,'2025-06-11 12:10:54','2025-06-11 12:10:54'),(178,NULL,88,51,NULL,0.00,5000.00,'IQD',1.000000,'2025-06-11 12:12:02','2025-06-11 12:12:02'),(179,NULL,88,91,NULL,5000.00,0.00,'IQD',1.000000,'2025-06-11 12:12:02','2025-06-11 12:12:02'),(180,NULL,89,3,NULL,0.00,2454.00,'USD',1420.000000,'2025-06-11 12:38:03','2025-06-11 12:38:03'),(181,NULL,89,161,NULL,2454.00,0.00,'USD',1420.000000,'2025-06-11 12:38:03','2025-06-11 12:38:03'),(182,NULL,90,24,'صرف راتب للموظف arkan',1900.00,0.00,'USD',1420.000000,'2025-06-11 13:53:15','2025-06-11 13:53:15'),(183,NULL,90,3,'صرف راتب للموظف arkan',0.00,1900.00,'USD',1420.000000,'2025-06-11 13:53:15','2025-06-11 13:53:15'),(184,NULL,91,51,NULL,0.00,300000.00,'IQD',1.000000,'2025-06-12 10:27:59','2025-06-12 10:27:59'),(185,NULL,91,97,NULL,300000.00,0.00,'IQD',1.000000,'2025-06-12 10:27:59','2025-06-12 10:27:59'),(186,NULL,92,51,NULL,0.00,30000.00,'IQD',1.000000,'2025-06-12 10:28:57','2025-06-12 10:28:57'),(187,NULL,92,97,NULL,30000.00,0.00,'IQD',1.000000,'2025-06-12 10:28:57','2025-06-12 10:28:57'),(188,NULL,93,51,NULL,0.00,18000.00,'IQD',1.000000,'2025-06-12 10:29:46','2025-06-12 10:29:46'),(189,NULL,93,88,NULL,18000.00,0.00,'IQD',1.000000,'2025-06-12 10:29:46','2025-06-12 10:29:46'),(190,NULL,94,51,NULL,0.00,5000.00,'IQD',1.000000,'2025-06-12 10:30:17','2025-06-12 10:30:17'),(191,NULL,94,91,NULL,5000.00,0.00,'IQD',1.000000,'2025-06-12 10:30:17','2025-06-12 10:30:17'),(192,NULL,95,51,NULL,0.00,10000.00,'IQD',1.000000,'2025-06-12 10:31:31','2025-06-12 10:31:31'),(193,NULL,95,91,NULL,10000.00,0.00,'IQD',1.000000,'2025-06-12 10:31:31','2025-06-12 10:31:31'),(194,NULL,96,51,NULL,0.00,3000.00,'IQD',1.000000,'2025-06-12 10:33:49','2025-06-12 10:33:49'),(195,NULL,96,93,NULL,3000.00,0.00,'IQD',1.000000,'2025-06-12 10:33:49','2025-06-12 10:33:49'),(196,NULL,97,51,NULL,0.00,10000.00,'IQD',1.000000,'2025-06-12 10:36:46','2025-06-12 10:36:46'),(197,NULL,97,90,NULL,10000.00,0.00,'IQD',1.000000,'2025-06-12 10:36:46','2025-06-12 10:36:46'),(198,NULL,98,7,'استحقاق فاتورة INV-00005',500.00,0.00,'USD',1420.000000,'2025-06-12 11:44:30','2025-06-12 11:44:30'),(199,NULL,98,31,'إيراد فاتورة INV-00005',0.00,500.00,'USD',1420.000000,'2025-06-12 11:44:31','2025-06-12 11:44:31'),(200,NULL,99,3,'استلام نقد لفاتورة INV-00005',500.00,0.00,'USD',1420.000000,'2025-06-12 11:45:41','2025-06-12 11:45:41'),(201,NULL,99,7,'تسوية فاتورة INV-00005',0.00,500.00,'USD',1420.000000,'2025-06-12 11:45:41','2025-06-12 11:45:41'),(202,NULL,100,3,NULL,0.00,500.00,'USD',1420.000000,'2025-06-12 11:51:28','2025-06-12 11:51:28'),(203,NULL,100,51,NULL,710000.00,0.00,'IQD',1.000000,'2025-06-12 11:51:28','2025-06-12 11:51:28'),(204,NULL,101,51,NULL,0.00,710000.00,'IQD',1.000000,'2025-06-12 11:53:38','2025-06-12 11:53:38'),(205,NULL,101,97,NULL,710000.00,0.00,'IQD',1.000000,'2025-06-12 11:53:38','2025-06-12 11:53:38'),(206,NULL,102,24,'صرف راتب للموظف umer',1000.00,0.00,'USD',1420.000000,'2025-06-14 09:51:34','2025-06-14 09:51:34'),(207,NULL,102,3,'صرف راتب للموظف umer',0.00,1000.00,'USD',1420.000000,'2025-06-14 09:51:34','2025-06-14 09:51:34'),(208,NULL,103,24,'صرف راتب للموظف tareq',750.00,0.00,'USD',1420.000000,'2025-06-14 10:39:46','2025-06-14 10:39:46'),(209,NULL,103,3,'صرف راتب للموظف tareq',0.00,750.00,'USD',1420.000000,'2025-06-14 10:39:46','2025-06-14 10:39:46'),(210,NULL,104,24,'صرف راتب للموظف ateeb',750.00,0.00,'USD',1420.000000,'2025-06-14 10:40:41','2025-06-14 10:40:41'),(211,NULL,104,3,'صرف راتب للموظف ateeb',0.00,750.00,'USD',1420.000000,'2025-06-14 10:40:41','2025-06-14 10:40:41'),(212,NULL,105,24,'صرف راتب للموظف Hisham',500.00,0.00,'USD',1420.000000,'2025-06-14 10:40:50','2025-06-14 10:40:50'),(213,NULL,105,3,'صرف راتب للموظف Hisham',0.00,500.00,'USD',1420.000000,'2025-06-14 10:40:50','2025-06-14 10:40:50'),(214,NULL,106,24,'صرف راتب للموظف Hothaifa Jaber',1400.00,0.00,'USD',1420.000000,'2025-06-14 10:40:58','2025-06-14 10:40:58'),(215,NULL,106,3,'صرف راتب للموظف Hothaifa Jaber',0.00,1400.00,'USD',1420.000000,'2025-06-14 10:40:58','2025-06-14 10:40:58'),(216,NULL,107,24,'صرف راتب للموظف Mohamed jaber',500.00,0.00,'USD',1420.000000,'2025-06-14 10:41:07','2025-06-14 10:41:07'),(217,NULL,107,3,'صرف راتب للموظف Mohamed jaber',0.00,500.00,'USD',1420.000000,'2025-06-14 10:41:07','2025-06-14 10:41:07'),(218,NULL,108,24,'صرف راتب للموظف Mohammad hassnain',750.00,0.00,'USD',1420.000000,'2025-06-14 10:41:16','2025-06-14 10:41:16'),(219,NULL,108,3,'صرف راتب للموظف Mohammad hassnain',0.00,750.00,'USD',1420.000000,'2025-06-14 10:41:16','2025-06-14 10:41:16'),(220,NULL,109,24,'صرف راتب للموظف naveed',1150.00,0.00,'USD',1420.000000,'2025-06-14 10:41:25','2025-06-14 10:41:25'),(221,NULL,109,3,'صرف راتب للموظف naveed',0.00,1150.00,'USD',1420.000000,'2025-06-14 10:41:25','2025-06-14 10:41:25'),(222,NULL,110,24,'صرف راتب للموظف firas',500.00,0.00,'USD',1420.000000,'2025-06-14 10:41:33','2025-06-14 10:41:33'),(223,NULL,110,3,'صرف راتب للموظف firas',0.00,500.00,'USD',1420.000000,'2025-06-14 10:41:33','2025-06-14 10:41:33'),(224,NULL,111,24,'صرف راتب للموظف siraj',800.00,0.00,'USD',1420.000000,'2025-06-14 10:41:41','2025-06-14 10:41:41'),(225,NULL,111,3,'صرف راتب للموظف siraj',0.00,800.00,'USD',1420.000000,'2025-06-14 10:41:41','2025-06-14 10:41:41'),(226,NULL,112,24,'صرف راتب للموظف sadqain',1500.00,0.00,'USD',1420.000000,'2025-06-14 10:41:56','2025-06-14 10:41:56'),(227,NULL,112,3,'صرف راتب للموظف sadqain',0.00,1500.00,'USD',1420.000000,'2025-06-14 10:41:56','2025-06-14 10:41:56'),(228,NULL,113,24,'صرف راتب للموظف usama',600.00,0.00,'USD',1420.000000,'2025-06-14 10:42:07','2025-06-14 10:42:07'),(229,NULL,113,3,'صرف راتب للموظف usama',0.00,600.00,'USD',1420.000000,'2025-06-14 10:42:07','2025-06-14 10:42:07'),(230,NULL,114,3,NULL,0.00,700.00,'USD',1420.000000,'2025-06-14 10:44:59','2025-06-14 10:44:59'),(231,NULL,114,37,NULL,700.00,0.00,'USD',1420.000000,'2025-06-14 10:44:59','2025-06-14 10:44:59'),(232,NULL,115,3,NULL,0.00,546.00,'USD',1420.000000,'2025-06-14 11:37:21','2025-06-14 11:37:21'),(233,NULL,115,51,NULL,775320.00,0.00,'IQD',1.000000,'2025-06-14 11:37:21','2025-06-14 11:37:21'),(234,NULL,116,3,NULL,0.00,400.00,'USD',1420.000000,'2025-06-15 06:10:49','2025-06-15 06:10:49'),(235,NULL,116,161,NULL,400.00,0.00,'USD',1420.000000,'2025-06-15 06:10:49','2025-06-15 06:10:49'),(236,NULL,117,51,NULL,0.00,750000.00,'IQD',1.000000,'2025-06-15 06:22:02','2025-06-15 06:22:02'),(237,NULL,117,85,NULL,750000.00,0.00,'IQD',1.000000,'2025-06-15 06:22:02','2025-06-15 06:22:02'),(238,NULL,118,51,NULL,375000.00,0.00,'IQD',1.000000,'2025-06-15 11:59:25','2025-06-15 11:59:25'),(239,NULL,118,79,NULL,0.00,375000.00,'IQD',1.000000,'2025-06-15 11:59:25','2025-06-15 11:59:25'),(240,NULL,119,51,NULL,0.00,700000.00,'IQD',1.000000,'2025-06-15 12:38:19','2025-06-15 12:38:19'),(241,NULL,119,87,NULL,700000.00,0.00,'IQD',1.000000,'2025-06-15 12:38:19','2025-06-15 12:38:19'),(242,NULL,120,51,NULL,0.00,10000.00,'IQD',1.000000,'2025-06-16 05:24:05','2025-06-16 05:24:05'),(243,NULL,120,88,NULL,10000.00,0.00,'IQD',1.000000,'2025-06-16 05:24:05','2025-06-16 05:24:05'),(244,NULL,121,51,NULL,0.00,5000.00,'IQD',1.000000,'2025-06-16 09:26:42','2025-06-16 09:26:42'),(245,NULL,121,91,NULL,5000.00,0.00,'IQD',1.000000,'2025-06-16 09:26:42','2025-06-16 09:26:42'),(246,NULL,122,51,NULL,0.00,30000.00,'IQD',1.000000,'2025-06-16 13:00:57','2025-06-16 13:00:57'),(247,NULL,122,90,NULL,30000.00,0.00,'IQD',1.000000,'2025-06-16 13:00:57','2025-06-16 13:00:57'),(248,NULL,123,51,NULL,0.00,5000.00,'IQD',1.000000,'2025-06-17 12:30:25','2025-06-17 12:30:25'),(249,NULL,123,91,NULL,5000.00,0.00,'IQD',1.000000,'2025-06-17 12:30:25','2025-06-17 12:30:25'),(250,NULL,124,51,NULL,0.00,5000.00,'IQD',1.000000,'2025-06-18 11:08:36','2025-06-18 11:08:36'),(251,NULL,124,91,NULL,5000.00,0.00,'IQD',1.000000,'2025-06-18 11:08:36','2025-06-18 11:08:36'),(252,NULL,125,51,NULL,500000.00,0.00,'IQD',1.000000,'2025-06-18 11:18:06','2025-06-18 11:18:06'),(253,NULL,125,97,NULL,0.00,500000.00,'IQD',1.000000,'2025-06-18 11:18:06','2025-06-18 11:18:06'),(254,NULL,126,51,NULL,0.00,180000.00,'IQD',1.000000,'2025-06-18 11:27:35','2025-06-18 11:27:35'),(255,NULL,126,93,NULL,180000.00,0.00,'IQD',1.000000,'2025-06-18 11:27:35','2025-06-18 11:27:35'),(256,NULL,127,51,NULL,0.00,320000.00,'IQD',1.000000,'2025-06-18 11:31:16','2025-06-18 11:31:16'),(257,NULL,127,160,NULL,320000.00,0.00,'IQD',1.000000,'2025-06-18 11:31:16','2025-06-18 11:31:16'),(258,NULL,128,51,NULL,0.00,36000.00,'IQD',1.000000,'2025-06-19 07:05:08','2025-06-19 07:05:08'),(259,NULL,128,88,NULL,36000.00,0.00,'IQD',1.000000,'2025-06-19 07:05:08','2025-06-19 07:05:08'),(260,NULL,129,51,NULL,0.00,190000.00,'IQD',1.000000,'2025-06-19 09:42:22','2025-06-19 09:42:22'),(261,NULL,129,58,NULL,190000.00,0.00,'IQD',1.000000,'2025-06-19 09:42:22','2025-06-19 09:42:22'),(262,NULL,130,51,NULL,0.00,5000.00,'IQD',1.000000,'2025-06-19 09:48:16','2025-06-19 09:48:16'),(263,NULL,130,91,NULL,5000.00,0.00,'IQD',1.000000,'2025-06-19 09:48:16','2025-06-19 09:48:16'),(264,NULL,131,51,NULL,0.00,5000.00,'IQD',1.000000,'2025-06-22 11:43:28','2025-06-22 11:43:28'),(265,NULL,131,91,NULL,5000.00,0.00,'IQD',1.000000,'2025-06-22 11:43:28','2025-06-22 11:43:28'),(266,NULL,132,51,NULL,0.00,14000.00,'IQD',1.000000,'2025-06-23 07:21:39','2025-06-23 07:21:39'),(267,NULL,132,88,NULL,14000.00,0.00,'IQD',1.000000,'2025-06-23 07:21:39','2025-06-23 07:21:39'),(268,NULL,133,51,NULL,0.00,5000.00,'IQD',1.000000,'2025-06-23 09:43:36','2025-06-23 09:43:36'),(269,NULL,133,91,NULL,5000.00,0.00,'IQD',1.000000,'2025-06-23 09:43:36','2025-06-23 09:43:36'),(270,NULL,134,51,NULL,0.00,5000.00,'IQD',1.000000,'2025-06-24 10:53:02','2025-06-24 10:53:02'),(271,NULL,134,91,NULL,5000.00,0.00,'IQD',1.000000,'2025-06-24 10:53:02','2025-06-24 10:53:02'),(272,NULL,135,51,NULL,0.00,10000.00,'IQD',1.000000,'2025-06-24 11:30:39','2025-06-24 11:30:39'),(273,NULL,135,88,NULL,10000.00,0.00,'IQD',1.000000,'2025-06-24 11:30:39','2025-06-24 11:30:39'),(274,NULL,136,51,NULL,305000.00,0.00,'IQD',1.000000,'2025-06-24 11:53:16','2025-06-24 11:53:16'),(275,NULL,136,97,NULL,0.00,305000.00,'IQD',1.000000,'2025-06-24 11:53:16','2025-06-24 11:53:16'),(276,NULL,137,51,NULL,0.00,305000.00,'IQD',1.000000,'2025-06-24 11:56:21','2025-06-24 11:56:21'),(277,NULL,137,160,NULL,305000.00,0.00,'IQD',1.000000,'2025-06-24 11:56:21','2025-06-24 11:56:21'),(278,NULL,138,51,NULL,50000.00,0.00,'IQD',1.000000,'2025-06-24 11:58:20','2025-06-24 11:58:20'),(279,NULL,138,97,NULL,0.00,50000.00,'IQD',1.000000,'2025-06-24 11:58:20','2025-06-24 11:58:20'),(280,NULL,139,51,NULL,0.00,5000.00,'IQD',1.000000,'2025-06-25 06:45:46','2025-06-25 06:45:46'),(281,NULL,139,88,NULL,5000.00,0.00,'IQD',1.000000,'2025-06-25 06:45:46','2025-06-25 06:45:46'),(282,NULL,140,51,NULL,0.00,25000.00,'IQD',1.000000,'2025-06-25 12:17:56','2025-06-25 12:17:56'),(283,NULL,140,91,NULL,25000.00,0.00,'IQD',1.000000,'2025-06-25 12:17:56','2025-06-25 12:17:56'),(284,NULL,141,7,'استحقاق فاتورة INV-00006',1587.00,0.00,'USD',1420.000000,'2025-06-25 12:51:56','2025-06-25 12:51:56'),(285,NULL,141,31,'إيراد فاتورة INV-00006',0.00,1587.00,'USD',1420.000000,'2025-06-25 12:51:56','2025-06-25 12:51:56'),(286,NULL,142,3,'استلام نقد لفاتورة INV-00006',1587.00,0.00,'USD',1420.000000,'2025-06-25 12:52:02','2025-06-25 12:52:02'),(287,NULL,142,7,'تسوية فاتورة INV-00006',0.00,1587.00,'USD',1420.000000,'2025-06-25 12:52:02','2025-06-25 12:52:02'),(288,NULL,143,3,NULL,0.00,1587.00,'USD',1420.000000,'2025-06-26 06:12:31','2025-06-26 06:12:31'),(289,NULL,143,51,NULL,1587.00,0.00,'IQD',1420.000000,'2025-06-26 06:12:31','2025-06-26 06:12:31'),(290,NULL,144,3,'عكس: ',1587.00,0.00,'USD',1420.000000,'2025-06-26 09:17:59','2025-06-26 09:17:59'),(291,NULL,144,51,'عكس: ',0.00,1587.00,'IQD',1420.000000,'2025-06-26 09:17:59','2025-06-26 09:17:59'),(292,NULL,145,3,NULL,0.00,1587.00,'USD',1420.000000,'2025-06-26 09:23:17','2025-06-26 09:23:17'),(293,NULL,145,51,NULL,1587.00,0.00,'IQD',0.000705,'2025-06-26 09:23:17','2025-06-26 09:23:17'),(294,NULL,146,3,'عكس: ',1587.00,0.00,'USD',1420.000000,'2025-06-26 09:24:53','2025-06-26 09:24:53'),(295,NULL,146,51,'عكس: ',0.00,1587.00,'IQD',0.000705,'2025-06-26 09:24:53','2025-06-26 09:24:53'),(296,NULL,147,3,NULL,0.00,1587.00,'USD',1420.000000,'2025-06-26 10:20:00','2025-06-26 10:20:00'),(297,NULL,147,51,NULL,2253540.00,0.00,'IQD',1.000000,'2025-06-26 10:20:00','2025-06-26 10:20:00'),(298,NULL,148,51,NULL,0.00,2000000.00,'IQD',1.000000,'2025-06-26 10:21:13','2025-06-26 10:21:13'),(299,NULL,148,50,NULL,2000000.00,0.00,'IQD',1.000000,'2025-06-26 10:21:13','2025-06-26 10:21:13'),(300,NULL,149,51,NULL,0.00,150000.00,'IQD',1.000000,'2025-06-28 07:18:12','2025-06-28 07:18:12'),(301,NULL,149,88,NULL,150000.00,0.00,'IQD',1.000000,'2025-06-28 07:18:12','2025-06-28 07:18:12'),(302,NULL,150,51,NULL,0.00,5000.00,'IQD',1.000000,'2025-06-28 07:20:13','2025-06-28 07:20:13'),(303,NULL,150,91,NULL,5000.00,0.00,'IQD',1.000000,'2025-06-28 07:20:13','2025-06-28 07:20:13'),(304,NULL,151,51,NULL,0.00,10000.00,'IQD',1.000000,'2025-06-29 08:55:09','2025-06-29 08:55:09'),(305,NULL,151,91,NULL,10000.00,0.00,'IQD',1.000000,'2025-06-29 08:55:09','2025-06-29 08:55:09'),(306,NULL,152,51,NULL,0.00,15000.00,'IQD',1.000000,'2025-06-29 11:03:23','2025-06-29 11:03:23'),(307,NULL,152,91,NULL,15000.00,0.00,'IQD',1.000000,'2025-06-29 11:03:23','2025-06-29 11:03:23'),(308,NULL,153,51,NULL,0.00,50000.00,'IQD',1.000000,'2025-06-30 08:33:19','2025-06-30 08:33:19'),(309,NULL,153,88,NULL,50000.00,0.00,'IQD',1.000000,'2025-06-30 08:33:19','2025-06-30 08:33:19'),(310,NULL,154,51,NULL,0.00,15000.00,'IQD',1.000000,'2025-06-30 09:05:00','2025-06-30 09:05:00'),(311,NULL,154,91,NULL,15000.00,0.00,'IQD',1.000000,'2025-06-30 09:05:00','2025-06-30 09:05:00'),(312,NULL,155,51,NULL,0.00,10000.00,'IQD',1.000000,'2025-06-30 09:17:28','2025-06-30 09:17:28'),(313,NULL,155,91,NULL,10000.00,0.00,'IQD',1.000000,'2025-06-30 09:17:28','2025-06-30 09:17:28'),(314,NULL,156,50,NULL,0.00,2000000.00,'IQD',1.000000,'2025-06-30 10:47:50','2025-06-30 10:47:50'),(315,NULL,156,160,NULL,2000000.00,0.00,'IQD',1.000000,'2025-06-30 10:47:50','2025-06-30 10:47:50'),(316,NULL,157,7,'استحقاق فاتورة INV-00007',10361.00,0.00,'USD',1415.000000,'2025-06-30 11:47:45','2025-06-30 11:47:45'),(317,NULL,157,31,'إيراد فاتورة INV-00007',0.00,10361.00,'USD',1415.000000,'2025-06-30 11:47:45','2025-06-30 11:47:45'),(318,NULL,158,3,'استلام نقد لفاتورة INV-00007',10361.00,0.00,'USD',1415.000000,'2025-06-30 11:48:14','2025-06-30 11:48:14'),(319,NULL,158,7,'تسوية فاتورة INV-00007',0.00,10361.00,'USD',1415.000000,'2025-06-30 11:48:14','2025-06-30 11:48:14'),(320,NULL,159,7,'استحقاق فاتورة INV-00008',13500.00,0.00,'USD',1415.000000,'2025-06-30 11:49:29','2025-06-30 11:49:29'),(321,NULL,159,31,'إيراد فاتورة INV-00008',0.00,13500.00,'USD',1415.000000,'2025-06-30 11:49:29','2025-06-30 11:49:29'),(322,NULL,160,3,'استلام نقد لفاتورة INV-00008',13500.00,0.00,'USD',1415.000000,'2025-06-30 11:49:43','2025-06-30 11:49:43'),(323,NULL,160,7,'تسوية فاتورة INV-00008',0.00,13500.00,'USD',1415.000000,'2025-06-30 11:49:43','2025-06-30 11:49:43'),(324,NULL,161,3,NULL,0.00,23861.00,'USD',1415.000000,'2025-06-30 12:01:30','2025-06-30 12:01:30'),(325,NULL,161,51,NULL,33763315.00,0.00,'IQD',1.000000,'2025-06-30 12:01:30','2025-06-30 12:01:30'),(326,NULL,162,51,NULL,0.00,5015000.00,'IQD',1.000000,'2025-06-30 12:32:01','2025-06-30 12:32:01'),(327,NULL,162,160,NULL,5015000.00,0.00,'IQD',1.000000,'2025-06-30 12:32:01','2025-06-30 12:32:01'),(328,NULL,163,51,NULL,0.00,5015000.00,'IQD',1.000000,'2025-06-30 12:34:03','2025-06-30 12:34:03'),(329,NULL,163,160,NULL,5015000.00,0.00,'IQD',1.000000,'2025-06-30 12:34:03','2025-06-30 12:34:03'),(330,NULL,164,51,NULL,0.00,10000.00,'IQD',1.000000,'2025-06-30 12:34:51','2025-06-30 12:34:51'),(331,NULL,164,91,NULL,10000.00,0.00,'IQD',1.000000,'2025-06-30 12:34:51','2025-06-30 12:34:51'),(332,NULL,165,51,NULL,0.00,235000.00,'IQD',1.000000,'2025-06-30 12:53:38','2025-06-30 12:53:38'),(333,NULL,165,97,NULL,235000.00,0.00,'IQD',1.000000,'2025-06-30 12:53:38','2025-06-30 12:53:38'),(334,NULL,166,51,NULL,0.00,8000.00,'IQD',1.000000,'2025-07-01 08:14:28','2025-07-01 08:14:28'),(335,NULL,166,88,NULL,8000.00,0.00,'IQD',1.000000,'2025-07-01 08:14:28','2025-07-01 08:14:28'),(336,NULL,167,85,'استحقاق رواتب شهر 2025-06',6000000.00,0.00,'IQD',1.000000,'2025-07-01 08:28:30','2025-07-01 08:28:30'),(337,NULL,167,72,'ذمم مستحقة للموظفين عن رواتب شهر 2025-06',0.00,6000000.00,'IQD',1.000000,'2025-07-01 08:28:30','2025-07-01 08:28:30'),(338,NULL,167,37,'استحقاق رواتب شهر 2025-06',15300.00,0.00,'USD',1415.000000,'2025-07-01 08:28:30','2025-07-01 08:28:30'),(339,NULL,167,24,'ذمم مستحقة للموظفين عن رواتب شهر 2025-06',0.00,15300.00,'USD',1415.000000,'2025-07-01 08:28:30','2025-07-01 08:28:30'),(340,NULL,168,72,'صرف راتب للموظف علي صادق',150000.00,0.00,'IQD',1.000000,'2025-07-01 09:25:47','2025-07-01 09:25:47'),(341,NULL,168,51,'صرف راتب للموظف علي صادق',0.00,150000.00,'IQD',1.000000,'2025-07-01 09:25:47','2025-07-01 09:25:47'),(342,NULL,169,72,'صرف راتب للموظف فرهاد حسين',750000.00,0.00,'IQD',1.000000,'2025-07-01 09:26:53','2025-07-01 09:26:53'),(343,NULL,169,51,'صرف راتب للموظف فرهاد حسين',0.00,750000.00,'IQD',1.000000,'2025-07-01 09:26:53','2025-07-01 09:26:53'),(344,NULL,170,72,'صرف راتب للموظف mustafa waheed',1100000.00,0.00,'IQD',1.000000,'2025-07-01 09:28:21','2025-07-01 09:28:21'),(345,NULL,170,51,'صرف راتب للموظف mustafa waheed',0.00,1100000.00,'IQD',1.000000,'2025-07-01 09:28:21','2025-07-01 09:28:21'),(346,NULL,171,72,'صرف راتب للموظف nabaa',850000.00,0.00,'IQD',1.000000,'2025-07-01 09:32:01','2025-07-01 09:32:01'),(347,NULL,171,51,'صرف راتب للموظف nabaa',0.00,850000.00,'IQD',1.000000,'2025-07-01 09:32:01','2025-07-01 09:32:01'),(348,NULL,172,72,'صرف راتب للموظف zainab moayed',900000.00,0.00,'IQD',1.000000,'2025-07-01 09:33:58','2025-07-01 09:33:58'),(349,NULL,172,51,'صرف راتب للموظف zainab moayed',0.00,900000.00,'IQD',1.000000,'2025-07-01 09:33:58','2025-07-01 09:33:58'),(350,NULL,173,72,'صرف راتب للموظف zahraa  Jumaah',650000.00,0.00,'IQD',1.000000,'2025-07-01 09:36:14','2025-07-01 09:36:14'),(351,NULL,173,51,'صرف راتب للموظف zahraa  Jumaah',0.00,650000.00,'IQD',1.000000,'2025-07-01 09:36:14','2025-07-01 09:36:14'),(352,NULL,174,72,'صرف راتب للموظف nafea amaad',800000.00,0.00,'IQD',1.000000,'2025-07-01 09:38:54','2025-07-01 09:38:54'),(353,NULL,174,51,'صرف راتب للموظف nafea amaad',0.00,800000.00,'IQD',1.000000,'2025-07-01 09:38:54','2025-07-01 09:38:54'),(354,NULL,175,72,'صرف راتب للموظف fatmasaleh',800000.00,0.00,'IQD',1.000000,'2025-07-01 09:55:42','2025-07-01 09:55:42'),(355,NULL,175,51,'صرف راتب للموظف fatmasaleh',0.00,800000.00,'IQD',1.000000,'2025-07-01 09:55:42','2025-07-01 09:55:42'),(356,NULL,176,51,NULL,0.00,70000.00,'IQD',1.000000,'2025-07-01 10:04:36','2025-07-01 10:04:36'),(357,NULL,176,97,NULL,70000.00,0.00,'IQD',1.000000,'2025-07-01 10:04:36','2025-07-01 10:04:36'),(358,NULL,177,51,NULL,0.00,814000.00,'IQD',1.000000,'2025-07-01 11:38:29','2025-07-01 11:38:29'),(359,NULL,177,97,NULL,814000.00,0.00,'IQD',1.000000,'2025-07-01 11:38:29','2025-07-01 11:38:29'),(360,NULL,178,51,NULL,0.00,1610000.00,'IQD',1.000000,'2025-07-02 07:59:08','2025-07-02 07:59:08'),(361,NULL,178,160,NULL,1610000.00,0.00,'IQD',1.000000,'2025-07-02 07:59:08','2025-07-02 07:59:08'),(362,NULL,179,51,NULL,0.00,728000.00,'IQD',1.000000,'2025-07-02 08:23:20','2025-07-02 08:23:20'),(363,NULL,179,97,NULL,728000.00,0.00,'IQD',1.000000,'2025-07-02 08:23:20','2025-07-02 08:23:20'),(364,NULL,180,51,NULL,0.00,15000.00,'IQD',1.000000,'2025-07-02 08:25:41','2025-07-02 08:25:41'),(365,NULL,180,91,NULL,15000.00,0.00,'IQD',1.000000,'2025-07-02 08:25:41','2025-07-02 08:25:41'),(366,NULL,181,51,NULL,0.00,5000.00,'IQD',1.000000,'2025-07-02 08:26:37','2025-07-02 08:26:37'),(367,NULL,181,91,NULL,5000.00,0.00,'IQD',1.000000,'2025-07-02 08:26:37','2025-07-02 08:26:37'),(368,NULL,182,51,NULL,0.00,1000.00,'IQD',1.000000,'2025-07-02 08:34:04','2025-07-02 08:34:04'),(369,NULL,182,88,NULL,1000.00,0.00,'IQD',1.000000,'2025-07-02 08:34:04','2025-07-02 08:34:04'),(370,NULL,183,51,NULL,0.00,5000.00,'IQD',1.000000,'2025-07-02 08:59:28','2025-07-02 08:59:28'),(371,NULL,183,91,NULL,5000.00,0.00,'IQD',1.000000,'2025-07-02 08:59:28','2025-07-02 08:59:28'),(372,NULL,184,51,NULL,0.00,150000.00,'IQD',1.000000,'2025-07-03 08:42:59','2025-07-03 08:42:59'),(373,NULL,184,88,NULL,150000.00,0.00,'IQD',1.000000,'2025-07-03 08:42:59','2025-07-03 08:42:59'),(374,NULL,185,51,NULL,0.00,5000.00,'IQD',1.000000,'2025-07-03 08:46:06','2025-07-03 08:46:06'),(375,NULL,185,91,NULL,5000.00,0.00,'IQD',1.000000,'2025-07-03 08:46:06','2025-07-03 08:46:06'),(376,NULL,186,51,NULL,0.00,9000.00,'IQD',1.000000,'2025-07-07 07:08:47','2025-07-07 07:08:47'),(377,NULL,186,88,NULL,9000.00,0.00,'IQD',1.000000,'2025-07-07 07:08:47','2025-07-07 07:08:47'),(378,NULL,187,51,NULL,0.00,500000.00,'IQD',1.000000,'2025-07-07 08:00:19','2025-07-07 08:00:19'),(379,NULL,187,160,NULL,500000.00,0.00,'IQD',1.000000,'2025-07-07 08:00:19','2025-07-07 08:00:19'),(380,NULL,188,51,NULL,0.00,5000.00,'IQD',1.000000,'2025-07-07 09:14:00','2025-07-07 09:14:00'),(381,NULL,188,91,NULL,5000.00,0.00,'IQD',1.000000,'2025-07-07 09:14:00','2025-07-07 09:14:00'),(382,NULL,189,85,'اجور الادارة شهر ٦',9940000.00,0.00,'IQD',1.000000,'2025-07-08 12:57:15','2025-07-08 12:57:15'),(383,NULL,189,97,'اجور الادارة شهر ٦',0.00,9940000.00,'IQD',1.000000,'2025-07-08 12:57:15','2025-07-08 12:57:15'),(384,NULL,190,51,NULL,0.00,7384000.00,'IQD',1.000000,'2025-07-09 12:28:21','2025-07-09 12:28:21'),(385,NULL,190,3,NULL,5168.80,0.00,'USD',1420.000000,'2025-07-09 12:28:21','2025-07-09 12:28:21'),(386,NULL,191,51,NULL,0.00,61000.00,'IQD',1.000000,'2025-07-09 12:55:05','2025-07-09 12:55:05'),(387,NULL,191,88,NULL,61000.00,0.00,'IQD',1.000000,'2025-07-09 12:55:05','2025-07-09 12:55:05'),(388,NULL,192,51,NULL,0.00,369000.00,'IQD',1.000000,'2025-07-10 07:31:13','2025-07-10 07:31:13'),(389,NULL,192,87,NULL,369000.00,0.00,'IQD',1.000000,'2025-07-10 07:31:13','2025-07-10 07:31:13'),(390,NULL,193,51,NULL,0.00,10000.00,'IQD',1.000000,'2025-07-10 09:18:38','2025-07-10 09:18:38'),(391,NULL,193,91,NULL,10000.00,0.00,'IQD',1.000000,'2025-07-10 09:18:38','2025-07-10 09:18:38');
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
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'2025_04_28_000001_create_users_table',1),(2,'2025_04_28_000002_create_branches_table',1),(3,'2025_04_28_000003_create_accounts_table',1),(4,'2025_04_28_000004_create_transactions_table',1),(5,'2025_04_28_000006_add_code_to_accounts_table',1),(6,'2025_04_28_143436_create_sessions_table',1),(7,'2025_04_28_143615_create_cache_table',1),(8,'2025_04_28_203550_create_vouchers_table',1),(9,'2025_04_28_203603_add_voucher_id_to_transactions_table',1),(10,'2025_04_28_220859_create_currencies_table',1),(11,'2025_04_28_223223_modify_type_column_in_transactions_table',1),(12,'2025_04_28_231449_add_is_cash_box_to_accounts_table',1),(13,'2025_05_01_110212_add_is_default_to_currencies_table',1),(14,'2025_05_01_110213_create_account_balances_table',1),(15,'2025_05_01_154726_add_currency_and_exchange_rate_to_vouchers_table',1),(16,'2025_05_01_232733_create_invoices_table',1),(17,'2025_05_02_000001_add_code_and_cashbox_to_accounts_table',1),(18,'2025_05_02_000138_create_items_table',1),(19,'2025_05_02_000139_create_customers_table',1),(20,'2025_05_02_000141_create_invoice_items_table',1),(21,'2025_05_02_001528_add_customer_id_to_invoices_table',1),(22,'2025_05_02_201011_create_salary_batches_table',1),(23,'2025_05_02_215900_create_accounting_settings_table',1),(24,'2025_05_02_223317_update_accounting_settings_for_currency_defaults',1),(25,'2025_05_03_000001_add_currency_to_accounts_table',1),(26,'2025_05_03_231438_create_permission_tables',1),(27,'2025_05_04_125328_change_date_column_type_in_vouchers_table',1),(28,'2025_05_04_133316_add_draft_status_to_invoices_table',1),(29,'2025_05_04_135401_add_canceled_status_to_invoices_table',1),(30,'2025_05_06_020000_create_settings_table',1),(31,'2025_05_10_000000_add_invoice_id_to_vouchers_table',1),(32,'2025_05_10_000001_add_invoice_id_to_transactions_table',1),(33,'2025_05_10_100000_create_journal_entries_table',1),(34,'2025_05_10_100001_create_journal_entry_lines_table',1),(35,'2025_05_11_000001_create_employees_table',1),(36,'2025_05_11_000002_create_salaries_table',1),(37,'2025_05_11_000003_create_salary_payments_table',1),(38,'2025_05_11_004422_create_account_user_table',1),(39,'2025_05_11_223653_add_unique_index_to_currencies_code',1),(40,'2025_05_15_000436_add_account_columns_to_vouchers_table',1),(41,'2025_05_17_000001_add_tenant_id_to_tables',1),(42,'2025_05_17_000002_add_tenant_id_to_user_tables',1),(43,'2025_05_17_185809_add_tenant_id_to_remaining_tables',1),(44,'2025_05_17_190023_create_tenants_table',1),(45,'2025_05_17_194157_add_tenant_id_to_permissions_table',1),(46,'2025_05_20_000001_fix_missing_tenant_ids',1),(47,'2025_05_22_231844_add_is_multi_currency_to_journal_entries_table',1),(48,'2025_06_08_001731_allow_null_currency_for_account_groups',2);
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
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  `tenant_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  KEY `model_has_permissions_tenant_id_index` (`tenant_id`),
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
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  `tenant_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  KEY `model_has_roles_tenant_id_index` (`tenant_id`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`, `tenant_id`) VALUES (1,'App\\Models\\User',1,NULL),(2,'App\\Models\\User',2,NULL);
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
  `tenant_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`),
  KEY `permissions_tenant_id_index` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` (`id`, `tenant_id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (1,NULL,'view_users','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(2,NULL,'add_user','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(3,NULL,'edit_user','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(4,NULL,'delete_user','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(5,NULL,'view_roles','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(6,NULL,'add_role','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(7,NULL,'edit_role','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(8,NULL,'delete_role','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(9,NULL,'view_permissions','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(10,NULL,'add_permission','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(11,NULL,'edit_permission','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(12,NULL,'delete_permission','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(13,NULL,'view_accounts','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(14,NULL,'add_account','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(15,NULL,'edit_account','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(16,NULL,'delete_account','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(17,NULL,'view_invoices','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(18,NULL,'add_invoice','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(19,NULL,'edit_invoice','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(20,NULL,'delete_invoice','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(21,NULL,'pay_invoice','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(22,NULL,'print_invoice','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(23,NULL,'view_vouchers','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(24,NULL,'add_voucher','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(25,NULL,'edit_voucher','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(26,NULL,'delete_voucher','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(27,NULL,'print_voucher','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(28,NULL,'view_all_vouchers','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(29,NULL,'cancel_vouchers','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(30,NULL,'view_transactions','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(31,NULL,'add_transaction','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(32,NULL,'edit_transaction','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(33,NULL,'delete_transaction','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(34,NULL,'view_customers','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(35,NULL,'add_customer','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(36,NULL,'edit_customer','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(37,NULL,'delete_customer','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(38,NULL,'view_items','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(39,NULL,'add_item','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(40,NULL,'edit_item','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(41,NULL,'delete_item','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(42,NULL,'view_employees','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(43,NULL,'add_employee','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(44,NULL,'edit_employee','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(45,NULL,'delete_employee','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(46,NULL,'view_salaries','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(47,NULL,'add_salary','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(48,NULL,'edit_salary','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(49,NULL,'delete_salary','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(50,NULL,'view_salary_payments','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(51,NULL,'add_salary_payment','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(52,NULL,'edit_salary_payment','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(53,NULL,'delete_salary_payment','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(54,NULL,'view_salary_batches','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(55,NULL,'add_salary_batch','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(56,NULL,'edit_salary_batch','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(57,NULL,'delete_salary_batch','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(58,NULL,'view_currencies','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(59,NULL,'add_currency','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(60,NULL,'edit_currency','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(61,NULL,'delete_currency','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(62,NULL,'view_branches','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(63,NULL,'add_branch','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(64,NULL,'edit_branch','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(65,NULL,'delete_branch','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(66,NULL,'view_settings','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(67,NULL,'edit_settings','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(68,NULL,'manage_settings','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(69,NULL,'view_journal_entries','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(70,NULL,'add_journal_entry','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(71,NULL,'edit_journal_entry','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(72,NULL,'delete_journal_entry','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(73,NULL,'view_all_journal_entries','web','2025-05-23 22:47:28','2025-05-23 22:47:28'),(74,NULL,'cancel_journal_entries','web','2025-05-23 22:47:28','2025-05-23 22:47:28');
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
  `tenant_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  KEY `role_has_permissions_tenant_id_index` (`tenant_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`, `tenant_id`) VALUES (13,2,NULL),(17,2,NULL),(18,2,NULL),(19,2,NULL),(20,2,NULL),(21,2,NULL),(22,2,NULL),(23,2,NULL),(24,2,NULL),(27,2,NULL),(29,2,NULL),(30,2,NULL),(34,2,NULL),(35,2,NULL),(36,2,NULL),(37,2,NULL),(38,2,NULL),(39,2,NULL),(40,2,NULL),(41,2,NULL),(42,2,NULL),(43,2,NULL),(44,2,NULL),(45,2,NULL),(46,2,NULL),(47,2,NULL),(48,2,NULL),(49,2,NULL),(50,2,NULL),(51,2,NULL),(52,2,NULL),(53,2,NULL),(54,2,NULL),(55,2,NULL),(56,2,NULL),(57,2,NULL),(58,2,NULL),(60,2,NULL);
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
  `tenant_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`),
  KEY `roles_tenant_id_index` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` (`id`, `tenant_id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (1,1,'super-admin','web','2025-05-23 22:47:14','2025-05-23 22:47:14'),(2,NULL,'Fatema saleh','web','2025-05-23 22:48:51','2025-05-23 22:48:51');
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
  `tenant_id` bigint unsigned DEFAULT NULL,
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
  KEY `salaries_tenant_id_index` (`tenant_id`),
  CONSTRAINT `salaries_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salaries`
--

LOCK TABLES `salaries` WRITE;
/*!40000 ALTER TABLE `salaries` DISABLE KEYS */;
INSERT INTO `salaries` (`id`, `tenant_id`, `employee_id`, `basic_salary`, `allowances`, `deductions`, `effective_from`, `effective_to`, `created_at`, `updated_at`) VALUES (1,1,1,1000000.00,'[]','[]','2025-05-01',NULL,'2025-05-24 07:31:39','2025-07-02 07:41:52'),(2,1,2,800000.00,'[]','[]','2025-05-01',NULL,'2025-05-24 07:31:59','2025-05-24 07:31:59'),(3,1,3,900000.00,'[]','[]','2025-05-01',NULL,'2025-05-24 07:32:23','2025-07-02 07:42:12'),(4,1,4,1100000.00,'[]','[]','2025-05-01',NULL,'2025-05-24 07:32:45','2025-06-23 05:18:55'),(5,1,5,900000.00,'[]','[]','2025-05-01',NULL,'2025-05-24 07:33:07','2025-07-02 07:41:23'),(6,1,6,1000.00,'[]','[]','2025-05-01',NULL,'2025-05-24 07:33:28','2025-05-26 09:26:18'),(7,1,7,500.00,'[]','[]','2025-05-01',NULL,'2025-05-24 07:33:51','2025-06-14 08:00:32'),(8,1,8,1500.00,'[]','[]','2025-05-01',NULL,'2025-05-24 07:34:12','2025-05-26 09:25:18'),(9,1,9,800.00,'[]','[]','2025-05-01',NULL,'2025-05-24 07:34:29','2025-05-26 09:26:59'),(10,1,10,500.00,'[]','[]','2025-05-01',NULL,'2025-05-24 07:34:46','2025-05-26 09:25:04'),(11,1,11,800.00,'[]','[]','2025-05-01',NULL,'2025-05-24 07:35:07','2025-06-21 11:50:36'),(12,1,12,1150.00,'[]','[]','2025-05-01',NULL,'2025-05-24 07:35:42','2025-05-26 09:24:45'),(13,1,13,750.00,'[]','[]','2025-05-01',NULL,'2025-05-24 07:35:59','2025-05-26 09:24:12'),(14,1,14,500.00,'[]','[]','2025-05-01',NULL,'2025-05-24 07:36:21','2025-05-26 09:23:58'),(15,1,15,1400.00,'[]','[]','2025-05-01',NULL,'2025-05-24 07:36:58','2025-05-26 09:23:41'),(16,1,16,500.00,'[]','[]','2025-05-01',NULL,'2025-05-24 07:37:22','2025-05-26 09:23:20'),(17,1,17,700.00,'[]','[]','2025-05-01',NULL,'2025-05-24 07:38:05','2025-06-14 08:07:14'),(18,1,18,1900.00,'[]','[]','2025-05-01',NULL,'2025-05-24 07:38:31','2025-05-26 09:22:42'),(19,1,19,1400.00,'[]','[]','2025-05-01',NULL,'2025-05-24 07:38:54','2025-05-26 09:22:26'),(20,1,20,1900.00,'[]','[]','2025-05-01',NULL,'2025-05-24 07:39:16','2025-05-25 23:56:44'),(21,1,21,150000.00,'[]','[]','2025-06-01',NULL,'2025-06-04 11:55:15','2025-06-04 11:55:15'),(22,1,22,750000.00,'[]','[]','2025-06-01',NULL,'2025-06-04 11:55:44','2025-06-04 11:55:44'),(23,1,23,650000.00,'[]','[]','2025-06-01',NULL,'2025-06-04 11:58:53','2025-06-04 11:58:53');
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
  `tenant_id` bigint unsigned DEFAULT NULL,
  `month` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','approved','closed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_by` bigint unsigned DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `salary_batches_created_by_foreign` (`created_by`),
  KEY `salary_batches_approved_by_foreign` (`approved_by`),
  KEY `salary_batches_tenant_id_index` (`tenant_id`),
  CONSTRAINT `salary_batches_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `salary_batches_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salary_batches`
--

LOCK TABLES `salary_batches` WRITE;
/*!40000 ALTER TABLE `salary_batches` DISABLE KEYS */;
INSERT INTO `salary_batches` (`id`, `tenant_id`, `month`, `status`, `created_by`, `approved_by`, `approved_at`, `created_at`, `updated_at`) VALUES (1,1,'2025-05','approved',2,1,'2025-06-01 11:48:34','2025-06-01 08:02:59','2025-06-01 11:48:34'),(2,NULL,'2025-06','approved',2,2,'2025-07-01 08:28:30','2025-07-01 08:27:20','2025-07-01 08:28:30');
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
  `tenant_id` bigint unsigned DEFAULT NULL,
  `salary_batch_id` bigint unsigned DEFAULT NULL,
  `employee_id` bigint unsigned NOT NULL,
  `salary_month` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gross_salary` decimal(18,2) NOT NULL,
  `total_allowances` decimal(18,2) NOT NULL DEFAULT '0.00',
  `total_deductions` decimal(18,2) NOT NULL DEFAULT '0.00',
  `net_salary` decimal(18,2) NOT NULL,
  `payment_date` date DEFAULT NULL,
  `status` enum('pending','paid','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `journal_entry_id` bigint unsigned DEFAULT NULL,
  `voucher_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `salary_payments_salary_batch_id_foreign` (`salary_batch_id`),
  KEY `salary_payments_employee_id_foreign` (`employee_id`),
  KEY `salary_payments_journal_entry_id_foreign` (`journal_entry_id`),
  KEY `salary_payments_voucher_id_foreign` (`voucher_id`),
  KEY `salary_payments_tenant_id_index` (`tenant_id`),
  CONSTRAINT `salary_payments_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `salary_payments_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `salary_payments_salary_batch_id_foreign` FOREIGN KEY (`salary_batch_id`) REFERENCES `salary_batches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `salary_payments_voucher_id_foreign` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salary_payments`
--

LOCK TABLES `salary_payments` WRITE;
/*!40000 ALTER TABLE `salary_payments` DISABLE KEYS */;
INSERT INTO `salary_payments` (`id`, `tenant_id`, `salary_batch_id`, `employee_id`, `salary_month`, `gross_salary`, `total_allowances`, `total_deductions`, `net_salary`, `payment_date`, `status`, `journal_entry_id`, `voucher_id`, `created_at`, `updated_at`) VALUES (1,1,1,1,'2025-05',900000.00,0.00,0.00,900000.00,'2025-06-01','paid',33,30,'2025-06-01 08:02:59','2025-06-01 12:19:14'),(2,1,1,2,'2025-05',800000.00,0.00,0.00,800000.00,'2025-06-01','paid',29,26,'2025-06-01 08:02:59','2025-06-01 12:16:15'),(3,1,1,3,'2025-05',750000.00,0.00,0.00,750000.00,'2025-06-01','paid',30,27,'2025-06-01 08:02:59','2025-06-01 12:17:54'),(4,1,1,4,'2025-05',1000000.00,0.00,0.00,1000000.00,'2025-06-01','paid',31,28,'2025-06-01 08:02:59','2025-06-01 12:18:25'),(5,1,1,5,'2025-05',800000.00,0.00,0.00,800000.00,'2025-06-01','paid',32,29,'2025-06-01 08:02:59','2025-06-01 12:18:43'),(6,1,1,6,'2025-05',1000.00,0.00,0.00,1000.00,'2025-06-14','paid',102,87,'2025-06-01 08:02:59','2025-06-14 09:51:34'),(7,1,1,7,'2025-05',600.00,0.00,0.00,600.00,'2025-06-14','paid',113,114,'2025-06-01 08:02:59','2025-06-14 10:42:07'),(8,1,1,8,'2025-05',1500.00,0.00,0.00,1500.00,'2025-06-14','paid',112,113,'2025-06-01 08:02:59','2025-06-14 10:41:56'),(9,1,1,9,'2025-05',800.00,0.00,0.00,800.00,'2025-06-14','paid',111,112,'2025-06-01 08:02:59','2025-06-14 10:41:41'),(10,1,1,10,'2025-05',500.00,0.00,0.00,500.00,'2025-06-14','paid',110,111,'2025-06-01 08:02:59','2025-06-14 10:41:33'),(11,1,1,11,'2025-05',750.00,0.00,0.00,750.00,'2025-06-14','paid',103,104,'2025-06-01 08:02:59','2025-06-14 10:39:46'),(12,1,1,12,'2025-05',1150.00,0.00,0.00,1150.00,'2025-06-14','paid',109,110,'2025-06-01 08:02:59','2025-06-14 10:41:25'),(13,1,1,13,'2025-05',750.00,0.00,0.00,750.00,'2025-06-14','paid',108,109,'2025-06-01 08:02:59','2025-06-14 10:41:16'),(14,1,1,14,'2025-05',500.00,0.00,0.00,500.00,'2025-06-14','paid',107,108,'2025-06-01 08:02:59','2025-06-14 10:41:07'),(15,1,1,15,'2025-05',1400.00,0.00,0.00,1400.00,'2025-06-14','paid',106,107,'2025-06-01 08:02:59','2025-06-14 10:40:58'),(16,1,1,16,'2025-05',500.00,0.00,0.00,500.00,'2025-06-14','paid',105,106,'2025-06-01 08:02:59','2025-06-14 10:40:50'),(17,1,1,17,'2025-05',750.00,0.00,0.00,750.00,'2025-06-14','paid',104,105,'2025-06-01 08:02:59','2025-06-14 10:40:41'),(18,1,1,18,'2025-05',1900.00,0.00,0.00,1900.00,'2025-06-11','paid',90,76,'2025-06-01 08:02:59','2025-06-11 13:53:15'),(19,1,1,19,'2025-05',1400.00,0.00,0.00,1400.00,'2025-06-11','paid',83,62,'2025-06-01 08:02:59','2025-06-11 09:49:23'),(20,1,1,20,'2025-05',1900.00,0.00,0.00,1900.00,'2025-06-11','paid',80,57,'2025-06-01 08:02:59','2025-06-11 09:30:13'),(21,NULL,2,1,'2025-06',900000.00,0.00,0.00,900000.00,'2025-07-01','paid',172,167,'2025-07-01 08:27:20','2025-07-01 09:33:58'),(22,NULL,2,2,'2025-06',800000.00,0.00,0.00,800000.00,'2025-07-01','paid',175,170,'2025-07-01 08:27:20','2025-07-01 09:55:42'),(23,NULL,2,3,'2025-06',850000.00,0.00,0.00,850000.00,'2025-07-01','paid',171,166,'2025-07-01 08:27:20','2025-07-01 09:32:01'),(24,NULL,2,4,'2025-06',1100000.00,0.00,0.00,1100000.00,'2025-07-01','paid',170,165,'2025-07-01 08:27:20','2025-07-01 09:28:21'),(25,NULL,2,5,'2025-06',800000.00,0.00,0.00,800000.00,'2025-07-01','paid',174,169,'2025-07-01 08:27:20','2025-07-01 09:38:54'),(26,NULL,2,6,'2025-06',1000.00,0.00,0.00,1000.00,'2025-06-30','pending',NULL,NULL,'2025-07-01 08:27:20','2025-07-01 08:27:20'),(27,NULL,2,7,'2025-06',500.00,0.00,0.00,500.00,'2025-06-30','pending',NULL,NULL,'2025-07-01 08:27:20','2025-07-01 08:27:20'),(28,NULL,2,8,'2025-06',1500.00,0.00,0.00,1500.00,'2025-06-30','pending',NULL,NULL,'2025-07-01 08:27:20','2025-07-01 08:27:20'),(29,NULL,2,9,'2025-06',800.00,0.00,0.00,800.00,'2025-06-30','pending',NULL,NULL,'2025-07-01 08:27:20','2025-07-01 08:27:20'),(30,NULL,2,10,'2025-06',500.00,0.00,0.00,500.00,'2025-06-30','pending',NULL,NULL,'2025-07-01 08:27:20','2025-07-01 08:27:20'),(31,NULL,2,11,'2025-06',800.00,0.00,0.00,800.00,'2025-06-30','pending',NULL,NULL,'2025-07-01 08:27:20','2025-07-01 08:27:20'),(32,NULL,2,12,'2025-06',1150.00,0.00,0.00,1150.00,'2025-06-30','pending',NULL,NULL,'2025-07-01 08:27:20','2025-07-01 08:27:20'),(33,NULL,2,13,'2025-06',750.00,0.00,0.00,750.00,'2025-06-30','pending',NULL,NULL,'2025-07-01 08:27:20','2025-07-01 08:27:20'),(34,NULL,2,14,'2025-06',500.00,0.00,0.00,500.00,'2025-06-30','pending',NULL,NULL,'2025-07-01 08:27:20','2025-07-01 08:27:20'),(35,NULL,2,15,'2025-06',1400.00,0.00,0.00,1400.00,'2025-06-30','pending',NULL,NULL,'2025-07-01 08:27:20','2025-07-01 08:27:20'),(36,NULL,2,16,'2025-06',500.00,0.00,0.00,500.00,'2025-06-30','pending',NULL,NULL,'2025-07-01 08:27:20','2025-07-01 08:27:20'),(37,NULL,2,17,'2025-06',700.00,0.00,0.00,700.00,'2025-06-30','pending',NULL,NULL,'2025-07-01 08:27:20','2025-07-01 08:27:20'),(38,NULL,2,18,'2025-06',1900.00,0.00,0.00,1900.00,'2025-06-30','pending',NULL,NULL,'2025-07-01 08:27:20','2025-07-01 08:27:20'),(39,NULL,2,19,'2025-06',1400.00,0.00,0.00,1400.00,'2025-06-30','pending',NULL,NULL,'2025-07-01 08:27:20','2025-07-01 08:27:20'),(40,NULL,2,20,'2025-06',1900.00,0.00,0.00,1900.00,'2025-06-30','pending',NULL,NULL,'2025-07-01 08:27:20','2025-07-01 08:27:20'),(41,NULL,2,21,'2025-06',150000.00,0.00,0.00,150000.00,'2025-07-01','paid',168,163,'2025-07-01 08:27:20','2025-07-01 09:25:47'),(42,NULL,2,22,'2025-06',750000.00,0.00,0.00,750000.00,'2025-07-01','paid',169,164,'2025-07-01 08:27:20','2025-07-01 09:26:53'),(43,NULL,2,23,'2025-06',650000.00,0.00,0.00,650000.00,'2025-07-01','paid',173,168,'2025-07-01 08:27:20','2025-07-01 09:36:14');
/*!40000 ALTER TABLE `salary_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `tenant_id` bigint unsigned DEFAULT NULL,
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`),
  KEY `settings_tenant_id_index` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` (`id`, `tenant_id`, `key`, `value`, `created_at`, `updated_at`) VALUES (1,1,'system_name','شركة التطوير','2025-05-23 22:50:00','2025-05-23 22:50:00'),(2,1,'company_name','Altatweer','2025-05-23 22:50:00','2025-05-23 22:50:00'),(3,1,'default_language','ar','2025-05-23 22:50:00','2025-05-23 22:50:00');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscription_plans`
--

DROP TABLE IF EXISTS `subscription_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscription_plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `billing_cycle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'monthly',
  `trial_days` int NOT NULL DEFAULT '0',
  `features` json DEFAULT NULL,
  `limits` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscription_plans_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscription_plans`
--

LOCK TABLES `subscription_plans` WRITE;
/*!40000 ALTER TABLE `subscription_plans` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscription_plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tenant_features`
--

DROP TABLE IF EXISTS `tenant_features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenant_features` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `feature_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `settings` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenant_features_tenant_id_feature_code_unique` (`tenant_id`,`feature_code`),
  CONSTRAINT `tenant_features_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tenant_features`
--

LOCK TABLES `tenant_features` WRITE;
/*!40000 ALTER TABLE `tenant_features` DISABLE KEYS */;
/*!40000 ALTER TABLE `tenant_features` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tenant_usage_stats`
--

DROP TABLE IF EXISTS `tenant_usage_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenant_usage_stats` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `resource_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `count` int NOT NULL DEFAULT '0',
  `monthly_count` int NOT NULL DEFAULT '0',
  `limit` int NOT NULL DEFAULT '0',
  `last_updated_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenant_usage_stats_tenant_id_resource_type_unique` (`tenant_id`,`resource_type`),
  CONSTRAINT `tenant_usage_stats_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tenant_usage_stats`
--

LOCK TABLES `tenant_usage_stats` WRITE;
/*!40000 ALTER TABLE `tenant_usage_stats` DISABLE KEYS */;
/*!40000 ALTER TABLE `tenant_usage_stats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tenants`
--

DROP TABLE IF EXISTS `tenants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subdomain` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `domain` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `database` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `subscription_plan_id` bigint unsigned DEFAULT NULL,
  `subscription_starts_at` timestamp NULL DEFAULT NULL,
  `subscription_ends_at` timestamp NULL DEFAULT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `settings` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenants_subdomain_unique` (`subdomain`),
  UNIQUE KEY `tenants_domain_unique` (`domain`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tenants`
--

LOCK TABLES `tenants` WRITE;
/*!40000 ALTER TABLE `tenants` DISABLE KEYS */;
INSERT INTO `tenants` (`id`, `name`, `subdomain`, `domain`, `database`, `logo`, `contact_email`, `contact_phone`, `address`, `is_active`, `subscription_plan_id`, `subscription_starts_at`, `subscription_ends_at`, `trial_ends_at`, `settings`, `created_at`, `updated_at`) VALUES (1,'Default Tenant','default','default',NULL,NULL,'admin@aursuite.com',NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,'2025-05-23 22:46:53','2025-05-23 22:46:53');
/*!40000 ALTER TABLE `tenants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned DEFAULT NULL,
  `voucher_id` bigint unsigned DEFAULT NULL,
  `invoice_id` bigint unsigned DEFAULT NULL,
  `date` date NOT NULL,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IQD',
  `exchange_rate` decimal(15,6) NOT NULL DEFAULT '1.000000',
  `account_id` bigint unsigned NOT NULL,
  `target_account_id` bigint unsigned DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
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
  KEY `transactions_tenant_id_index` (`tenant_id`),
  CONSTRAINT `transactions_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transactions_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transactions_target_account_id_foreign` FOREIGN KEY (`target_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transactions_voucher_id_foreign` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=145 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
INSERT INTO `transactions` (`id`, `tenant_id`, `voucher_id`, `invoice_id`, `date`, `type`, `amount`, `currency`, `exchange_rate`, `account_id`, `target_account_id`, `description`, `user_id`, `branch_id`, `created_at`, `updated_at`) VALUES (1,1,1,NULL,'2025-05-24','receipt',300000.00,'IQD',1.000000,51,97,NULL,2,NULL,'2025-05-24 12:01:33','2025-05-24 12:01:33'),(2,1,2,NULL,'2025-05-25','payment',-150000.00,'IQD',1.000000,51,94,NULL,2,NULL,'2025-05-25 06:53:14','2025-05-25 06:53:14'),(3,1,3,NULL,'2025-05-25','payment',-50000.00,'IQD',1.000000,51,94,NULL,2,NULL,'2025-05-25 07:49:22','2025-05-25 07:49:22'),(4,1,4,NULL,'2025-05-25','payment',-10000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-05-25 09:38:00','2025-05-25 09:38:00'),(5,1,5,NULL,'2025-05-26','payment',-5000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-05-26 09:55:30','2025-05-26 09:55:30'),(6,1,6,NULL,'2025-05-28','payment',-1500.00,'IQD',1.000000,51,94,NULL,2,NULL,'2025-05-28 08:55:47','2025-05-28 08:55:47'),(7,1,7,NULL,'2025-05-28','payment',-10000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-05-28 10:22:29','2025-05-28 10:22:29'),(8,1,8,NULL,'2025-05-29','payment',-10000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-05-29 07:47:31','2025-05-29 07:47:31'),(9,1,9,NULL,'2025-05-29','payment',-5000.00,'IQD',1.000000,51,92,NULL,2,NULL,'2025-05-29 08:33:46','2025-05-29 08:33:46'),(10,1,10,NULL,'2025-05-29','payment',-5000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-05-29 09:22:39','2025-05-29 09:22:39'),(11,1,11,NULL,'2025-05-29','payment',-15000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-05-29 09:58:44','2025-05-29 09:58:44'),(12,1,12,1,'2025-05-29','receipt',10361.00,'USD',1412.500000,3,NULL,'سداد فاتورة INV-00001',2,NULL,'2025-05-29 12:37:24','2025-05-29 12:37:24'),(13,1,13,NULL,'2025-05-31','payment',-15000.00,'IQD',1.000000,51,88,NULL,2,NULL,'2025-05-31 06:34:23','2025-05-31 06:34:23'),(16,1,15,NULL,'2025-05-31','transfer',-10361.00,'USD',1.000000,3,NULL,'تحويل من الصندوق (سند تحويل #VCH-00015)',2,NULL,'2025-05-31 10:43:53','2025-05-31 10:43:53'),(17,1,15,NULL,'2025-05-31','transfer',14634912.50,'IQD',1.000000,51,NULL,'تحويل إلى الصندوق (سند تحويل #VCH-00015)',2,NULL,'2025-05-31 10:43:53','2025-05-31 10:43:53'),(18,1,16,NULL,'2025-05-31','payment',-150000.00,'IQD',1.000000,51,88,NULL,2,NULL,'2025-05-31 10:46:32','2025-05-31 10:46:32'),(19,1,17,NULL,'2025-05-31','payment',-115000.00,'IQD',1.000000,51,90,NULL,2,NULL,'2025-05-31 10:48:53','2025-05-31 10:48:53'),(20,1,18,NULL,'2025-05-31','payment',-150000.00,'IQD',1.000000,51,85,NULL,2,NULL,'2025-05-31 10:58:41','2025-05-31 10:58:41'),(21,1,19,NULL,'2025-05-31','transfer',-4000000.00,'IQD',1.000000,51,NULL,'تحويل من الصندوق (سند تحويل #VCH-00019)',2,NULL,'2025-05-31 11:54:11','2025-05-31 11:54:11'),(22,1,19,NULL,'2025-05-31','transfer',4000000.00,'IQD',1.000000,50,NULL,'تحويل إلى الصندوق (سند تحويل #VCH-00019)',2,NULL,'2025-05-31 11:54:11','2025-05-31 11:54:11'),(23,1,20,NULL,'2025-06-01','payment',-100000.00,'IQD',1.000000,51,87,NULL,2,NULL,'2025-06-01 07:18:30','2025-06-01 07:18:30'),(24,1,21,NULL,'2025-06-01','payment',-40000.00,'IQD',1.000000,51,93,NULL,2,NULL,'2025-06-01 07:35:40','2025-06-01 07:35:40'),(25,1,22,NULL,'2025-06-01','payment',-10000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-06-01 07:36:49','2025-06-01 07:36:49'),(26,1,23,NULL,'2025-06-01','payment',-5000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-06-01 09:30:03','2025-06-01 09:30:03'),(27,1,24,NULL,'2025-06-01','payment',-200000.00,'IQD',1.000000,51,97,NULL,2,NULL,'2025-06-01 10:40:06','2025-06-01 10:40:06'),(28,1,25,NULL,'2025-06-01','payment',-250000.00,'IQD',1.000000,51,88,NULL,2,NULL,'2025-06-01 11:33:18','2025-06-01 11:33:18'),(29,1,31,NULL,'2025-06-01','payment',-750000.00,'IQD',1.000000,51,85,NULL,2,NULL,'2025-06-01 12:20:32','2025-06-01 12:20:32'),(30,1,32,NULL,'2025-06-01','payment',-25000.00,'IQD',1.000000,51,88,NULL,2,NULL,'2025-06-01 12:37:08','2025-06-01 12:37:08'),(31,1,33,NULL,'2025-06-02','payment',-5000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-06-02 09:53:32','2025-06-02 09:53:32'),(32,1,34,NULL,'2025-06-03','payment',-5000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-06-03 09:36:33','2025-06-03 09:36:33'),(33,1,35,NULL,'2025-06-03','payment',-10000.00,'IQD',1.000000,51,88,NULL,2,NULL,'2025-06-03 12:43:14','2025-06-03 12:43:14'),(34,1,36,NULL,'2025-06-03','payment',-40000.00,'IQD',1.000000,51,97,NULL,2,NULL,'2025-06-03 12:56:08','2025-06-03 12:56:08'),(35,1,37,NULL,'2025-06-04','payment',-300000.00,'IQD',1.000000,51,97,NULL,2,NULL,'2025-06-04 08:37:47','2025-06-04 08:37:47'),(36,1,38,NULL,'2025-06-04','payment',-75000.00,'IQD',1.000000,51,97,NULL,2,NULL,'2025-06-04 08:40:15','2025-06-04 08:40:15'),(39,1,41,NULL,'2025-06-04','payment',-2175000.00,'IQD',1.000000,51,88,NULL,2,NULL,'2025-06-04 09:36:07','2025-06-04 09:36:07'),(40,1,42,NULL,'2025-06-04','payment',-5000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-06-04 09:48:54','2025-06-04 09:48:54'),(41,1,43,NULL,'2025-06-04','payment',-10000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-06-04 09:50:07','2025-06-04 09:50:07'),(42,1,44,NULL,'2025-06-04','payment',-5000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-06-04 10:00:36','2025-06-04 10:00:36'),(43,1,45,NULL,'2025-06-04','payment',-10000.00,'IQD',1.000000,51,88,NULL,2,NULL,'2025-06-04 10:06:52','2025-06-04 10:06:52'),(44,1,46,2,'2025-06-07','receipt',8500.00,'USD',1412.500000,2,NULL,'سداد فاتورة INV-00002',1,NULL,'2025-06-07 20:47:10','2025-06-07 20:47:10'),(45,NULL,47,NULL,'2025-05-31','payment',-4000000.00,'IQD',1.000000,50,92,NULL,1,NULL,'2025-06-08 00:24:01','2025-06-08 00:24:01'),(46,NULL,48,NULL,'2025-05-31','payment',-8500.00,'USD',1412.500000,2,161,NULL,1,NULL,'2025-06-08 09:28:38','2025-06-08 09:28:38'),(47,NULL,49,NULL,'2025-06-10','payment',-15000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-06-10 09:36:57','2025-06-10 09:36:57'),(48,NULL,50,3,'2025-06-10','receipt',16500.00,'USD',1412.500000,3,NULL,'سداد فاتورة INV-00003',2,NULL,'2025-06-10 10:20:10','2025-06-10 10:20:10'),(49,NULL,51,NULL,'2025-06-10','payment',-10000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-06-10 12:38:16','2025-06-10 12:38:16'),(50,NULL,52,NULL,'2025-06-10','payment',-3000.00,'IQD',1.000000,51,93,NULL,2,NULL,'2025-06-10 12:43:23','2025-06-10 12:43:23'),(53,NULL,55,NULL,'2025-06-11','payment',-20000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-06-11 07:25:25','2025-06-11 07:25:25'),(54,NULL,56,NULL,'2025-06-11','payment',-150000.00,'IQD',1.000000,51,88,NULL,2,NULL,'2025-06-11 07:43:03','2025-06-11 07:43:03'),(55,NULL,60,NULL,'2025-06-11','payment',-5000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-06-11 09:42:45','2025-06-11 09:42:45'),(56,NULL,61,NULL,'2025-06-11','payment',-700000.00,'IQD',1.000000,51,97,NULL,2,NULL,'2025-06-11 09:48:01','2025-06-11 09:48:01'),(57,NULL,71,4,'2025-06-11','receipt',3000.00,'USD',1420.000000,3,NULL,'سداد فاتورة INV-00004',2,NULL,'2025-06-11 11:51:50','2025-06-11 11:51:50'),(58,NULL,72,NULL,'2025-06-11','payment',-31000.00,'IQD',1.000000,51,88,NULL,2,NULL,'2025-06-11 12:02:54','2025-06-11 12:02:54'),(59,NULL,73,NULL,'2025-06-11','payment',-25000.00,'IQD',1.000000,51,90,NULL,2,NULL,'2025-06-11 12:10:54','2025-06-11 12:10:54'),(60,NULL,74,NULL,'2025-06-11','payment',-5000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-06-11 12:12:02','2025-06-11 12:12:02'),(61,NULL,75,NULL,'2025-06-11','payment',-2454.00,'USD',1420.000000,3,161,NULL,2,NULL,'2025-06-11 12:38:03','2025-06-11 12:38:03'),(62,NULL,77,NULL,'2025-06-12','payment',-300000.00,'IQD',1.000000,51,97,NULL,2,NULL,'2025-06-12 10:27:59','2025-06-12 10:27:59'),(63,NULL,78,NULL,'2025-06-12','payment',-30000.00,'IQD',1.000000,51,97,NULL,2,NULL,'2025-06-12 10:28:56','2025-06-12 10:28:56'),(64,NULL,79,NULL,'2025-06-12','payment',-18000.00,'IQD',1.000000,51,88,NULL,2,NULL,'2025-06-12 10:29:46','2025-06-12 10:29:46'),(65,NULL,80,NULL,'2025-06-12','payment',-5000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-06-12 10:30:17','2025-06-12 10:30:17'),(66,NULL,81,NULL,'2025-06-12','payment',-10000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-06-12 10:31:31','2025-06-12 10:31:31'),(67,NULL,82,NULL,'2025-06-12','payment',-3000.00,'IQD',1.000000,51,93,NULL,2,NULL,'2025-06-12 10:33:49','2025-06-12 10:33:49'),(68,NULL,83,NULL,'2025-06-12','payment',-10000.00,'IQD',1.000000,51,90,NULL,2,NULL,'2025-06-12 10:36:46','2025-06-12 10:36:46'),(69,NULL,84,5,'2025-06-12','receipt',500.00,'USD',1420.000000,3,NULL,'سداد فاتورة INV-00005',2,NULL,'2025-06-12 11:45:41','2025-06-12 11:45:41'),(70,NULL,85,NULL,'2025-06-12','transfer',-500.00,'USD',1.000000,3,NULL,'تحويل من الصندوق (سند تحويل #VCH-00085)',2,NULL,'2025-06-12 11:51:28','2025-06-12 11:51:28'),(71,NULL,85,NULL,'2025-06-12','transfer',710000.00,'IQD',1.000000,51,NULL,'تحويل إلى الصندوق (سند تحويل #VCH-00085)',2,NULL,'2025-06-12 11:51:28','2025-06-12 11:51:28'),(72,NULL,86,NULL,'2025-06-12','payment',-710000.00,'IQD',1.000000,51,97,NULL,2,NULL,'2025-06-12 11:53:38','2025-06-12 11:53:38'),(73,NULL,115,NULL,'2025-06-14','payment',-700.00,'USD',1420.000000,3,37,NULL,2,NULL,'2025-06-14 10:44:59','2025-06-14 10:44:59'),(74,NULL,116,NULL,'2025-06-14','transfer',-546.00,'USD',1.000000,3,NULL,'تحويل من الصندوق (سند تحويل #VCH-00116)',2,NULL,'2025-06-14 11:37:21','2025-06-14 11:37:21'),(75,NULL,116,NULL,'2025-06-14','transfer',775320.00,'IQD',1.000000,51,NULL,'تحويل إلى الصندوق (سند تحويل #VCH-00116)',2,NULL,'2025-06-14 11:37:21','2025-06-14 11:37:21'),(76,NULL,117,NULL,'2025-06-15','payment',-400.00,'USD',1420.000000,3,161,NULL,2,NULL,'2025-06-15 06:10:49','2025-06-15 06:10:49'),(77,NULL,118,NULL,'2025-06-15','payment',-750000.00,'IQD',1.000000,51,85,NULL,2,NULL,'2025-06-15 06:22:02','2025-06-15 06:22:02'),(78,NULL,119,NULL,'2025-06-15','receipt',375000.00,'IQD',1.000000,51,79,NULL,2,NULL,'2025-06-15 11:59:25','2025-06-15 11:59:25'),(79,NULL,120,NULL,'2025-06-15','payment',-700000.00,'IQD',1.000000,51,87,NULL,2,NULL,'2025-06-15 12:38:19','2025-06-15 12:38:19'),(80,NULL,121,NULL,'2025-06-16','payment',-10000.00,'IQD',1.000000,51,88,NULL,2,NULL,'2025-06-16 05:24:05','2025-06-16 05:24:05'),(81,NULL,122,NULL,'2025-06-16','payment',-5000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-06-16 09:26:42','2025-06-16 09:26:42'),(82,NULL,123,NULL,'2025-06-16','payment',-30000.00,'IQD',1.000000,51,90,NULL,2,NULL,'2025-06-16 13:00:57','2025-06-16 13:00:57'),(83,NULL,124,NULL,'2025-06-17','payment',-5000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-06-17 12:30:25','2025-06-17 12:30:25'),(84,NULL,125,NULL,'2025-06-18','payment',-5000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-06-18 11:08:36','2025-06-18 11:08:36'),(85,NULL,126,NULL,'2025-06-18','receipt',500000.00,'IQD',1.000000,51,97,NULL,2,NULL,'2025-06-18 11:18:06','2025-06-18 11:18:06'),(86,NULL,127,NULL,'2025-06-18','payment',-180000.00,'IQD',1.000000,51,93,NULL,2,NULL,'2025-06-18 11:27:35','2025-06-18 11:27:35'),(87,NULL,128,NULL,'2025-06-18','payment',-320000.00,'IQD',1.000000,51,160,NULL,2,NULL,'2025-06-18 11:31:16','2025-06-18 11:31:16'),(88,NULL,129,NULL,'2025-06-19','payment',-36000.00,'IQD',1.000000,51,88,NULL,2,NULL,'2025-06-19 07:05:08','2025-06-19 07:05:08'),(89,NULL,130,NULL,'2025-06-19','payment',-190000.00,'IQD',1.000000,51,58,NULL,2,NULL,'2025-06-19 09:42:22','2025-06-19 09:42:22'),(90,NULL,131,NULL,'2025-06-19','payment',-5000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-06-19 09:48:16','2025-06-19 09:48:16'),(91,NULL,132,NULL,'2025-06-22','payment',-5000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-06-22 11:43:28','2025-06-22 11:43:28'),(92,NULL,133,NULL,'2025-06-23','payment',-14000.00,'IQD',1.000000,51,88,NULL,2,NULL,'2025-06-23 07:21:39','2025-06-23 07:21:39'),(93,NULL,134,NULL,'2025-06-23','payment',-5000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-06-23 09:43:36','2025-06-23 09:43:36'),(94,NULL,135,NULL,'2025-06-24','payment',-5000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-06-24 10:53:02','2025-06-24 10:53:02'),(95,NULL,136,NULL,'2025-06-24','payment',-10000.00,'IQD',1.000000,51,88,NULL,2,NULL,'2025-06-24 11:30:39','2025-06-24 11:30:39'),(96,NULL,137,NULL,'2025-06-24','receipt',305000.00,'IQD',1.000000,51,97,NULL,2,NULL,'2025-06-24 11:53:16','2025-06-24 11:53:16'),(97,NULL,138,NULL,'2025-06-24','payment',-305000.00,'IQD',1.000000,51,160,NULL,2,NULL,'2025-06-24 11:56:21','2025-06-24 11:56:21'),(98,NULL,139,NULL,'2025-06-24','receipt',50000.00,'IQD',1.000000,51,97,NULL,2,NULL,'2025-06-24 11:58:20','2025-06-24 11:58:20'),(99,NULL,140,NULL,'2025-06-25','payment',-5000.00,'IQD',1.000000,51,88,NULL,2,NULL,'2025-06-25 06:45:46','2025-06-25 06:45:46'),(100,NULL,141,NULL,'2025-06-25','payment',-25000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-06-25 12:17:56','2025-06-25 12:17:56'),(101,NULL,142,6,'2025-06-25','receipt',1587.00,'USD',1420.000000,3,NULL,'سداد فاتورة INV-00006',2,NULL,'2025-06-25 12:52:02','2025-06-25 12:52:02'),(106,NULL,145,NULL,'2025-06-26','transfer',-1587.00,'USD',1.000000,3,NULL,'تحويل من الصندوق (سند تحويل #VCH-00145)',2,NULL,'2025-06-26 10:20:00','2025-06-26 10:20:00'),(107,NULL,145,NULL,'2025-06-26','transfer',2253540.00,'IQD',1.000000,51,NULL,'تحويل إلى الصندوق (سند تحويل #VCH-00145)',2,NULL,'2025-06-26 10:20:00','2025-06-26 10:20:00'),(108,NULL,146,NULL,'2025-06-26','transfer',-2000000.00,'IQD',1.000000,51,NULL,'تحويل من الصندوق (سند تحويل #VCH-00146)',2,NULL,'2025-06-26 10:21:13','2025-06-26 10:21:13'),(109,NULL,146,NULL,'2025-06-26','transfer',2000000.00,'IQD',1.000000,50,NULL,'تحويل إلى الصندوق (سند تحويل #VCH-00146)',2,NULL,'2025-06-26 10:21:13','2025-06-26 10:21:13'),(110,NULL,147,NULL,'2025-06-28','payment',-150000.00,'IQD',1.000000,51,88,NULL,2,NULL,'2025-06-28 07:18:12','2025-06-28 07:18:12'),(111,NULL,148,NULL,'2025-06-28','payment',-5000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-06-28 07:20:13','2025-06-28 07:20:13'),(112,NULL,149,NULL,'2025-06-29','payment',-10000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-06-29 08:55:09','2025-06-29 08:55:09'),(113,NULL,150,NULL,'2025-06-29','payment',-15000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-06-29 11:03:23','2025-06-29 11:03:23'),(114,NULL,151,NULL,'2025-06-30','payment',-50000.00,'IQD',1.000000,51,88,NULL,2,NULL,'2025-06-30 08:33:19','2025-06-30 08:33:19'),(115,NULL,152,NULL,'2025-06-30','payment',-15000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-06-30 09:05:00','2025-06-30 09:05:00'),(116,NULL,153,NULL,'2025-06-30','payment',-10000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-06-30 09:17:28','2025-06-30 09:17:28'),(117,NULL,154,NULL,'2025-06-30','payment',-2000000.00,'IQD',1.000000,50,160,NULL,1,NULL,'2025-06-30 10:47:50','2025-06-30 10:47:50'),(118,NULL,155,7,'2025-06-30','receipt',10361.00,'USD',1415.000000,3,NULL,'سداد فاتورة INV-00007',2,NULL,'2025-06-30 11:48:14','2025-06-30 11:48:14'),(119,NULL,156,8,'2025-06-30','receipt',13500.00,'USD',1415.000000,3,NULL,'سداد فاتورة INV-00008',2,NULL,'2025-06-30 11:49:43','2025-06-30 11:49:43'),(120,NULL,157,NULL,'2025-06-30','transfer',-23861.00,'USD',1.000000,3,NULL,'تحويل من الصندوق (سند تحويل #VCH-00157)',2,NULL,'2025-06-30 12:01:30','2025-06-30 12:01:30'),(121,NULL,157,NULL,'2025-06-30','transfer',33763315.00,'IQD',1.000000,51,NULL,'تحويل إلى الصندوق (سند تحويل #VCH-00157)',2,NULL,'2025-06-30 12:01:30','2025-06-30 12:01:30'),(122,NULL,158,NULL,'2025-06-30','payment',-5015000.00,'IQD',1.000000,51,160,NULL,2,NULL,'2025-06-30 12:32:01','2025-06-30 12:32:01'),(123,NULL,159,NULL,'2025-06-30','payment',-5015000.00,'IQD',1.000000,51,160,NULL,2,NULL,'2025-06-30 12:34:03','2025-06-30 12:34:03'),(124,NULL,160,NULL,'2025-06-30','payment',-10000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-06-30 12:34:50','2025-06-30 12:34:50'),(125,NULL,161,NULL,'2025-06-30','payment',-235000.00,'IQD',1.000000,51,97,NULL,2,NULL,'2025-06-30 12:53:38','2025-06-30 12:53:38'),(126,NULL,162,NULL,'2025-07-01','payment',-8000.00,'IQD',1.000000,51,88,NULL,2,NULL,'2025-07-01 08:14:28','2025-07-01 08:14:28'),(127,NULL,171,NULL,'2025-07-01','payment',-70000.00,'IQD',1.000000,51,97,NULL,2,NULL,'2025-07-01 10:04:36','2025-07-01 10:04:36'),(128,NULL,172,NULL,'2025-07-01','payment',-814000.00,'IQD',1.000000,51,97,NULL,2,NULL,'2025-07-01 11:38:29','2025-07-01 11:38:29'),(129,NULL,173,NULL,'2025-07-02','payment',-1610000.00,'IQD',1.000000,51,160,NULL,2,NULL,'2025-07-02 07:59:08','2025-07-02 07:59:08'),(130,NULL,174,NULL,'2025-07-02','payment',-728000.00,'IQD',1.000000,51,97,NULL,2,NULL,'2025-07-02 08:23:20','2025-07-02 08:23:20'),(131,NULL,175,NULL,'2025-07-02','payment',-15000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-07-02 08:25:41','2025-07-02 08:25:41'),(132,NULL,176,NULL,'2025-07-02','payment',-5000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-07-02 08:26:37','2025-07-02 08:26:37'),(133,NULL,177,NULL,'2025-07-02','payment',-1000.00,'IQD',1.000000,51,88,NULL,2,NULL,'2025-07-02 08:34:04','2025-07-02 08:34:04'),(134,NULL,178,NULL,'2025-07-02','payment',-5000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-07-02 08:59:27','2025-07-02 08:59:27'),(135,NULL,179,NULL,'2025-07-03','payment',-150000.00,'IQD',1.000000,51,88,NULL,2,NULL,'2025-07-03 08:42:59','2025-07-03 08:42:59'),(136,NULL,180,NULL,'2025-07-03','payment',-5000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-07-03 08:46:06','2025-07-03 08:46:06'),(137,NULL,181,NULL,'2025-07-07','payment',-9000.00,'IQD',1.000000,51,88,NULL,2,NULL,'2025-07-07 07:08:47','2025-07-07 07:08:47'),(138,NULL,182,NULL,'2025-07-07','payment',-500000.00,'IQD',1.000000,51,160,NULL,2,NULL,'2025-07-07 08:00:19','2025-07-07 08:00:19'),(139,NULL,183,NULL,'2025-07-07','payment',-5000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-07-07 09:14:00','2025-07-07 09:14:00'),(140,NULL,184,NULL,'2025-07-09','transfer',-7384000.00,'IQD',1.000000,51,NULL,'تحويل من الصندوق (سند تحويل #VCH-00184)',2,NULL,'2025-07-09 12:28:21','2025-07-09 12:28:21'),(141,NULL,184,NULL,'2025-07-09','transfer',5168.80,'USD',1.000000,3,NULL,'تحويل إلى الصندوق (سند تحويل #VCH-00184)',2,NULL,'2025-07-09 12:28:21','2025-07-09 12:28:21'),(142,NULL,185,NULL,'2025-07-09','payment',-61000.00,'IQD',1.000000,51,88,NULL,2,NULL,'2025-07-09 12:55:05','2025-07-09 12:55:05'),(143,NULL,186,NULL,'2025-07-10','payment',-369000.00,'IQD',1.000000,51,87,NULL,2,NULL,'2025-07-10 07:31:13','2025-07-10 07:31:13'),(144,NULL,187,NULL,'2025-07-10','payment',-10000.00,'IQD',1.000000,51,91,NULL,2,NULL,'2025-07-10 09:18:38','2025-07-10 09:18:38');
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
  `tenant_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_super_admin` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_tenant_id_index` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `tenant_id`, `name`, `email`, `email_verified_at`, `password`, `is_super_admin`, `remember_token`, `created_at`, `updated_at`) VALUES (1,1,'Mohammed Almliki','mohammed@altatweertech.com',NULL,'$2y$12$Z/m3dbcA5r5FrnfKUk2EXOM9eGIOVriqjVAXhBNUK1XqV4ZYPLy2i',0,NULL,'2025-05-23 22:47:14','2025-05-23 22:47:14'),(2,1,'Fatema saleh','fatimasaleh@altatweertech.com',NULL,'$2y$12$ww7mFzzJFrGpaMF8AN6NEO/VtUsEafETCvwx/iUgSkznXPqA.2hca',0,NULL,'2025-05-23 22:49:13','2025-05-23 22:49:13');
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
  `tenant_id` bigint unsigned DEFAULT NULL,
  `voucher_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('receipt','payment','transfer') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IQD',
  `exchange_rate` decimal(15,6) NOT NULL DEFAULT '1.000000',
  `date` datetime NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `account_id` bigint unsigned DEFAULT NULL,
  `target_account_id` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `recipient_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_id` bigint unsigned DEFAULT NULL,
  `journal_entry_id` bigint unsigned DEFAULT NULL,
  `status` enum('active','canceled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vouchers_voucher_number_unique` (`voucher_number`),
  KEY `vouchers_created_by_foreign` (`created_by`),
  KEY `vouchers_invoice_id_foreign` (`invoice_id`),
  KEY `vouchers_tenant_id_index` (`tenant_id`),
  CONSTRAINT `vouchers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `vouchers_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=188 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vouchers`
--

LOCK TABLES `vouchers` WRITE;
/*!40000 ALTER TABLE `vouchers` DISABLE KEYS */;
INSERT INTO `vouchers` (`id`, `tenant_id`, `voucher_number`, `type`, `currency`, `exchange_rate`, `date`, `description`, `account_id`, `target_account_id`, `created_by`, `recipient_name`, `invoice_id`, `journal_entry_id`, `status`, `created_at`, `updated_at`) VALUES (1,1,'VCH-00001','receipt','IQD',1.000000,'2025-05-24 15:00:00','مصروف لشركه',NULL,NULL,2,'محمد حمدان',NULL,NULL,'active','2025-05-24 12:01:33','2025-05-24 12:01:33'),(2,1,'VCH-00002','payment','IQD',1.000000,'2025-05-25 09:50:00','وجبه غداء',NULL,NULL,2,'وجبه غداء',NULL,NULL,'active','2025-05-25 06:53:14','2025-05-25 06:53:14'),(3,1,'VCH-00003','payment','IQD',1.000000,'2025-05-25 10:47:00','32 الف ماء\r\nكهوه 6 الاف\r\nكوب سفري 2000\r\n10 الاف كروه الغداء',NULL,NULL,2,'مواد استهلاكيه',NULL,NULL,'active','2025-05-25 07:49:22','2025-05-25 07:49:22'),(4,1,'VCH-00004','payment','IQD',1.000000,'2025-05-25 12:36:00','5الاف طلب مسبق',NULL,NULL,2,'اجور نقل الغداء',NULL,NULL,'active','2025-05-25 09:38:00','2025-05-25 09:38:00'),(5,1,'VCH-00005','payment','IQD',1.000000,'2025-05-26 12:54:00','اجور غداء',NULL,NULL,2,'اجور نقل الغداء',NULL,NULL,'active','2025-05-26 09:55:30','2025-05-26 09:55:30'),(6,1,'VCH-00006','payment','IQD',1.000000,'2025-05-28 11:54:00','شراء سكر',NULL,NULL,2,'مواد استهلاكيه',NULL,NULL,'active','2025-05-28 08:55:47','2025-05-28 08:55:47'),(7,1,'VCH-00007','payment','IQD',1.000000,'2025-05-28 13:21:00','اجور نقل يوم الثلاثاء +الاربعاء',NULL,NULL,2,'اجور نقل الغداء',NULL,NULL,'active','2025-05-28 10:22:29','2025-05-28 10:22:29'),(8,1,'VCH-00008','payment','IQD',1.000000,'2025-05-29 10:46:00','الذهاب الى عافيات تصليح جهاز الطابعه',NULL,NULL,2,'اجور نقل ابو حسين',NULL,NULL,'active','2025-05-29 07:47:31','2025-05-29 07:47:31'),(9,1,'VCH-00009','payment','IQD',1.000000,'2025-05-29 11:32:00','شراء اثير ابو 5',NULL,NULL,2,'شراء رصيد اثير لشركه',NULL,NULL,'active','2025-05-29 08:33:46','2025-05-29 08:33:46'),(10,1,'VCH-00010','payment','IQD',1.000000,'2025-05-29 12:22:00','اجور نقل',NULL,NULL,2,'اجور نقل الغداء',NULL,NULL,'active','2025-05-29 09:22:39','2025-05-29 09:22:39'),(11,1,'VCH-00011','payment','IQD',1.000000,'2025-05-29 12:44:00','الذهاب الى فستقه',NULL,NULL,2,'اجور نقل ابو حسين',NULL,NULL,'active','2025-05-29 09:58:44','2025-05-29 09:58:44'),(12,1,'VCH-00012','receipt','USD',1412.500000,'2025-05-29 00:00:00','سداد فاتورة INV-00001',NULL,NULL,2,'INV-00001',1,NULL,'active','2025-05-29 12:37:24','2025-05-29 12:37:24'),(13,1,'VCH-00013','payment','IQD',1.000000,'2025-05-31 09:32:00','كهوه +فرهاد يطلبني 5 اجور نقل غداء\r\nكوب شاي ورقي+ اكياس قمامه+ زاهي',NULL,NULL,2,'مواد استهلاكيه',NULL,NULL,'active','2025-05-31 06:34:23','2025-05-31 06:34:23'),(14,1,'VCH-00014','transfer','USD',1.000000,'2025-05-31 13:02:00',NULL,3,51,2,NULL,NULL,15,'canceled','2025-05-31 10:04:10','2025-05-31 10:37:28'),(15,1,'VCH-00015','transfer','USD',1.000000,'2025-05-31 13:39:00',NULL,3,51,2,NULL,NULL,17,'active','2025-05-31 10:43:53','2025-05-31 10:43:53'),(16,1,'VCH-00016','payment','IQD',1.000000,'2025-05-31 13:44:00','وجبه الاسبوعيه نهايه شهر 5',NULL,NULL,2,'وجبه غداء الشركه',NULL,NULL,'active','2025-05-31 10:46:32','2025-05-31 10:46:32'),(17,1,'VCH-00017','payment','IQD',1.000000,'2025-05-31 13:47:00','صيانه سبلت الشركه تنزيل + شد + غاز + شراء ريمونت',NULL,NULL,2,'عامل الصيانه',NULL,NULL,'active','2025-05-31 10:48:53','2025-05-31 10:48:53'),(18,1,'VCH-00018','payment','IQD',1.000000,'2025-05-31 13:57:00','تم استلام المبلغ بتاريخ 29/5/2025',NULL,NULL,2,'راتب ابو حسين',NULL,NULL,'active','2025-05-31 10:58:41','2025-05-31 10:58:41'),(19,1,'VCH-00019','transfer','IQD',1.000000,'2025-05-31 14:53:00',NULL,51,50,2,NULL,NULL,21,'active','2025-05-31 11:54:11','2025-05-31 11:54:11'),(20,1,'VCH-00020','payment','IQD',1.000000,'2025-06-01 10:11:00','تم تسديد فاتوره الماء 47 الف + اكراميه 50الف',NULL,NULL,2,'فاتوره الماء',NULL,NULL,'active','2025-06-01 07:18:30','2025-06-01 07:18:30'),(21,1,'VCH-00021','payment','IQD',1.000000,'2025-06-01 10:28:00','تم ايداع مبلغ 4 مليون fip',NULL,NULL,2,'عموله  ايداع',NULL,NULL,'active','2025-06-01 07:35:40','2025-06-01 07:35:40'),(22,1,'VCH-00022','payment','IQD',1.000000,'2025-06-01 10:35:00','الذهاب الى جزائر ايداع فلوس بمكتب دبي الخليج',NULL,NULL,2,'اجور نقل ابو حسين',NULL,NULL,'active','2025-06-01 07:36:49','2025-06-01 07:36:49'),(23,1,'VCH-00023','payment','IQD',1.000000,'2025-06-01 12:29:00','تم  دفع',NULL,NULL,2,'اجور نقل الغداء',NULL,NULL,'active','2025-06-01 09:30:03','2025-06-01 09:30:03'),(24,1,'VCH-00024','payment','IQD',1.000000,'2025-06-01 13:39:00','تم استلام ابو حسين 200 الف',NULL,NULL,2,'محمد حمدان',NULL,NULL,'active','2025-06-01 10:40:06','2025-06-01 10:40:06'),(25,1,'VCH-00025','payment','IQD',1.000000,'2025-06-01 14:30:00','تم استلام 250 الف',NULL,NULL,2,'طعام حذيفه',NULL,NULL,'active','2025-06-01 11:33:18','2025-06-01 11:33:18'),(26,1,'PAY000-24','payment','IQD',1.000000,'2025-06-01 00:00:00','دفع راتب شهر 2025-05 للموظف fatmasaleh',NULL,NULL,2,'fatmasaleh',NULL,NULL,'active','2025-06-01 12:16:14','2025-06-01 12:16:14'),(27,1,'PAY000001','payment','IQD',1.000000,'2025-06-01 00:00:00','دفع راتب شهر 2025-05 للموظف nabaa',NULL,NULL,2,'nabaa',NULL,NULL,'active','2025-06-01 12:17:54','2025-06-01 12:17:54'),(28,1,'PAY000002','payment','IQD',1.000000,'2025-06-01 00:00:00','دفع راتب شهر 2025-05 للموظف mustafa waheed',NULL,NULL,2,'mustafa waheed',NULL,NULL,'active','2025-06-01 12:18:25','2025-06-01 12:18:25'),(29,1,'PAY000003','payment','IQD',1.000000,'2025-06-01 00:00:00','دفع راتب شهر 2025-05 للموظف nafea amaad',NULL,NULL,2,'nafea amaad',NULL,NULL,'active','2025-06-01 12:18:43','2025-06-01 12:18:43'),(30,1,'PAY000004','payment','IQD',1.000000,'2025-06-01 00:00:00','دفع راتب شهر 2025-05 للموظف zainab moayed',NULL,NULL,2,'zainab moayed',NULL,NULL,'active','2025-06-01 12:19:14','2025-06-01 12:19:14'),(31,1,'VCH-00031','payment','IQD',1.000000,'2025-06-01 15:19:00','تم تسليم 1/6/2025',NULL,NULL,2,'راتب فرهاد',NULL,NULL,'active','2025-06-01 12:20:32','2025-06-01 12:20:32'),(32,1,'VCH-00032','payment','IQD',1.000000,'2025-06-01 15:35:00','شراء ماء 12 كارتون 5 دبه',NULL,NULL,2,'مواد استهلاكيه',NULL,NULL,'active','2025-06-01 12:37:08','2025-06-01 12:37:08'),(33,1,'VCH-00033','payment','IQD',1.000000,'2025-06-02 12:52:00','تم تسليم',NULL,NULL,2,'اجور نقل الغداء',NULL,NULL,'active','2025-06-02 09:53:32','2025-06-02 09:53:32'),(34,1,'VCH-00034','payment','IQD',1.000000,'2025-06-03 12:35:00','.',NULL,NULL,2,'اجور نقل الغداء',NULL,NULL,'active','2025-06-03 09:36:33','2025-06-03 09:36:33'),(35,1,'VCH-00035','payment','IQD',1.000000,'2025-06-03 15:42:00','4 الاف شاي+ 6 قهوه',NULL,NULL,2,'مواد استهلاكيه',NULL,NULL,'active','2025-06-03 12:43:14','2025-06-03 12:43:14'),(36,1,'VCH-00036','payment','IQD',1.000000,'2025-06-03 15:55:00','تصليح سياره',NULL,NULL,2,'خاص محمد حمدان',NULL,NULL,'active','2025-06-03 12:56:08','2025-06-03 12:56:08'),(37,1,'VCH-00037','payment','IQD',1.000000,'2025-06-04 11:36:00','تم تسديد فرهاد',NULL,NULL,2,'خاص محمد حمدان',NULL,NULL,'active','2025-06-04 08:37:47','2025-06-04 08:37:47'),(38,1,'VCH-00038','payment','IQD',1.000000,'2025-06-04 11:39:00','طلبيه 63 الف\r\n12 الف ناركيله',NULL,NULL,2,'خاص محمد حمدان',NULL,NULL,'active','2025-06-04 08:40:15','2025-06-04 08:40:15'),(39,1,'VCH-00039','payment','IQD',1.000000,'2025-06-04 11:57:00','تم صرف مبلغ قدره مليون ومئه وخمسه وسبعون دينار عراقي لاغير ويمثل هذا المبلغ دفعه اولى بنسبه 50% من اجمالي المبلغ المتفق عليه لغرض اقامه  المطور حذيفه وقد تم هذا صرف بناء على الاتفاق المبرم بين الطرفين بتاريخ 4/6/2025 على ان يتم سداد الفعه الثانيه عند استلام الاقامه',NULL,NULL,2,'مصطفى محمد عبد الكنعان',NULL,NULL,'canceled','2025-06-04 09:10:15','2025-06-04 09:31:57'),(40,1,'VCH-00040','payment','IQD',1.000000,'2025-06-04 12:33:00','ايداع لدفع الخوادم \r\nجزء من الدفعة',NULL,NULL,1,'محمد حمدان',NULL,NULL,'canceled','2025-06-04 09:34:47','2025-06-07 20:49:04'),(41,1,'VCH-00041','payment','IQD',1.000000,'2025-06-04 12:35:00','تم صرف مبلغ قدره مليونين ومئه وخمسه وسبعون دينار عراقي لاغير ويمثل هذا المبلغ دفعه اولى بنسبه 50% من اجمالي المبلغ المتفق عليه لغرض اقامه المطور حذيفه وقد تم هذا صرف بناء على الاتفاق المبرم بين الطرفين بتاريخ 4/6/2025 على ان يتم سداد الفعه الثانيه عند استلام الاقامه',NULL,NULL,2,'مصطفى محمد عبد الكنعان',NULL,NULL,'active','2025-06-04 09:36:07','2025-06-04 09:36:07'),(42,1,'VCH-00042','payment','IQD',1.000000,'2025-06-04 12:45:00','تم سداد',NULL,NULL,2,'اجور نقل الغداء',NULL,NULL,'active','2025-06-04 09:48:54','2025-06-04 09:48:54'),(43,1,'VCH-00043','payment','IQD',1.000000,'2025-06-04 12:49:00','الذهاب لتسديد مبلغ اقامه حذيفه',NULL,NULL,2,'اجور نقل ابو حسين',NULL,NULL,'active','2025-06-04 09:50:07','2025-06-04 09:50:07'),(44,1,'VCH-00044','payment','IQD',1.000000,'2025-06-04 12:57:00','المتبقي من كروه اقامه حذيفه 15 الف المبلغ كان',NULL,NULL,2,'اجور نقل ابو حسين',NULL,NULL,'active','2025-06-04 10:00:36','2025-06-04 10:00:36'),(45,1,'VCH-00045','payment','IQD',1.000000,'2025-06-04 13:04:00','شكر+ زاهي+ كوب سفري+اكياس نفايه +ماء',NULL,NULL,2,'مواد استهلاكيه',NULL,NULL,'active','2025-06-04 10:06:52','2025-06-04 10:06:52'),(46,1,'VCH-00046','receipt','USD',1412.500000,'2025-06-07 00:00:00','سداد فاتورة INV-00002',NULL,NULL,1,'INV-00002',2,NULL,'active','2025-06-07 20:47:10','2025-06-07 20:47:10'),(47,NULL,'VCH-00047','payment','IQD',1.000000,'2025-05-31 03:22:00','سيرفرات ايداع fib',NULL,NULL,1,'محمد حمدان',NULL,NULL,'active','2025-06-08 00:24:01','2025-06-08 00:24:01'),(48,NULL,'VCH-00048','payment','USD',1.000000,'2025-05-31 12:27:00','خدمات سيرفرات ايداع',NULL,NULL,1,'محمد حمدان',NULL,NULL,'active','2025-06-08 09:28:38','2025-06-08 09:28:38'),(49,NULL,'VCH-00049','payment','IQD',1.000000,'2025-06-10 12:36:00','الذهاب الى فستقه استلام فاتوره',NULL,NULL,2,'اجور نقل ابو حسين',NULL,NULL,'active','2025-06-10 09:36:57','2025-06-10 09:36:57'),(50,NULL,'VCH-00050','receipt','USD',1412.500000,'2025-06-10 00:00:00','سداد فاتورة INV-00003',NULL,NULL,2,'INV-00003',3,NULL,'active','2025-06-10 10:20:10','2025-06-10 10:20:10'),(51,NULL,'VCH-00051','payment','IQD',1.000000,'2025-06-10 15:35:00','ايداع مبلغ الى بطاقه استاذ محمد',NULL,NULL,2,'اجور نقل ابو حسين',NULL,NULL,'active','2025-06-10 12:38:16','2025-06-10 12:38:16'),(52,NULL,'VCH-00052','payment','IQD',1.000000,'2025-06-10 15:38:00','عموله استاذ محمد',NULL,NULL,2,'عموله  ايداع',NULL,NULL,'active','2025-06-10 12:43:23','2025-06-10 12:43:23'),(53,NULL,'VCH-00053','payment','IQD',1.000000,'2025-06-10 15:43:00','ايداع 200 الف بطاقه استاذ محمد',NULL,NULL,2,'خاص محمد حمدان',NULL,NULL,'canceled','2025-06-10 12:45:35','2025-06-11 09:28:34'),(54,NULL,'VCH-00054','payment','IQD',1.000000,'2025-06-11 08:38:00','ايداع مبلغ بلبطاقه',NULL,NULL,2,'خاص محمد حمدان',NULL,NULL,'canceled','2025-06-11 05:39:37','2025-06-11 09:28:18'),(55,NULL,'VCH-00055','payment','IQD',1.000000,'2025-06-11 10:24:00','الذهاب الى عافيات والى مكتب تحويل رواتب الهنود',NULL,NULL,2,'اجور نقل ابو حسين',NULL,NULL,'active','2025-06-11 07:25:25','2025-06-11 07:25:25'),(56,NULL,'VCH-00056','payment','IQD',1.000000,'2025-06-11 10:41:00','تم التسديد',NULL,NULL,2,'وجبه غداء الشركه',NULL,NULL,'active','2025-06-11 07:43:03','2025-06-11 07:43:03'),(57,NULL,'PAY000-55','payment','USD',1420.000000,'2025-06-11 00:00:00','دفع راتب شهر 2025-05 للموظف ibraham',NULL,NULL,2,'ibraham',NULL,NULL,'active','2025-06-11 09:30:13','2025-06-11 09:30:13'),(60,NULL,'VCH-00058','payment','IQD',1.000000,'2025-06-11 12:41:00','تم سداد',NULL,NULL,2,'اجور نقل الغداء',NULL,NULL,'active','2025-06-11 09:42:45','2025-06-11 09:42:45'),(61,NULL,'VCH-00061','payment','IQD',1.000000,'2025-06-11 12:43:00','ايداع بطاقه استاذ محمد 200 الف \r\n500 الف',NULL,NULL,2,'محمد حمدان',NULL,NULL,'active','2025-06-11 09:48:01','2025-06-11 09:48:01'),(62,NULL,'PAY000-60','payment','USD',1420.000000,'2025-06-11 00:00:00','دفع راتب شهر 2025-05 للموظف kaly',NULL,NULL,2,'kaly',NULL,NULL,'active','2025-06-11 09:49:23','2025-06-11 09:49:23'),(71,NULL,'VCH-00063','receipt','USD',1420.000000,'2025-06-11 00:00:00','سداد فاتورة INV-00004',NULL,NULL,2,'INV-00004',4,NULL,'active','2025-06-11 11:51:50','2025-06-11 11:51:50'),(72,NULL,'VCH-00072','payment','IQD',1.000000,'2025-06-11 15:00:00','شراء ماء 26 الف\r\n5 قهوه',NULL,NULL,2,'مواد استهلاكيه',NULL,NULL,'active','2025-06-11 12:02:54','2025-06-11 12:02:54'),(73,NULL,'VCH-00073','payment','IQD',1.000000,'2025-06-11 15:06:00','تم الصيانة',NULL,NULL,2,'صيانه خزان  ماء الشركة',NULL,NULL,'active','2025-06-11 12:10:54','2025-06-11 12:10:54'),(74,NULL,'VCH-00074','payment','IQD',1.000000,'2025-06-11 15:11:00','تم تسديد اجور نقل يوم الخميس لحسناء',NULL,NULL,2,'اجور نقل الغداء',NULL,NULL,'active','2025-06-11 12:12:02','2025-06-11 12:12:02'),(75,NULL,'VCH-00075','payment','USD',1.000000,'2025-06-11 15:13:00','تم صرف مبلغ قدره 2454 دولار من مبلغ فاتورة عافيات لغرض السيرفرات',NULL,NULL,2,'سيرفرات',NULL,NULL,'active','2025-06-11 12:38:03','2025-06-11 12:38:03'),(76,NULL,'PAY000-74','payment','USD',1420.000000,'2025-06-11 00:00:00','دفع راتب شهر 2025-05 للموظف arkan',NULL,NULL,1,'arkan',NULL,NULL,'active','2025-06-11 13:53:15','2025-06-11 13:53:15'),(77,NULL,'VCH-00077','payment','IQD',1.000000,'2025-06-12 13:27:00','ايداع 3000 كي كارد',NULL,NULL,2,'خاص محمد حمدان',NULL,NULL,'active','2025-06-12 10:27:59','2025-06-12 10:27:59'),(78,NULL,'VCH-00078','payment','IQD',1.000000,'2025-06-12 13:28:00','طلبيه',NULL,NULL,2,'محمد حمدان',NULL,NULL,'active','2025-06-12 10:28:56','2025-06-12 10:28:56'),(79,NULL,'VCH-00079','payment','IQD',1.000000,'2025-06-12 13:29:00','كلينكس وقهوه',NULL,NULL,2,'مواد استهلاكيه',NULL,NULL,'active','2025-06-12 10:29:46','2025-06-12 10:29:46'),(80,NULL,'VCH-00080','payment','IQD',1.000000,'2025-06-12 13:29:00','تم',NULL,NULL,2,'اجور نقل الغداء',NULL,NULL,'active','2025-06-12 10:30:17','2025-06-12 10:30:17'),(81,NULL,'VCH-00081','payment','IQD',1.000000,'2025-06-12 13:30:00','الذهاب للجزائر ايداع لبطاقه كي كارد',NULL,NULL,2,'اجور نقل ابو حسين',NULL,NULL,'active','2025-06-12 10:31:31','2025-06-12 10:31:31'),(82,NULL,'VCH-00082','payment','IQD',1.000000,'2025-06-12 13:31:00','تم',NULL,NULL,2,'عموله  ايداع',NULL,NULL,'active','2025-06-12 10:33:49','2025-06-12 10:33:49'),(83,NULL,'VCH-00083','payment','IQD',1.000000,'2025-06-12 13:34:00','صيانه ماطور الشركه وكذلك سويج الكهرباء',NULL,NULL,2,'صيانه ماطور الشركه',NULL,NULL,'active','2025-06-12 10:36:46','2025-06-12 10:36:46'),(84,NULL,'VCH-00084','receipt','USD',1420.000000,'2025-06-12 00:00:00','سداد فاتورة INV-00005',NULL,NULL,2,'INV-00005',5,NULL,'active','2025-06-12 11:45:41','2025-06-12 11:45:41'),(85,NULL,'VCH-00085','transfer','USD',1.000000,'2025-06-12 14:51:00',NULL,3,51,2,NULL,NULL,100,'active','2025-06-12 11:51:28','2025-06-12 11:51:28'),(86,NULL,'VCH-00086','payment','IQD',1.000000,'2025-06-12 14:51:00','فاتورة شراء شخصيه لأستاذ محمد',NULL,NULL,2,'خاص محمد حمدان',NULL,NULL,'active','2025-06-12 11:53:38','2025-06-12 11:53:38'),(87,NULL,'PAY000-85','payment','USD',1420.000000,'2025-06-14 00:00:00','دفع راتب شهر 2025-05 للموظف umer',NULL,NULL,2,'umer',NULL,NULL,'active','2025-06-14 09:51:34','2025-06-14 09:51:34'),(104,NULL,'PAY000005','payment','USD',1420.000000,'2025-06-14 00:00:00','دفع راتب شهر 2025-05 للموظف tareq',NULL,NULL,2,'tareq',NULL,NULL,'active','2025-06-14 10:39:46','2025-06-14 10:39:46'),(105,NULL,'PAY000006','payment','USD',1420.000000,'2025-06-14 00:00:00','دفع راتب شهر 2025-05 للموظف ateeb',NULL,NULL,2,'ateeb',NULL,NULL,'active','2025-06-14 10:40:41','2025-06-14 10:40:41'),(106,NULL,'PAY000007','payment','USD',1420.000000,'2025-06-14 00:00:00','دفع راتب شهر 2025-05 للموظف Hisham',NULL,NULL,2,'Hisham',NULL,NULL,'active','2025-06-14 10:40:50','2025-06-14 10:40:50'),(107,NULL,'PAY000008','payment','USD',1420.000000,'2025-06-14 00:00:00','دفع راتب شهر 2025-05 للموظف Hothaifa Jaber',NULL,NULL,2,'Hothaifa Jaber',NULL,NULL,'active','2025-06-14 10:40:58','2025-06-14 10:40:58'),(108,NULL,'PAY000009','payment','USD',1420.000000,'2025-06-14 00:00:00','دفع راتب شهر 2025-05 للموظف Mohamed jaber',NULL,NULL,2,'Mohamed jaber',NULL,NULL,'active','2025-06-14 10:41:07','2025-06-14 10:41:07'),(109,NULL,'PAY000010','payment','USD',1420.000000,'2025-06-14 00:00:00','دفع راتب شهر 2025-05 للموظف Mohammad hassnain',NULL,NULL,2,'Mohammad hassnain',NULL,NULL,'active','2025-06-14 10:41:16','2025-06-14 10:41:16'),(110,NULL,'PAY000011','payment','USD',1420.000000,'2025-06-14 00:00:00','دفع راتب شهر 2025-05 للموظف naveed',NULL,NULL,2,'naveed',NULL,NULL,'active','2025-06-14 10:41:25','2025-06-14 10:41:25'),(111,NULL,'PAY000012','payment','USD',1420.000000,'2025-06-14 00:00:00','دفع راتب شهر 2025-05 للموظف firas',NULL,NULL,2,'firas',NULL,NULL,'active','2025-06-14 10:41:33','2025-06-14 10:41:33'),(112,NULL,'PAY000013','payment','USD',1420.000000,'2025-06-14 00:00:00','دفع راتب شهر 2025-05 للموظف siraj',NULL,NULL,2,'siraj',NULL,NULL,'active','2025-06-14 10:41:41','2025-06-14 10:41:41'),(113,NULL,'PAY000014','payment','USD',1420.000000,'2025-06-14 00:00:00','دفع راتب شهر 2025-05 للموظف sadqain',NULL,NULL,2,'sadqain',NULL,NULL,'active','2025-06-14 10:41:56','2025-06-14 10:41:56'),(114,NULL,'PAY000015','payment','USD',1420.000000,'2025-06-14 00:00:00','دفع راتب شهر 2025-05 للموظف usama',NULL,NULL,2,'usama',NULL,NULL,'active','2025-06-14 10:42:07','2025-06-14 10:42:07'),(115,NULL,'VCH-00115','payment','USD',1.000000,'2025-06-14 13:42:00','تم دفع راتب المطور منصور 700$ شهر الرابع 15/4/2025',NULL,NULL,2,'راتب منصور',NULL,NULL,'active','2025-06-14 10:44:59','2025-06-14 10:44:59'),(116,NULL,'VCH-00116','transfer','USD',1.000000,'2025-06-14 14:35:00',NULL,3,51,2,NULL,NULL,115,'active','2025-06-14 11:37:21','2025-06-14 11:37:21'),(117,NULL,'VCH-00117','payment','USD',1.000000,'2025-06-15 08:08:00','تم استقطاع من فاتوره فستقه مبلغ قدره670 الف دينار  لغرض سيرفرات',NULL,NULL,2,'سيرفرات',NULL,NULL,'active','2025-06-15 06:10:49','2025-06-15 06:10:49'),(118,NULL,'VCH-00118','payment','IQD',1.000000,'2025-06-15 09:18:00','تم اضافه مبلغ فاتوره عافيات الى رواتب المطورين الاجانب لاكمال الحواله بلكامل',NULL,NULL,2,'رواتب المطورين',NULL,NULL,'active','2025-06-15 06:22:02','2025-06-15 06:22:02'),(119,NULL,'VCH-00119','receipt','IQD',1.000000,'2025-06-15 14:56:00','فرق صرف',NULL,NULL,2,'فاطمه',NULL,NULL,'active','2025-06-15 11:59:25','2025-06-15 11:59:25'),(120,NULL,'VCH-00120','payment','IQD',1.000000,'2025-06-15 15:25:00','تم دفع المبلغ لشراء طن كاز للمولد',NULL,NULL,2,'ابو احمد ( شراء كاز للمولد)',NULL,NULL,'active','2025-06-15 12:38:19','2025-06-15 12:38:19'),(121,NULL,'VCH-00121','payment','IQD',1.000000,'2025-06-16 08:23:00','مساحيق تنظيف',NULL,NULL,2,'مواد استهلاكيه',NULL,NULL,'active','2025-06-16 05:24:05','2025-06-16 05:24:05'),(122,NULL,'VCH-00122','payment','IQD',1.000000,'2025-06-16 12:26:00','تم دفع',NULL,NULL,2,'اجور نقل الغداء',NULL,NULL,'active','2025-06-16 09:26:42','2025-06-16 09:26:42'),(123,NULL,'VCH-00123','payment','IQD',1.000000,'2025-06-16 15:57:00','صيانه سبلت  غرفه التطوير  + تبديل سويجات',NULL,NULL,2,'صيانه سبلت الشركه',NULL,NULL,'active','2025-06-16 13:00:57','2025-06-16 13:00:57'),(124,NULL,'VCH-00124','payment','IQD',1.000000,'2025-06-17 15:29:00','تم دفع ل علي',NULL,NULL,2,'اجور نقل الغداء',NULL,NULL,'active','2025-06-17 12:30:25','2025-06-17 12:30:25'),(125,NULL,'VCH-00125','payment','IQD',1.000000,'2025-06-18 14:07:00','تم دفع تكسي بلي',NULL,NULL,2,'اجور نقل الغداء',NULL,NULL,'active','2025-06-18 11:08:36','2025-06-18 11:08:36'),(126,NULL,'VCH-00126','receipt','IQD',1.000000,'2025-06-18 14:13:00','تم استلام مبلغ مالي من المدير محمد حمدان',NULL,NULL,2,'فاطمه',NULL,NULL,'active','2025-06-18 11:18:06','2025-06-18 11:18:06'),(127,NULL,'VCH-00127','payment','IQD',1.000000,'2025-06-18 14:18:00','صرف مبلغ 125$ كتعويض للموظف هشام عن عموله التحويل التي خصمت من راتبه عند ارسال الحواله الى المغرب اي مايعادل 180 الف عراقي (راتب شهر الخامس 15/5/2025) تم قطع عموله 25 دولار  ( مكتب البصرة 10$ ومكتب قطر 15$ )',NULL,NULL,2,'المطور هشام المغربي',NULL,NULL,'active','2025-06-18 11:27:35','2025-06-18 11:27:35'),(128,NULL,'VCH-00128','payment','IQD',1.000000,'2025-06-18 14:28:00','تم صرف  مبلغ لقاء خدمات تقنيه ( سيرفرات)',NULL,NULL,2,'سيرفرات',NULL,NULL,'active','2025-06-18 11:31:16','2025-06-18 11:31:16'),(129,NULL,'VCH-00129','payment','IQD',1.000000,'2025-06-19 10:04:00','شراء ماء + قهوه',NULL,NULL,2,'مواد استهلاكيه',NULL,NULL,'active','2025-06-19 07:05:07','2025-06-19 07:05:07'),(130,NULL,'VCH-00130','payment','IQD',1.000000,'2025-06-19 12:25:00','صرف مبلغ قدره 190 الف الى المطعم مقابل تقديم وجبه طعام لموظفي الشركة وتشمل مبلغ 40 الف عن وجبه واحده خلال اسبوع عيد الاضحى حيث تم التزويد بوجبه في يوم عمل واحد فقط وتم تسديد مبلغ 150 الف عن وجبات الاسبوع الحالي بالكامل حسب الجدول المتفق عليه',NULL,NULL,2,'وجبه غداء الشركه',NULL,NULL,'active','2025-06-19 09:42:22','2025-06-19 09:42:22'),(131,NULL,'VCH-00131','payment','IQD',1.000000,'2025-06-19 12:42:00','دفع اجور نقل وجبات الغداء من المطعم الى مقر الشركة',NULL,NULL,2,'اجور نقل الغداء',NULL,NULL,'active','2025-06-19 09:48:16','2025-06-19 09:48:16'),(132,NULL,'VCH-00132','payment','IQD',1.000000,'2025-06-22 14:42:00','دفع اجور نقل وجبات الغداء من المطعم الى مقر الشركة',NULL,NULL,2,'اجور نقل الغداء',NULL,NULL,'active','2025-06-22 11:43:28','2025-06-22 11:43:28'),(133,NULL,'VCH-00133','payment','IQD',1.000000,'2025-06-23 10:16:00','مقابل شراء مواد استهلاكية لصالح الشركة، وتشمل:\r\n\r\nمناديل ورقية (كلينكس)  أكواب ورقية وذلك لاستخدامها ضمن احتياجات المرافق الإدارية والمكتبية اليومية بناءً على طلب القسم المختص، وتحت إشراف الإدارة.',NULL,NULL,2,'مواد استهلاكيه',NULL,NULL,'active','2025-06-23 07:21:39','2025-06-23 07:21:39'),(134,NULL,'VCH-00134','payment','IQD',1.000000,'2025-06-23 12:42:00','دفع اجور نقل وجبات الغداء من المطعم الى مقر الشركة',NULL,NULL,2,'اجور نقل الغداء',NULL,NULL,'active','2025-06-23 09:43:36','2025-06-23 09:43:36'),(135,NULL,'VCH-00135','payment','IQD',1.000000,'2025-06-24 13:35:00','دفع اجور نقل وجبات الغداء من المطعم الى مقر الشركة',NULL,NULL,2,'اجور نقل الغداء',NULL,NULL,'active','2025-06-24 10:53:02','2025-06-24 10:53:02'),(136,NULL,'VCH-00136','payment','IQD',1.000000,'2025-06-24 14:24:00','تم صرف مبلغ مقابل شراء مواد استهلاكية لصالح الشركة، وتشمل:\r\n\r\nقهوه  وسكر  وذلك لاستخدامها ضمن احتياجات المرافق الإدارية والمكتبية اليومية بناءً على طلب القسم المختص، وتحت إشراف الإدارة.',NULL,NULL,2,'مواد استهلاكيه',NULL,NULL,'active','2025-06-24 11:30:39','2025-06-24 11:30:39'),(137,NULL,'VCH-00137','receipt','IQD',1.000000,'2025-06-24 14:47:00','من بطاقه fip الخاصه باستاذ محمد حمدان',NULL,NULL,2,'فاطمه',NULL,NULL,'active','2025-06-24 11:53:16','2025-06-24 11:53:16'),(138,NULL,'VCH-00138','payment','IQD',1.000000,'2025-06-24 14:53:00','تم صرف مبلغ لقاء خدمات تقنيه ( سيرفرات) مبلغ قدره300 الف وعموله 5 الاف',NULL,NULL,2,'سيرفرات',NULL,NULL,'active','2025-06-24 11:56:21','2025-06-24 11:56:21'),(139,NULL,'VCH-00139','receipt','IQD',1.000000,'2025-06-24 14:56:00','مصروف شركه',NULL,NULL,2,'فاطمه',NULL,NULL,'active','2025-06-24 11:58:20','2025-06-24 11:58:20'),(140,NULL,'VCH-00140','payment','IQD',1.000000,'2025-06-25 09:43:00','مقابل شراء مواد استهلاكية لصالح الشركة، وتشمل: وصله ارضيه وكلور واكواب ورقيه وذلك لاستخدامها ضمن احتياجات المرافق الإدارية والمكتبية اليومية بناءً على طلب القسم المختص، وتحت إشراف الإدارة.',NULL,NULL,2,'مواد استهلاكيه',NULL,NULL,'active','2025-06-25 06:45:46','2025-06-25 06:45:46'),(141,NULL,'VCH-00141','payment','IQD',1.000000,'2025-06-25 15:13:00','اجور نقل ابو حسين 10 الاف الذهاب الى مكتب الجزائر لإيداع مبلغ  والعودة الى مقر الشركة بتاريخ 24/6/2025\r\nو15 الف الذهاب الى فستقه والعودة الى مقر الشركة بتاريخ 25/6/2025',NULL,NULL,2,'اجور نقل ابو حسين',NULL,NULL,'active','2025-06-25 12:17:56','2025-06-25 12:17:56'),(142,NULL,'VCH-00142','receipt','USD',1420.000000,'2025-06-25 00:00:00','سداد فاتورة INV-00006',NULL,NULL,2,'INV-00006',6,NULL,'active','2025-06-25 12:52:02','2025-06-25 12:52:02'),(143,NULL,'VCH-00143','transfer','USD',1.000000,'2025-06-26 09:11:00',NULL,3,51,2,NULL,NULL,143,'canceled','2025-06-26 06:12:31','2025-06-26 09:17:59'),(144,NULL,'VCH-00144','transfer','USD',1.000000,'2025-06-26 12:22:00',NULL,3,51,2,NULL,NULL,145,'canceled','2025-06-26 09:23:17','2025-06-26 09:24:53'),(145,NULL,'VCH-00145','transfer','USD',1.000000,'2025-06-26 13:19:00',NULL,3,51,2,NULL,NULL,147,'active','2025-06-26 10:20:00','2025-06-26 10:20:00'),(146,NULL,'VCH-00146','transfer','IQD',1.000000,'2025-06-26 13:20:00',NULL,51,50,2,NULL,NULL,148,'active','2025-06-26 10:21:13','2025-06-26 10:21:13'),(147,NULL,'VCH-00147','payment','IQD',1.000000,'2025-06-28 10:13:00','تم تسديد مبلغ 150 الف عن وجبات الاسبوع  بالكامل حسب الجدول المتفق عليه',NULL,NULL,2,'وجبه غداء الشركه',NULL,NULL,'active','2025-06-28 07:18:12','2025-06-28 07:18:12'),(148,NULL,'VCH-00148','payment','IQD',1.000000,'2025-06-28 10:19:00','دفع اجور نقل وجبات الغداء من المطعم الى مقر الشركة يوم الخميس',NULL,NULL,2,'اجور نقل الغداء',NULL,NULL,'active','2025-06-28 07:20:13','2025-06-28 07:20:13'),(149,NULL,'VCH-00149','payment','IQD',1.000000,'2025-06-29 11:53:00','دفع اجور نقل وجبات الغداء من المطعم الى مقر الشركة الاحد والاثنين',NULL,NULL,2,'اجور نقل الغداء',NULL,NULL,'active','2025-06-29 08:55:09','2025-06-29 08:55:09'),(150,NULL,'VCH-00150','payment','IQD',1.000000,'2025-06-29 14:01:00','الذهاب الى العشار ثم الجزائر ثم الرجوع الى مقر الشركه لتبديل عدسات نظارات المدير يوم الخميس',NULL,NULL,2,'اجور نقل ابو حسين',NULL,NULL,'active','2025-06-29 11:03:23','2025-06-29 11:03:23'),(151,NULL,'VCH-00151','payment','IQD',1.000000,'2025-06-30 11:30:00','مقابل شراء مواد استهلاكية لصالح الشركة، وتشمل: كراتين ماء 40 الف + قهوه 6 الاف + اكياس قمامه ع اكواب ورقيه  وذلك لاستخدامها ضمن احتياجات الشركه اليومية بناءً على طلب القسم المختص، وتحت إشراف الإدارة.',NULL,NULL,2,'مواد استهلاكيه',NULL,NULL,'active','2025-06-30 08:33:19','2025-06-30 08:33:19'),(152,NULL,'VCH-00152','payment','IQD',1.000000,'2025-06-30 12:01:00','أجور نقل لمهمة الذهاب إلى شركة فستقه، وذلك لغرض استلام مبالغ فواتير مستحقة لصالح شركتنا، وفقاً لما تقتضيه طبيعة العمل. وقد تم تنفيذ المهمة',NULL,NULL,2,'اجور نقل ابو حسين',NULL,NULL,'active','2025-06-30 09:04:59','2025-06-30 09:04:59'),(153,NULL,'VCH-00153','payment','IQD',1.000000,'2025-06-30 12:16:00','الذهاب الى جزائر ايداع مبلغ 5 مليون',NULL,NULL,2,'اجور نقل ابو حسين',NULL,NULL,'active','2025-06-30 09:17:28','2025-06-30 09:17:28'),(154,NULL,'VCH-00154','payment','IQD',1.000000,'2025-06-30 13:47:00','صرف واتساب',NULL,NULL,1,'محمد حمدان',NULL,NULL,'active','2025-06-30 10:47:50','2025-06-30 10:47:50'),(155,NULL,'VCH-00155','receipt','USD',1415.000000,'2025-06-30 00:00:00','سداد فاتورة INV-00007',NULL,NULL,2,'INV-00007',7,NULL,'active','2025-06-30 11:48:14','2025-06-30 11:48:14'),(156,NULL,'VCH-00156','receipt','USD',1415.000000,'2025-06-30 00:00:00','سداد فاتورة INV-00008',NULL,NULL,2,'INV-00008',8,NULL,'active','2025-06-30 11:49:43','2025-06-30 11:49:43'),(157,NULL,'VCH-00157','transfer','USD',1.000000,'2025-06-30 14:50:00',NULL,3,51,2,NULL,NULL,161,'active','2025-06-30 12:01:30','2025-06-30 12:01:30'),(158,NULL,'VCH-00158','payment','IQD',1.000000,'2025-06-30 15:02:00','تم صرف مبلغ وقدره 5,015,000 (خمسة ملايين وخمسة عشر ألف فقط لا غير) إلى حساب الأستاذ محمد (Master) بواقع:\r\n	•	5,000,000 مقابل خدمات أونلاين وسيرفرات لصالح الشركة.\r\n	•	15,000 كعمولة تحويل/إيداع.\r\n\r\nوذلك لغرض تنفيذ خدمات تقنية وتشغيل السيرفرات الخاصة بالشركة، وقد تم الإيداع بتاريخ (30/6/2025) بموجب تعليمات الإدارة',NULL,NULL,2,'سيرفرات',NULL,NULL,'active','2025-06-30 12:32:01','2025-06-30 12:32:01'),(159,NULL,'VCH-00159','payment','IQD',1.000000,'2025-06-30 15:32:00','تم صرف مبلغ وقدره 5,015,000 (خمسة ملايين وخمسة عشر ألف فقط لا غير) إلى حساب علي صادق (ابوحسين)(Master) بواقع:\r\n	•	5,000,000 مقابل خدمات أونلاين وسيرفرات لصالح الشركة.\r\n	•	15,000 كعمولة تحويل/إيداع.\r\n\r\nوذلك لغرض تنفيذ خدمات تقنية وتشغيل السيرفرات الخاصة بالشركة، وقد تم الإيداع بتاريخ (30/6/2025) بموجب تعليمات الإدارة',NULL,NULL,2,'سيرفرات',NULL,NULL,'active','2025-06-30 12:34:03','2025-06-30 12:34:03'),(160,NULL,'VCH-00160','payment','IQD',1.000000,'2025-06-30 15:34:00','اجور نقل ابو حسين لغرض الايداع',NULL,NULL,2,'10000',NULL,NULL,'active','2025-06-30 12:34:50','2025-06-30 12:34:50'),(161,NULL,'VCH-00161','payment','IQD',1.000000,'2025-06-30 15:48:00','تم صرف مبلغ وقدره (235000) إلى فرهاد، وذلك بناءً على توجيه مباشر من استاذ محمد',NULL,NULL,2,'فرهاد',NULL,NULL,'active','2025-06-30 12:53:38','2025-06-30 12:53:38'),(162,NULL,'VCH-00162','payment','IQD',1.000000,'2025-07-01 11:12:00','مقابل شراء مواد استهلاكية لصالح الشركة، وتشمل: شاي لبتون عدد2 بناءً على طلب القسم المختص، وتحت إشراف الإدارة.',NULL,NULL,2,'مواد استهلاكيه',NULL,NULL,'active','2025-07-01 08:14:28','2025-07-01 08:14:28'),(163,NULL,'PAY000016','payment','IQD',1.000000,'2025-07-01 00:00:00','دفع راتب شهر 2025-06 للموظف علي صادق',NULL,NULL,2,'علي صادق',NULL,NULL,'active','2025-07-01 09:25:47','2025-07-01 09:25:47'),(164,NULL,'PAY000017','payment','IQD',1.000000,'2025-07-01 00:00:00','دفع راتب شهر 2025-06 للموظف فرهاد حسين',NULL,NULL,2,'فرهاد حسين',NULL,NULL,'active','2025-07-01 09:26:53','2025-07-01 09:26:53'),(165,NULL,'PAY000018','payment','IQD',1.000000,'2025-07-01 00:00:00','دفع راتب شهر 2025-06 للموظف mustafa waheed',NULL,NULL,2,'mustafa waheed',NULL,NULL,'active','2025-07-01 09:28:21','2025-07-01 09:28:21'),(166,NULL,'PAY000019','payment','IQD',1.000000,'2025-07-01 00:00:00','دفع راتب شهر 2025-06 للموظف nabaa',NULL,NULL,2,'nabaa',NULL,NULL,'active','2025-07-01 09:32:01','2025-07-01 09:32:01'),(167,NULL,'PAY000020','payment','IQD',1.000000,'2025-07-01 00:00:00','دفع راتب شهر 2025-06 للموظف zainab moayed',NULL,NULL,2,'zainab moayed',NULL,NULL,'active','2025-07-01 09:33:58','2025-07-01 09:33:58'),(168,NULL,'PAY000021','payment','IQD',1.000000,'2025-07-01 00:00:00','دفع راتب شهر 2025-06 للموظف zahraa  Jumaah',NULL,NULL,2,'zahraa  Jumaah',NULL,NULL,'active','2025-07-01 09:36:14','2025-07-01 09:36:14'),(169,NULL,'PAY000022','payment','IQD',1.000000,'2025-07-01 00:00:00','دفع راتب شهر 2025-06 للموظف nafea amaad',NULL,NULL,2,'nafea amaad',NULL,NULL,'active','2025-07-01 09:38:54','2025-07-01 09:38:54'),(170,NULL,'PAY000023','payment','IQD',1.000000,'2025-07-01 00:00:00','دفع راتب شهر 2025-06 للموظف fatmasaleh',NULL,NULL,1,'fatmasaleh',NULL,NULL,'active','2025-07-01 09:55:42','2025-07-01 09:55:42'),(171,NULL,'VCH-00171','payment','IQD',1.000000,'2025-07-01 13:01:00','تم صرف مبلغ وقدره 70 الف إلى فرهاد، من الحساب ا الأستاذ محمد، وذلك بناءً على توجيه مباشر من استاذ محمد',NULL,NULL,2,'فرهاد',NULL,NULL,'active','2025-07-01 10:04:36','2025-07-01 10:04:36'),(172,NULL,'VCH-00172','payment','IQD',1.000000,'2025-07-01 14:34:00','استلمت شركة: [طائر الشرق\r\nالمبلغ: 814,000 (ثمانمائة وأربعة عشر ألفًا دينار لا غير)\r\nوذلك مقابل: تسديد مستحقات حجوزات طيران / تذاكر سفر / خدمات النقل الجوي (لغرض السفر)  وتم تسديد المبلغ بالكامل نقدا',NULL,NULL,2,'شركة طائر الشرق',NULL,NULL,'active','2025-07-01 11:38:29','2025-07-01 11:38:29'),(173,NULL,'VCH-00173','payment','IQD',1.000000,'2025-07-02 10:50:00','تم صرف مبلغ وقدره 1.600.000 (مليون وستمائة الف فقط لا غير) إلى حساب استاذ محمد (Master) بواقع:  1.600.000مقابل خدمات أونلاين وسيرفرات لصالح الشركة. • 10.000كعمولة تحويل/إيداع. وذلك لغرض تنفيذ خدمات تقنية وتشغيل السيرفرات الخاصة بالشركة، وقد تم الإيداع بتاريخ (30/6/2025) بموجب تعليمات الإدارة',NULL,NULL,2,'سيرفرات',NULL,NULL,'active','2025-07-02 07:59:08','2025-07-02 07:59:08'),(174,NULL,'VCH-00174','payment','IQD',1.000000,'2025-07-02 10:59:00','Jتم صرف مبلغ وقدره 728.000 الف دينار لا غير، وذلك مقابل اشتراك الأستاذ/ محمد في تطبيق فودو (VODU) لشهر يوليو (شهر 7) وتم تسديد المبلغ نقدا بتاريخ 1/7/2025',NULL,NULL,2,'اشتراك تطبيق فودو',NULL,NULL,'active','2025-07-02 08:23:20','2025-07-02 08:23:20'),(175,NULL,'VCH-00175','payment','IQD',1.000000,'2025-07-02 11:23:00','أجور نقل لمهمة الذهاب إلى مكتب التحويل لغرض ايداع مبلغ مالي لبطاقة استاذ محمد والعودة الى مقر الشركة',NULL,NULL,2,'اجور نقل ابو حسين',NULL,NULL,'active','2025-07-02 08:25:41','2025-07-02 08:25:41'),(176,NULL,'VCH-00176','payment','IQD',1.000000,'2025-07-02 11:25:00','دفع اجور نقل وجبات الغداء من المطعم الى مقر الشركة',NULL,NULL,2,'اجور نقل الغداء',NULL,NULL,'active','2025-07-02 08:26:37','2025-07-02 08:26:37'),(177,NULL,'VCH-00177','payment','IQD',1.000000,'2025-07-02 11:26:00','مقابل شراء مواد استهلاكية لصالح الشركة، وتشمل: سيليفون بناءً على طلب القسم المختص، وتحت إشراف الإدارة.',NULL,NULL,2,'مواد استهلاكيه',NULL,NULL,'active','2025-07-02 08:34:04','2025-07-02 08:34:04'),(178,NULL,'VCH-00178','payment','IQD',1.000000,'2025-07-02 11:58:00','دفع اجور نقل وجبات الغداء من المطعم الى مقر الشركة',NULL,NULL,2,'اجور نقل الغداء',NULL,NULL,'active','2025-07-02 08:59:27','2025-07-02 08:59:27'),(179,NULL,'VCH-00179','payment','IQD',1.000000,'2025-07-03 11:25:00','تم تسديد مبلغ 150 الف عن وجبات الاسبوع الحالي بالكامل حسب الجدول المتفق عليه',NULL,NULL,2,'وجبه غداء الشركه',NULL,NULL,'active','2025-07-03 08:42:59','2025-07-03 08:42:59'),(180,NULL,'VCH-00180','payment','IQD',1.000000,'2025-07-03 11:45:00','دفع اجور نقل وجبات الغداء من المطعم الى مقر الشركة',NULL,NULL,2,'اجور نقل الغداء',NULL,NULL,'active','2025-07-03 08:46:06','2025-07-03 08:46:06'),(181,NULL,'VCH-00181','payment','IQD',1.000000,'2025-07-07 10:06:00','مقابل شراء مواد استهلاكية لصالح الشركة، وذلك لاستخدامها ضمن احتياجات المرافق الإدارية والمكتبية اليومية بناءً على طلب القسم المختص، وتحت إشراف الإدارة.',NULL,NULL,2,'مواد استهلاكيه',NULL,NULL,'active','2025-07-07 07:08:47','2025-07-07 07:08:47'),(182,NULL,'VCH-00182','payment','IQD',1.000000,'2025-07-07 10:56:00','تم صرف مبلغ وقدره 500 الف(خمسمائة الف فقط لا غير) إلى حساب استاذ محمد (Master) بواقع:500.000مقابل خدمات أونلاين وسيرفرات لصالح الشركة. •  تم إيداع. وذلك لغرض تنفيذ خدمات تقنية وتشغيل السيرفرات الخاصة بالشركة، وقد تم الإيداع بتاريخ (5/6/2025) بموجب تعليمات الإدارة',NULL,NULL,2,'سيرفرات',NULL,NULL,'active','2025-07-07 08:00:19','2025-07-07 08:00:19'),(183,NULL,'VCH-00183','payment','IQD',1.000000,'2025-07-07 12:12:00','دفع اجور نقل وجبات الغداء من المطعم الى مقر الشركة',NULL,NULL,2,'اجور نقل الغداء',NULL,NULL,'active','2025-07-07 09:14:00','2025-07-07 09:14:00'),(184,NULL,'VCH-00184','transfer','IQD',1.000000,'2025-07-09 14:32:00',NULL,51,3,2,NULL,NULL,190,'active','2025-07-09 12:28:21','2025-07-09 12:28:21'),(185,NULL,'VCH-00185','payment','IQD',1.000000,'2025-07-09 15:50:00','مقابل شراء مواد استهلاكية لصالح الشركة، وتشمل:\r\n\r\nمناديل ورقية (كلينكس)  9 الاف أكواب ورقية 3 الاف وصله 3 الاف كراتين ماء  عدد20 المبلغ 35 الف شاي عدد 2 المبلغ 8 الاف سكر  3الف وذلك لاستخدامها ضمن احتياجات المرافق الإدارية والمكتبية اليومية بناءً على طلب القسم المختص، وتحت إشراف الإدارة.',NULL,NULL,2,'مواد استهلاكيه',NULL,NULL,'active','2025-07-09 12:55:05','2025-07-09 12:55:05'),(186,NULL,'VCH-00186','payment','IQD',1.000000,'2025-07-10 10:26:00','تم صرف مبلغ وقدره 294 الف (مئتان واربعه وتسعون الف دينار فقط لا غير) وذلك لسداد فاتورة الكهرباء الخاصة بمقر لشركه، ويشمل المبلغ إكرامية قدرها 75,000 الف دينار عراقي',NULL,NULL,2,'فاتورة الكهرباء',NULL,NULL,'active','2025-07-10 07:31:13','2025-07-10 07:31:13'),(187,NULL,'VCH-00187','payment','IQD',1.000000,'2025-07-10 12:16:00','أجور نقل لمهمة الذهاب إلى مكتب عافيات وذلك لغرض استلام مبلغ فواتير مستحقة لصالح شركتنا، وفقاً لما تقتضيه طبيعة العمل. وقد تم تنفيذ المهمة  والرجوع الى مقر الشركة',NULL,NULL,2,'اجور نقل ابو حسين',NULL,NULL,'active','2025-07-10 09:18:38','2025-07-10 09:18:38');
/*!40000 ALTER TABLE `vouchers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'accaltatweertech_m2'
--

--
-- Dumping routines for database 'accaltatweertech_m2'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-07-11 17:58:05
