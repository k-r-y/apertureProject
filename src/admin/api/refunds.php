<?php
require_once '../../includes/functions/config.php';
require_once '../../includes/functions/session.php';
require_once '../../includes/functions/rate_limit.php';

// Enforce Rate Limit
enforceRateLimit(60, 60);

// Ensure user is logged in and is an Admin
if (!isset($_SESSION['userId']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get_all':
            $status = $_GET['status'] ?? 'all';
            
            $sql = "
                SELECT r.*, b.bookingRef, b.event_type, b.event_date, 
                       u.FirstName, u.LastName, u.Email
                FROM refunds r
                JOIN bookings b ON r.bookingID = b.bookingID
                JOIN users u ON b.userID = u.userID
            ";
            
            if ($status !== 'all') {
                $sql .= " WHERE r.status = ?";
                $stmt = $conn->prepare($sql . " ORDER BY r.requested_at DESC");
                $stmt->bind_param("s", $status);
            } else {
                $stmt = $conn->prepare($sql . " ORDER BY r.requested_at DESC");
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            $refunds = [];
            
            while ($row = $result->fetch_assoc()) {
                $refunds[] = $row;
            }
            
            echo json_encode(['success' => true, 'refunds' => $refunds]);
            break;

        case 'update_status':
            $data = json_decode(file_get_contents('php://input'), true);
            $refundId = intval($data['refundId']);
            $status = $data['status'];
            $notes = $data['notes'] ?? null;
            
            // Validate status
            $validStatuses = ['pending', 'approved', 'processed', 'rejected'];
            if (!in_array($status, $validStatuses)) {
                throw new Exception("Invalid status");
            }
            
            $processedAt = ($status === 'processed') ? date('Y-m-d H:i:s') : null;
            
            $stmt = $conn->prepare("
                UPDATE refunds 
                SET status = ?, notes = ?, processed_at = ?, processed_by = ?
                WHERE refundID = ?
            ");
            $stmt->bind_param("sssii", $status, $notes, $processedAt, $_SESSION['userId'], $refundId);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Refund status updated successfully']);
            } else {
                throw new Exception("Failed to update refund status");
            }
            break;

        default:
            throw new Exception("Invalid action");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
