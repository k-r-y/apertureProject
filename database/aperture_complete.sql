-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: aperture
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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
-- Current Database: `aperture`
--

/*!40000 DROP DATABASE IF EXISTS `aperture`*/;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `aperture` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;

USE `aperture`;

--
-- Table structure for table `addons`
--

DROP TABLE IF EXISTS `addons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `addons` (
  `addID` int(11) NOT NULL AUTO_INCREMENT,
  `packageID` varchar(100) NOT NULL,
  `Description` varchar(255) NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`addID`),
  KEY `packageID` (`packageID`),
  CONSTRAINT `addons_ibfk_1` FOREIGN KEY (`packageID`) REFERENCES `packages` (`packageID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10027 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `addons`
--

LOCK TABLES `addons` WRITE;
/*!40000 ALTER TABLE `addons` DISABLE KEYS */;
INSERT INTO `addons` VALUES (10015,'basic','Extended Access (1 Year Gallery & Cloud Storage)',800.00),(10016,'basic','Express Editing (1-week delivery)',1000.00),(10018,'standard','Same-Day Edit',2000.00),(10019,'standard','Additional Photographer',1500.00),(10020,'standard','Extended Access (1 Year Gallery & Cloud Storage)',1000.00),(10021,'standard','4K Video Upgrade',1000.00),(10023,'premium','4K Ultra HD Upgrade',1500.00),(10024,'premium','Second Drone Unit',2000.00),(10025,'premium','Live Streaming Setup',2500.00),(10026,'premium','Extended Access (2 Years Gallery & Cloud Storage)',1500.00);
/*!40000 ALTER TABLE `addons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `api_rate_limit`
--

DROP TABLE IF EXISTS `api_rate_limit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_rate_limit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(255) NOT NULL,
  `endpoint` varchar(255) NOT NULL,
  `request_count` int(11) DEFAULT 1,
  `window_start` datetime NOT NULL,
  `last_request` datetime NOT NULL,
  `is_blocked` tinyint(1) DEFAULT 0,
  `blocked_until` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_identifier` (`identifier`),
  KEY `idx_endpoint` (`endpoint`),
  KEY `idx_window` (`window_start`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_rate_limit`
--

LOCK TABLES `api_rate_limit` WRITE;
/*!40000 ALTER TABLE `api_rate_limit` DISABLE KEYS */;
INSERT INTO `api_rate_limit` VALUES (1,'0869e5154d4b2fca35910f6178c3634ab1bbcc19e1b6f0091a5a8063cadbf834','/api/get_booked_dates',1,'2025-11-24 13:20:53','2025-11-24 13:20:53',0,NULL),(2,'0869e5154d4b2fca35910f6178c3634ab1bbcc19e1b6f0091a5a8063cadbf834','/api/getPackageDetails',3,'2025-11-24 13:20:54','2025-11-24 13:21:00',0,NULL),(3,'6ade553ca81b30acd59a66ac330827a5cf4be10fcf07b2066f16308ebaf0747b','/api/check_availability',4,'2025-11-24 13:21:43','2025-11-24 14:15:27',0,NULL),(4,'6ade553ca81b30acd59a66ac330827a5cf4be10fcf07b2066f16308ebaf0747b','/api/getPackageDetails',3,'2025-11-24 13:25:52','2025-11-24 14:15:53',0,NULL),(5,'6ade553ca81b30acd59a66ac330827a5cf4be10fcf07b2066f16308ebaf0747b','/api/get_booked_dates',5,'2025-11-24 13:25:58','2025-11-24 14:16:31',0,NULL),(6,'6ade553ca81b30acd59a66ac330827a5cf4be10fcf07b2066f16308ebaf0747b','/api/getAppointments',20,'2025-11-24 13:26:10','2025-11-24 14:16:25',0,NULL),(7,'6ade553ca81b30acd59a66ac330827a5cf4be10fcf07b2066f16308ebaf0747b','/api/getPhotos',2,'2025-11-24 13:26:36','2025-11-24 14:16:27',0,NULL),(8,'6ade553ca81b30acd59a66ac330827a5cf4be10fcf07b2066f16308ebaf0747b','/api/get_booked_dates',5,'2025-11-24 15:09:25','2025-11-24 15:43:47',0,NULL),(9,'6ade553ca81b30acd59a66ac330827a5cf4be10fcf07b2066f16308ebaf0747b','/api/getAppointments',5,'2025-11-24 15:09:27','2025-11-24 15:43:44',0,NULL),(10,'6ade553ca81b30acd59a66ac330827a5cf4be10fcf07b2066f16308ebaf0747b','/api/getPhotos',4,'2025-11-24 15:09:31','2025-11-24 15:43:49',0,NULL),(11,'6ade553ca81b30acd59a66ac330827a5cf4be10fcf07b2066f16308ebaf0747b','/api/check_availability',1,'2025-11-24 15:42:27','2025-11-24 15:42:27',0,NULL),(12,'6ade553ca81b30acd59a66ac330827a5cf4be10fcf07b2066f16308ebaf0747b','/api/getPackageDetails',1,'2025-11-24 15:43:12','2025-11-24 15:43:12',0,NULL),(13,'4d0655e45e2d02fc8478797e82ca5b5f5ab10a334f57891f03968b9d1e9d0a01','/api/getAppointments',1,'2025-11-24 15:55:44','2025-11-24 15:55:44',0,NULL),(14,'4d0655e45e2d02fc8478797e82ca5b5f5ab10a334f57891f03968b9d1e9d0a01','/api/getPhotos',1,'2025-11-24 15:55:45','2025-11-24 15:55:45',0,NULL),(15,'4d0655e45e2d02fc8478797e82ca5b5f5ab10a334f57891f03968b9d1e9d0a01','/api/get_booked_dates',1,'2025-11-24 15:55:46','2025-11-24 15:55:46',0,NULL),(16,'6ade553ca81b30acd59a66ac330827a5cf4be10fcf07b2066f16308ebaf0747b','/api/get_booked_dates',3,'2025-11-25 05:33:42','2025-11-25 05:40:06',0,NULL),(17,'6ade553ca81b30acd59a66ac330827a5cf4be10fcf07b2066f16308ebaf0747b','/api/check_availability',3,'2025-11-25 05:33:55','2025-11-25 05:40:44',0,NULL),(18,'6ade553ca81b30acd59a66ac330827a5cf4be10fcf07b2066f16308ebaf0747b','/api/getPackageDetails',5,'2025-11-25 05:34:26','2025-11-25 05:42:42',0,NULL),(19,'6ade553ca81b30acd59a66ac330827a5cf4be10fcf07b2066f16308ebaf0747b','/api/getPhotos',1,'2025-11-25 05:39:32','2025-11-25 05:39:32',0,NULL),(20,'6ade553ca81b30acd59a66ac330827a5cf4be10fcf07b2066f16308ebaf0747b','/api/getAppointments',8,'2025-11-25 05:44:09','2025-11-25 05:45:31',0,NULL),(21,'6ade553ca81b30acd59a66ac330827a5cf4be10fcf07b2066f16308ebaf0747b','/api/get_booked_dates',8,'2025-11-25 13:05:07','2025-11-25 13:52:49',0,NULL),(22,'6ade553ca81b30acd59a66ac330827a5cf4be10fcf07b2066f16308ebaf0747b','/api/getPackageDetails',4,'2025-11-25 13:05:18','2025-11-25 13:19:05',0,NULL),(23,'6ade553ca81b30acd59a66ac330827a5cf4be10fcf07b2066f16308ebaf0747b','/api/check_availability',6,'2025-11-25 13:11:37','2025-11-25 14:11:02',0,NULL),(24,'0869e5154d4b2fca35910f6178c3634ab1bbcc19e1b6f0091a5a8063cadbf834','/api/get_booked_dates',5,'2025-11-25 13:12:33','2025-11-25 13:18:27',0,NULL),(25,'6ade553ca81b30acd59a66ac330827a5cf4be10fcf07b2066f16308ebaf0747b','/api/get_booked_dates',2,'2025-11-25 14:10:55','2025-11-25 14:31:06',0,NULL),(26,'6ade553ca81b30acd59a66ac330827a5cf4be10fcf07b2066f16308ebaf0747b','/api/getPackageDetails',1,'2025-11-25 14:11:30','2025-11-25 14:11:30',0,NULL),(27,'6ade553ca81b30acd59a66ac330827a5cf4be10fcf07b2066f16308ebaf0747b','/api/getPhotos',1,'2025-11-25 14:31:05','2025-11-25 14:31:05',0,NULL),(28,'6ade553ca81b30acd59a66ac330827a5cf4be10fcf07b2066f16308ebaf0747b','/api/get_booked_dates',6,'2025-11-25 15:34:29','2025-11-25 15:54:46',0,NULL),(29,'6ade553ca81b30acd59a66ac330827a5cf4be10fcf07b2066f16308ebaf0747b','/api/check_availability',8,'2025-11-25 15:34:39','2025-11-25 15:54:52',0,NULL),(30,'6ade553ca81b30acd59a66ac330827a5cf4be10fcf07b2066f16308ebaf0747b','/api/getPackageDetails',7,'2025-11-25 15:35:03','2025-11-25 15:55:05',0,NULL),(31,'0869e5154d4b2fca35910f6178c3634ab1bbcc19e1b6f0091a5a8063cadbf834','/api/get_booked_dates',1,'2025-11-25 15:46:45','2025-11-25 15:46:45',0,NULL),(32,'6ade553ca81b30acd59a66ac330827a5cf4be10fcf07b2066f16308ebaf0747b','/api/get_booked_dates',3,'2025-11-25 16:50:18','2025-11-25 17:04:54',0,NULL),(33,'6ade553ca81b30acd59a66ac330827a5cf4be10fcf07b2066f16308ebaf0747b','/api/getPhotos',1,'2025-11-25 17:05:35','2025-11-25 17:05:35',0,NULL),(34,'6ade553ca81b30acd59a66ac330827a5cf4be10fcf07b2066f16308ebaf0747b','/api/get_booked_dates',1,'2025-11-25 17:56:18','2025-11-25 17:56:18',0,NULL);
/*!40000 ALTER TABLE `api_rate_limit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `booking_logs`
--

DROP TABLE IF EXISTS `booking_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `booking_logs` (
  `logID` int(11) NOT NULL AUTO_INCREMENT,
  `bookingID` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`logID`),
  KEY `userID` (`userID`),
  KEY `idx_booking_logs_action` (`action`),
  KEY `idx_booking_logs_booking` (`bookingID`,`created_at`),
  CONSTRAINT `booking_logs_ibfk_1` FOREIGN KEY (`bookingID`) REFERENCES `bookings` (`bookingID`) ON DELETE CASCADE,
  CONSTRAINT `booking_logs_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_logs`
--

LOCK TABLES `booking_logs` WRITE;
/*!40000 ALTER TABLE `booking_logs` DISABLE KEYS */;
INSERT INTO `booking_logs` VALUES (1,20,1034,'status_change','Changed status from pending to confirmed','2025-11-25 16:48:13'),(2,16,1034,'note_updated','Updated admin notes','2025-11-25 16:48:52'),(3,16,1034,'status_change','Changed status from pending to confirmed','2025-11-25 16:48:58'),(4,9,1034,'status_change','Changed status from pending to confirmed','2025-11-25 16:49:39'),(5,12,1034,'note_updated','Updated admin notes','2025-11-25 16:54:23'),(6,12,1034,'status_change','Changed status from pending to confirmed','2025-11-25 16:54:28'),(7,19,1034,'status_change','Changed status from pending to confirmed','2025-11-25 17:02:17'),(8,11,1043,'status_change','Changed status from confirmed to cancelled','2025-11-25 17:12:32'),(9,13,1034,'status_change','Changed status from pending to confirmed','2025-11-25 17:13:14');
/*!40000 ALTER TABLE `booking_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `booking_logs_archive`
--

DROP TABLE IF EXISTS `booking_logs_archive`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `booking_logs_archive` (
  `logID` int(11) NOT NULL,
  `bookingID` int(11) NOT NULL,
  `Action` varchar(100) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `UserID` int(11) DEFAULT NULL,
  `CreatedAt` datetime DEFAULT NULL,
  `archivedAt` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`logID`),
  KEY `idx_archive_log_booking` (`bookingID`),
  KEY `idx_archive_log_date` (`CreatedAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_logs_archive`
--

LOCK TABLES `booking_logs_archive` WRITE;
/*!40000 ALTER TABLE `booking_logs_archive` DISABLE KEYS */;
/*!40000 ALTER TABLE `booking_logs_archive` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bookings` (
  `bookingID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `packageID` varchar(100) NOT NULL,
  `event_type` varchar(100) NOT NULL,
  `eventType_other` varchar(255) DEFAULT NULL,
  `event_date` date NOT NULL,
  `event_time_start` time NOT NULL,
  `event_time_end` time NOT NULL,
  `event_location` varchar(255) NOT NULL,
  `event_theme` varchar(100) DEFAULT NULL,
  `client_message` text DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `downpayment_amount` decimal(10,2) NOT NULL,
  `proof_payment` varchar(255) NOT NULL,
  `gdrive_link` varchar(255) DEFAULT NULL,
  `booking_status` enum('pending','confirmed','post_production','completed','cancelled') NOT NULL DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `is_fully_paid` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `consultation_date` date DEFAULT NULL,
  `consultation_time` time DEFAULT NULL,
  `gallery_token` varchar(64) DEFAULT NULL,
  `gallery_pin` varchar(6) DEFAULT NULL,
  `gallery_expiry` datetime DEFAULT NULL,
  `client_signature` longtext DEFAULT NULL,
  `contract_signed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`bookingID`),
  UNIQUE KEY `gallery_token` (`gallery_token`),
  KEY `idx_event_date` (`event_date`),
  KEY `idx_status` (`booking_status`),
  KEY `idx_bookings_status_date` (`booking_status`,`event_date`),
  KEY `idx_bookings_created` (`created_at`),
  KEY `idx_bookings_event_date` (`event_date`),
  KEY `idx_bookings_user_status` (`userID`,`booking_status`),
  KEY `idx_bookings_package` (`packageID`,`booking_status`),
  KEY `idx_gallery_token` (`gallery_token`),
  KEY `idx_user_booking` (`userID`,`booking_status`),
  CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`),
  CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`packageID`) REFERENCES `packages` (`packageID`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
INSERT INTO `bookings` VALUES (7,1043,'basic','Other',NULL,'2025-11-28','15:10:00','22:00:00','Kld (Near: arena)',NULL,'wadawdasda',11500.00,2875.00,'','../uploads/payment_proofs/1763878271_screencapture-localhost-aperture-src-user-bookingForm-php-2025-11-23-01_33_42.png','pending',NULL,0,'2025-11-23 06:11:11','2025-11-23 06:16:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(8,1043,'premium','Wedding',NULL,'2025-11-29','15:23:00','17:23:00','sdasdasdasdasdsa (Near: sdasdasdas)',NULL,'',31000.00,7750.00,'../uploads/payment_proofs/1763879237_screencapture-localhost-aperture-src-user-bookingForm-php-2025-11-23-01_33_42.png',NULL,'pending',NULL,0,'2025-11-23 06:27:17','2025-11-23 06:27:17',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,1043,'basic','Wedding',NULL,'2025-11-30','15:27:00','18:27:00','sasadasdasdasd (Near: sdasdasdsadsad)',NULL,'',9300.00,2325.00,'../uploads/payment_proofs/1763879308_screencapture-localhost-aperture-src-user-bookingForm-php-2025-11-23-01_33_42.png',NULL,'confirmed',NULL,0,'2025-11-23 06:28:28','2025-11-25 16:49:39',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(10,1043,'premium','Other',NULL,'2025-12-01','19:32:00','21:32:00','sdasdasdasdas (Near: dsdasdasdasda)',NULL,'',29000.00,7250.00,'../uploads/payment_proofs/1763879566_screencapture-localhost-aperture-src-user-bookingForm-php-2025-11-23-01_33_42.png',NULL,'pending',NULL,0,'2025-11-23 06:32:46','2025-11-23 06:32:46',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(11,1043,'standard','Wedding',NULL,'2025-12-16','17:20:00','20:20:00','KLD GROUNDS (Near: GRANDSTAND)',NULL,'HELLOWORLD',20500.00,5125.00,'../uploads/payment_proofs/1763961718_casiano.png',NULL,'cancelled',NULL,0,'2025-11-24 05:21:58','2025-11-25 17:12:32',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(12,1042,'premium','Gala Dinner / Awards Night',NULL,'2026-06-29','10:24:00','22:00:00','KLD (Near: kld)',NULL,'WALA',40500.00,10125.00,'../uploads/payment_proofs/1763972747_ulit.png',NULL,'confirmed','BASTA',0,'2025-11-24 08:25:47','2025-11-25 16:54:28',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(13,1043,'standard','Gala Dinner / Awards Night',NULL,'2025-12-02','09:21:00','21:21:00','helloWorld (Near: hjdahdkasdkjasndasd)',NULL,'wala po',23000.00,5750.00,'../uploads/payment_proofs/upload_69245cea9ff6b403910029_1763990762.png',NULL,'confirmed',NULL,0,'2025-11-24 13:26:02','2025-11-25 17:13:14',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(14,1047,'premium','Pet Photography',NULL,'2025-12-06','08:15:00','13:15:00','sdadadasdasd (Near: sdasdsadasdasd)',NULL,'sdadasdasd',32500.00,8125.00,'../uploads/payment_proofs/upload_692468ae6fc02652047179_1763993774.png',NULL,'pending',NULL,0,'2025-11-24 14:16:14','2025-11-24 14:16:14',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(15,1048,'basic','Music Concert / Live Performance',NULL,'2026-04-01','08:42:00','21:42:00','Solar System (Near: near earth)',NULL,'walawa awawwdadw',14000.00,3500.00,'../uploads/payment_proofs/upload_69247d2a59e61024621923_1763999018.png',NULL,'pending',NULL,0,'2025-11-24 15:43:38','2025-11-24 15:43:38',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(16,1049,'premium','Debut / 18th Birthday',NULL,'2026-10-12','09:41:00','18:41:00','Solar System (Near: Earth)',NULL,'',34500.00,8625.00,'../uploads/payment_proofs/upload_6925421f661a3593841916_1764049439.png',NULL,'confirmed','helloWorld',0,'2025-11-25 05:43:59','2025-11-25 16:48:58',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(17,1043,'premium','Engagement / Pre-Nup Session',NULL,'2026-01-02','12:11:00','21:11:00','sdadasd (Near: arena)',NULL,'',27000.00,6750.00,'../uploads/payment_proofs/upload_6925ab15b2384951550765_1764076309.png',NULL,'pending',NULL,0,'2025-11-25 13:11:49','2025-11-25 13:11:49',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(18,1043,'basic','Conference / Seminar Coverage',NULL,'2026-01-08','12:30:00','14:30:00','Solar System (Near: Earth)',NULL,'',8500.00,8500.00,'../uploads/payment_proofs/upload_6925b929cd7a4940891513_1764079913.png',NULL,'pending',NULL,1,'2025-11-25 14:11:53','2025-11-25 14:11:53',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(19,1043,'basic','Wedding Ceremony &amp; Reception',NULL,'2028-05-24','08:30:00','11:30:00','helloWorld (Near: helloWorld)',NULL,'sdasdasdasd',8800.00,2200.00,'../uploads/payment_proofs/upload_6925d02b493a0615604019_1764085803.pdf',NULL,'confirmed',NULL,0,'2025-11-25 15:50:03','2025-11-25 17:02:17',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(20,1034,'premium','Product Photography',NULL,'2027-03-25','14:30:00','18:00:00','helloWorld (Near: helloWorld)',NULL,'sdasdasd',29000.00,7250.00,'../uploads/payment_proofs/upload_6925d16de2344943652690_1764086125.pdf',NULL,'confirmed',NULL,0,'2025-11-25 15:55:25','2025-11-25 16:48:13',NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bookings_archive`
--

DROP TABLE IF EXISTS `bookings_archive`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bookings_archive` (
  `bookingID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `packageID` int(11) DEFAULT NULL,
  `EventType` varchar(100) DEFAULT NULL,
  `EventDate` date DEFAULT NULL,
  `EventTime` time DEFAULT NULL,
  `EventLocation` text DEFAULT NULL,
  `NumGuests` int(11) DEFAULT NULL,
  `SpecialRequests` text DEFAULT NULL,
  `TotalAmount` decimal(10,2) DEFAULT NULL,
  `BookingStatus` varchar(50) DEFAULT NULL,
  `BookingReference` varchar(50) DEFAULT NULL,
  `PaymentStatus` varchar(50) DEFAULT NULL,
  `CreatedAt` datetime DEFAULT NULL,
  `UpdatedAt` datetime DEFAULT NULL,
  `AdminNotes` text DEFAULT NULL,
  `archivedAt` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`bookingID`),
  UNIQUE KEY `BookingReference` (`BookingReference`),
  KEY `idx_archive_user` (`userID`),
  KEY `idx_archive_date` (`EventDate`),
  KEY `idx_archive_status` (`BookingStatus`),
  KEY `idx_archived_at` (`archivedAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings_archive`
--

LOCK TABLES `bookings_archive` WRITE;
/*!40000 ALTER TABLE `bookings_archive` DISABLE KEYS */;
/*!40000 ALTER TABLE `bookings_archive` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `client_notes`
--

DROP TABLE IF EXISTS `client_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client_notes` (
  `noteID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `adminID` int(11) NOT NULL,
  `note` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`noteID`),
  KEY `adminID` (`adminID`),
  KEY `idx_client_notes_user` (`userID`,`created_at`),
  CONSTRAINT `client_notes_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE,
  CONSTRAINT `client_notes_ibfk_2` FOREIGN KEY (`adminID`) REFERENCES `users` (`userID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client_notes`
--

LOCK TABLES `client_notes` WRITE;
/*!40000 ALTER TABLE `client_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `client_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `client_tags`
--

DROP TABLE IF EXISTS `client_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client_tags` (
  `tagID` int(11) NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(50) NOT NULL,
  `tag_color` varchar(7) DEFAULT '#d4af37',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`tagID`),
  UNIQUE KEY `tag_name` (`tag_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client_tags`
--

LOCK TABLES `client_tags` WRITE;
/*!40000 ALTER TABLE `client_tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `client_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `communication_log`
--

DROP TABLE IF EXISTS `communication_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `communication_log` (
  `logID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `adminID` int(11) NOT NULL,
  `type` enum('email','call','meeting','message') NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `communication_date` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`logID`),
  KEY `adminID` (`adminID`),
  KEY `idx_comm_log_type` (`type`),
  KEY `idx_comm_log_user` (`userID`,`communication_date`),
  CONSTRAINT `communication_log_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE,
  CONSTRAINT `communication_log_ibfk_2` FOREIGN KEY (`adminID`) REFERENCES `users` (`userID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `communication_log`
--

LOCK TABLES `communication_log` WRITE;
/*!40000 ALTER TABLE `communication_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `communication_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `communication_logs`
--

DROP TABLE IF EXISTS `communication_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `communication_logs` (
  `logID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `adminID` int(11) NOT NULL,
  `type` enum('email','call','meeting','message') NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `communication_date` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`logID`),
  KEY `userID` (`userID`),
  KEY `adminID` (`adminID`),
  CONSTRAINT `communication_logs_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE,
  CONSTRAINT `communication_logs_ibfk_2` FOREIGN KEY (`adminID`) REFERENCES `users` (`userID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `communication_logs`
--

LOCK TABLES `communication_logs` WRITE;
/*!40000 ALTER TABLE `communication_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `communication_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contactmessages`
--

DROP TABLE IF EXISTS `contactmessages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contactmessages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_read` (`is_read`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contactmessages`
--

LOCK TABLES `contactmessages` WRITE;
/*!40000 ALTER TABLE `contactmessages` DISABLE KEYS */;
INSERT INTO `contactmessages` VALUES (1,'','',NULL,'',NULL,0,'2025-11-23 16:17:34'),(2,'','','New Contact Message','',NULL,0,'2025-11-23 16:19:35'),(3,'','','New Contact Message','',NULL,0,'2025-11-23 16:19:40'),(4,'','','New Contact Message','',NULL,0,'2025-11-23 16:21:33'),(5,'','','New Contact Message','',NULL,0,'2025-11-24 11:39:00');
/*!40000 ALTER TABLE `contactmessages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inclusion`
--

DROP TABLE IF EXISTS `inclusion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inclusion` (
  `inclusionID` int(255) NOT NULL AUTO_INCREMENT,
  `packageID` varchar(255) NOT NULL,
  `Description` varchar(255) NOT NULL,
  PRIMARY KEY (`inclusionID`),
  KEY `packageID` (`packageID`),
  CONSTRAINT `inclusion_ibfk_1` FOREIGN KEY (`packageID`) REFERENCES `packages` (`packageID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inclusion`
--

LOCK TABLES `inclusion` WRITE;
/*!40000 ALTER TABLE `inclusion` DISABLE KEYS */;
INSERT INTO `inclusion` VALUES (1,'basic','1 Professional Videographer'),(2,'basic','2 Hours Event Coverage'),(3,'basic','Pre-event Consultation'),(4,'basic','40+ High-Quality Edited Photos (selected from video frames)'),(5,'basic','1-2 Minute Highlight Video'),(6,'basic','Full Event Video Coverage'),(7,'basic','Basic Color Correction & Audio Enhancement'),(8,'basic','Private Digital Gallery (4 Months Access)'),(9,'basic','Secure Cloud Storage Backup (4 Months)'),(10,'basic','Direct Digital Download of All Files'),(11,'basic','Mobile-Optimized Viewing'),(12,'standard','1 Professional Photographer'),(13,'standard','1 Professional Videographer'),(14,'standard','4 Hours Event Coverage'),(15,'standard','Detailed Pre-event Planning'),(16,'standard','100+ Professionally Edited Photos'),(17,'standard','3-5 Minute Cinematic Highlight Film'),(18,'standard','15-20 Minute Full Event Video'),(19,'standard','Drone Aerial Coverage Included'),(20,'standard','Professional Color Grading & Audio Mixing'),(21,'standard','Premium Digital Gallery (6 Months Access)'),(22,'standard','Secure Cloud Storage Backup (6 Months)'),(23,'standard','High-Resolution Digital Download'),(24,'standard','Social Media Optimized Clips'),(25,'premium','2 Professional Photographers'),(26,'premium','2 Professional Videographers'),(27,'premium','8 Hours Comprehensive Coverage'),(28,'premium','Dedicated Event Director'),(29,'premium','Extensive Pre-event Planning'),(30,'premium','250+ Expertly Edited Photos'),(31,'premium','5-7 Minute Cinematic Highlight Film'),(32,'premium','25-30 Minute Full Event Documentary'),(33,'premium','Drone Aerial Coverage Included'),(34,'premium','Same-Day Edit Service Included'),(35,'premium','Advanced Color Grading & Professional Audio Mixing'),(36,'premium','30-Second Save-the-Date Teaser'),(37,'premium','Exclusive Digital Gallery (1 Year Access)'),(38,'premium','Secure Cloud Storage Backup (1 Year)'),(39,'premium','Priority Digital Download'),(40,'premium','Custom Online Presentation'),(41,'premium','Social Media Content Package');
/*!40000 ALTER TABLE `inclusion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inquiries`
--

DROP TABLE IF EXISTS `inquiries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inquiries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','replied') NOT NULL DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inquiries`
--

LOCK TABLES `inquiries` WRITE;
/*!40000 ALTER TABLE `inquiries` DISABLE KEYS */;
/*!40000 ALTER TABLE `inquiries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoices` (
  `invoiceID` int(11) NOT NULL AUTO_INCREMENT,
  `bookingID` int(11) NOT NULL,
  `issue_date` date NOT NULL,
  `due_date` date NOT NULL,
  `status` enum('pending','paid','overdue','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`invoiceID`),
  KEY `bookingID` (`bookingID`),
  CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`bookingID`) REFERENCES `bookings` (`bookingID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `notificationID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`notificationID`),
  KEY `userID` (`userID`),
  KEY `is_read` (`is_read`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `packages`
--

DROP TABLE IF EXISTS `packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `packages` (
  `packageID` varchar(100) NOT NULL,
  `packageName` varchar(100) NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `coverage_hours` int(11) DEFAULT 0,
  `extra_hour_rate` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`packageID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `packages`
--

LOCK TABLES `packages` WRITE;
/*!40000 ALTER TABLE `packages` DISABLE KEYS */;
INSERT INTO `packages` VALUES ('basic','Basic Package',7500.00,'Perfect for: Birthday parties, baptisms, small family gatherings (50 guests or less)',2,500.00),('premium','Premium Package',25000.00,'Perfect for: Weddings, large corporate events, premium productions requiring cinematic storytelling',8,2000.00),('standard','Standard Package',15000.00,'Perfect for: Weddings, debuts, corporate events, large celebrations',4,1000.00);
/*!40000 ALTER TABLE `packages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_history`
--

DROP TABLE IF EXISTS `password_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user` (`userID`),
  KEY `idx_created` (`created_at`),
  CONSTRAINT `password_history_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores password history to prevent reuse of recent passwords';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_history`
--

LOCK TABLES `password_history` WRITE;
/*!40000 ALTER TABLE `password_history` DISABLE KEYS */;
INSERT INTO `password_history` VALUES (1,1047,'$2y$10$H9Fd2r6E.YQNuqSXUkjlO.H2HN78/DtFATdLdLmyHHi/lTnBkYG46','2025-11-24 12:10:07'),(2,1048,'$2y$10$Iuz/QD3UDZsTYDfarEADqeludopdYkh.7xtD9Sm74gg6XI.1lndgS','2025-11-24 15:38:06'),(3,1049,'$2y$10$DjlsNRXb4dsiDz3ouqshiuCDjcyCLklfSsA4ugzWD3bOWxDUkISKu','2025-11-25 05:29:55');
/*!40000 ALTER TABLE `password_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ratelimiting`
--

DROP TABLE IF EXISTS `ratelimiting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ratelimiting` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `userID` int(100) NOT NULL,
  `logInAttempt` int(100) NOT NULL,
  `loginLocked` timestamp NULL DEFAULT NULL,
  `loginEmailVerificationAttempt` int(100) NOT NULL,
  `loginEmailVerificationLocked` timestamp NULL DEFAULT NULL,
  `registrationEmailVerificationAttempt` int(100) NOT NULL,
  `registrationEmailVerificationLocked` timestamp NULL DEFAULT NULL,
  `fogotEmailVerificationAttempt` int(100) NOT NULL,
  `fogotEmailVerificationLocked` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userID` (`userID`),
  CONSTRAINT `ratelimiting_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ratelimiting`
--

LOCK TABLES `ratelimiting` WRITE;
/*!40000 ALTER TABLE `ratelimiting` DISABLE KEYS */;
INSERT INTO `ratelimiting` VALUES (5,1034,0,NULL,0,NULL,0,NULL,0,NULL),(9,1038,0,NULL,0,NULL,0,NULL,0,NULL),(12,1042,0,NULL,0,NULL,0,NULL,0,NULL),(14,1045,0,NULL,0,NULL,0,NULL,0,NULL),(15,1046,0,NULL,0,NULL,0,NULL,0,NULL),(16,1047,0,NULL,0,NULL,0,NULL,0,NULL),(17,1048,0,NULL,0,NULL,0,NULL,0,NULL),(18,1049,0,NULL,0,NULL,0,NULL,0,NULL);
/*!40000 ALTER TABLE `ratelimiting` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `refunds`
--

DROP TABLE IF EXISTS `refunds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `refunds` (
  `refundID` int(11) NOT NULL AUTO_INCREMENT,
  `bookingID` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','processed','rejected') DEFAULT 'pending',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_at` timestamp NULL DEFAULT NULL,
  `processed_by` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`refundID`),
  KEY `bookingID` (`bookingID`),
  KEY `processed_by` (`processed_by`),
  CONSTRAINT `refunds_ibfk_1` FOREIGN KEY (`bookingID`) REFERENCES `bookings` (`bookingID`) ON DELETE CASCADE,
  CONSTRAINT `refunds_ibfk_2` FOREIGN KEY (`processed_by`) REFERENCES `users` (`userID`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `refunds`
--

LOCK TABLES `refunds` WRITE;
/*!40000 ALTER TABLE `refunds` DISABLE KEYS */;
INSERT INTO `refunds` VALUES (1,11,5125.00,'Booking cancelled by client','pending','2025-11-25 17:12:36',NULL,NULL,NULL);
/*!40000 ALTER TABLE `refunds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reviews` (
  `reviewID` int(11) NOT NULL AUTO_INCREMENT,
  `bookingID` int(11) NOT NULL,
  `rating` int(1) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`reviewID`),
  UNIQUE KEY `bookingID` (`bookingID`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`bookingID`) REFERENCES `bookings` (`bookingID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tags` (
  `tagID` int(11) NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(50) NOT NULL,
  `tag_color` varchar(20) NOT NULL DEFAULT '#D4AF37',
  PRIMARY KEY (`tagID`),
  UNIQUE KEY `tag_name` (`tag_name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tags`
--

LOCK TABLES `tags` WRITE;
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
INSERT INTO `tags` VALUES (1,'VIP','#FFD700'),(2,'New Client','#4CAF50'),(3,'High Value','#9C27B0'),(4,'Corporate','#2196F3'),(5,'Wedding','#E91E63'),(6,'Potential','#FF9800');
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_photos`
--

DROP TABLE IF EXISTS `user_photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_photos` (
  `photoID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `fileName` varchar(255) NOT NULL,
  `originalName` varchar(255) NOT NULL,
  `uploadedBy` int(11) NOT NULL,
  `uploadDate` datetime DEFAULT current_timestamp(),
  `caption` text DEFAULT NULL,
  PRIMARY KEY (`photoID`),
  KEY `idx_userID` (`userID`),
  KEY `idx_uploadDate` (`uploadDate`),
  KEY `uploadedBy` (`uploadedBy`),
  CONSTRAINT `user_photos_ibfk_1` FOREIGN KEY (`uploadedBy`) REFERENCES `users` (`userID`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_photos`
--

LOCK TABLES `user_photos` WRITE;
/*!40000 ALTER TABLE `user_photos` DISABLE KEYS */;
INSERT INTO `user_photos` VALUES (1,1042,'photo_1763713088_692020403799c.png','evaluationProcessssss (1).png',1034,'2025-11-21 08:18:08',''),(2,1042,'photo_1763713088_69202040386a2.png','evaluationProcessssss.png',1034,'2025-11-21 08:18:08',''),(3,1043,'photo_1763713126_6920206615dbb.jpg','510691af-a11b-47e6-b66c-806424773120.jpg',1034,'2025-11-21 08:18:46',''),(4,1043,'photo_1763715458_6920298200e34.jpg','lux.jpg',1034,'2025-11-21 08:57:38',''),(5,1043,'photo_1763957149_6923d99d2b9fa.png','ulit.png',1034,'2025-11-24 04:05:49',''),(6,1043,'photo_1763957194_6923d9cad385a.png','ulit.png',1034,'2025-11-24 04:06:34',''),(7,1043,'photo_1763959470_6923e2ae15995.png','ulit.png',1034,'2025-11-24 04:44:30',''),(8,1042,'photo_1763972574_692415de6e03e.png','logo-for-light.png',1034,'2025-11-24 08:22:54',''),(9,1042,'photo_1763972574_692415de71f44.png','logo-for-dark.png',1034,'2025-11-24 08:22:54',''),(10,1042,'photo_1763972574_692415de735ff.png','photo_1763959470_6923e2ae15995.png',1034,'2025-11-24 08:22:54',''),(11,1042,'photo_1763972574_692415de75c06.png','Picture1.png',1034,'2025-11-24 08:22:54',''),(12,1049,'photo_1764049086_692540bed9ee9.png','casiano.png',1034,'2025-11-25 05:38:06','');
/*!40000 ALTER TABLE `user_photos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_tags`
--

DROP TABLE IF EXISTS `user_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_tags` (
  `userID` int(11) NOT NULL,
  `tagID` int(11) NOT NULL,
  `assigned_by` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`userID`,`tagID`),
  KEY `assigned_by` (`assigned_by`),
  KEY `idx_user_tags_tag` (`tagID`),
  KEY `idx_user_tags_user` (`userID`),
  CONSTRAINT `user_tags_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE,
  CONSTRAINT `user_tags_ibfk_2` FOREIGN KEY (`tagID`) REFERENCES `client_tags` (`tagID`) ON DELETE CASCADE,
  CONSTRAINT `user_tags_ibfk_3` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`userID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_tags`
--

LOCK TABLES `user_tags` WRITE;
/*!40000 ALTER TABLE `user_tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Role` enum('Admin','User') DEFAULT 'User',
  `FirstName` varchar(100) DEFAULT NULL,
  `LastName` varchar(100) DEFAULT NULL,
  `FullName` varchar(200) DEFAULT NULL,
  `contactNo` varchar(20) DEFAULT NULL,
  `isVerified` tinyint(1) DEFAULT 0,
  `profileCompleted` tinyint(1) DEFAULT 0,
  `verificationCode` varchar(255) DEFAULT NULL,
  `codeCreated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `codeExpires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `passwordResetCode` varchar(255) DEFAULT NULL COMMENT 'Hashed 6-digit reset code',
  `resetCodeCreated_at` timestamp NULL DEFAULT NULL,
  `resetCodeExpires_at` timestamp NULL DEFAULT NULL,
  `Status` enum('Active','Archived') NOT NULL DEFAULT 'Active',
  PRIMARY KEY (`userID`),
  UNIQUE KEY `Email` (`Email`),
  KEY `idx_email` (`Email`),
  KEY `idx_verification` (`verificationCode`),
  KEY `idx_verified` (`isVerified`),
  KEY `idx_reset_code` (`passwordResetCode`),
  KEY `idx_users_role_status` (`Role`,`Status`),
  KEY `idx_users_created` (`created_at`),
  KEY `idx_users_email` (`Email`)
) ENGINE=InnoDB AUTO_INCREMENT=1050 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1034,'pawcasiano@kld.edu.ph','$2y$10$7pq4gijtMOwQqrmpgJMeReLbK4j2omNKmeH3NGAYMZEThrlEZCX3m','Admin','Prince Andrew','Casiano','Prince Andrew Casiano','09977676554',1,1,NULL,'2025-11-25 16:10:42',NULL,'2025-11-19 07:31:04','2025-11-25 16:10:42','852625','2025-11-19 10:12:07','2025-11-19 10:17:07','Active'),(1038,'buban55aljhon@gmail.com','$2y$10$KCqZw4SZ56Zl9m2vGZaQNec3lueqAvY92N6bGG6g/r1T3N3u9x35O','User','aljhon','buban','aljhon buban','09926142519',1,1,NULL,'2025-11-19 15:06:18',NULL,'2025-11-19 10:23:42','2025-11-19 15:06:18',NULL,NULL,NULL,'Active'),(1042,'mcborja@kld.edu.ph','$2y$10$ASDaPugBg2eOI7LPhqsdeeSZnfR6PAz6uv.bwkfE4l/AqeEaDS46C','User','Marc Christopher','Borja','Marc Christopher Borja','09977676554',1,1,NULL,'2025-11-24 05:11:52',NULL,'2025-11-19 11:03:47','2025-11-24 05:11:52',NULL,NULL,NULL,'Active'),(1043,'casianoprince5@gmail.com','$2y$10$6EitgGc4d7I5QNBCCZuNmezD0ynrmr/WHQv8XekP7.OyEHij6AFxC','User','Prince Andrew','Casiano','Prince Andrew Casiano','09926142519',1,1,NULL,'2025-11-25 13:04:48',NULL,'2025-11-19 14:02:03','2025-11-25 13:04:48','852871','2025-11-24 15:46:25','2025-11-24 15:51:25','Active'),(1045,'rhenskief@gmail.com','$2y$10$UAikoXBSMUgNc6eEbPDuIOyMHeH0S1KEWNtLAYcqNVnXrg.hRqGv2','User','Rhenskie','Bading','Rhenskie Bading','09815984281',1,1,NULL,'2025-11-20 15:23:35',NULL,'2025-11-20 15:21:54','2025-11-20 15:23:35',NULL,NULL,NULL,'Active'),(1046,'test_booking_123@example.com','$2y$10$/geZCbjSl9dkKWEMPwGncOlqLF7NxZAo35X3ZhuiPvnBwprK8S.G6','User',NULL,NULL,NULL,NULL,0,0,'432263','2025-11-25 11:19:21','2025-11-23 15:03:58','2025-11-23 14:56:22','2025-11-25 11:19:21',NULL,NULL,NULL,''),(1047,'aperture.eventbookings@gmail.com','$2y$10$H9Fd2r6E.YQNuqSXUkjlO.H2HN78/DtFATdLdLmyHHi/lTnBkYG46','User','Admin','Account','Admin Account','09977676554',1,1,NULL,'2025-11-24 12:11:26',NULL,'2025-11-24 12:10:07','2025-11-24 12:11:26',NULL,NULL,NULL,'Active'),(1048,'princeleonardokazuto@gmail.com','$2y$10$EOph6cbZnShinVgmHrJo9.fdJpg34aKPTBv8HiPIF0qkG5/GrmySa','User','Prince Leonardo','Casiano','Prince Leonardo Casiano','09977676554',1,1,NULL,'2025-11-24 15:40:32',NULL,'2025-11-24 15:38:06','2025-11-24 15:40:32','631610','2025-11-24 15:39:46','2025-11-24 15:44:46','Active'),(1049,'tubiljoven@gmail.com','$2y$10$DjlsNRXb4dsiDz3ouqshiuCDjcyCLklfSsA4ugzWD3bOWxDUkISKu','User','Joven','Tubil','Joven Tubil','09872518323',1,1,NULL,'2025-11-25 05:33:35',NULL,'2025-11-25 05:29:55','2025-11-25 05:33:35',NULL,NULL,NULL,'Active');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-26  2:02:25
