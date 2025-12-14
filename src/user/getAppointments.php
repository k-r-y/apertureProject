<?php
// Start output buffering to prevent any stray output
ob_start();

require_once '../includes/functions/config.php';
require_once '../includes/functions/session.php';
require_once '../includes/functions/auth.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Disable for production
ini_set('log_errors', 1);

// Set content type header early
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION["userId"])) {
    ob_clean(); // Clear any captured output
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $userId = $_SESSION["userId"];
    $status = isset($_GET['status']) ? $_GET['status'] : 'all';
    
    // Check if proof_payment column exists
    $columnCheck = $conn->query("SHOW COLUMNS FROM bookings LIKE 'proof_payment'");
    $hasProofPaymentColumn = $columnCheck && $columnCheck->num_rows > 0;
    
    // Build SELECT clause based on column existence
    $proofPaymentField = $hasProofPaymentColumn ? "b.proof_payment," : "";
    
    // Base query with JOIN to packages table
    $query = "SELECT 
                b.bookingID,
                b.event_type,
                b.event_date,
                b.event_time_start,
                b.event_time_end,
                b.event_location,
                b.event_theme,
                b.client_message,
                b.total_amount,
                b.downpayment_amount,
                b.booking_status,
                b.is_fully_paid,
                b.balance_payment_proof,
                {$proofPaymentField}
                b.gdrive_link,
                b.meeting_link,
                b.created_at,
                p.packageName as package_name,
                p.description as package_description,
                r.status as refund_status
              FROM bookings b
              LEFT JOIN packages p ON b.packageID = p.packageID
              LEFT JOIN refunds r ON b.bookingID = r.bookingID
              WHERE b.userID = ?";
    
    // Add status filter if not 'all'
    $params = [$userId];
    $types = "i";
    
    if ($status !== 'all') {
        $query .= " AND b.booking_status = ?";
        $params[] = $status;
        $types .= "s";
    }
    
    $query .= " ORDER BY b.event_date DESC, b.created_at DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $appointments = [];
    while ($row = $result->fetch_assoc()) {
        // Format the data
        $appointments[] = [
            'bookingID' => $row['bookingID'],
            'bookingRef' => str_pad($row['bookingID'], 6, '0', STR_PAD_LEFT),
            'eventType' => $row['event_type'],
            'eventDate' => $row['event_date'],
            'eventDateFormatted' => date('F j, Y', strtotime($row['event_date'])),
            'eventTimeStart' => $row['event_time_start'],
            'eventTimeEnd' => $row['event_time_end'],
            'eventTimeFormatted' => date('g:i A', strtotime($row['event_time_start'])) . ' - ' . date('g:i A', strtotime($row['event_time_end'])),
            'eventLocation' => $row['event_location'],
            'eventTheme' => $row['event_theme'],
            'clientMessage' => $row['client_message'],
            'totalAmount' => floatval($row['total_amount']),
            'totalAmountFormatted' => '₱' . number_format($row['total_amount'], 2),
            'downpaymentAmount' => floatval($row['downpayment_amount']),
            'downpaymentFormatted' => '₱' . number_format($row['downpayment_amount'], 2),
            'balanceAmount' => floatval($row['total_amount']) - floatval($row['downpayment_amount']),
            'balanceFormatted' => '₱' . number_format(floatval($row['total_amount']) - floatval($row['downpayment_amount']), 2),
            'bookingStatus' => $row['booking_status'],
            'refund_status' => $row['refund_status'],
            'isFullyPaid' => (bool)$row['is_fully_paid'],
            'balancePaymentProof' => $row['balance_payment_proof'],
            'proofPayment' => $hasProofPaymentColumn ? ($row['proof_payment'] ?? null) : null,
            'gdriveLink' => $row['gdrive_link'],
            'meetingLink' => $row['meeting_link'],
            'packageName' => $row['package_name'],
            'packageDescription' => $row['package_description'],
            'createdAt' => $row['created_at'],
            'createdAtFormatted' => date('F j, Y g:i A', strtotime($row['created_at']))
        ];
    }
    
    $stmt->close();
    
    // Get status counts for all statuses
    $statusCounts = [
        'all' => 0,
        'pending_consultation' => 0,
        'confirmed' => 0,
        'post_production' => 0,
        'completed' => 0,
        'cancelled' => 0,
        'cancellation_pending' => 0
    ];
    
    $countQuery = "SELECT booking_status, COUNT(*) as count 
                   FROM bookings 
                   WHERE userID = ? 
                   GROUP BY booking_status";
    $countStmt = $conn->prepare($countQuery);
    $countStmt->bind_param("i", $userId);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    
    while ($countRow = $countResult->fetch_assoc()) {
        $statusCounts[$countRow['booking_status']] = intval($countRow['count']);
        $statusCounts['all'] += intval($countRow['count']);
    }
    $countStmt->close();
    
    // Return JSON response
    ob_clean(); // Clear any captured output before sending JSON
    echo json_encode([
        'success' => true,
        'appointments' => $appointments,
        'count' => count($appointments),
        'statusCounts' => $statusCounts,
        'lastUpdated' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    error_log("Get Appointments Error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    ob_clean(); // Clear any captured output before sending error JSON
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch appointments',
        'error' => $e->getMessage(), // Temporarily show detailed error for debugging
        'line' => $e->getLine(),
        'file' => basename($e->getFile())
    ]);
}
?>
