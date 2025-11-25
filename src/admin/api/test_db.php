<?php
/**
 * Test API - Simple database connection test
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../includes/functions/config.php';
require_once '../../includes/functions/session.php';

header('Content-Type: application/json');

// Check admin authentication
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'Admin') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Access global database connection
global $conn;

try {
    // Simple test query
    $testQuery = "SELECT COUNT(*) as count FROM bookings";
    $result = $conn->query($testQuery);
    
    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }
    
    $data = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'message' => 'Database connection working!',
        'total_bookings' => $data['count'],
        'connection_status' => 'OK'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>
