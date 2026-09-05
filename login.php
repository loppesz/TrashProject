<?php
require_once __DIR__ . '/auth.php';

// Se já estiver autenticado, redireciona para o destino adequado
if (!empty($_SESSION['id_usuario'])) {
  $destino = auth_next_path($_GET['next'] ?? null);
    header('Location: ' . $destino);
    exit;
}

// =============================================
// Processamento do formulário POST
// =============================================
$erro        = '';
$email_input = '';
$next        = auth_next_path($_GET['next'] ?? $_POST['next'] ?? null);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email_input = trim($_POST['email'] ?? '');
    $senha       = $_POST['senha'] ?? '';

    if ($email_input === '' || $senha === '') {
        $erro = 'Preencha o e-mail e a senha para continuar.';
    } else {
        try {
          $stmt = auth_db()->prepare(
            'SELECT id_usuario, nome, senha_hash, tipo, ativo FROM usuario WHERE email = ? LIMIT 1'
          );
          $stmt->execute([$email_input]);
          $usuario = $stmt->fetch();
          if (!$usuario || !$usuario['ativo'] || !password_verify($senha, $usuario['senha_hash'])) {
            $erro = 'E-mail ou senha incorretos.';
          } else {
            auth_login($usuario);
                $destino = $next;
            header('Location: ' . $destino);
            exit;
          }
        } catch (Throwable $e) {
          $erro = 'Não foi possível conectar ao banco de dados.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <script>
    (function(){
      var t = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
      if(t === 'dark') document.documentElement.classList.add('dark');
    })();
  </script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Acesse sua conta no ColetaFácil – Plataforma de coleta seletiva de Muriaé-MG.">
  <title>Entrar – ColetaFácil</title>
  <link rel="manifest" href="manifest.json">
  <meta name="theme-color" content="#16a34a">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="ColetaFácil">
  <link rel="stylesheet" href="style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    /* =============================================
       LOGIN – ESTILOS ESPECÍFICOS
       ============================================= */

    /* Hero da tela de login */
    .login-section {
      min-height: calc(100vh - 64px - 240px);
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #134e4a 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 48px 20px;
      position: relative;
      overflow: hidden;
    }

    /* Formas decorativas (igual ao hero-v2) */
    .login-bg-shapes { position: absolute; inset: 0; pointer-events: none; }
    .login-bg-shapes .shape { position: absolute; border-radius: 50%; opacity: 0.06; }
    .login-bg-shapes .s1 { width: 420px; height: 420px; background: #4ade80; top: -120px; right: -80px; }
    .login-bg-shapes .s2 { width: 260px; height: 260px; background: #22d3ee; bottom: -80px; left: 5%; }
    .login-bg-shapes .s3 { width: 160px; height: 160px; background: #f59e0b; top: 40%; left: 42%; }

    /* Layout de duas colunas */
    .login-inner {
      position: relative;
      z-index: 2;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 56px;
      align-items: center;
      max-width: 900px;
      width: 100%;
    }

    /* Lado esquerdo – copy */
    .login-brand { color: #fff; }

    .login-brand .hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: rgba(74,222,128,0.15);
      border: 1px solid rgba(74,222,128,0.3);
      color: #4ade80;
      font-size: 0.8rem;
      font-weight: 600;
      padding: 6px 14px;
      border-radius: 20px;
      margin-bottom: 24px;
    }

    .login-brand .badge-dot {
      width: 8px; height: 8px;
      background: #4ade80; border-radius: 50%;
      animation: pulse 2s infinite;
    }

    .login-brand h1 {
      font-size: 2.4rem;
      font-weight: 900;
      line-height: 1.15;
      margin-bottom: 16px;
    }

    .login-brand h1 .text-green { color: #4ade80; }

    .login-brand p {
      color: #94a3b8;
      font-size: 0.95rem;
      line-height: 1.7;
      margin-bottom: 28px;
    }

    .login-features { display: flex; flex-direction: column; gap: 12px; }

    .login-feature-item {
      display: flex;
      align-items: center;
      gap: 10px;
      color: #cbd5e1;
      font-size: 0.88rem;
    }

    .lfi-icon {
      width: 32px; height: 32px;
      background: rgba(74,222,128,0.12);
      border: 1px solid rgba(74,222,128,0.25);
      border-radius: 8px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1rem;
      flex-shrink: 0;
    }

    /* Card do formulário */
    .login-card {
      background: #fff;
      border-radius: 20px;
      padding: 36px 32px;
      box-shadow: 0 8px 40px rgba(0,0,0,0.3);
    }

    .login-card-header { text-align: center; margin-bottom: 28px; }
    .login-card-header .card-icon { font-size: 2.4rem; margin-bottom: 10px; display: block; }
    .login-card-header h2 { font-size: 1.4rem; font-weight: 800; color: var(--gray-900); margin-bottom: 6px; }
    .login-card-header p  { font-size: 0.85rem; color: var(--gray-500); }

    /* Alerta de erro */
    .login-error {
      background: #fee2e2;
      border-left: 4px solid var(--red);
      border-radius: 8px;
      padding: 12px 16px;
      margin-bottom: 20px;
      font-size: 0.88rem;
      color: #b91c1c;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    /* Alerta de sucesso (para mensagens vindas via query string) */
    .login-success {
      background: #dcfce7;
      border-left: 4px solid var(--green);
      border-radius: 8px;
      padding: 12px 16px;
      margin-bottom: 20px;
      font-size: 0.88rem;
      color: #15803d;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    /* Campo com ícone */
    .input-icon-wrap {
      position: relative;
    }
    .input-icon-wrap .field-icon {
      position: absolute;
      left: 13px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 1rem;
      pointer-events: none;
    }
    .input-icon-wrap input {
      padding-left: 40px !important;
    }

    /* Toggle de visibilidade da senha */
    .input-icon-wrap .senha-toggle {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      cursor: pointer;
      font-size: 1rem;
      color: var(--gray-500);
      padding: 4px;
      line-height: 1;
    }
    .input-icon-wrap .senha-toggle:hover { color: var(--green); }
    .input-icon-wrap input[type="password"],
    .input-icon-wrap input[type="text"] {
      padding-right: 40px !important;
    }

    /* Linha lembrar / esqueci */
    .login-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 20px;
      font-size: 0.82rem;
    }
    .login-row label {
      display: flex;
      align-items: center;
      gap: 6px;
      color: var(--gray-700);
      cursor: pointer;
      font-weight: 500;
      margin: 0;
    }
    .login-row label input[type="checkbox"] {
      width: 16px;
      height: 16px;
      accent-color: var(--green);
      cursor: pointer;
    }
    .login-row a {
      color: var(--green);
      font-weight: 600;
      transition: color 0.2s;
    }
    .login-row a:hover { color: var(--green-dark); text-decoration: underline; }

    /* Botão de submit */
    .btn-login {
      width: 100%;
      background: var(--green);
      color: #fff;
      border: none;
      padding: 13px 24px;
      border-radius: var(--radius);
      font-size: 1rem;
      font-weight: 700;
      cursor: pointer;
      transition: background 0.2s, transform 0.1s;
      font-family: inherit;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }
    .btn-login:hover { background: var(--green-dark); }
    .btn-login:active { transform: scale(0.98); }
    .btn-login:disabled { background: var(--gray-300); cursor: not-allowed; }

    /* Divider */
    .login-divider {
      display: flex;
      align-items: center;
      gap: 12px;
      margin: 22px 0;
      color: var(--gray-300);
      font-size: 0.8rem;
    }
    .login-divider::before,
    .login-divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: var(--gray-200);
    }

    /* Link de cadastro */
    .login-signup {
      text-align: center;
      font-size: 0.85rem;
      color: var(--gray-500);
    }
    .login-signup a {
      color: var(--green);
      font-weight: 700;
      transition: color 0.2s;
    }
    .login-signup a:hover { color: var(--green-dark); text-decoration: underline; }

    /* Dark mode */
    html.dark .login-card {
      background: #1e293b;
      box-shadow: 0 8px 40px rgba(0,0,0,0.5);
    }
    html.dark .login-card-header h2 { color: #f1f5f9; }
    html.dark .login-card-header p  { color: #94a3b8; }
    html.dark .form-group label     { color: #cbd5e1; }
    html.dark .form-group input,
    html.dark .form-group select    {
      background: #0f172a;
      border-color: #334155;
      color: #f1f5f9;
    }
    html.dark .form-group input:focus { border-color: #4ade80; }
    html.dark .login-row label       { color: #cbd5e1; }
    html.dark .login-divider         { color: #475569; }
    html.dark .login-divider::before,
    html.dark .login-divider::after  { background: #334155; }
    html.dark .login-signup          { color: #94a3b8; }

    /* Responsivo */
    @media (max-width: 768px) {
      .login-inner {
        grid-template-columns: 1fr;
        gap: 32px;
      }
      .login-brand { text-align: center; }
      .login-brand h1 { font-size: 1.8rem; }
      .login-features { align-items: center; }
      .login-card { padding: 28px 22px; }
    }

    @media (max-width: 480px) {
      .login-section { padding: 32px 16px; }
      .login-card    { padding: 24px 18px; }
    }
  </style>
</head>
<body>

  <!-- NAVBAR -->
  <nav class="navbar">
    <div class="container nav-inner">
      <a href="index.php" class="logo">
        <span class="logo-icon">♻️</span>
        <span>ColetaFácil</span>
      </a>
      <ul class="nav-links">
        <li><a href="index.php">Início</a></li>
        <li><a href="coleta.php">🗓️ Coleta</a></li>
        <li><a href="materiais.php">Materiais</a></li>
        <li><a href="pontos.php">Pontos</a></li>
        <li><a href="ocorrencias.php">Ocorrências</a></li>
        <li><a href="kids.php" class="nav-kids">🧒 Kids</a></li>
        <li><a href="recompensas.php" class="nav-reward">⭐ Pontos</a></li>
        <li><a href="login.php" class="active" style="background:var(--green-light);color:var(--green-dark);font-weight:700;">Entrar</a></li>
      </ul>
      <button id="themeToggle" class="theme-toggle-btn" onclick="toggleTheme()" aria-label="Alternar tema"></button>
      <button class="menu-btn" onclick="toggleMenu()">☰</button>
    </div>
    <div class="mobile-menu" id="mobileMenu">
      <a href="index.php">🏠 Início</a>
      <a href="coleta.php">🗓️ Consultar Coleta</a>
      <a href="materiais.php">📦 Materiais</a>
      <a href="pontos.php">📍 Pontos</a>
      <a href="ocorrencias.php">🚨 Ocorrências</a>
      <a href="kids.php">🧒 Kids</a>
      <a href="recompensas.php">⭐ Recompensas</a>
      <a href="login.php">🔑 Entrar</a>
    </div>
  </nav>

  <!-- =============================================
       SEÇÃO DE LOGIN
  ============================================= -->
  <section class="login-section">
    <div class="login-bg-shapes">
      <div class="shape s1"></div>
      <div class="shape s2"></div>
      <div class="shape s3"></div>
    </div>

    <div class="login-inner">

      <!-- LADO ESQUERDO – apresentação -->
      <div class="login-brand animate-in">
        <div class="hero-badge">
          <span class="badge-dot"></span>
          🌱 Plataforma de Conscientização Ambiental
        </div>
        <h1>Bem-vindo de<br>volta ao <span class="text-green">ColetaFácil</span></h1>
        <p>
          Acesse sua conta para acompanhar pontos, reportar ocorrências e
          ajudar a manter Muriaé-MG mais limpa.
        </p>

        <div class="login-features">
          <div class="login-feature-item">
            <div class="lfi-icon">🗓️</div>
            <span>Consulte o calendário de coleta do seu bairro</span>
          </div>
          <div class="login-feature-item">
            <div class="lfi-icon">🚨</div>
            <span>Reporte ocorrências e acompanhe o status</span>
          </div>
          <div class="login-feature-item">
            <div class="lfi-icon">⭐</div>
            <span>Acumule pontos e troque por recompensas</span>
          </div>
          <div class="login-feature-item">
            <div class="lfi-icon">🏆</div>
            <span>Apareça no ranking dos heróis da reciclagem</span>
          </div>
        </div>
      </div>

      <!-- LADO DIREITO – formulário -->
      <div class="login-card animate-in delay-1">
        <div class="login-card-header">
          <span class="card-icon">🔑</span>
          <h2>Entrar na sua conta</h2>
          <p>Use o e-mail e senha cadastrados</p>
        </div>

        <?php if (!empty($erro)): ?>
          <div class="login-error" role="alert">
            <span>⚠️</span>
            <?= htmlspecialchars($erro) ?>
          </div>
        <?php endif; ?>

        <?php if (isset($_GET['cadastro']) && $_GET['cadastro'] === 'ok'): ?>
          <div class="login-success" role="status">
            <span>✅</span>
            Cadastro realizado! Faça login para continuar.
          </div>
        <?php endif; ?>

        <?php if (isset($_GET['sessao']) && $_GET['sessao'] === 'expirada'): ?>
          <div class="login-error" role="alert">
            <span>⏳</span>
            Sua sessão expirou. Faça login novamente.
          </div>
        <?php endif; ?>

        <form method="POST" action="login.php" id="loginForm" novalidate>
          <input type="hidden" name="next" value="<?= htmlspecialchars($next, ENT_QUOTES, 'UTF-8') ?>">

          <!-- E-mail -->
          <div class="form-group">
            <label for="email">E-mail</label>
            <div class="input-icon-wrap">
              <span class="field-icon">📧</span>
              <input
                type="email"
                id="email"
                name="email"
                placeholder="seu@email.com"
                value="<?= htmlspecialchars($email_input) ?>"
                autocomplete="email"
                required
                autofocus
              >
            </div>
          </div>

          <!-- Senha -->
          <div class="form-group">
            <label for="senha">Senha</label>
            <div class="input-icon-wrap">
              <span class="field-icon">🔒</span>
              <input
                type="password"
                id="senha"
                name="senha"
                placeholder="••••••••"
                autocomplete="current-password"
                required
              >
              <button
                type="button"
                class="senha-toggle"
                onclick="toggleSenha()"
                aria-label="Mostrar ou ocultar senha"
                id="senhaToggleBtn"
              >👁️</button>
            </div>
          </div>

          <!-- Lembrar / Esqueci -->
          <div class="login-row">
            <label>
              <input type="checkbox" name="lembrar" id="lembrar">
              Lembrar de mim
            </label>
            <a href="recuperar-senha.php">Esqueci minha senha</a>
          </div>

          <!-- Botão de submit -->
          <button type="submit" class="btn-login" id="btnLogin">
            <span id="btnLoginText">🔑 Entrar</span>
            <span id="btnLoginLoad" style="display:none;">⏳ Verificando...</span>
          </button>

        </form>

        <div class="login-divider">ou</div>

        <div class="login-signup">
          Não tem conta?
          <a href="cadastro.php">Cadastre-se grátis</a>
        </div>
      </div>

    </div>
  </section>

  <!-- FOOTER -->
  <footer class="footer">
    <div class="container footer-inner">
      <div class="footer-brand">
        <span class="logo" style="color:#4ade80; font-size:1.3rem;">♻️ ColetaFácil</span>
        <p>Promovendo a coleta seletiva e a conscientização ambiental na nossa comunidade.</p>
        <div class="footer-social">
          <a href="#" title="Instagram">📸</a>
          <a href="#" title="Facebook">📘</a>
          <a href="#" title="WhatsApp">💬</a>
        </div>
      </div>
      <div class="footer-links">
        <h4>Plataforma</h4>
        <ul>
          <li><a href="index.php">Início</a></li>
          <li><a href="materiais.php">Guia de Materiais</a></li>
          <li><a href="pontos.php">Pontos de Descarte</a></li>
          <li><a href="ocorrencias.php">Registrar Ocorrência</a></li>
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
          <li style="margin-top:8px; color:#94a3b8;">Heron Leal – (32) 9 8411-6731</li>
          <li style="color:#94a3b8;">Nattan Silva – (32) 9 9917-3481</li>
          <li style="color:#94a3b8;">Paulo Victor – (32) 9 8809-4464</li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© 2026 ColetaFácil – Projeto de Extensão Universitária III – ADS</p>
    </div>
  </footer>

  <script src="script.js"></script>
  <script>
    // =============================================
    // TOGGLE VISIBILIDADE DA SENHA
    // =============================================
    function toggleSenha() {
      const input = document.getElementById('senha');
      const btn   = document.getElementById('senhaToggleBtn');
      if (!input) return;
      const escondida = input.type === 'password';
      input.type = escondida ? 'text' : 'password';
      btn.textContent = escondida ? '🙈' : '👁️';
      btn.setAttribute('aria-label', escondida ? 'Ocultar senha' : 'Mostrar senha');
    }

    // =============================================
    // FEEDBACK VISUAL NO SUBMIT
    // =============================================
    document.getElementById('loginForm')?.addEventListener('submit', function (e) {
      const email = document.getElementById('email').value.trim();
      const senha = document.getElementById('senha').value;

      // Validação básica antes de enviar
      if (!email || !senha) {
        e.preventDefault();
        return;
      }

      const btn      = document.getElementById('btnLogin');
      const txtNorm  = document.getElementById('btnLoginText');
      const txtLoad  = document.getElementById('btnLoginLoad');
      if (btn && txtNorm && txtLoad) {
        btn.disabled      = true;
        txtNorm.style.display = 'none';
        txtLoad.style.display = 'inline';
      }
    });
  </script>
</body>
</html>
