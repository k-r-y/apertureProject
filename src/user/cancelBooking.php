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
        final_payment_amount,
        final_payment_paid,
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

// Allow cancellation for pending, pending_consultation and confirmed bookings
if (!in_array($booking['booking_status'], ['pending', 'pending_consultation', 'confirmed'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'This booking cannot be cancelled. Current status: ' . $booking['booking_status'] . '. Only pending, pending_consultation, and confirmed bookings can be cancelled.'
    ]);
    exit;
}

// Calculate refund amount (40% of TOTAL amount paid)
$totalPaid = 0;
$refundAmount = 0;
$adminRetains = 0;

// Add downpayment if paid
if ($booking['downpayment_paid'] == 1 && $booking['downpayment_amount'] > 0) {
    $totalPaid += $booking['downpayment_amount'];
}

// Add final payment if paid
if ($booking['final_payment_paid'] == 1 && $booking['final_payment_amount'] > 0) {
    $totalPaid += $booking['final_payment_amount'];
}

// Calculate 40/60 split on total paid amount
if ($totalPaid > 0) {
    $refundAmount = $totalPaid * 0.40; // User gets 40%
    $adminRetains = $totalPaid * 0.60; // Admin keeps 60%
}

// Don't change booking status yet - admin will change it to 'cancelled' after approving
// Just store the refund amount for reference
$updateStmt = $conn->prepare("
    UPDATE bookings 
    SET refund_amount = ?
    WHERE bookingID = ?
");
$updateStmt->bind_param("di", $refundAmount, $bookingId);

if (!$updateStmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Failed to submit cancellation request']);
    $updateStmt->close();
    exit;
}
$updateStmt->close();

// Create refund record if there's a refund
$refundMessage = '';
if ($refundAmount > 0) {
    $refundStmt = $conn->prepare("
        INSERT INTO refunds (bookingID, amount, reason, status, created_at) 
        VALUES (?, ?, 'Cancellation requested by client - Pending admin approval (40% refund policy)', 'pending', NOW())
    ");
    $refundStmt->bind_param("id", $bookingId, $refundAmount);
    
    if ($refundStmt->execute()) {
        $refundMessage = sprintf(
            ' Total paid: ₱%s. You will receive a refund of ₱%s (40%%). Admin retains ₱%s (60%%) as cancellation fee.',
            number_format($totalPaid, 2),
            number_format($refundAmount, 2),
            number_format($adminRetains, 2)
        );
    }
    $refundStmt->close();
}

// Log cancellation activity
logUserActivity(
    $userId,
    'cancellation_requested',
    "Requested cancellation for {$booking['event_type']} booking. Refund: ₱" . number_format($refundAmount, 2),
    $bookingId,
    [
        'refund_amount' => $refundAmount,
        'admin_retains' => $adminRetains,
        'original_downpayment' => $booking['downpayment_amount']
    ]
);

echo json_encode([
    'success' => true,
    'message' => 'Cancellation request submitted successfully.' . $refundMessage . ' <br><br><strong>Note:</strong> Your request is pending admin review. You will be notified once the refund is processed.',
    'refund_amount' => $refundAmount,
    'admin_retains' => $adminRetains
]);
?>
