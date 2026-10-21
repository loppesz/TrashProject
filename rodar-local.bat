@echo off
REM ============================================================
REM  ColetaFacil - servidor local de desenvolvimento
REM
REM  Abre o projeto em http://localhost:8080
REM  Use este modo quando precisar de recursos que exigem
REM  "contexto seguro" do navegador, como a geolocalizacao
REM  (botao "Usar minha localizacao"), que NAO funciona pelo
REM  VirtualHost http://coletafacil.
REM
REM  Requisito: WAMP instalado (usa o PHP do WAMP e o MySQL na 3306).
REM  Para encerrar: feche esta janela ou pressione Ctrl+C.
REM ============================================================

REM Usa o PHP 8.3 instalado no WAMP (pega a versao 8.3.x disponivel).
set "PHP="
for /d %%D in ("C:\wamp64\bin\php\php8.3*") do set "PHP=%%D\php.exe"

if not defined PHP set "PHP=C:\wamp64\bin\php\php8.3\php.exe"
if not exist "%PHP%" (
  echo [ERRO] PHP do WAMP nao encontrado em:
  echo        %PHP%
  echo Ajuste o caminho no topo deste arquivo .bat conforme a sua versao do PHP.
  pause
  exit /b 1
)

echo.
echo  ColetaFacil rodando em:  http://localhost:8080
echo  (a geolocalizacao funciona neste endereco)
echo.
echo  Mantenha esta janela aberta. Feche-a para encerrar o servidor.
echo.

start "" "http://localhost:8080"
"%PHP%" -S 127.0.0.1:8080 serve-local.php
