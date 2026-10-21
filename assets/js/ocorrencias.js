// Dados do histórico e do mapa são fornecidos pelo servidor.
// Banco vazio deve permanecer vazio: não exibimos ocorrências demonstrativas como fatos reais.

const TIPO_ICONS = { 'descarte-irregular':'!', 'coleta':'C', 'descarte':'D', 'entulho':'E', 'toxico':'!', 'default':'•' };
const TIPO_INFO  = {
  'descarte-irregular': 'Fotos e localização precisa ajudam a equipe a verificar o relato.',
  'coleta-nao-realizada': 'Verifique o calendário de coleta de Muriaé antes de registrar.',
  'material-nao-recolhido': 'Informe quais materiais ficaram no local e o horário aproximado.',
  'ponto-inadequado': 'Inclua o endereço exato do ponto de descarte com problema.',
  'entulho': 'Informe o volume aproximado e se o material bloqueia a passagem.',
  'lixo-toxico': 'Situação de risco: evite contato e, se necessário, ligue 156.',
  'outros': 'Descreva a situação com o máximo de detalhes.',
};

// =============================================
// ABAS
// =============================================
function switchTab(tab, btn) {
  document.querySelectorAll('.tab-content').forEach(t => t.style.display = 'none');
  document.querySelectorAll('.oc-tab').forEach(b => b.classList.remove('active'));
  document.getElementById('tab-' + tab).style.display = 'block';
  if (btn) btn.classList.add('active');
  if (tab === 'historico') renderHistorico();
  if (tab === 'mapa') {
    initMapaOcorrencias();
    setTimeout(() => mapaOcorrencias?.invalidateSize(), 80);
  }
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
  atualizarPontosPreview();
}

