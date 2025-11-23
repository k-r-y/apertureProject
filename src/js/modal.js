const LuxuryModal = {
    overlay: null,
    container: null,
    title: null,
    message: null,
    icon: null,
    footer: null,
    resolvePromise: null,

    init() {
        this.overlay = document.getElementById('luxuryModal');
        if (!this.overlay) return;

        this.container = this.overlay.querySelector('.luxury-modal-container');
        this.title = document.getElementById('luxuryModalTitle');
        this.message = document.getElementById('luxuryModalMessage');
        this.icon = document.getElementById('luxuryModalIcon');
        this.footer = document.getElementById('luxuryModalFooter');
    },

    show({ title = 'Notification', message = '', html = null, icon = 'info', confirmText = 'OK', cancelText = null, confirmColor = null, showCancelButton = false, confirmButtonText = 'OK', cancelButtonText = 'Cancel' }) {
        if (!this.overlay) this.init();
        if (!this.overlay) return Promise.reject('Modal not found');

        return new Promise((resolve) => {
            this.resolvePromise = resolve;

            // Set content
            this.title.textContent = title;

            // Use html if provided, otherwise use message
            if (html) {
                this.message.innerHTML = html;
            } else {
                this.message.textContent = message;
            }

            // Set icon
            this.icon.className = 'luxury-modal-icon ' + icon;
            let iconHtml = '';
            switch (icon) {
                case 'success': iconHtml = '<i class="bi bi-check-circle"></i>'; break;
                case 'error': iconHtml = '<i class="bi bi-x-circle"></i>'; break;
                case 'warning': iconHtml = '<i class="bi bi-exclamation-triangle"></i>'; break;
                case 'question': iconHtml = '<i class="bi bi-question-circle"></i>'; break;
                default: iconHtml = '<i class="bi bi-info-circle"></i>';
            }
            this.icon.innerHTML = iconHtml;

            // Build buttons
            this.footer.innerHTML = '';

            // Use showCancelButton or cancelText to determine if cancel button should show
            const shouldShowCancel = showCancelButton || cancelText;
            const cancelBtnText = cancelButtonText || cancelText || 'Cancel';
            const confirmBtnText = confirmButtonText || confirmText || 'OK';

            if (shouldShowCancel) {
                const cancelBtn = document.createElement('button');
                cancelBtn.className = 'luxury-modal-btn luxury-modal-btn-secondary';
                cancelBtn.textContent = cancelBtnText;
                cancelBtn.onclick = () => this.close(false);
                this.footer.appendChild(cancelBtn);
            }

            const confirmBtn = document.createElement('button');
            confirmBtn.className = 'luxury-modal-btn luxury-modal-btn-primary';
            confirmBtn.textContent = confirmBtnText;
            if (confirmColor) {
                confirmBtn.style.background = confirmColor;
                confirmBtn.style.color = '#fff';
            }
            confirmBtn.onclick = () => this.close(true);
            this.footer.appendChild(confirmBtn);

            // Show modal
            this.overlay.style.display = 'flex';
            // Force reflow
            this.overlay.offsetHeight;
            this.overlay.classList.add('active');
        });
    },

    close(result = false) {
        if (!this.overlay) return;

        this.overlay.classList.remove('active');
        setTimeout(() => {
            this.overlay.style.display = 'none';
            if (this.resolvePromise) {
                this.resolvePromise({ isConfirmed: result });
                this.resolvePromise = null;
            }
        }, 300);
    }
};

// Initialize on load
document.addEventListener('DOMContentLoaded', () => {
    LuxuryModal.init();
});
