<?php
require_once 'includes/functions/config.php';

$result = $conn->query("DESCRIBE bookings");
if ($result) {
    echo "Field | Type | Null | Key | Default | Extra\n";
    echo "---|---|---|---|---|---\n";
    while ($row = $result->fetch_assoc()) {
        echo "{$row['Field']} | {$row['Type']} | {$row['Null']} | {$row['Key']} | {$row['Default']} | {$row['Extra']}\n";
    }
} else {
    echo "Error: " . $conn->error;
}
?>
