<?php require_once 'app/view/comuns/cabecalho.php'; ?>

  <!-- PAGE HEADER (breadcrumb enxuto) -->
  <div class="page-header-slim">
    <div class="container">
      <nav class="breadcrumb-nav">
        <a href="<?= baseUrl() ?>" style="color:#4ade80">Início</a>
        <span style="color:#475569">/</span>
        <span style="color:#94a3b8">Guia de Materiais</span>
      </nav>
    </div>
  </div>

  <!-- DESTAQUE: PROTEÇÃO DO COLETOR -->
  <div class="container">
    <div class="coletor-destaque">
      <div class="coletor-destaque-img" style="background-image:url('<?= baseUrl('assets/img/coleta-material-reciclavel-03.jpg') ?>')" role="img" aria-label="Materiais recicláveis separados: garrafas plásticas, latas e tampinhas coloridas"></div>
      <div class="coletor-destaque-text">
        <span class="coletor-tag">🧤 A sua atitude em casa salva vidas</span>
        <h2>Como você separa define a segurança de quem recolhe</h2>
        <p>Atrás de cada saco de lixo existe uma pessoa. Vidros quebrados, latas com bordas afiadas e materiais perfurocortantes mal embalados provocam <strong>cortes graves</strong> nas mãos dos coletores todos os dias.</p>
        <div class="coletor-dicas">
          <div class="coletor-dica"><span>🍶</span><p><strong>Vidro quebrado:</strong> embrulhe em jornal grosso ou papelão e escreva "CUIDADO VIDRO".</p></div>
          <div class="coletor-dica"><span>🥫</span><p><strong>Metais cortantes:</strong> lave, amasse e, se tiver bordas, proteja antes de descartar.</p></div>
          <div class="coletor-dica"><span>💉</span><p><strong>Perfurocortantes:</strong> nunca na coleta — leve a uma farmácia ou UBS.</p></div>
        </div>
      </div>
    </div>
  </div>

  <!-- FILTROS -->
  <div class="container">
    <div class="filtros">
      <button class="filtro-btn active" onclick="filtrar('todos', this)">Todos</button>
      <button class="filtro-btn" onclick="filtrar('aceito', this)">✅ Aceitos</button>
      <button class="filtro-btn" onclick="filtrar('nao-aceito', this)">❌ Não Aceitos</button>
      <button class="filtro-btn" onclick="filtrar('cuidado', this)">⚠️ Cuidado Especial</button>
    </div>
  </div>

  <!-- LISTA DE MATERIAIS (gerada a partir do banco) -->
  <section class="container materiais-section">
    <div class="materiais-grid" id="materiaisGrid">

      <?php
      $materiais = $materiais ?? [];
      // status_coleta (banco) -> data-tipo e classe/label do status (tela)
      $mapStatus = [
          'ACEITO'     => ['tipo' => 'aceito',     'classe' => 'aceito',     'rotulo' => '✅ Aceito'],
          'CUIDADO'    => ['tipo' => 'cuidado',    'classe' => 'cuidado',    'rotulo' => '⚠️ Cuidado'],
          'NAO_ACEITO' => ['tipo' => 'nao-aceito', 'classe' => 'nao-aceito', 'rotulo' => '❌ Não Aceito'],
      ];
      ?>

      <?php foreach ($materiais as $m): ?>
        <?php
        $st  = $mapStatus[$m['status_coleta']] ?? $mapStatus['ACEITO'];
        $cor = $m['cor_card'] ?? 'azul';
        // Regra fiel ao original: sem exemplos + com alerta => mostra alerta vermelho
        $mostraAlerta = empty($m['exemplos']) && !empty($m['alerta_seguranca']);
        ?>
        <div class="material-card" data-tipo="<?= $st['tipo'] ?>">
          <div class="material-header <?= htmlspecialchars($cor, ENT_QUOTES) ?>">
            <span class="material-icon"><?= htmlspecialchars((string) $m['icone']) ?></span>
            <span class="material-status <?= $st['classe'] ?>"><?= $st['rotulo'] ?></span>
          </div>
          <h3><?= htmlspecialchars($m['nome']) ?></h3>
          <p><?= htmlspecialchars($m['descricao']) ?></p>
          <div class="material-preparo">
            <strong><?= htmlspecialchars($m['preparo_label'] ?? 'Como preparar') ?>:</strong>
            <ul>
              <?php foreach ($m['instrucoes'] as $ins): ?>
                <li><?= htmlspecialchars($ins['texto']) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php if ($mostraAlerta): ?>
            <div class="alerta-vermelho">
              🔴 <?= htmlspecialchars($m['alerta_seguranca']) ?>
            </div>
          <?php elseif (!empty($m['exemplos'])): ?>
            <div class="exemplos">
              <?php foreach ($m['exemplos'] as $ex): ?>
                <span class="tag<?= $ex['negativo'] ? ' negativo' : '' ?>"><?= htmlspecialchars((string) $ex['icone']) . ' ' . htmlspecialchars($ex['rotulo']) ?></span>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
      <?php /* FIM_LOOP_MATERIAIS */ ?>

    </div>
  </section>

<?php require_once 'app/view/comuns/rodape.php'; ?>
