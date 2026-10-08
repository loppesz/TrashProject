# Atualização do Modelo ER - Coleta Fácil

Este arquivo reúne tudo que deve ser atualizado no documento `Coleta Fácil - ModeloER.docx`, tomando como fonte de verdade o arquivo `modeloColetaFacil.sql`.

## 1. Resumo das mudanças

O documento atual descreve o modelo inicial, com 8 tabelas. O SQL atual possui 17 tabelas e já contempla autenticação, sessões, sugestões de pontos, fotos, histórico de ocorrências, recompensas, pontuação e Área Kids.

O documento deve deixar de apresentar essas funcionalidades como futuras. Elas já fazem parte do modelo físico atual.

### Inventário atual

1. `usuario`
2. `sessao_usuario`
3. `bairro`
4. `coleta`
5. `material`
6. `coleta_material`
7. `ponto_descarte`
8. `ponto_material`
9. `ponto_foto`
10. `sugestao_ponto_descarte`
11. `ocorrencia`
12. `ocorrencia_foto`
13. `ocorrencia_historico_status`
14. `recompensa`
15. `resgate_recompensa`
16. `lancamento_pontos`
17. `perfil_kids`

<!-- ## 2. O que remover do documento antigo

- Remover a tabela `administrador` do diagrama e do dicionário.
- Remover o texto que trata pontuação, recompensas, ranking e Área Kids como funcionalidades futuras.
- Remover o campo `foto_url` da tabela `ocorrencia`.
- Remover a descrição de `ocorrencias` no plural; o nome correto da tabela é `ocorrencia`.
- Remover a afirmação de que o modelo possui apenas usuários administradores separados dos moradores.
- Remover a afirmação de que o modelo atende somente ao primeiro Documento de Requisitos.
- Remover do diagrama antigo todas as tabelas que não existam mais no SQL atual.
- Atualizar a descrição de `dia_semana` para o padrão atual: `1 = domingo` e `7 = sábado`.
- Remover a descrição antiga que não menciona horário, equipe, telefone e observação da programação de coleta. -->
<!-- 
## 3. Ajustes obrigatórios no conteúdo existente

### Identificação e objetivo

Trocar “Modelo ER inicial” por “Modelo ER atual” ou “Modelo lógico e físico atual”. O objetivo deve informar que o banco atende às áreas pública, autenticada e administrativa da plataforma. -->

<!-- ### Visão geral

Substituir a lista antiga de tabelas pela lista completa apresentada na seção 1. Organizar a explicação por módulos:

- **Acesso e usuários:** `usuario` e `sessao_usuario`.
- **Coleta seletiva:** `bairro`, `coleta`, `material` e `coleta_material`.
- **Pontos de descarte:** `ponto_descarte`, `ponto_material`, `ponto_foto` e `sugestao_ponto_descarte`.
- **Ocorrências:** `ocorrencia`, `ocorrencia_foto` e `ocorrencia_historico_status`.
- **Recompensas e pontuação:** `recompensa`, `resgate_recompensa` e `lancamento_pontos`.
- **Área Kids:** `perfil_kids`. -->

<!-- ### Usuários e administradores

Substituir a tabela `administrador` por `usuario`. Moradores e administradores usam a mesma tabela, diferenciados pelo campo `tipo`, que aceita `MORADOR` ou `ADMINISTRADOR`.

O campo `senha_hash` deve ser descrito como armazenamento do hash da senha, e não como senha criptografada. A tabela `sessao_usuario` armazena o hash do token de sessão, nunca o token original. -->

<!-- ### Coleta

Adotar a numeração definida no SQL: `1 = domingo` e `7 = sábado`.

Adicionar `equipe_responsavel`, `telefone_contato` e `observacao`. Registrar que `horario_fim` deve ser posterior a `horario_inicio` e que a combinação de bairro, dia e horário de início não pode se repetir. -->

<!-- ### Materiais

Manter os campos já descritos, corrigindo a grafia dos nomes para os nomes reais do SQL, sem acentos: `descricao`, `instrucao_preparo` e `orientacao_descarte`. -->
<!-- 
Adicionar `nivel_risco`, com os valores `BAIXO`, `MEDIO`, `ALTO` e `CRITICO`. Explicar que o material pode ser aceito ou não na coleta seletiva e possuir instruções específicas de segurança e descarte. -->

<!-- ### Pontos de descarte

