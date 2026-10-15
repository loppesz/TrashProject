<?php require_once 'app/view/comuns/admin_cabecalho.php'; ?>

      <div class="dashboard-card">
        <div class="filtros-admin">
          <input type="text" placeholder="Buscar por nome ou bairro..." id="buscaPontoAdmin">
        </div>
      </div>

      <div class="dashboard-card">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Nome</th>
              <th>Endereço</th>
              <th>Materiais Aceitos</th>
              <th>Horário</th>
              <th>Status</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Ecoponto Centro</td>
              <td>Rua das Flores, 123 – Centro</td>
              <td>Pilhas, Eletrônicos, Cartuchos</td>
              <td>Seg–Sex 08h–17h / Sáb 08h–12h</td>
              <td><span class="status-badge resolvida">🟢 Aberto</span></td>
              <td>
                <button class="btn-acao editar" onclick="editarPonto('Ecoponto Centro')">✏️ Editar</button>
                <button class="btn-acao excluir" onclick="excluirItem(this)">🗑️ Excluir</button>
              </td>
            </tr>
            <tr>
              <td>Farmácia Saúde Total</td>
              <td>Av. Brasil, 456 – Bairro Novo</td>
              <td>Medicamentos, Pilhas</td>
              <td>Todos os dias 07h–22h</td>
              <td><span class="status-badge resolvida">🟢 Aberto</span></td>
              <td>
                <button class="btn-acao editar" onclick="editarPonto('Farmácia Saúde Total')">✏️ Editar</button>
                <button class="btn-acao excluir" onclick="excluirItem(this)">🗑️ Excluir</button>
              </td>
            </tr>
            <tr>
              <td>Supermercado Boa Compra</td>
              <td>Rua Dom Pedro, 789 – Vila Nova</td>
              <td>Óleo de cozinha, Pilhas</td>
              <td>Seg–Sáb 07h–21h</td>
              <td><span class="status-badge resolvida">🟢 Aberto</span></td>
              <td>
                <button class="btn-acao editar" onclick="editarPonto('Supermercado')">✏️ Editar</button>
                <button class="btn-acao excluir" onclick="excluirItem(this)">🗑️ Excluir</button>
              </td>
            </tr>
            <tr>
              <td>Ponto Recicle – Jd. América</td>
              <td>Praça da Amizade – Jardim América</td>
              <td>Papel, Plástico, Metal, Vidro</td>
              <td>Ter, Qui, Sáb 08h–14h</td>
              <td><span class="status-badge nova">🔴 Fechado</span></td>
              <td>
                <button class="btn-acao editar" onclick="editarPonto('Ponto Recicle')">✏️ Editar</button>
                <button class="btn-acao excluir" onclick="excluirItem(this)">🗑️ Excluir</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

  <!-- MODAL CADASTRAR/EDITAR PONTO -->
  <div id="modalPonto" class="modal" style="display:none;">
    <div class="modal-content">
      <div class="modal-top">
        <h3 id="modalPontoTitulo">Cadastrar Ponto de Descarte</h3>
        <button class="modal-close" onclick="fecharModalAdmin()">✕</button>
      </div>
      <div class="form-group">
        <label>Nome do Local *</label>
        <input type="text" id="pontoNome" placeholder="Ex: Ecoponto Centro">
      </div>
      <div class="form-group">
        <label>Endereço *</label>
        <input type="text" id="pontoEndereco" placeholder="Rua, número – Bairro">
      </div>
      <div class="form-group">
        <label>Horário de Funcionamento</label>
        <input type="text" id="pontoHorario" placeholder="Ex: Seg–Sex 08h–17h">
      </div>
      <div class="form-group">
        <label>Materiais Aceitos</label>
        <input type="text" id="pontoMateriais" placeholder="Ex: Pilhas, Eletrônicos, Óleo">
      </div>
      <div class="form-group">
        <label>Status</label>
        <select id="pontoStatus">
          <option value="aberto">🟢 Aberto</option>
          <option value="fechado">🔴 Fechado</option>
        </select>
      </div>
      <div class="modal-actions">
        <button class="btn-primary" onclick="salvarPonto()">Salvar</button>
        <button class="btn-secondary" onclick="fecharModalAdmin()">Cancelar</button>
      </div>
    </div>
  </div>

<?php require_once 'app/view/comuns/admin_rodape.php'; ?>
