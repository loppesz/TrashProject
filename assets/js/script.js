// =============================================
// DARK MODE
// =============================================
function atualizarBotaoTema() {
  const btn = document.getElementById('themeToggle');
  if (!btn) return;
  const escuro = document.documentElement.classList.contains('dark');
  // Só o emoji: lua no tema claro (ir para o escuro), sol no escuro (voltar ao claro)
  btn.textContent = escuro ? '☀️' : '🌙';
  const rotulo = escuro ? 'Ativar tema claro' : 'Ativar tema escuro';
  btn.setAttribute('aria-label', rotulo);
  btn.setAttribute('title', rotulo);
}

function toggleTheme() {
  const isDark = document.documentElement.classList.toggle('dark');
  localStorage.setItem('theme', isDark ? 'dark' : 'light');
  document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';
  atualizarBotaoTema();
}

document.addEventListener('DOMContentLoaded', atualizarBotaoTema);

// =============================================
// SEGURANÇA: token CSRF para chamadas POST via fetch
// =============================================
// O servidor exige o token da sessão em todo POST. Formulários já levam o
// campo _csrf; chamadas fetch usam este cabeçalho.
function csrfToken() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

// Escapa texto para inserir com segurança em innerHTML.
function escapeHTML(valor) {
  return String(valor == null ? '' : valor).replace(/[&<>"']/g, c => (
    { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]
  ));
}

// =============================================
// AVISO DE PRIVACIDADE (uma vez por navegador)
// =============================================
function fecharAvisoPrivacidade() {
  const el = document.getElementById('avisoPrivacidade');
  if (el) el.hidden = true;
  try { localStorage.setItem('avisoPrivacidadeVisto', '1'); } catch (e) {}
}

document.addEventListener('DOMContentLoaded', () => {
  const el = document.getElementById('avisoPrivacidade');
  if (!el) return;
  let visto = false;
  try { visto = localStorage.getItem('avisoPrivacidadeVisto') === '1'; } catch (e) {}
  if (!visto) el.hidden = false;
});

// =============================================
// COPIAR PARA A ÁREA DE TRANSFERÊNCIA
// =============================================
// navigator.clipboard só funciona em HTTPS ou localhost. No endereço local
// (http://coletaseletiva) ele não existe, então usamos o método antigo
// (textarea + execCommand) como alternativa. Retorna Promise<boolean>.
function copiarParaAreaTransferencia(texto) {
  texto = String(texto == null ? '' : texto);
  if (navigator.clipboard && window.isSecureContext) {
    return navigator.clipboard.writeText(texto)
      .then(() => true)
      .catch(() => copiarTextoAlternativo(texto));
  }
  return Promise.resolve(copiarTextoAlternativo(texto));
}

function copiarTextoAlternativo(texto) {
  const focoAnterior = document.activeElement;
  const ta = document.createElement('textarea');
  ta.value = texto;
  ta.setAttribute('readonly', '');
  ta.setAttribute('aria-hidden', 'true');
  ta.style.cssText = 'position:fixed;top:0;left:0;width:1px;height:1px;opacity:0;pointer-events:none;';
  document.body.appendChild(ta);
  ta.select();
  ta.setSelectionRange(0, texto.length);
  let ok = false;
  try { ok = document.execCommand('copy'); } catch (e) { ok = false; }
  document.body.removeChild(ta);
  if (focoAnterior && typeof focoAnterior.focus === 'function') focoAnterior.focus();
  return ok;
}

// =============================================
// NAVBAR – DROPDOWNS (menu ☰ e Conta)
// =============================================
function toggleDropdown(event, id) {
  if (event) event.stopPropagation();
  const dd = document.getElementById(id);
  if (!dd) return;
  const abrindo = !dd.classList.contains('open');
  // Fecha todos antes de abrir o alvo
  document.querySelectorAll('.nav-dropdown.open').forEach(el => {
    el.classList.remove('open');
    const t = el.querySelector('[aria-expanded]');
    if (t) t.setAttribute('aria-expanded', 'false');
  });
  if (abrindo) {
    dd.classList.add('open');
    const trigger = dd.querySelector('[aria-expanded]');
    if (trigger) trigger.setAttribute('aria-expanded', 'true');
  }
}

