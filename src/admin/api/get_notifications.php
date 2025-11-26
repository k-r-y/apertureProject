<?php
require_once '../../includes/functions/config.php';
require_once '../../includes/functions/session.php';
require_once '../../includes/functions/auth.php';

// Check if user is admin
if (!isset($_SESSION["userId"]) || !isset($_SESSION["role"]) || $_SESSION["role"] !== "Admin") {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

try {
    $notifications = [];
    
    // 1. Check for Pending Bookings
    $pendingBookingsStmt = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE booking_status = 'pending'");
    $pendingBookingsCount = $pendingBookingsStmt->fetch_assoc()['count'];
    
    if ($pendingBookingsCount > 0) {
        $notifications[] = [
            'id' => 'pending_bookings',
            'type' => 'booking',
            'title' => 'Pending Bookings',
            'message' => "You have $pendingBookingsCount new booking request(s).",
            'link' => 'bookings.php?status=pending',
            'created_at' => date('Y-m-d H:i:s') // Just now
        ];
    }
    
    // 2. Check for Pending Refunds
    $pendingRefundsStmt = $conn->query("SELECT COUNT(*) as count FROM refunds WHERE refund_status = 'pending'");
    $pendingRefundsCount = $pendingRefundsStmt->fetch_assoc()['count'];
    
    if ($pendingRefundsCount > 0) {
        $notifications[] = [
            'id' => 'pending_refunds',
            'type' => 'refund',
            'title' => 'Refund Requests',
            'message' => "You have $pendingRefundsCount pending refund request(s).",
            'link' => 'refunds.php',
            'created_at' => date('Y-m-d H:i:s')
        ];
    }
    
    // 3. Check for Pending Balance Payments (Bookings with balance proof uploaded but not verified)
    // Assuming 'pending_balance' or similar status, or just check bookings where balance_proof is not null but not fully paid?
    // For now, let's stick to the main ones.
    
    echo json_encode(['success' => true, 'notifications' => $notifications, 'count' => count($notifications)]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
