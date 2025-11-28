<?php
// Minimal test API - no dependencies
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Just return a simple success message
    echo json_encode([
        'success' => true,
        'message' => 'API is working',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
