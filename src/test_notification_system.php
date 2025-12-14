<?php
require_once 'includes/functions/config.php';
require_once 'includes/functions/session.php';

// Check if notifications table exists
echo "<h2>Testing Notification System</h2>";

// 1. Check if table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'notifications'");
if ($tableCheck && $tableCheck->num_rows > 0) {
    echo "✅ Notifications table exists<br>";
    
    // 2. Check table structure
    $structure = $conn->query("DESCRIBE notifications");
    echo "<h3>Table Structure:</h3><pre>";
    while ($row = $structure->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . " - " . $row['Null'] . " - " . $row['Key'] . "\n";
    }
    echo "</pre>";
    
    // 3. Count notifications
    $count = $conn->query("SELECT COUNT(*) as total FROM notifications");
    $total = $count->fetch_assoc()['total'];
    echo "<h3>Total Notifications: $total</h3>";
    
    // 4. Show recent notifications
    $recent = $conn->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 10");
    echo "<h3>Recent 10 Notifications:</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>UserID</th><th>Title</th><th>Message</th><th>Type</th><th>Read</th><th>Created</th></tr>";
    while ($row = $recent->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['notificationID'] . "</td>";
        echo "<td>" . $row['userID'] . "</td>";
        echo "<td>" . $row['title'] . "</td>";
        echo "<td>" . $row['message'] . "</td>";
        echo "<td>" . $row['type'] . "</td>";
        echo "<td>" . ($row['is_read'] ?? 'N/A') . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // 5. Test creating a notification
    echo "<h3>Test: Creating a notification</h3>";
    $testUserId = 1; // Change this to an actual user ID
    $testStmt = $conn->prepare("INSERT INTO notifications (userID, title, message, type, link, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $testTitle = "Test Notification";
    $testMessage = "This is a test notification created at " . date('Y-m-d H:i:s');
    $testType = "test";
    $testLink = "appointments.php";
    
    $testStmt->bind_param("issss", $testUserId, $testTitle, $testMessage, $testType, $testLink);
    
    if ($testStmt->execute()) {
        $newId = $conn->insert_id;
        echo "✅ Test notification created successfully! ID: $newId<br>";
    } else {
        echo "❌ Failed to create test notification: " . $testStmt->error . "<br>";
    }
    $testStmt->close();
    
} else {
    echo "❌ Notifications table does NOT exist!<br>";
    echo "<h3>Creating notifications table...</h3>";
    
    $createTable = "CREATE TABLE IF NOT EXISTS notifications (
        notificationID INT AUTO_INCREMENT PRIMARY KEY,
        userID INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        type VARCHAR(50) NOT NULL,
        link VARCHAR(255),
        is_read TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user (userID),
        INDEX idx_read (is_read),
        FOREIGN KEY (userID) REFERENCES users(userID) ON DELETE CASCADE
    )";
    
    if ($conn->query($createTable)) {
        echo "✅ Notifications table created successfully!<br>";
    } else {
        echo "❌ Failed to create notifications table: " . $conn->error . "<br>";
    }
}

echo "<hr>";
echo "<h3>Check PHP Error Log:</h3>";
echo "Log file location: C:\\xampp\\apache\\logs\\error.log<br>";
echo "Check for lines containing 'In-App Notification'";
?>