// Prévia dos pontos com as mesmas regras do servidor (window.REGRAS_PONTOS):
// uma parte ao registrar (com limite diário) e o restante na confirmação.
function atualizarPontosPreview() {
  const el = document.getElementById('pontosPreview');
  const det = document.getElementById('pontosPreviewDetalhe');
  if (!el) return;
  const r = window.REGRAS_PONTOS || { registro: 10, resolvida: 40, foto: 15, restantesHoje: 3, logado: false };
  const anonima = document.getElementById('anonimo')?.checked;
  const temFoto = document.getElementById('fotoInput')?.files?.length > 0;

  if (!r.logado || anonima) {
    el.textContent = '0 pts';
    if (det) det.textContent = anonima ? 'Registro anônimo não soma pontos' : 'Entre na sua conta para somar pontos';
    return;
  }
  const agora = r.restantesHoje > 0 ? r.registro : 0;
  const depois = r.resolvida + (temFoto ? r.foto : 0);
  el.textContent = 'até ' + (agora + depois) + ' pts';
  if (det) {
    det.textContent = (agora > 0 ? agora + ' agora' : '0 agora (limite do dia)')
      + ' + ' + depois + ' quando a equipe confirmar' + (temFoto ? ' (com foto)' : '');
  }
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
let mapaLocalizacao = null;
let marcadorLocalizacao = null;
let circuloPrecisao = null;
let gpsWatchId = null;
let gpsTimeoutId = null;

function dentroDeMuriae(lat, lng) {
  const limites = window.MURIAE_GEO?.bounds || [[-21.31, -42.57], [-20.86, -42.25]];
  return lat >= limites[0][0] && lat <= limites[1][0] && lng >= limites[0][1] && lng <= limites[1][1];
}

function normalizarTexto(valor) {
  return String(valor || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
}

// Remove prefixos que o mapa às vezes acrescenta ("Bairro Centro", "Distrito de Vermelho").
function limparNomeBairro(valor) {
  return normalizarTexto(valor)
    .replace(/^(bairro|distrito( de| do| da)?|comunidade( de| do| da)?|loteamento)\s+/, '')
    .replace(/\s+/g, ' ')
    .trim();
}

function contemPalavras(texto, trecho) {
  const esc = trecho.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  return new RegExp('(^|\\s)' + esc + '(\\s|$)').test(texto);
}

/**
 * Procura, entre as opções do select de bairros (lista oficial do banco),
 * o bairro que corresponde a algum dos nomes devolvidos pelo mapa.
 * Ordem de confiança: nome idêntico > bairro contido no nome do mapa
 * (o mais longo vence: "Porto Belo" antes de "Porto") > nome do mapa
 * contido em um único bairro.
 */
function encontrarBairro(candidatos) {
  const select = document.getElementById('bairroOcorrencia');
  if (!select) return null;
  const opcoes = Array.from(select.options)
    .filter(o => o.value)
    .map(o => ({ valor: o.value, nome: limparNomeBairro(o.value) }));
  const nomes = candidatos.map(limparNomeBairro).filter(n => n.length >= 3);

  for (const n of nomes) {
    const exata = opcoes.find(o => o.nome === n);
    if (exata) return exata.valor;
  }
  for (const n of nomes) {
    const contidas = opcoes.filter(o => contemPalavras(n, o.nome)).sort((a, b) => b.nome.length - a.nome.length);
    if (contidas.length) return contidas[0].valor;
  }
  for (const n of nomes) {
    const contem = opcoes.filter(o => contemPalavras(o.nome, n));
    if (contem.length === 1) return contem[0].valor;
  }
  return null;
}

// Mostra ao usuário se o bairro foi identificado ou se precisa escolher.
function sinalizarBairro(encontrado) {
  const select = document.getElementById('bairroOcorrencia');
  const status = document.getElementById('gpsStatus');
  if (select) select.classList.toggle('campo-atencao', !encontrado);
  if (status) {
    status.textContent = encontrado
      ? 'Localização confirmada · bairro ' + encontrado + ' (confira)'
      : 'Localização confirmada · selecione o bairro na lista';
  }
  if (!encontrado && select) select.focus({ preventScroll: true });
}

function atualizarPreviewLocalizacao(lat, lng, accuracy) {
  const el = document.getElementById('localizacaoPreview');
  if (!el || typeof L === 'undefined') return;
  el.style.display = 'block';
  if (!mapaLocalizacao) {
    mapaLocalizacao = L.map(el, {
      center: [lat, lng], zoom: 17, maxBounds: window.MURIAE_GEO?.bounds,
      maxBoundsViscosity: 1, scrollWheelZoom: false, attributionControl: true,
    });
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19, attribution: '&copy; OpenStreetMap'
    }).addTo(mapaLocalizacao);
  }
  if (marcadorLocalizacao) mapaLocalizacao.removeLayer(marcadorLocalizacao);
  if (circuloPrecisao) mapaLocalizacao.removeLayer(circuloPrecisao);
  marcadorLocalizacao = L.marker([lat, lng], { draggable: true }).addTo(mapaLocalizacao);
  circuloPrecisao = L.circle([lat, lng], { radius: accuracy || 20, color: '#2563eb', fillOpacity: 0.12 }).addTo(mapaLocalizacao);
  mapaLocalizacao.setView([lat, lng], Math.min(18, accuracy > 80 ? 16 : 18));
  marcadorLocalizacao.on('dragend', function () {
    const p = marcadorLocalizacao.getLatLng();
    if (!dentroDeMuriae(p.lat, p.lng)) {
      showOcToast('O ponto precisa permanecer dentro da área de Muriaé.');
      marcadorLocalizacao.setLatLng([lat, lng]);
      return;
    }
    aplicarCoordenadas(p.lat, p.lng, Math.max(accuracy || 20, 20), true);
  });
  setTimeout(() => mapaLocalizacao.invalidateSize(), 100);
}

async function preencherEnderecoPorCoordenadas(lat, lng) {
  const url = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' +
    encodeURIComponent(lat) + '&lon=' + encodeURIComponent(lng) + '&zoom=18&addressdetails=1&accept-language=pt-BR';
  const resposta = await fetch(url, { headers: { 'Accept': 'application/json' } });
  if (!resposta.ok) throw new Error('geocodificacao');
  const dados = await resposta.json();
  const a = dados.address || {};
  const cidade = a.city || a.town || a.municipality || '';
  if (cidade && !normalizarTexto(cidade).includes('muriae')) throw new Error('fora_muriae');

  const rua = a.road || a.pedestrian || a.footway || a.residential || '';
  const numero = a.house_number || '';
  const endereco = [rua, numero].filter(Boolean).join(', ');

  const enderecoEl = document.getElementById('localOcorrencia');
  const cidadeEl = document.getElementById('cidadeOcorrencia');
  if (enderecoEl && endereco) enderecoEl.value = endereco;
  if (cidadeEl) cidadeEl.value = 'Muriaé - MG';

  // Todos os campos em que o OpenStreetMap costuma informar o bairro/distrito,
  // do mais específico para o mais genérico, mais as partes do endereço completo.
  const ignorar = ['muriae', 'minas gerais', 'brasil', 'regiao sudeste'];
  const partes = String(dados.display_name || '').split(',')
    .map(p => p.trim())
    .filter(p => p && !/^\d/.test(p) && !ignorar.some(i => normalizarTexto(p).includes(i)));
  const candidatos = [
    a.suburb, a.neighbourhood, a.quarter, a.city_district, a.residential,
    a.village, a.hamlet, a.isolated_dwelling, a.allotments,
    ...partes,
  ].filter(Boolean);

  const bairro = encontrarBairro(candidatos);
  const select = document.getElementById('bairroOcorrencia');
  if (bairro && select) select.value = bairro;
  sinalizarBairro(bairro);
  return bairro;
}

