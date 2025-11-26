<?php
require_once 'includes/functions/config.php';

echo "Database Schema Inspection\n";
echo "==========================\n";

// Get all tables
$tablesResult = $conn->query("SHOW TABLES");
if (!$tablesResult) {
    die("Error fetching tables: " . $conn->error . "\n");
}

$tables = [];
while ($row = $tablesResult->fetch_array()) {
    $tables[] = $row[0];
}

foreach ($tables as $table) {
    echo "\nTable: $table\n";
    echo str_repeat("-", strlen("Table: $table")) . "\n";
    
    $columnsResult = $conn->query("SHOW COLUMNS FROM `$table`");
    if (!$columnsResult) {
        echo "Error fetching columns for $table: " . $conn->error . "\n";
        continue;
    }
    
    while ($col = $columnsResult->fetch_assoc()) {
        echo str_pad($col['Field'], 25) . 
             str_pad($col['Type'], 20) . 
             str_pad($col['Null'], 6) . 
             str_pad($col['Key'], 5) . 
             str_pad($col['Default'] ?? 'NULL', 15) . 
             "\n";
    }
}

echo "\nInspection Complete.\n";
?>
