<?php
require_once '../../includes/functions/config.php';
require_once '../../includes/functions/session.php';
require_once '../../includes/functions/booking_logic.php';

header('Content-Type: application/json');

// Check authentication
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

try {
    $userId = $_SESSION['userId'];
    $bookingId = intval($_POST['bookingId'] ?? 0);
    
    if (!$bookingId) {
        throw new Exception("Invalid booking ID");
    }

    // Fetch current booking details
    $stmt = $conn->prepare("SELECT booking_status, event_date FROM bookings WHERE bookingID = ? AND userID = ?");
    $stmt->bind_param("ii", $bookingId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    $stmt->close();

    if (!$booking) {
        throw new Exception("Booking not found");
    }

    $status = $booking['booking_status'];
    
    // Define allowed fields based on status
    $allowedFields = [];
    if ($status === 'pending') {
        $allowedFields = ['eventDate', 'startTime', 'endTime', 'location', 'theme', 'message'];
    } elseif ($status === 'confirmed') {
        $allowedFields = ['location', 'theme', 'message'];
    } else {
        throw new Exception("Cannot edit booking with status: " . $status);
    }

    // Prepare update data
    $updates = [];
    $types = "";
    $params = [];

    // Helper to add field to update
    function addUpdate($field, $dbColumn, $type, &$updates, &$types, &$params, $allowedFields) {
        if (in_array($field, $allowedFields) && isset($_POST[$field])) {
            $updates[] = "$dbColumn = ?";
            $types .= $type;
            $params[] = $_POST[$field];
            return true;
        }
        return false;
    }

    // Collect updates
    $dateChanged = addUpdate('eventDate', 'event_date', 's', $updates, $types, $params, $allowedFields);
    $timeChanged = addUpdate('startTime', 'event_time_start', 's', $updates, $types, $params, $allowedFields) || 
                   addUpdate('endTime', 'event_time_end', 's', $updates, $types, $params, $allowedFields);
    
    addUpdate('location', 'event_location', 's', $updates, $types, $params, $allowedFields);
    addUpdate('theme', 'event_theme', 's', $updates, $types, $params, $allowedFields);
    addUpdate('message', 'client_message', 's', $updates, $types, $params, $allowedFields);

    if (empty($updates)) {
        throw new Exception("No changes provided");
    }

    // Validate Date/Time availability if changed (only for pending)
    if (($dateChanged || $timeChanged) && $status === 'pending') {
        $newDate = $_POST['eventDate'] ?? $booking['event_date'];
        
        // Basic date validation
        if (strtotime($newDate) < strtotime('+3 days')) {
            throw new Exception("Event date must be at least 3 days from today");
        }

        // Check availability (excluding current booking)
        $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM bookings WHERE event_date = ? AND booking_status IN ('confirmed', 'pending') AND bookingID != ?");
        $checkStmt->bind_param("si", $newDate, $bookingId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $checkRow = $checkResult->fetch_assoc();
        
        if ($checkRow['count'] >= 1) { // Assuming 1 booking per day limit
            throw new Exception("Selected date is already fully booked");
        }
    }

    // Execute Update
    $query = "UPDATE bookings SET " . implode(", ", $updates) . " WHERE bookingID = ? AND userID = ?";
    $types .= "ii";
    $params[] = $bookingId;
    $params[] = $userId;

    $updateStmt = $conn->prepare($query);
    $updateStmt->bind_param($types, ...$params);

    if ($updateStmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Booking updated successfully']);
    } else {
        throw new Exception("Database update failed: " . $conn->error);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
