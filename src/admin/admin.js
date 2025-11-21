document.addEventListener("DOMContentLoaded", function () {
    // ===================================
    // SIDEBAR TOGGLE LOGIC - RESPONSIVE
    // ===================================

    const sidebarToggle = document.getElementById('sidebar-toggle');
    const pageWrapper = document.getElementById('page-wrapper');
    const body = document.body;

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', (e) => {
            e.stopPropagation(); // Prevent event bubbling

            const isMobile = window.innerWidth <= 768;

            if (isMobile) {
                // Mobile: Toggle Hide/Show
                body.classList.toggle('sidebar-mobile-active');
            } else {
                // Desktop: Toggle Mini/Max
                body.classList.toggle('sidebar-mini');
            }
        });
    }

    // Close mobile sidebar when clicking on the main content
    if (pageWrapper) {
        pageWrapper.addEventListener('click', () => {
            if (window.innerWidth <= 768 && body.classList.contains('sidebar-mobile-active')) {
                body.classList.remove('sidebar-mobile-active');
            }
        });
    }

    // Close mobile sidebar when clicking on overlay (body::before)
    body.addEventListener('click', (e) => {
        if (window.innerWidth <= 768 &&
            body.classList.contains('sidebar-mobile-active') &&
            !document.getElementById('sidebar').contains(e.target) &&
            !sidebarToggle.contains(e.target)) {
            body.classList.remove('sidebar-mobile-active');
        }
    });

    // Handle window resize - cleanup states
    window.addEventListener('resize', () => {
        const isMobile = window.innerWidth <= 768;

        if (!isMobile) {
            // Switched to desktop - remove mobile class
            body.classList.remove('sidebar-mobile-active');
        } else {
            // Switched to mobile - remove mini class
            body.classList.remove('sidebar-mini');
        }
    });
});