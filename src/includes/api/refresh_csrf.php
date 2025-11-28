<?php
/**
 * CSRF Token Refresh API
 * Generates a new CSRF token for long-running forms
 */

require_once '../../includes/functions/csrf.php';
require_once '../../includes/functions/session.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['userId'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Session expired. Please log in again.'
    ]);
    exit;
}

// Generate new CSRF token
$newToken = generateCSRFToken();

echo json_encode([
    'success' => true,
    'token' => $newToken
]);
