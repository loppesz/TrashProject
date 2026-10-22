<?php
$perfil     = $perfil ?? [];
$resumo     = $resumo ?? [];
$perfisKids = $perfisKids ?? [];
$ehAdmin    = ($perfil['tipo'] ?? '') === 'ADMINISTRADOR';
$fmtData    = static fn(?string $d): string => $d ? date('d/m/Y H:i', strtotime($d)) : '—';
require_once 'app/view/comuns/cabecalho.php';
?>

  <section class="legal-page">
    <div class="container legal-wrap">

      <header class="legal-header">
        <span class="legal-tag">LGPD · Seus direitos</span>
        <h1>Minha conta e privacidade</h1>
        <p>Veja, corrija, baixe ou exclua os seus dados pessoais. Tudo aqui vale imediatamente.</p>
      </header>

      <?php if ($msg !== ''): ?>
        <div class="legal-alerta ok" role="status"><?= htmlspecialchars($msg) ?></div>
      <?php elseif ($erro !== ''): ?>
        <div class="legal-alerta erro" role="alert"><?= htmlspecialchars($erro) ?></div>
      <?php endif; ?>

      <div class="conta-grid">

        <!-- DADOS PESSOAIS (correção — art. 18, III) -->
        <article class="legal-card">
          <h2>Seus dados</h2>
          <form method="POST" action="<?= baseUrl('usuario/salvarConta') ?>" class="conta-form">
            <?= csrf_campo() ?>
            <label for="contaNome">Nome</label>
            <input type="text" id="contaNome" name="nome" value="<?= htmlspecialchars($perfil['nome'] ?? '') ?>" maxlength="120" required autocomplete="name">

            <label for="contaTelefone">Telefone <small>(opcional)</small></label>
            <input type="tel" id="contaTelefone" name="telefone" value="<?= htmlspecialchars($perfil['telefone'] ?? '') ?>" maxlength="20" autocomplete="tel">

            <label for="contaEmail">E-mail <small>(usado para entrar)</small></label>
            <input type="email" id="contaEmail" value="<?= htmlspecialchars($perfil['email'] ?? '') ?>" disabled>

            <button type="submit" class="legal-btn">Salvar alterações</button>
          </form>
          <dl class="conta-info">
            <div><dt>Conta criada em</dt><dd><?= $fmtData($perfil['criado_em'] ?? null) ?></dd></div>
            <div><dt>Termos aceitos</dt><dd>
              <?= !empty($perfil['termos_aceitos_em'])
                  ? 'Versão ' . htmlspecialchars((string) $perfil['termos_versao']) . ' em ' . $fmtData($perfil['termos_aceitos_em'])
                  : 'Conta anterior ao registro de aceite' ?>
            </dd></div>
          </dl>
        </article>

        <!-- O QUE GUARDAMOS (acesso — art. 18, II) -->
        <article class="legal-card">
          <h2>O que está ligado à sua conta</h2>
          <ul class="conta-resumo">
            <li><strong><?= (int) ($resumo['ocorrencias'] ?? 0) ?></strong> ocorrências registradas</li>
            <li><strong><?= (int) ($resumo['pontos'] ?? 0) ?></strong> pontos no saldo</li>
            <li><strong><?= (int) ($resumo['resgates'] ?? 0) ?></strong> resgates de recompensas</li>
            <li><strong><?= (int) ($resumo['perfis_kids'] ?? 0) ?></strong> perfis na Área Kids</li>
            <li><strong><?= (int) ($resumo['sugestoes'] ?? 0) ?></strong> sugestões de pontos de descarte</li>
          </ul>
          <p class="legal-nota">Guardamos sua senha apenas como um código irreversível (hash). Ninguém da equipe consegue vê-la.</p>

          <!-- Portabilidade — art. 18, V -->
          <form method="POST" action="<?= baseUrl('usuario/exportarDados') ?>">
            <?= csrf_campo() ?>
            <button type="submit" class="legal-btn secundario">Baixar meus dados (JSON)</button>
          </form>
        </article>

        <!-- SENHA -->
        <article class="legal-card">
          <h2>Trocar senha</h2>
          <form method="POST" action="<?= baseUrl('usuario/alterarSenha') ?>" class="conta-form">
            <?= csrf_campo() ?>
            <label for="senhaAtual">Senha atual</label>
            <input type="password" id="senhaAtual" name="senha_atual" required autocomplete="current-password">
            <label for="senhaNova">Nova senha <small>(8+ caracteres, letras e números)</small></label>
            <input type="password" id="senhaNova" name="senha_nova" minlength="8" maxlength="72" required autocomplete="new-password">
            <label for="senhaConf">Confirmar nova senha</label>
            <input type="password" id="senhaConf" name="senha_conf" minlength="8" maxlength="72" required autocomplete="new-password">
            <button type="submit" class="legal-btn">Trocar senha</button>
          </form>
        </article>

        <!-- ÁREA KIDS -->
        <article class="legal-card">
          <h2>Perfis da Área Kids</h2>
          <?php if (empty($perfisKids)): ?>
            <p class="legal-nota">Nenhum perfil infantil salvo. Visitantes da Área Kids guardam o progresso só no próprio navegador.</p>
          <?php else: ?>
            <p class="legal-nota">Guardamos apenas o apelido, os pontos e a quantidade de jogos de cada criança.</p>
            <ul class="conta-kids">
              <?php foreach ($perfisKids as $pk): ?>
                <li>
                  <span><strong><?= htmlspecialchars($pk['apelido']) ?></strong> · <?= (int) $pk['pontos'] ?> pts</span>
                  <form method="POST" action="<?= baseUrl('usuario/excluirPerfilKids') ?>"
                        onsubmit="return confirm(<?= jsAttr('Excluir o perfil "' . $pk['apelido'] . '"? O progresso será apagado.') ?>);">
                    <?= csrf_campo() ?>
                    <input type="hidden" name="id_perfil" value="<?= (int) $pk['id_perfil_kids'] ?>">
                    <button type="submit" class="legal-link-btn">Excluir</button>
                  </form>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </article>

        <!-- EXCLUSÃO — art. 18, VI -->
        <article class="legal-card perigo">
          <h2>Excluir minha conta</h2>
          <?php if ($ehAdmin): ?>
            <p class="legal-nota">Contas de administrador não podem ser excluídas por aqui. Peça a remoção à equipe responsável pelo sistema.</p>
          <?php else: ?>
            <p>Apagamos sua conta, seus pontos, resgates e perfis da Área Kids. As ocorrências que você registrou
               <strong>continuam no sistema de forma anônima</strong> (sem vínculo com você e sem nome, telefone ou e-mail),
               porque ajudam a acompanhar os problemas da cidade.</p>
            <p class="legal-nota">Esta ação não pode ser desfeita. Baixe seus dados antes, se quiser guardar uma cópia.</p>
            <form method="POST" action="<?= baseUrl('usuario/excluirConta') ?>" class="conta-form"
                  onsubmit="return confirm('Excluir definitivamente a sua conta?');">
              <?= csrf_campo() ?>
              <label for="excluirSenha">Confirme sua senha</label>
              <input type="password" id="excluirSenha" name="senha" required autocomplete="current-password">
              <label for="excluirConfirmacao">Digite <strong>EXCLUIR</strong> para confirmar</label>
              <input type="text" id="excluirConfirmacao" name="confirmacao" required autocomplete="off" pattern="[Ee][Xx][Cc][Ll][Uu][Ii][Rr]">
              <button type="submit" class="legal-btn perigo">Excluir minha conta</button>
            </form>
          <?php endif; ?>
        </article>

        <!-- OUTROS DIREITOS -->
        <article class="legal-card">
          <h2>Outros direitos</h2>
          <ul class="legal-lista">
            <li><strong>Ocorrências anônimas:</strong> no formulário, marque “Registrar anonimamente” e nada fica ligado à sua conta.</li>
            <li><strong>Histórico público:</strong> você pode escolher não exibir a ocorrência no histórico público.</li>
            <li><strong>Dúvidas, revogação ou reclamação:</strong> veja os canais na <a href="<?= baseUrl('home/privacidade') ?>#contato">Política de Privacidade</a>.
              Você também pode procurar a ANPD (Autoridade Nacional de Proteção de Dados).</li>
          </ul>
        </article>

      </div>
    </div>
  </section>

<?php require_once 'app/view/comuns/rodape.php'; ?>
