// Notifications handling
function initializeNotificationsScrollbar() {
    const container = document.querySelector('.header-notifications-list');
    if (container) {
        new PerfectScrollbar(container, {
            wheelSpeed: 2,
            wheelPropagation: true,
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
                markAllRead.style.display = 'flex';
            } else {
                notificationCount.style.display = 'none';
                unreadCount.textContent = '0 New';
                markAllRead.style.display = 'none';
            }

            if (data.notifications.length === 0) {
                container.innerHTML = `
                    <div class="text-center p-4">
                        <i class='bx bx-bell-off fs-1 text-muted'></i>
                        <p class="text-muted mt-2">No notifications</p>
                    </div>`;
                return;
            }

            container.innerHTML = data.notifications.map(notification => {
                const notificationUrl = notification.url || 'javascript:void(0)';
                const timeAgo = formatTimeAgo(new Date(notification.created_at));
                return `
                    <a class="dropdown-item notification-item ${!notification.read ? 'unread' : ''}" 
                       href="${notificationUrl}" 
                       onclick="handleNotificationClick(event, '${notification.id}', '${notification.url}')">
                        <div class="d-flex align-items-center">
                            <div class="notify ${notification.type} animate__animated animate__fadeIn">
                                <i class="bx bx-${getNotificationIcon(notification.type)}"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="msg-name mb-1">
                                    ${notification.title}
                                    ${notification.priority === 'high' ? '<span class="badge bg-danger ms-2">High Priority</span>' : ''}
                                </h6>
                                <p class="msg-info mb-1">${notification.message}</p>
                                <small class="text-muted">${timeAgo}</small>
                            </div>
                            ${!notification.read ? '<span class="unread-indicator"></span>' : ''}
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
                <div class="text-center p-4">
                    <i class='bx bx-error-circle fs-1 text-danger'></i>
                    <p class="text-muted mt-2">Error loading notifications</p>
                </div>`;
        });
}

function getNotificationIcon(type) {
    switch (type) {
        case 'error': return 'error-circle';
        case 'warning': return 'warning';
        case 'success': return 'check-circle';
        default: return 'info-circle';
    }
}

function formatTimeAgo(date) {
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);
    const diffInMinutes = Math.floor(diffInSeconds / 60);
    const diffInHours = Math.floor(diffInMinutes / 60);
    const diffInDays = Math.floor(diffInHours / 24);

    if (diffInSeconds < 60) return 'Just now';
    if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
    if (diffInHours < 24) return `${diffInHours}h ago`;
    if (diffInDays < 7) return `${diffInDays}d ago`;
    return date.toLocaleDateString();
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
