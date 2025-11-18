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
    <title>Client Directory - Aperture Admin</title>

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
                    <h1 class="header-title m-0">Client Directory</h1>
                    <a href="#" class="btn btn-gold">+ Add New Client</a>
                </div>

                <!-- Filters -->
                <div class="card-solid mb-4">
                    <div class="card-body d-flex flex-wrap gap-3 align-items-center">
                        <div class="flex-grow-1">
                            <input type="text" class="form-control bg-dark border-secondary text-light" placeholder="Search by client name or company...">
                        </div>
                        <button class="btn btn-outline-secondary">Search</button>
                    </div>
                </div>

                <!-- Client Cards -->
                <div class="row g-4">
                    <?php
                    $clients = [
                        ['name' => 'Tony Stark', 'company' => 'Stark Industries', 'bookings' => 5, 'revenue' => '150,000', 'img' => '1'],
                        ['name' => 'Bruce Wayne', 'company' => 'Wayne Enterprises', 'bookings' => 3, 'revenue' => '95,000', 'img' => '2'],
                        ['name' => 'Diana Prince', 'company' => 'Themyscira Exports', 'bookings' => 8, 'revenue' => '210,000', 'img' => '3'],
                        ['name' => 'Clark Kent', 'company' => 'Daily Planet', 'bookings' => 2, 'revenue' => '15,000', 'img' => '4'],
                        ['name' => 'Peter Parker', 'company' => 'Daily Bugle', 'bookings' => 12, 'revenue' => '80,000', 'img' => '5'],
                        ['name' => 'Selina Kyle', 'company' => 'Cat Co.', 'bookings' => 4, 'revenue' => '60,000', 'img' => '6'],
                    ];
                    foreach ($clients as $client) :
                    ?>
                        <div class="col-xl-4 col-md-6">
                            <div class="card-solid client-card p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="https://i.pravatar.cc/150?img=<?= $client['img'] ?>" alt="Client Avatar" class="client-avatar me-3">
                                    <div>
                                        <h5 class="mb-0 text-light"><?= $client['name'] ?></h5>
                                        <p class="mb-0 text-secondary small"><?= $client['company'] ?></p>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-around text-center border-top border-secondary pt-3">
                                    <div>
                                        <p class="mb-0 text-secondary small text-uppercase">Bookings</p>
                                        <h5 class="mb-0 text-light"><?= $client['bookings'] ?></h5>
                                    </div>
                                    <div>
                                        <p class="mb-0 text-secondary small text-uppercase">Revenue</p>
                                        <h5 class="mb-0 text-gold">₱<?= $client['revenue'] ?></h5>
                                    </div>
                                </div>
                                <a href="#" class="btn btn-sm btn-outline-secondary w-100 mt-3">View Profile</a>
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