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

/** Itens principais do menu público. */
$aMenu = [
    'home'         => ['',               'Início'],
    'coleta'       => ['coleta',         'Coleta'],
    'materiais'    => ['home/materiais', 'Materiais'],
    'pontos'       => ['ponto',          'Pontos'],
    'ocorrencias'  => ['ocorrencia',     'Ocorrências'],
    'sobre'        => ['home/sobre',      'Sobre'],
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
  <?php $geo = MURIAE_GEO; ?>
  <script>
    window.BASEURL = <?= json_encode(baseUrl(), JSON_UNESCAPED_SLASHES) ?>;
    // Limites do municipio de Muriae (fonte unica: app/config/Config.php).
    window.MURIAE_GEO = {
      center: <?= json_encode($geo['centro']) ?>,
      bounds: [[<?= $geo['lat_min'] ?>, <?= $geo['lng_min'] ?>], [<?= $geo['lat_max'] ?>, <?= $geo['lng_max'] ?>]]
    };
  </script>
  <link rel="manifest" href="<?= baseUrl('manifest.json') ?>">
  <meta name="theme-color" content="#16a34a">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="ColetaFácil">
  <?php $assetV = defined('ASSET_VER') ? ASSET_VER : '1'; ?>
  <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>?v=<?= $assetV ?>">
  <?php foreach ($cssPagina as $css): ?>
  <link rel="stylesheet" href="<?= baseUrl('assets/css/' . $css) ?>?v=<?= $assetV ?>">
  <?php endforeach; ?>
  <?php if (!empty($usaLeaflet)): ?>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
  <?php endif; ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>

  <!-- NAVEGAÇÃO PRINCIPAL -->
  <nav class="navbar" aria-label="Navegação principal">
    <div class="container nav-inner">
      <a href="<?= baseUrl() ?>" class="logo" aria-label="ColetaFácil - início">
        <span class="logo-icon" aria-hidden="true">♻️</span>
        <span class="logo-text">ColetaFácil</span>
      </a>

      <button type="button" class="menu-btn" onclick="toggleMenu()" aria-label="Abrir menu" aria-controls="siteNav">Menu</button>

      <div class="nav-main" id="siteNav">
        <?php foreach ($aMenu as $chave => [$rota, $rotulo]): ?>
          <a href="<?= baseUrl($rota) ?>" class="nav-main-link <?= $menu === $chave ? 'active' : '' ?>"><?= $rotulo ?></a>
        <?php endforeach; ?>
        <a href="<?= baseUrl('home/kids') ?>" class="nav-main-link nav-mobile-only <?= $menu === 'kids' ? 'active' : '' ?>">Área Kids</a>
        <a href="<?= baseUrl('home/recompensas') ?>" class="nav-main-link nav-mobile-only <?= $menu === 'recompensas' ? 'active' : '' ?>">Recompensas</a>
      </div>

      <div class="nav-actions">
        <a href="<?= baseUrl('home/kids') ?>" class="nav-utility <?= $menu === 'kids' ? 'active' : '' ?>">Área Kids</a>
        <a href="<?= baseUrl('home/recompensas') ?>" class="nav-utility nav-utility-accent <?= $menu === 'recompensas' ? 'active' : '' ?>">Recompensas</a>
        <button id="themeToggle" class="theme-toggle-btn" onclick="toggleTheme()" aria-label="Alternar tema">Tema</button>

        <div class="nav-dropdown" id="navUserDropdown">
          <button type="button" class="nav-user-trigger" onclick="toggleDropdown(event, 'navUserDropdown')" aria-haspopup="true" aria-expanded="false" aria-label="Abrir conta">Conta</button>
          <div class="nav-dd-panel nav-dd-panel-right">
            <?php if ($usuario): ?>
              <span class="nav-dd-user">Olá, <?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8') ?></span>
              <div class="nav-dd-sep"></div>
              <?php if ($usuario['tipo'] === 'ADMINISTRADOR'): ?>
                <a href="<?= baseUrl('admin') ?>">Painel administrativo</a>
              <?php endif; ?>
              <a href="<?= baseUrl('usuario/logout') ?>">Sair</a>
            <?php else: ?>
              <a href="<?= baseUrl('usuario/login') ?>">Entrar</a>
              <a href="<?= baseUrl('usuario/cadastro') ?>">Criar conta</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </nav>
