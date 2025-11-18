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
    <title>My Profile - Aperture</title>

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

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="header-title m-0">My Profile</h1>
                </div>

                <div class="row g-4">
                    <!-- Personal Information -->
                    <div class="col-lg-6">
                        <div class="card-solid">
                            <div class="card-body p-4">
                                <h5 class="card-header-title mb-4">Personal Information</h5>
                                <form>
                                    <div class="row g-3">
                                        <div class="col-md-6 mb-3">
                                            <label for="firstName" class="form-label text-secondary small">First Name</label>
                                            <input type="text" class="form-control bg-dark border-secondary text-light" id="firstName" value="<?= htmlspecialchars($_SESSION['firstName'] ?? '') ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="lastName" class="form-label text-secondary small">Last Name</label>
                                            <input type="text" class="form-control bg-dark border-secondary text-light" id="lastName" value="<?= htmlspecialchars($_SESSION['lastName'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label text-secondary small">Email Address</label>
                                        <input type="email" class="form-control bg-dark border-secondary text-light" id="email" value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="contactPhone" class="form-label text-secondary small">Contact Phone</label>
                                        <input type="text" class="form-control bg-dark border-secondary text-light" id="contactPhone" value="<?= htmlspecialchars($_SESSION['contact'] ?? '') ?>">
                                    </div>
                                    <button type="submit" class="btn btn-gold mt-3">Save Changes</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="col-lg-6">
                        <div class="card-solid">
                            <div class="card-body p-4">
                                <h5 class="card-header-title mb-4">Change Password</h5>
                                <form>
                                    <div class="mb-3">
                                        <label for="currentPassword" class="form-label text-secondary small">Current Password</label>
                                        <input type="password" class="form-control bg-dark border-secondary text-light" id="currentPassword" placeholder="Enter your current password">
                                    </div>
                                    <div class="mb-3">
                                        <label for="newPassword" class="form-label text-secondary small">New Password</label>
                                        <input type="password" class="form-control bg-dark border-secondary text-light" id="newPassword" placeholder="Enter new password">
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirmPassword" class="form-label text-secondary small">Confirm New Password</label>
                                        <input type="password" class="form-control bg-dark border-secondary text-light" id="confirmPassword" placeholder="Confirm new password">
                                    </div>
                                    <button type="submit" class="btn btn-gold mt-3">Update Password</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Account Deletion -->
                    <div class="col-12">
                        <div class="card-solid" style="border-color: rgba(220, 53, 69, 0.3);">
                            <div class="card-body p-4 d-md-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-1" style="color: #dc3545;">Delete Account</h5>
                                    <p class="text-secondary small mb-md-0">Permanently delete your account and all associated data. This action cannot be undone.</p>
                                </div>
                                <button type="button" class="btn btn-outline-danger mt-3 mt-md-0">Request Account Deletion</button>
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