<?php
require_once 'includes/functions/config.php';

// Add bookingID column
$sql1 = "ALTER TABLE user_photos ADD COLUMN bookingID INT(11) DEFAULT NULL";
if ($conn->query($sql1) === TRUE) {
    echo "Column bookingID added successfully\n";
} else {
    echo "Error adding bookingID: " . $conn->error . "\n";
}

// Add photo_type column
$sql2 = "ALTER TABLE user_photos ADD COLUMN photo_type ENUM('edited', 'raw') DEFAULT 'edited'";
if ($conn->query($sql2) === TRUE) {
    echo "Column photo_type added successfully\n";
} else {
    echo "Error adding photo_type: " . $conn->error . "\n";
}

// Add Foreign Key constraint for bookingID (optional but good practice)
// Checking if bookingID exists in bookings table first to avoid errors if data is inconsistent
// For now, we'll skip the strict FK constraint to avoid issues with existing data, 
// but in a real scenario we should clean data and add it.

$conn->close();
?>
