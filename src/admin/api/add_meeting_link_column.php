<?php
require_once '../../includes/functions/config.php';

echo "Adding meeting_link column to bookings table...<br>";

// Check if column exists first
$check = $conn->query("SHOW COLUMNS FROM bookings LIKE 'meeting_link'");
if ($check->num_rows == 0) {
    $sql = "ALTER TABLE bookings ADD COLUMN meeting_link VARCHAR(255) DEFAULT NULL AFTER admin_notes";
    if ($conn->query($sql)) {
        echo "Column 'meeting_link' added successfully.<br>";
    } else {
        echo "Error adding column: " . $conn->error . "<br>";
    }
} else {
    echo "Column 'meeting_link' already exists.<br>";
}
?>
