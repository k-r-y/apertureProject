<?php
require_once 'includes/functions/config.php';

// Check booking #25
$bookingID = 25;
$query = "SELECT bookingID, meeting_link, booking_status FROM bookings WHERE bookingID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $bookingID);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

header('Content-Type: application/json');
echo json_encode($booking, JSON_PRETTY_PRINT);
?>
