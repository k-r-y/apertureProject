<?php
require_once '../includes/functions/config.php';
require_once '../includes/functions/session.php';
require_once '../includes/functions/booking_workflow.php';
require_once '../includes/functions/activity_logger.php';

header('Content-Type: application/json');

if (!isset($_SESSION['userId'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$bookingId = $input['bookingId'] ?? 0;
$userId = $_SESSION['userId'];

// Verify booking belongs to user and is cancellable
$stmt = $conn->prepare("
    SELECT 
        booking_status, 
        downpayment_amount, 
        downpayment_paid,
        total_amount,
        event_type
    FROM bookings 
    WHERE bookingID = ? AND userID = ?
");
$stmt->bind_param("ii", $bookingId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();
$stmt->close();

if (!$booking) {
    echo json_encode(['success' => false, 'message' => 'Booking not found']);
    exit;
}

// Allow cancellation for pending_consultation and confirmed bookings
if (!in_array($booking['booking_status'], ['pending_consultation', 'confirmed'])) {
    echo json_encode(['success' => false, 'message' => 'This booking cannot be cancelled']);
    exit;
}

// Calculate refund amount (40% of downpayment if paid)
$refundAmount = 0;
$adminRetains = 0;

if ($booking['downpayment_paid'] == 1 && $booking['downpayment_amount'] > 0) {
    $refundAmount = $booking['downpayment_amount'] * 0.40; // User gets 40%
    $adminRetains = $booking['downpayment_amount'] * 0.60; // Admin keeps 60%
}

// Update booking status to cancelled and set refund amount
$updateStmt = $conn->prepare("
    UPDATE bookings 
    SET booking_status = 'cancelled',
        refund_amount = ?,
        refund_processed_date = NOW()
    WHERE bookingID = ?
");
$updateStmt->bind_param("di", $refundAmount, $bookingId);

if (!$updateStmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Failed to cancel booking']);
    $updateStmt->close();
    exit;
}
$updateStmt->close();

// Create refund record if there's a refund
$refundMessage = '';
if ($refundAmount > 0) {
    $refundStmt = $conn->prepare("
        INSERT INTO refunds (bookingID, amount, reason, status, created_at) 
        VALUES (?, ?, 'Booking cancelled by client - 40% refund policy', 'pending', NOW())
    ");
    $refundStmt->bind_param("id", $bookingId, $refundAmount);
    
    if ($refundStmt->execute()) {
        $refundMessage = sprintf(
            ' You will receive a refund of ₱%s (40%% of your downpayment). Admin retains ₱%s (60%%) as cancellation fee.',
            number_format($refundAmount, 2),
            number_format($adminRetains, 2)
        );
    }
    $refundStmt->close();
}

// Log cancellation activity
logUserActivity(
    $userId,
    'booking_cancelled',
    "Cancelled {$booking['event_type']} booking. Refund: ₱" . number_format($refundAmount, 2),
    $bookingId,
    [
        'refund_amount' => $refundAmount,
        'admin_retains' => $adminRetains,
        'original_downpayment' => $booking['downpayment_amount']
    ]
);

echo json_encode([
    'success' => true,
    'message' => 'Booking cancelled successfully.' . $refundMessage,
    'refund_amount' => $refundAmount,
    'admin_retains' => $adminRetains
]);
?>
