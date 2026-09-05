<?php require_once __DIR__ . '/../auth.php'; require_auth(true); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Indicadores – Admin ColetaFácil</title>
  <link rel="stylesheet" href="../style.css">
  <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-body">

  <aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-logo">
      <span>🌿 ColetaFácil</span>
      <small>Painel Admin</small>
    </div>
    <nav class="sidebar-nav">
      <a href="index.php" class="sidebar-link"><span class="sidebar-icon">📊</span> Dashboard</a>
      <a href="ocorrencias.php" class="sidebar-link"><span class="sidebar-icon">🚨</span> Ocorrências</a>
      <a href="materiais.php" class="sidebar-link"><span class="sidebar-icon">📦</span> Materiais</a>
      <a href="coleta.php" class="sidebar-link"><span class="sidebar-icon">🗓️</span> Coleta</a>
      <a href="pontos.php" class="sidebar-link"><span class="sidebar-icon">📍</span> Pontos de Descarte</a>
      <a href="indicadores.php" class="sidebar-link active"><span class="sidebar-icon">📈</span> Indicadores</a>
      <div class="sidebar-divider"></div>
      <a href="../index.php" class="sidebar-link"><span class="sidebar-icon">🌐</span> Ver Site</a>
    </nav>
  </aside>

  <div class="admin-main">
    <header class="admin-topbar">
      <button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>
      <h1 class="topbar-title">Indicadores</h1>
    </header>

    <div class="admin-content">

      <!-- MÉTRICAS RÁPIDAS -->
      <div class="metrics-grid">
        <div class="metric-card">
          <div class="metric-icon laranja">🚨</div>
          <div class="metric-info">
            <span class="metric-value">24</span>
            <span class="metric-label">Total de Ocorrências</span>
          </div>
        </div>
        <div class="metric-card">
          <div class="metric-icon verde">✅</div>
          <div class="metric-info">
            <span class="metric-value">18</span>
            <span class="metric-label">Ocorrências Resolvidas</span>
          </div>
        </div>
        <div class="metric-card">
          <div class="metric-icon azul">⏳</div>
          <div class="metric-info">
            <span class="metric-value">6</span>
            <span class="metric-label">Em Andamento</span>
          </div>
        </div>
        <div class="metric-card">
          <div class="metric-icon roxo">📊</div>
          <div class="metric-info">
            <span class="metric-value">75%</span>
            <span class="metric-label">Taxa de Resolução</span>
          </div>
        </div>
      </div>

      <div class="dashboard-row">

        <!-- BAIRROS COM MAIS OCORRÊNCIAS -->
        <div class="dashboard-card">
          <div class="card-header">
            <h2>📍 Bairros com Mais Ocorrências</h2>
          </div>
          <div class="bairro-list">
            <div class="bairro-item">
              <span class="bairro-nome">Centro</span>
              <div class="bairro-bar-wrap">
                <div class="bairro-bar" style="width: 80%; background: #ef4444;"></div>
              </div>
              <span class="bairro-count">8</span>
            </div>
            <div class="bairro-item">
              <span class="bairro-nome">Vila Nova</span>
              <div class="bairro-bar-wrap">
                <div class="bairro-bar" style="width: 60%; background: #f97316;"></div>
              </div>
              <span class="bairro-count">6</span>
            </div>
            <div class="bairro-item">
              <span class="bairro-nome">Bairro Novo</span>
              <div class="bairro-bar-wrap">
                <div class="bairro-bar" style="width: 40%; background: #eab308;"></div>
              </div>
              <span class="bairro-count">4</span>
            </div>
            <div class="bairro-item">
              <span class="bairro-nome">Jardim América</span>
              <div class="bairro-bar-wrap">
                <div class="bairro-bar" style="width: 30%; background: #22c55e;"></div>
              </div>
              <span class="bairro-count">3</span>
            </div>
            <div class="bairro-item">
              <span class="bairro-nome">Bairro Escolar</span>
              <div class="bairro-bar-wrap">
                <div class="bairro-bar" style="width: 30%; background: #22c55e;"></div>
              </div>
              <span class="bairro-count">3</span>
            </div>
          </div>
        </div>

        <!-- TIPOS DE OCORRÊNCIA -->
        <div class="dashboard-card dashboard-card-small">
          <div class="card-header">
            <h2>🗂️ Tipos Mais Frequentes</h2>
          </div>
          <div class="tipo-list">
            <div class="tipo-item">
              <span>🗑️ Descarte irregular</span>
              <strong>12 (50%)</strong>
            </div>
            <div class="tipo-item">
              <span>🚛 Coleta não realizada</span>
              <strong>6 (25%)</strong>
            </div>
            <div class="tipo-item">
              <span>📦 Material deixado</span>
              <strong>4 (17%)</strong>
            </div>
            <div class="tipo-item">
              <span>📍 Ponto inadequado</span>
              <strong>2 (8%)</strong>
            </div>
          </div>

          <div class="card-header" style="margin-top:24px;">
            <h2>❓ Materiais que Geram Mais Dúvidas</h2>
          </div>
          <div class="tipo-list">
            <div class="tipo-item">
              <span>🍶 Vidro</span>
              <strong>34 consultas</strong>
            </div>
            <div class="tipo-item">
              <span>🔋 Pilhas</span>
              <strong>28 consultas</strong>
            </div>
            <div class="tipo-item">
              <span>💊 Medicamentos</span>
              <strong>21 consultas</strong>
            </div>
            <div class="tipo-item">
              <span>🫙 Óleo de cozinha</span>
              <strong>18 consultas</strong>
            </div>
          </div>
        </div>

      </div>

      <!-- TABELA DE LOCAIS COM DESCARTE IRREGULAR -->
      <div class="dashboard-card">
        <div class="card-header">
          <h2>🗑️ Locais com Mais Relatos de Descarte Irregular</h2>
        </div>
        <table class="admin-table">
          <thead>
            <tr>
              <th>Local</th>
              <th>Bairro</th>
              <th>Quantidade de Relatos</th>
              <th>Última Ocorrência</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Rua das Palmeiras</td>
              <td>Centro</td>
              <td><strong>5</strong></td>
              <td>20/08/2026</td>
            </tr>
            <tr>
              <td>Av. Brasil (próx. ao mercado)</td>
              <td>Bairro Novo</td>
              <td><strong>3</strong></td>
              <td>19/08/2026</td>
            </tr>
            <tr>
              <td>Rua do Comércio</td>
              <td>Vila Nova</td>
              <td><strong>2</strong></td>
              <td>21/08/2026</td>
            </tr>
            <tr>
              <td>Praça da Amizade</td>
              <td>Jardim América</td>
              <td><strong>2</strong></td>
              <td>18/08/2026</td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </div>

  <script src="../script.js"></script>
  <script src="admin.js"></script>
</body>
</html>
