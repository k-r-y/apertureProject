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
    // Fetch bookings that are either fully paid OR completed
    // We need bookingID, client name, event type, and date for the dropdown
    $sql = "
        SELECT 
            b.bookingID, 
            b.event_type, 
            b.event_date, 
            u.FirstName, 
            u.LastName,
            u.email,
            b.gdrive_link
        FROM bookings b
        JOIN users u ON b.userID = u.userID
        WHERE b.is_fully_paid = 1 AND (b.booking_status = 'completed' OR b.booking_status = 'post_production')
        ORDER BY b.event_date DESC
    ";
    
    $result = $conn->query($sql);
    $bookings = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
    }
    
    echo json_encode(['success' => true, 'bookings' => $bookings]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
