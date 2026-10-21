// =============================================
// COLETA POR BAIRRO (comum + seletiva)
// Dados reais do banco (cronograma oficial do DEMSUR).
// Dias no banco: 1=domingo ... 7=sábado. No JS: 0=domingo ... 6=sábado.
// =============================================

// Cor da lixeira -> classe CSS (pinta os materiais recolhidos no resultado)
const COR_LIXEIRA_CLS = {
  'azul': 'azul', 'vermelho': 'vermelho', 'amarelo': 'amarelo',
  'verde': 'verde', 'marrom': 'marrom', 'preto': 'cinza', 'laranja': 'laranja',
};

const DIAS_NOMES = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
const DIAS_ABREV = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
const MESES_NOMES = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];

let calAno, calMes;
let diasSeletiva = [], diasComum = [];   // dias JS (0..6) com cada tipo de coleta
let proximaColetaData = null;            // Date da próxima coleta destacada no calendário
let bairroAtual = null;                  // último resultado exibido (para compartilhar)

function escHtml(s) {
  return String(s == null ? '' : s)
    .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

// '07:30' -> '07h30' ; '08:00' -> '08h' (mesmo formato do PHP formataHora)
function fmtHora(h) {
  if (!h) return '–';
  const [hh, mm] = String(h).split(':');
  return mm === '00' ? hh + 'h' : hh + 'h' + mm;
}

function textoHorarios(horarios, aPartir) {
  const temFim = horarios.some(h => h.fim);
  const txt = horarios.map(h => h.fim ? fmtHora(h.inicio) + ' às ' + fmtHora(h.fim) : fmtHora(h.inicio)).join(' e ');
  return aPartir && !temFim && txt ? 'a partir das ' + txt : txt;
}

// Lista de dias do banco (1..7) -> "Seg a Sex", "Ter e Qui", "Seg, Qua e Sex"
function rotuloDias(diasBanco) {
  const pos = d => (d + 5) % 7; // seg=0 ... dom=6
  const ds = [...diasBanco].sort((a, b) => pos(a) - pos(b));
  const nomes = ds.map(d => DIAS_ABREV[d - 1]);
  if (!ds.length) return '';
  const consecutivos = ds.every((d, i) => i === 0 || pos(d) === pos(ds[i - 1]) + 1);
  if (ds.length >= 3 && consecutivos) return nomes[0] + ' a ' + nomes[nomes.length - 1];
  if (ds.length === 1) return nomes[0];
  return nomes.slice(0, -1).join(', ') + ' e ' + nomes[nomes.length - 1];
}

// Agrupa dias com o mesmo horário -> [{rotulo, horario}]
function agruparDias(dias, aPartir) {
  const mapa = new Map();
  dias.forEach(d => {
    const txt = textoHorarios(d.horarios, aPartir);
    if (!mapa.has(txt)) mapa.set(txt, []);
    mapa.get(txt).push(d.dia);
  });
  return [...mapa.entries()].map(([horario, ds]) => ({ rotulo: rotuloDias(ds), horario }));
}

// Dias JS (0..6) presentes em qualquer trecho de um tipo de coleta
function diasJsDe(grupos) {
  const set = new Set();
  (grupos || []).forEach(g => g.dias.forEach(d => set.add(d.dia - 1)));
  return [...set];
}

// Primeiro horário (HH:MM) de um dia JS em qualquer trecho, ou null
function primeiroHorarioDoDia(grupos, diaJs) {
  let menor = null;
  (grupos || []).forEach(g => g.dias.forEach(d => {
    if (d.dia - 1 !== diaJs) return;
    d.horarios.forEach(h => { if (!menor || h.inicio < menor) menor = h.inicio; });
  }));
  return menor;
}

// Próxima passagem a partir de agora: {data, diasAte, hora} ou null.
// Hoje só conta se o primeiro horário do dia ainda não passou.
function proximaPassagem(grupos) {
  if (!grupos || !grupos.length) return null;
  const agora = new Date();
  const hhmmAgora = String(agora.getHours()).padStart(2, '0') + ':' + String(agora.getMinutes()).padStart(2, '0');
  for (let k = 0; k <= 7; k++) {
    const diaJs = (agora.getDay() + k) % 7;
    const hora = primeiroHorarioDoDia(grupos, diaJs);
    if (!hora) continue;
    if (k === 0 && hora <= hhmmAgora) continue;
    const data = new Date(agora);
    data.setDate(agora.getDate() + k);
    return { data, diasAte: k, hora };
  }
  return null;
}

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
    .then(r => r.text())
    .then(texto => {
      if (btnText) btnText.style.display = 'inline';
      if (btnLoad) btnLoad.style.display = 'none';

      let d;
      try { d = JSON.parse(texto); }
      catch (e) {
        console.error('Resposta não-JSON de consultarJson:', texto);
        alert('Não foi possível consultar agora. Tente novamente.');
        return;
      }
      if (!d || !d.ok) {
        alert('Bairro não encontrado. Escolha outro bairro da lista.');
        return;
      }
      mostrarResultado(d);
    })
    .catch(err => {
      if (btnText) btnText.style.display = 'inline';
      if (btnLoad) btnLoad.style.display = 'none';
      console.error('Falha ao consultar coleta:', err);
      alert('Não foi possível consultar agora. Tente novamente.');
    });
}

