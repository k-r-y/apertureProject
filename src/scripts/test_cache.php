<?php
/**
 * Test Cache System
 * Simple test script to verify cache is working
 */

require_once __DIR__ . '/../includes/functions/cache.php';

echo "=== Cache System Test ===\n\n";

// Test 1: Set and Get
echo "Test 1: Set and Get\n";
$testData = [
    'message' => 'Hello Cache!',
    'timestamp' => time(),
    'data' => [1, 2, 3, 4, 5]
];

Cache::set('test_key', $testData, 60);
$retrieved = Cache::get('test_key');

if ($retrieved === $testData) {
    echo "✓ PASS: Data stored and retrieved successfully\n\n";
} else {
    echo "✗ FAIL: Data mismatch\n\n";
}

// Test 2: Expiration
echo "Test 2: Expiration (2 second TTL)\n";
Cache::set('expire_test', 'This will expire', 2);
echo "Waiting 3 seconds...\n";
sleep(3);
$expired = Cache::get('expire_test');

if ($expired === null) {
    echo "✓ PASS: Cache expired correctly\n\n";
} else {
    echo "✗ FAIL: Cache did not expire\n\n";
}

// Test 3: Delete
echo "Test 3: Delete\n";
Cache::set('delete_test', 'Delete me', 60);
Cache::delete('delete_test');
$deleted = Cache::get('delete_test');

if ($deleted === null) {
    echo "✓ PASS: Cache deleted successfully\n\n";
} else {
    echo "✗ FAIL: Cache still exists\n\n";
}

// Test 4: Remember pattern
echo "Test 4: Remember Pattern\n";
$callCount = 0;
$getData = function() use (&$callCount) {
    $callCount++;
    return ['call_number' => $callCount];
};

$result1 = Cache::remember('remember_test', $getData, 60);
$result2 = Cache::remember('remember_test', $getData, 60);

if ($result1['call_number'] === 1 && $result2['call_number'] === 1 && $callCount === 1) {
    echo "✓ PASS: Remember pattern worked (callback called once)\n\n";
} else {
    echo "✗ FAIL: Callback called multiple times: $callCount\n\n";
}

// Test 5: Statistics
echo "Test 5: Cache Statistics\n";
$stats = Cache::getStats();
echo "Total files: {$stats['total_files']}\n";
echo "Expired files: {$stats['expired_files']}\n";
echo "Total size: {$stats['total_size_mb']} MB\n\n";

// Cleanup
echo "Cleaning up test cache...\n";
Cache::delete('test_key');
Cache::delete('remember_test');
echo "✓ Test complete!\n";
?>
