const VERSION = 'sidra-pwa-v1';
const APP_CACHE = `${VERSION}-app`;
const TICKET_CACHE = `${VERSION}-tickets`;
const APP_SHELL = [
    '/',
    '/assets/css/app.css',
    '/assets/js/app.js',
    '/manifest.json',
    '/offline.html',
    '/assets/images/pwa-icon.svg'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(APP_CACHE)
            .then((cache) => cache.addAll(APP_SHELL))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys.filter((key) => ![APP_CACHE, TICKET_CACHE].includes(key))
                    .map((key) => caches.delete(key))
            ))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const request = event.request;
    if (request.method !== 'GET' || new URL(request.url).origin !== self.location.origin) {
        return;
    }

    const url = new URL(request.url);
    const isTicketPage = /^\/((customer\/)?tickets)\/[^/]+$/.test(url.pathname);
    const isNavigation = request.mode === 'navigate';

    if (isTicketPage) {
        event.respondWith(networkFirstTicket(request));
        return;
    }

    if (isNavigation) {
        event.respondWith(networkFirstPage(request));
    }
});

async function networkFirstTicket(request) {
    const cache = await caches.open(TICKET_CACHE);
    try {
        const response = await fetch(request);
        if (response.ok) {
            await cache.put(request, response.clone());
        }
        return response;
    } catch (error) {
        return (await cache.match(request)) || caches.match('/offline.html');
    }
}

async function networkFirstPage(request) {
    try {
        const response = await fetch(request);
        if (response.ok && new URL(request.url).pathname === '/') {
            const cache = await caches.open(APP_CACHE);
            await cache.put(request, response.clone());
        }
        return response;
    } catch (error) {
        return (await caches.match(request)) || caches.match('/offline.html');
    }
}