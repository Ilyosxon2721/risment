/**
 * Push Notification Subscription Manager
 * Handles subscribing/unsubscribing to Web Push notifications
 */

// VAPID public key will be injected from a meta tag
function getVapidPublicKey() {
    const meta = document.querySelector('meta[name="vapid-public-key"]');
    return meta ? meta.content : null;
}

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

async function subscribeToPush() {
    const vapidKey = getVapidPublicKey();
    if (!vapidKey) {
        console.log('[Push] No VAPID key found');
        return null;
    }

    const registration = await navigator.serviceWorker.ready;

    // Check existing subscription
    let subscription = await registration.pushManager.getSubscription();
    if (subscription) {
        return subscription;
    }

    // Create new subscription
    try {
        subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(vapidKey),
        });

        // Send subscription to server
        await sendSubscriptionToServer(subscription);

        return subscription;
    } catch (error) {
        console.error('[Push] Subscription failed:', error);
        return null;
    }
}

async function unsubscribeFromPush() {
    const registration = await navigator.serviceWorker.ready;
    const subscription = await registration.pushManager.getSubscription();

    if (subscription) {
        const endpoint = subscription.endpoint;
        await subscription.unsubscribe();

        // Remove from server
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            await fetch('/api/push-subscriptions', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ endpoint }),
                credentials: 'same-origin',
            });
        } catch (e) {
            console.error('[Push] Failed to remove subscription from server:', e);
        }
    }
}

async function sendSubscriptionToServer(subscription) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const key = subscription.getKey('p256dh');
    const auth = subscription.getKey('auth');

    const response = await fetch('/api/push-subscriptions', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken || '',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({
            endpoint: subscription.endpoint,
            keys: {
                p256dh: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : '',
                auth: auth ? btoa(String.fromCharCode.apply(null, new Uint8Array(auth))) : '',
            },
            content_encoding: (PushManager.supportedContentEncodings || ['aesgcm'])[0],
        }),
        credentials: 'same-origin',
    });

    if (!response.ok) {
        throw new Error('Failed to save subscription on server');
    }
}

async function requestPermissionAndSubscribe() {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        console.log('[Push] Push notifications not supported');
        return;
    }

    if (Notification.permission === 'granted') {
        await subscribeToPush();
        return;
    }

    if (Notification.permission === 'denied') {
        console.log('[Push] Notifications denied by user');
        return;
    }

    // Ask permission
    const permission = await Notification.requestPermission();
    if (permission === 'granted') {
        await subscribeToPush();

        // Analytics
        if (typeof gtag === 'function') {
            gtag('event', 'pwa_push_subscribed');
        }
    }
}

export function initPushNotifications() {
    // Only init on cabinet/manager pages (not public pages)
    const isCabinet = window.location.pathname.startsWith('/cabinet');
    const isManager = window.location.pathname.startsWith('/manager');
    if (!isCabinet && !isManager) return;

    // Don't ask immediately - wait a bit after page load
    setTimeout(() => {
        requestPermissionAndSubscribe();
    }, 5000);
}

// Export for manual control
window.RismentPush = {
    subscribe: subscribeToPush,
    unsubscribe: unsubscribeFromPush,
    requestPermission: requestPermissionAndSubscribe,
};
