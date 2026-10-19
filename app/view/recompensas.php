<?php require_once 'app/view/comuns/cabecalho.php'; ?>

  <!-- HERO RECOMPENSAS -->
  <section class="reward-hero">
    <div class="container">
      <div class="reward-hero-inner">
        <div>
          <span class="hero-badge">⭐ Sistema de Recompensas</span>
          <h1>Recicle, reporte<br>e <span class="text-yellow">ganhe prêmios!</span></h1>
          <p>Cada ocorrência reportada vale pontos. Troque seus pontos por recompensas reais na sua comunidade.</p>
          <a href="<?= baseUrl('ocorrencia') ?>" class="btn-reward-hero">Começar a ganhar pontos →</a>
        </div>
        <div class="reward-hero-visual">
          <div class="reward-coin">
            <span>⭐</span>
            <strong>+50 pts</strong>
            <small>por ocorrência</small>
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
      <p>Cada ação de conscientização vale pontos no ColetaFácil</p>
    </div>
    <div class="pontos-como-grid">
      <div class="pontos-como-item">
        <span class="pc-icon">🚨</span>
        <div><h4>Reportar ocorrência</h4><p>Cada ocorrência registrada e validada</p></div>
        <span class="pc-pts">+50 pts</span>
      </div>
      <div class="pontos-como-item">
        <span class="pc-icon">📸</span>
        <div><h4>Foto com evidência</h4><p>Ocorrência com foto aprovada</p></div>
        <span class="pc-pts">+20 pts</span>
      </div>
      <div class="pontos-como-item">
        <span class="pc-icon">🔥</span>
        <div><h4>Sequência de 7 dias</h4><p>Acessar a plataforma por 7 dias seguidos</p></div>
        <span class="pc-pts">+100 pts</span>
      </div>
      <div class="pontos-como-item">
        <span class="pc-icon">🧒</span>
        <div><h4>Completar quiz kids</h4><p>Criança completar quiz na Área Kids</p></div>
        <span class="pc-pts">+30 pts</span>
      </div>
      <div class="pontos-como-item">
        <span class="pc-icon">👥</span>
        <div><h4>Indicar um amigo</h4><p>Quando o amigo se cadastrar</p></div>
        <span class="pc-pts">+80 pts</span>
      </div>
      <div class="pontos-como-item">
        <span class="pc-icon">✅</span>
        <div><h4>Ocorrência resolvida</h4><p>Bônus quando o problema for resolvido</p></div>
        <span class="pc-pts">+30 pts</span>
      </div>
    </div>
  </section>

  <!-- RECOMPENSAS DISPONÍVEIS -->
  <section class="recompensas-section">
    <div class="container">
      <div class="section-header">
        <h2>Troque seus pontos</h2>
        <p>Recompensas pensadas para a sua comunidade<?php if ($usuario): ?> — você tem <strong><?= number_format($saldo, 0, ',', '.') ?> pts</strong><?php endif; ?></p>
      </div>
      <div class="recompensas-grid">
        <?php
        $recompensas = $recompensas ?? [];
        // icones por nome (fallback generico)
        $iconeRec = [
            'Café Grátis' => '☕', 'Desconto no Mercado' => '🛒', 'Kit de Mudas' => '🌱',
            'Certificado Digital' => '🎓', 'Desconto Bicicletaria' => '🚲', 'Kit Escolar Eco' => '🎨',
        ];
        ?>
        <?php foreach ($recompensas as $r): ?>
          <?php
          $disp  = (int) $r['disponivel'] === 1;
          $custo = (int) $r['custo_pontos'];
          $podeResgatar = $usuario && $disp && $saldo >= $custo;
          $ic = $iconeRec[$r['nome']] ?? '🎁';
          ?>
          <div class="recompensa-card">
            <div class="rc-badge <?= $disp ? 'disponivel' : 'em-breve' ?>"><?= $disp ? 'Disponível' : 'Em breve' ?></div>
            <div class="rc-icon"><?= $ic ?></div>
            <h3><?= htmlspecialchars($r['nome']) ?></h3>
            <p>
              <?php if (!empty($r['parceiro'])): ?>Parceiro: <?= htmlspecialchars($r['parceiro']) ?>
              <?php elseif (!empty($r['descricao'])): ?><?= htmlspecialchars($r['descricao']) ?>
              <?php endif; ?>
            </p>
            <div class="rc-cost"><span>⭐ <?= number_format($custo, 0, ',', '.') ?> pts</span></div>
            <?php if (!$disp): ?>
              <button class="btn-resgatar disabled" disabled>Em breve</button>
            <?php elseif (!$usuario): ?>
              <a href="<?= baseUrl('usuario/login') ?>" class="btn-resgatar" style="text-decoration:none;display:inline-block;">Entrar para resgatar</a>
            <?php elseif ($saldo < $custo): ?>
              <button class="btn-resgatar disabled" disabled>Faltam <?= number_format($custo - $saldo, 0, ',', '.') ?> pts</button>
            <?php else: ?>
              <form method="POST" action="<?= baseUrl('home/resgatar') ?>" onsubmit="return confirm('Resgatar &quot;<?= htmlspecialchars($r['nome'], ENT_QUOTES) ?>&quot; por <?= $custo ?> pts?');">
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
            <span>👤 <?= htmlspecialchars($u['nome']) ?></span>
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
      <p>Apresente este código no parceiro:</p>
      <div class="resgate-code"><?= htmlspecialchars($codigoResgate) ?></div>
      <p style="font-size:0.8rem; color:#6b7280; margin-top:8px;">Válido por 7 dias. Leve um documento com foto.</p>
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
