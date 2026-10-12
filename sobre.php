<?php require_once __DIR__ . '/auth.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <?= theme_head_script() ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quem somos – ColetaFácil</title>
  <link rel="stylesheet" href="style.css?v=31">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>

  <?= site_navbar('sobre') ?>

  <!-- PAGE HEADER -->
  <div class="page-header">
    <div class="container">
      <h1>🌱 Quem somos</h1>
      <p>Conheça a plataforma ColetaFácil: o que é, para que serve e quem está por trás do projeto.</p>
    </div>
  </div>

  <!-- O QUE É / VISÃO -->
  <section class="container sobre-section">
    <div class="sobre-intro">
      <span class="sobre-tag">A plataforma</span>
      <h2>O que é o ColetaFácil</h2>
      <p>O ColetaFácil é uma plataforma de conscientização ambiental e apoio à coleta seletiva em Muriaé-MG. Reunimos, num só lugar, as informações que o cidadão precisa para reciclar corretamente: quando a coleta passa, como preparar cada material, onde descartar resíduos especiais e como relatar problemas na cidade.</p>
      <p>Nossa visão é simples: tornar a reciclagem parte natural do dia a dia, protegendo o meio ambiente e as pessoas que trabalham na limpeza urbana.</p>
    </div>
  </section>

  <!-- PARA QUE SERVE -->
  <section class="container sobre-section">
    <div class="section-header" style="text-align:left; margin-bottom:28px;">
      <h2>Para que serve</h2>
      <p>Quatro frentes que se completam</p>
    </div>
    <div class="sobre-grid">
      <div class="sobre-card">
        <span class="sobre-ic">🗓️</span>
        <h3>Consulta de coleta</h3>
        <p>Descubra os dias, horários e materiais aceitos da coleta seletiva no seu bairro.</p>
      </div>
      <div class="sobre-card">
        <span class="sobre-ic">📦</span>
        <h3>Gestão de resíduos</h3>
        <p>Um guia prático de como separar e preparar cada tipo de material com segurança.</p>
      </div>
      <div class="sobre-card">
        <span class="sobre-ic">📍</span>
        <h3>Pontos de descarte</h3>
        <p>Locais para entregar pilhas, eletrônicos, óleo e outros resíduos especiais.</p>
      </div>
      <div class="sobre-card">
        <span class="sobre-ic">🚨</span>
        <h3>Conscientização</h3>
        <p>Relato de ocorrências e conteúdo educativo para engajar toda a comunidade.</p>
      </div>
    </div>
  </section>

  <!-- PARA QUEM -->
  <section class="container sobre-section">
    <div class="section-header" style="text-align:left; margin-bottom:28px;">
      <h2>Para quem é</h2>
      <p>Uma plataforma pensada para toda a cidade</p>
    </div>
    <div class="sobre-publico">
      <div class="sobre-publico-item">
        <span class="sp-ic">🧑‍🤝‍🧑</span>
        <div>
          <h4>Cidadãos</h4>
          <p>Quem quer reciclar certo, consultar a coleta e ajudar a manter a cidade limpa.</p>
        </div>
      </div>
      <div class="sobre-publico-item">
        <span class="sp-ic">🧒</span>
        <div>
          <h4>Escolas e crianças</h4>
          <p>A Área Kids ensina reciclagem de forma lúdica, com jogos e quizzes educativos.</p>
        </div>
      </div>
      <div class="sobre-publico-item">
        <span class="sp-ic">🧤</span>
        <div>
          <h4>Profissionais de limpeza</h4>
          <p>Orientações de descarte seguro que protegem quem recolhe os resíduos nas ruas.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- CRÉDITOS ACADÊMICOS -->
  <section class="container sobre-section">
    <div class="sobre-creditos">
      <span class="sobre-tag">Créditos acadêmicos</span>
      <h2>Projeto de Extensão Universitária III</h2>
      <p class="sobre-curso">Curso de Análise e Desenvolvimento de Sistemas (ADS)</p>
      <p class="sobre-orientador">Orientação: <strong>Prof. Aldecir Fonseca</strong></p>

      <div class="sobre-equipe">
        <div class="equipe-card">
          <span class="eq-avatar">👨‍💻</span>
          <strong>Heron Leal</strong>
          <small>(32) 9 8411-6731</small>
        </div>
        <div class="equipe-card">
          <span class="eq-avatar">👨‍💻</span>
          <strong>Nattan Silva</strong>
          <small>(32) 9 9917-3481</small>
        </div>
        <div class="equipe-card">
          <span class="eq-avatar">👨‍💻</span>
          <strong>Paulo Victor</strong>
          <small>(32) 9 8809-4464</small>
        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="footer">
    <div class="container footer-inner">
      <div class="footer-brand">
        <span class="logo" style="color:#4ade80; font-size:1.3rem;">♻️ ColetaFácil</span>
        <p>Promovendo a coleta seletiva e a conscientização ambiental na nossa comunidade.</p>
        <div class="footer-social">
          <a href="#" title="Instagram">📸</a>
          <a href="#" title="Facebook">📘</a>
          <a href="#" title="WhatsApp">💬</a>
        </div>
      </div>
      <div class="footer-links">
        <h4>Plataforma</h4>
        <ul>
          <li><a href="index.php">Início</a></li>
          <li><a href="materiais.php">Guia de Materiais</a></li>
          <li><a href="pontos.php">Pontos de Descarte</a></li>
          <li><a href="ocorrencias.php">Registrar Ocorrência</a></li>
          <li><a href="recompensas.php">Recompensas</a></li>
          <li><a href="kids.php">Área Kids</a></li>
        </ul>
      </div>
      <div class="footer-links">
        <h4>Projeto</h4>
        <ul>
          <li>Curso: ADS</li>
          <li>Extensão Universitária III</li>
          <li>Prof. Aldecir Fonseca</li>
          <li style="margin-top:8px; color:#94a3b8;">Heron Leal – (32) 9 8411-6731</li>
          <li style="color:#94a3b8;">Nattan Silva – (32) 9 9917-3481</li>
          <li style="color:#94a3b8;">Paulo Victor – (32) 9 8809-4464</li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© 2026 ColetaFácil – Projeto de Extensão Universitária III – ADS</p>
    </div>
  </footer>

  <script src="script.js?v=31"></script>
</body>
</html>
