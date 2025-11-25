<?php
require_once '../includes/functions/config.php';

echo "Starting Database Optimization...\n\n";

$sqlFile = '../../database/optimize_indexes.sql';
if (!file_exists($sqlFile)) {
    die("Error: SQL file not found at $sqlFile\n");
}

$sql = file_get_contents($sqlFile);
$statements = array_filter(array_map('trim', explode(';', $sql)));

$successCount = 0;
$skipCount = 0;
$errorCount = 0;

foreach ($statements as $statement) {
    if (empty($statement) || strpos($statement, '--') === 0) continue;
    
    try {
        if ($conn->query($statement) === TRUE) {
            // Extract index name for better logging
            preg_match('/INDEX\s+(?:IF\s+NOT\s+EXISTS\s+)?(\w+)/i', $statement, $matches);
            $indexName = $matches[1] ?? 'unknown';
            echo "✓ Created index: $indexName\n";
            $successCount++;
        } else {
            if ($conn->errno == 1061) { // Duplicate key name
                preg_match('/INDEX\s+(?:IF\s+NOT\s+EXISTS\s+)?(\w+)/i', $statement, $matches);
                $indexName = $matches[1] ?? 'unknown';
                echo "⊘ Skipped (exists): $indexName\n";
                $skipCount++;
            } else {
                echo "✗ Error: " . $conn->error . "\n";
                $errorCount++;
            }
        }
    } catch (Exception $e) {
        echo "✗ Exception: " . $e->getMessage() . "\n";
        $errorCount++;
    }
}

echo "\n========================================\n";
echo "Database Optimization Complete!\n";
echo "========================================\n";
echo "Indexes created: $successCount\n";
echo "Already existed: $skipCount\n";
echo "Errors: $errorCount\n";
echo "========================================\n";

// Analyze query performance improvement
echo "\nAnalyzing table statistics...\n";
$tables = ['bookings', 'booking_logs', 'users', 'client_notes', 'user_tags', 'communication_log'];

foreach ($tables as $table) {
    $result = $conn->query("SELECT COUNT(*) as count FROM $table");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "$table: {$row['count']} records\n";
    }
}

echo "\n✓ Optimization complete. Run EXPLAIN on slow queries to verify improvements.\n";
?>
