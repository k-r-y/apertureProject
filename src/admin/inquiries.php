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
    <title>Inquiries - Aperture Admin</title>

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
                    <h1 class="header-title m-0">Inquiries</h1>
                    <button class="btn btn-sm btn-gold" onclick="loadInquiries()"><i class="bi bi-arrow-clockwise"></i> Refresh</button>
                </div>

                <!-- Inquiries Table -->
                <div class="glass-panel p-4">
                    <div class="table-responsive">
                        <table class="table table-luxury align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3">From</th>
                                    <th>Subject</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="inquiriesTableBody">
                                <tr><td colspan="5" class="text-center text-muted">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- View Inquiry Modal -->
    <div class="modal fade" id="viewInquiryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-gold">Inquiry Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-light">
                    <div class="mb-3">
                        <small class="text-muted">From:</small>
                        <div id="modalName" class="fw-bold"></div>
                        <div id="modalEmail" class="text-gold"></div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Subject:</small>
                        <div id="modalSubject"></div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Message:</small>
                        <div id="modalMessage" class="p-3 bg-black rounded border border-secondary"></div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <a id="replyBtn" href="#" class="btn btn-gold">Reply via Email</a>
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../libs/sweetalert2/sweetalert2.all.min.js"></script>
    <script src="admin.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', loadInquiries);

        function loadInquiries() {
            fetch('api/inquiries_api.php?action=get_all')
                .then(r => r.json())
                .then(data => {
                    const tbody = document.getElementById('inquiriesTableBody');
                    if (data.success && data.inquiries.length > 0) {
                        tbody.innerHTML = data.inquiries.map(inq => `
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-bold text-gold">${inq.name}</div>
                                    <small class="text-muted">${inq.email}</small>
                                </td>
                                <td>${inq.subject}</td>
                                <td class="text-muted">${new Date(inq.created_at).toLocaleDateString()}</td>
                                <td>
                                    <span class="status-badge ${inq.status === 'new' ? 'status-new' : 'status-read'}" 
                                          style="${inq.status === 'read' ? 'background: rgba(255,255,255,0.1); color: #ccc; border: 1px solid rgba(255,255,255,0.2);' : ''}">
                                        ${inq.status.toUpperCase()}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <button class="btn btn-sm btn-ghost" onclick='viewInquiry(${JSON.stringify(inq)})'>View</button>
                                    <button class="btn btn-sm btn-ghost text-danger" onclick="deleteInquiry(${inq.id})"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                        `).join('');
                    } else {
                        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No inquiries found</td></tr>';
                    }
                });
        }

        function viewInquiry(inq) {
            document.getElementById('modalName').textContent = inq.name;
            document.getElementById('modalEmail').textContent = inq.email;
            document.getElementById('modalSubject').textContent = inq.subject;
            document.getElementById('modalMessage').textContent = inq.message;
            document.getElementById('replyBtn').href = `mailto:${inq.email}?subject=Re: ${inq.subject}`;
            
            new bootstrap.Modal(document.getElementById('viewInquiryModal')).show();

            if (inq.status === 'new') {
                updateStatus(inq.id, 'read');
            }
        }

        function updateStatus(id, status) {
            fetch('api/inquiries_api.php?action=update_status', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({id, status})
            }).then(() => loadInquiries()); // Refresh to show updated status
        }

        function deleteInquiry(id) {
            if(!confirm('Are you sure you want to delete this inquiry?')) return;
            fetch('api/inquiries_api.php?action=delete', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({id})
            })
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    loadInquiries();
                    Swal.fire('Deleted!', 'Inquiry has been deleted.', 'success');
                }
            });
        }
    </script>
</body>

</html>