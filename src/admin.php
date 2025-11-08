<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['userId'])) {
    header("Location: index.php");
    exit;
}

if(isset($_GET['action']) and $_GET['action'] === 'logout'){
    require_once './includes/functions/auth.php';
    logout();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Admin Dashboard - Aperture</title>
</head>

<body>
    <?php include './includes/header.php'; ?>


    <section class="w-100 min-vh-100 bg-light d-flex justify-content-center align-items-center position-relative p-2 py-5 p-md-5" id="bookingFormSection">

        <div class="container">
            <div class="row">
                <div class="col d-flex justify-content-center align-items-center flex-column">
                    <form action="" name="bookingForm" method="POST" class="p-4 px-3 bg-white  rounded d-flex gap-4" style="max-width: 800px;" id="bookingForm">




                    </form>

                    <?php if (isset($_SESSION['fullName'])): ?>
                        <h1 class="bg-light text-dark position-absolute bottom-0"><?php echo $_SESSION['firstName']; ?></h1>
                        <a href="?action=logout" class="btn btn-danger position-absolute bottom-0">Logout</a>
                    <?php endif ?>
                    
                </div>
            </div>
        </div>


        <script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
        <script src="script.js"></script>
</body>

</html>