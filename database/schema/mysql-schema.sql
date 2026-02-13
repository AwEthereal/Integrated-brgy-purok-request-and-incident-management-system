/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `announcements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `category` varchar(255) NOT NULL DEFAULT 'general',
  `priority` varchar(255) NOT NULL DEFAULT 'normal',
  `created_by` bigint(20) unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `announcements_created_by_foreign` (`created_by`),
  CONSTRAINT `announcements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `feedback` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `incident_report_id` bigint(20) unsigned DEFAULT NULL,
  `request_id` bigint(20) unsigned DEFAULT NULL,
  `sqd0_rating` tinyint(4) NOT NULL COMMENT 'I am satisfied with the service that I availed.',
  `sqd1_rating` tinyint(4) NOT NULL COMMENT 'I spent an acceptable amount of time for my transaction.',
  `sqd2_rating` tinyint(4) NOT NULL COMMENT 'The office accurately informed me and followed the transaction''s requirements and steps.',
  `sqd3_rating` tinyint(4) NOT NULL COMMENT 'My online transaction (including steps and payment) was simple and convenient.',
  `sqd4_rating` tinyint(4) NOT NULL COMMENT 'I easily found information about my transaction from the office or its website.',
  `sqd5_rating` tinyint(4) NOT NULL COMMENT 'I paid an acceptable amount of fees for my transaction.',
  `sqd6_rating` tinyint(4) NOT NULL COMMENT 'I am confident that my online transaction was secure.',
  `sqd7_rating` tinyint(4) NOT NULL COMMENT 'The office''s online support was available, or (if asked questions) was quick to respond.',
  `sqd8_rating` tinyint(4) NOT NULL COMMENT 'I got what I needed from the government office.',
  `comments` text DEFAULT NULL,
  `is_anonymous` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `feedback_incident_report_id_foreign` (`incident_report_id`),
  KEY `feedback_request_id_foreign` (`request_id`),
  KEY `feedback_user_id_created_at_index` (`user_id`,`created_at`),
  CONSTRAINT `feedback_incident_report_id_foreign` FOREIGN KEY (`incident_report_id`) REFERENCES `incident_reports` (`id`) ON DELETE SET NULL,
  CONSTRAINT `feedback_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`) ON DELETE SET NULL,
  CONSTRAINT `feedback_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `incident_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `incident_reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `reporter_name` varchar(255) DEFAULT NULL,
  `contact_number` varchar(32) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `incident_type` enum('crime','accident','natural_disaster','medical_emergency','fire','public_disturbance','traffic_incident','missing_person','environmental_hazard','other') NOT NULL DEFAULT 'other',
  `incident_type_other` varchar(100) DEFAULT NULL,
  `purok_id` bigint(20) unsigned DEFAULT NULL,
  `description` text NOT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `photo_paths` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`photo_paths`)),
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `viewed_at` timestamp NULL DEFAULT NULL,
  `staff_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `feedback_requested_at` timestamp NULL DEFAULT NULL,
  `feedback_submitted_at` timestamp NULL DEFAULT NULL,
  `feedback_dismissed_at` timestamp NULL DEFAULT NULL,
  `feedback_skipped` tinyint(1) NOT NULL DEFAULT 0,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_by` bigint(20) unsigned DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `incident_reports_incident_type_index` (`incident_type`),
  KEY `incident_reports_approved_by_foreign` (`approved_by`),
  KEY `incident_reports_rejected_by_foreign` (`rejected_by`),
  KEY `incident_reports_user_id_index` (`user_id`),
  KEY `incident_reports_status_index` (`status`),
  KEY `incident_reports_purok_id_index` (`purok_id`),
  KEY `incident_reports_user_id_status_index` (`user_id`,`status`),
  KEY `incident_reports_created_at_index` (`created_at`),
  CONSTRAINT `incident_reports_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `incident_reports_purok_id_foreign` FOREIGN KEY (`purok_id`) REFERENCES `puroks` (`id`) ON DELETE SET NULL,
  CONSTRAINT `incident_reports_rejected_by_foreign` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `incident_reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_reset_tokens_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `purok_change_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purok_change_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `current_purok_id` bigint(20) unsigned NOT NULL,
  `requested_purok_id` bigint(20) unsigned NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `processed_at` timestamp NULL DEFAULT NULL,
  `processed_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purok_change_requests_current_purok_id_foreign` (`current_purok_id`),
  KEY `purok_change_requests_requested_purok_id_foreign` (`requested_purok_id`),
  KEY `purok_change_requests_processed_by_foreign` (`processed_by`),
  KEY `purok_change_requests_user_id_index` (`user_id`),
  KEY `purok_change_requests_status_index` (`status`),
  CONSTRAINT `purok_change_requests_current_purok_id_foreign` FOREIGN KEY (`current_purok_id`) REFERENCES `puroks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purok_change_requests_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purok_change_requests_requested_purok_id_foreign` FOREIGN KEY (`requested_purok_id`) REFERENCES `puroks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purok_change_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `puroks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `puroks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `puroks_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `contact_number` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `requester_name` varchar(255) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `civil_status` varchar(255) DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `form_type` varchar(255) NOT NULL,
  `valid_id_front_path` varchar(255) DEFAULT NULL,
  `valid_id_back_path` varchar(255) DEFAULT NULL,
  `valid_id_path` varchar(255) DEFAULT NULL,
  `purpose` varchar(255) NOT NULL,
  `status` enum('pending','purok_approved','barangay_approved','completed','rejected') NOT NULL DEFAULT 'pending',
  `incident_type` enum('complaint','assistance','document_request','incident_report','other') NOT NULL DEFAULT 'other',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_viewed_at` timestamp NULL DEFAULT NULL,
  `barangay_rejected_at` timestamp NULL DEFAULT NULL,
  `barangay_rejected_by` bigint(20) unsigned DEFAULT NULL,
  `purok_id` bigint(20) unsigned DEFAULT NULL,
  `purok_leader_id` bigint(20) unsigned DEFAULT NULL,
  `purok_approved_at` timestamp NULL DEFAULT NULL,
  `purok_approved_by` bigint(20) unsigned DEFAULT NULL,
  `purok_notes` text DEFAULT NULL,
  `purok_private_notes` text DEFAULT NULL,
  `barangay_approved_at` timestamp NULL DEFAULT NULL,
  `barangay_approved_by` bigint(20) unsigned DEFAULT NULL,
  `barangay_notes` text DEFAULT NULL,
  `barangay_rejection_reason` text DEFAULT NULL,
  `document_path` varchar(255) DEFAULT NULL,
  `document_generated_at` timestamp NULL DEFAULT NULL,
  `feedback_requested_at` timestamp NULL DEFAULT NULL,
  `feedback_provided_at` timestamp NULL DEFAULT NULL,
  `feedback_dismissed_at` timestamp NULL DEFAULT NULL,
  `feedback_skipped` tinyint(1) NOT NULL DEFAULT 0,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `rejected_by` bigint(20) unsigned DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `requests_barangay_rejected_by_foreign` (`barangay_rejected_by`),
  KEY `requests_purok_approved_by_foreign` (`purok_approved_by`),
  KEY `requests_barangay_approved_by_foreign` (`barangay_approved_by`),
  KEY `requests_rejected_by_foreign` (`rejected_by`),
  KEY `requests_purok_leader_id_foreign` (`purok_leader_id`),
  KEY `requests_user_id_index` (`user_id`),
  KEY `requests_status_index` (`status`),
  KEY `requests_purok_id_index` (`purok_id`),
  KEY `requests_user_id_status_index` (`user_id`,`status`),
  KEY `requests_created_at_index` (`created_at`),
  CONSTRAINT `requests_barangay_approved_by_foreign` FOREIGN KEY (`barangay_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `requests_barangay_rejected_by_foreign` FOREIGN KEY (`barangay_rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `requests_purok_approved_by_foreign` FOREIGN KEY (`purok_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `requests_purok_id_foreign` FOREIGN KEY (`purok_id`) REFERENCES `puroks` (`id`) ON DELETE SET NULL,
  CONSTRAINT `requests_purok_leader_id_foreign` FOREIGN KEY (`purok_leader_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `requests_rejected_by_foreign` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `resident_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `resident_records` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `purok_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `philsys_card_no` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `suffix` varchar(255) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `birth_place` varchar(255) DEFAULT NULL,
  `sex` varchar(16) DEFAULT NULL,
  `civil_status` varchar(32) DEFAULT NULL,
  `religion` varchar(255) DEFAULT NULL,
  `citizenship` varchar(255) DEFAULT NULL,
  `residence_address` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `province` varchar(255) DEFAULT NULL,
  `city_municipality` varchar(255) DEFAULT NULL,
  `barangay` varchar(255) DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `contact_number` varchar(64) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `highest_educ_attainment` varchar(32) DEFAULT NULL,
  `educ_specify` varchar(255) DEFAULT NULL,
  `is_graduate` tinyint(1) NOT NULL DEFAULT 0,
  `is_undergraduate` tinyint(1) NOT NULL DEFAULT 0,
  `date_accomplished` date DEFAULT NULL,
  `left_thumbmark_path` varchar(255) DEFAULT NULL,
  `right_thumbmark_path` varchar(255) DEFAULT NULL,
  `signature_path` varchar(255) DEFAULT NULL,
  `household_number` varchar(255) DEFAULT NULL,
  `attested_by_user_id` bigint(20) unsigned DEFAULT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(24) NOT NULL DEFAULT 'active',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `resident_records_philsys_card_no_unique` (`philsys_card_no`),
  KEY `resident_records_purok_id_foreign` (`purok_id`),
  KEY `resident_records_user_id_foreign` (`user_id`),
  KEY `resident_records_attested_by_user_id_foreign` (`attested_by_user_id`),
  CONSTRAINT `resident_records_attested_by_user_id_foreign` FOREIGN KEY (`attested_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `resident_records_purok_id_foreign` FOREIGN KEY (`purok_id`) REFERENCES `puroks` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `resident_records_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `civil_status` enum('single','married','widowed','separated','divorced') DEFAULT NULL,
  `nationality` varchar(255) DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `house_number` varchar(50) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `contact_number` varchar(255) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `place_of_birth` varchar(255) DEFAULT NULL,
  `sex` enum('male','female') DEFAULT NULL,
  `address` varchar(1000) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(64) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'resident',
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  `is_dummy` tinyint(1) NOT NULL DEFAULT 0,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `rejected_by` bigint(20) unsigned DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `purok_id` bigint(20) unsigned DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `leader_flag` tinyint(4) GENERATED ALWAYS AS (case when `role` = 'purok_leader' then 1 else NULL end) STORED,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_purok_leader_unique` (`purok_id`,`leader_flag`),
  KEY `users_approved_by_foreign` (`approved_by`),
  KEY `users_rejected_by_foreign` (`rejected_by`),
  KEY `users_role_index` (`role`),
  KEY `users_purok_id_index` (`purok_id`),
  KEY `users_is_approved_index` (`is_approved`),
  KEY `users_role_purok_id_index` (`role`,`purok_id`),
  CONSTRAINT `users_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_purok_id_foreign` FOREIGN KEY (`purok_id`) REFERENCES `puroks` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_rejected_by_foreign` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `websockets_statistics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `websockets_statistics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` varchar(255) NOT NULL,
  `peak_connection_count` int(11) NOT NULL,
  `websocket_message_count` int(11) NOT NULL,
  `api_message_count` int(11) NOT NULL,
  `trigger_type` varchar(255) DEFAULT NULL,
  `trigger_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `websockets_statistics_trigger_type_trigger_id_index` (`trigger_type`,`trigger_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2025_06_03_184609_modify_feedback_user_id_nullable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (107,'2025_05_17_000000_create_puroks_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (108,'2025_05_18_100000_create_users_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (109,'2025_05_18_103511_create_requests_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (110,'2025_05_18_103512_add_barangay_rejection_columns_to_requests_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (111,'2025_05_18_121139_create_sessions_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (112,'2025_05_18_123811_create_cache_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (113,'2025_05_22_142921_create_password_reset_tokens_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (114,'2025_05_24_000000_create_incident_reports_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (115,'2025_05_31_1828_add_approval_fields_to_requests',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (116,'2025_05_31_184333_add_personal_details_to_requests_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (117,'2025_05_31_191517_add_personal_info_to_users_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (118,'2025_05_31_192749_remove_redundant_fields_from_users_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (119,'2025_06_01_125735_add_email_verification_to_users_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (120,'2025_06_01_130148_add_missing_columns_to_users_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (121,'2025_06_01_135500_add_incident_type_to_requests_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (122,'2025_06_01_135600_add_incident_type_to_incident_reports_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (123,'2025_06_01_174449_create_feedback_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (124,'2025_06_03_151849_add_feedback_columns_to_requests_and_incidents',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (125,'2025_06_03_152608_check_and_add_feedback_columns',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (126,'2025_06_03_184608_modify_feedback_user_id_nullable',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (127,'2025_06_04_173219_add_valid_id_path_to_requests_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (128,'2025_06_04_173600_add_valid_id_photos_to_requests_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (129,'2025_06_05_201225_add_processed_columns_to_requests_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (130,'2025_06_06_220427_add_purok_private_notes_to_requests_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (131,'2025_06_07_160302_create_websockets_statistics_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (132,'2025_06_07_222553_create_jobs_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (133,'2025_06_10_151642_add_remarks_to_requests_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (134,'2025_06_10_154807_add_rejected_columns_to_requests_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (135,'2025_06_10_162139_add_approval_details_to_users_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (136,'2025_06_10_230353_create_purok_change_requests_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (137,'2025_08_03_101002_add_approval_fields_to_incident_reports_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (138,'2025_08_03_103828_add_barangay_rejection_reason_to_requests_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (139,'2025_08_06_110016_add_in_progress_status_to_incident_reports',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (140,'2025_08_06_121515_add_viewed_at_to_incident_reports_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (141,'2025_09_02_081155_add_purok_leader_id_to_requests_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (142,'2025_09_02_100209_update_purok_change_requests_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (143,'2025_10_06_000000_add_photos_to_incident_reports_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (144,'2025_10_12_112143_create_failed_jobs_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (145,'2025_10_14_000001_add_last_viewed_at_to_service_requests_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (146,'2025_10_27_123534_create_announcements_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (147,'2025_10_27_152311_add_is_dummy_to_users_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (148,'2025_10_27_152949_add_missing_columns_to_users_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (149,'2025_10_29_213513_add_performance_indexes',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (150,'2026_01_29_000001_add_username_to_users_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (151,'2026_01_30_000200_public_submission_support',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (152,'2026_02_01_000000_create_resident_records_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (153,'2026_02_01_010000_add_location_fields_to_resident_records_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (154,'2026_02_02_010100_add_is_locked_to_resident_records_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (155,'2026_02_02_020200_add_incident_type_other_to_incident_reports_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (156,'2026_02_03_055000_add_unique_purok_leader_constraint_to_users',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (157,'2026_02_03_091000_standardize_purok_leader_role',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (158,'2026_02_04_000001_add_missing_feedback_tracking_columns',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (159,'2026_100001_make_user_id_nullable',2);
