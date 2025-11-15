<?php
require_once './includes/functions/config.php';
require_once './includes/functions/auth.php';
require_once './includes/functions/function.php';
require_once './includes/functions/csrf.php';
require_once './includes/functions/session.php';

// Redirect if already logged in
if(isset($_SESSION["userId"]) and isset($_SESSION["role"]) and  $_SESSION["role"] === "Admin" and isset($_SESSION["isVerified"]) and  $_SESSION["isVerified"]){
    header("Location: admin.php");
    exit;
}else if (isset($_SESSION["userId"]) and isset($_SESSION["role"]) and $_SESSION["role"] === "User" and isset($_SESSION["isVerified"]) and  $_SESSION["isVerified"]){
    header("Location: booking.php");
    exit;
}

$errors = [];
$showVerificationForm = false;
$registrationEmail = '';

// Handle CSRF errors from session
if(isset($_SESSION['csrfError'])){
    $errors['csrf'] = $_SESSION['csrfError'];
    unset($_SESSION['csrfError']);
}

// Check if we should show verification form (from session)
if(isset($_SESSION['awaiting_verification']) && $_SESSION['awaiting_verification']){
    $showVerificationForm = true;
    $registrationEmail = $_SESSION['verification_email'] ?? '';
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Validate CSRF token
    if(!validateCSRFToken($_POST['csrfToken'] ?? '')){
        handleCSRFFailure('testing.php');
    }

    // Check which form was submitted
    if(isset($_POST['form_type']) && $_POST['form_type'] === 'registration'){
        // ============ REGISTRATION FORM HANDLING ============

        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirmPassword'];

        // Validation
        if (empty($email)) {
            $errors['email'] = "Email is required";
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Please use a valid email";
        }

        if (empty($password)) {
            $errors['password'] = "Password is required";
        } else if (strlen($password) < 8) {
            $errors['password'] = "The password must be at least 8 characters";
        }

        if ($password !== $confirmPassword) {
            $errors['ConfirmPassword'] = "Password Mismatched";
        }

        // If no errors, proceed with registration
        if(empty($errors)){
            $result = registerUserWithCode($email, $password);

            if ($result['success']) {
                // Set session variables to show verification form
                $_SESSION['awaiting_verification'] = true;
                $_SESSION['verification_email'] = $email;
                $_SESSION['verification_userId'] = $result['userId'];

                // Redirect to show verification form
                header("Location: testing.php");
                exit;
            } else {
                $errors['registration'] = $result['message'];
            }
        }

    } else if(isset($_POST['form_type']) && $_POST['form_type'] === 'verification'){
        // ============ VERIFICATION CODE FORM HANDLING ============

        $verificationCode = trim($_POST['verification_code']);

        // Validation
        if (empty($verificationCode)) {
            $errors['verification_code'] = "Verification code is required";
        } else if (!preg_match('/^[0-9]{6}$/', $verificationCode)) {
            $errors['verification_code'] = "Verification code must be exactly 6 digits";
        }

        // If no errors, verify the code
        if(empty($errors)){
            $email = $_SESSION['verification_email'] ?? '';

            if(empty($email)){
                $errors['verification_code'] = "Session expired. Please register again.";
            } else {
                $result = verifyEmailWithCode($email, $verificationCode);

                if ($result['success']) {
                    // Clear verification session variables
                    unset($_SESSION['awaiting_verification']);
                    unset($_SESSION['verification_email']);
                    unset($_SESSION['verification_userId']);

                    // Set user session
                    setSession($result['userId']);

                    // Set success flag for SweetAlert
                    $_SESSION['verification_success'] = true;

                    // Redirect to profile completion
                    header("Location: completeProfile.php");
                    exit;
                } else {
                    $errors['verification_code'] = $result['message'];
                    $showVerificationForm = true;
                    $registrationEmail = $email;
                }
            }
        } else {
            $showVerificationForm = true;
            $registrationEmail = $_SESSION['verification_email'] ?? '';
        }
    }
}

