/**
 * Examples of how to use the notification system with URLs
 */

// Example 1: Show a notification that links to a specific page when clicked
function showClickableNotification() {
    // Use the global notify object to show a notification
    notify.info(
        'New Feature Available',
        'Click here to check out our latest feature update!',
        {
            url: '/features/new',
            delay: 10000 // Show for 10 seconds
        }
    );
}

// Example 2: Show a payment notification with a link to payment details
function showPaymentNotification(paymentId) {
    notify.payment(
        'Payment Received',
        'Your payment has been processed successfully.',
        {
            url: `/payments/details/${paymentId}`
        }
    );
}

// Example 3: Show an error notification with link to support
function showErrorWithSupportLink(errorMessage) {
    notify.error(
        'Error Occurred',
        errorMessage,
        {
            url: '/support/ticket/new',
            delay: 15000 // Keep it longer since it's an error
        }
    );
}

// Example of triggering notifications from backend events
// This could be called when receiving data from an API or WebSocket
function handleServerNotification(notification) {
    // Map the server notification type to our client-side notification types
    const notificationType = notification.type || 'info';

    // Create a notification using the appropriate method based on type
    switch (notificationType) {
        case 'success':
            notify.success(notification.title, notification.message, {
                url: notification.url
            });
            break;

        case 'error':
            notify.error(notification.title, notification.message, {
                url: notification.url
            });
            break;

        case 'warning':
            notify.warning(notification.title, notification.message, {
                url: notification.url
            });
            break;

        case 'payment':
            notify.payment(notification.title, notification.message, {
                url: notification.url
            });
            break;

        case 'message':
            notify.message(notification.title, notification.message, {
                url: notification.url
            });
            break;

        default:
            notify.info(notification.title, notification.message, {
                url: notification.url
            });
    }
}

// Example usage in a page
document.addEventListener('DOMContentLoaded', function () {
    // Attach to demo buttons if they exist
    const demoBtn = document.getElementById('demo-notification');
    if (demoBtn) {
        demoBtn.addEventListener('click', function () {
            showClickableNotification();
        });
    }
}); 