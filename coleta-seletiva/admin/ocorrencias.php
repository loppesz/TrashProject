<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ocorrências – Admin ColetaFácil</title>
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
      <a href="index.php" class="sidebar-link" data-page="dashboard">
        <span class="sidebar-icon">📊</span> Dashboard
      </a>
      <a href="ocorrencias.php" class="sidebar-link active" data-page="ocorrencias">
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

  <div class="admin-main">
    <header class="admin-topbar">
      <button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>
      <h1 class="topbar-title">Ocorrências</h1>
      <div class="topbar-right">
        <span class="admin-user">👤 Administrador</span>
      </div>
    </header>

    <div class="admin-content">

      <!-- FILTROS -->
      <div class="dashboard-card">
        <div class="filtros-admin">
          <input type="text" placeholder="Buscar por bairro ou protocolo..." id="buscaOc" oninput="filtrarOcAdmin()">
          <select id="filtroStatus" onchange="filtrarOcAdmin()">
            <option value="">Todos os status</option>
            <option value="nova">Nova</option>
            <option value="analise">Em análise</option>
            <option value="andamento">Em atendimento</option>
            <option value="resolvida">Resolvida</option>
            <option value="arquivada">Arquivada</option>
          </select>
          <select id="filtroTipo" onchange="filtrarOcAdmin()">
            <option value="">Todos os tipos</option>
            <option value="descarte">Descarte irregular</option>
            <option value="coleta">Coleta não realizada</option>
            <option value="material">Material deixado</option>
            <option value="ponto">Ponto inadequado</option>
          </select>
        </div>
      </div>

      <!-- TABELA DE OCORRÊNCIAS -->
      <div class="dashboard-card">
        <div class="card-header">
          <h2>Lista de Ocorrências</h2>
          <span class="total-label">Total: <strong>24</strong></span>
        </div>
        <table class="admin-table" id="tabelaOcorrencias">
          <thead>
            <tr>
              <th>Protocolo</th>
              <th>Tipo</th>
              <th>Local</th>
              <th>Bairro</th>
              <th>Data</th>
              <th>Status</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>#2026001</td>
              <td>Descarte irregular</td>
              <td>Rua das Palmeiras, 50</td>
              <td>Centro</td>
              <td>20/08/2026</td>
              <td>
                <select class="status-select" onchange="atualizarStatus(this)">
                  <option>Nova</option>
                  <option>Em análise</option>
                  <option selected>Em atendimento</option>
                  <option>Resolvida</option>
                  <option>Arquivada</option>
                </select>
              </td>
              <td>
                <button class="btn-acao ver" onclick="verOcorrencia('2026001')">👁 Ver</button>
              </td>
            </tr>
            <tr>
              <td>#2026002</td>
              <td>Coleta não realizada</td>
              <td>Av. Brasil, 200</td>
              <td>Bairro Novo</td>
              <td>18/08/2026</td>
              <td>
                <select class="status-select" onchange="atualizarStatus(this)">
                  <option>Nova</option>
                  <option>Em análise</option>
                  <option>Em atendimento</option>
                  <option selected>Resolvida</option>
                  <option>Arquivada</option>
                </select>
              </td>
              <td>
                <button class="btn-acao ver" onclick="verOcorrencia('2026002')">👁 Ver</button>
              </td>
            </tr>
            <tr>
              <td>#2026003</td>
              <td>Descarte irregular</td>
              <td>Rua do Comércio, 15</td>
              <td>Vila Nova</td>
              <td>21/08/2026</td>
              <td>
                <select class="status-select" onchange="atualizarStatus(this)">
                  <option selected>Nova</option>
                  <option>Em análise</option>
                  <option>Em atendimento</option>
                  <option>Resolvida</option>
                  <option>Arquivada</option>
                </select>
              </td>
              <td>
                <button class="btn-acao ver" onclick="verOcorrencia('2026003')">👁 Ver</button>
              </td>
            </tr>
            <tr>
              <td>#2026004</td>
              <td>Material deixado</td>
              <td>Praça da Amizade</td>
              <td>Jardim América</td>
              <td>21/08/2026</td>
              <td>
                <select class="status-select" onchange="atualizarStatus(this)">
                  <option>Nova</option>
                  <option selected>Em análise</option>
                  <option>Em atendimento</option>
                  <option>Resolvida</option>
                  <option>Arquivada</option>
                </select>
              </td>
              <td>
                <button class="btn-acao ver" onclick="verOcorrencia('2026004')">👁 Ver</button>
              </td>
            </tr>
            <tr>
              <td>#2026005</td>
              <td>Ponto inadequado</td>
              <td>Rua da Saúde, 10</td>
              <td>Centro</td>
              <td>17/08/2026</td>
              <td>
                <select class="status-select" onchange="atualizarStatus(this)">
                  <option>Nova</option>
                  <option>Em análise</option>
                  <option>Em atendimento</option>
                  <option selected>Resolvida</option>
                  <option>Arquivada</option>
                </select>
              </td>
              <td>
                <button class="btn-acao ver" onclick="verOcorrencia('2026005')">👁 Ver</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </div>

  <!-- MODAL DE DETALHES -->
  <div id="modalOcorrencia" class="modal" style="display:none;">
    <div class="modal-content modal-large">
      <div class="modal-top">
        <h3>Detalhes da Ocorrência</h3>
        <button class="modal-close" onclick="fecharModalAdmin()">✕</button>
      </div>
      <div id="modalOcBody"></div>
      <div class="form-group">
        <label>Observação do Administrador</label>
        <textarea rows="3" placeholder="Adicione uma observação sobre esta ocorrência..."></textarea>
      </div>
      <div class="modal-actions">
        <button class="btn-primary" onclick="fecharModalAdmin()">Salvar</button>
        <button class="btn-secondary" onclick="fecharModalAdmin()">Fechar</button>
      </div>
    </div>
  </div>

  <script src="../script.js"></script>
  <script src="admin.js"></script>
</body>
</html>
