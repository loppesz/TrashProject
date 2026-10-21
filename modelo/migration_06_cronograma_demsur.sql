-- =====================================================================
-- Cronograma oficial de coleta de Muriaé (DEMSUR).
-- Fonte: https://www.demsur.com.br/pagina/107/cronograma-coleta
-- (consultado em outubro/2026).
--
-- 1) Estrutura da tabela coleta:
--    - tipo:   SELETIVA (recicláveis) ou COMUM (lixo doméstico).
--    - trecho: parte do bairro com horário próprio ('' = bairro todo),
--              ex.: "Parte alta", "Dornelas II".
--    - horario_fim passa a aceitar NULL: o DEMSUR divulga só o horário
--      aproximado de passagem do caminhão ("a partir de").
--    - chave única passa a considerar tipo e trecho.
-- 2) Desativa (ativo = 0) os horários de demonstração antigos
--    ("SAAE Muriaé"). NÃO apaga.
-- 3) Cadastra os horários do DEMSUR para os bairros ativos.
--    Bairros ativos sem horário no cronograma: Área Rural de Muriaé,
--    Boa Vista, Chácara da Gávea, Gávea e Zona Rural.
-- 4) Liga a coleta seletiva aos materiais recicláveis do guia
--    (aceito_coleta_seletiva = 1).
--
-- Idempotente: pode ser executada mais de uma vez.
-- Para desfazer os dados (passos 2 a 4):
--   DELETE FROM coleta WHERE observacao = 'Cronograma oficial do DEMSUR';
--   UPDATE coleta SET ativo = 1 WHERE equipe_responsavel = 'SAAE Muriaé';
-- =====================================================================

-- 1) Estrutura (DDL fora da transação; cada passo só roda se ainda faltar)
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS
               WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'coleta' AND COLUMN_NAME = 'tipo') = 0,
              'ALTER TABLE coleta ADD COLUMN tipo ENUM(''SELETIVA'',''COMUM'') NOT NULL DEFAULT ''SELETIVA'' AFTER id_bairro',
              'DO 0');