// =============================================
// MOSTRAR RESULTADO
// =============================================
function mostrarResultado(dados) {
  const section = document.getElementById('resultadoSection');
  if (!section) return;
  bairroAtual = dados;
  section.style.display = 'block';
  section.scrollIntoView({ behavior: 'smooth', block: 'start' });

  const setTxt = (id, valor) => { const el = document.getElementById(id); if (el) el.textContent = valor; };
  const seletiva = dados.seletiva || [];
  const comum = dados.comum || [];

  setTxt('resultadoBairroTitulo', dados.nome);
  setTxt('resultadoEndereco', 'Bairro ' + dados.nome + ' – Muriaé-MG');
  setTxt('equipeInfo', dados.equipe || 'DEMSUR');
  setTxt('telefoneEquipe', dados.telefone || 'Não informado');

  // Bairro sem horário divulgado: mostra o aviso e esconde o que depende do horário.
  section.classList.toggle('sem-cronograma', !!dados.sem_cronograma);
  const aviso = document.getElementById('semCronograma');
  if (aviso) aviso.hidden = !dados.sem_cronograma;

  diasSeletiva = diasJsDe(seletiva);
  diasComum = diasJsDe(comum);

  // Próxima coleta: a seletiva (recicláveis) tem prioridade; senão, a comum.
  const tipoPrincipal = seletiva.length ? 'seletiva' : 'comum';
  const prox = proximaPassagem(tipoPrincipal === 'seletiva' ? seletiva : comum);
  proximaColetaData = prox ? prox.data : null;

  setTxt('proximaLabel', tipoPrincipal === 'seletiva' ? 'Próxima coleta seletiva' : 'Próxima coleta comum');
  if (prox) {
    setTxt('proximaDia', DIAS_NOMES[prox.data.getDay()]);
    setTxt('proximaData', prox.data.toLocaleDateString('pt-BR') + ' · ' +
      (tipoPrincipal === 'seletiva' ? 'a partir das ' : 'por volta das ') + fmtHora(prox.hora));
    setTxt('cdValue', prox.diasAte === 0 ? 'Hoje' : prox.diasAte);
    setTxt('cdUnit', prox.diasAte === 0 ? '' : (prox.diasAte === 1 ? 'dia' : 'dias'));
  } else {
    setTxt('proximaDia', '–');
    setTxt('proximaData', '');
    setTxt('cdValue', '–');
    setTxt('cdUnit', '');
  }

  renderAlerta(seletiva, comum);
  renderDiasSemana();
  renderHorarios(seletiva, comum);
  renderMateriais(dados.materiais || [], dados.nao_recolhidos || [], seletiva.length > 0);

  const agora = new Date();
  calAno = agora.getFullYear();
  calMes = agora.getMonth();
  renderCalendario();

  // Mapa (busca pelo bairro + cidade)
  const enc = encodeURIComponent(dados.nome + ', Muriaé - MG');
  const mapaEl = document.getElementById('mapaResultado');
  if (mapaEl) {
    mapaEl.innerHTML = `
    <iframe src="https://maps.google.com/maps?q=${enc}&output=embed&z=15" title="Mapa do bairro ${escHtml(dados.nome)}"
      width="100%" height="300" style="border:0;border-radius:12px;" loading="lazy"></iframe>
  `;
  }
  const linkMapa = document.getElementById('linkMapaEndereco');
  if (linkMapa) linkMapa.href = `https://www.google.com/maps/search/${enc}`;
}

