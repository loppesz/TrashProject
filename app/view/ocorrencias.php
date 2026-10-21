<?php require_once 'app/view/comuns/cabecalho.php'; ?>
<?php
  // Regras de pontos vindas do controller (fonte unica: PontosModel::regras()).
  $rp = $regrasPontos ?? ['registro' => 10, 'resolvida' => 40, 'foto' => 15, 'limiteDia' => 3, 'maximo' => 65];
  $restantesHoje = max(0, (int) $rp['limiteDia'] - (int) ($registrosHoje ?? 0));
?>

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
            <div class="pontos-badge">Até <?= (int) $rp['maximo'] ?> pts por ocorrência confirmada</div>
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
                <input type="text" id="localOcorrencia" name="endereco" placeholder="Rua e número em Muriaé" required autocomplete="street-address">
                <button type="button" class="use-location-btn" onclick="usarLocalizacao()">Usar localização atual</button>
              </div>
              <input type="hidden" id="latOcorrencia" name="lat" value="">
              <input type="hidden" id="lngOcorrencia" name="lng" value="">
              <input type="hidden" id="precisaoOcorrencia" name="precisao" value="">
              <div id="gpsAviso" class="gps-aviso" style="display:none;">
                <div>
                  <strong id="gpsStatus">Localização confirmada</strong>
                  <span id="gpsPrecisao"></span>
                </div>
                <button type="button" class="gps-limpar" onclick="limparLocalizacao()">Remover</button>
              </div>
              <div id="localizacaoPreview" class="localizacao-preview" style="display:none;" aria-label="Prévia da localização selecionada"></div>
              <small>O endereço preenchido pelo GPS pode ser editado antes do envio.</small>
            </div>

            <div class="form-row-2">
              <div class="form-group">
                <label>Bairro *</label>
                <select id="bairroOcorrencia" name="bairro" required>
                  <option value="">Selecione...</option>
                  <?php foreach (($bairrosAtivos ?? []) as $bairro): ?>
                    <option value="<?= htmlspecialchars($bairro['nome'], ENT_QUOTES) ?>"><?= htmlspecialchars($bairro['nome']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label>Cidade *</label>
                <input type="text" id="cidadeOcorrencia" name="cidade" value="Muriaé - MG" required autocomplete="address-level2">
              </div>
            </div>

            <div class="form-group form-group-compact">
              <label>Data da ocorrência</label>
              <input type="date" id="dataOcorrencia" name="data_ocorrencia">
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
                <small>JPG, PNG ou WEBP – máx. 10MB. Foto de evidência vale +<?= (int) $rp['foto'] ?> pts quando a ocorrência for confirmada.</small>
                <input type="file" id="fotoInput" name="foto" accept="image/*" style="display:none" onchange="previewFoto(this)">
              </div>
              <div id="fotoPreview" style="display:none;" class="foto-preview-wrap">
                <img id="fotoImg" src="" alt="Preview">
                <button type="button" onclick="removerFoto()" class="remove-foto">✕ Remover</button>
              </div>
              <small style="display:block;margin-top:6px;color:#64748b;">🔒 Por privacidade, removemos os metadados da foto (incluindo a localização GPS que o celular costuma gravar) antes de armazená-la.</small>
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
                <input type="checkbox" id="anonimo" name="anonima" value="1" onchange="atualizarTipoInfo()">
                <span>Registrar anonimamente — não salvamos seu nome, telefone nem e-mail (e a ocorrência não fica vinculada à sua conta).</span>
              </label>
            </div>

            <div class="oc-form-footer">
              <div class="pontos-preview" aria-live="polite">
                <span>⭐ Você pode ganhar:</span>
                <strong id="pontosPreview">até <?= (int) ($rp['registro'] + $rp['resolvida']) ?> pts</strong>
                <small id="pontosPreviewDetalhe" class="pontos-preview-detalhe"><?= (int) $rp['registro'] ?> agora + <?= (int) $rp['resolvida'] ?> quando a equipe confirmar</small>
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
                <p>Uma parte ao registrar e a maior parte quando a equipe confirma e resolve.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- TIPOS E PONTOS -->
        <div class="oc-info-card">
          <h3>Pontuação</h3>
          <div class="pontos-lista">
            <div class="pl-item"><span>Ao registrar (até <?= (int) $rp['limiteDia'] ?> por dia)</span><strong>+<?= (int) $rp['registro'] ?> pts</strong></div>
            <div class="pl-item"><span>Confirmada e resolvida pela equipe</span><strong>+<?= (int) $rp['resolvida'] ?> pts</strong></div>
            <div class="pl-item"><span>Foto de evidência (na confirmação)</span><strong>+<?= (int) $rp['foto'] ?> pts</strong></div>
          </div>
          <p class="pontos-regra">Pontos só para quem está logado e não registra como anônimo. Ocorrências repetidas ou falsas são arquivadas e não recebem o bônus.</p>
          <?php if (!empty($usuarioLogado)): ?>
            <p class="pontos-regra pontos-regra-hoje">
              <?= $restantesHoje > 0
                  ? 'Hoje você ainda pode ganhar pontos de registro em ' . $restantesHoje . ' ocorrência' . ($restantesHoje > 1 ? 's' : '') . '.'
                  : 'Você já atingiu o limite de pontos de registro de hoje. Os pontos de confirmação continuam valendo.' ?>
            </p>
          <?php endif; ?>
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

            <!-- Descrição: aparece só para o autor da ocorrência (logado) -->
            <div class="sr-desc" id="srDescWrap" hidden>
              <small>Sua descrição</small>
              <p id="srDesc"></p>
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

        <div class="consultar-recentes">
          <h3>Suas ocorrências recentes</h3>
          <p class="consultar-sub">Registros vinculados à sua conta</p>
          <div class="minhas-oc-lista" id="minhasOcorrencias">
            <?php foreach (($minhasOcorrencias ?? []) as $minha): ?>
              <?php $descMinha = trim((string) ($minha['descricao'] ?? '')); ?>
              <button type="button" class="mo-item" onclick="consultarOcorrenciaDaLista('<?= htmlspecialchars($minha['protocolo'], ENT_QUOTES) ?>')">
                <span class="mo-topo">
                  <strong><?= htmlspecialchars(rotuloTipoOcorrencia($minha['tipo'])) ?> — <?= htmlspecialchars($minha['bairro']) ?></strong>
                  <span class="sr-badge <?= classeStatusOcorrencia($minha['status']) ?>"><?= rotuloStatusOcorrencia($minha['status']) ?></span>
                </span>
                <?php if ($descMinha !== ''): ?>
                  <span class="mo-desc"><?= htmlspecialchars(mb_strimwidth($descMinha, 0, 140, '…', 'UTF-8')) ?></span>
                <?php endif; ?>
                <small><span><?= htmlspecialchars($minha['protocolo']) ?></span><span><?= date('d/m/Y', strtotime($minha['criada_em'])) ?></span></small>
              </button>
            <?php endforeach; ?>
            <?php if (empty($minhasOcorrencias)): ?>
              <div class="mo-vazio"><?= !empty($usuarioLogado) ? 'Você ainda não registrou ocorrências.' : 'Entre na sua conta para ver seus registros.' ?></div>
            <?php endif; ?>
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
          <?php foreach (($bairrosAtivos ?? []) as $bairro): ?>
            <option value="<?= htmlspecialchars($bairro['nome'], ENT_QUOTES) ?>"><?= htmlspecialchars($bairro['nome']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- LISTA DE OCORRÊNCIAS -->
      <div class="historico-lista" id="historicoLista"></div>

    </div>
  </div>

  <!-- ============================================================
       ABA: MAPA
  ============================================================ -->
  <div id="tab-mapa" class="tab-content" style="display:none;">
    <div class="container">
      <div class="mapa-oc-section">
        <div class="mapa-oc-header">
          <div>
            <h2>Mapa de ocorrências em Muriaé</h2>
            <p>Os marcadores usam os registros públicos com localização disponível. Por privacidade, a posição pública é aproximada.</p>
          </div>
          <button type="button" class="open-maps-btn" onclick="centralizarMapaNaMinhaLocalizacao()">Minha localização</button>
        </div>

        <div class="mapa-oc-legenda">
          <span class="mol-item"><span class="map-dot alta"></span> Alta urgência</span>
          <span class="mol-item"><span class="map-dot media"></span> Média urgência</span>
          <span class="mol-item"><span class="map-dot resolvida"></span> Resolvida</span>
        </div>

        <div id="mapaOcorrencias" class="mapa-oc-leaflet" role="application" aria-label="Mapa de ocorrências ambientais em Muriaé"></div>
        <p id="mapaOcorrenciasAviso" class="mapa-oc-aviso" hidden></p>

        <div class="mapa-oc-stats">
          <?php
          $mapaPorBairro = $mapaPorBairro ?? [];
          $maxMapa = 0;
          foreach ($mapaPorBairro as $linha) { $maxMapa = max($maxMapa, (int) $linha['total']); }
          ?>
          <?php foreach ($mapaPorBairro as $linha): ?>
            <?php $pctMapa = $maxMapa > 0 ? (int) round(((int) $linha['total'] / $maxMapa) * 100) : 0; ?>
            <div class="mos-item">
              <strong><?= htmlspecialchars($linha['bairro']) ?></strong>
              <div class="mos-bar-wrap"><div class="mos-bar" style="width:<?= $pctMapa ?>%"></div></div>
              <span><?= (int) $linha['total'] ?> ocorrência(s)</span>
            </div>
          <?php endforeach; ?>
          <?php if (empty($mapaPorBairro)): ?>
            <p class="empty-state">Ainda não há ocorrências públicas para representar no mapa.</p>
          <?php endif; ?>
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
        <button type="button" onclick="copiarProtocolo(this)" class="btn-copiar-prot" aria-label="Copiar protocolo">📋 Copiar</button>
      </div>
      <p id="avisoCopiado" class="copiado-aviso" role="status" aria-live="polite" hidden></p>
      <p class="modal-dica">Guarde este número para acompanhar o status da ocorrência na aba "Consultar Status".</p>
      <?php
        // Mensagem de pontos com o valor realmente creditado no registro.
        $rpRet = $retornoPontos ?? ['agora' => 0, 'foto' => false, 'vinculada' => false];
        $bonusConfirmacao = (int) $rp['resolvida'] + (!empty($rpRet['foto']) ? (int) $rp['foto'] : 0);
      ?>
      <?php if (!empty($rpRet['vinculada'])): ?>
        <?php if ((int) $rpRet['agora'] > 0): ?>
          <div class="pontos-ganhos">⭐ +<span id="pontosGanhos"><?= (int) $rpRet['agora'] ?></span> pontos adicionados!</div>
        <?php else: ?>
          <div class="pontos-ganhos">Limite diário de pontos de registro atingido.</div>
        <?php endif; ?>
        <p class="modal-dica">Quando a equipe confirmar e resolver, você recebe mais <strong>+<?= $bonusConfirmacao ?> pts</strong><?= !empty($rpRet['foto']) ? ' (inclui a foto)' : '' ?>.</p>
      <?php else: ?>
        <p class="modal-dica">Ocorrências anônimas ou sem login não somam pontos. <a href="<?= baseUrl('usuario/login') ?>">Entre na sua conta</a> para participar das recompensas.</p>
      <?php endif; ?>
      <div style="display:flex;gap:10px;justify-content:center;margin-top:20px;flex-wrap:wrap;">
        <button class="btn-primary" onclick="fecharModalSucesso()">Fechar</button>
        <button class="btn-secondary" onclick="switchTab('consultar'); fecharModalSucesso()">Consultar Status</button>
      </div>
    </div>
  </div>

  <!-- TOAST -->
  <div id="ocToast" role="status" aria-live="polite" style="display:none;position:fixed;bottom:24px;right:24px;background:#1e293b;color:#fff;padding:14px 22px;border-radius:12px;font-weight:600;font-size:.9rem;z-index:9999;box-shadow:0 4px 16px rgba(0,0,0,.2);transition:opacity .3s;"></div>

  <?php
  // --- Dados reais do historico publico, no formato esperado pelo ocorrencias.js ---
  $statusJs = [
      'NOVA' => 'nova', 'EM_ANALISE' => 'analise',
      'EM_ANDAMENTO' => 'andamento', 'RESOLVIDA' => 'resolvida', 'ARQUIVADA' => 'arquivada',
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
    window.MAPA_OCORRENCIAS = <?= json_encode($mapaOcorrencias ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    // Regras de pontos (as mesmas do servidor) para a prévia do formulário.
    window.REGRAS_PONTOS = <?= json_encode([
        'registro'      => (int) $rp['registro'],
        'resolvida'     => (int) $rp['resolvida'],
        'foto'          => (int) $rp['foto'],
        'restantesHoje' => $restantesHoje,
        'logado'        => !empty($usuarioLogado),
    ]) ?>;
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