async function aplicarCoordenadas(lat, lng, accuracy, geocodificar) {
  if (!dentroDeMuriae(lat, lng)) {
    showOcToast('A localização encontrada está fora da área de Muriaé-MG.');
    limparLocalizacao(false);
    return;
  }
  document.getElementById('latOcorrencia').value = Number(lat).toFixed(7);
  document.getElementById('lngOcorrencia').value = Number(lng).toFixed(7);
  document.getElementById('precisaoOcorrencia').value = Math.round(accuracy || 0);
  const aviso = document.getElementById('gpsAviso');
  const precisaoEl = document.getElementById('gpsPrecisao');
  if (aviso) aviso.style.display = 'flex';
  if (precisaoEl) precisaoEl.textContent = 'Precisão aproximada: ' + Math.round(accuracy || 0) + ' m';
  atualizarPreviewLocalizacao(lat, lng, accuracy);

  if (geocodificar) {
    try { await preencherEnderecoPorCoordenadas(lat, lng); }
    catch (e) {
      if (e.message === 'fora_muriae') showOcToast('O serviço de endereço indicou uma cidade diferente de Muriaé.');
      else showOcToast('Localização obtida. Revise e preencha o endereço manualmente.');
    }
  }
}

function pararBuscaGps() {
  if (gpsWatchId !== null) navigator.geolocation.clearWatch(gpsWatchId);
  if (gpsTimeoutId) clearTimeout(gpsTimeoutId);
  gpsWatchId = null;
  gpsTimeoutId = null;
}

function usarLocalizacao() {
  const btn = document.querySelector('.use-location-btn');
  if (!navigator.geolocation || (typeof isSecureContext !== 'undefined' && !isSecureContext)) {
    showOcToast('A localização automática exige HTTPS ou localhost.');
    return;
  }

  pararBuscaGps();
  if (btn) { btn.textContent = 'Buscando sinal preciso...'; btn.disabled = true; }
  let melhor = null;

  const finalizar = async () => {
    pararBuscaGps();
    if (btn) { btn.textContent = 'Atualizar localização'; btn.disabled = false; }
    if (!melhor) {
      showOcToast('Não foi possível obter a localização. Preencha o endereço manualmente.');
      return;
    }
    await aplicarCoordenadas(melhor.coords.latitude, melhor.coords.longitude, melhor.coords.accuracy, true);
    const bairroSel = document.getElementById('bairroOcorrencia')?.value;
    showOcToast(bairroSel
      ? 'Rua e bairro preenchidos. Revise antes de enviar.'
      : 'Rua preenchida. Selecione o bairro na lista.');
  };

  gpsWatchId = navigator.geolocation.watchPosition(
    pos => {
      if (!melhor || pos.coords.accuracy < melhor.coords.accuracy) melhor = pos;
      if (pos.coords.accuracy <= 25) finalizar();
    },
    err => {
      pararBuscaGps();
      if (btn) { btn.textContent = 'Usar localização atual'; btn.disabled = false; }
      const mensagens = {
        1: 'Permissão de localização negada. Libere-a no navegador ou digite o endereço.',
        2: 'Localização indisponível. Tente novamente em uma área aberta.',
        3: 'A busca demorou demais. Tente novamente.'
      };
      showOcToast(mensagens[err.code] || 'Não foi possível obter a localização.');
    },
    { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 }
  );
  gpsTimeoutId = setTimeout(finalizar, 9000);
}

function limparLocalizacao(mostrarMensagem = true) {
  pararBuscaGps();
  ['latOcorrencia', 'lngOcorrencia', 'precisaoOcorrencia'].forEach(id => {
    const el = document.getElementById(id); if (el) el.value = '';
  });
  const aviso = document.getElementById('gpsAviso');
  const preview = document.getElementById('localizacaoPreview');
  if (aviso) aviso.style.display = 'none';
  if (preview) preview.style.display = 'none';
  const btn = document.querySelector('.use-location-btn');
  if (btn) { btn.textContent = 'Usar localização atual'; btn.disabled = false; }
  if (mostrarMensagem) showOcToast('Localização removida. Você pode editar o endereço manualmente.');
}

