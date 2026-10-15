<?php
/**
 * Cabecalho comum: abre o documento, monta o <head> e a navbar dinamica.
 *
 * Variaveis esperadas (opcionais, injetadas pela view via BaseController::view):
 *  - $tituloPagina : string  Titulo da aba (<title>).
 *  - $descricao    : string  Meta description.
 *  - $cssPagina    : array   Lista de CSS extras (caminhos relativos a assets/css).
 *  - $menu         : string  Chave da rota ativa para destacar no menu.
 */
$tituloPagina = $tituloPagina ?? 'ColetaFácil – Coleta Seletiva Muriaé-MG';
$descricao    = $descricao    ?? 'ColetaFácil – Plataforma de coleta seletiva de Muriaé-MG.';
$cssPagina    = $cssPagina    ?? [];
$menu         = $menu         ?? 'home';

/** Itens do menu (chave da rota => [rota, rotulo]). */
$aMenu = [
    'coleta'       => ['coleta',      '🗓️ Consultar Coleta'],
    'materiais'    => ['home/materiais',   'Materiais'],
    'pontos'       => ['ponto',       'Pontos'],
    'ocorrencias'  => ['ocorrencia',  'Ocorrências'],
    'sobre'        => ['home/sobre',       'Quem somos'],
];

$usuario = auth_user();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <?= themeHeadScript() ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= htmlspecialchars($descricao, ENT_QUOTES, 'UTF-8') ?>">
  <title><?= htmlspecialchars($tituloPagina, ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="manifest" href="<?= baseUrl('manifest.json') ?>">
  <meta name="theme-color" content="#16a34a">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="ColetaFácil">
  <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
  <?php foreach ($cssPagina as $css): ?>
  <link rel="stylesheet" href="<?= baseUrl('assets/css/' . $css) ?>">
  <?php endforeach; ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>

  <!-- NAVBAR -->
  <nav class="navbar">
    <div class="container nav-inner">
      <a href="<?= baseUrl() ?>" class="logo">
        <span class="logo-icon">♻️</span>
        <span class="logo-text">ColetaFácil</span>
      </a>

      <div class="nav-dropdown" id="navMenuDropdown">
        <button type="button" class="nav-dd-trigger <?= $menu === 'coleta' ? 'active' : '' ?>" onclick="toggleDropdown(event, 'navMenuDropdown')" aria-haspopup="true" aria-expanded="false">
          🗓️ Coleta <span class="nav-dd-caret">▾</span>
        </button>
        <div class="nav-dd-panel">
          <?php foreach ($aMenu as $chave => [$rota, $rotulo]): ?>
          <a href="<?= baseUrl($rota) ?>" class="<?= $menu === $chave ? 'active' : '' ?>"><?= $rotulo ?></a>
          <?php endforeach; ?>
        </div>
      </div>

      <a href="<?= baseUrl('home/kids') ?>" class="nav-pill nav-pill-kids <?= $menu === 'kids' ? 'active' : '' ?>">🧒 Kids</a>
      <a href="<?= baseUrl('home/recompensas') ?>" class="nav-pill nav-pill-reward <?= $menu === 'recompensas' ? 'active' : '' ?>">⭐ Recompensas</a>

      <button id="themeToggle" class="theme-toggle-btn" onclick="toggleTheme()" aria-label="Alternar tema">🌙</button>

      <div class="nav-dropdown" id="navUserDropdown">
        <button type="button" class="nav-user-trigger" onclick="toggleDropdown(event, 'navUserDropdown')" aria-haspopup="true" aria-expanded="false" aria-label="Conta">
          👤
        </button>
        <div class="nav-dd-panel nav-dd-panel-right">
          <?php if ($usuario): ?>
            <span class="nav-dd-user">Olá, <?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8') ?></span>
            <div class="nav-dd-sep"></div>
            <?php if ($usuario['tipo'] === 'ADMINISTRADOR'): ?>
              <a href="<?= baseUrl('admin') ?>">⚙️ Admin</a>
            <?php endif; ?>
            <a href="<?= baseUrl('usuario/logout') ?>">🚪 Sair</a>
          <?php else: ?>
            <a href="<?= baseUrl('usuario/login') ?>">🔑 Entrar</a>
            <a href="<?= baseUrl('usuario/cadastro') ?>">📝 Cadastrar</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </nav>
