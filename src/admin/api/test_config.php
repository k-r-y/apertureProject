<?php
// Test if config.php loads without errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Testing Config File</h2>";

try {
    require_once '../../includes/functions/config.php';
    echo "✅ config.php loaded successfully<br>";
    
    if (isset($conn)) {
        echo "✅ Database connection variable exists<br>";
        
        if ($conn instanceof mysqli) {
            echo "✅ Connection is a valid mysqli object<br>";
            
            if ($conn->ping()) {
                echo "✅ Database connection is active<br>";
                echo "Database: " . $conn->get_server_info() . "<br>";
            } else {
                echo "❌ Database ping failed<br>";
            }
        } else {
            echo "❌ Connection is not a mysqli object<br>";
        }
    } else {
        echo "❌ Database connection variable not set<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error loading config: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h2>Testing Session</h2>";
try {
    require_once '../../includes/functions/session.php';
    echo "✅ session.php loaded successfully<br>";
    echo "Session ID: " . session_id() . "<br>";
} catch (Exception $e) {
    echo "❌ Error loading session: " . $e->getMessage() . "<br>";
}
?>
