-- Padroniza os dados demonstrativos antigos para referências de Muriaé-MG.
-- Não remove ocorrências, fotos, usuários ou relacionamentos.
START TRANSACTION;

UPDATE bairro SET nome = 'Dornelas'
 WHERE nome = 'Bairro Novo'
   AND NOT EXISTS (SELECT 1 FROM (SELECT nome FROM bairro) b WHERE b.nome = 'Dornelas');
UPDATE bairro SET nome = 'Porto'
 WHERE nome = 'Vila Nova'
   AND NOT EXISTS (SELECT 1 FROM (SELECT nome FROM bairro) b WHERE b.nome = 'Porto');
UPDATE bairro SET nome = 'José Cirilo'
 WHERE nome = 'Jardim América'
   AND NOT EXISTS (SELECT 1 FROM (SELECT nome FROM bairro) b WHERE b.nome = 'José Cirilo');
UPDATE bairro SET nome = 'Inconfidência'
 WHERE nome = 'Bairro Escolar'
   AND NOT EXISTS (SELECT 1 FROM (SELECT nome FROM bairro) b WHERE b.nome = 'Inconfidência');

UPDATE ponto_descarte
   SET nome = 'Ponto de Entrega Dornelas',
       endereco = 'Rua João Dornelas – Dornelas',
       avaliacao_media = NULL,
       avaliacao_total = 0
 WHERE nome = 'Farmácia Saúde Total';

UPDATE ponto_descarte
   SET nome = 'Ponto de Entrega Porto',
       endereco = 'Rua Coronel Pereira Sobrinho – Porto',
       avaliacao_media = NULL,
       avaliacao_total = 0
 WHERE nome = 'Supermercado Boa Compra';

UPDATE ponto_descarte
   SET nome = 'Ponto de Entrega José Cirilo',
       endereco = 'Rua São Pedro – José Cirilo',
       avaliacao_media = NULL,
       avaliacao_total = 0
 WHERE nome LIKE 'Ponto Recicle%Jardim América%';

UPDATE ponto_descarte
   SET nome = 'E.M. Professora Esmeralda Vianna',
       endereco = 'Bairro Inconfidência – Muriaé',
       avaliacao_media = NULL,
       avaliacao_total = 0
 WHERE nome = 'E.M. Prof. Souza';

UPDATE ponto_descarte
   SET endereco = 'Rua Coronel Amador Pinheiro de Barros – Centro',
       avaliacao_media = NULL,
       avaliacao_total = 0
 WHERE nome = 'Ecoponto Centro' AND endereco LIKE 'Rua das Flores%';

UPDATE ocorrencia SET endereco_descricao = REPLACE(endereco_descricao, 'Rua das Palmeiras', 'Rua Doutor Alves Pequeno')
 WHERE endereco_descricao LIKE 'Rua das Palmeiras%';
UPDATE ocorrencia SET endereco_descricao = REPLACE(endereco_descricao, 'Av. Brasil', 'Rua João Dornelas')
 WHERE endereco_descricao LIKE 'Av. Brasil%';
UPDATE ocorrencia SET endereco_descricao = REPLACE(endereco_descricao, 'Rua do Comércio', 'Rua Barão do Monte Alto')
 WHERE endereco_descricao LIKE 'Rua do Comércio%';
UPDATE ocorrencia SET endereco_descricao = REPLACE(endereco_descricao, 'Praça da Amizade', 'Rua São Pedro')
 WHERE endereco_descricao LIKE 'Praça da Amizade%';

COMMIT;
