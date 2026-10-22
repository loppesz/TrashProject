<?php require_once 'app/view/comuns/admin_cabecalho.php'; ?>
<?php
  $inv  = $inventario ?? [];
  $pend = $pendencias ?? [];
  $ret  = $retencao ?? RETENCAO_DIAS;
  $totalPend = array_sum($pend);
  $rotuloAcao = [
      'EXPORTACAO_DADOS'  => 'Titular baixou os próprios dados',
      'EXCLUSAO_CONTA'    => 'Conta excluída a pedido do titular',
      'TROCA_SENHA'       => 'Titular trocou a senha',
      'RETENCAO_APLICADA' => 'Política de retenção aplicada',
  ];
?>

      <?php if (!empty($msg)): ?>
        <div class="dashboard-card" style="border-left:4px solid #22c55e;" role="status"><strong style="color:var(--success,#15803d);">✅ <?= htmlspecialchars($msg) ?></strong></div>
      <?php elseif (!empty($erro)): ?>
        <div class="dashboard-card" style="border-left:4px solid #ef4444;" role="alert"><strong style="color:var(--danger,#b91c1c);">⚠️ <?= htmlspecialchars($erro) ?></strong></div>
      <?php endif; ?>

      <!-- INVENTÁRIO -->
      <div class="dashboard-card">
        <div class="card-header"><h2>Dados pessoais guardados hoje</h2></div>
        <div class="lgpd-grid">
          <div class="lgpd-item"><strong><?= (int) ($inv['usuarios'] ?? 0) ?></strong><span>contas (<?= (int) ($inv['usuarios_aceite'] ?? 0) ?> com aceite registrado)</span></div>
          <div class="lgpd-item"><strong><?= (int) ($inv['oc_com_contato'] ?? 0) ?></strong><span>ocorrências com contato</span></div>
          <div class="lgpd-item"><strong><?= (int) ($inv['oc_com_gps'] ?? 0) ?></strong><span>ocorrências com GPS</span></div>
          <div class="lgpd-item"><strong><?= (int) ($inv['fotos'] ?? 0) ?></strong><span>fotos de ocorrências</span></div>
          <div class="lgpd-item"><strong><?= (int) ($inv['oc_anonimas'] ?? 0) ?></strong><span>ocorrências anônimas</span></div>
          <div class="lgpd-item"><strong><?= (int) ($inv['oc_ocultas'] ?? 0) ?></strong><span>fora do histórico público</span></div>
          <div class="lgpd-item"><strong><?= (int) ($inv['perfis_kids'] ?? 0) ?></strong><span>perfis da Área Kids</span></div>
          <div class="lgpd-item"><strong><?= (int) ($inv['tentativas'] ?? 0) ?></strong><span>registros de tentativas (hash do IP)</span></div>
        </div>
      </div>

      <!-- RETENÇÃO -->
      <div class="dashboard-card">
        <div class="card-header">
          <h2>Política de retenção</h2>
          <span class="total-label">Última aplicação: <strong><?= !empty($ultima) ? date('d/m/Y H:i', strtotime($ultima)) : 'nunca' ?></strong></span>
        </div>
        <table class="admin-table">
          <thead><tr><th>Dado</th><th>Prazo</th><th>O que acontece</th><th>Pendentes agora</th></tr></thead>
          <tbody>
            <tr><td>Contato em ocorrências</td><td><?= (int) $ret['contato_ocorrencia'] ?> dias após encerrar</td><td>Nome, telefone e e-mail apagados</td><td><?= (int) ($pend['contatos'] ?? 0) ?></td></tr>
            <tr><td>GPS de ocorrências</td><td><?= (int) $ret['gps_ocorrencia'] ?> dias após encerrar</td><td>Posição exata vira aproximada (~100 m)</td><td><?= (int) ($pend['gps'] ?? 0) ?></td></tr>
            <tr><td>Fotos de ocorrências</td><td><?= (int) $ret['foto_ocorrencia'] ?> dias após encerrar</td><td>Foto apagada</td><td><?= (int) ($pend['fotos'] ?? 0) ?></td></tr>
            <tr><td>Controle de tentativas</td><td><?= (int) $ret['registro_acao'] ?> dia</td><td>Registro apagado</td><td><?= (int) ($pend['tentativas'] ?? 0) ?></td></tr>
            <tr><td>Auditoria de privacidade</td><td><?= (int) $ret['registro_privacidade'] ?> dias</td><td>Registro apagado</td><td><?= (int) ($pend['auditoria'] ?? 0) ?></td></tr>
          </tbody>
        </table>
        <form method="POST" action="<?= baseUrl('admin/aplicarRetencao') ?>" style="margin-top:14px;"
              onsubmit="return confirm('Aplicar a política de retenção agora? Os dados vencidos serão apagados definitivamente.');">
          <?= csrf_campo() ?>
          <button type="submit" class="btn-primary" <?= $totalPend === 0 ? 'disabled' : '' ?>>
            <?= $totalPend === 0 ? 'Nada pendente' : 'Aplicar retenção agora (' . $totalPend . ' itens)' ?>
          </button>
        </form>
        <p style="margin-top:8px;font-size:.82rem;color:var(--text-muted);">Recomendação: aplicar uma vez por mês. As ocorrências continuam no sistema; só os dados pessoais vencidos são removidos.</p>
      </div>

      <!-- AUDITORIA -->
      <div class="dashboard-card">
        <div class="card-header"><h2>Registro de ações de privacidade</h2></div>
        <table class="admin-table">
          <thead><tr><th>Data</th><th>Ação</th><th>Quem</th></tr></thead>
          <tbody>
            <?php foreach (($auditoria ?? []) as $a): ?>
              <tr>
                <td><?= date('d/m/Y H:i', strtotime($a['criada_em'])) ?></td>
                <td><?= htmlspecialchars($rotuloAcao[$a['acao']] ?? $a['acao']) ?></td>
                <td><?= htmlspecialchars($a['usuario'] ?? 'Titular (conta excluída)') ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($auditoria)): ?>
              <tr><td colspan="3" class="admin-empty">Nenhuma ação registrada ainda.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

<?php require_once 'app/view/comuns/admin_rodape.php'; ?>
