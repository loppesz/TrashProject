<?php require_once 'app/view/comuns/cabecalho.php'; ?>

  <!-- PAGE HEADER (breadcrumb enxuto) -->
  <div class="page-header-slim">
    <div class="container">
      <nav class="breadcrumb-nav">
        <a href="<?= baseUrl() ?>" style="color:#4ade80">Início</a>
        <span style="color:#475569">/</span>
        <span style="color:#94a3b8">Pontos de Descarte</span>
      </nav>
    </div>
  </div>

  <!-- BANNER DE CONSCIENTIZAÇÃO -->
  <div class="container">
    <div class="eco-banner">
      <div class="eco-banner-img" style="background-image:url('<?= baseUrl('assets/img/poluicao-residuos.jpg') ?>')" role="img" aria-label="Resíduos descartados de forma incorreta no ambiente"></div>
      <div class="eco-banner-text">
        <span class="eco-banner-tag">🌍 Por que entregar no lugar certo?</span>
        <h2>Eletrônicos, pilhas e lâmpadas não são lixo comum</h2>
        <p>Quando descartados no lixo normal, liberam <strong>metais pesados</strong> como mercúrio, chumbo e cádmio. Eles contaminam o <strong>solo e os lençóis freáticos</strong>, chegando à água que bebemos e aos alimentos que comemos — uma única pilha pode poluir milhares de litros de água.</p>
        <p class="eco-banner-cta">Levar esses materiais a um ponto de entrega voluntária garante o tratamento adequado e protege a saúde de toda a comunidade. 👇</p>
      </div>
    </div>
  </div>

  <!-- BUSCA E FILTROS -->
  <div class="container">
    <div class="filtros-avancados">
      <div class="busca-wrap">
        <span class="busca-icone">🔍</span>
        <input type="text" id="buscaPonto" placeholder="Buscar por nome, bairro ou material aceito..." oninput="filtrarPontos()">
        <button class="busca-clear" onclick="limparBusca()" id="buscaClear" style="display:none;">✕</button>
      </div>
      <div class="filtros-chips">
        <button class="chip active" onclick="filtrarMaterial('todos', this)">Todos</button>
        <button class="chip" onclick="filtrarMaterial('pilhas', this)">🔋 Pilhas</button>
        <button class="chip" onclick="filtrarMaterial('eletronicos', this)">📱 Eletrônicos</button>
        <button class="chip" onclick="filtrarMaterial('oleo', this)">🫙 Óleo</button>
        <button class="chip" onclick="filtrarMaterial('medicamentos', this)">💊 Medicamentos</button>
        <button class="chip" onclick="filtrarMaterial('papel', this)">📄 Papel/Papelão</button>
        <button class="chip" onclick="filtrarMaterial('plastico', this)">🧴 Plástico</button>
      </div>
      <div class="filtros-chips" style="margin-top:6px">
        <button class="chip chip-status active-green" onclick="filtrarStatus('todos', this)">Todos</button>
        <button class="chip chip-status" onclick="filtrarStatus('aberto', this)">🟢 Abertos agora</button>
        <button class="chip chip-status" onclick="filtrarStatus('fechado', this)">🔴 Fechados</button>
      </div>
    </div>
  </div>

  <!-- GRID DE PONTOS (gerado a partir do banco) -->
  <div class="container" id="pontosContainer">
    <div class="pontos-rich-grid" id="pontosGrid">

      <?php
      $pontos = $pontos ?? [];
      $corClasse = [
          'verde'    => 'prc-foto-verde',
          'azul'     => 'prc-foto-azul',
          'laranja'  => 'prc-foto-laranja',
          'vermelho' => 'prc-foto-vermelho',
          'roxo'     => 'prc-foto-roxo',
      ];
      $iconeMaterial = [
          'Papel e Papelão' => '📄', 'Plástico' => '🧴', 'Metal' => '🥫', 'Vidro' => '🍶',
          'Pilhas e Baterias' => '🔋', 'Medicamentos' => '💊', 'Materiais Perfurocortantes' => '💉',
          'Lixo Orgânico' => '🍎',
      ];
      $hojeIso   = (int) date('N');            // 1=segunda..7=domingo
      $hojeBanco = $hojeIso === 7 ? 1 : $hojeIso + 1; // 1=domingo..7=sabado
      ?>

      <?php foreach ($pontos as $p): ?>
        <?php
        $id         = (int) $p['id_ponto'];
        $aberto     = $p['status_funcionamento'] === 'ABERTO';
        $classeFoto = $corClasse[$p['cor']] ?? 'prc-foto-verde';
        $materiais  = $p['materiais'] ?? [];
        $dataMaterial = strtolower(implode(' ', array_map(static fn($m) => str_replace(' ', '', $m), $materiais)));
        $dataNome   = strtolower($p['nome']);
        $horarioHoje = '–';
        foreach (($p['horarios'] ?? []) as $h) {
            if ((int) $h['dia_semana'] === $hojeBanco) {
                $horarioHoje = $h['fechado']
                    ? 'Fechado'
                    : formataHora($h['horario_inicio']) . ' – ' . formataHora($h['horario_fim']);
            }
        }
        $enderecoMaps = rawurlencode($p['endereco'] . ' Muriae MG');
        ?>
        <div class="ponto-rich-card" id="ponto<?= $id ?>"
             data-nome="<?= htmlspecialchars($dataNome, ENT_QUOTES) ?>"
             data-material="<?= htmlspecialchars($dataMaterial, ENT_QUOTES) ?>"
             data-status="<?= $aberto ? 'aberto' : 'fechado' ?>"
             data-lat="<?= htmlspecialchars((string) $p['latitude'], ENT_QUOTES) ?>"
             data-lng="<?= htmlspecialchars((string) $p['longitude'], ENT_QUOTES) ?>"
             data-endereco="<?= htmlspecialchars($p['endereco'], ENT_QUOTES) ?>">

          <div class="prc-header" onclick="togglePonto('ponto<?= $id ?>')">
            <div class="prc-foto-wrap">
              <div class="prc-foto <?= $classeFoto ?>"><span><?= $p['icone'] ?></span></div>
              <span class="prc-status-dot <?= $aberto ? 'aberto' : 'fechado' ?>"></span>
            </div>
            <div class="prc-info">
              <div class="prc-title-row">
                <h3><?= htmlspecialchars($p['nome']) ?></h3>
                <span class="prc-status <?= $aberto ? 'aberto' : 'fechado' ?>"><?= $aberto ? '🟢 Aberto' : '🔴 Fechado agora' ?></span>
              </div>
              <p class="prc-endereco">📍 <?= htmlspecialchars($p['endereco']) ?></p>
              <div class="prc-materiais-preview">
                <?php foreach ($materiais as $mat): ?>
                  <span class="mat-chip"><?= ($iconeMaterial[$mat] ?? '♻️') . ' ' . htmlspecialchars($mat) ?></span>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="prc-horario-resumo">
              <span class="prc-hoje-label">Hoje</span>
              <span class="prc-hoje-hora"><?= htmlspecialchars($horarioHoje) ?></span>
            </div>
            <button class="prc-toggle-btn" id="btn-ponto<?= $id ?>">▼</button>
          </div>

          <div class="prc-expanded" id="exp-ponto<?= $id ?>" style="display:none;">
            <div class="prc-expanded-inner">

              <div class="prc-gallery">
                <div class="gallery-main">
                  <div class="gallery-placeholder">
                    <span><?= $p['icone'] ?></span>
                    <p><?= htmlspecialchars($p['nome']) ?></p>
                    <?php if (!empty($p['fotos'][0]['legenda'])): ?>
                      <small><?= htmlspecialchars($p['fotos'][0]['legenda']) ?></small>
                    <?php endif; ?>
                  </div>
                </div>
              </div>

              <?php if (!empty($p['observacao'])): ?>
                <div class="alert-box info-alert">ℹ️ <?= htmlspecialchars($p['observacao']) ?></div>
              <?php endif; ?>

              <div class="prc-details-grid">

                <div class="prc-detail-section">
                  <h4>🕐 Horários de Funcionamento</h4>
                  <table class="horario-table">
                    <tbody>
                      <?php foreach (($p['horarios'] ?? []) as $h): ?>
                        <?php $ehHoje = (int) $h['dia_semana'] === $hojeBanco; ?>
                        <tr<?= $ehHoje ? ' class="hoje-row"' : '' ?>>
                          <td><?= nomeDiaSemana((int) $h['dia_semana']) ?></td>
                          <td><strong><?= $h['fechado'] ? '–' : formataHora($h['horario_inicio']) . ' – ' . formataHora($h['horario_fim']) ?></strong></td>
                          <td><span class="hst <?= $h['fechado'] ? 'fechado' : 'aberto' ?>"><?= $h['fechado'] ? 'Fechado' : 'Aberto' ?></span></td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>

                <div class="prc-detail-section">
                  <h4>✅ Materiais Aceitos</h4>
                  <div class="materiais-aceitos-list">
                    <?php foreach ($materiais as $mat): ?>
                      <div class="ma-item aceito"><span><?= $iconeMaterial[$mat] ?? '♻️' ?></span> <?= htmlspecialchars($mat) ?></div>
                    <?php endforeach; ?>
                  </div>
                </div>

                <div class="prc-detail-section">
                  <h4>📞 Contato &amp; Informações</h4>
                  <div class="contato-list">
                    <?php if (!empty($p['telefone'])): ?>
                      <div class="ct-item"><span>📞</span> <?= htmlspecialchars($p['telefone']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($p['email'])): ?>
                      <div class="ct-item"><span>📧</span> <?= htmlspecialchars($p['email']) ?></div>
                    <?php endif; ?>
                    <?php if ($p['acessivel_pcd']): ?>
                      <div class="ct-item"><span>♿</span> Acessível para PCD</div>
                    <?php endif; ?>
                    <?php if ($p['tem_estacionamento']): ?>
                      <div class="ct-item"><span>🅿️</span> Estacionamento disponível</div>
                    <?php endif; ?>
                  </div>
                  <?php if ($p['avaliacao_media'] !== null): ?>
                    <div class="prc-avaliacao">
                      <span>⭐ <?= number_format((float) $p['avaliacao_media'], 1, ',', '') ?></span>
                      <span class="av-count">(<?= (int) $p['avaliacao_total'] ?> avaliações)</span>
                    </div>
                  <?php endif; ?>
                  <div class="prc-action-btns">
                    <?php if (!empty($p['whatsapp'])): ?>
                      <a href="https://wa.me/<?= htmlspecialchars($p['whatsapp']) ?>" target="_blank" rel="noopener noreferrer" class="act-btn whatsapp">💬 WhatsApp</a>
                    <?php endif; ?>
                    <button class="act-btn compartilhar" onclick="compartilhar('<?= htmlspecialchars($p['nome'], ENT_QUOTES) ?>', '<?= htmlspecialchars($p['endereco'], ENT_QUOTES) ?>')">🔗 Compartilhar</button>
                  </div>
                </div>

              </div>

              <div class="prc-map-section">
                <div class="prc-map-header">
                  <h4>🗺️ Localização</h4>
                  <a href="https://www.google.com/maps/search/<?= $enderecoMaps ?>" target="_blank" rel="noopener noreferrer" class="open-maps-btn">Abrir no Google Maps ↗</a>
                </div>
                <div class="prc-map-embed">
                  <iframe src="https://maps.google.com/maps?q=<?= $enderecoMaps ?>&output=embed&z=16" width="100%" height="280" style="border:0;border-radius:12px;" loading="lazy"></iframe>
                </div>
                <div class="prc-como-chegar">
                  <a href="https://www.google.com/maps/dir//<?= $enderecoMaps ?>" target="_blank" rel="noopener noreferrer" class="como-chegar-btn">🧭 Como chegar de carro</a>
                  <a href="https://www.google.com/maps/dir//<?= $enderecoMaps ?>&travelmode=transit" target="_blank" rel="noopener noreferrer" class="como-chegar-btn">🚌 Ônibus</a>
                  <a href="https://www.google.com/maps/dir//<?= $enderecoMaps ?>&travelmode=walking" target="_blank" rel="noopener noreferrer" class="como-chegar-btn">🚶 A pé</a>
                </div>
              </div>

            </div>
          </div>
        </div>
      <?php endforeach; ?>
      <?php /* FIM_LOOP_PONTOS */ ?>

    </div><!-- fim grid -->

    <div id="semResultados" style="display:none;" class="sem-resultados-rich">
      <span>😕</span>
      <p>Nenhum ponto encontrado para esta busca.</p>
      <button onclick="limparBusca()" class="btn-outline-green">Limpar filtros</button>
    </div>

  </div><!-- fim container -->

  <!-- MAPA GERAL INTERATIVO (Leaflet + OpenStreetMap) -->
  <section class="mapa-geral-section">
    <div class="container">
      <div class="mapa-geral-header">
        <h2>🗺️ Todos os pontos no mapa</h2>
        <a href="https://www.google.com/maps/search/pontos+de+reciclagem+Muriae+MG" target="_blank" rel="noopener noreferrer" class="btn-outline-green">Ver no Google Maps ↗</a>
      </div>

      <div class="mapa-legenda">
        <span class="mapa-leg-item"><span class="leg-dot aberto"></span> Aberto agora</span>
        <span class="mapa-leg-item"><span class="leg-dot fechado"></span> Fechado agora</span>
        <span class="mapa-leg-hint">💡 Clique em um marcador para ver os detalhes do ponto</span>
      </div>

      <div id="mapaInterativo" class="mapa-interativo" role="application" aria-label="Mapa dos pontos de coleta em Muriaé"></div>
    </div>
  </section>

  <!-- SUGIRA UM PONTO -->
  <section class="container sugira-section" id="sugira">
    <?php if (!empty($sugestaoOk)): ?>
      <div style="background:#dcfce7;color:#166534;border:1px solid #86efac;border-radius:12px;padding:14px 18px;margin-bottom:16px;font-weight:600;">
        ✅ Sugestão enviada! Nossa equipe vai verificar o local. Obrigado pela contribuição.
      </div>
    <?php elseif (!empty($sugestaoErro)): ?>
      <div style="background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;border-radius:12px;padding:14px 18px;margin-bottom:16px;font-weight:600;">
        ⚠️ <?= htmlspecialchars($sugestaoErro) ?>
      </div>
    <?php endif; ?>
    <div class="sugira-card">
      <div class="sugira-text">
        <h3>Conhece um ponto de descarte que não está aqui?</h3>
        <p>Ajude a comunidade sugerindo novos locais de descarte. Nossa equipe vai verificar e adicionar!</p>
      </div>
      <button class="btn-primary" onclick="abrirModalSugira()">+ Sugerir novo ponto</button>
    </div>
  </section>

  <!-- MODAL SUGERIR -->
  <div id="modalSugira" class="modal" style="display:none;">
    <div class="modal-content" style="text-align:left; max-width:500px;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
        <h3>📍 Sugerir novo ponto</h3>
        <button type="button" onclick="document.getElementById('modalSugira').style.display='none'" style="background:none;border:none;font-size:1.3rem;cursor:pointer;">✕</button>
      </div>
      <form method="post" action="<?= baseUrl('ponto/sugerir') ?>">
        <div class="form-group"><label>Nome do local *</label><input type="text" name="nome_local" placeholder="Ex: Ecoponto Vila Rica" required></div>
        <div class="form-group"><label>Endereço *</label><input type="text" name="endereco" placeholder="Rua, número – Bairro" required></div>
        <div class="form-group"><label>Materiais aceitos</label><input type="text" name="materiais" placeholder="Ex: Pilhas, eletrônicos, papel..."></div>
        <div class="form-group"><label>Horário de funcionamento</label><input type="text" name="horario" placeholder="Ex: Seg-Sex 08h-17h"></div>
        <div class="form-group"><label>Observações</label><textarea name="observacao" rows="3" placeholder="Qualquer informação adicional..."></textarea></div>
        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:16px;">
          <button type="submit" class="btn-primary">Enviar sugestão</button>
          <button type="button" class="btn-secondary" onclick="document.getElementById('modalSugira').style.display='none'">Cancelar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- TOAST -->
  <div id="toastMsg" style="display:none;position:fixed;bottom:24px;right:24px;background:#1e293b;color:#fff;padding:14px 22px;border-radius:12px;font-weight:600;font-size:.9rem;z-index:9999;box-shadow:0 4px 16px rgba(0,0,0,.2);"></div>

<?php require_once 'app/view/comuns/rodape.php'; ?>
