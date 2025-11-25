<?php
require_once '../includes/functions/config.php';
require_once '../includes/functions/session.php';
require_once '../includes/functions/booking_workflow.php';

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
$stmt = $conn->prepare("SELECT booking_status, downpayment_amount FROM bookings WHERE bookingID = ? AND userID = ?");
$stmt->bind_param("ii", $bookingId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if (!$booking) {
    echo json_encode(['success' => false, 'message' => 'Booking not found']);
    exit;
}

// Allow cancellation for pending_consultation and confirmed bookings
if (!in_array($booking['booking_status'], ['pending_consultation', 'confirmed'])) {
    echo json_encode(['success' => false, 'message' => 'This booking cannot be cancelled']);
    exit;
}

// Update status using workflow function
$result = updateBookingStatus($bookingId, 'cancelled', $userId);

// If cancellation was successful and there was a downpayment, create a refund record
if ($result['success'] && $booking['downpayment_amount'] > 0) {
    $refundStmt = $conn->prepare("INSERT INTO refunds (bookingID, amount, reason, status) VALUES (?, ?, ?, 'pending')");
    $reason = "Booking cancelled by client";
    $refundStmt->bind_param("ids", $bookingId, $booking['downpayment_amount'], $reason);
    
    if ($refundStmt->execute()) {
        $result['message'] .= ' A refund request for your downpayment has been created and will be processed by our admin.';
    }
}

echo json_encode($result);
?>
