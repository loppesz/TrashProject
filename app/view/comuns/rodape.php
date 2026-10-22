  <!-- FOOTER -->
  <footer class="footer">
    <div class="container footer-inner">
      <div class="footer-brand">
        <a href="<?= baseUrl() ?>" class="logo footer-logo"><span class="logo-icon" aria-hidden="true">♻️</span><span class="logo-text">ColetaFácil</span></a>
        <p>Informação e participação para uma Muriaé mais limpa, segura e sustentável.</p>
      </div>
      <div class="footer-links">
        <h4>Plataforma</h4>
        <ul>
          <li><a href="<?= baseUrl() ?>">Início</a></li>
          <li><a href="<?= baseUrl('home/materiais') ?>">Guia de Materiais</a></li>
          <li><a href="<?= baseUrl('ponto') ?>">Pontos de Descarte</a></li>
          <li><a href="<?= baseUrl('ocorrencia') ?>">Registrar Ocorrência</a></li>
          <li><a href="<?= baseUrl('home/recompensas') ?>">Recompensas</a></li>
          <li><a href="<?= baseUrl('home/kids') ?>">Área Kids</a></li>
        </ul>
      </div>
      <div class="footer-links">
        <h4>Privacidade</h4>
        <ul>
          <li><a href="<?= baseUrl('home/privacidade') ?>">Política de Privacidade</a></li>
          <li><a href="<?= baseUrl('home/termos') ?>">Termos de Uso</a></li>
          <li><a href="<?= baseUrl('usuario/conta') ?>">Meus dados (LGPD)</a></li>
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

  <!-- Aviso de privacidade (cookies essenciais + serviços de terceiros). Não bloqueia o uso. -->
  <div class="aviso-privacidade" id="avisoPrivacidade" role="region" aria-label="Aviso de privacidade" hidden>
    <p>
      Usamos apenas um <strong>cookie essencial</strong> de sessão (para manter seu login) e o armazenamento do
      navegador para o tema e o progresso da Área Kids. Mapas e fontes vêm de serviços de terceiros.
      <a href="<?= baseUrl('home/privacidade') ?>">Saiba mais</a>
    </p>
    <button type="button" class="aviso-privacidade-btn" onclick="fecharAvisoPrivacidade()">Entendi</button>
  </div>

  <?php $assetV = defined('ASSET_VER') ? ASSET_VER : '1'; ?>
  <?php if (!empty($usaLeaflet)): ?>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
  <?php endif; ?>
  <script src="<?= baseUrl('assets/js/script.js') ?>?v=<?= $assetV ?>"></script>
  <?php foreach (($jsPagina ?? []) as $js): ?>
  <script src="<?= baseUrl('assets/js/' . $js) ?>?v=<?= $assetV ?>"></script>
  <?php endforeach; ?>
</body>
</html>
