<?php require_once __DIR__ . '/auth.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Guia de Materiais – ColetaFácil</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <nav class="navbar">
    <div class="container nav-inner">
      <a href="index.php" class="logo"><span class="logo-icon">♻️</span><span>ColetaFácil</span></a>
      <ul class="nav-links">
        <li><a href="index.php">Início</a></li>
        <li><a href="coleta.php">🗓️ Coleta</a></li>
        <li><a href="materiais.php" class="active">Materiais</a></li>
        <li><a href="pontos.php">Pontos</a></li>
        <li><a href="ocorrencias.php">Ocorrências</a></li>
        <li><a href="kids.php" class="nav-kids">🧒 Kids</a></li>
        <li><a href="recompensas.php" class="nav-reward">⭐ Pontos</a></li>
        <?= site_auth_links() ?>
      </ul>
      <button class="menu-btn" onclick="toggleMenu()">☰</button>
    </div>
    <div class="mobile-menu" id="mobileMenu">
      <a href="index.php">🏠 Início</a>
      <a href="coleta.php">🗓️ Coleta</a>
      <a href="materiais.php">📦 Materiais</a>
      <a href="pontos.php">📍 Pontos</a>
      <a href="ocorrencias.php">🚨 Ocorrências</a>
      <a href="kids.php">🧒 Kids</a>
      <a href="recompensas.php">⭐ Recompensas</a>
      <?= site_auth_mobile_links() ?>
    </div>
  </nav>

  <!-- PAGE HEADER -->
  <div class="page-header">
    <div class="container">
      <h1>📦 Guia de Materiais</h1>
      <p>Saiba o que pode ser reciclado, como preparar e o que não colocar na coleta seletiva.</p>
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

  <footer class="footer">
    <div class="container footer-inner">
      <div class="footer-brand">
        <span class="logo" style="color:#4ade80;font-size:1.3rem;">♻️ ColetaFácil</span>
        <p>Promovendo a coleta seletiva e a conscientização ambiental.</p>
      </div>
      <div class="footer-links">
        <h4>Navegação</h4>
        <ul>
          <li><a href="index.php">Início</a></li>
          <li><a href="coleta.php">Consultar Coleta</a></li>
          <li><a href="materiais.php">Guia de Materiais</a></li>
          <li><a href="pontos.php">Pontos de Descarte</a></li>
          <li><a href="ocorrencias.php">Ocorrências</a></li>
          <li><a href="recompensas.php">Recompensas</a></li>
          <li><a href="kids.php">Área Kids</a></li>
        </ul>
      </div>
      <div class="footer-links">
        <h4>Projeto</h4>
        <ul>
          <li>Curso: ADS</li>
          <li>Extensão Universitária III</li>
          <li>Prof. Aldecir Fonseca</li>
          <li style="margin-top:8px;color:#94a3b8;">Heron Leal</li>
          <li style="color:#94a3b8;">Nattan Silva</li>
          <li style="color:#94a3b8;">Paulo Victor</li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© 2026 ColetaFácil – Projeto de Extensão Universitária III – ADS</p>
    </div>
  </footer>

  <script src="script.js"></script>
</body>
</html>