// Aviso com o horário real do bairro
function renderAlerta(seletiva, comum) {
  const el = document.getElementById('alertaProxima');
  if (!el) return;
  const horasSel = [];
  seletiva.forEach(g => g.dias.forEach(d => d.horarios.forEach(h => horasSel.push(h.inicio))));
  horasSel.sort();
  if (horasSel.length) {
    el.innerHTML = '⚠️ <strong>Lembre-se:</strong> nos dias de coleta seletiva o caminhão passa a partir das <strong>' +
      escHtml(fmtHora(horasSel[0])) + '</strong>. Deixe os recicláveis separados na calçada antes desse horário.';
  } else if (comum.length) {
    el.innerHTML = '⚠️ <strong>Lembre-se:</strong> este bairro ainda não tem coleta seletiva porta a porta. ' +
      'Coloque o lixo comum na calçada pouco antes da passagem do caminhão e leve os recicláveis a um ponto de descarte.';
  } else {
    el.innerHTML = '';
  }
}

// Lista de horários: seletiva e comum, com os trechos do bairro
function renderHorarios(seletiva, comum) {
  const cont = document.getElementById('horariosColeta');
  if (!cont) return;
  const bloco = (titulo, classe, grupos, aPartir, vazio) => {
    let html = `<div class="hc-bloco ${classe}"><h4>${titulo}</h4>`;
    if (!grupos.length) {
      html += `<p class="hc-vazio">${vazio}</p>`;
    } else {
      grupos.forEach(g => {
        html += '<div class="hc-linha">';
        if (g.trecho) html += `<span class="hc-trecho">${escHtml(g.trecho)}</span>`;
        html += '<span class="hc-chips">' + agruparDias(g.dias, aPartir)
          .map(x => `<span class="hc-chip ${classe}"><b>${escHtml(x.rotulo)}</b> ${escHtml(x.horario)}</span>`)
          .join('') + '</span></div>';
      });
    }
    return html + '</div>';
  };
  cont.innerHTML =
    bloco('Coleta seletiva (recicláveis)', 'seletiva', seletiva, true, 'Sem coleta seletiva porta a porta neste bairro.') +
    bloco('Coleta comum (lixo doméstico)', 'comum', comum, false, 'Horário não divulgado.');
}

// =============================================
// MATERIAIS RECOLHIDOS / NÃO RECOLHIDOS
// =============================================
function renderMateriais(recolhidos, naoRecolhidos, temSeletiva) {
  const cont = document.getElementById('materiaisRecolhidos');
  if (cont) {
    if (!temSeletiva) {
      const base = (window.BASEURL || '/').replace(/\/+$/, '');
      cont.innerHTML = `<div class="mr-item mr-aviso">Este bairro ainda não tem coleta seletiva porta a porta.
        Leve papel, plástico, metal e vidro a um <a href="${base}/ponto">ponto de descarte</a>.</div>`;
    } else {
      cont.innerHTML = recolhidos.length
        ? recolhidos.map(m => {
            const cls = COR_LIXEIRA_CLS[m.cor] || 'cinza';
            return `<div class="mr-item aceito"><div class="mr-cor ${cls}"></div><span>${escHtml(m.icone || '♻️')} ${escHtml(m.nome)}</span></div>`;
          }).join('')
        : '<div class="mr-item">Nenhum material cadastrado para este bairro.</div>';
    }
  }

  const chips = document.querySelector('.nao-recolhidos .nr-chips');
  if (chips) {
    chips.innerHTML = naoRecolhidos
      .map(m => `<span>${escHtml(m.icone || '❌')} ${escHtml(m.nome)}</span>`)
      .join('');
  }
}

