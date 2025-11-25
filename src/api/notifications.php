<?php
require_once '../includes/functions/config.php';
require_once '../includes/functions/session.php';

// Check if user is logged in
if (!isset($_SESSION['userId'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$userId = $_SESSION['userId'];

try {
    switch ($action) {
        case 'get_unread_count':
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM notifications WHERE userID = ? AND is_read = 0");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            
            echo json_encode(['success' => true, 'count' => (int)$result['count']]);
            break;

        case 'get_all':
            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
            
            $stmt = $conn->prepare("
                SELECT * FROM notifications 
                WHERE userID = ? 
                ORDER BY created_at DESC 
                LIMIT ?
            ");
            $stmt->bind_param("ii", $userId, $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $notifications = [];
            while ($row = $result->fetch_assoc()) {
                $notifications[] = $row;
            }
            
            echo json_encode(['success' => true, 'notifications' => $notifications]);
            break;

        case 'mark_read':
            $notificationId = intval($_POST['notificationId'] ?? 0);
            
            if ($notificationId) {
                $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE notificationID = ? AND userID = ?");
                $stmt->bind_param("ii", $notificationId, $userId);
                $stmt->execute();
            }
            
            echo json_encode(['success' => true]);
            break;

        case 'mark_all_read':
            $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE userID = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            
            echo json_encode(['success' => true]);
            break;

        default:
            throw new Exception("Invalid action");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
