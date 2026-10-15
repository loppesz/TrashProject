// =============================================
// MAPA INTERATIVO DOS PONTOS DE COLETA (Leaflet)
// Base: Muriaé-MG. Lê os pontos direto dos cards (.ponto-rich-card)
// para não duplicar dados. Marcadores coloridos por status.
// =============================================

// Centro aproximado de Muriaé-MG
const MURIAE_CENTER = [-21.1306, -42.3664];

// Guarda os marcadores por id de card para integrar com os filtros
const mapaMarcadores = {};
let mapaLeaflet = null;

// Cria um ícone "pino" colorido via divIcon (sem depender de imagens externas)
function criarIconePonto(status) {
  const cor = status === 'fechado' ? '#ef4444' : '#16a34a';
  const pulse = status === 'fechado' ? '' : 'cf-pin-pulse';
  const html = `
    <div class="cf-pin ${pulse}" style="--pin-color:${cor}">
      <div class="cf-pin-head"></div>
    </div>`;
  return L.divIcon({
    className: 'cf-pin-wrap',
    html,
    iconSize: [40, 52],
    iconAnchor: [20, 50],
    popupAnchor: [0, -46],
  });
}

// Monta o conteúdo do popup de um ponto
function popupPonto(id, nome, endereco, status) {
  const statusLabel = status === 'fechado'
    ? '<span class="cf-popup-status fechado">🔴 Fechado agora</span>'
    : '<span class="cf-popup-status aberto">🟢 Aberto agora</span>';
  return `
    <div class="cf-popup">
      <strong class="cf-popup-nome">${nome}</strong>
      ${statusLabel}
      <p class="cf-popup-end">📍 ${endereco}</p>
      <button type="button" class="cf-popup-btn" onclick="verPontoNoMapa('${id}')">
        Ver detalhes do ponto →
      </button>
    </div>`;
}

// Chamado pelo botão do popup: rola até o card e o expande
function verPontoNoMapa(id) {
  const card = document.getElementById(id);
  if (!card) return;
  // Se o card estiver filtrado/oculto, não faz nada
  if (card.style.display === 'none') return;
  if (typeof togglePonto === 'function') {
    // Abre apenas se ainda não estiver aberto
    const exp = document.getElementById('exp-' + id);
    const estaAberto = exp && exp.style.display === 'block';
    if (!estaAberto) togglePonto(id);
    else card.scrollIntoView({ behavior: 'smooth', block: 'start' });
  } else {
    card.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
}

// Inicializa o mapa e adiciona um marcador por card
function initMapaPontos() {
  const el = document.getElementById('mapaInterativo');
  if (!el || typeof L === 'undefined') return;

  mapaLeaflet = L.map(el, {
    center: MURIAE_CENTER,
    zoom: 14,
    scrollWheelZoom: false, // evita "sequestrar" o scroll da página
  });

  // Camada base do OpenStreetMap (gratuita, sem chave de API)
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
  }).addTo(mapaLeaflet);

  // Habilita o scroll-zoom só depois de clicar no mapa (melhor UX)
  mapaLeaflet.on('focus', () => mapaLeaflet.scrollWheelZoom.enable());
  mapaLeaflet.on('blur', () => mapaLeaflet.scrollWheelZoom.disable());

  const bounds = [];
  document.querySelectorAll('.ponto-rich-card').forEach(card => {
    const lat = parseFloat(card.dataset.lat);
    const lng = parseFloat(card.dataset.lng);
    if (Number.isNaN(lat) || Number.isNaN(lng)) return;

    const id = card.id;
    const status = (card.dataset.status || 'aberto').toLowerCase();
    const endereco = card.dataset.endereco || '';
    const nomeEl = card.querySelector('.prc-title-row h3');
    const nome = nomeEl ? nomeEl.textContent.trim() : (card.dataset.nome || 'Ponto de coleta');

    const marker = L.marker([lat, lng], { icon: criarIconePonto(status), title: nome })
      .addTo(mapaLeaflet)
      .bindPopup(popupPonto(id, nome, endereco, status));

    mapaMarcadores[id] = marker;
    bounds.push([lat, lng]);
  });

  // Enquadra todos os pontos
  if (bounds.length > 1) {
    mapaLeaflet.fitBounds(bounds, { padding: [50, 50], maxZoom: 15 });
  }

  // Garante renderização correta (containers que mudam de tamanho)
  setTimeout(() => mapaLeaflet.invalidateSize(), 200);
}

// Sincroniza os marcadores com os filtros da página:
// esconde o marcador de pontos que foram filtrados (card display:none)
function sincronizarMapaComFiltros() {
  if (!mapaLeaflet) return;
  Object.keys(mapaMarcadores).forEach(id => {
    const card = document.getElementById(id);
    const marker = mapaMarcadores[id];
    if (!card || !marker) return;
    const visivel = card.style.display !== 'none';
    const noMapa = mapaLeaflet.hasLayer(marker);
    if (visivel && !noMapa) marker.addTo(mapaLeaflet);
    if (!visivel && noMapa) mapaLeaflet.removeLayer(marker);
  });
}

document.addEventListener('DOMContentLoaded', initMapaPontos);
