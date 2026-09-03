const CACHE_NAME = 'coletafacil-v1';
const PRECACHE = [
  './index.php',
  './style.css',
  './script.js',
  './coleta.php',
  './coleta.css',
  './coleta.js',
  './pontos.php',
  './pontos.css',
  './pontos.js',
  './materiais.php',
  './ocorrencias.php',
  './ocorrencias.css',
  './ocorrencias.js',
  './recompensas.php',
  './kids.php',
  './kids.css',
  './kids.js',
  './kids-data.js',
  './busca.php',
  './busca.js',
  './offline.php',
  './404.php',
];

// Install – precache
self.addEventListener('install', e => {
  e.waitUntil(
    caches.open(CACHE_NAME)
      .then(c => c.addAll(PRECACHE))
      .then(() => self.skipWaiting())
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

// Fetch – cache-first para estáticos, network-first para o resto
self.addEventListener('fetch', e => {
  if (e.request.method !== 'GET') return;

  const url = new URL(e.request.url);
  const isStatic = /\.(html|css|js|json|png|jpg|svg|ico|webp|woff2?)$/.test(url.pathname);

  if (isStatic) {
    e.respondWith(
      caches.match(e.request).then(cached => {
        if (cached) return cached;
        return fetch(e.request).then(res => {
          const clone = res.clone();
          caches.open(CACHE_NAME).then(c => c.put(e.request, clone));
          return res;
        }).catch(() => caches.match('./offline.php'));
      })
    );
  } else {
    e.respondWith(
      fetch(e.request).catch(() => caches.match('./offline.php'))
    );
  }
});
