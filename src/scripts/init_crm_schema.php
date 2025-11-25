<?php
require_once '../includes/functions/config.php';

echo "Starting CRM Schema Setup...\n";

$sqlFile = '../../database/crm_schema.sql';
if (!file_exists($sqlFile)) {
    die("Error: SQL file not found at $sqlFile\n");
}

$sql = file_get_contents($sqlFile);
$statements = array_filter(array_map('trim', explode(';', $sql)));

foreach ($statements as $statement) {
    if (empty($statement)) continue;
    
    try {
        if ($conn->query($statement) === TRUE) {
            echo "Success: " . substr($statement, 0, 60) . "...\n";
        } else {
            if ($conn->errno == 1050) { // Table already exists
                echo "Skipped (exists): " . substr($statement, 0, 60) . "...\n";
            } else {
                echo "Error: " . $conn->error . "\n";
            }
        }
    } catch (Exception $e) {
        echo "Exception: " . $e->getMessage() . "\n";
    }
}

echo "CRM schema setup completed.\n";
?>
