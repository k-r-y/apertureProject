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
    <link rel="stylesheet" href="admin.css">
    <link rel="icon" href="../assets/camera.png" type="image/x-icon">
</head>
<body class="admin-dashboard">
    <?php include_once 'components/sidebar.php'; ?>
    
    <div class="page-wrapper" id="page-wrapper">
        <div class="page-content">
            <!-- Header -->
            <div class="mb-5">
                <h1 class="text-gold font-serif">Refund Management</h1>
                <p class="text-muted">View and process client refund requests</p>
            </div>

            <!-- Filter Tabs -->
            <div class="mb-4">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-gold active" data-status="all">All Refunds</button>
                    <button type="button" class="btn btn-outline-gold" data-status="pending">Pending</button>
                    <button type="button" class="btn btn-outline-gold" data-status="approved">Approved</button>
                    <button type="button" class="btn btn-outline-gold" data-status="processed">Processed</button>
                    <button type="button" class="btn btn-outline-gold" data-status="rejected">Rejected</button>
                </div>
            </div>

            <!-- RefundsTable -->
            <div class="card bg-darker border-secondary">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Booking Ref</th>
                                    <th>Client</th>
                                    <th>Event</th>
                                    <th>Amount</th>
                                    <th>Requested</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="refundsTableBody">
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Refund Details Modal -->
    <div class="modal fade" id="refundModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-dark border-gold">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-gold">Refund Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-gold small">Booking Reference</label>
                            <p class="text-light" id="modalBookingRef"></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-gold small">Client Name</label>
                            <p class="text-light" id="modalClientName"></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-gold small">Event Type</label>
                            <p class="text-light" id="modalEventType"></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-gold small">Event Date</label>
                            <p class="text-light" id="modalEventDate"></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-gold small">Refund Amount</label>
                            <p class="text-light text-gold fw-bold" id="modalAmount"></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-gold small">Requested On</label>
                            <p class="text-light" id="modalRequested"></p>
                        </div>
                        <div class="col-12">
                            <label class="text-gold small">Reason</label>
                            <p class="text-light" id="modalReason"></p>
                        </div>
                        <div class="col-12">
                            <label class="text-gold small">Update Status</label>
                            <select class="form-select bg-dark text-light border-secondary" id="modalStatus">
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="processed">Processed</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="text-gold small">Admin Notes</label>
                            <textarea class="form-control bg-dark text-light border-secondary" id="modalNotes" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-gold" id="saveRefundBtn">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/feedback.js"></script>
    <script src="js/refunds.js?v=<?= time() ?>"></script>
</body>
</html>
