import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// PWA
import { initInstallPrompt } from './pwa/install-prompt';
import { initOfflineIndicator } from './pwa/offline-indicator';

initInstallPrompt();
initOfflineIndicator();
