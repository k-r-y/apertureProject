<?php
require_once '../../includes/functions/config.php';
require_once '../../includes/functions/session.php';
require_once '../../includes/functions/rate_limit.php';

// Enforce Rate Limit
enforceRateLimit(60, 60);

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'submit_review':
            if (!isset($_SESSION['userId'])) throw new Exception("Unauthorized");
            
            $data = json_decode(file_get_contents('php://input'), true);
            $bookingId = intval($data['bookingId']);
            $rating = intval($data['rating']);
            $comment = trim($data['comment']);
            
            // Verify booking belongs to user and is completed
            $stmt = $conn->prepare("SELECT userID, booking_status FROM bookings WHERE bookingID = ?");
            $stmt->bind_param("i", $bookingId);
            $stmt->execute();
            $booking = $stmt->get_result()->fetch_assoc();
            
            if (!$booking || $booking['userID'] !== $_SESSION['userId']) {
                throw new Exception("Invalid booking");
            }
            if ($booking['booking_status'] !== 'completed') {
                throw new Exception("Can only review completed bookings");
            }

            $stmt = $conn->prepare("INSERT INTO reviews (bookingID, rating, comment) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $bookingId, $rating, $comment);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                throw new Exception("Failed to submit review");
            }
            break;

        case 'get_approved_reviews':
            $sql = "
                SELECT r.rating, r.comment, u.FirstName, u.LastName, b.event_type 
                FROM reviews r
                JOIN bookings b ON r.bookingID = b.bookingID
                JOIN users u ON b.userID = u.userID
                WHERE r.status = 'approved'
                ORDER BY r.created_at DESC
                LIMIT 10
            ";
            $result = $conn->query($sql);
            $reviews = [];
            while ($row = $result->fetch_assoc()) {
                $reviews[] = $row;
            }
            echo json_encode(['success' => true, 'reviews' => $reviews]);
            break;
            
        // Admin Actions
        case 'admin_get_all':
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') throw new Exception("Unauthorized");
            
            $sql = "
                SELECT r.*, u.FirstName, u.LastName, b.event_type 
                FROM reviews r
                JOIN bookings b ON r.bookingID = b.bookingID
                JOIN users u ON b.userID = u.userID
                ORDER BY r.created_at DESC
            ";
            $result = $conn->query($sql);
            $reviews = [];
            while ($row = $result->fetch_assoc()) {
                $reviews[] = $row;
            }
            echo json_encode(['success' => true, 'reviews' => $reviews]);
            break;

        case 'admin_update_status':
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') throw new Exception("Unauthorized");
            
            $data = json_decode(file_get_contents('php://input'), true);
            $id = intval($data['id']);
            $status = $data['status'];
            
            $stmt = $conn->prepare("UPDATE reviews SET status = ? WHERE reviewID = ?");
            $stmt->bind_param("si", $status, $id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                throw new Exception("Failed to update status");
            }
            break;

        default:
            throw new Exception("Invalid action");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
