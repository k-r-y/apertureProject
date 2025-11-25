<?php
require_once '../../includes/functions/config.php';
require_once '../../includes/functions/session.php';
require_once '../../includes/functions/rate_limit.php';

// Enforce Rate Limit (60 requests per minute)
enforceRateLimit(60, 60);

// Ensure user is logged in and is an Admin
if (!isset($_SESSION['userId']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$adminId = $_SESSION['userId'];

try {
    switch ($action) {
        // ==========================================
        // NOTES
        // ==========================================
        case 'get_notes':
            $userId = intval($_GET['userId']);
            $stmt = $conn->prepare("
                SELECT n.*, u.FirstName, u.LastName 
                FROM client_notes n 
                JOIN users u ON n.adminID = u.userID 
                WHERE n.userID = ? 
                ORDER BY n.created_at DESC
            ");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $notes = [];
            while ($row = $result->fetch_assoc()) {
                $notes[] = $row;
            }
            echo json_encode(['success' => true, 'notes' => $notes]);
            break;

        case 'add_note':
            $data = json_decode(file_get_contents('php://input'), true);
            $userId = intval($data['userId']);
            $note = trim($data['note']);
            
            if (empty($note)) throw new Exception("Note cannot be empty");

            $stmt = $conn->prepare("INSERT INTO client_notes (userID, adminID, note) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $userId, $adminId, $note);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                throw new Exception("Failed to add note");
            }
            break;

        // ==========================================
        // TAGS
        // ==========================================
        case 'get_all_tags':
            $result = $conn->query("SELECT * FROM tags ORDER BY tag_name ASC");
            $tags = [];
            while ($row = $result->fetch_assoc()) {
                $tags[] = $row;
            }
            echo json_encode(['success' => true, 'tags' => $tags]);
            break;

        case 'get_tags':
            $userId = intval($_GET['userId']);
            $stmt = $conn->prepare("
                SELECT t.* 
                FROM tags t 
                JOIN client_tags ct ON t.tagID = ct.tagID 
                WHERE ct.userID = ?
            ");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $tags = [];
            while ($row = $result->fetch_assoc()) {
                $tags[] = $row;
            }
            echo json_encode(['success' => true, 'tags' => $tags]);
            break;

        case 'add_tag':
            $data = json_decode(file_get_contents('php://input'), true);
            $userId = intval($data['userId']);
            $tagId = intval($data['tagId']);

            $stmt = $conn->prepare("INSERT IGNORE INTO client_tags (userID, tagID) VALUES (?, ?)");
            $stmt->bind_param("ii", $userId, $tagId);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                throw new Exception("Failed to assign tag");
            }
            break;

        case 'remove_tag':
            $data = json_decode(file_get_contents('php://input'), true);
            $userId = intval($data['userId']);
            $tagId = intval($data['tagId']);

            $stmt = $conn->prepare("DELETE FROM client_tags WHERE userID = ? AND tagID = ?");
            $stmt->bind_param("ii", $userId, $tagId);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                throw new Exception("Failed to remove tag");
            }
            break;

        // ==========================================
        // COMMUNICATION LOGS
        // ==========================================
        case 'get_communications':
            $userId = intval($_GET['userId']);
            $stmt = $conn->prepare("
                SELECT c.*, u.FirstName, u.LastName 
                FROM communication_logs c 
                JOIN users u ON c.adminID = u.userID 
                WHERE c.userID = ? 
                ORDER BY c.communication_date DESC
            ");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $logs = [];
            while ($row = $result->fetch_assoc()) {
                $logs[] = $row;
            }
            echo json_encode(['success' => true, 'communications' => $logs]);
            break;

        case 'log_communication':
            $data = json_decode(file_get_contents('php://input'), true);
            $userId = intval($data['userId']);
            $type = $data['type'];
            $subject = trim($data['subject']);
            $notes = trim($data['notes']);
            $date = $data['date'];

            if (empty($type) || empty($date)) throw new Exception("Type and Date are required");

            $stmt = $conn->prepare("INSERT INTO communication_logs (userID, adminID, type, subject, notes, communication_date) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iissss", $userId, $adminId, $type, $subject, $notes, $date);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                throw new Exception("Failed to log communication");
            }
            break;

        default:
            throw new Exception("Invalid action");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
