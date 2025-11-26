<?php
require_once __DIR__ . '/../includes/functions/config.php';

// Migrate data
$sql = "INSERT INTO inquiries (name, email, subject, message, status, created_at)
        SELECT name, email, subject, message, IF(is_read=1, 'read', 'new'), created_at 
        FROM contactMessages";

if ($conn->query($sql) === TRUE) {
    echo "Migration successful. " . $conn->affected_rows . " messages moved.";
} else {
    echo "Error migrating: " . $conn->error;
}
?>