// Handle resend verification code
if(isset($_GET['resend']) && $_GET['resend'] === 'true'){
    $email = $_SESSION['verification_email'] ?? '';

    if(!empty($email)){
        $result = resendVerificationCode($email);

        if($result['success']){
            $_SESSION['resend_success'] = true;
        } else {
            $_SESSION['resend_error'] = $result['message'];
        }

        header("Location: testing.php");
        exit;
    }
}

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $showVerificationForm ? 'Email Verification' : 'Create Account'; ?> - Aperture</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        :root {
            --gold: #d4af37;
            --dark: #212529;
        }

        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .auth-container {
            max-width: 480px;
            width: 100%;
            padding: 2rem;
        }

        .auth-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 3rem 2.5rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .brand-logo {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 300;
            letter-spacing: 2px;
            color: var(--gold);
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .auth-subtitle {
            text-align: center;
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 2rem;
            font-weight: 300;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 500;
            color: #333;
            margin-bottom: 0.5rem;
            letter-spacing: 0.3px;
        }

        .form-control {
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 6px;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }

        .btn-primary {
            background: var(--gold);
            border: none;
            border-radius: 6px;
            padding: 0.85rem;
            font-size: 0.85rem;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #000;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-primary:hover {
            background: #c9a232;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.3);
        }

        .btn-outline-secondary {
            border: 1px solid rgba(0, 0, 0, 0.2);
            border-radius: 6px;
            padding: 0.85rem;
            font-size: 0.85rem;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #333;
            background: transparent;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-outline-secondary:hover {
            background: #f8f9fa;
            border-color: rgba(0, 0, 0, 0.3);
            color: #000;
        }

        .alert {
            border-radius: 6px;
            font-size: 0.85rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1.5rem;
        }

        .verification-info {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .verification-info i {
            font-size: 3rem;
            color: var(--gold);
            margin-bottom: 1rem;
        }

        .verification-info h5 {
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .verification-info p {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 0;
            line-height: 1.6;
        }

        .verification-email {
            color: var(--gold);
            font-weight: 500;
        }

        .code-input {
            text-align: center;
            font-size: 1.5rem;
            letter-spacing: 0.5rem;
            font-weight: 500;
            font-family: 'Courier New', monospace;
        }

        .resend-link {
            text-align: center;
            margin-top: 1rem;
        }

        .resend-link a {
            color: var(--gold);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .resend-link a:hover {
            text-decoration: underline;
        }

        .back-link {
            text-align: center;
            margin-top: 1.5rem;
        }

        .back-link a {
            color: #666;
            text-decoration: none;
            font-size: 0.85rem;
        }

        .back-link a:hover {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <?php if (!$showVerificationForm): ?>
                <!-- REGISTRATION FORM -->
                <h1 class="brand-logo">APERTURE</h1>
                <p class="auth-subtitle">Create your account to get started</p>

                <?php if (isset($errors['registration'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?php echo htmlspecialchars($errors['registration']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($errors['csrf'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-shield-exclamation me-2"></i>
                        <?php echo htmlspecialchars($errors['csrf']); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="testing.php" id="registrationForm">
                    <input type="hidden" name="csrfToken" value="<?php echo $csrfToken; ?>">
                    <input type="hidden" name="form_type" value="registration">

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input
                            type="email"
                            class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>"
                            id="email"
                            name="email"
                            placeholder="your.email@example.com"
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                            required
                        >
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback d-block">
                                <?php echo htmlspecialchars($errors['email']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input
                            type="password"
                            class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>"
                            id="password"
                            name="password"
                            placeholder="At least 8 characters"
                            required
                        >
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback d-block">
                                <?php echo htmlspecialchars($errors['password']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <input
                            type="password"
                            class="form-control <?php echo isset($errors['ConfirmPassword']) ? 'is-invalid' : ''; ?>"
                            id="confirmPassword"
                            name="confirmPassword"
                            placeholder="Re-enter your password"
                            required
                        >
                        <?php if (isset($errors['ConfirmPassword'])): ?>
                            <div class="invalid-feedback d-block">
                                <?php echo htmlspecialchars($errors['ConfirmPassword']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary">Create Account</button>
                </form>

                <div class="back-link">
                    <a href="logIn.php">
                        <i class="bi bi-arrow-left me-1"></i>
                        Already have an account? Sign in
                    </a>
                </div>

            <?php else: ?>
                <!-- VERIFICATION CODE FORM -->
                <h1 class="brand-logo">VERIFY EMAIL</h1>
                <p class="auth-subtitle">Enter the verification code we sent to your email</p>

                <div class="verification-info">
                    <i class="bi bi-envelope-check"></i>
                    <h5>Check Your Email</h5>
                    <p>
                        We sent a 6-digit verification code to<br>
                        <span class="verification-email"><?php echo htmlspecialchars($registrationEmail); ?></span>
                    </p>
                </div>

                <?php if (isset($errors['verification_code'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?php echo htmlspecialchars($errors['verification_code']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['resend_success'])): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        Verification code resent successfully!
                    </div>
                    <?php unset($_SESSION['resend_success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['resend_error'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?php echo htmlspecialchars($_SESSION['resend_error']); ?>
                    </div>
                    <?php unset($_SESSION['resend_error']); ?>
                <?php endif; ?>

                <form method="POST" action="testing.php" id="verificationForm">
                    <input type="hidden" name="csrfToken" value="<?php echo $csrfToken; ?>">
                    <input type="hidden" name="form_type" value="verification">

                    <div class="mb-4">
                        <label for="verification_code" class="form-label">Verification Code</label>
                        <input
                            type="text"
                            class="form-control code-input <?php echo isset($errors['verification_code']) ? 'is-invalid' : ''; ?>"
                            id="verification_code"
                            name="verification_code"
                            placeholder="000000"
                            maxlength="6"
                            pattern="[0-9]{6}"
                            inputmode="numeric"
                            required
                            autocomplete="off"
                        >
                    </div>

                    <button type="submit" class="btn btn-primary mb-2">Verify Email</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="location.href='testing.php?cancel=true'">Cancel</button>
                </form>

                <div class="resend-link">
                    <p class="mb-0" style="font-size: 0.85rem; color: #666;">
                        Didn't receive the code?
                        <a href="testing.php?resend=true">Resend Code</a>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Auto-format verification code input (numbers only)
        const codeInput = document.getElementById('verification_code');
        if(codeInput) {
            codeInput.addEventListener('input', function(e) {
                // Remove non-numeric characters
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }

        // Form validation for registration
        const registrationForm = document.getElementById('registrationForm');
        if(registrationForm) {
            registrationForm.addEventListener('submit', function(e) {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirmPassword').value;

                if(password.length < 8) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Password',
                        text: 'Password must be at least 8 characters long',
                        confirmButtonColor: '#d4af37'
                    });
                    return false;
                }

                if(password !== confirmPassword) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Password Mismatch',
                        text: 'Passwords do not match',
                        confirmButtonColor: '#d4af37'
                    });
                    return false;
                }
            });
        }

        // Form validation for verification code
        const verificationForm = document.getElementById('verificationForm');
        if(verificationForm) {
            verificationForm.addEventListener('submit', function(e) {
                const code = document.getElementById('verification_code').value;

                if(!/^[0-9]{6}$/.test(code)) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Code',
                        text: 'Please enter a valid 6-digit verification code',
                        confirmButtonColor: '#d4af37'
                    });
                    return false;
                }
            });
        }

        // Handle cancel verification
        <?php if(isset($_GET['cancel']) && $_GET['cancel'] === 'true'): ?>
            <?php
                unset($_SESSION['awaiting_verification']);
                unset($_SESSION['verification_email']);
                unset($_SESSION['verification_userId']);
                header("Location: testing.php");
                exit;
            ?>
        <?php endif; ?>
    </script>
</body>
</html>
