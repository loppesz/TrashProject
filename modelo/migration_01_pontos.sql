-- =====================================================================
-- Migração 01 — Enriquecimento dos Pontos de Descarte
-- Alinha o schema ao que a aplicação já exibe na tela de Pontos:
--   - Colunas de contato, acessibilidade, avaliação e aparência
--   - Observação livre
--   - Nova tabela ponto_horario (horário por dia da semana)
--
-- Uso (WAMP, MySQL 3306):
--   mysql -h 127.0.0.1 -P 3306 -u root coletafacil < modelo/migration_01_pontos.sql
-- =====================================================================

USE coletafacil;

-- --- Novas colunas em ponto_descarte -------------------------------------
ALTER TABLE ponto_descarte
  ADD COLUMN telefone           VARCHAR(20)   NULL AFTER status_funcionamento,
  ADD COLUMN email              VARCHAR(150)  NULL AFTER telefone,
  ADD COLUMN whatsapp           VARCHAR(20)   NULL AFTER email,
  ADD COLUMN acessivel_pcd      BOOLEAN       NOT NULL DEFAULT FALSE AFTER whatsapp,
  ADD COLUMN tem_estacionamento BOOLEAN       NOT NULL DEFAULT FALSE AFTER acessivel_pcd,
  ADD COLUMN avaliacao_media    DECIMAL(2,1)  NULL AFTER tem_estacionamento,
  ADD COLUMN avaliacao_total    INT UNSIGNED  NOT NULL DEFAULT 0 AFTER avaliacao_media,
  ADD COLUMN icone              VARCHAR(8)    NULL AFTER avaliacao_total,
  ADD COLUMN cor                VARCHAR(20)   NULL AFTER icone,
  ADD COLUMN observacao         VARCHAR(300)  NULL AFTER cor;

-- --- Nova tabela: horário por dia da semana ------------------------------
-- dia_semana: 1=domingo, 2=segunda, ... 7=sábado (mesmo padrão da tabela coleta)
CREATE TABLE IF NOT EXISTS ponto_horario (
  id_ponto_horario BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_ponto INT UNSIGNED NOT NULL,
  dia_semana TINYINT UNSIGNED NOT NULL,
  horario_inicio TIME NULL,
  horario_fim TIME NULL,
  fechado BOOLEAN NOT NULL DEFAULT FALSE,
  FOREIGN KEY (id_ponto) REFERENCES ponto_descarte(id_ponto) ON DELETE CASCADE,
  CONSTRAINT ck_ponto_horario_dia CHECK (dia_semana BETWEEN 1 AND 7),
  UNIQUE KEY uk_ponto_horario (id_ponto, dia_semana)
) ENGINE=InnoDB;
