<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/email_templates.php';

class NotificationSystem {
    
    private $mail;
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->mail = new PHPMailer(true);
        $this->setupServer();
    }

    private function setupServer() {
        try {
            $this->mail->isSMTP();
            $this->mail->Host       = $_ENV['SMTP_HOST'];
            $this->mail->SMTPAuth   = true;
            $this->mail->Username   = $_ENV['SMTP_USERNAME'];
            $this->mail->Password   = $_ENV['SMTP_PASSWORD'];
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port       = $_ENV['SMTP_PORT'];
            $this->mail->setFrom($_ENV['SMTP_USERNAME'], 'Aperture Studios');
            $this->mail->isHTML(true);
        } catch (Exception $e) {
            error_log("Mailer Setup Error: " . $e->getMessage());
        }
    }

    // Helper to create in-app notification
    private function createInAppNotification($userId, $title, $message, $type, $link = null) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO notifications (userID, title, message, type, link, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("issss", $userId, $title, $message, $type, $link);
            $stmt->execute();
            $stmt->close();
            return true;
        } catch (Exception $e) {
            error_log("In-App Notification Error: " . $e->getMessage());
            return false;
        }
    }

    public function sendStatusUpdate($email, $name, $userId, $bookingRef, $newStatus, $date) {
        // 1. Send In-App Notification
        $statusDisplay = ucwords(str_replace('_', ' ', $newStatus));
        $this->createInAppNotification(
            $userId,
            "Booking Status Update",
            "Your booking #{$bookingRef} is now {$statusDisplay}.",
            "booking_update",
            "appointments.php?booking_id=" . $bookingRef // Assuming we can link by ref or ID, but ref is safer for display
        );

        // 2. Send Email
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($email, $name);
            $this->mail->Subject = "Booking Update: #{$bookingRef} is " . $statusDisplay;
            $this->mail->Body = EmailTemplates::getStatusUpdate($name, $bookingRef, $newStatus, $date);
            $this->mail->AltBody = "Your booking #{$bookingRef} status has been updated to " . $statusDisplay;
            
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email Error: " . $this->mail->ErrorInfo);
            return false;
        }
    }

    public function sendBookingConfirmation($email, $name, $userId, $bookingRef, $date, $time, $location, $total, $downpayment) {
        // 1. Send In-App Notification
        $this->createInAppNotification(
            $userId,
            "Booking Received",
            "We have received your booking #{$bookingRef}. It is currently pending review.",
            "booking_created",
            "appointments.php"
        );

        // 2. Send Email
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($email, $name);
            $this->mail->Subject = "Booking Confirmation - #{$bookingRef}";
            $this->mail->Body = EmailTemplates::getBookingConfirmation($name, $bookingRef, $date, $time, $location, $total, $downpayment);
            $this->mail->AltBody = "Booking #{$bookingRef} received. Total: {$total}.";
            
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email Error: " . $this->mail->ErrorInfo);
            return false;
        }
    }

    public function sendAdminNewBooking($adminEmail, $bookingRef, $clientName, $eventType, $date, $total) {
        // Admin In-App Notification (Assuming Admin ID is 1 or we fetch all admins)
        // For now, let's assume we notify all admins or a specific admin. 
        // Since we don't have admin IDs handy here, we might skip in-app for admin in this specific method 
        // OR we fetch admin IDs. Let's fetch admin IDs.
        
        $adminIds = [];
        $result = $this->conn->query("SELECT userID FROM users WHERE role = 'Admin'");
        while($row = $result->fetch_assoc()) {
            $this->createInAppNotification(
                $row['userID'],
                "New Booking Request",
                "New {$eventType} booking #{$bookingRef} from {$clientName}.",
                "new_booking",
                "bookings.php?search={$bookingRef}"
            );
        }

        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($adminEmail, 'Admin');
            $this->mail->Subject = "New Booking Request - #{$bookingRef}";
            $this->mail->Body = EmailTemplates::getAdminNewBookingNotification('Admin', $bookingRef, $clientName, $eventType, $date, $total);
            $this->mail->AltBody = "New booking request #{$bookingRef} from {$clientName}.";
            
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Admin Email Error: " . $this->mail->ErrorInfo);
            return false;
        }
    }

    public function sendMeetingLinkNotification($email, $name, $userId, $bookingRef, $meetingLink, $date) {
        // 1. In-App
        $this->createInAppNotification(
            $userId,
            "Meeting Link Added",
            "A meeting link has been added for booking #{$bookingRef}.",
            "meeting_link",
            "appointments.php"
        );

        // 2. Email
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($email, $name);
            $this->mail->Subject = "Meeting Link Update - #{$bookingRef}";
            $this->mail->Body = EmailTemplates::getMeetingLinkNotification($name, $bookingRef, $meetingLink, $date);
            $this->mail->AltBody = "A meeting link has been updated for your booking #{$bookingRef}. Link: {$meetingLink}";
            
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email Error: " . $this->mail->ErrorInfo);
            return false;
        }
    }

    public function sendPhotoUploadNotification($email, $name, $userId, $bookingRef, $count, $link = null) {
        // 1. In-App
        $this->createInAppNotification(
            $userId,
            "Photos Ready",
            "{$count} new photos uploaded for booking #{$bookingRef}!",
            "photos_ready",
            "myPhotos.php"
        );

        // 2. Email
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($email, $name);
            $this->mail->Subject = "Your Photos Are Ready! - #{$bookingRef}";
            $this->mail->Body = EmailTemplates::getPhotoUploadNotification($name, $bookingRef, $count, $link);
            $this->mail->AltBody = "Your photos for booking #{$bookingRef} are ready! {$count} new photos have been uploaded.";
            
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email Error: " . $this->mail->ErrorInfo);
            return false;
        }
    }

    public function sendAdminCancellationRequest($adminEmail, $bookingRef, $clientName, $eventType, $refundAmount) {
        // 1. Notify Admins In-App
        $result = $this->conn->query("SELECT userID FROM users WHERE role = 'Admin'");
        while($row = $result->fetch_assoc()) {
            $this->createInAppNotification(
                $row['userID'],
                "Cancellation Request",
                "{$clientName} requested cancellation for #{$bookingRef}. Refund: ₱" . number_format($refundAmount, 2),
                "cancellation_request",
                "refunds.php"
            );
        }

        // 2. Email Admin
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($adminEmail, 'Admin');
            $this->mail->Subject = "Cancellation Request - #{$bookingRef}";
            // We might need a template for this, but for now simple HTML is fine or we add to EmailTemplates
            $this->mail->Body = EmailTemplates::getAdminCancellationRequest('Admin', $bookingRef, $clientName, $eventType, $refundAmount);
            $this->mail->AltBody = "Cancellation request for #{$bookingRef} from {$clientName}. Refund: " . number_format($refundAmount, 2);
            
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Admin Email Error: " . $this->mail->ErrorInfo);
            return false;
        }
    }
    public function sendUserCancellationSubmitted($email, $name, $userId, $bookingRef, $refundAmount) {
        // 1. In-App Notification
        $this->createInAppNotification(
            $userId,
            "Cancellation Submitted",
            "Your cancellation request for booking #{$bookingRef} has been submitted. Refund pending: ₱" . number_format($refundAmount, 2),
            "cancellation_submitted",
            "appointments.php"
        );

        // 2. Email
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($email, $name);
            $this->mail->Subject = "Cancellation Request Submitted - #{$bookingRef}";
            // Using a simple body for now or we could add to templates
            $this->mail->Body = "
                <div style='font-family: sans-serif; color: #333;'>
                    <h2>Cancellation Request Received</h2>
                    <p>Dear {$name},</p>
                    <p>We have received your cancellation request for booking <strong>#{$bookingRef}</strong>.</p>
                    <p><strong>Estimated Refund:</strong> ₱" . number_format($refundAmount, 2) . "</p>
                    <p>Your request is currently pending admin approval. You will be notified once it has been processed.</p>
                    <br>
                    <p>Best regards,<br>Aperture Studios</p>
                </div>
            ";
            $this->mail->AltBody = "Cancellation request submitted for #{$bookingRef}. Estimated refund: " . number_format($refundAmount, 2);
            
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email Error: " . $this->mail->ErrorInfo);
            return false;
        }
    }
}
