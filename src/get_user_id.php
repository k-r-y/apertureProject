<?php
require_once 'includes/functions/config.php';
$result = $conn->query("SELECT userID FROM users LIMIT 1");
if ($row = $result->fetch_assoc()) {
    $uid = $row['userID'];
    echo "User ID: " . $uid . "\n";
    
    // Try insert
    $stmt = $conn->prepare("INSERT INTO notifications (userID, title, message, type, created_at) VALUES (?, 'Test', 'Test Message', 'test', NOW())");
    $stmt->bind_param("i", $uid);
    if ($stmt->execute()) {
        echo "Insert success";
    } else {
        echo "Insert failed: " . $stmt->error;
    }
} else {
    echo "No users found";
}
?>
