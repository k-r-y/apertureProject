<?php
require_once '../includes/functions/config.php';

echo "Starting Phase 3 Schema Update...\n";

// Read SQL file
$sqlFile = '../../database/enhance_bookings_schema.sql';
if (!file_exists($sqlFile)) {
    die("Error: SQL file not found at $sqlFile\n");
}

$sql = file_get_contents($sqlFile);
$statements = array_filter(array_map('trim', explode(';', $sql)));

foreach ($statements as $statement) {
    if (empty($statement)) continue;
    
    try {
        if ($conn->query($statement) === TRUE) {
            echo "Successfully executed: " . substr($statement, 0, 50) . "...\n";
        } else {
            // Check if column already exists error (1060)
            if ($conn->errno == 1060) {
                echo "Skipped (already exists): " . substr($statement, 0, 50) . "...\n";
            } else {
                echo "Error executing: " . $conn->error . "\n";
            }
        }
    } catch (Exception $e) {
        echo "Exception: " . $e->getMessage() . "\n";
    }
}

echo "Schema update completed.\n";
?>
