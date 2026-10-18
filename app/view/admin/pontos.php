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
            <?php foreach (($pontos ?? []) as $p): ?>
              <?php $aberto = $p['status_funcionamento'] === 'ABERTO'; ?>
              <tr data-busca="<?= htmlspecialchars(strtolower($p['nome'] . ' ' . $p['bairro']), ENT_QUOTES) ?>">
                <td><?= htmlspecialchars($p['nome']) ?></td>
                <td><?= htmlspecialchars($p['endereco']) ?></td>
                <td><?= htmlspecialchars(implode(', ', $p['materiais'] ?? [])) ?></td>
                <td><?= htmlspecialchars((string) $p['horario_funcionamento']) ?></td>
                <td>
                  <?php if ($aberto): ?>
                    <span class="status-badge resolvida">🟢 Aberto</span>
                  <?php else: ?>
                    <span class="status-badge nova">🔴 Fechado</span>
                  <?php endif; ?>
                </td>
                <td>
                  <button class="btn-acao editar" onclick="editarPonto('<?= htmlspecialchars($p['nome'], ENT_QUOTES) ?>')">✏️ Editar</button>
                  <button class="btn-acao excluir" onclick="excluirItem(this)">🗑️ Excluir</button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <div class="admin-add-bar">
          <button class="btn-primary btn-add" onclick="abrirModalPonto()">➕ Adicionar Ponto de Descarte</button>
        </div>
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
