
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
    <link rel="stylesheet" href="../css/sidebar.css">
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
                        <div class="d-flex gap-2 align-items-center flex-wrap justify-content-end">
                            <div id="customDateRange" class="d-none d-flex gap-2 align-items-center">
                                <input type="date" id="startDate" class="form-control form-control-sm neo-input text-light bg-dark border-secondary" placeholder="Start Date">
                                <span class="text-muted">-</span>
                                <input type="date" id="endDate" class="form-control form-control-sm neo-input text-light bg-dark border-secondary" placeholder="End Date">
                            </div>
                            
                            <label class="text-light mb-0 me-2">
                                <i class="bi bi-calendar3"></i> Timeframe:
                            </label>
                            <select id="timeframeFilter" class="form-select form-select-sm neo-input" style="width: auto; min-width: 150px;">
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month" selected>This Month</option>
                                <option value="quarter">This Quarter</option>
                                <option value="year">This Year</option>
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
                            <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
                            <div class="stat-title">Pending Requests</div>
                            <div class="stat-value" id="stat-pending">0</div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="neo-card h-100">
                            <div class="stat-icon"><i class="bi bi-graph-up-arrow"></i></div>
                            <div class="stat-title">Average Revenue</div>
                            <div class="stat-value" id="stat-avg-value">₱0.00</div>
                        </div>
                    </div>
                </div>

                <!-- Main Visual Row -->
                <div class="row g-4 mb-4">
                    <div class="col-lg-8">
                        <div class="neo-card h-100">
                            <h4 class="card-header-title mb-4">Revenue Trend</h4>
                            <div id="revenueBookingChart"></div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="neo-card h-100" style="min-height: 400px;">
                            <h4 class="card-header-title mb-4">Action Center</h4>
                            
                            <ul class="nav nav-tabs border-bottom-0 mb-3" id="actionTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active text-gold bg-transparent border-0 ps-0" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-pane" type="button" role="tab">Pending</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link text-muted bg-transparent border-0" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming-pane" type="button" role="tab">Upcoming</button>
                                </li>
                            </ul>
                            
                            <div class="tab-content" id="actionContent" style="height: 300px; overflow-y: auto;">
                                <div class="tab-pane fade show active" id="pending-pane" role="tabpanel">
                                    <div id="pendingFeed"></div>
                                </div>
                                <div class="tab-pane fade" id="upcoming-pane" role="tabpanel">
                                    <div id="upcomingFeed"></div>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>

                <!-- Insights Row -->
                <div class="row g-4 mb-4">
                    <div class="col-lg-6">
                        <div class="neo-card h-100">
                            <h4 class="card-header-title mb-4">Package Popularity</h4>
                            <div id="topPackagesChart"></div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="neo-card h-100">
                            <h4 class="card-header-title mb-4">Booking Status</h4>
                            <div id="bookingStatusChart"></div>
                        </div>
                    </div>
                </div>

                <!-- Event Type Distribution -->
                <div class="row g-4 mb-4">
                    <div class="col-12">
                        <div class="neo-card h-100">
                            <h4 class="card-header-title mb-4">Event Type Distribution</h4>
                            <div id="eventTypeChart" style="min-height: 350px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../libs/sweetalert2/sweetalert2.all.min.js"></script>
    <script src="admin.js"></script>
    <script src="js/dashboard-charts.js"></script>
    <script src="js/notifications.js"></script>
</body>

</html>