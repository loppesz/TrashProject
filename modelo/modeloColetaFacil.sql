-- Modelo físico do ColetaFácil | MySQL 8.0+ | UTF-8
CREATE DATABASE IF NOT EXISTS coletafacil CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE coletafacil;

-- Login único para moradores e administradores. Armazene apenas password_hash() em senha_hash.
CREATE TABLE usuario (
  id_usuario INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  senha_hash VARCHAR(255) NOT NULL,
  telefone VARCHAR(20) NULL,
  tipo ENUM('MORADOR','ADMINISTRADOR') NOT NULL DEFAULT 'MORADOR',
  ativo BOOLEAN NOT NULL DEFAULT TRUE,
  email_verificado_em DATETIME NULL,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Guarde o hash SHA-256 do token de sessão, nunca o token original.
CREATE TABLE sessao_usuario (
  id_sessao BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT UNSIGNED NOT NULL,
  token_hash CHAR(64) NOT NULL UNIQUE,
  expira_em DATETIME NOT NULL,
  criada_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  encerrada_em DATETIME NULL,
  ip_origem VARCHAR(45) NULL,
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
  INDEX idx_sessao_usuario_expira (id_usuario, expira_em)
) ENGINE=InnoDB;

CREATE TABLE bairro (
  id_bairro INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL UNIQUE,
  ativo BOOLEAN NOT NULL DEFAULT TRUE
) ENGINE=InnoDB;

-- Um registro por dia/horário de coleta do bairro. 1=domingo, ... 7=sabado.
CREATE TABLE coleta (
  id_coleta INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_bairro INT UNSIGNED NOT NULL,
  dia_semana TINYINT UNSIGNED NOT NULL,
  horario_inicio TIME NOT NULL,
  horario_fim TIME NOT NULL,
  equipe_responsavel VARCHAR(150) NULL,
  telefone_contato VARCHAR(20) NULL,
  observacao VARCHAR(500) NULL,
  ativo BOOLEAN NOT NULL DEFAULT TRUE,
  FOREIGN KEY (id_bairro) REFERENCES bairro(id_bairro),
  CONSTRAINT ck_coleta_dia CHECK (dia_semana BETWEEN 1 AND 7),
  CONSTRAINT ck_coleta_horario CHECK (horario_fim > horario_inicio),
  UNIQUE KEY uk_coleta_bairro_dia_hora (id_bairro,dia_semana,horario_inicio),
  INDEX idx_coleta_consulta (id_bairro,ativo,dia_semana)
) ENGINE=InnoDB;

CREATE TABLE material (
  id_material INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL UNIQUE,
  descricao TEXT NOT NULL,
  aceito_coleta_seletiva BOOLEAN NOT NULL DEFAULT FALSE,
  nivel_risco ENUM('BAIXO','MEDIO','ALTO','CRITICO') NOT NULL DEFAULT 'BAIXO',
  instrucao_preparo TEXT NULL,
  alerta_seguranca TEXT NULL,
  orientacao_descarte TEXT NULL,
  cor_lixeira VARCHAR(30) NULL,
  ativo BOOLEAN NOT NULL DEFAULT TRUE
) ENGINE=InnoDB;

CREATE TABLE coleta_material (
  id_coleta INT UNSIGNED NOT NULL,
  id_material INT UNSIGNED NOT NULL,
  PRIMARY KEY (id_coleta,id_material),
  FOREIGN KEY (id_coleta) REFERENCES coleta(id_coleta) ON DELETE CASCADE,
  FOREIGN KEY (id_material) REFERENCES material(id_material)
) ENGINE=InnoDB;

CREATE TABLE ponto_descarte (
  id_ponto INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_bairro INT UNSIGNED NOT NULL,
  nome VARCHAR(150) NOT NULL,
  endereco VARCHAR(200) NOT NULL,
  horario_funcionamento VARCHAR(150) NULL,
  status_funcionamento ENUM('ABERTO','FECHADO') NOT NULL DEFAULT 'ABERTO',
  latitude DECIMAL(10,7) NULL,
  longitude DECIMAL(10,7) NULL,
  ativo BOOLEAN NOT NULL DEFAULT TRUE,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (id_bairro) REFERENCES bairro(id_bairro),
  INDEX idx_ponto_bairro_status (id_bairro,ativo,status_funcionamento)
) ENGINE=InnoDB;

CREATE TABLE ponto_material (
  id_ponto INT UNSIGNED NOT NULL,
  id_material INT UNSIGNED NOT NULL,
  PRIMARY KEY (id_ponto,id_material),
  FOREIGN KEY (id_ponto) REFERENCES ponto_descarte(id_ponto) ON DELETE CASCADE,
  FOREIGN KEY (id_material) REFERENCES material(id_material)
) ENGINE=InnoDB;

-- Galeria dos pontos de descarte.
CREATE TABLE ponto_foto (
  id_ponto_foto BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_ponto INT UNSIGNED NOT NULL,
  url_arquivo VARCHAR(255) NOT NULL,
  legenda VARCHAR(150) NULL,
  ordem TINYINT UNSIGNED NOT NULL DEFAULT 1,
  FOREIGN KEY (id_ponto) REFERENCES ponto_descarte(id_ponto) ON DELETE CASCADE,
  UNIQUE KEY uk_ponto_foto_ordem (id_ponto,ordem)
) ENGINE=InnoDB;

-- Formulário público “Sugira um ponto”.
CREATE TABLE sugestao_ponto_descarte (
  id_sugestao BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT UNSIGNED NULL,
  nome_local VARCHAR(150) NOT NULL,
  endereco VARCHAR(200) NOT NULL,
  materiais_informados VARCHAR(500) NULL,
  horario_informado VARCHAR(150) NULL,
  observacao TEXT NULL,
  status ENUM('NOVA','EM_ANALISE','APROVADA','RECUSADA') NOT NULL DEFAULT 'NOVA',
  criada_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  analisada_em DATETIME NULL,
  id_administrador_analise INT UNSIGNED NULL,
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE SET NULL,
  FOREIGN KEY (id_administrador_analise) REFERENCES usuario(id_usuario) ON DELETE SET NULL,
  INDEX idx_sugestao_status_data (status,criada_em)
) ENGINE=InnoDB;

CREATE TABLE ocorrencia (
  id_ocorrencia BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  protocolo VARCHAR(25) NOT NULL UNIQUE,
  id_usuario INT UNSIGNED NULL COMMENT 'Nulo para denúncia anônima',
  id_bairro INT UNSIGNED NOT NULL,
  tipo ENUM('DESCARTE_IRREGULAR','COLETA_NAO_REALIZADA','MATERIAL_NAO_RECOLHIDO','PONTO_INADEQUADO','ENTULHO','LIXO_TOXICO','OUTROS') NOT NULL,
  urgencia ENUM('BAIXA','MEDIA','ALTA') NOT NULL DEFAULT 'MEDIA',
  endereco_descricao VARCHAR(200) NOT NULL,
  latitude DECIMAL(10,7) NULL,
  longitude DECIMAL(10,7) NULL,
  data_ocorrencia DATE NULL,
  descricao VARCHAR(500) NOT NULL,
  anonima BOOLEAN NOT NULL DEFAULT FALSE,
  nome_contato VARCHAR(120) NULL,
  telefone_contato VARCHAR(20) NULL,
  email_contato VARCHAR(150) NULL,
  visivel_publicamente BOOLEAN NOT NULL DEFAULT TRUE,
  status ENUM('NOVA','EM_ANALISE','EM_ANDAMENTO','RESOLVIDA','ARQUIVADA') NOT NULL DEFAULT 'NOVA',
  observacao_admin TEXT NULL,
  id_administrador_responsavel INT UNSIGNED NULL,
  criada_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizada_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  resolvida_em DATETIME NULL,
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE SET NULL,
  FOREIGN KEY (id_bairro) REFERENCES bairro(id_bairro),
  FOREIGN KEY (id_administrador_responsavel) REFERENCES usuario(id_usuario) ON DELETE SET NULL,
  INDEX idx_ocorrencia_consulta (status,tipo,id_bairro,criada_em),
  INDEX idx_ocorrencia_usuario (id_usuario,criada_em)
) ENGINE=InnoDB;

-- A tela aceita uma foto hoje, mas a relação suporta evidências futuras.
CREATE TABLE ocorrencia_foto (
  id_ocorrencia_foto BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_ocorrencia BIGINT UNSIGNED NOT NULL,
  url_arquivo VARCHAR(255) NOT NULL,
  tipo_mime VARCHAR(100) NULL,
  tamanho_bytes INT UNSIGNED NULL,
  criada_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_ocorrencia) REFERENCES ocorrencia(id_ocorrencia) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Linha do tempo e auditoria das mudanças feitas pelo painel administrativo.
CREATE TABLE ocorrencia_historico_status (
  id_historico BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_ocorrencia BIGINT UNSIGNED NOT NULL,
  status ENUM('NOVA','EM_ANALISE','EM_ANDAMENTO','RESOLVIDA','ARQUIVADA') NOT NULL,
  observacao TEXT NULL,
  id_usuario_responsavel INT UNSIGNED NULL,
  criada_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_ocorrencia) REFERENCES ocorrencia(id_ocorrencia) ON DELETE CASCADE,
  FOREIGN KEY (id_usuario_responsavel) REFERENCES usuario(id_usuario) ON DELETE SET NULL,
  INDEX idx_historico_ocorrencia_data (id_ocorrencia,criada_em)
) ENGINE=InnoDB;

