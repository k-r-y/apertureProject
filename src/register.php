<?php
require_once './includes/functions/config.php';
require_once './includes/functions/auth.php';
require_once './includes/functions/function.php';
require_once './includes/functions/csrf.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(isset($_SESSION["userId"]) and isset($_SESSION["role"]) and  $_SESSION["role"] === "Admin" and isset($_SESSION["isVerified"]) and  $_SESSION["isVerified"]){
    header("Location: admin.php");
    exit;
}else if (isset($_SESSION["userId"]) and isset($_SESSION["role"]) and $_SESSION["role"] === "User" and isset($_SESSION["isVerified"]) and  $_SESSION["isVerified"]){
    header("Location: booking.php");
    exit;
}




$errors = [];
 
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if(validateCSRFToken($_POST[]))

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
                header("Location: register.php");
                exit;
            } else {
                $errors['email'] = $result['error'];
            }
        }
    }
}

// $errors = [];

// if ($_SERVER["REQUEST_METHOD"] === "POST") {
//     $firstName = $_POST['fname'];
//     $lastName = $_POST['lname'];
//     $fullName = $_POST['fname'] . " " . $_POST['lname'];
//     $email = trim($_POST['email']);
//     $password = $_POST['password'];
//     $confirmPassword = $_POST['confirmPassword'];
//     $role = $_POST['role'] ?? 'User';

//     if (empty($firstName)) {
//         $errors['fname'] = "First name is required";
//     }

//     if (empty($lastName)) {
//         $errors['lname'] = "Last name is required";
//     }

//     if (empty($email)) {
//         $errors['email'] = "Email is required";
//     }

//     if (empty($password)) {
//         $errors['password'] = "Password is required";
//     }

//     if (empty($confirmPassword)) {
//         $errors['ConfirmPassword'] = "Please confirm your password";
//     }

//     if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//         $errors['email'] = "Please input a valid email";
//     } else {
//         $query = $conn->prepare("SELECT userID from users WHERE Email = ?");
//         $query->bind_param("s", $email);
//         $query->execute();
//         $query->store_result();

//         if ($query->num_rows !== 0) {
//             $errors['email'] = "Email is already registered";
//         }

//         $query->close();
//     }

//     if ($password !== $confirmPassword) {
//         $errors['ConfirmPassword'] = "Password do not matched";
//     }

//     if (empty($errors)) {

//         $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

//         $query = $conn->prepare("INSERT into users(FirstName, LastName, FullName, Email, Password, Role) values (?,?,?,?,?,?)");
//         $query->bind_param("ssssss", $firstName, $lastName, $fullName, $email, $hashedPassword, $role);

//         if ($query->execute()) {
//             $query->close();
//             echo "<script>
//                         alert('Account successfully created');
//                         window.location = 'logIn.php';
//                   </script>";
//             exit;
//         } else {
//             echo "<script>alert('Something went wrong');</script>";
//             $query->close();
//             exit;
//         }
//     }
// }

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../bootstrap-5.3.8-dist/css/bootstrap.min.css">
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
            <div class="row justify-content-center align-items-center bg-white shadow p-0 p-md-3 rounded-5  ">

                <div class="col ">
                    <form action="" method="POST" class="p-2">

                        <input type="hidden" name="csrfToken" value="<?= htmlspecialchars($csrfToken) ?>">

                        <div class="text-center mb-3">
                            <h1 class=" display-1 m-0 serif">Sign up</h1>
                            <small>Join Aperture today and enjoy seamless booking, transparent pricing, and trusted pros at your fingertips.</small>
                        </div>



                        <!-- Email  -->

                        <div class="mb-2">
                            <label class="form-label" for="email">Email<span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" value="<?=  (isset($errors['email']) ? htmlspecialchars($email) : (isset($errors['password']) ? htmlspecialchars($email) : (isset($errors['ConfirmPassword']) ? htmlspecialchars($email) : ''))) ?>" class="form-control <?= (!isset($errors['email']) ? '' : 'is-invalid')  ?> " required>
                 
                                <p class="text-danger"><?= (isset($errors['email'])) ? htmlspecialchars($errors['email']) : '' ?></p>
                         
                        </div>

                        <!-- Password -->

                        <div class="mb-2">
                            <label class="form-label" for="password">Password<span class="text-danger">*</span></label>
                            <input type="password" name="password" id="password"  class="form-control <?=  (isset($errors['password'])  ? 'is-invalid' : '')   ?> " required>
                             <p class="text-danger"><?= (isset($errors['password'])) ? htmlspecialchars($errors['password']) : '' ?></p>
                        </div>

                        <!-- Confirm Password -->

                        <div class="mb-3">
                            <label class="form-label" for="confirmPassword">Confirm Password<span class="text-danger">*</span></label>
                            <input type="password" name="confirmPassword" id="confirmPassword" class="form-control <?=  htmlspecialchars(!isset($errors['ConfirmPassword']) ? '' : 'is-invalid')  ?> " required>
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
            html: '<p>A verification link has been sent to your email.</p><p class="text-muted">Please check your inbox and click the link to verify your account.</p>',
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
    endif;
    ?>
