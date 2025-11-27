<?php
require_once '../../includes/functions/config.php';

echo "Checking notifications table...<br>";

if ($result = $conn->query("SHOW TABLES LIKE 'notifications'")) {
    if($result->num_rows == 1) {
        echo "Table 'notifications' exists.<br>";
        
        echo "Columns:<br>";
        $columns = $conn->query("SHOW COLUMNS FROM notifications");
        while($row = $columns->fetch_assoc()) {
            echo $row['Field'] . " - " . $row['Type'] . "<br>";
        }
    } else {
        echo "Table 'notifications' DOES NOT exist.<br>";
    }
} else {
    echo "Error checking table: " . $conn->error . "<br>";
}

echo "Testing query preparation...<br>";
$stmt = $conn->prepare("SELECT id, type, title, message, link, created_at FROM notifications WHERE userID = ?");
if ($stmt) {
    echo "Prepare successful.<br>";
} else {
    echo "Prepare failed: " . $conn->error . "<br>";
}
?>
