<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar" id="sidebar">
    <!-- Sidebar Header: Logo + Toggle -->
    <div class="sidebar-header">
        <a class="sidebar-brand" href="user.php">
            <img src="../assets/logo-for-dark.png" alt="Aperture Logo" class="brand-icon">
        </a>
        <button class="sidebar-toggle-btn" id="sidebar-collapse-btn" aria-label="Toggle Sidebar">
            <i class="bi bi-chevron-left"></i>
        </button>
    </div>

    <!-- Sidebar Navigation -->
    <ul class="sidebar-nav">
        <li class="nav-section-title"><span class="nav-text">Main</span></li>
        <li class="sidebar-nav-item">
            <a href="user.php" class="sidebar-nav-link <?= ($currentPage == 'user.php') ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2-fill nav-icon"></i>
                <span class="nav-text">Dashboard</span>
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="bookingForm.php" class="sidebar-nav-link <?= ($currentPage == 'bookingForm.php') ? 'active' : '' ?>">
                <i class="bi bi-journal-plus nav-icon"></i>
                <span class="nav-text">New Booking</span>
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="appointments.php" class="sidebar-nav-link <?= ($currentPage == 'appointments.php') ? 'active' : '' ?>">
                <i class="bi bi-calendar2-check-fill nav-icon"></i>
                <span class="nav-text">My Appointments</span>
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="myPhotos.php" class="sidebar-nav-link <?= ($currentPage == 'myPhotos.php') ? 'active' : '' ?>">
                <i class="bi bi-images nav-icon"></i>
                <span class="nav-text">My Photos</span>
            </a>
        </li>
    </ul>

    <!-- Sidebar Footer: User Profile + Logout -->
    <div class="sidebar-footer">
        <div class="user-profile">
            <a href="profile.php" class="name-card">
                <div class="user-avatar">
                    <?php 
                        $name = $_SESSION['fullName'] ?? 'Client';
                        $initials = substr($name, 0, 1);
                        echo strtoupper($initials);
                    ?>
                </div>
                <div class="user-info">
                    <span class="user-name"><?php echo htmlspecialchars($_SESSION['fullName'] ?? 'Client'); ?></span>
                    <small class="user-role">Client</small>
                </div>
            </a>
        </div>
        <div class="sidebar-nav-item">
            <a href="../logout.php" class="sidebar-nav-link">
                <i class="bi bi-box-arrow-left nav-icon"></i>
                <span class="nav-text">Logout</span>
            </a>
        </div>
    </div>
</aside>