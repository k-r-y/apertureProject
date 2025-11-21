<?php
require_once './includes/functions/config.php';
require_once './includes/functions/auth.php';
require_once './includes/functions/function.php';
require_once './includes/functions/csrf.php';
require_once './includes/functions/session.php';



if (!isset($_SESSION['userId'])) {
    header("Location: logIn.php");
    exit;
} else {
    $isProfileCompleted = isProfileCompleted($_SESSION['userId']);
    if ($isProfileCompleted) {
        if (isset($_SESSION["userId"]) and isset($_SESSION["role"]) and  $_SESSION["role"] === "Admin") {
            header("Location: admin/adminDashboard.php");
            exit;
        } else if (isset($_SESSION["userId"]) and isset($_SESSION["role"]) and $_SESSION["role"] === "User") {
            header("Location: user/user.php");
            exit;
        }
    }
}

$errors = [];

if (isset($_SESSION['csrfError'])) {
    $errors['csrf'] = $_SESSION['csrfError'];
    unset($_SESSION['csrfError']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!validateCSRFToken($_POST['csrfToken'] ?? '')) {
        handleCSRFFailure("completeProfile.php");
    }

    $firstName = trim($_POST['fname']);
    $lastName = trim($_POST['lname']);
    $contact = trim($_POST['contactInput']);
    $fullName = $firstName . " " . $lastName;

    if (empty($firstName)) {
        $errors['fname'] = "First Name is required";
    }

    if (empty($lastName)) {
        $errors['lname'] = "Last name is required";
    }

    if (empty($contact)) {
        $errors['contact'] = "Contact number is required";
    } else if (!ctype_digit($contact)) {
        $errors['contact'] = "Contact must contain only digits";
    } else if (strlen($contact) !== 11) {
        $errors['contact'] = "Invalid contact number";
    } else if (!preg_match('/^09[0-9]{9}$/', $contact)) {
        $errors['contact'] = "Contact number must start with 09";
    }

    if (empty($errors)) {
        $completeProfile = saveUserProfile($_SESSION['userId'], $firstName, $lastName, $fullName, $contact);

        if ($completeProfile) {
            $_SESSION['firstName'] = $firstName;
            $_SESSION['lastName'] = $lastName;
            $_SESSION['fullName'] = $fullName;
            $_SESSION['contact'] = $contact;
            $_SESSION['completeProfile-success'] = true;
        } else {
            $errors['submitError'] = "Something went wrong";
        }
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
    <title>Complete your profile - Aperture</title>
</head>

<body>
    <!-- <?php include './includes/header.php'; ?> -->




    <section class="w-100 min-vh-100  p-0 p-sm-2  d-flex justify-content-center align-items-center" id="reg">

        <div class="container justify-content-center px-4 p-md-3">
            <div class="row justify-content-center align-items-center bg-white shadow p-3 rounded-5  ">

                <div class="col position-relative">


                    <form action="" method="POST" class="p-4">
                        <?php csrfField(); ?>

                        <div class="text-center mb-3">
                            <h1 class=" display-5 m-0 serif">Complete your profile</h1>
                            <small>Join Aperture today and enjoy seamless booking, transparent pricing, and trusted pros at your fingertips.</small>
                        </div>

                        <!-- First and Last Name  -->

                        <div class="mb-2 d-flex gap-2 flex-column flex-md-row ">
                            <div class="w-100">
                                <label for="fname" class="form-label">First name<span class="text-danger">*</span></label>
                                <input type="text" name="fname" id="fname" class="form-control <?php echo (!isset($errors['fname']) ? '' : 'is-invalid')  ?> " placeholder="e.g., Prince Andrew" required>
                            </div>
                            <div class="w-100">
                                <label for="lname" class="form-label">Last name<span class="text-danger">*</span></label>
                                <input type="text" name="lname" id="lname" class="form-control <?php echo (!isset($errors['lname']) ? '' : 'is-invalid')  ?> " placeholder="e.g., Casiano" required>
                            </div>
                        </div>

                        <!-- Phone  -->

                        <div class="mb-2">
                            <label class="form-label" for="contactInput">Contact No.<span class="text-danger">*</span></label>
                            <input
                                type="text"
                                name="contactInput"
                                id="contactInput"
                                class="form-control <?php echo (!isset($errors['contact']) ? '' : 'is-invalid')  ?> "
                                placeholder="e.g., 09827386287"
                                inputmode="numeric"
                                maxlength="11"
                                required>
                            <?php if (isset($errors['contact'])): ?>
                                <p class="text-danger"><?= htmlspecialchars($errors['contact']); ?></p>
                            <?php endif ?>
                        </div>


                        <?php if (isset($errors['submitError'])): ?>
                            <p class="text-danger"><?= htmlspecialchars($errors['submitError']) ?></p>
                        <?php endif ?>

                        <!-- Check Terms and Condition -->

                        <div class="form-check mb-3">
                            <input type="checkbox" name="termsCheck" id="termsCheck" class="form-check-input" required>
                            <label for="termsCheck" id="termsLabel" class="form-check-label"><small>By creating an account, you confirm that you have read, understood, and agreed to the <a href="#" type="button" data-bs-toggle="modal" data-bs-target="#dataModal">Terms and Conditions and Privacy Notice.</a></small></label>
                        </div>

                        <?php include "./includes/modals/terms.php" ?>


                        <input type="submit" class="btn w-100 bg-dark text-light mb-1" value="Complete Profile" id="profileSubmitBtn" disabled>


                    </form>
                </div>

                <div class="col d-none d-lg-inline p-4 rounded-4 bg-secondary overflow-hidden">
                    <img src="./assets/undraw_complete-form_aarh.svg" class="img-fluid object-fit-cover" alt="">
                </div>
            </div>
        </div>

    </section>



    <!-- <?php include './includes/footer.php'; ?> -->

    <script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../bootstrap-5.3.8-dist/sweetalert2.min.js"></script>
    <script src="script.js"></script>

    <?php if (isset($_SESSION['completeProfile-success']) and ($_SESSION['completeProfile-success'])): ?>

        <script>
            Swal.fire({
                icon: 'success',
                title: "Profile Completed!",
                html: '<p>Welcome to aperture</p><p>Your profile has been successfully set up. </p>',
                allowOutsideClick: false,
                allowEnterKey: true,
                allowEscapeKey: false,
                confirmButtonText: 'Continue to dashboard',
                confirmButtonColor: '#212529'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "<?= ($_SESSION['role'] === 'Admin') ? 'admin/adminDashboard.php' : 'user/user.php'; ?>";
                    console.log('<?= $_SESSION['role'] . " " . $_SESSION['userId']; ?>');
                }
            });
        </script>
        <?php unset($_SESSION['completeProfile-success']);
        echo $_SESSION['role'] . " " . $_SESSION['userId']; ?>
    <?php endif; ?>

</body>

</html>