Adicionar `status_funcionamento`, com os valores `ABERTO` e `FECHADO`, além de `criado_em` e `atualizado_em`.

Adicionar a relação 1:N entre `ponto_descarte` e `ponto_foto`, permitindo várias fotos ordenadas para cada ponto. -->

<!-- ### Sugestões de pontos

Adicionar a tabela `sugestao_ponto_descarte`, que registra sugestões feitas por usuários autenticados ou anonimamente. A sugestão possui status `NOVA`, `EM_ANALISE`, `APROVADA` ou `RECUSADA` e pode guardar o administrador responsável pela análise. -->

<!-- ### Ocorrências

Substituir o campo `foto_url` por uma relação 1:N com `ocorrencia_foto`. Embora a tela atual possa enviar uma foto, o modelo permite várias evidências. -->

<!-- Adicionar usuário opcional, anonimato, urgência, coordenadas, data da ocorrência, dados de contato, visibilidade pública, administrador responsável e data de resolução.

Adicionar a relação 1:N com `ocorrencia_historico_status`, responsável por registrar a linha do tempo das mudanças de status e suas observações. -->

<!-- ### Recompensas, pontos e Área Kids

Adicionar as tabelas `recompensa`, `resgate_recompensa`, `lancamento_pontos` e `perfil_kids`.

O saldo de pontos não deve ser descrito como um valor armazenado diretamente no usuário. Ele é calculado pela soma de `lancamento_pontos`: créditos possuem quantidade positiva e resgates possuem quantidade negativa. -->

<!-- ## 4. Texto sugerido para substituir a seção “Objetivo do Documento”

Este documento apresenta o Modelo ER atual do projeto Coleta Fácil, desenvolvido na disciplina de Extensão Universitária III. O modelo representa a estrutura de dados utilizada pelas áreas pública, autenticada e administrativa da plataforma.

O banco gerencia usuários e sessões de acesso, bairros, programações de coleta seletiva, materiais, pontos de descarte, sugestões de novos pontos, ocorrências ambientais, evidências fotográficas, histórico de atendimento, recompensas, lançamentos de pontos e o perfil da Área Kids.

O modelo foi elaborado a partir do esquema físico definido em `modeloColetaFacil.sql`, utilizando chaves primárias, chaves estrangeiras, restrições, índices e regras de integridade para garantir a consistência dos dados. -->

<!-- ## 5. Texto sugerido para substituir a seção “Modelo ER”

O diagrama atualizado deve conter as 17 tabelas do SQL e suas respectivas chaves primárias e estrangeiras. A tabela `usuario` centraliza moradores e administradores. As tabelas associativas `coleta_material` e `ponto_material` representam relacionamentos N:N.

As fotos de pontos e ocorrências foram separadas em tabelas próprias, permitindo mais de uma imagem por registro. O histórico de ocorrência registra as alterações de status ao longo do atendimento. As tabelas de recompensas e lançamentos de pontos representam a participação dos usuários, enquanto `perfil_kids` armazena o progresso da Área Kids para usuários autenticados. -->

<!-- ### Relacionamentos que devem aparecer na imagem -->
<!--  -->
<!-- - `usuario` 1:N `sessao_usuario` -->
<!-- - `bairro` 1:N `coleta` -->
<!-- - `coleta` N:N `material`, por meio de `coleta_material` -->
<!-- - `bairro` 1:N `ponto_descarte` -->
<!-- - `ponto_descarte` N:N `material`, por meio de `ponto_material` -->
<!-- - `ponto_descarte` 1:N `ponto_foto` -->
<!-- - `usuario` 1:N `sugestao_ponto_descarte` por `id_usuario`, opcional -->
<!-- - `usuario` 1:N `sugestao_ponto_descarte` por `id_administrador_analise`, opcional -->
<!-- - `bairro` 1:N `ocorrencia` -->
<!-- - `usuario` 1:N `ocorrencia` por `id_usuario`, opcional -->
<!-- - `usuario` 1:N `ocorrencia` por `id_administrador_responsavel`, opcional -->
<!-- - `ocorrencia` 1:N `ocorrencia_foto` -->
<!-- - `ocorrencia` 1:N `ocorrencia_historico_status` -->
<!-- - `usuario` 1:N `ocorrencia_historico_status`, opcional -->
<!-- - `usuario` 1:N `resgate_recompensa` -->
<!-- - `recompensa` 1:N `resgate_recompensa` -->
<!-- - `usuario` 1:N `lancamento_pontos` -->
<!-- - `ocorrencia` 1:N `lancamento_pontos`, opcional -->
<!-- - `resgate_recompensa` 1:N `lancamento_pontos`, opcional -->
<!-- - `usuario` 1:N `perfil_kids` -->
<!-- - `perfil_kids` 1:N `lancamento_pontos`, opcional -->

