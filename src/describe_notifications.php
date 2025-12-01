<?php
require_once 'includes/functions/config.php';
$result = $conn->query("DESCRIBE notifications");
while ($row = $result->fetch_assoc()) {
    print_r($row);
}
?>
