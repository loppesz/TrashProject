-- =====================================================================
-- Usuário do banco para a aplicação (princípio do menor privilégio).
-- Rode UMA vez como root, troque a senha e use em config.local.php:
--   'db_usuario' => 'coletafacil_app', 'db_senha' => '...'
-- A aplicação só lê e grava dados; não cria nem apaga tabelas.
-- Migrations continuam sendo aplicadas com um usuário administrativo.
-- =====================================================================
CREATE USER IF NOT EXISTS 'coletafacil_app'@'localhost' IDENTIFIED BY 'TROQUE_ESTA_SENHA';
GRANT SELECT, INSERT, UPDATE, DELETE ON coletafacil.* TO 'coletafacil_app'@'localhost';
FLUSH PRIVILEGES;
