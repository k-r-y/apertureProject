<?php
require_once '../includes/functions/config.php';
require_once '../includes/functions/auth.php';
require_once '../includes/functions/function.php';
require_once '../includes/functions/csrf.php';
require_once '../includes/functions/session.php';
require_once '../includes/functions/booking_logic.php'; 

// Redirect non-users or unverified users
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'User' || !$_SESSION['isVerified']) {
    header("Location: ../logIn.php");
    exit;
}

$bookingCount = getBookingCount($_SESSION['userId']);      
$getUpcomingBookingsCount = getUpcomingBookingsCount($_SESSION['userId']);           
$totalSpent = getTotalSpent($_SESSION['userId']);

// Get bookings by package type for chart
$bookingsByType = getBookingsByPackageType($_SESSION['userId']);

// Prepare data for JavaScript
$packageNames = [];
$packageCounts = [];
foreach ($bookingsByType as $booking) {
    $packageNames[] = $booking['packageName'];
    $packageCounts[] = (int)$booking['count'];
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
    <!-- Sidebar CSS -->
    <link rel="stylesheet" href="../css/sidebar.css">
    <!-- Luxury Design System -->
    <link rel="stylesheet" href="../luxuryDesignSystem.css">
    <!-- Custom User CSS -->
    <link rel="stylesheet" href="user.css">
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="../style.css">
    <!-- Favicon -->
    <link rel="icon" href="../assets/camera.png" type="image/x-icon">

    <!-- Google Fonts -->
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
            <div class="container-fluid px-3 px-lg-5 py-0">

                <div class="mb-5">
                    <h1 class="mb-2">Welcome, <span class="text-gold"><?= htmlspecialchars($_SESSION['firstName'] ?? 'Client') ?></span></h1>
                    <p class="text-muted">Here's a summary of your account.</p>
                </div>

                <!-- KPI Cards -->
                <div class="row g-4 mb-5">
                    <div class="col-lg-4 col-md-6">
                        <div class="neo-card h-100 d-flex align-items-center p-4">
                            <div class="rounded-circle bg-gold bg-opacity-10 p-3 me-4">
                                <i class="bi bi-calendar-event fs-2 text-gold"></i>
                            </div>
                            <div>
                                <div class="h2 mb-1 text-light"><?= htmlspecialchars($getUpcomingBookingsCount) ?></div>
                                <div class="text-muted small text-uppercase letter-spacing-1">Upcoming Events</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="neo-card h-100 d-flex align-items-center p-4">
                            <div class="rounded-circle bg-gold bg-opacity-10 p-3 me-4">
                                <i class="bi bi-journal-check fs-2 text-gold"></i>
                            </div>
                            <div>
                                <div class="h2 mb-1 text-light"><?= htmlspecialchars($bookingCount) ?></div>
                                <div class="text-muted small text-uppercase letter-spacing-1">Total Bookings</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <div class="neo-card h-100 d-flex align-items-center p-4">
                            <div class="rounded-circle bg-gold bg-opacity-10 p-3 me-4">
                                <i class="bi bi-wallet2 fs-2 text-gold"></i>
                            </div>
                            <div>
                                <div class="h2 mb-1 text-light">₱<?= isset($totalSpent) ? number_format($totalSpent) : '0'; ?></div>
                                <div class="text-muted small text-uppercase letter-spacing-1">Total Spent</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="row g-4">
                    <!-- Bookings by Type Chart -->
                    <div class="col-12">
                        <div class="glass-panel p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="m-0"><i class="bi bi-bar-chart-fill me-2 text-gold"></i>Your Bookings by Type</h5>
                            </div>
                            <?php if (empty($bookingsByType)): ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-calendar-x fs-1 text-muted mb-3 d-block"></i>
                                    <p class="text-muted">No bookings yet. Book your first event to see statistics!</p>
                                    <a href="bookingForm.php" class="btn btn-gold mt-3">
                                        <i class="bi bi-plus-circle me-2"></i>Create Booking
                                    </a>
                                </div>
                            <?php else: ?>
                                <div id="userBookingsChart"></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="user.js"></script>
    <script src="js/notifications.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            <?php if (!empty($bookingsByType)): ?>
            var options = {
                series: [{
                    name: 'Bookings',
                    data: <?= json_encode($packageCounts) ?>
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: false
                    },
                    foreColor: '#888888',
                    fontFamily: 'Inter, sans-serif',
                    background: 'transparent'
                },
                plotOptions: {
                    bar: {
                        distributed: true,
                        horizontal: false,
                        borderRadius: 4,
                        columnWidth: '40%'
                    },
                },
                colors: ['#D4AF37', '#AA8C2C', '#F4CF57', '#C9A961', '#E6C86F'],
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: <?= json_encode($packageNames) ?>,
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                yaxis: {
                    title: {
                        text: 'Number of Bookings',
                        style: {
                            color: '#888888'
                        }
                    }
                },
                grid: {
                    borderColor: 'rgba(255, 255, 255, 0.05)',
                    strokeDashArray: 4,
                },
                legend: {
                    show: false
                },
                tooltip: {
                    theme: 'dark',
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Inter, sans-serif'
                    },
                    x: {
                        show: true
                    },
                    marker: {
                        show: true,
                    },
                }
            };

            var chart = new ApexCharts(document.querySelector("#userBookingsChart"), options);
            chart.render();
            <?php endif; ?>
        });
    </script>
</body>

</html>