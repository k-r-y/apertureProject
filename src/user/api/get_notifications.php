<?php
require_once '../../includes/functions/config.php';
require_once '../../includes/functions/session.php';

if (!isset($_SESSION['userId'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['userId'];

try {
    // Get unread count
    $countStmt = $conn->prepare("SELECT COUNT(*) as count FROM notifications WHERE userID = ? AND is_read = 0");
    $countStmt->bind_param("i", $userId);
    $countStmt->execute();
    $unreadCount = $countStmt->get_result()->fetch_assoc()['count'];
    $countStmt->close();

    // Get notifications (limit 10)
    $stmt = $conn->prepare("SELECT notificationID as id, userID, title, message, type, is_read, created_at, link FROM notifications WHERE userID = ? ORDER BY created_at DESC LIMIT 10");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
    $stmt->close();

    echo json_encode([
        'success' => true,
        'count' => $unreadCount,
        'notifications' => $notifications
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
