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
TRUNCATE TABLE material_exemplo;
TRUNCATE TABLE material_instrucao;
TRUNCATE TABLE material;
TRUNCATE TABLE recompensa;
TRUNCATE TABLE sessao_usuario;
TRUNCATE TABLE bairro;
TRUNCATE TABLE usuario;
SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
-- USUARIOS (admin + morador de teste)
-- ---------------------------------------------------------------------
-- Senha de todos os moradores de exemplo: morador123
INSERT INTO usuario (id_usuario, nome, email, senha_hash, telefone, tipo, ativo) VALUES
(1, 'Administrador', 'admin@coletafacil.com',   '$2y$10$qMzrIkEWwl.DqgbfNWdI7uEJnZy/wOIHgD30sSvt0ayv2ne54MZdC', NULL, 'ADMINISTRADOR', TRUE),
(2, 'Morador Teste', 'morador@coletafacil.com', '$2y$10$4PMfgpAQ/Eofwo1I2wOQD.BlIaSlhkxD9jJ1Ikw8qtJsm7KoASLmC', '(32) 9 9999-0000', 'MORADOR', TRUE),
(3, 'Maria Silva',   'maria@coletafacil.com',   '$2y$10$4PMfgpAQ/Eofwo1I2wOQD.BlIaSlhkxD9jJ1Ikw8qtJsm7KoASLmC', NULL, 'MORADOR', TRUE),
(4, 'João Ferreira', 'joao@coletafacil.com',    '$2y$10$4PMfgpAQ/Eofwo1I2wOQD.BlIaSlhkxD9jJ1Ikw8qtJsm7KoASLmC', NULL, 'MORADOR', TRUE),
(5, 'Ana Costa',     'ana@coletafacil.com',     '$2y$10$4PMfgpAQ/Eofwo1I2wOQD.BlIaSlhkxD9jJ1Ikw8qtJsm7KoASLmC', NULL, 'MORADOR', TRUE),
(6, 'Carlos Moura',  'carlos@coletafacil.com',  '$2y$10$4PMfgpAQ/Eofwo1I2wOQD.BlIaSlhkxD9jJ1Ikw8qtJsm7KoASLmC', NULL, 'MORADOR', TRUE),
(7, 'Paula Rocha',   'paula@coletafacil.com',   '$2y$10$4PMfgpAQ/Eofwo1I2wOQD.BlIaSlhkxD9jJ1Ikw8qtJsm7KoASLmC', NULL, 'MORADOR', TRUE);

-- ---------------------------------------------------------------------
-- BAIRROS — lista oficial de Muriaé-MG.
-- Os ids 1-12 são mantidos porque coletas, pontos e ocorrências de exemplo
-- referenciam esses ids. Os bairros 2, 4-8, 11 e 12 ficam INATIVOS: não
-- fazem parte da lista oficial, mas preservam o histórico de exemplo.
-- ---------------------------------------------------------------------
INSERT INTO bairro (id_bairro, nome, ativo) VALUES
(1,  'Centro', TRUE),
(2,  'Padre Eustáquio', FALSE),
(3,  'Santa Terezinha', TRUE),
(4,  'Nossa Senhora de Fátima', FALSE),
(5,  'Laranjeiras', FALSE),
(6,  'Granjas Betânia', FALSE),
(7,  'Dom Silvério', FALSE),
(8,  'Mauá', FALSE),
(9,  'Dornelas', TRUE),
(10, 'Porto', TRUE),
(11, 'José Cirilo', FALSE),
(12, 'Inconfidência', FALSE),
(13, 'Santa Laura', TRUE),
(14, 'Villa Real', TRUE),
(15, 'Belisário', TRUE),
(16, 'Gaspar', TRUE),
(17, 'Gávea', TRUE),
(18, 'Prefeito Hélio Araújo', TRUE),
(19, 'Santana III', TRUE),
(20, 'Barra', TRUE),
(21, 'Planalto', TRUE),
(22, 'São Vicente de Paulo', TRUE),
(23, 'Joanópolis', TRUE),
(24, 'Área Rural de Muriaé', TRUE),
(25, 'Alto do Castelo', TRUE),
(26, 'Quinta das Flores', TRUE),
(27, 'São Joaquim', TRUE),
(28, 'Chácara Doutor Brum', TRUE),
(29, 'João VI', TRUE),
(30, 'Colety', TRUE),
(31, 'Santo Antônio', TRUE),
(32, 'São Cristóvão', TRUE),
(33, 'Vermelho', TRUE),
(34, 'Cerâmica', TRUE),
(35, 'União', TRUE),
(36, 'Vale do Castelo', TRUE),
(37, 'Zona Rural', TRUE),
(38, 'João XXIII', TRUE),
(39, 'Chalé', TRUE),
(40, 'São Francisco', TRUE),
(41, 'Padre Tiago', TRUE),
(42, 'Sofoco', TRUE),
(43, 'Napoleão', TRUE),
(44, 'Franco Suíço', TRUE),
(45, 'Porto Belo', TRUE),
(46, 'Safira', TRUE),
(47, 'Distrito Industrial', TRUE),
(48, 'São Gotardo', TRUE),
(49, 'Primavera', TRUE),
(50, 'Chácara da Gávea', TRUE),
(51, 'Recreio', TRUE),
(52, 'Boa Família', TRUE),
(53, 'Alterosa', TRUE),
(54, 'Pirapanema', TRUE),
(55, 'Santana', TRUE),
(56, 'Vila Conceição', TRUE),
(57, 'Aeroporto', TRUE),
(58, 'Santa Helena', TRUE),
(59, 'Boa Vista', TRUE),
(60, 'Recanto Verde', TRUE),
(61, 'Cardoso de Melo', TRUE),
(62, 'São Pedro', TRUE);

