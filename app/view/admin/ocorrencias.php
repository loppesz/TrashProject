<?php require_once 'app/view/comuns/admin_cabecalho.php'; ?>
<?php
  // Abas pelo status salvo no banco (persistem ao recarregar a página).
  $ocorrencias  = $ocorrencias ?? [];
  $statusOpcoes = ['NOVA','EM_ANALISE','EM_ANDAMENTO','RESOLVIDA','ARQUIVADA'];
  $abas = ['abertas' => 'Em aberto', 'resolvidas' => 'Resolvidas', 'arquivadas' => 'Arquivadas'];
  $abaAtual   = isset($abas[$abaAtual ?? '']) ? $abaAtual : 'abertas';
  $movidaPara = isset($abas[$movidaPara ?? '']) ? $movidaPara : '';

  $contAbas = ['abertas' => 0, 'resolvidas' => 0, 'arquivadas' => 0];
  $protAlterada = '';
  foreach ($ocorrencias as $o) {
      $contAbas[abaAdminOcorrencia($o['status'])]++;
      if ((int) $o['id_ocorrencia'] === (int) ($ocAlterada ?? 0)) {
          $protAlterada = $o['protocolo'];
      }
  }
?>

      <?php if (!empty($sucesso)): ?>
        <div class="dashboard-card" style="border-left:4px solid #22c55e;" role="status">
          <strong style="color:var(--success, #15803d);">✅ Status atualizado.</strong>
          <?php if ($movidaPara !== '' && $movidaPara !== $abaAtual && $protAlterada !== ''): ?>
            <span style="color:var(--text-soft);">
              A ocorrência #<?= htmlspecialchars($protAlterada) ?> foi para
              <button type="button" class="link-aba" onclick="trocarAbaOc('<?= $movidaPara ?>')"><?= $abas[$movidaPara] ?></button>.
            </span>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <!-- ABAS + FILTROS -->
      <div class="dashboard-card">
        <div class="oc-abas" role="tablist" aria-label="Situação das ocorrências">
          <?php foreach ($abas as $chave => $rotulo): ?>
            <button type="button" role="tab" class="oc-aba <?= $chave === $abaAtual ? 'active' : '' ?>"
                    data-aba="<?= $chave ?>" aria-selected="<?= $chave === $abaAtual ? 'true' : 'false' ?>"
                    aria-controls="tabelaOcorrencias" onclick="trocarAbaOc('<?= $chave ?>')">
              <?= $rotulo ?> <span class="oc-aba-num"><?= $contAbas[$chave] ?></span>
            </button>
          <?php endforeach; ?>
        </div>
        <div class="filtros-admin">
          <input type="text" placeholder="Buscar por bairro ou protocolo..." id="buscaOc" oninput="filtrarOcAdmin()" aria-label="Buscar por bairro ou protocolo">
          <!-- Status só faz sentido em "Em aberto" (as outras abas já são um status só) -->
          <select id="filtroStatus" onchange="filtrarOcAdmin()" aria-label="Filtrar por status" <?= $abaAtual !== 'abertas' ? 'hidden' : '' ?>>
            <option value="">Todos em aberto</option>
            <option value="nova">Nova</option>
            <option value="analise">Em análise</option>
            <option value="andamento">Em atendimento</option>
          </select>
          <select id="filtroTipo" onchange="filtrarOcAdmin()" aria-label="Filtrar por tipo">
            <option value="">Todos os tipos</option>
            <option value="descarte">Descarte irregular</option>
            <option value="coleta">Coleta não realizada</option>
            <option value="material">Material deixado</option>
            <option value="ponto">Ponto inadequado</option>
            <option value="entulho">Entulho/Móveis</option>
            <option value="tóxico">Lixo tóxico</option>
            <option value="outros">Outros</option>
          </select>
        </div>
      </div>

      <!-- TABELA DE OCORRÊNCIAS -->
      <div class="dashboard-card">
        <div class="card-header">
          <h2>Lista de Ocorrências</h2>
          <span class="total-label">Total geral: <strong><?= (int) ($total ?? 0) ?></strong></span>
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
            <?php foreach ($ocorrencias as $o): ?>
              <?php
              $tipoBusca   = mb_strtolower(rotuloTipoOcorrencia($o['tipo']), 'UTF-8');
              $statusBusca = classeStatusOcorrencia($o['status']);
              $abaLinha    = abaAdminOcorrencia($o['status']);
              ?>
              <tr data-id="<?= (int) $o['id_ocorrencia'] ?>" data-status="<?= $statusBusca ?>" data-aba="<?= $abaLinha ?>"
                  data-tipo="<?= htmlspecialchars($tipoBusca, ENT_QUOTES) ?>"
                  data-busca="<?= htmlspecialchars(mb_strtolower($o['protocolo'] . ' ' . $o['bairro'], 'UTF-8'), ENT_QUOTES) ?>"
                  <?= $abaLinha !== $abaAtual ? 'style="display:none"' : '' ?>>
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
                    <input type="hidden" name="aba" class="input-aba" value="<?= $abaAtual ?>">
                    <select class="status-select" name="status" onchange="this.form.submit()" aria-label="Status da ocorrência #<?= htmlspecialchars($o['protocolo'], ENT_QUOTES) ?>">
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
            <!-- Mensagem quando a aba (ou o filtro) não tem nenhuma ocorrência -->
            <tr class="oc-vazio-aba" id="ocVazioAba" <?= $contAbas[$abaAtual] > 0 ? 'style="display:none"' : '' ?>>
              <td colspan="8" id="ocVazioTexto"><?= [
                  'abertas'    => 'Nenhuma ocorrência em aberto. Tudo em dia.',
                  'resolvidas' => 'Nenhuma ocorrência resolvida ainda.',
                  'arquivadas' => 'Nenhuma ocorrência arquivada.',
              ][$abaAtual] ?></td>
            </tr>
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
        <input type="hidden" name="aba" class="input-aba" value="<?= $abaAtual ?>">
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
    As funções do modal, das abas e dos filtros desta página
    (verOcorrencia, verOcorrenciaDaLinha, fecharModalAdmin, trocarAbaOc, filtrarOcAdmin)
    ficam em assets/js/admin.js — fonte única, para evitar definições duplicadas.
  -->

<?php require_once 'app/view/comuns/admin_rodape.php'; ?>
