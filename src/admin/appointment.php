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
    <title>Appointments - Aperture Admin</title>

    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="admin.css">
    <link rel="icon" href="../assets/camera.png" type="image/x-icon">

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>

<body class="admin-dashboard">
    <?php include_once 'components/sidebar.php'; ?>

    <div class="page-wrapper" id="page-wrapper">
        <?php include_once 'components/header.php'; ?>

        <main class="main-content">
            <div class="container-fluid p-0">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="header-title m-0">Appointments</h1>
                    <a href="#" class="btn btn-gold">+ New Appointment</a>
                </div>

                <!-- Filters -->
                <div class="card-solid mb-4">
                    <div class="card-body d-flex flex-wrap gap-3 align-items-center">
                        <div class="flex-grow-1">
                            <input type="text" class="form-control bg-dark border-secondary text-light" placeholder="Search by client name or event...">
                        </div>
                        <div>
                            <select class="form-select bg-dark border-secondary text-light">
                                <option selected>All Statuses</option>
                                <option value="1">Pending</option>
                                <option value="2">Confirmed</option>
                                <option value="3">Completed</option>
                                <option value="4">Canceled</option>
                            </select>
                        </div>
                        <div>
                            <input type="date" class="form-control bg-dark border-secondary text-light">
                        </div>
                        <button class="btn btn-outline-secondary">Filter</button>
                    </div>
                </div>

                <!-- Appointments Table -->
                <div class="card-solid">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3">Client</th>
                                    <th>Event Date</th>
                                    <th>Package</th>
                                    <th>Assigned Team</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($i = 0; $i < 10; $i++) : ?>
                                    <tr>
                                        <td class="client-name ps-3">Stark Industries</td>
                                        <td>Nov 18, 2025</td>
                                        <td>Premium</td>
                                        <td>Team Alpha</td>
                                        <td><span class="status-badge status-confirmed">Confirmed</span></td>
                                        <td class="text-end pe-3">
                                            <a href="#" class="btn btn-sm btn-outline-secondary">View</a>
                                            <a href="#" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil-fill"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="client-name ps-3">Wayne Enterprises</td>
                                        <td>Nov 22, 2025</td>
                                        <td>Elite</td>
                                        <td>Team Bravo</td>
                                        <td><span class="status-badge status-pending">Pending Deposit</span></td>
                                        <td class="text-end pe-3">
                                            <a href="#" class="btn btn-sm btn-outline-secondary">View</a>
                                            <a href="#" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil-fill"></i></a>
                                        </td>
                                    </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 d-flex justify-content-between align-items-center">
                        <span class="text-secondary small">Showing 1-10 of 50 appointments</span>
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item"><a class="page-link" href="#">Next</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="admin.js"></script>
</body>

</html>