<?php
require_once '../../includes/functions/config.php';

echo "Checking bookings table schema...<br>";

if ($result = $conn->query("SHOW COLUMNS FROM bookings")) {
    while($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "<br>";
    }
} else {
    echo "Error checking table: " . $conn->error . "<br>";
}
?>
