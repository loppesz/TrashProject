<?php

/**
 * Controller da consulta de coleta seletiva por bairro.
 */
class Coleta extends BaseController
{
    public function index(): void
    {
        $this->view('coleta', [
            'tituloPagina' => 'Consultar Coleta – ColetaFácil',
            'descricao'    => 'Consulte o calendário de coleta seletiva em Muriaé-MG. Veja os dias e horários de coleta por bairro.',
            'cssPagina'    => ['coleta.css'],
            'jsPagina'     => ['coleta.js'],
            'menu'         => 'coleta',
        ]);
    }
}
