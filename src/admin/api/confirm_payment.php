<?php
/**
 * Payment Confirmation API
 * Handles separate confirmation of downpayment and final payment
 */

require_once '../../includes/functions/config.php';
require_once '../../includes/functions/session.php';
require_once '../../includes/functions/activity_logger.php';
require_once '../../includes/functions/booking_status_automation.php';

header('Content-Type: application/json');

// Check admin authentication
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$bookingId = $data['bookingId'] ?? 0;
$paymentType = $data['paymentType'] ?? ''; // 'downpayment', 'final', or 'full'

if (!$bookingId || !$paymentType) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

try {
    // Get booking details
    $stmt = $conn->prepare("
        SELECT 
            b.*,
            u.userID,
            u.fullName,
            u.email
        FROM bookings b
        JOIN users u ON b.userID = u.userID
        WHERE b.bookingID = ?
    ");
    $stmt->bind_param("i", $bookingId);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    $stmt->close();
    
    if (!$booking) {
        echo json_encode(['success' => false, 'message' => 'Booking not found']);
        exit;
    }
    
    $currentDate = date('Y-m-d H:i:s');
    $eventDate = $booking['event_date'];
    $today = date('Y-m-d');
    
    // Determine new status based on payment type and event date
    $newStatus = $booking['booking_status'];
    
    switch ($paymentType) {
        case 'downpayment':
            // Mark downpayment as paid
            $stmt = $conn->prepare("
                UPDATE bookings 
                SET downpayment_paid = 1,
                    downpayment_paid_date = ?,
                    booking_status = 'confirmed'
                WHERE bookingID = ?
            ");
            $stmt->bind_param("si", $currentDate, $bookingId);
            $stmt->execute();
            $stmt->close();
            
            // Update invoice status to partially paid
            $conn->query("UPDATE invoices SET status = 'partially_paid' WHERE bookingID = $bookingId");
            
            // Auto-update status to confirmed
            checkAndUpdateToConfirmed($bookingId);
            
            $newStatus = 'confirmed';
            $message = 'Downpayment confirmed. Booking status automatically updated to Confirmed.';
            
            // Log activity
            logUserActivity(
                $booking['userID'], 
                'payment_made', 
                "Downpayment of ₱" . number_format($booking['downpayment_amount'], 2) . " confirmed by admin",
                $bookingId,
                ['payment_type' => 'downpayment', 'amount' => $booking['downpayment_amount']]
            );
            break;
            
        case 'final':
            // Mark final payment as paid
            // Also mark downpayment as paid (you can't pay final without paying downpayment)
            // Check if event has passed
            if ($eventDate < $today) {
                $newStatus = 'post_production';
            } else {
                $newStatus = 'confirmed';
            }
            
            $stmt = $conn->prepare("
                UPDATE bookings 
                SET downpayment_paid = 1,
                    downpayment_paid_date = IF(downpayment_paid_date IS NULL, ?, downpayment_paid_date),
                    final_payment_paid = 1,
                    final_payment_paid_date = ?,
                    booking_status = ?,
                    is_fully_paid = 1
                WHERE bookingID = ?
            ");
            $stmt->bind_param("sssi", $currentDate, $currentDate, $newStatus, $bookingId);
            $stmt->execute();
            $stmt->close();
            
            // Update invoice status to paid and mark as completed
            $conn->query("UPDATE invoices SET status = 'paid', due_date = CURRENT_DATE WHERE bookingID = $bookingId");
            
            // Auto-check if can transition to completed (if photos already uploaded)
            checkAndUpdateToCompleted($bookingId);
            
            $message = $newStatus === 'post_production' 
                ? 'Final payment confirmed. Checking if ready for completion.' 
                : 'Final payment confirmed. Booking is now fully paid.';
            
            // Log activity
            $balanceAmount = $booking['total_amount'] - $booking['downpayment_amount'];
            logUserActivity(
                $booking['userID'], 
                'payment_made', 
                "Final payment of ₱" . number_format($balanceAmount, 2) . " confirmed by admin",
                $bookingId,
                ['payment_type' => 'final', 'amount' => $balanceAmount]
            );
            break;
            
        case 'full':
            // User paid full amount - mark both as paid
            if ($eventDate < $today) {
                $newStatus = 'post_production';
            } else {
                $newStatus = 'confirmed';
            }
            
            $stmt = $conn->prepare("
                UPDATE bookings 
                SET downpayment_paid = 1,
                    downpayment_paid_date = ?,
                    final_payment_paid = 1,
                    final_payment_paid_date = ?,
                    booking_status = ?,
                    is_fully_paid = 1
                WHERE bookingID = ?
            ");
            $stmt->bind_param("sssi", $currentDate, $currentDate, $newStatus, $bookingId);
            $stmt->execute();
            $stmt->close();
            
            // Auto-check if can transition to completed
            checkAndUpdateToCompleted($bookingId);
            
            $message = 'Full payment confirmed. Both downpayment and final payment marked as paid.';
            
            // Log activity
            logUserActivity(
                $booking['userID'], 
                'payment_made', 
                "Full payment of ₱" . number_format($booking['total_amount'], 2) . " confirmed by admin",
                $bookingId,
                ['payment_type' => 'full', 'amount' => $booking['total_amount']]
            );
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid payment type']);
            exit;
    }
    
    echo json_encode([
        'success' => true, 
        'message' => $message,
        'newStatus' => $newStatus
    ]);
    
} catch (Exception $e) {
    error_log("Payment confirmation error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error occurred']);
}
?>
