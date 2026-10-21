<?php require_once 'app/view/comuns/cabecalho.php'; ?>

  <!-- HERO COLETA -->
  <section class="coleta-hero">
    <div class="container">
      <div class="coleta-hero-inner">
        <div class="coleta-hero-text">
          <nav class="breadcrumb-nav" style="margin-bottom:10px">
            <a href="<?= baseUrl() ?>" style="color:#4ade80">Início</a> <span style="color:#475569">/</span>
            <span style="color:#94a3b8">Consultar Coleta</span>
          </nav>
          <h1>🗓️ Quando passa a coleta<br>na <span style="color:#4ade80">sua rua?</span></h1>
          <p>Escolha o seu bairro e veja os dias e horários da coleta comum e da coleta seletiva, os materiais aceitos e o calendário do mês.</p>
        </div>

        <!-- FORMULÁRIO PRINCIPAL -->
        <div class="coleta-form-card">
          <h3>Consultar minha coleta</h3>
          <form onsubmit="consultarColeta(event)">
            <div class="form-group">
              <label>Selecione o seu bairro</label>
              <div class="input-with-icon">
                <span>📍</span>
                <select id="bairroSelect" required>
                  <option value="">Escolha o bairro...</option>
                  <?php foreach (($bairrosSelect ?? []) as $b): ?>
                    <option value="<?= (int) $b['id_bairro'] ?>"><?= htmlspecialchars($b['nome']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <button type="submit" class="btn-consultar" id="btnConsultar">
              <span id="btnText">🔍 Consultar coleta</span>
              <span id="btnLoad" style="display:none">⏳ Buscando...</span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>

  <!-- RESULTADO DA CONSULTA -->
  <div id="resultadoSection" style="display:none;">
    <div class="container">

      <!-- HEADER DO RESULTADO -->
      <div class="resultado-header-rich">
        <div class="rhr-left">
          <div class="rhr-icon">🏘️</div>
          <div>
            <h2 id="resultadoBairroTitulo">Bairro Centro</h2>
            <p id="resultadoEndereco">Rua das Flores, 123 – Centro</p>
          </div>
        </div>
        <div class="rhr-proxima">
          <span class="proxima-label" id="proximaLabel">Próxima coleta</span>
          <span class="proxima-dia" id="proximaDia">–</span>
          <span class="proxima-data" id="proximaData"></span>
        </div>
        <div class="rhr-countdown" id="countdown">
          <span class="cd-label">Faltam</span>
          <span class="cd-value" id="cdValue">–</span>
          <span class="cd-unit" id="cdUnit">dias</span>
        </div>
        <button type="button" class="share-coleta-btn" onclick="compartilharColeta()" title="Compartilhar">🔗 Compartilhar</button>
      </div>

      <!-- BAIRRO SEM HORÁRIO DIVULGADO -->
      <div class="sem-cronograma-box" id="semCronograma" hidden>
        <strong>O DEMSUR ainda não divulga o horário deste bairro.</strong>
        <p>Para saber quando o caminhão passa, fale com o <?= htmlspecialchars($fonteColeta['orgao'] ?? 'DEMSUR') ?> pelo
          <a href="tel:<?= preg_replace('/\D/', '', $fonteColeta['telefone'] ?? '') ?>"><?= htmlspecialchars($fonteColeta['telefone'] ?? '') ?></a>.
          Enquanto isso, os recicláveis podem ir para um <a href="<?= baseUrl('ponto') ?>">ponto de descarte</a>.</p>
      </div>

      <!-- ALERTA PRÓXIMA COLETA (texto montado pelo coleta.js com o horário real) -->
      <div class="alerta-proxima" id="alertaProxima">
        ⚠️ <strong>Lembre-se:</strong> coloque os sacos na calçada pouco antes do horário de passagem do caminhão.
      </div>

      <!-- GRID DE INFORMAÇÕES RICAS -->
      <div class="resultado-rich-grid">

        <!-- COLUNA 1: DIAS E HORÁRIOS -->
        <div class="resultado-card-rich">
          <h3>📅 Dias e Horários</h3>
          <div class="dias-semana-visual" id="diasSemana" aria-label="Dias de coleta na semana"></div>
          <p class="dsv-legenda"><span class="dsv-leg seletiva"></span>Coleta seletiva <span class="dsv-leg comum"></span>Coleta comum</p>
          <!-- Horários por tipo e por trecho do bairro (preenchido pelo coleta.js) -->
          <div class="horarios-coleta" id="horariosColeta"></div>
          <div class="horario-rich">
            <div class="hr-item">
              <span class="hr-icon">🚛</span>
              <div>
                <strong>Responsável</strong>
                <p id="equipeInfo">–</p>
              </div>
            </div>
            <div class="hr-item">
              <span class="hr-icon">📞</span>
              <div>
                <strong>Contato</strong>
                <p id="telefoneEquipe">–</p>
              </div>
            </div>
          </div>
        </div>

        <!-- COLUNA 2: MATERIAIS -->
        <div class="resultado-card-rich">
          <h3>♻️ O que é recolhido</h3>
          <div class="materiais-recolhidos" id="materiaisRecolhidos">
            <div class="mr-item aceito"><div class="mr-cor azul"></div><span>📄 Papel e Papelão</span></div>
            <div class="mr-item aceito"><div class="mr-cor vermelho"></div><span>🧴 Plástico</span></div>
            <div class="mr-item aceito"><div class="mr-cor amarelo"></div><span>🥫 Metal/Latas</span></div>
            <div class="mr-item aceito"><div class="mr-cor verde"></div><span>🍶 Vidro</span></div>
          </div>
          <div class="nao-recolhidos">
            <h4>❌ Não recolhidos nesta coleta</h4>
            <div class="nr-chips">
              <span>🍎 Orgânico</span>
              <span>🔋 Pilhas</span>
              <span>💊 Medicamentos</span>
              <span>💉 Perfurocortantes</span>
            </div>
            <a href="<?= baseUrl('ponto') ?>" class="link-pontos">Ver pontos para esses materiais →</a>
          </div>
        </div>

        <!-- COLUNA 3: DICAS PREPARO -->
        <div class="resultado-card-rich">
          <h3>💡 Como preparar</h3>
          <div class="dicas-preparo">
            <div class="dica-item">
              <span class="dica-num">1</span>
              <p>Lave as embalagens plásticas e de vidro antes de descartar</p>
            </div>
            <div class="dica-item">
              <span class="dica-num">2</span>
              <p>Amasse garrafas PET e latas para economizar espaço</p>
            </div>
            <div class="dica-item">
              <span class="dica-num">3</span>
              <p>Separe os materiais em sacos diferentes por tipo</p>
            </div>
            <div class="dica-item">
              <span class="dica-num">4</span>
              <p>Deixe os recicláveis na calçada pouco antes do horário da coleta seletiva do seu bairro</p>
            </div>
            <div class="dica-item danger">
              <span class="dica-num red">!</span>
              <p>Vidros quebrados: embrulhe em jornal e escreva "CUIDADO VIDRO"</p>
            </div>
          </div>
        </div>

      </div>

      <!-- CALENDÁRIO MENSAL -->
      <div class="calendario-section">
        <div class="calendario-header">
          <h3>📆 Calendário da Coleta – <span id="calMesAno"></span></h3>
          <div style="display:flex;gap:8px;align-items:center;">
            <button onclick="window.print()" class="btn-print" title="Imprimir calendário">🖨️ Imprimir</button>
            <div class="cal-nav">
              <button onclick="navegarMes(-1)">←</button>
              <button onclick="navegarMes(1)">→</button>
            </div>
          </div>
        </div>
        <div class="calendario-wrap" id="calendarioWrap"></div>
        <div class="calendario-legenda">
          <span class="leg-item"><span class="leg-dot coleta-dia"></span>Coleta seletiva</span>
          <span class="leg-item"><span class="leg-dot comum-dot"></span>Coleta comum</span>
          <span class="leg-item"><span class="leg-dot hoje-dot"></span>Hoje</span>
          <span class="leg-item"><span class="leg-dot proximo-dot"></span>Próxima coleta</span>
        </div>
      </div>

      <!-- MAPA DA REGIÃO -->
      <div class="mapa-resultado-section">
        <div class="mapa-resultado-header">
          <h3>🗺️ Sua região no mapa</h3>
          <a id="linkMapaEndereco" href="#" target="_blank" rel="noopener noreferrer" class="open-maps-btn">Abrir no Google Maps ↗</a>
        </div>
        <div id="mapaResultado" class="mapa-resultado-embed">
          <!-- preenchido por JS -->
        </div>
        <div class="mapa-resultado-info">
          <div class="mri-item">
            <div>
              <strong>Encontre pontos próximos</strong>
              <p>Consulte os pontos de descarte ativos cadastrados em Muriaé.</p>
            </div>
            <a href="<?= baseUrl('ponto') ?>" class="mri-link">Ver pontos</a>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- TABELA GERAL DE HORÁRIOS -->
  <section class="tabela-geral-section">
    <div class="container">
      <?php
      $bairros = $bairros ?? [];
      $resumo  = $resumoColeta ?? ['bairros' => count($bairros), 'comum' => 0, 'seletiva' => 0];
      $fonte   = $fonteColeta ?? ['orgao' => 'DEMSUR', 'telefone' => '', 'url' => ''];
      // remove acentos para o data-bairro (usado pelo filtro JS).
      // Mapa fixo: o iconv//TRANSLIT do Windows gera "?" no lugar das letras acentuadas.
      $semAcento = static function (string $s): string {
          $t = strtr(mb_strtolower(trim($s), 'UTF-8'), [
              'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
              'é' => 'e', 'ê' => 'e', 'è' => 'e', 'ë' => 'e',
              'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
              'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ò' => 'o', 'ö' => 'o',
              'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'ç' => 'c',
          ]);
          return $t;
      };
      /** Chips "Seg e Qua 08h" de um trecho. */
      $chips = static function (array $dias, bool $aPartir, string $classe): string {
          $html = '';
          foreach (agrupaDiasColeta($dias, $aPartir) as $g) {
              $html .= '<span class="hc-chip ' . $classe . '"><b>' . htmlspecialchars($g['rotulo']) . '</b> '
                     . htmlspecialchars($g['horario']) . '</span>';
          }
          return $html;
      };
      /** Lista de trechos (bairro todo primeiro, depois as partes com horário próprio). */
      $trechos = static function (array $grupos, bool $aPartir, string $classe) use ($chips): string {
          $html = '';
          foreach ($grupos as $g) {
              $html .= '<div class="hc-linha">';
              if ($g['trecho'] !== '') {
                  $html .= '<span class="hc-trecho">' . htmlspecialchars($g['trecho']) . '</span>';
              }
              $html .= '<span class="hc-chips">' . $chips($g['dias'], $aPartir, $classe) . '</span></div>';
          }
          return $html;
      };
      ?>
      <div class="tg-header">
        <div>
          <h2>📋 Horários por Bairro</h2>
          <p class="tg-resumo">
            <?= (int) $resumo['bairros'] ?> bairros ·
            <?= (int) $resumo['comum'] ?> com horário da coleta comum ·
            <?= (int) $resumo['seletiva'] ?> com coleta seletiva porta a porta
          </p>
        </div>
        <div class="tg-filtros">
          <input type="text" id="buscaBairro" placeholder="Filtrar por bairro..." oninput="filtrarBairros()" class="tg-busca" aria-label="Filtrar por bairro">
          <select id="filtroSeletiva" onchange="filtrarBairros()" class="tg-busca tg-select" aria-label="Filtrar por tipo de coleta">
            <option value="">Todos os bairros</option>
            <option value="1">Com coleta seletiva</option>
            <option value="0">Sem coleta seletiva</option>
          </select>
        </div>
      </div>
      <div class="tabela-geral-wrap">
        <table class="tabela-geral" id="tabelaGeral">
          <thead>
            <tr>
              <th>Bairro / Região</th>
              <th>Coleta seletiva <small>(recicláveis)</small></th>
              <th>Coleta comum <small>(lixo doméstico)</small></th>
              <th><span class="sr-only">Ação</span></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($bairros as $b): ?>
              <?php
              $temSeletiva = !empty($b['seletiva']);
              $temComum    = !empty($b['comum']);
              ?>
              <tr data-bairro="<?= htmlspecialchars($semAcento($b['nome']), ENT_QUOTES) ?>" data-seletiva="<?= $temSeletiva ? '1' : '0' ?>">
                <td><strong><?= htmlspecialchars($b['nome']) ?></strong></td>
                <?php if (!$temSeletiva && !$temComum): ?>
                  <td colspan="2" class="hc-sem-dados">
                    Horário ainda não divulgado pelo <?= htmlspecialchars($fonte['orgao']) ?>.
                    Informe-se pelo <a href="tel:<?= preg_replace('/\D/', '', $fonte['telefone']) ?>"><?= htmlspecialchars($fonte['telefone']) ?></a>.
                  </td>
                <?php else: ?>
                  <td>
                    <?php if ($temSeletiva): ?>
                      <?= $trechos($b['seletiva'], true, 'seletiva') ?>
                    <?php else: ?>
                      <span class="hc-vazio">Sem coleta seletiva porta a porta.
                        <a href="<?= baseUrl('ponto') ?>">Ver pontos de descarte</a></span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?= $temComum ? $trechos($b['comum'], false, 'comum') : '<span class="hc-vazio">Não divulgado</span>' ?>
                  </td>
                <?php endif; ?>
                <td><button type="button" class="btn-ver-coleta" onclick="consultarBairro(<?= (int) $b['id_bairro'] ?>)">Ver detalhes</button></td>
              </tr>
            <?php endforeach; ?>
            <tr id="tgVazio" hidden><td colspan="4" class="hc-sem-dados">Nenhum bairro encontrado com esse filtro.</td></tr>
          </tbody>
        </table>
      </div>
      <p class="tg-fonte">
        Fonte: <a href="<?= htmlspecialchars($fonte['url'], ENT_QUOTES) ?>" target="_blank" rel="noopener noreferrer">cronograma de coleta do <?= htmlspecialchars($fonte['orgao']) ?></a> (outubro/2026).
        Na coleta comum, o horário é o de passagem aproximada do caminhão e pode variar alguns minutos.
        Mudou algo no seu bairro? Fale com o <?= htmlspecialchars($fonte['orgao']) ?> pelo <?= htmlspecialchars($fonte['telefone']) ?>.
      </p>
    </div>
  </section>

  <script>
    // Base URL do servidor (usada por coleta.js para a consulta real por bairro).
    window.BASEURL = <?= json_encode(baseUrl(), JSON_UNESCAPED_SLASHES) ?>;
  </script>

<?php require_once 'app/view/comuns/rodape.php'; ?>
