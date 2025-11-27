
<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar pt-0 pe-0" id="sidebar">
    <!-- Sidebar Header: Logo + Toggle -->
    <div class="sidebar-header">
        <a class="sidebar-brand p-0" href="adminDashboard.php">
            <img src="../assets/logo-for-dark.png" alt="Aperture Logo" class="brand-icon p-0 pt-3" style="width: 150px; height: 150px;">
        </a>
        <button class="sidebar-toggle-btn" id="sidebar-collapse-btn" aria-label="Toggle Sidebar">
            <i class="bi bi-chevron-left"></i>
        </button>
    </div>

    <!-- Sidebar Navigation -->
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
            <a href="bookings.php" class="sidebar-nav-link <?= ($currentPage == 'bookings.php') ? 'active' : '' ?>">
                <i class="bi bi-calendar-check nav-icon"></i>
                <span class="nav-text">Bookings</span>
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
        <li class="sidebar-nav-item">
            <a href="refunds.php" class="sidebar-nav-link <?= ($currentPage == 'refunds.php') ? 'active' : '' ?>">
                <i class="bi bi-cash-coin nav-icon"></i>
                <span class="nav-text">Refunds</span>
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
        <li class="sidebar-nav-item">
            <a href="photoUpload.php" class="sidebar-nav-link <?= ($currentPage == 'photoUpload.php') ? 'active' : '' ?>">
                <i class="bi bi-cloud-upload nav-icon"></i>
                <span class="nav-text">Photo Upload</span>
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="reviews.php" class="sidebar-nav-link <?= ($currentPage == 'reviews.php') ? 'active' : '' ?>">
                <i class="bi bi-star-fill nav-icon"></i>
                <span class="nav-text">Reviews</span>
            </a>
        </li>
    </ul>


</aside>