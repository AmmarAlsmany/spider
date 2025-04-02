// Notifications handling
let csrfToken = '';
let currentPage = 1;
let hasMoreNotifications = true;
let isLoadingNotifications = false;
let currentFilter = null;

function initializeNotificationsScrollbar() {
    const container = document.querySelector('.header-notifications-list');
    if (container) {
        new PerfectScrollbar(container, {
            wheelSpeed: 2,
            wheelPropagation: true,
            minScrollbarLength: 20
        });

        // Add scroll event listener to load more notifications
        container.addEventListener('scroll', function () {
            if (hasMoreNotifications && !isLoadingNotifications) {
                // If scrolled to bottom (with some margin)
                const scrollPosition = container.scrollTop + container.clientHeight;
                const scrollHeight = container.scrollHeight;

                if (scrollPosition >= scrollHeight - 100) {
                    loadMoreNotifications();
                }
            }
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

function fetchNotifications(page = 1, limit = 10, category = null) {
    if (isLoadingNotifications) return;

    isLoadingNotifications = true;
    currentPage = page;
    currentFilter = category;

    // Show loading indicator if first page
    if (page === 1) {
        const container = document.getElementById('notifications-container');
        if (container) {
            container.innerHTML = `
                <div class="p-3 text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading notifications...</p>
                </div>`;
        }
    } else {
        // Add loading indicator at the bottom for subsequent pages
        const container = document.getElementById('notifications-container');
        if (container) {
            const loadingIndicator = document.createElement('div');
            loadingIndicator.id = 'notifications-loading';
            loadingIndicator.className = 'p-2 text-center';
            loadingIndicator.innerHTML = `
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mb-0 text-muted small">Loading more...</p>
            `;
            container.appendChild(loadingIndicator);
        }
    }

    // Build query parameters
    let queryParams = `?page=${page}&limit=${limit}`;
    if (category) {
        queryParams += `&category=${category}`;
    }

    fetch(`/notifications${queryParams}`, {
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

            // Remove loading indicator if it exists
            const loadingIndicator = document.getElementById('notifications-loading');
            if (loadingIndicator) {
                loadingIndicator.remove();
            }

            // Update hasMoreNotifications flag
            hasMoreNotifications = data.has_more;

            // Update unread counts
            if (data.unread_count > 0) {
                notificationCount.style.display = 'inline-block';
                notificationCount.textContent = data.unread_count;
                unreadCount.textContent = data.unread_count + ' New';
                markAllRead.style.display = 'flex';
            } else {
                notificationCount.style.display = 'none';
                unreadCount.textContent = '0 New';
                markAllRead.style.display = 'none';
            }

            if (data.notifications.length === 0 && page === 1) {
                container.innerHTML = `
                <div class="p-4 text-center">
                    <i class='bx bx-bell-off fs-1 text-muted'></i>
                    <p class="mt-2 text-muted">No notifications</p>
                </div>`;
                isLoadingNotifications = false;
                return;
            }

            // If it's the first page, replace all content
            if (page === 1) {
                container.innerHTML = '';
            }

            // Add filter tabs if on first page
            if (page === 1) {
                const filterTabs = document.createElement('div');
                filterTabs.className = 'px-3 pt-2 d-flex justify-content-between border-bottom';
                filterTabs.innerHTML = `
                <div class="notification-filter-tabs">
                    <button class="btn btn-sm ${category === null ? 'btn-primary' : 'btn-outline-primary'}" data-filter="all">All</button>
                    <button class="btn btn-sm ${category === 'info' ? 'btn-primary' : 'btn-outline-primary'}" data-filter="info">Info</button>
                    <button class="btn btn-sm ${category === 'success' ? 'btn-primary' : 'btn-outline-primary'}" data-filter="success">Success</button>
                    <button class="btn btn-sm ${category === 'warning' ? 'btn-primary' : 'btn-outline-primary'}" data-filter="warning">Warning</button>
                    <button class="btn btn-sm ${category === 'error' ? 'btn-primary' : 'btn-outline-primary'}" data-filter="error">Error</button>
                </div>
            `;
                container.appendChild(filterTabs);

                // Add event listeners to filter buttons
                filterTabs.querySelectorAll('button').forEach(button => {
                    button.addEventListener('click', function () {
                        const filter = this.getAttribute('data-filter');
                        fetchNotifications(1, 10, filter === 'all' ? null : filter);
                    });
                });
            }

            // Append new notifications
            data.notifications.forEach(notification => {
                const notificationElement = document.createElement('a');
                notificationElement.className = `dropdown-item notification-item ${!notification.read ? 'unread' : ''}`;
                notificationElement.href = notification.url || 'javascript:void(0)';
                notificationElement.setAttribute('data-id', notification.id);

                const notificationUrl = notification.url || 'javascript:void(0)';
                notificationElement.onclick = function (event) {
                    handleNotificationClick(event, notification.id, notification.url);
                };

                notificationElement.innerHTML = `
                <div class="d-flex align-items-center">
                    <div class="notify ${notification.type}">
                        <i class="${getNotificationIcon(notification.type)}"></i>
                    </div>
                    <div class="notify-details flex-grow-1">
                        <p class="mb-0 notify-title">
                            ${notification.title}
                            ${notification.priority === 'high' ? '<span class="badge bg-danger ms-1">High</span>' : ''}
                        </p>
                        <small class="text-muted">${notification.message}</small>
                        <small class="text-muted d-block">${formatTimeAgo(new Date(notification.created_at_raw))}</small>
                    </div>
                </div>
            `;

                container.appendChild(notificationElement);
            });

            // Initialize perfect scrollbar
            initializeNotificationsScrollbar();
            isLoadingNotifications = false;
        })
        .catch(error => {
            console.error('Error fetching notifications:', error);
            const container = document.getElementById('notifications-container');

            // Remove loading indicator if it exists
            const loadingIndicator = document.getElementById('notifications-loading');
            if (loadingIndicator) {
                loadingIndicator.remove();
            }

            if (container && page === 1) {
                container.innerHTML = `
                <div class="p-4 text-center">
                    <i class='bx bx-error-circle fs-1 text-muted'></i>
                    <p class="mt-2 text-muted">Unable to load notifications</p>
                </div>`;
            }
            // Hide notification count and mark all read button
            const notificationCount = document.getElementById('notification-count');
            const markAllRead = document.getElementById('mark-all-read');
            if (notificationCount) notificationCount.style.display = 'none';
            if (markAllRead) markAllRead.style.display = 'none';

            isLoadingNotifications = false;
        });
}

function loadMoreNotifications() {
    fetchNotifications(currentPage + 1, 10, currentFilter);
}

function getNotificationIcon(type) {
    switch (type) {
        case 'success': return 'bx bx-check-circle';
        case 'warning': return 'bx bx-error';
        case 'error': return 'bx bx-x-circle';
        case 'info': return 'bx bx-info-circle';
        case 'payment': return 'bx bx-credit-card';
        case 'message': return 'bx bx-message-detail';
        case 'update': return 'bx bx-refresh';
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
    console.log('Notification clicked:', id, url);
    // Mark notification as read
    markAsReadAndNavigate(id, url);
    
    // Prevent default only if URL is empty or javascript:void(0)
    if (!url || url === 'javascript:void(0)') {
        event.preventDefault();
    }
}

function markAsReadAndNavigate(id, url) {
    console.log('Marking notification as read and navigating to:', id, url);
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
    .then(response => {
        console.log('Response status:', response.status);
        if (response.ok) {
            return response.json();
        }
        throw new Error('Network response was not ok');
    })
    .then(data => {
        console.log('Response data:', data);
        if (data && data.redirect_url && data.redirect_url !== '#' && data.redirect_url !== '') {
            console.log('Navigating to URL:', data.redirect_url);
            // Navigate to the URL after marking as read
            window.location.href = data.redirect_url;
        } else {
            console.log('No valid redirect URL found in response');
            // Refresh notifications without redirecting
            return fetchNotifications();
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
        // Refresh notifications even on error
        return fetchNotifications();
    });
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
    console.log('Marking all notifications as read');
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
    .then(response => {
        console.log('Mark all as read response status:', response.status);
        if (response.ok) {
            return response.json();
        }
        throw new Error('Network response was not ok');
    })
    .then(data => {
        console.log('Mark all as read response data:', data);
        // Refresh notifications list
        return fetchNotifications();
    })
    .catch(error => {
        console.error('Error marking all notifications as read:', error);
        // Refresh notifications even on error
        return fetchNotifications();
    });
}

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', () => {
    fetchNotifications();
    // Initialize scrollbar
    initializeNotificationsScrollbar();

    // Refresh notifications every minute
    setInterval(fetchNotifications, 60000);
});
