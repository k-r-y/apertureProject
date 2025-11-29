<?php
require_once '../includes/functions/config.php';
require_once '../includes/functions/session.php';
require_once '../includes/functions/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['userId'] ?? null;

if (!$userId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'User ID is required']);
    exit;
}

try {
    // Get user info before recovery
    $stmt = $conn->prepare("SELECT Role, isVerified FROM users WHERE userID = ? AND status = 'archived'");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if (!$user) {
        throw new Exception('Account not found or not archived');
    }
    
    // Recover the account by setting status to 'active'
    $updateStmt = $conn->prepare("UPDATE users SET status = 'active' WHERE userID = ?");
    $updateStmt->bind_param("i", $userId);
    
    if ($updateStmt->execute()) {
        // Clear the archived account session data
        if (isset($_SESSION['archived_account'])) {
            unset($_SESSION['archived_account']);
        }
        
        // Set full user session (auto-login)
        setSession($userId);
        
        // Determine redirect URL based on role and profile completion
        $isProfileCompleted = isProfileCompleted($userId);
        
        if ($user['Role'] === 'Admin') {
            $redirectUrl = 'admin/adminDashboard.php';
        } else {
            if ($isProfileCompleted) {
                $redirectUrl = 'user/user.php';
            } else {
                $redirectUrl = 'completeProfile.php';
            }
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Account recovered successfully!',
            'redirect' => $redirectUrl
        ]);
    } else {
        throw new Exception('Failed to recover account');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
