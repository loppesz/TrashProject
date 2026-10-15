// =============================================
// SISTEMA DE PONTOS E NÍVEL
// =============================================
const NIVEIS = [
  { nome:"🌱 Muda",     avatar:"🌱", min:0,    max:200,  cor:"#86efac" },
  { nome:"🌿 Broto",    avatar:"🌿", min:200,  max:500,  cor:"#4ade80" },
  { nome:"🌲 Árvore",   avatar:"🌲", min:500,  max:1000, cor:"#22c55e" },
  { nome:"🌳 Floresta", avatar:"🌳", min:1000, max:99999, cor:"#16a34a" },
];

const PLANETAS = [
  { pct:0,   emoji:"🌑", txt:"Planeta árido e seco..." },
  { pct:20,  emoji:"🌒", txt:"Apareceram as primeiras plantinhas!" },
  { pct:40,  emoji:"🌓", txt:"Matas começando a crescer!" },
  { pct:60,  emoji:"🌍", txt:"Planeta bem verde já!" },
  { pct:80,  emoji:"🌎", txt:"Floresta linda e exuberante!" },
  { pct:100, emoji:"🌏", txt:"Planeta 100% ecológico! Perfeito!" },
];

let kidsPoints  = parseInt(localStorage.getItem('kidsPoints')  || '0');
let playerName  = localStorage.getItem('playerName')  || 'Eco Herói';
let playerAvatar= localStorage.getItem('playerAvatar') || '🌱';
let gamesPlayed = JSON.parse(localStorage.getItem('gamesPlayed') || '[]');

function save() {
  localStorage.setItem('kidsPoints', kidsPoints);
  localStorage.setItem('playerName', playerName);
  localStorage.setItem('playerAvatar', playerAvatar);
  localStorage.setItem('gamesPlayed', JSON.stringify(gamesPlayed));
}

function getNivel(pts) {
  for (let i = NIVEIS.length - 1; i >= 0; i--)
    if (pts >= NIVEIS[i].min) return NIVEIS[i];
  return NIVEIS[0];
}

function getPlanetaPct(pts) {
  return Math.min(100, Math.floor(pts / 15));
}

function updateUI() {
  const nivel = getNivel(kidsPoints);
  const proxNivel = NIVEIS.find(n => n.min > kidsPoints) || nivel;
  const xpInLevel = kidsPoints - nivel.min;
  const xpToNext  = proxNivel.min - nivel.min;
  const xpPct     = Math.min(100, Math.round((xpInLevel / xpToNext) * 100));

  document.getElementById('kidsPoints').textContent    = kidsPoints.toLocaleString('pt-BR');
  document.getElementById('kidsPointsNav').textContent = kidsPoints.toLocaleString('pt-BR');
  document.getElementById('playerNameDisplay').textContent = playerName;
  document.getElementById('playerAvatar').textContent  = playerAvatar;
  document.getElementById('playerLevelBadge').textContent = nivel.nome;
  document.getElementById('xpBar').style.width         = xpPct + '%';
  document.getElementById('xpText').textContent        = `${xpInLevel} / ${xpToNext} XP para ${proxNivel.nome}`;

  const pct = getPlanetaPct(kidsPoints);
  const planeta = [...PLANETAS].reverse().find(p => pct >= p.pct) || PLANETAS[0];
  document.getElementById('planetDisplay').textContent = planeta.emoji;
  document.getElementById('planetStatus').textContent  = planeta.txt;
  document.getElementById('planetBar').style.width     = Math.min(100, pct) + '%';
  document.getElementById('planetPct').textContent     = pct + '% ecológico';

  if (pct >= 100) unlockConquista('c-planeta');
}

function ganharPontos(pts, msg) {
  const nivelAntes = getNivel(kidsPoints);
  kidsPoints += pts;
  save();
  const nivelDepois = getNivel(kidsPoints);
  updateUI();
  if (msg) showKidsToast('🎉 +' + pts + ' pts! ' + msg);
  if (nivelAntes.nome !== nivelDepois.nome) showLevelUp(nivelDepois);
  checkConquistas();
}

function showLevelUp(nivel) {
  const ov = document.getElementById('levelUpOverlay');
  document.getElementById('levelUpEmoji').textContent = nivel.avatar;
  document.getElementById('levelUpText').textContent  = 'Você chegou ao nível ' + nivel.nome + '!';
  ov.style.display = 'flex';
  mascotFalar('UHUUU! Você subiu de nível! ' + nivel.nome + '! 🎉');
  setTimeout(() => ov.style.display = 'none', 3000);
}

document.getElementById('levelUpOverlay')?.addEventListener('click', () => {
  document.getElementById('levelUpOverlay').style.display = 'none';
});

// =============================================
// MASCOTE
// =============================================
const MASCOT_FRASES = [
  "Vamos reciclar e salvar o planeta! 🌍",
  "Você está indo muito bem! Continue! 💪",
  "Cada latinha reciclada é uma árvore salva! 🌳",
  "Reciclar é legal e faz bem para todos! ♻️",
  "Você é um herói do meio ambiente! 🦸",
  "Juntos somos mais fortes! 🤝",
  "O planeta te agradece! 🌿",
  "Reciclou hoje? Parabéns! ⭐",
];
let mascotIdx = 0;

function mascotFalar(msg) {
  const el = document.getElementById('mascotSpeech');
  if (!el) return;
  el.style.opacity = '0';
  setTimeout(() => { el.textContent = msg || MASCOT_FRASES[mascotIdx++ % MASCOT_FRASES.length]; el.style.opacity = '1'; }, 200);
}

setInterval(() => mascotFalar(), 8000);

