<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pontos de Descarte – ColetaFácil</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="pontos.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>

  <nav class="navbar">
    <div class="container nav-inner">
      <a href="index.php" class="logo"><span class="logo-icon">♻️</span><span>ColetaFácil</span></a>
      <ul class="nav-links">
        <li><a href="index.php">Início</a></li>
        <li><a href="materiais.php">Materiais</a></li>
        <li><a href="pontos.php" class="active">Pontos</a></li>
        <li><a href="coleta.php">Coleta</a></li>
        <li><a href="ocorrencias.php">Ocorrências</a></li>
        <li><a href="kids.php" class="nav-kids">🧒 Kids</a></li>
        <li><a href="recompensas.php" class="nav-reward">⭐ Pontos</a></li>
        <li><a href="admin/index.php" class="nav-admin">Admin</a></li>
      </ul>
      <button class="menu-btn" onclick="toggleMenu()">☰</button>
    </div>
    <div class="mobile-menu" id="mobileMenu">
      <a href="index.php">🏠 Início</a>
      <a href="materiais.php">📦 Materiais</a>
      <a href="pontos.php">📍 Pontos</a>
      <a href="coleta.php">🗓️ Coleta</a>
      <a href="ocorrencias.php">🚨 Ocorrências</a>
      <a href="kids.php">🧒 Kids</a>
    </div>
  </nav>

  <!-- PAGE HEADER -->
  <div class="page-header-rich">
    <div class="container">
      <div class="phr-inner">
        <div>
          <nav class="breadcrumb-nav">
            <a href="index.php">Início</a> <span>/</span>
            <span class="bc-active">Pontos de Descarte</span>
          </nav>
          <h1>📍 Pontos de Descarte</h1>
          <p>Encontre locais para descartar materiais especiais próximos a você. Clique em qualquer ponto para ver detalhes, fotos e localização no mapa.</p>
        </div>
        <div class="phr-stats">
          <div class="phr-stat">
            <strong>6</strong>
            <span>Pontos ativos</span>
          </div>
          <div class="phr-stat">
            <strong>12</strong>
            <span>Tipos de material</span>
          </div>
          <div class="phr-stat">
            <strong>5km</strong>
            <span>Raio coberto</span>
          </div>
        </div>
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

  <!-- GRID DE PONTOS -->
  <div class="container" id="pontosContainer">
    <div class="pontos-rich-grid" id="pontosGrid">

      <!-- PONTO 1 – ECOPONTO CENTRO -->
      <div class="ponto-rich-card" id="ponto1"
           data-nome="ecoponto centro" data-material="pilhas eletronicos cartuchos impressora"
           data-status="aberto" data-lat="-21.7555" data-lng="-43.3496"
           data-endereco="Rua das Flores, 123 – Centro">

        <div class="prc-header" onclick="togglePonto('ponto1')">
          <div class="prc-foto-wrap">
            <div class="prc-foto prc-foto-verde">
              <span>🏭</span>
            </div>
            <span class="prc-status-dot aberto" title="Aberto agora"></span>
          </div>
          <div class="prc-info">
            <div class="prc-title-row">
              <h3>Ecoponto Centro</h3>
              <span class="prc-status aberto">🟢 Aberto</span>
            </div>
            <p class="prc-endereco">📍 Rua das Flores, 123 – Centro</p>
            <div class="prc-materiais-preview">
              <span class="mat-chip">🔋 Pilhas</span>
              <span class="mat-chip">📱 Eletrônicos</span>
              <span class="mat-chip">🖨️ Cartuchos</span>
            </div>
          </div>
          <div class="prc-horario-resumo">
            <span class="prc-hoje-label">Hoje</span>
            <span class="prc-hoje-hora">08h – 17h</span>
          </div>
          <button class="prc-toggle-btn" id="btn-ponto1">▼</button>
        </div>

        <!-- EXPANDIDO -->
        <div class="prc-expanded" id="exp-ponto1" style="display:none;">
          <div class="prc-expanded-inner">

            <!-- GALERIA DE FOTOS -->
            <div class="prc-gallery">
              <div class="gallery-main" id="gal-main-1">
                <div class="gallery-placeholder">
                  <span>🏭</span>
                  <p>Ecoponto Centro</p>
                  <small>Foto da fachada</small>
                </div>
              </div>
              <div class="gallery-thumbs">
                <div class="gal-thumb active" onclick="selectPhoto(1,0)">🏭 Fachada</div>
                <div class="gal-thumb" onclick="selectPhoto(1,1)">📦 Interior</div>
                <div class="gal-thumb" onclick="selectPhoto(1,2)">🔋 Contêineres</div>
              </div>
            </div>

            <div class="prc-details-grid">

              <!-- HORÁRIOS COMPLETOS -->
              <div class="prc-detail-section">
                <h4>🕐 Horários de Funcionamento</h4>
                <table class="horario-table">
                  <tbody>
                    <tr class="hoje-row"><td>Segunda</td><td><strong>08h – 17h</strong></td><td><span class="hst aberto">Aberto</span></td></tr>
                    <tr class="hoje-row"><td>Terça</td><td><strong>08h – 17h</strong></td><td><span class="hst aberto">Aberto</span></td></tr>
                    <tr class="hoje-row"><td>Quarta</td><td><strong>08h – 17h</strong></td><td><span class="hst aberto">Aberto</span></td></tr>
                    <tr class="hoje-row"><td>Quinta</td><td><strong>08h – 17h</strong></td><td><span class="hst aberto">Aberto</span></td></tr>
                    <tr class="hoje-row"><td>Sexta</td><td><strong>08h – 17h</strong></td><td><span class="hst aberto">Aberto</span></td></tr>
                    <tr><td>Sábado</td><td><strong>08h – 12h</strong></td><td><span class="hst aberto">Aberto</span></td></tr>
                    <tr><td>Domingo</td><td><strong>–</strong></td><td><span class="hst fechado">Fechado</span></td></tr>
                  </tbody>
                </table>
              </div>

              <!-- MATERIAIS ACEITOS -->
              <div class="prc-detail-section">
                <h4>✅ Materiais Aceitos</h4>
                <div class="materiais-aceitos-list">
                  <div class="ma-item aceito"><span>🔋</span> Pilhas e Baterias</div>
                  <div class="ma-item aceito"><span>📱</span> Celulares</div>
                  <div class="ma-item aceito"><span>💻</span> Notebooks/Tablets</div>
                  <div class="ma-item aceito"><span>🖨️</span> Cartuchos de tinta</div>
                  <div class="ma-item aceito"><span>📺</span> Monitores</div>
                  <div class="ma-item nao-aceito"><span>🍎</span> Lixo orgânico</div>
                  <div class="ma-item nao-aceito"><span>🧴</span> Plástico comum</div>
                </div>
              </div>

              <!-- CONTATO E INFORMAÇÕES -->
              <div class="prc-detail-section">
                <h4>📞 Contato & Informações</h4>
                <div class="contato-list">
                  <div class="ct-item"><span>📞</span> (32) 3333-1001</div>
                  <div class="ct-item"><span>📧</span> ecoponto@prefeiturajf.gov.br</div>
                  <div class="ct-item"><span>♿</span> Acessível para PCD</div>
                  <div class="ct-item"><span>🅿️</span> Estacionamento disponível</div>
                </div>
                <div class="prc-avaliacao">
                  <span>⭐ 4.7</span>
                  <span class="av-count">(38 avaliações)</span>
                </div>
                <div class="prc-action-btns">
                  <a href="https://wa.me/5532999990001" target="_blank" class="act-btn whatsapp">💬 WhatsApp</a>
                  <button class="act-btn compartilhar" onclick="compartilhar('Ecoponto Centro', 'Rua das Flores, 123')">🔗 Compartilhar</button>
                </div>
              </div>

            </div>

            <!-- MAPA GOOGLE -->
            <div class="prc-map-section">
              <div class="prc-map-header">
                <h4>🗺️ Localização</h4>
                <a href="https://www.google.com/maps/search/Rua+das+Flores+123+Juiz+de+Fora" target="_blank" class="open-maps-btn">
                  Abrir no Google Maps ↗
                </a>
              </div>
              <div class="prc-map-embed">
                <iframe
                  src="https://maps.google.com/maps?q=Rua+das+Flores+123+Juiz+de+Fora+MG&output=embed&z=16"
                  width="100%" height="300" style="border:0;border-radius:12px;"
                  allowfullscreen="" loading="lazy"
                  referrerpolicy="no-referrer-when-downgrade">
                </iframe>
              </div>
              <div class="prc-como-chegar">
                <a href="https://www.google.com/maps/dir//Rua+das+Flores+123+Juiz+de+Fora" target="_blank" class="como-chegar-btn">
                  🧭 Como chegar de carro
                </a>
                <a href="https://www.google.com/maps/dir//Rua+das+Flores+123+Juiz+de+Fora&travelmode=transit" target="_blank" class="como-chegar-btn">
                  🚌 Como chegar de ônibus
                </a>
                <a href="https://www.google.com/maps/dir//Rua+das+Flores+123+Juiz+de+Fora&travelmode=walking" target="_blank" class="como-chegar-btn">
                  🚶 Ir a pé
                </a>
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- PONTO 2 – FARMÁCIA -->
      <div class="ponto-rich-card" id="ponto2"
           data-nome="farmacia saude total" data-material="medicamentos pilhas remédios"
           data-status="aberto" data-lat="-21.7620" data-lng="-43.3550"
           data-endereco="Av. Brasil, 456 – Bairro Novo">

        <div class="prc-header" onclick="togglePonto('ponto2')">
          <div class="prc-foto-wrap">
            <div class="prc-foto prc-foto-azul"><span>💊</span></div>
            <span class="prc-status-dot aberto"></span>
          </div>
          <div class="prc-info">
            <div class="prc-title-row">
              <h3>Farmácia Saúde Total</h3>
              <span class="prc-status aberto">🟢 Aberto</span>
            </div>
            <p class="prc-endereco">📍 Av. Brasil, 456 – Bairro Novo</p>
            <div class="prc-materiais-preview">
              <span class="mat-chip">💊 Medicamentos</span>
              <span class="mat-chip">🔋 Pilhas</span>
            </div>
          </div>
          <div class="prc-horario-resumo">
            <span class="prc-hoje-label">Hoje</span>
            <span class="prc-hoje-hora">07h – 22h</span>
          </div>
          <button class="prc-toggle-btn" id="btn-ponto2">▼</button>
        </div>

        <div class="prc-expanded" id="exp-ponto2" style="display:none;">
          <div class="prc-expanded-inner">
            <div class="prc-gallery">
              <div class="gallery-main">
                <div class="gallery-placeholder" style="background:linear-gradient(135deg,#eff6ff,#dbeafe)">
                  <span>💊</span><p>Farmácia Saúde Total</p><small>Ponto de coleta na entrada</small>
                </div>
              </div>
              <div class="gallery-thumbs">
                <div class="gal-thumb active">💊 Fachada</div>
                <div class="gal-thumb">📦 Coletor</div>
              </div>
            </div>
            <div class="prc-details-grid">
              <div class="prc-detail-section">
                <h4>🕐 Horários</h4>
                <table class="horario-table">
                  <tr><td>Segunda a Sábado</td><td><strong>07h – 22h</strong></td><td><span class="hst aberto">Aberto</span></td></tr>
                  <tr><td>Domingo e Feriados</td><td><strong>08h – 20h</strong></td><td><span class="hst aberto">Aberto</span></td></tr>
                </table>
              </div>
              <div class="prc-detail-section">
                <h4>✅ Materiais Aceitos</h4>
                <div class="materiais-aceitos-list">
                  <div class="ma-item aceito"><span>💊</span> Remédios vencidos</div>
                  <div class="ma-item aceito"><span>🔋</span> Pilhas e baterias</div>
                  <div class="ma-item aceito"><span>💉</span> Seringas (com tampa)</div>
                  <div class="ma-item nao-aceito"><span>🧴</span> Frascos de plástico</div>
                </div>
              </div>
              <div class="prc-detail-section">
                <h4>📞 Contato</h4>
                <div class="contato-list">
                  <div class="ct-item"><span>📞</span> (32) 3333-2002</div>
                  <div class="ct-item"><span>♿</span> Acessível para PCD</div>
                </div>
                <div class="prc-avaliacao"><span>⭐ 4.5</span><span class="av-count">(24 avaliações)</span></div>
                <div class="prc-action-btns">
                  <button class="act-btn compartilhar" onclick="compartilhar('Farmácia Saúde Total', 'Av. Brasil, 456')">🔗 Compartilhar</button>
                </div>
              </div>
            </div>
            <div class="prc-map-section">
              <div class="prc-map-header">
                <h4>🗺️ Localização</h4>
                <a href="https://www.google.com/maps/search/Av+Brasil+456+Juiz+de+Fora" target="_blank" class="open-maps-btn">Abrir no Google Maps ↗</a>
              </div>
              <div class="prc-map-embed">
                <iframe src="https://maps.google.com/maps?q=Av+Brasil+456+Juiz+de+Fora+MG&output=embed&z=16" width="100%" height="280" style="border:0;border-radius:12px;" loading="lazy"></iframe>
              </div>
              <div class="prc-como-chegar">
                <a href="https://www.google.com/maps/dir//Av+Brasil+456+Juiz+de+Fora" target="_blank" class="como-chegar-btn">🧭 Como chegar de carro</a>
                <a href="https://www.google.com/maps/dir//Av+Brasil+456+Juiz+de+Fora&travelmode=transit" target="_blank" class="como-chegar-btn">🚌 Ônibus</a>
                <a href="https://www.google.com/maps/dir//Av+Brasil+456+Juiz+de+Fora&travelmode=walking" target="_blank" class="como-chegar-btn">🚶 A pé</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- PONTO 3 – SUPERMERCADO -->
      <div class="ponto-rich-card" id="ponto3"
           data-nome="supermercado boa compra" data-material="oleo cozinha pilhas plastico"
           data-status="aberto" data-lat="-21.7600" data-lng="-43.3580"
           data-endereco="Rua Dom Pedro, 789 – Vila Nova">

        <div class="prc-header" onclick="togglePonto('ponto3')">
          <div class="prc-foto-wrap">
            <div class="prc-foto prc-foto-laranja"><span>🛒</span></div>
            <span class="prc-status-dot aberto"></span>
          </div>
          <div class="prc-info">
            <div class="prc-title-row">
              <h3>Supermercado Boa Compra</h3>
              <span class="prc-status aberto">🟢 Aberto</span>
            </div>
            <p class="prc-endereco">📍 Rua Dom Pedro, 789 – Vila Nova</p>
            <div class="prc-materiais-preview">
              <span class="mat-chip">🫙 Óleo usado</span>
              <span class="mat-chip">🔋 Pilhas</span>
              <span class="mat-chip">🛍️ Sacolas plásticas</span>
            </div>
          </div>
          <div class="prc-horario-resumo">
            <span class="prc-hoje-label">Hoje</span>
            <span class="prc-hoje-hora">07h – 21h</span>
          </div>
          <button class="prc-toggle-btn" id="btn-ponto3">▼</button>
        </div>

        <div class="prc-expanded" id="exp-ponto3" style="display:none;">
          <div class="prc-expanded-inner">
            <div class="prc-gallery">
              <div class="gallery-main">
                <div class="gallery-placeholder" style="background:linear-gradient(135deg,#fff7ed,#fed7aa)">
                  <span>🛒</span><p>Supermercado Boa Compra</p><small>Coletores na entrada do mercado</small>
                </div>
              </div>
            </div>
            <div class="prc-details-grid">
              <div class="prc-detail-section">
                <h4>🕐 Horários</h4>
                <table class="horario-table">
                  <tr><td>Segunda a Sábado</td><td><strong>07h – 21h</strong></td><td><span class="hst aberto">Aberto</span></td></tr>
                  <tr><td>Domingo</td><td><strong>08h – 14h</strong></td><td><span class="hst aberto">Aberto</span></td></tr>
                </table>
                <div class="info-extra-box">
                  ℹ️ O coletor de óleo fica na entrada lateral (portão 2)
                </div>
              </div>
              <div class="prc-detail-section">
                <h4>✅ Materiais Aceitos</h4>
                <div class="materiais-aceitos-list">
                  <div class="ma-item aceito"><span>🫙</span> Óleo de cozinha usado</div>
                  <div class="ma-item aceito"><span>🔋</span> Pilhas domésticas</div>
                  <div class="ma-item aceito"><span>🛍️</span> Sacolas plásticas</div>
                  <div class="ma-item aceito"><span>🥤</span> Garrafas PET</div>
                  <div class="ma-item nao-aceito"><span>🍎</span> Lixo orgânico</div>
                </div>
              </div>
              <div class="prc-detail-section">
                <h4>📞 Contato</h4>
                <div class="contato-list">
                  <div class="ct-item"><span>📞</span> (32) 3333-3003</div>
                  <div class="ct-item"><span>🅿️</span> Estacionamento gratuito</div>
                  <div class="ct-item"><span>♿</span> Acessível para PCD</div>
                </div>
                <div class="prc-avaliacao"><span>⭐ 4.2</span><span class="av-count">(19 avaliações)</span></div>
                <div class="prc-action-btns">
                  <button class="act-btn compartilhar" onclick="compartilhar('Supermercado Boa Compra', 'Rua Dom Pedro, 789')">🔗 Compartilhar</button>
                </div>
              </div>
            </div>
            <div class="prc-map-section">
              <div class="prc-map-header">
                <h4>🗺️ Localização</h4>
                <a href="https://www.google.com/maps/search/Rua+Dom+Pedro+789+Juiz+de+Fora" target="_blank" class="open-maps-btn">Abrir no Google Maps ↗</a>
              </div>
              <div class="prc-map-embed">
                <iframe src="https://maps.google.com/maps?q=Rua+Dom+Pedro+789+Juiz+de+Fora+MG&output=embed&z=16" width="100%" height="280" style="border:0;border-radius:12px;" loading="lazy"></iframe>
              </div>
              <div class="prc-como-chegar">
                <a href="https://www.google.com/maps/dir//Rua+Dom+Pedro+789+Juiz+de+Fora" target="_blank" class="como-chegar-btn">🧭 Carro</a>
                <a href="https://www.google.com/maps/dir//Rua+Dom+Pedro+789+Juiz+de+Fora&travelmode=transit" target="_blank" class="como-chegar-btn">🚌 Ônibus</a>
                <a href="https://www.google.com/maps/dir//Rua+Dom+Pedro+789+Juiz+de+Fora&travelmode=walking" target="_blank" class="como-chegar-btn">🚶 A pé</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- PONTO 4 – UBS -->
      <div class="ponto-rich-card" id="ponto4"
           data-nome="ubs posto saude central" data-material="seringas medicamentos perfurocortantes"
           data-status="aberto" data-lat="-21.7580" data-lng="-43.3510"
           data-endereco="Rua da Saúde, 10 – Centro">

        <div class="prc-header" onclick="togglePonto('ponto4')">
          <div class="prc-foto-wrap">
            <div class="prc-foto prc-foto-vermelho"><span>🏥</span></div>
            <span class="prc-status-dot aberto"></span>
          </div>
          <div class="prc-info">
            <div class="prc-title-row">
              <h3>UBS – Posto de Saúde Central</h3>
              <span class="prc-status aberto">🟢 Aberto</span>
            </div>
            <p class="prc-endereco">📍 Rua da Saúde, 10 – Centro</p>
            <div class="prc-materiais-preview">
              <span class="mat-chip danger">💉 Seringas</span>
              <span class="mat-chip danger">💊 Medicamentos</span>
            </div>
          </div>
          <div class="prc-horario-resumo">
            <span class="prc-hoje-label">Hoje</span>
            <span class="prc-hoje-hora">07h – 17h</span>
          </div>
          <button class="prc-toggle-btn" id="btn-ponto4">▼</button>
        </div>

        <div class="prc-expanded" id="exp-ponto4" style="display:none;">
          <div class="prc-expanded-inner">
            <div class="prc-gallery">
              <div class="gallery-main">
                <div class="gallery-placeholder" style="background:linear-gradient(135deg,#fff1f2,#fecdd3)">
                  <span>🏥</span><p>UBS Central</p><small>Coletor de perfurocortantes na recepção</small>
                </div>
              </div>
            </div>
            <div class="alert-box danger-alert">
              🔴 <strong>Atenção:</strong> Seringas e materiais perfurocortantes devem ser entregues na recepção com caixa coletora própria. Não descarte em sacos plásticos comuns!
            </div>
            <div class="prc-details-grid">
              <div class="prc-detail-section">
                <h4>🕐 Horários</h4>
                <table class="horario-table">
                  <tr><td>Segunda a Sexta</td><td><strong>07h – 17h</strong></td><td><span class="hst aberto">Aberto</span></td></tr>
                  <tr><td>Sábado</td><td><strong>07h – 12h</strong></td><td><span class="hst aberto">Aberto</span></td></tr>
                  <tr><td>Domingo/Feriado</td><td><strong>–</strong></td><td><span class="hst fechado">Fechado</span></td></tr>
                </table>
              </div>
              <div class="prc-detail-section">
                <h4>✅ Materiais Aceitos</h4>
                <div class="materiais-aceitos-list">
                  <div class="ma-item aceito danger-item"><span>💉</span> Seringas/agulhas (caixa coletora)</div>
                  <div class="ma-item aceito danger-item"><span>🩹</span> Materiais perfurocortantes</div>
                  <div class="ma-item aceito"><span>💊</span> Medicamentos vencidos</div>
                  <div class="ma-item aceito"><span>🩺</span> Resíduos de saúde domiciliar</div>
                </div>
              </div>
              <div class="prc-detail-section">
                <h4>📞 Contato</h4>
                <div class="contato-list">
                  <div class="ct-item"><span>📞</span> (32) 3333-4004</div>
                  <div class="ct-item"><span>♿</span> Acessível para PCD</div>
                  <div class="ct-item"><span>🅿️</span> Sem estacionamento</div>
                </div>
                <div class="prc-avaliacao"><span>⭐ 4.8</span><span class="av-count">(51 avaliações)</span></div>
              </div>
            </div>
            <div class="prc-map-section">
              <div class="prc-map-header">
                <h4>🗺️ Localização</h4>
                <a href="https://www.google.com/maps/search/Rua+da+Saude+10+Juiz+de+Fora" target="_blank" class="open-maps-btn">Abrir no Google Maps ↗</a>
              </div>
              <div class="prc-map-embed">
                <iframe src="https://maps.google.com/maps?q=Rua+da+Saude+10+Juiz+de+Fora+MG&output=embed&z=16" width="100%" height="280" style="border:0;border-radius:12px;" loading="lazy"></iframe>
              </div>
              <div class="prc-como-chegar">
                <a href="https://www.google.com/maps/dir//Rua+da+Saude+10+Juiz+de+Fora" target="_blank" class="como-chegar-btn">🧭 Carro</a>
                <a href="https://www.google.com/maps/dir//Rua+da+Saude+10+Juiz+de+Fora&travelmode=transit" target="_blank" class="como-chegar-btn">🚌 Ônibus</a>
                <a href="https://www.google.com/maps/dir//Rua+da+Saude+10+Juiz+de+Fora&travelmode=walking" target="_blank" class="como-chegar-btn">🚶 A pé</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- PONTO 5 – PONTO RECICLE -->
      <div class="ponto-rich-card" id="ponto5"
           data-nome="ponto recicle jardim america" data-material="papel plastico metal vidro papelao"
           data-status="fechado" data-lat="-21.7700" data-lng="-43.3600"
           data-endereco="Praça da Amizade – Jardim América">

        <div class="prc-header" onclick="togglePonto('ponto5')">
          <div class="prc-foto-wrap">
            <div class="prc-foto prc-foto-verde"><span>♻️</span></div>
            <span class="prc-status-dot fechado"></span>
          </div>
          <div class="prc-info">
            <div class="prc-title-row">
              <h3>Ponto Recicle – Jardim América</h3>
              <span class="prc-status fechado">🔴 Fechado agora</span>
            </div>
            <p class="prc-endereco">📍 Praça da Amizade – Jardim América</p>
            <div class="prc-materiais-preview">
              <span class="mat-chip">📄 Papel</span>
              <span class="mat-chip">🧴 Plástico</span>
              <span class="mat-chip">🥫 Metal</span>
              <span class="mat-chip">🍶 Vidro</span>
            </div>
          </div>
          <div class="prc-horario-resumo">
            <span class="prc-hoje-label">Próx. abertura</span>
            <span class="prc-hoje-hora">Ter 08h</span>
          </div>
          <button class="prc-toggle-btn" id="btn-ponto5">▼</button>
        </div>

        <div class="prc-expanded" id="exp-ponto5" style="display:none;">
          <div class="prc-expanded-inner">
            <div class="alert-box info-alert">
              ℹ️ Este ponto funciona apenas às <strong>terças, quintas e sábados</strong>. Próxima abertura: <strong>amanhã às 08h</strong>.
            </div>
            <div class="prc-details-grid">
              <div class="prc-detail-section">
                <h4>🕐 Horários</h4>
                <table class="horario-table">
                  <tr><td>Terça-feira</td><td><strong>08h – 14h</strong></td><td><span class="hst aberto">Aberto</span></td></tr>
                  <tr><td>Quinta-feira</td><td><strong>08h – 14h</strong></td><td><span class="hst aberto">Aberto</span></td></tr>
                  <tr><td>Sábado</td><td><strong>08h – 14h</strong></td><td><span class="hst aberto">Aberto</span></td></tr>
                  <tr><td>Outros dias</td><td><strong>–</strong></td><td><span class="hst fechado">Fechado</span></td></tr>
                </table>
              </div>
              <div class="prc-detail-section">
                <h4>✅ Materiais Aceitos</h4>
                <div class="materiais-aceitos-list">
                  <div class="ma-item aceito"><span>📄</span> Papel e papelão</div>
                  <div class="ma-item aceito"><span>🧴</span> Embalagens plásticas</div>
                  <div class="ma-item aceito"><span>🥫</span> Latas de metal</div>
                  <div class="ma-item aceito"><span>🍶</span> Vidros e garrafas</div>
                  <div class="ma-item nao-aceito"><span>🍎</span> Orgânicos</div>
                  <div class="ma-item nao-aceito"><span>🔋</span> Pilhas e eletrônicos</div>
                </div>
              </div>
              <div class="prc-detail-section">
                <h4>📞 Contato</h4>
                <div class="contato-list">
                  <div class="ct-item"><span>📞</span> (32) 3333-5005</div>
                  <div class="ct-item"><span>🌳</span> Área ao ar livre</div>
                </div>
                <div class="prc-avaliacao"><span>⭐ 4.3</span><span class="av-count">(16 avaliações)</span></div>
                <div class="prc-action-btns">
                  <button class="act-btn compartilhar" onclick="compartilhar('Ponto Recicle', 'Praça da Amizade')">🔗 Compartilhar</button>
                </div>
              </div>
            </div>
            <div class="prc-map-section">
              <div class="prc-map-header">
                <h4>🗺️ Localização</h4>
                <a href="https://www.google.com/maps/search/Praca+da+Amizade+Jardim+America+Juiz+de+Fora" target="_blank" class="open-maps-btn">Abrir no Google Maps ↗</a>
              </div>
              <div class="prc-map-embed">
                <iframe src="https://maps.google.com/maps?q=Jardim+America+Juiz+de+Fora+MG&output=embed&z=15" width="100%" height="280" style="border:0;border-radius:12px;" loading="lazy"></iframe>
              </div>
              <div class="prc-como-chegar">
                <a href="https://www.google.com/maps/dir//Jardim+America+Juiz+de+Fora" target="_blank" class="como-chegar-btn">🧭 Carro</a>
                <a href="https://www.google.com/maps/dir//Jardim+America+Juiz+de+Fora&travelmode=transit" target="_blank" class="como-chegar-btn">🚌 Ônibus</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- PONTO 6 – ESCOLA -->
      <div class="ponto-rich-card" id="ponto6"
           data-nome="escola municipal professor souza" data-material="papel livros papelao"
           data-status="aberto" data-lat="-21.7650" data-lng="-43.3520"
           data-endereco="Rua da Educação, 55 – Bairro Escolar">

        <div class="prc-header" onclick="togglePonto('ponto6')">
          <div class="prc-foto-wrap">
            <div class="prc-foto prc-foto-roxo"><span>🏫</span></div>
            <span class="prc-status-dot aberto"></span>
          </div>
          <div class="prc-info">
            <div class="prc-title-row">
              <h3>E.M. Prof. Souza</h3>
              <span class="prc-status aberto">🟢 Aberto</span>
            </div>
            <p class="prc-endereco">📍 Rua da Educação, 55 – Bairro Escolar</p>
            <div class="prc-materiais-preview">
              <span class="mat-chip">📄 Papel</span>
              <span class="mat-chip">📚 Livros</span>
              <span class="mat-chip">📦 Papelão</span>
            </div>
          </div>
          <div class="prc-horario-resumo">
            <span class="prc-hoje-label">Hoje</span>
            <span class="prc-hoje-hora">07h – 17h</span>
          </div>
          <button class="prc-toggle-btn" id="btn-ponto6">▼</button>
        </div>

        <div class="prc-expanded" id="exp-ponto6" style="display:none;">
          <div class="prc-expanded-inner">
            <div class="alert-box info-alert">ℹ️ Apenas em período letivo (fev–nov). Férias de julho: fechado.</div>
            <div class="prc-details-grid">
              <div class="prc-detail-section">
                <h4>🕐 Horários</h4>
                <table class="horario-table">
                  <tr><td>Seg a Sex (letivo)</td><td><strong>07h – 17h</strong></td><td><span class="hst aberto">Aberto</span></td></tr>
                  <tr><td>Férias/Feriados</td><td><strong>–</strong></td><td><span class="hst fechado">Fechado</span></td></tr>
                </table>
              </div>
              <div class="prc-detail-section">
                <h4>✅ Materiais Aceitos</h4>
                <div class="materiais-aceitos-list">
                  <div class="ma-item aceito"><span>📄</span> Papel limpo e seco</div>
                  <div class="ma-item aceito"><span>📚</span> Livros e revistas</div>
                  <div class="ma-item aceito"><span>📦</span> Caixas de papelão</div>
                  <div class="ma-item nao-aceito"><span>🧴</span> Plástico</div>
                  <div class="ma-item nao-aceito"><span>🥫</span> Metal</div>
                </div>
              </div>
              <div class="prc-detail-section">
                <h4>📞 Contato</h4>
                <div class="contato-list">
                  <div class="ct-item"><span>📞</span> (32) 3333-6006</div>
                  <div class="ct-item"><span>♿</span> Acessível para PCD</div>
                </div>
                <div class="prc-avaliacao"><span>⭐ 4.6</span><span class="av-count">(12 avaliações)</span></div>
              </div>
            </div>
            <div class="prc-map-section">
              <div class="prc-map-header">
                <h4>🗺️ Localização</h4>
                <a href="https://www.google.com/maps/search/Rua+da+Educacao+55+Juiz+de+Fora" target="_blank" class="open-maps-btn">Abrir no Google Maps ↗</a>
              </div>
              <div class="prc-map-embed">
                <iframe src="https://maps.google.com/maps?q=Rua+da+Educacao+55+Juiz+de+Fora+MG&output=embed&z=16" width="100%" height="280" style="border:0;border-radius:12px;" loading="lazy"></iframe>
              </div>
              <div class="prc-como-chegar">
                <a href="https://www.google.com/maps/dir//Rua+da+Educacao+55+Juiz+de+Fora" target="_blank" class="como-chegar-btn">🧭 Carro</a>
                <a href="https://www.google.com/maps/dir//Rua+da+Educacao+55+Juiz+de+Fora&travelmode=transit" target="_blank" class="como-chegar-btn">🚌 Ônibus</a>
                <a href="https://www.google.com/maps/dir//Rua+da+Educacao+55+Juiz+de+Fora&travelmode=walking" target="_blank" class="como-chegar-btn">🚶 A pé</a>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div><!-- fim grid -->

    <div id="semResultados" style="display:none;" class="sem-resultados-rich">
      <span>😕</span>
      <p>Nenhum ponto encontrado para esta busca.</p>
      <button onclick="limparBusca()" class="btn-outline-green">Limpar filtros</button>
    </div>

  </div><!-- fim container -->

  <!-- MAPA GERAL -->
  <section class="mapa-geral-section">
    <div class="container">
      <div class="mapa-geral-header">
        <h2>🗺️ Todos os pontos no mapa</h2>
        <a href="https://www.google.com/maps/search/pontos+de+reciclagem+Juiz+de+Fora" target="_blank" class="btn-outline-green">Ver no Google Maps ↗</a>
      </div>
      <div class="mapa-geral-embed">
        <iframe
          src="https://maps.google.com/maps?q=Juiz+de+Fora+MG+reciclagem&output=embed&z=13"
          width="100%" height="400"
          style="border:0;border-radius:16px;"
          allowfullscreen="" loading="lazy">
        </iframe>
      </div>
    </div>
  </section>

  <!-- SUGIRA UM PONTO -->
  <section class="container sugira-section">
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
        <button onclick="document.getElementById('modalSugira').style.display='none'" style="background:none;border:none;font-size:1.3rem;cursor:pointer;">✕</button>
      </div>
      <div class="form-group"><label>Nome do local *</label><input type="text" placeholder="Ex: Ecoponto Vila Rica"></div>
      <div class="form-group"><label>Endereço *</label><input type="text" placeholder="Rua, número – Bairro"></div>
      <div class="form-group"><label>Materiais aceitos</label><input type="text" placeholder="Ex: Pilhas, eletrônicos, papel..."></div>
      <div class="form-group"><label>Horário de funcionamento</label><input type="text" placeholder="Ex: Seg-Sex 08h-17h"></div>
      <div class="form-group"><label>Observações</label><textarea rows="3" placeholder="Qualquer informação adicional..."></textarea></div>
      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:16px;">
        <button class="btn-primary" onclick="enviarSugestao()">Enviar sugestão</button>
        <button class="btn-secondary" onclick="document.getElementById('modalSugira').style.display='none'">Cancelar</button>
      </div>
    </div>
  </div>

  <!-- TOAST -->
  <div id="toastMsg" style="display:none;position:fixed;bottom:24px;right:24px;background:#1e293b;color:#fff;padding:14px 22px;border-radius:12px;font-weight:600;font-size:.9rem;z-index:9999;box-shadow:0 4px 16px rgba(0,0,0,.2);"></div>

  <footer class="footer">
    <div class="container footer-inner">
      <div class="footer-brand">
        <span class="logo" style="color:#4ade80;font-size:1.3rem;">♻️ ColetaFácil</span>
        <p>Facilitando o descarte correto para toda a comunidade.</p>
      </div>
      <div class="footer-links">
        <h4>Navegação</h4>
        <ul>
          <li><a href="index.php">Início</a></li>
          <li><a href="materiais.php">Guia de Materiais</a></li>
          <li><a href="pontos.php">Pontos de Descarte</a></li>
          <li><a href="coleta.php">Consultar Coleta</a></li>
          <li><a href="ocorrencias.php">Ocorrências</a></li>
        </ul>
      </div>
      <div class="footer-links">
        <h4>Projeto</h4>
        <ul>
          <li>Curso: ADS</li>
          <li>Extensão Universitária III</li>
          <li>Prof. Aldecir Fonseca</li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom"><p>© 2026 ColetaFácil – Projeto de Extensão Universitária III</p></div>
  </footer>

  <script src="script.js"></script>
  <script src="pontos.js"></script>
</body>
</html>