-- ---------------------------------------------------------------------
-- MATERIAIS (guia de materiais)
-- ---------------------------------------------------------------------
INSERT INTO material
  (id_material, nome, descricao, aceito_coleta_seletiva, status_coleta, icone, cor_card, preparo_label,
   nivel_risco, instrucao_preparo, alerta_seguranca, cor_lixeira, ativo) VALUES
(1, 'Papel e Papelão', 'Jornais, revistas, caixas, cadernos, papel de escritório.', TRUE,  'ACEITO',     '📄', 'azul',     'Como preparar',  'BAIXO',   'Retire grampos, clipes e fitas adesivas. Mantenha seco. Dobre caixas.', NULL, 'Azul', TRUE),
(2, 'Plástico',        'Garrafas PET, embalagens de produtos de limpeza, sacolas plásticas.', TRUE, 'ACEITO',  '🧴', 'vermelho', 'Como preparar',  'BAIXO',   'Lave as embalagens. Amasse garrafas PET e retire tampas.', NULL, 'Vermelho', TRUE),
(3, 'Metal',           'Latas de alumínio, latas de aço, panelas velhas (sem cabo de madeira).', TRUE, 'ACEITO', '🥫', 'amarelo', 'Como preparar',  'MEDIO',   'Lave as latas. Amasse para economizar espaço.', 'Cuidado com bordas cortantes — use luvas.', 'Amarelo', TRUE),
(4, 'Vidro',           'Garrafas, potes, frascos de perfume e vidros de conserva.', TRUE, 'CUIDADO',       '🍶', 'verde',    'Como preparar',  'ALTO',    'Lave os vidros. Nunca misture inteiros com quebrados.', 'Vidros quebrados: embrulhe em jornal e identifique "VIDRO QUEBRADO".', 'Verde', TRUE),
(5, 'Lixo Orgânico',   'Restos de comida, cascas de frutas, borra de café, alimentos em geral.', FALSE, 'NAO_ACEITO', '🍌', 'cinza', 'Alternativas',   'BAIXO',   'Compostagem doméstica ou lixo comum (lixeira marrom).', NULL, 'Marrom', TRUE),
(6, 'Materiais Perfurocortantes', 'Seringas, agulhas, bisturis, lâminas de barbear.', FALSE, 'NAO_ACEITO', '💉', 'preto',  'Como descartar', 'CRITICO', 'Leve a uma farmácia ou unidade de saúde.', 'NUNCA coloque na coleta seletiva. Risco grave de acidente para os coletores.', NULL, TRUE),
(7, 'Pilhas e Baterias', 'Pilhas AA, AAA, baterias de celular, bateria de carro.', FALSE, 'NAO_ACEITO',    '🔋', 'laranja',  'Como descartar', 'ALTO',    'Procure pontos de coleta em supermercados ou lojas de eletrônicos.', 'Nunca jogue no lixo comum ou coleta seletiva.', NULL, TRUE),
(8, 'Medicamentos',    'Remédios vencidos ou sobras de medicamentos.', FALSE, 'NAO_ACEITO',             '💊', 'marrom',   'Como descartar', 'MEDIO',   'Leve a uma farmácia com programa de descarte.', 'Nunca jogue no vaso sanitário ou lixo comum.', NULL, TRUE);

