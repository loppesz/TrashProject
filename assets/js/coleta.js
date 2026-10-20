// =============================================
// MAPEAMENTO DE CORES DA LIXEIRA -> CLASSE CSS
// (usado para pintar os materiais recolhidos no resultado)
// =============================================
const COR_LIXEIRA_CLS = {
  'azul': 'azul', 'vermelho': 'vermelho', 'amarelo': 'amarelo',
  'verde': 'verde', 'marrom': 'marrom', 'preto': 'cinza', 'laranja': 'laranja',
};

const DIAS_NOMES = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
const DIAS_ABREV = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
const MESES_NOMES = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];

let calAno, calMes, diasColetaAtivos = [];

// =============================================
// CONSULTA PRINCIPAL (por bairro, lê do banco)
// =============================================
function consultarColeta(event) {
  if (event) event.preventDefault();
  const idBairro = parseInt(document.getElementById('bairroSelect')?.value || '0', 10);
  if (!idBairro) return;
  consultarBairro(idBairro);
}

// Busca os dados reais do bairro no servidor e exibe o resultado.
function consultarBairro(idBairro) {
  const sel = document.getElementById('bairroSelect');
  if (sel) sel.value = String(idBairro);

  const btnText = document.getElementById('btnText');
  const btnLoad = document.getElementById('btnLoad');
  if (btnText) btnText.style.display = 'none';
  if (btnLoad) btnLoad.style.display = 'inline';

  // Monta a URL do endpoint garantindo exatamente uma barra entre a base e a rota.
  const base = (window.BASEURL || '/').replace(/\/+$/, '');
  const url = base + '/coleta/consultarJson?id_bairro=' + encodeURIComponent(idBairro);

  fetch(url, { headers: { 'Accept': 'application/json' } })
    .then(function (r) { return r.text(); })
    .then(function (texto) {
      if (btnText) btnText.style.display = 'inline';
      if (btnLoad) btnLoad.style.display = 'none';

      let d;
      try {
        d = JSON.parse(texto);
      } catch (e) {
        console.error('Resposta não-JSON de consultarJson:', texto);
        alert('Não foi possível consultar agora. Tente novamente.');
        return;
      }

      if (!d || !d.ok) {
        alert('Ainda não há coleta cadastrada para este bairro.');
        return;
      }
      // Converte dias do banco (1=dom..7=sab) para o padrao JS (0=dom..6=sab).
      d.diasJs = (d.dias || []).map(function (x) { return x - 1; });
      mostrarResultado(d);
    })
    .catch(function (err) {
      if (btnText) btnText.style.display = 'inline';
      if (btnLoad) btnLoad.style.display = 'none';
      console.error('Falha ao consultar coleta:', err);
      alert('Não foi possível consultar agora. Tente novamente.');
    });
}

// =============================================
// MOSTRAR RESULTADO (dados reais vindos do servidor)
// =============================================
function mostrarResultado(dados) {
  const section = document.getElementById('resultadoSection');
  if (!section) return;
  section.style.display = 'block';
  section.scrollIntoView({ behavior: 'smooth', block: 'start' });

  const diasJs = dados.diasJs || [];

  // Helper: escreve textContent só se o elemento existir (evita quebrar tudo).
  const setTxt = (id, valor) => { const el = document.getElementById(id); if (el) el.textContent = valor; };

  // Títulos
  setTxt('resultadoBairroTitulo', dados.nome);
  setTxt('resultadoEndereco', 'Bairro ' + dados.nome + ' – Muriaé-MG');
  setTxt('horarioInfo', dados.horario);

  // Equipe responsável
  const equipeEl = document.querySelector('#diasSemana')?.closest('.resultado-card-rich')?.querySelector('.hr-item:nth-child(2) p');
  if (equipeEl && dados.equipe) equipeEl.textContent = dados.equipe;

  // Próxima coleta
  const hoje = new Date();
  const diaHoje = hoje.getDay();
  let proxDia = null, diasAte = 99;

  diasJs.forEach(d => {
    let diff = d - diaHoje;
    if (diff <= 0) diff += 7;
    if (diff < diasAte) { diasAte = diff; proxDia = d; }
  });

  const proxData = new Date(hoje);
  proxData.setDate(hoje.getDate() + diasAte);

  setTxt('proximaDia', DIAS_NOMES[proxDia] || '–');
  setTxt('proximaData', proxData.toLocaleDateString('pt-BR'));
  setTxt('cdValue', diasAte === 0 ? 'Hoje' : diasAte);
  setTxt('cdUnit', diasAte === 1 ? 'dia' : diasAte === 0 ? '!' : 'dias');

  // Dias da semana visual
  diasColetaAtivos = diasJs;
  renderDiasSemana(diasJs);

  // Materiais recolhidos (dinâmico, a partir do banco)
  renderMateriais(dados.materiais || [], dados.nao_recolhidos || []);

  // Calendário
  const agora = new Date();
  calAno = agora.getFullYear();
  calMes = agora.getMonth();
  renderCalendario();

  // Mapa (busca pelo bairro + cidade)
  const enc = encodeURIComponent(dados.nome + ', Muriaé - MG');
  const mapaEl = document.getElementById('mapaResultado');
  if (mapaEl) {
    mapaEl.innerHTML = `
    <iframe src="https://maps.google.com/maps?q=${enc}&output=embed&z=15"
      width="100%" height="300" style="border:0;border-radius:12px;" loading="lazy"></iframe>
  `;
  }
  const linkMapa = document.getElementById('linkMapaEndereco');
  if (linkMapa) linkMapa.href = `https://www.google.com/maps/search/${enc}`;
}

// =============================================
// MATERIAIS RECOLHIDOS / NÃO RECOLHIDOS
// =============================================
function renderMateriais(recolhidos, naoRecolhidos) {
  const cont = document.getElementById('materiaisRecolhidos');
  if (cont) {
    cont.innerHTML = recolhidos.length
      ? recolhidos.map(m => {
          const cls = COR_LIXEIRA_CLS[m.cor] || 'cinza';
          return `<div class="mr-item aceito"><div class="mr-cor ${cls}"></div><span>${m.icone || '♻️'} ${m.nome}</span></div>`;
        }).join('')
      : '<div class="mr-item">Nenhum material cadastrado para este bairro.</div>';
  }

  const chips = document.querySelector('.nao-recolhidos .nr-chips');
  if (chips) {
    chips.innerHTML = naoRecolhidos
      .map(m => `<span>${m.icone || '❌'} ${m.nome}</span>`)
      .join('');
  }
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
// AUTO-CONSULTA POR QUERY STRING (?id_bairro=)
// =============================================
window.addEventListener('DOMContentLoaded', () => {
  const params = new URLSearchParams(window.location.search);
  const idBairro = parseInt(params.get('id_bairro') || '0', 10);
  if (idBairro) {
    setTimeout(() => consultarBairro(idBairro), 300);
  }
});
