<?php
require_once '../../includes/functions/config.php';
require_once '../../includes/functions/session.php';

header('Content-Type: application/json');

// Check admin access
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'Admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';
$adminId = $_SESSION['userId'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    switch ($action) {
        case 'add_note':
            $userId = $input['userId'] ?? 0;
            $note = $input['note'] ?? '';
            
            if (empty($note)) {
                echo json_encode(['success' => false, 'message' => 'Note cannot be empty']);
                exit;
            }
            
            $stmt = $conn->prepare("INSERT INTO client_notes (userID, adminID, note) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $userId, $adminId, $note);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Note added successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Database error']);
            }
            break;
            
        case 'add_tag':
            $userId = $input['userId'] ?? 0;
            $tagId = $input['tagId'] ?? 0;
            
            $stmt = $conn->prepare("INSERT IGNORE INTO user_tags (userID, tagID, assigned_by) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $userId, $tagId, $adminId);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Tag assigned successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Database error']);
            }
            break;
            
        case 'remove_tag':
            $userId = $input['userId'] ?? 0;
            $tagId = $input['tagId'] ?? 0;
            
            $stmt = $conn->prepare("DELETE FROM user_tags WHERE userID = ? AND tagID = ?");
            $stmt->bind_param("ii", $userId, $tagId);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Tag removed successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Database error']);
            }
            break;
            
        case 'create_tag':
            $tagName = $input['tagName'] ?? '';
            $tagColor = $input['tagColor'] ?? '#d4af37';
            
            if (empty($tagName)) {
                echo json_encode(['success' => false, 'message' => 'Tag name cannot be empty']);
                exit;
            }
            
            $stmt = $conn->prepare("INSERT INTO client_tags (tag_name, tag_color) VALUES (?, ?)");
            $stmt->bind_param("ss", $tagName, $tagColor);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'tagId' => $conn->insert_id, 'message' => 'Tag created successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Tag already exists or database error']);
            }
            break;
            
        case 'log_communication':
            $userId = $input['userId'] ?? 0;
            $type = $input['type'] ?? 'email';
            $subject = $input['subject'] ?? '';
            $notes = $input['notes'] ?? '';
            $date = $input['date'] ?? date('Y-m-d H:i:s');
            
            $stmt = $conn->prepare("INSERT INTO communication_log (userID, adminID, type, subject, notes, communication_date) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iissss", $userId, $adminId, $type, $subject, $notes, $date);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Communication logged successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Database error']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $userId = $_GET['userId'] ?? 0;
    
    switch ($action) {
        case 'get_notes':
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
            
        case 'get_tags':
            $stmt = $conn->prepare("
                SELECT t.*, ut.assigned_at
                FROM user_tags ut
                JOIN client_tags t ON ut.tagID = t.tagID
                WHERE ut.userID = ?
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
            
        case 'get_all_tags':
            $result = $conn->query("SELECT * FROM client_tags ORDER BY tag_name");
            $tags = [];
            while ($row = $result->fetch_assoc()) {
                $tags[] = $row;
            }
            
            echo json_encode(['success' => true, 'tags' => $tags]);
            break;
            
        case 'get_communications':
            $stmt = $conn->prepare("
                SELECT c.*, u.FirstName, u.LastName
                FROM communication_log c
                JOIN users u ON c.adminID = u.userID
                WHERE c.userID = ?
                ORDER BY c.communication_date DESC
            ");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $comms = [];
            while ($row = $result->fetch_assoc()) {
                $comms[] = $row;
            }
            
            echo json_encode(['success' => true, 'communications' => $comms]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
