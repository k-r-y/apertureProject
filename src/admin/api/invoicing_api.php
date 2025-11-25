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
            $sql = "
                SELECT i.*, b.event_type, u.FirstName, u.LastName, b.total_amount 
                FROM invoices i
                JOIN bookings b ON i.bookingID = b.bookingID
                JOIN users u ON b.userID = u.userID
                ORDER BY i.issue_date DESC
            ";
            $result = $conn->query($sql);
            $invoices = [];
            while ($row = $result->fetch_assoc()) {
                $invoices[] = $row;
            }
            echo json_encode(['success' => true, 'invoices' => $invoices]);
            break;

        case 'create_invoice':
            $data = json_decode(file_get_contents('php://input'), true);
            $bookingId = intval($data['bookingId']);
            $dueDate = $data['dueDate'];
            $issueDate = date('Y-m-d');

            $stmt = $conn->prepare("INSERT INTO invoices (bookingID, issue_date, due_date, status) VALUES (?, ?, ?, 'pending')");
            $stmt->bind_param("iss", $bookingId, $issueDate, $dueDate);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                throw new Exception("Failed to create invoice");
            }
            break;

        case 'update_status':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = intval($data['id']);
            $status = $data['status'];

            $stmt = $conn->prepare("UPDATE invoices SET status = ? WHERE invoiceID = ?");
            $stmt->bind_param("si", $status, $id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                throw new Exception("Failed to update status");
            }
            break;
            
        case 'get_bookings_without_invoice':
             $sql = "
                SELECT b.bookingID, b.event_type, b.event_date, u.FirstName, u.LastName 
                FROM bookings b
                JOIN users u ON b.userID = u.userID
                LEFT JOIN invoices i ON b.bookingID = i.bookingID
                WHERE i.invoiceID IS NULL AND b.booking_status != 'cancelled'
            ";
            $result = $conn->query($sql);
            $bookings = [];
            while ($row = $result->fetch_assoc()) {
                $bookings[] = $row;
            }
            echo json_encode(['success' => true, 'bookings' => $bookings]);
            break;

        default:
            throw new Exception("Invalid action");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
