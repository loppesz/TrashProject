# ColetaFácil – Muriaé-MG

Plataforma web de coleta seletiva de Muriaé-MG, desenvolvida como projeto de extensão. Com ela o morador:

- consulta os dias e horários da coleta comum e seletiva do seu bairro (cronograma oficial do DEMSUR);
- encontra pontos de descarte de materiais especiais (pilhas, medicamentos, perfurocortantes etc.);
- registra e acompanha ocorrências (descarte irregular, coleta não realizada), com foto e localização;
- acumula pontos e troca por recompensas;
- aprende sobre reciclagem na Área Kids;
- gerencia os próprios dados em **Minha conta**: editar perfil, trocar senha, exportar os dados e excluir a conta.

O painel administrativo gerencia ocorrências, materiais, coleta, pontos de descarte, sugestões de novos pontos e a **política de retenção de dados** (Admin → Privacidade).

## Privacidade e segurança (LGPD)

- Páginas de **Termos de Uso** (`/home/termos`) e **Política de Privacidade** (`/home/privacidade`). O cadastro exige o aceite, e a data e a versão aceitas ficam registradas no usuário.
- **Retenção:** dados de contato, GPS e fotos de ocorrências encerradas são apagados depois dos prazos definidos em `RETENCAO_DIAS` (`app/config/Config.php`). O administrador aplica a retenção pelo painel.
- Ações sobre dados pessoais (exportação, exclusão de conta, retenção) ficam numa trilha de auditoria, sem o conteúdo dos dados.
- Formulários com proteção **CSRF** e limite de tentativas em login, cadastro, ocorrências e sugestões. O limite guarda só um hash do IP, apagado em 24 horas.
- O `.htaccess` bloqueia o acesso direto pelo navegador às pastas `app/`, `modelo/`, `docs/` e `assets/uploads/`, a arquivos ocultos e a arquivos `.sql`, `.md`, `.bat` etc.

## Tecnologias

- PHP 8.3 sem framework, em MVC próprio com Front Controller (`index.php`)
- MySQL 8 ou superior (testado no MySQL 9.1 do WAMP)
- HTML, CSS e JavaScript puros; Leaflet + OpenStreetMap e Google Maps nos mapas
- PWA (`manifest.json` e `sw.js`)

## Requisitos

