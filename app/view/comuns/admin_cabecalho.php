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
    'dashboard'   => ['admin',              '📊', 'Dashboard'],
    'ocorrencias' => ['admin/ocorrencias',  '🚨', 'Ocorrências'],
    'materiais'   => ['admin/materiais',    '📦', 'Materiais'],
    'coleta'      => ['admin/coleta',       '🗓️', 'Coleta'],
    'pontos'      => ['admin/pontos',       '📍', 'Pontos de Descarte'],
    'indicadores' => ['admin/indicadores',  '📈', 'Indicadores'],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <?= themeHeadScript() ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($tituloPagina, ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
  <link rel="stylesheet" href="<?= baseUrl('assets/css/admin.css') ?>">
</head>
<body class="admin-body">

  <!-- SIDEBAR -->
  <aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-logo">
      <span>🌿 ColetaFácil</span>
      <small>Painel Admin</small>
    </div>
    <nav class="sidebar-nav">
      <?php foreach ($aSidebar as $chave => [$rota, $icone, $rotulo]): ?>
      <a href="<?= baseUrl($rota) ?>" class="sidebar-link <?= $adminMenu === $chave ? 'active' : '' ?>" data-page="<?= $chave ?>">
        <span class="sidebar-icon"><?= $icone ?></span> <?= $rotulo ?>
      </a>
      <?php endforeach; ?>
      <div class="sidebar-divider"></div>
      <a href="<?= baseUrl() ?>" class="sidebar-link">
        <span class="sidebar-icon">🌐</span> Ver Site
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
        <span class="admin-user">👤 <?= htmlspecialchars(auth_user()['nome'] ?? 'Administrador', ENT_QUOTES, 'UTF-8') ?></span>
        <a href="<?= baseUrl('usuario/logout') ?>" class="admin-logout" style="margin-left:12px;">🚪 Sair</a>
      </div>
    </header>

    <!-- CONTEÚDO -->
    <div class="admin-content">
