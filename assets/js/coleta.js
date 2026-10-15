// =============================================
// DADOS DE COLETA POR BAIRRO
// =============================================
const COLETA_DATA = {
  'centro':         { nome: 'Centro',          dias: [2, 5],    horario: '07h às 12h', equipe: 'ReciclaVida', tel: '(32) 3333-7700', materiais: ['Papel', 'Plástico', 'Metal', 'Vidro'] },
  'bairro-novo':    { nome: 'Bairro Novo',      dias: [1, 4],    horario: '08h às 13h', equipe: 'ReciclaVida', tel: '(32) 3333-7700', materiais: ['Papel', 'Plástico', 'Metal'] },
  'vila-nova':      { nome: 'Vila Nova',         dias: [3, 6],    horario: '07h às 11h', equipe: 'EcoMais',    tel: '(32) 3333-8800', materiais: ['Papel', 'Plástico', 'Vidro'] },
  'jardim-america': { nome: 'Jardim América',    dias: [2, 5],    horario: '13h às 18h', equipe: 'ReciclaVida', tel: '(32) 3333-7700', materiais: ['Papel', 'Plástico', 'Metal', 'Vidro'] },
  'bairro-escolar': { nome: 'Bairro Escolar',    dias: [4],       horario: '07h às 12h', equipe: 'EcoMais',    tel: '(32) 3333-8800', materiais: ['Papel', 'Plástico'] },
  'outros':         { nome: 'Sua Região',        dias: [3, 6],    horario: '07h às 12h', equipe: 'ReciclaVida', tel: '(32) 3333-7700', materiais: ['Papel', 'Plástico', 'Metal', 'Vidro'] },
};

const DIAS_NOMES = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
const DIAS_ABREV = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
const MESES_NOMES = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];

let calAno, calMes, diasColetaAtivos = [];

// =============================================
// CONSULTA PRINCIPAL
// =============================================
function consultarColeta(event) {
  if (event) event.preventDefault();

  const endereco = document.getElementById('enderecoInput')?.value || '';
  const bairroSel = document.getElementById('bairroSelect')?.value || 'centro';
  const chave = bairroSel || detectarBairroPorEndereco(endereco);
  const dados = COLETA_DATA[chave] || COLETA_DATA['centro'];

  // Simula loading
  const btnText = document.getElementById('btnText');
  const btnLoad = document.getElementById('btnLoad');
  if (btnText) btnText.style.display = 'none';
  if (btnLoad) btnLoad.style.display = 'inline';

  setTimeout(() => {
    if (btnText) btnText.style.display = 'inline';
    if (btnLoad) btnLoad.style.display = 'none';
    mostrarResultado(dados, endereco || dados.nome);
  }, 800);
}

function detectarBairroPorEndereco(endereco) {
  const e = endereco.toLowerCase();
  if (e.includes('centro')) return 'centro';
  if (e.includes('novo')) return 'bairro-novo';
  if (e.includes('nova')) return 'vila-nova';
  if (e.includes('jardim') || e.includes('america')) return 'jardim-america';
  if (e.includes('escolar')) return 'bairro-escolar';
  return 'centro';
}

