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
    <title>My Appointments - Aperture</title>

    <!-- Local Bootstrap CSS -->
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/font/bootstrap-icons.css">
    <!-- Custom Admin CSS -->
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

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="header-title m-0">My Appointments</h1>
                    <a href="../booking.php" class="btn btn-gold">+ New Booking</a>
                </div>

                <!-- Filters -->
                <div class="card-solid mb-4" style="background-color: var(--card-bg); border: 1px solid var(--border-color); border-radius: 12px;">
                    <div class="card-body d-flex flex-wrap gap-3 align-items-center">
                        <div class="flex-grow-1">
                            <input type="text" class="form-control bg-dark border-secondary text-light" placeholder="Search by event type...">
                        </div>
                        <div>
                            <select class="form-select bg-dark border-secondary text-light">
                                <option selected>All Statuses</option>
                                <option value="1">Upcoming</option>
                                <option value="2">Completed</option>
                                <option value="3">Canceled</option>
                            </select>
                        </div>
                        <button class="btn btn-outline-secondary">Filter</button>
                    </div>
                </div>

                <!-- Appointments Table -->
                <div class="card-solid" style="background-color: var(--card-bg); border: 1px solid var(--border-color); border-radius: 12px;">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3">Event Type</th>
                                    <th>Date</th>
                                    <th>Package</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $appointments = [
                                    ['event' => 'Wedding Photography', 'date' => 'Nov 18, 2025', 'package' => 'Premium', 'status' => 'Upcoming', 'status_class' => 'status-confirmed'],
                                    ['event' => 'Corporate Headshots', 'date' => 'Oct 22, 2025', 'package' => 'Essential', 'status' => 'Completed', 'status_class' => 'status-completed'],
                                    ['event' => 'Birthday Celebration', 'date' => 'Sep 05, 2025', 'package' => 'Essential', 'status' => 'Completed', 'status_class' => 'status-completed'],
                                    ['event' => 'Product Launch Video', 'date' => 'Jul 15, 2025', 'package' => 'Elite', 'status' => 'Canceled', 'status_class' => 'status-canceled'],
                                ];
                                foreach ($appointments as $appt) :
                                ?>
                                    <tr>
                                        <td class="client-name ps-3"><?= $appt['event'] ?></td>
                                        <td><?= $appt['date'] ?></td>
                                        <td><?= $appt['package'] ?></td>
                                        <td><span class="status-badge <?= $appt['status_class'] ?>"><?= $appt['status'] ?></span></td>
                                        <td class="text-end pe-3">
                                            <a href="#" class="btn btn-sm btn-outline-secondary">View Details</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 d-flex justify-content-between align-items-center">
                        <span class="text-secondary small">Showing 1-4 of 4 appointments</span>
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">Next</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="user.js"></script>
</body>

</html>