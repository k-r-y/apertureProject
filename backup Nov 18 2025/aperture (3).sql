-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 18, 2025 at 04:45 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aperture`
--

-- --------------------------------------------------------

--
-- Table structure for table `addons`
--

CREATE TABLE `addons` (
  `addID` int(11) NOT NULL,
  `packageID` varchar(100) NOT NULL,
  `Description` varchar(255) NOT NULL,
  `Price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `addons`
--

INSERT INTO `addons` (`addID`, `packageID`, `Description`, `Price`) VALUES
(10000, 'basic', 'Extra Hour', 1000.00),
(10001, 'basic', 'Drone Shots', 2000.00),
(10002, 'basic', 'Full-Length Video (5–8 minutes)', 2500.00),
(10003, 'basic', 'USB Copy with Case', 500.00),
(10004, 'elite', 'Drone Shots', 2000.00),
(10005, 'elite', 'Same-Day Edit (SDE)', 3500.00),
(10006, 'elite', 'Photo Album (30 Pages)', 2000.00),
(10007, 'elite', 'Extra Photographer', 2000.00),
(10008, 'elite', 'Short BTS (Behind-the-Scenes Reel)', 1500.00),
(10009, 'premium', 'Extra Hour', 1500.00),
(10010, 'premium', 'Livestream Setup', 3000.00),
(10011, 'premium', 'Extra Location', 1000.00),
(10012, 'premium', '4K Upgrade', 2000.00);

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `bookingID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `packageID` int(11) NOT NULL,
  `clientFirstName` varchar(100) NOT NULL,
  `clientLastName` varchar(100) NOT NULL,
  `clientEmail` varchar(255) NOT NULL,
  `clientPhone` varchar(20) NOT NULL,
  `eventDate` date NOT NULL,
  `eventTimeFrom` time NOT NULL,
  `eventTimeTo` time NOT NULL,
  `eventLocation` text NOT NULL,
  `eventLandmark` varchar(255) DEFAULT NULL,
  `packagePrice` decimal(10,2) NOT NULL,
  `addonsTotal` decimal(10,2) DEFAULT 0.00,
  `totalAmount` decimal(10,2) NOT NULL,
  `downpayment` decimal(10,2) DEFAULT 0.00,
  `status` enum('Pending','Confirmed','Completed','Cancelled') DEFAULT 'Pending',
  `paymentStatus` enum('Unpaid','Partial','Paid') DEFAULT 'Unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contactmessages`
--

