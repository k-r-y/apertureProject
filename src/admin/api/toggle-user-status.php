<?php
/**
 * Toggle User Status API
 * Quick toggle between Active/Inactive status
 * Admin-only access
 */

require_once '../../includes/functions/config.php';
require_once '../../includes/functions/auth.php';
require_once '../../includes/functions/session.php';
require_once '../../includes/functions/csrf.php';

header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['userId']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit;
}

// Validate input
if (!isset($_POST['userID']) || !is_numeric($_POST['userID'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid user ID']);
    exit;
}

$userID = intval($_POST['userID']);

// Prevent admin from toggling their own status
if ($userID === $_SESSION['userId']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Cannot toggle your own account status']);
    exit;
}

try {
    // Get current status
    $query = "SELECT Status FROM users WHERE userID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'User not found']);
        exit;
    }
    
    $user = $result->fetch_assoc();
    $currentStatus = $user['Status'];
    $stmt->close();
    
    // Toggle status
    $newStatus = ($currentStatus === 'Active') ? 'Inactive' : 'Active';
    
    // Update status
    $updateQuery = "UPDATE users SET Status = ? WHERE userID = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param('si', $newStatus, $userID);
    
    if ($updateStmt->execute()) {
        $updateStmt->close();
        
        echo json_encode([
            'success' => true,
            'message' => "User status changed to {$newStatus}",
            'newStatus' => $newStatus
        ]);
    } else {
        throw new Exception($updateStmt->error);
    }
    
} catch (Exception $e) {
    error_log("Error toggling user status: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to toggle user status']);
}
