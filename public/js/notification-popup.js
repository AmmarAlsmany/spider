// Notification Popup for New Login
document.addEventListener('DOMContentLoaded', function () {
    // Check if user has just logged in and has unread notifications
    const hasNewLogin = document.body.getAttribute('data-new-login') === 'true';

    if (hasNewLogin) {
        // Fetch notifications for the popup with a slight delay for better UX
        setTimeout(() => {
            fetchNotificationsForPopup();
        }, 800); // Delay to allow page to load first
    }
});

function fetchNotificationsForPopup() {
    // Show loading indicator
    const loadingPopup = createLoadingPopup();
    document.body.appendChild(loadingPopup);

    fetch('/notifications', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Remove loading indicator
            if (document.body.contains(loadingPopup)) {
                loadingPopup.remove();
            }

            // Filter unread notifications
            const unreadNotifications = data.notifications.filter(n => !n.read);

            if (unreadNotifications.length > 0) {
                // Show popup with unread notifications
                showNotificationPopup(unreadNotifications);
            }
        })
        .catch(error => {
            console.error('Error fetching notifications for popup:', error);
            // Remove loading indicator on error
            if (document.body.contains(loadingPopup)) {
                loadingPopup.remove();
            }
        });
}

function createLoadingPopup() {
    const loadingContainer = document.createElement('div');
    loadingContainer.className = 'notification-popup-container';
    loadingContainer.style.opacity = '1';
    loadingContainer.style.transform = 'translateX(0)';

    const loadingContent = document.createElement('div');
    loadingContent.className = 'notification-popup-content';
    loadingContent.innerHTML = `
        <div class="notification-popup-header">
            <h4>Loading notifications</h4>
        </div>
        <div style="padding: 20px; text-align: center;">
            <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p style="margin-top: 10px; font-size: 14px; color: #6c757d;">Fetching your notifications...</p>
        </div>
    `;

    loadingContainer.appendChild(loadingContent);
    return loadingContainer;
}