</body>

</html>






<!-- <form action="" method="POST" class="px-md-5 px-1 py-2 justify-content-center">
                        <div class="text-center mb-3">
                            <h1 class=" display-1 m-0 serif">Sign up</h1>
                            <small>Join Aperture today and enjoy seamless booking, transparent pricing, and trusted pros at your fingertips.</small>
                        </div>

                        <div class="mb-2">
                            <div class="input-group">
                                <span class="input-group-text"><img src="./assets/email(1).png" alt=""></span>
                                <div class="form-floating">
                                    <input type="text" name="fullName" id="fullName" placeholder="e.g., Prince Andrew Casiano" class="form-control" value="<?= htmlspecialchars($fullName ?? '') ?>" required>
                                    <label for="fullName" class="form-label">Full Name</label>
                                </div>


                            </div>

                            <?php if (isset($errors['fullName'])): ?>
                                <small class="d-block text-danger"><?php echo $errors['fullName']; ?></small>
                            <?php endif; ?>
                        </div>

                        




                        <div class=" mb-2">
                            <div class="input-group">
                                <span class="input-group-text"><img src="./assets/id-card.png" alt=""></span>
                                <div class="form-floating ">
                                    <input type="email" name="email" id="email" placeholder="e.g., aperture.eventbookings@gmail.com" class="form-control" value="<?= htmlspecialchars($email ?? '') ?>" required>
                                    <label for="email" class="form-label">Email</label>
                                </div>



                            </div>
                            <?php if (isset($errors['email'])): ?>
                                <small class="d-block text-danger"><?php echo $errors['email']; ?></small>
                            <?php endif; ?>

                        </div>







                        <div class="input-group mb-1">
                            <span class="input-group-text"><img src="./assets/padlock.png" alt=""></span>
                            <div class="form-floating">
                                <input type="password" name="password" id="password" placeholder="Enter your password" class="form-control" value="<?= htmlspecialchars($password ?? '') ?>" aria-describedby="passMess" required>
                                <label for="password" class="form-label">Password</label>
                            </div>
                        </div>
                        <span class="form-text d-block mb-2" id="passMess">Password must be at least 8 characters long</span>


                        <div class="mb-2">

                            <div class="input-group ">
                                <span class="input-group-text"><img src="./assets/padlock.png" alt=""></span>
                                <div class="form-floating">
                                    <input type="password" name="ConfirmPassword" id="ConfirmPassword" placeholder="Confirm your Password" class="form-control  " value="<?= htmlspecialchars($confirmPassword ?? '') ?>" required>
                                    <label for="ConfirmPassword" class="form-label">ConfirmPassword</label>

                                </div>

                            </div>
                            <?php if (isset($errors['ConfirmPassword'])): ?>
                                <small class="d-block text-danger"><?php echo $errors['ConfirmPassword']; ?></small>
                            <?php endif; ?>
                        </div>



                        <div class="form-check">
                            <input type="checkbox" name="" id="check" class="form-check-input" required>
                            <label for="check" class="form-check-label"><small>By creating an account, you confirm that you have read, understood, and agreed to the <a href="#" type="button" data-bs-toggle="modal" data-bs-target="#dataModal">Terms and Conditions and Privacy Notice.</a></small></label>


                            <div class="modal fade" id="dataModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="dataModalLabel">Terms and Conditions</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">

                                            <div class="mb-5">
                                                <h1 class="serif">Aperture Terms and Conditions</h1>

                                                <p>By creating an account in Aperture: Event Photography and Videography Appointment System, you agree to the following:</p>

                                                <ol>
                                                    <li>
                                                        <strong>Account Responsibility</strong>
                                                        <ul>
                                                            <li>You must provide accurate and truthful information when creating an account.</li>
                                                            <li>You are responsible for maintaining the confidentiality of your login details.</li>
                                                            <li>Any activity under your account will be considered your responsibility.</li>
                                                        </ul>
                                                    </li>

                                                    <li>
                                                        <strong>Service Usage</strong>
                                                        <ul>
                                                            <li>The system is intended only for booking, scheduling, and managing event photography and videography services.</li>
                                                            <li>Misuse of the system (e.g., fake bookings, spam, or unauthorized access) may result in account suspension or termination.</li>
                                                        </ul>
                                                    </li>

                                                    <li>
                                                        <strong>Appointment Policy</strong>
                                                        <ul>
                                                            <li>Bookings should be made honestly with the correct details of the event.</li>
                                                            <li>Cancellation or rescheduling must be done within the timeframe set by the service provider.</li>
                                                            <li>Failure to comply may lead to restrictions in future bookings.</li>
                                                        </ul>
                                                    </li>

                                                    <li>
                                                        <strong>Content and Ownership</strong>
                                                        <ul>
                                                            <li>All media files (photos, videos) produced belong to the photographer/videographer unless stated otherwise in the service agreement.</li>
                                                            <li>Clients are prohibited from using Aperture to distribute or upload unlawful or offensive materials.</li>
                                                        </ul>
                                                    </li>

                                                    <li>
                                                        <strong>System Rights</strong>
                                                        <ul>
                                                            <li>The developers reserve the right to update, modify, or suspend the system for improvements or maintenance.</li>
                                                            <li>Terms may be updated from time to time, and users will be notified within the system.</li>
                                                        </ul>
                                                    </li>
                                                </ol>

                                            </div>

                                            <div>
                                                <h1 class="serif">Aperture Privacy Notice</h1>

                                                <p>Your privacy is important to us. When you create an account and use Aperture, we collect and process the following information:</p>

                                                <ol>
                                                    <li>
                                                        <strong>Information We Collect</strong>
                                                        <ul>
                                                            <li>Personal details (name, email, contact number).</li>
                                                            <li>Event details (event type, date, location).</li>
                                                            <li>Login information (email, encrypted password).</li>
                                                        </ul>
                                                    </li>

                                                    <li>
                                                        <strong>How We Use Your Information </strong>
                                                        <ul>
                                                            <li>To process and manage your bookings.</li>
                                                            <li>To contact you about appointments, cancellations, or service updates.</li>
                                                            <li>To improve our system and services.</li>
                                                        </ul>
                                                    </li>

                                                    <li>
                                                        <strong>Data Protection</strong>
                                                        <ul>
                                                            <li>All collected information is stored securely and will not be shared with third parties without your consent, unless required by law.</li>
                                                            <li>We use security measures to protect your account and booking details.</li>
                                                        </ul>
                                                    </li>

                                                    <li>
                                                        <strong>User Rights</strong>
                                                        <ul>
                                                            <li>You have the right to access, update, or delete your account information.</li>
                                                            <li>You may contact us if you have concerns about your data privacy.</li>
                                                        </ul>
                                                    </li>


                                                </ol>
                                            </div>



                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>




                        <input type="submit" value="Register" class="btn bg-dark text-light w-100 my-2">

                        <p>Already have an account? <a href="logIn.php">Sign in</a></p>

                    </form> -->