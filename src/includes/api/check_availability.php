<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in JSON response

require_once '../functions/config.php';
require_once '../functions/session.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['available' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$date = $data['date'] ?? null;

if (!$date) {
    echo json_encode(['available' => false, 'message' => 'Date is required']);
    exit;
}

// Validate date format
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    echo json_encode(['available' => false, 'message' => 'Invalid date format']);
    exit;
}

// Check if date is in the past
if (strtotime($date) < strtotime(date('Y-m-d'))) {
    echo json_encode(['available' => false, 'message' => 'Cannot book past dates']);
    exit;
}

try {
    // Check database connection
    if (!isset($conn) || $conn->connect_error) {
        throw new Exception("Database connection failed: " . ($conn->connect_error ?? 'Connection not established'));
    }
    
    // Check for existing confirmed or pending bookings on this date
    // Limit to 2 events per day
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM bookings WHERE event_date = ? AND booking_status IN ('confirmed', 'pending_consultation')");
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("s", $date);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    // Limit to 1 event per day
    $limit = 1;
    
    if ($row['count'] >= $limit) {
        echo json_encode(['available' => false, 'message' => 'Date is fully booked. Only 1 booking allowed per day.']);
    } else {
        echo json_encode([
            'available' => true, 
            'message' => "Date is available"
        ]);
    }
    
} catch (Exception $e) {
    error_log("Availability check error: " . $e->getMessage());
    error_log("Date requested: " . $date);
    http_response_code(500);
    echo json_encode(['available' => false, 'message' => 'Server error checking availability. Please try again.']);
}
?>
