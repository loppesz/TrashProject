<?php
$erro = $erro ?? '';
$form = $form ?? ['nome' => '', 'email' => '', 'telefone' => ''];
require_once 'app/view/comuns/cabecalho.php';
?>

  <!-- SEÇÃO DE CADASTRO -->
  <section class="login-section">
    <div class="login-bg-shapes">
      <div class="shape s1"></div>
      <div class="shape s2"></div>
      <div class="shape s3"></div>
    </div>

    <div class="login-inner cadastro">

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
          <div class="login-feature-item"><div class="lfi-icon">⭐</div><span>Ganhe pontos por cada ação ambiental</span></div>
          <div class="login-feature-item"><div class="lfi-icon">🏆</div><span>Entre no ranking dos heróis da reciclagem</span></div>
          <div class="login-feature-item"><div class="lfi-icon">🎁</div><span>Troque pontos por recompensas reais</span></div>
          <div class="login-feature-item"><div class="lfi-icon">🚨</div><span>Reporte ocorrências e acompanhe o status</span></div>
          <div class="login-feature-item"><div class="lfi-icon">🔒</div><span>Baixe ou exclua seus dados quando quiser</span></div>
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

        <form method="POST" action="<?= baseUrl('usuario/cadastro') ?>" id="cadastroForm" novalidate>
          <?= csrf_campo() ?>
          <?= honeypot_campo() ?>

          <p class="form-section-label">Dados pessoais</p>

          <div class="form-group">
            <label for="nome">Nome completo</label>
            <div class="input-icon-wrap">
              <span class="field-icon">👤</span>
              <input
                type="text" id="nome" name="nome"
                placeholder="Ex: Maria Silva"
                value="<?= htmlspecialchars($form['nome']) ?>"
                autocomplete="name" maxlength="120" required autofocus
              >
            </div>
          </div>

          <div class="form-group">
            <label for="telefone">
              Telefone / WhatsApp
              <span style="font-weight:400; color:var(--gray-500);">(opcional)</span>
            </label>
            <div class="input-icon-wrap">
              <span class="field-icon">📱</span>
              <input
                type="tel" id="telefone" name="telefone"
                placeholder="(32) 9 0000-0000"
                value="<?= htmlspecialchars($form['telefone']) ?>"
                autocomplete="tel" maxlength="20"
              >
            </div>
            <small>Usado apenas para contato relacionado às ocorrências reportadas.</small>
          </div>

          <p class="form-section-label">Dados de acesso</p>

          <div class="form-group">
            <label for="email">E-mail</label>
            <div class="input-icon-wrap">
              <span class="field-icon">📧</span>
              <input
                type="email" id="email" name="email"
                placeholder="seu@email.com"
                value="<?= htmlspecialchars($form['email']) ?>"
                autocomplete="email" maxlength="150" required
              >
            </div>
            <small>Usado apenas para entrar na sua conta. Não enviamos propaganda nem compartilhamos o seu e-mail.</small>
          </div>

          <div class="form-row-2">
            <div class="form-group" style="margin-bottom:0;">
              <label for="senha">Senha</label>
              <div class="input-icon-wrap">
                <span class="field-icon">🔒</span>
                <input
                  type="password" id="senha" name="senha"
                  placeholder="8+ caracteres, letras e números"
                  autocomplete="new-password" minlength="8" maxlength="72" required
                  oninput="avaliarSenha(this.value)"
                >
                <button type="button" class="senha-toggle"
                  onclick="toggleSenha('senha','btnToggleSenha')"
                  aria-label="Mostrar ou ocultar senha"
                  id="btnToggleSenha">👁️</button>
              </div>
            </div>

            <div class="form-group" style="margin-bottom:0;">
              <label for="senha_conf">Confirmar senha</label>
              <div class="input-icon-wrap">
                <span class="field-icon">🔒</span>
                <input
                  type="password" id="senha_conf" name="senha_conf"
                  placeholder="Repita a senha"
                  autocomplete="new-password" required
                  oninput="verificarConfirmacao()"
                >
                <button type="button" class="senha-toggle"
                  onclick="toggleSenha('senha_conf','btnToggleSenhaConf')"
                  aria-label="Mostrar ou ocultar confirmação de senha"
                  id="btnToggleSenhaConf">👁️</button>
              </div>
            </div>
          </div>

          <div class="senha-strength" id="senhaStrength" style="margin-top:10px; margin-bottom:16px;">
            <div class="strength-bars">
              <div class="strength-bar" id="sb1"></div>
              <div class="strength-bar" id="sb2"></div>
              <div class="strength-bar" id="sb3"></div>
              <div class="strength-bar" id="sb4"></div>
            </div>
            <span class="strength-label" id="strengthLabel">—</span>
          </div>

          <div id="confMsg" style="font-size:0.78rem; margin-bottom:16px; display:none;"></div>

          <label class="termos-group">
            <input type="checkbox" name="termos" id="termos" required>
            <span>
              Li e concordo com os
              <a href="<?= baseUrl('home/termos') ?>" target="_blank" rel="noopener">Termos de Uso</a>
              e a
              <a href="<?= baseUrl('home/privacidade') ?>" target="_blank" rel="noopener">Política de Privacidade</a>
              do ColetaFácil (versão <?= htmlspecialchars(TERMOS_VERSAO) ?>).
            </span>
          </label>

          <button type="submit" class="btn-login" id="btnCadastro">
            <span id="btnCadastroText">✅ Criar minha conta</span>
            <span id="btnCadastroLoad" style="display:none;">⏳ Criando conta...</span>
          </button>

        </form>

        <div class="login-divider">ou</div>

        <div class="login-signup">
          Já tem conta?
          <a href="<?= baseUrl('usuario/login') ?>">Entrar agora</a>
        </div>
      </div>

    </div>
  </section>

  <script>
    function toggleSenha(inputId, btnId) {
      const input = document.getElementById(inputId);
      const btn   = document.getElementById(btnId);
      if (!input || !btn) return;
      const escondida = input.type === 'password';
      input.type      = escondida ? 'text' : 'password';
      btn.textContent = escondida ? '🙈' : '👁️';
      btn.setAttribute('aria-label', escondida ? 'Ocultar senha' : 'Mostrar senha');
    }

    function avaliarSenha(valor) {
      const bars  = ['sb1','sb2','sb3','sb4'].map(id => document.getElementById(id));
      const label = document.getElementById('strengthLabel');
      if (!bars[0] || !label) return;
      let pontos = 0;
      if (valor.length >= 8)           pontos++;
      if (/[A-Z]/.test(valor))         pontos++;
      if (/[0-9]/.test(valor))         pontos++;
      if (/[^A-Za-z0-9]/.test(valor))  pontos++;
      const niveis = ['', 'fraca', 'media', 'forte', 'forte'];
      const textos = ['—', 'Fraca', 'Média', 'Forte', 'Muito forte'];
      bars.forEach((b, i) => {
        b.className = 'strength-bar';
        if (i < pontos) b.classList.add(niveis[pontos]);
      });
      label.textContent = valor.length === 0 ? '—' : textos[pontos];
      label.className   = 'strength-label ' + (valor.length === 0 ? '' : niveis[pontos]);
      verificarConfirmacao();
    }

    function verificarConfirmacao() {
      const senha = document.getElementById('senha')?.value      ?? '';
      const conf  = document.getElementById('senha_conf')?.value ?? '';
      const msgEl = document.getElementById('confMsg');
      if (!msgEl || conf === '') { if (msgEl) msgEl.style.display = 'none'; return; }
      msgEl.style.display = 'block';
      if (senha === conf) {
        msgEl.textContent = '✅ As senhas coincidem.';
        msgEl.style.color = 'var(--green)';
      } else {
        msgEl.textContent = '❌ As senhas não coincidem.';
        msgEl.style.color = 'var(--red)';
      }
    }

    document.getElementById('telefone')?.addEventListener('input', function () {
      let v = this.value.replace(/\D/g, '').slice(0, 11);
      if (v.length > 10)      v = v.replace(/^(\d{2})(\d{1})(\d{4})(\d{4})$/, '($1) $2 $3-$4');
      else if (v.length > 6)  v = v.replace(/^(\d{2})(\d{4})(\d{0,4})$/, '($1) $2-$3');
      else if (v.length > 2)  v = v.replace(/^(\d{2})(\d+)$/, '($1) $2');
      this.value = v;
    });

    document.getElementById('cadastroForm')?.addEventListener('submit', function (e) {
      const nome   = document.getElementById('nome').value.trim();
      const email  = document.getElementById('email').value.trim();
      const senha  = document.getElementById('senha').value;
      const conf   = document.getElementById('senha_conf').value;
      const termos = document.getElementById('termos').checked;
      if (!nome || !email || !senha || !conf || !termos) { e.preventDefault(); return; }
      if (senha !== conf) {
        e.preventDefault();
        const msgEl = document.getElementById('confMsg');
        if (msgEl) { msgEl.textContent = '❌ As senhas não coincidem.'; msgEl.style.color = 'var(--red)'; msgEl.style.display = 'block'; }
        document.getElementById('senha_conf')?.focus();
        return;
      }
      const btn     = document.getElementById('btnCadastro');
      const txtNorm = document.getElementById('btnCadastroText');
      const txtLoad = document.getElementById('btnCadastroLoad');
      if (btn && txtNorm && txtLoad) { btn.disabled = true; txtNorm.style.display = 'none'; txtLoad.style.display = 'inline'; }
    });
  </script>

<?php require_once 'app/view/comuns/rodape.php'; ?>
