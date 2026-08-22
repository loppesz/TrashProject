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
function filtrarOcAdmin() {
  const busca = (document.getElementById('buscaOc')?.value || '').toLowerCase();
  const status = (document.getElementById('filtroStatus')?.value || '').toLowerCase();

  const linhas = document.querySelectorAll('#tabelaOcorrencias tbody tr');
  linhas.forEach(tr => {
    const texto = tr.textContent.toLowerCase();
    const select = tr.querySelector('.status-select');
    const statusValor = select ? select.value.toLowerCase() : '';

    const matchBusca = !busca || texto.includes(busca);
    const matchStatus = !status || statusValor.includes(status.replace('-', ' '));

    tr.style.display = matchBusca && matchStatus ? '' : 'none';
  });
}

function atualizarStatus(selectEl) {
  // Feedback visual simples
  const tr = selectEl.closest('tr');
  if (tr) {
    tr.style.background = '#f0fdf4';
    setTimeout(() => tr.style.background = '', 1000);
  }
}

function verOcorrencia(protocolo) {
  const modal = document.getElementById('modalOcorrencia');
  const body = document.getElementById('modalOcBody');

  if (modal && body) {
    body.innerHTML = `
      <div style="margin-bottom:16px;">
        <p><strong>Protocolo:</strong> #${protocolo}</p>
        <p><strong>Tipo:</strong> Descarte irregular</p>
        <p><strong>Local:</strong> Rua das Palmeiras, 50</p>
        <p><strong>Bairro:</strong> Centro</p>
        <p><strong>Data:</strong> 20/08/2026 às 14:30</p>
        <p><strong>Descrição:</strong> Resíduos descartados de forma inadequada na calçada, bloqueando a passagem.</p>
      </div>
    `;
    modal.style.display = 'flex';
  }
}

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
