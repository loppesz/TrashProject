<?php require_once __DIR__ . '/auth.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Busca – ColetaFácil Muriaé</title>
  <meta name="description" content="Busque bairros, materiais e pontos de descarte em Muriaé-MG">
  <link rel="manifest" href="manifest.json">
  <meta name="theme-color" content="#16a34a">
  <script>(function(){var t=localStorage.getItem('theme')||(window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light');if(t==='dark')document.documentElement.classList.add('dark');}());</script>
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>
  <nav class="navbar">
    <div class="container nav-inner">
      <a href="index.php" class="logo"><span class="logo-icon">♻️</span><span>ColetaFácil</span></a>
      <ul class="nav-links">
        <li><a href="index.php">Início</a></li>
        <li><a href="coleta.php">🗓️ Coleta</a></li>
        <li><a href="materiais.php">Materiais</a></li>
        <li><a href="pontos.php">Pontos</a></li>
        <li><a href="ocorrencias.php">Ocorrências</a></li>
        <li><a href="kids.php" class="nav-kids">🧒 Kids</a></li>
        <li><a href="recompensas.php" class="nav-reward">⭐ Pontos</a></li>
        <?= site_auth_links() ?>
      </ul>
      <button id="themeToggle" class="theme-toggle-btn" onclick="toggleTheme()" aria-label="Alternar tema">🌙</button>
      <button class="menu-btn" onclick="toggleMenu()">☰</button>
    </div>
    <div class="mobile-menu" id="mobileMenu">
      <a href="index.php">🏠 Início</a>
      <a href="coleta.php">🗓️ Coleta</a>
      <a href="materiais.php">📦 Materiais</a>
      <a href="pontos.php">📍 Pontos</a>
      <a href="ocorrencias.php">🚨 Ocorrências</a>
      <?= site_auth_mobile_links() ?>
    </div>
  </nav>

  <div class="container busca-page">
    <div class="busca-page-header">
      <h1>🔍 Busca</h1>
      <form class="busca-global-form" onsubmit="buscarGlobal(event)">
        <div class="busca-global-wrap">
          <span>🔍</span>
          <input type="text" id="buscaGlobalInput" placeholder="Buscar bairros, materiais, pontos de descarte..." autofocus>
          <button type="submit">Buscar</button>
        </div>
      </form>
      <p id="buscaResumo" class="busca-resumo"></p>
    </div>

    <div id="buscaResultados">
      <!-- Seção: Coleta por bairro -->
      <div class="busca-section" id="secBairros" style="display:none;">
        <h2>🗓️ Coleta por Bairro</h2>
        <div class="busca-cards" id="resBairros"></div>
      </div>

      <!-- Seção: Materiais -->
      <div class="busca-section" id="secMateriais" style="display:none;">
        <h2>📦 Materiais</h2>
        <div class="busca-cards" id="resMateriais"></div>
      </div>

      <!-- Seção: Pontos de descarte -->
      <div class="busca-section" id="secPontos" style="display:none;">
        <h2>📍 Pontos de Descarte</h2>
        <div class="busca-cards" id="resPontos"></div>
      </div>

      <!-- Estado vazio -->
      <div id="buscaVazio" style="display:none;" class="busca-vazio">
        <span>🔍</span>
        <h3>Nenhum resultado encontrado</h3>
        <p>Tente buscar por: "papel", "Centro", "pilhas", "ecoponto"...</p>
        <a href="index.php" class="btn-outline-green">← Voltar ao início</a>
      </div>

      <!-- Estado inicial -->
      <div id="buscaInicial" class="busca-inicial">
        <div class="bi-grid">
          <a href="coleta.php" class="bi-card"><span>🗓️</span><p>Consultar Coleta</p></a>
          <a href="pontos.php" class="bi-card"><span>📍</span><p>Pontos de Descarte</p></a>
          <a href="materiais.php" class="bi-card"><span>📦</span><p>Guia de Materiais</p></a>
          <a href="ocorrencias.php" class="bi-card"><span>🚨</span><p>Registrar Ocorrência</p></a>
          <a href="recompensas.php" class="bi-card"><span>⭐</span><p>Recompensas</p></a>
          <a href="kids.php" class="bi-card purple"><span>🧒</span><p>Área Kids</p></a>
        </div>
        <div class="busca-sugestoes">
          <p>Sugestões de busca:</p>
          <div class="sug-chips">
            <button onclick="setQuery('papel')">Papel</button>
            <button onclick="setQuery('plástico')">Plástico</button>
            <button onclick="setQuery('pilhas')">Pilhas</button>
            <button onclick="setQuery('Centro')">Centro</button>
            <button onclick="setQuery('Padre Eustáquio')">Padre Eustáquio</button>
            <button onclick="setQuery('ecoponto')">Ecoponto</button>
            <button onclick="setQuery('medicamentos')">Medicamentos</button>
            <button onclick="setQuery('vidro')">Vidro</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <style>
    .busca-page { padding: 32px 20px 64px; min-height: calc(100vh - 130px); }
    .busca-page-header { margin-bottom: 32px; }
    .busca-page-header h1 { font-size: 1.6rem; font-weight: 900; margin-bottom: 16px; color: var(--text,#111827); }
    .busca-global-form { width: 100%; max-width: 640px; }
    .busca-global-wrap { display: flex; align-items: center; background: var(--surface,#fff); border: 2px solid var(--border,#e5e7eb); border-radius: 14px; padding: 0 16px; gap: 10px; transition: border-color .2s; }
    .busca-global-wrap:focus-within { border-color: #16a34a; }
    .busca-global-wrap input { flex: 1; border: none; outline: none; padding: 14px 0; font-size: 1rem; background: transparent; font-family: inherit; color: var(--text,#111827); }
    .busca-global-wrap button { background: #16a34a; color: #fff; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 700; cursor: pointer; font-family: inherit; transition: background .2s; }
    .busca-global-wrap button:hover { background: #15803d; }
    .busca-resumo { font-size: 0.85rem; color: var(--text-muted,#6b7280); margin-top: 10px; }
    .busca-section { margin-bottom: 32px; }
    .busca-section h2 { font-size: 1rem; font-weight: 800; color: var(--text,#111827); margin-bottom: 14px; padding-bottom: 8px; border-bottom: 2px solid var(--border,#e5e7eb); }
    .busca-cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 12px; }
    .busca-card { background: var(--surface,#fff); border: 1px solid var(--border,#e5e7eb); border-radius: 14px; padding: 16px; text-decoration: none; color: var(--text,#111827); transition: all .2s; display: block; }
    .busca-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.1); transform: translateY(-2px); border-color: #4ade80; }
    .busca-card .bc-title { font-weight: 800; font-size: 0.95rem; margin-bottom: 4px; display: flex; align-items: center; gap: 8px; }
    .busca-card .bc-sub { font-size: 0.78rem; color: var(--text-muted,#6b7280); margin-bottom: 8px; }
    .busca-card .bc-tag { background: #dcfce7; color: #15803d; font-size: 0.68rem; font-weight: 700; padding: 2px 8px; border-radius: 8px; }
    .busca-vazio { text-align: center; padding: 60px 20px; }
    .busca-vazio span { font-size: 3rem; display: block; margin-bottom: 12px; }
    .busca-vazio h3 { font-size: 1.1rem; font-weight: 800; margin-bottom: 8px; color: var(--text,#111827); }
    .busca-vazio p { color: var(--text-muted,#6b7280); margin-bottom: 20px; }
    .busca-inicial { padding: 8px 0; }
    .bi-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 12px; margin-bottom: 28px; max-width: 640px; }
    .bi-card { background: var(--surface,#fff); border: 1.5px solid var(--border,#e5e7eb); border-radius: 14px; padding: 20px 14px; text-align: center; text-decoration: none; color: var(--text,#111827); transition: all .2s; }
    .bi-card:hover { border-color: #4ade80; background: #f0fdf4; }
    .bi-card.purple:hover { border-color: #c4b5fd; background: #faf5ff; }
    .bi-card span { font-size: 2rem; display: block; margin-bottom: 8px; }
    .bi-card p { font-size: 0.82rem; font-weight: 700; }
    .busca-sugestoes p { font-size: 0.82rem; color: var(--text-muted,#6b7280); margin-bottom: 10px; }
    .sug-chips { display: flex; flex-wrap: wrap; gap: 8px; }
    .sug-chips button { background: var(--surface,#fff); border: 1.5px solid var(--border,#e5e7eb); color: var(--text-muted,#6b7280); padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all .2s; font-family: inherit; }
    .sug-chips button:hover { background: #dcfce7; border-color: #4ade80; color: #15803d; }
    @media(max-width:640px){.bi-grid{grid-template-columns:repeat(2,1fr)}}
  </style>

  <script src="script.js"></script>
  <script src="busca.js"></script>
</body>
</html>
