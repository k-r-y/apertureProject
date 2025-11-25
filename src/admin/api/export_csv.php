<?php
require_once '../../includes/functions/config.php';
require_once '../../includes/functions/session.php';

// Ensure user is logged in and is an Admin
if (!isset($_SESSION['userId']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    die("Unauthorized Access");
}

$type = $_GET['type'] ?? 'bookings';

if ($type === 'bookings') {
    $filename = "bookings_report_" . date('Y-m-d') . ".csv";
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // Add BOM for Excel compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Header Row
    fputcsv($output, ['Booking ID', 'Client Name', 'Email', 'Event Date', 'Event Type', 'Location', 'Status', 'Total Amount', 'Paid Amount', 'Balance']);
    
    // Fetch Data
    $sql = "
        SELECT 
            b.bookingID,
            CONCAT(u.FirstName, ' ', u.LastName) as client_name,
            u.Email,
            b.event_date,
            b.event_type,
            b.event_location,
            b.booking_status,
            b.total_amount,
            (CASE WHEN b.is_fully_paid = 1 THEN b.total_amount ELSE b.downpayment_amount END) as paid_amount,
            (b.total_amount - (CASE WHEN b.is_fully_paid = 1 THEN b.total_amount ELSE b.downpayment_amount END)) as balance
        FROM bookings b
        JOIN users u ON b.userID = u.userID
        ORDER BY b.event_date DESC
    ";
    
    $result = $conn->query($sql);
    
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['bookingID'],
            $row['client_name'],
            $row['Email'],
            $row['event_date'],
            $row['event_type'],
            $row['event_location'],
            ucfirst($row['booking_status']),
            number_format($row['total_amount'], 2),
            number_format($row['paid_amount'], 2),
            number_format($row['balance'], 2)
        ]);
    }
    
    fclose($output);
    exit;
}
?>
