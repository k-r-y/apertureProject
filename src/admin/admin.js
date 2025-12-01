document.addEventListener("DOMContentLoaded", function () {
    // ===================================
    // SIDEBAR TOGGLE LOGIC - RESPONSIVE & PERSISTENT
    // ===================================

    const sidebarCollapseBtn = document.getElementById('sidebar-collapse-btn');
    const headerToggle = document.querySelector('.header-toggle');
    const pageWrapper = document.getElementById('page-wrapper');
    const body = document.body;
    const sidebar = document.getElementById('sidebar');

    // 1. Load Saved State (Desktop Only)
    const savedState = localStorage.getItem('sidebarState');
    const isMobile = window.innerWidth <= 991;

    if (!isMobile && savedState === 'collapsed') {
        body.classList.add('sidebar-mini');
    }

    // 2. Desktop Toggle (Sidebar Header)
    if (sidebarCollapseBtn) {
        sidebarCollapseBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            body.classList.toggle('sidebar-mini');

            // Save state
            if (body.classList.contains('sidebar-mini')) {
                localStorage.setItem('sidebarState', 'collapsed');
            } else {
                localStorage.setItem('sidebarState', 'expanded');
            }
        });
    }

    // 3. Mobile Toggle (Header Hamburger)
    if (headerToggle) {
        headerToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            body.classList.toggle('sidebar-mobile-active');
        });
    }

    // 4. Close Mobile Sidebar (Click Outside)
    // Click on main content
    if (pageWrapper) {
        pageWrapper.addEventListener('click', () => {
            if (window.innerWidth <= 991 && body.classList.contains('sidebar-mobile-active')) {
                body.classList.remove('sidebar-mobile-active');
            }
        });
    }

    // Click on overlay (pseudo-element on body)
    body.addEventListener('click', (e) => {
        if (window.innerWidth <= 991 &&
            body.classList.contains('sidebar-mobile-active') &&
            sidebar && !sidebar.contains(e.target) &&
            headerToggle && !headerToggle.contains(e.target)) {
            body.classList.remove('sidebar-mobile-active');
        }
    });

    // 5. Handle Window Resize
    window.addEventListener('resize', () => {
        const isMobileNow = window.innerWidth <= 991;

        if (!isMobileNow) {
            // Desktop Mode
            body.classList.remove('sidebar-mobile-active');

            // Restore saved preference for desktop
            const savedState = localStorage.getItem('sidebarState');
            if (savedState === 'collapsed') {
                body.classList.add('sidebar-mini');
            } else {
                body.classList.remove('sidebar-mini');
            }
        } else {
            // Mobile Mode
            body.classList.remove('sidebar-mini'); // Always expand content in mobile (sidebar is hidden/overlay)
        }
    });
});