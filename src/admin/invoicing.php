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
    <title>Invoicing - Aperture Admin</title>

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
</head>

<body class="admin-dashboard">
    <?php include_once 'components/sidebar.php'; ?>

    <div class="page-wrapper" id="page-wrapper">
        <?php include_once 'components/header.php'; ?>

        <main class="main-content">
            <div class="container-fluid p-0">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="header-title m-0">Invoicing</h1>
                    <button class="btn btn-gold" onclick="showCreateInvoiceModal()">+ Create New Invoice</button>
                </div>

                <!-- Invoices Table -->
                <div class="glass-panel p-4">
                    <div class="table-responsive">
                        <table class="table table-luxury align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3">Invoice #</th>
                                    <th>Client</th>
                                    <th>Event Type</th>
                                    <th>Issue Date</th>
                                    <th>Due Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="invoicesTableBody">
                                <tr><td colspan="8" class="text-center text-muted">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Create Invoice Modal -->
    <div class="modal fade" id="createInvoiceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-gold">Create Invoice</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-light">Select Booking</label>
                        <select id="bookingSelect" class="form-select bg-dark text-light border-secondary">
                            <option value="">Loading bookings...</option>
                        </select>
                        <small class="text-muted">Only confirmed bookings without invoices are shown.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light">Due Date</label>
                        <input type="date" id="dueDate" class="form-control bg-dark text-light border-secondary">
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-gold" onclick="createInvoice()">Create</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../libs/sweetalert2/sweetalert2.all.min.js"></script>
    <script src="admin.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', loadInvoices);

        function loadInvoices() {
            fetch('api/invoicing_api.php?action=get_all')
                .then(r => r.json())
                .then(data => {
                    const tbody = document.getElementById('invoicesTableBody');
                    if (data.success && data.invoices.length > 0) {
                        tbody.innerHTML = data.invoices.map(inv => `
                            <tr>
                                <td class="ps-3 text-gold">INV-${String(inv.invoiceID).padStart(5, '0')}</td>
                                <td>${inv.FirstName} ${inv.LastName}</td>
                                <td>${inv.event_type}</td>
                                <td>${new Date(inv.issue_date).toLocaleDateString()}</td>
                                <td>${new Date(inv.due_date).toLocaleDateString()}</td>
                                <td>₱${Number(inv.total_amount).toLocaleString()}</td>
                                <td>
                                    <span class="status-badge status-${inv.status.toLowerCase()}">${inv.status.toUpperCase()}</span>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-ghost" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-dark">
                                            <li><a class="dropdown-item" href="#" onclick="updateStatus(${inv.invoiceID}, 'paid')">Mark as Paid</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="updateStatus(${inv.invoiceID}, 'pending')">Mark as Pending</a></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="updateStatus(${inv.invoiceID}, 'cancelled')">Cancel Invoice</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        `).join('');
                    } else {
                        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No invoices found</td></tr>';
                    }
                });
        }

        function showCreateInvoiceModal() {
            // Load eligible bookings
            fetch('api/invoicing_api.php?action=get_bookings_without_invoice')
                .then(r => r.json())
                .then(data => {
                    const select = document.getElementById('bookingSelect');
                    if (data.success && data.bookings.length > 0) {
                        select.innerHTML = data.bookings.map(b => `
                            <option value="${b.bookingID}">
                                #${b.bookingID} - ${b.FirstName} ${b.LastName} (${b.event_type} on ${b.event_date})
                            </option>
                        `).join('');
                    } else {
                        select.innerHTML = '<option value="">No eligible bookings found</option>';
                    }
                    new bootstrap.Modal(document.getElementById('createInvoiceModal')).show();
                });
        }

        function createInvoice() {
            const bookingId = document.getElementById('bookingSelect').value;
            const dueDate = document.getElementById('dueDate').value;

            if (!bookingId || !dueDate) {
                Swal.fire('Error', 'Please select a booking and due date', 'error');
                return;
            }

            fetch('api/invoicing_api.php?action=create_invoice', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({bookingId, dueDate})
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('createInvoiceModal')).hide();
                    loadInvoices();
                    Swal.fire('Success', 'Invoice created successfully', 'success');
                }
            });
        }

        function updateStatus(id, status) {
            fetch('api/invoicing_api.php?action=update_status', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({id, status})
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    loadInvoices();
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    Toast.fire({
                        icon: 'success',
                        title: 'Status updated'
                    });
                }
            });
        }
    </script>
</body>

</html>