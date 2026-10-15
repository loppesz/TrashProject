<?php

/**
 * Controller do painel administrativo.
 *
 * Todas as acoes exigem usuario autenticado do tipo ADMINISTRADOR
 * (guard aplicado no construtor via auth_require(true)).
 */
class Admin extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        auth_require(true);
    }

    public function index(): void
    {
        $this->view('admin/dashboard', [
            'tituloPagina' => 'Dashboard',
            'adminMenu'    => 'dashboard',
        ]);
    }

    public function ocorrencias(): void
    {
        $this->view('admin/ocorrencias', [
            'tituloPagina' => 'Ocorrências',
            'adminMenu'    => 'ocorrencias',
        ]);
    }

    public function materiais(): void
    {
        $this->view('admin/materiais', [
            'tituloPagina' => 'Materiais',
            'adminMenu'    => 'materiais',
        ]);
    }

    public function coleta(): void
    {
        $this->view('admin/coleta', [
            'tituloPagina' => 'Coleta',
            'adminMenu'    => 'coleta',
        ]);
    }

    public function pontos(): void
    {
        $this->view('admin/pontos', [
            'tituloPagina' => 'Pontos de Descarte',
            'adminMenu'    => 'pontos',
        ]);
    }

    public function indicadores(): void
    {
        $this->view('admin/indicadores', [
            'tituloPagina' => 'Indicadores',
            'adminMenu'    => 'indicadores',
        ]);
    }
}
