// =============================================
// TOGGLE EXPANDIR PONTO
// =============================================
function togglePonto(id) {
  const exp = document.getElementById('exp-' + id);
  const btn = document.getElementById('btn-' + id);
  const card = document.getElementById(id);

  const isOpen = exp.style.display !== 'none';

  // Fecha todos
  document.querySelectorAll('.prc-expanded').forEach(el => el.style.display = 'none');
  document.querySelectorAll('.prc-toggle-btn').forEach(b => b.classList.remove('open'));
  document.querySelectorAll('.ponto-rich-card').forEach(c => c.classList.remove('expanded'));

  if (!isOpen) {
    exp.style.display = 'block';
    btn.classList.add('open');
    card.classList.add('expanded');

    // Scroll suave para o card
    setTimeout(() => {
      card.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, 100);
  }
}

// =============================================
// FILTRO POR MATERIAL
// =============================================
let filtroMaterialAtivo = 'todos';
let filtroStatusAtivo   = 'todos';

function filtrarMaterial(tipo, btn) {
  filtroMaterialAtivo = tipo;
  document.querySelectorAll('.filtros-chips:first-of-type .chip').forEach(b => b.classList.remove('active'));
  if (btn) btn.classList.add('active');
  aplicarFiltros();
}

function filtrarStatus(status, btn) {
  filtroStatusAtivo = status;
  document.querySelectorAll('.chip-status').forEach(b => b.classList.remove('active'));
  if (btn) btn.classList.add('active');
  aplicarFiltros();
}

function filtrarPontos() {
  const busca = document.getElementById('buscaPonto')?.value || '';
  const clearBtn = document.getElementById('buscaClear');
  if (clearBtn) clearBtn.style.display = busca.length > 0 ? 'block' : 'none';
  aplicarFiltros();
}

function aplicarFiltros() {
  const busca = (document.getElementById('buscaPonto')?.value || '').toLowerCase();
  const cards = document.querySelectorAll('.ponto-rich-card');
  let visiveis = 0;

  cards.forEach(card => {
    const nome     = (card.dataset.nome || '').toLowerCase();
    const material = (card.dataset.material || '').toLowerCase();
    const status   = (card.dataset.status || '').toLowerCase();

    const matchBusca    = !busca || nome.includes(busca) || material.includes(busca);
    const matchMaterial = filtroMaterialAtivo === 'todos' || material.includes(filtroMaterialAtivo);
    const matchStatus   = filtroStatusAtivo === 'todos' || status === filtroStatusAtivo;

    if (matchBusca && matchMaterial && matchStatus) {
      card.style.display = 'block';
      visiveis++;
    } else {
      card.style.display = 'none';
      // Fecha se estava aberto
      const id = card.id;
      const exp = document.getElementById('exp-' + id);
      if (exp) exp.style.display = 'none';
    }
  });

  const semRes = document.getElementById('semResultados');
  if (semRes) semRes.style.display = visiveis === 0 ? 'block' : 'none';

  // Atualiza bordas (primeiro/último visível)
  updateCardBorders();
}

function updateCardBorders() {
  const cards = Array.from(document.querySelectorAll('.ponto-rich-card')).filter(c => c.style.display !== 'none');
  document.querySelectorAll('.ponto-rich-card').forEach(c => {
    c.style.borderRadius = '0';
    c.style.borderTop = '1px solid #e5e7eb';
  });
  if (cards.length === 0) return;
  cards[0].style.borderRadius = '14px 14px 0 0';
  cards[0].style.borderTop = '1px solid #e5e7eb';
  cards[cards.length - 1].style.borderRadius = cards.length === 1 ? '14px' : '0 0 14px 14px';
}

function limparBusca() {
  const input = document.getElementById('buscaPonto');
  if (input) input.value = '';
  const clearBtn = document.getElementById('buscaClear');
  if (clearBtn) clearBtn.style.display = 'none';
  filtroMaterialAtivo = 'todos';
  filtroStatusAtivo   = 'todos';
  document.querySelectorAll('.chip').forEach((b, i) => { b.classList.remove('active'); if (i === 0 || b.classList.contains('chip-status')) {} });
  document.querySelectorAll('.chip:first-of-type').forEach(b => b.classList.add('active'));
  aplicarFiltros();
}

// =============================================
// COMPARTILHAR
// =============================================
function compartilhar(nome, endereco) {
  const texto = `📍 ${nome} – ${endereco}\nEncontrado no ColetaFácil!`;
  if (navigator.share) {
    navigator.share({ title: nome, text: texto, url: window.location.href })
      .catch(() => copiarTexto(texto));
  } else {
    copiarTexto(texto);
  }
}

function copiarTexto(txt) {
  navigator.clipboard.writeText(txt)
    .then(() => showToast('✅ Copiado! Cole onde quiser.'))
    .catch(() => showToast('📋 ' + txt));
}

// =============================================
// MODAL SUGERIR
// =============================================
function abrirModalSugira() {
  document.getElementById('modalSugira').style.display = 'flex';
}

function enviarSugestao() {
  document.getElementById('modalSugira').style.display = 'none';
  showToast('✅ Sugestão enviada! Obrigado pela contribuição.');
}

// =============================================
// TOAST
// =============================================
function showToast(msg) {
  const el = document.getElementById('toastMsg');
  if (!el) return;
  el.textContent = msg;
  el.style.display = 'block';
  el.style.opacity = '1';
  clearTimeout(el._t);
  el._t = setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.style.display = 'none', 300); }, 3000);
}

// Fechar modal clicando fora
document.addEventListener('click', e => {
  const m = document.getElementById('modalSugira');
  if (m && e.target === m) m.style.display = 'none';
});

// =============================================
// OPEN FIRST BY DEFAULT (URL HASH)
// =============================================
window.addEventListener('DOMContentLoaded', () => {
  updateCardBorders();
  const hash = window.location.hash;
  if (hash) {
    const id = hash.replace('#', '');
    const card = document.getElementById(id);
    if (card) {
      setTimeout(() => { togglePonto(id); }, 300);
    }
  }
});
