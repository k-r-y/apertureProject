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
    <title>Settings - Aperture Admin</title>

    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../luxuryDesignSystem.css">
    <link rel="stylesheet" href="../css/modal.css">
    <link rel="stylesheet" href="../css/sidebar.css">
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
            <div class="container-fluid p-0">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="header-title m-0">System Settings</h1>
                </div>

                <div class="row g-4">
                    <!-- General Settings -->
                    <div class="col-lg-6">
                        <div class="neo-card h-100">
                            <div class="neo-card-header">
                                <i class="bi bi-gear me-2"></i>
                                <span>General Settings</span>
                            </div>
                            <div class="neo-card-body">
                                <form id="generalSettingsForm">
                                    <div class="mb-3">
                                        <label for="site_name" class="luxury-label">Site Name</label>
                                        <input type="text" class="neo-input" id="site_name" name="site_name">
                                    </div>
                                    <div class="mb-3">
                                        <label for="admin_email" class="luxury-label">Admin Email</label>
                                        <input type="email" class="neo-input" id="admin_email" name="admin_email">
                                    </div>
                                    <div class="mb-3">
                                        <label for="contact_phone" class="luxury-label">Public Contact Phone</label>
                                        <input type="text" class="neo-input" id="contact_phone" name="contact_phone">
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" role="switch" id="maintenance_mode" name="maintenance_mode">
                                        <label class="form-check-label" for="maintenance_mode">Enable Maintenance Mode</label>
                                    </div>
                                    <button type="submit" class="btn btn-gold w-100 mt-2">Save Changes</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Security Settings -->
                    <div class="col-lg-6">
                        <div class="neo-card h-100">
                            <div class="neo-card-header">
                                <i class="bi bi-shield-lock me-2"></i>
                                <span>Security</span>
                            </div>
                            <div class="neo-card-body">
                                <form id="securitySettingsForm">
                                    <div class="mb-3">
                                        <label for="adminPassword" class="luxury-label">New Password</label>
                                        <input type="password" class="neo-input" id="adminPassword" placeholder="Enter new password">
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirmAdminPassword" class="luxury-label">Confirm New Password</label>
                                        <input type="password" class="neo-input" id="confirmAdminPassword" placeholder="Confirm new password">
                                    </div>
                                    <button type="submit" class="btn btn-outline-gold w-100 mt-2">Update Password</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>


    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../libs/sweetalert2/sweetalert2.all.min.js"></script>
    <script src="admin.js"></script>
    <script src="js/settings.js"></script>
</body>

</html>
