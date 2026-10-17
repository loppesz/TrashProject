-- =====================================================================
-- Migração 02 — Enriquecimento dos Materiais (Etapa B)
-- Alinha a tabela `material` ao que a tela de Materiais exibe:
--   - status de coleta em 3 estados (aceito / cuidado / nao aceito)
--   - icone e cor do card, rotulo da secao de preparo
--   - instrucoes (lista) e exemplos (tags) normalizados em tabelas proprias
--
-- Uso (WAMP, MySQL 3306):
--   mysql -h 127.0.0.1 -P 3306 -u root coletafacil < modelo/migration_02_etapaB.sql
-- =====================================================================

USE coletafacil;

-- --- Novas colunas em material -------------------------------------------
ALTER TABLE material
  ADD COLUMN status_coleta ENUM('ACEITO','CUIDADO','NAO_ACEITO') NOT NULL DEFAULT 'ACEITO' AFTER aceito_coleta_seletiva,
  ADD COLUMN icone         VARCHAR(8)  NULL AFTER status_coleta,
  ADD COLUMN cor_card      VARCHAR(20) NULL AFTER icone,
  ADD COLUMN preparo_label VARCHAR(40) NULL AFTER cor_card;

-- --- Instrucoes de preparo (lista ordenada por material) -----------------
CREATE TABLE IF NOT EXISTS material_instrucao (
  id_material_instrucao BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_material INT UNSIGNED NOT NULL,
  ordem TINYINT UNSIGNED NOT NULL DEFAULT 1,
  texto VARCHAR(300) NOT NULL,
  perigo BOOLEAN NOT NULL DEFAULT FALSE,
  FOREIGN KEY (id_material) REFERENCES material(id_material) ON DELETE CASCADE,
  UNIQUE KEY uk_material_instrucao (id_material, ordem)
) ENGINE=InnoDB;

-- --- Exemplos / tags do material -----------------------------------------
CREATE TABLE IF NOT EXISTS material_exemplo (
  id_material_exemplo BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_material INT UNSIGNED NOT NULL,
  ordem TINYINT UNSIGNED NOT NULL DEFAULT 1,
  icone VARCHAR(8) NULL,
  rotulo VARCHAR(60) NOT NULL,
  negativo BOOLEAN NOT NULL DEFAULT FALSE,
  FOREIGN KEY (id_material) REFERENCES material(id_material) ON DELETE CASCADE,
  UNIQUE KEY uk_material_exemplo (id_material, ordem)
) ENGINE=InnoDB;
