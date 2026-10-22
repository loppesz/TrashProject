<?php
$retencao = $retencao ?? RETENCAO_DIAS;
$contato  = $contato ?? LGPD_CONTATO;
require_once 'app/view/comuns/cabecalho.php';
?>

  <section class="legal-page">
    <div class="container legal-wrap legal-texto">

      <?php if (($_GET['conta'] ?? '') === 'excluida'): ?>
        <div class="legal-alerta ok" role="status">
          Sua conta foi excluída. Seus pontos, resgates e perfis da Área Kids foram apagados e as ocorrências que você registrou ficaram anônimas.
        </div>
      <?php endif; ?>

      <header class="legal-header">
        <span class="legal-tag">LGPD · Lei nº 13.709/2018</span>
        <h1>Política de Privacidade</h1>
        <p>Versão <?= htmlspecialchars(TERMOS_VERSAO) ?> · atualizada em <?= htmlspecialchars(TERMOS_DATA) ?></p>
      </header>

      <nav class="legal-indice" aria-label="Nesta página">
        <a href="#quem">Quem somos</a><a href="#dados">Dados tratados</a><a href="#publico">O que é público</a>
        <a href="#terceiros">Terceiros</a><a href="#retencao">Retenção</a><a href="#direitos">Seus direitos</a>
        <a href="#criancas">Crianças</a><a href="#seguranca">Segurança</a><a href="#contato">Contato</a>
      </nav>

      <section id="quem" class="legal-secao">
        <h2>1. Quem é responsável pelos dados</h2>
        <p>O ColetaFácil é um <strong>projeto acadêmico</strong> do curso de Análise e Desenvolvimento de Sistemas
          (Extensão Universitária III). O controlador dos dados é a <strong><?= htmlspecialchars($contato['responsavel']) ?></strong>.
          O ColetaFácil <strong>não é um serviço oficial</strong> da Prefeitura de Muriaé nem do DEMSUR, e nenhum dado é enviado
          automaticamente a esses órgãos.</p>
        <p>Resumo em uma frase: coletamos o mínimo necessário para você consultar a coleta, registrar problemas e participar
          das recompensas; não vendemos dados, não usamos propaganda e não usamos ferramentas de rastreamento.</p>
      </section>

      <section id="dados" class="legal-secao">
        <h2>2. Quais dados tratamos, para quê e com qual base legal</h2>
        <div class="legal-tabela-wrap">
          <table class="legal-tabela">
            <thead><tr><th>Dados</th><th>Para quê</th><th>Base legal (LGPD art. 7º)</th></tr></thead>
            <tbody>
              <tr>
                <td><strong>Conta:</strong> nome, e-mail, telefone (opcional), senha guardada como hash irreversível, data e versão do aceite destes termos.</td>
                <td>Identificar você, permitir o login, ligar ocorrências e pontos à sua conta.</td>
                <td>Execução do serviço pedido por você (inciso V).</td>
              </tr>
              <tr>
                <td><strong>Ocorrências:</strong> tipo, urgência, endereço, bairro, descrição, data, foto (opcional, sem metadados) e localização GPS (só se você usar “Usar localização atual”).</td>
                <td>Registrar, encaminhar e acompanhar o problema ambiental relatado.</td>
                <td>Consentimento (inciso I), dado ao enviar o formulário.</td>
              </tr>
              <tr>
                <td><strong>Contato na ocorrência</strong> (opcional): nome, telefone, e-mail.</td>
                <td>Permitir que a equipe dê retorno sobre a ocorrência. Só a equipe administrativa vê.</td>
                <td>Consentimento (inciso I). Use o modo anônimo para não informar.</td>
              </tr>
              <tr>
                <td><strong>Pontos e recompensas:</strong> lançamentos de pontos, resgates e códigos.</td>
                <td>Calcular saldo, nível, ranking e entregar recompensas.</td>
                <td>Execução do serviço (inciso V).</td>
              </tr>
              <tr>
                <td><strong>Área Kids</strong> (com conta do responsável): apelido, pontos e número de jogos de cada criança.</td>
                <td>Guardar o progresso dos jogos educativos.</td>
                <td>Consentimento do responsável (art. 14, §1º).</td>
              </tr>
              <tr>
                <td><strong>Sugestões de pontos de descarte:</strong> nome do local, endereço, materiais, horário e observação.</td>
                <td>Avaliar e cadastrar novos pontos de descarte.</td>
                <td>Consentimento (inciso I).</td>
              </tr>
              <tr>
                <td><strong>Segurança:</strong> código (hash) gerado a partir do endereço IP; registro de ações de privacidade (exportação, exclusão), sem o conteúdo dos dados.</td>
                <td>Bloquear tentativas abusivas de login e envios automáticos; comprovar o atendimento aos seus pedidos.</td>
                <td>Legítimo interesse (inciso IX) e cumprimento da LGPD (inciso II).</td>
              </tr>
            </tbody>
          </table>
        </div>
        <p><strong>No seu navegador</strong> guardamos apenas: um cookie de sessão essencial (<code>CFSESSID</code>, apagado ao fechar o
          navegador) e, no armazenamento local, o tema claro/escuro, o aviso de privacidade já visto e o progresso da Área Kids de visitantes.
          Não usamos cookies de publicidade nem de estatística.</p>
      </section>

      <section id="publico" class="legal-secao">
        <h2>3. O que aparece publicamente</h2>
        <ul class="legal-lista">
          <li><strong>Histórico público</strong> (se você não desmarcar a opção): tipo da ocorrência, bairro, nome da rua <em>sem número</em>, status e data.
            <strong>Nunca</strong> mostramos descrição, foto, contato, nome de quem registrou nem a localização exata.</li>
          <li><strong>Mapa de ocorrências:</strong> posição aproximada (arredondada para cerca de 100 metros).</li>
          <li><strong>Consulta por protocolo:</strong> quem tem o código vê o andamento e a resposta da equipe. A descrição completa aparece só para o autor logado.</li>
          <li><strong>Ranking de pontos:</strong> mostra apenas o primeiro nome e a inicial do sobrenome (ex.: “Maria S.”) de quem acumulou pontos. Se não quiser aparecer, registre ocorrências no modo anônimo ou exclua a conta.</li>
          <li><strong>Fotos</strong> ficam guardadas no banco de dados e só a equipe administrativa logada consegue abri-las.</li>
        </ul>
      </section>

      <section id="terceiros" class="legal-secao">
        <h2>4. Com quem os dados são compartilhados</h2>
        <p>Não vendemos nem cedemos dados para marketing. Alguns recursos carregam conteúdo de serviços externos, e o seu navegador
          se comunica diretamente com eles (eles recebem, no mínimo, o seu endereço IP):</p>
        <ul class="legal-lista">
          <li><strong>OpenStreetMap</strong> — imagens dos mapas.</li>
          <li><strong>Nominatim (OpenStreetMap)</strong> — somente quando você clica em “Usar localização atual”: as coordenadas são enviadas para descobrir o nome da rua.</li>
          <li><strong>Google Maps</strong> — mapas incorporados nos detalhes de pontos de descarte e da coleta (recebe o endereço público do ponto ou o bairro).</li>
          <li><strong>Google Fonts</strong> e <strong>unpkg</strong> — fontes do site e biblioteca de mapas.</li>
        </ul>
        <p>Esses serviços têm políticas próprias e podem estar fora do Brasil (transferência internacional, LGPD art. 33).
          Os dados também podem ser fornecidos a autoridades quando houver obrigação legal ou ordem judicial.</p>
      </section>

      <section id="retencao" class="legal-secao">
        <h2>5. Por quanto tempo guardamos</h2>
        <ul class="legal-lista">
          <li><strong>Conta:</strong> enquanto existir. Você pode excluí-la a qualquer momento em <a href="<?= baseUrl('usuario/conta') ?>">Minha conta</a>.</li>
          <li><strong>Contato informado em ocorrências:</strong> apagado <?= (int) $retencao['contato_ocorrencia'] ?> dias após a ocorrência ser resolvida ou arquivada.</li>
          <li><strong>Localização GPS exata:</strong> apagada <?= (int) $retencao['gps_ocorrencia'] ?> dias após o encerramento (fica apenas o bairro e a rua).</li>
          <li><strong>Fotos:</strong> apagadas <?= (int) $retencao['foto_ocorrencia'] ?> dias após o encerramento.</li>
          <li><strong>Controle de tentativas (hash do IP):</strong> apagado em até <?= (int) $retencao['registro_acao'] ?> dia.</li>
          <li><strong>Registro de ações de privacidade:</strong> <?= (int) round($retencao['registro_privacidade'] / 365) ?> anos, para comprovar o atendimento aos pedidos.</li>
          <li><strong>A ocorrência em si</strong> (tipo, bairro, rua, status, datas) é mantida para o histórico e os indicadores da cidade, sem dados pessoais depois dos prazos acima.</li>
        </ul>
      </section>

      <section id="direitos" class="legal-secao">
        <h2>6. Seus direitos (LGPD art. 18) e como exercê-los</h2>
        <div class="legal-direitos">
          <div><strong>Acesso e confirmação</strong><span>Veja o que está ligado à sua conta em Minha conta.</span></div>
          <div><strong>Portabilidade</strong><span>Baixe todos os seus dados em um arquivo JSON.</span></div>
          <div><strong>Correção</strong><span>Altere nome e telefone a qualquer momento.</span></div>
          <div><strong>Eliminação</strong><span>Exclua a conta; suas ocorrências ficam anônimas.</span></div>
          <div><strong>Revogação do consentimento</strong><span>Use o modo anônimo, exclua perfis Kids ou a conta.</span></div>
          <div><strong>Informação e reclamação</strong><span>Fale com a equipe ou com a ANPD (gov.br/anpd).</span></div>
        </div>
        <p>Os pedidos feitos em <a href="<?= baseUrl('usuario/conta') ?>">Minha conta</a> são atendidos na hora. Pedidos enviados à equipe
          são respondidos em até 15 dias.</p>
      </section>

      <section id="criancas" class="legal-secao">
        <h2>7. Crianças e adolescentes</h2>
        <p>A Área Kids pode ser usada sem conta: nesse caso o progresso fica <strong>só no navegador</strong> e nada é enviado ao servidor.
          Para salvar o progresso, um <strong>responsável adulto</strong> precisa estar logado; guardamos apenas o apelido escolhido
          (até 16 caracteres), os pontos e a quantidade de jogos. Não pedimos nome completo, idade, escola, foto ou localização de crianças.
          O responsável pode excluir cada perfil infantil em Minha conta.</p>
      </section>

      <section id="seguranca" class="legal-secao">
        <h2>8. Como protegemos os dados</h2>
        <ul class="legal-lista">
          <li>Senhas guardadas com hash bcrypt; ninguém da equipe consegue lê-las.</li>
          <li>Sessão com cookie protegido (HttpOnly, SameSite) que expira após 2 horas sem uso.</li>
          <li>Proteção contra envio de formulários por outros sites (token CSRF) e limite de tentativas de login.</li>
          <li>Fotos reprocessadas para remover metadados (inclusive o GPS gravado pelo celular) e acessíveis só à equipe.</li>
          <li>Painel administrativo restrito a contas de administrador; consultas ao banco parametrizadas.</li>
          <li>Arquivos internos (código, banco, configurações) bloqueados para acesso pela internet.</li>
        </ul>
        <p>Em caso de incidente de segurança que possa causar risco ou dano relevante, os titulares afetados e a ANPD serão comunicados (LGPD art. 48).
          Recomendamos que a versão publicada use HTTPS.</p>
      </section>

      <section id="contato" class="legal-secao">
        <h2>9. Contato e encarregado de dados</h2>
        <p>Encarregado/canal de privacidade: <strong><?= htmlspecialchars($contato['responsavel']) ?></strong>.
          <?php if (!empty($contato['email'])): ?>
            E-mail: <a href="mailto:<?= htmlspecialchars($contato['email'], ENT_QUOTES) ?>"><?= htmlspecialchars($contato['email']) ?></a>.
          <?php else: ?>
            Use os contatos da equipe listados no rodapé desta página.
          <?php endif; ?>
        </p>
        <p>Se mudarmos esta política, a versão e a data acima serão atualizadas e o novo texto passará a valer para os próximos aceites.</p>
        <p><a href="<?= baseUrl('home/termos') ?>">Leia também os Termos de Uso →</a></p>
      </section>

    </div>
  </section>

<?php require_once 'app/view/comuns/rodape.php'; ?>
