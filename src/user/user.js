document.addEventListener("DOMContentLoaded", function () {
    // ===================================
    // SIDEBAR TOGGLE LOGIC
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

    // Mobile toggle (in user header)
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

    // Close mobile sidebar when clicking on overlay (body::before) or outside
    body.addEventListener('click', (e) => {
        if (window.innerWidth <= 991 &&
            body.classList.contains('sidebar-mobile-active') &&
            sidebar && !sidebar.contains(e.target) &&
            headerToggle && !headerToggle.contains(e.target)) {
            body.classList.remove('sidebar-mobile-active');
        }
    });

    // Handle window resize
    window.addEventListener('resize', () => {
        const isMobile = window.innerWidth <= 991;
        if (!isMobile) {
            body.classList.remove('sidebar-mobile-active');
        } else {
            body.classList.remove('sidebar-mini');
        }
    });

    // ===================================
    // SECURITY & VALIDATION HELPERS
    // ===================================

    function sanitizeInput(input) {
        const div = document.createElement('div');
        div.textContent = input;
        return div.innerHTML;
    }
});