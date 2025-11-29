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



$errors = [];

$showVerificationForm = false;
$lastSentTime = null;
$loginEmail = '';
$showArchivedRecovery = false;
$archivedEmail = '';
$archivedPassword = '';
$_SESSION['counter'] = 0;

if (isset($_SESSION['csrfError'])) {
    $errors['csrf'] = $_SESSION['csrfError'];
    unset($_SESSION['csrfError']);
}

if (isset($_SESSION['awaitingLoginVerification']) && ($_SESSION['awaitingLoginVerification'])) {
    $showVerificationForm = true;
    $loginEmail = $_SESSION['loginVerificationEmail'] ?? '';
    $lastSentTime = getCodeCreationTime($loginEmail, 'login_email_verification');
}

if ($_SERVER["REQUEST_METHOD"] === 'POST') {

    if (!validateCSRFToken($_POST['csrfToken'] ?? '')) {
        handleCSRFFailure('logIn.php');
    }

    if (isset($_POST['formType']) and $_POST['formType'] === 'login') {

        // Getting the user input
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        //checking if there's an error in the password and email
        if (empty($email)) {
            $errors['logIn'] = "Email is required";
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['logIn'] = "Please use a valid email";
        }

        if (empty($password)) {
            $errors['logIn'] = "Password is required";
        }

        //loggin in the user
        if (empty($errors)) {

            $result = logInUser($email, $password, 'login');

            if ($result['success']) {
                $isEmailVerified = isVerified($email);

                if ($isEmailVerified) {
                    $isProfileCompleted = isProfileCompleted($result['userId']);

                    if ($isProfileCompleted) {
                        setSession($result['userId']);
                        if ($result['role'] === 'Admin') {
                            header("Location: admin/adminDashboard.php");
                            exit;
                        } else {
                            header("Location:user/user.php");
                            exit;
                        }
                        // If profile is not complete, set a partial session and redirect to completeProfile.php.
                    } else {
                        $_SESSION['email'] = $email;
                        $_SESSION['role'] = $result['role'];
                        $_SESSION['userId'] = $result['userId'];
                        $_SESSION['isVerified'] = true;
                        header("Location:completeProfile.php");
                        exit;
                    }
                    // --- USER IS NOT VERIFIED ---
                } else {
                    $code = createCode($email); // This should probably be createToken for email verification link, but current logic uses code.
                    $emailSent = sendVerificationEmailWithCode($email, $code);

                    if ($emailSent) {
                        $_SESSION['awaitingLoginVerification'] = true;
                        $_SESSION['loginVerificationEmail'] = $email;
                        $_SESSION['loginVerificationUserId'] = getUserId($email);
                        $_SESSION['sendCodeSuccess'] = true;

                        header("Location: logIn.php");
                        exit;
                    } else {
                        $errors['logIn'] = 'Could not send verification code. Please try again.';
                    }
                }
                // --- LOGIN FAILURE ---
            } else {
                if (isset($result['archived']) && $result['archived']) {
                    $showArchivedRecovery = true;
                    $archivedEmail = $email;
                    // We need to pass the password to the recovery API. 
                    // Ideally we would use a temporary session or re-prompt, but for seamless UX we'll pass it to JS.
                    $archivedPassword = $password; 
                } else {
                    $errors['logIn'] = $result['error'];
                }
            }
        }
    } else if (isset($_POST['formType']) && $_POST['formType'] === 'verification') {
        // --- VERIFICATION CODE FORM SUBMISSION ---

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
            $email = $_SESSION['loginVerificationEmail'];

            // Check if the session for the verification email has expired.
            if (empty($email)) {
                $errors['verification'] = "Your session has expired. Please try logging in again.";
            } else {
                $result = verifyEmail($email, $code, 'login_email_verification');
                // --- VERIFICATION SUCCESS ---
                if ($result['success']) {
                    // Clean up verification-related session variables.
                    unset($_SESSION['awaitingLoginVerification']);
                    unset($_SESSION['loginVerificationEmail']);
                    unset($_SESSION['loginVerificationUserId']);

                    // Set the full user session.
                    setSession($result['userId']);

                    $_SESSION['login_success'] = true;
                } else {
                    $errors['verification'] = $result['error'];
                    $showVerificationForm = true;
                    $loginEmail = $_SESSION['loginVerificationEmail'];
                    // addFailedAttempt is now called inside verifyEmail()
                }
            }
            // If code format is invalid, redisplay the verification form.
        } else {
            $showVerificationForm = true;
            $loginEmail = $_SESSION['loginVerificationEmail'] ?? '';
        }
    }
}

// --- RESEND CODE LOGIC (via GET request) ---
if (isset($_GET['resend']) and $_GET['resend'] === 'true') {
    $loginEmail = $_SESSION['loginVerificationEmail'] ?? '';

    $resend = resendCode($loginEmail);

    if ($resend) {
        $_SESSION['resend_success'] = true;
    } else {
        $_SESSION['resend_fail'] = true;
        $errors['resend'] = "Failed to resend the code. Please try again in a few moments.";
    }
}

