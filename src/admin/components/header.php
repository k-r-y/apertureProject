<header class="header">
    <div class="d-flex align-items-center">
        <i class="bi bi-list header-toggle" id="sidebar-toggle"></i>
        <div class="header-search ms-3 d-none d-lg-block" style="width: 300px;">
            <input type="text" class="form-control form-control-sm" placeholder="Search...">
        </div>
    </div>
    <div class="d-flex align-items-center">
        <a href="#" class="btn btn-sm btn-gold me-3">+ New Appointment</a>
        <div class="text-end d-none d-md-block">
            <div class="fw-semibold">
                <?= htmlspecialchars($_SESSION['firstName'] ?? 'Admin') ?>
                <?= htmlspecialchars($_SESSION['lastName'] ?? 'User') ?>
            </div>
            <div class="small text-secondary">
                <?= htmlspecialchars($_SESSION['role'] ?? 'Administrator') ?>
            </div>
        </div>
        <img src="../assets/pic.png" alt="Profile" class="ms-3 profile-img" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border-color);">
    </div>
</header>