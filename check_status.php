<?php
require_once 'src/includes/functions/config.php';

$result = $conn->query("SELECT DISTINCT booking_status FROM bookings");
while ($row = $result->fetch_assoc()) {
    echo $row['booking_status'] . "\n";
}
?>
