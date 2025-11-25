<?php
require_once '../includes/functions/config.php';

// First create addons table
$sql1 = "CREATE TABLE IF NOT EXISTS `addons` (
  `addonID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `Price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`addonID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

try {
    if ($conn->query($sql1)) {
        echo "✅ Table 'addons' created successfully!<br>";
    } else {
        echo "❌ Error creating 'addons' table: " . $conn->error . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Exception creating 'addons': " . $e->getMessage() . "<br>";
}

// Then create booking_addons table
$sql2 = "CREATE TABLE IF NOT EXISTS `booking_addons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bookingID` int(11) NOT NULL,
  `addonID` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `bookingID` (`bookingID`),
  KEY `addonID` (`addonID`),
  CONSTRAINT `booking_addons_ibfk_1` FOREIGN KEY (`bookingID`) REFERENCES `bookings` (`bookingID`) ON DELETE CASCADE,
  CONSTRAINT `booking_addons_ibfk_2` FOREIGN KEY (`addonID`) REFERENCES `addons` (`addonID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

try {
    if ($conn->query($sql2)) {
        echo "✅ Table 'booking_addons' created successfully!<br>";
        echo "<br><strong>All done! Please refresh your admin bookings page.</strong>";
    } else {
        echo "❌ Error creating 'booking_addons' table: " . $conn->error . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Exception creating 'booking_addons': " . $e->getMessage() . "<br>";
}

$conn->close();
?>
