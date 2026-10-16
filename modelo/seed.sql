-- =====================================================================
-- Seed de dados do ColetaFacil | MySQL 8+ | UTF-8
-- Popula o banco com os mesmos dados que hoje estao "mockados" no HTML,
-- para que as paginas fiquem identicas ao ligar o back-end.
--
-- Uso (WAMP, MySQL na porta 3306):
--   mysql -h 127.0.0.1 -P 3306 -u root coletafacil < modelo/seed.sql
--
-- Senhas de teste:
--   admin@coletafacil.com   / admin123     (ADMINISTRADOR)
--   morador@coletafacil.com / morador123   (MORADOR)
-- =====================================================================

USE coletafacil;

-- Limpa dados anteriores (ordem respeita as FKs). Idempotente para re-seed.
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE lancamento_pontos;
TRUNCATE TABLE resgate_recompensa;
TRUNCATE TABLE perfil_kids;
TRUNCATE TABLE ocorrencia_historico_status;
TRUNCATE TABLE ocorrencia_foto;
TRUNCATE TABLE ocorrencia;
TRUNCATE TABLE sugestao_ponto_descarte;
TRUNCATE TABLE ponto_horario;
TRUNCATE TABLE ponto_foto;
TRUNCATE TABLE ponto_material;
TRUNCATE TABLE ponto_descarte;
TRUNCATE TABLE coleta_material;
TRUNCATE TABLE coleta;
TRUNCATE TABLE material;
TRUNCATE TABLE recompensa;
TRUNCATE TABLE sessao_usuario;
TRUNCATE TABLE bairro;
TRUNCATE TABLE usuario;
SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
-- USUARIOS (admin + morador de teste)
-- ---------------------------------------------------------------------
INSERT INTO usuario (id_usuario, nome, email, senha_hash, telefone, tipo, ativo) VALUES
(1, 'Administrador', 'admin@coletafacil.com',   '$2y$10$qMzrIkEWwl.DqgbfNWdI7uEJnZy/wOIHgD30sSvt0ayv2ne54MZdC', NULL, 'ADMINISTRADOR', TRUE),
(2, 'Morador Teste', 'morador@coletafacil.com', '$2y$10$4PMfgpAQ/Eofwo1I2wOQD.BlIaSlhkxD9jJ1Ikw8qtJsm7KoASLmC', '(32) 9 9999-0000', 'MORADOR', TRUE);

-- ---------------------------------------------------------------------
-- BAIRROS (uniao dos bairros usados na coleta e nos pontos/admin)
-- ---------------------------------------------------------------------
INSERT INTO bairro (id_bairro, nome, ativo) VALUES
(1,  'Centro', TRUE),
(2,  'Padre Eustáquio', TRUE),
(3,  'Santa Terezinha', TRUE),
(4,  'Nossa Senhora de Fátima', TRUE),
(5,  'Laranjeiras', TRUE),
(6,  'Granjas Betânia', TRUE),
(7,  'Dom Silvério', TRUE),
(8,  'Mauá', TRUE),
(9,  'Bairro Novo', TRUE),
(10, 'Vila Nova', TRUE),
(11, 'Jardim América', TRUE),
(12, 'Bairro Escolar', TRUE);

