/**
 * PWA Analytics Events
 * Tracks PWA-specific events in Google Analytics and Yandex Metrika
 */

function trackEvent(eventName, params = {}) {
    // Google Analytics (gtag)
    if (typeof gtag === 'function') {
        gtag('event', eventName, params);
    }

    // Yandex Metrika
    if (typeof ym === 'function') {
        const metrikaId = document.querySelector('meta[name="yandex-metrika-id"]')?.content;
        if (metrikaId) {
            ym(parseInt(metrikaId), 'reachGoal', eventName, params);
        }
    }
}

export function initPWAAnalytics() {
    // Track if launched in standalone mode
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches
        || window.navigator.standalone === true;

    if (isStandalone) {
        trackEvent('pwa_launched_standalone');
    }

    // Track install prompt events (already handled in install-prompt.js via gtag)
    // Here we add Yandex Metrika support

    // Track offline page shown
    window.addEventListener('offline', () => {
        trackEvent('pwa_offline');
    });

    window.addEventListener('online', () => {
        trackEvent('pwa_online');
    });

    // Track SW update
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.ready.then((registration) => {
            registration.addEventListener('updatefound', () => {
                trackEvent('pwa_sw_update_found');
            });
        });
    }

    // Track app display mode changes
    window.matchMedia('(display-mode: standalone)').addEventListener('change', (e) => {
        if (e.matches) {
            trackEvent('pwa_entered_standalone');
        }
    });
}
