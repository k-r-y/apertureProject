<?php
/**
 * Invoicing API
 * Fetches transaction-level data for the invoicing page
 */

require_once '../../includes/functions/config.php';
require_once '../../includes/functions/session.php';

header('Content-Type: application/json');

// Check admin authentication
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'Admin') {
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
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function getAllTransactions($conn) {
    $transactions = [];
    
    // 1. Fetch Downpayments
    $stmt = $conn->prepare("
        SELECT 
            b.bookingID,
            b.userID,
            u.fullName as client_name,
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
            'raw_status' => $row['is_paid'] // For logic
        ];
    }
    $stmt->close();
    
    // 2. Fetch Final Payments
    $stmt = $conn->prepare("
        SELECT 
            b.bookingID,
            b.userID,
            u.fullName as client_name,
            'Final Payment' as type,
            b.balance_amount as amount,
            b.final_payment_paid as is_paid,
            b.final_payment_paid_date as date_paid,
            b.event_date
        FROM bookings b
        JOIN users u ON b.userID = u.userID
        WHERE b.booking_status != 'cancelled'
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $status = $row['is_paid'] ? 'Paid' : 'Pending';
        // For final payment, if not paid, show event date as due date
        $date = $row['date_paid'] ? $row['date_paid'] : $row['event_date'];
        
        $transactions[] = [
            'id' => 'FP-' . $row['bookingID'],
            'booking_id' => $row['bookingID'],
            'type' => 'Final Payment',
            'client_name' => $row['client_name'],
            'amount' => $row['amount'],
            'status' => $status,
            'date' => $date,
            'proof' => null, // Usually no proof for final payment unless uploaded separately
            'raw_status' => $row['is_paid']
        ];
    }
    $stmt->close();
    
    // 3. Fetch Refunds (if any)
    $stmt = $conn->prepare("
        SELECT 
            r.refundID,
            r.bookingID,
            u.fullName as client_name,
            'Refund' as type,
            r.amount,
            r.status,
            r.created_at
        FROM refunds r
        JOIN bookings b ON r.bookingID = b.bookingID
        JOIN users u ON b.userID = u.userID
    ");
    if ($stmt) { // Check if refunds table exists/query works
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

    // Sort by date descending
    usort($transactions, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    
    echo json_encode(['success' => true, 'transactions' => $transactions]);
}

function updateTransactionStatus($conn) {
    // This would handle manual status updates if needed
    // For now, we rely on the confirm_payment.php API
    echo json_encode(['success' => false, 'message' => 'Use confirm payment API']);
}
?>