// =============================================
// DIAS DA SEMANA VISUAL (♻️ seletiva · ● comum)
// =============================================
function renderDiasSemana() {
  const container = document.getElementById('diasSemana');
  if (!container) return;
  const diaHoje = new Date().getDay();
  // Mostra de segunda a domingo
  const ordem = [1, 2, 3, 4, 5, 6, 0];
  container.innerHTML = ordem.map(i => {
    const sel = diasSeletiva.includes(i);
    const com = diasComum.includes(i);
    const cls = [sel ? 'ativo' : '', com ? 'comum' : '', !sel && !com ? 'inativo' : '', i === diaHoje ? 'hoje' : '']
      .filter(Boolean).join(' ');
    const desc = [sel ? 'coleta seletiva' : '', com ? 'coleta comum' : ''].filter(Boolean).join(' e ') || 'sem coleta';
    return `<div class="dsv-item ${cls}" title="${DIAS_NOMES[i]}: ${desc}" aria-label="${DIAS_NOMES[i]}: ${desc}">
      <span aria-hidden="true">${sel ? '♻️' : (com ? '●' : '—')}</span>
      <small>${DIAS_ABREV[i]}</small>
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
  const proxStr = proximaColetaData ? proximaColetaData.toDateString() : '';

  let html = '<div class="cal-grid">';
  DIAS_ABREV.forEach(d => html += `<div class="cal-head">${d}</div>`);

  // Dias do mês anterior
  const mesAnterior = new Date(calAno, calMes, 0);
  for (let i = iniciaSemana - 1; i >= 0; i--) {
    html += `<div class="cal-day outro-mes">${mesAnterior.getDate() - i}</div>`;
  }

  // Dias do mês atual
  for (let dia = 1; dia <= ultimoDia.getDate(); dia++) {
    const data = new Date(calAno, calMes, dia);
    const dSem = data.getDay();
    const sel = diasSeletiva.includes(dSem);
    const com = diasComum.includes(dSem);
    const isHoje = data.toDateString() === hoje.toDateString();
    const isProx = proxStr !== '' && data.toDateString() === proxStr;

    let cls = 'cal-day';
    if (isHoje) cls += ' hoje';
    else if (isProx) cls += ' proximo-coleta';
    else if (sel) cls += ' coleta-dia';
    if (com) cls += ' comum-dia';

    const desc = [sel ? 'coleta seletiva' : '', com ? 'coleta comum' : ''].filter(Boolean).join(' e ');
    const tooltip = desc ? `title="${DIAS_NOMES[dSem]}: ${desc}"` : '';
    const ponto = com && !isHoje && !isProx ? '<div class="cal-dot cal-dot-comum"></div>' : '';
    html += `<div class="${cls}" ${tooltip}>${dia}${ponto}</div>`;
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
// COMPARTILHAR O HORÁRIO DO BAIRRO
// =============================================
function compartilharColeta() {
  if (!bairroAtual) return;
  const linhas = ['Coleta no bairro ' + bairroAtual.nome + ' (Muriaé-MG):'];
  const resumo = (grupos, aPartir) => grupos.map(g =>
    (g.trecho ? g.trecho + ': ' : '') + agruparDias(g.dias, aPartir).map(x => x.rotulo + ' ' + x.horario).join(' · ')
  ).join(' | ');
  if ((bairroAtual.seletiva || []).length) linhas.push('Seletiva: ' + resumo(bairroAtual.seletiva, true));
  if ((bairroAtual.comum || []).length) linhas.push('Comum: ' + resumo(bairroAtual.comum, false));
  if (bairroAtual.sem_cronograma) linhas.push('Horário ainda não divulgado pelo DEMSUR (' + (bairroAtual.telefone || '') + ').');
  const base = (window.BASEURL || '/').replace(/\/+$/, '');
  const link = window.location.origin + base + '/coleta?id_bairro=' + bairroAtual.id_bairro;
  const texto = linhas.join('\n');

  if (navigator.share) {
    navigator.share({ title: 'Coleta – ' + bairroAtual.nome, text: texto, url: link }).catch(() => {});
    return;
  }
  // Sem compartilhamento nativo: copia (helper de script.js, funciona fora de HTTPS)
  const copiar = typeof copiarParaAreaTransferencia === 'function'
    ? copiarParaAreaTransferencia(texto + '\n' + link)
    : Promise.resolve(false);
  copiar.then(ok => alert(ok ? 'Horário copiado. Cole onde quiser.' : texto));
}

// =============================================
// FILTRAR TABELA DE BAIRROS (texto + tipo de coleta)
// =============================================
function filtrarBairros() {
  const semAcento = s => s.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
  const termo = semAcento(document.getElementById('buscaBairro')?.value.trim() || '');
  const tipo = document.getElementById('filtroSeletiva')?.value || '';
  let visiveis = 0;
  // Filtra pelo nome do bairro (data-bairro já vem sem acento) e pelo tipo de coleta.
  document.querySelectorAll('#gradeBairros .bairro-card[data-bairro]').forEach(card => {
    const okTexto = !termo || (card.dataset.bairro || '').includes(termo);
    const okTipo = !tipo || card.dataset.seletiva === tipo;
    const mostrar = okTexto && okTipo;
    card.style.display = mostrar ? '' : 'none';
    if (mostrar) visiveis++;
  });
  const vazio = document.getElementById('tgVazio');
  if (vazio) vazio.hidden = visiveis > 0;
}

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
