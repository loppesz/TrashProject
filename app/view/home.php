<?php require_once 'app/view/comuns/cabecalho.php'; ?>

  <!-- ================================================
       HERO SECTION
  ================================================ -->
  <section class="hero-v2">
    <div class="hero-bg-shapes">
      <div class="shape shape1"></div>
      <div class="shape shape2"></div>
      <div class="shape shape3"></div>
    </div>
    <div class="container hero-v2-inner">
      <div class="hero-v2-text">
        <div class="hero-badge animate-in">
          <span class="badge-dot"></span>
          🌱 Plataforma de Conscientização Ambiental
        </div>
        <h1 class="hero-v2-title animate-in delay-1">
          Cidade mais limpa<br>
          começa <span class="text-green">com você</span>
        </h1>
        <p class="hero-v2-sub animate-in delay-2">
          Descubra quando a coleta seletiva passa na sua rua, aprenda a descartar corretamente e ajude a proteger os coletores. Juntos fazemos a diferença.
        </p>
        <div class="hero-v2-actions animate-in delay-3">
          <form class="search-form-v2" onsubmit="irParaColeta(event)">
            <span class="search-icon">📍</span>
            <input type="text" id="enderecoInput" placeholder="Digite seu endereço em Muriaé-MG..." required>
            <button type="submit">Consultar coleta</button>
          </form>
        </div>
      </div>

      <!-- VIDEO PLACEHOLDER -->
      <div class="hero-video-wrap animate-in delay-2">
        <div class="video-container" id="videoContainer">
          <div class="video-thumb" onclick="playVideo()">
            <div class="video-overlay">
              <div class="play-btn">
                <span>▶</span>
              </div>
              <p>Assista: O que é a Coleta Seletiva?</p>
            </div>
            <div class="video-placeholder-bg">
              <span class="video-emoji">♻️</span>
            </div>
          </div>
          <!-- Troque o src pelo link do seu vídeo -->
          <video id="meuVideo" controls controlslist="nodownload" preload="metadata" playsinline style="display:none; width:100%; border-radius:16px;">
            <source src="<?= baseUrl('assets/img/video-apresentacao.mp4') ?>" type="video/mp4">
            Seu navegador não suporta vídeo HTML5.
          </video>
          <!-- Controles rápidos de avanço/retrocesso -->
          <div class="video-quick-controls" id="videoQuickControls" style="display:none;">
            <button type="button" onclick="seekVideo(-10)" aria-label="Voltar 10 segundos">⏪ 10s</button>
            <button type="button" onclick="togglePlayPause()" id="playPauseBtn" aria-label="Reproduzir ou pausar">⏸️ Pausar</button>
            <button type="button" onclick="seekVideo(10)" aria-label="Avançar 10 segundos">10s ⏩</button>
          </div>
        </div>
        <div class="video-caption">
          <span>🎬</span> Vídeo de apresentação — 2 min
        </div>
      </div>
    </div>
  </section>

  <!-- RESULTADO DA CONSULTA -->
  <div class="container" id="resultadoColeta" style="display:none;">
    <div class="resultado-card-v2">
      <div class="resultado-header">
        <span class="resultado-icon">📍</span>
        <div>
          <h2>Coleta para: <strong id="enderecoResultado"></strong></h2>
          <p>Informações encontradas para a sua região</p>
        </div>
      </div>
      <div class="coleta-info-v2">
        <div class="info-block">
          <span class="info-emoji">📅</span>
          <strong>Dias</strong>
          <p>Terça e Sexta-feira</p>
        </div>
        <div class="info-block">
          <span class="info-emoji">🕐</span>
          <strong>Horário</strong>
          <p>07h às 12h</p>
        </div>
        <div class="info-block">
          <span class="info-emoji">♻️</span>
          <strong>Materiais</strong>
          <p>Papel, Plástico, Metal, Vidro</p>
        </div>
        <div class="info-block">
          <span class="info-emoji">📞</span>
          <strong>Contato</strong>
          <p>(32) 3333-0000</p>
        </div>
      </div>
      <div class="alerta-v2">
        ⚠️ Coloque os materiais na calçada <strong>até às 06h30</strong> no dia da coleta, devidamente separados e ensacados.
      </div>
    </div>
  </div>

  <!-- ================================================
       FUNCIONALIDADES PRINCIPAIS
  ================================================ -->
  <section class="features-v2">
    <div class="container">
      <div class="section-header">
        <h2>Três passos para reciclar certo</h2>
        <p>Serviços essenciais para participar da coleta seletiva em Muriaé-MG</p>
      </div>
      <div class="features-v2-grid pilares-3">

        <a href="#consulta" class="feature-card-v2 green" onclick="document.getElementById('enderecoInput').focus()">
          <div class="fc-icon">🗓️</div>
          <h3>Quando passa a coleta?</h3>
          <p>Informe seu endereço e veja os dias e horários exatos para o seu bairro.</p>
          <div class="fc-chips">
            <span>8 bairros</span>
            <span>06h às 11h</span>
            <span>4 materiais</span>
          </div>
          <span class="fc-link">Consultar agora →</span>
        </a>

        <a href="<?= baseUrl('home/materiais') ?>" class="feature-card-v2 blue">
          <div class="fc-icon">📦</div>
          <h3>Guia de Materiais</h3>
          <p>Saiba o que reciclar, como preparar e quais materiais oferecem risco.</p>
          <div class="fc-chips">
            <span>8 guias</span>
            <span>Como preparar</span>
            <span>Riscos</span>
          </div>
          <span class="fc-link">Ver materiais →</span>
        </a>

        <a href="<?= baseUrl('ponto') ?>" class="feature-card-v2 orange">
          <div class="fc-icon">📍</div>
          <h3>Pontos de Descarte</h3>
          <p>Encontre o local mais próximo para descartar pilhas, eletrônicos e mais.</p>
          <div class="fc-chips">
            <span>6 pontos</span>
            <span>Pilhas e eletrônicos</span>
            <span>Busca por material</span>
          </div>
          <span class="fc-link">Ver pontos →</span>
        </a>

      </div>
    </div>
  </section>

  <!-- ================================================
       CONTADORES DE IMPACTO
  ================================================ -->
  <section class="impact-section">
    <div class="container">
      <div class="impact-grid">
        <div class="impact-item">
          <span class="impact-number" data-target="1240">0</span>
          <span class="impact-label">Ocorrências Resolvidas</span>
        </div>
        <div class="impact-item">
          <span class="impact-number" data-target="5830">0</span>
          <span class="impact-label">Consultas Realizadas</span>
        </div>
        <div class="impact-item">
          <span class="impact-number" data-target="342">0</span>
          <span class="impact-label">Heróis da Reciclagem</span>
        </div>
        <div class="impact-item">
          <span class="impact-number" data-target="18">0</span>
          <span class="impact-label">Bairros Atendidos</span>
        </div>
      </div>
    </div>
  </section>

  <!-- ================================================
       O IMPACTO REAL DO DESCARTE
  ================================================ -->
  <section class="impacto-real-section">
    <div class="container">
      <div class="section-header">
        <h2>O Impacto Real do Descarte</h2>
        <p>Dados que mostram por que a forma como descartamos faz diferença</p>
      </div>
      <div class="impacto-grid">

        <article class="impacto-card">
          <div class="impacto-img" style="background-image:url('<?= baseUrl('assets/img/poluicao-residuos.jpg') ?>')" role="img" aria-label="Resíduos plásticos acumulados na água"></div>
          <div class="impacto-body">
            <span class="impacto-dado">+80 mil ton/dia</span>
            <h3>O crescimento da poluição</h3>
            <p>O Brasil gera mais de 80 mil toneladas de lixo por dia e cerca de 40% ainda vai para destinos irregulares. Resíduos sem tratamento poluem rios, mares e o ar das cidades.</p>
          </div>
        </article>

        <article class="impacto-card">
          <div class="impacto-img" style="background-image:url('<?= baseUrl('assets/img/hero-coletor.jpg') ?>')" role="img" aria-label="Trabalhador da limpeza urbana"></div>
          <div class="impacto-body">
            <span class="impacto-dado">Risco diário</span>
            <h3>O risco humano dos coletores</h3>
            <p>Vidros, latas e materiais perfurocortantes mal embalados são a principal causa de cortes e perfurações nas mãos dos garis. Embalar corretamente protege quem recolhe o seu lixo.</p>
          </div>
        </article>

        <article class="impacto-card">
          <div class="impacto-img" style="background-image:url('<?= baseUrl('assets/img/caminhao-coleta.jpg') ?>')" role="img" aria-label="Caminhão de coleta de resíduos"></div>
          <div class="impacto-body">
            <span class="impacto-dado">Economia circular</span>
            <h3>A transformação positiva</h3>
            <p>Reciclar reduz a pressão sobre os aterros, economiza energia e matéria-prima e gera renda para cooperativas. Cada material separado corretamente volta ao ciclo produtivo.</p>
          </div>
        </article>

      </div>
    </div>
  </section>

  <!-- ================================================
       ÁREA KIDS + RANKING
  ================================================ -->
  <section class="kr2-section">
    <div class="container">
      <div class="section-header">
        <h2>Engaje, aprenda e seja recompensado</h2>
        <p>Diversão para as crianças e reconhecimento para quem recicla</p>
      </div>

      <div class="kr2-grid">

        <!-- CARD: ÁREA KIDS (estilo da página Kids) -->
        <a href="<?= baseUrl('home/kids') ?>" class="kr2-card kr2-kids">
          <div class="kr2-kids-mascot" aria-hidden="true">
            <div class="kkm-eyes"><span></span><span></span></div>
            <div class="kkm-smile">◡</div>
          </div>
          <h3>Área Kids</h3>
          <p>Jogos educativos, quizzes e desafios que ensinam as crianças a reciclar de um jeito divertido.</p>
          <div class="kr2-chips">
            <span>🎮 Jogos</span>
            <span>❓ Quiz</span>
            <span>🏅 Conquistas</span>
          </div>
          <span class="kr2-link kr2-link-light">Entrar na Área Kids →</span>
        </a>

        <!-- CARD: RANKING / RECOMPENSAS -->
        <div class="kr2-card kr2-reward">
          <div class="kr2-icon">🏆</div>
          <h3>Heróis da Reciclagem</h3>
          <p>Ganhe pontos reciclando e reportando. Veja quem lidera o ranking da cidade este mês.</p>
          <ul class="kr2-rank">
            <li><span class="kr2-pos pos-1">1º</span> <span class="kr2-name">Maria Silva</span> <span class="kr2-pts">2.450 pts</span></li>
            <li><span class="kr2-pos pos-2">2º</span> <span class="kr2-name">João Ferreira</span> <span class="kr2-pts">2.190 pts</span></li>
            <li><span class="kr2-pos pos-3">3º</span> <span class="kr2-name">Ana Costa</span> <span class="kr2-pts">1.870 pts</span></li>
          </ul>
          <a href="<?= baseUrl('home/recompensas') ?>" class="kr2-link kr2-link-reward">Ver ranking completo →</a>
        </div>

      </div>
    </div>
  </section>

<?php require_once 'app/view/comuns/rodape.php'; ?>
