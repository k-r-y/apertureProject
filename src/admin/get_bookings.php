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
    
    // Calculate end time (assuming 2 hours duration for visualization)
    $endTime = date('H:i:s', strtotime($row['event_time']) + 7200); // +2 hours
    $end = $row['event_date'] . 'T' . $endTime;
    
    $color = '#d4af37'; // Default gold
    if ($row['booking_status'] === 'Pending') $color = '#ffc107';
    if ($row['booking_status'] === 'Cancelled') $color = '#dc3545';
    if ($row['booking_status'] === 'Completed') $color = '#0d6efd';
    if ($row['booking_status'] === 'Confirmed') $color = '#198754';

    $events[] = [
        'id' => $row['bookingID'],
        'title' => $row['event_type'] . ' (' . $row['booking_status'] . ')',
        'start' => $start,
        'end' => $end,
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