// =============================================
// MOSTRAR RESULTADO
// =============================================
function mostrarResultado(dados, enderecoTexto) {
  const section = document.getElementById('resultadoSection');
  if (!section) return;
  section.style.display = 'block';
  section.scrollIntoView({ behavior: 'smooth', block: 'start' });

  // Títulos
  document.getElementById('resultadoBairroTitulo').textContent = dados.nome;
  document.getElementById('resultadoEndereco').textContent = enderecoTexto;
  document.getElementById('horarioInfo').textContent = dados.horario;

  // Próxima coleta
  const hoje = new Date();
  const diaHoje = hoje.getDay();
  let proxDia = null, diasAte = 99;

  dados.dias.forEach(d => {
    let diff = d - diaHoje;
    if (diff <= 0) diff += 7;
    if (diff < diasAte) { diasAte = diff; proxDia = d; }
  });

  const proxData = new Date(hoje);
  proxData.setDate(hoje.getDate() + diasAte);

  document.getElementById('proximaDia').textContent = DIAS_NOMES[proxDia] || '–';
  document.getElementById('proximaData').textContent = proxData.toLocaleDateString('pt-BR');
  document.getElementById('cdValue').textContent = diasAte === 0 ? 'Hoje' : diasAte;
  document.getElementById('cdUnit').textContent = diasAte === 1 ? 'dia' : diasAte === 0 ? '!' : 'dias';

  // Dias da semana visual
  diasColetaAtivos = dados.dias;
  renderDiasSemana(dados.dias);

  // Calendário
  const agora = new Date();
  calAno = agora.getFullYear();
  calMes = agora.getMonth();
  renderCalendario();

  // Mapa
  const enc = encodeURIComponent(enderecoTexto + ' Juiz de Fora MG');
  document.getElementById('mapaResultado').innerHTML = `
    <iframe src="https://maps.google.com/maps?q=${enc}&output=embed&z=15"
      width="100%" height="300" style="border:0;border-radius:12px;" loading="lazy"></iframe>
  `;
  const linkMapa = document.getElementById('linkMapaEndereco');
  if (linkMapa) linkMapa.href = `https://www.google.com/maps/search/${enc}`;
}

// =============================================
// DIAS DA SEMANA VISUAL
// =============================================
function renderDiasSemana(dias) {
  const container = document.getElementById('diasSemana');
  if (!container) return;
  const diaHoje = new Date().getDay();
  const icones = ['☀️','🌙','⭐','💫','🔥','🌿','🎯'];
  container.innerHTML = DIAS_ABREV.map((nome, i) => {
    const isAtivo = dias.includes(i);
    const isHoje = i === diaHoje;
    return `<div class="dsv-item ${isAtivo ? 'ativo' : 'inativo'} ${isHoje ? 'hoje' : ''}">
      <span>${isAtivo ? '♻️' : '—'}</span>
      <small>${nome}</small>
    </div>`;
  }).join('');
}

// =============================================
// CALENDÁRIO MENSAL
// =============================================
function renderCalendario() {
  const wrap = document.getElementById('calendarioWrap');
  const label = document.getElementById('calMesAno');
  if (!wrap || !label) return;

  label.textContent = MESES_NOMES[calMes] + ' ' + calAno;

  const hoje = new Date();
  const primeiroDia = new Date(calAno, calMes, 1);
  const ultimoDia   = new Date(calAno, calMes + 1, 0);
  const iniciaSemana = primeiroDia.getDay();

  // Próxima coleta global
  const diaHoje = hoje.getDay();
  let proxDiff = 99, proxDiaNum = -1;
  diasColetaAtivos.forEach(d => {
    let diff = d - diaHoje;
    if (diff <= 0) diff += 7;
    if (diff < proxDiff) { proxDiff = diff; }
  });
  const proxData = new Date(hoje);
  proxData.setDate(hoje.getDate() + proxDiff);

  let html = '<div class="cal-grid">';

  // Cabeçalhos
  DIAS_ABREV.forEach(d => html += `<div class="cal-head">${d}</div>`);

  // Dias do mês anterior
  const mesAnterior = new Date(calAno, calMes, 0);
  for (let i = iniciaSemana - 1; i >= 0; i--) {
    html += `<div class="cal-day outro-mes">${mesAnterior.getDate() - i}</div>`;
  }

  // Dias do mês atual
  for (let dia = 1; dia <= ultimoDia.getDate(); dia++) {
    const data = new Date(calAno, calMes, dia);
    const diaSemana = data.getDay();
    const isColeta = diasColetaAtivos.includes(diaSemana);
    const isHoje = data.toDateString() === hoje.toDateString();
    const isProxColeta = data.toDateString() === proxData.toDateString();

    let cls = 'cal-day';
    if (isHoje) cls += ' hoje';
    else if (isProxColeta && calAno === hoje.getFullYear() && calMes === hoje.getMonth()) cls += ' proximo-coleta';
    else if (isColeta) cls += ' coleta-dia';

    const tooltip = isColeta ? `title="Dia de coleta: ${diasColetaAtivos.join(', ')}"` : '';
    html += `<div class="${cls}" ${tooltip}>${dia}${isColeta && !isHoje && !isProxColeta ? '<div class="cal-dot"></div>' : ''}</div>`;
  }

  // Dias do próximo mês
  const totalCelulas = iniciaSemana + ultimoDia.getDate();
  const celulasFaltam = totalCelulas % 7 === 0 ? 0 : 7 - (totalCelulas % 7);
  for (let i = 1; i <= celulasFaltam; i++) {
    html += `<div class="cal-day outro-mes">${i}</div>`;
  }

  html += '</div>';
  wrap.innerHTML = html;
}

