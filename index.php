<?php

/**
 * Front Controller do ColetaFacil.
 *
 * Toda requisicao que nao corresponda a um arquivo/diretorio real cai aqui
 * (ver .htaccess). A rota segue o padrao /{controller}/{metodo}/{action}/{id}.
 * Rota vazia => Home::index().
 */

require_once 'app/config/Config.php';
require_once 'app/library/Request.php';
require_once 'app/helper/Utilits.php';
require_once 'app/helper/Seguranca.php';
require_once 'app/helper/Auth.php';
// Funcoes das views (baseUrl, themeHeadScript...). Carregado aqui tambem para a
// pagina 404 funcionar quando o controller nem chega a ser criado.
require_once 'app/helper/Utilits.php';
require_once 'app/controller/BaseController.php';
require_once 'app/model/BaseModel.php';

// Cabecalhos de seguranca em todas as respostas dinamicas.
seguranca_cabecalhos();

// CSRF: todo POST precisa do token da sessao (campo _csrf ou X-CSRF-Token).
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && !csrf_valido()) {
    csrf_bloquear();
}

$request = new Request($_GET['url'] ?? '');

$controllerNome = $request->getController();
$metodoNome     = $request->getMetodo();
$id             = $request->getId();

// So aceita nomes simples de controller/metodo (letras e numeros).
if (!preg_match('/^[A-Za-z][A-Za-z0-9]*$/', $controllerNome) || !preg_match('/^[A-Za-z][A-Za-z0-9]*$/', $metodoNome)) {
    http_response_code(404);
    require_once 'app/view/404.php';
    exit;
}

$arquivoController = 'app/controller/' . $controllerNome . '.php';

if ($controllerNome === 'BaseController' || !file_exists($arquivoController)) {
    http_response_code(404);
    require_once 'app/view/404.php';
    exit;
}

require_once $arquivoController;

if (!class_exists($controllerNome)) {
    http_response_code(404);
    require_once 'app/view/404.php';
    exit;
}

$controller = new $controllerNome();

// Apenas metodos publicos declarados no proprio controller podem virar rota
// (nada de __construct, helpers herdados de BaseController ou metodos privados).
$rotaValida = false;
if (method_exists($controller, $metodoNome)) {
    $ref = new ReflectionMethod($controller, $metodoNome);
    $rotaValida = $ref->isPublic() && !$ref->isStatic() && !str_starts_with($metodoNome, '__')
        && $ref->getDeclaringClass()->getName() === $controllerNome;
}
if (!$rotaValida) {
    http_response_code(404);
    require_once 'app/view/404.php';
    exit;
}

// Passa o id como argumento quando presente (ex.: /ponto/detalhe/5).
if ($id !== '') {
    $controller->{$metodoNome}($id);
} else {
    $controller->{$metodoNome}();
}