O relacionamento de `sugestao_ponto_descarte.id_administrador_analise` e o de `ocorrencia.id_administrador_responsavel` apontam para `usuario`, mas devem ser apresentados conceitualmente como administradores porque possuem essa responsabilidade no fluxo do sistema.

## 6. Dicionário de dados atualizado

### Tabela: usuario

<!-- - `id_usuario` (PK) - identificador único do usuário.
- `nome` - nome completo.
- `email` - endereço de login, único.
- `senha_hash` - hash da senha armazenada.
- `telefone` - telefone opcional.
- `tipo` - perfil do usuário: `MORADOR` ou `ADMINISTRADOR`.
- `ativo` - informa se o acesso está habilitado.
- `email_verificado_em` - data e hora da confirmação do e-mail.
- `criado_em` - data e hora do cadastro.
- `atualizado_em` - data e hora da última atualização. -->
<!-- 
### Tabela: sessao_usuario

- `id_sessao` (PK) - identificador da sessão.
- `id_usuario` (FK > usuario) - usuário dono da sessão.
- `token_hash` - hash único do token de sessão.
- `expira_em` - data e hora de expiração.
- `criada_em` - data e hora de criação.
- `encerrada_em` - data e hora do encerramento, quando houver.
- `ip_origem` - endereço IP de origem, quando disponível.

### Tabela: bairro

- `id_bairro` (PK) - identificador único.
- `nome` - nome do bairro ou região, único.
- `ativo` - informa se o bairro está disponível para consulta. -->

<!-- ### Tabela: coleta

- `id_coleta` (PK) - identificador da programação.
- `id_bairro` (FK > bairro) - bairro atendido.
- `dia_semana` - dia da semana de 1 (domingo) a 7 (sábado).
- `horario_inicio` - início previsto da coleta.
- `horario_fim` - término previsto da coleta.
- `equipe_responsavel` - equipe responsável, quando informada.
- `telefone_contato` - telefone para contato, quando informado.
- `observacao` - observações da programação.
- `ativo` - informa se a programação está disponível. -->

<!-- ### Tabela: material

- `id_material` (PK) - identificador único.
- `nome` - nome do material, único.
- `descricao` - exemplos e explicação sobre o material.
- `aceito_coleta_seletiva` - informa se é aceito na coleta seletiva regular.
- `nivel_risco` - nível de risco: `BAIXO`, `MEDIO`, `ALTO` ou `CRITICO`.
- `instrucao_preparo` - orientação para preparar o material.
- `alerta_seguranca` - aviso sobre riscos para usuários e profissionais.
- `orientacao_descarte` - alternativa ou orientação de descarte.
- `cor_lixeira` - cor de lixeira recomendada, quando aplicável.
- `ativo` - informa se o material está disponível para consulta. -->

<!-- ### Tabela: coleta_material

- `id_coleta` (PK, FK > coleta) - programação relacionada.
- `id_material` (PK, FK > material) - material recolhido. -->

<!-- ### Tabela: ponto_descarte

- `id_ponto` (PK) - identificador único.
- `id_bairro` (FK > bairro) - bairro do ponto.
- `nome` - nome do local.
- `endereco` - endereço completo.
- `horario_funcionamento` - dias e horários de funcionamento.
- `status_funcionamento` - situação atual: `ABERTO` ou `FECHADO`.
- `latitude` - coordenada de latitude.
- `longitude` - coordenada de longitude.
- `ativo` - informa se o ponto aparece nas consultas.
- `criado_em` - data e hora de criação.
- `atualizado_em` - data e hora da última atualização. -->

<!-- ### Tabela: ponto_material

- `id_ponto` (PK, FK > ponto_descarte) - ponto associado.
- `id_material` (PK, FK > material) - material aceito no ponto. -->

<!-- ### Tabela: ponto_foto

- `id_ponto_foto` (PK) - identificador da foto.
- `id_ponto` (FK > ponto_descarte) - ponto fotografado.
- `url_arquivo` - caminho ou URL do arquivo.
- `legenda` - legenda opcional.
- `ordem` - posição da foto na galeria do ponto. -->

