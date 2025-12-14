<?php
require_once './includes/functions/config.php';
require_once './includes/functions/auth.php';
require_once './includes/functions/function.php';
require_once './includes/functions/csrf.php';
require_once './includes/functions/session.php';

if (isset($_SESSION["userId"]) and isset($_SESSION["role"]) and  $_SESSION["role"] === "Admin" and isset($_SESSION["isVerified"]) and  $_SESSION["isVerified"]) {
    header("Location: admin/adminDashboard.php");
    exit;
} else if (isset($_SESSION["userId"]) and isset($_SESSION["role"]) and $_SESSION["role"] === "User" and isset($_SESSION["isVerified"]) and  $_SESSION["isVerified"]) {
    header("Location: user/user.php");
    exit;
}

// --- INITIALIZATION ---
$errors = [];
$showVerification = false;
$lastSentTime = null;
$registrationEmail = '';
$showArchivedRecovery = false;
$archivedEmail = '';

// --- CSRF & STATE HANDLING ---
// Check for CSRF errors from previous requests.
if (isset($_SESSION['csrfError'])) {
    $errors['csrf'] = $_SESSION['csrfError'];
    unset($_SESSION['csrfError']);
}

// If the user is in the middle of verifying their email, show the verification form.
if (isset($_SESSION['awaitingVerification']) && ($_SESSION['awaitingVerification'])) {
    $showVerification = true;
    $registrationEmail = $_SESSION['verificationEmail'] ?? '';
    $lastSentTime = getCodeCreationTime($registrationEmail, 'registration');
}

// --- FORM PROCESSING ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Validate the CSRF token to prevent cross-site request forgery.
    if (!validateCSRFToken($_POST['csrfToken'] ?? '')) {
        handleCSRFFailure('register.php');
    }

    // --- REGISTRATION FORM SUBMISSION ---
    if (isset($_POST['formType']) && $_POST['formType'] === 'registration') {

        // Sanitize and retrieve user input.
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirmPassword'];
        // Check if the email is already registered in the database.
        if (isEmailExists($email)) {
            $status = getAccountStatus($email);
            if ($status && strtolower($status) === 'archived') {
                $showArchivedRecovery = true;
                $archivedEmail = $email;
                $errors['email'] = 'This email belongs to an archived account.';
            } else {
                $errors['email'] = 'An account with this email already exists.';
            }
        } else {
            // If there are no validation errors, proceed with registration.
            if (empty($errors)) {

                // Register the user and send a verification code.
                $result = registerUser($email, $password);

                if ($result['success']) {
                    $_SESSION['registration_success'] = true;
                    $_SESSION['awaitingVerification'] = true;
                    $_SESSION['verificationEmail'] = $email;
                    $_SESSION['verificationUserId'] = $result['userId'];

                    header("Location: register.php");
                    exit;
                } else {
                    $errors['registration'] = $result['error'];
                }
            }
        }
    } 
    // --- VERIFICATION CODE FORM SUBMISSION ---
    else if (isset($_POST['formType']) && $_POST['formType'] === 'verification') {

        // Concatenate the 6-digit code from individual input fields.
        $code = trim($_POST['code1'] . $_POST['code2'] . $_POST['code3'] . $_POST['code4'] . $_POST['code5'] . $_POST['code6']);

        // Validate the verification code format.
        if (!$code) {
            $errors['verification'] = 'Verification code is required';
        } else if (!preg_match('/^[0-9]{6}$/', $code)) {
            $errors['verification'] = "Verification code must be exactly 6 digits";
        }

        // If the code format is valid, proceed with verification.
        if (empty($errors)) {
            $email = $_SESSION['verificationEmail'];

            // Check if the session for the verification email has expired.
            if (empty($email)) {
                $errors['verification'] = "Your session has expired. Please start the registration process again.";
            } else {
                $result = verifyEmail($email, $code, "registration");
                // --- VERIFICATION SUCCESS ---
                if ($result['success']) {
                    // Clean up verification-related session variables.
                    unset($_SESSION['awaitingVerification']);
                    unset($_SESSION['verificationEmail']);
                    unset($_SESSION['verificationUserId']);

                    // Set the full user session.
                    setSession($result['userId']);

                    $_SESSION['verification_success'] = true;
                } else {
                    $errors['verification'] = $result['error'];
                    $showVerification = true;
                    $registrationEmail = $email; // Keep email for display
                    // The addFailedAttempt is now called inside verifyEmail()
                }
            }
            // If code format is invalid, redisplay the verification form.
        } else {
            $showVerification = true;
            $registrationEmail = $_SESSION['verificationEmail'] ?? '';
        }
    }
}

