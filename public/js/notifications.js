// Notifications handling
function initializeNotificationsScrollbar() {
    const container = document.querySelector('.header-notifications-list');
    if (container) {
        new PerfectScrollbar(container, {
            wheelSpeed: 2,
            wheelPropagation: false,
            minScrollbarLength: 20
        });
    }
}

function fetchNotifications() {
    fetch('/notifications')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('notifications-container');
            const unreadCount = document.getElementById('unread-count');
            const notificationCount = document.getElementById('notification-count');
            const markAllRead = document.getElementById('mark-all-read');
            
            // Count unread notifications
            const unreadNotifications = data.notifications.filter(n => !n.read).length;
            
            if (unreadNotifications > 0) {
                notificationCount.style.display = 'inline-block';
                notificationCount.textContent = unreadNotifications;
                unreadCount.textContent = unreadNotifications + ' New';
                markAllRead.style.display = 'block';
            } else {
                notificationCount.style.display = 'none';
                unreadCount.textContent = '0 New';
                markAllRead.style.display = 'none';
            }

            if (data.notifications.length === 0) {
                container.innerHTML = `
                    <div class="text-center p-3">
                        <p class="text-muted">No notifications</p>
                    </div>`;
                return;
            }

            container.innerHTML = data.notifications.map(notification => {
                const notificationUrl = notification.url || 'javascript:void(0)';
                return `
                    <a class="dropdown-item" href="${notificationUrl}" onclick="handleNotificationClick(event, '${notification.id}', '${notification.url}')">
                        <div class="d-flex align-items-center">
                            <div class="notify ${notification.type}">
                                <i class="bx bx-${notification.type === 'error' ? 'error' : (notification.type === 'warning' ? 'warning' : 'info-circle')}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="msg-name">
                                    ${notification.title}
                                    ${notification.priority === 'high' ? '<span class="text-danger">(High Priority)</span>' : ''}
                                    <span class="msg-time float-end">${notification.created_at}</span>
                                </h6>
                                <p class="msg-info">${notification.message}</p>
                            </div>
                        </div>
                    </a>
                `;
            }).join('');

            // Initialize PerfectScrollbar after content is loaded
            setTimeout(initializeNotificationsScrollbar, 100);
        })
        .catch(error => {
            console.error('Error fetching notifications:', error);
            document.getElementById('notifications-container').innerHTML = `
                <div class="text-center p-3">
                    <p class="text-muted">Error loading notifications</p>
                </div>`;
        });
}

function handleNotificationClick(event, id, url) {
    // Prevent the default anchor behavior
    event.preventDefault();
    
    // First mark as read
    markAsRead(id).then(() => {
        // Then navigate if URL is provided
        if (url && url !== 'undefined' && url !== 'null') {
            window.location.href = url;
        }
    });
}

function markAsRead(id) {
    return fetch(`/notifications/${id}/mark-as-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .catch(error => {
        console.error('Error marking notification as read:', error);
        return Promise.reject(error);
    });
}

function markAllAsRead() {
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(() => fetchNotifications())
    .catch(error => console.error('Error marking all notifications as read:', error));
}

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', () => {
    fetchNotifications();
    // Initialize scrollbar
    initializeNotificationsScrollbar();
    // Refresh notifications every 30 seconds
    setInterval(fetchNotifications, 30000);
});
