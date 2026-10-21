// =============================================
// DADOS HISTÓRICO FAKE
// =============================================
const HISTORICO_FAKE = [
  { id:'CF-2026-0001', tipo:'descarte-irregular', tipoLabel:'Descarte irregular', bairro:'Centro',       local:'Rua das Palmeiras, 50',    urgencia:'alta',  status:'andamento', data:'20/08/2026', desc:'Lixo hospitalar descartado na calçada, bloqueando passagem.' },
  { id:'CF-2026-0002', tipo:'coleta',            tipoLabel:'Coleta não realizada', bairro:'Bairro Novo',  local:'Av. Brasil, 200',           urgencia:'media', status:'resolvida', data:'18/08/2026', desc:'Coleta seletiva não passou no dia previsto (quinta-feira).' },
  { id:'CF-2026-0003', tipo:'descarte',           tipoLabel:'Descarte irregular', bairro:'Vila Nova',    local:'Rua do Comércio, 15',        urgencia:'alta',  status:'nova',      data:'21/08/2026', desc:'Entulho de obra depositado em área pública.' },
  { id:'CF-2026-0004', tipo:'entulho',            tipoLabel:'Entulho/Móveis',     bairro:'Jardim América',local:'Praça da Amizade',          urgencia:'media', status:'analise',   data:'21/08/2026', desc:'Móveis velhos e entulho abandonados na praça.' },
  { id:'CF-2026-0005', tipo:'toxico',             tipoLabel:'Lixo tóxico',        bairro:'Centro',       local:'Rua da Saúde, 80',           urgencia:'alta',  status:'resolvida', data:'17/08/2026', desc:'Embalagens de produto químico jogadas em terreno baldio.' },
  { id:'CF-2026-0006', tipo:'coleta',             tipoLabel:'Coleta não realizada',bairro:'Bairro Escolar',local:'Rua da Educação',         urgencia:'baixa', status:'resolvida', data:'16/08/2026', desc:'Caminhão passou mas não recolheu todo o material.' },
  { id:'CF-2026-0007', tipo:'descarte',           tipoLabel:'Descarte irregular', bairro:'Centro',       local:'Av. Central, 300',           urgencia:'media', status:'analise',   data:'19/08/2026', desc:'Descarte de lixo doméstico fora do dia da coleta.' },
  { id:'CF-2026-0008', tipo:'entulho',            tipoLabel:'Entulho/Móveis',     bairro:'Vila Nova',    local:'Rua do Progresso, 120',      urgencia:'baixa', status:'nova',      data:'21/08/2026', desc:'Sofá e camas descartados na calçada há 3 dias.' },
];

const TIPO_ICONS = { 'descarte-irregular':'🗑️', 'coleta':'🚛', 'descarte':'🗑️', 'entulho':'🧱', 'toxico':'⚠️', 'default':'📋' };
const TIPO_CORES = { 'descarte-irregular':'#fee2e2', 'coleta':'#dbeafe', 'entulho':'#fef3c7', 'toxico':'#fff7ed', 'default':'#f1f5f9' };
const TIPO_INFO  = {
  'descarte-irregular': '⚡ Ocorrência frequente. Fotos ajudam muito na resolução!',
  'coleta-nao-realizada': '📅 Verifique o <a href="coleta.php">calendário de coleta</a> antes de registrar.',
  'material-nao-recolhido': '♻️ Materiais deixados para trás geralmente são recolhidos no próximo turno.',
  'ponto-inadequado': '📍 Inclua o endereço exato do ponto de descarte com problema.',
  'entulho': '🧱 Entulho de obras requer coleta especial — informe tamanho aproximado.',
  'lixo-toxico': '⚠️ URGENTE! Produtos tóxicos oferecem risco de saúde. Considere ligar 156.',
  'outros': '💬 Detalhe ao máximo na descrição.',
};

let minhasOcorrenciasArr = JSON.parse(sessionStorage.getItem('minhasOc') || '[]');

