<?php
require_once '../includes/functions/config.php';

// Helper to get revenue
function getRevenue($conn, $dateCondDP, $dateCondFP, $dateCondRF) {
    $revenue = 0;
    
    $sqlDP = "SELECT SUM(downpayment_amount) as total FROM bookings WHERE downpayment_paid = 1 AND booking_status != 'cancelled' $dateCondDP";
    $resultDP = $conn->query($sqlDP);
    $revenue += ($resultDP->fetch_assoc()['total'] ?? 0);

    $sqlFP = "SELECT SUM(total_amount - downpayment_amount) as total FROM bookings WHERE final_payment_paid = 1 AND booking_status != 'cancelled' $dateCondFP";
    $resultFP = $conn->query($sqlFP);
    $revenue += ($resultFP->fetch_assoc()['total'] ?? 0);

    $sqlRF = "SELECT SUM(amount) as total FROM refunds WHERE status = 'processed' $dateCondRF";
    $resultRF = $conn->query($sqlRF);
    $revenue -= ($resultRF->fetch_assoc()['total'] ?? 0);
    
    return $revenue;
}

// All Time
$revAll = getRevenue($conn, "", "", "");

// This Month
$dpMonth = "AND MONTH(downpayment_paid_date) = MONTH(CURDATE()) AND YEAR(downpayment_paid_date) = YEAR(CURDATE())";
$fpMonth = "AND MONTH(final_payment_paid_date) = MONTH(CURDATE()) AND YEAR(final_payment_paid_date) = YEAR(CURDATE())";
$rfMonth = "AND MONTH(processed_at) = MONTH(CURDATE()) AND YEAR(processed_at) = YEAR(CURDATE())";
$revMonth = getRevenue($conn, $dpMonth, $fpMonth, $rfMonth);

echo "Total Revenue (All Time): ₱" . number_format($revAll, 2) . "\n";
echo "Total Revenue (This Month): ₱" . number_format($revMonth, 2) . "\n";
?>
