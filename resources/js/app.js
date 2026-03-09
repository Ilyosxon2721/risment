import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// PWA
import { initInstallPrompt } from './pwa/install-prompt';
import { initOfflineIndicator } from './pwa/offline-indicator';

initInstallPrompt();
initOfflineIndicator();

import { initBackgroundSync } from './pwa/background-sync';
initBackgroundSync();

import { initPushNotifications } from './pwa/push-notifications';
initPushNotifications();

import { initPWAAnalytics } from './pwa/analytics';
initPWAAnalytics();
