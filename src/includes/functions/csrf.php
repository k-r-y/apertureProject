<?php

function generateCSRFToken($forceNew = false)
{

    if (session_status() !== PHP_SESSION_ACTIVE) {
        throw new Exception("Session must be started before generation");
    }

    if ($forceNew or empty($_SESSION['csrfToken'])) {
        $_SESSION['csrfToken'] = bin2hex(random_bytes(32));
        $_SESSION['csrfTokenTime'] = time();
    }

    return $_SESSION['csrfToken'];
}

function validateCSRFToken($token, $regenerate = true)
{

    if (session_status() !== PHP_SESSION_ACTIVE) {
        error_log("CSRF: Session must be active during validation");
        return false;
    }

    if (empty($_SESSION['csrfToken'])) {
        error_log("CSRF: No token in session");
        return false;
    }

    if (empty($token)) {
        error_log("CSRF: No token provided in request");
        return false;
    }

    if (isset($_SESSION['csrfTokenTime'])) {
        $tokenAge = time() - $_SESSION['csrfTokenTime'];
        if ($tokenAge > 3600) {
            error_log("CSRF: Token expired (Age: " . $tokenAge . ' seconds ');
            unset($_SESSION['csrfTokenTime'], $_SESSION['csrfToken']);
            return false;
        }
    }


    $isValid = hash_equals($_SESSION['csrfToken'], $token);


    if (!$isValid) {
        error_log('CSRF: Token mismatch - Potential attack detected');
        error_log('CSRF: Expected: ' . substr($_SESSION['csrfToken'], 0, 10) . '...');
        error_log('CSRF: Received: ' . substr($token, 0, 10) . '...');
        error_log('CSRF: IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        error_log('CSRF: User Agent: ' . ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown'));
    }

    if ($isValid and $regenerate) {
        generateCSRFToken(true);
    }

    return $isValid;
}

function csrfField()
{
    $token = generateCSRFToken();
    echo ('<input type="hidden" name="csrfToken" value = "' . htmlspecialchars($token, ENT_QUOTES, "UTF-8") . '" >');
}

function getCSRFToken()
{
    return generateCSRFToken();
}

function handleCSRFFailure($returnUrl = null)
{
    error_log("CSRF: Validation Failed");

    $_SESSION['csrfError'] = "Your session has expired or the request is invalid. Please try again.";

    if ($returnUrl) {
        header("Location: $returnUrl");
    } else {
        header("Location: " . $_SESSION['PHP_SELF']);
    }

    exit;
}
