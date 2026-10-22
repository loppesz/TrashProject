<?php
$contato = $contato ?? LGPD_CONTATO;
require_once 'app/view/comuns/cabecalho.php';
?>

  <section class="legal-page">
    <div class="container legal-wrap legal-texto">

      <header class="legal-header">
        <span class="legal-tag">Regras de uso</span>
        <h1>Termos de Uso</h1>
        <p>Versão <?= htmlspecialchars(TERMOS_VERSAO) ?> · atualizada em <?= htmlspecialchars(TERMOS_DATA) ?></p>
      </header>

      <section class="legal-secao">
        <h2>1. O que é o ColetaFácil</h2>
        <p>O ColetaFácil é um <strong>protótipo acadêmico</strong> que reúne informações de coleta de resíduos de Muriaé-MG,
          orientações de descarte, pontos de entrega, registro de ocorrências, pontos de participação e uma área educativa infantil.
          Ele <strong>não substitui</strong> os canais oficiais da Prefeitura de Muriaé nem do DEMSUR.</p>
      </section>

      <section class="legal-secao">
        <h2>2. Informações de coleta</h2>
        <p>Os dias e horários são baseados no cronograma publicado pelo DEMSUR e podem mudar sem aviso. Horários da coleta comum são
          aproximados. Em caso de dúvida, confirme com o DEMSUR pelo 0800 283 1453.</p>
      </section>

      <section class="legal-secao">
        <h2>3. Sua conta</h2>
        <ul class="legal-lista">
          <li>Use dados verdadeiros e mantenha sua senha em segredo; você responde pelas ações feitas com a sua conta.</li>
          <li>Uma conta por pessoa. Contas criadas de forma automatizada ou para fraudar pontos podem ser desativadas.</li>
          <li>Você pode corrigir, baixar ou excluir seus dados em <a href="<?= baseUrl('usuario/conta') ?>">Minha conta</a>.</li>
        </ul>
      </section>

      <section class="legal-secao">
        <h2>4. Ocorrências</h2>
        <ul class="legal-lista">
          <li>Relate apenas situações reais, em Muriaé-MG, com informações que você acredita serem verdadeiras.</li>
          <li><strong>Não inclua dados pessoais de outras pessoas</strong> (nomes, placas de veículos, rostos em fotos, telefones) além do necessário para descrever o problema.</li>
          <li>É proibido conteúdo ofensivo, discriminatório, ameaças ou acusações sem fundamento.</li>
          <li>A equipe pode arquivar ocorrências repetidas, falsas, fora de Muriaé ou que violem estes termos. Ocorrências arquivadas não geram o bônus de pontos.</li>
          <li>Em situação de risco imediato, não espere pelo sistema: ligue 193 (Bombeiros), 192 (SAMU) ou 156 (Prefeitura).</li>
        </ul>
      </section>

      <section class="legal-secao">
        <h2>5. Pontos e recompensas</h2>
        <ul class="legal-lista">
          <li>Pontos não têm valor em dinheiro, não podem ser vendidos nem transferidos.</li>
          <li>A maior parte dos pontos só é creditada após a confirmação da ocorrência pela equipe. Há limite diário de pontos por registro.</li>
          <li>Recompensas dependem de disponibilidade e estoque; itens marcados como “em breve” ainda não podem ser resgatados.</li>
          <li>Pontos obtidos por fraude (ocorrências falsas, várias contas, automação) podem ser cancelados.</li>
          <li>As regras de pontuação podem mudar; os pontos já creditados são mantidos.</li>
        </ul>
      </section>

      <section class="legal-secao">
        <h2>6. Área Kids</h2>
        <p>A Área Kids é educativa e deve ser usada com acompanhamento de um adulto responsável. O progresso só é salvo no servidor
          quando o responsável está logado, e apenas com um apelido. Veja a <a href="<?= baseUrl('home/privacidade') ?>#criancas">Política de Privacidade</a>.</p>
      </section>

      <section class="legal-secao">
        <h2>7. Uso adequado do sistema</h2>
        <p>Não tente acessar áreas restritas, contornar limites de segurança, enviar arquivos maliciosos ou sobrecarregar o serviço.
          Se encontrar uma falha de segurança, avise a equipe antes de divulgar.</p>
      </section>

      <section class="legal-secao">
        <h2>8. Responsabilidades</h2>
        <p>Por ser um protótipo, o serviço pode ficar indisponível ou conter erros. A equipe se esforça para manter as informações corretas,
          mas não garante que horários, pontos de descarte e recompensas estejam sempre atualizados.</p>
      </section>

      <section class="legal-secao">
        <h2>9. Alterações e lei aplicável</h2>
        <p>Estes termos podem ser atualizados; a versão e a data acima indicam o texto vigente. Aplicam-se as leis brasileiras,
          incluindo o Marco Civil da Internet (Lei 12.965/2014) e a LGPD (Lei 13.709/2018).</p>
        <p>Contato: <strong><?= htmlspecialchars($contato['responsavel']) ?></strong>
          <?php if (!empty($contato['email'])): ?>
            — <a href="mailto:<?= htmlspecialchars($contato['email'], ENT_QUOTES) ?>"><?= htmlspecialchars($contato['email']) ?></a>
          <?php else: ?>
            — contatos da equipe no rodapé.
          <?php endif; ?>
        </p>
      </section>

    </div>
  </section>

<?php require_once 'app/view/comuns/rodape.php'; ?>
