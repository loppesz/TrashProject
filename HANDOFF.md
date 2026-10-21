# HANDOFF — ColetaFácil (TrashProject)

Documento de transferência para continuidade do desenvolvimento. Conciso e técnico.

---

## 1. Visão Geral e Contexto

**Objetivo:** Plataforma web de coleta seletiva de **Muriaé-MG**. Permite ao cidadão consultar a coleta por bairro, localizar pontos de descarte, registrar/consultar ocorrências ambientais, acumular pontos e trocar por recompensas, além de uma área Kids e um painel administrativo.

**Stack:**
- **Linguagem:** PHP 8.3 (puro, sem framework) + JavaScript vanilla.
- **Arquitetura:** MVC nativo com **Front Controller** (`index.php`). Sem Composer/dependências PHP externas.
- **Banco:** MySQL 9.1 (WAMP), charset `utf8mb4`. Banco `coletafacil`.
- **Front-end:** HTML/CSS/JS puro. Leaflet (mapa de pontos). PWA (`manifest.json` + `sw.js`).
- **Servidor dev:** Apache do WAMP via VirtualHost `coletafacil/`.

**Arquitetura (fluxo):** Toda requisição que não casa um arquivo real cai no `.htaccess` → `index.php?url=...` → `Request` parseia a rota `/{controller}/{metodo}/{action}/{id}` → instancia o controller → método renderiza uma `view` (que inclui `comuns/cabecalho.php` + `comuns/rodape.php`) ou devolve JSON.

---

## 2. Estado Atual (funcionando)

Tudo abaixo está implementado, testado e **commitado** (ver seção 5):

- **Migração completa para MVC** (projeto nasceu procedural).
- **Autenticação nativa PHP:** login/cadastro/logout, guard `auth_require()`, sessão em `sessao_usuario`. Usuário em `$_SESSION`.
- **Páginas públicas ligadas ao banco:** Home, Materiais, Pontos de descarte, Coleta, Ocorrências, Recompensas, Kids, Busca.
- **Ocorrências:** registro via POST (gera protocolo), upload de foto (`assets/uploads/ocorrencias/`), consulta por protocolo (JSON via `fetch`), **timeline de status real** (`ocorrencia_historico_status`).
- **Consulta de coleta por bairro:** `<select>` populado do banco → `fetch` em `coleta/consultarJson?id_bairro=N` → preenche dias, horário, equipe, materiais recolhidos e não recolhidos. (Geocodificação foi **descartada**.)
- **Sugestão de ponto de descarte:** form público (POST → `ponto/sugerir`, grava `sugestao_ponto_descarte` status `NOVA`) + tela admin `admin/sugestoes` para aprovar/recusar.
- **Pontos/Recompensas:** livro-razão `lancamento_pontos` (saldo = `SUM`), crédito ao registrar ocorrência, resgate com débito, ranking.
- **Kids:** perfis em `perfil_kids` (um usuário pode ter vários filhos); anônimo usa `localStorage`, logado sincroniza no banco.
- **Painel Admin (guard ADMINISTRADOR):** dashboard com cards clicáveis (inclui card "Sugestões Pendentes"), CRUD de materiais/pontos/coleta, gestão de status de ocorrências, indicadores, análise de sugestões.
- **PWA:** service worker com cache-first para estáticos e network-first para rotas; endpoints JSON (`consultarJson`, `kidsSalvar`) passam direto à rede (sem fallback offline).

---

## 3. Decisões Técnicas e Padrões (MANTER)

### Estrutura de pastas
```
index.php            Front Controller
.htaccess            Rewrite -> index.php?url=$1 [QSA,L]
sw.js, manifest.json PWA
app/
  config/    Config.php, config.local.exemplo.php (senha em config.local.php, gitignored)
  library/   Request.php, Database.php (PDO, utf8mb4)
  helper/    Auth.php (auth_user/auth_require), Utilits.php (baseUrl, formataHora, rotulo*, etc.)
  controller/ BaseController + Home, Usuario, Ponto, Coleta, Ocorrencia, Admin, Busca
  model/     BaseModel + {Entidade}Model
  view/      {pagina}.php ; view/comuns/ (cabecalho/rodape/admin_*) ; view/admin/ (telas do painel)
assets/      css/ js/ (uploads/ocorrencias/ p/ fotos)
modelo/      modeloColetaFacil.sql (schema), seed.sql, migrations, .mwb (MySQL Workbench)
```

