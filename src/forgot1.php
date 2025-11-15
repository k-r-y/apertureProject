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


$errors  = [];

$showEmailForm = true;
$showCodeForm = false;
$showNewPassword = false;

if (isset($_SESSION['csrfError'])) {
    $errors['csrf'] = $_SESSION['csrfError'];
    unset($_SESSION['csrfError']);
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="./assets/camera.png" type="image/x-icon">
    <title>Forgot Password - Aperture</title>
</head>

<body>
    <!-- <?php include './includes/header.php'; ?> -->

    <section class="w-100 min-vh-100  p-0 p-sm-2  d-flex justify-content-center align-items-center position-relative" id="forgot1">

        <a href="index.php"><img src="./assets/logo.png" alt="" id="logo"></a>


        <div class="container justify-content-center px-4 p-md-3">
            <div class="row justify-content-center align-items-center bg-white shadow py-3 rounded-5">
                <div class="col d-none d-md-inline p-4 rounded-4 overflow-hidden bg-secondary">
                    <img src="./assets/undraw_forgot-password_nttj.svg" class="img-fluid" alt="">
                </div>

                <?php if ($showEmailForm): ?>
                    <div class="col">
                        <form action="verification.php" method="POST" class=" px-1 py-3 justify-content-center">

                            <div class="text-center mb-3">
                                <h1 class=" display-3 m-0 serif">Forgot Your Password?</h1>
                                <p>Enter your email address below and we'll send you a verification code to reset your password.</p>
                            </div>


                            <div class="mb-3">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="e.g., princesuperpogi@email.com" required>
                            </div>


                            <!-- <input type="submit" value="Send Verification Code" class="btn bg-dark text-light w-100 my-2 py-2"> -->

                            <div class="mb-1">
                                <input type="submit" value="Send Verification Code" class="btn bg-dark text-light w-100 my-2 py-2">
                                <a href="login.php" class="btn bg-light text-dark border w-100 mb-2">Back to Login</a>
                            </div>

                        </form>
                    </div>
                <?php elseif ($showCodeForm): ?>
                    <div class="col">


                        <form class=" px-0 py-3 justify-content-center">

                            <div class="text-center mb-3">
                                <h1 class=" display-3 m-0 serif">Check your email</h1>
                                <p>Enter the verification code we just sent to your email</p>
                            </div>

                            <div class="d-flex gap-1 gap-md-3 justify-content-center align-items-center mb-4" id="verificationCode">
                                <input type="text" inputmode="numeric" pattern="[0-9]*" name="" id="" class="codessssss form-control fs-3 text-center" maxlength="1" required>
                                <input type="text" inputmode="numeric" pattern="[0-9]*" name="" id="" class="codessssss form-control fs-3 text-center" maxlength="1" required>
                                <input type="text" inputmode="numeric" pattern="[0-9]*" name="" id="" class="codessssss form-control fs-3 text-center" maxlength="1" required>
                                <input type="text" inputmode="numeric" pattern="[0-9]*" name="" id="" class="codessssss form-control fs-3 text-center" maxlength="1" required>
                                <input type="text" inputmode="numeric" pattern="[0-9]*" name="" id="" class="codessssss form-control fs-3 text-center" maxlength="1" required>
                                <input type="text" inputmode="numeric" pattern="[0-9]*" name="" id="" class="codessssss form-control fs-3 text-center" maxlength="1" required>
                            </div>

                            <div class="mb-1">
                                <a href="forgot2.php" class="btn bg-dark text-light w-100 mb-2">Verify Code</a>
                                <a href="login.php" class="btn bg-light text-dark border w-100 mb-2">Back to Login</a>
                                <p>Didn't receive an email? <a href="#" class="">Resend</a></p>
                            </div>

                        </form>
                    </div>
                <?php elseif ($showNewPassword): ?>
                    <div class="col">
                        <form action="" method="POST" class=" px-1 py-3 justify-content-center">

                            <div class="text-center mb-3">
                                <h1 class=" display-3 m-0 serif">Create new Password</h1>
                                <p>Your new password should be at least 8 characters long.</p>
                            </div>


                            <!-- Password -->

                            <div class="mb-2">
                                <label class="form-label" for="password">Password</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>

                            <!-- Confirm Password -->

                            <div class="mb-2">
                                <label class="form-label" for="confirmPassword">Confirm Password</label>
                                <input type="password" name="confirmPassword" id="confirmPassword" class="form-control" required>
                            </div>


                            <!-- <input type="submit" value="Send Verification Code" class="btn bg-dark text-light w-100 my-2 py-2"> -->

                            <div class="my-3">
                                <input type="submit" value="Update Password" class="btn bg-dark text-light w-100 mb-2">
                                <a href="login.php" class="btn bg-light text-dark border w-100 mb-2">Back to Login</a>
                            </div>


                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </section>

    <!-- <?php include './includes/footer.php'; ?> -->

    <script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>

</html>