// Olhos seguem o mouse
document.addEventListener('mousemove', (e) => {
  const pupils = document.querySelectorAll('.eye-pupil');
  pupils.forEach(p => {
    const eye = p.parentElement.getBoundingClientRect();
    const cx = eye.left + eye.width / 2;
    const cy = eye.top + eye.height / 2;
    const dx = e.clientX - cx;
    const dy = e.clientY - cy;
    const angle = Math.atan2(dy, dx);
    const dist = Math.min(4, Math.hypot(dx, dy) / 10);
    p.style.transform = `translate(${Math.cos(angle)*dist}px, ${Math.sin(angle)*dist}px)`;
  });
});

// =============================================
// BOLHAS DO HERO
// =============================================
function createBubbles() {
  const container = document.getElementById('heroBubbles');
  if (!container) return;
  const emojis = ['♻️','🌱','🐸','🌍','💧','🌳','⭐','🌿','🍃','🦋'];
  for (let i = 0; i < 12; i++) {
    const b = document.createElement('span');
    b.textContent = emojis[Math.floor(Math.random() * emojis.length)];
    b.style.cssText = `
      position:absolute; font-size:${1.5+Math.random()*2}rem;
      left:${Math.random()*95}%; top:${Math.random()*90}%;
      opacity:${0.08+Math.random()*0.12};
      animation: floatBubble ${5+Math.random()*8}s ease-in-out infinite;
      animation-delay: -${Math.random()*8}s;
      pointer-events:none;
    `;
    container.appendChild(b);
  }
}
createBubbles();

// =============================================
// PERFIL DO JOGADOR
// =============================================
function editarNome() {
  document.getElementById('nomeInput').value = playerName;
  document.getElementById('modalNome').style.display = 'flex';
}

function selectAvatar(el, emoji) {
  document.querySelectorAll('.av-opt').forEach(a => a.classList.remove('selected'));
  el.classList.add('selected');
  playerAvatar = emoji;
}

function salvarNome() {
  const v = document.getElementById('nomeInput').value.trim();
  if (v) playerName = v;
  save(); updateUI();
  document.getElementById('modalNome').style.display = 'none';
  mascotFalar('Prazer em te conhecer, ' + playerName + '! 🤝');
  showKidsToast('✅ Perfil salvo!');
}

// =============================================
// NAVEGAÇÃO
// =============================================
function voltarMenu() {
  hideAll();
  document.getElementById('mainMenu').style.display = 'block';
  stopSepararGame();
}

function showSection(id) {
  hideAll();
  document.getElementById(id).style.display = 'block';
  document.getElementById(id).scrollIntoView({ behavior:'smooth' });
}

function hideAll() {
  ['quizSection','memoriaSection','separarSection','palavrasSection',
   'verdadeiroSection','lixeirasSection','aprendaSection','rankingSection'
  ].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.style.display = 'none';
  });
}

function startGame(game) {
  hideAll();
  if (!gamesPlayed.includes(game)) { gamesPlayed.push(game); save(); }

  const map = {
    quiz: ()=>{ document.getElementById('quizSection').style.display='block'; initQuiz(); },
    memoria: ()=>{ document.getElementById('memoriaSection').style.display='block'; initMemory(4, null); },
    separar: ()=>{ document.getElementById('separarSection').style.display='block'; resetSeparar(); },
    palavras:()=>{ document.getElementById('palavrasSection').style.display='block'; initCaca(); },
    verdadeiro:()=>{ document.getElementById('verdadeiroSection').style.display='block'; initVM(); },
    lixeiras:()=>{ document.getElementById('lixeirasSection').style.display='block'; initLixeiras(); },
    aprenda: ()=>{ document.getElementById('aprendaSection').style.display='block'; initFatos(); },
  };
  map[game]?.();
  document.querySelector('.kids-section[style*="block"]')?.scrollIntoView({behavior:'smooth'});
  checkTodosJogos();
}

// =============================================
// TOAST
// =============================================
function showKidsToast(msg) {
  const el = document.getElementById('kidsToast');
  if (!el) return;
  el.textContent = msg;
  el.style.display = 'flex';
  el.classList.add('show');
  clearTimeout(el._t);
  el._t = setTimeout(()=>{ el.classList.remove('show'); setTimeout(()=>el.style.display='none',400); }, 3000);
}

// =============================================
// QUIZ DINÂMICO
// =============================================
let quizQueue = [], quizIdx = 0, quizPts = 0, quizVidas = 3, quizStreak = 0;

function shuffle(arr) { return [...arr].sort(()=>Math.random()-0.5); }

function initQuiz() {
  // Pega 10 perguntas aleatórias, balanceando dificuldade
  const easy   = shuffle(QUIZ_BANCO.filter(q=>q.dif==='easy')).slice(0,4);
  const medium = shuffle(QUIZ_BANCO.filter(q=>q.dif==='medium')).slice(0,4);
  const hard   = shuffle(QUIZ_BANCO.filter(q=>q.dif==='hard')).slice(0,2);
  quizQueue = shuffle([...easy, ...medium, ...hard]);
  quizIdx = 0; quizPts = 0; quizVidas = 3; quizStreak = 0;
  document.getElementById('quizResult').style.display = 'none';
  document.getElementById('quizArea').style.display = 'block';
  document.getElementById('quizScore').textContent = '0';
  document.getElementById('quizVidas').textContent = '❤️❤️❤️';
  document.getElementById('quizStreak').textContent = '0';
  renderQuizPergunta();
}