// --- RESEND CODE LOGIC (via GET request) ---
if (isset($_GET['resend']) and $_GET['resend'] === 'true') {
    $email = $_SESSION['verificationEmail'] ?? '';

    $resend = resendCode($email);

    if ($resend) {
        $_SESSION['resend_success'] = true;
    } else {
        $_SESSION['resend_fail'] = true;
        $errors['resend'] = "Failed to resend the code. Please try again in a few moments.";
    }
}

// --- CANCEL REGISTRATION LOGIC (via GET request) ---
if (isset($_GET['cancel']) and $_GET['cancel'] === 'true') {
    unset($_SESSION['awaitingVerification']);
    unset($_SESSION['verificationEmail']);
    unset($_SESSION['verificationUserId']);

    header("Location: register.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../bootstrap-5.3.8-dist/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="../bootstrap-5.3.8-dist/sweetalert2.min.css">
    <title>Sign up - Aperture</title>
</head>

<body class="">
    <section class="w-100 min-vh-100   p-0 p-sm-2  d-flex flex-column justify-content-center align-items-center position-relative" id="reg">

        <a href="index.php"><img src="./assets/logo-for-light.png" alt="" id="logo"></a>

        <!-- Main container for the form -->
        <div class="container justify-content-center px-4 p-md-3">
            <div class="row justify-content-center align-items-center bg-white shadow p-0 p-md-3 rounded-5 position-relative ">

                <?php if (!$showVerification): ?>
                    <div class="col ">

                        <form action="" method="POST" class="p-2 ">
                            <?php csrfField() ?>
                            <input type="hidden" name="formType" value="registration">

                            <div class="text-center mb-3">
                                <h1 class=" display-1 m-0 serif">Sign up</h1>
                                <small>Join Aperture today and enjoy seamless booking, transparent pricing, and trusted pros at your fingertips.</small>
                            </div>

                            <!-- Email  -->
                            <div class="mb-2">
                                <label class="form-label" for="email">Email<span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" value="<?= htmlspecialchars($email ?? '') ?>" class="form-control <?= (!isset($errors['email']) ? '' : 'is-invalid')  ?> "  required>
                                <p class="text-danger"><?= (isset($errors['email'])) ? htmlspecialchars($errors['email']) : '' ?></p>
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label class="form-label" for="password">Password<span class="text-danger">*</span></label>
                                <input type="password" name="password" id="password" class="form-control <?= (isset($errors['password'])  ? 'is-invalid' : '')   ?> " required>
                                <small style="font-size: 12px;">Password must be at least 8 characters long, include at least one uppercase letter, one lowercase letter, one number, and one special character.</small>
                                <p class="text-danger"><?= (isset($errors['password'])) ? htmlspecialchars($errors['password']) : '' ?></p>
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <label class="form-label" for="confirmPassword">Confirm Password<span class="text-danger">*</span></label>
                                <input type="password" name="confirmPassword" id="confirmPassword" class="form-control <?= htmlspecialchars(!isset($errors['ConfirmPassword']) ? '' : 'is-invalid')  ?> "  required>
                                <p class="text-danger"><?= (isset($errors['ConfirmPassword'])) ? htmlspecialchars($errors['ConfirmPassword']) : '' ?></p>
                            </div>

                            <!-- Check Terms and Condition -->

                            <div class="form-check mb-2 d-flex gap-2 justify-content-center align-items-start">
                                <input type="checkbox" name="termsCheck" id="termsCheck" class="form-check-input" required>
                                <label for="termsCheck" id="termsLabel" class="form-check-label">By creating an account, you confirm that you have read, understood, and agreed to the <a href="#" type="button" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and Conditions and Privacy Notice.</a></label>
                            </div>

                            <?php include "./includes/modals/terms.php" ?>

                            <!-- Submit Button -->
                            <div class="mt-3">
                                <input type="submit" class="btn w-100 bg-dark text-light mb-1" value="Sign up">
                                <p>Already have an account? <a href="logIn.php">Log in</a></p>
                            </div>


                        </form>


                    </div>

                    <!-- VERIFICATION FORM: Display if user needs to enter a code -->
                <?php else: ?>


                    <div class="col ">


                        <form class=" px-1 py-3 justify-content-center" method="POST">
                            <?php csrfField() ?>
                            <input type="hidden" name="formType" value="verification">


                            <div class="text-center mb-3">
                                <h1 class=" display-3 m-0 serif">Check your email</h1>
                                <p>Enter the verification code we just sent to your email</p>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex gap-3 justify-content-center align-items-center mb-4" id="verificationCode">
                                    <input type="text" inputmode="numeric" pattern="[0-9]*" name="code1" id="" class="codessssss form-control fs-3 text-center" maxlength="1" required>
                                    <input type="text" inputmode="numeric" pattern="[0-9]*" name="code2" id="" class="codessssss form-control fs-3 text-center" maxlength="1" required>
                                    <input type="text" inputmode="numeric" pattern="[0-9]*" name="code3" id="" class="codessssss form-control fs-3 text-center" maxlength="1" required>
                                    <input type="text" inputmode="numeric" pattern="[0-9]*" name="code4" id="" class="codessssss form-control fs-3 text-center" maxlength="1" required>
                                    <input type="text" inputmode="numeric" pattern="[0-9]*" name="code5" id="" class="codessssss form-control fs-3 text-center" maxlength="1" required>
                                    <input type="text" inputmode="numeric" pattern="[0-9]*" name="code6" id="" class="codessssss form-control fs-3 text-center" maxlength="1" required>
                                </div>
                            </div>



                            <div class="mb-1">
                                <input type="submit" class="btn bg-dark text-light w-100 mb-2" value="Verify Code">
                                <a href="register.php?cancel=true" class="btn bg-light text-dark border w-100 mb-2">Back to Registration</a>
                                <p>Didn't receive an email? <a href="register.php?resend=true" id="resendLink">Resend</a><span id="resendTimer" class="ms-2"></span></p>
                            </div>

                        </form>
                    </div>

                <?php endif; ?>

                <div class="col d-none d-lg-inline p-4 rounded-4 bg-secondary overflow-hidden">
                    <img src="./assets/undraw_fingerprint-login_19qv.svg" class="img-fluid object-fit-cover" alt="">
                </div>
            </div>
        </div>

    </section>


    <script src="../bootstrap-5.3.8-dist/sweetalert2.min.js"></script>
    <script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <!-- Password strength removed for minimalist design -->
    <script src="script.js"></script>
    
    <script>
        // Initialize password strength meter
        document.addEventListener('DOMContentLoaded', function() {
            // Password strength meter removed for minimalist design
        });
    </script>

    <?php if ($showVerification && $lastSentTime): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const resendLink = document.getElementById('resendLink');
                const resendTimer = document.getElementById('resendTimer');
                const lastSent = new Date('<?= $lastSentTime ?> UTC').getTime();
                const waitTime = 60; // 60 seconds

                function updateTimer() {
                    const now = new Date().getTime();
                    const timeDiff = (now - lastSent) / 1000;
                    const remaining = Math.ceil(waitTime - timeDiff);

                    if (remaining > 0) {
                        resendLink.style.pointerEvents = 'none';
                        resendLink.style.color = '#6c757d'; // Muted color
                        resendTimer.textContent = `(wait ${remaining}s)`;
                    } else {
                        resendLink.style.pointerEvents = 'auto';
                        resendLink.style.color = ''; // Reset color
                        resendTimer.textContent = '';
                        clearInterval(timerInterval);
                    }
                }

                // Initial check
                const initialDiff = (new Date().getTime() - lastSent) / 1000;
                if (initialDiff < waitTime) {
                    const timerInterval = setInterval(updateTimer, 1000);
                    updateTimer(); // Run once immediately
                }
            });
        </script>
    <?php endif; ?>

    <?php if (isset($_SESSION['registration_success']) && $_SESSION['registration_success']): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Registration Successful',
                text: 'A verification code has been sent to your email. Please check your inbox to activate your account.',
                confirmButtonText: 'Continue',
                confirmButtonColor: '#212529',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'register.php';
                }
            });
        </script>
    <?php
        unset($_SESSION['registration_success']);
    endif; ?>


    <!-- SweetAlert for successful code resend -->
    <?php if (isset($_SESSION['resend_success']) && $_SESSION['resend_success']): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Code Resent Successfully',
                text: 'A new verification code has been sent to your email address. Please check your inbox.',
                confirmButtonText: 'Continue',
                confirmButtonColor: '#212529',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'register.php';
                }
            });
        </script>
    <?php
        unset($_SESSION['resend_success']);
    endif; ?>

    <!-- SweetAlert for failed code resend -->
    <?php if (isset($_SESSION['resend_fail']) && $_SESSION['resend_fail']): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Resend Failed',
                text: 'We couldn\'t resend the code. Please wait a minute before trying again.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#212529',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'register.php';
                }
            });
        </script>
    <?php unset($_SESSION['resend_fail']);
    endif; ?>


    <!-- SweetAlert for successful account verification -->
    <?php if (isset($_SESSION['verification_success']) && $_SESSION['verification_success']): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Account Verified',
                text: 'Your account has been successfully verified. Please complete your profile to continue.',
                confirmButtonText: 'Continue',
                confirmButtonColor: '#212529',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "completeProfile.php";
                }
            });
        </script>
    <?php
        unset($_SESSION['verification_success']);
    endif; ?>

    <!-- SweetAlert for registration failure -->
    <?php if (isset($errors['registration'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Registration Failed',
                    text: '<?= addslashes(htmlspecialchars($errors['registration'])) ?>',
                    confirmButtonColor: '#212529'
                });
            });
        </script>
    <?php endif; ?>

    <!-- SweetAlert for verification failure -->
    <?php if (isset($errors['verification'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let errorText = '<?= addslashes(htmlspecialchars($errors['verification'])) ?>';
                let secondsMatch = errorText.match(/(\d+)\s*seconds/);

                if (secondsMatch) {
                    let duration = parseInt(secondsMatch[1], 10);
                    let timerInterval;
                    Swal.fire({
                        icon: 'error',
                        title: 'Account Locked',
                        html: `Too many failed attempts. Please try again in <b></b> seconds.`,
                        timer: duration * 1000,
                        timerProgressBar: true,
                        allowOutsideClick: false,
                        didOpen: () => {
                            const b = Swal.getHtmlContainer().querySelector('b');
                            timerInterval = setInterval(() => {
                                b.textContent = Math.ceil(Swal.getTimerLeft() / 1000);
                            }, 100);
                        },
                        willClose: () => {
                            clearInterval(timerInterval);
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Verification Failed',
                        text: errorText,
                        confirmButtonColor: '#212529'
                    });
                }
            });
        </script>
    <?php endif; ?>

    <!-- SweetAlert for Archived Account Recovery Redirect -->
    <?php if ($showArchivedRecovery): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Account Archived',
                    text: "This email belongs to an archived account. Do you want to log in and recover it?",
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#212529',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, go to Login',
                    cancelButtonText: 'No, stay here'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'logIn.php';
                    }
                });
            });
        </script>
    <?php endif; ?>
</body>

</html>