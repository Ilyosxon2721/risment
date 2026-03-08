/**
 * Offline Indicator
 * Shows a banner when the user loses network connection
 */

export function initOfflineIndicator() {
    let indicator = null;

    function showOffline() {
        if (indicator) return;

        const locale = document.documentElement.lang || localStorage.getItem('locale') || 'ru';
        const messages = {
            ru: 'Нет подключения к интернету',
            uz: "Internetga ulanish yo'q",
            en: 'No internet connection',
        };

        indicator = document.createElement('div');
        indicator.id = 'offline-indicator';
        indicator.style.cssText = 'position:fixed;top:0;left:0;right:0;z-index:10000;padding:8px 16px;background:#ef4444;color:white;text-align:center;font-size:13px;font-family:Inter,sans-serif;font-weight:500;transition:transform 0.3s ease;';
        indicator.textContent = messages[locale] || messages.ru;
        document.body.appendChild(indicator);

        // Push body content down
        document.body.style.marginTop = '36px';
    }

    function hideOffline() {
        if (indicator) {
            indicator.remove();
            indicator = null;
            document.body.style.marginTop = '';
        }
    }

    window.addEventListener('offline', showOffline);
    window.addEventListener('online', hideOffline);

    // Check initial state
    if (!navigator.onLine) showOffline();
}
