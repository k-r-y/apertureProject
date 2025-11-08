<?php
require_once 'includes/functions/auth.php';
require_once 'includes/functions/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['token'])) {
    $result = verifyEmail($_GET['token']);

    if ($result['success']) {
        header("refresh:3;url=completeProfile.php");
        setSession($result['userId']);
    } else {
        echo "Invalid token or expired";
    }
}
