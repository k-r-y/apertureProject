<?php
require_once 'includes/functions/config.php';

$result = $conn->query("DESCRIBE notifications");
if ($result) {
    echo "Table 'notifications' exists.\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " | " . $row['Type'] . " | " . $row['Null'] . "\n";
    }
} else {
    echo "Table 'notifications' does not exist.\n";
}
?>
