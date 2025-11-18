<?php
require_once '../includes/functions/config.php';
require_once '../includes/functions/auth.php';
require_once '../includes/functions/function.php';
require_once '../includes/functions/csrf.php';
require_once '../includes/functions/session.php';

if (isset($_SESSION["userId"]) and isset($_SESSION["role"]) and $_SESSION["role"] === "User" and isset($_SESSION["isVerified"]) and  $_SESSION["isVerified"]) {
    header("Location: ../booking.php");
    exit;
}

if (!isset($_SESSION['userId']) or !isset($_SESSION['isVerified']) or $_SESSION['isVerified'] === 0) {
    header("Location: ../logIn.php");
    exit;
} else {
    $isProfileCompleted = isProfileCompleted($_SESSION['userId']);
    if (!$isProfileCompleted) {
        header("Location: ../completeProfile.php");
        exit;
    }
}

if (isset($_GET['action']) and $_GET['action'] === 'logout') {
    require_once '../includes/functions/auth.php';
    logout();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services & Packages - Aperture Admin</title>

    <!-- Local Bootstrap CSS -->
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/font/bootstrap-icons.css">
    <link rel="stylesheet" href="admin.css">
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="../style.css">
    <!-- Favicon -->
    <link rel="icon" href="../assets/camera.png" type="image/x-icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Old+Standard+TT:wght@400;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body class="admin-dashboard">
    <?php include_once 'components/sidebar.php'; ?>

    <div class="page-wrapper" id="page-wrapper">
        <?php include_once 'components/header.php'; ?>

        <main class="main-content">
            <div class="container-fluid p-0">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="header-title m-0">Services & Packages</h1>
                    <a href="#" class="btn btn-gold">+ Add New Package</a>
                </div>

                <div class="row g-4">
                    <?php
                    $packages = [
                        ['name' => 'Essential', 'price' => '7,500', 'desc' => 'Perfect for small celebrations or short events.'],
                        ['name' => 'Premium', 'price' => '15,000', 'desc' => 'A balanced package for most events.'],
                        ['name' => 'Elite', 'price' => '25,000', 'desc' => 'A full cinematic experience and top-tier service.'],
                    ];
                    foreach ($packages as $pkg) :
                    ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="card-solid package-card p-4 d-flex flex-column">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h4 class="text-light mb-0"><?= $pkg['name'] ?> Package</h4>
                                        <span class="text-gold fs-5 fw-bold">₱<?= $pkg['price'] ?></span>
                                    </div>
                                    <p class="text-secondary small"><?= $pkg['desc'] ?></p>
                                    <ul class="text-secondary small p-0" style="list-style-type: none;">
                                        <li><i class="bi bi-check-circle-fill text-gold me-2"></i>Feature one</li>
                                        <li><i class="bi bi-check-circle-fill text-gold me-2"></i>Feature two</li>
                                        <li><i class="bi bi-check-circle-fill text-gold me-2"></i>Feature three</li>
                                    </ul>
                                </div>
                                <div class="mt-3">
                                    <a href="#" class="btn btn-sm btn-outline-secondary w-100">Edit Package</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </main>
    </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="admin.js"></script>
</body>

</html>