// =============================================
// ABAS
// =============================================
function switchTab(tab, btn) {
  document.querySelectorAll('.tab-content').forEach(t => t.style.display = 'none');
  document.querySelectorAll('.oc-tab').forEach(b => b.classList.remove('active'));
  document.getElementById('tab-' + tab).style.display = 'block';
  if (btn) btn.classList.add('active');
  if (tab === 'historico') renderHistorico();
}

// =============================================
// TIPO INFO DINÂMICO
// =============================================
function atualizarTipoInfo() {
  const val = document.getElementById('tipoOcorrencia')?.value;
  const box = document.getElementById('tipoInfo');
  if (!box) return;
  if (val && TIPO_INFO[val]) {
    box.innerHTML = TIPO_INFO[val];
    box.style.display = 'block';
  } else {
    box.style.display = 'none';
  }
  // Atualiza pontos
  const pts = val === 'lixo-toxico' ? 80 : val === 'entulho' ? 60 : val === 'coleta-nao-realizada' ? 40 : 50;
  const fotoExtra = document.getElementById('fotoInput')?.files?.length > 0 ? 20 : 0;
  const el = document.getElementById('pontosPreview');
  if (el) el.textContent = (pts + fotoExtra) + ' pts';
}

// =============================================
// FOTO UPLOAD
// =============================================
function previewFoto(input) {
  if (!input.files || !input.files[0]) return;
  const reader = new FileReader();
  reader.onload = (e) => {
    document.getElementById('fotoImg').src = e.target.result;
    document.getElementById('fotoPreview').style.display = 'block';
    document.getElementById('fotoArea').style.display = 'none';
  };
  reader.readAsDataURL(input.files[0]);
  atualizarTipoInfo(); // atualiza pontos com foto
}

function removerFoto() {
  document.getElementById('fotoInput').value = '';
  document.getElementById('fotoPreview').style.display = 'none';
  document.getElementById('fotoArea').style.display = 'block';
  atualizarTipoInfo();
}

// =============================================
// CONTADOR DE CARACTERES
// =============================================
document.getElementById('descricaoOcorrencia')?.addEventListener('input', function() {
  document.getElementById('charCount').textContent = Math.min(500, this.value.length);
  if (this.value.length > 500) this.value = this.value.substring(0, 500);
});

// =============================================
// USAR LOCALIZAÇÃO
// =============================================
function usarLocalizacao() {
  const btn = document.querySelector('.use-location-btn');
  const resetBtn = () => { if (btn) { btn.textContent = '📡 Usar minha localização'; btn.disabled = false; } };

  // A API de geolocalização só funciona em contexto seguro (HTTPS ou localhost).
  // Em http://host (ex.: VirtualHost), o navegador bloqueia e o botão "não funciona".
  if (!navigator.geolocation || (typeof isSecureContext !== 'undefined' && !isSecureContext)) {
    showOcToast('📍 A localização automática exige HTTPS. Digite o endereço manualmente.');
    resetBtn();
    return;
  }

  if (btn) { btn.textContent = '📡 Buscando...'; btn.disabled = true; }

  navigator.geolocation.getCurrentPosition(
    (pos) => {
      // Privacidade: a coordenada vai para campos ocultos (uso interno da
      // equipe) e NUNCA é colada no endereço, que é exibido publicamente.
      const lat = pos.coords.latitude.toFixed(6);
      const lng = pos.coords.longitude.toFixed(6);
      const latEl = document.getElementById('latOcorrencia');
      const lngEl = document.getElementById('lngOcorrencia');
      if (latEl) latEl.value = lat;
      if (lngEl) lngEl.value = lng;
      const aviso = document.getElementById('gpsAviso');
      if (aviso) aviso.style.display = 'block';
      if (btn) { btn.textContent = '✅ Localização anexada'; btn.disabled = false; }
      showOcToast('📍 Localização anexada (uso interno).');
    },
    (err) => {
      resetBtn();
      // Mensagem específica conforme o motivo da falha.
      let msg = 'Não foi possível obter a localização. Preencha o endereço manualmente.';
      if (err) {
        if (err.code === 1) msg = '🚫 Permissão de localização negada. Libere no ícone do navegador ou digite o endereço.';
        else if (err.code === 2) msg = '📡 Localização indisponível no momento. Tente novamente ou digite o endereço.';
        else if (err.code === 3) msg = '⏱️ A busca pela localização demorou demais. Tente de novo.';
      }
      showOcToast(msg);
    },
    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
  );
}

