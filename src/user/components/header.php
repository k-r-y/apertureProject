<header class="header">
    <div class="d-flex align-items-center">
        <i class="bi bi-list header-toggle" id="sidebar-toggle"></i>
        <div class="header-search ms-3 d-none d-lg-block" style="width: 300px;">
            <input type="text" class="form-control form-control-sm" placeholder="Search your bookings...">
        </div>
    </div>
    <div class="d-flex align-items-center">
        <a href="../booking.php" class="btn btn-sm btn-gold me-3">+ New Booking</a>
        <div class="text-end d-none d-md-block">
            <div class="fw-semibold">
                <?= htmlspecialchars($_SESSION['firstName'] ?? 'Client') ?>
                <?= htmlspecialchars($_SESSION['lastName'] ?? 'Name') ?>
            </div>
            <div class="small text-secondary">
                Client
            </div>
        </div>
        <img src="../assets/pic.png" alt="Profile" class="ms-3 profile-img" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border-color);">
    </div>
</header>