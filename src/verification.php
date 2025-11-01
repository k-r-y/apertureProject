<?php 
$emailSent = isset($_POST['email']) ? $_POST['email'] : 'xxxx@email.com';

$emailHidden = $emailSent;

for($i = 1; $i < strlen($emailSent)-10; $i++){
    $emailHidden[$i] = '*';
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
            <div class="row justify-content-center align-items-center bg-white shadow p-3 rounded-5">
                <div class="col d-none d-lg-inline p-4 rounded-4 overflow-hidden bg-secondary">
                    <img src="./assets/undraw_forgot-password_nttj.svg" class="img-fluid" alt="">
                </div>
                <div class="col">


                    <form class=" px-1 py-3 justify-content-center">

                     <div class="text-center mb-3">
                            <h1 class=" display-3 m-0 serif">Check your email</h1>
                            <p>Enter the verification code we just sent to <?php echo $emailHidden?></p>
                        </div>

                        <div class="d-flex gap-3 justify-content-center align-items-center mb-4" id="verificationCode">
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
            </div>
        </div>

    </section>

    <!-- <?php include './includes/footer.php'; ?> -->

    <script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
    <script>
        const codeInputs = document.querySelectorAll(".codessssss ");

    codeInputs.forEach((input, index) => {
        input.addEventListener("input", (e) => {
            // Only numbers
            input.value = input.value.replace(/[^0-9]/g, "");

            // Move to next box automatically
            if (input.value && index < codeInputs.length - 1) {
                codeInputs[index + 1].focus();
            }
        });

        input.addEventListener("keydown", (e) => {
            // Move back if pressing Backspace on empty input
            if (e.key === "Backspace" && !input.value && index > 0) {
                codeInputs[index - 1].focus();
            }
        });

        // Handle paste (e.g. user pastes "123456")
        input.addEventListener("paste", (e) => {
            e.preventDefault();
            const paste = e.clipboardData.getData("text").replace(/[^0-9]/g, "");
            paste.split("").forEach((char, i) => {
                if (codeInputs[index + i]) {
                    codeInputs[index + i].value = char;
                }
            });
            const nextIndex = Math.min(index + paste.length, codeInputs.length - 1);
            codeInputs[nextIndex].focus();
        });
    });




    </script>
    
</body>

</html>