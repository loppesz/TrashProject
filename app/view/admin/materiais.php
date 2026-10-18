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
            <?php
            $materiais = $materiais ?? [];
            // nivel_risco -> [classe de badge, rotulo]
            $riscoMap = [
                'BAIXO'   => ['analise',   'Baixo'],
                'MEDIO'   => ['andamento', 'Médio'],
                'ALTO'    => ['nova',      'Alto'],
                'CRITICO' => ['nova',      'Crítico'],
            ];
            // status_coleta -> data-tipo do filtro
            $tipoMap = ['ACEITO' => 'aceito', 'CUIDADO' => 'cuidado', 'NAO_ACEITO' => 'nao-aceito'];
            ?>
            <?php foreach ($materiais as $m): ?>
              <?php
              $risco = $riscoMap[$m['nivel_risco']] ?? ['analise', $m['nivel_risco']];
              $aceito = (int) $m['aceito_coleta_seletiva'] === 1;
              $dataTipo = $tipoMap[$m['status_coleta']] ?? 'aceito';
              ?>
              <tr data-tipo="<?= $dataTipo ?>" data-busca="<?= htmlspecialchars(strtolower($m['nome']), ENT_QUOTES) ?>">
                <td><?= $m['icone'] ?> <?= htmlspecialchars($m['nome']) ?></td>
                <td>
                  <?php if ($aceito): ?>
                    <span class="status-badge resolvida">✅ Sim</span>
                  <?php else: ?>
                    <span class="status-badge nova">❌ Não</span>
                  <?php endif; ?>
                </td>
                <td><span class="status-badge <?= $risco[0] ?>"><?= $risco[1] ?></span></td>
                <td>
                  <button class="btn-acao editar" onclick="editarMaterial('<?= htmlspecialchars($m['nome'], ENT_QUOTES) ?>')">✏️ Editar</button>
                  <button class="btn-acao excluir" onclick="excluirItem(this)">🗑️ Excluir</button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <div class="admin-add-bar">
          <button class="btn-primary btn-add" onclick="abrirModalMaterial()">➕ Adicionar Material</button>
        </div>
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
