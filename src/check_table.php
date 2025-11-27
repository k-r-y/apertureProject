<?php
require_once 'includes/functions/config.php';

$result = $conn->query("SHOW TABLES LIKE 'notifications'");
if ($result->num_rows > 0) {
    echo "EXISTS";
} else {
    echo "MISSING";
}
?>
