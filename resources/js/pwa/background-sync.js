/**
 * Background Sync for offline form submissions
 * Queues failed requests in IndexedDB and replays when online
 */

const DB_NAME = 'risment-offline';
const DB_VERSION = 1;
const STORE_NAME = 'pending-requests';

function openDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(DB_NAME, DB_VERSION);
        request.onupgradeneeded = (e) => {
            const db = e.target.result;
            if (!db.objectStoreNames.contains(STORE_NAME)) {
                db.createObjectStore(STORE_NAME, { keyPath: 'id', autoIncrement: true });
            }
        };
        request.onsuccess = (e) => resolve(e.target.result);
        request.onerror = (e) => reject(e.target.error);
    });
}

async function saveRequest(url, method, body, headers) {
    const db = await openDB();
    return new Promise((resolve, reject) => {
        const tx = db.transaction(STORE_NAME, 'readwrite');
        const store = tx.objectStore(STORE_NAME);
        store.add({
            url,
            method,
            body,
            headers: Object.fromEntries(
                Object.entries(headers).filter(([k]) => k.toLowerCase() !== 'cookie')
            ),
            timestamp: Date.now(),
        });
        tx.oncomplete = () => resolve();
        tx.onerror = (e) => reject(e.target.error);
    });
}

async function getPendingRequests() {
    const db = await openDB();
    return new Promise((resolve, reject) => {
        const tx = db.transaction(STORE_NAME, 'readonly');
        const store = tx.objectStore(STORE_NAME);
        const request = store.getAll();
        request.onsuccess = () => resolve(request.result);
        request.onerror = (e) => reject(e.target.error);
    });
}

async function deleteRequest(id) {
    const db = await openDB();
    return new Promise((resolve, reject) => {
        const tx = db.transaction(STORE_NAME, 'readwrite');
        const store = tx.objectStore(STORE_NAME);
        store.delete(id);
        tx.oncomplete = () => resolve();
        tx.onerror = (e) => reject(e.target.error);
    });
}

async function replayPendingRequests() {
    const pending = await getPendingRequests();
    if (pending.length === 0) return;

    const locale = document.documentElement.lang || 'ru';
    const messages = {
        ru: { syncing: 'Синхронизация данных...', success: 'Данные синхронизированы', error: 'Ошибка синхронизации' },
        uz: { syncing: 'Ma\'lumotlar sinxronlanmoqda...', success: 'Ma\'lumotlar sinxronlandi', error: 'Sinxronlash xatosi' },
        en: { syncing: 'Syncing data...', success: 'Data synced', error: 'Sync error' },
    };
    const msg = messages[locale] || messages.ru;

    showSyncNotification(msg.syncing, 'info');

    // Get fresh CSRF token
    let csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!csrfToken) {
        try {
            const resp = await fetch('/sanctum/csrf-cookie', { credentials: 'same-origin' });
            if (resp.ok) {
                csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            }
        } catch (e) {
            // Fall back - token might still work
        }
    }

    let successCount = 0;
    let errorCount = 0;

    for (const req of pending) {
        try {
            const headers = { ...req.headers };
            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken;
            }

            const response = await fetch(req.url, {
                method: req.method,
                headers,
                body: req.body,
                credentials: 'same-origin',
            });

            if (response.ok || response.status === 302 || response.status === 419) {
                await deleteRequest(req.id);
                successCount++;
            } else {
                errorCount++;
            }
        } catch (e) {
            errorCount++;
        }
    }

    if (successCount > 0) {
        showSyncNotification(msg.success, 'success');
    }
    if (errorCount > 0) {
        showSyncNotification(msg.error, 'error');
    }
}

function showSyncNotification(text, type) {
    // Remove existing
    const existing = document.getElementById('sync-notification');
    if (existing) existing.remove();

    const colors = {
        info: { bg: '#3b82f6', text: '#fff' },
        success: { bg: '#22c55e', text: '#fff' },
        error: { bg: '#ef4444', text: '#fff' },
    };
    const color = colors[type] || colors.info;

    const el = document.createElement('div');
    el.id = 'sync-notification';
    el.style.cssText = `position:fixed;top:16px;right:16px;z-index:10001;padding:12px 20px;border-radius:10px;background:${color.bg};color:${color.text};font-size:14px;font-family:Inter,sans-serif;font-weight:500;box-shadow:0 4px 12px rgba(0,0,0,0.15);transition:opacity 0.3s,transform 0.3s;`;
    el.textContent = text;
    document.body.appendChild(el);

    setTimeout(() => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(-10px)';
        setTimeout(() => el.remove(), 300);
    }, 3000);
}

export function initBackgroundSync() {
    // Replay pending requests when coming back online
    window.addEventListener('online', () => {
        setTimeout(() => replayPendingRequests(), 1000);
    });

    // Also try on page load if online
    if (navigator.onLine) {
        getPendingRequests().then((pending) => {
            if (pending.length > 0) {
                replayPendingRequests();
            }
        });
    }
}

// Export for use in forms
window.RismentOfflineQueue = {
    save: saveRequest,
    replay: replayPendingRequests,
    getPending: getPendingRequests,
};
