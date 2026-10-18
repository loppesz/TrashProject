<?php require_once 'app/view/comuns/admin_cabecalho.php'; ?>

      <!-- CARDS DE MÉTRICAS (clicáveis) -->
      <div class="metrics-grid">
        <a href="<?= baseUrl('admin/ocorrencias') ?>" class="metric-card metric-card-link">
          <div class="metric-icon laranja">🚨</div>
          <div class="metric-info">
            <span class="metric-value"><?= (int) ($totalOc ?? 0) ?></span>
            <span class="metric-label">Ocorrências Registradas</span>
          </div>
        </a>
        <a href="<?= baseUrl('admin/ocorrencias') ?>" class="metric-card metric-card-link">
          <div class="metric-icon verde">✅</div>
          <div class="metric-info">
            <span class="metric-value"><?= (int) ($resolvidas ?? 0) ?></span>
            <span class="metric-label">Ocorrências Resolvidas</span>
          </div>
        </a>
        <a href="<?= baseUrl('admin/materiais') ?>" class="metric-card metric-card-link">
          <div class="metric-icon azul">📦</div>
          <div class="metric-info">
            <span class="metric-value"><?= (int) ($totalMat ?? 0) ?></span>
            <span class="metric-label">Materiais Cadastrados</span>
          </div>
        </a>
        <a href="<?= baseUrl('admin/pontos') ?>" class="metric-card metric-card-link">
          <div class="metric-icon roxo">📍</div>
          <div class="metric-info">
            <span class="metric-value"><?= (int) ($totalPontos ?? 0) ?></span>
            <span class="metric-label">Pontos de Descarte</span>
          </div>
        </a>
      </div>

      <!-- LINHA: TABELA + GRÁFICO SIMPLES -->
      <div class="dashboard-row">

        <!-- OCORRÊNCIAS RECENTES -->
        <div class="dashboard-card">
          <div class="card-header">
            <h2>Ocorrências Recentes</h2>
            <a href="<?= baseUrl('admin/ocorrencias') ?>" class="ver-mais">Ver todas →</a>
          </div>
          <table class="admin-table">
            <thead>
              <tr>
                <th>Protocolo</th>
                <th>Tipo</th>
                <th>Bairro</th>
                <th>Status</th>
                <th>Data</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach (($recentes ?? []) as $o): ?>
                <tr>
                  <td>#<?= htmlspecialchars($o['protocolo']) ?></td>
                  <td><?= rotuloTipoOcorrencia($o['tipo']) ?></td>
                  <td><?= htmlspecialchars($o['bairro']) ?></td>
                  <td><span class="status-badge <?= classeStatusOcorrencia($o['status']) ?>"><?= rotuloStatusOcorrencia($o['status']) ?></span></td>
                  <td><?= date('d/m/Y', strtotime($o['criada_em'])) ?></td>
                </tr>
              <?php endforeach; ?>
              <?php if (empty($recentes)): ?>
                <tr><td colspan="5" style="text-align:center;padding:20px;color:#94a3b8;">Nenhuma ocorrência registrada.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- RESUMO POR BAIRRO -->
        <div class="dashboard-card dashboard-card-small">
          <div class="card-header">
            <h2>Ocorrências por Bairro</h2>
          </div>
          <div class="bairro-list">
            <?php
            $porBairro = $porBairro ?? [];
            $maxBairro = 0;
            foreach ($porBairro as $b) { $maxBairro = max($maxBairro, (int) $b['total']); }
            ?>
            <?php foreach (array_slice($porBairro, 0, 6) as $b): ?>
              <?php $pct = $maxBairro > 0 ? (int) round(((int) $b['total'] / $maxBairro) * 100) : 0; ?>
              <div class="bairro-item">
                <span class="bairro-nome"><?= htmlspecialchars($b['bairro']) ?></span>
                <div class="bairro-bar-wrap">
                  <div class="bairro-bar" style="width: <?= $pct ?>%"></div>
                </div>
                <span class="bairro-count"><?= (int) $b['total'] ?></span>
              </div>
            <?php endforeach; ?>
            <?php if (empty($porBairro)): ?>
              <p style="color:#94a3b8;font-size:.85rem;">Sem dados ainda.</p>
            <?php endif; ?>
          </div>

          <div class="card-header" style="margin-top: 24px;">
            <h2>Tipos de Ocorrência</h2>
          </div>
          <div class="tipo-list">
            <?php foreach (($porTipo ?? []) as $t): ?>
              <div class="tipo-item">
                <span><?= rotuloTipoOcorrencia($t['tipo']) ?></span>
                <strong><?= (int) $t['total'] ?></strong>
              </div>
            <?php endforeach; ?>
            <?php if (empty($porTipo)): ?>
              <p style="color:#94a3b8;font-size:.85rem;">Sem dados ainda.</p>
            <?php endif; ?>
          </div>
        </div>

      </div>

<?php require_once 'app/view/comuns/admin_rodape.php'; ?>
