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
$sql = "SELECT bookingID, event_date, event_time_start, event_time_end, event_type, booking_status, total_amount FROM bookings";
$result = $conn->query($sql);

$events = [];

while ($row = $result->fetch_assoc()) {
    $start = $row['event_date'] . 'T' . $row['event_time_start'];
    $end = $row['event_date'] . 'T' . $row['event_time_end'];
    
    $color = '#d4af37'; // Default gold
    if ($row['booking_status'] === 'pending') $color = '#ffc107';
    if ($row['booking_status'] === 'cancelled') $color = '#dc3545';
    if ($row['booking_status'] === 'completed') $color = '#0d6efd';
    if ($row['booking_status'] === 'confirmed') $color = '#198754';

    $events[] = [
        'id' => $row['bookingID'],
        'title' => $row['event_type'] . ' (' . ucfirst($row['booking_status']) . ')',
        'start' => $start,
        'end' => $end,
        'backgroundColor' => $color,
        'borderColor' => $color,
        'extendedProps' => [
            'status' => ucfirst($row['booking_status']),
            'amount' => $row['total_amount']
        ]
    ];
}

header('Content-Type: application/json');
echo json_encode($events);
?>