function showNotificationPopup(notifications) {
    // Create popup container
    const popupContainer = document.createElement('div');
    popupContainer.className = 'notification-popup-container';

    // Create popup content
    const popupContent = document.createElement('div');
    popupContent.className = 'notification-popup-content';

    // Create header with notification count and badge
    const header = document.createElement('div');
    header.className = 'notification-popup-header';

    const notificationCount = notifications.length;
    const notificationText = notificationCount === 1 ? 'notification' : 'notifications';

    header.innerHTML = `
        <h4>You have ${notificationCount} new ${notificationText}</h4>
        <button class="notification-popup-close" aria-label="Close">&times;</button>
    `;

    // Create notification list
    const notificationList = document.createElement('div');
    notificationList.className = 'notification-popup-list';

    // Add notifications to the list (limit to 5 most recent)
    const recentNotifications = notifications.slice(0, 5);

    if (recentNotifications.length === 0) {
        // Show empty state if no notifications (shouldn't happen but just in case)
        notificationList.innerHTML = `
            <div class="notification-popup-empty">
                <i class="bx bx-bell-off"></i>
                <p>No new notifications</p>
            </div>
        `;
    } else {
        // Sort notifications by date (newest first)
        recentNotifications.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

        // Add each notification with staggered animation delay
        recentNotifications.forEach((notification, index) => {
            const notificationItem = document.createElement('div');
            notificationItem.className = `notification-popup-item ${notification.priority} new`;
            notificationItem.style.animationDelay = `${index * 0.15}s`;

            notificationItem.innerHTML = `
                <div class="notification-popup-icon">
                    <i class="${getNotificationIcon(notification.type)}"></i>
                </div>
                <div class="notification-popup-details">
                    <h5>${notification.title}</h5>
                    <p>${notification.message}</p>
                    <small>${formatTimeAgo(new Date(notification.created_at))}</small>
                </div>
            `;

            // Add click event to navigate to notification URL if available
            if (notification.url && notification.url !== '#') {
                notificationItem.addEventListener('click', function () {
                    handleNotificationNavigation(notification.id, notification.url);
                });
                notificationItem.style.cursor = 'pointer';

                // Add tooltip to indicate it's clickable
                notificationItem.setAttribute('title', 'Click to view details');
            }

            notificationList.appendChild(notificationItem);
        });

        // Add "more" message if there are more than 5 notifications
        if (notifications.length > 5) {
            const moreNotifications = document.createElement('div');
            moreNotifications.className = 'notification-popup-item';
            moreNotifications.style.textAlign = 'center';
            moreNotifications.style.padding = '10px';
            moreNotifications.style.color = '#6c757d';
            moreNotifications.style.fontSize = '13px';
            moreNotifications.innerHTML = `
                <div style="width: 100%;">
                    + ${notifications.length - 5} more notifications
                </div>
            `;
            notificationList.appendChild(moreNotifications);
        }
    }

    // Create footer with action buttons
    const footer = document.createElement('div');
    footer.className = 'notification-popup-footer';

    // Add "View All" button
    const viewAllButton = document.createElement('button');
    viewAllButton.className = 'notification-popup-view-all';
    viewAllButton.innerHTML = '<i class="bx bx-list-ul" style="margin-right: 5px;"></i> View All';
    viewAllButton.addEventListener('click', function () {
        // Close popup and open notification dropdown
        closePopupWithAnimation();
        setTimeout(() => {
            document.querySelector('.dropdown-toggle[data-bs-toggle="dropdown"]').click();
        }, 300);
    });

    // Add "Mark All as Read" button
    const markAllButton = document.createElement('button');
    markAllButton.className = 'notification-popup-mark-all';
    markAllButton.innerHTML = '<i class="bx bx-check-double" style="margin-right: 5px;"></i> Mark All as Read';
    markAllButton.addEventListener('click', function () {
        markAllAsRead();
        closePopupWithAnimation();
    });

    footer.appendChild(viewAllButton);
    footer.appendChild(markAllButton);

    // Assemble popup
    popupContent.appendChild(header);
    popupContent.appendChild(notificationList);
    popupContent.appendChild(footer);
    popupContainer.appendChild(popupContent);

    // Add close button functionality
    popupContainer.querySelector('.notification-popup-close').addEventListener('click', function () {
        closePopupWithAnimation();
    });

    // Add popup to body
    document.body.appendChild(popupContainer);

    // Add animation class after a small delay to trigger animation
    setTimeout(() => {
        popupContainer.classList.add('show');
    }, 100);

    // Auto close after 15 seconds (increased from 10 for better readability)
    setTimeout(() => {
        if (document.body.contains(popupContainer)) {
            closePopupWithAnimation();
        }
    }, 15000);

    // Function to close popup with animation
    function closePopupWithAnimation() {
        popupContainer.classList.remove('show');
        setTimeout(() => {
            if (document.body.contains(popupContainer)) {
                popupContainer.remove();
            }
        }, 300); // Wait for animation to complete
    }

    // Add escape key listener to close popup
    const escKeyHandler = function (e) {
        if (e.key === 'Escape') {
            closePopupWithAnimation();
            document.removeEventListener('keydown', escKeyHandler);
        }
    };
    document.addEventListener('keydown', escKeyHandler);
}

// Real-time notification system
class NotificationSystem {
    constructor(options = {}) {
        this.options = {
            toastContainer: 'toast-container',
            autoHide: true,
            delay: 5000,
            position: 'bottom-right',
            maxNotifications: 5,
            sounds: {
                info: null,
                success: null,
                warning: null,
                error: null
            },
            ...options
        };

        this.notificationCount = 0;
        this.soundsLoaded = false;

        this.init();
    }

