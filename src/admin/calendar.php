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
    <title>Calendar - Aperture Admin</title>

    <link rel="stylesheet" href="../luxuryDesignSystem.css">
    <link rel="stylesheet" href="../css/modal.css">
 
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" href="../assets/camera.png" type="image/x-icon">
  

    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../luxuryDesignSystem.css?v=<?= time() ?>">

    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="admin.css?v=<?= time() ?>">

    <link rel="icon" href="../assets/camera.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Fix for modal width issue */
        .modal-dialog {
            max-width: 500px;
            margin: 1.75rem auto;
        }
        @media (min-width: 576px) {
            .modal-dialog {
                max-width: 500px;
            }
        }
        @media (min-width: 992px) {
            .modal-lg {
                max-width: 800px;
            }
        }
    </style>

    <!-- FullCalendar -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <style>
        .fc-event { cursor: pointer; }
        .fc-toolbar-title { color: #D4AF37 !important; font-family: 'Playfair Display', serif; }
        .fc-button-primary { background-color: #D4AF37 !important; border-color: #D4AF37 !important; color: #000 !important; }
        .fc-daygrid-day-number { color: #fff; text-decoration: none; }
        .fc-col-header-cell-cushion { color: #fff; text-decoration: none; }
    </style>
</head>

<body class="admin-dashboard">
    <?php include_once 'components/sidebar.php'; ?>

    <div class="page-wrapper" id="page-wrapper">
        <?php include_once 'components/header.php'; ?>

        <main class="main-content">
            <div class="container-fluid p-0">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="header-title m-0">Booking Calendar</h1>
                    <a href="bookings.php" class="btn btn-gold">Manage Bookings</a>
                </div>

                <div class="glass-panel p-4">
                    <div class="calendar-luxury" id="calendar"></div>
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
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-gold m-0">Event Details</h6>
                                <button id="editDetailsBtn" class="btn btn-sm btn-link text-gold p-0" style="text-decoration: none;">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </button>
                            </div>
                            
                            <!-- View Mode -->
                            <div id="viewModeDetails">
                                <p class="mb-1"><i class="bi bi-calendar-event me-2 text-muted"></i> <span id="modalEventDate" class="text-light"></span></p>
                                <p class="mb-1"><i class="bi bi-clock me-2 text-muted"></i> <span id="modalEventTime" class="text-light"></span></p>
                                <p class="mb-1"><i class="bi bi-geo-alt me-2 text-muted"></i> <span id="modalEventLocation" class="text-light"></span></p>
                                <p class="mb-1"><i class="bi bi-camera me-2 text-muted"></i> <span id="modalEventType" class="text-light"></span></p>
                            </div>

                            <!-- Edit Mode (Hidden by default) -->
                            <div id="editModeDetails" style="display: none;">
                                <div class="mb-2">
                                    <label class="text-muted small">Date</label>
                                    <input type="date" id="editEventDate" class="form-control form-control-sm bg-dark text-light border-secondary">
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <label class="text-muted small">Start Time</label>
                                        <input type="time" id="editEventStartTime" class="form-control form-control-sm bg-dark text-light border-secondary">
                                    </div>
                                    <div class="col-6">
                                        <label class="text-muted small">End Time</label>
                                        <input type="time" id="editEventEndTime" class="form-control form-control-sm bg-dark text-light border-secondary">
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label class="text-muted small">Location</label>
                                    <input type="text" id="editEventLocation" class="form-control form-control-sm bg-dark text-light border-secondary">
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted small">Event Type</label>
                                    <select id="editEventType" class="form-select form-select-sm bg-dark text-light border-secondary">
                                        <option value="Wedding">Wedding</option>
                                        <option value="Debut">Debut</option>
                                        <option value="Christening">Christening</option>
                                        <option value="Corporate">Corporate</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="d-flex gap-2">
                                    <button id="saveDetailsBtn" class="btn btn-sm btn-gold flex-grow-1">Save Changes</button>
                                    <button id="cancelEditBtn" class="btn btn-sm btn-outline-light">Cancel</button>
                                </div>
                            </div>
                            
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
                                        <h6 class="text-gold mb-2">Payment Status</h6>
                                        <div class="mb-3 d-flex flex-column align-items-end gap-2">
                                            <div class="form-check form-switch">
                                                <label class="form-check-label text-light small me-2" for="confirmDownpayment">Downpayment Paid</label>
                                                <input class="form-check-input" type="checkbox" id="confirmDownpayment">
                                            </div>
                                            <div class="form-check form-switch">
                                                <label class="form-check-label text-light small me-2" for="confirmFinalPayment">Final Payment Paid</label>
                                                <input class="form-check-input" type="checkbox" id="confirmFinalPayment">
                                            </div>
                                        </div>
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
    <script src="js/notifications.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/booking-modal.js?v=<?= time() ?>"></script>
    <script src="admin.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                themeSystem: 'bootstrap5',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: 'get_bookings.php',
                eventClick: function(info) {
                    const event = info.event;
                    const props = event.extendedProps;
                    
                    Swal.fire({
                        title: event.title,
                        html: `
                            <div class="text-start">
                                <p><strong>Date:</strong> ${event.start.toLocaleString()}</p>
                                <p><strong>Status:</strong> <span class="badge bg-secondary">${props.status}</span></p>
                                <p><strong>Amount:</strong> ₱${Number(props.amount).toLocaleString()}</p>
                            </div>
                        `,
                        icon: 'info',
                        background: '#1a1a1a',
                        color: '#fff',
                        confirmButtonColor: '#D4AF37',
                        confirmButtonText: 'View Booking Details',
                        showCancelButton: true,
                        cancelButtonColor: '#6c757d'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            viewBooking(event.id);
                        }
                    });
                }
            });
            calendar.render();
        });
    </script>
</body>

</html>
