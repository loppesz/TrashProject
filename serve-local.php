<?php
/**
 * Router para o servidor embutido do PHP (apenas desenvolvimento local).
 *
 * Permite abrir o projeto em http://localhost:8080, que é um "contexto seguro"
 * para o navegador — assim recursos como a geolocalização (Usar minha
 * localização) funcionam, o que NÃO acontece via VirtualHost em http://.
 *
 * Simula o rewrite do .htaccess: arquivos reais (css/js/img) são servidos
 * diretamente; qualquer outra rota cai no index.php (Front Controller).
 *
 * NÃO use em produção — serve apenas para rodar/testar na sua máquina.
 */

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Serve arquivos reais existentes diretamente (assets, manifest, sw.js...).
$alvo = __DIR__ . $path;
if ($path !== '/' && file_exists($alvo) && !is_dir($alvo)) {
    return false;
}

// Demais requisições vão para o Front Controller, com a rota em $_GET['url'].
$_GET['url'] = ltrim($path, '/');
require __DIR__ . '/index.php';
