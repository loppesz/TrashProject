// =============================================
// SIDEBAR TOGGLE
// =============================================
function toggleSidebar() {
  const sidebar = document.getElementById('adminSidebar');
  if (!sidebar) return;

  if (window.innerWidth <= 900) {
    sidebar.classList.toggle('open');
  } else {
    sidebar.classList.toggle('hidden');
    const main = document.querySelector('.admin-main');
    if (main) {
      main.style.marginLeft = sidebar.classList.contains('hidden') ? '0' : '240px';
    }
  }
}

// Fecha sidebar mobile ao clicar fora
document.addEventListener('click', function(e) {
  const sidebar = document.getElementById('adminSidebar');
  const toggle = document.querySelector('.sidebar-toggle');
  if (
    sidebar &&
    sidebar.classList.contains('open') &&
    !sidebar.contains(e.target) &&
    e.target !== toggle
  ) {
    sidebar.classList.remove('open');
  }
});

// =============================================
// FECHAR MODAL ADMIN
// =============================================
function fecharModalAdmin() {
  document.querySelectorAll('.modal').forEach(m => m.style.display = 'none');
}

// Fechar ao clicar fora
document.addEventListener('click', function(e) {
  document.querySelectorAll('.modal').forEach(modal => {
    if (e.target === modal) fecharModalAdmin();
  });
});

// =============================================
// OCORRÊNCIAS ADMIN
// =============================================
// --- Ocorrencias "ocultas" (triadas): guardadas no navegador via localStorage ---
// Quando o admin altera o status de uma ocorrencia, ela sai da lista (fica oculta).
// O botao "Mostrar ocultas" reexibe; cada linha oculta tem um botao para reexibir.
function ocOcultasSet() {
  try { return new Set(JSON.parse(localStorage.getItem('oc_ocultas') || '[]')); }
  catch (e) { return new Set(); }
}
function salvarOcOcultas(set) {
  localStorage.setItem('oc_ocultas', JSON.stringify(Array.from(set)));
}

// Filtro da lista de ocorrencias: busca + status + tipo + estado oculto.
function filtrarOcAdmin() {
  const tabela = document.getElementById('tabelaOcorrencias');
  if (!tabela) return;

  const busca  = (document.getElementById('buscaOc')?.value || '').toLowerCase();
  const status = document.getElementById('filtroStatus')?.value || '';
  const tipo   = document.getElementById('filtroTipo')?.value || '';
  const mostrarOcultas = !!document.getElementById('mostrarOcultas')?.checked;
  const ocultas = ocOcultasSet();
  let nOcultas = 0;

  tabela.querySelectorAll('tbody tr').forEach(tr => {
    if (!tr.dataset.busca) return; // ignora a linha "nenhuma ocorrencia"
    const estaOculta = ocultas.has(tr.dataset.id);
    if (estaOculta) nOcultas++;

    const okBusca  = !busca  || tr.dataset.busca.includes(busca);
    const okStatus = !status || tr.dataset.status === status;
    const okTipo   = !tipo   || (tr.dataset.tipo || '').includes(tipo);
    const passaFiltro = okBusca && okStatus && okTipo;
    const visivel = passaFiltro && (mostrarOcultas || !estaOculta);

    tr.style.display = visivel ? '' : 'none';
    tr.classList.toggle('linha-oculta', estaOculta);
  });

  const cont = document.getElementById('contadorOcultas');
  if (cont) cont.textContent = String(nOcultas);
}

// Reexibe (desoculta) uma ocorrencia a partir do botao da linha.
function reexibirOc(btn) {
  const set = ocOcultasSet();
  set.delete(btn.dataset.id);
  salvarOcOcultas(set);
  filtrarOcAdmin();
}

function atualizarStatus(selectEl) {
  // Feedback visual simples
  const tr = selectEl.closest('tr');
  if (tr) {
    tr.style.background = '#f0fdf4';
    setTimeout(() => tr.style.background = '', 1000);
  }
}

// Monta o badge colorido de urgencia.
function urgenciaBadgeOc(u) {
  const chave = (u || '').toUpperCase();
  const map = {
    ALTA:  ['🔴 Alta',  '#fee2e2', '#b91c1c'],
    MEDIA: ['🟡 Média', '#fef3c7', '#92400e'],
    BAIXA: ['🟢 Baixa', '#dcfce7', '#15803d'],
  };
  const m = map[chave] || [u || '–', '#f1f5f9', '#334155'];
  return '<span class="oc-urg-badge" style="background:' + m[1] + ';color:' + m[2] + '">' + m[0] + '</span>';
}