CREATE TABLE recompensa (
  id_recompensa INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  descricao VARCHAR(500) NULL,
  parceiro VARCHAR(150) NULL,
  custo_pontos INT UNSIGNED NOT NULL,
  disponivel BOOLEAN NOT NULL DEFAULT TRUE,
  estoque INT UNSIGNED NULL COMMENT 'Nulo = ilimitado',
  ativo BOOLEAN NOT NULL DEFAULT TRUE,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT ck_recompensa_custo CHECK (custo_pontos > 0)
) ENGINE=InnoDB;

CREATE TABLE resgate_recompensa (
  id_resgate BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT UNSIGNED NOT NULL,
  id_recompensa INT UNSIGNED NOT NULL,
  codigo VARCHAR(30) NOT NULL UNIQUE,
  pontos_utilizados INT UNSIGNED NOT NULL,
  status ENUM('SOLICITADO','UTILIZADO','CANCELADO','EXPIRADO') NOT NULL DEFAULT 'SOLICITADO',
  solicitado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  expira_em DATETIME NULL,
  utilizado_em DATETIME NULL,
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
  FOREIGN KEY (id_recompensa) REFERENCES recompensa(id_recompensa),
  CONSTRAINT ck_resgate_pontos CHECK (pontos_utilizados > 0),
  INDEX idx_resgate_usuario_status (id_usuario,status)
) ENGINE=InnoDB;

