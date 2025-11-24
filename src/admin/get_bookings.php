<?php
require_once '../includes/functions/config.php';
require_once '../includes/functions/session.php';

// Ensure user is admin
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'Admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

global $conn;

// Fetch bookings
$sql = "SELECT bookingID, event_date, event_time, event_type, booking_status, total_amount FROM bookings";
$result = $conn->query($sql);

$events = [];

while ($row = $result->fetch_assoc()) {
    $start = $row['event_date'] . 'T' . $row['event_time'];
    
    // Calculate end time (assuming default duration or if stored)
    // For now, let's assume 2 hours duration if not specified, or just use start
    // If you have duration, you can calculate end.
    // Let's just use start for now.
    
    $color = '#d4af37'; // Default gold
    if ($row['booking_status'] === 'Pending') $color = '#ffc107';
    if ($row['booking_status'] === 'Cancelled') $color = '#dc3545';
    if ($row['booking_status'] === 'Completed') $color = '#0d6efd';

    $events[] = [
        'id' => $row['bookingID'],
        'title' => $row['event_type'] . ' (' . $row['booking_status'] . ')',
        'start' => $start,
        'backgroundColor' => $color,
        'borderColor' => $color,
        'extendedProps' => [
            'status' => $row['booking_status'],
            'amount' => $row['total_amount']
        ]
    ];
}

header('Content-Type: application/json');
echo json_encode($events);
?>
