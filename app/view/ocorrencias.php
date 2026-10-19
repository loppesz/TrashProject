<?php require_once 'app/view/comuns/cabecalho.php'; ?>

  <!-- HEADER -->
  <div class="page-header-slim">
    <div class="container">
      <nav class="breadcrumb-nav">
        <a href="<?= baseUrl() ?>" style="color:#4ade80">Início</a>
        <span style="color:#475569">/</span>
        <span style="color:#94a3b8">Ocorrências</span>
      </nav>
    </div>
  </div>

  <!-- CONSCIENTIZAÇÃO CÍVICA -->
  <div class="container">
    <div class="civica-banner">
      <div class="civica-img" style="background-image:url('<?= baseUrl('assets/img/lixo-urbano.jpg') ?>')" role="img" aria-label="Lixo acumulado em via pública"></div>
      <div class="civica-text">
        <span class="civica-tag">🏙️ Cidadania ativa</span>
        <h2>O lixo no lugar errado volta pra todo mundo</h2>
        <p>O descarte irregular em terrenos, calçadas e esquinas <strong>entope bueiros</strong>, provoca <strong>enchentes</strong> nas chuvas, atrai pragas e degrada o espaço público que é de todos.</p>
        <p class="civica-cta">Ao registrar uma ocorrência, você aciona a limpeza, protege sua rua e participa da fiscalização colaborativa da cidade. 👇</p>
      </div>
    </div>
  </div>

  <!-- ABAS -->
  <div class="container">
    <div class="oc-tabs">
      <button class="oc-tab active" onclick="switchTab('registrar', this)">✏️ Registrar Ocorrência</button>
      <button class="oc-tab" onclick="switchTab('consultar', this)">🔍 Consultar Status</button>
      <button class="oc-tab" onclick="switchTab('historico', this)">📋 Histórico Público</button>
      <button class="oc-tab" onclick="switchTab('mapa', this)">🗺️ Mapa de Ocorrências</button>
    </div>
  </div>

  <!-- ============================================================
       ABA: REGISTRAR
  ============================================================ -->
  <div id="tab-registrar" class="tab-content active-tab">
    <div class="container oc-registrar-grid">

      <!-- FORMULÁRIO -->
      <div class="oc-form-section">
        <div class="oc-form-card">
          <div class="oc-form-header">
            <h2>📝 Registrar nova ocorrência</h2>
            <div class="pontos-badge">+50 pts ao registrar</div>
          </div>

          <?php if (!empty($erroRegistro)): ?>
            <div class="login-error" role="alert" style="margin-bottom:16px;">⚠️ <?= htmlspecialchars($erroRegistro) ?></div>
          <?php endif; ?>

          <form id="ocorrenciaForm" method="POST" action="<?= baseUrl('ocorrencia/registrar') ?>" enctype="multipart/form-data">

            <div class="form-row-2">
              <div class="form-group">
                <label>Tipo de ocorrência *</label>
                <select id="tipoOcorrencia" name="tipo" required onchange="atualizarTipoInfo()">
                  <option value="">Selecione...</option>
                  <option value="descarte-irregular">🗑️ Descarte irregular de resíduos</option>
                  <option value="coleta-nao-realizada">🚛 Coleta não realizada no dia previsto</option>
                  <option value="material-nao-recolhido">📦 Material deixado para trás</option>
                  <option value="ponto-inadequado">📍 Ponto de descarte inadequado/sujo</option>
                  <option value="entulho">🧱 Descarte de entulho ou móveis</option>
                  <option value="lixo-toxico">⚠️ Descarte de lixo tóxico/perigoso</option>
                  <option value="outros">💬 Outros</option>
                </select>
                <div id="tipoInfo" class="tipo-info" style="display:none;"></div>
              </div>
              <div class="form-group">
                <label>Urgência</label>
                <select id="urgencia" name="urgencia">
                  <option value="baixa">🟢 Baixa</option>
                  <option value="media" selected>🟡 Média</option>
                  <option value="alta">🔴 Alta</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label>Endereço da ocorrência *</label>
              <div class="input-icon-wrap">
                <span>📍</span>
                <input type="text" id="localOcorrencia" name="endereco" placeholder="Rua, número – Bairro" required>
                <button type="button" class="use-location-btn" onclick="usarLocalizacao()">📡 Usar minha localização</button>
              </div>
            </div>

            <div class="form-row-2">
              <div class="form-group">
                <label>Bairro *</label>
                <select id="bairroOcorrencia" name="bairro" required>
                  <option value="">Selecione...</option>
                  <option value="Centro">Centro</option>
                  <option value="Bairro Novo">Bairro Novo</option>
                  <option value="Vila Nova">Vila Nova</option>
                  <option value="Jardim América">Jardim América</option>
                  <option value="Bairro Escolar">Bairro Escolar</option>
                  <option value="Outro">Outro</option>
                </select>
              </div>
              <div class="form-group">
                <label>Data da ocorrência</label>
                <input type="date" id="dataOcorrencia" name="data_ocorrencia">
              </div>
            </div>

            <div class="form-group">
              <label>Descrição detalhada *</label>
              <textarea id="descricaoOcorrencia" name="descricao" rows="4" maxlength="500" placeholder="Descreva o que aconteceu com o máximo de detalhes. Ex: 'Lixo hospitalar descartado na calçada da Rua X, próximo à escola. Material incluindo seringas expostas.'" required></textarea>
              <span class="char-counter"><span id="charCount">0</span>/500 caracteres</span>
            </div>

            <div class="form-group">
              <label>Foto da ocorrência (recomendado)</label>
              <div class="foto-upload-area" onclick="document.getElementById('fotoInput').click()" id="fotoArea">
                <span class="foto-icon">📸</span>
                <p>Clique para adicionar foto</p>
                <small>JPG, PNG ou WEBP – máx. 10MB. Foto com evidência vale +20 pts!</small>
                <input type="file" id="fotoInput" name="foto" accept="image/*" style="display:none" onchange="previewFoto(this)">
              </div>
              <div id="fotoPreview" style="display:none;" class="foto-preview-wrap">
                <img id="fotoImg" src="" alt="Preview">
                <button type="button" onclick="removerFoto()" class="remove-foto">✕ Remover</button>
              </div>
            </div>

            <div class="form-section-divider">Dados de contato (opcional – para acompanhamento)</div>

            <div class="form-row-2">
              <div class="form-group">
                <label>Seu nome</label>
                <input type="text" id="nomeContato" name="nome_contato" placeholder="Para receber atualizações">
              </div>
              <div class="form-group">
                <label>Telefone / WhatsApp</label>
                <input type="tel" id="telContato" name="telefone_contato" placeholder="(32) 9 0000-0000">
              </div>
            </div>

            <div class="form-group">
              <label>E-mail</label>
              <input type="email" id="emailContato" name="email_contato" placeholder="Para acompanhar via e-mail">
            </div>

            <div class="form-group" style="margin-top:8px;">
              <label class="checkbox-label">
                <input type="checkbox" id="anonimo" name="anonima" value="1">
                <span>Registrar anonimamente (não vincular dados pessoais)</span>
              </label>
            </div>

            <div class="oc-form-footer">
              <div class="pontos-preview">
                <span>⭐ Você vai ganhar:</span>
                <strong id="pontosPreview">50 pts</strong>
              </div>
              <button type="submit" class="btn-registrar">
                🚨 Registrar Ocorrência
              </button>
            </div>

          </form>
        </div>
      </div>

      <!-- LATERAL: INFO + DICAS -->
      <div class="oc-side">
        <!-- COMO FUNCIONA -->
        <div class="oc-info-card">
          <h3>ℹ️ Como funciona</h3>
          <div class="oc-steps">
            <div class="oc-step">
              <span class="oc-step-num">1</span>
              <div>
                <strong>Você registra</strong>
                <p>Preenche o formulário com local, tipo e descrição da ocorrência.</p>
              </div>
            </div>
            <div class="oc-step">
              <span class="oc-step-num">2</span>
              <div>
                <strong>Nós analisamos</strong>
                <p>Nossa equipe verifica e encaminha para o órgão responsável.</p>
              </div>
            </div>
            <div class="oc-step">
              <span class="oc-step-num">3</span>
              <div>
                <strong>Você acompanha</strong>
                <p>Use o protocolo para verificar o status da sua ocorrência.</p>
              </div>
            </div>
            <div class="oc-step">
              <span class="oc-step-num" style="background:#f59e0b;">⭐</span>
              <div>
                <strong>Você ganha pontos</strong>
                <p>Pontos por registrar + bônus quando for resolvida.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- TIPOS E PONTOS -->
        <div class="oc-info-card">
          <h3>💰 Pontuação por tipo</h3>
          <div class="pontos-lista">
            <div class="pl-item"><span>🗑️ Descarte irregular</span><strong>50 pts</strong></div>
            <div class="pl-item"><span>⚠️ Lixo tóxico</span><strong>80 pts</strong></div>
            <div class="pl-item"><span>🧱 Entulho</span><strong>60 pts</strong></div>
            <div class="pl-item"><span>🚛 Coleta não realizada</span><strong>40 pts</strong></div>
            <div class="pl-item"><span>📸 + Foto aprovada</span><strong>+20 pts</strong></div>
            <div class="pl-item"><span>✅ Quando resolvida</span><strong>+30 pts</strong></div>
          </div>
          <a href="<?= baseUrl('home/recompensas') ?>" class="link-recompensas">Ver o que fazer com os pontos →</a>
        </div>

        <!-- URGÊNCIA -->
        <div class="oc-info-card urgencia-info">
          <h3>🔴 Situação de risco?</h3>
          <p>Se encontrou <strong>lixo hospitalar, produtos químicos, seringas expostas</strong> ou qualquer material que ofereça risco imediato:</p>
          <a href="tel:156" class="btn-urgencia">📞 Ligue 156 – Prefeitura</a>
          <a href="tel:192" class="btn-urgencia secundario">📞 Ligue 192 – SAMU</a>
        </div>
      </div>

    </div>
  </div>

  <!-- ============================================================
       ABA: CONSULTAR STATUS
  ============================================================ -->
  <div id="tab-consultar" class="tab-content" style="display:none;">
    <div class="container">
      <div class="consultar-wrap">
        <div class="consultar-form-card">
          <h2>🔍 Consultar Status da Ocorrência</h2>
          <p>Informe o número de protocolo recebido ao registrar sua ocorrência.</p>

          <div class="consultar-input-row">
            <input type="text" id="protocoloInput" placeholder="Ex: CF-2026-4821" class="protocolo-input">
            <button onclick="consultarProtocolo()" class="btn-consultar-oc">Consultar</button>
          </div>

          <div id="statusResultado" style="display:none;" class="status-resultado">
            <div class="sr-header">
              <div class="sr-protocolo">Protocolo: <strong id="srProtocolo"></strong></div>
              <span class="sr-badge" id="srBadge"></span>
            </div>

            <div class="sr-info-grid">
              <div class="sr-info-item"><span>📍</span><div><small>Local</small><p id="srLocal"></p></div></div>
              <div class="sr-info-item"><span>🗑️</span><div><small>Tipo</small><p id="srTipo"></p></div></div>
              <div class="sr-info-item"><span>📅</span><div><small>Registrada em</small><p id="srData"></p></div></div>
              <div class="sr-info-item"><span>👤</span><div><small>Equipe</small><p id="srEquipe"></p></div></div>
            </div>

            <!-- TIMELINE -->
            <div class="status-timeline-rich" id="srTimeline"></div>

            <div class="sr-footer">
              <p id="srObs" class="sr-obs"></p>
            </div>
          </div>

          <div id="statusNotFound" style="display:none;" class="status-not-found">
            <span>😕</span>
            <p>Protocolo não encontrado. Verifique se digitou corretamente.</p>
          </div>
        </div>

        <!-- PROTOCOLOS RECENTES DO USUÁRIO (SIMULADO) -->
        <div class="consultar-recentes">
          <h3>📋 Suas ocorrências recentes</h3>
          <p class="consultar-sub">Registros desta sessão</p>
          <div id="minhasOcorrencias" class="minhas-oc-lista">
            <div class="mo-vazio">Você ainda não registrou nenhuma ocorrência nesta sessão.</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ============================================================
       ABA: HISTÓRICO PÚBLICO
  ============================================================ -->
  <div id="tab-historico" class="tab-content" style="display:none;">
    <div class="container">
      <div class="historico-header">
        <h2>📋 Ocorrências da Comunidade</h2>
        <p>Registro público das ocorrências reportadas pelos moradores.</p>
      </div>

      <!-- FILTROS HISTÓRICO -->
      <div class="historico-filtros">
        <input type="text" id="buscaHistorico" placeholder="Buscar por bairro ou tipo..." oninput="filtrarHistorico()">
        <select id="filtroStatusH" onchange="filtrarHistorico()">
          <option value="">Todos os status</option>
          <option value="nova">🔴 Nova</option>
          <option value="analise">🟡 Em análise</option>
          <option value="andamento">🔵 Em atendimento</option>
          <option value="resolvida">🟢 Resolvida</option>
        </select>
        <select id="filtroTipoH" onchange="filtrarHistorico()">
          <option value="">Todos os tipos</option>
          <option value="descarte">Descarte irregular</option>
          <option value="coleta">Coleta não realizada</option>
          <option value="entulho">Entulho</option>
          <option value="toxico">Lixo tóxico</option>
        </select>
        <select id="filtroBairroH" onchange="filtrarHistorico()">
          <option value="">Todos os bairros</option>
          <option value="Centro">Centro</option>
          <option value="Bairro Novo">Bairro Novo</option>
          <option value="Vila Nova">Vila Nova</option>
          <option value="Jardim América">Jardim América</option>
        </select>
      </div>

      <!-- LISTA DE OCORRÊNCIAS -->
      <div class="historico-lista" id="historicoLista"></div>

      <!-- PAGINAÇÃO -->
      <div class="historico-paginacao" id="historicoPage">
        <button class="page-btn active">1</button>
        <button class="page-btn">2</button>
        <button class="page-btn">3</button>
      </div>

    </div>
  </div>

  <!-- ============================================================
       ABA: MAPA
  ============================================================ -->
  <div id="tab-mapa" class="tab-content" style="display:none;">
    <div class="container">
      <div class="mapa-oc-section">
        <div class="mapa-oc-header">
          <h2>🗺️ Mapa de Ocorrências</h2>
          <p>Visualize os locais com mais registros de problemas na cidade.</p>
          <a href="https://www.google.com/maps/search/descarte+irregular+Juiz+de+Fora" target="_blank" class="open-maps-btn">Abrir no Google Maps ↗</a>
        </div>

        <!-- LEGENDA RÁPIDA -->
        <div class="mapa-oc-legenda">
          <span class="mol-item"><span style="color:#ef4444">🔴</span> Alta urgência</span>
          <span class="mol-item"><span style="color:#f59e0b">🟡</span> Média urgência</span>
          <span class="mol-item"><span style="color:#22c55e">🟢</span> Resolvida</span>
        </div>

        <div class="mapa-oc-embed">
          <iframe
            src="https://maps.google.com/maps?q=Juiz+de+Fora+MG+descarte+ilegal&output=embed&z=13"
            width="100%" height="480"
            style="border:0; border-radius:16px;"
            allowfullscreen="" loading="lazy">
          </iframe>
        </div>

        <!-- STATS DO MAPA -->
        <div class="mapa-oc-stats">
          <div class="mos-item">
            <strong>Centro</strong>
            <div class="mos-bar-wrap"><div class="mos-bar" style="width:80%;background:#ef4444"></div></div>
            <span>8 ocorrências</span>
          </div>
          <div class="mos-item">
            <strong>Vila Nova</strong>
            <div class="mos-bar-wrap"><div class="mos-bar" style="width:60%;background:#f59e0b"></div></div>
            <span>6 ocorrências</span>
          </div>
          <div class="mos-item">
            <strong>Bairro Novo</strong>
            <div class="mos-bar-wrap"><div class="mos-bar" style="width:40%;background:#eab308"></div></div>
            <span>4 ocorrências</span>
          </div>
          <div class="mos-item">
            <strong>Jardim América</strong>
            <div class="mos-bar-wrap"><div class="mos-bar" style="width:30%;background:#22c55e"></div></div>
            <span>3 ocorrências</span>
          </div>
          <div class="mos-item">
            <strong>Bairro Escolar</strong>
            <div class="mos-bar-wrap"><div class="mos-bar" style="width:30%;background:#22c55e"></div></div>
            <span>3 ocorrências</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL DE SUCESSO -->
  <div id="modalSucesso" class="modal" style="display:none;">
    <div class="modal-content modal-sucesso-content">
      <div class="modal-icon">✅</div>
      <h3>Ocorrência Registrada!</h3>
      <p>Sua ocorrência foi enviada com sucesso e já está sendo analisada.</p>
      <div class="protocolo-box">
        <span>Protocolo:</span>
        <strong id="protocoloGerado"></strong>
        <button onclick="copiarProtocolo()" class="btn-copiar-prot">📋 Copiar</button>
      </div>
      <p class="modal-dica">Guarde este número para acompanhar o status da ocorrência na aba "Consultar Status".</p>
      <div class="pontos-ganhos">⭐ +<span id="pontosGanhos">50</span> pontos adicionados!</div>
      <div style="display:flex;gap:10px;justify-content:center;margin-top:20px;flex-wrap:wrap;">
        <button class="btn-primary" onclick="fecharModalSucesso()">Fechar</button>
        <button class="btn-secondary" onclick="switchTab('consultar'); fecharModalSucesso()">Consultar Status</button>
      </div>
    </div>
  </div>

  <!-- TOAST -->
  <div id="ocToast" style="display:none;position:fixed;bottom:24px;right:24px;background:#1e293b;color:#fff;padding:14px 22px;border-radius:12px;font-weight:600;font-size:.9rem;z-index:9999;box-shadow:0 4px 16px rgba(0,0,0,.2);transition:opacity .3s;"></div>

  <?php
  // --- Dados reais do historico publico, no formato esperado pelo ocorrencias.js ---
  $statusJs = [
      'NOVA' => 'nova', 'EM_ANALISE' => 'analise',
      'EM_ANDAMENTO' => 'andamento', 'RESOLVIDA' => 'resolvida', 'ARQUIVADA' => 'resolvida',
  ];
  $tipoSlug = [
      'DESCARTE_IRREGULAR' => 'descarte-irregular', 'COLETA_NAO_REALIZADA' => 'coleta',
      'MATERIAL_NAO_RECOLHIDO' => 'material', 'PONTO_INADEQUADO' => 'ponto',
      'ENTULHO' => 'entulho', 'LIXO_TOXICO' => 'toxico', 'OUTROS' => 'outros',
  ];
  $tipoLabel = [
      'DESCARTE_IRREGULAR' => 'Descarte irregular', 'COLETA_NAO_REALIZADA' => 'Coleta não realizada',
      'MATERIAL_NAO_RECOLHIDO' => 'Material deixado', 'PONTO_INADEQUADO' => 'Ponto inadequado',
      'ENTULHO' => 'Entulho/Móveis', 'LIXO_TOXICO' => 'Lixo tóxico', 'OUTROS' => 'Outros',
  ];
  $historicoJs = [];
  foreach (($historico ?? []) as $o) {
      $historicoJs[] = [
          'id'        => $o['protocolo'],
          'tipo'      => $tipoSlug[$o['tipo']] ?? 'outros',
          'tipoLabel' => $tipoLabel[$o['tipo']] ?? 'Ocorrência',
          'bairro'    => $o['bairro'],
          'local'     => $o['endereco_descricao'],
          'urgencia'  => strtolower($o['urgencia']),
          'status'    => $statusJs[$o['status']] ?? 'nova',
          'data'      => date('d/m/Y', strtotime($o['criada_em'])),
          'desc'      => $o['descricao'],
      ];
  }
  ?>
  <script>
    // Base URL e histórico público vindos do servidor (usados por ocorrencias.js)
    window.BASEURL = <?= json_encode(baseUrl(), JSON_UNESCAPED_SLASHES) ?>;
    window.HISTORICO_DB = <?= json_encode($historicoJs, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  </script>

  <?php if (!empty($protocoloCriado)): ?>
  <script>
    // Abre o modal de sucesso com o protocolo real gerado no servidor.
    document.addEventListener('DOMContentLoaded', function () {
      var el = document.getElementById('protocoloGerado');
      if (el) el.textContent = <?= json_encode($protocoloCriado, JSON_UNESCAPED_UNICODE) ?>;
      var modal = document.getElementById('modalSucesso');
      if (modal) modal.style.display = 'flex';
    });
  </script>
  <?php endif; ?>

<?php require_once 'app/view/comuns/rodape.php'; ?>
