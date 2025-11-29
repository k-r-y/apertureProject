<?php
require_once __DIR__ . '/../includes/functions/config.php';

$userID = 1043; // Hardcoded for this session based on previous logs
echo "Resetting gallery for user $userID...\n";

$stmt = $conn->prepare("DELETE FROM user_photos WHERE userID = ?");
$stmt->bind_param("i", $userID);

if ($stmt->execute()) {
    echo "Successfully deleted " . $stmt->affected_rows . " photos.\n";
} else {
    echo "Error: " . $stmt->error . "\n";
}
?>
