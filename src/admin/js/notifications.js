// Admin Notifications System
class AdminNotificationManager {
    constructor() {
        this.badge = document.getElementById('notificationBadge');
        this.notificationsList = document.getElementById('notificationsList');
        this.refreshBtn = document.getElementById('refreshNotifications');

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

        // Refresh button
        if (this.refreshBtn) {
            this.refreshBtn.addEventListener('click', (e) => {
                e.stopPropagation(); // Prevent dropdown from closing
                this.fetchNotifications();
            });
        }

        // Add styles if not present
        if (!document.getElementById('admin-notification-styles')) {
            const style = document.createElement('style');
            style.id = 'admin-notification-styles';
            style.textContent = `
                .notification-item {
                    cursor: pointer;
                    transition: background-color 0.2s;
                }
                .notification-item:hover {
                    background-color: rgba(255, 255, 255, 0.05) !important;
                }
            `;
            document.head.appendChild(style);
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
            <li class="notification-item px-3 py-2 border-bottom border-secondary" 
                onclick="window.adminNotificationManager.handleNotificationClick(${notif.id}, '${notif.link}')">
                <div class="d-flex align-items-start">
                    <i class="bi ${this.getIcon(notif.type)} text-gold me-2 mt-1"></i>
                    <div class="flex-grow-1">
                        <h6 class="mb-0 text-light" style="font-size: 0.85rem;">${notif.title}</h6>
                        <p class="mb-1 text-muted small">${notif.message}</p>
                        <small class="text-muted" style="font-size: 0.7rem;">${notif.time_ago || 'Just now'}</small>
                    </div>
                </div>
            </li>
        `).join('');
    }

    async handleNotificationClick(id, link) {
        try {
            await fetch('api/mark_notification_read.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: id })
            });

            // Redirect after marking as read
            if (link) {
                window.location.href = link;
            } else {
                this.fetchNotifications(); // Refresh if no link
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
            if (link) window.location.href = link; // Redirect anyway
        }
    }

    getIcon(type) {
        const icons = {
            'booking': 'bi-calendar-check',
            'refund': 'bi-cash-coin',
            'message': 'bi-chat-dots'
        };
        return icons[type] || 'bi-bell';
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    window.adminNotificationManager = new AdminNotificationManager();
});
