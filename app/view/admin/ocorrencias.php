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
          <label class="toggle-ocultas" title="Ocorrências já triadas (com status alterado) ficam ocultas">
            <input type="checkbox" id="mostrarOcultas" onchange="filtrarOcAdmin()">
            👁 Mostrar ocultas (<span id="contadorOcultas">0</span>)
          </label>
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
              <th>Foto</th>
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
              <tr data-id="<?= (int) $o['id_ocorrencia'] ?>" data-status="<?= $statusBusca ?>" data-tipo="<?= htmlspecialchars($tipoBusca, ENT_QUOTES) ?>"
                  data-busca="<?= htmlspecialchars(strtolower($o['protocolo'] . ' ' . $o['bairro']), ENT_QUOTES) ?>">
                <td>#<?= htmlspecialchars($o['protocolo']) ?></td>
                <td class="td-foto">
                  <?php $ocFotos = $o['fotos'] ?? []; ?>
                  <?php if (!empty($ocFotos)): ?>
                    <button type="button" class="oc-thumb" onclick="verOcorrenciaDaLinha(this)" title="Ver foto(s) e autorizar">
                      <img src="<?= htmlspecialchars(baseUrl($ocFotos[0]), ENT_QUOTES) ?>" alt="Evidência da ocorrência" loading="lazy">
                      <?php if (count($ocFotos) > 1): ?>
                        <span class="oc-thumb-badge">+<?= count($ocFotos) - 1 ?></span>
                      <?php endif; ?>
                    </button>
                  <?php else: ?>
                    <span class="oc-thumb oc-thumb-vazio" title="Sem foto enviada">📷</span>
                  <?php endif; ?>
                </td>
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
                  <button type="button" class="btn-acao reexibir-btn" onclick="reexibirOc(this)" data-id="<?= (int) $o['id_ocorrencia'] ?>" title="Reexibir na lista">↩ Reexibir</button>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($ocorrencias)): ?>
              <tr><td colspan="8" style="text-align:center;padding:24px;color:#94a3b8;">Nenhuma ocorrência registrada.</td></tr>
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

  <!--
    As funções do modal e dos filtros desta página
    (verOcorrencia, verOcorrenciaDaLinha, fecharModalAdmin, filtrarOcAdmin)
    ficam em assets/js/admin.js — fonte única, para evitar definições duplicadas.
  -->

<?php require_once 'app/view/comuns/admin_rodape.php'; ?>
