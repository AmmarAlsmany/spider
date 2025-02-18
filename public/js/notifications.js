// Notifications handling
let csrfToken = '';

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

function updateCsrfToken(token) {
    csrfToken = token;
    // Update meta tag if it exists
    let metaTag = document.querySelector('meta[name="csrf-token"]');
    if (metaTag) {
        metaTag.content = token;
    } else {
        // Create meta tag if it doesn't exist
        metaTag = document.createElement('meta');
        metaTag.name = 'csrf-token';
        metaTag.content = token;
        document.head.appendChild(metaTag);
    }
}

function fetchNotifications() {
    fetch('/notifications', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            if (response.status === 401) {
                throw new Error('Unauthorized');
            }
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        // Update CSRF token if provided
        if (data.csrf_token) {
            updateCsrfToken(data.csrf_token);
        }

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
                        <div class="notify ${notification.type}">
                            <i class="${getNotificationIcon(notification.type)}"></i>
                        </div>
                        <div class="notify-details flex-grow-1">
                            <p class="notify-title mb-0">${notification.title}</p>
                            <small class="text-muted">${notification.message}</small>
                            <small class="text-muted d-block">${timeAgo}</small>
                        </div>
                    </div>
                </a>
            `;
        }).join('');

        // Initialize perfect scrollbar
        initializeNotificationsScrollbar();
    })
    .catch(error => {
        console.error('Error fetching notifications:', error);
        const container = document.getElementById('notifications-container');
        if (container) {
            container.innerHTML = `
                <div class="text-center p-4">
                    <i class='bx bx-error-circle fs-1 text-muted'></i>
                    <p class="text-muted mt-2">Unable to load notifications</p>
                </div>`;
        }
        // Hide notification count and mark all read button
        const notificationCount = document.getElementById('notification-count');
        const markAllRead = document.getElementById('mark-all-read');
        if (notificationCount) notificationCount.style.display = 'none';
        if (markAllRead) markAllRead.style.display = 'none';
    });
}

function getNotificationIcon(type) {
    switch (type) {
        case 'success': return 'bx bx-check-circle';
        case 'warning': return 'bx bx-error';
        case 'danger': return 'bx bx-x-circle';
        default: return 'bx bx-bell';
    }
}

function formatTimeAgo(date) {
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);
    
    if (diffInSeconds < 60) return 'Just now';
    if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + ' minutes ago';
    if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + ' hours ago';
    return Math.floor(diffInSeconds / 86400) + ' days ago';
}

function handleNotificationClick(event, id, url) {
    if (!url || url === 'javascript:void(0)') {
        event.preventDefault();
    }
    markAsRead(id);
}

function markAsRead(id) {
    fetch(`/notifications/mark-as-read/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(() => fetchNotifications())
    .catch(error => console.error('Error marking notification as read:', error));
}

function markAllAsRead() {
    fetch('/notifications/mark-all-as-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(() => fetchNotifications())
    .catch(error => console.error('Error marking all notifications as read:', error));
}

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', () => {
    fetchNotifications();
    // Initialize scrollbar
    initializeNotificationsScrollbar();
    
    // Refresh notifications every minute
    setInterval(fetchNotifications, 60000);
});
