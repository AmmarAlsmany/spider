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
                    markAsRead(notification.id);
                    window.location.href = notification.url;
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

// Helper functions (duplicated from notifications.js for independence)
function getNotificationIcon(type) {
    switch (type) {
        case 'success': return 'bx bx-check-circle';
        case 'warning': return 'bx bx-error';
        case 'danger': return 'bx bx-x-circle';
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