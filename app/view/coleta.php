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
          <p>Informe seu endereço e veja os dias, horários, materiais aceitos e um calendário completo da coleta seletiva na sua região.</p>
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
          <span class="proxima-label">Próxima coleta</span>
          <span class="proxima-dia" id="proximaDia">Sexta-feira</span>
          <span class="proxima-data" id="proximaData">22/08/2026</span>
        </div>
        <div class="rhr-countdown" id="countdown">
          <span class="cd-label">Faltam</span>
          <span class="cd-value" id="cdValue">2</span>
          <span class="cd-unit" id="cdUnit">dias</span>
        </div>
        <button class="share-coleta-btn" onclick="compartilharColeta()" title="Compartilhar">🔗 Compartilhar</button>
      </div>

      <!-- ALERTA PRÓXIMA COLETA -->
      <div class="alerta-proxima" id="alertaProxima">
        ⚠️ <strong>Lembre-se:</strong> Coloque os materiais na calçada até as <strong>06h30</strong> na manhã do dia da coleta.
        <button class="btn-notificar" onclick="ativarNotificacao()">🔔 Me lembrar</button>
      </div>

      <!-- GRID DE INFORMAÇÕES RICAS -->
      <div class="resultado-rich-grid">

        <!-- COLUNA 1: DIAS E HORÁRIOS -->
        <div class="resultado-card-rich">
          <h3>📅 Dias e Horários</h3>
          <div class="dias-semana-visual" id="diasSemana"></div>
          <div class="horario-rich">
            <div class="hr-item">
              <span class="hr-icon">🕐</span>
              <div>
                <strong>Horário</strong>
                <p id="horarioInfo">07h às 12h</p>
              </div>
            </div>
            <div class="hr-item">
              <span class="hr-icon">🚛</span>
              <div>
                <strong>Equipe responsável</strong>
                <p>Cooperativa ReciclaVida</p>
              </div>
            </div>
            <div class="hr-item">
              <span class="hr-icon">📞</span>
              <div>
                <strong>Contato da equipe</strong>
                <p>(32) 3333-7700</p>
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
              <p>Deixe tudo na calçada em frente à sua casa até 06h30</p>
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
          <span class="leg-item"><span class="leg-dot coleta-dia"></span>Dia de coleta</span>
          <span class="leg-item"><span class="leg-dot hoje-dot"></span>Hoje</span>
          <span class="leg-item"><span class="leg-dot proximo-dot"></span>Próxima coleta</span>
        </div>
      </div>

      <!-- MAPA DA REGIÃO -->
      <div class="mapa-resultado-section">
        <div class="mapa-resultado-header">
          <h3>🗺️ Sua região no mapa</h3>
          <a id="linkMapaEndereco" href="#" target="_blank" class="open-maps-btn">Abrir no Google Maps ↗</a>
        </div>
        <div id="mapaResultado" class="mapa-resultado-embed">
          <!-- preenchido por JS -->
        </div>
        <div class="mapa-resultado-info">
          <div class="mri-item">
            <span>📍</span>
            <div>
              <strong>Ponto de descarte mais próximo</strong>
              <p>Ecoponto Centro – Rua das Flores, 123 (0,3km)</p>
            </div>
            <a href="<?= baseUrl('ponto') ?>#ponto1" class="mri-link">Ver →</a>
          </div>
          <div class="mri-item">
            <span>🏥</span>
            <div>
              <strong>Para materiais especiais</strong>
              <p>UBS Central – Rua da Saúde, 10 (0,8km)</p>
            </div>
            <a href="<?= baseUrl('ponto') ?>#ponto4" class="mri-link">Ver →</a>
          </div>
        </div>
      </div>

      <!-- NOTIFICAÇÃO MODAL -->
      <div id="modalNotif" class="modal" style="display:none;">
        <div class="modal-content">
          <div class="modal-icon">🔔</div>
          <h3>Lembrete configurado!</h3>
          <p>Você receberá um alerta <strong>na véspera</strong> de cada coleta no seu bairro.</p>
          <p style="font-size:0.8rem;color:#94a3b8;">(Funcionalidade simulada – em breve disponível no app)</p>
          <button class="btn-primary" onclick="document.getElementById('modalNotif').style.display='none'">Entendido!</button>
        </div>
      </div>

    </div>
  </div>

  <!-- TABELA GERAL DE HORÁRIOS -->
  <section class="tabela-geral-section">
    <div class="container">
      <div class="tg-header">
        <h2>📋 Horários por Bairro</h2>
        <input type="text" id="buscaBairro" placeholder="Filtrar por bairro..." oninput="filtrarBairros()" class="tg-busca">
      </div>
      <div class="tabela-geral-wrap">
        <table class="tabela-geral" id="tabelaGeral">
          <thead>
            <tr>
              <th>Bairro / Região</th>
              <th>Dias da Coleta</th>
              <th>Horário</th>
              <th>Materiais</th>
              <th>Equipe</th>
              <th>Ação</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $bairros = $bairros ?? [];
            // remove acentos para o data-bairro (usado pelo filtro JS)
            $semAcento = static function (string $s): string {
                $t = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
                return strtolower(trim((string) $t));
            };
            ?>
            <?php foreach ($bairros as $b): ?>
              <?php
              $mats = $b['materiais'] ?? [];
              // nomes curtos para a coluna de materiais (remove "e Papelão")
              $matsCurto = array_map(static fn($m) => $m === 'Papel e Papelão' ? 'Papel' : $m, $mats);
              ?>
              <tr data-bairro="<?= htmlspecialchars($semAcento($b['nome']), ENT_QUOTES) ?>">
                <td><strong><?= htmlspecialchars($b['nome']) ?></strong></td>
                <td>
                  <?php foreach ($b['dias'] as $d): ?>
                    <span class="dia-badge"><?= abreviaDiaSemana((int) $d) ?></span>
                  <?php endforeach; ?>
                </td>
                <td><?= formataHora($b['inicio']) ?> – <?= formataHora($b['fim']) ?></td>
                <td><?= htmlspecialchars(implode(', ', $matsCurto)) ?></td>
                <td><?= htmlspecialchars($b['equipe']) ?></td>
                <td><button class="btn-ver-coleta" onclick="consultarBairro(<?= (int) $b['id_bairro'] ?>)">Ver detalhes</button></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <script>
    // Base URL do servidor (usada por coleta.js para a consulta real por bairro).
    window.BASEURL = <?= json_encode(baseUrl(), JSON_UNESCAPED_SLASHES) ?>;
  </script>

<?php require_once 'app/view/comuns/rodape.php'; ?>