-- ---------------------------------------------------------------------
-- MATERIAIS (guia de materiais)
-- ---------------------------------------------------------------------
INSERT INTO material (id_material, nome, descricao, aceito_coleta_seletiva, nivel_risco, instrucao_preparo, alerta_seguranca, cor_lixeira, ativo) VALUES
(1, 'Papel e Papelão', 'Jornais, revistas, caixas, cadernos, papel de escritório.', TRUE,  'BAIXO',   'Retire grampos, clipes e fitas adesivas. Mantenha seco. Dobre caixas.', NULL, 'Azul', TRUE),
(2, 'Plástico',        'Garrafas PET, embalagens de produtos de limpeza, sacolas plásticas.', TRUE, 'BAIXO', 'Lave as embalagens. Amasse garrafas PET e retire tampas.', NULL, 'Vermelho', TRUE),
(3, 'Metal',           'Latas de alumínio, latas de aço, panelas velhas.', TRUE, 'MEDIO', 'Lave as latas. Amasse para economizar espaço.', 'Cuidado com bordas cortantes — use luvas.', 'Amarelo', TRUE),
(4, 'Vidro',           'Garrafas, potes, frascos de perfume e vidros de conserva.', TRUE, 'ALTO', 'Lave os vidros. Nunca misture inteiros com quebrados.', 'Vidros quebrados: embrulhe em jornal e identifique "VIDRO QUEBRADO".', 'Verde', TRUE),
(5, 'Lixo Orgânico',   'Restos de comida, cascas de frutas, borra de café.', FALSE, 'BAIXO', 'Compostagem doméstica ou lixo comum (lixeira marrom).', NULL, 'Marrom', TRUE),
(6, 'Materiais Perfurocortantes', 'Seringas, agulhas, bisturis, lâminas de barbear.', FALSE, 'CRITICO', 'Leve a uma farmácia ou unidade de saúde.', 'NUNCA coloque na coleta seletiva. Risco grave de acidente para os coletores.', NULL, TRUE),
(7, 'Pilhas e Baterias', 'Pilhas AA, AAA, baterias de celular, bateria de carro.', FALSE, 'ALTO', 'Procure pontos de coleta em supermercados ou lojas de eletrônicos.', 'Nunca jogue no lixo comum ou coleta seletiva.', NULL, TRUE),
(8, 'Medicamentos',    'Remédios vencidos ou sobras de medicamentos.', FALSE, 'MEDIO', 'Leve a uma farmácia com programa de descarte.', 'Nunca jogue no vaso sanitário ou lixo comum.', NULL, TRUE);

-- ---------------------------------------------------------------------
-- COLETA (horarios por bairro — dados da tabela geral da pagina Coleta)
-- dia_semana: 1=domingo, 2=segunda, 3=terca, 4=quarta, 5=quinta, 6=sexta, 7=sabado
-- ---------------------------------------------------------------------
INSERT INTO coleta (id_coleta, id_bairro, dia_semana, horario_inicio, horario_fim, equipe_responsavel, ativo) VALUES
(1,  1, 3, '06:00:00', '11:00:00', 'SAAE Muriaé', TRUE),  -- Centro: Ter
(2,  1, 6, '06:00:00', '11:00:00', 'SAAE Muriaé', TRUE),  -- Centro: Sex
(3,  2, 2, '06:00:00', '11:00:00', 'SAAE Muriaé', TRUE),  -- Padre Eustáquio: Seg
(4,  2, 5, '06:00:00', '11:00:00', 'SAAE Muriaé', TRUE),  -- Padre Eustáquio: Qui
(5,  3, 4, '06:00:00', '11:00:00', 'SAAE Muriaé', TRUE),  -- Santa Terezinha: Qua
(6,  3, 7, '06:00:00', '11:00:00', 'SAAE Muriaé', TRUE),  -- Santa Terezinha: Sáb
(7,  4, 3, '13:00:00', '18:00:00', 'SAAE Muriaé', TRUE),  -- N.S. Fátima: Ter
(8,  4, 6, '13:00:00', '18:00:00', 'SAAE Muriaé', TRUE),  -- N.S. Fátima: Sex
(9,  5, 2, '06:00:00', '11:00:00', 'SAAE Muriaé', TRUE),  -- Laranjeiras: Seg
(10, 5, 5, '06:00:00', '11:00:00', 'SAAE Muriaé', TRUE),  -- Laranjeiras: Qui
(11, 6, 4, '06:00:00', '11:00:00', 'SAAE Muriaé', TRUE),  -- Granjas Betânia: Qua
(12, 7, 5, '06:00:00', '11:00:00', 'SAAE Muriaé', TRUE),  -- Dom Silvério: Qui
(13, 8, 3, '06:00:00', '11:00:00', 'SAAE Muriaé', TRUE),  -- Mauá: Ter
(14, 8, 7, '06:00:00', '11:00:00', 'SAAE Muriaé', TRUE);  -- Mauá: Sáb