// --- CANCEL VERIFICATION LOGIC (via GET request) ---
if (isset($_GET['cancel']) and $_GET['cancel'] === 'true') {
    unset($_SESSION['awaitingLoginVerification']);
    unset($_SESSION['loginVerificationEmail']);
    unset($_SESSION['loginVerificationUserId']);

    header("Location: logIn.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../bootstrap-5.3.8-dist/sweetalert2.min.css">
    <link rel="icon" href="./assets/camera.png" type="image/x-icon">
    <title>Login - Aperture</title>
</head>

<body class="">


    <section class="w-100 min-vh-100  p-0 p-sm-2  d-flex flex-column justify-content-center align-items-center position-relative" id="logSection">

        <a href="index.php" id="logo"><img src="./assets/logo-for-light.png" alt=""></a>


        <div class="container justify-content-center px-2 p-md-4">
            <div class="row justify-content-center align-items-center bg-white shadow p-3 rounded-5">

                <div class="col d-none d-lg-inline p-2 rounded-4 bg-secondary overflow-hidden">
                    <img src="./assets/undraw_secure-login_m11a (1).svg" class="img-fluid object-fit-cover" alt="">
                </div>

                <!-- LOGIN FORM: Display if not in verification mode -->
                <?php if (!$showVerificationForm) : ?>

                    <div class="col p-0">
                        <form action="logIn.php" method="POST" class="p-2 p-md-4">
                            <?php csrfField() ?>
                            <input type="hidden" name="formType" value="login">

                            <div class="text-center mb-3">
                                <h1 class=" display-3 m-0 serif">Log in</h1>
                                <p>Please enter your registered email address and password to securely access your Aperture account.</p>
                            </div>


                            <!-- Email  -->

                            <div class="mb-2">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control" value="<?php echo (htmlspecialchars($email ?? '')) ?>" required>
                            </div>

                            <!-- Password -->

                            <div class="mb-1">
                                <label class="form-label" for="password">Password</label>
                                <input type="password" name="password" id="password" class="form-control 
                            
                            <?php echo (!isset($errors['logIn']) ? '' : 'is-invalid')  ?>

                            " value="" required>
                            </div>

                            <!-- Remember me and Forgot Password -->

                            <div class="mb-2 d-flex justify-content-end align-items-center">
                                <!-- <div class="form-check">
                                    <input type="checkbox" name="remember" class="form-check-input" id="remember">
                                    <label for="remember" class="form-check-label rememberLabel" id="rememberLabel">Remember me</label>
                                </div> -->

                                <a href="forgot1.php">Forgot Password?</a>
                            </div>


                            <!-- Submit Button -->
                            <div class="mt-3">
                                <input type="submit" class="btn w-100 bg-dark text-light mb-1" value="Log in">
                                <p>Don't have an account? <a href="register.php">Sign up</a></p>
                            </div>




                        </form>
                    </div>

                    <!-- VERIFICATION FORM: Display if user needs to enter a code -->
                <?php else: ?>
                    <div class="col p-0">


                        <form class=" p-2 p-md-4 justify-content-center" method="POST">
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
                                <a href="login.php?cancel=true" class="btn btn-light border w-100">Back to Login</a>
                                <p>Didn't receive an email? <a href="logIn.php?resend=true" id="resendLink">Resend</a><span id="resendTimer" class="ms-2"></span></p>
                            </div>

                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </section>


    <script src="../bootstrap-5.3.8-dist/sweetalert2.min.js"></script>
    <script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>

    <?php if ($showVerificationForm && $lastSentTime): ?>
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
                    window.location.href = 'logIn.php';
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
                    window.location.href = 'logIn.php';
                }
            });
        </script>
    <?php unset($_SESSION['resend_fail']);
    endif; ?>

    <!-- SweetAlert for successful code sending on first attempt -->
    <?php if (isset($_SESSION['sendCodeSuccess']) && $_SESSION['sendCodeSuccess']): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Verification Code Sent',
                text: 'A verification code has been sent to your email to complete your login. Please check your inbox.',
                confirmButtonText: 'Continue',
                confirmButtonColor: '#212529',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'logIn.php';
                }
            });
        </script>
    <?php
        unset($_SESSION['sendCodeSuccess']);
    endif; ?>


    <!-- SweetAlert for successful login verification -->
    <?php if (isset($_SESSION['login_success']) && $_SESSION['login_success']): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Email Verified Successfully',
                text: 'Your email has been verified. You will now be redirected to continue.',
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
        unset($_SESSION['login_success']);
    endif; ?>

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

    <!-- SweetAlert for login failure -->
    <?php if (isset($errors['logIn'])) : ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let errorText = '<?= addslashes(htmlspecialchars($errors['logIn'])) ?>';
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
                        title: 'Login Failed',
                        text: errorText,
                        confirmButtonColor: '#212529'
                    });
                }
            });
        </script>
    <?php endif; ?>

    <!-- SweetAlert for Archived Account Recovery -->
    <?php if ($showArchivedRecovery): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Account Archived',
                    text: "Your account is currently archived. Do you want to recover it and continue logging in?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#212529',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, recover it!',
                    cancelButtonText: 'No, cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Perform AJAX request to recover account
                        const formData = new FormData();
                        formData.append('email', '<?= addslashes($archivedEmail) ?>');
                        formData.append('password', '<?= addslashes($archivedPassword) ?>');
                        formData.append('csrfToken', '<?= $_SESSION['csrfToken'] ?? '' ?>');

                        fetch('./includes/api/recover_archived_account.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Recovered!',
                                    text: 'Your account has been recovered.',
                                    icon: 'success',
                                    confirmButtonColor: '#212529'
                                }).then(() => {
                                    window.location.href = data.redirectUrl || 'user/user.php';
                                });
                            } else {
                                Swal.fire(
                                    'Error!',
                                    data.error || 'Something went wrong.',
                                    'error'
                                );
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire(
                                'Error!',
                                'An unexpected error occurred.',
                                'error'
                            );
                        });
                    }
                });
            });
        </script>
    <?php endif; ?>
</body>

</html>