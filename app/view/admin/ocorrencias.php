<?php require_once 'app/view/comuns/admin_cabecalho.php'; ?>

      <?php if (!empty($sucesso)): ?>
        <div class="dashboard-card" style="border-left:4px solid #22c55e;">
          <strong style="color:#15803d;">✅ Status atualizado com sucesso.</strong>
        </div>
      <?php endif; ?>

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
          <span class="total-label">Total: <strong><?= (int) ($total ?? 0) ?></strong></span>
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
            <?php
            $ocorrencias = $ocorrencias ?? [];
            $statusOpcoes = ['NOVA','EM_ANALISE','EM_ANDAMENTO','RESOLVIDA','ARQUIVADA'];
            ?>
            <?php foreach ($ocorrencias as $o): ?>
              <?php
              $tipoBusca   = strtolower(rotuloTipoOcorrencia($o['tipo']));
              $statusBusca = classeStatusOcorrencia($o['status']);
              ?>
              <tr data-status="<?= $statusBusca ?>" data-tipo="<?= htmlspecialchars($tipoBusca, ENT_QUOTES) ?>"
                  data-busca="<?= htmlspecialchars(strtolower($o['protocolo'] . ' ' . $o['bairro']), ENT_QUOTES) ?>">
                <td>#<?= htmlspecialchars($o['protocolo']) ?></td>
                <td><?= rotuloTipoOcorrencia($o['tipo']) ?></td>
                <td><?= htmlspecialchars($o['endereco_descricao']) ?></td>
                <td><?= htmlspecialchars($o['bairro']) ?></td>
                <td><?= date('d/m/Y', strtotime($o['criada_em'])) ?></td>
                <td>
                  <form method="POST" action="<?= baseUrl('admin/atualizarStatus') ?>" class="form-status-inline">
                    <input type="hidden" name="id_ocorrencia" value="<?= (int) $o['id_ocorrencia'] ?>">
                    <select class="status-select" name="status" onchange="this.form.submit()">
                      <?php foreach ($statusOpcoes as $op): ?>
                        <option value="<?= $op ?>" <?= $o['status'] === $op ? 'selected' : '' ?>><?= rotuloStatusOcorrencia($op) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </form>
                </td>
                <td>
                  <button class="btn-acao ver"
                    onclick="verOcorrencia(this)"
                    data-id="<?= (int) $o['id_ocorrencia'] ?>"
                    data-protocolo="<?= htmlspecialchars($o['protocolo'], ENT_QUOTES) ?>"
                    data-tipo="<?= htmlspecialchars(rotuloTipoOcorrencia($o['tipo']), ENT_QUOTES) ?>"
                    data-local="<?= htmlspecialchars($o['endereco_descricao'], ENT_QUOTES) ?>"
                    data-bairro="<?= htmlspecialchars($o['bairro'], ENT_QUOTES) ?>"
                    data-urgencia="<?= htmlspecialchars($o['urgencia'], ENT_QUOTES) ?>"
                    data-descricao="<?= htmlspecialchars($o['descricao'], ENT_QUOTES) ?>"
                    data-status="<?= htmlspecialchars($o['status'], ENT_QUOTES) ?>"
                    data-obs="<?= htmlspecialchars((string) $o['observacao_admin'], ENT_QUOTES) ?>"
                    data-fotos="<?= htmlspecialchars(json_encode(array_map(static fn($u) => baseUrl($u), $o['fotos'] ?? []), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), ENT_QUOTES) ?>">👁 Ver</button>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($ocorrencias)): ?>
              <tr><td colspan="7" style="text-align:center;padding:24px;color:#94a3b8;">Nenhuma ocorrência registrada.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

  <!-- MODAL DE DETALHES / GESTÃO -->
  <div id="modalOcorrencia" class="modal" style="display:none;">
    <div class="modal-content modal-large">
      <div class="modal-top">
        <h3>Detalhes da Ocorrência</h3>
        <button class="modal-close" onclick="fecharModalAdmin()">✕</button>
      </div>

      <!-- Dados do relato (somente leitura) -->
      <div id="modalOcBody"></div>

      <!-- Gestão: status + observação do administrador (editável, grava no banco) -->
      <form method="POST" action="<?= baseUrl('admin/atualizarStatus') ?>">
        <input type="hidden" name="id_ocorrencia" id="modalIdOc" value="">
        <div class="form-group">
          <label>Status</label>
          <select name="status" id="modalStatus">
            <?php foreach ($statusOpcoes as $op): ?>
              <option value="<?= $op ?>"><?= rotuloStatusOcorrencia($op) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Observação do Administrador</label>
          <textarea name="observacao" id="modalObs" rows="3" placeholder="Adicione uma observação sobre esta ocorrência..."></textarea>
        </div>
        <div class="modal-actions">
          <button type="submit" class="btn-primary">Salvar</button>
          <button type="button" class="btn-secondary" onclick="fecharModalAdmin()">Fechar</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Preenche o modal a partir dos data-* do botao (dados do banco).
    function verOcorrencia(btn) {
      var d = btn.dataset;
      var html =
        '<div class="sr-info-grid" style="display:grid;gap:10px;margin-bottom:16px;">' +
        '<p><strong>Protocolo:</strong> #' + d.protocolo + '</p>' +
        '<p><strong>Tipo:</strong> ' + d.tipo + '</p>' +
        '<p><strong>Local:</strong> ' + d.local + '</p>' +
        '<p><strong>Bairro:</strong> ' + d.bairro + '</p>' +
        '<p><strong>Urgência:</strong> ' + d.urgencia + '</p>' +
        '<p><strong>Descrição:</strong> ' + d.descricao + '</p>' +
        '</div>';

      // Fotos de evidência enviadas pelo cidadão (abrem em tamanho real em nova aba).
      var fotos = [];
      try { fotos = JSON.parse(d.fotos || '[]'); } catch (e) { fotos = []; }
      if (fotos.length) {
        html += '<div class="oc-fotos-admin"><strong>📷 Fotos da ocorrência</strong><div class="oc-fotos-grid">';
        html += fotos.map(function (url) {
          return '<a href="' + url + '" target="_blank" rel="noopener" title="Abrir em tamanho real">'
               + '<img src="' + url + '" alt="Foto da ocorrência" loading="lazy"></a>';
        }).join('');
        html += '</div></div>';
      } else {
        html += '<p style="color:#94a3b8;">📷 Nenhuma foto enviada para esta ocorrência.</p>';
      }

      document.getElementById('modalOcBody').innerHTML = html;

      // Pre-carrega o form de gestao com os valores atuais
      document.getElementById('modalIdOc').value  = d.id;
      document.getElementById('modalStatus').value = d.status;
      document.getElementById('modalObs').value    = d.obs || '';

      document.getElementById('modalOcorrencia').style.display = 'flex';
    }
    function fecharModalAdmin() {
      document.getElementById('modalOcorrencia').style.display = 'none';
    }
    // Filtro client-side sobre as linhas reais da tabela.
    function filtrarOcAdmin() {
      var busca  = (document.getElementById('buscaOc').value || '').toLowerCase();
      var status = document.getElementById('filtroStatus').value;
      var tipo   = document.getElementById('filtroTipo').value;
      document.querySelectorAll('#tabelaOcorrencias tbody tr').forEach(function (tr) {
        if (!tr.dataset.busca) return;
        var okBusca  = !busca  || tr.dataset.busca.includes(busca);
        var okStatus = !status || tr.dataset.status === status;
        var okTipo   = !tipo   || tr.dataset.tipo.includes(tipo);
        tr.style.display = (okBusca && okStatus && okTipo) ? '' : 'none';
      });
    }
  </script>

<?php require_once 'app/view/comuns/admin_rodape.php'; ?>