-- Materiais recolhidos por coleta (conforme a tabela da pagina)
INSERT INTO coleta_material (id_coleta, id_material) VALUES
(1,1),(1,2),(1,3),(1,4), (2,1),(2,2),(2,3),(2,4),           -- Centro: Papel, Plástico, Metal, Vidro
(3,1),(3,2),(3,3), (4,1),(4,2),(4,3),                       -- Padre Eustáquio: Papel, Plástico, Metal
(5,1),(5,2),(5,4), (6,1),(6,2),(6,4),                       -- Santa Terezinha: Papel, Plástico, Vidro
(7,1),(7,2),(7,3),(7,4), (8,1),(8,2),(8,3),(8,4),           -- N.S. Fátima: todos
(9,1),(9,2), (10,1),(10,2),                                 -- Laranjeiras: Papel, Plástico
(11,1),(11,2),(11,3),                                       -- Granjas Betânia: Papel, Plástico, Metal
(12,1),(12,2),                                              -- Dom Silvério: Papel, Plástico
(13,1),(13,2),(13,3),(13,4), (14,1),(14,2),(14,3),(14,4);   -- Mauá: todos

-- ---------------------------------------------------------------------
-- PONTOS DE DESCARTE (6 pontos da pagina Pontos)
-- ---------------------------------------------------------------------
INSERT INTO ponto_descarte
  (id_ponto, id_bairro, nome, endereco, horario_funcionamento, status_funcionamento,
   telefone, email, whatsapp, acessivel_pcd, tem_estacionamento,
   avaliacao_media, avaliacao_total, icone, cor, observacao, latitude, longitude, ativo) VALUES
(1, 1,  'Ecoponto Centro',               'Rua das Flores, 123 – Centro',        'Seg–Sex 08h–17h / Sáb 08h–12h', 'ABERTO',
   '(32) 3333-1001', 'ecoponto@muriae.mg.gov.br', '5532999990001', TRUE, TRUE,  4.7, 38, '🏭', 'verde',    NULL, -21.1306000, -42.3664000, TRUE),
(2, 9,  'Farmácia Saúde Total',          'Av. Brasil, 456 – Bairro Novo',       'Seg–Sáb 07h–22h / Dom 08h–20h', 'ABERTO',
   '(32) 3333-2002', NULL,                        NULL,            TRUE, FALSE, 4.5, 24, '💊', 'azul',     NULL, -21.1358000, -42.3721000, TRUE),
(3, 10, 'Supermercado Boa Compra',       'Rua Dom Pedro, 789 – Vila Nova',      'Seg–Sáb 07h–21h / Dom 08h–14h', 'ABERTO',
   '(32) 3333-3003', NULL,                        NULL,            TRUE, TRUE,  4.2, 19, '🛒', 'laranja',  'O coletor de óleo fica na entrada lateral (portão 2).', -21.1251000, -42.3598000, TRUE),
(4, 1,  'UBS – Posto de Saúde Central',  'Rua da Saúde, 10 – Centro',           'Seg–Sex 07h–17h / Sáb 07h–12h', 'ABERTO',
   '(32) 3333-4004', NULL,                        NULL,            TRUE, FALSE, 4.8, 51, '🏥', 'vermelho', 'Seringas e perfurocortantes devem ser entregues na recepção em caixa coletora própria.', -21.1289000, -42.3627000, TRUE),
(5, 11, 'Ponto Recicle – Jardim América','Praça da Amizade – Jardim América',   'Ter, Qui, Sáb 08h–14h',         'FECHADO',
   '(32) 3333-5005', NULL,                        NULL,            FALSE, FALSE, 4.3, 16, '♻️', 'verde',    'Funciona apenas às terças, quintas e sábados. Área ao ar livre.', -21.1402000, -42.3589000, TRUE),
