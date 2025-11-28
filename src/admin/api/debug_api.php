<?php
// Debug version - shows what's wrong
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>API Debug Information</h2>";

echo "<h3>1. Session Status</h3>";
session_start();
echo "Session ID: " . session_id() . "<br>";
echo "Session started: ✅<br>";

echo "<h3>2. Session Variables</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>3. Database Connection</h3>";
require_once '../../includes/functions/config.php';
if ($conn) {
    echo "✅ Connected to database<br>";
} else {
    echo "❌ Database connection failed<br>";
}

echo "<h3>4. Check Inquiries Table</h3>";
$check = $conn->query("SHOW TABLES LIKE 'inquiries'");
if ($check && $check->num_rows > 0) {
    echo "✅ Inquiries table exists<br>";
    
    $count = $conn->query("SELECT COUNT(*) as cnt FROM inquiries")->fetch_assoc();
    echo "Total records: " . $count['cnt'] . "<br>";
} else {
    echo "❌ Inquiries table does NOT exist<br>";
}

echo "<h3>5. Try to Fetch Data</h3>";
try {
    $result = $conn->query("SELECT * FROM inquiries ORDER BY created_at DESC LIMIT 5");
    if ($result) {
        echo "✅ Query executed successfully<br>";
        echo "Rows returned: " . $result->num_rows . "<br>";
        
        if ($result->num_rows > 0) {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Subject</th><th>Status</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "<td>" . htmlspecialchars($row['subject']) . "</td>";
                echo "<td>{$row['status']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "❌ Query failed: " . $conn->error . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "<br>";
}

echo "<h3>6. Test JSON Output</h3>";
$testData = ['success' => true, 'message' => 'Test successful'];
echo "JSON: " . json_encode($testData) . "<br>";

echo "<h3>7. Authorization Check</h3>";
if (isset($_SESSION['userId']) && isset($_SESSION['role'])) {
    echo "User ID: " . $_SESSION['userId'] . "<br>";
    echo "Role: " . $_SESSION['role'] . "<br>";
    
    if ($_SESSION['role'] === 'Admin') {
        echo "✅ User is Admin<br>";
    } else {
        echo "❌ User is NOT Admin (role: " . $_SESSION['role'] . ")<br>";
    }
} else {
    echo "❌ User not logged in<br>";
}
?>
