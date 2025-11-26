-- Phase 1: User Activity Log
-- Create table to track all user actions for enquiries page

CREATE TABLE IF NOT EXISTS `user_activity_log` (
  `activity_id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `activity_type` ENUM('contact_form', 'booking_created', 'booking_cancelled', 'payment_made', 'inquiry_sent', 'booking_updated') NOT NULL,
  `activity_description` TEXT,
  `related_booking_id` INT NULL,
  `metadata` JSON NULL COMMENT 'Additional data about the activity',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_user_id` (`user_id`),
  KEY `idx_activity_type` (`activity_type`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_related_booking` (`related_booking_id`),
  CONSTRAINT `fk_activity_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`userID`) ON DELETE CASCADE,
  CONSTRAINT `fk_activity_booking` FOREIGN KEY (`related_booking_id`) REFERENCES `bookings`(`bookingID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
