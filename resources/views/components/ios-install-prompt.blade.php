{{-- iOS PWA Install Instructions --}}
<div id="ios-install-prompt" style="display:none;">
    <div style="position:fixed;bottom:0;left:0;right:0;z-index:9998;padding:16px;padding-bottom:calc(16px + env(safe-area-inset-bottom, 0px));background:white;border-top:2px solid #CB4FE4;box-shadow:0 -4px 20px rgba(0,0,0,0.1);font-family:Inter,sans-serif;">
        <div style="max-width:480px;margin:0 auto;">
            <div style="display:flex;align-items:flex-start;gap:12px;">
                <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#CB4FE4,#8E2BC6);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <span style="color:white;font-weight:bold;font-size:20px;">R</span>
                </div>
                <div style="flex:1;min-width:0;">
                    <div id="ios-prompt-title" style="font-weight:600;font-size:15px;color:#0B0B10;margin-bottom:4px;"></div>
                    <div id="ios-prompt-text" style="font-size:13px;color:#5E6278;line-height:1.5;"></div>
                </div>
                <button id="ios-prompt-close" style="width:28px;height:28px;border:none;background:none;color:#9ca3af;font-size:20px;cursor:pointer;flex-shrink:0;display:flex;align-items:center;justify-content:center;">&times;</button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    // Only show on iOS Safari, not in standalone mode
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    const isStandalone = window.navigator.standalone === true || window.matchMedia('(display-mode: standalone)').matches;
    const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);

    if (!isIOS || isStandalone || !isSafari) return;

    // Check if dismissed
    const dismissed = localStorage.getItem('ios-install-dismissed');
    if (dismissed) {
        const days = (Date.now() - new Date(dismissed).getTime()) / (1000 * 60 * 60 * 24);
        if (days < 30) return;
    }

    // Track visits
    let visits = parseInt(localStorage.getItem('ios-install-visits') || '0', 10);
    visits++;
    localStorage.setItem('ios-install-visits', visits.toString());
    if (visits < 2) return;

    const locale = document.documentElement.lang || localStorage.getItem('locale') || 'ru';

    const messages = {
        ru: {
            title: 'Установите Risment',
            text: 'Нажмите <svg style="display:inline;vertical-align:middle;width:18px;height:18px;" fill="none" stroke="#007AFF" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 00-2.25 2.25v9a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25H15m0-3l-3-3m0 0l-3 3m3-3v11.25"/></svg> «Поделиться», затем «На экран Домой»',
        },
        uz: {
            title: "Risment-ni o'rnating",
            text: '<svg style="display:inline;vertical-align:middle;width:18px;height:18px;" fill="none" stroke="#007AFF" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 00-2.25 2.25v9a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25H15m0-3l-3-3m0 0l-3 3m3-3v11.25"/></svg> «Ulashish» → «Bosh ekranga» tugmasini bosing',
        },
        en: {
            title: 'Install Risment',
            text: 'Tap <svg style="display:inline;vertical-align:middle;width:18px;height:18px;" fill="none" stroke="#007AFF" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 00-2.25 2.25v9a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25H15m0-3l-3-3m0 0l-3 3m3-3v11.25"/></svg> "Share", then "Add to Home Screen"',
        },
    };

    const msg = messages[locale] || messages.ru;

    const prompt = document.getElementById('ios-install-prompt');
    const title = document.getElementById('ios-prompt-title');
    const text = document.getElementById('ios-prompt-text');
    const close = document.getElementById('ios-prompt-close');

    if (!prompt || !title || !text) return;

    title.textContent = msg.title;
    text.innerHTML = msg.text;
    prompt.style.display = 'block';

    close.addEventListener('click', function() {
        prompt.style.display = 'none';
        localStorage.setItem('ios-install-dismissed', new Date().toISOString());
    });
})();
</script>
