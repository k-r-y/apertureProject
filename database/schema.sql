-- =================================================================================================
--
--                                  Aperture Studios - Main Database Schema
--
-- This schema is designed to support the entire business logic of the Aperture booking system,
-- including user management, bookings, packages, payments, galleries, and reviews.
--
-- =================================================================================================

-- --------------------------------------------------------
--
-- Table structure for table `users`
-- Stores user account information, roles, and verification status.
--
-- --------------------------------------------------------
CREATE TABLE `users` (
  `userID` INT AUTO_INCREMENT PRIMARY KEY,
  `FirstName` VARCHAR(50) DEFAULT NULL,
  `LastName` VARCHAR(50) DEFAULT NULL,
  `FullName` VARCHAR(101) GENERATED ALWAYS AS (CONCAT_WS(' ', `FirstName`, `LastName`)) STORED,
  `Email` VARCHAR(100) NOT NULL UNIQUE,
  `Password` VARCHAR(255) NOT NULL,
  `contactNo` VARCHAR(20) DEFAULT NULL,
  `Role` ENUM('User', 'Admin') NOT NULL DEFAULT 'User',
  `isVerified` BOOLEAN NOT NULL DEFAULT FALSE,
  `profileCompleted` BOOLEAN NOT NULL DEFAULT FALSE,
  `verificationCode` VARCHAR(6) DEFAULT NULL,
  `codeCreated_at` DATETIME DEFAULT NULL,
  `codeExpires_at` DATETIME DEFAULT NULL,
  `passwordResetCode` VARCHAR(6) DEFAULT NULL,
  `resetCodeCreated_at` DATETIME DEFAULT NULL,
  `resetCodeExpires_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `ratelimiting`
-- Tracks failed attempts and locks accounts to prevent brute-force attacks.
--
-- --------------------------------------------------------
CREATE TABLE `ratelimiting` (
  `rateLimitID` INT AUTO_INCREMENT PRIMARY KEY,
  `userID` INT NOT NULL UNIQUE,
  `logInAttempt` INT NOT NULL DEFAULT 0,
  `loginLocked` DATETIME DEFAULT NULL,
  `registrationEmailVerificationAttempt` INT NOT NULL DEFAULT 0,
  `registrationEmailVerificationLocked` DATETIME DEFAULT NULL,
  `loginEmailVerificationAttempt` INT NOT NULL DEFAULT 0,
  `loginEmailVerificationLocked` DATETIME DEFAULT NULL,
  `fogotEmailVerificationAttempt` INT NOT NULL DEFAULT 0, -- Typo matches auth.php
  `fogotEmailVerificationLocked` DATETIME DEFAULT NULL, -- Typo matches auth.php
  FOREIGN KEY (`userID`) REFERENCES `users`(`userID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `packages`
-- Stores the main service packages offered.
--
-- --------------------------------------------------------
CREATE TABLE `packages` (
  `packageID` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `price` DECIMAL(10, 2) NOT NULL,
  `coverage_hours` INT DEFAULT 0,
  `extra_hour_rate` DECIMAL(10, 2) DEFAULT 0,
  `is_active` BOOLEAN NOT NULL DEFAULT TRUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `package_inclusions`
-- Stores the list of features included in each package.
--
-- --------------------------------------------------------
CREATE TABLE `package_inclusions` (
  `inclusionID` INT AUTO_INCREMENT PRIMARY KEY,
  `packageID` INT NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  FOREIGN KEY (`packageID`) REFERENCES `packages`(`packageID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `addons`
-- Stores optional add-ons that can be added to bookings.
--
-- --------------------------------------------------------
CREATE TABLE `addons` (
  `addonID` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255),
  `price` DECIMAL(10, 2) NOT NULL,
  `is_active` BOOLEAN NOT NULL DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `bookings`
-- The central table for all client bookings.
--
-- --------------------------------------------------------
CREATE TABLE `bookings` (
  `bookingID` INT AUTO_INCREMENT PRIMARY KEY,
  `userID` INT NOT NULL,
  `packageID` INT NOT NULL,
  `event_type` VARCHAR(100) NOT NULL,
  `event_date` DATE NOT NULL,
  `event_time_start` TIME NOT NULL,
  `event_time_end` TIME NOT NULL,
  `event_location` VARCHAR(255) NOT NULL,
  `event_theme` VARCHAR(100),
  `client_message` TEXT,
  `total_amount` DECIMAL(10, 2) NOT NULL,
  `downpayment_amount` DECIMAL(10, 2) NOT NULL,
  `gdrive_link` VARCHAR(255) DEFAULT NULL,
  `booking_status` ENUM('pending_consultation', 'confirmed', 'post_production', 'completed', 'cancelled') NOT NULL DEFAULT 'pending_consultation',
  `is_fully_paid` BOOLEAN NOT NULL DEFAULT FALSE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`userID`) REFERENCES `users`(`userID`),
  FOREIGN KEY (`packageID`) REFERENCES `packages`(`packageID`),
  INDEX `idx_event_date` (`event_date`),
  INDEX `idx_status` (`booking_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `booking_addons`
-- A pivot table linking selected add-ons to a specific booking.
--
-- --------------------------------------------------------
CREATE TABLE `booking_addons` (
  `bookingID` INT NOT NULL,
  `addonID` INT NOT NULL,
  PRIMARY KEY (`bookingID`, `addonID`),
  FOREIGN KEY (`bookingID`) REFERENCES `bookings`(`bookingID`) ON DELETE CASCADE,
  FOREIGN KEY (`addonID`) REFERENCES `addons`(`addonID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `payments`
-- Tracks all payments (downpayments and final payments) for bookings.
--
-- --------------------------------------------------------
CREATE TABLE `payments` (
  `paymentID` INT AUTO_INCREMENT PRIMARY KEY,
  `bookingID` INT NOT NULL,
  `amount` DECIMAL(10, 2) NOT NULL,
  `payment_method` VARCHAR(50),
  `proof_of_payment_path` VARCHAR(255),
  `payment_status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  `payment_type` ENUM('downpayment', 'final_payment') NOT NULL,
  `paid_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`bookingID`) REFERENCES `bookings`(`bookingID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `gallery_photos`
-- Stores paths to web-optimized photos for a client's private gallery.
--
-- --------------------------------------------------------
CREATE TABLE `gallery_photos` (
  `photoID` INT AUTO_INCREMENT PRIMARY KEY,
  `bookingID` INT NOT NULL,
  `photo_path` VARCHAR(255) NOT NULL,
  `uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`bookingID`) REFERENCES `bookings`(`bookingID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `reviews`
-- Stores client reviews and ratings for completed bookings.
--
-- --------------------------------------------------------
CREATE TABLE `reviews` (
  `reviewID` INT AUTO_INCREMENT PRIMARY KEY,
  `bookingID` INT NOT NULL,
  `userID` INT NOT NULL,
  `rating` TINYINT NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
  `comment` TEXT,
  `is_approved` BOOLEAN NOT NULL DEFAULT FALSE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`bookingID`) REFERENCES `bookings`(`bookingID`),
  FOREIGN KEY (`userID`) REFERENCES `users`(`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;