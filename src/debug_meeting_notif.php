<?php
require_once 'includes/functions/config.php';

echo "<style>
body { font-family: Arial; background: #1a1a1a; color: #e0e0e0; padding: 20px; }
.good { color: #4CAF50; }
.bad { color: #f44336; }
.warn { color: #ff9800; }
table { border-collapse: collapse; width: 100%; margin: 20px 0; }
th, td { border: 1px solid #333; padding: 8px; text-align: left; }
th { background: #2a2a2a; color: #d4af37; }
pre { background: #2a2a2a; padding: 10px; border-radius: 4px; overflow-x: auto; }
</style>";

echo "<h1>Meeting Link & Notification Debug Report</h1>";

// 1. Check bookings with meeting links
echo "<h2>1. Bookings with Meeting Links</h2>";
$query = "SELECT 
    b.bookingID,
    b.booking_status,
    b.meeting_link,
    u.userID,
    u.Email,
    CONCAT(u.FirstName, ' ', u.LastName) as name
FROM bookings b
JOIN users u ON b.userID = u.userID
WHERE b.booking_status IN ('confirmed', 'pending', 'post_production')
ORDER BY b.bookingID DESC
LIMIT 10";

$result = $conn->query($query);
echo "<table>";
echo "<tr><th>Booking ID</th><th>Status</th><th>User</th><th>Email</th><th>Meeting Link</th></tr>";

$hasLinks = 0;
$noLinks = 0;

while ($row = $result->fetch_assoc()) {
    $linkStatus = $row['meeting_link'] ? "<span class='good'>✓ " . htmlspecialchars($row['meeting_link']) . "</span>" : "<span class='bad'>✗ NULL</span>";
    if ($row['meeting_link']) $hasLinks++;
    else $noLinks++;
    
    echo "<tr>";
    echo "<td>#{$row['bookingID']}</td>";
    echo "<td>{$row['booking_status']}</td>";
    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['Email']) . "</td>";
    echo "<td>$linkStatus</td>";
    echo "</tr>";
}
echo "</table>";
echo "<p><span class='good'>$hasLinks bookings have meeting links</span> | <span class='bad'>$noLinks bookings don't have meeting links</span></p>";

// 2. Check recent notifications
echo "<h2>2. Recent Notifications (Last 10)</h2>";
$notifQuery = "SELECT 
    n.notificationID,
    n.userID,
    n.type,
    n.message,
    n.is_read,
    n.created_at,
    u.Email
FROM notifications n
JOIN users u ON n.userID = u.userID
WHERE u.Role = 'User'
ORDER BY n.created_at DESC
LIMIT 10";

$notifResult = $conn->query($notifQuery);
echo "<table>";
echo "<tr><th>ID</th><th>User Email</th><th>Type</th><th>Message</th><th>Read</th><th>Created</th></tr>";

$meetingLinkNotifs = 0;
while ($row = $notifResult->fetch_assoc()) {
    $readStatus = $row['is_read'] ? "<span class='good'>Read</span>" : "<span class='warn'>Unread</span>";
    if (strpos($row['message'], 'meeting link') !== false) $meetingLinkNotifs++;
    
    echo "<tr>";
    echo "<td>{$row['notificationID']}</td>";
    echo "<td>" . htmlspecialchars($row['Email']) . "</td>";
    echo "<td>" . htmlspecialchars($row['type']) . "</td>";
    echo "<td>" . htmlspecialchars(substr($row['message'], 0, 50)) . "...</td>";
    echo "<td>$readStatus</td>";
    echo "<td>{$row['created_at']}</td>";
    echo "</tr>";
}
echo "</table>";
echo "<p><span class='good'>$meetingLinkNotifs meeting link notifications found</span></p>";

// 3. Test a specific booking's data as returned by the API
echo "<h2>3. API Response Simulation (Booking #25)</h2>";
$testBookingID = 25;
$apiQuery = "SELECT 
    b.bookingID,
    b.meeting_link,
    b.event_type,
    b.event_date,
    b.booking_status,
    CONCAT(u.FirstName, ' ', u.LastName) as client_name
FROM bookings b
JOIN users u ON b.userID = u.userID
WHERE b.bookingID = ?";

$stmt = $conn->prepare($apiQuery);
$stmt->bind_param("i", $testBookingID);
$stmt->execute();
$apiResult = $stmt->get_result();
$apiData = $apiResult->fetch_assoc();

if ($apiData) {
    echo "<pre>";
    echo json_encode($apiData, JSON_PRETTY_PRINT);
    echo "</pre>";
    
    if ($apiData['meeting_link']) {
        echo "<p class='good'>✓ This booking HAS a meeting link: " . htmlspecialchars($apiData['meeting_link']) . "</p>";
        echo "<p class='good'>✓ The frontend SHOULD display it</p>";
    } else {
        echo "<p class='bad'>✗ This booking has NO meeting link (NULL)</p>";
        echo "<p class='warn'>⚠ The frontend will NOT display a meeting link section</p>";
        echo "<p><strong>Action Required:</strong> Add a meeting link via Admin → Bookings → Edit Booking #$testBookingID</p>";
    }
} else {
    echo "<p class='bad'>Booking #$testBookingID not found</p>";
}

// 4. Check if column exists
echo "<h2>4. Database Schema Check</h2>";
$schemaQuery = "DESCRIBE bookings";
$schemaResult = $conn->query($schemaQuery);
$hasMeetingLinkColumn = false;

while ($col = $schemaResult->fetch_assoc()) {
    if ($col['Field'] === 'meeting_link') {
        $hasMeetingLinkColumn = true;
        echo "<p class='good'>✓ Column 'meeting_link' exists in bookings table</p>";
        echo "<pre>Type: {$col['Type']}, Null: {$col['Null']}, Default: {$col['Default']}</pre>";
    }
}

if (!$hasMeetingLinkColumn) {
    echo "<p class='bad'>✗ Column 'meeting_link' DOES NOT exist in bookings table!</p>";
    echo "<p><strong>Fix:</strong> Run this SQL:</p>";
    echo "<pre>ALTER TABLE bookings ADD COLUMN meeting_link VARCHAR(255) DEFAULT NULL;</pre>";
}

// 5. Check notification functions
echo "<h2>5. System Check</h2>";
$checksToRun = [
    'NotificationSystem class' => file_exists('includes/functions/notifications.php'),
    'Email templates' => file_exists('includes/functions/email_templates.php'),
    'PHPMailer library' => class_exists('PHPMailer\\PHPMailer\\PHPMailer'),
];

foreach ($checksToRun as $check => $result) {
    $status = $result ? "<span class='good'>✓ Available</span>" : "<span class='bad'>✗ Missing</span>";
    echo "<p><strong>$check:</strong> $status</p>";
}

echo "<hr>";
echo "<h2>Summary & Recommendations</h2>";
echo "<ol>";

if ($noLinks > 0) {
    echo "<li><span class='warn'>⚠ $noLinks active bookings don't have meeting links</span><br>";
    echo "→ Admins need to add meeting links via the Bookings page</li>";
}

if ($meetingLinkNotifs == 0) {
    echo "<li><span class='warn'>⚠ No meeting link notifications found in recent history</span><br>";
    echo "→ Either no links have been added recently, or notification system isn't firing</li>";
}

echo "<li>To add a meeting link:<br>";
echo "→ Admin Dashboard → Bookings → Click on a booking → Add meeting link → Save</li>";

echo "<li>To verify frontend display:<br>";
echo "→ Open browser console on appointments page<br>";
echo "→ Type: <code>allAppointments[0]</code><br>";
echo "→ Check if 'meetingLink' property exists and has a value</li>";

echo "</ol>";

$conn->close();
?>
