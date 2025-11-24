<?php
/**
 * Update User API
 * Updates user information (Status, Role, Profile Completion, Verification)
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
$status = $_POST['status'] ?? null;
$role = $_POST['role'] ?? null;
$profileCompleted = isset($_POST['profileCompleted']) ? (int)$_POST['profileCompleted'] : null;
$isVerified = isset($_POST['isVerified']) ? (int)$_POST['isVerified'] : null;

// Validate status
if ($status !== null && !in_array($status, ['Active', 'Inactive'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid status value']);
    exit;
}

// Validate role
if ($role !== null && !in_array($role, ['User', 'Admin'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid role value']);
    exit;
}

// Prevent admin from deactivating their own account
if ($userID === $_SESSION['userId'] && $status === 'Inactive') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Cannot deactivate your own account']);
    exit;
}

try {
    // Build update query dynamically based on provided fields
    $updateFields = [];
    $params = [];
    $types = '';
    
    if ($status !== null) {
        $updateFields[] = "Status = ?";
        $params[] = $status;
        $types .= 's';
    }
    
    if ($role !== null) {
        $updateFields[] = "Role = ?";
        $params[] = $role;
        $types .= 's';
    }
    
    if ($profileCompleted !== null) {
        $updateFields[] = "ProfileCompleted = ?";
        $params[] = $profileCompleted;
        $types .= 'i';
    }
    
    if ($isVerified !== null) {
        $updateFields[] = "isVerified = ?";
        $params[] = $isVerified;
        $types .= 'i';
    }
    
    if (empty($updateFields)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'No fields to update']);
        exit;
    }
    
    // Add userID to params
    $params[] = $userID;
    $types .= 'i';
    
    $query = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE userID = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $stmt->close();
            
            // Fetch updated user data
            $fetchQuery = "SELECT userID, Email, FirstName, LastName, Role, Status, ProfileCompleted, isVerified 
                          FROM users WHERE userID = ?";
            $fetchStmt = $conn->prepare($fetchQuery);
            $fetchStmt->bind_param('i', $userID);
            $fetchStmt->execute();
            $result = $fetchStmt->get_result();
            $updatedUser = $result->fetch_assoc();
            $fetchStmt->close();
            
            echo json_encode([
                'success' => true,
                'message' => 'User updated successfully',
                'user' => [
                    'userID' => $updatedUser['userID'],
                    'email' => $updatedUser['Email'],
                    'firstName' => $updatedUser['FirstName'] ?? '',
                    'lastName' => $updatedUser['LastName'] ?? '',
                    'role' => $updatedUser['Role'],
                    'status' => $updatedUser['Status'],
                    'profileCompleted' => (bool)$updatedUser['ProfileCompleted'],
                    'isVerified' => (bool)$updatedUser['isVerified']
                ]
            ]);
        } else {
            echo json_encode(['success' => true, 'message' => 'No changes made']);
        }
    } else {
        throw new Exception($stmt->error);
    }
    
} catch (Exception $e) {
    error_log("Error updating user: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to update user']);
}
