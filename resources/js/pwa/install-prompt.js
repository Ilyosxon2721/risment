/**
 * PWA Install Prompt Handler
 * Shows a custom install banner after the user's 2nd visit to cabinet
 */

let deferredPrompt = null;

export function initInstallPrompt() {
    // Don't show if already installed
    if (window.matchMedia('(display-mode: standalone)').matches) return;
    if (window.navigator.standalone === true) return;

    // Check if dismissed recently (30 days)
    const dismissed = localStorage.getItem('pwa-install-dismissed');
    if (dismissed) {
        const dismissedAt = new Date(dismissed);
        const daysSince = (Date.now() - dismissedAt.getTime()) / (1000 * 60 * 60 * 24);
        if (daysSince < 30) return;
    }

    // Track visits
    let visits = parseInt(localStorage.getItem('pwa-visits') || '0', 10);
    visits++;
    localStorage.setItem('pwa-visits', visits.toString());

    // Show after 2nd visit
    if (visits < 2) return;

    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        showInstallBanner();
    });
}

function showInstallBanner() {
    // Don't show duplicate
    if (document.getElementById('pwa-install-banner')) return;

    const locale = document.documentElement.lang || localStorage.getItem('locale') || 'ru';

    const messages = {
        ru: {
            text: 'Установите Risment для быстрого доступа',
            install: 'Установить',
            later: 'Позже',
        },
        uz: {
            text: "Tez kirish uchun Risment-ni o'rnating",
            install: "O'rnatish",
            later: 'Keyinroq',
        },
        en: {
            text: 'Install Risment for quick access',
            install: 'Install',
            later: 'Later',
        },
    };

    const msg = messages[locale] || messages.ru;

    const banner = document.createElement('div');
    banner.id = 'pwa-install-banner';
    banner.innerHTML = `
        <div style="position:fixed;bottom:0;left:0;right:0;z-index:9999;padding:12px 16px;background:#1a1a2e;border-top:2px solid #CB4FE4;display:flex;align-items:center;justify-content:space-between;gap:12px;font-family:Inter,sans-serif;box-shadow:0 -4px 20px rgba(0,0,0,0.3);">
            <div style="display:flex;align-items:center;gap:12px;flex:1;min-width:0;">
                <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#CB4FE4,#8E2BC6);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <span style="color:white;font-weight:bold;font-size:20px;">R</span>
                </div>
                <span style="color:white;font-size:14px;line-height:1.3;">${msg.text}</span>
            </div>
            <div style="display:flex;gap:8px;flex-shrink:0;">
                <button id="pwa-install-later" style="padding:8px 16px;border:1px solid rgba(255,255,255,0.2);border-radius:8px;background:transparent;color:rgba(255,255,255,0.7);font-size:13px;cursor:pointer;">${msg.later}</button>
                <button id="pwa-install-btn" style="padding:8px 16px;border:none;border-radius:8px;background:linear-gradient(135deg,#CB4FE4,#8E2BC6);color:white;font-size:13px;font-weight:600;cursor:pointer;">${msg.install}</button>
            </div>
        </div>
    `;

    document.body.appendChild(banner);

    document.getElementById('pwa-install-btn').addEventListener('click', async () => {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            deferredPrompt = null;

            // Analytics event
            if (typeof gtag === 'function') {
                gtag('event', outcome === 'accepted' ? 'pwa_install_accepted' : 'pwa_install_dismissed');
            }
        }
        removeBanner();
    });

    document.getElementById('pwa-install-later').addEventListener('click', () => {
        localStorage.setItem('pwa-install-dismissed', new Date().toISOString());
        if (typeof gtag === 'function') {
            gtag('event', 'pwa_install_dismissed');
        }
        removeBanner();
    });

    // Analytics: prompt shown
    if (typeof gtag === 'function') {
        gtag('event', 'pwa_install_prompt_shown');
    }
}

function removeBanner() {
    const banner = document.getElementById('pwa-install-banner');
    if (banner) banner.remove();
}
