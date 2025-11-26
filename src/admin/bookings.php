<?php
require_once '../includes/functions/config.php';
require_once '../includes/functions/auth.php';
require_once '../includes/functions/function.php';
require_once '../includes/functions/session.php';

// Check admin access
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../logIn.php");
    exit;
}

$currentPage = 'bookings.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Aperture Admin</title>
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
</head>
<body class="admin-dashboard">
    <?php include_once 'components/sidebar.php'; ?>

    <div class="page-wrapper" id="page-wrapper">
        <?php include_once 'components/header.php'; ?>

        <main class="main-content">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <div>
                        <h1 class="header-title m-0">Manage Bookings</h1>
                        <p class="text-light" style="opacity: 0.7;">View and update client bookings</p>
                    </div>
                </div>

                <!-- Filters -->
                <div class="neo-card mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="text-muted small mb-2">Status</label>
                            <select id="statusFilter" class="neo-input form-select">
                                <option value="all" selected>All Statuses</option>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="post_production">Post Production</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">  
                            <label class="text-muted small mb-2">Search</label>
                            <input type="text" id="searchInput" class="neo-input form-control bg-transparent text-light border-secondary" placeholder="Client name or ID...">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button id="applyFilters" class="btn btn-gold w-100">Apply</button>
                        </div>
                    </div>
                </div>

                <!-- Bookings Table -->
                <div class="neo-card">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle mb-0" id="bookingsTable">
                            <thead>
                                <tr>
                                    <th class="text-gold text-uppercase small letter-spacing-1">ID</th>
                                    <th class="text-gold text-uppercase small letter-spacing-1">Client</th>
                                    <th class="text-gold text-uppercase small letter-spacing-1">Event</th>
                                    <th class="text-gold text-uppercase small letter-spacing-1">Date</th>
                                    <th class="text-gold text-uppercase small letter-spacing-1">Status</th>
                                    <th class="text-gold text-uppercase small letter-spacing-1">Amount</th>
                                    <th class="text-gold text-uppercase small letter-spacing-1 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="bookingsTableBody">
                                <!-- Populated by JS -->
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">Loading bookings...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4" id="paginationControls">
                        <!-- Populated by JS -->
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Booking Details Modal -->
    <div class="modal fade" id="bookingDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-dark border border-secondary">
                <div class="modal-header border-bottom border-secondary">
                    <h5 class="modal-title text-gold font-serif">Booking Details #<span id="modalBookingId"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-4">
                        <!-- Status & Actions -->
                        <div class="col-12">
                            <div class="neo-card p-3 bg-opacity-10 bg-white">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                    <div>
                                        <label class="text-muted small d-block mb-1">Current Status</label>
                                        <span id="modalStatusBadge" class="badge bg-warning text-dark">Pending</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Client & Event Info -->
                        <div class="col-md-6">
                            <h6 class="text-gold mb-3">Client Information</h6>
                            <p class="mb-1"><i class="bi bi-person me-2 text-muted"></i> <span id="modalClientName" class="text-light"></span></p>
                            <p class="mb-1"><i class="bi bi-envelope me-2 text-muted"></i> <span id="modalClientEmail" class="text-light"></span></p>
                            <p class="mb-1"><i class="bi bi-phone me-2 text-muted"></i> <span id="modalClientPhone" class="text-light"></span></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-gold mb-3">Event Details</h6>
                            <p class="mb-1"><i class="bi bi-calendar-event me-2 text-muted"></i> <span id="modalEventDate" class="text-light"></span></p>
                            <p class="mb-1"><i class="bi bi-clock me-2 text-muted"></i> <span id="modalEventTime" class="text-light"></span></p>
                            <p class="mb-1"><i class="bi bi-geo-alt me-2 text-muted"></i> <span id="modalEventLocation" class="text-light"></span></p>
                            <p class="mb-1"><i class="bi bi-camera me-2 text-muted"></i> <span id="modalEventType" class="text-light"></span></p>
                            
                            <!-- Consultation Info Container -->
                            <div id="modalConsultation"></div>

                            <!-- Meeting Link -->
                            <div class="mt-3">
                                <label class="text-muted small mb-1"><i class="bi bi-link-45deg me-1"></i>Meeting Link (Google Meet/Zoom)</label>
                                <div class="input-group">
                                    <input type="text" id="modalMeetingLink" class="form-control bg-dark text-light border-secondary" placeholder="https://meet.google.com/...">
                                    <button id="saveLinkBtn" class="btn btn-outline-gold">Save</button>
                                </div>
                            </div>
                        </div>

                        <!-- Package & Payment -->
                        <div class="col-12">
                            <div class="border-top border-secondary pt-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-gold mb-2">Package</h6>
                                        <p id="modalPackageName" class="text-light mb-1"></p>
                                        <div id="modalAddons" class="small text-muted"></div>
                                    </div>
                                    <div class="col-md-6 text-md-end">
                                        <h6 class="text-gold mb-2">Payment</h6>
                                        <p class="mb-1">Total: <span id="modalTotalAmount" class="text-light fw-bold"></span></p>
                                        <p class="mb-1">Downpayment: <span id="modalDownpayment" class="text-light"></span></p>
                                        <p class="mb-0">Balance: <span id="modalBalance" class="text-light"></span></p>
                                        
                                        <!-- Proof of Payment Container -->
                                        <div id="modalPaymentProof"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Admin Notes -->
                        <div class="col-12">
                            <h6 class="text-gold mb-2">Admin Notes</h6>
                            <textarea id="modalAdminNotes" class="form-control bg-dark text-light border-secondary mb-2" rows="3" placeholder="Add internal notes here..."></textarea>
                            <button id="saveNotesBtn" class="btn btn-sm btn-outline-gold">Save Notes</button>
                            <button id="downloadInvoiceBtn" class="btn btn-sm btn-gold ms-2"><i class="bi bi-file-pdf me-1"></i>Download Invoice</button>
                        </div>

                        <!-- Activity Log -->
                        <div class="col-12">
                            <h6 class="text-gold mb-2">Activity Log</h6>
                            <div id="modalActivityLog" class="bg-darker p-3 rounded border border-secondary" style="max-height: 200px; overflow-y: auto;">
                                <!-- Populated by JS -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/feedback.js"></script>
    <script src="js/booking-modal.js?v=<?= time() ?>"></script>
    <script src="js/bookings.js?v=<?= time() ?>"></script>
</body>
</html>
