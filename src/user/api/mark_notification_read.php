<?php
require_once '../../includes/functions/config.php';
require_once '../../includes/functions/session.php';

if (!isset($_SESSION['userId'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['userId'];
$data = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? '';

try {
    if ($action === 'mark_all_read') {
        $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE userID = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['success' => true]);
    } elseif (isset($data['id'])) {
        $notifId = $data['id'];
        $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE notificationID = ? AND userID = ?");
        $stmt->bind_param("ii", $notifId, $userId);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
