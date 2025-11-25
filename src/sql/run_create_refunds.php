<?php
require_once '../includes/functions/config.php';

// Create refunds table
$sql = "CREATE TABLE IF NOT EXISTS `refunds` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

try {
    if ($conn->query($sql)) {
        echo "✅ Table 'refunds' created successfully!";
    } else {
        echo "❌ Error creating 'refunds' table: " . $conn->error;
    }
} catch (Exception $e) {
    echo "❌ Exception creating 'refunds': " . $e->getMessage();
}

$conn->close();
?>
