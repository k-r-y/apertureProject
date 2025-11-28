<?php
/**
 * Booking Status Automation
 * Handles automatic status transitions based on business rules
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/booking_workflow.php';

/**
 * Auto-update status to 'confirmed' when downpayment is paid
 */
function checkAndUpdateToConfirmed($bookingID) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT booking_status, downpayment_paid 
        FROM bookings 
        WHERE bookingID = ?
    ");
    $stmt->bind_param("i", $bookingID);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    $stmt->close();
    
    if (!$booking) return false;
    
    // Only transition from pending to confirmed
    if ($booking['booking_status'] === 'pending' && $booking['downpayment_paid'] == 1) {
        $updateStmt = $conn->prepare("
            UPDATE bookings 
            SET booking_status = 'confirmed' 
            WHERE bookingID = ?
        ");
        $updateStmt->bind_param("i", $bookingID);
        $success = $updateStmt->execute();
        $updateStmt->close();
        
        if ($success) {
            logBookingAction($bookingID, null, 'status_auto_updated', 'Status automatically changed to confirmed (downpayment paid)');
        }
        
        return $success;
    }
    
    return false;
}

/**
 * Auto-update confirmed bookings to 'post_production' when event date has passed
 */
function updatePastEventsToPostProduction() {
    global $conn;
    
    $stmt = $conn->prepare("
        UPDATE bookings 
        SET booking_status = 'post_production' 
        WHERE booking_status = 'confirmed' 
        AND event_date < CURDATE()
    ");
    
    $success = $stmt->execute();
    $affectedRows = $stmt->affected_rows;
    $stmt->close();
    
    if ($affectedRows > 0) {
        error_log("Auto-updated $affectedRows bookings to post_production");
    }
    
    return $affectedRows;
}

/**
 * Auto-update status to 'completed' when final payment paid AND photos uploaded
 */
function checkAndUpdateToCompleted($bookingID) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT 
            b.booking_status, 
            b.final_payment_paid,
            b.is_fully_paid,
            COUNT(up.photoID) as photo_count
        FROM bookings b
        LEFT JOIN user_photos up ON b.bookingID = up.bookingID
        WHERE b.bookingID = ?
        GROUP BY b.bookingID
    ");
    $stmt->bind_param("i", $bookingID);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    $stmt->close();
    
    if (!$booking) return false;
    
    // Can only transition from post_production to completed
    // Requirements: final payment paid AND photos uploaded
    if (($booking['booking_status'] === 'post_production' || $booking['booking_status'] === 'confirmed') 
        && ($booking['final_payment_paid'] == 1 || $booking['is_fully_paid'] == 1)
        && $booking['photo_count'] > 0) {
        
        $updateStmt = $conn->prepare("
            UPDATE bookings 
            SET booking_status = 'completed' 
            WHERE bookingID = ?
        ");
        $updateStmt->bind_param("i", $bookingID);
        $success = $updateStmt->execute();
        $updateStmt->close();
        
        if ($success) {
            logBookingAction($bookingID, null, 'status_auto_updated', 'Status automatically changed to completed (final payment paid & photos uploaded)');
        }
        
        return $success;
    }
    
    return false;
}

/**
 * Manual cancellation of booking (requires admin action)
 */
function cancelBooking($bookingID, $reason, $adminID) {
    global $conn;
    
    $stmt = $conn->prepare("
        UPDATE bookings 
        SET booking_status = 'cancelled',
            cancellation_reason = ?,
            cancelled_at = NOW(),
            cancelled_by = ?
        WHERE bookingID = ?
    ");
    $stmt->bind_param("sii", $reason, $adminID, $bookingID);
    $success = $stmt->execute();
    $stmt->close();
    
    if ($success) {
        logBookingAction($bookingID, $adminID, 'booking_cancelled', "Booking cancelled. Reason: $reason");
    }
    
    return $success;
}

/**
 * Run daily status checks (call this from cron or on page load)
 */
function runDailyStatusChecks() {
    // Check if already run today
    $lastRun = @file_get_contents(__DIR__ . '/../../cache/last_status_check.txt');
    $today = date('Y-m-d');
    
    if ($lastRun === $today) {
        return false; // Already run today
    }
    
    // Update past events to post_production
    $updated = updatePastEventsToPostProduction();
    
    // Save last run date
    @file_put_contents(__DIR__ . '/../../cache/last_status_check.txt', $today);
    
    return $updated;
}
