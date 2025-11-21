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
                    <h1 class="header-title m-0">System Settings</h1>
                </div>

                <div class="row g-4">
                    <!-- General Settings -->
                    <div class="col-lg-6 ">
                        <div class="card-solid">
                            <div class="card-body p-4">
                                <h5 class="card-header-title mb-4">General</h5>
                                <form>
                                    <div class="mb-3">
                                        <label for="siteName" class="form-label text-secondary small">Site Name</label>
                                        <input type="text" class="form-control bg-dark border-secondary text-light" id="siteName" value="Aperture">
                                    </div>
                                    <div class="mb-3">
                                        <label for="adminEmail" class="form-label text-secondary small">Admin Email</label>
                                        <input type="email" class="form-control bg-dark border-secondary text-light" id="adminEmail" value="admin@aperture.com">
                                    </div>
                                    <div class="mb-3">
                                        <label for="contactPhone" class="form-label text-secondary small">Public Contact Phone</label>
                                        <input type="text" class="form-control bg-dark border-secondary text-light" id="contactPhone" value="+63 912 345 6789">
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" role="switch" id="maintenanceMode">
                                        <label class="form-check-label" for="maintenanceMode">Enable Maintenance Mode</label>
                                    </div>
                                    <button type="submit" class="btn btn-gold mt-3">Save General Settings</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Theme & Appearance -->
                    <div class="col-lg-6">
                        <div class="card-solid">
                            <div class="card-body p-4">
                                <h5 class="card-header-title mb-4">Appearance</h5>
                                <form>
                                    <div class="mb-3">
                                        <label for="primaryColor" class="form-label text-secondary small">Primary Accent Color</label>
                                        <div class="d-flex align-items-center">
                                            <input type="color" class="form-control form-control-color bg-dark border-secondary" id="primaryColor" value="#D4AF37" title="Choose your color">
                                            <input type="text" class="form-control bg-dark border-secondary text-light ms-2" value="#D4AF37">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-secondary small">Logo Upload</label>
                                        <div class="input-group">
                                            <input type="file" class="form-control bg-dark border-secondary text-light" id="logoUpload">
                                            <button class="btn btn-outline-secondary" type="button">Upload</button>
                                        </div>
                                        <div class="mt-3">
                                            <img src="../assets/logo.png" alt="Current Logo" style="height: 40px; background: #333; padding: 5px; border-radius: 4px;">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-secondary small">Favicon Upload</label>
                                        <div class="input-group">
                                            <input type="file" class="form-control bg-dark border-secondary text-light" id="faviconUpload">
                                            <button class="btn btn-outline-secondary" type="button">Upload</button>
                                        </div>
                                        <div class="mt-3">
                                            <img src="../assets/camera.png" alt="Current Favicon" style="height: 32px; width: 32px; background: #333; padding: 5px; border-radius: 4px;">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-gold mt-3">Save Appearance</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Security Settings -->
                    <div class="col-lg-6">
                        <div class="card-solid">
                            <div class="card-body p-4">
                                <h5 class="card-header-title mb-4">Security</h5>
                                <form>
                                    <div class="mb-3">
                                        <label for="adminPassword" class="form-label text-secondary small">Change Admin Password</label>
                                        <input type="password" class="form-control bg-dark border-secondary text-light" id="adminPassword" placeholder="New Password">
                                    </div>
                                    <div class="mb-3">
                                        <input type="password" class="form-control bg-dark border-secondary text-light" id="confirmAdminPassword" placeholder="Confirm New Password">
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" role="switch" id="twoFactorAuth" checked>
                                        <label class="form-check-label" for="twoFactorAuth">Enable Two-Factor Authentication</label>
                                    </div>
                                    <button type="submit" class="btn btn-gold mt-3">Update Security</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- SMTP Settings -->
                    <div class="col-lg-6">
                        <div class="card-solid">
                            <div class="card-body p-4">
                                <h5 class="card-header-title mb-4">Email (SMTP) Settings</h5>
                                <form>
                                    <div class="mb-3">
                                        <label for="smtpHost" class="form-label text-secondary small">SMTP Host</label>
                                        <input type="text" class="form-control bg-dark border-secondary text-light" id="smtpHost" value="smtp.example.com">
                                    </div>
                                    <div class="mb-3">
                                        <label for="smtpUser" class="form-label text-secondary small">SMTP Username</label>
                                        <input type="text" class="form-control bg-dark border-secondary text-light" id="smtpUser" value="user@example.com">
                                    </div>
                                    <div class="mb-3">
                                        <label for="smtpPass" class="form-label text-secondary small">SMTP Password</label>
                                        <input type="password" class="form-control bg-dark border-secondary text-light" id="smtpPass" value="••••••••">
                                    </div>
                                    <button type="submit" class="btn btn-gold mt-3">Save SMTP Settings</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="admin.js"></script>
</body>

</html>
