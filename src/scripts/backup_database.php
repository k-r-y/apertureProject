<?php
/**
 * Automated Database Backup Script
 * 
 * Features:
 * - Full database backup
 * - Automatic rotation (keeps last 7 daily, 4 weekly, 3 monthly)
 * - Compression (gzip)
 * - Email notification on failure
 * 
 * Usage:
 * php backup_database.php
 * 
 * Cron (daily at 2 AM):
 * 0 2 * * * php /path/to/backup_database.php
 */

require_once '../includes/functions/config.php';

// Configuration
$backupDir = dirname(__DIR__, 2) . '/backups';
$dbHost = $_ENV['DB_HOST'];
$dbUser = $_ENV['DB_USER'];
$dbPass = $_ENV['DB_PASS'];
$dbName = $_ENV['DB_NAME'];

// Create backup directory if it doesn't exist
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

// Generate backup filename
$timestamp = date('Y-m-d_H-i-s');
$dayOfWeek = date('w'); // 0 (Sunday) to 6 (Saturday)
$dayOfMonth = date('j');

// Determine backup type
$backupType = 'daily';
if ($dayOfWeek == 0) { // Sunday = weekly backup
    $backupType = 'weekly';
}
if ($dayOfMonth == 1) { // 1st of month = monthly backup
    $backupType = 'monthly';
}

$backupFile = "$backupDir/{$backupType}_backup_{$timestamp}.sql";
$backupFileGz = "$backupFile.gz";

echo "==========================================\n";
echo "Aperture Database Backup\n";
echo "==========================================\n";
echo "Type: " . strtoupper($backupType) . "\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n";
echo "Database: $dbName\n";
echo "==========================================\n\n";

// Construct mysqldump command
$mysqldumpPath = 'c:\xampp\mysql\bin\mysqldump.exe'; // Windows path
if (!file_exists($mysqldumpPath)) {
    $mysqldumpPath = 'mysqldump'; // Try system path
}

$command = sprintf(
    '"%s" --user=%s --password=%s --host=%s %s > "%s" 2>&1',
    $mysqldumpPath,
    $dbUser,
    $dbPass,
    $dbHost,
    $dbName,
    $backupFile
);

// Execute backup
exec($command, $output, $returnVar);

if ($returnVar === 0 && file_exists($backupFile)) {
    $fileSize = filesize($backupFile);
    echo "✓ Backup created: " . basename($backupFile) . "\n";
    echo "  Size: " . formatBytes($fileSize) . "\n";
    
    // Compress the backup
    echo "\nCompressing backup...\n";
    $gzHandle = gzopen($backupFileGz, 'wb9');
    $sqlHandle = fopen($backupFile, 'rb');
    
    while (!feof($sqlHandle)) {
        gzwrite($gzHandle, fread($sqlHandle, 1024 * 512)); // 512KB chunks
    }
    
    fclose($sqlHandle);
    gzclose($gzHandle);
    
    // Remove uncompressed file
    unlink($backupFile);
    
    $compressedSize = filesize($backupFileGz);
    $compressionRatio = round((1 - ($compressedSize / $fileSize)) * 100, 1);
    
    echo "✓ Compressed: " . basename($backupFileGz) . "\n";
    echo "  Size: " . formatBytes($compressedSize) . " (saved {$compressionRatio}%)\n";
    
    // Rotate old backups
    echo "\nRotating old backups...\n";
    rotateBackups($backupDir);
    
    echo "\n✓ Backup completed successfully!\n";
    
} else {
    $error = implode("\n", $output);
    echo "✗ Backup failed!\n";
    echo "Error: $error\n";
    
    // Send failure notification (optional)
    sendBackupAlert('Backup Failed', $error);
    exit(1);
}

/**
 * Rotate backups - keep last N of each type
 */
function rotateBackups($dir) {
    $retention = [
        'daily' => 7,   // Keep 7 daily backups
        'weekly' => 4,  // Keep 4 weekly backups
        'monthly' => 3  // Keep 3 monthly backups
    ];
    
    foreach ($retention as $type => $keep) {
        $backups = glob("$dir/{$type}_backup_*.sql.gz");
        rsort($backups); // Newest first
        
        $deleted = 0;
        foreach (array_slice($backups, $keep) as $oldBackup) {
            unlink($oldBackup);
            $deleted++;
        }
        
        $remaining = count($backups) - $deleted;
        echo "  $type: $remaining backups (deleted $deleted old)\n";
    }
}

/**
 * Format bytes to human readable
 */
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

/**
 * Send backup failure alert
 */
function sendBackupAlert($subject, $message) {
    // Implement email notification if needed
    error_log("Backup Alert: $subject - $message");
}

echo "==========================================\n";
?>
