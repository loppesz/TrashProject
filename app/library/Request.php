<?php

/**
 * Representa a requisicao HTTP decomposta pelo Front Controller.
 *
 * A rota segue o padrao /{controller}/{metodo}/{action}/{id}, onde:
 *  - controller: classe em app/controller (ex.: Home)
 *  - metodo:     metodo publico do controller (ex.: index)
 *  - action:     primeiro parametro extra, exposto as views como $action
 *  - id:         segundo parametro extra, tipicamente um identificador numerico
 */
class Request
{
    private string $controller = 'Home';
    private string $metodo = 'index';
    private string $action = '';
    private string $id = '';
    private array $params = [];

    public function __construct(string $url = '')
    {
        $this->resolve($url);
    }

    /**
     * Normaliza a URL recebida do .htaccess e extrai os segmentos da rota.
     * Suporta execucao em VirtualHost (raiz) e em subpasta.
     */
    private function resolve(string $url): void
    {
        // Remove query string e barras extras das pontas.
        $url = trim(parse_url($url, PHP_URL_PATH) ?? '', '/');

        if ($url === '') {
            return;
        }

        $segmentos = array_values(array_filter(explode('/', $url), static fn($s) => $s !== ''));

        if (isset($segmentos[0])) {
            $this->controller = ucfirst(strtolower($segmentos[0]));
        }
        if (isset($segmentos[1])) {
            $this->metodo = $segmentos[1];
        }
        if (isset($segmentos[2])) {
            $this->action = $segmentos[2];
        }
        if (isset($segmentos[3])) {
            $this->id = $segmentos[3];
        }

        // Qualquer segmento adicional vira parametro posicional.
        $this->params = array_slice($segmentos, 2);
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function getMetodo(): string
    {
        return $this->metodo;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    /** Retorna um valor de $_POST com fallback. */
    public function post(string $campo, mixed $default = ''): mixed
    {
        return $_POST[$campo] ?? $default;
    }

    /** Retorna um valor de $_GET com fallback. */
    public function get(string $campo, mixed $default = ''): mixed
    {
        return $_GET[$campo] ?? $default;
    }

    /** True se a requisicao atual for POST. */
    public function isPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }
}
