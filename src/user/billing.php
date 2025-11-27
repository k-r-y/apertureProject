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
            <div class="container-fluid px-3 px-lg-5 py-5">
                
                <div class="mb-5">
                    <h1 class="mb-2">Billing History</h1>
                    <p class="text-muted">View your invoices, payments, and refunds</p>
                </div>
                
                <div class="glass-panel p-4">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="text-gold text-uppercase small letter-spacing-1">Type</th>
                                    <th class="text-gold text-uppercase small letter-spacing-1">Ref #</th>
                                    <th class="text-gold text-uppercase small letter-spacing-1">Description</th>
                                    <th class="text-gold text-uppercase small letter-spacing-1">Date</th>
                                    <th class="text-gold text-uppercase small letter-spacing-1">Amount</th>
                                    <th class="text-gold text-uppercase small letter-spacing-1">Status</th>
                                    <th class="text-gold text-uppercase small letter-spacing-1 text-end">Details</th>
                                </tr>
                            </thead>
                            <tbody id="billingTableBody">
                                <tr><td colspan="7" class="text-center py-5 text-muted">Loading history...</td></tr>
                            </tbody>
                        </table>
                    </div>
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
                        tbody.innerHTML = data.transactions.map(t => `
                            <tr>
                                <td><span class="badge bg-dark border border-secondary text-light">${t.type}</span></td>
                                <td class="font-monospace text-gold">${t.ref_number}</td>
                                <td>${t.description}</td>
                                <td>${new Date(t.date).toLocaleDateString()}</td>
                                <td>₱${Number(t.amount).toLocaleString()}</td>
                                <td>
                                    <span class="badge ${getStatusClass(t.status)}">${t.status.toUpperCase()}</span>
                                </td>
                                <td class="text-end">
                                    ${getActionButtons(t)}
                                </td>
                            </tr>
                        `).join('');
                    } else {
                        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-5 text-muted">No transactions found</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center py-5 text-danger">Error loading history</td></tr>';
                });
        });

        function getStatusClass(status) {
            switch(status.toLowerCase()) {
                case 'paid': return 'bg-success';
                case 'pending': return 'bg-warning text-dark';
                case 'processed': return 'bg-info text-dark';
                case 'approved': return 'bg-success';
                case 'rejected': return 'bg-danger';
                case 'cancelled': return 'bg-danger';
                default: return 'bg-secondary';
            }
        }

        function getActionButtons(t) {
            if (t.type === 'Invoice') {
                return `<button class="btn btn-sm btn-gold" onclick="window.open('../api/generate_invoice.php?id=${t.id}', '_blank')"><i class="bi bi-download"></i> PDF</button>`;
            } else if (t.proof) {
                return `<a href="${t.proof}" target="_blank" class="btn btn-sm btn-outline-light"><i class="bi bi-eye"></i> Proof</a>`;
            }
            return '<span class="text-muted">-</span>';
        }
    </script>
</body>
</html>
