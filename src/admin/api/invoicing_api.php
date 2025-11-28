<?php
/**
 * Invoicing API
 * Fetches transaction-level data for the invoicing page
 */

// CRITICAL: Disable error display for API endpoints to prevent HTML output
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);

// Start output buffering to catch any stray output
ob_start();

require_once '../../includes/functions/config.php';
require_once '../../includes/functions/session.php';

// Clean buffer
ob_end_clean();
ob_start();

header('Content-Type: application/json');

// Check admin authentication
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'Admin') {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_all':
        getAllTransactions($conn);
        break;
    case 'update_status':
        updateTransactionStatus($conn);
        break;
    default:
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function getAllTransactions($conn) {
    $transactions = [];
    
    // 1. Fetch Downpayments
    $stmt = $conn->prepare("
        SELECT 
            b.bookingID,
            b.userID,
            CONCAT(u.FirstName, ' ', u.LastName) as client_name,
            'Downpayment' as type,
            b.downpayment_amount as amount,
            b.downpayment_paid as is_paid,
            b.downpayment_paid_date as date_paid,
            b.created_at as created_date,
            b.proof_payment
        FROM bookings b
        JOIN users u ON b.userID = u.userID
        WHERE b.booking_status != 'cancelled'
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $status = $row['is_paid'] ? 'Paid' : 'Pending';
        $date = $row['date_paid'] ? $row['date_paid'] : $row['created_date'];
        
        $transactions[] = [
            'id' => 'DP-' . $row['bookingID'],
            'booking_id' => $row['bookingID'],
            'type' => 'Downpayment',
            'client_name' => $row['client_name'],
            'amount' => $row['amount'],
            'status' => $status,
            'date' => $date,
            'proof' => $row['proof_payment'],
            'raw_status' => $row['is_paid']
        ];
    }
    $stmt->close();
    
    // 2. Fetch Final Payments
    $stmt = $conn->prepare("
        SELECT 
            b.bookingID,
            b.userID,
            CONCAT(u.FirstName, ' ', u.LastName) as client_name,
            'Final Payment' as type,
            (b.total_amount - b.downpayment_amount) as amount,
            b.final_payment_paid as is_paid,
            b.final_payment_paid_date as date_paid,
            b.event_date,
            b.proof_final_payment
        FROM bookings b
        JOIN users u ON b.userID = u.userID
        WHERE b.booking_status != 'cancelled'
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $status = $row['is_paid'] ? 'Paid' : 'Pending';
        $date = $row['date_paid'] ? $row['date_paid'] : $row['event_date'];
        
        $transactions[] = [
            'id' => 'FP-' . $row['bookingID'],
            'booking_id' => $row['bookingID'],
            'type' => 'Final Payment',
            'client_name' => $row['client_name'],
            'amount' => $row['amount'],
            'status' => $status,
            'date' => $date,
            'proof' => $row['proof_final_payment'],
            'raw_status' => $row['is_paid']
        ];
    }
    $stmt->close();
    
    // 3. Fetch Refunds (if any)
    try {
        $stmt = $conn->prepare("
            SELECT 
                r.refundID,
                r.bookingID,
                CONCAT(u.FirstName, ' ', u.LastName) as client_name,
                'Refund' as type,
                r.amount,
                r.status,
                r.created_at
            FROM refunds r
            JOIN bookings b ON r.bookingID = b.bookingID
            JOIN users u ON b.userID = u.userID
        ");
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $transactions[] = [
                    'id' => 'RF-' . $row['refundID'],
                    'booking_id' => $row['bookingID'],
                    'type' => 'Refund',
                    'client_name' => $row['client_name'],
                    'amount' => $row['amount'],
                    'status' => ucfirst($row['status']),
                    'date' => $row['created_at'],
                    'proof' => null,
                    'raw_status' => $row['status']
                ];
            }
            $stmt->close();
        }
    } catch (Exception $e) {
        // Refunds table might not exist, ignore
    }

    // Sort by date descending
    usort($transactions, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    
    // Clean ALL output and send JSON
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'transactions' => $transactions]);
    exit;
}

function updateTransactionStatus($conn) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Use confirm payment API']);
    exit;
}
