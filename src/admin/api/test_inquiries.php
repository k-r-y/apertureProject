<?php
// Test script to verify inquiries API is working
require_once '../../includes/functions/config.php';
require_once '../../includes/functions/session.php';

// Simulate admin session for testing
$_SESSION['userId'] = 1;
$_SESSION['role'] = 'Admin';

header('Content-Type: application/json');

echo "Testing Inquiries API...\n\n";

// Test 1: Check if inquiries table has data
echo "Test 1: Check table contents\n";
$result = $conn->query("SELECT COUNT(*) as count FROM inquiries");
$row = $result->fetch_assoc();
echo "Total inquiries in database: " . $row['count'] . "\n\n";

// Test 2: Try to fetch all inquiries
echo "Test 2: Fetch all inquiries\n";
$result = $conn->query("SELECT * FROM inquiries ORDER BY created_at DESC");
if ($result->num_rows > 0) {
    echo "✓ Successfully fetched " . $result->num_rows . " inquiries\n";
    while ($inq = $result->fetch_assoc()) {
        echo json_encode($inq, JSON_PRETTY_PRINT) . "\n";
    }
} else {
    echo "No inquiries found in database\n";
}

echo "\n\nTest 3: Check rate_limit.php file exists\n";
if (file_exists('../../includes/functions/rate_limit.php')) {
    echo "✓ rate_limit.php exists\n";
} else {
    echo "✗ rate_limit.php NOT FOUND - this may cause API to fail\n";
}

echo "\n\nTest 4: Check activity_logger.php file exists\n";
if (file_exists('../../includes/functions/activity_logger.php')) {
    echo "✓ activity_logger.php exists\n";
} else {
    echo "✗ activity_logger.php NOT FOUND - this may cause get_activity_log to fail\n";
}
?>