// Fecha os dropdowns ao clicar fora ou apertar Esc
document.addEventListener('click', e => {
  if (!e.target.closest('.nav-dropdown')) {
    document.querySelectorAll('.nav-dropdown.open').forEach(el => {
      el.classList.remove('open');
      const t = el.querySelector('[aria-expanded]');
      if (t) t.setAttribute('aria-expanded', 'false');
    });
  }
});
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    document.querySelectorAll('.nav-dropdown.open').forEach(el => {
      el.classList.remove('open');
      const t = el.querySelector('[aria-expanded]');
      if (t) t.setAttribute('aria-expanded', 'false');
    });
  }
});

// =============================================
// BUSCA GLOBAL (NAVBAR / HOME)
// =============================================
function irParaBusca(event) {
  if (event) event.preventDefault();
  const input = document.getElementById('globalSearchInput') || document.getElementById('enderecoInput');
  const q = input?.value.trim();
  if (!q) return;
  const base = (window.BASEURL || '/').replace(/\/+$/, '') + '/';
  window.location.href = base + 'busca?q=' + encodeURIComponent(q);
}

// =============================================
// VÍDEO – PLAY / CONTROLES
// =============================================
function playVideo() {
  const thumb = document.querySelector('.video-thumb');
  const video = document.getElementById('meuVideo');
  const controls = document.getElementById('videoQuickControls');
  if (!thumb || !video) return;
  thumb.style.display = 'none';
  video.style.display = 'block';
  video.play().then(() => {
    if (controls) controls.style.display = 'flex';
  }).catch(() => {
    thumb.style.display = 'block';
    video.style.display = 'none';
    const overlay = document.querySelector('.video-overlay p');
    if (overlay) { overlay.textContent = '📁 Adicione video-apresentacao.mp4 na pasta'; overlay.style.color = '#fbbf24'; }
  });
}

function seekVideo(seconds) {
  const video = document.getElementById('meuVideo');
  if (!video) return;
  video.currentTime = Math.max(0, Math.min(video.duration || 0, video.currentTime + seconds));
}

function togglePlayPause() {
  const video = document.getElementById('meuVideo');
  const btn = document.getElementById('playPauseBtn');
  if (!video) return;
  if (video.paused) {
    video.play();
    if (btn) btn.textContent = '⏸️ Pausar';
  } else {
    video.pause();
    if (btn) btn.textContent = '▶️ Reproduzir';
  }
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
// SCROLL REVEAL – anima elementos ao entrar na tela
// =============================================
function initScrollReveal() {
  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Seleciona blocos que valem a pena animar
  const targets = document.querySelectorAll(
    '.feature-card-v2, .material-card, .ponto-card, .recompensa-card, ' +
    '.pontos-como-item, .section-header, .impacto-card, .kr2-card, ' +
    '.ranking-card, .my-points-card, .resultado-card-v2'
  );

  targets.forEach((el, i) => {
    el.classList.add('reveal');
    // cascata leve dentro de grupos
    el.classList.add('d' + ((i % 5) + 1));
  });

  if (prefersReduced || !('IntersectionObserver' in window)) {
    targets.forEach(el => el.classList.add('is-visible'));
    return;
  }

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

  targets.forEach(el => observer.observe(el));
}

// =============================================
// BOTÃO VOLTAR AO TOPO
// =============================================
function initBackToTop() {
  let btn = document.getElementById('backToTop');
  if (!btn) {
    btn = document.createElement('button');
    btn.id = 'backToTop';
    btn.className = 'back-to-top';
    btn.setAttribute('aria-label', 'Voltar ao topo');
    btn.innerHTML = '↑';
    btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
    document.body.appendChild(btn);
  }
  const toggle = () => btn.classList.toggle('show', window.scrollY > 500);
  window.addEventListener('scroll', toggle, { passive: true });
  toggle();
}

document.addEventListener('DOMContentLoaded', () => {
  initScrollReveal();
  initBackToTop();
});

// =============================================
// SERVICE WORKER
// =============================================
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    const base = (window.BASEURL || '/').replace(/\/+$/, '') + '/';
    navigator.serviceWorker.register(base + 'sw.js').catch(() => {});
  });
}