(6, 12, 'E.M. Prof. Souza',              'Rua da Educação, 55 – Bairro Escolar','Seg–Sex (letivo) 07h–17h',      'ABERTO',
   '(32) 3333-6006', NULL,                        NULL,            TRUE, FALSE, 4.6, 12, '🏫', 'roxo',     'Apenas em período letivo (fev–nov). Férias de julho: fechado.', -21.1223000, -42.3702000, TRUE);

-- Materiais aceitos por ponto
INSERT INTO ponto_material (id_ponto, id_material) VALUES
(1,7),(1,8),                 -- Ecoponto Centro: Pilhas e Baterias, Medicamentos
(2,8),(2,7),(2,6),           -- Farmácia: Medicamentos, Pilhas, Perfurocortantes
(3,7),(3,2),                 -- Supermercado: Pilhas, Plástico
(4,6),(4,8),                 -- UBS: Perfurocortantes, Medicamentos
(5,1),(5,2),(5,3),(5,4),     -- Ponto Recicle: Papel, Plástico, Metal, Vidro
(6,1);                       -- Escola: Papel/Papelão

-- Horarios por dia (1=dom,2=seg,3=ter,4=qua,5=qui,6=sex,7=sab)
INSERT INTO ponto_horario (id_ponto, dia_semana, horario_inicio, horario_fim, fechado) VALUES
-- Ecoponto Centro: Seg-Sex 08-17, Sab 08-12, Dom fechado
(1,2,'08:00','17:00',FALSE),(1,3,'08:00','17:00',FALSE),(1,4,'08:00','17:00',FALSE),(1,5,'08:00','17:00',FALSE),(1,6,'08:00','17:00',FALSE),(1,7,'08:00','12:00',FALSE),(1,1,NULL,NULL,TRUE),
-- Farmácia: Seg-Sab 07-22, Dom 08-20
(2,2,'07:00','22:00',FALSE),(2,3,'07:00','22:00',FALSE),(2,4,'07:00','22:00',FALSE),(2,5,'07:00','22:00',FALSE),(2,6,'07:00','22:00',FALSE),(2,7,'07:00','22:00',FALSE),(2,1,'08:00','20:00',FALSE),
-- Supermercado: Seg-Sab 07-21, Dom 08-14
(3,2,'07:00','21:00',FALSE),(3,3,'07:00','21:00',FALSE),(3,4,'07:00','21:00',FALSE),(3,5,'07:00','21:00',FALSE),(3,6,'07:00','21:00',FALSE),(3,7,'07:00','21:00',FALSE),(3,1,'08:00','14:00',FALSE),
-- UBS: Seg-Sex 07-17, Sab 07-12, Dom fechado
(4,2,'07:00','17:00',FALSE),(4,3,'07:00','17:00',FALSE),(4,4,'07:00','17:00',FALSE),(4,5,'07:00','17:00',FALSE),(4,6,'07:00','17:00',FALSE),(4,7,'07:00','12:00',FALSE),(4,1,NULL,NULL,TRUE),
-- Ponto Recicle: Ter, Qui, Sab 08-14; demais fechado
(5,3,'08:00','14:00',FALSE),(5,5,'08:00','14:00',FALSE),(5,7,'08:00','14:00',FALSE),(5,2,NULL,NULL,TRUE),(5,4,NULL,NULL,TRUE),(5,6,NULL,NULL,TRUE),(5,1,NULL,NULL,TRUE),
-- Escola: Seg-Sex 07-17; fim de semana fechado
(6,2,'07:00','17:00',FALSE),(6,3,'07:00','17:00',FALSE),(6,4,'07:00','17:00',FALSE),(6,5,'07:00','17:00',FALSE),(6,6,'07:00','17:00',FALSE),(6,7,NULL,NULL,TRUE),(6,1,NULL,NULL,TRUE);