function escHtml(s) {
  return String(s == null ? '' : s)
    .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

// Abre o modal com os dados REAIS da ocorrencia (lidos dos data-* do botao),
// incluindo as fotos de evidencia. Layout compacto em 2 colunas.
function verOcorrencia(btn) {
  const d = btn.dataset;
  const modal = document.getElementById('modalOcorrencia');
  const body = document.getElementById('modalOcBody');
  if (!modal || !body) return;

  let fotos = [];
  try { fotos = JSON.parse(d.fotos || '[]'); } catch (e) { fotos = []; }

  let html = '<div class="oc-det">';

  // Coluna de dados (campos compactos em 2 colunas).
  html +=
    '<div class="oc-det-grid">' +
      '<div class="oc-det-item"><span>Protocolo</span><strong>#' + escHtml(d.protocolo) + '</strong></div>' +
      '<div class="oc-det-item"><span>Tipo</span><strong>' + escHtml(d.tipo) + '</strong></div>' +
      '<div class="oc-det-item"><span>Bairro</span><strong>' + escHtml(d.bairro) + '</strong></div>' +
      '<div class="oc-det-item"><span>Urgência</span>' + urgenciaBadgeOc(d.urgencia) + '</div>' +
      '<div class="oc-det-item oc-det-wide"><span>Local</span><strong>' + escHtml(d.local) + '</strong></div>' +
    '</div>';

  // Descricao (largura total, com fundo).
  html += '<div class="oc-det-desc"><span>Descrição</span><p>' + escHtml(d.descricao) + '</p></div>';

  // Fotos de evidencia (abrem em tamanho real em nova aba ao clicar).
  if (fotos.length) {
    html += '<div class="oc-fotos-admin"><strong>📷 Fotos da ocorrência (' + fotos.length + ')</strong><div class="oc-fotos-grid">';
    html += fotos.map(function (url) {
      return '<a href="' + url + '" target="_blank" rel="noopener" title="Abrir em tamanho real">' +
             '<img src="' + url + '" alt="Foto da ocorrência" loading="lazy"></a>';
    }).join('');
    html += '</div></div>';
  } else {
    html += '<p class="oc-sem-foto">📷 Nenhuma foto enviada para esta ocorrência.</p>';
  }

  html += '</div>';
  body.innerHTML = html;

  // Pre-carrega o form de gestao com os valores atuais.
  const idEl = document.getElementById('modalIdOc');
  const stEl = document.getElementById('modalStatus');
  const obEl = document.getElementById('modalObs');
  if (idEl) idEl.value = d.id || '';
  if (stEl) stEl.value = d.status || 'NOVA';
  if (obEl) obEl.value = d.obs || '';

  modal.style.display = 'flex';
}

// Clique no quadradinho de foto da lista: abre o modal da propria linha.
function verOcorrenciaDaLinha(el) {
  const tr = el.closest('tr');
  const btn = tr ? tr.querySelector('.btn-acao.ver') : null;
  if (btn) verOcorrencia(btn);
}

// Inicializacao da pagina de ocorrencias do admin.
document.addEventListener('DOMContentLoaded', function () {
  if (!document.getElementById('tabelaOcorrencias')) return;

  // Se veio de uma alteracao de status (?oc=ID), marca a ocorrencia como oculta
  // e limpa o parametro da URL para nao repetir ao recarregar.
  try {
    const params = new URLSearchParams(window.location.search);
    const oc = params.get('oc');
    if (oc) {
      const set = ocOcultasSet();
      set.add(oc);
      salvarOcOcultas(set);
      params.delete('oc');
      params.delete('ok');
      const nova = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
      window.history.replaceState({}, '', nova);
    }
  } catch (e) {}

  filtrarOcAdmin();
});

// =============================================
// MATERIAIS ADMIN
// =============================================
function abrirModalMaterial(nome) {
  const modal = document.getElementById('modalMaterial');
  const titulo = document.getElementById('modalMaterialTitulo');

  if (modal) {
    if (nome) {
      if (titulo) titulo.textContent = 'Editar Material';
      const input = document.getElementById('matNome');
      if (input) input.value = nome;
    } else {
      if (titulo) titulo.textContent = 'Cadastrar Material';
      const form = modal.querySelectorAll('input, select, textarea');
      form.forEach(el => { if (el.tagName !== 'SELECT') el.value = ''; });
    }
    modal.style.display = 'flex';
  }
}

function editarMaterial(nome) {
  abrirModalMaterial(nome);
}

function salvarMaterial() {
  const nome = document.getElementById('matNome')?.value.trim();
  if (!nome) {
    alert('Informe o nome do material.');
    return;
  }
  fecharModalAdmin();
  mostrarToast('Material salvo com sucesso!');
}

function filtrarMateriais() {
  const busca = (document.getElementById('buscaMaterial')?.value || '').toLowerCase();
  const filtro = document.getElementById('filtroAceito')?.value || '';

  const linhas = document.querySelectorAll('#tabelaMateriais tbody tr');
  linhas.forEach(tr => {
    const texto = tr.textContent.toLowerCase();
    const matchBusca = !busca || texto.includes(busca);
    tr.style.display = matchBusca ? '' : 'none';
  });
}

// =============================================
// COLETA ADMIN
// =============================================
function abrirModalColeta(bairro) {
  const modal = document.getElementById('modalColeta');
  const titulo = document.getElementById('modalColetaTitulo');

  if (modal) {
    if (bairro) {
      if (titulo) titulo.textContent = 'Editar Horário de Coleta';
      const input = document.getElementById('coletaBairro');
      if (input) input.value = bairro;
    } else {
      if (titulo) titulo.textContent = 'Cadastrar Horário de Coleta';
    }
    modal.style.display = 'flex';
  }
}

function editarColeta(bairro) {
  abrirModalColeta(bairro);
}

function salvarColeta() {
  const bairro = document.getElementById('coletaBairro')?.value.trim();
  if (!bairro) {
    alert('Informe o bairro.');
    return;
  }
  fecharModalAdmin();
  mostrarToast('Horário de coleta salvo!');
}

// =============================================
// PONTOS ADMIN
// =============================================
function abrirModalPonto(nome) {
  const modal = document.getElementById('modalPonto');
  const titulo = document.getElementById('modalPontoTitulo');

  if (modal) {
    if (nome) {
      if (titulo) titulo.textContent = 'Editar Ponto de Descarte';
      const input = document.getElementById('pontoNome');
      if (input) input.value = nome;
    } else {
      if (titulo) titulo.textContent = 'Cadastrar Ponto de Descarte';
    }
    modal.style.display = 'flex';
  }
}

function editarPonto(nome) {
  abrirModalPonto(nome);
}

function salvarPonto() {
  const nome = document.getElementById('pontoNome')?.value.trim();
  if (!nome) {
    alert('Informe o nome do local.');
    return;
  }
  fecharModalAdmin();
  mostrarToast('Ponto de descarte salvo!');
}

// =============================================
// EXCLUIR ITEM (visual apenas)
// =============================================
function excluirItem(btn) {
  if (!confirm('Deseja realmente excluir este item?')) return;
  const tr = btn.closest('tr');
  if (tr) {
    tr.style.opacity = '0';
    tr.style.transition = 'opacity 0.3s';
    setTimeout(() => tr.remove(), 300);
  }
}

// =============================================
// TOAST NOTIFICATION
// =============================================
function mostrarToast(mensagem) {
  let toast = document.getElementById('adminToast');

  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'adminToast';
    toast.style.cssText = `
      position: fixed;
      bottom: 24px;
      right: 24px;
      background: #1e293b;
      color: #fff;
      padding: 12px 20px;
      border-radius: 10px;
      font-size: 0.875rem;
      font-weight: 600;
      box-shadow: 0 4px 16px rgba(0,0,0,0.2);
      z-index: 9999;
      opacity: 0;
      transition: opacity 0.3s;
    `;
    document.body.appendChild(toast);
  }

  toast.textContent = '✅ ' + mensagem;
  toast.style.opacity = '1';

  clearTimeout(toast._timeout);
  toast._timeout = setTimeout(() => {
    toast.style.opacity = '0';
  }, 3000);
}
