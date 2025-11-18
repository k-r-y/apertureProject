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
</head>

<body class="admin-dashboard">
    <?php include_once 'components/sidebar.php'; ?>

    <div class="page-wrapper" id="page-wrapper">
        <?php include_once 'components/header.php'; ?>

        <main class="main-content">
            <div class="container-fluid p-0">

                <div class="mb-4">
                    <h1 class="header-title m-0">Welcome Back, <?= htmlspecialchars($_SESSION['firstName'] ?? 'Client') ?>!</h1>
                    <p class="text-secondary">Here's a summary of your account and upcoming events.</p>
                </div>

                <!-- KPI Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="admin-card card-solid kpi-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="kpi-title">Total Bookings</div>
                                    <div class="kpi-value">4</div>
                                </div>
                                <div class="kpi-icon"><i class="bi bi-journal-check"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="admin-card card-solid kpi-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="kpi-title">Total Spent</div>
                                    <div class="kpi-value">₱55,700</div>
                                </div>
                                <div class="kpi-icon"><i class="bi bi-wallet2"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="row g-4">
                    <!-- Upcoming Appointment -->
                    <div class="col-lg-7">
                        <div class="card-solid admin-card d-flex flex-column">
                            <h5 class="card-header-title">Upcoming Appointment</h5>
                            <div class="mt-4 flex-grow-1 d-flex flex-column justify-content-center" style="background: var(--admin-bg); border-radius: 8px; padding: 2rem;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="text-light mb-0">Wedding Photography</h4>
                                    <span class="status-badge status-confirmed">Confirmed</span>
                                </div>
                                <div class="row g-3 text-secondary">
                                    <div class="col-md-6 d-flex align-items-center">
                                        <i class="bi bi-calendar-event me-3 fs-4 text-gold"></i>
                                        <div>
                                            <div class="small text-uppercase">Date</div>
                                            <div class="text-light">November 18, 2025</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 d-flex align-items-center">
                                        <i class="bi bi-clock me-3 fs-4 text-gold"></i>
                                        <div>
                                            <div class="small text-uppercase">Time</div>
                                            <div class="text-light">2:00 PM - 6:00 PM</div>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex align-items-center">
                                        <i class="bi bi-geo-alt me-3 fs-4 text-gold"></i>
                                        <div>
                                            <div class="small text-uppercase">Location</div>
                                            <div class="text-light">The Grand Ballroom, Makati City</div>
                                        </div>
                                    </div>
                                </div>
                                <a href="appointments.php" class="btn btn-outline-secondary mt-4 align-self-start">View Details</a>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="col-lg-5">
                        <div class="card-solid admin-card">
                            <h5 class="card-header-title">Recent Activity</h5>
                            <div class="activity-feed mt-3">
                                <div class="feed-item">
                                    <div class="feed-icon"><i class="bi bi-check-circle"></i></div>
                                    <div class="feed-content">
                                        <p class="feed-text mb-1">Your booking for <strong class="text-gold">Wedding Photography</strong> was confirmed.</p>
                                        <p class="feed-time">2 days ago</p>
                                    </div>
                                </div>
                                <div class="feed-item">
                                    <div class="feed-icon"><i class="bi bi-credit-card"></i></div>
                                    <div class="feed-content">
                                        <p class="feed-text mb-1">Payment of ₱3,000 for <strong class="text-gold">Premium Package</strong> was successful.</p>
                                        <p class="feed-time">2 days ago</p>
                                    </div>
                                </div>
                                <div class="feed-item">
                                    <div class="feed-icon"><i class="bi bi-pencil-square"></i></div>
                                    <div class="feed-content">
                                        <p class="feed-text mb-1">You updated your profile information.</p>
                                        <p class="feed-time">1 week ago</p>
                                    </div>
                                </div>
                                <div class="feed-item">
                                    <div class="feed-icon"><i class="bi-patch-check"></i></div>
                                    <div class="feed-content">
                                        <p class="feed-text mb-1">Your booking for <strong class="text-gold">Corporate Headshots</strong> was completed.</p>
                                        <p class="feed-time">3 weeks ago</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="user.js"></script>
</body>

</html>