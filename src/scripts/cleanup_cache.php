<?php
/**
 * Cache Cleanup Script
 * Removes expired cache files
 * 
 * Usage: php cleanup_cache.php
 */

require_once __DIR__ . '/../includes/functions/cache.php';

echo "=== Cache Cleanup Script ===\n\n";

// Get cache statistics before cleanup
$statsBefore = Cache::getStats();

echo "Before cleanup:\n";
echo "  Total files: {$statsBefore['total_files']}\n";
echo "  Expired files: {$statsBefore['expired_files']}\n";
echo "  Total size: {$statsBefore['total_size_mb']} MB\n\n";

// Perform cleanup
$deletedCount = Cache::cleanup();

// Get statistics after cleanup
$statsAfter = Cache::getStats();

echo "After cleanup:\n";
echo "  Total files: {$statsAfter['total_files']}\n";
echo "  Expired files: {$statsAfter['expired_files']}\n";
echo "  Total size: {$statsAfter['total_size_mb']} MB\n\n";

echo "Deleted: $deletedCount expired cache file(s)\n";
?>
