<?php
/**
 * Initialize API Rate Limiting
 * 
 * Run this script once to set up the rate limiting table
 * 
 * Usage: php init_rate_limiting.php
 */

require_once __DIR__ . '/../includes/functions/config.php';
require_once __DIR__ . '/../includes/functions/api_security.php';

echo "Initializing API Rate Limiting...\n\n";

try {
    // Create rate limiting table
    initializeRateLimitTable();
    echo "✓ Rate limiting table created successfully\n";
    
    // Test the rate limiting functionality
    echo "\nTesting rate limiting...\n";
    $testResult = checkRateLimit('/api/test', 100, 3600);
    
    if ($testResult['allowed']) {
        echo "✓ Rate limiting is working correctly\n";
        echo "  - Remaining requests: " . $testResult['remaining'] . "\n";
        echo "  - Reset time: " . date('Y-m-d H:i:s', $testResult['reset_time']) . "\n";
    } else {
        echo "✗ Rate limiting test failed\n";
    }
    
    echo "\n✓ API Rate Limiting initialized successfully!\n\n";
    echo "Next steps:\n";
    echo "1. Add rate limiting to your API endpoints using enforceRateLimit()\n";
    echo "2. Set up a cron job to run cleanupRateLimitRecords() daily\n";
    echo "3. Configure API_KEY in your .env file for admin endpoints\n\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