function navegarMes(dir) {
  calMes += dir;
  if (calMes < 0)  { calMes = 11; calAno--; }
  if (calMes > 11) { calMes = 0;  calAno++; }
  renderCalendario();
}

// =============================================
// SIMULAR CONSULTA (BOTÃO DA TABELA)
// =============================================
function simularConsulta(nomeStr) {
  // Mapeia nome para chave
  const mapa = {
    'Centro':          'centro',
    'Bairro Novo':     'bairro-novo',
    'Vila Nova':       'vila-nova',
    'Jardim América':  'jardim-america',
    'Bairro Escolar':  'bairro-escolar',
    'Zona Industrial': 'outros',
  };
  const chave = mapa[nomeStr] || 'centro';
  const dados = COLETA_DATA[chave];

  const input = document.getElementById('enderecoInput');
  const sel   = document.getElementById('bairroSelect');
  if (input) input.value = dados.nome;
  if (sel)   sel.value   = chave;

  mostrarResultado(dados, dados.nome);
}

// =============================================
// FILTRAR TABELA DE BAIRROS
// =============================================
function filtrarBairros() {
  const termo = document.getElementById('buscaBairro')?.value.toLowerCase() || '';
  document.querySelectorAll('#tabelaGeral tbody tr').forEach(tr => {
    const txt = (tr.dataset.bairro || '').toLowerCase() + tr.textContent.toLowerCase();
    tr.style.display = txt.includes(termo) ? '' : 'none';
  });
}

// =============================================
// NOTIFICAÇÃO (SIMULADA)
// =============================================
function ativarNotificacao() {
  document.getElementById('modalNotif').style.display = 'flex';
}

// Fechar modal
document.addEventListener('click', e => {
  const m = document.getElementById('modalNotif');
  if (m && e.target === m) m.style.display = 'none';
});

// =============================================
// AUTO-DETECTAR PELO HASH OU QUERY STRING
// =============================================
window.addEventListener('DOMContentLoaded', () => {
  const params = new URLSearchParams(window.location.search);
  const bairro = params.get('bairro');
  const q      = params.get('q');

  if (q) {
    // Veio da busca da home
    const input = document.getElementById('enderecoInput');
    if (input) input.value = decodeURIComponent(q);
    const chave = detectarBairroPorEndereco(decodeURIComponent(q));
    const dados = COLETA_DATA[chave] || COLETA_DATA['centro'];
    setTimeout(() => mostrarResultado(dados, decodeURIComponent(q)), 400);
  } else if (bairro) {
    const sel = document.getElementById('bairroSelect');
    if (sel) sel.value = bairro;
    const dados = COLETA_DATA[bairro] || COLETA_DATA['centro'];
    setTimeout(() => mostrarResultado(dados, dados.nome), 400);
  }
});