    init() {
        // Create toast container if it doesn't exist
        if (!document.getElementById(this.options.toastContainer)) {
            const container = document.createElement('div');
            container.id = this.options.toastContainer;
            container.className = `position-fixed ${this.getPositionClasses()} p-3`;
            container.style.zIndex = '1090';
            document.body.appendChild(container);
        }

        // Load sound effects if provided
        this.loadSounds();
    }

    getPositionClasses() {
        switch (this.options.position) {
            case 'top-right': return 'top-0 end-0';
            case 'top-left': return 'top-0 start-0';
            case 'bottom-left': return 'bottom-0 start-0';
            case 'bottom-right': return 'bottom-0 end-0';
            case 'top-center': return 'top-0 start-50 translate-middle-x';
            case 'bottom-center': return 'bottom-0 start-50 translate-middle-x';
            default: return 'bottom-0 end-0';
        }
    }

    loadSounds() {
        if (!this.soundsLoaded) {
            for (const [type, path] of Object.entries(this.options.sounds)) {
                if (path) {
                    try {
                        this.options.sounds[type] = new Audio(path);
                    } catch (e) {
                        console.error(`Failed to load sound for ${type} notifications`, e);
                    }
                }
            }
            this.soundsLoaded = true;
        }
    }

    show(options) {
        this.notificationCount++;

        const {
            type = 'info',
            title = '',
            message = '',
            autohide = this.options.autoHide,
            delay = this.options.delay,
            playSound = true,
            url = null
        } = options;

        // Create toast element
        const toastId = `toast-${Date.now()}-${this.notificationCount}`;
        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = `toast mb-2 notification-toast notification-${type}`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        toast.setAttribute('data-bs-autohide', autohide.toString());
        toast.setAttribute('data-bs-delay', delay.toString());

        // Make it clickable if URL is provided
        if (url && url !== '#') {
            toast.style.cursor = 'pointer';
            toast.setAttribute('title', 'Click to view details');
            toast.setAttribute('data-url', url);
            toast.addEventListener('click', function (e) {
                // Don't navigate if close button was clicked
                if (e.target.classList.contains('btn-close') ||
                    e.target.closest('.btn-close')) {
                    return;
                }

                // Use the common navigation handler (without marking as read for toasts)
                handleNotificationNavigation(null, this.getAttribute('data-url'), false);
            });
        }

        // Add toast content
        const iconClass = this.getIconClass(type);
        const bgClass = this.getBgClass(type);

        toast.innerHTML = `
            <div class="toast-header ${bgClass}">
                <i class="${iconClass} me-2"></i>
                <strong class="me-auto">${title}</strong>
                <small>${this.getTimeText()}</small>
                <button type="button" class="btn-close ${type === 'warning' ? '' : 'btn-close-white'}" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        `;

        // Add to container
        const container = document.getElementById(this.options.toastContainer);
        container.appendChild(toast);

        // Initialize toast
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        // Play sound if enabled
        if (playSound && this.options.sounds[type]) {
            try {
                this.options.sounds[type].play();
            } catch (e) {
                console.error('Failed to play notification sound', e);
            }
        }

        // Remove oldest toast if we have too many
        const toasts = container.querySelectorAll('.toast');
        if (toasts.length > this.options.maxNotifications) {
            container.removeChild(toasts[0]);
        }

        // Return toast for further manipulation
        return { id: toastId, element: toast, bsToast };
    }

