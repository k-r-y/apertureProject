document.addEventListener("DOMContentLoaded", function () {
    // ===================================
    // SIDEBAR TOGGLE LOGIC
    // ===================================

    const sidebarToggle = document.getElementById('sidebar-toggle');
    const pageWrapper = document.getElementById('page-wrapper');
    const body = document.body;

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            const isMobile = window.innerWidth <= 768;

            if (isMobile) {
                body.classList.toggle('sidebar-mobile-active');
            } else {
                body.classList.toggle('sidebar-mini');
            }
        });
    }

    if (pageWrapper) {
        pageWrapper.addEventListener('click', () => {
            if (window.innerWidth <= 768 && body.classList.contains('sidebar-mobile-active')) {
                body.classList.remove('sidebar-mobile-active');
            }
        });
    }

    body.addEventListener('click', (e) => {
        if (window.innerWidth <= 768 &&
            body.classList.contains('sidebar-mobile-active') &&
            !document.getElementById('sidebar').contains(e.target) &&
            !sidebarToggle.contains(e.target)) {
            body.classList.remove('sidebar-mobile-active');
        }
    });

    window.addEventListener('resize', () => {
        const isMobile = window.innerWidth <= 768;
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