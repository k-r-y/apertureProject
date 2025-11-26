<?php
require_once '../../includes/functions/config.php';

$tables = ['bookings', 'booking_logs', 'invoices', 'users', 'packages'];
$missing = [];

foreach ($tables as $table) {
    $check = $conn->query("SHOW TABLES LIKE '$table'");
    if ($check->num_rows == 0) {
        $missing[] = $table;
    }
}

if (empty($missing)) {
    echo json_encode(['success' => true, 'message' => 'All tables exist']);
} else {
    echo json_encode(['success' => false, 'message' => 'Missing tables: ' . implode(', ', $missing)]);
}
