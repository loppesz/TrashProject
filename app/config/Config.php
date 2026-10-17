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
define('DB_CONF_CONEXAO', [
    'driver'   => 'mysql',
    'host'     => '127.0.0.1',
    'porta'    => '3306',
    'banco'    => 'coletafacil',
    'usuario'  => 'root',
    'senha'    => $configLocal['db_senha'] ?? '',
    'charset'  => 'utf8mb4',
]);

// Versao dos assets (CSS/JS) para controle de cache do navegador.
// Incremente ao publicar mudancas de estilo/script.
define('ASSET_VER', '7');

// Fuso horario padrao da aplicacao.
date_default_timezone_set('America/Sao_Paulo');
