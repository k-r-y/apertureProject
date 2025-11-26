<?php
require_once 'includes/functions/config.php';

echo "Tables in database:\n";
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
    echo $row[0] . "\n";
}

echo "\nDescription of user_photos:\n";
$result = $conn->query("DESCRIBE user_photos");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "Table user_photos not found.\n";
}
?>
