
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
    <title>Admin Dashboard - Aperture</title>
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../luxuryDesignSystem.css">
    <link rel="stylesheet" href="../css/modal.css">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" href="../assets/camera.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>

<body class="admin-dashboard">
    <?php include_once 'components/sidebar.php'; ?>

    <div class="page-wrapper" id="page-wrapper">
        <?php include_once 'components/header.php'; ?>

        <main class="main-content">
            <div class="container-fluid">
                <div class="mb-5">
                    <h1 class="header-title m-0">Admin Dashboard</h1>
                    <p class="text-light" style="opacity: 0.7;">Welcome back, <?= htmlspecialchars($_SESSION['fullName']); ?>!</p>
                </div>

                <!-- Stat Cards Row -->
                <div class="row g-4 mb-5">
                    <div class="col-xl-3 col-md-6">
                        <div class="neo-card">
                            <div class="stat-icon"><i class="bi bi-cash-coin"></i></div>
                            <div class="stat-title">Total Revenue</div>
                            <div class="stat-value">₱125,680</div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="neo-card">
                            <div class="stat-icon"><i class="bi bi-journal-check"></i></div>
                            <div class="stat-title">Total Bookings</div>
                            <div class="stat-value">84</div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="neo-card">
                            <div class="stat-icon"><i class="bi bi-calendar-event"></i></div>
                            <div class="stat-title">Upcoming Events</div>
                            <div class="stat-value">12</div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="neo-card">
                            <div class="stat-icon"><i class="bi bi-people"></i></div>
                            <div class="stat-title">New Clients</div>
                            <div class="stat-value">23</div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="neo-card h-100">
                            <h4 class="card-header-title mb-4">Monthly Bookings</h4>
                            <div id="monthlyBookingsChart"></div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="neo-card h-100">
                            <h4 class="card-header-title mb-4">Service Popularity</h4>
                            <div id="servicePopularityChart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="admin.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // --- Monthly Bookings Chart (Bar Chart) ---
            var monthlyBookingsOptions = {
                series: [{
                    name: 'Bookings',
                    // Replace with your dynamic data from PHP
                    data: [10, 15, 8, 12, 20, 18, 22, 14, 16, 11, 19, 25]
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
                        horizontal: false,
                        columnWidth: '55%',
                        borderRadius: 4
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                },
                yaxis: {
                    title: {
                        text: 'Number of Bookings'
                    }
                },
                fill: {
                    opacity: 1,
                    colors: ['#D4AF37'] // Gold color for bars
                },
                tooltip: {
                    theme: 'dark',
                    y: {
                        formatter: function(val) {
                            return val + " bookings"
                        }
                    }
                }
            };

            var monthlyBookingsChart = new ApexCharts(document.querySelector("#monthlyBookingsChart"), monthlyBookingsOptions);
            monthlyBookingsChart.render();

            // --- Service Popularity Chart (Donut Chart) ---
            var servicePopularityOptions = {
                series: [44, 55, 25], // Replace with your dynamic data from PHP
                chart: {
                    type: 'donut',
                    height: 350,
                    foreColor: 'var(--text-secondary)'
                },
                labels: ['Premium Package', 'Standard Package', 'Basic Package'], // Replace with your package names
                colors: ['#D4AF37', '#E0C670', '#F8E4A0'], // Shades of gold
                legend: {
                    position: 'bottom'
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var servicePopularityChart = new ApexCharts(document.querySelector("#servicePopularityChart"), servicePopularityOptions);
            servicePopularityChart.render();
        });
    </script>
</body>

</html>