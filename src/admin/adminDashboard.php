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
session_start();

if (!isset($_SESSION['fullName'])) {
    header("Location: ../login.php");
    exit();
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    
    <style>
        
    </style>
</head>

<body class="admin-dashboard">
    <?php include_once 'components/sidebar.php'; ?>

    <div class="page-wrapper" id="page-wrapper">
        <?php include_once 'components/header.php'; ?>

        <main class="main-content">
            <div class="container-fluid">
                <!-- Header with Timeframe Filter -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h1 class="header-title m-0">Analytics Dashboard</h1>
                            <p class="text-light mb-0" style="opacity: 0.7;">Welcome back, <?= htmlspecialchars($_SESSION['fullName']); ?>!</p>
                        </div>
                        
                        <!-- Timeframe Filter -->
                        <div class="d-flex gap-2 align-items-center">
                            <label class="text-light mb-0 me-2">
                                <i class="bi bi-calendar3"></i> Timeframe:
                            </label>
                            <select id="timeframeFilter" class="form-select form-select-sm neo-input" style="width: auto; min-width: 150px;">
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month" selected>This Month</option>
                                <option value="quarter">This Quarter</option>
                                <option value="year">This Year</option>
                                <option value="all">All Time</option>
                            </select>
                            <button id="refreshDashboard" class="btn btn-sm btn-gold">
                                <i class="bi bi-arrow-clockwise"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stat Cards Row -->
                <div class="row g-4 mb-5">
                    <div class="col-xl-3 col-md-6">
                        <div class="neo-card h-100">
                            <div class="stat-icon"><i class="bi bi-cash-coin"></i></div>
                            <div class="stat-title">Total Revenue</div>
                            <div class="stat-value" id="stat-revenue">₱0.00</div>
                            <div class="stat-trend" id="revenue-growth">+0%</div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="neo-card h-100">
                            <div class="stat-icon"><i class="bi bi-journal-check"></i></div>
                            <div class="stat-title">Total Bookings</div>
                            <div class="stat-value" id="stat-bookings">0</div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="neo-card h-100">
                            <div class="stat-icon"><i class="bi bi-calendar-event"></i></div>
                            <div class="stat-title">Upcoming Events</div>
                            <div class="stat-value" id="stat-upcoming">0</div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="neo-card h-100">
                            <div class="stat-icon"><i class="bi bi-people"></i></div>
                            <div class="stat-title">New Clients (30d)</div>
                            <div class="stat-value" id="stat-clients">0</div>
                        </div>
                    </div>
                </div>

                <!-- Additional Metrics Row -->
                <div class="row g-4 mb-5">
                    <div class="col-xl-3 col-md-6">
                        <div class="neo-card h-100">
                            <div class="stat-icon"><i class="bi bi-graph-up"></i></div>
                            <div class="stat-title">Avg Booking Value</div>
                            <div class="stat-value" id="stat-avg-value">₱0.00</div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="neo-card h-100">
                            <div class="stat-icon"><i class="bi bi-percent"></i></div>
                            <div class="stat-title">Conversion Rate</div>
                            <div class="stat-value" id="stat-conversion">0%</div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="neo-card h-100">
                            <div class="stat-icon"><i class="bi bi-arrow-repeat"></i></div>
                            <div class="stat-title">Client Retention</div>
                            <div class="stat-value" id="stat-retention">0%</div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="neo-card h-100">
                            <a href="api/export_csv.php?type=bookings" class="btn btn-gold w-100 mt-2">
                                <i class="bi bi-download"></i> Export CSV
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Charts Row 1 -->
                <div class="row g-4 mb-4">
                    <div class="col-lg-8">
                        <div class="neo-card ">
                            <h4 class="card-header-title mb-4"><i class="bi bi-graph-up me-2"></i>Revenue Trend (12 Months)</h4>
                            <div id="revenueChart"></div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="neo-card h-100 " style="max-height: 500px; overflow-y: auto;">
                            <h4 class="card-header-title mb-4"><i class="bi bi-clock-history me-2"></i>Recent Activity</h4>
                            <div id="activityFeed" class="mt-3"></div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row 2 -->
                <div class="row g-4 mb-4">
                    <div class="col-lg-8">
                        <div class="neo-card h-100 ">
                            <h4 class="card-header-title mb-4"><i class="bi bi-bar-chart me-2"></i>Monthly Bookings</h4>
                            <div id="bookingsChart"></div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="neo-card h-100">
                            <h4 class="card-header-title mb-4"><i class="bi bi-pie-chart me-2"></i>Booking Status</h4>
                            <div id="statusChart"></div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row 3 -->
                <div class="row g-4 mb-4">
                    <div class="col-lg-6">
                        <div class="neo-card h-100">
                            <h4 class="card-header-title mb-4"><i class="bi bi-box me-2"></i>Package Performance</h4>
                            <div id="packageChart"></div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="neo-card h-100" style="max-height: 30rem; overflow-y: auto;">
                            <h4 class="card-header-title mb-4"><i class="bi bi-calendar-event me-2"></i>Upcoming Events (Next 7 Days)</h4>
                            <div id="upcomingEvents" class="mt-3"></div>
                        </div>
                    </div>
                </div>

                <!-- Event Types Chart -->
                <div class="row g-4">
                    <div class="col-lg-12">
                        <div class="neo-card h-100">
                            <h4 class="card-header-title mb-4"><i class="bi bi-tags me-2"></i>Event Type Distribution</h4>
                            <div id="eventTypeChart"></div>
                        </div>
                    </div>
                </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../libs/sweetalert2/sweetalert2.all.min.js"></script>
    <script src="admin.js"></script>
    <script src="js/dashboard-charts.js"></script>
</body>

</html>