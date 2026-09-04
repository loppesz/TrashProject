<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin – ColetaFácil</title>
  <link rel="stylesheet" href="../style.css">
  <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-body">

  <!-- SIDEBAR -->
  <aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-logo">
      <span>🌿 ColetaFácil</span>
      <small>Painel Admin</small>
    </div>
    <nav class="sidebar-nav">
      <a href="index.php" class="sidebar-link active" data-page="dashboard">
        <span class="sidebar-icon">📊</span> Dashboard
      </a>
      <a href="ocorrencias.php" class="sidebar-link" data-page="ocorrencias">
        <span class="sidebar-icon">🚨</span> Ocorrências
      </a>
      <a href="materiais.php" class="sidebar-link" data-page="materiais">
        <span class="sidebar-icon">📦</span> Materiais
      </a>
      <a href="coleta.php" class="sidebar-link" data-page="coleta">
        <span class="sidebar-icon">🗓️</span> Coleta
      </a>
      <a href="pontos.php" class="sidebar-link" data-page="pontos">
        <span class="sidebar-icon">📍</span> Pontos de Descarte
      </a>
      <a href="indicadores.php" class="sidebar-link" data-page="indicadores">
        <span class="sidebar-icon">📈</span> Indicadores
      </a>
      <div class="sidebar-divider"></div>
      <a href="../index.php" class="sidebar-link">
        <span class="sidebar-icon">🌐</span> Ver Site
      </a>
    </nav>
  </aside>

  <!-- CONTEÚDO PRINCIPAL -->
  <div class="admin-main">

    <!-- TOPBAR -->
    <header class="admin-topbar">
      <button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>
      <h1 class="topbar-title">Dashboard</h1>
      <div class="topbar-right">
        <span class="admin-user">👤 Administrador</span>
      </div>
    </header>

    <!-- CONTEÚDO -->
    <div class="admin-content">

      <!-- CARDS DE MÉTRICAS -->
      <div class="metrics-grid">
        <div class="metric-card">
          <div class="metric-icon laranja">🚨</div>
          <div class="metric-info">
            <span class="metric-value">24</span>
            <span class="metric-label">Ocorrências Registradas</span>
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
          <div class="metric-icon azul">📦</div>
          <div class="metric-info">
            <span class="metric-value">12</span>
            <span class="metric-label">Materiais Cadastrados</span>
          </div>
        </div>
        <div class="metric-card">
          <div class="metric-icon roxo">📍</div>
          <div class="metric-info">
            <span class="metric-value">6</span>
            <span class="metric-label">Pontos de Descarte</span>
          </div>
        </div>
      </div>

      <!-- LINHA: TABELA + GRÁFICO SIMPLES -->
      <div class="dashboard-row">

        <!-- OCORRÊNCIAS RECENTES -->
        <div class="dashboard-card">
          <div class="card-header">
            <h2>Ocorrências Recentes</h2>
            <a href="ocorrencias.php" class="ver-mais">Ver todas →</a>
          </div>
          <table class="admin-table">
            <thead>
              <tr>
                <th>Protocolo</th>
                <th>Tipo</th>
                <th>Bairro</th>
                <th>Status</th>
                <th>Data</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>#2026001</td>
                <td>Descarte irregular</td>
                <td>Centro</td>
                <td><span class="status-badge andamento">Em atendimento</span></td>
                <td>20/08/2026</td>
              </tr>
              <tr>
                <td>#2026002</td>
                <td>Coleta não realizada</td>
                <td>Bairro Novo</td>
                <td><span class="status-badge resolvida">Resolvida</span></td>
                <td>18/08/2026</td>
              </tr>
              <tr>
                <td>#2026003</td>
                <td>Descarte irregular</td>
                <td>Vila Nova</td>
                <td><span class="status-badge nova">Nova</span></td>
                <td>21/08/2026</td>
              </tr>
              <tr>
                <td>#2026004</td>
                <td>Material deixado</td>
                <td>Jardim América</td>
                <td><span class="status-badge analise">Em análise</span></td>
                <td>21/08/2026</td>
              </tr>
              <tr>
                <td>#2026005</td>
                <td>Ponto inadequado</td>
                <td>Centro</td>
                <td><span class="status-badge resolvida">Resolvida</span></td>
                <td>17/08/2026</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- RESUMO POR BAIRRO -->
        <div class="dashboard-card dashboard-card-small">
          <div class="card-header">
            <h2>Ocorrências por Bairro</h2>
          </div>
          <div class="bairro-list">
            <div class="bairro-item">
              <span class="bairro-nome">Centro</span>
              <div class="bairro-bar-wrap">
                <div class="bairro-bar" style="width: 80%"></div>
              </div>
              <span class="bairro-count">8</span>
            </div>
            <div class="bairro-item">
              <span class="bairro-nome">Vila Nova</span>
              <div class="bairro-bar-wrap">
                <div class="bairro-bar" style="width: 60%"></div>
              </div>
              <span class="bairro-count">6</span>
            </div>
            <div class="bairro-item">
              <span class="bairro-nome">Bairro Novo</span>
              <div class="bairro-bar-wrap">
                <div class="bairro-bar" style="width: 40%"></div>
              </div>
              <span class="bairro-count">4</span>
            </div>
            <div class="bairro-item">
              <span class="bairro-nome">Jd. América</span>
              <div class="bairro-bar-wrap">
                <div class="bairro-bar" style="width: 30%"></div>
              </div>
              <span class="bairro-count">3</span>
            </div>
            <div class="bairro-item">
              <span class="bairro-nome">Outros</span>
              <div class="bairro-bar-wrap">
                <div class="bairro-bar" style="width: 30%"></div>
              </div>
              <span class="bairro-count">3</span>
            </div>
          </div>

          <div class="card-header" style="margin-top: 24px;">
            <h2>Tipos de Ocorrência</h2>
          </div>
          <div class="tipo-list">
            <div class="tipo-item">
              <span>🗑️ Descarte irregular</span>
              <strong>12</strong>
            </div>
            <div class="tipo-item">
              <span>🚛 Coleta não realizada</span>
              <strong>6</strong>
            </div>
            <div class="tipo-item">
              <span>📦 Material deixado</span>
              <strong>4</strong>
            </div>
            <div class="tipo-item">
              <span>📍 Ponto inadequado</span>
              <strong>2</strong>
            </div>
          </div>
        </div>

      </div>

    </div>
  </div>

  <script src="../script.js"></script>
  <script src="admin.js"></script>
</body>
</html>