    getIconClass(type) {
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

    getBgClass(type) {
        switch (type) {
            case 'success': return 'bg-success text-white';
            case 'warning': return 'bg-warning text-dark';
            case 'error': return 'bg-danger text-white';
            case 'info': return 'bg-info text-white';
            default: return 'bg-primary text-white';
        }
    }

    getTimeText() {
        return 'Just now';
    }

    success(title, message, options = {}) {
        return this.show({ type: 'success', title, message, ...options });
    }

    info(title, message, options = {}) {
        return this.show({ type: 'info', title, message, ...options });
    }

    warning(title, message, options = {}) {
        return this.show({ type: 'warning', title, message, ...options });
    }

    error(title, message, options = {}) {
        return this.show({ type: 'error', title, message, ...options });
    }

    message(title, message, options = {}) {
        return this.show({ type: 'message', title, message, ...options });
    }

    payment(title, message, options = {}) {
        return this.show({ type: 'payment', title, message, ...options });
    }

    update(title, message, options = {}) {
        return this.show({ type: 'update', title, message, ...options });
    }

    closeAll() {
        const container = document.getElementById(this.options.toastContainer);
        const toasts = container.querySelectorAll('.toast');
        toasts.forEach(toast => {
            const bsToast = bootstrap.Toast.getInstance(toast);
            if (bsToast) {
                bsToast.hide();
            }
        });
    }
}

// Initialize notification system
let notifySystem;

document.addEventListener('DOMContentLoaded', () => {
    notifySystem = new NotificationSystem({
        position: 'bottom-right',
        delay: 5000,
        maxNotifications: 5,
        sounds: {
            error: null,
            success: null
        }
    });

    // Expose globally if needed
    window.notify = notifySystem;
});

// Helper functions (duplicated from notifications.js for independence)
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
    if (diffInSeconds < 3600) {
        const minutes = Math.floor(diffInSeconds / 60);
        return `${minutes} ${minutes === 1 ? 'minute' : 'minutes'} ago`;
    }
    if (diffInSeconds < 86400) {
        const hours = Math.floor(diffInSeconds / 3600);
        return `${hours} ${hours === 1 ? 'hour' : 'hours'} ago`;
    }
    if (diffInSeconds < 604800) {
        const days = Math.floor(diffInSeconds / 86400);
        return `${days} ${days === 1 ? 'day' : 'days'} ago`;
    }

    // Format date for older notifications
    return date.toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function markAsRead(id) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

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
            if (!response.ok) {
                throw new Error('Failed to mark notification as read');
            }
            // Update notification counter in the UI if it exists
            updateNotificationCounter();
        })
        .catch(error => console.error('Error marking notification as read:', error));
}

function markAllAsRead() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

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
            if (!response.ok) {
                throw new Error('Failed to mark all notifications as read');
            }
            // Update notification counter in the UI if it exists
            updateNotificationCounter();
        })
        .catch(error => console.error('Error marking all notifications as read:', error));
}

// Update notification counter in the header if it exists
function updateNotificationCounter() {
    const counter = document.querySelector('.notification-count');
    if (counter) {
        counter.textContent = '0';
        counter.style.display = 'none';
    }
}

/**
 * Global utility function to handle notification clicks
 * This ensures consistent behavior across all notification types
 * 
 * @param {string} id - The notification ID
 * @param {string} url - The URL to navigate to
 * @param {boolean} markRead - Whether to mark the notification as read (default: true)
 */
function handleNotificationNavigation(id, url, markRead = true) {
    // If we should mark as read and ID is provided
    if (markRead && id) {
        // Mark the notification as read
        markAsRead(id);
    }

    // Then navigate to the URL if it's provided and valid
    if (url && url !== 'javascript:void(0)' && url !== '#') {
        // Wait a tiny bit to allow the notification to be marked as read first
        setTimeout(() => {
            window.location.href = url;
        }, 100);
        return true;
    }

    return false;
}

// Update the showNotificationPopup function to use the new utility function
document.addEventListener('DOMContentLoaded', function () {
    // Find existing notifications and add click handlers
    const existingNotifications = document.querySelectorAll('.notification-item');
    existingNotifications.forEach(notification => {
        const id = notification.getAttribute('data-id');
        const url = notification.getAttribute('href');

        if (id && url) {
            notification.addEventListener('click', function (e) {
                e.preventDefault();
                handleNotificationNavigation(id, url);
            });
        }
    });
}); 