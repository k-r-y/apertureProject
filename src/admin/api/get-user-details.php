<?php
/**
 * Get User Details API
 * Fetches complete user information for view/edit modal
 * Admin-only access
 */

require_once '../../includes/functions/config.php';
require_once '../../includes/functions/auth.php';
require_once '../../includes/functions/session.php';

header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['userId']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
}

// Validate input
if (!isset($_GET['userID']) || !is_numeric($_GET['userID'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid user ID']);
    exit;
}

$userID = intval($_GET['userID']);

try {
    // Fetch user details
    $query = "SELECT 
                u.userID,
                u.Email,
                u.FirstName,
                u.LastName,
                u.contactNo,
                u.Role,
                u.Status,
                u.ProfileCompleted,
                u.isVerified,
                u.created_at,
                (SELECT COUNT(*) FROM bookings WHERE userID = u.userID) as total_bookings,
                (SELECT COUNT(*) FROM bookings WHERE userID = u.userID AND booking_status = 'confirmed') as confirmed_bookings
              FROM users u
              WHERE u.userID = ?";
    
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
    $stmt->close();
    
    // Format the response
    $response = [
        'success' => true,
        'user' => [
            'userID' => $user['userID'],
            'email' => $user['Email'],
            'firstName' => $user['FirstName'] ?? '',
            'lastName' => $user['LastName'] ?? '',
            'contactNo' => $user['contactNo'] ?? '',
            'role' => $user['Role'],
            'status' => $user['Status'],
            'profileCompleted' => (bool)$user['ProfileCompleted'],
            'isVerified' => (bool)$user['isVerified'],
            'createdAt' => $user['created_at'],
            'totalBookings' => (int)$user['total_bookings'],
            'confirmedBookings' => (int)$user['confirmed_bookings']
        ]
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    error_log("Error fetching user details: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to fetch user details']);
}
