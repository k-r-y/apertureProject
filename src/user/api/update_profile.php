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

$action = $_GET['action'] ?? '';
$userId = $_SESSION['userId'];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if ($action === 'update_info') {
        // Update Personal Information
        $firstName = trim($data['firstName'] ?? '');
        $lastName = trim($data['lastName'] ?? '');
        $phone = trim($data['phone'] ?? '');

        if (empty($firstName) || empty($lastName)) {
            throw new Exception('First Name and Last Name are required');
        }

        // Validate phone number - Required, 11 digits, must start with 09
        if (empty($phone)) {
            throw new Exception('Contact number is required');
        } else if (!preg_match('/^09[0-9]{9}$/', $phone)) {
            throw new Exception('Contact number must be 11 digits and start with 09 (e.g., 09171234567)');
        }

        $stmt = $conn->prepare("UPDATE users SET FirstName = ?, LastName = ?, contactNo = ? WHERE userID = ?");
        $stmt->bind_param("sssi", $firstName, $lastName, $phone, $userId);
        
        if ($stmt->execute()) {
            // Update ALL session variables to reflect changes
            $_SESSION['firstName'] = $firstName;
            $_SESSION['lastName'] = $lastName;
            $_SESSION['contact'] = $phone;
            
            echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
        } else {
            throw new Exception('Failed to update profile');
        }

    } elseif ($action === 'change_password') {
        // Change Password
        $currentPassword = $data['currentPassword'] ?? '';
        $newPassword = $data['newPassword'] ?? '';
        $confirmPassword = $data['confirmPassword'] ?? '';

        if (empty($currentPassword) || empty($newPassword)) {
            throw new Exception('All fields are required');
        }

        if ($newPassword !== $confirmPassword) {
            throw new Exception('New passwords do not match');
        }

        if (strlen($newPassword) < 8) {
            throw new Exception('Password must be at least 8 characters');
        }
        
        if (!preg_match('/[A-Z]/', $newPassword)) {
            throw new Exception('Password must contain at least one uppercase letter');
        }
        
        if (!preg_match('/[a-z]/', $newPassword)) {
            throw new Exception('Password must contain at least one lowercase letter');
        }
        
        if (!preg_match('/[0-9]/', $newPassword)) {
            throw new Exception('Password must contain at least one number');
        }

        // Verify current password
        $stmt = $conn->prepare("SELECT password FROM users WHERE userID = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user || !password_verify($currentPassword, $user['password'])) {
            throw new Exception('Incorrect current password');
        }

        // Check if new password is same as current password
        if (password_verify($newPassword, $user['password'])) {
            throw new Exception('New password cannot be the same as your current password');
        }

        // Update to new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE userID = ?");
        $updateStmt->bind_param("si", $hashedPassword, $userId);

        if ($updateStmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
        } else {
            throw new Exception('Failed to update password');
        }

    } else {
        throw new Exception('Invalid action');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
