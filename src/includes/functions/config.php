<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

// Set a consistent timezone to prevent calculation errors between PHP and MySQL.
date_default_timezone_set('UTC');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../..');
$dotenv->load();

$host = $_ENV['DB_HOST'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];
$db   = $_ENV['DB_NAME'];

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set the MySQL connection's timezone to UTC to match PHP's timezone.
$conn->query("SET time_zone = '+00:00'");
