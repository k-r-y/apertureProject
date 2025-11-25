<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar d-flex flex-column" id="sidebar">
    <div>
        <a class="sidebar-brand p-0" href="user.php">
            <img src="../assets/logo-for-dark.png" alt="Aperture Logo" class="brand-icon m-0" style="height: 50px; width: 150px; object-fit: cover;">
        </a>
    </div>


    <ul class="sidebar-nav flex-grow-1">
        <li class="nav-section-title pt-0"><span class="nav-text">Main</span></li>
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

        <li class="nav-section-title"><span class="nav-text">Account</span></li>
        <li class="sidebar-nav-item">
            <a href="#" class="sidebar-nav-link">
                <i class="bi bi-receipt-cutoff nav-icon"></i>
                <span class="nav-text">Billing History</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer mt-auto">
        <hr class="mx-3" style="border-color: rgba(255,255,255,0.1);">
        <div class="user-profile ">
            <a href="profile.php" class="d-flex align-items-center text-decoration-none name-card">
                
                <div class="ms-3">
                    <span class="d-block text-light fw-bold" style="font-size: 0.9rem;"><?php echo htmlspecialchars($_SESSION['fullName'] ?? 'Client'); ?></span>
                    <small class="text-secondary" style="font-size: 0.75rem;">Client</small>
                </div>
            </a>
            <a href="../logout.php" class="sidebar-nav-link mt-2">
                <i class="bi bi-box-arrow-left nav-icon"></i>
                <span class="nav-text">Logout</span>
            </a>
        </div>
    </div>
</aside>