function renderQuizPergunta() {
  if (quizIdx >= quizQueue.length || quizVidas <= 0) { mostrarResultadoQuiz(); return; }
  const p = quizQueue[quizIdx];
  const pct = (quizIdx / quizQueue.length) * 100;
  document.getElementById('quizProgressBar').style.width = pct + '%';
  document.getElementById('quizProgressText').textContent = `Pergunta ${quizIdx+1} de ${quizQueue.length} | ${p.dif === 'easy' ? '🟢 Fácil' : p.dif === 'medium' ? '🟡 Médio' : '🔴 Difícil'}`;

  document.getElementById('quizArea').innerHTML = `
    <div class="quiz-question">
      <p class="quiz-q-text">${p.q}</p>
      <div class="quiz-options">
        ${p.ops.map((op,i)=>`<button class="quiz-option" onclick="responderQuiz(${i})">${op}</button>`).join('')}
      </div>
      <div class="quiz-feedback" id="quizFeedback" style="display:none;"></div>
    </div>`;
}

function responderQuiz(idx) {
  const p = quizQueue[quizIdx];
  const btns = document.querySelectorAll('.quiz-option');
  btns.forEach(b => b.disabled = true);
  btns.forEach((b, i) => {
    if (i === p.c) b.classList.add('correct');
    else if (i === idx && idx !== p.c) b.classList.add('wrong');
  });

  const fb = document.getElementById('quizFeedback');
  fb.style.display = 'flex';

  if (idx === p.c) {
    quizStreak++;
    const bonus = quizStreak >= 3 ? 10 : 0;
    const pts = (p.dif === 'easy' ? 5 : p.dif === 'medium' ? 8 : 12) + bonus;
    quizPts += pts;
    document.getElementById('quizScore').textContent = quizPts;
    document.getElementById('quizStreak').textContent = quizStreak;
    fb.innerHTML = `<span>✅</span> Correto!${bonus > 0 ? ' 🔥 Bônus de sequência +'+bonus+' pts!' : ''} ${p.exp}`;
    fb.className = 'quiz-feedback correct-fb';
    if (quizStreak >= 5) unlockConquista('c-streak');
  } else {
    quizStreak = 0;
    quizVidas--;
    document.getElementById('quizStreak').textContent = '0';
    const hearts = '❤️'.repeat(quizVidas) + '🖤'.repeat(3-quizVidas);
    document.getElementById('quizVidas').textContent = hearts;
    fb.innerHTML = `<span>❌</span> ${p.exp}`;
    fb.className = 'quiz-feedback wrong-fb';
  }

  quizIdx++;
  setTimeout(renderQuizPergunta, 2800);
}

function mostrarResultadoQuiz() {
  document.getElementById('quizArea').style.display = 'none';
  document.getElementById('quizProgressBar').style.width = '100%';
  const total = quizQueue.length;
  const corretas = quizQueue.slice(0, quizIdx).filter((_,i) => {
    // Não conseguimos rastrear exatamente sem overhead — usamos pontos como proxy
    return true;
  }).length;

  const stars = quizPts >= 80 ? 3 : quizPts >= 50 ? 2 : 1;
  const starsHtml = '⭐'.repeat(stars) + '☆'.repeat(3-stars);

  let titulo, icon;
  if (quizPts >= 80) { titulo = 'Incrível! Expert em Reciclagem!'; icon = '🏆'; ganharPontos(50,'Quiz completado com maestria!'); unlockConquista('c-quiz'); }
  else if (quizPts >= 40) { titulo = 'Muito bem! Continue aprendendo!'; icon = '⭐'; ganharPontos(30,'Quiz completado!'); }
  else { titulo = 'Continue praticando!'; icon = '💪'; ganharPontos(10,'Você tentou!'); }

  document.getElementById('quizResult').style.display = 'flex';
  document.getElementById('quizResult').innerHTML = `
    <div class="result-icon">${icon}</div>
    <h3>${titulo}</h3>
    <div class="result-stars">${starsHtml}</div>
    <p>Você fez <strong>${quizPts} pontos</strong> nesta rodada!</p>
    <button class="kids-btn" onclick="initQuiz()">Jogar de novo! 🔄</button>
    <button class="kids-btn secondary" onclick="voltarMenu()">Outros Jogos</button>
  `;
  mascotFalar(quizPts >= 80 ? 'UHUUU! Você é demais! 🏆' : 'Bom jogo! Pratique mais! 💪');
}

// =============================================
// JOGO DA MEMÓRIA
// =============================================
const MEM_POOL = [
  {id:1,e:'📄',n:'Papel'},{id:2,e:'🧴',n:'Plástico'},{id:3,e:'🥫',n:'Metal'},
  {id:4,e:'🍶',n:'Vidro'},{id:5,e:'🔋',n:'Pilha'},{id:6,e:'🌱',n:'Planta'},
  {id:7,e:'♻️',n:'Reciclar'},{id:8,e:'🌍',n:'Planeta'},
];
let memCards=[], memFlipped=[], memMatched=0, memTent=0, memLocked=false;
let memTimer=null, memSeconds=0, currentMemPares=4;

function initMemory(pares, btnEl) {
  if (btnEl) {
    document.querySelectorAll('.nivel-btn').forEach(b=>b.classList.remove('active'));
    btnEl.classList.add('active');
  }
  currentMemPares = pares;
  memMatched = 0; memTent = 0; memFlipped = []; memLocked = false;
  clearInterval(memTimer); memSeconds = 0;
  document.getElementById('tentativas').textContent = '0';
  document.getElementById('paresEncontrados').textContent = '0';
  document.getElementById('totalPares').textContent = pares;
  document.getElementById('memoryWin').style.display = 'none';
  document.getElementById('memoriaTimer').textContent = '0s';

  const pool = shuffle(MEM_POOL).slice(0, pares);
  memCards = shuffle([...pool, ...pool].map((c,i) => ({ ...c, uid: i, flipped: false, matched: false })));

  const cols = pares <= 4 ? 4 : pares <= 6 ? 4 : 4;
  const grid = document.getElementById('memoryGrid');
  grid.style.gridTemplateColumns = `repeat(${cols}, 1fr)`;
  renderMemory();

  memTimer = setInterval(() => {
    memSeconds++;
    document.getElementById('memoriaTimer').textContent = memSeconds + 's';
  }, 1000);
}