CREATE TABLE `contactmessages` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inclusion`
--

CREATE TABLE `inclusion` (
  `inclusionID` varchar(255) NOT NULL,
  `packageID` varchar(255) NOT NULL,
  `Description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inclusion`
--

INSERT INTO `inclusion` (`inclusionID`, `packageID`, `Description`) VALUES
('BSC001', 'basic', '1 Photographer + 1 Videographer'),
('BSC002', 'basic', 'Full Event Coverage (Up to 5 hours)'),
('BSC003', 'basic', '100+ Professionally Edited Photos'),
('BSC004', 'basic', '3–5 Minute Highlight Video'),
('BSC005', 'basic', 'Full Event Video (10–15 minutes)'),
('BSC006', 'basic', 'On-site Lighting Setup'),
('BSC007', 'basic', 'Audio Recording for Key Moments'),
('BSC008', 'basic', 'Online Gallery Access (2 months)'),
('ELT001', 'elite', '1 Photographer + 1 Videographer + Assistant'),
('ELT002', 'elite', 'Full Event Coverage (Up to 8 hours)'),
('ELT003', 'elite', '150+ Professionally Edited Photos'),
('ELT004', 'elite', '5–7 Minute Highlight Film (Full HD)'),
('ELT005', 'elite', 'Full Event Video (15–20 minutes)'),
('ELT006', 'elite', 'Drone Coverage Included'),
('ELT007', 'elite', 'Audio Recording for Vows, Speeches & Messages'),
('ELT008', 'elite', 'Cinematic Color Grading'),
('ELT009', 'elite', 'On-site Lighting & Audio Setup'),
('ELT010', 'elite', 'Online Gallery Access (3 months)'),
('ELT011', 'elite', 'Personalized USB Copy'),
('PRM001', 'premium', '2 Photographers + 2 Videographers'),
('PRM002', 'premium', 'Full Event Coverage (Up to 10 hours)'),
('PRM003', 'premium', 'Unlimited Shots + 250+ Edited Photos'),
('PRM004', 'premium', '7–10 Minute Cinematic Highlight Film (Full HD or 4K)'),
('PRM005', 'premium', 'Full Event Video (25+ minutes)'),
('PRM006', 'premium', 'Drone Coverage (Included)'),
('PRM007', 'premium', 'Same-Day Edit (SDE) Included'),
('PRM008', 'premium', 'Audio Mixing & Cinematic Color Grading'),
('PRM009', 'premium', 'Livestream Setup (Optional)'),
('PRM010', 'premium', 'Personalized Online Gallery (1 Year Access)'),
('PRM011', 'premium', 'Premium USB Copy + Custom Box'),
('PRM012', 'premium', 'Printed Photo Album (40 Pages)'),
('PRM013', 'premium', 'Free Save-The-Date Teaser (30 seconds)');

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `packageID` varchar(100) NOT NULL,
  `packageName` varchar(100) NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`packageID`, `packageName`, `Price`, `description`) VALUES
('basic', 'Basic Package', 7500.00, 'Perfect for small celebrations or short events that need professional documentation.'),
('elite', 'Elite Package', 15000.00, 'A balanced package for most events — professional coverage, storytelling, and cinematic editing.'),
('premium', 'Premium Package', 25000.00, 'For clients who want a full cinematic experience and top-tier service.');

-- --------------------------------------------------------

--
-- Table structure for table `ratelimiting`
--

CREATE TABLE `ratelimiting` (
  `id` int(100) NOT NULL,
  `userID` int(100) NOT NULL,
  `logInAttempt` int(100) NOT NULL,
  `loginLocked` timestamp NULL DEFAULT NULL,
  `loginEmailVerificationAttempt` int(100) NOT NULL,
  `loginEmailVerificationLocked` timestamp NULL DEFAULT NULL,
  `registrationEmailVerificationAttempt` int(100) NOT NULL,
  `registrationEmailVerificationLocked` timestamp NULL DEFAULT NULL,
  `fogotEmailVerificationAttempt` int(100) NOT NULL,
  `fogotEmailVerificationLocked` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
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
  `resetCodeExpires_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `Email`, `Password`, `Role`, `FirstName`, `LastName`, `FullName`, `contactNo`, `isVerified`, `profileCompleted`, `verificationCode`, `codeCreated_at`, `codeExpires_at`, `created_at`, `updated_at`, `passwordResetCode`, `resetCodeCreated_at`, `resetCodeExpires_at`) VALUES
(1029, 'pawcasiano@kld.edu.ph', '$2y$10$1QEIwuoTtzsScJb9WAvaiuREXf8wYPfADVQpkdKupvHgB94rhk6qC', 'Admin', 'Prince Andrew', 'Casiano', 'Prince Andrew Casiano', '09977676554', 1, 1, NULL, '2025-11-18 05:45:42', NULL, '2025-11-17 16:48:24', '2025-11-18 05:45:42', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addons`
--
ALTER TABLE `addons`
  ADD PRIMARY KEY (`addID`),
  ADD KEY `packageID` (`packageID`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`bookingID`),
  ADD KEY `idx_user` (`userID`),
  ADD KEY `idx_package` (`packageID`),
  ADD KEY `idx_event_date` (`eventDate`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `contactmessages`
--
ALTER TABLE `contactmessages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_read` (`is_read`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `inclusion`
--
ALTER TABLE `inclusion`
  ADD PRIMARY KEY (`inclusionID`),
  ADD KEY `packageID` (`packageID`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`packageID`);

--
-- Indexes for table `ratelimiting`
--
ALTER TABLE `ratelimiting`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `idx_email` (`Email`),
  ADD KEY `idx_verification` (`verificationCode`),
  ADD KEY `idx_verified` (`isVerified`),
  ADD KEY `idx_reset_code` (`passwordResetCode`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addons`
--
ALTER TABLE `addons`
  MODIFY `addID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10014;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `bookingID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contactmessages`
--
ALTER TABLE `contactmessages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ratelimiting`
--
ALTER TABLE `ratelimiting`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1030;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addons`
--
ALTER TABLE `addons`
  ADD CONSTRAINT `addons_ibfk_1` FOREIGN KEY (`packageID`) REFERENCES `packages` (`packageID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `inclusion`
--
ALTER TABLE `inclusion`
  ADD CONSTRAINT `inclusion_ibfk_1` FOREIGN KEY (`packageID`) REFERENCES `packages` (`packageID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ratelimiting`
--
ALTER TABLE `ratelimiting`
  ADD CONSTRAINT `ratelimiting_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
