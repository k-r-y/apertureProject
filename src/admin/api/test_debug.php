<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Starting debug...<br>";

echo "Including config.php... ";
try {
    require_once '../../includes/functions/config.php';
    echo "OK<br>";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

echo "Including session.php... ";
try {
    require_once '../../includes/functions/session.php';
    echo "OK<br>";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

echo "Including auth.php... ";
try {
    require_once '../../includes/functions/auth.php';
    echo "OK<br>";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

echo "Checking DB connection... ";
if (isset($conn) && $conn instanceof mysqli) {
    echo "OK<br>";
} else {
    echo "Failed<br>";
}

echo "Done.";
?>
