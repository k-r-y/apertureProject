/**
 * Luxury Feedback System
 * Handles Toast Notifications and Loading States
 */

const LuxuryToast = {
    container: null,

    init() {
        if (this.container) return;

        this.container = document.createElement('div');
        this.container.className = 'luxury-toast-container';
        document.body.appendChild(this.container);
    },

    /**
     * Show a toast notification
     * @param {Object} options
     * @param {string} options.message - The message to display
     * @param {string} options.type - 'success', 'error', 'warning', 'info'
     * @param {number} options.duration - Duration in ms (default 3000)
     */
    show({ message, type = 'info', duration = 3000 }) {
        this.init();

        const toast = document.createElement('div');
        toast.className = `luxury-toast luxury-toast-${type}`;

        let icon = '';
        switch (type) {
            case 'success': icon = '<i class="bi bi-check-circle-fill"></i>'; break;
            case 'error': icon = '<i class="bi bi-x-circle-fill"></i>'; break;
            case 'warning': icon = '<i class="bi bi-exclamation-triangle-fill"></i>'; break;
            default: icon = '<i class="bi bi-info-circle-fill"></i>';
        }

        toast.innerHTML = `
            <div class="toast-icon">${icon}</div>
            <div class="toast-message">${message}</div>
            <div class="toast-close"><i class="bi bi-x"></i></div>
        `;

        // Close button logic
        toast.querySelector('.toast-close').addEventListener('click', () => {
            this.dismiss(toast);
        });

        this.container.appendChild(toast);

        // Animate in
        requestAnimationFrame(() => {
            toast.classList.add('show');
        });

        // Auto dismiss
        if (duration > 0) {
            setTimeout(() => {
                this.dismiss(toast);
            }, duration);
        }
    },

    dismiss(toast) {
        toast.classList.remove('show');
        toast.addEventListener('transitionend', () => {
            if (toast.parentElement) {
                toast.remove();
            }
        });
    }
};

const LuxuryLoader = {
    /**
     * Show a loading spinner inside a container
     * @param {HTMLElement} container - The element to show the loader in
     * @param {string} text - Optional loading text
     */
    show(container, text = 'Loading...') {
        if (!container) return;

        // Check if loader already exists
        if (container.querySelector('.luxury-loader-overlay')) return;

        const loader = document.createElement('div');
        loader.className = 'luxury-loader-overlay';
        loader.innerHTML = `
            <div class="luxury-loader">
                <div class="spinner"></div>
                ${text ? `<div class="loader-text">${text}</div>` : ''}
            </div>
        `;

        // Ensure container is positioned relatively so absolute loader works
        const style = window.getComputedStyle(container);
        if (style.position === 'static') {
            container.style.position = 'relative';
        }

        container.appendChild(loader);

        requestAnimationFrame(() => {
            loader.classList.add('active');
        });
    },

    /**
     * Hide the loader from a container
     * @param {HTMLElement} container 
     */
    hide(container) {
        if (!container) return;

        const loader = container.querySelector('.luxury-loader-overlay');
        if (loader) {
            loader.classList.remove('active');
            setTimeout(() => {
                loader.remove();
            }, 300);
        }
    }
};

// Initialize on load
document.addEventListener('DOMContentLoaded', () => {
    LuxuryToast.init();
});
