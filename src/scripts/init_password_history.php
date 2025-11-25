<?php
/**
 * Initialize Password History System
 * 
 * Run this script once to set up password history tracking
 * 
 * Usage: php init_password_history.php
 */

require_once __DIR__ . '/../includes/functions/config.php';
require_once __DIR__ . '/../includes/functions/password_history.php';

echo "Initializing Password History System...\n\n";

try {
    // Create password history table
    if (initializePasswordHistoryTable()) {
        echo "✓ Password history table created successfully\n";
    } else {
        throw new Exception("Failed to create password history table");
    }
    
    // Migrate existing passwords
    echo "\nMigrating existing user passwords to history...\n";
    $count = migrateExistingPasswords();
    echo "✓ Migrated {$count} user passwords\n";
    
    echo "\n✓ Password History System initialized successfully!\n\n";
    echo "Next steps:\n";
    echo "1. Password history is now tracked for all new registrations\n";
    echo "2. Users cannot reuse their last 5 passwords\n";
    echo "3. Password strength is enforced on registration\n\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
