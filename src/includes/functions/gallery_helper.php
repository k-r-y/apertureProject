<?php
function generateGalleryCredentials($bookingId) {
    global $conn;
    
    // Generate secure token
    $token = bin2hex(random_bytes(32));
    
    // Generate 6-digit PIN
    $pin = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
    
    $stmt = $conn->prepare("UPDATE bookings SET gallery_token = ?, gallery_pin = ? WHERE bookingID = ?");
    $stmt->bind_param("ssi", $token, $pin, $bookingId);
    $stmt->execute();
    
    return ['token' => $token, 'pin' => $pin];
}
?>
