<?php
header('Content-Type: application/json');
require_once '../../includes/functions/config.php';
require_once '../../includes/functions/session.php';
require_once '../../includes/functions/auth.php';

// Check if user is admin
if (!isset($_SESSION["userId"]) || !isset($_SESSION["role"]) || $_SESSION["role"] !== "Admin") {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $bookings = [];

    // Select bookings that are either 'post_production' or 'completed'
    // Join with users to get client names
    $query = "
        SELECT 
            b.bookingID, 
            b.event_date, 
            b.event_type, 
            b.gdrive_link, 
            u.FirstName, 
            u.LastName 
        FROM bookings b 
        JOIN users u ON b.userID = u.userID 
        WHERE b.booking_status IN ('post_production', 'completed')
        ORDER BY b.event_date DESC
    ";

    $result = $conn->query($query);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
    } else {
        throw new Exception("Database query failed: " . $conn->error);
    }

    echo json_encode(['success' => true, 'bookings' => $bookings]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