function fecharModalSucesso() {
  document.getElementById('modalSucesso').style.display = 'none';
}

// Copia o protocolo e confirma no próprio modal (aviso + botão "Copiado!") e no toast.
function copiarProtocolo(btn) {
  const prot = (document.getElementById('protocoloGerado')?.textContent || '').trim();
  if (!prot) return;
  const aviso = document.getElementById('avisoCopiado');

  copiarParaAreaTransferencia(prot).then(ok => {
    if (aviso) {
      aviso.textContent = ok
        ? '✓ Protocolo ' + prot + ' copiado para a área de transferência.'
        : 'Não foi possível copiar automaticamente. Selecione o número e use Ctrl+C.';
      aviso.classList.toggle('erro', !ok);
      aviso.hidden = false;
      clearTimeout(aviso._t);
      aviso._t = setTimeout(() => { aviso.hidden = true; }, 4000);
    }
    showOcToast(ok ? '✅ Copiado para a área de transferência' : 'Não foi possível copiar o protocolo.');

    if (ok && btn) {
      if (!btn.dataset.rotulo) btn.dataset.rotulo = btn.textContent;
      btn.textContent = '✓ Copiado!';
      btn.classList.add('copiado');
      clearTimeout(btn._t);
      btn._t = setTimeout(() => {
        btn.textContent = btn.dataset.rotulo;
        btn.classList.remove('copiado');
      }, 2000);
    }
  });
}

// =============================================
// CONSULTAR STATUS
// =============================================
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
      document.getElementById('srEquipe').textContent = 'Atendimento municipal de Muriaé';

      // Descrição: o servidor só envia para o autor da ocorrência (logado).
      const descWrap = document.getElementById('srDescWrap');
      const descEl = document.getElementById('srDesc');
      if (descWrap && descEl) {
        const temDesc = d.minha && typeof d.descricao === 'string' && d.descricao.trim() !== '';
        descEl.textContent = temDesc ? d.descricao : '';
        descWrap.hidden = !temDesc;
      }

      const badge = document.getElementById('srBadge');
      const badgeMap = { nova:'Nova', analise:'Em análise', andamento:'Em atendimento', resolvida:'Resolvida', arquivada:'Arquivada' };
      badge.textContent = badgeMap[d.status] || d.status;
      badge.className   = 'sr-badge ' + d.status;

      renderTimeline(d.status, d.timeline);
      document.getElementById('srObs').textContent = d.obs
        ? d.obs
        : (d.status === 'resolvida'
            ? '✅ Esta ocorrência foi resolvida. Obrigado pela sua contribuição!'
            : d.status === 'arquivada'
              ? 'Esta ocorrência foi arquivada pela equipe (repetida, fora da área ou sem confirmação).'
              : 'Nossa equipe está acompanhando esta ocorrência. Acompanhe o status por aqui.');

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
  arquivada: { icon:'🗂️', label:'Arquivada' },
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

function consultarOcorrenciaDaLista(protocolo) {
  const input = document.getElementById('protocoloInput');
  if (input) input.value = protocolo;
  switchTab('consultar', document.querySelectorAll('.oc-tab')[1]);
  consultarProtocolo();
}

