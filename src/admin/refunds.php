<?php
require_once '../includes/functions/config.php';
require_once '../includes/functions/session.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../logIn.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refund Management - Aperture Admin</title>
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../luxuryDesignSystem.css">
    <link rel="stylesheet" href="../css/modal.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" href="../assets/camera.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="admin-dashboard">
    <?php include_once 'components/sidebar.php'; ?>
    
    <div class="page-wrapper" id="page-wrapper">
        <?php include_once 'components/header.php'; ?>

        <main class="main-content">
            <div class="container-fluid">
                <!-- Header -->
                <div class="mb-4">
                    <h1 class="header-title m-0">Refund Management</h1>
                    <p class="text-light mb-0" style="opacity: 0.7;">Process and track client refund requests</p>
                </div>

                <div class="neo-card">
                    <!-- Minimalist Tabs -->
                    <ul class="nav nav-tabs border-bottom-0 mb-4" id="refundTabs">
                        <li class="nav-item">
                            <button class="nav-link active text-gold bg-transparent border-0 ps-0" data-status="all">All Refunds</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link text-muted bg-transparent border-0" data-status="pending">Pending</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link text-muted bg-transparent border-0" data-status="approved">Approved</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link text-muted bg-transparent border-0" data-status="processed">Processed</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link text-muted bg-transparent border-0" data-status="rejected">Rejected</button>
                        </li>
                    </ul>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle" style="background: transparent;">
                            <thead>
                                <tr class="text-muted small text-uppercase" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                                    <th class="fw-normal ps-0">ID</th>
                                    <th class="fw-normal">Booking Ref</th>
                                    <th class="fw-normal">Client</th>
                                    <th class="fw-normal">Event</th>
                                    <th class="fw-normal">Amount</th>
                                    <th class="fw-normal">Requested</th>
                                    <th class="fw-normal">Status</th>
                                    <th class="fw-normal text-end pe-0">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="refundsTableBody" class="border-0">
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Refund Details Modal -->
    <div class="modal fade" id="refundModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark border border-secondary" style="background-color: #1a1a1a !important;">
                <div class="modal-header border-bottom border-secondary">
                    <h5 class="modal-title text-gold font-serif">Refund Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h6 class="text-light mb-1" id="modalClientName"></h6>
                            <small class="text-muted" id="modalBookingRef"></small>
                        </div>
                        <div class="text-end">
                            <h4 class="text-gold mb-0" id="modalAmount"></h4>
                            <small class="text-muted">Refund Amount</small>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="text-muted small text-uppercase">Event Type</label>
                            <p class="text-light mb-0" id="modalEventType"></p>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small text-uppercase">Event Date</label>
                            <p class="text-light mb-0" id="modalEventDate"></p>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small text-uppercase">Requested On</label>
                            <p class="text-light mb-0" id="modalRequested"></p>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small text-uppercase">Reason</label>
                            <p class="text-light mb-0 fst-italic" id="modalReason"></p>
                        </div>
                    </div>

                    <div class="border-top border-secondary pt-4">
                        <div class="mb-3">
                            <label class="text-gold small mb-2">Update Status</label>
                            <select class="form-select neo-input" id="modalStatus">
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="processed">Processed</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="text-gold small mb-2">Admin Notes</label>
                            <textarea class="form-control neo-input" id="modalNotes" rows="3" placeholder="Add internal notes..."></textarea>
                        </div>
                        <div class="mb-3" id="proofUploadContainer" style="display: none;">
                            <label class="text-gold small mb-2">Refund Proof (Receipt)</label>
                            <input type="file" class="form-control neo-input" id="refundProof" accept="image/*,application/pdf">
                            <small class="text-muted">Required when marking as Processed</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary">
                    <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-gold btn-sm" id="saveRefundBtn">Save Changes</button>
                </div>
            </div>
