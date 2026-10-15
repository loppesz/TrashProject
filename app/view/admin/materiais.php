<?php require_once 'app/view/comuns/admin_cabecalho.php'; ?>

      <div class="dashboard-card">
        <div class="filtros-admin">
          <input type="text" placeholder="Buscar material..." id="buscaMaterial" oninput="filtrarMateriais()">
          <select id="filtroAceito" onchange="filtrarMateriais()">
            <option value="">Todos</option>
            <option value="aceito">✅ Aceitos</option>
            <option value="nao-aceito">❌ Não Aceitos</option>
            <option value="cuidado">⚠️ Cuidado Especial</option>
          </select>
        </div>
      </div>

      <div class="dashboard-card">
        <table class="admin-table" id="tabelaMateriais">
          <thead>
            <tr>
              <th>Material</th>
              <th>Aceito na Coleta</th>
              <th>Risco para Coletor</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>📄 Papel e Papelão</td>
              <td><span class="status-badge resolvida">✅ Sim</span></td>
              <td><span class="status-badge analise">Baixo</span></td>
              <td>
                <button class="btn-acao editar" onclick="editarMaterial('Papel e Papelão')">✏️ Editar</button>
                <button class="btn-acao excluir" onclick="excluirItem(this)">🗑️ Excluir</button>
              </td>
            </tr>
            <tr>
              <td>🧴 Plástico</td>
              <td><span class="status-badge resolvida">✅ Sim</span></td>
              <td><span class="status-badge analise">Baixo</span></td>
              <td>
                <button class="btn-acao editar" onclick="editarMaterial('Plástico')">✏️ Editar</button>
                <button class="btn-acao excluir" onclick="excluirItem(this)">🗑️ Excluir</button>
              </td>
            </tr>
            <tr>
              <td>🥫 Metal</td>
              <td><span class="status-badge resolvida">✅ Sim</span></td>
              <td><span class="status-badge andamento">Médio</span></td>
              <td>
                <button class="btn-acao editar" onclick="editarMaterial('Metal')">✏️ Editar</button>
                <button class="btn-acao excluir" onclick="excluirItem(this)">🗑️ Excluir</button>
              </td>
            </tr>
            <tr>
              <td>🍶 Vidro</td>
              <td><span class="status-badge resolvida">✅ Sim</span></td>
              <td><span class="status-badge nova">Alto</span></td>
              <td>
                <button class="btn-acao editar" onclick="editarMaterial('Vidro')">✏️ Editar</button>
                <button class="btn-acao excluir" onclick="excluirItem(this)">🗑️ Excluir</button>
              </td>
            </tr>
            <tr>
              <td>🍌 Lixo Orgânico</td>
              <td><span class="status-badge nova">❌ Não</span></td>
              <td><span class="status-badge analise">Baixo</span></td>
              <td>
                <button class="btn-acao editar" onclick="editarMaterial('Lixo Orgânico')">✏️ Editar</button>
                <button class="btn-acao excluir" onclick="excluirItem(this)">🗑️ Excluir</button>
              </td>
            </tr>
            <tr>
              <td>💉 Perfurocortantes</td>
              <td><span class="status-badge nova">❌ Não</span></td>
              <td><span class="status-badge nova">Crítico</span></td>
              <td>
                <button class="btn-acao editar" onclick="editarMaterial('Perfurocortantes')">✏️ Editar</button>
                <button class="btn-acao excluir" onclick="excluirItem(this)">🗑️ Excluir</button>
              </td>
            </tr>
            <tr>
              <td>🔋 Pilhas e Baterias</td>
              <td><span class="status-badge nova">❌ Não</span></td>
              <td><span class="status-badge nova">Alto</span></td>
              <td>
                <button class="btn-acao editar" onclick="editarMaterial('Pilhas e Baterias')">✏️ Editar</button>
                <button class="btn-acao excluir" onclick="excluirItem(this)">🗑️ Excluir</button>
              </td>
            </tr>
            <tr>
              <td>💊 Medicamentos</td>
              <td><span class="status-badge nova">❌ Não</span></td>
              <td><span class="status-badge andamento">Médio</span></td>
              <td>
                <button class="btn-acao editar" onclick="editarMaterial('Medicamentos')">✏️ Editar</button>
                <button class="btn-acao excluir" onclick="excluirItem(this)">🗑️ Excluir</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

  <!-- MODAL CADASTRAR/EDITAR MATERIAL -->
  <div id="modalMaterial" class="modal" style="display:none;">
    <div class="modal-content">
      <div class="modal-top">
        <h3 id="modalMaterialTitulo">Cadastrar Material</h3>
        <button class="modal-close" onclick="fecharModalAdmin()">✕</button>
      </div>
      <div class="form-group">
        <label>Nome do Material *</label>
        <input type="text" id="matNome" placeholder="Ex: Papel e Papelão">
      </div>
      <div class="form-group">
        <label>Aceito na Coleta Seletiva?</label>
        <select id="matAceito">
          <option value="sim">✅ Sim</option>
          <option value="nao">❌ Não</option>
          <option value="cuidado">⚠️ Com cuidados especiais</option>
        </select>
      </div>
      <div class="form-group">
        <label>Nível de Risco para Coletores</label>
        <select id="matRisco">
          <option value="baixo">Baixo</option>
          <option value="medio">Médio</option>
          <option value="alto">Alto</option>
          <option value="critico">Crítico</option>
        </select>
      </div>
      <div class="form-group">
        <label>Orientações de Preparo</label>
        <textarea id="matOrientacoes" rows="3" placeholder="Como o usuário deve preparar o material..."></textarea>
      </div>
      <div class="form-group">
        <label>Alerta de Segurança</label>
        <textarea id="matAlerta" rows="2" placeholder="Alertas importantes para os profissionais da coleta..."></textarea>
      </div>
      <div class="modal-actions">
        <button class="btn-primary" onclick="salvarMaterial()">Salvar</button>
        <button class="btn-secondary" onclick="fecharModalAdmin()">Cancelar</button>
      </div>
    </div>
  </div>

<?php require_once 'app/view/comuns/admin_rodape.php'; ?>
