<?php require_once 'app/view/comuns/admin_cabecalho.php'; ?>

      <?php if (!empty($msg)): ?>
        <div class="dashboard-card" style="border-left:4px solid #22c55e;"><strong style="color:#15803d;">✅ <?= htmlspecialchars($msg) ?></strong></div>
      <?php endif; ?>
      <?php if (!empty($erro)): ?>
        <div class="dashboard-card" style="border-left:4px solid #ef4444;"><strong style="color:#b91c1c;">⚠️ <?= htmlspecialchars($erro) ?></strong></div>
      <?php endif; ?>

      <div class="dashboard-card">
        <div class="filtros-admin">
          <input type="text" placeholder="Buscar por nome ou bairro..." id="buscaPontoAdmin" oninput="filtrarPontosAdmin()">
        </div>
      </div>

      <div class="dashboard-card">
        <table class="admin-table" id="tabelaPontosAdmin">
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
                  <button class="btn-acao editar"
                    onclick='editarPonto(<?= json_encode([
                        "id"       => (int) $p["id_ponto"],
                        "bairro"   => (int) $p["id_bairro"],
                        "nome"     => $p["nome"],
                        "endereco" => $p["endereco"],
                        "horario"  => $p["horario_funcionamento"],
                        "status"   => $p["status_funcionamento"],
                        "mats"     => array_map("intval", $p["materiais_ids"] ?? []),
                    ], JSON_SEGURO) ?>)'>✏️ Editar</button>
                  <form method="POST" action="<?= baseUrl('admin/excluirPonto') ?>" style="display:inline"
                        onsubmit="return confirm(<?= jsAttr('Excluir o ponto "' . $p['nome'] . '"?') ?>);">
                    <?= csrf_campo() ?>
                    <input type="hidden" name="id_ponto" value="<?= (int) $p['id_ponto'] ?>">
                    <button type="submit" class="btn-acao excluir">🗑️ Excluir</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <div class="admin-add-bar">
          <button class="btn-primary btn-add" onclick="abrirNovoPonto()">➕ Adicionar Ponto de Descarte</button>
        </div>
      </div>

  <!-- MODAL CADASTRAR/EDITAR PONTO -->
  <div id="modalPonto" class="modal" style="display:none;">
    <div class="modal-content">
      <div class="modal-top">
        <h3 id="modalPontoTitulo">Cadastrar Ponto de Descarte</h3>
        <button class="modal-close" onclick="fecharModalAdmin()">✕</button>
      </div>
      <form method="POST" action="<?= baseUrl('admin/salvarPonto') ?>">
        <?= csrf_campo() ?>
        <input type="hidden" name="id_ponto" id="pontoId" value="">
        <div class="form-group">
          <label>Nome do Local *</label>
          <input type="text" name="nome" id="pontoNome" placeholder="Ex: Ecoponto Centro" required>
        </div>
        <div class="form-group">
          <label>Endereço *</label>
          <input type="text" name="endereco" id="pontoEndereco" placeholder="Rua, número – Bairro" required>
        </div>
        <div class="form-row-2" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
          <div class="form-group">
            <label>Bairro *</label>
            <select name="id_bairro" id="pontoBairro" required>
              <option value="">Selecione...</option>
              <?php foreach (($listaBairros ?? []) as $b): ?>
                <option value="<?= (int) $b['id_bairro'] ?>"><?= htmlspecialchars($b['nome']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Status</label>
            <select name="status_funcionamento" id="pontoStatus">
              <option value="ABERTO">🟢 Aberto</option>
              <option value="FECHADO">🔴 Fechado</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label>Horário de Funcionamento</label>
          <input type="text" name="horario_funcionamento" id="pontoHorario" placeholder="Ex: Seg–Sex 08h–17h">
        </div>
        <div class="form-group">
          <label>Materiais Aceitos</label>
          <div class="checkbox-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:6px;">
            <?php foreach (($materiaisTodos ?? []) as $m): ?>
              <label style="display:flex;align-items:center;gap:6px;font-size:.85rem;">
                <input type="checkbox" name="materiais[]" value="<?= (int) $m['id_material'] ?>" class="chk-ponto-mat">
                <?= htmlspecialchars((string) ($m['icone'] ?? '')) ?> <?= htmlspecialchars($m['nome']) ?>
              </label>
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
    function marcarMateriais(ids) {
      var set = {};
      (ids || []).forEach(function (i) { set[i] = true; });
      document.querySelectorAll('.chk-ponto-mat').forEach(function (chk) {
        chk.checked = !!set[parseInt(chk.value, 10)];
      });
    }
    function abrirNovoPonto() {
      document.getElementById('modalPontoTitulo').textContent = 'Cadastrar Ponto de Descarte';
      document.getElementById('pontoId').value = '';
      document.getElementById('pontoNome').value = '';
      document.getElementById('pontoEndereco').value = '';
      document.getElementById('pontoBairro').value = '';
      document.getElementById('pontoStatus').value = 'ABERTO';
      document.getElementById('pontoHorario').value = '';
      marcarMateriais([]);
      document.getElementById('modalPonto').style.display = 'flex';
    }
    function editarPonto(d) {
      document.getElementById('modalPontoTitulo').textContent = 'Editar Ponto de Descarte';
      document.getElementById('pontoId').value = d.id;
      document.getElementById('pontoNome').value = d.nome || '';
      document.getElementById('pontoEndereco').value = d.endereco || '';
      document.getElementById('pontoBairro').value = d.bairro || '';
      document.getElementById('pontoStatus').value = d.status || 'ABERTO';
      document.getElementById('pontoHorario').value = d.horario || '';
      marcarMateriais(d.mats || []);
      document.getElementById('modalPonto').style.display = 'flex';
    }
    function filtrarPontosAdmin() {
      var busca = (document.getElementById('buscaPontoAdmin').value || '').toLowerCase();
      document.querySelectorAll('#tabelaPontosAdmin tbody tr').forEach(function (tr) {
        if (!tr.dataset.busca) return;
        tr.style.display = (!busca || tr.dataset.busca.includes(busca)) ? '' : 'none';
      });
    }
  </script>

<?php require_once 'app/view/comuns/admin_rodape.php'; ?>
