<header class="header admin-header position-fixed">
    <div class="d-flex align-items-center justify-content-between w-100 px-3">
        <!-- Mobile/Tablet Toggle Button -->
        <button class="header-toggle" id="mobile-toggle" aria-label="Toggle Menu">
            <i class="bi bi-list"></i>
        </button>
        
        <div class="d-flex align-items-center gap-3">
            <!-- Notifications Bell (Always Visible) -->
            <div class="dropdown">
                <button class="btn btn-link text-light position-relative p-0" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell fs-5"></i>
                    <span id="notificationBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">
                        0
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end bg-dark border-secondary" style="width: 350px; max-height: 400px; overflow-y: auto;" aria-labelledby="notificationDropdown">
                    <li class="dropdown-header text-gold d-flex justify-content-between align-items-center">
                        <span>Notifications</span>
                        <button class="btn btn-link btn-sm text-light p-0" id="markAllRead" style="font-size: 0.75rem;">Mark all read</button>
                    </li>
                    <li><hr class="dropdown-divider border-secondary"></li>
                    <div id="notificationsList">
                        <li class="px-3 py-2 text-muted text-center">No new notifications</li>
                    </div>
                </ul>
            </div>

            <!-- User Profile Dropdown -->
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-avatar me-2">
                        <?php 
                            $name = $_SESSION['fullName'] ?? 'Client';
                            $initials = substr($name, 0, 1);
                            echo strtoupper($initials);
                        ?>
                    </div>
                    <div class="d-none d-md-block">
                        <span class="text-light fw-bold" style="font-size: 0.875rem;"><?php echo htmlspecialchars($_SESSION['fullName'] ?? 'Client'); ?></span>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end bg-dark border-secondary">
                    <li><a class="dropdown-item text-light" href="profile.php"><i class="bi bi-person me-2"></i>My Profile</a></li>
                    <li><hr class="dropdown-divider border-secondary"></li>
                    <li><a class="dropdown-item text-danger" href="../logout.php"><i class="bi bi-box-arrow-left me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>

<style>
.user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #d4af37 0%, #f0d068 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    font-weight: 600;
    color: #000;
}

.dropdown-menu {
    margin-top: 0.5rem;
}

.dropdown-item:hover {
    background: rgba(212, 175, 55, 0.1);
}
</style>