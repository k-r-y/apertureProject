<?php
/**
 * Change Password API
 * Handles password changes for authenticated users with validation
 */

require_once '../../includes/functions/config.php';
require_once '../../includes/functions/auth.php';
require_once '../../includes/functions/csrf.php';
require_once '../../includes/functions/session.php';
require_once '../../includes/functions/logger.php';

header('Content-Type: application/json');

// Ensure user is logged in
if (!isset($_SESSION['userId'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

$currentPassword = trim($input['currentPassword'] ?? '');
$newPassword = trim($input['newPassword'] ?? '');
$confirmPassword = trim($input['confirmPassword'] ?? '');
$userId = $_SESSION['userId'];

$errors = [];

// Validate inputs
if (empty($currentPassword)) {
    $errors['currentPassword'] = 'Current password is required';
}

if (empty($newPassword)) {
    $errors['newPassword'] = 'New password is required';
} else if (strlen($newPassword) < 8) {
    $errors['newPassword'] = 'Password must be at least 8 characters';
} else if (!preg_match('/[A-Z]/', $newPassword)) {
    $errors['newPassword'] = 'Password must contain at least one uppercase letter';
} else if (!preg_match('/[a-z]/', $newPassword)) {
    $errors['newPassword'] = 'Password must contain at least one lowercase letter';
} else if (!preg_match('/[0-9]/', $newPassword)) {
    $errors['newPassword'] = 'Password must contain at least one number';
}

if ($newPassword !== $confirmPassword) {
    $errors['confirmPassword'] = 'Passwords do not match';
}

// Verify current password and check if new password is different
if (empty($errors)) {
    $stmt = $conn->prepare("SELECT Password, Email FROM users WHERE userID = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $row = $result->fetch_assoc()) {
        // Verify current password
        if (!password_verify($currentPassword, $row['Password'])) {
            $errors['currentPassword'] = 'Current password is incorrect';
        }
        
        // Check if new password is same as current
        if (password_verify($newPassword, $row['Password'])) {
            $errors['newPassword'] = 'New password cannot be the same as your current password';
        }
        
        $userEmail = $row['Email'];
    } else {
        $errors['general'] = 'User not found';
    }
}

// If validation passes, update password
if (empty($errors)) {
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET Password = ? WHERE userID = ?");
    $stmt->bind_param("si", $hashedPassword, $userId);
    
    if ($stmt->execute()) {
        // Log the password change (wrapped in try-catch to prevent logging errors)
        try {
            Logger::security('Password changed', [
                'user_id' => $userId,
                'email' => $userEmail
            ]);
        } catch (Exception $e) {
            // Logging failed but password was updated successfully
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Database error: Failed to update password'
        ]);
    }
} else {
    http_response_code(400);
    // Return first error message as main message
    $firstError = reset($errors);
    echo json_encode([
        'success' => false,
        'message' => $firstError,
        'errors' => $errors
    ]);
}
?>
