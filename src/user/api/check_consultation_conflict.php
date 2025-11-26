<?php
require_once '../../includes/functions/config.php';
require_once '../../includes/functions/session.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$consultationDate = $data['consultationDate'] ?? '';
$startTime = $data['startTime'] ?? '';
$endTime = $data['endTime'] ?? '';

// Validate inputs
if (empty($consultationDate) || empty($startTime) || empty($endTime)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Validate date is not in the past
$selectedDate = strtotime($consultationDate);
$today = strtotime(date('Y-m-d'));

if ($selectedDate < $today) {
    echo json_encode(['success' => false, 'message' => 'Cannot schedule consultation in the past']);
    exit;
}

// Validate time is not in the past (if today)
if ($selectedDate == $today) {
    $currentTime = date('H:i:s');
    if ($startTime <= $currentTime) {
        echo json_encode(['success' => false, 'message' => 'Cannot schedule consultation in the past']);
        exit;
    }
}

// Validate duration (max 1.5 hours = 90 minutes)
$startTimestamp = strtotime($startTime);
$endTimestamp = strtotime($endTime);
$durationMinutes = ($endTimestamp - $startTimestamp) / 60;

if ($durationMinutes <= 0) {
    echo json_encode(['success' => false, 'message' => 'End time must be after start time']);
    exit;
}

if ($durationMinutes > 90) {
    echo json_encode(['success' => false, 'message' => 'Consultation duration cannot exceed 1.5 hours']);
    exit;
}

// Check for conflicts with existing consultations
$stmt = $conn->prepare("
    SELECT bookingID, consultation_date, consultation_start_time, consultation_end_time
    FROM bookings 
    WHERE consultation_date =? 
    AND consultation_start_time IS NOT NULL 
    AND consultation_end_time IS NOT NULL
    AND booking_status NOT IN ('cancelled', 'refunded')
    AND (
        (consultation_start_time < ? AND consultation_end_time > ?) OR
        (consultation_start_time < ? AND consultation_end_time > ?) OR
        (consultation_start_time >= ? AND consultation_end_time <= ?)
    )
");

$stmt->bind_param("sssssss", 
    $consultationDate, 
    $endTime, $startTime,    // Existing starts before requested ends, and existing ends after requested starts
    $endTime, $endTime,      // Existing starts within requested period
    $startTime, $endTime     // Existing is completely within requested period
);

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $conflicts = [];
    while ($row = $result->fetch_assoc()) {
        $conflicts[] = [
            'startTime' => date('g:i A', strtotime($row['consultation_start_time'])),
            'endTime' => date('g:i A', strtotime($row['consultation_end_time']))
        ];
    }
    
    echo json_encode([
        'success' => false, 
        'message' => 'This time slot conflicts with existing consultations',
        'conflicts' => $conflicts
    ]);
    exit;
}

echo json_encode([
    'success' => true, 
    'message' => 'Time slot is available'
]);
?>
