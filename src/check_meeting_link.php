<?php
// Quick script to check and add meeting link to booking #25
require_once 'includes/functions/config.php';

$bookingID = 25;

// Check current meeting_link value
$checkQuery = "SELECT bookingID, meeting_link FROM bookings WHERE bookingID = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("i", $bookingID);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

echo "<h3>Current Meeting Link Status for Booking #$bookingID</h3>";
echo "<pre>";
echo "Booking ID: " . $booking['bookingID'] . "\n";
echo "Meeting Link: " . ($booking['meeting_link'] ? $booking['meeting_link'] : 'NULL (not set)') . "\n";
echo "</pre>";

// If you want to add a test meeting link, uncomment the lines below:
/*
$testLink = "https://zoom.us/j/123456789";
$updateQuery = "UPDATE bookings SET meeting_link = ? WHERE bookingID = ?";
$updateStmt = $conn->prepare($updateQuery);
$updateStmt->bind_param("si", $testLink, $bookingID);
$updateStmt->execute();
echo "<p style='color: green;'>✓ Test meeting link added: $testLink</p>";
*/

?>
