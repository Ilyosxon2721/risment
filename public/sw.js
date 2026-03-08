const CACHE_VERSION = 'v1';
const STATIC_CACHE = `risment-static-${CACHE_VERSION}`;
const PAGES_CACHE = `risment-pages-${CACHE_VERSION}`;
const IMAGES_CACHE = `risment-images-${CACHE_VERSION}`;
const API_CACHE = `risment-api-${CACHE_VERSION}`;

const PRECACHE_URLS = [
  '/offline.html',
  '/manifest.webmanifest',
];

// Install: precache essential resources
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(STATIC_CACHE)
      .then((cache) => cache.addAll(PRECACHE_URLS))
      .then(() => {
        // Try to load and cache Vite assets from sw-manifest.json
        return fetch('/sw-manifest.json')
          .then((response) => response.json())
          .then((manifest) => {
            return caches.open(STATIC_CACHE).then((cache) => cache.addAll(manifest.assets || []));
          })
          .catch(() => {
            // sw-manifest.json may not exist in dev mode, that's ok
            console.log('[SW] No sw-manifest.json found, skipping Vite asset precache');
          });
      })
      .then(() => self.skipWaiting())
  );
});

// Activate: clean up old caches
self.addEventListener('activate', (event) => {
  const currentCaches = [STATIC_CACHE, PAGES_CACHE, IMAGES_CACHE, API_CACHE];
  event.waitUntil(
    caches.keys()
      .then((cacheNames) => {
        return Promise.all(
          cacheNames
            .filter((name) => name.startsWith('risment-') && !currentCaches.includes(name))
            .map((name) => caches.delete(name))
        );
      })
      .then(() => self.clients.claim())
  );
});

// Fetch: route requests by strategy
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  // Skip non-GET requests (POST forms etc need network)
  if (request.method !== 'GET') return;

  // Skip external requests
  if (url.origin !== self.location.origin) return;

  // Skip admin panel (Filament)
  if (url.pathname.startsWith('/admin')) return;

  // Skip API/payment callbacks
  if (url.pathname.startsWith('/payment')) return;

  // Strategy: Vite build assets (CSS/JS with hash) -> Cache First
  if (url.pathname.startsWith('/build/')) {
    event.respondWith(cacheFirst(request, STATIC_CACHE));
    return;
  }

  // Strategy: Icons and static images -> Cache First
  if (url.pathname.startsWith('/icons/') || url.pathname.startsWith('/images/')) {
    event.respondWith(cacheFirst(request, IMAGES_CACHE));
    return;
  }

  // Strategy: Product/upload images -> Stale While Revalidate
  if (url.pathname.startsWith('/storage/')) {
    event.respondWith(staleWhileRevalidate(request, IMAGES_CACHE));
    return;
  }

  // Strategy: Manifest -> Cache First
  if (url.pathname === '/manifest.webmanifest') {
    event.respondWith(cacheFirst(request, STATIC_CACHE));
    return;
  }

  // Strategy: HTML pages -> Network First with offline fallback
  if (request.headers.get('Accept')?.includes('text/html')) {
    event.respondWith(networkFirstWithOfflineFallback(request, PAGES_CACHE));
    return;
  }

  // Strategy: Fonts -> Cache First
  if (url.pathname.match(/\.(woff2?|ttf|otf|eot)$/)) {
    event.respondWith(cacheFirst(request, STATIC_CACHE));
    return;
  }

  // Default: Network First
  event.respondWith(networkFirst(request, API_CACHE));
});

// === Caching Strategies ===

async function cacheFirst(request, cacheName) {
  const cached = await caches.match(request);
  if (cached) return cached;

  try {
    const response = await fetch(request);
    if (response.ok) {
      const cache = await caches.open(cacheName);
      cache.put(request, response.clone());
    }
    return response;
  } catch (error) {
    return new Response('Offline', { status: 503 });
  }
}

async function networkFirst(request, cacheName) {
  try {
    const response = await fetch(request);
    if (response.ok) {
      const cache = await caches.open(cacheName);
      cache.put(request, response.clone());
    }
    return response;
  } catch (error) {
    const cached = await caches.match(request);
    return cached || new Response('Offline', { status: 503 });
  }
}

async function networkFirstWithOfflineFallback(request, cacheName) {
  try {
    const response = await fetch(request);
    if (response.ok) {
      const cache = await caches.open(cacheName);
      cache.put(request, response.clone());
    }
    return response;
  } catch (error) {
    const cached = await caches.match(request);
    if (cached) return cached;

    // Return offline page
    const offlinePage = await caches.match('/offline.html');
    return offlinePage || new Response('Offline', {
      status: 503,
      headers: { 'Content-Type': 'text/html' },
    });
  }
}

async function staleWhileRevalidate(request, cacheName) {
  const cache = await caches.open(cacheName);
  const cached = await cache.match(request);

  const fetchPromise = fetch(request).then((response) => {
    if (response.ok) {
      cache.put(request, response.clone());
    }
    return response;
  }).catch(() => cached);

  return cached || fetchPromise;
}

// === Push Notifications ===

self.addEventListener('push', (event) => {
  let data = { title: 'Risment', body: 'Новое уведомление', url: '/cabinet/dashboard' };

  if (event.data) {
    try {
      data = { ...data, ...event.data.json() };
    } catch (e) {
      data.body = event.data.text();
    }
  }

  const options = {
    body: data.body,
    icon: '/icons/icon-192x192.png',
    badge: '/icons/icon-72x72.png',
    vibrate: [100, 50, 100],
    data: { url: data.url || '/cabinet/dashboard' },
    actions: data.actions || [],
  };

  event.waitUntil(
    self.registration.showNotification(data.title, options)
  );
});

self.addEventListener('notificationclick', (event) => {
  event.notification.close();

  const url = event.notification.data?.url || '/cabinet/dashboard';

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
      // Focus existing window if available
      for (const client of clientList) {
        if (client.url.includes(self.location.origin) && 'focus' in client) {
          client.navigate(url);
          return client.focus();
        }
      }
      // Open new window
      return clients.openWindow(url);
    })
  );
});
