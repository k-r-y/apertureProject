<?php
require_once '../../includes/functions/config.php';
require_once '../../includes/functions/session.php';

header('Content-Type: application/json');

// Check admin access
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$bookingID = $_GET['bookingID'] ?? 0;

if (!$bookingID) {
    echo json_encode(['success' => false, 'message' => 'Invalid Booking ID']);
    exit;
}

try {
    $sql = "
        SELECT 
            b.*,
            u.FirstName, u.LastName, u.Email, u.contactNo,
            p.package_name, p.package_price
        FROM bookings b
        JOIN users u ON b.userID = u.userID
        JOIN packages p ON b.packageID = p.packageID
        WHERE b.bookingID = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $bookingID);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();

    if ($booking) {
        echo json_encode(['success' => true, 'booking' => $booking]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Booking not found']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
