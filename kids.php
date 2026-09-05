<?php require_once __DIR__ . '/auth.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Área Kids – ColetaFácil</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="kids.css">
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body class="kids-body">

  <!-- NAVBAR KIDS -->
  <nav class="navbar kids-navbar">
    <div class="container nav-inner">
      <a href="index.php" class="logo kids-logo"><span>🌍</span><span>ColetaFácil Kids</span></a>
      <div class="kids-nav-right">
        <div class="kids-nav-points">⭐ <span id="kidsPointsNav">0</span> pts</div>
        <a href="index.php" class="kids-back-btn">← Voltar ao site</a>
        <?= site_auth_mobile_links() ?>
      </div>
    </div>
  </nav>

  <!-- HERO KIDS -->
  <section class="kids-hero">
    <div class="kids-hero-bubbles" id="heroBubbles"></div>
    <div class="container kids-hero-inner">

      <!-- MASCOTE RECICO -->
      <div class="kids-mascot">
        <div class="mascot-wrap">
          <div class="mascot-body" id="mascotBody">
            <div class="mascot-eyes-row">
              <div class="mascot-eye"><div class="eye-pupil"></div></div>
              <div class="mascot-eye"><div class="eye-pupil"></div></div>
            </div>
            <div class="mascot-smile">😊</div>
            <div class="mascot-badge">♻️</div>
          </div>
          <div class="mascot-speech" id="mascotSpeech">
            Olá! Vamos salvar o planeta juntos? 🌍
          </div>
        </div>
        <p class="mascot-name">🐸 Recico, o Guardião</p>
      </div>

      <!-- INFO DO JOGADOR -->
      <div class="kids-hero-text">
        <div class="player-card" id="playerCard">
          <div class="player-top">
            <div class="player-avatar" id="playerAvatar">🌱</div>
            <div class="player-info">
              <div class="player-name-wrap">
                <span id="playerNameDisplay">Eco Herói</span>
                <button class="edit-name-btn" onclick="editarNome()">✏️</button>
              </div>
              <div class="player-level">
                <span id="playerLevelBadge" class="level-badge">🌱 Muda</span>
              </div>
            </div>
            <div class="player-pts">
              <span class="pts-big" id="kidsPoints">0</span>
              <span class="pts-label">pontos</span>
            </div>
          </div>
          <div class="xp-bar-section">
            <div class="xp-bar-wrap">
              <div class="xp-bar" id="xpBar" style="width:0%"></div>
            </div>
            <span class="xp-text" id="xpText">0 / 200 XP para o próximo nível</span>
          </div>
        </div>

        <!-- PLANETA DO JOGADOR -->
        <div class="planet-card">
          <div class="planet-display">
            <div class="planet" id="planetDisplay">🌑</div>
            <div class="planet-info">
              <h4>Seu Planeta</h4>
              <p id="planetStatus">Recicle mais para deixar o planeta verde!</p>
              <div class="planet-bar-wrap">
                <div class="planet-bar" id="planetBar" style="width:0%"></div>
              </div>
              <span id="planetPct">0% ecológico</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- MODAL NOME -->
  <div id="modalNome" class="modal" style="display:none;">
    <div class="modal-content kids-modal">
      <h3>✏️ Qual é o seu nome?</h3>
      <p>Escolha seu apelido de herói da reciclagem!</p>
      <div class="avatar-picker" id="avatarPicker">
        <span class="av-opt selected" onclick="selectAvatar(this,'🌱')">🌱</span>
        <span class="av-opt" onclick="selectAvatar(this,'🐸')">🐸</span>
        <span class="av-opt" onclick="selectAvatar(this,'🦋')">🦋</span>
        <span class="av-opt" onclick="selectAvatar(this,'🐬')">🐬</span>
        <span class="av-opt" onclick="selectAvatar(this,'🦁')">🦁</span>
        <span class="av-opt" onclick="selectAvatar(this,'🐼')">🐼</span>
        <span class="av-opt" onclick="selectAvatar(this,'🦊')">🦊</span>
        <span class="av-opt" onclick="selectAvatar(this,'🐢')">🐢</span>
      </div>
      <input type="text" id="nomeInput" placeholder="Ex: SuperEco, Verde Kid..." maxlength="16">
      <button class="kids-btn" onclick="salvarNome()">Salvar! 🚀</button>
    </div>
  </div>

  <!-- ============= MENU DE JOGOS ============= -->
  <section class="kids-activities" id="mainMenu">
    <div class="container">
      <h2 class="kids-section-title">🎮 Escolha sua aventura!</h2>
      <div class="kids-activity-grid">

        <div class="activity-card quiz-card" onclick="startGame('quiz')">
          <div class="ac-emoji">🧠</div>
          <h3>Quiz Dinâmico</h3>
          <p>30+ perguntas que mudam toda vez!</p>
          <div class="ac-meta">
            <span class="ac-badge">+50 pts</span>
            <span class="ac-diff easy">Fácil → Difícil</span>
          </div>
        </div>

        <div class="activity-card memoria-card" onclick="startGame('memoria')">
          <div class="ac-emoji">🃏</div>
          <h3>Jogo da Memória</h3>
          <p>3 níveis: Fácil, Médio e Difícil!</p>
          <div class="ac-meta">
            <span class="ac-badge">+30 pts</span>
            <span class="ac-diff medium">3 Níveis</span>
          </div>
        </div>

        <div class="activity-card separar-card" onclick="startGame('separar')">
          <div class="ac-emoji">🎯</div>
          <h3>Salva a Cidade!</h3>
          <p>Itens caem do céu! Clique na lixeira certa!</p>
          <div class="ac-meta">
            <span class="ac-badge">+60 pts</span>
            <span class="ac-diff hard">Tempo Limit</span>
          </div>
        </div>

        <div class="activity-card palavras-card" onclick="startGame('palavras')">
          <div class="ac-emoji">🔤</div>
          <h3>Caça-Palavras</h3>
          <p>Encontre 8 palavras sobre reciclagem!</p>
          <div class="ac-meta">
            <span class="ac-badge">+40 pts</span>
            <span class="ac-diff medium">Palavras</span>
          </div>
        </div>

        <div class="activity-card verdadeiro-card" onclick="startGame('verdadeiro')">
          <div class="ac-emoji">✅</div>
          <h3>Verdade ou Mito?</h3>
          <p>É verdade que vidro leva 4000 anos? Descubra!</p>
          <div class="ac-meta">
            <span class="ac-badge">+35 pts</span>
            <span class="ac-diff easy">V ou F</span>
          </div>
        </div>

        <div class="activity-card lixeiras-card" onclick="startGame('lixeiras')">
          <div class="ac-emoji">🗑️</div>
          <h3>Mestre das Lixeiras</h3>
          <p>Arraste cada item para a lixeira certa!</p>
          <div class="ac-meta">
            <span class="ac-badge">+25 pts</span>
            <span class="ac-diff easy">Interativo</span>
          </div>
        </div>

        <div class="activity-card aprenda-card" onclick="startGame('aprenda')">
          <div class="ac-emoji">📚</div>
          <h3>Fatos Incríveis</h3>
          <p>Curiosidades sobre reciclagem que vão te impressionar!</p>
          <div class="ac-meta">
            <span class="ac-badge">+15 pts</span>
            <span class="ac-diff easy">Leitura</span>
          </div>
        </div>

        <div class="activity-card ranking-card-btn" onclick="showSection('rankingSection')">
          <div class="ac-emoji">🏆</div>
          <h3>Ranking Kids</h3>
          <p>Veja quem são os maiores heróis da reciclagem!</p>
          <div class="ac-meta">
            <span class="ac-badge">Ver</span>
            <span class="ac-diff medium">Ranking</span>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ============================================================
       SEÇÃO: QUIZ DINÂMICO
  ============================================================ -->
  <section id="quizSection" class="kids-section" style="display:none;">
    <div class="container">
      <button class="kids-back" onclick="voltarMenu()">← Voltar</button>
      <div class="quiz-container">
        <div class="quiz-header">
          <h2>🧠 Quiz Dinâmico</h2>
          <div class="quiz-stats-row">
            <span class="quiz-stat">❤️ <span id="quizVidas">3</span></span>
            <span class="quiz-stat">⭐ <span id="quizScore">0</span></span>
            <span class="quiz-stat">🔥 Sequência: <span id="quizStreak">0</span></span>
          </div>
        </div>
        <div class="quiz-progress"><div class="quiz-progress-bar" id="quizProgressBar"></div></div>
        <p class="quiz-progress-text" id="quizProgressText">Carregando...</p>
        <div id="quizArea"></div>
        <div id="quizResult" style="display:none;" class="quiz-result"></div>
      </div>
    </div>
  </section>

  <!-- ============================================================
       SEÇÃO: MEMÓRIA
  ============================================================ -->
  <section id="memoriaSection" class="kids-section" style="display:none;">
    <div class="container">
      <button class="kids-back" onclick="voltarMenu()">← Voltar</button>
      <div class="memory-container">
        <div class="memory-header">
          <h2>🃏 Jogo da Memória</h2>
          <div class="memory-stats">
            <span>⏱️ <span id="memoriaTimer">0s</span></span>
            <span>🔄 <span id="tentativas">0</span> tent.</span>
            <span>✅ <span id="paresEncontrados">0</span>/<span id="totalPares">0</span></span>
          </div>
        </div>
        <div id="nivelSelector" class="nivel-selector">
          <button class="nivel-btn active" onclick="initMemory(4, this)">🟢 Fácil (4 pares)</button>
          <button class="nivel-btn" onclick="initMemory(6, this)">🟡 Médio (6 pares)</button>
          <button class="nivel-btn" onclick="initMemory(8, this)">🔴 Difícil (8 pares)</button>
        </div>
        <div class="memory-grid" id="memoryGrid"></div>
        <div id="memoryWin" style="display:none;" class="memory-win">
          <div class="win-icon">🏆</div>
          <h3>Parabéns!</h3>
          <p>Tempo: <strong id="memTempo"></strong> | Tentativas: <strong id="memTentativas"></strong></p>
          <div id="memStars" class="stars-row"></div>
          <button class="kids-btn" onclick="initMemory(currentMemPares, null)">Jogar de novo! 🔄</button>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================================
       SEÇÃO: SALVA A CIDADE (FALLING ITEMS)
  ============================================================ -->
  <section id="separarSection" class="kids-section" style="display:none;">
    <div class="container">
      <button class="kids-back" onclick="voltarMenu(); stopSepararGame()">← Voltar</button>
      <div class="separar-container">
        <div class="separar-header">
          <h2>🎯 Salva a Cidade!</h2>
          <div class="separar-stats">
            <span>⏱️ <span id="sepTimer">30</span>s</span>
            <span>✅ <span id="sepAcertos">0</span></span>
            <span>❌ <span id="sepErros">0</span></span>
            <span>⭐ <span id="sepPontos">0</span></span>
          </div>
        </div>
        <div id="sepInstructions" class="sep-instructions">
          <p>🗑️ Itens vão cair do céu! Clique no item e depois na lixeira correta. Rápido!</p>
          <div class="sep-lixeiras-preview">
            <span>🟦 Papel</span>
            <span>🟥 Plástico</span>
            <span>🟨 Metal</span>
            <span>🟩 Vidro</span>
            <span>🟫 Orgânico</span>
            <span>⬛ Rejeito</span>
          </div>
          <button class="kids-btn" onclick="startSeparar()">Começar! 🚀</button>
        </div>
        <div id="sepGame" style="display:none;">
          <div class="sep-arena" id="sepArena">
            <div id="sepItemDisplay" class="sep-falling-item" style="display:none;"></div>
          </div>
          <div class="sep-lixeiras-row" id="sepLixeirasRow">
            <div class="sep-lixeira" data-cor="azul"    onclick="responderSeparar('azul')">    <span>📄</span><br>Azul</div>
            <div class="sep-lixeira" data-cor="vermelha" onclick="responderSeparar('vermelha')"><span>🧴</span><br>Verm.</div>
            <div class="sep-lixeira" data-cor="amarela" onclick="responderSeparar('amarela')"> <span>🥫</span><br>Amar.</div>
            <div class="sep-lixeira" data-cor="verde"   onclick="responderSeparar('verde')">   <span>🍶</span><br>Verde</div>
            <div class="sep-lixeira" data-cor="marrom"  onclick="responderSeparar('marrom')">  <span>🍎</span><br>Marrom</div>
            <div class="sep-lixeira" data-cor="cinza"   onclick="responderSeparar('cinza')">   <span>🗑️</span><br>Cinza</div>
          </div>
          <div class="sep-feedback" id="sepFeedback"></div>
        </div>
        <div id="sepResult" style="display:none;" class="sep-result"></div>
      </div>
    </div>
  </section>

  <!-- ============================================================
       SEÇÃO: CAÇA-PALAVRAS
  ============================================================ -->
  <section id="palavrasSection" class="kids-section" style="display:none;">
    <div class="container">
      <button class="kids-back" onclick="voltarMenu()">← Voltar</button>
      <div class="caca-container">
        <div class="caca-header">
          <h2>🔤 Caça-Palavras da Reciclagem</h2>
          <div class="caca-stats">
            <span>⏱️ <span id="cacaTimer">0s</span></span>
            <span>✅ <span id="cacaEncontradas">0</span>/8 palavras</span>
          </div>
        </div>
        <div class="caca-layout">
          <div class="caca-grid-wrap">
            <div class="caca-grid" id="cacaGrid"></div>
          </div>
          <div class="caca-lista">
            <h4>Encontre estas palavras:</h4>
            <ul id="cacaLista"></ul>
            <div id="cacaWin" style="display:none;" class="caca-win">
              <p>🎉 Você encontrou todas!</p>
              <button class="kids-btn sm" onclick="initCaca()">Jogar de novo!</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================================
       SEÇÃO: VERDADE OU MITO
  ============================================================ -->
  <section id="verdadeiroSection" class="kids-section" style="display:none;">
    <div class="container">
      <button class="kids-back" onclick="voltarMenu()">← Voltar</button>
      <div class="vm-container">
        <div class="vm-header">
          <h2>✅ Verdade ou Mito?</h2>
          <div class="vm-stats">
            <span>⭐ <span id="vmScore">0</span></span>
            <span>❓ <span id="vmNum">0</span>/10</span>
          </div>
        </div>
        <div id="vmArea"></div>
        <div id="vmResult" style="display:none;" class="vm-result"></div>
      </div>
    </div>
  </section>

  <!-- ============================================================
       SEÇÃO: MESTRE DAS LIXEIRAS
  ============================================================ -->
  <section id="lixeirasSection" class="kids-section" style="display:none;">
    <div class="container">
      <button class="kids-back" onclick="voltarMenu()">← Voltar</button>
      <div class="lixeiras-game-container">
        <h2>🗑️ Mestre das Lixeiras</h2>
        <p class="lx-sub">Clique no item e depois na lixeira certa. <strong id="lxAcertos">0</strong>/12 corretos</p>
        <div class="lx-game-wrap">
          <div class="lx-itens-area" id="lxItensArea"></div>
          <div class="lx-lixeiras-area" id="lxLixeirasArea"></div>
        </div>
        <div class="lx-feedback" id="lxFeedback"></div>
        <div id="lxWin" style="display:none;" class="lx-win">
          <h3>🏆 Mestre das Lixeiras!</h3>
          <p id="lxWinText"></p>
          <button class="kids-btn" onclick="initLixeiras()">Jogar de novo! 🔄</button>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================================
       SEÇÃO: FATOS INCRÍVEIS
  ============================================================ -->
  <section id="aprendaSection" class="kids-section" style="display:none;">
    <div class="container">
      <button class="kids-back" onclick="voltarMenu()">← Voltar</button>
      <div class="aprenda-container">
        <h2>📚 Fatos Incríveis sobre Reciclagem</h2>
        <div class="fatos-swiper" id="fatosSwiper">
          <div class="fato-card-big active" id="fatoCard">
            <!-- preenchido por JS -->
          </div>
          <div class="fato-nav">
            <button class="fato-btn" onclick="prevFato()">←</button>
            <span id="fatoIndicador">1 / 10</span>
            <button class="fato-btn" onclick="nextFato()">→</button>
          </div>
        </div>
        <div class="aprenda-btn-wrap">
          <button class="kids-btn" onclick="completarFatos()">✅ Li tudo! (+15 pts)</button>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================================
       SEÇÃO: RANKING KIDS
  ============================================================ -->
  <section id="rankingSection" class="kids-section" style="display:none;">
    <div class="container">
      <button class="kids-back" onclick="voltarMenu()">← Voltar</button>
      <div class="ranking-kids-container">
        <h2>🏆 Hall da Fama – Heróis da Reciclagem</h2>
        <div class="ranking-kids-top3" id="rankingTop3"></div>
        <div class="ranking-kids-table" id="rankingTable"></div>
        <div class="ranking-seu-lugar">
          <h3>Sua posição</h3>
          <div class="seu-card" id="seuCard"></div>
        </div>
        <div class="reciclagem-facts-ranking">
          <h3>♻️ Sabia que reciclar faz diferença?</h3>
          <div id="recyclingFactsRanking"></div>
        </div>
      </div>
    </div>
  </section>

  <!-- CONQUISTAS -->
  <section class="kids-conquistas" id="conquistasSection">
    <div class="container">
      <h2 class="kids-section-title">🏅 Suas Conquistas</h2>
      <div class="conquistas-grid" id="conquistasGrid">
        <div class="conquista-item locked" id="c-quiz">    <span class="conquista-icon">🧠</span><p>Mestre do Quiz</p></div>
        <div class="conquista-item locked" id="c-memoria"> <span class="conquista-icon">🃏</span><p>Rei da Memória</p></div>
        <div class="conquista-item locked" id="c-separar"> <span class="conquista-icon">🎯</span><p>Salva Cidades</p></div>
        <div class="conquista-item locked" id="c-palavras"><span class="conquista-icon">🔤</span><p>Detetive Verde</p></div>
        <div class="conquista-item locked" id="c-vm">      <span class="conquista-icon">✅</span><p>Caça Mitos</p></div>
        <div class="conquista-item locked" id="c-lixeira"> <span class="conquista-icon">🗑️</span><p>Mestre Lixeiras</p></div>
        <div class="conquista-item locked" id="c-100pts">  <span class="conquista-icon">⭐</span><p>100 Pontos!</p></div>
        <div class="conquista-item locked" id="c-500pts">  <span class="conquista-icon">💫</span><p>500 Pontos!</p></div>
        <div class="conquista-item locked" id="c-planeta">  <span class="conquista-icon">🌍</span><p>Planeta Verde!</p></div>
        <div class="conquista-item locked" id="c-streak">  <span class="conquista-icon">🔥</span><p>Sequência x5</p></div>
        <div class="conquista-item locked" id="c-todos">   <span class="conquista-icon">🌟</span><p>Jogou Tudo!</p></div>
        <div class="conquista-item locked" id="c-recico">  <span class="conquista-icon">🐸</span><p>Amigo do Recico</p></div>
      </div>
    </div>
  </section>

  <!-- TOAST -->
  <div id="kidsToast" class="kids-toast" style="display:none;"></div>

  <!-- LEVEL UP OVERLAY -->
  <div id="levelUpOverlay" class="levelup-overlay" style="display:none;">
    <div class="levelup-content">
      <div class="levelup-emoji" id="levelUpEmoji"></div>
      <h2>NÍVEL UP! 🎉</h2>
      <p id="levelUpText"></p>
    </div>
  </div>

  <footer style="background:#1a1a2e;color:#a0a0c0;text-align:center;padding:20px;">
    <p>🌍 ColetaFácil Kids – Aprendendo a cuidar do planeta! © 2026</p>
    <a href="index.php" style="color:#4ade80;font-size:.85rem;">← Voltar ao site principal</a>
  </footer>

  <script src="script.js"></script>
  <script src="kids-data.js"></script>
  <script src="kids.js"></script>
</body>
</html>
