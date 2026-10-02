/**
 * Edvora Official Service Worker
 * Fully compliant with Google Play Store (TWA) and Microsoft Store (PWABuilder)
 */

const CACHE_VERSION = 'edvora-pwa-v2';
const STATIC_CACHE = `${CACHE_VERSION}-static`;
const DYNAMIC_CACHE = `${CACHE_VERSION}-dynamic`;

const PRECACHE_ASSETS = [
    '/offline.html',
    '/manifest.json',
    '/logo.png',
    '/extension_icon.png',
    '/favicon.png',
    '/apple-touch-icon.png',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
    '/icons/icon-maskable-192x192.png',
    '/icons/icon-maskable-512x512.png'
];

// Install: precache offline fallback and essential assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE).then((cache) => {
            return cache.addAll(PRECACHE_ASSETS);
        }).then(() => self.skipWaiting())
    );
});

// Activate: clean up older caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.filter((key) => key !== STATIC_CACHE && key !== DYNAMIC_CACHE)
                    .map((key) => caches.delete(key))
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch: Strategy for TWA / Play Store compliance
self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);

    // Skip non-GET requests and internal browser schemes
    if (request.method !== 'GET' || !url.protocol.startsWith('http')) {
        return;
    }

    // Skip WebSocket, broadcasting, live streams, and API polling
    if (
        url.pathname.includes('/broadcasting/') ||
        url.pathname.includes('/live-check') ||
        url.pathname.includes('/check-new') ||
        url.pathname.includes('/api/') ||
        url.pathname.includes('/sanctum/')
    ) {
        return;
    }

    // HTML Navigation requests: Network-First with Offline Fallback (prevents 404 & connection error)
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    // Cache successful navigation responses for fast reload
                    if (response.status === 200) {
                        const copy = response.clone();
                        caches.open(DYNAMIC_CACHE).then((cache) => cache.put(request, copy));
                    }
                    return response;
                })
                .catch(async () => {
                    const cache = await caches.open(STATIC_CACHE);
                    const dynamicCache = await caches.open(DYNAMIC_CACHE);
                    // Try to serve previously cached version of page if available, else offline.html
                    const cachedResponse = await dynamicCache.match(request);
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    return cache.match('/offline.html');
                })
        );
        return;
    }

    // Static Assets (Build CSS/JS, Fonts, Icons, Images): Stale-While-Revalidate
    if (
        url.pathname.startsWith('/build/') ||
        url.pathname.startsWith('/assets/') ||
        url.pathname.startsWith('/icons/') ||
        url.pathname.endsWith('.js') ||
        url.pathname.endsWith('.css') ||
        url.pathname.endsWith('.png') ||
        url.pathname.endsWith('.jpg') ||
        url.pathname.endsWith('.woff2')
    ) {
        event.respondWith(
            caches.match(request).then((cachedResponse) => {
                const fetchPromise = fetch(request).then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseClone = networkResponse.clone();
                        caches.open(DYNAMIC_CACHE).then((cache) => cache.put(request, responseClone));
                    }
                    return networkResponse;
                }).catch(() => null);

                return cachedResponse || fetchPromise;
            })
        );
        return;
    }

    // Default: Network with cache fallback
    event.respondWith(
        fetch(request).catch(() => caches.match(request))
    );
});

// Push notification support (Student & Teacher class alerts, messages, exams)
self.addEventListener('push', (event) => {
    let data = {
        title: 'ادورا | Edvora',
        body: 'یک پیام جدید در داشبورد شما دریافت شد.',
        icon: '/icons/icon-192x192.png',
        badge: '/icons/icon-72x72.png',
        url: '/app'
    };

    if (event.data) {
        try {
            data = Object.assign(data, event.data.json());
        } catch (e) {
            data.body = event.data.text();
        }
    }

    const options = {
        body: data.body,
        icon: data.icon || '/icons/icon-192x192.png',
        badge: data.badge || '/icons/icon-72x72.png',
        data: { url: data.url || '/app' },
        dir: 'rtl',
        lang: 'fa',
        vibrate: [100, 50, 100]
    };

    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

// Notification Click: navigate directly to Student/Teacher Dashboard
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const targetUrl = event.notification.data?.url || '/app';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            for (const client of clientList) {
                if (client.url && 'focus' in client) {
                    client.navigate(targetUrl);
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});
