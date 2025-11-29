<?php
require_once __DIR__ . '/../includes/functions/config.php';

echo "Inspecting all photos...\n";

$sql = "SELECT photoID, userID, fileName, originalName, photo_type, uploadDate FROM user_photos ORDER BY originalName, uploadDate";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo json_encode($row) . "\n";
    }
} else {
    echo "Query failed: " . $conn->error . "\n";
}
?>