// Remove a localização GPS anexada.
function limparLocalizacao() {
  const latEl = document.getElementById('latOcorrencia');
  const lngEl = document.getElementById('lngOcorrencia');
  if (latEl) latEl.value = '';
  if (lngEl) lngEl.value = '';
  const aviso = document.getElementById('gpsAviso');
  if (aviso) aviso.style.display = 'none';
  const btn = document.querySelector('.use-location-btn');
  if (btn) btn.textContent = '📡 Usar minha localização';
  showOcToast('Localização removida.');
}

// =============================================
// REGISTRAR OCORRÊNCIA
// =============================================
function registrarOcorrencia(event) {
  event.preventDefault();

  const tipo      = document.getElementById('tipoOcorrencia').value;
  const local     = document.getElementById('localOcorrencia').value;
  const bairro    = document.getElementById('bairroOcorrencia').value;
  const urgencia  = document.getElementById('urgencia').value;
  const descricao = document.getElementById('descricaoOcorrencia').value;
  const temFoto   = document.getElementById('fotoInput').files?.length > 0;

  const protocolo = 'CF-' + new Date().getFullYear() + '-' + String(Math.floor(Math.random() * 9000) + 1000);
  const pts = (tipo === 'lixo-toxico' ? 80 : tipo === 'entulho' ? 60 : tipo === 'coleta-nao-realizada' ? 40 : 50) + (temFoto ? 20 : 0);

  // Salva na sessão
  const novaOc = {
    id: protocolo,
    tipo, local, bairro, urgencia,
    status: 'nova',
    data: new Date().toLocaleDateString('pt-BR'),
    desc: descricao,
  };
  minhasOcorrenciasArr.unshift(novaOc);
  sessionStorage.setItem('minhasOc', JSON.stringify(minhasOcorrenciasArr));

  // Exibe modal
  document.getElementById('protocoloGerado').textContent = protocolo;
  document.getElementById('pontosGanhos').textContent = pts;
  document.getElementById('modalSucesso').style.display = 'flex';

  // Reseta form
  document.getElementById('ocorrenciaForm').reset();
  document.getElementById('tipoInfo').style.display = 'none';
  removerFoto();
}

function fecharModalSucesso() {
  document.getElementById('modalSucesso').style.display = 'none';
  renderMinhasOcorrencias();
}

function copiarProtocolo() {
  const prot = document.getElementById('protocoloGerado').textContent;
  navigator.clipboard.writeText(prot).then(() => showOcToast('✅ Protocolo copiado!'));
}

// =============================================
// CONSULTAR STATUS
// =============================================
const STATUS_DATA = {
  'CF-2026-0001': HISTORICO_FAKE[0],
  'CF-2026-0002': HISTORICO_FAKE[1],
  'CF-2026-0003': HISTORICO_FAKE[2],
  'CF-2026-0004': HISTORICO_FAKE[3],
  'CF-2026-0005': HISTORICO_FAKE[4],
};

