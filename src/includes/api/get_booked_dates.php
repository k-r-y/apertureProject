<?php
require_once '../functions/config.php';
require_once '../functions/session.php';
require_once '../functions/api_security.php';

// Apply rate limiting
enforceRateLimit('/api/get_booked_dates', 300, 3600);

header('Content-Type: application/json');

try {
    // Check database connection
    if (!isset($conn) || $conn->connect_error) {
        throw new Exception("Database connection failed");
    }
    
    // Get all booked dates (confirmed and pending bookings)
    $stmt = $conn->prepare("SELECT DISTINCT event_date FROM bookings WHERE booking_status IN ('confirmed', 'pending_consultation')");
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $bookedDates = [];
    
    while ($row = $result->fetch_assoc()) {
        $bookedDates[] = $row['event_date'];
    }
    
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'bookedDates' => $bookedDates
    ]);
    
} catch (Exception $e) {
    error_log("Get booked dates error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Unable to fetch booked dates',
        'bookedDates' => []
    ]);
}
