<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../..');
$dotenv->load();

// Set a consistent timezone to prevent calculation errors between PHP and MySQL.
date_default_timezone_set('UTC');

// ============================================================================
// ENVIRONMENT-BASED ERROR HANDLING
// ============================================================================

// Determine environment (production, staging, development)
$environment = $_ENV['APP_ENV'] ?? 'production';

if ($environment === 'production') {
    // Production: Hide errors from users, log to file
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../../../logs/php-errors.log');
} else {
    // Development/Staging: Show errors for debugging
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Create logs directory if it doesn't exist
$logDir = __DIR__ . '/../../../logs';
if (!file_exists($logDir)) {
    mkdir($logDir, 0755, true);
}

// ============================================================================
// DATABASE CONNECTION
// ============================================================================

$conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_NAME']);

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Database connection failed. Please try again later.");
}

// Set the MySQL connection's timezone to UTC to match PHP's timezone.
$conn->query("SET time_zone = '+00:00'");

// Set charset to prevent SQL injection via charset
$conn->set_charset("utf8mb4");