function renderMemory() {
  const grid = document.getElementById('memoryGrid');
  grid.innerHTML = '';
  memCards.forEach((card, idx) => {
    const div = document.createElement('div');
    div.className = 'memory-card' + (card.flipped || card.matched ? ' flipped' : '') + (card.matched ? ' matched' : '');
    div.innerHTML = `<div class="mc-inner"><div class="mc-front">♻️</div><div class="mc-back">${card.e}<span>${card.n}</span></div></div>`;
    if (!card.matched) div.onclick = () => flipCard(idx);
    grid.appendChild(div);
  });
}

function flipCard(idx) {
  if (memLocked || memCards[idx].flipped || memCards[idx].matched || memFlipped.length >= 2) return;
  memCards[idx].flipped = true;
  memFlipped.push(idx);
  renderMemory();

  if (memFlipped.length === 2) {
    memLocked = true; memTent++;
    document.getElementById('tentativas').textContent = memTent;
    const [a, b] = memFlipped;
    if (memCards[a].id === memCards[b].id) {
      setTimeout(() => {
        memCards[a].matched = memCards[b].matched = true;
        memCards[a].flipped = memCards[b].flipped = false;
        memMatched++; memFlipped = []; memLocked = false;
        document.getElementById('paresEncontrados').textContent = memMatched;
        renderMemory();
        if (memMatched === currentMemPares) {
          clearInterval(memTimer);
          const stars = memTent <= currentMemPares+2 ? 3 : memTent <= currentMemPares*2 ? 2 : 1;
          setTimeout(() => {
            document.getElementById('memTempo').textContent = memSeconds + 's';
            document.getElementById('memTentativas').textContent = memTent;
            document.getElementById('memStars').innerHTML = '⭐'.repeat(stars) + '☆'.repeat(3-stars);
            document.getElementById('memoryWin').style.display = 'flex';
            ganharPontos(10 * stars, 'Jogo da Memória completado!');
            unlockConquista('c-memoria');
          }, 400);
        }
      }, 500);
    } else {
      setTimeout(() => {
        memCards[a].flipped = memCards[b].flipped = false;
        memFlipped = []; memLocked = false;
        renderMemory();
      }, 900);
    }
  }
}

// =============================================
// JOGO: SALVA A CIDADE
// =============================================
let sepTimer = null, sepTimeLeft = 30, sepAcertos = 0, sepErros = 0, sepPontosJogo = 0;
let sepCurrentItem = null, sepActive = false;

function resetSeparar() {
  stopSepararGame();
  document.getElementById('sepInstructions').style.display = 'block';
  document.getElementById('sepGame').style.display = 'none';
  document.getElementById('sepResult').style.display = 'none';
}

function startSeparar() {
  sepTimeLeft = 30; sepAcertos = 0; sepErros = 0; sepPontosJogo = 0; sepActive = true;
  document.getElementById('sepInstructions').style.display = 'none';
  document.getElementById('sepGame').style.display = 'block';
  document.getElementById('sepResult').style.display = 'none';
  document.getElementById('sepTimer').textContent = '30';
  document.getElementById('sepAcertos').textContent = '0';
  document.getElementById('sepErros').textContent = '0';
  document.getElementById('sepPontos').textContent = '0';
  document.getElementById('sepFeedback').textContent = '';

  sepTimer = setInterval(() => {
    sepTimeLeft--;
    document.getElementById('sepTimer').textContent = sepTimeLeft;
    if (sepTimeLeft <= 0) { stopSepararGame(); mostrarResultadoSeparar(); }
  }, 1000);
  proximoItemSeparar();
}

function proximoItemSeparar() {
  if (!sepActive) return;
  sepCurrentItem = SEPARAR_ITENS[Math.floor(Math.random() * SEPARAR_ITENS.length)];
  const el = document.getElementById('sepItemDisplay');
  el.style.display = 'flex';
  el.innerHTML = `<span class="sep-emoji">${sepCurrentItem.emoji}</span><span class="sep-nome">${sepCurrentItem.nome}</span>`;

  // Animação de aparecimento
  el.style.animation = 'none';
  void el.offsetWidth;
  el.style.animation = 'sepPop 0.4s ease';
}

function responderSeparar(lixeira) {
  if (!sepCurrentItem || !sepActive) return;
  const fb = document.getElementById('sepFeedback');
  const lixBtns = document.querySelectorAll('.sep-lixeira');

  if (lixeira === sepCurrentItem.lixeira) {
    sepAcertos++;
    sepPontosJogo += 6;
    fb.innerHTML = `<span class="fb-ok">✅ Correto! ${sepCurrentItem.dica}</span>`;
    lixBtns.forEach(b => { if (b.dataset.cor === lixeira) { b.classList.add('sep-correct'); setTimeout(()=>b.classList.remove('sep-correct'),500); } });
  } else {
    sepErros++;
    fb.innerHTML = `<span class="fb-err">❌ ${sepCurrentItem.dica}</span>`;
    lixBtns.forEach(b => { if (b.dataset.cor === lixeira) { b.classList.add('sep-wrong'); setTimeout(()=>b.classList.remove('sep-wrong'),500); } });
  }

  document.getElementById('sepAcertos').textContent = sepAcertos;
  document.getElementById('sepErros').textContent = sepErros;
  document.getElementById('sepPontos').textContent = sepPontosJogo;
  setTimeout(proximoItemSeparar, 600);
}

