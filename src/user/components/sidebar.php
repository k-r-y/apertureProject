<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<nav class="sidebar" id="sidebar">
    <a class="sidebar-brand" href="dashboard.php">
        <img src="../assets/logo.png" alt="Aperture Logo" class="brand-icon">
    </a>

    <ul class="sidebar-nav">
        <li class="nav-section-title"><span class="nav-text">Main</span></li>
        <li class="sidebar-nav-item">
            <a href="dashboard.php" class="sidebar-nav-link <?= ($currentPage == 'dashboard.php') ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2-fill nav-icon"></i>
                <span class="nav-text">Dashboard</span>
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="appointments.php" class="sidebar-nav-link <?= ($currentPage == 'appointments.php') ? 'active' : '' ?>">
                <i class="bi bi-calendar2-check-fill nav-icon"></i>
                <span class="nav-text">My Appointments</span>
            </a>
        </li>

        <li class="nav-section-title"><span class="nav-text">Account</span></li>
        <li class="sidebar-nav-item">
            <a href="profile.php" class="sidebar-nav-link <?= ($currentPage == 'profile.php') ? 'active' : '' ?>">
                <i class="bi bi-person-fill nav-icon"></i>
                <span class="nav-text">My Profile</span>
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="#" class="sidebar-nav-link">
                <i class="bi bi-receipt-cutoff nav-icon"></i>
                <span class="nav-text">Billing History</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <a href="#" class="sidebar-nav-link">
            <i class="bi bi-gear-fill nav-icon"></i>
            <span class="nav-text">Settings</span>
        </a>
        <a href="../logout.php" class="sidebar-nav-link">
            <i class="bi bi-box-arrow-left nav-icon"></i>
            <span class="nav-text">Logout</span>
        </a>
    </div>
</nav>