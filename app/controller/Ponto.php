<?php

/**
 * Controller dos pontos de descarte (locais de entrega voluntaria).
 */
class Ponto extends BaseController
{
    public function index(): void
    {
        $pontos = $this->model('Ponto')->listaCompleta();

        $this->view('pontos', [
            'tituloPagina' => 'Pontos de Descarte – ColetaFácil',
            'descricao'    => 'Encontre pontos de descarte de materiais especiais em Muriaé-MG: pilhas, eletrônicos, óleo, medicamentos e mais.',
            'cssPagina'    => ['pontos.css'],
            'jsPagina'     => ['pontos.js', 'mapa.js'],
            'menu'         => 'pontos',
            'pontos'       => $pontos,
        ]);
    }
}
