<?php
/**
 * Event Reminder Cron Job
 * 
 * This script should be run daily (e.g., via cron job or Task Scheduler)
 * to send automated reminders to clients about upcoming events.
 * 
 * Cron example: 0 9 * * * php /path/to/send_event_reminders.php
 * (Runs daily at 9 AM)
 */

require_once '../includes/functions/config.php';
require_once '../includes/functions/notifications.php';

// Days before event to send reminders
$reminderDays = [7, 3, 1];

foreach ($reminderDays as $days) {
    $targetDate = date('Y-m-d', strtotime("+{$days} days"));
    
    // Get bookings for the target date
    $query = "SELECT 
                b.bookingID,
                b.event_date,
                b.event_time_start,
                b.event_location,
                b.event_type,
                u.Email,
                u.FirstName,
                p.name as packageName
              FROM bookings b
              JOIN users u ON b.userID = u.userID
              JOIN packages p ON b.packageID = p.packageID
              WHERE b.event_date = ?
              AND b.booking_status IN ('confirmed', 'post_production')
              AND b.bookingID NOT IN (
                  SELECT bookingID FROM booking_logs 
                  WHERE action = 'reminder_sent' 
                  AND details LIKE ?
              )";
    
    $stmt = $conn->prepare($query);
    $reminderKey = "%{$days}_day_reminder%";
    $stmt->bind_param("ss", $targetDate, $reminderKey);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $notification = new NotificationSystem($conn);
    
    while ($booking = $result->fetch_assoc()) {
        try {
            // Send reminder email
            $sent = sendEventReminder(
                $notification,
                $booking['Email'],
                $booking['FirstName'],
                $days,
                $booking['event_date'],
                $booking['event_time_start'],
                $booking['event_location'],
                $booking['event_type'],
                $booking['packageName']
            );
            
            if ($sent) {
                // Log reminder sent
                $logQuery = "INSERT INTO booking_logs (bookingID, userID, action, details) 
                             VALUES (?, NULL, 'reminder_sent', ?)";
                $logStmt = $conn->prepare($logQuery);
                $logDetails = "{$days}_day_reminder sent successfully";
                $logStmt->bind_param("is", $booking['bookingID'], $logDetails);
                $logStmt->execute();
                
                echo "Sent {$days}-day reminder for booking #{$booking['bookingID']}\n";
            }
        } catch (Exception $e) {
            error_log("Reminder error for booking #{$booking['bookingID']}: " . $e->getMessage());
        }
    }
}

function sendEventReminder($notification, $email, $name, $days, $date, $time, $location, $eventType, $package) {
    $subject = "Reminder: Your {$eventType} is in {$days} days!";
    
    $body = "
    <div style='font-family: \"Inter\", sans-serif; max-width: 600px; margin: 0 auto; color: #333;'>
        <div style='background: #0a0a0a; padding: 30px; text-align: center;'>
            <h1 style='color: #d4af37; margin: 0; font-family: \"Playfair Display\", serif;'>APERTURE</h1>
        </div>
        <div style='padding: 40px; background: #fff;'>
            <h2 style='color: #0a0a0a; margin-top: 0;'>Event Reminder</h2>
            <p>Dear {$name},</p>
            <p>This is a friendly reminder that your <strong>{$eventType}</strong> is coming up in <strong>{$days} days</strong>!</p>
            
            <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 30px 0;'>
                <h3 style='margin-top: 0; font-size: 16px; color: #666;'>Event Details</h3>
                <table style='width: 100%; border-collapse: collapse;'>
                    <tr>
                        <td style='padding: 8px 0; color: #666;'>Date:</td>
                        <td style='padding: 8px 0; text-align: right; font-weight: 600;'>" . date('F d, Y', strtotime($date)) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; color: #666;'>Time:</td>
                        <td style='padding: 8px 0; text-align: right; font-weight: 600;'>" . date('g:i A', strtotime($time)) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; color: #666;'>Location:</td>
                        <td style='padding: 8px 0; text-align: right; font-weight: 600;'>{$location}</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; color: #666;'>Package:</td>
                        <td style='padding: 8px 0; text-align: right; font-weight: 600;'>{$package}</td>
                    </tr>
                </table>
            </div>

            <p>We're excited to capture your special moments! If you have any questions or need to make changes, please contact us.</p>
            
            <div style='text-align: center; margin-top: 30px;'>
                <a href='http://localhost/aperture/src/user/appointments.php' style='background: #0a0a0a; color: #d4af37; text-decoration: none; padding: 12px 25px; border-radius: 4px; font-weight: bold;'>View Booking Details</a>
            </div>
        </div>
        <div style='background: #f8f9fa; padding: 30px; text-align: center; font-size: 12px; color: #999;'>
            &copy; " . date('Y') . " Aperture Photography. All rights reserved.
        </div>
    </div>";
    
    try {
        $notification->mail->clearAddresses();
        $notification->mail->addAddress($email, $name);
        $notification->mail->Subject = $subject;
        $notification->mail->Body = $body;
        $notification->mail->AltBody = "Reminder: Your {$eventType} is in {$days} days on " . date('F d, Y', strtotime($date));
        
        return $notification->mail->send();
    } catch (Exception $e) {
        error_log("Email send error: " . $e->getMessage());
        return false;
    }
}

echo "Event reminder job completed at " . date('Y-m-d H:i:s') . "\n";
?>