PREPARE st FROM @sql;
EXECUTE st;
DEALLOCATE PREPARE st;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS
               WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'coleta' AND COLUMN_NAME = 'trecho') = 0,
              'ALTER TABLE coleta ADD COLUMN trecho VARCHAR(80) NOT NULL DEFAULT '''' AFTER tipo',
              'DO 0');
PREPARE st FROM @sql;
EXECUTE st;
DEALLOCATE PREPARE st;

ALTER TABLE coleta MODIFY horario_fim TIME NULL;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.STATISTICS
               WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'coleta' AND INDEX_NAME = 'uk_coleta_bairro_dia_hora') > 0,
              'ALTER TABLE coleta DROP INDEX uk_coleta_bairro_dia_hora, ADD UNIQUE KEY uk_coleta_slot (id_bairro, tipo, trecho, dia_semana, horario_inicio)',
              'DO 0');
PREPARE st FROM @sql;
EXECUTE st;
DEALLOCATE PREPARE st;

START TRANSACTION;

-- 2) Horários de demonstração antigos fora do ar
UPDATE coleta SET ativo = 0 WHERE equipe_responsavel = 'SAAE Muriaé' AND ativo = 1;

-- 3) Cronograma DEMSUR. dia: 2=segunda ... 6=sexta, 7=sábado (igual ao "2ª".."6ª" do DEMSUR)
INSERT INTO coleta (id_bairro, tipo, trecho, dia_semana, horario_inicio, horario_fim,
                    equipe_responsavel, telefone_contato, observacao, ativo)
SELECT b.id_bairro, v.tipo, v.trecho, v.dia, CAST(v.hora AS TIME), NULL,
       'DEMSUR', '0800 283 1453', 'Cronograma oficial do DEMSUR', TRUE
  FROM (VALUES
    ROW('Aeroporto','COMUM','',2,'08:00'), ROW('Aeroporto','COMUM','',4,'08:00'), ROW('Aeroporto','COMUM','',6,'08:30'),
    ROW('Alterosa','COMUM','',3,'07:50'), ROW('Alterosa','COMUM','',5,'07:50'), ROW('Alterosa','COMUM','',7,'07:50'),
    ROW('Alto do Castelo','COMUM','',3,'08:30'), ROW('Alto do Castelo','COMUM','',5,'08:30'), ROW('Alto do Castelo','COMUM','',7,'08:30'),
    ROW('Alto do Castelo','SELETIVA','',3,'07:30'), ROW('Alto do Castelo','SELETIVA','',5,'07:30'),
    ROW('Barra','COMUM','',2,'18:50'), ROW('Barra','COMUM','',3,'18:30'), ROW('Barra','COMUM','',6,'18:30'),
    ROW('Barra','COMUM','',7,'17:00'), ROW('Barra','COMUM','',7,'19:00'),
    ROW('Barra','SELETIVA','',2,'07:00'), ROW('Barra','SELETIVA','',3,'07:00'), ROW('Barra','SELETIVA','',4,'07:00'),
    ROW('Barra','SELETIVA','',5,'07:00'), ROW('Barra','SELETIVA','',6,'07:00'),
    ROW('Belisário','COMUM','',2,'07:00'), ROW('Belisário','COMUM','',4,'07:00'), ROW('Belisário','COMUM','',6,'07:30'),
    ROW('Boa Família','COMUM','',2,'09:20'), ROW('Boa Família','COMUM','',4,'07:30'), ROW('Boa Família','COMUM','',6,'07:00'),
    ROW('Cardoso de Melo','COMUM','',2,'10:50'), ROW('Cardoso de Melo','COMUM','',4,'10:00'), ROW('Cardoso de Melo','COMUM','',6,'10:20'),
    ROW('Cardoso de Melo','COMUM','Parte baixa',2,'12:40'), ROW('Cardoso de Melo','COMUM','Parte baixa',4,'10:50'), ROW('Cardoso de Melo','COMUM','Parte baixa',6,'10:50'),
    ROW('Centro','COMUM','',2,'18:30'), ROW('Centro','COMUM','',3,'18:20'), ROW('Centro','COMUM','',6,'18:20'), ROW('Centro','COMUM','',7,'17:30'),
    ROW('Centro','SELETIVA','',2,'07:00'), ROW('Centro','SELETIVA','',3,'07:00'), ROW('Centro','SELETIVA','',4,'07:00'),
    ROW('Centro','SELETIVA','',5,'07:00'), ROW('Centro','SELETIVA','',6,'07:00'),
    ROW('Cerâmica','COMUM','',2,'07:50'), ROW('Cerâmica','COMUM','',4,'07:50'), ROW('Cerâmica','COMUM','',6,'07:50'),
    ROW('Cerâmica','SELETIVA','',3,'07:30'), ROW('Cerâmica','SELETIVA','',5,'07:30'),
    ROW('Chácara Doutor Brum','COMUM','',2,'07:40'), ROW('Chácara Doutor Brum','COMUM','',4,'07:40'), ROW('Chácara Doutor Brum','COMUM','',6,'07:40'),
    ROW('Chalé','COMUM','',3,'10:10'), ROW('Chalé','COMUM','',5,'10:10'), ROW('Chalé','COMUM','',7,'08:20'),
    ROW('Colety','COMUM','',3,'07:40'), ROW('Colety','COMUM','',5,'07:40'), ROW('Colety','COMUM','',7,'07:40'),
    ROW('Colety','COMUM','Parte alta',3,'08:50'), ROW('Colety','COMUM','Parte alta',5,'08:40'), ROW('Colety','COMUM','Parte alta',7,'08:30'),
    ROW('Colety','COMUM','Colety / São Gotardo',3,'09:30'), ROW('Colety','COMUM','Colety / São Gotardo',5,'09:00'), ROW('Colety','COMUM','Colety / São Gotardo',7,'08:30'),
    ROW('Colety','SELETIVA','',2,'07:00'), ROW('Colety','SELETIVA','',3,'07:00'), ROW('Colety','SELETIVA','',4,'07:00'),
    ROW('Colety','SELETIVA','',5,'07:00'), ROW('Colety','SELETIVA','',6,'07:00'),
    ROW('Distrito Industrial','COMUM','',2,'13:15'), ROW('Distrito Industrial','COMUM','',4,'12:30'), ROW('Distrito Industrial','COMUM','',6,'12:30'),
    ROW('Dornelas','COMUM','',2,'07:40'), ROW('Dornelas','COMUM','',4,'07:40'), ROW('Dornelas','COMUM','',6,'07:40'),
    ROW('Dornelas','COMUM','Dornelas II',2,'08:50'), ROW('Dornelas','COMUM','Dornelas II',4,'08:15'), ROW('Dornelas','COMUM','Dornelas II',6,'08:15'),
    ROW('Dornelas','COMUM','Dornelas II Residencial',2,'08:50'), ROW('Dornelas','COMUM','Dornelas II Residencial',4,'08:30'), ROW('Dornelas','COMUM','Dornelas II Residencial',6,'08:30'),
    ROW('Franco Suíço','COMUM','',2,'09:10'), ROW('Franco Suíço','COMUM','',4,'09:00'), ROW('Franco Suíço','COMUM','',6,'09:00'),
    ROW('Gaspar','COMUM','',3,'09:20'), ROW('Gaspar','COMUM','',5,'09:00'), ROW('Gaspar','COMUM','',7,'09:00'),
    ROW('Joanópolis','COMUM','',2,'09:40'), ROW('Joanópolis','COMUM','',4,'09:00'), ROW('Joanópolis','COMUM','',6,'09:20'),
    ROW('João VI','COMUM','',3,'10:00'), ROW('João VI','COMUM','',5,'09:50'), ROW('João VI','COMUM','',7,'09:30'),
    ROW('João VI','SELETIVA','',2,'07:00'), ROW('João VI','SELETIVA','',6,'07:00'),
    ROW('João XXIII','COMUM','',3,'09:40'), ROW('João XXIII','COMUM','',5,'09:30'), ROW('João XXIII','COMUM','',7,'09:20'),
    ROW('João XXIII','COMUM','Parte alta',3,'10:00'), ROW('João XXIII','COMUM','Parte alta',5,'09:30'), ROW('João XXIII','COMUM','Parte alta',7,'09:30'),
    ROW('João XXIII','COMUM','Parte baixa',3,'11:00'), ROW('João XXIII','COMUM','Parte baixa',5,'09:40'), ROW('João XXIII','COMUM','Parte baixa',7,'09:20'),
    ROW('João XXIII','SELETIVA','',2,'07:00'), ROW('João XXIII','SELETIVA','',6,'07:00'),
    ROW('Napoleão','COMUM','',2,'09:20'), ROW('Napoleão','COMUM','',4,'09:00'), ROW('Napoleão','COMUM','',6,'09:00'),
    ROW('Padre Tiago','COMUM','',3,'09:00'), ROW('Padre Tiago','COMUM','',5,'08:40'), ROW('Padre Tiago','COMUM','',7,'08:20'),
    ROW('Pirapanema','COMUM','',3,'10:00'), ROW('Pirapanema','COMUM','',5,'10:00'), ROW('Pirapanema','COMUM','',7,'10:00'),
    ROW('Planalto','COMUM','',2,'08:30'), ROW('Planalto','COMUM','',4,'08:20'), ROW('Planalto','COMUM','',6,'08:20'),
    ROW('Planalto','SELETIVA','',3,'07:30'), ROW('Planalto','SELETIVA','',5,'07:30'),
    ROW('Porto','COMUM','',2,'18:00'), ROW('Porto','COMUM','',2,'21:20'),
    ROW('Porto','COMUM','',3,'18:30'), ROW('Porto','COMUM','',3,'21:00'),
    ROW('Porto','COMUM','',6,'18:30'), ROW('Porto','COMUM','',6,'21:00'),
    ROW('Porto','COMUM','',7,'17:00'), ROW('Porto','COMUM','',7,'19:00'),
    ROW('Porto','COMUM','Ruas Felipe Couri e Amâncio de Freitas',3,'07:40'), ROW('Porto','COMUM','Ruas Felipe Couri e Amâncio de Freitas',5,'07:40'),
    ROW('Porto','COMUM','Ruas Felipe Couri e Amâncio de Freitas',7,'07:35'),
    ROW('Porto Belo','COMUM','',3,'09:20'), ROW('Porto Belo','COMUM','',5,'09:20'), ROW('Porto Belo','COMUM','',7,'08:00'),
    ROW('Prefeito Hélio Araújo','COMUM','',3,'08:20'), ROW('Prefeito Hélio Araújo','COMUM','',5,'08:10'), ROW('Prefeito Hélio Araújo','COMUM','',7,'08:00'),
    ROW('Prefeito Hélio Araújo','SELETIVA','',2,'07:00'), ROW('Prefeito Hélio Araújo','SELETIVA','',3,'07:00'), ROW('Prefeito Hélio Araújo','SELETIVA','',4,'07:00'),
    ROW('Prefeito Hélio Araújo','SELETIVA','',5,'07:00'), ROW('Prefeito Hélio Araújo','SELETIVA','',6,'07:00'),
    ROW('Primavera','COMUM','Parte alta',3,'09:10'), ROW('Primavera','COMUM','Parte alta',5,'09:00'), ROW('Primavera','COMUM','Parte alta',7,'09:00'),
    ROW('Primavera','COMUM','Parte baixa',3,'08:00'), ROW('Primavera','COMUM','Parte baixa',5,'08:00'), ROW('Primavera','COMUM','Parte baixa',7,'08:00'),
    ROW('Primavera','SELETIVA','Parte alta',2,'07:00'), ROW('Primavera','SELETIVA','Parte alta',3,'07:00'), ROW('Primavera','SELETIVA','Parte alta',4,'07:00'),
    ROW('Primavera','SELETIVA','Parte alta',5,'07:00'), ROW('Primavera','SELETIVA','Parte alta',6,'07:00'),
    ROW('Quinta das Flores','COMUM','',3,'07:50'), ROW('Quinta das Flores','COMUM','',5,'07:50'), ROW('Quinta das Flores','COMUM','',7,'07:50'),
    ROW('Quinta das Flores','SELETIVA','',2,'07:00'), ROW('Quinta das Flores','SELETIVA','',3,'07:00'), ROW('Quinta das Flores','SELETIVA','',4,'07:00'),
    ROW('Quinta das Flores','SELETIVA','',5,'07:00'), ROW('Quinta das Flores','SELETIVA','',6,'07:00'),
    ROW('Recanto Verde','COMUM','',2,'13:30'), ROW('Recanto Verde','COMUM','',4,'11:40'), ROW('Recanto Verde','COMUM','',6,'11:30'),
    ROW('Recreio','COMUM','',2,'09:30'), ROW('Recreio','COMUM','',4,'08:30'),
    ROW('Safira','COMUM','',2,'07:50'), ROW('Safira','COMUM','',4,'07:50'), ROW('Safira','COMUM','',6,'07:50'),
    ROW('Safira','SELETIVA','',3,'07:30'), ROW('Safira','SELETIVA','',5,'07:30'),
    ROW('Santa Helena','COMUM','',2,'10:30'), ROW('Santa Helena','COMUM','',4,'10:00'), ROW('Santa Helena','COMUM','',6,'10:00'),
    ROW('Santa Laura','COMUM','',3,'07:50'), ROW('Santa Laura','COMUM','',5,'08:00'), ROW('Santa Laura','COMUM','',7,'07:50'),
    ROW('Santa Terezinha','COMUM','',2,'07:40'), ROW('Santa Terezinha','COMUM','',4,'07:40'), ROW('Santa Terezinha','COMUM','',6,'07:40'),
    ROW('Santana','COMUM','',2,'09:40'), ROW('Santana','COMUM','',4,'09:40'), ROW('Santana','COMUM','',6,'09:40'),
    ROW('Santana III','COMUM','',2,'10:20'), ROW('Santana III','COMUM','',4,'10:40'), ROW('Santana III','COMUM','',6,'10:40'),
    ROW('Santo Antônio','COMUM','',2,'11:30'), ROW('Santo Antônio','COMUM','',4,'11:30'), ROW('Santo Antônio','COMUM','',6,'10:50'),
    ROW('Santo Antônio','COMUM','Santo Antônio II',3,'11:30'), ROW('Santo Antônio','COMUM','Santo Antônio II',5,'10:10'), ROW('Santo Antônio','COMUM','Santo Antônio II',7,'10:00'),
    ROW('São Cristóvão','COMUM','',3,'11:00'), ROW('São Cristóvão','COMUM','',5,'09:40'), ROW('São Cristóvão','COMUM','',7,'09:30'),
    ROW('São Francisco','COMUM','',2,'08:40'), ROW('São Francisco','COMUM','',4,'08:25'), ROW('São Francisco','COMUM','',6,'08:15'),
    ROW('São Francisco','SELETIVA','',3,'07:30'), ROW('São Francisco','SELETIVA','',5,'07:30'),
    ROW('São Gotardo','COMUM','',3,'08:40'), ROW('São Gotardo','COMUM','',5,'08:30'), ROW('São Gotardo','COMUM','',7,'08:20'),
    ROW('São Joaquim','COMUM','',2,'07:50'), ROW('São Joaquim','COMUM','',4,'07:50'), ROW('São Joaquim','COMUM','',6,'07:50'),
    ROW('São Pedro','COMUM','',2,'11:20'), ROW('São Pedro','COMUM','',4,'10:30'), ROW('São Pedro','COMUM','',6,'10:30'),
    ROW('São Vicente de Paulo','COMUM','',2,'12:40'), ROW('São Vicente de Paulo','COMUM','',4,'09:40'), ROW('São Vicente de Paulo','COMUM','',6,'10:30'),
    ROW('São Vicente de Paulo','SELETIVA','',3,'07:30'), ROW('São Vicente de Paulo','SELETIVA','',5,'07:30'),
    ROW('Sofoco','COMUM','',3,'13:30'), ROW('Sofoco','COMUM','',5,'13:30'),
    ROW('União','COMUM','',3,'13:00'), ROW('União','COMUM','',5,'10:30'), ROW('União','COMUM','',7,'11:00'),
    ROW('Vale do Castelo','COMUM','',2,'07:50'), ROW('Vale do Castelo','COMUM','',4,'07:45'), ROW('Vale do Castelo','COMUM','',6,'07:45'),
    ROW('Vale do Castelo','SELETIVA','',3,'07:30'), ROW('Vale do Castelo','SELETIVA','',5,'07:30'),
    ROW('Vermelho','COMUM','',7,'08:50'),
    ROW('Vermelho','COMUM','Centro e Morro',3,'11:30'), ROW('Vermelho','COMUM','Centro e Morro',5,'10:40'),
    ROW('Vermelho','COMUM','Lado da pista',3,'09:30'), ROW('Vermelho','COMUM','Lado da pista',5,'09:00'),
    ROW('Vermelho','COMUM','Vermelho II',3,'09:00'), ROW('Vermelho','COMUM','Vermelho II',5,'08:30'), ROW('Vermelho','COMUM','Vermelho II',7,'08:30'),
    ROW('Vila Conceição','COMUM','',3,'08:30'), ROW('Vila Conceição','COMUM','',5,'08:30'), ROW('Vila Conceição','COMUM','',7,'07:40'),
    ROW('Villa Real','COMUM','',2,'08:15'), ROW('Villa Real','COMUM','',4,'08:10'), ROW('Villa Real','COMUM','',6,'08:00')
  ) AS v (bairro, tipo, trecho, dia, hora)
  JOIN bairro b ON b.nome = v.bairro AND b.ativo = 1
 WHERE NOT EXISTS (
        SELECT 1 FROM coleta c
         WHERE c.id_bairro = b.id_bairro AND c.tipo = v.tipo AND c.trecho = v.trecho
           AND c.dia_semana = v.dia AND c.horario_inicio = CAST(v.hora AS TIME)
       );

-- 4) Coleta seletiva recolhe os recicláveis do guia de materiais
INSERT INTO coleta_material (id_coleta, id_material)
SELECT c.id_coleta, m.id_material
  FROM coleta c
  JOIN material m ON m.aceito_coleta_seletiva = 1 AND m.ativo = 1
 WHERE c.tipo = 'SELETIVA' AND c.observacao = 'Cronograma oficial do DEMSUR'
   AND NOT EXISTS (SELECT 1 FROM coleta_material cm WHERE cm.id_coleta = c.id_coleta AND cm.id_material = m.id_material);

COMMIT;
