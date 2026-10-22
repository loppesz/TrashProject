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
// FILTROS (busca, materiais e status)
// Cada chip liga/desliga o seu filtro. Dentro de um grupo vale "qualquer um"
// dos marcados (ex.: Pilhas OU Medicamentos). Nenhum marcado = "Todos".
// =============================================
const filtrosMaterial = new Set();
const filtrosStatus   = new Set();

// Minúsculas e sem acento, para "plastico" casar com "plástico".
function normalizarTexto(s) {
  return String(s || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
}

function alternarFiltro(conjunto, valor) {
  if (valor === 'todos') conjunto.clear();
  else if (conjunto.has(valor)) conjunto.delete(valor);
  else conjunto.add(valor);
}

// Marca visualmente os chips do grupo conforme o conjunto de filtros.
function sincronizarChips(idGrupo, conjunto) {
  document.querySelectorAll('#' + idGrupo + ' .chip').forEach(btn => {
    const v = btn.dataset.filtro;
    const ativo = v === 'todos' ? conjunto.size === 0 : conjunto.has(v);
    btn.classList.toggle('active', ativo);
    btn.setAttribute('aria-pressed', ativo ? 'true' : 'false');
  });
}

function filtrarMaterial(tipo) {
  alternarFiltro(filtrosMaterial, tipo);
  aplicarFiltros();
}

function filtrarStatus(status) {
  alternarFiltro(filtrosStatus, status);
  aplicarFiltros();
}

function filtrarPontos() {
  aplicarFiltros();
}

function aplicarFiltros() {
  const busca = normalizarTexto(document.getElementById('buscaPonto')?.value.trim());
  const cards = document.querySelectorAll('.ponto-rich-card');
  let visiveis = 0;

  sincronizarChips('chipsMaterial', filtrosMaterial);
  sincronizarChips('chipsStatus', filtrosStatus);

  cards.forEach(card => {
    const nome     = normalizarTexto(card.dataset.nome);
    const material = normalizarTexto(card.dataset.material);
    const status   = normalizarTexto(card.dataset.status);

    const matchBusca    = !busca || nome.includes(busca) || material.includes(busca);
    const matchMaterial = filtrosMaterial.size === 0 || [...filtrosMaterial].some(m => material.includes(m));
    const matchStatus   = filtrosStatus.size === 0 || filtrosStatus.has(status);

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

  // "X" da busca só aparece com texto digitado.
  const clearBtn = document.getElementById('buscaClear');
  if (clearBtn) clearBtn.style.display = busca ? 'block' : 'none';

  // Barra "Limpar filtros": visível enquanto houver qualquer filtro aplicado.
  const temFiltro = !!busca || filtrosMaterial.size > 0 || filtrosStatus.size > 0;
  const barra = document.getElementById('filtrosAtivos');
  if (barra) barra.hidden = !temFiltro;
  const resumo = document.getElementById('filtrosResumo');
  if (resumo) {
    resumo.textContent = visiveis === 1 ? '1 ponto encontrado' : visiveis + ' pontos encontrados';
  }

  // Atualiza bordas (primeiro/último visível)
  updateCardBorders();

  // Sincroniza os marcadores do mapa com os filtros aplicados
  if (typeof sincronizarMapaComFiltros === 'function') sincronizarMapaComFiltros();
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

// "X" dentro do campo: limpa só o texto da busca.
function limparBusca() {
  const input = document.getElementById('buscaPonto');
  if (input) { input.value = ''; input.focus(); }
  aplicarFiltros();
}

// Botão "Limpar filtros": tira busca, materiais e status de uma vez.
function limparFiltros() {
  const input = document.getElementById('buscaPonto');
  if (input) input.value = '';
  filtrosMaterial.clear();
  filtrosStatus.clear();
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
  // Helper de script.js: funciona também fora de HTTPS (endereço local).
  copiarParaAreaTransferencia(txt)
    .then(ok => { if (!ok) throw new Error('falhou'); })
    .then(() => showToast('✅ Copiado! Cole onde quiser.'))
    .catch(() => showToast('📋 ' + txt));
}

// =============================================
// MODAL SUGERIR
// =============================================
function abrirModalSugira() {
  document.getElementById('modalSugira').style.display = 'flex';
}
// O envio da sugestao agora e um POST real do formulario (ponto/sugerir).

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
