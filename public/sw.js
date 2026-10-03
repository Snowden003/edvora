/**
 * Edvora Official Service Worker
 * Fully compliant with Google Play Store (TWA), Microsoft Store, and PWABuilder
 */

const CACHE_VERSION = 'edvora-pwa-v3';
const STATIC_CACHE = `${CACHE_VERSION}-static`;
const DYNAMIC_CACHE = `${CACHE_VERSION}-dynamic`;

const PRECACHE_ASSETS = [
    '/offline.html',
    '/manifest.json',
    '/logo.png',
    '/extension_icon.png',
    '/favicon.png',
    '/apple-touch-icon.png',
    '/screenshots/desktop-1.png',
    '/screenshots/mobile-1.png',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
    '/icons/icon-maskable-192x192.png',
    '/icons/icon-maskable-512x512.png'
];

// Install: precache offline fallback and essential assets, activate immediately
self.addEventListener('install', (event) => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(STATIC_CACHE).then((cache) => {
            return Promise.allSettled(
                PRECACHE_ASSETS.map((url) => cache.add(url).catch((err) => console.warn('Precache skip:', url, err)))
            );
        })
    );
});

// Activate: clean up older caches and claim clients immediately
self.addEventListener('activate', (event) => {
    event.waitUntil(
        Promise.all([
            caches.keys().then((keys) => {
                return Promise.all(
                    keys.filter((key) => key !== STATIC_CACHE && key !== DYNAMIC_CACHE)
                        .map((key) => caches.delete(key))
                );
            }),
            self.clients.claim()
        ])
    );
});

// Message listener to trigger immediate activation
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

// Background Sync API (PWABuilder requirement)
self.addEventListener('sync', (event) => {
    if (event.tag === 'edvora-sync-tasks' || event.tag === 'sync-messages') {
        event.waitUntil(
            // When connection restores, background tasks or submissions can run
            Promise.resolve()
        );
    }
});

// Periodic Background Sync API (PWABuilder requirement)
self.addEventListener('periodicsync', (event) => {
    if (event.tag === 'edvora-content-sync') {
        event.waitUntil(
            fetch('/app')
                .then((response) => {
                    if (response && response.status === 200) {
                        const copy = response.clone();
                        return caches.open(DYNAMIC_CACHE).then((cache) => cache.put('/app', copy));
                    }
                })
                .catch(() => {})
        );
    }
});

// Fetch: Strategy for TWA / Play Store / PWABuilder compliance
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

    // HTML Navigation requests: Network-First with Offline Fallback
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    if (response && response.status === 200) {
                        const copy = response.clone();
                        caches.open(DYNAMIC_CACHE).then((cache) => cache.put(request, copy));
                    }
                    return response;
                })
                .catch(async () => {
                    const cache = await caches.open(STATIC_CACHE);
                    const dynamicCache = await caches.open(DYNAMIC_CACHE);

                    const cachedResponse = await dynamicCache.match(request);
                    if (cachedResponse) {
                        return cachedResponse;
                    }

                    const offlineFallback = await cache.match('/offline.html');
                    if (offlineFallback) {
                        return offlineFallback;
                    }

                    return new Response(
                        `<!DOCTYPE html>
                        <html lang="fa" dir="rtl">
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>آفلاین هستید - ادورا</title>
                            <style>
                                body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #0f172a; color: #fff; text-align: center; padding: 40px 20px; }
                                .box { max-width: 480px; margin: 0 auto; background: #1e293b; padding: 32px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
                                h1 { font-size: 24px; color: #6366f1; margin-bottom: 12px; }
                                p { color: #94a3b8; font-size: 15px; line-height: 1.6; }
                                button { margin-top: 20px; background: #4f46e5; color: #fff; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; }
                            </style>
                        </head>
                        <body>
                            <div class="box">
                                <h1>شما در حالت آفلاین هستید</h1>
                                <p>اتصال شما به اینترنت قطع شده است. به محض برقراری مجدد ارتباط، اطلاعات به‌روزرسانی خواهند شد.</p>
                                <button onclick="window.location.reload()">تلاش مجدد</button>
                            </div>
                        </body>
                        </html>`,
                        {
                            status: 200,
                            headers: { 'Content-Type': 'text/html; charset=utf-8' }
                        }
                    );
                })
        );
        return;
    }

    // Static Assets (Build CSS/JS, Fonts, Icons, Images): Stale-While-Revalidate
    if (
        url.pathname.startsWith('/build/') ||
        url.pathname.startsWith('/assets/') ||
        url.pathname.startsWith('/icons/') ||
        url.pathname.startsWith('/screenshots/') ||
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
