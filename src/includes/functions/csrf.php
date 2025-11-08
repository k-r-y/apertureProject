<?php

/**
 * CSRF (Cross-Site Request Forgery) Protection Functions
 *
 * This file provides security functions to prevent CSRF attacks on forms.
 * CSRF attacks trick authenticated users into submitting malicious requests
 * without their knowledge. These functions use cryptographically secure tokens
 * to verify that form submissions are legitimate.
 *
 * Usage:
 * 1. Call csrfField() inside your form to generate a hidden token field
 * 2. Call validateCSRFToken() when processing the form submission
 * 3. Call handleCSRFFailure() if validation fails
 *
 * @package Aperture
 * @author  Aperture Team
 * @version 1.0
 */

/**
 * Generate a CSRF token and store it in the session
 *
 * Creates a cryptographically secure random token (64 characters) and stores
 * it in the session along with a timestamp. The token is used to verify that
 * form submissions originate from the user's own session.
 *
 * @param bool $forceNew If true, generates a new token even if one exists
 *
 * @return string The generated CSRF token (64 hexadecimal characters)
 *
 * @throws Exception If session is not started
 *
 * @example
 * // Generate a token at the top of your form page
 * session_start();
 * $token = generateCSRFToken();
 */
function generateCSRFToken($forceNew = false){

    // Verify that session is active before accessing $_SESSION
    if (session_status() !== PHP_SESSION_ACTIVE) {
        throw new Exception("Session must be started before generation");
    }

    // Generate new token if forced or if no token exists
    if ($forceNew or empty($_SESSION['csrfToken'])) {
        // random_bytes(32) generates 32 random bytes (256 bits)
        // bin2hex() converts to 64 hexadecimal characters for safe storage
        $_SESSION['csrfToken'] = bin2hex(random_bytes(32));

        // Store timestamp to implement token expiration (1 hour)
        $_SESSION['csrfTokenTime'] = time();
    }

    // Return the token for use in forms
    return $_SESSION['csrfToken'];
}

/**
 * Validate a CSRF token against the session token
 *
 * Performs multiple security checks to verify the token is valid:
 * - Session must be active
 * - Token must exist in session
 * - Token must be provided in request
 * - Token must not be expired (1 hour limit)
 * - Token must match exactly (using timing-attack-safe comparison)
 *
 * All validation failures are logged for security monitoring.
 *
 * @param string $token      The token from the form submission ($_POST['csrfToken'])
 * @param bool   $regenerate If true, generates a new token after successful validation
 *
 * @return bool True if token is valid, false otherwise
 *
 * @example
 * // In your form processing script
 * if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 *     if (!validateCSRFToken($_POST['csrfToken'] ?? '')) {
 *         handleCSRFFailure('form.php');
 *     }
 *     // Process form data...
 * }
 */
function validateCSRFToken($token, $regenerate = true){

    // Check 1: Verify session is active
    if (session_status() !== PHP_SESSION_ACTIVE) {
        error_log("CSRF: Session must be active during validation");
        return false;
    }

    // Check 2: Verify a token exists in the session
    if (empty($_SESSION['csrfToken'])) {
        error_log("CSRF: No token in session");
        return false;
    }

    // Check 3: Verify a token was provided in the request
    if (empty($token)) {
        error_log("CSRF: No token provided in request");
        return false;
    }

    // Check 4: Verify token has not expired (1 hour = 3600 seconds)
    if (isset($_SESSION['csrfTokenTime'])) {
        $tokenAge = time() - $_SESSION['csrfTokenTime'];
        if ($tokenAge > 3600) {
            error_log("CSRF: Token expired (Age: " . $tokenAge . " seconds" );
            // Remove expired token from session
            unset($_SESSION['csrfTokenTime'], $_SESSION['csrfToken']);
            return false;
        }
    }

    // Check 5: Verify token matches using timing-attack-safe comparison
    // hash_equals() prevents attackers from using timing analysis to guess the token
    $isValid = hash_equals($_SESSION['csrfToken'], $token);

    // Log potential CSRF attacks with detailed information
    if (!$isValid) {
        error_log('CSRF: Token mismatch - Potential attack detected');
        error_log('CSRF: Expected: ' . substr($_SESSION['csrfToken'], 0, 10) . '...');
        error_log('CSRF: Received: ' . substr($token, 0, 10) . '...');
        error_log('CSRF: IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        error_log('CSRF: User Agent: ' . ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown'));
    }

    // Regenerate token after successful validation for one-time use security
    if ($isValid and $regenerate) {
        generateCSRFToken(true);
    }

    return $isValid;
}

/**
 * Output a hidden form field containing the CSRF token
 *
 * Generates a hidden input field with the CSRF token, properly escaped
 * to prevent XSS attacks. This should be called inside your <form> tags.
 *
 * @return void Outputs HTML directly
 *
 * @example
 * // Inside your HTML form
 * <form method="POST" action="submit.php">
 *     <?php csrfField(); ?>
 *     <input type="text" name="username">
 *     <button type="submit">Submit</button>
 * </form>
 */
function csrfField(){
    // Generate or retrieve existing token
    $token = generateCSRFToken();

    // Output hidden field with XSS protection using htmlspecialchars()
    echo ('<input type="hidden" name="csrfToken" value = "' . htmlspecialchars($token, ENT_QUOTES, "UTF-8") . '" >');
}

/**
 * Get the CSRF token for AJAX requests
 *
 * Returns the current CSRF token without outputting HTML. Use this when
 * you need to include the token in JavaScript/AJAX requests.
 *
 * @return string The current CSRF token
 *
 * @example
 * // In your JavaScript
 * <script>
 * const csrfToken = "<?php echo getCSRFToken(); ?>";
 * fetch('/api/submit', {
 *     method: 'POST',
 *     headers: { 'X-CSRF-Token': csrfToken },
 *     body: formData
 * });
 * </script>
 */
function getCSRFToken(){
    return generateCSRFToken();
}

/**
 * Handle CSRF validation failures
 *
 * Logs the failure, stores an error message in the session, and redirects
 * the user back to the form. This provides a user-friendly experience while
 * maintaining security.
 *
 * @param string|null $returnUrl The URL to redirect to (default: current page)
 *
 * @return void Exits after redirect
 *
 * @example
 * // In your form processing script
 * if (!validateCSRFToken($_POST['csrfToken'] ?? '')) {
 *     handleCSRFFailure('register.php');
 * }
 */
function handleCSRFFailure($returnUrl = null){
    // Log the failure for security monitoring
    error_log("CSRF: Validation Failed");

    // Store user-friendly error message to display on the form
    $_SESSION['csrfError'] = "Your session has expired or the request is invalid. Please try again.";

    // Redirect back to the form
    if ($returnUrl) {
        header("Location: $returnUrl");
    } else {
        header("Location: " . $_SESSION['PHP_SELF']);
    }

    // Stop script execution after redirect
    exit;
}
