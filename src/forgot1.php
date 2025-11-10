<?php
/**
 * Forgot Password - Step 1: Email Entry
 *
 * This page allows users to request a password reset by entering their email.
 * Upon submission:
 * 1. Validates the email exists in the database
 * 2. Generates a 6-digit verification code
 * 3. Stores the code (hashed) in the database with 15-minute expiration
 * 4. Sends the code via email
 * 5. Redirects to verification.php
 *
 * Security Features:
 * - CSRF token protection
 * - Email enumeration prevention (always show success message)
 * - Rate limiting recommended (not implemented yet)
 * - Code is hashed before storage
 *
 * @package Aperture
 * @author  Aperture Team
 */

// Include required files
require_once './includes/functions/config.php';      // Database connection
require_once './includes/functions/auth.php';        // Authentication functions
require_once './includes/functions/function.php';    // Email sending functions
require_once './includes/functions/csrf.php';        // CSRF protection
require_once './includes/functions/session.php';     // Session configuration

// Initialize variables
$errors = [];        // Array to store validation errors
$success = false;    // Flag to track successful code generation

// Handle CSRF error from session (set by handleCSRFFailure)
if (isset($_SESSION['csrfError'])) {
    $errors['csrf'] = $_SESSION['csrfError'];
    unset($_SESSION['csrfError']);  // Clean up after displaying
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // SECURITY: Validate CSRF token first
    // This prevents cross-site request forgery attacks
    if (!validateCSRFToken($_POST['csrfToken'] ?? '')) {
        handleCSRFFailure('forgot1.php');
    }

    // Get and sanitize email input
    $email = trim($_POST['email']);

    // Validate email format
    if (empty($email)) {
        $errors['email'] = "Email is required";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address";
    }

    // If validation passes, process the password reset request
    if (empty($errors)) {

        // Check if email exists in the database
        if (isEmailExists($email)) {

            // Generate a cryptographically secure 6-digit code
            // random_int is more secure than rand() or mt_rand()
            $code = (string) random_int(100000, 999999);

            // Store the code in database (hashed for security)
            // The code will expire after 5 minutes
            if (storePasswordResetCode($email, $code)) {

                // Prepare email content
                $subject = "Password Reset Code - Aperture";
                $message = "
                    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                        <h2 style='color: #212529;'>Password Reset Request</h2>
                        <p>You requested to reset your password for your Aperture account.</p>
                        <p>Use the verification code below to proceed:</p>
                        <div style='background: #f8f9fa; padding: 20px; text-align: center; margin: 20px 0;'>
                            <h1 style='font-size: 36px; letter-spacing: 8px; color: #212529; margin: 0;'>{$code}</h1>
                        </div>
                        <p><strong>This code will expire in 15 minutes.</strong></p>
                        <p style='color: #6c757d;'>If you didn't request this password reset, please ignore this email. Your account remains secure.</p>
                        <hr style='border: 0; border-top: 1px solid #dee2e6; margin: 20px 0;'>
                        <p style='color: #6c757d; font-size: 12px;'>This is an automated message from Aperture. Please do not reply to this email.</p>
                    </div>
                ";

                // Send the verification code via email
                if (sendEmail($email, $subject, $message)) {

                    // Store email in session for the next step (verification.php)
                    $_SESSION['reset_email'] = $email;

                    // Set success flag to show success message
                    $success = true;

                    // Auto-redirect to verification page after 3 seconds
                    header("refresh:3;url=verification.php");

                } else {
                    // Email sending failed
                    $errors['email'] = "Failed to send email. Please try again later.";
                    error_log("Password Reset: Failed to send email to {$email}");
                }

            } else {
                // Database error - failed to store code
                $errors['email'] = "Something went wrong. Please try again.";
                error_log("Password Reset: Failed to store code for {$email}");
            }

        } else {
            // SECURITY: Email doesn't exist, but don't tell the user
            // This prevents email enumeration attacks
            // Show success message even though no email was sent
            $_SESSION['reset_email'] = $email;  // Store for next page
            $success = true;
            header("refresh:3;url=verification.php");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../bootstrap-5.3.8-dist/sweetalert2.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="./assets/camera.png" type="image/x-icon">
    <title>Forgot Password - Aperture</title>
</head>

<body>
    <!-- Navigation header (commented out for now) -->
    <!-- <?php include './includes/header.php'; ?> -->

    <!-- Main section containing the forgot password form -->
    <section class="w-100 min-vh-100 p-0 p-sm-2 d-flex justify-content-center align-items-center position-relative" id="forgot1">

        <!-- Logo link back to home page -->
        <a href="index.php"><img src="./assets/logo.png" alt="Aperture Logo" id="logo"></a>

        <div class="container justify-content-center px-4 p-md-3">
            <div class="row justify-content-center align-items-center bg-white shadow p-3 rounded-5">

                <!-- Left column: Illustration (hidden on mobile) -->
                <div class="col d-none d-md-inline p-4 rounded-4 overflow-hidden bg-secondary">
                    <img src="./assets/undraw_forgot-password_nttj.svg" class="img-fluid" alt="Forgot Password Illustration">
                </div>

                <!-- Right column: Form -->
                <div class="col">

                    <?php if ($success): ?>
                        <!-- Success message displayed after code is sent -->
                        <div class="p-4 text-center">
                            <div class="alert alert-success" role="alert">
                                <h4 class="alert-heading">Code Sent!</h4>
                                <p>Please check your email for the 6-digit verification code.</p>
                                <hr>
                                <p class="mb-0">Redirecting to verification page in 3 seconds...</p>
                            </div>
                            <div class="spinner-border text-success mt-3" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Form to enter email address -->
                        <form method="POST" class="px-1 py-3 justify-content-center">

                            <!-- CSRF Token (hidden field for security) -->
                            <?php csrfField(); ?>

                            <!-- Page title and description -->
                            <div class="text-center mb-3">
                                <h1 class="display-3 m-0 serif">Forgot Your Password?</h1>
                                <p>Enter your email address below and we'll send you a verification code to reset your password.</p>
                            </div>

                            <!-- Email input field -->
                            <div class="mb-3">
                                <label class="form-label" for="email">Email Address</label>
                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>"
                                    placeholder="e.g., john.doe@email.com"
                                    value="<?php echo htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                    required
                                >
                                <!-- Display email validation error if any -->
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8'); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Display CSRF error if any -->
                            <?php if (isset($errors['csrf'])): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo htmlspecialchars($errors['csrf'], ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            <?php endif; ?>

                            <!-- Form buttons -->
                            <div class="mb-1">
                                <!-- Submit button to send verification code -->
                                <button type="submit" class="btn bg-dark text-light w-100 my-2 py-2">
                                    Send Verification Code
                                </button>

                                <!-- Link back to login page -->
                                <a href="logIn.php" class="btn bg-light text-dark border w-100 mb-2">
                                    Back to Login
                                </a>
                            </div>

                        </form>
                    <?php endif; ?>

                </div>
            </div>
        </div>

    </section>

    <!-- Footer (commented out for now) -->
    <!-- <?php include './includes/footer.php'; ?> -->

    <!-- JavaScript libraries -->
    <script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../bootstrap-5.3.8-dist/sweetalert2.min.js"></script>
    <script src="script.js"></script>
</body>

</html>
