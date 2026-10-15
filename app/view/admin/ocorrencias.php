<?php require_once 'app/view/comuns/admin_cabecalho.php'; ?>

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

<?php require_once 'app/view/comuns/admin_rodape.php'; ?>
