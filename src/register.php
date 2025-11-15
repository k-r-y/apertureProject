<?php
require_once './includes/functions/config.php';
require_once './includes/functions/auth.php';
require_once './includes/functions/function.php';
require_once './includes/functions/csrf.php';
require_once './includes/functions/session.php';



if (isset($_SESSION["userId"]) and isset($_SESSION["role"]) and  $_SESSION["role"] === "Admin" and isset($_SESSION["isVerified"]) and  $_SESSION["isVerified"]) {
    header("Location: admin.php");
    exit;
} else if (isset($_SESSION["userId"]) and isset($_SESSION["role"]) and $_SESSION["role"] === "User" and isset($_SESSION["isVerified"]) and  $_SESSION["isVerified"]) {
    header("Location: booking.php");
    exit;
}

$errors = [];
$showVerification = false;
$registrationEmail = '';

if (isset($_SESSION['csrfError'])) {
    $errors['csrf'] = $_SESSION['csrfError'];
    unset($_SESSION['csrfError']);
}

if (isset($_SESSION['awaitingVerification']) && ($_SESSION['awaitingVerification'])) {
    $showVerification = true;
    $registrationEmail = $_SESSION['verificationEmail'] ?? '';
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!validateCSRFToken($_POST['csrfToken'] ?? '')) {
        handleCSRFFailure('register.php');
    }

    if (isset($_POST['formType']) && $_POST['formType'] === 'registration') {
        // Getting the user input
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirmPassword'];

        //checking if there's an error in the password and email
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

        //checking if there is an existing email/account
        $checkEmail = isEmailExists($email);

        if (!$checkEmail['success']) {
            $errors['email'] = $checkEmail['error'];
        } else {
            if (empty($errors)) {

                // registering the user
                $result = registerUser($email, $password);

                if ($result['success']) {
                    $_SESSION['registration_success'] = true;
                    $_SESSION['awaitingVerification'] = true;
                    $_SESSION['verificationEmail'] = $email;
                    $_SESSION['verificationUserId'] = $result['userId'];

                    header("Location: register.php");
                    exit;
                } else {
                    $errors['email'] = $result['error'];
                }
            }
        }
    } else if (isset($_POST['formType']) && $_POST['formType'] === 'verification') {

        $code = trim($_POST['code1'] . $_POST['code2'] . $_POST['code3'] . $_POST['code4'] . $_POST['code5'] . $_POST['code6']);

        if (!$code) {
            $errors['verification'] = 'Verification code is required';
        } else if (!preg_match('/^[0-9]{6}$/', $code)) {
            $errors['verification'] = "Verification code must be exactly 6 digits";
        }

        if (empty($errors)) {
            $email = $_SESSION['verificationEmail'];

            if (empty($email)) {
                $errors['verification'] = "Session expired. please register again";
            } else {
                $result = verifyEmail($code, $email);
                if ($result['success']) {
                    unset($_SESSION['awaitingVerification']);
                    unset($_SESSION['verificationEmail']);
                    unset($_SESSION['verificationUserId']);

                    setSession($result['userId']);

                    $_SESSION['verification_success'] = true;
                } else {
                    $errors['verification'] = "Invalid code or expired";
                    $showVerification = true;
                    $registrationEmail = $email;
                }
            }
        } else {
            $showVerificationForm = true;
            $registrationEmail = $_SESSION['verificationEmail'] ?? '';
        }
    }
}

if (isset($_GET['resend']) and $_GET['resend'] === 'true') {
    $email = $_SESSION['verificationEmail'] ?? '';

    $resend = resendCode($registrationEmail);

    if ($resend) {
        $_SESSION['resend_success'] = true;
    } else {
        $_SESSION['resendSuccess'] = false;
        $errors['resend'] = "Something went wrong. Can't resend  another code.";
    }
}
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

<body>
    <!-- <?php include './includes/header.php'; ?> -->




    <section class="w-100 min-vh-100  p-0 p-sm-2  d-flex justify-content-center align-items-center position-relative" id="reg">

        <a href="index.php"><img src="./assets/logo.png" alt="" id="logo"></a>


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
                                <input type="email" name="email" id="email" value="<?= (isset($errors['email']) ? htmlspecialchars($email) : (isset($errors['password']) ? htmlspecialchars($email) : (isset($errors['ConfirmPassword']) ? htmlspecialchars($email) : ''))) ?>" class="form-control <?= (!isset($errors['email']) ? '' : 'is-invalid')  ?> " required>

                                <p class="text-danger"><?= (isset($errors['email'])) ? htmlspecialchars($errors['email']) : '' ?></p>
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

                            <!-- Check Terms and Condition -->

                            <div class="form-check mb-2 d-flex gap-2 justify-content-center align-items-start">
                                <input type="checkbox" name="termsCheck" id="termsCheck" class="form-check-input" required>
                                <label for="termsCheck" id="termsLabel" class="form-check-label">By creating an account, you confirm that you have read, understood, and agreed to the <a href="#" type="button" data-bs-toggle="modal" data-bs-target="#dataModal">Terms and Conditions and Privacy Notice.</a></label>
                            </div>

                            <?php include "./includes/modals/terms.php" ?>

                            <!-- Submit Button -->
                            <div class="mt-3">
                                <input type="submit" class="btn w-100 bg-dark text-light mb-1" value="Sign up">
                                <p>Already have an account? <a href="logIn.php">Log in</a></p>
                            </div>


                        </form>


                    </div>

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
                                <p>Didn't receive an email? <a href="register.php?resend=true" class="">Resend</a></p>
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


    <!-- <?php include './includes/footer.php'; ?> -->

    <!-- SweetAlert2 JS -->
    <script src="../bootstrap-5.3.8-dist/sweetalert2.min.js"></script>
    <script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>

    <?php if (isset($_SESSION['registration_success']) && $_SESSION['registration_success']): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Registration Successful!',
                html: '<p>A verification code has been sent to your email.</p><p class="text-muted">Please check your inbox and enter the code to verify your account.</p>',
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



    <?php if (isset($_SESSION['resend_success']) && $_SESSION['resend_success']): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Resend code Successful!',
                html: '<p>A verification code has been sent to your email.</p><p class="text-muted">Please check your inbox and enter the code to verify your account.</p>',
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



    <?php if (isset($_SESSION['verification_success']) && $_SESSION['verification_success']): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Verification Code Successfully Confirmed!',
                html: '<p>Please finish your profile information to continue.</p>',
                confirmButtonText: 'Continue',
                confirmButtonColor: '#212529',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "booking.php";
                }
            });
        </script>
    <?php
        unset($_SESSION['verification_success']);
    endif; ?>


    <?php if (isset($errors['verification'])): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Registration Failed!',
                text: '<?= htmlspecialchars($errors['verification']) ?>' + '. Please try again.',
                confirmButtonText: 'Continue',
                confirmButtonColor: '#212529',
                allowOutsideClick: false
            });
            1
        </script>
    <?php endif; ?>
</body>

</html>