<?php
require_once '../../includes/functions/config.php';
require_once '../../includes/functions/session.php';
require_once '../../includes/functions/auth.php';

// Check if user is logged in
if (!isset($_SESSION['userId'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$userId = $_SESSION['userId'];

try {
    // Update user status to 'archived' instead of deleting
    $stmt = $conn->prepare("UPDATE users SET status = 'archived' WHERE userID = ?");
    $stmt->bind_param("i", $userId);
    
    if ($stmt->execute()) {
        // Log the user out by destroying session
        session_destroy();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Account deletion requested. Your account has been archived and you will be logged out.'
        ]);
    } else {
        throw new Exception('Failed to archive account');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
