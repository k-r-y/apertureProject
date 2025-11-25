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
    <title>Reviews Moderation - Aperture Admin</title>

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
                    <h1 class="header-title m-0">Reviews Moderation</h1>
                    <button class="btn btn-sm btn-gold" onclick="loadReviews()"><i class="bi bi-arrow-clockwise"></i> Refresh</button>
                </div>

                <!-- Reviews Table -->
                <div class="glass-panel p-4">
                    <div class="table-responsive">
                        <table class="table table-luxury align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3">Client</th>
                                    <th>Event</th>
                                    <th>Rating</th>
                                    <th>Comment</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="reviewsTableBody">
                                <tr><td colspan="6" class="text-center text-muted">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../libs/sweetalert2/sweetalert2.all.min.js"></script>
    <script src="admin.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', loadReviews);

        function loadReviews() {
            fetch('../user/api/reviews_api.php?action=admin_get_all')
                .then(r => r.json())
                .then(data => {
                    const tbody = document.getElementById('reviewsTableBody');
                    if (data.success && data.reviews.length > 0) {
                        tbody.innerHTML = data.reviews.map(r => `
                            <tr>
                                <td class="ps-3 text-gold">${r.FirstName} ${r.LastName}</td>
                                <td>${r.event_type}</td>
                                <td>${renderStars(r.rating)}</td>
                                <td class="text-truncate" style="max-width: 200px;" title="${r.comment}">${r.comment}</td>
                                <td>
                                    <span class="status-badge status-${r.status}">${r.status.toUpperCase()}</span>
                                </td>
                                <td class="text-end pe-3">
                                    ${r.status === 'pending' ? `
                                        <button class="btn btn-sm btn-success me-1" onclick="updateStatus(${r.reviewID}, 'approved')"><i class="bi bi-check-lg"></i></button>
                                        <button class="btn btn-sm btn-danger" onclick="updateStatus(${r.reviewID}, 'rejected')"><i class="bi bi-x-lg"></i></button>
                                    ` : `
                                        <button class="btn btn-sm btn-ghost" onclick="updateStatus(${r.reviewID}, 'pending')">Reset</button>
                                    `}
                                </td>
                            </tr>
                        `).join('');
                    } else {
                        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No reviews found</td></tr>';
                    }
                });
        }

        function renderStars(rating) {
            let stars = '';
            for(let i=0; i<5; i++) {
                stars += `<i class="bi bi-star${i < rating ? '-fill text-warning' : ' text-muted'}"></i>`;
            }
            return stars;
        }

        function updateStatus(id, status) {
            fetch('../user/api/reviews_api.php?action=admin_update_status', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({id, status})
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    loadReviews();
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
