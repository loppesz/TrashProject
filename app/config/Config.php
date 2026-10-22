<?php

/**
 * Configuracoes globais da aplicacao ColetaFacil.
 *
 * BASEURL e detectada automaticamente, funcionando tanto em subpasta
 * (http://localhost/TrashProject/) quanto na raiz de um VirtualHost.
 *
 * Credenciais sensiveis (senha do banco) ficam em config.local.php,
 * que NAO e versionado. Veja config.local.exemplo.php.
 */

// Carrega as credenciais locais (senha do banco, etc.), se existirem.
$configLocal = [];
$arquivoLocal = __DIR__ . '/config.local.php';
if (file_exists($arquivoLocal)) {
    $configLocal = require $arquivoLocal;
}

// URL base da aplicacao, detectada automaticamente.
// Funciona tanto em subpasta (http://localhost/TrashProject/) quanto na raiz
// de um VirtualHost (http://coletafacil/), sem precisar reconfigurar.
if (!defined('BASEURL')) {
    // Diretorio onde esta o index.php (front controller), relativo a raiz web.
    $raizApp = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    $raizApp = rtrim($raizApp, '/') . '/';   // garante barra final
    define('BASEURL', $raizApp);
}

// Dados de conexao do banco coletafacil (MySQL 8+, utf8mb4).
// Em producao, use um usuario proprio com permissoes minimas
// (ver modelo/usuario_banco_app.sql) em vez do root.
define('DB_CONF_CONEXAO', [
    'driver'   => 'mysql',
    'host'     => $configLocal['db_host'] ?? '127.0.0.1',
    'porta'    => $configLocal['db_porta'] ?? '3306',
    'banco'    => $configLocal['db_banco'] ?? 'coletafacil',
    'usuario'  => $configLocal['db_usuario'] ?? 'root',
    'senha'    => $configLocal['db_senha'] ?? '',
    'charset'  => 'utf8mb4',
]);

// Modo de depuracao: so mostra erros do PHP na tela quando ligado em
// config.local.php ('debug' => true). Em producao fica desligado para nao
// expor caminhos, consultas ou dados em mensagens de erro.
define('APP_DEBUG', (bool) ($configLocal['debug'] ?? false));
ini_set('display_errors', APP_DEBUG ? '1' : '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

// Chave secreta da aplicacao (HMAC do IP no controle de tentativas).
// Defina 'app_key' em config.local.php com um valor aleatorio longo.
define('APP_KEY', (string) ($configLocal['app_key'] ?? hash('sha256', 'coletafacil|' . __DIR__ . '|' . php_uname('n'))));

// LGPD: versao vigente dos Termos de Uso e da Politica de Privacidade.
// Ao alterar o texto, incremente a versao (o aceite fica registrado por versao).
define('TERMOS_VERSAO', '1.0');
define('TERMOS_DATA', '21/10/2026');

// LGPD: contato do encarregado/canal de privacidade. Se nao houver e-mail
// configurado, as paginas indicam os contatos da equipe no rodape.
define('LGPD_CONTATO', [
    'responsavel' => $configLocal['lgpd_responsavel'] ?? 'Equipe do projeto ColetaFácil (ADS – Extensão Universitária III)',
    'email'       => $configLocal['lgpd_email'] ?? '',
]);

// LGPD: prazos de retencao (em dias), aplicados pelo painel admin
// (Admin > Privacidade). Ver Politica de Privacidade.
define('RETENCAO_DIAS', [
    'contato_ocorrencia' => 180,   // nome/telefone/e-mail de ocorrencias encerradas
    'gps_ocorrencia'     => 180,   // coordenadas exatas de ocorrencias encerradas
    'foto_ocorrencia'    => 365,   // fotos de ocorrencias encerradas
    'registro_acao'      => 1,     // controle de tentativas (hash do IP)
    'registro_privacidade' => 730, // trilha de auditoria (exportacoes, exclusoes)
]);

// Area geografica do municipio de Muriae-MG (limites do OpenStreetMap,
// relacao 315428). Usada para validar GPS e limitar os mapas, incluindo
// os distritos rurais (Belisario, Vermelho, Boa Familia, Pirapanema...).
define('MURIAE_GEO', [
    'centro' => [-21.1306, -42.3664],
    'lat_min' => -21.31, 'lat_max' => -20.86,
    'lng_min' => -42.57, 'lng_max' => -42.25,
]);

// Versao dos assets (CSS/JS) para controle de cache do navegador.
// Incremente ao publicar mudancas de estilo/script.
define('ASSET_VER', '31');

// Fuso horario padrao da aplicacao.
date_default_timezone_set('America/Sao_Paulo');
