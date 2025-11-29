<?php
require_once __DIR__ . '/../includes/functions/config.php';

echo "Checking photo types...\n";

$sql = "SELECT photo_type, COUNT(*) as count FROM user_photos GROUP BY photo_type";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "Type: " . ($row['photo_type'] ?: 'NULL') . " - Count: " . $row['count'] . "\n";
    }
} else {
    echo "Query failed: " . $conn->error . "\n";
}
?>