-- Persistência opcional do apelido e progresso da Área Kids para usuários autenticados.
CREATE TABLE perfil_kids (
  id_perfil_kids INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT UNSIGNED NOT NULL,
  apelido VARCHAR(16) NOT NULL,
  pontos INT UNSIGNED NOT NULL DEFAULT 0,
  jogos_concluidos INT UNSIGNED NOT NULL DEFAULT 0,
  atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB;

-- O saldo do usuário é SUM(quantidade): créditos positivos, resgates negativos.
CREATE TABLE lancamento_pontos (
  id_lancamento BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT UNSIGNED NOT NULL,
  id_perfil_kids INT UNSIGNED NULL,
  id_ocorrencia BIGINT UNSIGNED NULL,
  id_resgate BIGINT UNSIGNED NULL,
  tipo ENUM('OCORRENCIA_REGISTRADA','FOTO_APROVADA','OCORRENCIA_RESOLVIDA','QUIZ_KIDS','SEQUENCIA_ACESSO','INDICACAO','RESGATE','AJUSTE') NOT NULL,
  quantidade INT NOT NULL,
  descricao VARCHAR(255) NULL,
  criada_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
  FOREIGN KEY (id_perfil_kids) REFERENCES perfil_kids(id_perfil_kids) ON DELETE SET NULL,
  FOREIGN KEY (id_ocorrencia) REFERENCES ocorrencia(id_ocorrencia) ON DELETE SET NULL,
  FOREIGN KEY (id_resgate) REFERENCES resgate_recompensa(id_resgate) ON DELETE SET NULL,
  INDEX idx_lancamento_usuario_data (id_usuario,criada_em),
  INDEX idx_lancamento_ocorrencia (id_ocorrencia)
) ENGINE=InnoDB;

