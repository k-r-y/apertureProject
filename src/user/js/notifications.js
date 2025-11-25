// Notifications System
class NotificationManager {
    constructor() {
        this.badge = document.getElementById('notificationBadge');
        this.notificationsList = document.getElementById('notificationsList');
        this.markAllReadBtn = document.getElementById('markAllRead');

        if (this.badge && this.notificationsList) {
            this.init();
        }
    }

    init() {
        // Fetch notifications on load
        this.fetchUnreadCount();
        this.fetchNotifications();

        // Poll for new notifications every 30 seconds
        setInterval(() => {
            this.fetchUnreadCount();
            this.fetchNotifications();
        }, 30000);

        // Mark all as read
        if (this.markAllReadBtn) {
            this.markAllReadBtn.addEventListener('click', () => this.markAllAsRead());
        }
    }

    async fetchUnreadCount() {
        try {
            const response = await fetch('../api/notifications.php?action=get_unread_count', {
                credentials: 'include'
            });
            const data = await response.json();

            if (data.success && data.count > 0) {
                this.badge.textContent = data.count;
                this.badge.style.display = 'block';
            } else {
                this.badge.style.display = 'none';
            }
        } catch (error) {
            console.error('Error fetching unread count:', error);
        }
    }

    async fetchNotifications() {
        try {
            const response = await fetch('../api/notifications.php?action=get_all&limit=10', {
                credentials: 'include'
            });
            const data = await response.json();

            if (data.success) {
                this.renderNotifications(data.notifications);
            }
        } catch (error) {
            console.error('Error fetching notifications:', error);
        }
    }

    renderNotifications(notifications) {
        if (notifications.length === 0) {
            this.notificationsList.innerHTML = '<li class="px-3 py-2 text-muted text-center">No new notifications</li>';
            return;
        }

        this.notificationsList.innerHTML = notifications.map(notif => `
            <li class="notification-item px-3 py-2 border-bottom border-secondary ${notif.is_read ? '' : 'unread'}" 
                data-id="${notif.notificationID}" 
                onclick="notificationManager.handleNotificationClick(${notif.notificationID}, '${notif.link || ''}')">
                <div class="d-flex align-items-start">
                    <i class="bi ${this.getIcon(notif.type)} text-gold me-2 mt-1"></i>
                    <div class="flex-grow-1">
                        <h6 class="mb-0 text-light" style="font-size: 0.85rem;">${notif.title}</h6>
                        <p class="mb-1 text-muted small">${notif.message}</p>
                        <small class="text-muted" style="font-size: 0.7rem;">${this.formatDate(notif.created_at)}</small>
                    </div>
                    ${!notif.is_read ? '<span class="badge bg-gold ms-2">New</span>' : ''}
                </div>
            </li>
        `).join('');

        // Add styles for unread notifications
        const style = document.createElement('style');
        style.textContent = `
            .notification-item {
                cursor: pointer;
                transition: background-color 0.2s;
            }
            .notification-item:hover {
                background-color: rgba(255, 255, 255, 0.05) !important;
            }
            .notification-item.unread {
                background-color: rgba(212, 175, 55, 0.1);
            }
        `;
        if (!document.getElementById('notification-styles')) {
            style.id = 'notification-styles';
            document.head.appendChild(style);
        }
    }

    async handleNotificationClick(notificationId, link) {
        // Mark as read
        try {
            await fetch('../api/notifications.php?action=mark_read', {
                method: 'POST',
                credentials: 'include',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `notificationId=${notificationId}`
            });

            // Update UI
            this.fetchUnreadCount();
            this.fetchNotifications();

            // Navigate if link provided
            if (link) {
                window.location.href = link;
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }

    async markAllAsRead() {
        try {
            const response = await fetch('../api/notifications.php?action=mark_all_read', {
                method: 'POST',
                credentials: 'include'
            });
            const data = await response.json();

            if (data.success) {
                this.fetchUnreadCount();
                this.fetchNotifications();
            }
        } catch (error) {
            console.error('Error marking all as read:', error);
        }
    }

    getIcon(type) {
        const icons = {
            'booking_status': 'bi-calendar-check',
            'payment': 'bi-credit-card',
            'message': 'bi-chat-dots',
            'reminder': 'bi-clock'
        };
        return icons[type] || 'bi-bell';
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);

        if (diffMins < 1) return 'Just now';
        if (diffMins < 60) return `${diffMins}m ago`;
        if (diffHours < 24) return `${diffHours}h ago`;
        if (diffDays < 7) return `${diffDays}d ago`;

        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    }
}

// Initialize notifications on page load
let notificationManager;
document.addEventListener('DOMContentLoaded', () => {
    notificationManager = new NotificationManager();
});