function stopSepararGame() { clearInterval(sepTimer); sepActive = false; }

function mostrarResultadoSeparar() {
  document.getElementById('sepGame').style.display = 'none';
  const pct = Math.round(sepAcertos / (sepAcertos + sepErros || 1) * 100);
  const stars = pct >= 80 ? 3 : pct >= 60 ? 2 : 1;
  const icon = pct >= 80 ? '🏆' : pct >= 60 ? '⭐' : '💪';
  ganharPontos(stars * 20, 'Salva a Cidade completado!');
  unlockConquista('c-separar');
  document.getElementById('sepResult').style.display = 'flex';
  document.getElementById('sepResult').innerHTML = `
    <div class="result-icon">${icon}</div>
    <h3>${pct >= 80 ? 'Cidade Salva!' : pct >= 60 ? 'Bom trabalho!' : 'Continue praticando!'}</h3>
    <div class="result-stars">${'⭐'.repeat(stars)}${'☆'.repeat(3-stars)}</div>
    <p>✅ <strong>${sepAcertos}</strong> acertos | ❌ <strong>${sepErros}</strong> erros | <strong>${pct}%</strong> de precisão</p>
    <button class="kids-btn" onclick="startSeparar()">Jogar de novo! 🔄</button>
    <button class="kids-btn secondary" onclick="voltarMenu()">Outros Jogos</button>
  `;
  mascotFalar(pct >= 80 ? 'A cidade está salva graças a você! 🏙️' : 'Boa tentativa! Tente novamente! 💪');
}

// =============================================
// CAÇA-PALAVRAS
// =============================================
const GRID_SIZE = 12;
let cacaGrid = [], cacaSelection = [], cacaFound = [];
let cacaTimerInterval = null, cacaSeconds = 0;
const WORD_LIST = CACA_PALAVRAS.slice(0, 8);

function initCaca() {
  cacaFound = []; cacaSelection = [];
  clearInterval(cacaTimerInterval); cacaSeconds = 0;
  document.getElementById('cacaTimer').textContent = '0s';
  document.getElementById('cacaEncontradas').textContent = '0';
  document.getElementById('cacaWin').style.display = 'none';

  cacaGrid = Array.from({length:GRID_SIZE}, () => Array(GRID_SIZE).fill(''));
  const placed = [];

  WORD_LIST.forEach(word => {
    let tries = 0;
    while (tries < 200) {
      tries++;
      const dir = Math.floor(Math.random() * 3); // 0=h,1=v,2=diag
      const row = Math.floor(Math.random() * GRID_SIZE);
      const col = Math.floor(Math.random() * GRID_SIZE);
      if (canPlace(word, row, col, dir)) { placeWord(word, row, col, dir); placed.push(word); break; }
    }
  });

  // Preenche espaços vazios
  const letters = 'ABCDEFGHIJKLMNOPRSTUVXZ';
  cacaGrid = cacaGrid.map(row => row.map(c => c || letters[Math.floor(Math.random() * letters.length)]));

  renderCaca();
  renderCacaLista();

  cacaTimerInterval = setInterval(() => {
    cacaSeconds++;
    document.getElementById('cacaTimer').textContent = cacaSeconds + 's';
  }, 1000);
}

function canPlace(word, r, c, dir) {
  for (let i = 0; i < word.length; i++) {
    const nr = r + (dir === 1 ? i : dir === 2 ? i : 0);
    const nc = c + (dir === 0 ? i : dir === 2 ? i : 0);
    if (nr < 0 || nr >= GRID_SIZE || nc < 0 || nc >= GRID_SIZE) return false;
    if (cacaGrid[nr][nc] && cacaGrid[nr][nc] !== word[i]) return false;
  }
  return true;
}

function placeWord(word, r, c, dir) {
  for (let i = 0; i < word.length; i++) {
    const nr = r + (dir === 1 ? i : dir === 2 ? i : 0);
    const nc = c + (dir === 0 ? i : dir === 2 ? i : 0);
    cacaGrid[nr][nc] = word[i];
  }
}

function renderCaca() {
  const grid = document.getElementById('cacaGrid');
  grid.innerHTML = '';
  grid.style.gridTemplateColumns = `repeat(${GRID_SIZE}, 1fr)`;
  for (let r = 0; r < GRID_SIZE; r++) {
    for (let c = 0; c < GRID_SIZE; c++) {
      const cell = document.createElement('div');
      cell.className = 'caca-cell';
      cell.dataset.r = r; cell.dataset.c = c;
      cell.textContent = cacaGrid[r][c];
      cell.addEventListener('mousedown', () => startSelect(r, c));
      cell.addEventListener('mouseover', () => continueSelect(r, c));
      cell.addEventListener('mouseup', () => endSelect());
      // Touch
      cell.addEventListener('touchstart', (e) => { e.preventDefault(); startSelect(r,c); }, {passive:false});
      cell.addEventListener('touchmove', (e) => {
        e.preventDefault();
        const t = e.touches[0];
        const el = document.elementFromPoint(t.clientX, t.clientY);
        if (el?.classList.contains('caca-cell')) continueSelect(+el.dataset.r, +el.dataset.c);
      }, {passive:false});
      cell.addEventListener('touchend', () => endSelect());
      grid.appendChild(cell);
    }
  }
  // Reaplica palavras encontradas
  cacaFound.forEach(w => highlightWord(w));
}

