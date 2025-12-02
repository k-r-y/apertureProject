
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
    <title>Financial Overview - Aperture Admin</title>

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
            <div class="container-fluid px-3 px-lg-5 py-4">
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="header-title m-0">Financial Overview</h1>
                        <p class="text-muted mb-0">Track all invoices and payments</p>
                    </div>
                    <div class="d-flex gap-2">
                        <select id="filterType" class="form-select form-select-sm neo-input" style="width: auto;">
                            <option value="all">All Transactions</option>
                            <option value="Downpayment">Downpayments</option>
                            <option value="Final Payment">Final Payments</option>
                            <option value="Refund">Refunds</option>
                        </select>
                        <button class="btn btn-sm btn-gold" onclick="loadTransactions()">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </button>
                    </div>
                </div>

                <!-- Transactions Table -->
                <div class="neo-card">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3">Type</th>
                                    <th>Ref #</th>
                                    <th>Client</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Proof</th>
                                    <th class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="transactionsTableBody">
                                <tr><td colspan="8" class="text-center text-muted">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Booking Details Modal -->
    <div class="modal fade" id="invoiceDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark border border-secondary" style="background-color: #1a1a1a !important;">
                <div class="modal-header border-bottom border-secondary">
                    <h5 class="modal-title text-gold font-serif">Transaction Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h6 class="text-light mb-1" id="modalClientName"></h6>
                            <small class="text-muted text-gold font-monospace" id="modalRef"></small>
                        </div>
                        <div class="text-end">
                            <h4 class="text-gold mb-0" id="modalAmount"></h4>
                            <span class="badge bg-secondary" id="modalType"></span>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="text-muted small text-uppercase">Date</label>
                            <p class="text-light mb-0" id="modalDate"></p>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small text-uppercase">Status</label>
                            <p class="text-light mb-0" id="modalStatus"></p>
                        </div>
                    </div>

                    <div class="border-top border-secondary pt-3 mt-3">
                        <label class="text-muted small text-uppercase mb-2">Proof of Payment</label>
                        <div id="modalProofContainer">
                            <!-- Proof link or message will be injected here -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary">
                    <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">Close</button>
                    <a href="#" id="modalViewBookingBtn" class="btn btn-gold btn-sm">View Full Booking</a>
                </div>
            </div>
        </div>
    </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../libs/sweetalert2/sweetalert2.all.min.js"></script>
    <script src="admin.js"></script>
    <script>
        // Load transactions on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadTransactions();
            
            // Filter change event
            document.getElementById('filterType').addEventListener('change', loadTransactions);
        });

        let allTransactions = [];

        function loadTransactions() {
            const tbody = document.getElementById('transactionsTableBody');
            const filterType = document.getElementById('filterType').value;
            
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Loading...</td></tr>';
            
            fetch(`api/invoicing_api.php?action=get_all`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.transactions.length > 0) {
                        allTransactions = data.transactions; // Store for modal access
                        
                        // Client-side filtering
                        const filteredTransactions = filterType === 'all' 
                            ? data.transactions 
                            : data.transactions.filter(t => t.type === filterType);

                        if (filteredTransactions.length > 0) {
                            tbody.innerHTML = filteredTransactions.map((t, index) => {
                                let typeClass = 'bg-dark border-secondary text-light';
                                if (t.type === 'Downpayment') typeClass = 'bg-primary bg-opacity-10 text-primary border-primary';
                                if (t.type === 'Final Payment') typeClass = 'bg-success bg-opacity-10 text-success border-success';
                                if (t.type === 'Refund') typeClass = 'bg-danger bg-opacity-10 text-danger border-danger';

                                let statusClass = 'status-pending';
                                if (t.status === 'Paid' || t.status === 'Completed') statusClass = 'status-confirmed';
                                if (t.status === 'Cancelled' || t.status === 'Refunded') statusClass = 'status-cancelled';

                                return `
                                <tr>
                                    <td class="ps-3"><span class="badge ${typeClass}">${t.type}</span></td>
                                    <td class="text-gold font-monospace">#${t.booking_id}</td>
                                    <td>${t.client_name}</td>
                                    <td>${new Date(t.date).toLocaleDateString()}</td>
                                    <td>₱${Number(t.amount).toLocaleString()}</td>
                                    <td>
                                        <span class="status-badge ${statusClass}">${t.status.toUpperCase()}</span>
                                    </td>
                                    <td>
                                        ${t.proof ? `<a href="${t.proof}" target="_blank" class="btn btn-sm btn-outline-light"><i class="bi bi-image"></i> View</a>` : '<span class="text-muted">-</span>'}
                                    </td>
                                    <td class="text-end pe-3">
                                        <button class="btn btn-sm btn-ghost" onclick="viewTransactionDetails(${t.booking_id}, '${t.type}', '${t.amount}')">
                                            <i class="bi bi-eye"></i> Details
                                        </button>
                                    </td>
                                </tr>
                            `}).join('');
                        } else {
                            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No transactions found</td></tr>';
                        }
                    } else {
                        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No transactions found</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Error loading transactions:', error);
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error loading transactions</td></tr>';
                });
        }
        
        function viewTransactionDetails(bookingId, type, amount) {
            // Find the transaction in the stored data
            // Since we might have multiple transactions for the same booking (downpayment, final), we need to be careful.
            // But for simplicity in this view, we can filter by bookingId and type/amount or just show the first match.
            // Better yet, pass the index or unique ID if available. Since we don't have a unique transaction ID in the frontend table loop easily without re-indexing, 
            // we will search for a match.
            
            const transaction = allTransactions.find(t => t.booking_id == bookingId && t.type == type && t.amount == amount);
            
            if (transaction) {
                document.getElementById('modalClientName').textContent = transaction.client_name;
                document.getElementById('modalRef').textContent = '#' + transaction.booking_id;
                document.getElementById('modalAmount').textContent = '₱' + Number(transaction.amount).toLocaleString();
                document.getElementById('modalType').textContent = transaction.type;
                document.getElementById('modalDate').textContent = new Date(transaction.date).toLocaleDateString();
                document.getElementById('modalStatus').textContent = transaction.status;
                
                const proofContainer = document.getElementById('modalProofContainer');
                if (transaction.proof) {
                    proofContainer.innerHTML = `<a href="${transaction.proof}" target="_blank" class="btn btn-outline-gold btn-sm"><i class="bi bi-file-earmark-image me-2"></i>View Proof Document</a>`;
                } else {
                    proofContainer.innerHTML = '<span class="text-muted fst-italic">No proof of payment uploaded.</span>';
                }
                
                document.getElementById('modalViewBookingBtn').href = `bookings.php?id=${transaction.booking_id}`;
                
                const modal = new bootstrap.Modal(document.getElementById('invoiceDetailsModal'));
                modal.show();
            }
        }
    </script>
</body>
</html>
```