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
$showLogInForm = true;
$showVerificationForm = false;
$loginEmail = '';

if (isset($_SESSION['csrfError'])) {
    $errors['csrf'] = $_SESSION['csrfError'];
    unset($_SESSION['csrfError']);
}

if (isset($_SESSION['awaitingLoginVerification']) && ($_SESSION['awaitingLoginVerification'])) {
    $showVerificationForm = true;
    $showLogInForm = false;
    $loginEmail = $_SESSION['loginVerificationEmail'] ?? '';
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
            $result = logInUser($email, $password);

            if ($result['success']) {
                $isEmailVerified = isVerified($email);

                if ($isEmailVerified) {
                    $isProfileCompleted = isProfileCompleted($result['userId']);

                    if ($isProfileCompleted) {
                        setSession($result['userId']);
                        if ($result['role'] === 'Admin') {
                            header("Location: admin.php");
                            exit;
                        } else {
                            header("Location:booking.php");
                            exit;
                        }
                    } else {
                        $_SESSION['email'] = $email;
                        $_SESSION['role'] = $result['role'];
                        $_SESSION['userId'] = $result['userId'];
                        $_SESSION['isVerified'] = true;
                        header("Location:completeProfile.php");
                        exit;
                    }
                } else {
                    $code = createCode($email);
                    $emailSent = sendVerificationEmailWithCode($email, $code);

                    if ($emailSent) {
                        $_SESSION['awaitingLoginVerification'] = true;
                        $_SESSION['loginVerificationEmail'] = $email;
                        $_SESSION['login_success'] = true;

                        $showLogInForm = false;

                        header("Location: logIn.php");
                        exit;
                    } else {
                        $errors['logIn'] = 'Something went wrong';
                    }
                }
            } else {
                $errors['logIn'] = $result['error'];
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
            $email = $_SESSION['loginVerificationEmail'];

            if (empty($email)) {
                $errors['verification'] = "Session expired. please register again";
            } else {
                $result = verifyEmail($code, $email);
                if ($result['success']) {
                    unset($_SESSION['awaitingLoginVerification']);
                    unset($_SESSION['loginVerificationEmail']);
                    unset($_SESSION['loginVerificationUserId']);

                    setSession($result['userId']);

                    $_SESSION['login_success'] = true;
                } else {
                    $errors['verification'] = "Invalid code or expired";
                    $showVerificationForm = true;
                    $loginEmail = $email;
                }
            }
        } else {
            $showVerificationForm = true;
            $loginEmail = $_SESSION['loginVerificationEmail'] ?? '';
        }
    }
}

if (isset($_GET['resend']) and $_GET['resend'] === 'true') {
    $loginEmail = $_SESSION['loginVerificationEmail'] ?? '';

    $resend = resendCode($loginEmail);

    if ($resend) {
        $_SESSION['resend_success'] = true;
    } else {
        $_SESSION['resendSuccess'] = false;
        $errors['resend'] = "Something went wrong. Can't resend  another code.";
    }
}

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

<body>
    <!-- <?php include './includes/header.php'; ?> -->

    <!-- <a href="index.php" class="btn back bg-light border-1 border-secondary shadow">
        <img src="./assets/back.png" class="img-fluid" alt="">
        Back to Home
    </a> -->

    <section class="w-100 min-vh-100  p-0 p-sm-2  d-flex justify-content-center align-items-center position-relative" id="logSection">

        <a href="index.php"><img src="./assets/logo.png" alt="" id="logo"></a>

        <div class="container justify-content-center px-2 p-md-4">
            <div class="row justify-content-center align-items-center bg-white shadow p-3 rounded-5">

                <div class="col d-none d-lg-inline p-2 rounded-4 bg-secondary overflow-hidden">
                    <img src="./assets/undraw_secure-login_m11a (1).svg" class="img-fluid object-fit-cover" alt="">
                </div>

                <?php if ($showLogInForm) : ?>

                    <div class="col p-0">
                        <form action="" method="POST" class="p-2 p-md-4">
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
                                <?php if (isset($errors['logIn'])): ?>
                                    <small class="text-danger"><?= htmlspecialchars($errors['logIn']) ?></small>
                                <?php endif ?>
                            </div>

                            <!-- Remember me and Forgot Password -->

                            <div class="mb-2 d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input type="checkbox" name="remember" class="form-check-input" id="remember">
                                    <label for="remember" class="form-check-label rememberLabel" id="rememberLabel">Remember me</label>
                                </div>

                                <a href="forgot1.php">Forgot Password?</a>
                            </div>


                            <!-- Submit Button -->
                            <div class="mt-3">
                                <input type="submit" class="btn w-100 bg-dark text-light mb-1" value="Log in">
                                <p>Don't have an account? <a href="register.php">Sign up</a></p>
                            </div>




                        </form>
                    </div>

                <?php elseif ($showVerificationForm): ?>
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
                                <p>Didn't receive an email? <a href="logIn.php?resend=true" class="">Resend</a></p>
                            </div>

                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </section>




    <!-- <?php include './includes/footer.php'; ?> -->

    <script src="../bootstrap-5.3.8-dist/sweetalert2.min.js"></script>
    <script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>

    <?php if (isset($_SESSION['userId']) and ($_SESSION['role'])): ?>

        <script>
            console.log('=== SESSION DEBUG ===');
            console.log('User ID:', '<?= $_SESSION['userId'] ?? 'NOT SET'; ?>');
            console.log('Role:', '<?= $_SESSION['role'] ?? 'NOT SET'; ?>');
            console.log('Profile Completed:', <?= $isProfileCompleted ? 'true' : 'false'; ?>);
        </script>
        <?php echo $_SESSION['role'] . " " . $_SESSION['userId']; ?>
    <?php endif; ?>

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
                    window.location.href = 'logIn.php';
                }
            });
        </script>
    <?php
        unset($_SESSION['resend_success']);
    endif; ?>



    <?php if (isset($_SESSION['login']) && $_SESSION['login_success']): ?>
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
        unset($_SESSION['login_success']);
    endif; ?>


    <?php if (($errors['verification'])): ?>
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