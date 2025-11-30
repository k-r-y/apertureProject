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
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../luxuryDesignSystem.css">
    <link rel="stylesheet" href="user.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" href="../assets/camera.png" type="image/x-icon">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="../libs/sweetalert2/sweetalert2.min.css">
    
</head>
<body class="admin-dashboard">
    <?php include_once 'components/sidebar.php'; ?>
    
    <div class="page-wrapper" id="page-wrapper">
        <?php include_once 'components/header.php'; ?>
        
        <main class="main-content">
            <div class="container-fluid px-3 px-lg-5">
                    
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
                    <button class="filter-btn" data-status="pending">
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

    <!-- Review Modal -->
    <div id="reviewModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-gold font-serif mb-0">Write a Review</h3>
                <button class="modal-close" onclick="closeReviewModal()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="reviewForm">
                    <input type="hidden" id="reviewBookingId">
                    <div class="mb-3 text-center">
                        <label class="form-label text-light d-block">Rating</label>
                        <div class="rating-stars">
                            <i class="bi bi-star fs-3 text-muted" data-value="1"></i>
                            <i class="bi bi-star fs-3 text-muted" data-value="2"></i>
                            <i class="bi bi-star fs-3 text-muted" data-value="3"></i>
                            <i class="bi bi-star fs-3 text-muted" data-value="4"></i>
                            <i class="bi bi-star fs-3 text-muted" data-value="5"></i>
                        </div>
                        <input type="hidden" id="reviewRating" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light">Comment</label>
                        <textarea id="reviewComment" class="form-control bg-dark text-light border-secondary" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-gold w-100">Submit Review</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Pay Balance Modal -->
    <div id="payBalanceModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-gold font-serif mb-0">Pay Balance</h3>
                <button class="modal-close" onclick="closePayBalanceModal()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="payBalanceForm">
                    <input type="hidden" id="payBalanceBookingId">
                    <input type="hidden" id="paymentType" value="balance">
                    <div class="mb-4 text-center">
                        <p class="text-light mb-1">Remaining Balance</p>
                        <h2 class="text-gold" id="payBalanceAmount">₱0.00</h2>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light">Upload Proof of Payment</label>
                        <input type="file" id="balanceProof" class="form-control bg-dark text-light border-secondary" accept="image/*,application/pdf" required>
                        <small class="text-muted">Please upload a clear photo or screenshot of your payment receipt.</small>
                    </div>
                    <button type="submit" class="btn btn-gold w-100">Submit Payment Proof</button>
                </form>
            </div>
        </div>
        </div>
    </div>

    <!-- Edit Booking Modal -->
    <div id="editBookingModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-gold font-serif mb-0">Edit Booking</h3>
                <button class="modal-close" onclick="closeEditModal()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="editBookingForm">
                    <input type="hidden" id="editBookingId">
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label text-light">Event Date</label>
                            <input type="date" id="editEventDate" name="eventDate" class="form-control bg-dark text-light border-secondary">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-light">Start Time</label>
                            <select id="editStartTime" name="startTime" class="form-select bg-dark text-light border-secondary">
                                <!-- Options populated by JS -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-light">End Time</label>
                            <select id="editEndTime" name="endTime" class="form-select bg-dark text-light border-secondary">
                                <!-- Options populated by JS -->
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-light">Location</label>
                            <input type="text" id="editLocation" name="location" class="form-control bg-dark text-light border-secondary" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-light">Theme (Optional)</label>
                            <input type="text" id="editTheme" name="theme" class="form-control bg-dark text-light border-secondary">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-light">Special Requests</label>
                            <textarea id="editMessage" name="message" class="form-control bg-dark text-light border-secondary" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-gold w-100">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../libs/sweetalert2/sweetalert2.all.min.js"></script>
    <script src="user.js"></script>

    <script src="appointmentsHandler.js"></script>
    <script src="js/user_notifications.js"></script>
</body>
</html>
