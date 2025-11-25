<header class="header position-fixed w-100 ">
    <div class="d-flex align-items-center justify-content-between w-100">
        <div class="d-flex align-items-center">
            <i class="bi bi-list header-toggle" id="sidebar-toggle"></i>
            <div class="header-search ms-3 d-none d-lg-block" style="width: 300px;">
                <input type="text" class="form-control form-control-sm" placeholder="Search your bookings...">
            </div>
        </div>
        
        <!-- Notifications Bell -->
        <div class="dropdown me-4">
            <button class="btn btn-link text-light position-relative" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
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
    </div>

</header>