-- Instrucoes de preparo por material (ordem + flag de perigo)
INSERT INTO material_instrucao (id_material, ordem, texto, perigo) VALUES
-- Papel
(1,1,'Retire grampos, clipes e fitas adesivas',FALSE),(1,2,'Mantenha seco — papel molhado não é reciclável',FALSE),(1,3,'Dobre caixas para economizar espaço',FALSE),
-- Plástico
(2,1,'Lave as embalagens com água',FALSE),(2,2,'Amasse garrafas PET e retire tampas',FALSE),(2,3,'Sacolas podem ser agrupadas em uma só',FALSE),
-- Metal
(3,1,'Lave as latas antes de descartar',FALSE),(3,2,'Amasse para economizar espaço',FALSE),(3,3,'⚠️ Cuidado com bordas cortantes — use luvas',TRUE),
-- Vidro
(4,1,'Lave os vidros antes do descarte',FALSE),(4,2,'Nunca misture vidros inteiros com quebrados',FALSE),(4,3,'⚠️ Vidros quebrados: embrulhe em papel jornal e identifique "VIDRO QUEBRADO"',TRUE),
-- Organico
(5,1,'Compostagem doméstica',FALSE),(5,2,'Descarte no lixo comum (lixeira marrom)',FALSE),
-- Perfurocortantes
(6,1,'🔴 NUNCA coloque na coleta seletiva',TRUE),(6,2,'Leve a uma farmácia ou unidade de saúde',FALSE),(6,3,'Risco grave de acidente para os coletores',TRUE),
-- Pilhas
(7,1,'Procure pontos de coleta específicos em supermercados',FALSE),(7,2,'Lojas de eletrônicos geralmente aceitam',FALSE),(7,3,'Nunca jogue no lixo comum ou coleta seletiva',FALSE),
-- Medicamentos
(8,1,'Leve a uma farmácia com programa de descarte',FALSE),(8,2,'Nunca jogue no vaso sanitário ou lixo comum',FALSE);

-- Exemplos (tags) por material; negativo=TRUE usa o estilo vermelho
INSERT INTO material_exemplo (id_material, ordem, icone, rotulo, negativo) VALUES
(1,1,'📰','Jornal',FALSE),(1,2,'📦','Caixa',FALSE),(1,3,'📚','Revista',FALSE),
(2,1,'🧴','Frasco',FALSE),(2,2,'🍼','PET',FALSE),(2,3,'🛍️','Sacola',FALSE),
(3,1,'🥤','Lata',FALSE),(3,2,'🥫','Conserva',FALSE),(3,3,'🔩','Metal',FALSE),
(4,1,'🍷','Garrafa',FALSE),(4,2,'🫙','Pote',FALSE),(4,3,'🧪','Frasco',FALSE),
(5,1,'🍎','Casca',TRUE),(5,2,'🍗','Restos',TRUE);

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
(1, 1,  'Ecoponto Centro',               'Rua Coronel Amador Pinheiro de Barros, 123 – Centro',        'Seg–Sex 08h–17h / Sáb 08h–12h', 'ABERTO',
   '(32) 3333-1001', 'ecoponto@muriae.mg.gov.br', '5532999990001', TRUE, TRUE,  4.7, 38, '🏭', 'verde',    NULL, -21.1306000, -42.3664000, TRUE),
(2, 9,  'Farmácia Saúde Total',          'Rua João Dornelas, 456 – Dornelas',       'Seg–Sáb 07h–22h / Dom 08h–20h', 'ABERTO',
   '(32) 3333-2002', NULL,                        NULL,            TRUE, FALSE, 4.5, 24, '💊', 'azul',     NULL, -21.1358000, -42.3721000, TRUE),
