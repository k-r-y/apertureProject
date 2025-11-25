CREATE TABLE IF NOT EXISTS `booking_addons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bookingID` int(11) NOT NULL,
  `addonID` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `bookingID` (`bookingID`),
  KEY `addonID` (`addonID`),
  CONSTRAINT `booking_addons_ibfk_1` FOREIGN KEY (`bookingID`) REFERENCES `bookings` (`bookingID`) ON DELETE CASCADE,
  CONSTRAINT `booking_addons_ibfk_2` FOREIGN KEY (`addonID`) REFERENCES `addons` (`addonID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
