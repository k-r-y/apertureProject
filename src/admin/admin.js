document.addEventListener("DOMContentLoaded", function () {
    // ===================================
    // SIDEBAR TOGGLE LOGIC - RESPONSIVE
    // ===================================

    const sidebarCollapseBtn = document.getElementById('sidebar-collapse-btn');
    const headerToggle = document.querySelector('.header-toggle');
    const pageWrapper = document.getElementById('page-wrapper');
    const body = document.body;
    const sidebar = document.getElementById('sidebar');

    // Desktop toggle (in sidebar header)
    if (sidebarCollapseBtn) {
        sidebarCollapseBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            body.classList.toggle('sidebar-mini');
        });
    }

    // Mobile toggle (in admin header)
    if (headerToggle) {
        headerToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            body.classList.toggle('sidebar-mobile-active');
        });
    }

    // Close mobile sidebar when clicking on the main content
    if (pageWrapper) {
        pageWrapper.addEventListener('click', () => {
            if (window.innerWidth <= 991 && body.classList.contains('sidebar-mobile-active')) {
                body.classList.remove('sidebar-mobile-active');
            }
        });
    }

    // Close mobile sidebar when clicking on overlay (body::before)
    body.addEventListener('click', (e) => {
        if (window.innerWidth <= 991 &&
            body.classList.contains('sidebar-mobile-active') &&
            sidebar && !sidebar.contains(e.target) &&
            headerToggle && !headerToggle.contains(e.target)) {
            body.classList.remove('sidebar-mobile-active');
        }
    });

    // Handle window resize - cleanup states
    window.addEventListener('resize', () => {
        const isMobile = window.innerWidth <= 991;

        if (!isMobile) {
            // Switched to desktop - remove mobile class
            body.classList.remove('sidebar-mobile-active');
        } else {
            // Switched to mobile - remove mini class
            body.classList.remove('sidebar-mini');
        }
    });
});