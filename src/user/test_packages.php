<?php
// Test script to debug package data retrieval
require_once '../includes/functions/config.php';

// Fetch packages exactly as bookingForm.php does
$query = ("SELECT * FROM packages ORDER BY Price ASC");
$result = $conn->query($query);

$packages = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $packages[] = $row;
    }
}

// Display results
echo "<h2>Package Data Debug</h2>";
echo "<pre>";
foreach ($packages as $pkg) {
    echo "Package: " . $pkg['packageName'] . "\n";
    echo "  packageID: " . $pkg['packageID'] . "\n";
    echo "  Price: " . $pkg['Price'] . "\n";
    echo "  coverage_hours: " . (isset($pkg['coverage_hours']) ? $pkg['coverage_hours'] : "NOT SET") . "\n";
    echo "  extra_hour_rate: " . (isset($pkg['extra_hour_rate']) ? $pkg['extra_hour_rate'] : "NOT SET") . "\n";
    echo "  \n";
    echo "  Data attributes that would be output:\n";
    echo "    data-price=\"" . $pkg['Price'] . "\"\n";
    echo "    data-coverage-hours=\"" . (isset($pkg['coverage_hours']) ? $pkg['coverage_hours'] : 4) . "\"\n";
    echo "    data-hourly-rate=\"" . (isset($pkg['extra_hour_rate']) ? $pkg['extra_hour_rate'] : 1000) . "\"\n";
    echo "\n---\n\n";
}
echo "</pre>";

$conn->close();
?>
