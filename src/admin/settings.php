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
                        <div class="glass-card h-100">
                            <div class="glass-card-header">
                                <i class="bi bi-gear me-2"></i>
                                <span>General Settings</span>
                            </div>
                            <div class="glass-card-body">
                                <form>
                                    <div class="mb-3">
                                        <label for="siteName" class="luxury-label">Site Name</label>
                                        <input type="text" class="luxury-input" id="siteName" value="Aperture">
                                    </div>
                                    <div class="mb-3">
                                        <label for="adminEmail" class="luxury-label">Admin Email</label>
                                        <input type="email" class="luxury-input" id="adminEmail" value="admin@aperture.com">
                                    </div>
                                    <div class="mb-3">
                                        <label for="contactPhone" class="luxury-label">Public Contact Phone</label>
                                        <input type="text" class="luxury-input" id="contactPhone" value="+63 912 345 6789">
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" role="switch" id="maintenanceMode">
                                        <label class="form-check-label" for="maintenanceMode">Enable Maintenance Mode</label>
                                    </div>
                                    <button type="submit" class="btn btn-gold w-100 mt-2">Save Changes</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Theme & Appearance -->
                    <div class="col-lg-6">
                        <div class="glass-card h-100">
                            <div class="glass-card-header">
                                <i class="bi bi-palette me-2"></i>
                                <span>Appearance</span>
                            </div>
                            <div class="glass-card-body">
                                <form>
                                    <div class="mb-3">
                                        <label for="primaryColor" class="luxury-label">Primary Accent Color</label>
                                        <div class="d-flex align-items-center">
                                            <input type="color" class="form-control form-control-color" id="primaryColor" value="#D4AF37" title="Choose your color">
                                            <input type="text" class="luxury-input ms-2" value="#D4AF37">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="luxury-label">Logo Upload</label>
                                        <div class="input-group">
                                            <input type="file" class="form-control" id="logoUpload">
                                            <button class="btn btn-outline-gold" type="button">Upload</button>
                                        </div>
                                        <div class="mt-3">
                                            <img src="../assets/logo.png" alt="Current Logo" style="height: 40px; background: #333; padding: 5px; border-radius: 4px;">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="luxury-label">Favicon Upload</label>
                                        <div class="input-group">
                                            <input type="file" class="form-control" id="faviconUpload">
                                            <button class="btn btn-outline-gold" type="button">Upload</button>
                                        </div>
                                        <div class="mt-3">
                                            <img src="../assets/camera.png" alt="Current Favicon" style="height: 32px; width: 32px; background: #333; padding: 5px; border-radius: 4px;">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-gold w-100 mt-2">Save Changes</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Security Settings -->
                    <div class="col-lg-6">
                        <div class="glass-card h-100">
                            <div class="glass-card-header">
                                <i class="bi bi-shield-lock me-2"></i>
                                <span>Security</span>
                            </div>
                            <div class="glass-card-body">
                                <form>
                                    <div class="mb-3">
                                        <label for="adminPassword" class="luxury-label">New Password</label>
                                        <input type="password" class="luxury-input" id="adminPassword" placeholder="Enter new password">
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirmAdminPassword" class="luxury-label">Confirm New Password</label>
                                        <input type="password" class="luxury-input" id="confirmAdminPassword" placeholder="Confirm new password">
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" role="switch" id="twoFactorAuth" checked>
                                        <label class="form-check-label" for="twoFactorAuth">Enable Two-Factor Authentication</label>
                                    </div>
                                    <button type="submit" class="btn btn-outline-gold w-100 mt-2">Update Password</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- SMTP Settings -->
                    <div class="col-lg-6">
                        <div class="glass-card h-100">
                            <div class="glass-card-header">
                                <i class="bi bi-envelope me-2"></i>
                                <span>Email (SMTP) Settings</span>
                            </div>
                            <div class="glass-card-body">
                                <form>
                                    <div class="mb-3">
                                        <label for="smtpHost" class="luxury-label">SMTP Host</label>
                                        <input type="text" class="luxury-input" id="smtpHost" value="smtp.example.com">
                                    </div>
                                    <div class="mb-3">
                                        <label for="smtpUser" class="luxury-label">SMTP Username</label>
                                        <input type="text" class="luxury-input" id="smtpUser" value="user@example.com">
                                    </div>
                                    <div class="mb-3">
                                        <label for="smtpPass" class="luxury-label">SMTP Password</label>
                                        <input type="password" class="luxury-input" id="smtpPass" value="••••••••">
                                    </div>
                                    <button type="submit" class="btn btn-gold w-100 mt-2">Save Changes</button>
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
