<?php

/**
 * Controller da area publica institucional:
 * home, sobre, materiais, recompensas e kids.
 */
class Home extends BaseController
{
    public function index(): void
    {
        $this->view('home', [
            'tituloPagina' => 'ColetaFácil – Coleta Seletiva Muriaé-MG',
            'menu'         => 'home',
        ]);
    }

    public function sobre(): void
    {
        $this->view('sobre', [
            'tituloPagina' => 'Quem somos – ColetaFácil',
            'menu'         => 'sobre',
        ]);
    }

    public function materiais(): void
    {
        $this->view('materiais', [
            'tituloPagina' => 'Guia de Materiais – ColetaFácil',
            'menu'         => 'materiais',
        ]);
    }

    public function recompensas(): void
    {
        $this->view('recompensas', [
            'tituloPagina' => 'Recompensas – ColetaFácil',
            'menu'         => 'recompensas',
        ]);
    }

    public function kids(): void
    {
        $this->view('kids', [
            'tituloPagina' => 'Área Kids – ColetaFácil',
            'menu'         => 'kids',
        ]);
    }

    public function offline(): void
    {
        require_once 'app/view/offline.php';
    }
}
