<?php

/**
 * Classe base para todos os Controllers.
 *
 * Disponibiliza a requisicao atual ($this->request), o carregamento de
 * views, helpers e models, seguindo o padrao do projeto de referencia.
 */
class BaseController
{
    protected Request $request;

    public function __construct()
    {
        $this->request = new Request($_GET['url'] ?? '');
        $this->helper('Utilits');
    }

    /**
     * Renderiza uma view de app/view, injetando $data como variaveis locais.
     * A action atual fica sempre disponivel como $action.
     */
    public function view(string $view, array $data = []): void
    {
        $data['action'] = $this->request->getAction();
        extract($data);

        $arquivo = 'app/view/' . $view . '.php';
        if (file_exists($arquivo)) {
            require_once $arquivo;
        } else {
            http_response_code(404);
            require_once 'app/view/404.php';
        }
    }

    /**
     * Carrega um ou mais helpers de app/helper.
     *
     * @param string|array $nomeHelper
     */
    public function helper(string|array $nomeHelper): void
    {
        foreach ((array) $nomeHelper as $nome) {
            $arquivo = 'app/helper/' . ucfirst($nome) . '.php';
            if (file_exists($arquivo)) {
                require_once $arquivo;
            }
        }
    }

    /**
     * Instancia um Model de app/model (ex.: model('Usuario') => UsuarioModel).
     */
    public function model(string $nomeModel): object
    {
        $nomeModel = ucfirst($nomeModel) . 'Model';
        if (file_exists('app/model/' . $nomeModel . '.php')) {
            require_once 'app/model/' . $nomeModel . '.php';
            return new $nomeModel();
        }
        return (object) null;
    }
}