// =============================================
// HISTÓRICO PÚBLICO
// =============================================
// Usa somente os dados reais enviados pelo servidor.
const HISTORICO_BASE = Array.isArray(window.HISTORICO_DB) ? window.HISTORICO_DB : [];
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
    return `
      <div class="hc-item">
        <div class="hc-type-icon ${oc.tipo || 'default'}">${icon}</div>
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
  return { nova:'Nova', analise:'Em análise', andamento:'Em atendimento', resolvida:'Resolvida', arquivada:'Arquivada' }[s] || s;
}

function urgLabel(u) {
  return { alta:'Alta', media:'Média', baixa:'Baixa' }[u] || u;
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
// MAPA DE OCORRÊNCIAS — Muriaé + localização atual
// =============================================
let mapaOcorrencias = null;
let marcadorUsuarioMapa = null;
let circuloUsuarioMapa = null;

function escaparHtml(valor) {
  return String(valor == null ? '' : valor)
    .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}

function initMapaOcorrencias() {
  const el = document.getElementById('mapaOcorrencias');
  if (!el || typeof L === 'undefined' || mapaOcorrencias) return;
  const centro = window.MURIAE_GEO?.center || [-21.1306, -42.3664];
  const limites = window.MURIAE_GEO?.bounds || [[-21.31, -42.57], [-20.86, -42.25]];
  mapaOcorrencias = L.map(el, {
    center: centro, zoom: 13, maxBounds: limites, maxBoundsViscosity: 1,
    scrollWheelZoom: false,
  });
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19, attribution: '&copy; OpenStreetMap'
  }).addTo(mapaOcorrencias);

  const pontos = Array.isArray(window.MAPA_OCORRENCIAS) ? window.MAPA_OCORRENCIAS : [];
  const bounds = [];
  pontos.forEach(item => {
    const lat = Number(item.latitude);
    const lng = Number(item.longitude);
    if (!Number.isFinite(lat) || !Number.isFinite(lng) || !dentroDeMuriae(lat, lng)) return;
    const resolvida = item.status === 'RESOLVIDA';
    const cor = resolvida ? '#16a34a' : item.urgencia === 'ALTA' ? '#dc2626' : item.urgencia === 'MEDIA' ? '#d97706' : '#2563eb';
    const marker = L.circleMarker([lat, lng], {
      radius: 9, color: '#ffffff', weight: 2, fillColor: cor, fillOpacity: 0.95,
    }).addTo(mapaOcorrencias);
    marker.bindPopup(
      '<div class="oc-map-popup"><strong>' + escaparHtml(item.tipo ? item.tipo.replaceAll('_', ' ') : 'Ocorrência') + '</strong>' +
      '<span>' + escaparHtml(item.bairro) + '</span>' +
      '<small>Status: ' + escaparHtml(item.status ? item.status.replaceAll('_', ' ') : '') + '</small></div>'
    );
    bounds.push([lat, lng]);
  });
  if (bounds.length) mapaOcorrencias.fitBounds(bounds, { padding: [36, 36], maxZoom: 15 });
}

function centralizarMapaNaMinhaLocalizacao() {
  const aviso = document.getElementById('mapaOcorrenciasAviso');
  initMapaOcorrencias();
  if (!mapaOcorrencias || !navigator.geolocation) {
    if (aviso) { aviso.hidden = false; aviso.textContent = 'Localização não disponível neste navegador.'; }
    return;
  }
  navigator.geolocation.getCurrentPosition(pos => {
    const lat = pos.coords.latitude;
    const lng = pos.coords.longitude;
    if (!dentroDeMuriae(lat, lng)) {
      if (aviso) { aviso.hidden = false; aviso.textContent = 'Sua localização atual está fora da área de Muriaé. O mapa foi mantido no município.'; }
      mapaOcorrencias.setView(window.MURIAE_GEO.center, 13);
      return;
    }
    if (marcadorUsuarioMapa) mapaOcorrencias.removeLayer(marcadorUsuarioMapa);
    if (circuloUsuarioMapa) mapaOcorrencias.removeLayer(circuloUsuarioMapa);
    marcadorUsuarioMapa = L.marker([lat, lng]).addTo(mapaOcorrencias).bindPopup('Sua localização aproximada').openPopup();
    circuloUsuarioMapa = L.circle([lat, lng], { radius: pos.coords.accuracy || 30, color: '#2563eb', fillOpacity: 0.1 }).addTo(mapaOcorrencias);
    mapaOcorrencias.setView([lat, lng], 16);
    if (aviso) aviso.hidden = true;
  }, () => {
    if (aviso) { aviso.hidden = false; aviso.textContent = 'Não foi possível acessar sua localização. Verifique a permissão do navegador.'; }
  }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
}

// =============================================
// INIT
// =============================================
window.addEventListener('DOMContentLoaded', () => {
  renderHistorico();
  atualizarPontosPreview();

  // Ao escolher o bairro manualmente, remove o destaque de atenção.
  document.getElementById('bairroOcorrencia')?.addEventListener('change', function () {
    this.classList.remove('campo-atencao');
    const status = document.getElementById('gpsStatus');
    if (status && this.value) status.textContent = 'Localização confirmada · bairro ' + this.value;
  });

  // Preenche data de hoje
  const hoje = new Date().toISOString().split('T')[0];
  const dataInput = document.getElementById('dataOcorrencia');
  if (dataInput) dataInput.value = hoje;
});
