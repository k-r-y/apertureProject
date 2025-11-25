<?php
/**
 * Simple Rate Limiter using PHP Sessions
 * 
 * @param int $limit Number of requests allowed
 * @param int $timeWindow Time window in seconds
 * @return bool True if request is allowed, False if limit exceeded
 */
function checkRateLimit($limit = 60, $timeWindow = 60) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $currentTime = time();
    
    if (!isset($_SESSION['rate_limit_requests'])) {
        $_SESSION['rate_limit_requests'] = [];
    }

    // Filter out requests older than the time window
    $_SESSION['rate_limit_requests'] = array_filter($_SESSION['rate_limit_requests'], function($timestamp) use ($currentTime, $timeWindow) {
        return ($currentTime - $timestamp) < $timeWindow;
    });

    // Check if limit exceeded
    if (count($_SESSION['rate_limit_requests']) >= $limit) {
        return false;
    }

    // Add current request
    $_SESSION['rate_limit_requests'][] = $currentTime;
    return true;
}

/**
 * Enforce Rate Limit
 * Terminates execution if limit exceeded
 */
function enforceRateLimit($limit = 60, $timeWindow = 60) {
    if (!checkRateLimit($limit, $timeWindow)) {
        http_response_code(429);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Too many requests. Please try again later.']);
        exit;
    }
}
?>
