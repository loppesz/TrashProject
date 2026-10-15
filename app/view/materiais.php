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
      <div class="coletor-destaque-img" style="background-image:url('<?= baseUrl('assets/img/hero-coletor.jpg') ?>')" role="img" aria-label="Coletor de resíduos sorrindo durante o trabalho"></div>
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

  <!-- LISTA DE MATERIAIS -->
  <section class="container materiais-section">
    <div class="materiais-grid" id="materiaisGrid">

      <div class="material-card" data-tipo="aceito">
        <div class="material-header azul">
          <span class="material-icon">📄</span>
          <span class="material-status aceito">✅ Aceito</span>
        </div>
        <h3>Papel e Papelão</h3>
        <p>Jornais, revistas, caixas, cadernos, papel de escritório.</p>
        <div class="material-preparo">
          <strong>Como preparar:</strong>
          <ul>
            <li>Retire grampos, clipes e fitas adesivas</li>
            <li>Mantenha seco — papel molhado não é reciclável</li>
            <li>Dobre caixas para economizar espaço</li>
          </ul>
        </div>
        <div class="exemplos">
          <span class="tag">📰 Jornal</span>
          <span class="tag">📦 Caixa</span>
          <span class="tag">📚 Revista</span>
        </div>
      </div>

      <div class="material-card" data-tipo="aceito">
        <div class="material-header vermelho">
          <span class="material-icon">🧴</span>
          <span class="material-status aceito">✅ Aceito</span>
        </div>
        <h3>Plástico</h3>
        <p>Garrafas PET, embalagens de produtos de limpeza, sacolas plásticas.</p>
        <div class="material-preparo">
          <strong>Como preparar:</strong>
          <ul>
            <li>Lave as embalagens com água</li>
            <li>Amasse garrafas PET e retire tampas</li>
            <li>Sacolas podem ser agrupadas em uma só</li>
          </ul>
        </div>
        <div class="exemplos">
          <span class="tag">🧴 Frasco</span>
          <span class="tag">🍼 PET</span>
          <span class="tag">🛍️ Sacola</span>
        </div>
      </div>

      <div class="material-card" data-tipo="aceito">
        <div class="material-header amarelo">
          <span class="material-icon">🥫</span>
          <span class="material-status aceito">✅ Aceito</span>
        </div>
        <h3>Metal</h3>
        <p>Latas de alumínio, latas de aço, panelas velhas (sem cabo de madeira).</p>
        <div class="material-preparo">
          <strong>Como preparar:</strong>
          <ul>
            <li>Lave as latas antes de descartar</li>
            <li>Amasse para economizar espaço</li>
            <li>⚠️ Cuidado com bordas cortantes — use luvas</li>
          </ul>
        </div>
        <div class="exemplos">
          <span class="tag">🥤 Lata</span>
          <span class="tag">🥫 Conserva</span>
          <span class="tag">🔩 Metal</span>
        </div>
      </div>

      <div class="material-card" data-tipo="cuidado">
        <div class="material-header verde">
          <span class="material-icon">🍶</span>
          <span class="material-status cuidado">⚠️ Cuidado</span>
        </div>
        <h3>Vidro</h3>
        <p>Garrafas, potes, frascos de perfume e vidros de conserva.</p>
        <div class="material-preparo">
          <strong>Como preparar:</strong>
          <ul>
            <li>Lave os vidros antes do descarte</li>
            <li>Nunca misture vidros inteiros com quebrados</li>
            <li>⚠️ Vidros quebrados: embrulhe em papel jornal e identifique "VIDRO QUEBRADO"</li>
          </ul>
        </div>
        <div class="exemplos">
          <span class="tag">🍷 Garrafa</span>
          <span class="tag">🫙 Pote</span>
          <span class="tag">🧪 Frasco</span>
        </div>
      </div>

      <div class="material-card" data-tipo="nao-aceito">
        <div class="material-header cinza">
          <span class="material-icon">🍌</span>
          <span class="material-status nao-aceito">❌ Não Aceito</span>
        </div>
        <h3>Lixo Orgânico</h3>
        <p>Restos de comida, cascas de frutas, borra de café, alimentos em geral.</p>
        <div class="material-preparo">
          <strong>Alternativas:</strong>
          <ul>
            <li>Compostagem doméstica</li>
            <li>Descarte no lixo comum (lixeira marrom)</li>
          </ul>
        </div>
        <div class="exemplos">
          <span class="tag negativo">🍎 Casca</span>
          <span class="tag negativo">🍗 Restos</span>
        </div>
      </div>

      <div class="material-card" data-tipo="nao-aceito">
        <div class="material-header preto">
          <span class="material-icon">💉</span>
          <span class="material-status nao-aceito">❌ Não Aceito</span>
        </div>
        <h3>Materiais Perfurocortantes</h3>
        <p>Seringas, agulhas, bisturis, lâminas de barbear.</p>
        <div class="material-preparo">
          <strong>Como descartar:</strong>
          <ul>
            <li>🔴 NUNCA coloque na coleta seletiva</li>
            <li>Leve a uma farmácia ou unidade de saúde</li>
            <li>Risco grave de acidente para os coletores</li>
          </ul>
        </div>
        <div class="alerta-vermelho">
          🔴 Risco de acidente grave para os profissionais da coleta!
        </div>
      </div>

      <div class="material-card" data-tipo="nao-aceito">
        <div class="material-header laranja">
          <span class="material-icon">🔋</span>
          <span class="material-status nao-aceito">❌ Não Aceito</span>
        </div>
        <h3>Pilhas e Baterias</h3>
        <p>Pilhas AA, AAA, baterias de celular, bateria de carro.</p>
        <div class="material-preparo">
          <strong>Como descartar:</strong>
          <ul>
            <li>Procure pontos de coleta específicos em supermercados</li>
            <li>Lojas de eletrônicos geralmente aceitam</li>
            <li>Nunca jogue no lixo comum ou coleta seletiva</li>
          </ul>
        </div>
      </div>

      <div class="material-card" data-tipo="nao-aceito">
        <div class="material-header marrom">
          <span class="material-icon">💊</span>
          <span class="material-status nao-aceito">❌ Não Aceito</span>
        </div>
        <h3>Medicamentos</h3>
        <p>Remédios vencidos ou sobras de medicamentos.</p>
        <div class="material-preparo">
          <strong>Como descartar:</strong>
          <ul>
            <li>Leve a uma farmácia com programa de descarte</li>
            <li>Nunca jogue no vaso sanitário ou lixo comum</li>
          </ul>
        </div>
      </div>

    </div>
  </section>

<?php require_once 'app/view/comuns/rodape.php'; ?>
