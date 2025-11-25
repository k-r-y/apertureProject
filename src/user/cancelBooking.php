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

// Verify booking belongs to user and is pending
$stmt = $conn->prepare("SELECT booking_status FROM bookings WHERE bookingID = ? AND userID = ?");
$stmt->bind_param("ii", $bookingId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if (!$booking) {
    echo json_encode(['success' => false, 'message' => 'Booking not found']);
    exit;
}

if ($booking['booking_status'] !== 'pending_consultation') {
    echo json_encode(['success' => false, 'message' => 'Only pending bookings can be cancelled']);
    exit;
}

// Update status using workflow function
$result = updateBookingStatus($bookingId, 'cancelled', $userId);

echo json_encode($result);
?>
