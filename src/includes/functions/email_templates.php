<?php

class EmailTemplates {
    
    private static function getHeader() {
        return "
        <div style='font-family: \"Inter\", sans-serif; max-width: 600px; margin: 0 auto; color: #333;'>
            <div style='background: #0a0a0a; padding: 30px; text-align: center;'>
                <h1 style='color: #d4af37; margin: 0; font-family: \"Playfair Display\", serif;'>APERTURE</h1>
            </div>
            <div style='padding: 40px; background: #fff;'>";
    }

    private static function getFooter() {
        return "
            </div>
            <div style='background: #f8f9fa; padding: 30px; text-align: center; font-size: 12px; color: #999;'>
                &copy; " . date('Y') . " Aperture Photography. All rights reserved.
            </div>
        </div>";
    }

    public static function getBookingConfirmation($name, $bookingRef, $date, $time, $location, $total, $downpayment) {
        $body = "
            <h2 style='color: #0a0a0a; margin-top: 0;'>Booking Received</h2>
            <p>Dear {$name},</p>
            <p>Thank you for choosing Aperture. We have received your booking request and it is currently <strong>Pending</strong> review.</p>
            
            <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 30px 0;'>
                <h3 style='margin-top: 0; font-size: 16px; color: #666;'>Booking Details</h3>
                <table style='width: 100%; border-collapse: collapse;'>
                    <tr>
                        <td style='padding: 8px 0; color: #666;'>Reference No:</td>
                        <td style='padding: 8px 0; text-align: right; font-weight: 600; color: #d4af37;'>#{$bookingRef}</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; color: #666;'>Event Date:</td>
                        <td style='padding: 8px 0; text-align: right; font-weight: 600;'>{$date}</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; color: #666;'>Time:</td>
                        <td style='padding: 8px 0; text-align: right; font-weight: 600;'>{$time}</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; color: #666;'>Location:</td>
                        <td style='padding: 8px 0; text-align: right; font-weight: 600;'>{$location}</td>
                    </tr>
                </table>
            </div>

            <h3 style='font-size: 16px; color: #666;'>Payment Summary</h3>
            <table style='width: 100%; border-collapse: collapse; margin-bottom: 30px;'>
                <tr>
                    <td style='padding: 15px 10px; font-weight: bold;'>Total</td>
                    <td style='padding: 15px 10px; text-align: right; font-weight: bold; color: #d4af37;'>{$total}</td>
                </tr>
                <tr>
                    <td style='padding: 5px 10px; font-size: 14px; color: #666;'>Downpayment (25%)</td>
                    <td style='padding: 5px 10px; text-align: right; font-size: 14px; color: #666;'>{$downpayment}</td>
                </tr>
            </table>

            <p>Please allow 24-48 hours for our team to review your booking and verify your payment. You will receive another email once your booking is confirmed.</p>";

        return self::getHeader() . $body . self::getFooter();
    }

    public static function getStatusUpdate($name, $bookingRef, $newStatus, $date) {
        $statusDisplay = ucwords(str_replace('_', ' ', $newStatus));
        $color = '#d4af37'; // Default gold
        
        if ($newStatus == 'confirmed') $color = '#28a745';
        if ($newStatus == 'cancelled') $color = '#dc3545';
        if ($newStatus == 'completed') $color = '#007bff';

        $body = "
            <h2 style='color: #0a0a0a; margin-top: 0;'>Booking Status Update</h2>
            <p>Dear {$name},</p>
            <p>The status of your booking <strong>#{$bookingRef}</strong> for {$date} has been updated.</p>
            
            <div style='text-align: center; margin: 40px 0;'>
                <span style='background: {$color}; color: #fff; padding: 10px 20px; border-radius: 50px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;'>
                    {$statusDisplay}
                </span>
            </div>

            <p>You can view the full details of your booking by logging into your account.</p>
            
            <div style='text-align: center; margin-top: 30px;'>
                <a href='http://localhost/aperture/src/logIn.php' style='background: #0a0a0a; color: #d4af37; text-decoration: none; padding: 12px 25px; border-radius: 4px; font-weight: bold;'>View Booking</a>
            </div>";

        return self::getHeader() . $body . self::getFooter();
    }
}
?>
