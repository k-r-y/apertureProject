<?php
require_once '../includes/functions/config.php';
require_once '../includes/functions/session.php';

header('Content-Type: application/json');

// Check admin
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    // Check if column already exists
    $checkResult = $conn->query("SHOW COLUMNS FROM bookings LIKE 'meeting_link'");
    
    if ($checkResult->num_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Column already exists!']);
        exit;
    }
    
    // Add the column
    $sql = "ALTER TABLE `bookings` ADD COLUMN `meeting_link` VARCHAR(500) NULL DEFAULT NULL AFTER `admin_notes`";
    
    if ($conn->query($sql)) {
        echo json_encode([
            'success' => true, 
            'message' => 'Meeting link column added successfully! Refresh your booking modals to see it.'
        ]);
    } else {
        throw new Exception($conn->error);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}
