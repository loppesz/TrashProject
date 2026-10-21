-- =====================================================================
-- Recompensas realistas + pontuação justa.
--
-- 1) Desativa (ativo = 0) o catálogo antigo, com parceiros fictícios.
--    NÃO apaga: resgates antigos continuam ligados à recompensa original.
-- 2) Cadastra o catálogo novo (itens úteis para reciclar e reduzir lixo).
--    estoque NULL = ilimitado (itens digitais / ainda em breve).
-- 3) Recalcula os saldos pela regra nova, SEM apagar lançamentos: grava
--    lançamentos de ajuste (mesmo tipo e ocorrência) com a diferença.
--    Regra nova: registro 10 | confirmada 40 | foto 15 (só se confirmada).
--    Pontos sem origem comprovada (ajustes fictícios, sequência de acesso
--    não implementada) são estornados.
--    Para desfazer o passo 3:
--      DELETE FROM lancamento_pontos WHERE descricao = 'Ajuste: nova regra de pontos';
--
-- Idempotente: pode ser executada mais de uma vez.
-- =====================================================================
START TRANSACTION;

-- 1) Catálogo antigo fora do ar
UPDATE recompensa SET ativo = 0
 WHERE nome IN ('Café Grátis', 'Desconto no Mercado', 'Kit de Mudas',
                'Certificado Digital', 'Desconto Bicicletaria', 'Kit Escolar Eco');

-- 2) Catálogo novo (insere só o que ainda não existe)
INSERT INTO recompensa (nome, descricao, parceiro, custo_pontos, disponivel, estoque, ativo)
SELECT 'Certificado digital "Morador Consciente"',
       'Certificado em PDF com seu nome e o total de ocorrências confirmadas em Muriaé. Serve como comprovante de participação cívica.',
       NULL, 150, TRUE, NULL, TRUE FROM DUAL
 WHERE NOT EXISTS (SELECT 1 FROM recompensa WHERE nome = 'Certificado digital "Morador Consciente"');

INSERT INTO recompensa (nome, descricao, parceiro, custo_pontos, disponivel, estoque, ativo)
SELECT 'Kit de sacos coloridos para separação',
       'Sacos nas cores da coleta seletiva (azul, vermelho, verde e amarelo) para separar os recicláveis em casa.',
       NULL, 250, TRUE, 40, TRUE FROM DUAL
 WHERE NOT EXISTS (SELECT 1 FROM recompensa WHERE nome = 'Kit de sacos coloridos para separação');

INSERT INTO recompensa (nome, descricao, parceiro, custo_pontos, disponivel, estoque, ativo)
SELECT 'Muda de árvore nativa da Mata Atlântica',
       'Muda de espécie nativa da região, entregue em ações de plantio do programa.',
       NULL, 300, FALSE, NULL, TRUE FROM DUAL
 WHERE NOT EXISTS (SELECT 1 FROM recompensa WHERE nome = 'Muda de árvore nativa da Mata Atlântica');

INSERT INTO recompensa (nome, descricao, parceiro, custo_pontos, disponivel, estoque, ativo)
SELECT 'Ecobag reutilizável',
       'Sacola de algodão para trocar as sacolas plásticas nas compras do dia a dia.',
       NULL, 400, TRUE, 30, TRUE FROM DUAL
 WHERE NOT EXISTS (SELECT 1 FROM recompensa WHERE nome = 'Ecobag reutilizável');

INSERT INTO recompensa (nome, descricao, parceiro, custo_pontos, disponivel, estoque, ativo)
SELECT 'Vaga em oficina de compostagem',
       'Inscrição em oficina prática sobre compostagem doméstica e aproveitamento de resíduos orgânicos.',
       NULL, 500, FALSE, NULL, TRUE FROM DUAL
 WHERE NOT EXISTS (SELECT 1 FROM recompensa WHERE nome = 'Vaga em oficina de compostagem');

INSERT INTO recompensa (nome, descricao, parceiro, custo_pontos, disponivel, estoque, ativo)
SELECT 'Garrafa reutilizável',
       'Garrafa de 500 ml em alumínio para reduzir o uso de copos e garrafas descartáveis.',
       NULL, 600, TRUE, 20, TRUE FROM DUAL
 WHERE NOT EXISTS (SELECT 1 FROM recompensa WHERE nome = 'Garrafa reutilizável');

INSERT INTO recompensa (nome, descricao, parceiro, custo_pontos, disponivel, estoque, ativo)
SELECT 'Desconto em comércio parceiro de Muriaé',
       'Cupom de desconto em estabelecimentos locais que aderirem ao programa. Parceiros em definição.',
       NULL, 800, FALSE, NULL, TRUE FROM DUAL
 WHERE NOT EXISTS (SELECT 1 FROM recompensa WHERE nome = 'Desconto em comércio parceiro de Muriaé');

INSERT INTO recompensa (nome, descricao, parceiro, custo_pontos, disponivel, estoque, ativo)
SELECT 'Kit compostagem doméstica',
       'Composteira doméstica com manual de uso para transformar restos de alimentos em adubo.',
       NULL, 1200, FALSE, NULL, TRUE FROM DUAL
 WHERE NOT EXISTS (SELECT 1 FROM recompensa WHERE nome = 'Kit compostagem doméstica');

-- 3) Recalculo justo: para cada (usuário, tipo, ocorrência), grava a diferença
--    entre o valor pela regra nova (alvo) e o saldo líquido atual.
INSERT INTO lancamento_pontos (id_usuario, id_ocorrencia, tipo, quantidade, descricao)
SELECT g.id_usuario, g.id_ocorrencia, g.tipo, g.alvo - g.atual, 'Ajuste: nova regra de pontos'
  FROM (
        SELECT lp.id_usuario, lp.id_ocorrencia, lp.tipo,
               SUM(lp.quantidade) AS atual,
               CASE
                 WHEN lp.tipo = 'OCORRENCIA_REGISTRADA' AND lp.id_ocorrencia IS NOT NULL THEN 10
                 WHEN lp.tipo = 'OCORRENCIA_RESOLVIDA'  AND lp.id_ocorrencia IS NOT NULL THEN 40
                 WHEN lp.tipo = 'FOTO_APROVADA' AND o.status = 'RESOLVIDA' THEN 15
                 ELSE 0
               END AS alvo
          FROM lancamento_pontos lp
          LEFT JOIN ocorrencia o ON o.id_ocorrencia = lp.id_ocorrencia
         WHERE lp.tipo IN ('OCORRENCIA_REGISTRADA', 'FOTO_APROVADA', 'OCORRENCIA_RESOLVIDA',
                           'SEQUENCIA_ACESSO', 'INDICACAO', 'QUIZ_KIDS')
            OR (lp.tipo = 'AJUSTE' AND lp.descricao IN ('Pontuação acumulada', 'Ajuste: nova regra de pontos'))
         GROUP BY lp.id_usuario, lp.id_ocorrencia, lp.tipo, o.status
       ) g
 WHERE g.alvo <> g.atual;

COMMIT;
