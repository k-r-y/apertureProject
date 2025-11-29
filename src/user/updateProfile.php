<?php
session_start();
require_once '../includes/functions/config.php';
require_once '../includes/functions/auth.php';
require_once '../includes/functions/csrf.php';

header('Content-Type: application/json');

// Check if user is logged in and is a User
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'User' || !$_SESSION['isVerified']) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !validateToken($_POST['csrf_token'])) {
   http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid security token. Please refresh the page and try again.']);
    exit;
}

$action = $_POST['action'] ?? '';
$userId = $_SESSION['userId'];

try {
    switch ($action) {
        case 'update_profile':
            updatePersonalInfo($conn, $userId);
            break;
        
        case 'change_password':
            changePassword($conn, $userId);
            break;
        
        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}

// ========================================
// UPDATE PERSONAL INFORMATION
// ========================================
function updatePersonalInfo($conn, $userId) {
    // Validate and sanitize inputs
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    
    // Validation
    $errors = [];
    
    if (empty($firstName)) {
        $errors[] = 'First name is required';
    } elseif (strlen($firstName) < 2 || strlen($firstName) > 50) {
        $errors[] = 'First name must be between 2 and 50 characters';
    } elseif (!preg_match("/^[a-zA-Z\s'-]+$/", $firstName)) {
        $errors[] = 'First name contains invalid characters';
    }
    
    if (empty($lastName)) {
        $errors[] = 'Last name is required';
    } elseif (strlen($lastName) < 2 || strlen($lastName) > 50) {
        $errors[] = 'Last name must be between 2 and 50 characters';
    } elseif (!preg_match("/^[a-zA-Z\s'-]+$/", $lastName)) {
        $errors[] = 'Last name contains invalid characters';
    }
    
    if (!empty($phone)) {
        // Remove spaces, dashes, and parentheses
        $phone = preg_replace('/[\s\-\(\)]/', '', $phone);
        
        // Validate Philippine phone number format (09XX-XXX-XXXX or +639XX-XXX-XXXX)
        if (!preg_match('/^(09|\+639)\d{9}$/', $phone)) {
            $errors[] = 'Invalid phone number format. Use format: 09XX-XXX-XXXX';
        }
    }
    
    if (count($errors) > 0) {
        throw new Exception(implode(', ', $errors));
    }
    
    // Update database
    $stmt = $conn->prepare("UPDATE users SET fname = ?, lname = ?, contactNo = ? WHERE userID = ?");
    $stmt->bind_param("ssss", $firstName, $lastName, $phone, $userId);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to update profile. Please try again.');
    }
    
    // Update session variables
    $_SESSION['firstName'] = $firstName;
    $_SESSION['lastName'] = $lastName;
    $_SESSION['fullName'] = $firstName . ' ' . $lastName;
    $_SESSION['contact'] = $phone;
    
    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully!',
        'data' => [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'fullName' => $_SESSION['fullName'],
            'phone' => $phone
        ]
    ]);
    exit;
}

// ========================================
// CHANGE PASSWORD
// ========================================
function changePassword($conn, $userId) {
    $currentPassword = $_POST['currentPassword'] ?? '';
    $newPassword = $_POST['newPassword'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    
    // Validation
    $errors = [];
    
    if (empty($currentPassword)) {
        $errors[] = 'Current password is required';
    }
    
    if (empty($newPassword)) {
        $errors[] = 'New password is required';
    } elseif (strlen($newPassword) < 8) {
        $errors[] = 'New password must be at least 8 characters long';
    } elseif (!preg_match('/[A-Z]/', $newPassword)) {
        $errors[] = 'New password must contain at least one uppercase letter';
    } elseif (!preg_match('/[a-z]/', $newPassword)) {
        $errors[] = 'New password must contain at least one lowercase letter';
    } elseif (!preg_match('/[0-9]/', $newPassword)) {
        $errors[] = 'New password must contain at least one number';
    }
    
    if ($newPassword !== $confirmPassword) {
        $errors[] = 'New passwords do not match';
    }
    
    if ($currentPassword === $newPassword) {
        $errors[] = 'New password must be different from current password';
    }
    
    if (count($errors) > 0) {
        throw new Exception(implode(', ', $errors));
    }
    
    // Verify current password
    $stmt = $conn->prepare("SELECT password FROM users WHERE userID = ?");
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('User not found');
    }
    
    $user = $result->fetch_assoc();
    
    if (!password_verify($currentPassword, $user['password'])) {
        throw new Exception('Current password is incorrect');
    }
    
    // Hash new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    // Update password
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE userID = ?");
    $stmt->bind_param("ss", $hashedPassword, $userId);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to update password. Please try again.');
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Password changed successfully!'
    ]);
    exit;
}

?>
