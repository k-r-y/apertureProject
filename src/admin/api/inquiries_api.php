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
            $result = $conn->query("SELECT * FROM inquiries ORDER BY created_at DESC");
            $inquiries = [];
            while ($row = $result->fetch_assoc()) {
                $inquiries[] = $row;
            }
            echo json_encode(['success' => true, 'inquiries' => $inquiries]);
            break;

        case 'get_activity_log':
            require_once '../../includes/functions/activity_logger.php';
            // Fetch all activities (no user filter)
            $activities = getUserActivities($conn); 
            echo json_encode(['success' => true, 'activities' => $activities]);
            break;

        case 'update_status':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = intval($data['id']);
            $status = $data['status'];

            $stmt = $conn->prepare("UPDATE inquiries SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                throw new Exception("Failed to update status");
            }
            break;

        case 'delete':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = intval($data['id']);

            $stmt = $conn->prepare("DELETE FROM inquiries WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                throw new Exception("Failed to delete inquiry");
            }
            break;

        default:
            throw new Exception("Invalid action");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
