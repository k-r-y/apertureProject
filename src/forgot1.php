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

// --- INITIALIZATION & STATE MANAGEMENT ---
$errors  = [];
$showEmailForm = true;
$showCodeForm = false;
$showNewPassword = false;
$lastSentTime = null;
$forgotEmail = '';

// Check for CSRF errors from previous requests.
if (isset($_SESSION['csrfError'])) {
    $errors['csrf'] = $_SESSION['csrfError'];
    unset($_SESSION['csrfError']);
}

// Determine which part of the form to show based on the session state.
// This creates a multi-step form experience.
if (isset($_SESSION['awaitingVerification']) and $_SESSION['awaitingVerification']) {
    $showEmailForm = false;
    $showCodeForm = true;
    $forgotEmail = $_SESSION['forgotEmail'] ?? '';
    $lastSentTime = getCodeCreationTime($forgotEmail, 'forgot_password');
} else if (isset($_SESSION['awaitingNewPassword']) and $_SESSION['awaitingNewPassword']) {
    $showNewPassword = true;
    $showEmailForm = false;
    $showCodeForm = false;
    $forgotEmail = $_SESSION['forgotEmail'] ?? '';
}

// --- FORM PROCESSING ---
if ($_SERVER["REQUEST_METHOD"] === 'POST') {

    // Validate the CSRF token to prevent cross-site request forgery.
    if (!validateCSRFToken($_POST['csrfToken'] ?? '')) {
        handleCSRFFailure('forgot1.php');
    }

    $forgotEmail = $_SESSION['forgotEmail'] ?? '';

    // --- STEP 1: EMAIL SUBMISSION ---
    if ($_POST['formType'] === 'enterEmail') {
        $email = trim($_POST['email']);

        // Validate the email format.
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Use a valid email";
        }

        // If email is valid, proceed.
        if (empty($errors)) {
            $isEmailExist = isEmailExists($email);

            // If the email exists in the database, create and send a password reset code.
            if ($isEmailExist) {
                $code = createForgotCode($email);
                $sendEmailForgot = sendForgotPasswordWithCode($email, $code);

                if ($sendEmailForgot) {
                    $_SESSION['sendEmailSuccess'] = true;
                    $_SESSION['awaitingVerification'] = true;
                    $_SESSION['forgotEmail'] = $email;
                } else {
                    $errors['email'] = "We couldn't send a verification code at this time. Please try again later.";
                }
                // SECURITY: If the email does NOT exist, still pretend to send an email.
                // This prevents attackers from using this form to discover which emails are registered (timing attack mitigation).
            } else {
                // We still simulate success to prevent email enumeration.
                sleep(rand(1, 2)); // Simulate work to mitigate timing attacks
                $_SESSION['sendEmailSuccess'] = true;
                $_SESSION['awaitingVerification'] = true;
                $_SESSION['forgotEmail'] = $email;
            }
            // Redirect to the same page to show the next step (or the same step with an error).
            header("Location: forgot1.php");
            exit;
        }
        // --- STEP 2: VERIFICATION CODE SUBMISSION ---
    } else if ($_POST['formType'] === 'enterCode') {
        // Concatenate the 6-digit code from individual input fields.
        $code = trim($_POST['code1'] . $_POST['code2'] . $_POST['code3'] . $_POST['code4'] . $_POST['code5'] . $_POST['code6']);

        // Attempt to verify the code against the email stored in the session.
        $verifyCode = verifyCode($forgotEmail, $code, "forgot_password");

        if ($verifyCode['success']) {
            $_SESSION['awaitingVerification'] = false;
            $_SESSION['awaitingNewPassword'] = true;
            $_SESSION['forgotPasswordUserID'] = $verifyCode['userId'];
            $_SESSION['verificationSuccess'] = true;

            header("Location: forgot1.php");
            exit;
        } else {
            $errors['verificationCode'] = $verifyCode['error'];
            // addFailedAttempt is now called inside verifyCode()
        }
        // --- STEP 3: NEW PASSWORD SUBMISSION ---
    } else if ($_POST['formType'] === 'enterPassword') {

        // Sanitize and retrieve new password inputs.
        $password =  trim($_POST['password']);
        $confirmPassword =  trim($_POST['confirmPassword']);
        $forgotEmail = $_SESSION['forgotEmail'];
        $forgotUserId = $_SESSION['forgotPasswordUserID'];

        if (empty($password)) {
            $errors['password'] = "Password is required";
        } else if (strlen($password) < 8) {
            $errors['password'] = "The password must be at least 8 characters";
        }

        if ($password !== $confirmPassword) {
            $errors['ConfirmPassword'] = "Password Mismatched";
        }

        // If validation passes, update the password in the database.
        if (empty($errors)) {
            $updatePass = updatePassword($password, $forgotEmail);

            if ($updatePass) {
                $_SESSION['newPassCreated'] = true;
                header("Location: forgot1.php"); // Redirect to show success message
                exit;
            }
        }
    }
}

