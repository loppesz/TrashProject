<?php
require_once __DIR__ . '/auth.php';

// Se já estiver autenticado, não precisa cadastrar
if (!empty($_SESSION['id_usuario'])) {
    $destino = auth_next_path($_GET['next'] ?? null);
    header('Location: ' . $destino);
    exit;
}

// =============================================
// Processamento do formulário POST
// =============================================
$erro   = '';
$sucesso = false;

// Campos para repopular o formulário em caso de erro
$form = [
    'nome'      => '',
    'email'     => '',
    'telefone'  => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $form['nome']     = trim($_POST['nome']     ?? '');
    $form['email']    = trim($_POST['email']    ?? '');
    $form['telefone'] = trim($_POST['telefone'] ?? '');
    $senha            = $_POST['senha']         ?? '';
    $senha_conf       = $_POST['senha_conf']    ?? '';
    $termos           = isset($_POST['termos']);

    // ── Validações ──────────────────────────────
    if ($form['nome'] === '') {
        $erro = 'Informe seu nome completo.';
    } elseif (strlen($form['nome']) < 3) {
        $erro = 'O nome deve ter pelo menos 3 caracteres.';
    } elseif ($form['email'] === '' || !filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $erro = 'Informe um e-mail válido.';
    } elseif (strlen($senha) < 8) {
        $erro = 'A senha deve ter pelo menos 8 caracteres.';
    } elseif ($senha !== $senha_conf) {
        $erro = 'As senhas não coincidem. Verifique e tente novamente.';
    } elseif (!$termos) {
        $erro = 'Você precisa aceitar os termos de uso para continuar.';
    } else {
        try {
          $pdo = auth_db();
          $check = $pdo->prepare('SELECT id_usuario FROM usuario WHERE email = ? LIMIT 1');
          $check->execute([$form['email']]);
          if ($check->fetch()) {
            $erro = 'Este e-mail já está cadastrado. Faça login ou recupere sua senha.';
          } else {
            $stmt = $pdo->prepare(
              'INSERT INTO usuario (nome, email, senha_hash, telefone, tipo, ativo)
               VALUES (?, ?, ?, ?, \'MORADOR\', TRUE)'
            );
            $stmt->execute([
              $form['nome'],
              $form['email'],
              password_hash($senha, PASSWORD_DEFAULT),
              $form['telefone'] !== '' ? $form['telefone'] : null,
            ]);
            header('Location: login.php?cadastro=ok');
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
  <meta name="description" content="Crie sua conta no ColetaFácil e comece a contribuir com a coleta seletiva de Muriaé-MG.">
  <title>Criar conta – ColetaFácil</title>
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
       CADASTRO – ESTILOS ESPECÍFICOS
       (reutiliza as mesmas classes do login.php)
       ============================================= */

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

    .login-bg-shapes { position: absolute; inset: 0; pointer-events: none; }
    .login-bg-shapes .shape { position: absolute; border-radius: 50%; opacity: 0.06; }
    .login-bg-shapes .s1 { width: 420px; height: 420px; background: #4ade80; top: -120px; right: -80px; }
    .login-bg-shapes .s2 { width: 260px; height: 260px; background: #22d3ee; bottom: -80px; left: 5%; }
    .login-bg-shapes .s3 { width: 160px; height: 160px; background: #f59e0b; top: 40%; left: 42%; }

    /* Layout – card de cadastro é mais largo (coluna única centralizada) */
    .login-inner {
      position: relative;
      z-index: 2;
      display: grid;
      grid-template-columns: 1fr 1.2fr;
      gap: 56px;
      align-items: start;
      max-width: 960px;
      width: 100%;
    }

    /* Lado esquerdo – copy */
    .login-brand { color: #fff; position: sticky; top: 88px; }

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
      font-size: 2.2rem;
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

    /* Alertas */
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

    /* Campo com ícone */
    .input-icon-wrap { position: relative; }

    .input-icon-wrap .field-icon {
      position: absolute;
      left: 13px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 1rem;
      pointer-events: none;
    }

    .input-icon-wrap input { padding-left: 40px !important; }

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
    .input-icon-wrap input[type="text"] { padding-right: 40px !important; }

    /* Força da senha */
    .senha-strength {
      margin-top: 6px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .strength-bars {
      display: flex;
      gap: 4px;
      flex: 1;
    }
    .strength-bar {
      flex: 1;
      height: 4px;
      border-radius: 2px;
      background: var(--gray-200);
      transition: background 0.3s;
    }
    .strength-bar.fraca    { background: var(--red); }
    .strength-bar.media    { background: var(--yellow); }
    .strength-bar.forte    { background: var(--green); }
    .strength-label {
      font-size: 0.75rem;
      font-weight: 600;
      min-width: 52px;
      text-align: right;
      color: var(--gray-500);
      transition: color 0.3s;
    }
    .strength-label.fraca { color: var(--red); }
    .strength-label.media { color: var(--yellow); }
    .strength-label.forte { color: var(--green); }

    /* Dois campos na mesma linha (senha / confirmar senha) */
    .form-row-2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    /* Termos de uso */
    .termos-group {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      margin-bottom: 20px;
      font-size: 0.82rem;
      color: var(--gray-700);
      line-height: 1.5;
    }
    .termos-group input[type="checkbox"] {
      width: 17px;
      height: 17px;
      accent-color: var(--green);
      cursor: pointer;
      flex-shrink: 0;
      margin-top: 2px;
    }
    .termos-group a {
      color: var(--green);
      font-weight: 600;
      transition: color 0.2s;
    }
    .termos-group a:hover { color: var(--green-dark); text-decoration: underline; }

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

    /* Link para login */
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

    /* Separador de seção no formulário */
    .form-section-label {
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--gray-500);
      margin: 20px 0 14px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .form-section-label::after {
      content: '';
      flex: 1;
      height: 1px;
      background: var(--gray-200);
    }

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
    html.dark .form-group input:focus,
    html.dark .form-group select:focus { border-color: #4ade80; }
    html.dark .form-group input::placeholder { color: #475569; }
    html.dark .form-section-label  { color: #64748b; }
    html.dark .form-section-label::after { background: #334155; }
    html.dark .termos-group        { color: #cbd5e1; }
    html.dark .login-divider       { color: #475569; }
    html.dark .login-divider::before,
    html.dark .login-divider::after { background: #334155; }
    html.dark .login-signup         { color: #94a3b8; }
    html.dark .strength-bar         { background: #334155; }

    /* Responsivo */
    @media (max-width: 768px) {
      .login-inner {
        grid-template-columns: 1fr;
        gap: 32px;
      }
      .login-brand {
        text-align: center;
        position: static;
      }
      .login-brand h1   { font-size: 1.8rem; }
      .login-features   { align-items: center; }
      .login-card       { padding: 28px 22px; }
      .form-row-2       { grid-template-columns: 1fr; }
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
        <li><a href="login.php">Entrar</a></li>
        <li><a href="cadastro.php" class="active" style="background:var(--green-light);color:var(--green-dark);font-weight:700;">Cadastrar</a></li>
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
      <a href="cadastro.php">✅ Criar conta</a>
    </div>
  </nav>

  <!-- =============================================
       SEÇÃO DE CADASTRO
  ============================================= -->
  <section class="login-section">
    <div class="login-bg-shapes">
      <div class="shape s1"></div>
      <div class="shape s2"></div>
      <div class="shape s3"></div>
    </div>

    <div class="login-inner">

      <!-- LADO ESQUERDO – benefícios -->
      <div class="login-brand animate-in">
        <div class="hero-badge">
          <span class="badge-dot"></span>
          🌱 Cadastro gratuito
        </div>
        <h1>Faça parte do<br><span class="text-green">ColetaFácil</span></h1>
        <p>
          Crie sua conta em menos de um minuto e comece a contribuir com
          uma Muriaé mais limpa e sustentável.
        </p>

        <div class="login-features">
          <div class="login-feature-item">
            <div class="lfi-icon">⭐</div>
            <span>Ganhe pontos por cada ação ambiental</span>
          </div>
          <div class="login-feature-item">
            <div class="lfi-icon">🏆</div>
            <span>Entre no ranking dos heróis da reciclagem</span>
          </div>
          <div class="login-feature-item">
            <div class="lfi-icon">🎁</div>
            <span>Troque pontos por recompensas reais</span>
          </div>
          <div class="login-feature-item">
            <div class="lfi-icon">🚨</div>
            <span>Reporte ocorrências e acompanhe o status</span>
          </div>
          <div class="login-feature-item">
            <div class="lfi-icon">🔔</div>
            <span>Receba avisos da coleta no seu bairro</span>
          </div>
        </div>
      </div>

      <!-- LADO DIREITO – formulário -->
      <div class="login-card animate-in delay-1">
        <div class="login-card-header">
          <span class="card-icon">🌿</span>
          <h2>Criar conta gratuita</h2>
          <p>Preencha os dados abaixo para começar</p>
        </div>

        <?php if (!empty($erro)): ?>
          <div class="login-error" role="alert">
            <span>⚠️</span>
            <?= htmlspecialchars($erro) ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="cadastro.php" id="cadastroForm" novalidate>

          <!-- ── Dados pessoais ──────────────────── -->
          <p class="form-section-label">Dados pessoais</p>

          <!-- Nome completo -->
          <div class="form-group">
            <label for="nome">Nome completo</label>
            <div class="input-icon-wrap">
              <span class="field-icon">👤</span>
              <input
                type="text"
                id="nome"
                name="nome"
                placeholder="Ex: Maria Silva"
                value="<?= htmlspecialchars($form['nome']) ?>"
                autocomplete="name"
                required
                autofocus
              >
            </div>
          </div>

          <!-- Telefone (opcional – coluna telefone da tabela usuario) -->
          <div class="form-group">
            <label for="telefone">
              Telefone / WhatsApp
              <span style="font-weight:400; color:var(--gray-500);">(opcional)</span>
            </label>
            <div class="input-icon-wrap">
              <span class="field-icon">📱</span>
              <input
                type="tel"
                id="telefone"
                name="telefone"
                placeholder="(32) 9 0000-0000"
                value="<?= htmlspecialchars($form['telefone']) ?>"
                autocomplete="tel"
                maxlength="20"
              >
            </div>
            <small>Usado apenas para contato relacionado às ocorrências reportadas.</small>
          </div>

          <!-- ── Acesso ──────────────────────────── -->
          <p class="form-section-label">Dados de acesso</p>

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
                value="<?= htmlspecialchars($form['email']) ?>"
                autocomplete="email"
                required
              >
            </div>
            <small>Será usado para entrar na sua conta.</small>
          </div>

          <!-- Senha e confirmação (linha dupla) -->
          <div class="form-row-2">
            <div class="form-group" style="margin-bottom:0;">
              <label for="senha">Senha</label>
              <div class="input-icon-wrap">
                <span class="field-icon">🔒</span>
                <input
                  type="password"
                  id="senha"
                  name="senha"
                  placeholder="Min. 8 caracteres"
                  autocomplete="new-password"
                  required
                  oninput="avaliarSenha(this.value)"
                >
                <button
                  type="button"
                  class="senha-toggle"
                  onclick="toggleSenha('senha','btnToggleSenha')"
                  aria-label="Mostrar ou ocultar senha"
                  id="btnToggleSenha"
                >👁️</button>
              </div>
            </div>

            <div class="form-group" style="margin-bottom:0;">
              <label for="senha_conf">Confirmar senha</label>
              <div class="input-icon-wrap">
                <span class="field-icon">🔒</span>
                <input
                  type="password"
                  id="senha_conf"
                  name="senha_conf"
                  placeholder="Repita a senha"
                  autocomplete="new-password"
                  required
                  oninput="verificarConfirmacao()"
                >
                <button
                  type="button"
                  class="senha-toggle"
                  onclick="toggleSenha('senha_conf','btnToggleSenhaConf')"
                  aria-label="Mostrar ou ocultar confirmação de senha"
                  id="btnToggleSenhaConf"
                >👁️</button>
              </div>
            </div>
          </div>

          <!-- Indicador de força da senha -->
          <div class="senha-strength" id="senhaStrength" style="margin-top:10px; margin-bottom:16px;">
            <div class="strength-bars">
              <div class="strength-bar" id="sb1"></div>
              <div class="strength-bar" id="sb2"></div>
              <div class="strength-bar" id="sb3"></div>
              <div class="strength-bar" id="sb4"></div>
            </div>
            <span class="strength-label" id="strengthLabel">—</span>
          </div>

          <!-- Aviso de confirmação de senha -->
          <div id="confMsg" style="font-size:0.78rem; margin-bottom:16px; display:none;"></div>

          <!-- ── Termos ───────────────────────────── -->
          <label class="termos-group">
            <input type="checkbox" name="termos" id="termos" required>
            <span>
              Li e concordo com os
              <a href="#" onclick="return false;" title="Termos de uso em breve">Termos de Uso</a>
              e a
              <a href="#" onclick="return false;" title="Política de privacidade em breve">Política de Privacidade</a>
              do ColetaFácil.
            </span>
          </label>

          <!-- Botão de submit -->
          <button type="submit" class="btn-login" id="btnCadastro">
            <span id="btnCadastroText">✅ Criar minha conta</span>
            <span id="btnCadastroLoad" style="display:none;">⏳ Criando conta...</span>
          </button>

        </form>

        <div class="login-divider">ou</div>

        <div class="login-signup">
          Já tem conta?
          <a href="login.php">Entrar agora</a>
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
    function toggleSenha(inputId, btnId) {
      const input = document.getElementById(inputId);
      const btn   = document.getElementById(btnId);
      if (!input || !btn) return;
      const escondida = input.type === 'password';
      input.type       = escondida ? 'text' : 'password';
      btn.textContent  = escondida ? '🙈' : '👁️';
      btn.setAttribute('aria-label', escondida ? 'Ocultar senha' : 'Mostrar senha');
    }

    // =============================================
    // INDICADOR DE FORÇA DA SENHA
    // =============================================
    function avaliarSenha(valor) {
      const bars  = [
        document.getElementById('sb1'),
        document.getElementById('sb2'),
        document.getElementById('sb3'),
        document.getElementById('sb4'),
      ];
      const label = document.getElementById('strengthLabel');
      if (!bars[0] || !label) return;

      let pontos = 0;
      if (valor.length >= 8)                          pontos++;
      if (/[A-Z]/.test(valor))                        pontos++;
      if (/[0-9]/.test(valor))                        pontos++;
      if (/[^A-Za-z0-9]/.test(valor))                pontos++;

      const niveis = ['', 'fraca', 'media', 'forte', 'forte'];
      const textos = ['—', 'Fraca', 'Média', 'Forte', 'Muito forte'];

      bars.forEach((b, i) => {
        b.className = 'strength-bar';
        if (i < pontos) b.classList.add(niveis[pontos]);
      });

      label.textContent = valor.length === 0 ? '—' : textos[pontos];
      label.className   = 'strength-label ' + (valor.length === 0 ? '' : niveis[pontos]);

      // Re-checa confirmação ao digitar
      verificarConfirmacao();
    }

    // =============================================
    // VERIFICAÇÃO DE CONFIRMAÇÃO DE SENHA
    // =============================================
    function verificarConfirmacao() {
      const senha     = document.getElementById('senha')?.value      ?? '';
      const conf      = document.getElementById('senha_conf')?.value ?? '';
      const msgEl     = document.getElementById('confMsg');
      if (!msgEl || conf === '') { if (msgEl) msgEl.style.display = 'none'; return; }

      msgEl.style.display = 'block';
      if (senha === conf) {
        msgEl.textContent  = '✅ As senhas coincidem.';
        msgEl.style.color  = 'var(--green)';
      } else {
        msgEl.textContent  = '❌ As senhas não coincidem.';
        msgEl.style.color  = 'var(--red)';
      }
    }

    // =============================================
    // MÁSCARA DE TELEFONE
    // =============================================
    document.getElementById('telefone')?.addEventListener('input', function () {
      let v = this.value.replace(/\D/g, '').slice(0, 11);
      if (v.length > 10) {
        v = v.replace(/^(\d{2})(\d{1})(\d{4})(\d{4})$/, '($1) $2 $3-$4');
      } else if (v.length > 6) {
        v = v.replace(/^(\d{2})(\d{4})(\d{0,4})$/, '($1) $2-$3');
      } else if (v.length > 2) {
        v = v.replace(/^(\d{2})(\d+)$/, '($1) $2');
      }
      this.value = v;
    });

    // =============================================
    // FEEDBACK VISUAL NO SUBMIT
    // =============================================
    document.getElementById('cadastroForm')?.addEventListener('submit', function (e) {
      const nome      = document.getElementById('nome').value.trim();
      const email     = document.getElementById('email').value.trim();
      const senha     = document.getElementById('senha').value;
      const conf      = document.getElementById('senha_conf').value;
      const termos    = document.getElementById('termos').checked;

      // Validação básica no frontend
      if (!nome || !email || !senha || !conf || !termos) {
        e.preventDefault();
        return;
      }

      if (senha !== conf) {
        e.preventDefault();
        const msgEl = document.getElementById('confMsg');
        if (msgEl) {
          msgEl.textContent  = '❌ As senhas não coincidem.';
          msgEl.style.color  = 'var(--red)';
          msgEl.style.display = 'block';
        }
        document.getElementById('senha_conf')?.focus();
        return;
      }

      const btn     = document.getElementById('btnCadastro');
      const txtNorm = document.getElementById('btnCadastroText');
      const txtLoad = document.getElementById('btnCadastroLoad');
      if (btn && txtNorm && txtLoad) {
        btn.disabled           = true;
        txtNorm.style.display  = 'none';
        txtLoad.style.display  = 'inline';
      }
    });
  </script>
</body>
</html>