-- Galeria de fotos dos pontos (placeholders — a tela usa fundo/emoji por enquanto)
INSERT INTO ponto_foto (id_ponto, url_arquivo, legenda, ordem) VALUES
(1, 'assets/img/pontos/ecoponto-centro.jpg', 'Fachada do Ecoponto Centro', 1),
(2, 'assets/img/pontos/farmacia-saude-total.jpg', 'Ponto de coleta na entrada', 1),
(3, 'assets/img/pontos/supermercado-boa-compra.jpg', 'Coletores na entrada do mercado', 1),
(4, 'assets/img/pontos/ubs-central.jpg', 'Coletor de perfurocortantes na recepção', 1),
(5, 'assets/img/pontos/ponto-recicle.jpg', 'Contêineres na Praça da Amizade', 1),
(6, 'assets/img/pontos/escola-souza.jpg', 'Ponto de coleta de papel na escola', 1);

-- ---------------------------------------------------------------------
-- RECOMPENSAS (pagina Recompensas)
-- ---------------------------------------------------------------------
INSERT INTO recompensa (id_recompensa, nome, descricao, parceiro, custo_pontos, disponivel, ativo) VALUES
(1, 'Café Grátis',            'Um café cortesia no parceiro.',            'Padaria Central',          200,  TRUE,  TRUE),
(2, 'Desconto no Mercado',    '5% de desconto nas compras.',              'Supermercado Boa Compra',  500,  TRUE,  TRUE),
(3, 'Kit de Mudas',           '3 mudas de plantas para o seu jardim.',    NULL,                       300,  TRUE,  TRUE),
(4, 'Certificado Digital',    'Certificado de Agente Ambiental.',         NULL,                       1000, TRUE,  TRUE),
(5, 'Desconto Bicicletaria',  '10% de desconto em serviços.',             NULL,                       800,  FALSE, TRUE),
(6, 'Kit Escolar Eco',        'Material escolar sustentável.',            NULL,                       600,  FALSE, TRUE);

-- ---------------------------------------------------------------------
-- OCORRENCIAS (lista do painel admin / exemplos)
-- tipos conforme ENUM; bairros mapeados aos ids acima
-- ---------------------------------------------------------------------
INSERT INTO ocorrencia (id_ocorrencia, protocolo, id_usuario, id_bairro, tipo, urgencia, endereco_descricao, descricao, anonima, visivel_publicamente, status, criada_em) VALUES
(1, '2026001', NULL, 1,  'DESCARTE_IRREGULAR',      'ALTA',  'Rua das Palmeiras, 50',        'Descarte irregular de entulho na calçada.',        TRUE,  TRUE, 'EM_ANDAMENTO', '2026-08-20 09:00:00'),
(2, '2026002', NULL, 9,  'COLETA_NAO_REALIZADA',    'MEDIA', 'Av. Brasil, 200',              'Coleta seletiva não passou no dia previsto.',      TRUE,  TRUE, 'RESOLVIDA',    '2026-08-18 10:30:00'),
(3, '2026003', NULL, 10, 'DESCARTE_IRREGULAR',      'MEDIA', 'Rua do Comércio, 15',          'Lixo acumulado em terreno baldio.',                TRUE,  TRUE, 'NOVA',         '2026-08-21 08:15:00'),
(4, '2026004', NULL, 11, 'MATERIAL_NAO_RECOLHIDO',  'BAIXA', 'Praça da Amizade',             'Material reciclável deixado e não recolhido.',     TRUE,  TRUE, 'EM_ANALISE',   '2026-08-21 14:45:00'),
(5, '2026005', NULL, 1,  'PONTO_INADEQUADO',        'BAIXA', 'Rua da Saúde, 10',             'Ponto de descarte em local inadequado.',           TRUE,  TRUE, 'RESOLVIDA',    '2026-08-17 16:20:00');
