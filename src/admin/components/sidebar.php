<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<nav class="sidebar" id="sidebar">
    <a class="sidebar-brand" href="adminDashboard.php">
        <img src="../assets/logo.png" alt="Aperture Logo" class="brand-icon">
    </a>

    <ul class="sidebar-nav">
        <li class="nav-section-title"><span class="nav-text">Main</span></li>
        <li class="sidebar-nav-item">
            <a href="adminDashboard.php" class="sidebar-nav-link <?= ($currentPage == 'adminDashboard.php') ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2-fill nav-icon"></i>
                <span class="nav-text">Dashboard</span>
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="calendar.php" class="sidebar-nav-link <?= ($currentPage == 'calendar.php') ? 'active' : '' ?>">
                <i class="bi bi-calendar-month-fill nav-icon"></i>
                <span class="nav-text">Calendar</span>
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="inquiries.php" class="sidebar-nav-link <?= ($currentPage == 'inquiries.php') ? 'active' : '' ?>">
                <i class="bi bi-journal-album nav-icon"></i>
                <span class="nav-text">Enquiries</span>
            </a>
        </li>

        <li class="nav-section-title"><span class="nav-text">Management</span></li>
        <li class="sidebar-nav-item">
            <a href="appointment.php" class="sidebar-nav-link <?= ($currentPage == 'appointment.php') ? 'active' : '' ?>">
                <i class="bi bi-calendar2-check-fill nav-icon"></i>
                <span class="nav-text">Appointments</span>
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="client.php" class="sidebar-nav-link <?= ($currentPage == 'client.php') ? 'active' : '' ?>">
                <i class="bi bi-people-fill nav-icon"></i>
                <span class="nav-text">Client Directory</span>
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="invoicing.php" class="sidebar-nav-link <?= ($currentPage == 'invoicing.php') ? 'active' : '' ?>">
                <i class="bi bi-receipt-cutoff nav-icon"></i>
                <span class="nav-text">Invoicing</span>
            </a>
        </li>

        <li class="nav-section-title"><span class="nav-text">Content</span></li>
        <li class="sidebar-nav-item">
            <a href="serviceAndPackages.php" class="sidebar-nav-link <?= ($currentPage == 'serviceAndPackages.php') ? 'active' : '' ?>">
                <i class="bi bi-box-fill nav-icon"></i>
                <span class="nav-text">Services & Packages</span>
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="portfoliomanager.php" class="sidebar-nav-link <?= ($currentPage == 'portfoliomanager.php') ? 'active' : '' ?>">
                <i class="bi bi-images nav-icon"></i>
                <span class="nav-text">Portfolio Manager</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <a href="settings.php" class="sidebar-nav-link <?= ($currentPage == 'settings.php') ? 'active' : '' ?>">
            <i class="bi bi-gear-fill nav-icon"></i>
            <span class="nav-text">Settings</span>
        </a>
        <a href="../logout.php" class="sidebar-nav-link">
            <i class="bi bi-box-arrow-left nav-icon"></i>
            <span class="nav-text">Logout</span>
        </a>
    </div>
</nav>