let isSelecting = false;
let selectStart = null;

function startSelect(r, c) { isSelecting = true; selectStart = {r,c}; cacaSelection = [{r,c}]; highlightSelection(); }

function continueSelect(r, c) {
  if (!isSelecting) return;
  const dr = c - selectStart.c, dc = r - selectStart.r;
  const len = Math.max(Math.abs(dr), Math.abs(dc));
  if (len === 0) { cacaSelection = [selectStart]; }
  else {
    const sr = dc === 0 ? 0 : dc/Math.abs(dc);
    const sc = dr === 0 ? 0 : dr/Math.abs(dr);
    if (!(dc === 0 || dr === 0 || Math.abs(dc) === Math.abs(dr))) return;
    cacaSelection = [];
    for (let i = 0; i <= len; i++) cacaSelection.push({r: selectStart.r+i*sr, c: selectStart.c+i*sc});
  }
  highlightSelection();
}

function endSelect() {
  if (!isSelecting) return;
  isSelecting = false;
  const word = cacaSelection.map(pos => cacaGrid[pos.r][pos.c]).join('');
  const wordRev = word.split('').reverse().join('');
  const match = WORD_LIST.find(w => w === word || w === wordRev);
  if (match && !cacaFound.includes(match)) {
    cacaFound.push(match);
    highlightWord(match);
    document.getElementById('cacaEncontradas').textContent = cacaFound.length;
    renderCacaLista();
    showKidsToast('🎉 "' + match + '" encontrada!');
    if (cacaFound.length === WORD_LIST.length) {
      clearInterval(cacaTimerInterval);
      document.getElementById('cacaWin').style.display = 'block';
      ganharPontos(40, 'Caça-Palavras completado em ' + cacaSeconds + 's!');
      unlockConquista('c-palavras');
      mascotFalar('Você encontrou todas as palavras! Incrível! 🔤');
    }
  }
  clearHighlightSelection();
  cacaSelection = [];
}

function highlightSelection() {
  document.querySelectorAll('.caca-cell').forEach(c => c.classList.remove('selecting'));
  cacaSelection.forEach(pos => {
    const el = document.querySelector(`.caca-cell[data-r="${pos.r}"][data-c="${pos.c}"]`);
    if (el) el.classList.add('selecting');
  });
}

function clearHighlightSelection() {
  document.querySelectorAll('.caca-cell.selecting').forEach(c => c.classList.remove('selecting'));
}

function highlightWord(word) {
  // Procura a palavra no grid e marca
  for (let r = 0; r < GRID_SIZE; r++) {
    for (let c = 0; c < GRID_SIZE; c++) {
      for (let dir = 0; dir < 3; dir++) {
        const cells = [];
        let valid = true;
        for (let i = 0; i < word.length; i++) {
          const nr = r + (dir===1?i:dir===2?i:0);
          const nc = c + (dir===0?i:dir===2?i:0);
          if (nr<0||nr>=GRID_SIZE||nc<0||nc>=GRID_SIZE) { valid=false; break; }
          if (cacaGrid[nr][nc] !== word[i]) { valid=false; break; }
          cells.push({r:nr,c:nc});
        }
        if (valid) {
          cells.forEach(pos => {
            const el = document.querySelector(`.caca-cell[data-r="${pos.r}"][data-c="${pos.c}"]`);
            if (el) el.classList.add('found');
          });
          return;
        }
      }
    }
  }
}

function renderCacaLista() {
  const ul = document.getElementById('cacaLista');
  ul.innerHTML = WORD_LIST.map(w => `
    <li class="${cacaFound.includes(w)?'caca-found':''}">${cacaFound.includes(w)?'✅':'⬜'} ${w}</li>
  `).join('');
}

// =============================================
// VERDADE OU MITO
// =============================================
let vmQueue = [], vmIdx = 0, vmPts = 0;

function initVM() {
  vmQueue = shuffle(VM_BANCO).slice(0, 10);
  vmIdx = 0; vmPts = 0;
  document.getElementById('vmResult').style.display = 'none';
  document.getElementById('vmArea').style.display = 'block';
  document.getElementById('vmScore').textContent = '0';
  document.getElementById('vmNum').textContent = '0';
  renderVM();
}

function renderVM() {
  if (vmIdx >= vmQueue.length) { mostrarResultadoVM(); return; }
  const item = vmQueue[vmIdx];
  document.getElementById('vmNum').textContent = vmIdx + 1;
  document.getElementById('vmArea').innerHTML = `
    <div class="vm-card">
      <div class="vm-afirmacao">${item.afirm}</div>
      <div class="vm-btns">
        <button class="vm-btn verdade" onclick="responderVM(true)">✅ VERDADE</button>
        <button class="vm-btn mito"    onclick="responderVM(false)">❌ MITO</button>
      </div>
      <div id="vmFeedback" style="display:none;" class="vm-feedback"></div>
    </div>
  `;
}

function responderVM(resp) {
  const item = vmQueue[vmIdx];
  document.querySelectorAll('.vm-btn').forEach(b => b.disabled = true);
  const fb = document.getElementById('vmFeedback');
  fb.style.display = 'block';

  if (resp === item.v) {
    vmPts += 10;
    fb.innerHTML = `✅ ${item.exp}`;
    fb.className = 'vm-feedback correct-fb';
    document.getElementById('vmScore').textContent = vmPts;
  } else {
    fb.innerHTML = `❌ ${item.exp}`;
    fb.className = 'vm-feedback wrong-fb';
  }
  vmIdx++;
  setTimeout(renderVM, 2500);
}

