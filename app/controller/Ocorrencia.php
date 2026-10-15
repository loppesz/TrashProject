<?php

/**
 * Controller do registro e consulta de ocorrencias ambientais.
 */
class Ocorrencia extends BaseController
{
    public function index(): void
    {
        $this->view('ocorrencias', [
            'tituloPagina' => 'Ocorrências – ColetaFácil',
            'descricao'    => 'Registre descartes irregulares e problemas na coleta em Muriaé-MG. Acompanhe o status e ganhe pontos de recompensa.',
            'cssPagina'    => ['ocorrencias.css'],
            'jsPagina'     => ['ocorrencias.js'],
            'menu'         => 'ocorrencias',
        ]);
    }
}
