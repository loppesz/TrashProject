<?php

/**
 * Controller da busca global (bairros, materiais e pontos de descarte).
 */
class Busca extends BaseController
{
    public function index(): void
    {
        $this->view('busca', [
            'tituloPagina' => 'Busca – ColetaFácil',
            'descricao'    => 'Busque bairros, materiais e pontos de descarte em Muriaé-MG.',
            'cssPagina'    => ['busca.css'],
            'jsPagina'     => ['busca.js'],
            'menu'         => '',
        ]);
    }
}
