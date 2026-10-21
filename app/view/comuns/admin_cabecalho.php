<?php
/**
 * Cabecalho do painel administrativo: <head>, sidebar e topbar.
 *
 * Variaveis esperadas (injetadas pela view via BaseController::view):
 *  - $tituloPagina : string  Titulo da aba e da topbar.
 *  - $adminMenu    : string  Chave do item ativo na sidebar.
 */
$tituloPagina = $tituloPagina ?? 'Admin – ColetaFácil';
$adminMenu    = $adminMenu    ?? 'dashboard';

/** Itens da sidebar: chave => [rota, icone, rotulo]. */
$aSidebar = [
    'dashboard'   => ['admin',              'DB', 'Dashboard'],
    'ocorrencias' => ['admin/ocorrencias',  'OC', 'Ocorrências'],
    'materiais'   => ['admin/materiais',    'MT', 'Materiais'],
    'coleta'      => ['admin/coleta',       'CL', 'Coleta'],
    'pontos'      => ['admin/pontos',       'PD', 'Pontos de Descarte'],
    'sugestoes'   => ['admin/sugestoes',    'SG', 'Sugestões'],
    'indicadores' => ['admin/indicadores',  'IN', 'Indicadores'],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <?= themeHeadScript() ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($tituloPagina, ENT_QUOTES, 'UTF-8') ?></title>
  <script>window.BASEURL = <?= json_encode(baseUrl(), JSON_UNESCAPED_SLASHES) ?>;</script>
  <?php $assetV = defined('ASSET_VER') ? ASSET_VER : '1'; ?>
  <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>?v=<?= $assetV ?>">
  <link rel="stylesheet" href="<?= baseUrl('assets/css/admin.css') ?>?v=<?= $assetV ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="admin-body">

  <!-- SIDEBAR -->
  <aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-logo">
      <span>ColetaFácil</span>
      <small>Gestão municipal de Muriaé</small>
    </div>
    <nav class="sidebar-nav">
      <?php foreach ($aSidebar as $chave => [$rota, $icone, $rotulo]): ?>
      <a href="<?= baseUrl($rota) ?>" class="sidebar-link <?= $adminMenu === $chave ? 'active' : '' ?>" data-page="<?= $chave ?>">
        <span class="sidebar-icon"><?= $icone ?></span> <?= $rotulo ?>
      </a>
      <?php endforeach; ?>
      <div class="sidebar-divider"></div>
      <a href="<?= baseUrl() ?>" class="sidebar-link">
        <span class="sidebar-icon">←</span> Voltar ao site
      </a>
    </nav>
  </aside>

  <!-- CONTEÚDO PRINCIPAL -->
  <div class="admin-main">

    <!-- TOPBAR -->
    <header class="admin-topbar">
      <button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>
      <h1 class="topbar-title"><?= htmlspecialchars($tituloPagina, ENT_QUOTES, 'UTF-8') ?></h1>
      <div class="topbar-right">
        <button type="button" id="themeToggle" class="admin-theme-btn" onclick="toggleTheme()" aria-label="Alternar tema">🌙</button>
        <span class="admin-user"><?= htmlspecialchars(auth_user()['nome'] ?? 'Administrador', ENT_QUOTES, 'UTF-8') ?></span>
        <a href="<?= baseUrl('usuario/logout') ?>" class="admin-logout">Sair</a>
      </div>
    </header>

    <!-- CONTEÚDO -->
    <div class="admin-content">
