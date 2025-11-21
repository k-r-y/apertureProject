<?php
require_once '../includes/functions/config.php';
require_once '../includes/functions/auth.php';
require_once '../includes/functions/function.php';
require_once '../includes/functions/csrf.php';
require_once '../includes/functions/session.php';

// Redirect non-users or unverified users
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'User' || !$_SESSION['isVerified']) {
    header("Location: ../logIn.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Aperture</title>

    <!-- Local Bootstrap CSS -->
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/font/bootstrap-icons.css">
    <!-- Custom User CSS -->
    <link rel="stylesheet" href="user.css">
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="../style.css">
    <!-- Favicon -->
    <link rel="icon" href="../assets/camera.png" type="image/x-icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Old+Standard+TT:wght@400;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>

<body class="admin-dashboard">
    <?php include_once 'components/sidebar.php'; ?>

    <div class="page-wrapper" id="page-wrapper">
        <?php include_once 'components/header.php'; ?>

        <main class="main-content">
            <div class="container-fluid p-0">

                <div class="mb-4">
                    <h1 class="header-title m-0">Welcome, <?= htmlspecialchars($_SESSION['firstName'] ?? 'Client') ?>!</h1>
                    <p class="text-secondary">Here's a summary of your account.</p>
                </div>

                <!-- KPI Cards -->
                <div class="row g-4 mb-5">
                    <div class="col-lg-4 col-md-6">
                        <div class="stat-card">
                            <div class="stat-icon"><i class="bi bi-calendar-event"></i></div>
                            <div class="stat-value">1</div>
                            <div class="stat-label">Upcoming Events</div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="stat-card">
                            <div class="stat-icon"><i class="bi bi-journal-check"></i></div>
                            <div class="stat-value">4</div>
                            <div class="stat-label">Total Bookings</div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <div class="stat-card">
                            <div class="stat-icon"><i class="bi bi-wallet2"></i></div>
                            <div class="stat-value">₱55,700</div>
                            <div class="stat-label">Total Spent</div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="row g-4">
                    <!-- Bookings by Type Chart -->
                    <div class="col-12">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h5 class="chart-title">Your Bookings by Type</h5>
                            </div>
                            <div id="userBookingsChart"></div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="user.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var options = {
                series: [{
                    name: 'Bookings',
                    // Replace with dynamic data from PHP
                    data: [2, 1, 1]
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: false
                    },
                    foreColor: 'var(--text-secondary)'
                },
                plotOptions: {
                    bar: {
                        distributed: true,
                        horizontal: false,
                        borderRadius: 4
                    },
                },
                colors: ['#D4AF37', '#E0C670', '#F8E4A0'],
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: ['Weddings', 'Corporate', 'Birthdays'],
                },
                yaxis: {
                    title: {
                        text: 'Number of Bookings'
                    }
                },
                legend: {
                    show: false
                },
                tooltip: {
                    theme: 'dark'
                }
            };

            var chart = new ApexCharts(document.querySelector("#userBookingsChart"), options);
            chart.render();
        });
    </script>
</body>

</html>