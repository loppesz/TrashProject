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
require_once 'app/helper/Auth.php';
require_once 'app/controller/BaseController.php';
require_once 'app/model/BaseModel.php';

$request = new Request($_GET['url'] ?? '');

$controllerNome = $request->getController();
$metodoNome     = $request->getMetodo();
$id             = $request->getId();

$arquivoController = 'app/controller/' . $controllerNome . '.php';

if (!file_exists($arquivoController)) {
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

if (!method_exists($controller, $metodoNome)) {
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
