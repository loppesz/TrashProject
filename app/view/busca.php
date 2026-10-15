<?php require_once 'app/view/comuns/cabecalho.php'; ?>

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
        <a href="<?= baseUrl() ?>" class="btn-outline-green">← Voltar ao início</a>
      </div>

      <!-- Estado inicial -->
      <div id="buscaInicial" class="busca-inicial">
        <div class="bi-grid">
          <a href="<?= baseUrl('coleta') ?>" class="bi-card"><span>🗓️</span><p>Consultar Coleta</p></a>
          <a href="<?= baseUrl('ponto') ?>" class="bi-card"><span>📍</span><p>Pontos de Descarte</p></a>
          <a href="<?= baseUrl('home/materiais') ?>" class="bi-card"><span>📦</span><p>Guia de Materiais</p></a>
          <a href="<?= baseUrl('ocorrencia') ?>" class="bi-card"><span>🚨</span><p>Registrar Ocorrência</p></a>
          <a href="<?= baseUrl('home/recompensas') ?>" class="bi-card"><span>⭐</span><p>Recompensas</p></a>
          <a href="<?= baseUrl('home/kids') ?>" class="bi-card purple"><span>🧒</span><p>Área Kids</p></a>
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

<?php require_once 'app/view/comuns/rodape.php'; ?>
