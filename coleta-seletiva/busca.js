// =============================================
// DADOS PARA BUSCA GLOBAL
// =============================================
const BAIRROS_DATA = [
  { nome: 'Centro', dias: 'Terça e Sexta', horario: '06h às 11h', slug: 'centro' },
  { nome: 'Padre Eustáquio', dias: 'Segunda e Quinta', horario: '06h às 11h', slug: 'padre-eustaquio' },
  { nome: 'Santa Terezinha', dias: 'Quarta e Sábado', horario: '06h às 11h', slug: 'santa-terezinha' },
  { nome: 'Nossa Senhora de Fátima', dias: 'Terça e Sexta', horario: '13h às 18h', slug: 'nossa-senhora-fatima' },
  { nome: 'Laranjeiras', dias: 'Segunda e Quinta', horario: '06h às 11h', slug: 'laranjeiras' },
  { nome: 'Granjas Betânia', dias: 'Quarta', horario: '06h às 11h', slug: 'granjas-betania' },
  { nome: 'Dom Silvério', dias: 'Quinta', horario: '06h às 11h', slug: 'dom-silveiro' },
  { nome: 'Mauá', dias: 'Terça e Sábado', horario: '06h às 11h', slug: 'maua' },
  { nome: 'Industrial', dias: 'Quarta', horario: '05h às 10h', slug: 'outros' },
  { nome: 'São Cristóvão', dias: 'Segunda e Quinta', horario: '06h às 11h', slug: 'outros' },
  { nome: 'Palmeiras', dias: 'Sexta', horario: '06h às 11h', slug: 'outros' },
  { nome: 'Fátima', dias: 'Terça e Sexta', horario: '13h às 18h', slug: 'nossa-senhora-fatima' },
];

const MATERIAIS_DATA = [
  { nome: 'Papel e Papelão', aceito: true, keywords: 'papel papelão jornal revista caixa', slug: 'materiais.html', cor: '#3b82f6' },
  { nome: 'Plástico', aceito: true, keywords: 'plástico plástico pet garrafa frasco sacola', slug: 'materiais.html', cor: '#ef4444' },
  { nome: 'Metal / Latas', aceito: true, keywords: 'metal lata alumínio ferro aço latinha', slug: 'materiais.html', cor: '#eab308' },
  { nome: 'Vidro', aceito: true, keywords: 'vidro garrafa pote frasco', slug: 'materiais.html', cor: '#22c55e' },
  { nome: 'Pilhas e Baterias', aceito: false, keywords: 'pilhas bateria baterias', slug: 'pontos.html', cor: '#f97316' },
  { nome: 'Eletrônicos', aceito: false, keywords: 'eletrônico celular notebook computador tv monitor', slug: 'pontos.html', cor: '#7c3aed' },
  { nome: 'Medicamentos', aceito: false, keywords: 'medicamento remédio farmácia vencido', slug: 'pontos.html', cor: '#ec4899' },
  { nome: 'Óleo de Cozinha', aceito: false, keywords: 'óleo cozinha usado gordura', slug: 'pontos.html', cor: '#a16207' },
  { nome: 'Lixo Orgânico', aceito: false, keywords: 'orgânico comida resto casca fruta alimento', slug: 'materiais.html', cor: '#6b7280' },
  { nome: 'Seringas / Perfurocortantes', aceito: false, keywords: 'seringa agulha perfurocortante', slug: 'pontos.html', cor: '#ef4444' },
  { nome: 'Entulho / Móveis', aceito: false, keywords: 'entulho obra mobília móvel sofá', slug: 'ocorrencias.html', cor: '#6b7280' },
];

const PONTOS_DATA = [
  { nome: 'Ecoponto Municipal de Muriaé', endereco: 'Av. Maestro Haroldo Braga – Industrial', materiais: 'Pilhas, Eletrônicos, Cartuchos', link: 'pontos.html#ponto1' },
  { nome: 'Farmácia Popular – Centro', endereco: 'Rua Coronel Albino Pinto, 150 – Centro', materiais: 'Medicamentos, Pilhas', link: 'pontos.html#ponto2' },
  { nome: 'Super Maia – Santa Terezinha', endereco: 'Rua João Pinheiro, 200 – Santa Terezinha', materiais: 'Óleo de cozinha, Pilhas, Plástico', link: 'pontos.html#ponto3' },
  { nome: 'UBS Central – Muriaé', endereco: 'Rua Comendador Venâncio, 80 – Centro', materiais: 'Medicamentos, Seringas', link: 'pontos.html#ponto4' },
  { nome: 'Ponto Recicle – Padre Eustáquio', endereco: 'Praça da Igreja – Padre Eustáquio', materiais: 'Papel, Plástico, Metal, Vidro', link: 'pontos.html#ponto5' },
  { nome: 'E.M. Nossa Senhora de Fátima', endereco: 'Rua Dom Silvério – N. S. de Fátima', materiais: 'Papel, Livros, Papelão', link: 'pontos.html#ponto6' },
];

