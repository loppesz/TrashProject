-- =====================================================================
-- LGPD e segurança.
--
-- 1) usuario: registra QUANDO e QUAL versão dos Termos/Política foi aceita
--    no cadastro (prova do consentimento — LGPD art. 8º, §2º).
-- 2) registro_acao: controle de tentativas (login, cadastro, ocorrências,
--    sugestões, consultas). Guarda só o HASH do IP (pseudonimizado) e é
--    limpo automaticamente após 24 horas.
-- 3) config_sistema: chave/valor para controles internos
--    (ex.: data da última aplicação da política de retenção).
-- 4) registro_privacidade: trilha de auditoria de ações sobre dados pessoais
--    (exportação, exclusão de conta, aplicação da retenção). Não guarda o
--    conteúdo dos dados, apenas o que foi feito e quando.
--
-- Idempotente: pode ser executada mais de uma vez.
-- =====================================================================

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS
               WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuario' AND COLUMN_NAME = 'termos_aceitos_em') = 0,
              'ALTER TABLE usuario ADD COLUMN termos_aceitos_em DATETIME NULL AFTER email_verificado_em, ADD COLUMN termos_versao VARCHAR(10) NULL AFTER termos_aceitos_em',
              'DO 0');
PREPARE st FROM @sql;
EXECUTE st;
DEALLOCATE PREPARE st;

CREATE TABLE IF NOT EXISTS registro_acao (
  id_registro BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  acao VARCHAR(30) NOT NULL,
  chave CHAR(64) NOT NULL,
  criada_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_registro_acao (acao, chave, criada_em),
  INDEX idx_registro_data (criada_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS config_sistema (
  chave VARCHAR(60) NOT NULL PRIMARY KEY,
  valor VARCHAR(255) NULL,
  atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS registro_privacidade (
  id_registro BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  acao VARCHAR(40) NOT NULL,
  id_usuario INT UNSIGNED NULL COMMENT 'Quem executou (nulo após exclusão da própria conta)',
  detalhes VARCHAR(255) NULL,
  criada_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_privacidade_data (criada_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
