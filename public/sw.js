const CACHE_NAME = 'ecolusyon-shell-v1';
const APP_SHELL = [
    '/offline.html',
    '/css/ecolusyon.css',
    '/js/camera-capture.js',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
];
 
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(APP_SHELL))
    );
    self.skipWaiting();
});
 
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((k) => k !== CACHE_NAME).map((k) => caches.delete(k)))
        )
    );
    self.clients.claim();
});
 
self.addEventListener('fetch', (event) => {
    const { request } = event;
 
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() => caches.match('/offline.html'))
        );
        return;
    }
 
    if (APP_SHELL.some((asset) => request.url.endsWith(asset))) {
        event.respondWith(
            caches.match(request).then((cached) => cached || fetch(request))
        );
    }
});