function consultarProtocolo() {
  const prot = document.getElementById('protocoloInput').value.trim().toUpperCase();
  document.getElementById('statusResultado').style.display = 'none';
  document.getElementById('statusNotFound').style.display = 'none';
  if (!prot) return;

  // Consulta real no banco (endpoint JSON do servidor).
  const base = (window.BASEURL || '/');
  fetch(base + 'ocorrencia/consultarJson?protocolo=' + encodeURIComponent(prot))
    .then(function (r) { return r.json(); })
    .then(function (d) {
      if (!d || !d.ok) {
        document.getElementById('statusNotFound').style.display = 'block';
        return;
      }
      document.getElementById('srProtocolo').textContent = d.protocolo;
      document.getElementById('srLocal').textContent  = d.local || '–';
      document.getElementById('srTipo').textContent   = d.tipo || '–';
      document.getElementById('srData').textContent   = d.data || '–';
      document.getElementById('srEquipe').textContent = 'Equipe ReciclaVida';

      const badge = document.getElementById('srBadge');
      const badgeMap = { nova:'🔴 Nova', analise:'🟡 Em análise', andamento:'🔵 Em atendimento', resolvida:'🟢 Resolvida' };
      badge.textContent = badgeMap[d.status] || d.status;
      badge.className   = 'sr-badge ' + d.status;

      renderTimeline(d.status, d.timeline);
      document.getElementById('srObs').textContent = d.obs
        ? d.obs
        : (d.status === 'resolvida'
            ? '✅ Esta ocorrência foi resolvida. Obrigado pela sua contribuição!'
            : 'Nossa equipe está acompanhando esta ocorrência. Você será notificado de atualizações.');

      document.getElementById('statusResultado').style.display = 'block';
    })
    .catch(function () {
      document.getElementById('statusNotFound').style.display = 'block';
    });
}

// Metadados de cada etapa da timeline.
const ETAPA_META = {
  nova:      { icon:'📋', label:'Registrada' },
  analise:   { icon:'🔍', label:'Em análise' },
  andamento: { icon:'🚛', label:'Em atendimento' },
  resolvida: { icon:'✅', label:'Resolvida' },
};

function renderTimeline(status, historico) {
  // Quando ha historico real do banco, mostra os eventos efetivamente ocorridos.
  if (Array.isArray(historico) && historico.length) {
    const html = historico.map((h, i) => {
      const meta = ETAPA_META[h.status] || { icon:'📋', label:h.status };
      const atual = i === historico.length - 1;
      const detalhe = h.obs ? h.obs : (atual ? 'Status atual' : 'Concluído');
      return `<div class="str-item">
        <div class="str-dot ${atual ? 'active' : 'done'}">${meta.icon}</div>
        <div class="str-content">
          <strong>${meta.label}</strong>
          <span>${h.data || ''}${h.data && detalhe ? ' • ' : ''}${detalhe}</span>
        </div>
      </div>`;
    }).join('');
    document.getElementById('srTimeline').innerHTML = html;
    return;
  }

  // Fallback: timeline derivada apenas do status atual.
  const etapas = [
    { key:'nova',      icon:'📋', label:'Registrada',       done:true },
    { key:'analise',   icon:'🔍', label:'Em análise',       done:['analise','andamento','resolvida'].includes(status) },
    { key:'andamento', icon:'🚛', label:'Em atendimento',   done:['andamento','resolvida'].includes(status) },
    { key:'resolvida', icon:'✅', label:'Resolvida',        done:status === 'resolvida' },
  ];

  const atualIdx = etapas.findIndex(e => e.key === status);

  const html = etapas.map((e, i) => {
    const cls = e.done ? 'done' : (i === atualIdx ? 'active' : 'pending');
    return `<div class="str-item">
      <div class="str-dot ${cls}">${e.icon}</div>
      <div class="str-content">
        <strong>${e.label}</strong>
        <span>${e.done && i < atualIdx ? 'Concluído' : i === atualIdx ? 'Status atual' : 'Aguardando'}</span>
      </div>
    </div>`;
  }).join('');

  document.getElementById('srTimeline').innerHTML = html;
}

// =============================================
// MINHAS OCORRÊNCIAS (SESSÃO)
// =============================================
function renderMinhasOcorrencias() {
  const container = document.getElementById('minhasOcorrencias');
  if (!container) return;

  if (minhasOcorrenciasArr.length === 0) {
    container.innerHTML = '<div class="mo-vazio">Você ainda não registrou nenhuma ocorrência nesta sessão.</div>';
    return;
  }

  container.innerHTML = minhasOcorrenciasArr.slice(0, 5).map(oc => `
    <div class="mo-item" onclick="document.getElementById('protocoloInput').value='${oc.id}'; switchTab('consultar', document.querySelectorAll('.oc-tab')[1])">
      <strong>${oc.tipo || 'Ocorrência'} – ${oc.bairro}</strong>
      <small>
        <span>${oc.id}</span>
        <span>${oc.data}</span>
      </small>
    </div>
  `).join('');
}

