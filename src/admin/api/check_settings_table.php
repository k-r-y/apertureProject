<?php
require_once '../../includes/functions/config.php';

echo "Checking database connection...\n";
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully.\n";

echo "Checking settings table...\n";
$result = $conn->query("SHOW TABLES LIKE 'settings'");
if ($result->num_rows > 0) {
    echo "Table 'settings' exists.\n";
    
    $result = $conn->query("SELECT * FROM settings");
    echo "Found " . $result->num_rows . " rows in settings.\n";
    while($row = $result->fetch_assoc()) {
        echo $row['setting_key'] . ": " . $row['setting_value'] . "\n";
    }
} else {
    echo "Table 'settings' does NOT exist.\n";
}
?>