<!-- ### Tabela: sugestao_ponto_descarte

- `id_sugestao` (PK) - identificador da sugestão.
- `id_usuario` (FK > usuario, opcional) - usuário que sugeriu o ponto.
- `nome_local` - nome do local sugerido.
- `endereco` - endereço informado.
- `materiais_informados` - materiais que o local receberia.
- `horario_informado` - horário informado pelo usuário.
- `observacao` - informações complementares.
- `status` - situação: `NOVA`, `EM_ANALISE`, `APROVADA` ou `RECUSADA`.
- `criada_em` - data e hora da sugestão.
- `analisada_em` - data e hora da análise.
- `id_administrador_analise` (FK > usuario, opcional) - administrador responsável pela análise. -->

<!-- ### Tabela: ocorrencia

- `id_ocorrencia` (PK) - identificador único.
- `protocolo` - código único para acompanhamento.
- `id_usuario` (FK > usuario, opcional) - usuário que registrou a ocorrência; nulo para denúncia anônima.
- `id_bairro` (FK > bairro) - bairro da ocorrência.
- `tipo` - categoria: `DESCARTE_IRREGULAR`, `COLETA_NAO_REALIZADA`, `MATERIAL_NAO_RECOLHIDO`, `PONTO_INADEQUADO`, `ENTULHO`, `LIXO_TOXICO` ou `OUTROS`.
- `urgencia` - nível `BAIXA`, `MEDIA` ou `ALTA`.
- `endereco_descricao` - endereço ou descrição do local.
- `latitude` e `longitude` - coordenadas opcionais.
- `data_ocorrencia` - data em que o fato ocorreu.
- `descricao` - explicação da situação registrada.
- `anonima` - informa se a denúncia é anônima.
- `nome_contato`, `telefone_contato` e `email_contato` - dados opcionais para contato.
- `visivel_publicamente` - informa se a ocorrência pode ser exibida publicamente.
- `status` - `NOVA`, `EM_ANALISE`, `EM_ANDAMENTO`, `RESOLVIDA` ou `ARQUIVADA`.
- `observacao_admin` - observação do atendimento administrativo.
- `id_administrador_responsavel` (FK > usuario, opcional) - administrador responsável.
- `criada_em` - data e hora de criação.
- `atualizada_em` - data e hora da última atualização.
- `resolvida_em` - data e hora da resolução, quando houver. -->
<!-- 
### Tabela: ocorrencia_foto

- `id_ocorrencia_foto` (PK) - identificador da evidência.
- `id_ocorrencia` (FK > ocorrencia) - ocorrência relacionada.
- `url_arquivo` - caminho ou URL da imagem.
- `tipo_mime` - tipo MIME do arquivo.
- `tamanho_bytes` - tamanho do arquivo em bytes.
- `criada_em` - data e hora do envio. -->

<!-- ### Tabela: ocorrencia_historico_status

- `id_historico` (PK) - identificador do registro histórico.
- `id_ocorrencia` (FK > ocorrencia) - ocorrência alterada.
- `status` - status registrado na linha do tempo.
- `observacao` - comentário sobre a alteração.
- `id_usuario_responsavel` (FK > usuario, opcional) - usuário responsável pela alteração.
- `criada_em` - data e hora do registro. -->

<!-- ### Tabela: recompensa

- `id_recompensa` (PK) - identificador da recompensa.
- `nome` - nome da recompensa.
- `descricao` - descrição da recompensa.
- `parceiro` - parceiro que oferece a recompensa.
- `custo_pontos` - quantidade de pontos necessária; deve ser maior que zero.
- `disponivel` - informa se pode ser resgatada.
- `estoque` - quantidade disponível; nulo significa estoque ilimitado.
- `ativo` - informa se permanece cadastrada no sistema.
- `criado_em` - data e hora de criação. -->

<!-- ### Tabela: resgate_recompensa

- `id_resgate` (PK) - identificador do resgate.
- `id_usuario` (FK > usuario) - usuário que solicitou.
- `id_recompensa` (FK > recompensa) - recompensa escolhida.
- `codigo` - código único de utilização.
- `pontos_utilizados` - quantidade de pontos consumida.
- `status` - `SOLICITADO`, `UTILIZADO`, `CANCELADO` ou `EXPIRADO`.
- `solicitado_em` - data e hora da solicitação.
- `expira_em` - data e hora de expiração, quando houver.
- `utilizado_em` - data e hora da utilização, quando houver.

