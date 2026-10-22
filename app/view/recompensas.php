<?php require_once 'app/view/comuns/cabecalho.php'; ?>
<?php
  // Regras de pontos vindas do controller (fonte unica: PontosModel::regras()).
  $rp = $regrasPontos ?? ['registro' => 10, 'resolvida' => 40, 'foto' => 15, 'limiteDia' => 3, 'maximo' => 65];
?>

  <!-- HERO RECOMPENSAS -->
  <section class="reward-hero">
    <div class="container">
      <div class="reward-hero-inner">
        <div>
          <span class="hero-badge">⭐ Sistema de Recompensas</span>
          <h1>Cuide da sua rua<br>e <span class="text-yellow">troque por recompensas</span></h1>
          <p>Registre problemas reais de descarte em Muriaé. Quando a equipe confirma e resolve, você recebe a maior parte dos pontos e pode trocá-los por itens que ajudam a reciclar em casa.</p>
          <a href="<?= baseUrl('ocorrencia') ?>" class="btn-reward-hero">Registrar uma ocorrência →</a>
        </div>
        <div class="reward-hero-visual">
          <div class="reward-coin">
            <span>⭐</span>
            <strong>até <?= (int) $rp['maximo'] ?> pts</strong>
            <small>por ocorrência confirmada</small>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- MEUS PONTOS -->
  <section class="container my-points-section">
    <?php
    $usuario = $usuario ?? null;
    $saldo   = (int) ($saldo ?? 0);
    $nivel   = $nivel ?? ['nome' => 'Iniciante', 'icone' => '🌱', 'classe' => 'iniciante'];
    // proximo nivel: 300 (bronze), 1000 (prata), 2000 (ouro)
    $limiares = [300, 1000, 2000];
    $proximo = 2000;
    foreach ($limiares as $lim) { if ($saldo < $lim) { $proximo = $lim; break; } }
    $pctNivel = $proximo > 0 ? min(100, (int) round(($saldo / $proximo) * 100)) : 100;
    ?>
    <div class="my-points-card">
      <div class="points-left">
        <div class="points-avatar">👤</div>
        <div>
          <?php if ($usuario): ?>
            <p class="points-greeting">Olá, <strong><?= htmlspecialchars($usuario['nome']) ?>!</strong></p>
            <p class="points-sub">Continue contribuindo para subir de nível</p>
          <?php else: ?>
            <p class="points-greeting">Olá, <strong>Visitante!</strong></p>
            <p class="points-sub"><a href="<?= baseUrl('usuario/login') ?>">Entre</a> para acumular e resgatar pontos</p>
          <?php endif; ?>
        </div>
      </div>
      <div class="points-right">
        <div class="points-display">
          <span class="points-value"><?= number_format($saldo, 0, ',', '.') ?></span>
          <span class="points-unit">pontos</span>
        </div>
        <div class="points-level">
          <span><?= $nivel['icone'] ?> Nível: <?= htmlspecialchars($nivel['nome']) ?></span>
          <div class="level-bar-wrap">
            <div class="level-bar" style="width: <?= $pctNivel ?>%"></div>
          </div>
          <span class="level-next"><?= number_format($saldo, 0, ',', '.') ?> / <?= number_format($proximo, 0, ',', '.') ?> pts para o próximo nível</span>
        </div>
      </div>
    </div>
  </section>

  <!-- COMO GANHAR PONTOS -->
  <section class="container">
    <div class="section-header">
      <h2>Como ganhar pontos?</h2>
      <p>A maior parte dos pontos só entra quando o problema é confirmado pela equipe</p>
    </div>
    <div class="pontos-como-grid">
      <div class="pontos-como-item">
        <span class="pc-icon">📝</span>
        <div><h4>Registrar ocorrência</h4><p>Ao enviar, logado e sem modo anônimo (até <?= (int) $rp['limiteDia'] ?> por dia)</p></div>
        <span class="pc-pts">+<?= (int) $rp['registro'] ?> pts</span>
      </div>
      <div class="pontos-como-item">
        <span class="pc-icon">✅</span>
        <div><h4>Ocorrência confirmada</h4><p>Quando a equipe verifica e resolve o problema</p></div>
        <span class="pc-pts">+<?= (int) $rp['resolvida'] ?> pts</span>
      </div>
      <div class="pontos-como-item">
        <span class="pc-icon">📸</span>
        <div><h4>Foto de evidência</h4><p>Creditada junto com a confirmação da ocorrência</p></div>
        <span class="pc-pts">+<?= (int) $rp['foto'] ?> pts</span>
      </div>
    </div>
    <ul class="pontos-regras-justas">
      <li>Ocorrências repetidas, falsas ou fora de Muriaé são arquivadas e não recebem o bônus de confirmação.</li>
      <li>Registros anônimos não somam pontos, porque não ficam ligados a uma conta.</li>
      <li>Administradores não pontuam ao resolver as próprias ocorrências e não entram no ranking.</li>
    </ul>
  </section>

  <!-- RECOMPENSAS DISPONÍVEIS -->
  <section class="recompensas-section">
    <div class="container">
      <div class="section-header">
        <h2>Troque seus pontos</h2>
        <p>Itens que ajudam a separar, reaproveitar e reduzir o lixo em casa<?php if ($usuario): ?> — você tem <strong><?= number_format($saldo, 0, ',', '.') ?> pts</strong><?php endif; ?></p>
      </div>
      <div class="recompensas-grid">
        <?php
        $recompensas = $recompensas ?? [];
        // Ícones por nome (fallback genérico)
        $iconeRec = [
            'Certificado digital "Morador Consciente"' => '📜',
            'Kit de sacos coloridos para separação'   => '🛍️',
            'Muda de árvore nativa da Mata Atlântica' => '🌳',
            'Ecobag reutilizável'                     => '👜',
            'Vaga em oficina de compostagem'          => '🧑‍🌾',
            'Garrafa reutilizável'                    => '🧴',
            'Desconto em comércio parceiro de Muriaé' => '🏪',
            'Kit compostagem doméstica'               => '🪱',
        ];
        ?>
        <?php foreach ($recompensas as $r): ?>
          <?php
          $custo   = (int) $r['custo_pontos'];
          $estoque = $r['estoque'] === null ? null : (int) $r['estoque'];
          $esgotado = $estoque !== null && $estoque <= 0;
          $disp  = (int) $r['disponivel'] === 1 && !$esgotado;
          $ic = $iconeRec[$r['nome']] ?? '🎁';
          $badgeTxt = $esgotado ? 'Esgotado' : ((int) $r['disponivel'] === 1 ? 'Disponível' : 'Em breve');
          ?>
          <div class="recompensa-card">
            <div class="rc-badge <?= $disp ? 'disponivel' : 'em-breve' ?>"><?= $badgeTxt ?></div>
            <div class="rc-icon" aria-hidden="true"><?= $ic ?></div>
            <h3><?= htmlspecialchars($r['nome']) ?></h3>
            <?php if (!empty($r['descricao'])): ?>
              <p><?= htmlspecialchars($r['descricao']) ?></p>
            <?php endif; ?>
            <?php if (!empty($r['parceiro'])): ?>
              <p class="rc-parceiro">Parceiro: <?= htmlspecialchars($r['parceiro']) ?></p>
            <?php endif; ?>
            <div class="rc-cost">
              <span>⭐ <?= number_format($custo, 0, ',', '.') ?> pts</span>
              <?php if ($estoque !== null && !$esgotado && (int) $r['disponivel'] === 1): ?>
                <small class="rc-estoque">Restam <?= $estoque ?></small>
              <?php endif; ?>
            </div>
            <?php if ($esgotado): ?>
              <button class="btn-resgatar disabled" disabled>Esgotado</button>
            <?php elseif (!$disp): ?>
              <button class="btn-resgatar disabled" disabled>Em breve</button>
            <?php elseif (!$usuario): ?>
              <a href="<?= baseUrl('usuario/login') ?>" class="btn-resgatar" style="text-decoration:none;display:inline-block;">Entrar para resgatar</a>
            <?php elseif ($saldo < $custo): ?>
              <button class="btn-resgatar disabled" disabled>Faltam <?= number_format($custo - $saldo, 0, ',', '.') ?> pts</button>
            <?php else: ?>
              <form method="POST" action="<?= baseUrl('home/resgatar') ?>" onsubmit="return confirm(<?= jsAttr('Resgatar "' . $r['nome'] . '" por ' . $custo . ' pts?') ?>);">
                <?= csrf_campo() ?>
                <input type="hidden" name="id_recompensa" value="<?= (int) $r['id_recompensa'] ?>">
                <button type="submit" class="btn-resgatar">Resgatar</button>
              </form>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
        <?php if (empty($recompensas)): ?>
          <p style="color:var(--text-muted);">Nenhuma recompensa cadastrada no momento.</p>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- RANKING -->
  <section class="ranking-section">
    <div class="container">
      <div class="section-header light">
        <h2>🏆 Ranking de Pontos</h2>
        <p>Os maiores contribuidores da comunidade</p>
      </div>
      <div class="ranking-full-table">
        <div class="rt-header">
          <span>#</span>
          <span>Participante</span>
          <span>Ocorrências</span>
          <span>Pontos</span>
          <span>Nível</span>
        </div>
        <?php
        $ranking = $ranking ?? [];
        $medalhas = ['🥇 1', '🥈 2', '🥉 3'];
        $classeLinha = ['gold-row', 'silver-row', 'bronze-row'];
        ?>
        <?php foreach ($ranking as $i => $u): ?>
          <?php
          $pos = $i < 3 ? $medalhas[$i] : (string) ($i + 1);
          $linha = $classeLinha[$i] ?? '';
          $niv = PontosModel::nivel((int) $u['pontos']);
          ?>
          <div class="rt-row <?= $linha ?>">
            <span><?= $pos ?></span>
            <span>👤 <?= htmlspecialchars(nomePublico($u['nome'])) ?></span>
            <span><?= (int) $u['ocorrencias'] ?></span>
            <span><strong><?= number_format((int) $u['pontos'], 0, ',', '.') ?></strong></span>
            <span class="nivel-badge <?= $niv['classe'] ?>"><?= $niv['icone'] ?> <?= $niv['nome'] ?></span>
          </div>
        <?php endforeach; ?>
        <?php if (empty($ranking)): ?>
          <div class="rt-row"><span colspan="5" style="padding:16px;color:var(--text-muted);">Ainda não há pontuações registradas. Seja o primeiro!</span></div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- MODAL RESGATE (sucesso) -->
  <?php if (($msgResgate ?? '') === 'ok' && !empty($codigoResgate)): ?>
  <div id="modalResgate" class="modal" style="display:flex;">
    <div class="modal-content">
      <div class="modal-icon">🎉</div>
      <h3>Resgate Solicitado!</h3>
      <p>Sua solicitação foi registrada e os pontos foram debitados.</p>
      <p>Guarde este código para a retirada:</p>
      <div class="resgate-code"><?= htmlspecialchars($codigoResgate) ?></div>
      <p style="font-size:0.8rem; color:var(--text-muted); margin-top:8px;">Válido por 7 dias. A equipe do ColetaFácil informa o local e o horário de retirada; leve um documento com foto.</p>
      <button class="btn-primary" onclick="document.getElementById('modalResgate').style.display='none'">Fechar</button>
    </div>
  </div>
  <?php endif; ?>

  <?php if (!empty($erroResgate)): ?>
  <div id="modalResgateErro" class="modal" style="display:flex;">
    <div class="modal-content">
      <div class="modal-icon">⚠️</div>
      <h3>Não foi possível resgatar</h3>
      <p><?= htmlspecialchars($erroResgate) ?></p>
      <button class="btn-primary" onclick="document.getElementById('modalResgateErro').style.display='none'">Entendi</button>
    </div>
  </div>
  <?php endif; ?>

<?php require_once 'app/view/comuns/rodape.php'; ?>