### Convenções obrigatórias
- **Roteamento:** rota = `/{controller}/{metodo}`. `Request` faz `ucfirst(strtolower())` no controller; o método preserva o case (ex.: `consultarJson`).
- **Controller:** `extends BaseController`. `$this->model('Nome')` → `NomeModel`; `$this->view('arquivo', $data)` faz `extract($data)`. `$this->request->post()/get()/isPost()`.
- **Padrão de ação POST:** validar → chamar model → `header('Location: ...?msg=|?erro=')` → `exit`. Nunca retornar HTML de um POST.
- **Endpoints JSON:** `header('Content-Type: application/json; charset=utf-8')`, `ob_end_clean()` para descartar saída espúria, `try/catch`, `json_encode(..., JSON_UNESCAPED_UNICODE)`, `exit`.
- **Model:** `extends BaseModel`, usa `$this->db->select/insert/update/delete` (PDO). **Não** sobrescrever a assinatura de métodos herdados de `BaseModel` (ex.: `lista()` já existe com `(string $orderField='')` — use outro nome, como `listaTodas()`).
- **Views:** sempre abrir com `require comuns/cabecalho.php` e fechar com `rodape.php` (admin usa `admin_cabecalho/admin_rodape`). Escapar saída com `htmlspecialchars`. Expor dados ao JS via `window.BASEURL = <?= json_encode(baseUrl()) ?>` **antes** do rodapé (que carrega o JS da página).
- **Cache-busting:** CSS/JS versionados por `?v=<ASSET_VER>` (constante em `Config.php`). **Ao alterar qualquer CSS/JS, incrementar `ASSET_VER`** (hoje `'12'`) e, se mudar comportamento do SW ou assets precacheados, incrementar `CACHE_NAME` em `sw.js` (hoje `coletafacil-v34`).
- **UTF-8:** editar arquivos SEMPRE pelas ferramentas de edição (preservam UTF-8). **NUNCA** `Set-Content` do PowerShell (adiciona BOM / corrompe acentos/emojis). Rodar o seed via `.bat` com redirecionamento `<` (o pipe do PowerShell corrompe UTF-8).
- **JS defensivo:** ao escrever em elementos do DOM dentro de callbacks, proteger contra `null` (ver helper `setTxt` em `coleta.js`). Esse padrão evitou um `TypeError` que quebrava a consulta de coleta.

### Banco (tabelas principais)
`usuario`, `sessao_usuario`, `bairro`, `material` (+`material_instrucao`,`material_exemplo`), `coleta` (+`coleta_material`), `ponto_descarte` (+`ponto_material`,`ponto_horario`,`ponto_foto`), `ocorrencia` (+`ocorrencia_foto`,`ocorrencia_historico_status`), `sugestao_ponto_descarte`, `recompensa`, `resgate_recompensa`, `lancamento_pontos`, `perfil_kids`.
- `coleta.dia_semana`: TINYINT **1=domingo … 7=sábado** (no JS converte-se para 0–6 com `dia-1`).
- Schema canônico: `modelo/modeloColetaFacil.sql`. Dados de teste: `modelo/seed.sql` (idempotente, com `TRUNCATE`).

---

## 4. Pendências e Próximos Passos (To-Do)

**Commit pendente (único):**
- `sw.js` está modificado no working tree (regra para endpoints JSON não passarem pela página offline) e **ainda não foi commitado**. Validar no navegador e commitar.

**Bug recém-corrigido (verificar em produção/navegador real):**
- Consulta de coleta exibia o popup *"Não foi possível consultar agora"*. **Causa real:** `mostrarResultado()` em `coleta.js` acessava `getElementById('cdUnit')` que não existia na view → `TypeError` dentro do `.then` → caía no `.catch`. **Correção aplicada:** `id="cdUnit"` adicionado em `coleta.php` + helper `setTxt` blindando todos os `textContent`/`innerHTML`. Falta **confirmar no navegador do usuário** (limpar cache/SW: fechar todas as abas ou hard refresh, pois o service worker mudou).

