<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "Testing config.php... ";
try {
    require_once '../../includes/functions/config.php';
    echo "OK<br>";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

echo "Testing auth.php... ";
try {
    require_once '../../includes/functions/auth.php';
    echo "OK<br>";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

echo "Testing booking_workflow.php... ";
try {
    require_once '../../includes/functions/booking_workflow.php';
    echo "OK<br>";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

echo "Testing notifications.php... ";
try {
    require_once '../../includes/functions/notifications.php';
    echo "OK<br>";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}
