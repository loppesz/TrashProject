<?php

/**
 * Configuracoes globais da aplicacao ColetaFacil.
 *
 * BASEURL usa caminho absoluto a partir da raiz do dominio ("/"), o que
 * funciona tanto no VirtualHost coletaseletiva/ quanto em coletafacil/ sem
 * precisar reconfigurar nada ao trocar o host.
 */

// URL base da aplicacao (raiz do VirtualHost).
define('BASEURL', '/');

// Dados de conexao do banco coletafacil (MySQL 8+, utf8mb4).
define('DB_CONF_CONEXAO', [
    'driver'   => 'mysql',
    'host'     => '127.0.0.1',
    'porta'    => '3306',
    'banco'    => 'coletafacil',
    'usuario'  => 'root',
    'senha'    => '',
    'charset'  => 'utf8mb4',
]);

// Fuso horario padrao da aplicacao.
date_default_timezone_set('America/Sao_Paulo');
