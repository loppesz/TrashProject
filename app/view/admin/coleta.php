<?php require_once 'app/view/comuns/admin_cabecalho.php'; ?>

      <?php if (!empty($msg)): ?>
        <div class="dashboard-card" style="border-left:4px solid #22c55e;"><strong style="color:#15803d;">✅ <?= htmlspecialchars($msg) ?></strong></div>
      <?php endif; ?>
      <?php if (!empty($erro)): ?>
        <div class="dashboard-card" style="border-left:4px solid #ef4444;"><strong style="color:#b91c1c;">⚠️ <?= htmlspecialchars($erro) ?></strong></div>
      <?php endif; ?>

      <div class="dashboard-card">
        <div class="card-header">
          <h2>Horários de Coleta por Região</h2>
        </div>
        <table class="admin-table" id="tabelaColetaAdmin">
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
              $diasTxt = count($diasNome) <= 2 ? implode(' e ', $diasNome) : implode(', ', $diasNome);
              $matsCurto = array_map(static fn($m) => $m === 'Papel e Papelão' ? 'Papel' : $m, $b['materiais'] ?? []);
              ?>
              <tr data-busca="<?= htmlspecialchars(strtolower($b['nome']), ENT_QUOTES) ?>">
                <td><?= htmlspecialchars($b['nome']) ?></td>
                <td><?= htmlspecialchars($diasTxt) ?></td>
                <td><?= formataHora($b['inicio']) ?> – <?= formataHora($b['fim']) ?></td>
                <td><?= htmlspecialchars(implode(', ', $matsCurto)) ?></td>
                <td>
                  <button class="btn-acao editar"
                    onclick='editarColeta(<?= json_encode([
                        "id_bairro" => (int) $b["id_bairro"],
                        "nome"      => $b["nome"],
                        "dias"      => array_map("intval", $b["dias"] ?? []),
                        "inicio"    => substr((string) $b["inicio"], 0, 5),
                        "fim"       => substr((string) $b["fim"], 0, 5),
                        "equipe"    => $b["equipe"],
                        "mats"      => $b["materiais_ids"] ?? [],
                    ], JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>✏️ Editar</button>
                  <form method="POST" action="<?= baseUrl('admin/excluirColeta') ?>" style="display:inline"
                        onsubmit="return confirm('Remover a coleta de &quot;<?= htmlspecialchars($b['nome'], ENT_QUOTES) ?>&quot;?');">
                    <input type="hidden" name="id_bairro" value="<?= (int) $b['id_bairro'] ?>">
                    <button type="submit" class="btn-acao excluir">🗑️ Excluir</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <div class="admin-add-bar">
          <button class="btn-primary btn-add" onclick="abrirNovaColeta()">➕ Adicionar Horário de Coleta</button>
        </div>
      </div>

  <!-- MODAL CADASTRAR/EDITAR COLETA -->
  <div id="modalColeta" class="modal" style="display:none;">
    <div class="modal-content">
      <div class="modal-top">
        <h3 id="modalColetaTitulo">Cadastrar Horário de Coleta</h3>
        <button class="modal-close" onclick="fecharModalAdmin()">✕</button>
      </div>
      <form method="POST" action="<?= baseUrl('admin/salvarColeta') ?>">
        <div class="form-group">
          <label>Bairro / Região *</label>
          <select name="id_bairro" id="coletaBairro" required>
            <option value="">Selecione...</option>
            <?php foreach (($listaBairros ?? []) as $b): ?>
              <option value="<?= (int) $b['id_bairro'] ?>"><?= htmlspecialchars($b['nome']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Dias da Semana *</label>
          <div class="dias-check">
            <label><input type="checkbox" name="dias[]" value="2" class="chk-dia"> Segunda</label>
            <label><input type="checkbox" name="dias[]" value="3" class="chk-dia"> Terça</label>
            <label><input type="checkbox" name="dias[]" value="4" class="chk-dia"> Quarta</label>
            <label><input type="checkbox" name="dias[]" value="5" class="chk-dia"> Quinta</label>
            <label><input type="checkbox" name="dias[]" value="6" class="chk-dia"> Sexta</label>
            <label><input type="checkbox" name="dias[]" value="7" class="chk-dia"> Sábado</label>
            <label><input type="checkbox" name="dias[]" value="1" class="chk-dia"> Domingo</label>
          </div>
        </div>
        <div class="form-row-2" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
          <div class="form-group">
            <label>Horário Início</label>
            <input type="time" name="inicio" id="coletaHoraInicio" value="07:00">
          </div>
          <div class="form-group">
            <label>Horário Fim</label>
            <input type="time" name="fim" id="coletaHoraFim" value="12:00">
          </div>
        </div>
        <div class="form-group">
          <label>Equipe Responsável</label>
          <input type="text" name="equipe" id="coletaEquipe" placeholder="Ex: SAAE Muriaé">
        </div>
        <div class="form-group">
          <label>Materiais Recolhidos</label>
          <div class="dias-check">
            <?php foreach (($materiaisTodos ?? []) as $m): ?>
              <label><input type="checkbox" name="materiais[]" value="<?= (int) $m['id_material'] ?>" class="chk-coleta-mat"> <?= $m['icone'] ?? '' ?> <?= htmlspecialchars($m['nome']) ?></label>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="modal-actions">
          <button type="submit" class="btn-primary">Salvar</button>
          <button type="button" class="btn-secondary" onclick="fecharModalAdmin()">Cancelar</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function marcarChecks(classe, valores) {
      var set = {};
      (valores || []).forEach(function (v) { set[parseInt(v, 10)] = true; });
      document.querySelectorAll('.' + classe).forEach(function (chk) {
        chk.checked = !!set[parseInt(chk.value, 10)];
      });
    }
    function abrirNovaColeta() {
      document.getElementById('modalColetaTitulo').textContent = 'Cadastrar Horário de Coleta';
      document.getElementById('coletaBairro').value = '';
      document.getElementById('coletaHoraInicio').value = '07:00';
      document.getElementById('coletaHoraFim').value = '12:00';
      document.getElementById('coletaEquipe').value = '';
      marcarChecks('chk-dia', []);
      marcarChecks('chk-coleta-mat', []);
      document.getElementById('modalColeta').style.display = 'flex';
    }
    function editarColeta(d) {
      document.getElementById('modalColetaTitulo').textContent = 'Editar Horário de Coleta';
      document.getElementById('coletaBairro').value = d.id_bairro || '';
      document.getElementById('coletaHoraInicio').value = d.inicio || '07:00';
      document.getElementById('coletaHoraFim').value = d.fim || '12:00';
      document.getElementById('coletaEquipe').value = d.equipe || '';
      marcarChecks('chk-dia', d.dias || []);
      marcarChecks('chk-coleta-mat', d.mats || []);
      document.getElementById('modalColeta').style.display = 'flex';
    }
  </script>

<?php require_once 'app/view/comuns/admin_rodape.php'; ?>
