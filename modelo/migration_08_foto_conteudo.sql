-- =====================================================================
-- Fotos de ocorrência: coluna de bytes (conteudo).
--
-- Bancos criados antes da migração para armazenamento em BLOB tinham a
-- tabela ocorrencia_foto apenas com url_arquivo (foto em disco). O código
-- atual grava e lê os bytes da coluna "conteudo" (sem metadados EXIF/GPS).
-- Sem essa coluna, o INSERT e o SELECT da foto quebravam: no admin a imagem
-- não aparecia e o registro de ocorrência exibia "tente novamente" mesmo
-- tendo sido gravado.
--
-- Esta migração garante as colunas conteudo e tamanho_bytes, alinhando o
-- banco ao modeloColetaFacil.sql.
--
-- Idempotente: pode ser executada mais de uma vez.
-- =====================================================================

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS
               WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ocorrencia_foto' AND COLUMN_NAME = 'conteudo') = 0,
              'ALTER TABLE ocorrencia_foto ADD COLUMN conteudo LONGBLOB NULL AFTER id_ocorrencia',
              'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS
               WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ocorrencia_foto' AND COLUMN_NAME = 'tamanho_bytes') = 0,
              'ALTER TABLE ocorrencia_foto ADD COLUMN tamanho_bytes INT UNSIGNED NULL AFTER tipo_mime',
              'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
