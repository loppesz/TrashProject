<?php require_once __DIR__ . '/auth.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Página não encontrada – ColetaFácil</title>
  <meta name="description" content="Página não encontrada no ColetaFácil – Plataforma de coleta seletiva de Muriaé-MG">
  <link rel="manifest" href="manifest.json">
  <meta name="theme-color" content="#00c853">
  <script>(function(){var t=localStorage.getItem('theme')||(window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light');if(t==='dark')document.documentElement.classList.add('dark');}());</script>
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>
  <?= site_navbar('') ?>

  <div class="not-found-section">
    <div class="container">
      <div class="nf-card">
        <div class="nf-icon title-emoji">♻️</div>
        <div class="nf-code">404</div>
        <h1>Página não encontrada</h1>
        <p>O endereço que você digitou não existe ou foi movido.<br>Mas ainda dá para encontrar o que precisa aqui:</p>
        <div class="nf-links">
          <a href="index.php" class="nf-link green">🏠 Ir para o Início</a>
          <a href="coleta.php" class="nf-link">🗓️ Consultar Coleta</a>
          <a href="pontos.php" class="nf-link">📍 Pontos de Descarte</a>
          <a href="materiais.php" class="nf-link">📦 Guia de Materiais</a>
          <a href="ocorrencias.php" class="nf-link">🚨 Registrar Ocorrência</a>
          <a href="kids.php" class="nf-link purple">🧒 Área Kids</a>
        </div>
      </div>
    </div>
  </div>

  <style>
    .not-found-section { min-height: calc(100vh - 130px); display: flex; align-items: center; padding: 40px 0; background: var(--bg, #f9fafb); }
    .nf-card { max-width: 540px; margin: 0 auto; text-align: center; background: var(--surface, #fff); border-radius: 24px; padding: 56px 40px; border: 1px solid var(--border, #e5e7eb); box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
    .nf-icon { font-size: 5rem; display: block; margin-bottom: 8px; }
    .nf-code { font-size: 5rem; font-weight: 900; color: #00c853; line-height: 1; margin-bottom: 12px; }
    .nf-card h1 { font-size: 1.5rem; font-weight: 800; color: var(--text, #111827); margin-bottom: 10px; }
    .nf-card p { color: var(--text-muted, #6b7280); font-size: 0.92rem; line-height: 1.7; margin-bottom: 28px; }
    .nf-links { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .nf-link { background: #f1f5f9; color: #334155; padding: 12px 16px; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 0.85rem; transition: all 0.2s; display: block; border: 1.5px solid #e5e7eb; }
    .nf-link:hover { background: #dcfce7; border-color: #4ade80; color: #15803d; }
    .nf-link.green { background: linear-gradient(135deg,#00c853,#00e5cc); color: #06130c; border-color: transparent; grid-column: 1/-1; font-size: 0.95rem; }
    .nf-link.green:hover { background: #15803d; }
    .nf-link.purple { background: #f5f3ff; border-color: #c4b5fd; color: #7c3aed; }
    @media(max-width:480px){.nf-links{grid-template-columns:1fr}}
  </style>

  <footer class="footer">
    <div class="container footer-inner">
      <div class="footer-brand">
        <span class="logo" style="color:#4ade80;font-size:1.3rem;">♻️ ColetaFácil</span>
        <p>Plataforma de coleta seletiva de Muriaé-MG.</p>
      </div>
    </div>
    <div class="footer-bottom"><p>© 2026 ColetaFácil – Projeto de Extensão Universitária III – ADS</p></div>
  </footer>

  <script src="script.js"></script>
</body>
</html>
