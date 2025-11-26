<?php
require_once '../../includes/functions/config.php';
require_once '../../includes/functions/session.php';
require_once '../../includes/functions/rate_limit.php';

// Enforce Rate Limit
enforceRateLimit(60, 60);

header('Content-Type: application/json');

if (!isset($_SESSION['userId'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $userId = $_SESSION['userId'];
    $action = $_GET['action'] ?? 'get_all';

    if ($action === 'get_all') {
        $sql = "
            (SELECT 
                'Invoice' as type,
                i.invoiceID as id,
                CONCAT('INV-', LPAD(i.invoiceID, 5, '0')) as ref_number,
                b.event_type as description,
                b.total_amount as amount,
                i.issue_date as date,
                i.status as status,
                NULL as proof
            FROM invoices i
            JOIN bookings b ON i.bookingID = b.bookingID
            WHERE b.userID = ?)
            
            UNION ALL
            
            (SELECT 
                'Refund' as type,
                r.refundID as id,
                CONCAT('REF-', LPAD(r.refundID, 5, '0')) as ref_number,
                CONCAT('Refund for Booking #', LPAD(r.bookingID, 6, '0')) as description,
                r.amount as amount,
                r.requested_at as date,
                r.status as status,
                r.proof_image as proof
            FROM refunds r
            JOIN bookings b ON r.bookingID = b.bookingID
            WHERE b.userID = ?)
            
            UNION ALL
            
            (SELECT 
                'Downpayment' as type,
                b.bookingID as id,
                CONCAT('BK-', LPAD(b.bookingID, 5, '0')) as ref_number,
                'Initial Downpayment' as description,
                b.downpayment_amount as amount,
                b.created_at as date,
                'Paid' as status,
                b.proof_payment as proof
            FROM bookings b
            WHERE b.userID = ? AND b.downpayment_amount > 0 AND b.booking_status != 'cancelled')
            
            UNION ALL
            
            (SELECT 
                'Balance Payment' as type,
                b.bookingID as id,
                CONCAT('BK-', LPAD(b.bookingID, 5, '0')) as ref_number,
                'Remaining Balance' as description,
                (b.total_amount - b.downpayment_amount) as amount,
                b.updated_at as date,
                'Paid' as status,
                b.balance_payment_proof as proof
            FROM bookings b
            WHERE b.userID = ? AND b.is_fully_paid = 1)
            
            ORDER BY date DESC
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiii", $userId, $userId, $userId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $transactions = [];
        while ($row = $result->fetch_assoc()) {
            $transactions[] = $row;
        }
        
        echo json_encode(['success' => true, 'transactions' => $transactions]);
    } else {
        throw new Exception("Invalid action");
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