<?php
require_once '../includes/functions/config.php';
require_once '../includes/functions/session.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../logIn.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refund Management - Aperture Admin</title>
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../luxuryDesignSystem.css">
    <link rel="stylesheet" href="../css/modal.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" href="../assets/camera.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="admin-dashboard">
    <?php include_once 'components/sidebar.php'; ?>
    
    <div class="page-wrapper" id="page-wrapper">
        <?php include_once 'components/header.php'; ?>

        <main class="main-content">
            <div class="container-fluid">
                <!-- Header -->
                <div class="mb-4">
                    <h1 class="header-title m-0">Refund Management</h1>
                    <p class="text-light mb-0" style="opacity: 0.7;">Process and track client refund requests</p>
                </div>

                <div class="neo-card">
                    <!-- Minimalist Tabs -->
                    <ul class="nav nav-tabs border-bottom-0 mb-4" id="refundTabs">
                        <li class="nav-item">
                            <button class="nav-link active text-gold bg-transparent border-0 ps-0" data-status="all">All Refunds</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link text-muted bg-transparent border-0" data-status="pending">Pending</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link text-muted bg-transparent border-0" data-status="approved">Approved</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link text-muted bg-transparent border-0" data-status="processed">Processed</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link text-muted bg-transparent border-0" data-status="rejected">Rejected</button>
                        </li>
                    </ul>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle" style="background: transparent;">
                            <thead>
                                <tr class="text-muted small text-uppercase" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                                    <th class="fw-normal ps-0">ID</th>
                                    <th class="fw-normal">Booking Ref</th>
                                    <th class="fw-normal">Client</th>
                                    <th class="fw-normal">Event</th>
                                    <th class="fw-normal">Amount</th>
                                    <th class="fw-normal">Requested</th>
                                    <th class="fw-normal">Status</th>
                                    <th class="fw-normal text-end pe-0">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="refundsTableBody" class="border-0">
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Refund Details Modal -->
    <div class="modal fade" id="refundModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark border border-secondary" style="background-color: #1a1a1a !important;">
                <div class="modal-header border-bottom border-secondary">
                    <h5 class="modal-title text-gold font-serif">Refund Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h6 class="text-light mb-1" id="modalClientName"></h6>
                            <small class="text-muted" id="modalBookingRef"></small>
                        </div>
                        <div class="text-end">
                            <h4 class="text-gold mb-0" id="modalAmount"></h4>
                            <small class="text-muted">Refund Amount</small>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="text-muted small text-uppercase">Event Type</label>
                            <p class="text-light mb-0" id="modalEventType"></p>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small text-uppercase">Event Date</label>
                            <p class="text-light mb-0" id="modalEventDate"></p>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small text-uppercase">Requested On</label>
                            <p class="text-light mb-0" id="modalRequested"></p>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small text-uppercase">Reason</label>
                            <p class="text-light mb-0 fst-italic" id="modalReason"></p>
                        </div>
                    </div>

                    <div class="border-top border-secondary pt-4">
                        <div class="mb-3">
                            <label class="text-gold small mb-2">Update Status</label>
                            <select class="form-select neo-input" id="modalStatus">
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="processed">Processed</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="text-gold small mb-2">Admin Notes</label>
                            <textarea class="form-control neo-input" id="modalNotes" rows="3" placeholder="Add internal notes..."></textarea>
                        </div>
                        <div class="mb-3" id="proofUploadContainer" style="display: none;">
                            <label class="text-gold small mb-2">Refund Proof (Receipt)</label>
                            <input type="file" class="form-control neo-input" id="refundProof" accept="image/*,application/pdf">
                            <small class="text-muted">Required when marking as Processed</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary">
                    <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-gold btn-sm" id="saveRefundBtn">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../libs/sweetalert2/sweetalert2.all.min.js"></script>
    <script src="../js/feedback.js"></script>
    <script src="js/refunds.js?v=<?= time() ?>"></script>
    <script src="js/notifications.js"></script>
    <script src="admin.js"></script>
</body>
</html>
