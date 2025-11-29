<?php
require_once '../includes/functions/config.php';
require_once '../includes/functions/session.php';
require_once '../includes/functions/activity_logger.php';

// Check if user is admin
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'Admin') {
    die("Unauthorized");
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Activity Log</title>
    <style>
        body { font-family: monospace; background: #1a1a1a; color: #fff; padding: 20px; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #444; padding: 8px; text-align: left; }
        th { background: #333; }
        pre { background: #222; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔍 Activity Log Debug</h1>

    <h2>1. Check if table exists</h2>
    <?php
    $tableCheck = $conn->query("SHOW TABLES LIKE 'user_activity_log'");
    if ($tableCheck->num_rows > 0) {
        echo "<p class='success'>✅ Table 'user_activity_log' exists</p>";
    } else {
        echo "<p class='error'>❌ Table 'user_activity_log' does NOT exist!</p>";
        echo "<p>You need to create the table first.</p>";
        exit;
    }
    ?>

    <h2>2. Check table structure</h2>
    <?php
    $structure = $conn->query("DESCRIBE user_activity_log");
    echo "<table><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    while ($row = $structure->fetch_assoc()) {
        echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td></tr>";
    }
    echo "</table>";
    ?>

    <h2>3. Check row count</h2>
    <?php
    $count = $conn->query("SELECT COUNT(*) as total FROM user_activity_log")->fetch_assoc();
    echo "<p>Total records: <strong>{$count['total']}</strong></p>";
    
    if ($count['total'] == 0) {
        echo "<p class='warning'>⚠️ No activity records found. Try creating a test booking or performing some actions.</p>";
    }
    ?>

    <h2>4. Test getUserActivities() function</h2>
    <?php
    try {
        $activities = getUserActivities(null, null, 10, 0);
        echo "<p class='success'>✅ Function executed successfully</p>";
        echo "<p>Returned " . count($activities) . " records</p>";
        
        if (count($activities) > 0) {
            echo "<h3>Sample data:</h3>";
            echo "<pre>" . print_r($activities[0], true) . "</pre>";
            
            echo "<h3>All activities:</h3>";
            echo "<table>";
            echo "<tr><th>ID</th><th>User</th><th>Activity</th><th>Description</th><th>Date</th></tr>";
            foreach ($activities as $act) {
                $userName = isset($act['FirstName']) && isset($act['LastName']) 
                    ? $act['FirstName'] . ' ' . $act['LastName'] 
                    : 'Unknown';
                echo "<tr>";
                echo "<td>{$act['id']}</td>";
                echo "<td>{$userName} (ID: {$act['user_id']})</td>";
                echo "<td>{$act['activity_type']}</td>";
                echo "<td>{$act['activity_description']}</td>";
                echo "<td>{$act['created_at']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
    }
    ?>

    <h2>5. Test API endpoint</h2>
    <?php
    echo "<p><a href='api/inquiries_api.php?action=get_activity_log' target='_blank' style='color: #D4AF37;'>→ Click to test API directly</a></p>";
    ?>

    <h2>6. Raw SQL query</h2>
    <?php
    $query = "
        SELECT 
            a.*,
            u.FirstName,
            u.LastName,
            u.Email as user_email
        FROM user_activity_log a
        LEFT JOIN users u ON a.user_id = u.userID
        ORDER BY a.created_at DESC
        LIMIT 10
    ";
    echo "<pre>$query</pre>";
    
    $result = $conn->query($query);
    if ($result) {
        echo "<p class='success'>✅ Query executed successfully</p>";
        echo "<p>Rows returned: " . $result->num_rows . "</p>";
    } else {
        echo "<p class='error'>❌ Query failed: " . $conn->error . "</p>";
    }
    ?>

    <p><a href="inquiries.php" style="color: #D4AF37;">← Back to Inquiries page</a></p>
</body>
</html>
