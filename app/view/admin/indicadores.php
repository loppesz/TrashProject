<?php require_once 'app/view/comuns/admin_cabecalho.php'; ?>

      <!-- MÉTRICAS RÁPIDAS -->
      <div class="metrics-grid">
        <div class="metric-card">
          <div class="metric-icon laranja">🚨</div>
          <div class="metric-info">
            <span class="metric-value"><?= (int) ($totalOc ?? 0) ?></span>
            <span class="metric-label">Total de Ocorrências</span>
          </div>
        </div>
        <div class="metric-card">
          <div class="metric-icon verde">✅</div>
          <div class="metric-info">
            <span class="metric-value"><?= (int) ($resolvidas ?? 0) ?></span>
            <span class="metric-label">Ocorrências Resolvidas</span>
          </div>
        </div>
        <div class="metric-card">
          <div class="metric-icon azul">⏳</div>
          <div class="metric-info">
            <span class="metric-value"><?= (int) ($emAndamento ?? 0) ?></span>
            <span class="metric-label">Em Andamento</span>
          </div>
        </div>
        <div class="metric-card">
          <div class="metric-icon roxo">📊</div>
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
            <h2>📍 Bairros com Mais Ocorrências</h2>
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
            <h2>🗂️ Tipos Mais Frequentes</h2>
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
            <h2>❓ Materiais que Geram Mais Dúvidas</h2>
          </div>
          <div class="tipo-list">
            <div class="tipo-item">
              <span>🍶 Vidro</span>
              <strong>34 consultas</strong>
            </div>
            <div class="tipo-item">
              <span>🔋 Pilhas</span>
              <strong>28 consultas</strong>
            </div>
            <div class="tipo-item">
              <span>💊 Medicamentos</span>
              <strong>21 consultas</strong>
            </div>
            <div class="tipo-item">
              <span>🫙 Óleo de cozinha</span>
              <strong>18 consultas</strong>
            </div>
          </div>
        </div>

      </div>

      <!-- TABELA DE LOCAIS COM DESCARTE IRREGULAR -->
      <div class="dashboard-card">
        <div class="card-header">
          <h2>🗑️ Locais com Mais Relatos de Descarte Irregular</h2>
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
            <tr>
              <td>Rua das Palmeiras</td>
              <td>Centro</td>
              <td><strong>5</strong></td>
              <td>20/08/2026</td>
            </tr>
            <tr>
              <td>Av. Brasil (próx. ao mercado)</td>
              <td>Bairro Novo</td>
              <td><strong>3</strong></td>
              <td>19/08/2026</td>
            </tr>
            <tr>
              <td>Rua do Comércio</td>
              <td>Vila Nova</td>
              <td><strong>2</strong></td>
              <td>21/08/2026</td>
            </tr>
            <tr>
              <td>Praça da Amizade</td>
              <td>Jardim América</td>
              <td><strong>2</strong></td>
              <td>18/08/2026</td>
            </tr>
          </tbody>
        </table>
      </div>

<?php require_once 'app/view/comuns/admin_rodape.php'; ?>
