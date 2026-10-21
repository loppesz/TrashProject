<?php require_once 'app/view/comuns/admin_cabecalho.php'; ?>

      <!-- MÉTRICAS RÁPIDAS -->
      <div class="metrics-grid">
        <div class="metric-card">
          <div class="metric-icon laranja">OC</div>
          <div class="metric-info">
            <span class="metric-value"><?= (int) ($totalOc ?? 0) ?></span>
            <span class="metric-label">Total de Ocorrências</span>
          </div>
        </div>
        <div class="metric-card">
          <div class="metric-icon verde">OK</div>
          <div class="metric-info">
            <span class="metric-value"><?= (int) ($resolvidas ?? 0) ?></span>
            <span class="metric-label">Ocorrências Resolvidas</span>
          </div>
        </div>
        <div class="metric-card">
          <div class="metric-icon azul">TR</div>
          <div class="metric-info">
            <span class="metric-value"><?= (int) ($emAndamento ?? 0) ?></span>
            <span class="metric-label">Em tratamento</span>
          </div>
        </div>
        <div class="metric-card">
          <div class="metric-icon roxo">%</div>
          <div class="metric-info">
            <span class="metric-value"><?= (int) ($taxa ?? 0) ?>%</span>
            <span class="metric-label">Taxa de Resolução</span>
          </div>
        </div>
      </div>

      <div class="dashboard-row">

        <!-- BAIRROS COM MAIS OCORRÊNCIAS -->
        <div class="dashboard-card">
          <div class="card-header">
            <h2>Bairros com mais ocorrências</h2>
          </div>
          <div class="bairro-list">
            <?php
            $porBairro = $porBairro ?? [];
            $maxB = 0;
            foreach ($porBairro as $b) { $maxB = max($maxB, (int) $b['total']); }
            $cores = ['#ef4444', '#f97316', '#eab308', '#22c55e', '#22c55e', '#3b82f6'];
            ?>
            <?php foreach (array_slice($porBairro, 0, 6) as $i => $b): ?>
              <?php $pct = $maxB > 0 ? (int) round(((int) $b['total'] / $maxB) * 100) : 0; ?>
              <div class="bairro-item">
                <span class="bairro-nome"><?= htmlspecialchars($b['bairro']) ?></span>
                <div class="bairro-bar-wrap">
                  <div class="bairro-bar" style="width: <?= $pct ?>%; background: <?= $cores[$i] ?? '#22c55e' ?>;"></div>
                </div>
                <span class="bairro-count"><?= (int) $b['total'] ?></span>
              </div>
            <?php endforeach; ?>
            <?php if (empty($porBairro)): ?>
              <p style="color:#94a3b8;font-size:.85rem;">Sem dados ainda.</p>
            <?php endif; ?>
          </div>
        </div>

        <!-- TIPOS DE OCORRÊNCIA -->
        <div class="dashboard-card dashboard-card-small">
          <div class="card-header">
            <h2>Tipos mais frequentes</h2>
          </div>
          <div class="tipo-list">
            <?php
            $porTipo = $porTipo ?? [];
            $totalTipos = 0;
            foreach ($porTipo as $t) { $totalTipos += (int) $t['total']; }
            ?>
            <?php foreach ($porTipo as $t): ?>
              <?php $pctT = $totalTipos > 0 ? (int) round(((int) $t['total'] / $totalTipos) * 100) : 0; ?>
              <div class="tipo-item">
                <span><?= rotuloTipoOcorrencia($t['tipo']) ?></span>
                <strong><?= (int) $t['total'] ?> (<?= $pctT ?>%)</strong>
              </div>
            <?php endforeach; ?>
            <?php if (empty($porTipo)): ?>
              <p style="color:#94a3b8;font-size:.85rem;">Sem dados ainda.</p>
            <?php endif; ?>
          </div>

          <div class="card-header" style="margin-top:24px;">
            <h2>Situação atual</h2>
          </div>
          <div class="tipo-list">
            <?php
            $porStatus = $porStatus ?? [];
            $statusOrdem = ['NOVA', 'EM_ANALISE', 'EM_ANDAMENTO', 'RESOLVIDA', 'ARQUIVADA'];
            ?>
            <?php foreach ($statusOrdem as $status): ?>
              <div class="tipo-item">
                <span><?= htmlspecialchars(rotuloStatusOcorrencia($status)) ?></span>
                <strong><?= (int) ($porStatus[$status] ?? 0) ?></strong>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

      </div>

      <!-- TABELA DE LOCAIS COM DESCARTE IRREGULAR -->
      <div class="dashboard-card">
        <div class="card-header">
          <h2>Locais com mais relatos de descarte irregular</h2>
        </div>
        <table class="admin-table">
          <thead>
            <tr>
              <th>Local</th>
              <th>Bairro</th>
              <th>Quantidade de Relatos</th>
              <th>Última Ocorrência</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach (($locaisFrequentes ?? []) as $local): ?>
              <tr>
                <td><?= htmlspecialchars($local['local']) ?></td>
                <td><?= htmlspecialchars($local['bairro']) ?></td>
                <td><strong><?= (int) $local['total'] ?></strong></td>
                <td><?= date('d/m/Y', strtotime($local['ultima'])) ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($locaisFrequentes)): ?>
              <tr><td colspan="4" class="admin-empty">Nenhum relato de descarte irregular registrado.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

<?php require_once 'app/view/comuns/admin_rodape.php'; ?>