**Funcionalidades/refino sugeridos (não bloqueantes):**
- Ao **aprovar** uma sugestão de ponto, opcionalmente criar o `ponto_descarte` automaticamente (hoje só muda status; falta mapear bairro, pois a sugestão não captura `id_bairro`).
- Autenticação segue em sessão PHP nativa — migração para tokens foi **descartada** (risco alto). Manter.
- Testes automatizados: não existem. Hoje a verificação é manual via servidor embutido + `curl`.
- Notificações de coleta (botão "Me lembrar") e compartilhamento são simulados no front.

---

## 5. Fase Atual e Primeira Tarefa Imediata

**Onde paramos:** Os 3 itens da última sessão (histórico de status, consulta de coleta por bairro, sugestão de ponto) + card de sugestões pendentes **estão implementados e commitados** no commit `efc886c`. Em seguida, corrigimos o bug do popup na consulta de coleta — essa correção (`coleta.js` + `coleta.php` + `ASSET_VER=12`) **também já está commitada**. Resta apenas o `sw.js` modificado e não commitado.

**Branch:** `main` (sincronizada com `origin/main`).

**PRIMEIRA TAREFA IMEDIATA para a próxima IA:**
1. Pedir ao usuário para **validar no navegador** (com cache/SW limpos) que a consulta de coleta por bairro funciona sem o popup e exibe o resultado (dias, horário, materiais).
2. Se confirmado, **commitar o `sw.js`** (mensagem curta em PT, ex.: `Service worker: endpoints JSON direto a rede`).
3. Então seguir para o refino da aprovação de sugestões (item do To-Do) ou o que o usuário priorizar.

**Convenção de commit do usuário:** mensagens curtas em português; **o usuário commita manualmente** (sugerir a mensagem, não commitar sem pedir).

---

## 6. Instruções de Execução

### Ambiente (Windows + WAMP)
- PHP CLI: `C:\wamp64\bin\php\php8.3.22\php.exe`
- MySQL client: `C:\wamp64\bin\mysql\mysql9.1.0\bin\mysql.exe`
- MySQL do projeto: **porta 3306**, user `root`, **sem senha** (MariaDB na 3307 — NÃO usar).
- VirtualHost: `coletafacil/` apontando para a raiz do projeto. `BASEURL` é autodetectada em `Config.php` (funciona em VirtualHost e em subpasta).

### Rodar o projeto
- Via WAMP/Apache: acessar `http://coletafacil/` (VirtualHost).
- Via servidor embutido (debug rápido), usar um router que simula o rewrite:
  ```powershell
  # router-test.php: popula $_GET['url'] a partir do path e inclui index.php
  & "C:\wamp64\bin\php\php8.3.22\php.exe" -c php.ini -S 127.0.0.1:8099 router-test.php
  ```
  (precisa de um `php.ini` com `extension_dir`, `pdo_mysql`, `mbstring`, `default_charset=UTF-8`.)

### Configuração local (variáveis sensíveis)
- Copiar `app/config/config.local.exemplo.php` → `app/config/config.local.php` (gitignored).
- Definir a senha do banco em `['db_senha' => '...']` (vazia no WAMP padrão).

### Banco de dados
```powershell
# Criar schema (uma vez)
C:\wamp64\bin\mysql\mysql9.1.0\bin\mysql.exe -u root --port=3306 < modelo\modeloColetaFacil.sql

# Popular/repopular dados de teste (idempotente). Rodar via .bat com redirecionamento '<'
# (um arquivo .bat contendo a linha abaixo evita corrupção de UTF-8 pelo PowerShell):
C:\wamp64\bin\mysql\mysql9.1.0\bin\mysql.exe -u root --port=3306 --default-character-set=utf8mb4 coletafacil < modelo\seed.sql
```

### Credenciais de teste (do seed)
- Admin: `admin@coletafacil.com` / `admin123`
- Morador: `morador@coletafacil.com` / `morador123` (tem 2 perfis Kids)
- Outros moradores: `maria|joao|ana|carlos|paula @coletafacil.com` / `morador123`

### Testes
- Não há suíte automatizada. Verificação manual: subir o servidor, `curl` nas rotas e endpoints JSON, conferir status HTTP e o corpo. Sempre **limpar artefatos de teste** (`router-test.php`, `php.ini` temporário, `_*`) e **restaurar o banco** rodando o seed ao final.
- Lint PHP: `& "C:\wamp64\bin\php\php8.3.22\php.exe" -l caminho\arquivo.php`
- Lint JS (se Node disponível): `node --check assets\js\arquivo.js`