### Tabela: lancamento_pontos

- `id_lancamento` (PK) - identificador do lançamento.
- `id_usuario` (FK > usuario) - usuário dono do lançamento.
- `id_perfil_kids` (FK > perfil_kids, opcional) - perfil Kids relacionado ao lançamento.
- `id_ocorrencia` (FK > ocorrencia, opcional) - ocorrência que originou os pontos.
- `id_resgate` (FK > resgate_recompensa, opcional) - resgate relacionado.
- `tipo` - origem do lançamento: `OCORRENCIA_REGISTRADA`, `FOTO_APROVADA`, `OCORRENCIA_RESOLVIDA`, `QUIZ_KIDS`, `SEQUENCIA_ACESSO`, `INDICACAO`, `RESGATE` ou `AJUSTE`.
- `quantidade` - pontos creditados ou debitados.
- `descricao` - explicação do lançamento.
- `criada_em` - data e hora do lançamento.

### Tabela: perfil_kids

- `id_perfil_kids` (PK) - identificador do perfil Kids.
- `id_usuario` (FK > usuario) - usuário autenticado associado.
- `apelido` - apelido exibido na Área Kids.
- `pontos` - pontuação específica do perfil Kids.
- `jogos_concluidos` - quantidade de jogos concluídos.
- `atualizado_em` - data e hora da última atualização. -->

## 7. Regras de integridade que devem ser mencionadas

- Todas as tabelas usam InnoDB e chaves estrangeiras.
- Identificadores são inteiros sem sinal e, nas tabelas principais, gerados automaticamente.
- E-mails de usuários, nomes de bairros e nomes de materiais são únicos.
- O token original de sessão não é armazenado; apenas seu hash é persistido.
- Fotos de pontos e ocorrências são excluídas quando o registro pai é excluído.
- Usuários excluídos podem deixar nulas as referências opcionais de ocorrências, sugestões e histórico.
- `dia_semana` aceita apenas valores de 1 a 7.
- `horario_fim` deve ser maior que `horario_inicio`.
- `custo_pontos` e `pontos_utilizados` devem ser maiores que zero.
- O vínculo entre coleta e material e entre ponto e material possui chave primária composta.
- O usuario pode possuir varios perfis Kids.

## 8. Checklist para atualizar o DOCX

- [ ] Atualizar a data e manter os dados da equipe, se ainda estiverem corretos.
- [ ] Substituir o objetivo pela versão atualizada.
- [ ] Reescrever a visão geral por módulos.
- [ ] Refazer a imagem do diagrama com as 17 tabelas atuais.
- [ ] Remover `administrador` e inserir `usuario` e `sessao_usuario`.
- [ ] Inserir `ponto_foto` e `sugestao_ponto_descarte`.
- [ ] Inserir `ocorrencia_foto` e `ocorrencia_historico_status`.
- [ ] Inserir `recompensa`, `resgate_recompensa`, `lancamento_pontos` e `perfil_kids`.
- [ ] Atualizar todas as cardinalidades e chaves estrangeiras.
- [ ] Atualizar `dia_semana` para o padrão domingo a sábado.
- [ ] Remover `foto_url` e documentar `ocorrencia_foto`.
- [ ] Substituir o dicionário antigo pelo dicionário da seção 6.
- [ ] Atualizar a seção de relacionamentos com as relações da seção 5.
- [ ] Reescrever as considerações finais para afirmar que este é o modelo atual.
- [ ] Conferir se nenhum campo descrito no DOCX deixou de existir no SQL.
- [ ] Conferir se todas as colunas, enums e tabelas do SQL estão representados no DOCX.

## 9. Modelo de encerramento atualizado

Este documento apresenta o modelo ER atual do sistema Coleta Fácil, alinhado ao esquema físico definido no arquivo `modeloColetaFacil.sql`. O modelo contempla o gerenciamento de usuários, sessões, coleta seletiva, materiais, pontos de descarte, sugestões, ocorrências, evidências, auditoria, recompensas, pontuação e Área Kids.

As tabelas e relacionamentos poderão ser ampliados em versões futuras, mas a documentação desta versão deve representar integralmente as funcionalidades e regras já previstas no banco de dados atual.
