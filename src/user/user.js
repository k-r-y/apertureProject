document.addEventListener("DOMContentLoaded", function () {
    // --- Sidebar Toggle Logic ---
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const body = document.body;

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                body.classList.toggle('sidebar-mobile-active');
            } else {
                body.classList.toggle('sidebar-mini');
            }
        });
    }
});