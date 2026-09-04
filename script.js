// =============================================
// DARK MODE
// =============================================
function toggleTheme() {
  const isDark = document.documentElement.classList.toggle('dark');
  localStorage.setItem('theme', isDark ? 'dark' : 'light');
  const btn = document.getElementById('themeToggle');
  if (btn) btn.textContent = isDark ? '☀️' : '🌙';
}

// Inicializa ícone do botão no load
document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('themeToggle');
  if (btn) btn.textContent = document.documentElement.classList.contains('dark') ? '☀️' : '🌙';
});

// =============================================
// NAVBAR MOBILE
// =============================================
function toggleMenu() {
  const menu = document.getElementById('mobileMenu');
  if (menu) menu.classList.toggle('open');
}

// =============================================
// HOME – REDIRECIONA PARA PÁGINA DE COLETA
// =============================================
function irParaColeta(event) {
  if (event) event.preventDefault();
  const endereco = document.getElementById('enderecoInput')?.value.trim();
  if (!endereco) return;
  window.location.href = 'coleta.php?q=' + encodeURIComponent(endereco);
}

// Mantido para compatibilidade
function consultarColeta(event) {
  if (event) event.preventDefault();
  irParaColeta(event);
}

// =============================================
// BUSCA GLOBAL (NAVBAR / HOME)
// =============================================
function irParaBusca(event) {
  if (event) event.preventDefault();
  const input = document.getElementById('globalSearchInput') || document.getElementById('enderecoInput');
  const q = input?.value.trim();
  if (!q) return;
  window.location.href = 'busca.php?q=' + encodeURIComponent(q);
}

// =============================================
// VÍDEO – PLAY
// =============================================
function playVideo() {
  const thumb = document.querySelector('.video-thumb');
  const video = document.getElementById('meuVideo');
  if (!thumb || !video) return;
  thumb.style.display = 'none';
  video.style.display = 'block';
  video.play().catch(() => {
    thumb.style.display = 'block';
    video.style.display = 'none';
    const overlay = document.querySelector('.video-overlay p');
    if (overlay) { overlay.textContent = '📁 Adicione video-apresentacao.mp4 na pasta'; overlay.style.color = '#fbbf24'; }
  });
}

// =============================================
// CONTADORES ANIMADOS (HOME)
// =============================================
function animateCounters() {
  document.querySelectorAll('.impact-number').forEach(counter => {
    const target = parseInt(counter.getAttribute('data-target'));
    const step = target / (1800 / 16);
    let current = 0;
    const timer = setInterval(() => {
      current += step;
      if (current >= target) { current = target; clearInterval(timer); }
      counter.textContent = Math.floor(current).toLocaleString('pt-BR');
    }, 16);
  });
}

function observeImpact() {
  const section = document.querySelector('.impact-section');
  if (!section) return;
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => { if (entry.isIntersecting) { animateCounters(); observer.unobserve(entry.target); } });
  }, { threshold: 0.3 });
  observer.observe(section);
}
observeImpact();

// =============================================
// MATERIAIS – FILTRO
// =============================================
function filtrar(tipo, btn) {
  document.querySelectorAll('.filtro-btn').forEach(b => b.classList.remove('active'));
  if (btn) btn.classList.add('active');
  document.querySelectorAll('.material-card').forEach(card => {
    card.style.display = (tipo === 'todos' || card.dataset.tipo === tipo) ? 'block' : 'none';
  });
}

// =============================================
// PONTOS – FILTRO
// =============================================
function filtrarPontos() {
  const termo = document.getElementById('buscaPonto')?.value.toLowerCase() || '';
  const cards = document.querySelectorAll('.ponto-card');
  let n = 0;
  cards.forEach(card => {
    const txt = ((card.dataset.nome || '') + ' ' + (card.dataset.material || '')).toLowerCase();
    const show = txt.includes(termo);
    card.style.display = show ? 'block' : 'none';
    if (show) n++;
  });
  const sr = document.getElementById('semResultados');
  if (sr) sr.style.display = n === 0 ? 'block' : 'none';
}

// =============================================
// OCORRÊNCIAS – REGISTRO (PÁGINA ANTIGA)
// =============================================
function registrarOcorrencia(event) {
  if (event) event.preventDefault();
  const protocolo = 'CF-' + new Date().getFullYear() + '-' + String(Math.floor(Math.random() * 9000) + 1000);
  const modal = document.getElementById('modalSucesso');
  const el = document.getElementById('protocolo');
  if (modal && el) { el.textContent = protocolo; modal.style.display = 'flex'; }
}

function fecharModal() {
  const modal = document.getElementById('modalSucesso');
  if (modal) modal.style.display = 'none';
  const form = document.querySelector('.ocorrencia-form');
  if (form) form.reset();
}

// =============================================
// STATUS CONSULTA (PÁGINA ANTIGA)
// =============================================
function consultarStatus() {
  const protocolo = document.getElementById('protocoloConsulta')?.value.trim();
  if (!protocolo) return;
  const el = document.getElementById('statusResultado');
  const pel = document.getElementById('protocoloResultado');
  if (el && pel) { pel.textContent = protocolo; el.style.display = 'block'; }
}

// =============================================
// MODAIS – FECHAR COM CLICK FORA / ESC
// =============================================
document.addEventListener('click', e => {
  const m = document.getElementById('modalSucesso');
  if (m && e.target === m) fecharModal();
});

document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    fecharModal();
    if (typeof fecharModalAdmin === 'function') fecharModalAdmin();
  }
});

// =============================================
// SERVICE WORKER
// =============================================
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('./sw.js').catch(() => {});
  });
}
