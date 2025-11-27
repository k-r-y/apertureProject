// User Notifications System
class UserNotificationManager {
    constructor() {
        this.badge = document.getElementById('notificationBadge');
        this.notificationsList = document.getElementById('notificationsList');
        this.markAllBtn = document.getElementById('markAllRead');

        if (this.badge && this.notificationsList) {
            this.init();
        }
    }

    init() {
        // Fetch notifications on load
        this.fetchNotifications();

        // Poll for new notifications every 60 seconds
        setInterval(() => {
            this.fetchNotifications();
        }, 60000);

        // Mark all read button
        if (this.markAllBtn) {
            this.markAllBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.markAllAsRead();
            });
        }
    }

    async fetchNotifications() {
        try {
            const response = await fetch('api/get_notifications.php');
            const data = await response.json();

            if (data.success) {
                this.updateBadge(data.count);
                this.renderNotifications(data.notifications);
            }
        } catch (error) {
            console.error('Error fetching notifications:', error);
        }
    }

    updateBadge(count) {
        if (count > 0) {
            this.badge.textContent = count;
            this.badge.style.display = 'block';
        } else {
            this.badge.style.display = 'none';
        }
    }

    renderNotifications(notifications) {
        if (!notifications || notifications.length === 0) {
            this.notificationsList.innerHTML = '<li class="px-3 py-2 text-muted text-center">No new notifications</li>';
            return;
        }

        this.notificationsList.innerHTML = notifications.map(notif => `
            <li class="notification-item px-3 py-2 border-bottom border-secondary ${notif.is_read == 0 ? 'bg-dark-highlight' : ''}" 
                onclick="notificationManager.handleNotificationClick(${notif.id}, '${notif.link}')">
                <div class="d-flex align-items-start">
                    <i class="bi ${this.getIcon(notif.type)} text-gold me-2 mt-1"></i>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-0 text-light" style="font-size: 0.85rem;">${notif.title}</h6>
                            ${notif.is_read == 0 ? '<span class="badge bg-gold rounded-pill" style="width: 6px; height: 6px; padding: 0;"> </span>' : ''}
                        </div>
                        <p class="mb-1 text-muted small">${notif.message}</p>
                        <small class="text-muted" style="font-size: 0.7rem;">${this.formatDate(notif.created_at)}</small>
                    </div>
                </div>
            </li>
        `).join('');
    }

    getIcon(type) {
        const icons = {
            'booking_status': 'bi-calendar-check',
            'payment': 'bi-credit-card',
            'message': 'bi-chat-dots',
            'system': 'bi-info-circle'
        };
        return icons[type] || 'bi-bell';
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = (now - date) / 1000; // seconds

        if (diff < 60) return 'Just now';
        if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
        if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
        return date.toLocaleDateString();
    }

    async handleNotificationClick(id, link) {
        // Mark as read
        try {
            await fetch('api/mark_notification_read.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            });
        } catch (error) {
            console.error('Error marking as read:', error);
        }

        // Redirect
        if (link) {
            window.location.href = link;
        }
    }

    async markAllAsRead() {
        try {
            const response = await fetch('api/mark_notification_read.php?action=mark_all_read', {
                method: 'POST'
            });
            const data = await response.json();

            if (data.success) {
                this.fetchNotifications();
            }
        } catch (error) {
            console.error('Error marking all as read:', error);
        }
    }
}

// Initialize and expose global instance for onclick handlers
let notificationManager;
document.addEventListener('DOMContentLoaded', () => {
    notificationManager = new UserNotificationManager();
});
