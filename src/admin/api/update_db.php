<?php
require_once '../../includes/functions/config.php';

// Add meeting_link column if it doesn't exist
$query = "SHOW COLUMNS FROM bookings LIKE 'meeting_link'";
$result = $conn->query($query);

if ($result->num_rows == 0) {
    $alterQuery = "ALTER TABLE bookings ADD COLUMN meeting_link VARCHAR(255) NULL AFTER consultation_time";
    if ($conn->query($alterQuery)) {
        echo "Successfully added 'meeting_link' column to bookings table.";
    } else {
        echo "Error adding column: " . $conn->error;
    }
} else {
    echo "Column 'meeting_link' already exists.";
}
?>
