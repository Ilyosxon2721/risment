{{-- Offline warning for forms --}}
<div id="offline-form-warning" style="display:none;" class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-lg flex items-center gap-3">
    <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    <span class="text-sm text-amber-700" id="offline-form-warning-text"></span>
</div>
<script>
(function() {
    const locale = document.documentElement.lang || 'ru';
    const messages = {
        ru: 'Нет подключения к интернету. Отправка формы недоступна.',
        uz: "Internetga ulanish yo'q. Forma yuborish imkonsiz.",
        en: 'No internet connection. Form submission is unavailable.',
    };
    const warning = document.getElementById('offline-form-warning');
    const text = document.getElementById('offline-form-warning-text');
    if (!warning || !text) return;
    text.textContent = messages[locale] || messages.ru;

    function update() {
        warning.style.display = navigator.onLine ? 'none' : 'flex';
    }
    window.addEventListener('online', update);
    window.addEventListener('offline', update);
    update();
})();
</script>
