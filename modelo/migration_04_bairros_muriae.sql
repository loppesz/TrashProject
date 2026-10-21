-- =====================================================================
-- Bairros oficiais de Muriaé-MG (lista fornecida pela equipe do projeto).
--
-- 1) Cadastra/ativa todos os bairros da lista.
-- 2) Desativa (ativo = 0) os bairros que não pertencem à lista. NÃO apaga:
--    coletas e ocorrências antigas continuam ligadas ao bairro original.
-- 3) Move os pontos de descarte de demonstração que estavam em bairros
--    desativados para bairros da lista.
--
-- Idempotente: pode ser executada mais de uma vez.
-- =====================================================================
START TRANSACTION;

INSERT INTO bairro (nome, ativo) VALUES
('Santa Laura', TRUE),
('Villa Real', TRUE),
('Belisário', TRUE),
('Gaspar', TRUE),
('Gávea', TRUE),
('Prefeito Hélio Araújo', TRUE),
('Santana III', TRUE),
('Barra', TRUE),
('Planalto', TRUE),
('Centro', TRUE),
('São Vicente de Paulo', TRUE),
('Dornelas', TRUE),
('Joanópolis', TRUE),
('Área Rural de Muriaé', TRUE),
('Alto do Castelo', TRUE),
('Quinta das Flores', TRUE),
('São Joaquim', TRUE),
('Chácara Doutor Brum', TRUE),
('Santa Terezinha', TRUE),
('João VI', TRUE),
('Colety', TRUE),
('Santo Antônio', TRUE),
('São Cristóvão', TRUE),
('Vermelho', TRUE),
('Cerâmica', TRUE),
('União', TRUE),
('Vale do Castelo', TRUE),
('Zona Rural', TRUE),
('João XXIII', TRUE),
('Chalé', TRUE),
('São Francisco', TRUE),
('Padre Tiago', TRUE),
('Sofoco', TRUE),
('Napoleão', TRUE),
('Franco Suíço', TRUE),
('Porto Belo', TRUE),
('Safira', TRUE),
('Porto', TRUE),
('Distrito Industrial', TRUE),
('São Gotardo', TRUE),
('Primavera', TRUE),
('Chácara da Gávea', TRUE),
('Recreio', TRUE),
('Boa Família', TRUE),
('Alterosa', TRUE),
('Pirapanema', TRUE),
('Santana', TRUE),
('Vila Conceição', TRUE),
('Aeroporto', TRUE),
('Santa Helena', TRUE),
('Boa Vista', TRUE),
('Recanto Verde', TRUE),
('Cardoso de Melo', TRUE),
('São Pedro', TRUE)
ON DUPLICATE KEY UPDATE ativo = TRUE;

-- Pontos de demonstração que estavam em bairros fora da lista.
SET @b_sao_pedro := (SELECT id_bairro FROM bairro WHERE nome = 'São Pedro' LIMIT 1);
SET @b_barra     := (SELECT id_bairro FROM bairro WHERE nome = 'Barra' LIMIT 1);
SET @b_jose      := (SELECT id_bairro FROM bairro WHERE nome = 'José Cirilo' LIMIT 1);
SET @b_inconf    := (SELECT id_bairro FROM bairro WHERE nome = 'Inconfidência' LIMIT 1);

UPDATE ponto_descarte
   SET id_bairro = @b_sao_pedro,
       nome = 'Ponto de Entrega São Pedro',
       endereco = 'Bairro São Pedro – Muriaé'
 WHERE @b_jose IS NOT NULL AND id_bairro = @b_jose;

UPDATE ponto_descarte
   SET id_bairro = @b_barra,
       nome = 'Ponto de Entrega Barra',
       endereco = 'Bairro Barra – Muriaé',
       horario_funcionamento = 'Seg–Sex 07h–17h',
       icone = '♻️',
       observacao = NULL
 WHERE @b_inconf IS NOT NULL AND id_bairro = @b_inconf;

UPDATE ponto_foto pf
  JOIN ponto_descarte p ON p.id_ponto = pf.id_ponto
   SET pf.legenda = 'Ponto de entrega de papel no bairro Barra'
 WHERE p.nome = 'Ponto de Entrega Barra' AND pf.legenda LIKE '%escola%';

-- Desativa o que não está na lista oficial (comparação sem acento/caixa).
UPDATE bairro SET ativo = FALSE
 WHERE nome NOT IN (
  'Santa Laura','Villa Real','Belisário','Gaspar','Gávea','Prefeito Hélio Araújo','Santana III',
  'Barra','Planalto','Centro','São Vicente de Paulo','Dornelas','Joanópolis','Área Rural de Muriaé',
  'Alto do Castelo','Quinta das Flores','São Joaquim','Chácara Doutor Brum','Santa Terezinha','João VI',
  'Colety','Santo Antônio','São Cristóvão','Vermelho','Cerâmica','União','Vale do Castelo','Zona Rural',
  'João XXIII','Chalé','São Francisco','Padre Tiago','Sofoco','Napoleão','Franco Suíço','Porto Belo',
  'Safira','Porto','Distrito Industrial','São Gotardo','Primavera','Chácara da Gávea','Recreio',
  'Boa Família','Alterosa','Pirapanema','Santana','Vila Conceição','Aeroporto','Santa Helena',
  'Boa Vista','Recanto Verde','Cardoso de Melo','São Pedro'
 );

COMMIT;
