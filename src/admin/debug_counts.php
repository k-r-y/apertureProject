<?php
require_once '../includes/functions/config.php';

// Total Bookings (All Time)
$sqlAll = "SELECT COUNT(*) as total FROM bookings";
$resultAll = $conn->query($sqlAll);
$totalAll = $resultAll->fetch_assoc()['total'];

// Bookings This Month (Event Date)
$sqlMonth = "SELECT COUNT(*) as total FROM bookings WHERE MONTH(event_date) = MONTH(CURDATE()) AND YEAR(event_date) = YEAR(CURDATE())";
$resultMonth = $conn->query($sqlMonth);
$totalMonth = $resultMonth->fetch_assoc()['total'];

// Pending Bookings (All Time)
$sqlPending = "SELECT COUNT(*) as total FROM bookings WHERE booking_status = 'pending'";
$resultPending = $conn->query($sqlPending);
$totalPending = $resultPending->fetch_assoc()['total'];

echo "Total Bookings (All Time): $totalAll\n";
echo "Bookings This Month (Event Date): $totalMonth\n";
echo "Pending Bookings (All Time): $totalPending\n";
?>
