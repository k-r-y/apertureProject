<?php
// Start output buffering to catch any errors/warnings
ob_start();

$response = ['success' => false, 'message' => 'An unexpected error occurred'];

try {
    require_once '../includes/functions/config.php';
    require_once '../includes/functions/session.php';
    require_once '../includes/functions/booking_workflow.php';
    require_once '../includes/functions/activity_logger.php';
    require_once '../includes/functions/notifications.php';

    if (!isset($_SESSION['userId'])) {
        throw new Exception('Unauthorized');
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $bookingId = $input['bookingId'] ?? 0;
    $userId = $_SESSION['userId'];

    // Verify booking belongs to user and is cancellable
    $query = "
        SELECT 
            bookingID,
            booking_status, 
            downpayment_amount, 
            downpayment_paid,
            final_payment_paid,
            total_amount,
            event_type
        FROM bookings 
        WHERE bookingID = ? AND userID = ?
    ";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception('Database error (prepare booking): ' . $conn->error);
    }

    $stmt->bind_param("ii", $bookingId, $userId);
    if (!$stmt->execute()) {
        throw new Exception('Database error (execute booking): ' . $stmt->error);
    }

    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    $stmt->close();

    if (!$booking) {
        throw new Exception('Booking not found');
    }

    // Allow cancellation for pending, pending_consultation and confirmed bookings
    if (!in_array($booking['booking_status'], ['pending', 'pending_consultation', 'confirmed'])) {
        throw new Exception('This booking cannot be cancelled. Current status: ' . $booking['booking_status']);
    }

    // Calculate refund amount (40% of TOTAL amount paid)
    $totalPaid = 0;
    $refundAmount = 0;
    $adminRetains = 0;

    // Add downpayment if paid
    if ($booking['downpayment_paid'] == 1 && $booking['downpayment_amount'] > 0) {
        $totalPaid += $booking['downpayment_amount'];
    }

    // Calculate final payment amount
    $finalPaymentAmount = $booking['total_amount'] - $booking['downpayment_amount'];

    // Add final payment if paid
    if ($booking['final_payment_paid'] == 1 && $finalPaymentAmount > 0) {
        $totalPaid += $finalPaymentAmount;
    }

    // Calculate 40/60 split on total paid amount
    if ($totalPaid > 0) {
        $refundAmount = $totalPaid * 0.40; // User gets 40%
        $adminRetains = $totalPaid * 0.60; // Admin keeps 60%
    }

    // Update booking status to 'cancellation_pending' and store refund amount
    $updateStmt = $conn->prepare("
        UPDATE bookings 
        SET refund_amount = ?,
            booking_status = 'cancellation_pending'
        WHERE bookingID = ?
    ");

    if (!$updateStmt) {
        throw new Exception('Database error (prepare update): ' . $conn->error);
    }

    $updateStmt->bind_param("di", $refundAmount, $bookingId);

    if (!$updateStmt->execute()) {
        throw new Exception('Failed to submit cancellation request: ' . $updateStmt->error);
    }
    $updateStmt->close();

    // Create refund record if there's a refund
    $refundMessage = '';
    if ($refundAmount > 0) {
        $refundStmt = $conn->prepare("
            INSERT INTO refunds (bookingID, amount, reason, status, requested_at) 
            VALUES (?, ?, 'Cancellation requested by client - Pending admin approval (40% refund policy)', 'pending', NOW())
        ");
        
        if (!$refundStmt) {
            throw new Exception('Database error (prepare refund): ' . $conn->error);
        }

        $refundStmt->bind_param("id", $bookingId, $refundAmount);
        
        if ($refundStmt->execute()) {
            $refundMessage = sprintf(
                ' Total paid: ₱%s. You will receive a refund of ₱%s (40%%). Admin retains ₱%s (60%%) as cancellation fee.',
                number_format($totalPaid, 2),
                number_format($refundAmount, 2),
                number_format($adminRetains, 2)
            );
        } else {
             throw new Exception('Database error (execute refund): ' . $refundStmt->error);
        }
        $refundStmt->close();
    }

    // Notifications and Logging (Non-critical)
    try {
        // Log cancellation activity
        logUserActivity(
            $userId,
            'booking_cancelled',
            "Requested cancellation for {$booking['event_type']} booking. Refund: ₱" . number_format($refundAmount, 2),
            $bookingId,
            [
                'refund_amount' => $refundAmount,
                'admin_retains' => $adminRetains,
                'total_paid' => $totalPaid,
                'status' => 'pending_approval'
            ]
        );

        // Send Admin Notification
        $notifier = new NotificationSystem($conn);
        $adminEmail = $_ENV['SMTP_USERNAME'] ?? 'admin@aperture.com'; // Fallback
        
            $booking['event_type'],
            $refundAmount
        );

        // Send User Notification
        $notifier->sendUserCancellationSubmitted(
            $_SESSION['email'] ?? 'user@example.com', // Fallback if session email not set, though it should be
            $_SESSION['FirstName'] . ' ' . $_SESSION['LastName'],
            $userId,
            $booking['bookingID'],
            $refundAmount
        );
    } catch (Exception $e) {
        // Log error but don't fail the request
        error_log("Notification/Logging error in cancelBooking.php: " . $e->getMessage());
    }

    $response = [
        'success' => true,
        'message' => 'Cancellation request submitted successfully.' . $refundMessage . ' <br><br><strong>Note:</strong> Your request is pending admin review. You will be notified once the refund is processed.',
        'refund_amount' => $refundAmount,
        'admin_retains' => $adminRetains
    ];

} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

// Clean buffer and output JSON
ob_end_clean();
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
