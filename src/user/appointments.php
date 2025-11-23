<?php
require_once '../includes/functions/config.php';
require_once '../includes/functions/session.php';
require_once '../includes/functions/auth.php';

// Check if user is logged in
if (!isset($_SESSION["userId"])) {
    header("Location: ../logIn.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - Aperture Studios</title>
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../luxuryDesignSystem.css">
    <link rel="stylesheet" href="user.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" href="../assets/camera.png" type="image/x-icon">
    
</head>
<body class="admin-dashboard">
    <?php include_once 'components/sidebar.php'; ?>
    
    <div class="page-wrapper" id="page-wrapper">
        <?php include_once 'components/header.php'; ?>
        
        <main class="main-content">
            <div class="container-fluid px-3 px-lg-5 py-5">
                
                <!-- Page Header -->
                <div class="mb-5">
                    <h1 class="mb-2">My Appointments</h1>
                    <p class="text-muted">View and manage all your photography bookings</p>
                </div>
                
                <!-- Filter Buttons -->
                <div class="filter-buttons">
                    <button class="filter-btn active" data-status="all">
                        <i class="bi bi-grid-fill me-2"></i>All Appointments
                    </button>
                    <button class="filter-btn" data-status="pending_consultation">
                        <i class="bi bi-clock-fill me-2"></i>Pending
                    </button>
                    <button class="filter-btn" data-status="confirmed">
                        <i class="bi bi-check-circle-fill me-2"></i>Confirmed
                    </button>
                    <button class="filter-btn" data-status="post_production">
                        <i class="bi bi-camera-reels-fill me-2"></i>Post Production
                    </button>
                    <button class="filter-btn" data-status="completed">
                        <i class="bi bi-star-fill me-2"></i>Completed
                    </button>
                    <button class="filter-btn" data-status="cancelled">
                        <i class="bi bi-x-circle-fill me-2"></i>Cancelled
                    </button>
                </div>
                
                <!-- Loading State -->
                <div id="loadingState" class="loading-state">
                    <div class="loading-spinner"></div>
                    <p class="text-muted">Loading your appointments...</p>
                </div>
                
                <!-- Appointments Grid -->
                <div id="appointmentsGrid" class="appointments-grid" style="display: none;"></div>
                
                <!-- Empty State -->
                <div id="emptyState" class="empty-state" style="display: none;">
                    <i class="bi bi-calendar-x"></i>
                    <h3 class="text-gold">No Appointments Found</h3>
                    <p class="text-muted">You don't have any appointments matching the selected filter.</p>
                    <a href="bookingForm.php" class="btn btn-gold mt-3">
                        <i class="bi bi-plus-circle me-2"></i>Create New Booking
                    </a>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Appointment Details Modal -->
    <div id="appointmentModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-gold font-serif mb-0">Appointment Details</h3>
                <button class="modal-close" onclick="closeModal()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Dynamic content will be inserted here -->
            </div>
        </div>
    </div>
    
    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="user.js"></script>
    <script src="appointmentsHandler.js"></script>
</body>
</html>
