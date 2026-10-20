<?php require_once 'app/view/comuns/admin_cabecalho.php'; ?>

      <?php if (!empty($msg)): ?>
        <div class="dashboard-card" style="border-left:4px solid #22c55e;">
          <strong style="color:#15803d;">✅ <?= htmlspecialchars($msg) ?></strong>
        </div>
      <?php elseif (!empty($erro)): ?>
        <div class="dashboard-card" style="border-left:4px solid #ef4444;">
          <strong style="color:#b91c1c;">⚠️ <?= htmlspecialchars($erro) ?></strong>
        </div>
      <?php endif; ?>

      <?php
      $sugestoes = $sugestoes ?? [];
      $contagens = $contagens ?? [];
      $rotuloStatus = [
          'NOVA'       => '🔵 Nova',
          'EM_ANALISE' => '🟡 Em análise',
          'APROVADA'   => '🟢 Aprovada',
          'RECUSADA'   => '🔴 Recusada',
      ];
      ?>

      <!-- RESUMO -->
      <div class="dashboard-card">
        <div class="card-header">
          <h2>Sugestões da comunidade</h2>
          <span class="total-label">Total: <strong><?= count($sugestoes) ?></strong></span>
        </div>
        <div class="filtros-admin" style="gap:16px;flex-wrap:wrap;">
          <span>🔵 Novas: <strong><?= (int) ($contagens['NOVA'] ?? 0) ?></strong></span>
          <span>🟡 Em análise: <strong><?= (int) ($contagens['EM_ANALISE'] ?? 0) ?></strong></span>
          <span>🟢 Aprovadas: <strong><?= (int) ($contagens['APROVADA'] ?? 0) ?></strong></span>
          <span>🔴 Recusadas: <strong><?= (int) ($contagens['RECUSADA'] ?? 0) ?></strong></span>
        </div>
      </div>

      <!-- TABELA DE SUGESTÕES -->
      <div class="dashboard-card">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Local</th>
              <th>Endereço</th>
              <th>Materiais</th>
              <th>Horário</th>
              <th>Enviado por</th>
              <th>Data</th>
              <th>Status</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($sugestoes as $s): ?>
              <tr>
                <td><strong><?= htmlspecialchars($s['nome_local']) ?></strong>
                  <?php if (!empty($s['observacao'])): ?>
                    <br><small style="color:#64748b;"><?= htmlspecialchars($s['observacao']) ?></small>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($s['endereco']) ?></td>
                <td><?= htmlspecialchars($s['materiais_informados'] ?? '') ?: '—' ?></td>
                <td><?= htmlspecialchars($s['horario_informado'] ?? '') ?: '—' ?></td>
                <td><?= htmlspecialchars($s['usuario_nome'] ?? '') ?: 'Anônimo' ?></td>
                <td><?= date('d/m/Y', strtotime($s['criada_em'])) ?></td>
                <td><?= $rotuloStatus[$s['status']] ?? htmlspecialchars($s['status']) ?></td>
                <td>
                  <?php if (in_array($s['status'], ['NOVA', 'EM_ANALISE'], true)): ?>
                    <div style="display:flex;gap:6px;flex-wrap:wrap;">
                      <form method="POST" action="<?= baseUrl('admin/atualizarSugestao') ?>" style="display:inline;">
                        <input type="hidden" name="id_sugestao" value="<?= (int) $s['id_sugestao'] ?>">
                        <input type="hidden" name="status" value="APROVADA">
                        <button type="submit" class="btn-acao ver" style="background:#16a34a;">✓ Aprovar</button>
                      </form>
                      <form method="POST" action="<?= baseUrl('admin/atualizarSugestao') ?>" style="display:inline;">
                        <input type="hidden" name="id_sugestao" value="<?= (int) $s['id_sugestao'] ?>">
                        <input type="hidden" name="status" value="RECUSADA">
                        <button type="submit" class="btn-acao" style="background:#dc2626;color:#fff;">✕ Recusar</button>
                      </form>
                    </div>
                  <?php else: ?>
                    <span style="color:#94a3b8;">Analisada</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($sugestoes)): ?>
              <tr><td colspan="8" style="text-align:center;padding:24px;color:#94a3b8;">Nenhuma sugestão recebida ainda.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

<?php require_once 'app/view/comuns/admin_rodape.php'; ?>