// =============================================
// HISTÓRICO PÚBLICO
// =============================================
// Usa os dados reais do banco (window.HISTORICO_DB) quando disponiveis;
// caso contrario, recai nos dados de exemplo.
const HISTORICO_BASE = (Array.isArray(window.HISTORICO_DB) && window.HISTORICO_DB.length)
  ? window.HISTORICO_DB
  : HISTORICO_FAKE;
let historicoFiltrado = [...HISTORICO_BASE];

function renderHistorico() {
  const lista = document.getElementById('historicoLista');
  if (!lista) return;

  if (historicoFiltrado.length === 0) {
    lista.innerHTML = '<div style="text-align:center;padding:40px;color:#94a3b8;">Nenhuma ocorrência encontrada.</div>';
    return;
  }

  lista.innerHTML = historicoFiltrado.map(oc => {
    const icon = TIPO_ICONS[oc.tipo] || TIPO_ICONS.default;
    const bg   = TIPO_CORES[oc.tipo] || TIPO_CORES.default;
    return `
      <div class="hc-item">
        <div class="hc-type-icon" style="background:${bg}">${icon}</div>
        <div class="hc-body">
          <div class="hc-title-row">
            <strong>${oc.tipoLabel}</strong>
            <span class="hc-status ${oc.status}">${statusLabel(oc.status)}</span>
          </div>
          <div class="hc-meta">
            <span>📍 ${oc.local} – ${oc.bairro}</span>
            <span>💬 ${oc.desc.substring(0, 60)}...</span>
          </div>
        </div>
        <div class="hc-side">
          <span class="hc-data">${oc.data}</span>
          <span class="hc-urgencia ${oc.urgencia}">${urgLabel(oc.urgencia)}</span>
        </div>
      </div>`;
  }).join('');
}

function statusLabel(s) {
  return { nova:'🔴 Nova', analise:'🟡 Em análise', andamento:'🔵 Atendimento', resolvida:'🟢 Resolvida' }[s] || s;
}

function urgLabel(u) {
  return { alta:'🔴 Alta', media:'🟡 Média', baixa:'🟢 Baixa' }[u] || u;
}

function filtrarHistorico() {
  const busca   = (document.getElementById('buscaHistorico')?.value || '').toLowerCase();
  const status  = document.getElementById('filtroStatusH')?.value || '';
  const tipo    = document.getElementById('filtroTipoH')?.value || '';
  const bairro  = document.getElementById('filtroBairroH')?.value || '';

  historicoFiltrado = HISTORICO_BASE.filter(oc => {
    const txt = (oc.bairro + oc.local + oc.tipoLabel + oc.desc).toLowerCase();
    return (!busca  || txt.includes(busca))
        && (!status || oc.status === status)
        && (!tipo   || oc.tipo.includes(tipo))
        && (!bairro || oc.bairro === bairro);
  });
  renderHistorico();
}

// =============================================
// TOAST
// =============================================
function showOcToast(msg) {
  const el = document.getElementById('ocToast');
  if (!el) return;
  el.textContent = msg; el.style.display = 'block'; el.style.opacity = '1';
  clearTimeout(el._t);
  el._t = setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.style.display = 'none', 300); }, 3000);
}

// =============================================
// FECHAR MODAL COM CLICK FORA
// =============================================
document.addEventListener('click', e => {
  if (e.target === document.getElementById('modalSucesso')) fecharModalSucesso();
});

// =============================================
// INIT
// =============================================
window.addEventListener('DOMContentLoaded', () => {
  renderMinhasOcorrencias();
  renderHistorico();

  // Preenche data de hoje
  const hoje = new Date().toISOString().split('T')[0];
  const dataInput = document.getElementById('dataOcorrencia');
  if (dataInput) dataInput.value = hoje;
});