// --- CANCEL PASSWORD RESET LOGIC (via GET request) ---
if (isset($_GET['cancel']) and $_GET['cancel'] === 'true') {
    unset($_SESSION['awaitingVerification']);
    unset($_SESSION['awaitingNewPassword']);
    unset($_SESSION['forgotPasswordUserID']);

    header("Location: logIn.php");
    exit;
}

// --- RESEND CODE LOGIC (via GET request) ---
if (isset($_GET['resend']) and $_GET['resend'] === 'true') {
    $email = $_SESSION['forgotEmail'] ?? '';

    $isEmailExist = isEmailExists($email);

    if ($isEmailExist) {
        $resend = resendForgotCode($email);

        if ($resend) {
            $_SESSION['resend_success'] = true;
        } else {
            $_SESSION['resend_fail'] = true;
            $errors['resend'] = "Failed to resend the code. Please try again in a few moments.";
        }
    } else {
        sleep(rand(1, 2));
        $_SESSION['resend_success'] = true;
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

    <section class="w-100 min-vh-100  p-0 p-sm-2  d-flex justify-content-center align-items-center position-relative" id="forgot1">

        <a href="index.php"><img src="./assets/logo.png" alt="" id="logo"></a>


        <div class="container justify-content-center px-2 p-md-3">
            <div class="row justify-content-center align-items-center bg-white shadow p-3 rounded-5">
                <div class="col d-none d-md-inline p-4 rounded-4 overflow-hidden bg-secondary">
                    <img src="./assets/undraw_forgot-password_nttj.svg" class="img-fluid" alt="">
                </div>

                <!-- STEP 1 FORM: Enter Email -->
                <?php if ($showEmailForm): ?>
                    <div class="col">
                        <form action="" method="POST" class=" px-1 py-3 justify-content-center">
                            <?php csrfField() ?>
                            <input type="hidden" name="formType" value="enterEmail">


                            <div class="text-center mb-3">
                                <h1 class=" display-3 m-0 serif">Forgot Your Password?</h1>
                                <p>Enter your email address below and we'll send you a verification code to reset your password.</p>
                            </div>


                            <div class="mb-3">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="e.g., princesuperpogi@email.com" required>
                            </div>


                            <div class="mb-1">
                                <input type="submit" value="Send Verification Code" class="btn bg-dark text-light w-100 my-2 py-2">
                                <a href="login.php" class="btn bg-light text-dark border w-100 mb-2">Back to Login</a>
                            </div>

                        </form>
                    </div>
                    <!-- STEP 2 FORM: Enter Verification Code -->
                <?php elseif ($showCodeForm): ?>
                    <div class="col">


                        <form class=" px-0 py-3 justify-content-center" method="POST" action="">
                            <?php csrfField() ?>
                            <input type="hidden" name="formType" value="enterCode">

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
                                <a href="forgot1.php?cancel=true" class="btn bg-light text-dark border w-100 mb-2">Back to Login</a>
                                <p>Didn't receive an email? <a href="forgot1.php?resend=true" id="resendLink">Resend</a><span id="resendTimer" class="ms-2"></span></p>
                            </div>

                        </form>
                    </div>
                    <!-- STEP 3 FORM: Enter New Password -->
                <?php elseif ($showNewPassword): ?>
                    <div class="col">
                        <form action="" method="POST" class=" px-1 py-3 justify-content-center">
                            <?php csrfField() ?>
                            <input type="hidden" name="formType" value="enterPassword">

                            <div class="text-center mb-3">
                                <h1 class=" display-3 m-0 serif">Create new Password</h1>
                                <p>Your new password should be at least 8 characters long.</p>
                            </div>


                            <!-- Password -->

                            <div class="mb-2">
                                <label class="form-label" for="password">Password<span class="text-danger">*</span></label>
                                <input type="password" name="password" id="password" class="form-control <?= (isset($errors['password'])  ? 'is-invalid' : '')   ?> " required>
                                <p class="text-danger"><?= (isset($errors['password'])) ? htmlspecialchars($errors['password']) : '' ?></p>
                            </div>

                            <!-- Confirm Password -->

                            <div class="mb-3">
                                <label class="form-label" for="confirmPassword">Confirm Password<span class="text-danger">*</span></label>
                                <input type="password" name="confirmPassword" id="confirmPassword" class="form-control <?= htmlspecialchars(!isset($errors['ConfirmPassword']) ? '' : 'is-invalid')  ?> " required>
                                <p class="text-danger"><?= (isset($errors['ConfirmPassword'])) ? htmlspecialchars($errors['ConfirmPassword']) : '' ?></p>
                            </div>


                            <div class="my-3">
                                <input type="submit" value="Update Password" class="btn bg-dark text-light w-100 mb-2">
                                <a href="forgot1.php?cancel=true" class="btn bg-light text-dark border w-100 mb-2">Back to Login</a>
                            </div>


                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </section>

    <script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../bootstrap-5.3.8-dist/sweetalert2.min.js"></script>
    <script src="script.js"></script>

    <?php if ($showCodeForm && $lastSentTime): ?>
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

    <!-- SweetAlert for successful email sending (or simulated sending) -->
    <?php if (isset($_SESSION['sendEmailSuccess']) && $_SESSION['sendEmailSuccess']): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Verification Code Sent',
                html: 'A verification code has been sent to your email address. Please check your inbox to proceed.',
                confirmButtonText: 'Continue',
                confirmButtonColor: '#212529',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "forgot1.php";
                }
            });
        </script>
    <?php
        unset($_SESSION['sendEmailSuccess']);
    endif; ?>

    <!-- SweetAlert for email sending failure -->
    <?php if (isset($errors['email']) && !isset($_POST['formType'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Request Failed',
                    text: '<?= addslashes(htmlspecialchars($errors['email'])) ?>',
                    confirmButtonColor: '#212529'
                });
            });
        </script>
    <?php endif; ?>

    <!-- SweetAlert for verification code failure -->
    <?php if (isset($errors['verificationCode'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let errorText = '<?= addslashes(htmlspecialchars($errors['verificationCode'])) ?>';
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

    <!-- SweetAlert for successful code verification -->
    <?php if (isset($_SESSION['verificationSuccess']) and $_SESSION['verificationSuccess']): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Verification Successful',
                text: 'You may now create a new password for your account.',
                confirmButtonText: 'Continue',
                confirmButtonColor: '#212529',
                allowOutsideClick: false
            });
        </script>
    <?php unset($_SESSION['verificationSuccess']);
    endif; ?>

    <!-- SweetAlert for successful password update -->
    <?php if (isset($_SESSION['newPassCreated']) and $_SESSION['newPassCreated']): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Password Updated Successfully',
                text: 'Your password has been reset. You may now log in with your new credentials.',
                confirmButtonText: 'Go to login',
                confirmButtonColor: '#212529',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "forgot1.php?cancel=true";
                }
            })
        </script>
    <?php unset($_SESSION['newPassCreated']);
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
                    window.location.href = 'forgot1.php';
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
                    window.location.href = 'forgot1.php';
                }
            });
        </script>
    <?php unset($_SESSION['resend_fail']);
    endif; ?>

</body>

</html>