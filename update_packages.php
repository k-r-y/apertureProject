<?php
require_once 'src/includes/functions/config.php';

// Add columns if they don't exist
try {
    $conn->query("ALTER TABLE packages ADD COLUMN coverage_hours INT DEFAULT 0");
    $conn->query("ALTER TABLE packages ADD COLUMN extra_hour_rate DECIMAL(10, 2) DEFAULT 0");
    echo "Columns added.\n";
} catch (Exception $e) {
    echo "Columns might already exist or error: " . $e->getMessage() . "\n";
}

// Update Basic Package
$conn->query("UPDATE packages SET coverage_hours = 3, extra_hour_rate = 1000.00 WHERE packageID = 'basic' OR packageID = 1");

// Update Elite Package
$conn->query("UPDATE packages SET coverage_hours = 5, extra_hour_rate = 1500.00 WHERE packageID = 'elite' OR packageID = 2");

// Update Premium Package
$conn->query("UPDATE packages SET coverage_hours = 8, extra_hour_rate = 2000.00 WHERE packageID = 'premium' OR packageID = 3");

echo "Packages updated successfully.";
?>
