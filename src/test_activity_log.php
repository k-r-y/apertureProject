-- Test if activity log table exists and show sample data
<?php
require_once '../includes/functions/config.php';

echo "<h2>User Activity Log Table Check</h2>";

// Check if table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'user_activity_log'");
if ($tableCheck->num_rows > 0) {
    echo "✅ user_activity_log table exists<br>";
    
    // Count records
    $count = $conn->query("SELECT COUNT(*) as total FROM user_activity_log")->fetch_assoc();
    echo "📊 Total activity records: " . $count['total'] . "<br><br>";
    
    // Show sample data
    if ($count['total'] > 0) {
        $sample = $conn->query("
            SELECT 
                a.*,
                u.fullName as user_name,
                u.email as user_email
            FROM user_activity_log a
            LEFT JOIN users u ON a.user_id = u.userID
            ORDER BY a.created_at DESC 
            LIMIT 10
        ");
        
        echo "<h3>Recent Activity (Last 10):</h3>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>User</th><th>Type</th><th>Description</th><th>Date</th></tr>";
        
        while ($row = $sample->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>" . htmlspecialchars($row['user_name'] ?? 'Unknown') . "</td>";
            echo "<td>{$row['activity_type']}</td>";
            echo "<td>" . htmlspecialchars($row['activity_description']) . "</td>";
            echo "<td>{$row['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "⚠️ <strong>Table exists but is empty!</strong><br>";
        echo "<p>Activity logging is set up but no activities have been recorded yet.</p>";
        echo "<p style='color: blue;'>This is normal if users haven't performed any tracked actions yet.</p>";
    }
} else {
    echo "❌ <strong>user_activity_log table does NOT exist</strong><br><br>";
    echo "<p>Run this SQL to create it:</p>";
    echo "<pre>";
    echo htmlspecialchars(file_get_contents(__DIR__ . '/sql/activity_log_schema.sql'));
    echo "</pre>";
}

echo "<hr>";
echo "<h3>Test API Call</h3>";
echo "<a href='admin/api/inquiries_api.php?action=get_activity_log' target='_blank'>Test Activity Log API</a>";
?>
