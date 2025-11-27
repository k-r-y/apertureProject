<?php
require_once '../../includes/functions/config.php';
require_once '../../includes/functions/session.php';
require_once '../../includes/functions/auth.php';

// Check if user is admin
if (!isset($_SESSION["userId"]) || !isset($_SESSION["role"]) || $_SESSION["role"] !== "Admin") {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

try {
    $userId = $_SESSION['userId'];
    
    // Fetch unread notifications from database
    $stmt = $conn->prepare("
        SELECT notificationID as id, type, title, message, link, created_at 
        FROM notifications 
        WHERE userID = ? AND is_read = 0 
        ORDER BY created_at DESC 
        LIMIT 10
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        // Format relative time
        $created = new DateTime($row['created_at']);
        $now = new DateTime();
        $interval = $now->diff($created);
        
        if ($interval->y > 0) $timeAgo = $interval->y . ' years ago';
        elseif ($interval->m > 0) $timeAgo = $interval->m . ' months ago';
        elseif ($interval->d > 0) $timeAgo = $interval->d . ' days ago';
        elseif ($interval->h > 0) $timeAgo = $interval->h . ' hours ago';
        elseif ($interval->i > 0) $timeAgo = $interval->i . ' mins ago';
        else $timeAgo = 'Just now';
        
        $row['time_ago'] = $timeAgo;
        $notifications[] = $row;
    }
    
    // Get total unread count
    $countStmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE userID = ? AND is_read = 0");
    $countStmt->bind_param("i", $userId);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $unreadCount = $countResult->fetch_row()[0];
    
    echo json_encode([
        'success' => true, 
        'notifications' => $notifications, 
        'count' => $unreadCount
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
