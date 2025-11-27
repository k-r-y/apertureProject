<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/email_templates.php';

class NotificationSystem {
    
    private $mail;

    public function __construct() {
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

    public function sendStatusUpdate($email, $name, $bookingRef, $newStatus, $date) {
        try {
            $this->mail->addAddress($email, $name);
            $this->mail->Subject = "Booking Update: #{$bookingRef} is " . ucwords(str_replace('_', ' ', $newStatus));
            $this->mail->Body = EmailTemplates::getStatusUpdate($name, $bookingRef, $newStatus, $date);
            $this->mail->AltBody = "Your booking #{$bookingRef} status has been updated to " . ucwords(str_replace('_', ' ', $newStatus));
            
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email Error: " . $this->mail->ErrorInfo);
            return false;
        }
    }

    public function sendBookingConfirmation($email, $name, $bookingRef, $date, $time, $location, $total, $downpayment) {
        try {
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
        try {
            // Clear previous recipients
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
    public function sendMeetingLinkNotification($email, $name, $bookingRef, $meetingLink, $date) {
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
}
