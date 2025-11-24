<?php
/**
 * Resend Verification Email API
 * Admin can manually trigger verification email resend
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

try {
    // Get user email and verification status
    $query = "SELECT Email, isVerified FROM users WHERE userID = ?";
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
    $email = $user['Email'];
    $isVerified = $user['isVerified'];
    $stmt->close();
    
    // Check if already verified
    if ($isVerified) {
        echo json_encode([
            'success' => false,
            'error' => 'User is already verified'
        ]);
        exit;
    }
    
    // Generate new verification code
    $code = createCode($email);
    
    if ($code === false) {
        throw new Exception("Failed to create verification code");
    }
    
    // Send verification email
    $emailSent = sendVerificationEmailWithCode($email, $code);
    
    if ($emailSent) {
        echo json_encode([
            'success' => true,
            'message' => 'Verification email sent successfully'
        ]);
    } else {
        throw new Exception("Failed to send verification email");
    }
    
} catch (Exception $e) {
    error_log("Error resending verification: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to send verification email']);
}
