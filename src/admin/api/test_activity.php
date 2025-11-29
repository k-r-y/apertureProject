<?php
// Simple test to see if API returns data
require_once '../../includes/functions/config.php';
require_once '../../includes/functions/session.php';
require_once '../../includes/functions/activity_logger.php';

if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'Admin') {
    die(json_encode(['error' => 'Unauthorized']));
}

header('Content-Type: application/json');

try {
    $activities = getUserActivities(null);
    echo json_encode([
        'success' => true,
        'count' => count($activities),
        'sample' => isset($activities[0]) ? $activities[0] : null,
        'activities' => $activities
    ], JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
