<?php require_once 'app/view/comuns/admin_cabecalho.php'; ?>

      <div class="dashboard-card">
        <div class="card-header">
          <h2>Horários de Coleta por Região</h2>
        </div>
        <table class="admin-table">
          <thead>
            <tr>
              <th>Bairro / Região</th>
              <th>Dias da Semana</th>
              <th>Horário</th>
              <th>Materiais</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $bairros = $bairros ?? [];
            $nomesDias = [1 => 'Domingo', 2 => 'Segunda', 3 => 'Terça', 4 => 'Quarta', 5 => 'Quinta', 6 => 'Sexta', 7 => 'Sábado'];
            ?>
            <?php foreach ($bairros as $b): ?>
              <?php
              $diasNome = array_map(static fn($d) => $nomesDias[(int) $d] ?? '', $b['dias']);
              // junta com " e " quando 2, ", " quando mais
              $diasTxt = count($diasNome) <= 2 ? implode(' e ', $diasNome) : implode(', ', $diasNome);
              $matsCurto = array_map(static fn($m) => $m === 'Papel e Papelão' ? 'Papel' : $m, $b['materiais'] ?? []);
              ?>
              <tr data-busca="<?= htmlspecialchars(strtolower($b['nome']), ENT_QUOTES) ?>">
                <td><?= htmlspecialchars($b['nome']) ?></td>
                <td><?= htmlspecialchars($diasTxt) ?></td>
                <td><?= formataHora($b['inicio']) ?> – <?= formataHora($b['fim']) ?></td>
                <td><?= htmlspecialchars(implode(', ', $matsCurto)) ?></td>
                <td>
                  <button class="btn-acao editar" onclick="editarColeta('<?= htmlspecialchars($b['nome'], ENT_QUOTES) ?>')">✏️ Editar</button>
                  <button class="btn-acao excluir" onclick="excluirItem(this)">🗑️ Excluir</button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <div class="admin-add-bar">
          <button class="btn-primary btn-add" onclick="abrirModalColeta()">➕ Adicionar Horário de Coleta</button>
        </div>
      </div>

  <!-- MODAL CADASTRAR/EDITAR COLETA -->
  <div id="modalColeta" class="modal" style="display:none;">
    <div class="modal-content">
      <div class="modal-top">
        <h3 id="modalColetaTitulo">Cadastrar Horário de Coleta</h3>
        <button class="modal-close" onclick="fecharModalAdmin()">✕</button>
      </div>
      <div class="form-group">
        <label>Bairro / Região *</label>
        <input type="text" id="coletaBairro" placeholder="Ex: Centro">
      </div>
      <div class="form-group">
        <label>Dias da Semana *</label>
        <div class="dias-check">
          <label><input type="checkbox" value="seg"> Segunda</label>
          <label><input type="checkbox" value="ter"> Terça</label>
          <label><input type="checkbox" value="qua"> Quarta</label>
          <label><input type="checkbox" value="qui"> Quinta</label>
          <label><input type="checkbox" value="sex"> Sexta</label>
          <label><input type="checkbox" value="sab"> Sábado</label>
        </div>
      </div>
      <div class="form-group">
        <label>Horário Início</label>
        <input type="time" id="coletaHoraInicio" value="07:00">
      </div>
      <div class="form-group">
        <label>Horário Fim</label>
        <input type="time" id="coletaHoraFim" value="12:00">
      </div>
      <div class="form-group">
        <label>Materiais Recolhidos</label>
        <div class="dias-check">
          <label><input type="checkbox" value="papel" checked> Papel</label>
          <label><input type="checkbox" value="plastico" checked> Plástico</label>
          <label><input type="checkbox" value="metal" checked> Metal</label>
          <label><input type="checkbox" value="vidro"> Vidro</label>
        </div>
      </div>
      <div class="modal-actions">
        <button class="btn-primary" onclick="salvarColeta()">Salvar</button>
        <button class="btn-secondary" onclick="fecharModalAdmin()">Cancelar</button>
      </div>
    </div>
  </div>

<?php require_once 'app/view/comuns/admin_rodape.php'; ?>
