<?php
/**
 * Database Restore Script
 * 
 * Restores database from a backup file
 * 
 * Usage:
 * php restore_database.php backup_file.sql.gz
 */

require_once '../includes/functions/config.php';

if ($argc < 2) {
    die("Usage: php restore_database.php <backup_file.sql.gz>\n");
}

$backupFile = $argv[1];

if (!file_exists($backupFile)) {
    die("Error: Backup file not found: $backupFile\n");
}

echo "==========================================\n";
echo "Aperture Database Restore\n";
echo "==========================================\n";
echo "Backup file: " . basename($backupFile) . "\n";
echo "WARNING: This will REPLACE the current database!\n";
echo "==========================================\n\n";

// Confirmation (skip if running automated tests)
if (php_sapi_name() === 'cli') {
    echo "Type 'YES' to continue: ";
    $handle = fopen("php://stdin", "r");
    $line = trim(fgets($handle));
    if ($line !== 'YES') {
        die("Restore cancelled.\n");
    }
}

echo "\nRestoring database...\n";

// Decompress if needed
$sqlFile = $backupFile;
if (substr($backupFile, -3) === '.gz') {
    echo "Decompressing backup...\n";
    $sqlFile = str_replace('.gz', '', $backupFile);
    
    $gzHandle = gzopen($backupFile, 'rb');
    $sqlHandle = fopen($sqlFile, 'wb');
    
    while (!gzeof($gzHandle)) {
        fwrite($sqlHandle, gzread($gzHandle, 1024 * 512));
    }
    
    gzclose($gzHandle);
    fclose($sqlHandle);
    
    echo "✓ Decompressed\n";
}

// Restore database
$mysqlPath = 'c:\xampp\mysql\bin\mysql.exe'; // Windows
if (!file_exists($mysqlPath)) {
    $mysqlPath = 'mysql'; // Try system path
}

$command = sprintf(
    '"%s" --user=%s --password=%s --host=%s %s < "%s" 2>&1',
    $mysqlPath,
    $_ENV['DB_USER'],
    $_ENV['DB_PASS'],
    $_ENV['DB_HOST'],
    $_ENV['DB_NAME'],
    $sqlFile
);

exec($command, $output, $returnVar);

// Clean up temporary SQL file if it was decompressed
if ($sqlFile !== $backupFile && file_exists($sqlFile)) {
    unlink($sqlFile);
}

if ($returnVar === 0) {
    echo "✓ Database restored successfully!\n";
} else {
    $error = implode("\n", $output);
    die("✗ Restore failed!\nError: $error\n");
}

echo "==========================================\n";
?>
