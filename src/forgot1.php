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
            <div class="row justify-content-center align-items-center bg-white shadow p-3 rounded-5">
                <div class="col d-none d-md-inline p-4 rounded-4 overflow-hidden bg-secondary">
                    <img src="./assets/undraw_forgot-password_nttj.svg" class="img-fluid" alt="">
                </div>
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
            </div>
        </div>

    </section>

    <!-- <?php include './includes/footer.php'; ?> -->

    <script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>

</html>