(3, 10, 'Supermercado Boa Compra',       'Rua Coronel Pereira Sobrinho, 789 – Porto',      'Seg–Sáb 07h–21h / Dom 08h–14h', 'ABERTO',
   '(32) 3333-3003', NULL,                        NULL,            TRUE, TRUE,  4.2, 19, '🛒', 'laranja',  'O coletor de óleo fica na entrada lateral (portão 2).', -21.1251000, -42.3598000, TRUE),
(4, 1,  'UBS – Posto de Saúde Central',  'Rua da Saúde, 10 – Centro',           'Seg–Sex 07h–17h / Sáb 07h–12h', 'ABERTO',
   '(32) 3333-4004', NULL,                        NULL,            TRUE, FALSE, 4.8, 51, '🏥', 'vermelho', 'Seringas e perfurocortantes devem ser entregues na recepção em caixa coletora própria.', -21.1289000, -42.3627000, TRUE),
(5, 62, 'Ponto de Entrega São Pedro',   'Bairro São Pedro – Muriaé',   'Ter, Qui, Sáb 08h–14h',         'FECHADO',
   '(32) 3333-5005', NULL,                        NULL,            FALSE, FALSE, 4.3, 16, '♻️', 'verde',    'Funciona apenas às terças, quintas e sábados. Área ao ar livre.', -21.1402000, -42.3589000, TRUE),
(6, 20, 'Ponto de Entrega Barra',        'Bairro Barra – Muriaé','Seg–Sex 07h–17h',              'ABERTO',
   '(32) 3333-6006', NULL,                        NULL,            TRUE, FALSE, NULL, 0, '♻️', 'roxo',     NULL, -21.1223000, -42.3702000, TRUE);

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
(5, 'assets/img/pontos/ponto-recicle.jpg', 'Contêineres na Rua São Pedro', 1),
(6, 'assets/img/pontos/escola-souza.jpg', 'Ponto de entrega de papel no bairro Barra', 1);

-- ---------------------------------------------------------------------
-- RECOMPENSAS (pagina Recompensas)
-- ---------------------------------------------------------------------
-- Itens úteis para separar, reaproveitar e reduzir o lixo (sem parceiros fictícios).
-- estoque NULL = ilimitado (itens digitais ou ainda "em breve").
INSERT INTO recompensa (id_recompensa, nome, descricao, parceiro, custo_pontos, disponivel, estoque, ativo) VALUES
(1, 'Certificado digital "Morador Consciente"', 'Certificado em PDF com seu nome e o total de ocorrências confirmadas em Muriaé. Serve como comprovante de participação cívica.', NULL, 150,  TRUE,  NULL, TRUE),
(2, 'Kit de sacos coloridos para separação',    'Sacos nas cores da coleta seletiva (azul, vermelho, verde e amarelo) para separar os recicláveis em casa.',                      NULL, 250,  TRUE,  40,   TRUE),
(3, 'Muda de árvore nativa da Mata Atlântica',  'Muda de espécie nativa da região, entregue em ações de plantio do programa.',                                                     NULL, 300,  FALSE, NULL, TRUE),
(4, 'Ecobag reutilizável',                      'Sacola de algodão para trocar as sacolas plásticas nas compras do dia a dia.',                                                    NULL, 400,  TRUE,  30,   TRUE),
(5, 'Vaga em oficina de compostagem',           'Inscrição em oficina prática sobre compostagem doméstica e aproveitamento de resíduos orgânicos.',                                NULL, 500,  FALSE, NULL, TRUE),
(6, 'Garrafa reutilizável',                     'Garrafa de 500 ml em alumínio para reduzir o uso de copos e garrafas descartáveis.',                                              NULL, 600,  TRUE,  20,   TRUE),
(7, 'Desconto em comércio parceiro de Muriaé',  'Cupom de desconto em estabelecimentos locais que aderirem ao programa. Parceiros em definição.',                                  NULL, 800,  FALSE, NULL, TRUE),
(8, 'Kit compostagem doméstica',                'Composteira doméstica com manual de uso para transformar restos de alimentos em adubo.',                                          NULL, 1200, FALSE, NULL, TRUE);

