<?php require_once 'app/view/comuns/admin_cabecalho.php'; ?>

      <?php if (!empty($msg)): ?>
        <div class="dashboard-card" style="border-left:4px solid #22c55e;"><strong style="color:#15803d;">✅ <?= htmlspecialchars($msg) ?></strong></div>
      <?php endif; ?>
      <?php if (!empty($erro)): ?>
        <div class="dashboard-card" style="border-left:4px solid #ef4444;"><strong style="color:#b91c1c;">⚠️ <?= htmlspecialchars($erro) ?></strong></div>
      <?php endif; ?>

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
            $riscoMap = [
                'BAIXO'   => ['analise',   'Baixo'],
                'MEDIO'   => ['andamento', 'Médio'],
                'ALTO'    => ['nova',      'Alto'],
                'CRITICO' => ['nova',      'Crítico'],
            ];
            $tipoMap = ['ACEITO' => 'aceito', 'CUIDADO' => 'cuidado', 'NAO_ACEITO' => 'nao-aceito'];
            ?>
            <?php foreach ($materiais as $m): ?>
              <?php
              $risco = $riscoMap[$m['nivel_risco']] ?? ['analise', $m['nivel_risco']];
              $aceito = (int) $m['aceito_coleta_seletiva'] === 1;
              $dataTipo = $tipoMap[$m['status_coleta']] ?? 'aceito';
              ?>
              <tr data-tipo="<?= $dataTipo ?>" data-busca="<?= htmlspecialchars(strtolower($m['nome']), ENT_QUOTES) ?>">
                <td><?= htmlspecialchars((string) $m['icone']) ?> <?= htmlspecialchars($m['nome']) ?></td>
                <td>
                  <?php if ($aceito): ?>
                    <span class="status-badge resolvida">✅ Sim</span>
                  <?php else: ?>
                    <span class="status-badge nova">❌ Não</span>
                  <?php endif; ?>
                </td>
                <td><span class="status-badge <?= $risco[0] ?>"><?= $risco[1] ?></span></td>
                <td>
                  <button class="btn-acao editar"
                    onclick='editarMaterial(<?= json_encode([
                        "id"     => (int) $m["id_material"],
                        "nome"   => $m["nome"],
                        "status" => $m["status_coleta"],
                        "risco"  => $m["nivel_risco"],
                        "icone"  => $m["icone"],
                        "cor"    => $m["cor_card"],
                        "label"  => $m["preparo_label"],
                        "desc"   => $m["descricao"],
                        "alerta" => $m["alerta_seguranca"],
                    ], JSON_SEGURO) ?>)'>✏️ Editar</button>
                  <form method="POST" action="<?= baseUrl('admin/excluirMaterial') ?>" style="display:inline"
                        onsubmit="return confirm(<?= jsAttr('Excluir o material "' . $m['nome'] . '"?') ?>);">
                    <?= csrf_campo() ?>
                    <input type="hidden" name="id_material" value="<?= (int) $m['id_material'] ?>">
                    <button type="submit" class="btn-acao excluir">🗑️ Excluir</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <div class="admin-add-bar">
          <button class="btn-primary btn-add" onclick="abrirNovoMaterial()">➕ Adicionar Material</button>
        </div>
      </div>

  <!-- MODAL CADASTRAR/EDITAR MATERIAL -->
  <div id="modalMaterial" class="modal" style="display:none;">
    <div class="modal-content">
      <div class="modal-top">
        <h3 id="modalMaterialTitulo">Cadastrar Material</h3>
        <button class="modal-close" onclick="fecharModalAdmin()">✕</button>
      </div>
      <form method="POST" action="<?= baseUrl('admin/salvarMaterial') ?>">
        <?= csrf_campo() ?>
        <input type="hidden" name="id_material" id="matId" value="">
        <div class="form-group">
          <label>Nome do Material *</label>
          <input type="text" name="nome" id="matNome" placeholder="Ex: Papel e Papelão" required>
        </div>
        <div class="form-row-2" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
          <div class="form-group">
            <label>Ícone (emoji)</label>
            <input type="text" name="icone" id="matIcone" maxlength="4" placeholder="📄">
          </div>
          <div class="form-group">
            <label>Cor do card</label>
            <select name="cor_card" id="matCor">
              <option value="azul">Azul</option>
              <option value="vermelho">Vermelho</option>
              <option value="amarelo">Amarelo</option>
              <option value="verde">Verde</option>
              <option value="cinza">Cinza</option>
              <option value="preto">Preto</option>
              <option value="laranja">Laranja</option>
              <option value="marrom">Marrom</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label>Status na Coleta Seletiva</label>
          <select name="status_coleta" id="matStatus">
            <option value="ACEITO">✅ Aceito</option>
            <option value="CUIDADO">⚠️ Cuidado especial</option>
            <option value="NAO_ACEITO">❌ Não aceito</option>
          </select>
        </div>
        <div class="form-group">
          <label>Nível de Risco para Coletores</label>
          <select name="nivel_risco" id="matRisco">
            <option value="BAIXO">Baixo</option>
            <option value="MEDIO">Médio</option>
            <option value="ALTO">Alto</option>
            <option value="CRITICO">Crítico</option>
          </select>
        </div>
        <div class="form-group">
          <label>Descrição</label>
          <textarea name="descricao" id="matDesc" rows="2" placeholder="Breve descrição do material..."></textarea>
        </div>
        <div class="form-group">
          <label>Rótulo da seção de preparo</label>
          <input type="text" name="preparo_label" id="matLabel" placeholder="Como preparar">
        </div>
        <div class="form-group">
          <label>Alerta de Segurança</label>
          <textarea name="alerta_seguranca" id="matAlerta" rows="2" placeholder="Alertas importantes para os profissionais da coleta..."></textarea>
        </div>
        <div class="modal-actions">
          <button type="submit" class="btn-primary">Salvar</button>
          <button type="button" class="btn-secondary" onclick="fecharModalAdmin()">Cancelar</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function abrirNovoMaterial() {
      document.getElementById('modalMaterialTitulo').textContent = 'Cadastrar Material';
      document.getElementById('matId').value = '';
      document.getElementById('matNome').value = '';
      document.getElementById('matIcone').value = '';
      document.getElementById('matCor').value = 'azul';
      document.getElementById('matStatus').value = 'ACEITO';
      document.getElementById('matRisco').value = 'BAIXO';
      document.getElementById('matDesc').value = '';
      document.getElementById('matLabel').value = 'Como preparar';
      document.getElementById('matAlerta').value = '';
      document.getElementById('modalMaterial').style.display = 'flex';
    }
    function editarMaterial(d) {
      document.getElementById('modalMaterialTitulo').textContent = 'Editar Material';
      document.getElementById('matId').value = d.id;
      document.getElementById('matNome').value = d.nome || '';
      document.getElementById('matIcone').value = d.icone || '';
      document.getElementById('matCor').value = d.cor || 'azul';
      document.getElementById('matStatus').value = d.status || 'ACEITO';
      document.getElementById('matRisco').value = d.risco || 'BAIXO';
      document.getElementById('matDesc').value = d.desc || '';
      document.getElementById('matLabel').value = d.label || 'Como preparar';
      document.getElementById('matAlerta').value = d.alerta || '';
      document.getElementById('modalMaterial').style.display = 'flex';
    }
    function filtrarMateriais() {
      var busca  = (document.getElementById('buscaMaterial').value || '').toLowerCase();
      var filtro = document.getElementById('filtroAceito').value;
      document.querySelectorAll('#tabelaMateriais tbody tr').forEach(function (tr) {
        if (!tr.dataset.busca) return;
        var okBusca = !busca || tr.dataset.busca.includes(busca);
        var okTipo  = !filtro || tr.dataset.tipo === filtro;
        tr.style.display = (okBusca && okTipo) ? '' : 'none';
      });
    }
  </script>

<?php require_once 'app/view/comuns/admin_rodape.php'; ?>
