<?php
/**
 * Account Recovery API Endpoint
 * Handles account recovery for archived accounts
 */

require_once '../functions/config.php';
require_once '../functions/session.php';
require_once '../functions/csrf.php';
require_once '../functions/auth.php';

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Validate CSRF token
if (!validateCSRFToken($_POST['csrfToken'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit;
}

// Get input data
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validate input
if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Email and password are required']);
    exit;
}

// Verify email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid email format']);
    exit;
}

try {
    // Check if account exists and is archived
    $stmt = $conn->prepare("SELECT userID, Password, status FROM users WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Account not found']);
        exit;
    }

    // Verify account is archived
    $accountStatus = $user['status'] ?? $user['Status'] ?? null;
    if (!$accountStatus || strtolower($accountStatus) !== 'archived') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Account is not archived']);
        exit;
    }

    // Verify password before allowing recovery (security measure)
    if (!password_verify($password, $user['Password'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Invalid credentials']);
        exit;
    }

    // Recover the account by updating status to 'active'
    $stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE userID = ?");
    $stmt->bind_param("i", $user['userID']);
    
    if ($stmt->execute()) {
        $stmt->close();
        
        // Log the user in
        setSession($user['userID']);
        
        // Determine redirect URL based on role
        $redirectUrl = ($user['Role'] ?? 'User') === 'Admin' ? 'admin/adminDashboard.php' : 'user/user.php';
        
        echo json_encode([
            'success' => true,
            'message' => 'Account recovered successfully',
            'userId' => $user['userID'],
            'redirectUrl' => $redirectUrl
        ]);
    } else {
        $stmt->close();
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to recover account']);
    }

} catch (Exception $e) {
    error_log("Account recovery error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'An error occurred during account recovery']);
}