-- ---------------------------------------------------------------------
-- OCORRENCIAS (lista do painel admin / exemplos)
-- tipos conforme ENUM; bairros mapeados aos ids acima
-- ---------------------------------------------------------------------
INSERT INTO ocorrencia (id_ocorrencia, protocolo, id_usuario, id_bairro, tipo, urgencia, endereco_descricao, descricao, anonima, visivel_publicamente, status, criada_em) VALUES
(1, '2026001', NULL, 1,  'DESCARTE_IRREGULAR',      'ALTA',  'Rua Doutor Alves Pequeno, 50',        'Descarte irregular de entulho na calçada.',        TRUE,  TRUE, 'EM_ANDAMENTO', '2026-08-20 09:00:00'),
(2, '2026002', NULL, 9,  'COLETA_NAO_REALIZADA',    'MEDIA', 'Rua João Dornelas, 200',              'Coleta seletiva não passou no dia previsto.',      TRUE,  TRUE, 'RESOLVIDA',    '2026-08-18 10:30:00'),
(3, '2026003', NULL, 10, 'DESCARTE_IRREGULAR',      'MEDIA', 'Rua Barão do Monte Alto, 15',          'Lixo acumulado em terreno baldio.',                TRUE,  TRUE, 'NOVA',         '2026-08-21 08:15:00'),
(4, '2026004', NULL, 11, 'MATERIAL_NAO_RECOLHIDO',  'BAIXA', 'Rua São Pedro',             'Material reciclável deixado e não recolhido.',     TRUE,  TRUE, 'EM_ANALISE',   '2026-08-21 14:45:00'),
(5, '2026005', NULL, 1,  'PONTO_INADEQUADO',        'BAIXA', 'Rua da Saúde, 10',             'Ponto de descarte em local inadequado.',           TRUE,  TRUE, 'RESOLVIDA',    '2026-08-17 16:20:00'),
(6, '2026006', 2,    10, 'ENTULHO',                 'ALTA',  'Rua Coronel Pereira Sobrinho, 400',           'Entulho de obra descartado na esquina há dias.',   FALSE, TRUE, 'NOVA',         '2026-08-22 07:40:00'),
(7, '2026007', NULL, 8,  'LIXO_TOXICO',             'ALTA',  'Rua Barão do Monte Alto, 88 – Mauá',   'Latas de tinta e solvente abandonadas na via.',    TRUE,  TRUE, 'EM_ANALISE',   '2026-08-22 11:10:00'),
(8, '2026008', 2,    1,  'DESCARTE_IRREGULAR',      'MEDIA', 'Praça Central',                'Sacos de lixo fora do horário da coleta.',         FALSE, TRUE, 'EM_ANDAMENTO', '2026-08-19 18:05:00'),
(9, '2026009', NULL, 11, 'COLETA_NAO_REALIZADA',    'MEDIA', 'Rua das Acácias, 30',          'Coleta atrasada há dois dias no bairro.',          TRUE,  TRUE, 'RESOLVIDA',    '2026-08-15 09:25:00'),
(10,'2026010', 2,    12, 'OUTROS',                  'BAIXA', 'Av. da Educação, 120',         'Lixeira pública danificada e transbordando.',      FALSE, TRUE, 'NOVA',         '2026-08-23 08:00:00');

-- Observacao do admin + responsavel em ocorrencias ja tratadas (para o painel)
UPDATE ocorrencia SET id_administrador_responsavel = 1,
  observacao_admin = 'Equipe acionada; coleta extra agendada.',
  resolvida_em = '2026-08-18 15:00:00'
  WHERE protocolo = '2026002';
UPDATE ocorrencia SET id_administrador_responsavel = 1,
  observacao_admin = 'Local vistoriado e regularizado.',
  resolvida_em = '2026-08-17 17:30:00'
  WHERE protocolo = '2026005';
UPDATE ocorrencia SET id_administrador_responsavel = 1,
  observacao_admin = 'Coleta normalizada após contato com a equipe.',
  resolvida_em = '2026-08-16 10:00:00'
  WHERE protocolo = '2026009';