function mostrarResultadoVM() {
  document.getElementById('vmArea').style.display = 'none';
  const stars = vmPts >= 80 ? 3 : vmPts >= 50 ? 2 : 1;
  ganharPontos(stars * 12, 'Verdade ou Mito completado!');
  unlockConquista('c-vm');
  document.getElementById('vmResult').style.display = 'flex';
  document.getElementById('vmResult').innerHTML = `
    <div class="result-icon">${vmPts >= 80 ? '🏆' : '⭐'}</div>
    <h3>${vmPts >= 80 ? 'Caça Mitos Oficial!' : 'Bom trabalho!'}</h3>
    <div class="result-stars">${'⭐'.repeat(stars)}${'☆'.repeat(3-stars)}</div>
    <p>Você fez <strong>${vmPts} pontos</strong> de 100!</p>
    <button class="kids-btn" onclick="initVM()">Jogar de novo! 🔄</button>
    <button class="kids-btn secondary" onclick="voltarMenu()">Outros Jogos</button>
  `;
}

// =============================================
// MESTRE DAS LIXEIRAS
// =============================================
let lxSelecionado = null, lxAcertosCount = 0, lxTotal = 12;
let lxItensRestantes = [];

function initLixeiras() {
  lxAcertosCount = 0; lxSelecionado = null;
  document.getElementById('lxAcertos').textContent = '0';
  document.getElementById('lxWin').style.display = 'none';
  document.getElementById('lxFeedback').textContent = '';
  lxItensRestantes = shuffle(LIXEIRAS_ITENS).slice(0, lxTotal);
  renderLixeirasGame();
}

function renderLixeirasGame() {
  const itensArea = document.getElementById('lxItensArea');
  const lixArea   = document.getElementById('lxLixeirasArea');

  itensArea.innerHTML = lxItensRestantes.map(item => `
    <div class="lx-item" data-id="${item.nome}" onclick="selecionarLxItem(this,'${item.lixeira}','${item.emoji} ${item.nome}')">
      <span>${item.emoji}</span>
      <small>${item.nome}</small>
    </div>
  `).join('');

  lixArea.innerHTML = LIXEIRAS_CONFIG.map(lx => `
    <div class="lx-lixeira-btn" style="--lbg:${lx.bg}" onclick="jogarNaLxLixeira('${lx.id}')">
      <span class="lxb-emoji">${lx.emoji}</span>
      <span class="lxb-nome">${lx.nome}</span>
    </div>
  `).join('');
}

function selecionarLxItem(el, lixeira, nome) {
  document.querySelectorAll('.lx-item').forEach(i => i.classList.remove('lx-selected'));
  el.classList.add('lx-selected');
  lxSelecionado = { lixeira, nome, el };
  document.getElementById('lxFeedback').innerHTML = `📌 <em>"${nome}"</em> selecionado. Clique na lixeira correta!`;
  document.getElementById('lxFeedback').className = 'lx-feedback info';
}

function jogarNaLxLixeira(lixId) {
  if (!lxSelecionado) { document.getElementById('lxFeedback').textContent = '⚠️ Primeiro clique em um item!'; return; }
  const fb = document.getElementById('lxFeedback');

  if (lixId === lxSelecionado.lixeira) {
    lxAcertosCount++;
    document.getElementById('lxAcertos').textContent = lxAcertosCount;
    fb.innerHTML = `✅ Correto! <em>${lxSelecionado.nome}</em> vai na lixeira ${lixId}!`;
    fb.className = 'lx-feedback correct';
    lxSelecionado.el.style.opacity = '0.2';
    lxSelecionado.el.style.pointerEvents = 'none';
    lxSelecionado.el.classList.remove('lx-selected');
    lxSelecionado = null;
    ganharPontos(2, '');

    if (lxAcertosCount === lxTotal) {
      document.getElementById('lxWinText').textContent = `Você acertou todos os ${lxTotal} itens! `;
      document.getElementById('lxWin').style.display = 'block';
      ganharPontos(25, 'Mestre das Lixeiras!');
      unlockConquista('c-lixeira');
      mascotFalar('Você é o Mestre das Lixeiras! 🗑️🏆');
    }
  } else {
    fb.innerHTML = `❌ Ops! <em>${lxSelecionado.nome}</em> NÃO vai na lixeira ${lixId}. Tente de novo!`;
    fb.className = 'lx-feedback wrong';
    document.querySelectorAll('.lx-item').forEach(i => i.classList.remove('lx-selected'));
    lxSelecionado = null;
  }
}

// =============================================
// FATOS INCRÍVEIS
// =============================================
let fatoAtual = 0;
let fatosLidosSet = new Set();

function initFatos() {
  fatoAtual = 0; fatosLidosSet = new Set();
  renderFato();
}

function renderFato() {
  const fato = FATOS_LISTA[fatoAtual];
  document.getElementById('fatoIndicador').textContent = `${fatoAtual + 1} / ${FATOS_LISTA.length}`;
  const card = document.getElementById('fatoCard');
  card.style.opacity = '0';
  setTimeout(() => {
    card.innerHTML = `
      <div class="fato-emoji-big">${fato.emoji}</div>
      <h3>${fato.titulo}</h3>
      <p>${fato.texto}</p>
    `;
    card.style.opacity = '1';
    fatosLidosSet.add(fatoAtual);
  }, 200);
}

function nextFato() { fatoAtual = (fatoAtual + 1) % FATOS_LISTA.length; renderFato(); }
function prevFato() { fatoAtual = (fatoAtual - 1 + FATOS_LISTA.length) % FATOS_LISTA.length; renderFato(); }

