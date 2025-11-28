<?php
// Quick test script to check API and database
require_once '../includes/functions/config.php';

echo "<h2>Database Connection Test</h2>";
if ($conn) {
    echo "✅ Database connected<br>";
    
    // Check if inquiries table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'inquiries'");
    if ($tableCheck->num_rows > 0) {
        echo "✅ Inquiries table exists<br>";
        
        // Count records
        $count = $conn->query("SELECT COUNT(*) as total FROM inquiries")->fetch_assoc();
        echo "📊 Total inquiries: " . $count['total'] . "<br>";
        
        // Show sample data
        $sample = $conn->query("SELECT * FROM inquiries ORDER BY created_at DESC LIMIT 3");
        if ($sample->num_rows > 0) {
            echo "<h3>Recent Inquiries:</h3>";
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Subject</th><th>Status</th><th>Date</th></tr>";
            while ($row = $sample->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "<td>" . htmlspecialchars($row['subject']) . "</td>";
                echo "<td>{$row['status']}</td>";
                echo "<td>{$row['created_at']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "⚠️ No inquiries found in database<br>";
        }
    } else {
        echo "❌ Inquiries table does NOT exist<br>";
        echo "<p>Run this SQL to create it:</p>";
        echo "<pre>CREATE TABLE IF NOT EXISTS `inquiries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','replied') NOT NULL DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;</pre>";
    }
} else {
    echo "❌ Database connection failed<br>";
}

echo "<hr>";
echo "<h2>API Test</h2>";
echo "<a href='admin/api/inquiries_api.php?action=get_all' target='_blank'>Test API (must be logged in as admin)</a>";
?>