-- ---------------------------------------------------------------------
-- HISTORICO DE STATUS (timeline real da consulta por protocolo)
-- Reconstroi a trajetoria de cada ocorrencia ate o status atual.
-- ---------------------------------------------------------------------
INSERT INTO ocorrencia_historico_status (id_ocorrencia, status, observacao, id_usuario_responsavel, criada_em) VALUES
-- 1 (EM_ANDAMENTO)
(1, 'NOVA',          'Ocorrência registrada',                         NULL, '2026-08-20 09:00:00'),
(1, 'EM_ANALISE',    'Encaminhada para triagem.',                     1,    '2026-08-20 11:00:00'),
(1, 'EM_ANDAMENTO',  'Equipe de limpeza acionada.',                   1,    '2026-08-21 08:00:00'),
-- 2 (RESOLVIDA)
(2, 'NOVA',          'Ocorrência registrada',                         NULL, '2026-08-18 10:30:00'),
(2, 'EM_ANALISE',    'Verificação do calendário de coleta.',          1,    '2026-08-18 12:00:00'),
(2, 'RESOLVIDA',     'Equipe acionada; coleta extra agendada.',       1,    '2026-08-18 15:00:00'),
-- 3 (NOVA)
(3, 'NOVA',          'Ocorrência registrada',                         NULL, '2026-08-21 08:15:00'),
-- 4 (EM_ANALISE)
(4, 'NOVA',          'Ocorrência registrada',                         NULL, '2026-08-21 14:45:00'),
(4, 'EM_ANALISE',    'Em avaliação pela equipe.',                     1,    '2026-08-21 16:00:00'),
-- 5 (RESOLVIDA)
(5, 'NOVA',          'Ocorrência registrada',                         NULL, '2026-08-17 16:20:00'),
(5, 'EM_ANALISE',    'Vistoria agendada.',                            1,    '2026-08-17 16:45:00'),
(5, 'RESOLVIDA',     'Local vistoriado e regularizado.',              1,    '2026-08-17 17:30:00'),
-- 6 (NOVA)
(6, 'NOVA',          'Ocorrência registrada',                         2,    '2026-08-22 07:40:00'),
-- 7 (EM_ANALISE)
(7, 'NOVA',          'Ocorrência registrada',                         NULL, '2026-08-22 11:10:00'),
(7, 'EM_ANALISE',    'Material tóxico — análise prioritária.',        1,    '2026-08-22 12:00:00'),
-- 8 (EM_ANDAMENTO)
(8, 'NOVA',          'Ocorrência registrada',                         2,    '2026-08-19 18:05:00'),
(8, 'EM_ANALISE',    'Triagem concluída.',                            1,    '2026-08-19 19:00:00'),
(8, 'EM_ANDAMENTO',  'Coleta de reforço em andamento.',               1,    '2026-08-20 08:00:00'),
-- 9 (RESOLVIDA)
(9, 'NOVA',          'Ocorrência registrada',                         NULL, '2026-08-15 09:25:00'),
(9, 'EM_ANALISE',    'Contato com a equipe de coleta.',               1,    '2026-08-15 10:30:00'),
(9, 'RESOLVIDA',     'Coleta normalizada após contato com a equipe.', 1,    '2026-08-16 10:00:00'),
-- 10 (NOVA)
(10,'NOVA',          'Ocorrência registrada',                         2,    '2026-08-23 08:00:00');

-- ---------------------------------------------------------------------
-- LANCAMENTOS DE PONTOS (livro-razao; saldo = SUM(quantidade))
-- Alimenta o ranking e o "meus pontos" da pagina de recompensas.
-- ---------------------------------------------------------------------
-- Regra (PontosModel::VALORES): registro 10 | confirmada/resolvida 40 | foto 15
-- (a foto só pontua na confirmação). Só há pontos com origem real.
INSERT INTO lancamento_pontos (id_usuario, id_ocorrencia, tipo, quantidade, descricao, criada_em) VALUES
-- Morador Teste (id 2): registrou as ocorrencias 6, 8, 10 (ainda não resolvidas)
(2, 6,  'OCORRENCIA_REGISTRADA', 10, 'Ocorrência 2026006', '2026-08-22 07:40:00'),
(2, 8,  'OCORRENCIA_REGISTRADA', 10, 'Ocorrência 2026008', '2026-08-19 18:05:00'),
(2, 10, 'OCORRENCIA_REGISTRADA', 10, 'Ocorrência 2026010', '2026-08-23 08:00:00');

-- ---------------------------------------------------------------------
-- PERFIL KIDS (exemplo; um usuario pode ter varios filhos)
-- ---------------------------------------------------------------------
INSERT INTO perfil_kids (id_usuario, apelido, pontos, jogos_concluidos) VALUES
(2, 'Joãozinho', 120, 3),
(2, 'Mariazinha', 80, 2);
