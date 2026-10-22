<?php

/**
 * MODELO de configuracao local (credenciais da sua maquina).
 *
 * Como usar:
 *   1. Copie este arquivo para "config.local.php" (na mesma pasta).
 *   2. Preencha os valores da SUA maquina/servidor.
 *
 * O config.local.php NAO e versionado (esta no .gitignore), entao suas
 * senhas nunca vao para o repositorio.
 */

return [
    // Banco de dados. Em producao, use o usuario restrito criado por
    // modelo/usuario_banco_app.sql em vez do root.
    'db_usuario' => 'root',
    'db_senha'   => '',

    // Chave secreta da aplicacao (gere uma: php -r "echo bin2hex(random_bytes(32));").
    'app_key'    => '',

    // Mostra erros do PHP na tela. Use true SOMENTE no desenvolvimento.
    'debug'      => false,

    // LGPD: canal de contato para pedidos de titulares (opcional).
    // 'lgpd_email' => 'privacidade@seu-dominio.br',
];
