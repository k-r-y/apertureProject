<?php
require_once '../../includes/functions/config.php';
require_once '../../includes/functions/session.php';

// Check admin access
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'Admin') {
    http_response_code(403);
    die('Unauthorized');
}

$type = $_GET['type'] ?? 'bookings';

// Set CSV headers
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $type . '_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');

try {
    switch ($type) {
        case 'bookings':
            exportBookings($output);
            break;
        case 'revenue':
            exportRevenue($output);
            break;
        case 'clients':
            exportClients($output);
            break;
        default:
            fputcsv($output, ['Error: Invalid export type']);
    }
} catch (Exception $e) {
    fputcsv($output, ['Error: ' . $e->getMessage()]);
}

fclose($output);

function exportBookings($output) {
    global $conn;
    
    // CSV Header
    fputcsv($output, ['Booking ID', 'Client Name', 'Email', 'Phone', 'Event Type', 'Event Date', 'Event Time', 'Location', 'Package', 'Total Amount', 'Downpayment', 'Balance', 'Status', 'Created At']);
    
    $query = "SELECT 
                b.bookingID,
                CONCAT(u.FirstName, ' ', u.LastName) as client_name,
                u.Email,
                u.contactNo,
                b.event_type,
                b.event_date,
                CONCAT(b.event_time_start, ' - ', b.event_time_end) as event_time,
                b.event_location,
                p.packageName as package_name,
                b.total_amount,
                b.downpayment_amount,
                (b.total_amount - b.downpayment_amount) as balance,
                b.booking_status,
                b.created_at
              FROM bookings b
              JOIN users u ON b.userID = u.userID
              JOIN packages p ON b.packageID = p.packageID
              ORDER BY b.created_at DESC";
    
    $result = $conn->query($query);
    
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['bookingID'],
            $row['client_name'],
            $row['Email'],
            $row['contactNo'] ?? 'N/A',
            $row['event_type'],
            $row['event_date'],
            $row['event_time'],
            $row['event_location'],
            $row['package_name'],
            number_format($row['total_amount'], 2),
            number_format($row['downpayment_amount'], 2),
            number_format($row['balance'], 2),
            ucwords(str_replace('_', ' ', $row['booking_status'])),
            $row['created_at']
        ]);
    }
}

function exportRevenue($output) {
    global $conn;
    
    // CSV Header
    fputcsv($output, ['Month', 'Year', 'Total Bookings', 'Total Revenue', 'Avg Booking Value']);
    
    $query = "SELECT 
                MONTH(created_at) as month,
                YEAR(created_at) as year,
                COUNT(*) as booking_count,
                SUM(total_amount) as revenue,
                AVG(total_amount) as avg_value
              FROM bookings
              WHERE booking_status != 'cancelled'
              GROUP BY YEAR(created_at), MONTH(created_at)
              ORDER BY year DESC, month DESC";
    
    $result = $conn->query($query);
    
    while ($row = $result->fetch_assoc()) {
        $monthName = date('F', mktime(0, 0, 0, $row['month'], 1));
        fputcsv($output, [
            $monthName,
            $row['year'],
            $row['booking_count'],
            number_format($row['revenue'], 2),
            number_format($row['avg_value'], 2)
        ]);
    }
}

function exportClients($output) {
    global $conn;
    
    // CSV Header
    fputcsv($output, ['Client ID', 'Name', 'Email', 'Phone', 'Total Bookings', 'Total Spent', 'Last Booking Date', 'Registered Date']);
    
    $query = "SELECT 
                u.userID,
                CONCAT(u.FirstName, ' ', u.LastName) as client_name,
                u.Email,
                u.contactNo,
                COUNT(b.bookingID) as total_bookings,
                COALESCE(SUM(b.total_amount), 0) as total_spent,
                MAX(b.event_date) as last_booking,
                u.created_at
              FROM users u
              LEFT JOIN bookings b ON u.userID = b.userID AND b.booking_status != 'cancelled'
              WHERE u.Role = 'User'
              GROUP BY u.userID
              ORDER BY total_spent DESC";
    
    $result = $conn->query($query);
    
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['userID'],
            $row['client_name'],
            $row['Email'],
            $row['contactNo'] ?? 'N/A',
            $row['total_bookings'],
            number_format($row['total_spent'], 2),
            $row['last_booking'] ?? 'Never',
            $row['created_at']
        ]);
    }
}
?>
