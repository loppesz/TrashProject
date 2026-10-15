const CACHE_NAME = 'coletafacil-v32';
const PRECACHE = [
  '/',
  '/assets/css/style.css',
  '/assets/js/script.js',
  '/home/offline',
];

// Install – precache mínimo (demais recursos entram sob demanda no fetch)
self.addEventListener('install', e => {
  e.waitUntil(
    caches.open(CACHE_NAME)
      .then(c => c.addAll(PRECACHE))
      .then(() => self.skipWaiting())
      .catch(() => self.skipWaiting())
  );
});

// Activate – limpa caches antigos
self.addEventListener('activate', e => {
  e.waitUntil(
    caches.keys().then(keys =>
      Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))
    ).then(() => self.clients.claim())
  );
});

// Fetch – cache-first para estáticos, network-first para páginas (rotas MVC)
self.addEventListener('fetch', e => {
  if (e.request.method !== 'GET') return;

  const url = new URL(e.request.url);
  const isStatic = /\.(css|js|json|png|jpg|jpeg|svg|ico|webp|woff2?|mp4)$/.test(url.pathname);

  if (isStatic) {
    e.respondWith(
      caches.match(e.request).then(cached => {
        if (cached) return cached;
        return fetch(e.request).then(res => {
          const clone = res.clone();
          caches.open(CACHE_NAME).then(c => c.put(e.request, clone));
          return res;
        }).catch(() => caches.match('/home/offline'));
      })
    );
  } else {
    e.respondWith(
      fetch(e.request).catch(() => caches.match('/home/offline'))
    );
  }
});
