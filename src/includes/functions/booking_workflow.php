<?php
require_once 'config.php';

/**
 * Update booking status and log the action
 */
function updateBookingStatus($bookingId, $newStatus, $userId) {
    global $conn;
    
    // Validate status
    $validStatuses = ['pending', 'confirmed', 'post_production', 'completed', 'cancelled'];
    if (!in_array($newStatus, $validStatuses)) {
        return ['success' => false, 'message' => 'Invalid status'];
    }

    // Get current status for logging
    $stmt = $conn->prepare("SELECT booking_status FROM bookings WHERE bookingID = ?");
    $stmt->bind_param("i", $bookingId);
    $stmt->execute();
    $result = $stmt->get_result();
    $currentStatus = $result->fetch_assoc()['booking_status'];
    $stmt->close();

    if ($currentStatus === $newStatus) {
        return ['success' => false, 'message' => 'Status is already ' . $newStatus];
    }

    // Update status
    $stmt = $conn->prepare("UPDATE bookings SET booking_status = ? WHERE bookingID = ?");
    $stmt->bind_param("si", $newStatus, $bookingId);
    
    if ($stmt->execute()) {
        // Log the change
        logBookingAction($bookingId, $userId, 'status_change', "Changed status from $currentStatus to $newStatus");
        
        // Send email notification
        try {
            require_once 'notifications.php';
            $notification = new NotificationSystem();
            
            // Get client details
            $stmt = $conn->prepare("SELECT u.Email, u.FirstName, b.event_date, b.bookingID 
                                     FROM bookings b 
                                     JOIN users u ON b.userID = u.userID 
                                     WHERE b.bookingID = ?");
            $stmt->bind_param("i", $bookingId);
            $stmt->execute();
            $client = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            if ($client) {
                $notification->sendStatusUpdate(
                    $client['Email'],
                    $client['FirstName'],
                    $client['bookingID'],
                    $newStatus,
                    date('M d, Y', strtotime($client['event_date']))
                );
                
                // Create in-app notification
                $clientStmt = $conn->prepare("SELECT userID FROM bookings WHERE bookingID = ?");
                $clientStmt->bind_param("i", $bookingId);
                $clientStmt->execute();
                $clientUser = $clientStmt->get_result()->fetch_assoc();
                
                if ($clientUser) {
                    createNotification(
                        $clientUser['userID'],
                        'booking_status',
                        'Booking Status Updated',
                        "Your booking #{$bookingId} status has been updated to " . str_replace('_', ' ', ucwords($newStatus)),
                        'appointments.php'
                    );
                }
            }
        } catch (Exception $e) {
            // Don't fail the status update if email fails
            error_log("Notification error: " . $e->getMessage());
        }
        
        return ['success' => true, 'message' => 'Status updated successfully'];
    } else {
        return ['success' => false, 'message' => 'Database error: ' . $conn->error];
    }
}

/**
 * Add an admin note to a booking
 */
function addBookingNote($bookingId, $note, $userId) {
    global $conn;
    
    $stmt = $conn->prepare("UPDATE bookings SET admin_notes = ? WHERE bookingID = ?");
    $stmt->bind_param("si", $note, $bookingId);
    
    if ($stmt->execute()) {
        logBookingAction($bookingId, $userId, 'note_updated', 'Updated admin notes');
        return ['success' => true, 'message' => 'Note updated successfully'];
    } else {
        return ['success' => false, 'message' => 'Database error: ' . $conn->error];
    }
}

/**
 * Log an action related to a booking
 */
function logBookingAction($bookingId, $userId, $action, $details) {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO booking_logs (bookingID, userID, action, details) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $bookingId, $userId, $action, $details);
    $stmt->execute();
    $stmt->close();
}

/**
 * Get all logs for a booking
 */
function getBookingLogs($bookingId) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT l.*, u.FirstName, u.LastName, u.Role 
        FROM booking_logs l 
        LEFT JOIN users u ON l.userID = u.userID 
        WHERE l.bookingID = ? 
        ORDER BY l.created_at DESC
    ");
    $stmt->bind_param("i", $bookingId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $logs = [];
    while ($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }
    return $logs;
}

/**
 * Create an in-app notification for a user
 */
function createNotification($userId, $type, $title, $message, $link = null) {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO notifications (userID, type, title, message, link) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $userId, $type, $title, $message, $link);
    $stmt->execute();
    $stmt->close();
}
?>