function completarFatos() {
  ganharPontos(15, 'Leu todos os fatos incríveis!');
  showKidsToast('📚 Você é muito curioso! +15 pts!');
  mascotFalar('Conhecimento é poder! Você aprendeu muito! 🌟');
}

// =============================================
// RANKING KIDS
// =============================================
function renderRanking() {
  const meuPts = kidsPoints;
  const meuNome = playerAvatar + ' ' + playerName;
  const rankData = [...RANKING_FAKE, {nome: meuNome, pts: meuPts, nivel: getNivel(meuPts).nome, jogos: gamesPlayed.length}]
    .sort((a,b) => b.pts - a.pts);

  // Top 3
  const top3 = document.getElementById('rankingTop3');
  const podium = [{pos:1,medal:'🥇',bg:'#fbbf24'},{pos:0,medal:'🥈',bg:'#94a3b8'},{pos:2,medal:'🥉',bg:'#d97706'}];
  const ord = [rankData[1], rankData[0], rankData[2]]; // ordem visual do pódio

  top3.innerHTML = ord.map((r,i) => `
    <div class="rk-podium-card" style="--pk:${podium[i].bg}">
      <div class="rk-medal">${podium[i].medal}</div>
      <div class="rk-avatar-big">${r.nome.split(' ')[0]}</div>
      <h4>${r.nome}</h4>
      <div class="rk-pts-big">${r.pts.toLocaleString('pt-BR')} pts</div>
      <div class="rk-nivel-s">${r.nivel}</div>
    </div>
  `).join('');

  // Tabela
  const table = document.getElementById('rankingTable');
  table.innerHTML = rankData.map((r, i) => `
    <div class="rk-row ${r.nome === meuNome ? 'rk-meu' : ''}">
      <span class="rk-pos">${i+1}º</span>
      <span class="rk-nm">${r.nome}</span>
      <span class="rk-nv">${r.nivel}</span>
      <span class="rk-jg">${r.jogos} jogos</span>
      <span class="rk-pt">${r.pts.toLocaleString('pt-BR')} ⭐</span>
    </div>
  `).join('');

  // Seu card
  const meuPos = rankData.findIndex(r => r.nome === meuNome) + 1;
  document.getElementById('seuCard').innerHTML = `
    <div class="seu-pos">${meuPos}º lugar</div>
    <div class="seu-avatar">${playerAvatar}</div>
    <div class="seu-info">
      <strong>${playerName}</strong>
      <span>${getNivel(meuPts).nome}</span>
      <span>${meuPts} pontos</span>
    </div>
  `;

  // Fatos de reciclagem no ranking
  const factsEl = document.getElementById('recyclingFactsRanking');
  const rfacts = shuffle([
    "🇧🇷 O Brasil recicla 97% das latas de alumínio – somos recordistas mundiais!",
    "⚡ Reciclar alumínio economiza 95% de energia em comparação à produção nova.",
    "🌊 8 milhões de toneladas de plástico vão para os oceanos anualmente.",
    "🌳 1 tonelada de papel reciclado poupa 20 árvores do corte.",
    "🍶 Uma garrafa de vidro pode levar mais de 4.000 anos para se decompor.",
    "👕 25 garrafas PET recicladas produzem uma jaqueta de frio.",
    "💧 Reciclar 1kg de papel economiza 300 litros de água.",
    "⏱️ Uma lata de alumínio reciclada pode voltar à prateleira em 60 dias!",
  ]).slice(0, 4);

  factsEl.innerHTML = rfacts.map(f => `<div class="rf-item">${f}</div>`).join('');
}

// =============================================
// CONQUISTAS
// =============================================
function unlockConquista(id) {
  const el = document.getElementById(id);
  if (el && el.classList.contains('locked')) {
    el.classList.remove('locked');
    el.classList.add('unlocked');
    showKidsToast('🏅 Nova conquista desbloqueada!');
    const saved = JSON.parse(localStorage.getItem('kidsConquistas') || '[]');
    if (!saved.includes(id)) { saved.push(id); localStorage.setItem('kidsConquistas', JSON.stringify(saved)); }
  }
}

function loadConquistas() {
  JSON.parse(localStorage.getItem('kidsConquistas') || '[]').forEach(id => {
    const el = document.getElementById(id);
    if (el) { el.classList.remove('locked'); el.classList.add('unlocked'); }
  });
}

function checkConquistas() {
  if (kidsPoints >= 100)  unlockConquista('c-100pts');
  if (kidsPoints >= 500)  unlockConquista('c-500pts');
  if (kidsPoints >= 50)   unlockConquista('c-recico');
}

function checkTodosJogos() {
  const todos = ['quiz','memoria','separar','palavras','verdadeiro','lixeiras','aprenda'];
  if (todos.every(g => gamesPlayed.includes(g))) unlockConquista('c-todos');
}

// =============================================
// INICIALIZAÇÃO
// =============================================
document.addEventListener('DOMContentLoaded', () => {
  updateUI();
  loadConquistas();
  checkConquistas();

  // Pergunta nome se for primeira vez
  if (!localStorage.getItem('playerName')) {
    setTimeout(() => document.getElementById('modalNome').style.display = 'flex', 1500);
  }

  // Observa quando ranking é aberto
  const origShow = showSection;
  window.showSection = function(id) {
    origShow(id);
    if (id === 'rankingSection') renderRanking();
  };
});

// Fechar modais com ESC
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    document.getElementById('modalNome').style.display = 'none';
    document.getElementById('levelUpOverlay').style.display = 'none';
  }
});

document.getElementById('modalNome')?.addEventListener('click', e => {
  if (e.target === document.getElementById('modalNome')) document.getElementById('modalNome').style.display = 'none';
});
