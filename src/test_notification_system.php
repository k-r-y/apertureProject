<?php
require_once 'includes/functions/config.php';
require_once 'includes/functions/notifications.php';

// Mock session for testing if needed, but NotificationSystem doesn't use session directly
// It uses passed arguments.

echo "Testing NotificationSystem...\n";

try {
    $notifier = new NotificationSystem($conn);
    
    // Test Data
    $userId = 1046; // Valid user ID from DB
    $email = "test@example.com";
    $name = "Test User";
    $bookingRef = "TEST01";
    
    // 1. Test In-App Notification (via private method wrapper or public method if I had one)
    // Since createInAppNotification is private, we test a public method that uses it.
    // Let's test sendStatusUpdate
    
    echo "Sending Status Update Notification...\n";
    $result = $notifier->sendStatusUpdate($email, $name, $userId, $bookingRef, 'confirmed', date('Y-m-d'));
    
    if ($result) {
        echo "Status Update Sent (Email might fail if SMTP not configured, but method returned true/false based on email).\n";
    } else {
        echo "Status Update Failed (likely email error).\n";
    }
    
    // Check Database for Notification
    $stmt = $conn->prepare("SELECT * FROM notifications WHERE userID = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    $notif = $res->fetch_assoc();
    
    if ($notif) {
        echo "In-App Notification Found:\n";
        print_r($notif);
    } else {
        echo "No In-App Notification found!\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
