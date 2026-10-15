<?php
$erro        = $erro        ?? '';
$email_input = $email_input ?? '';
$next        = $next        ?? baseUrl();
require_once 'app/view/comuns/cabecalho.php';
?>

  <!-- SEÇÃO DE LOGIN -->
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

        <form method="POST" action="<?= baseUrl('usuario/login') ?>" id="loginForm" novalidate>
          <input type="hidden" name="next" value="<?= htmlspecialchars($next, ENT_QUOTES, 'UTF-8') ?>">

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

          <div class="login-row">
            <label>
              <input type="checkbox" name="lembrar" id="lembrar">
              Lembrar de mim
            </label>
            <a href="#" onclick="return false;" title="Recuperação de senha em breve">Esqueci minha senha</a>
          </div>

          <button type="submit" class="btn-login" id="btnLogin">
            <span id="btnLoginText">🔑 Entrar</span>
            <span id="btnLoginLoad" style="display:none;">⏳ Verificando...</span>
          </button>

        </form>

        <div class="login-divider">ou</div>

        <div class="login-signup">
          Não tem conta?
          <a href="<?= baseUrl('usuario/cadastro') ?>">Cadastre-se grátis</a>
        </div>
      </div>

    </div>
  </section>

  <script>
    function toggleSenha() {
      const input = document.getElementById('senha');
      const btn   = document.getElementById('senhaToggleBtn');
      if (!input) return;
      const escondida = input.type === 'password';
      input.type = escondida ? 'text' : 'password';
      btn.textContent = escondida ? '🙈' : '👁️';
      btn.setAttribute('aria-label', escondida ? 'Ocultar senha' : 'Mostrar senha');
    }

    document.getElementById('loginForm')?.addEventListener('submit', function (e) {
      const email = document.getElementById('email').value.trim();
      const senha = document.getElementById('senha').value;
      if (!email || !senha) { e.preventDefault(); return; }
      const btn     = document.getElementById('btnLogin');
      const txtNorm = document.getElementById('btnLoginText');
      const txtLoad = document.getElementById('btnLoginLoad');
      if (btn && txtNorm && txtLoad) {
        btn.disabled          = true;
        txtNorm.style.display = 'none';
        txtLoad.style.display = 'inline';
      }
    });
  </script>

<?php require_once 'app/view/comuns/rodape.php'; ?>
