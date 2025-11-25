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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    
    <style>
        .stat-trend {
            font-size: 0.875rem;
            font-weight: 600;
        }
        .trend-up {
            color: #4CAF50;
        }
        .trend-down {
            color: #F44336;
        }
        .activity-item {
            display: flex;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .activity-item:last-child {
            border-bottom: none;
        }
        .activity-icon {
            font-size: 1.5rem;
            color: #D4AF37;
        }
        .upcoming-event-item {
            display: flex;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .event-date {
            background: rgba(212,175,55,0.1);
            border-radius: 8px;
            padding: 0.5rem;
            text-align: center;
            min-width: 60px;
        }
        .event-day {
            font-size: 1.5rem;
            font-weight: 700;
            color: #D4AF37;
        }
        .event-month {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #999;
        }
    </style>
</head>

<body class="admin-dashboard">
    <?php include_once 'components/sidebar.php'; ?>

    <div class="page-wrapper" id="page-wrapper">
        <?php include_once 'components/header.php'; ?>

        <main class="main-content">
            <div class="container-fluid">
                <!-- Header -->
                <div class="mb-5">
                    <h1 class="header-title m-0">Analytics Dashboard</h1>
                    <p class="text-light" style="opacity: 0.7;">Welcome back, <?= htmlspecialchars($_SESSION['fullName']); ?>!</p>
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
                        <div class="neo-card h-100 ">
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
                        <div class="neo-card h-100 h-100">
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
            </div>
        </main>
    </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="admin.js"></script>
    <script src="js/dashboard.js"></script>
</body>

</html>