// =============================================
// LÓGICA DE BUSCA
// =============================================
function buscarGlobal(event) {
  if (event) event.preventDefault();
  const q = document.getElementById('buscaGlobalInput')?.value.trim() || '';
  if (!q) return;
  executarBusca(q);
}

function setQuery(termo) {
  const input = document.getElementById('buscaGlobalInput');
  if (input) input.value = termo;
  executarBusca(termo);
}

function executarBusca(q) {
  const termo = q.toLowerCase();
  document.getElementById('buscaInicial').style.display = 'none';
  document.getElementById('buscaVazio').style.display = 'none';

  // Filtra bairros
  const bairros = BAIRROS_DATA.filter(b =>
    b.nome.toLowerCase().includes(termo) || b.dias.toLowerCase().includes(termo)
  );

  // Filtra materiais
  const materiais = MATERIAIS_DATA.filter(m =>
    m.nome.toLowerCase().includes(termo) || m.keywords.toLowerCase().includes(termo)
  );

  // Filtra pontos
  const pontos = PONTOS_DATA.filter(p =>
    p.nome.toLowerCase().includes(termo) ||
    p.endereco.toLowerCase().includes(termo) ||
    p.materiais.toLowerCase().includes(termo)
  );

  const total = bairros.length + materiais.length + pontos.length;

  document.getElementById('buscaResumo').textContent =
    total > 0
      ? `${total} resultado${total > 1 ? 's' : ''} para "${q}"`
      : '';

  // Renderiza bairros
  const secB = document.getElementById('secBairros');
  const resB = document.getElementById('resBairros');
  if (bairros.length > 0) {
    resB.innerHTML = bairros.map(b => `
      <a href="coleta.html?bairro=${b.slug}" class="busca-card">
        <div class="bc-title">🏘️ ${b.nome}</div>
        <div class="bc-sub">📅 ${b.dias} &nbsp;|&nbsp; 🕐 ${b.horario}</div>
        <span class="bc-tag">Ver calendário →</span>
      </a>`).join('');
    secB.style.display = 'block';
  } else {
    secB.style.display = 'none';
  }

  // Renderiza materiais
  const secM = document.getElementById('secMateriais');
  const resM = document.getElementById('resMateriais');
  if (materiais.length > 0) {
    resM.innerHTML = materiais.map(m => `
      <a href="${m.slug}" class="busca-card">
        <div class="bc-title">
          <span style="width:12px;height:12px;border-radius:50%;background:${m.cor};display:inline-block;flex-shrink:0"></span>
          ${m.nome}
          <span class="bc-tag" style="${m.aceito ? '' : 'background:#fee2e2;color:#b91c1c'}">${m.aceito ? '✅ Aceito na coleta' : '📍 Ponto especial'}</span>
        </div>
        <div class="bc-sub">${m.aceito ? 'Recolhido pela coleta seletiva' : 'Leve a um ponto de descarte específico'}</div>
      </a>`).join('');
    secM.style.display = 'block';
  } else {
    secM.style.display = 'none';
  }

  // Renderiza pontos
  const secP = document.getElementById('secPontos');
  const resP = document.getElementById('resPontos');
  if (pontos.length > 0) {
    resP.innerHTML = pontos.map(p => `
      <a href="${p.link}" class="busca-card">
        <div class="bc-title">📍 ${p.nome}</div>
        <div class="bc-sub">${p.endereco}</div>
        <span class="bc-tag">${p.materiais}</span>
      </a>`).join('');
    secP.style.display = 'block';
  } else {
    secP.style.display = 'none';
  }

  if (total === 0) {
    document.getElementById('buscaVazio').style.display = 'block';
  }

  // Atualiza URL sem recarregar
  const url = new URL(window.location);
  url.searchParams.set('q', q);
  window.history.replaceState({}, '', url);
}

// =============================================
// INIT — lê ?q= da URL
// =============================================
window.addEventListener('DOMContentLoaded', () => {
  const params = new URLSearchParams(window.location.search);
  const q = params.get('q');
  if (q) {
    const input = document.getElementById('buscaGlobalInput');
    if (input) input.value = q;
    executarBusca(q);
  }
});
