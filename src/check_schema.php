<?php
require_once 'includes/functions/config.php';

echo "--- Refunds Table ---\n";
$res = $conn->query('DESCRIBE refunds');
if ($res) {
    while($row = $res->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "Refunds table not found or error: " . $conn->error . "\n";
}

echo "\n--- Payments Table ---\n";
$res2 = $conn->query("SHOW TABLES LIKE 'payments'");
echo 'Payments table: ' . ($res2->num_rows > 0 ? 'Yes' : 'No') . "\n";

echo "\n--- Bookings Table ---\n";
$res3 = $conn->query('DESCRIBE bookings');
if ($res3) {
    while($row = $res3->fetch_assoc()) {
        if (strpos($row['Field'], 'proof') !== false || strpos($row['Field'], 'pay') !== false) {
             echo $row['Field'] . " - " . $row['Type'] . "\n";
        }
    }
}
?>
