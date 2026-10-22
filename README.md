# ColetaFácil – Muriaé-MG

Plataforma web de coleta seletiva de Muriaé-MG, desenvolvida como projeto de extensão. Com ela o morador:

- consulta os dias e horários da coleta comum e seletiva do seu bairro (cronograma oficial do DEMSUR);
- encontra pontos de descarte de materiais especiais (pilhas, medicamentos, perfurocortantes etc.);
- registra e acompanha ocorrências (descarte irregular, coleta não realizada), com foto e localização;
- acumula pontos e troca por recompensas;
- aprende sobre reciclagem na Área Kids.

O painel administrativo gerencia ocorrências, materiais, coleta, pontos de descarte e sugestões de novos pontos.

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

- `modeloColetaFacil.sql` cria o banco `coletafacil` e todas as tabelas. Ele deve rodar em um banco vazio.
- `seed.sql` carrega os dados de exemplo: bairros, cronograma de coleta, materiais, pontos, recompensas, ocorrências e usuários de teste. Ele **apaga os dados existentes** antes de inserir, então pode ser rodado de novo para voltar ao estado inicial.

Ajuste a pasta `mysql9.1.0` conforme a versão instalada no seu WAMP. O `--default-character-set=utf8mb4` é necessário para os acentos e emojis não serem corrompidos. Pelo mesmo motivo, use o `cmd` e não o PowerShell para o redirecionamento `<`.

### 2. Senha do banco

Copie `app/config/config.local.exemplo.php` para `app/config/config.local.php` e preencha a senha do usuário `root` do seu MySQL. No WAMP padrão a senha é vazia, então o arquivo pode ficar como está.

O `config.local.php` não vai para o repositório (está no `.gitignore`).

### 3. Endereço do site

Crie um VirtualHost no WAMP apontando para a pasta do projeto (ícone do WAMP → *Your VirtualHosts* → *VirtualHost Management*), por exemplo com o nome `coletafacil`, e acesse `http://coletafacil/`.

O endereço base é detectado sozinho. O projeto também abre em subpasta (`http://localhost/TrashProject/`), mas o modo app (PWA) e a página offline só funcionam corretamente na raiz de um VirtualHost.

## Como rodar

**Pelo Apache (uso normal):** com o WAMP ligado, acesse `http://coletafacil/`.

**Pelo `rodar-local.bat`:** dê dois cliques no arquivo. Ele abre o projeto em `http://localhost:8080` usando o PHP do WAMP (o WAMP precisa estar ligado por causa do MySQL). Use este modo para testar o botão **"Usar minha localização"** das ocorrências: o navegador só libera a geolocalização em HTTPS ou em `localhost`, e por isso ela não funciona pelo VirtualHost em `http://`.

O `serve-local.php` é o roteador usado pelo `rodar-local.bat` (faz o papel do `.htaccess`). Não é preciso abri-lo.

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

O **Morador Teste** já tem ocorrências registradas, pontos e dois perfis na Área Kids. O painel administrativo fica em `/admin` e só abre com o usuário Administrador.

Essas senhas servem apenas para testes. Troque-as se o sistema for publicado.

## Estrutura

```
index.php              Front Controller: toda rota passa por aqui
.htaccess              Redireciona as rotas para o index.php (Apache)
rodar-local.bat        Servidor local em http://localhost:8080
serve-local.php        Roteador do servidor local
sw.js, manifest.json   PWA (cache offline e instalação como app)
app/
  config/              Config.php (configurações gerais) e config.local.php (senha do banco)
  controller/          Um controller por área: Home, Usuario, Coleta, Ponto, Ocorrencia, Admin, Busca
  model/               Acesso ao banco, um model por entidade
  view/                Páginas; comuns/ tem cabeçalho e rodapé; admin/ tem o painel
  helper/              Funções de apoio (autenticação, formatação, URLs)
  library/             Request (rotas) e Database (PDO)
assets/                CSS, JavaScript e imagens
modelo/                modeloColetaFacil.sql (estrutura), seed.sql (dados), modelo ER do Workbench
docs/apresentacao/     Material de apresentação do projeto
```

As rotas seguem o padrão `/{controller}/{metodo}`. Exemplos: `/coleta`, `/ponto`, `/ocorrencia`, `/home/kids`, `/admin/ocorrencias`.

## Observações

- **Fotos das ocorrências** ficam gravadas no banco (tabela `ocorrencia_foto`), sem os metadados EXIF/GPS da câmera, e só o administrador consegue abri-las.
- **Cache do navegador:** ao alterar CSS ou JavaScript, aumente o `ASSET_VER` em `app/config/Config.php`. Se o site parecer desatualizado, use Ctrl+F5.
- **Cronograma de coleta:** dados do [DEMSUR](https://www.demsur.com.br/pagina/107/cronograma-coleta), consultados em outubro de 2026.
