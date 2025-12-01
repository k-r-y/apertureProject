<?php
require_once '../includes/functions/config.php';
require_once '../includes/functions/session.php';
require_once '../includes/functions/auth.php';

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
    <title>Billing History - Aperture Studios</title>
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
            <div class="container-fluid px-3 px-lg-5 py-4">
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="header-title m-0">Billing History</h1>
                        <p class="text-muted mb-0">Track your invoices, payments, and refunds</p>
                    </div>
                </div>
                
                <!-- Billing Table -->
                <div class="neo-card">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3">Type</th>
                                    <th>Ref #</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="billingTableBody">
                                <tr><td colspan="7" class="text-center py-5 text-muted">Loading history...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="user.js"></script>
    <script src="js/notifications.js"></script>
    <script src="js/user_notifications.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tbody = document.getElementById('billingTableBody');
            
            fetch('api/billing_api.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.transactions.length > 0) {
                        tbody.innerHTML = data.transactions.map(t => {
                            let typeClass = 'bg-dark border-secondary text-light';
                            if (t.type === 'Downpayment') typeClass = 'bg-primary bg-opacity-10 text-primary border-primary';
                            if (t.type === 'Balance Payment') typeClass = 'bg-success bg-opacity-10 text-success border-success';
                            if (t.type === 'Refund') typeClass = 'bg-danger bg-opacity-10 text-danger border-danger';
                            if (t.type === 'Invoice') typeClass = 'bg-warning bg-opacity-10 text-warning border-warning';

                            let statusClass = 'status-pending';
                            if (t.status === 'Paid' || t.status === 'Completed' || t.status === 'Processed') statusClass = 'status-confirmed';
                            if (t.status === 'Cancelled' || t.status === 'Refunded' || t.status === 'Rejected') statusClass = 'status-cancelled';

                            return `
                            <tr>
                                <td class="ps-3"><span class="badge ${typeClass}">${t.type}</span></td>
                                <td class="text-gold font-monospace">${t.ref_number}</td>
                                <td class="text-light">${t.description}</td>
                                <td class="text-muted small">${new Date(t.date).toLocaleDateString()}</td>
                                <td class="text-light fw-medium">₱${Number(t.amount).toLocaleString()}</td>
                                <td>
                                    <span class="status-badge ${statusClass}">${t.status.toUpperCase()}</span>
                                </td>
                                <td class="text-end pe-3">
                                    ${getActionButtons(t)}
                                </td>
                            </tr>
                        `}).join('');
                    } else {
                        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-5 text-muted">No transactions found</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center py-5 text-danger">Error loading history</td></tr>';
                });
        });

        function getActionButtons(t) {
            if (t.type === 'Invoice') {
                return `<button class="btn btn-sm btn-gold" onclick="window.open('../api/generate_invoice.php?id=${t.id}', '_blank')"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</button>`;
            } else if (t.proof) {
                return `<a href="${t.proof}" target="_blank" class="btn btn-sm btn-outline-light"><i class="bi bi-eye me-1"></i>Proof</a>`;
            }
            return '<span class="text-muted small">-</span>';
        }
    </script>
</body>
</html>
