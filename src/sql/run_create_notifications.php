<?php
require_once '../includes/functions/config.php';

// Create notifications table
$sql = "CREATE TABLE IF NOT EXISTS `notifications` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

try {
    if ($conn->query($sql)) {
        echo "✅ Table 'notifications' created successfully!";
    } else {
        echo "❌ Error creating 'notifications' table: " . $conn->error;
    }
} catch (Exception $e) {
    echo "❌ Exception creating 'notifications': " . $e->getMessage();
}

$conn->close();
?>