- [WAMP](https://www.wampserver.com/) (Apache + PHP 8.3 + MySQL)
- Extensões do PHP: `pdo_mysql`, `mbstring`, `gd` e `fileinfo` (já vêm ativas no WAMP)
- No Apache, `mod_rewrite` ativo e `AllowOverride All` na pasta do projeto (o roteamento usa o `.htaccess`)

## Instalação

### 1. Banco de dados

O WAMP pode ter dois servidores de banco. Use o **MySQL na porta 3306**, não o MariaDB (porta 3307).

Rode os dois scripts na ordem, pelo terminal (`cmd`) na pasta do projeto:

```bat
C:\wamp64\bin\mysql\mysql9.1.0\bin\mysql.exe -u root -P 3306 --default-character-set=utf8mb4 < modelo\modeloColetaFacil.sql
C:\wamp64\bin\mysql\mysql9.1.0\bin\mysql.exe -u root -P 3306 --default-character-set=utf8mb4 coletafacil < modelo\seed.sql
```

- `modeloColetaFacil.sql` cria o banco `coletafacil` e todas as tabelas, incluindo as de LGPD e segurança. Ele deve rodar quando o banco ainda não existe.
- `seed.sql` carrega os dados de exemplo: bairros, cronograma de coleta, materiais, pontos, recompensas, ocorrências e usuários de teste. Ele **apaga os dados existentes** antes de inserir, então pode ser rodado de novo para voltar ao estado inicial.

Ajuste a pasta `mysql9.1.0` conforme a versão instalada no seu WAMP. O `--default-character-set=utf8mb4` é necessário para os acentos e emojis não serem corrompidos. Pelo mesmo motivo, use o `cmd` e não o PowerShell para o redirecionamento `<`.

**Já tem um banco `coletafacil` antigo?** O `modeloColetaFacil.sql` não atualiza um banco existente. Apague o banco antigo (`DROP DATABASE coletafacil;`) e rode os dois scripts de novo. Isso apaga os dados cadastrados nele.

### 2. Configuração local

Copie `app/config/config.local.exemplo.php` para `app/config/config.local.php` e preencha:

| Chave | Para que serve |
|---|---|
| `db_usuario`, `db_senha` | Usuário e senha do MySQL. No WAMP padrão: `root` e senha vazia. |
| `app_key` | Chave secreta usada no controle de tentativas. Gere uma com `php -r "echo bin2hex(random_bytes(32));"`. Sem ela o sistema usa uma chave derivada da máquina. |
| `debug` | `true` mostra os erros do PHP na tela. Use só no desenvolvimento; com `false` (padrão) os erros não aparecem para o usuário e vão para o log do PHP. |
| `lgpd_email` | Opcional. E-mail de contato exibido na Política de Privacidade. |

Também é possível definir `db_host`, `db_porta` e `db_banco` se o banco não estiver no padrão (`127.0.0.1`, `3306`, `coletafacil`).

O `config.local.php` não vai para o repositório (está no `.gitignore`).

**Opcional, para produção:** em vez do `root`, a aplicação pode usar um usuário do MySQL que só lê e grava dados, sem poder criar ou apagar tabelas. Rode `modelo\usuario_banco_app.sql` uma vez como `root`, trocando antes a senha `TROQUE_ESTA_SENHA`, e coloque `'db_usuario' => 'coletafacil_app'` e a nova senha no `config.local.php`. Os scripts do banco continuam rodando com o `root`.

### 3. Endereço do site

Crie um VirtualHost no WAMP apontando para a pasta do projeto (ícone do WAMP → *Your VirtualHosts* → *VirtualHost Management*), por exemplo com o nome `coletafacil`, e acesse `http://coletafacil/`.

O endereço base é detectado sozinho. O projeto também abre em subpasta (`http://localhost/TrashProject/`), mas o modo app (PWA) e a página offline só funcionam corretamente na raiz de um VirtualHost.

## Como rodar

**Pelo Apache (uso normal):** com o WAMP ligado, acesse `http://coletafacil/`.

**Pelo `rodar-local.bat`:** dê dois cliques no arquivo. Ele abre o projeto em `http://localhost:8080` usando o PHP do WAMP (o WAMP precisa estar ligado por causa do MySQL). Use este modo para testar o botão **"Usar minha localização"** das ocorrências: o navegador só libera a geolocalização em HTTPS ou em `localhost`, e por isso ela não funciona pelo VirtualHost em `http://`.

O `serve-local.php` é o roteador usado pelo `rodar-local.bat`: faz o papel do `.htaccess`, inclusive os mesmos bloqueios de pastas e arquivos internos. Não é preciso abri-lo.

## Usuários de teste

Criados pelo `seed.sql`:

| Nome | E-mail | Senha | Perfil |
|---|---|---|---|
| Administrador | admin@coletafacil.com | admin123 | Administrador |
| Morador Teste | morador@coletafacil.com | morador123 | Morador |
| Maria Silva | maria@coletafacil.com | morador123 | Morador |
| João Ferreira | joao@coletafacil.com | morador123 | Morador |
| Ana Costa | ana@coletafacil.com | morador123 | Morador |
| Carlos Moura | carlos@coletafacil.com | morador123 | Morador |
| Paula Rocha | paula@coletafacil.com | morador123 | Morador |

O **Morador Teste** já tem ocorrências registradas, pontos e dois perfis na Área Kids. O painel administrativo fica em `/admin` e só abre com o usuário Administrador. Logado, qualquer usuário acessa a própria conta em `/usuario/conta`.

Esses usuários foram criados direto pelo seed, então aparecem em "Minha conta" como "Conta anterior ao registro de aceite". Contas novas criadas pelo cadastro registram o aceite normalmente.

Essas senhas servem apenas para testes. Troque-as se o sistema for publicado.

## Estrutura

```
index.php              Front Controller: toda rota passa por aqui
.htaccess              Redireciona as rotas para o index.php (Apache)
rodar-local.bat        Servidor local em http://localhost:8080
serve-local.php        Roteador do servidor local
sw.js, manifest.json   PWA (cache offline e instalação como app)
app/
  config/              Config.php (configurações gerais, prazos de retenção) e config.local.php (credenciais)
  controller/          Um controller por área: Home, Usuario, Coleta, Ponto, Ocorrencia, Admin, Busca
  model/               Acesso ao banco, um model por entidade
  view/                Páginas; comuns/ tem cabeçalho e rodapé; admin/ tem o painel
  helper/              Auth (sessão e login), Seguranca (CSRF, limite de tentativas), Utilits (formatação, URLs)
  library/             Request (rotas) e Database (PDO)
assets/                CSS, JavaScript e imagens
modelo/
  modeloColetaFacil.sql      Estrutura completa do banco
  seed.sql                   Dados de exemplo
  usuario_banco_app.sql      Usuário do MySQL com permissões mínimas (opcional)
  *.mwb, *.png               Modelo ER do MySQL Workbench
docs/apresentacao/     Material de apresentação do projeto
```

As rotas seguem o padrão `/{controller}/{metodo}`. Exemplos: `/coleta`, `/ponto`, `/ocorrencia`, `/home/kids`, `/usuario/conta`, `/admin/ocorrencias`, `/admin/privacidade`.

## Observações

- **Fotos das ocorrências** ficam gravadas no banco (tabela `ocorrencia_foto`), sem os metadados EXIF/GPS da câmera, e só o administrador consegue abri-las.
- **Cache do navegador:** ao alterar CSS ou JavaScript, aumente o `ASSET_VER` em `app/config/Config.php`. Se o site parecer desatualizado, use Ctrl+F5.
- **Cronograma de coleta:** dados do [DEMSUR](https://www.demsur.com.br/pagina/107/cronograma-coleta), consultados em outubro de 2026.
