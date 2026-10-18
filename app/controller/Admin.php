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
        $ocModel   = $this->model('Ocorrencia');
        $contagens = $ocModel->contagemPorStatus();
        $recentes  = array_slice($ocModel->listaAdmin(5), 0, 5);

        $totalOc     = $ocModel->total();
        $resolvidas  = $contagens['RESOLVIDA'] ?? 0;
        $totalMat    = count($this->model('Material')->lista());
        $totalPontos = count($this->model('Ponto')->lista());

        $this->view('admin/dashboard', [
            'tituloPagina'  => 'Dashboard',
            'adminMenu'     => 'dashboard',
            'totalOc'       => $totalOc,
            'resolvidas'    => $resolvidas,
            'totalMat'      => $totalMat,
            'totalPontos'   => $totalPontos,
            'recentes'      => $recentes,
            'porBairro'     => $ocModel->contagemPorBairro(),
            'porTipo'       => $ocModel->contagemPorTipo(),
        ]);
    }

    public function ocorrencias(): void
    {
        $ocModel = $this->model('Ocorrencia');

        $this->view('admin/ocorrencias', [
            'tituloPagina' => 'Ocorrências',
            'adminMenu'    => 'ocorrencias',
            'ocorrencias'  => $ocModel->listaAdmin(),
            'total'        => $ocModel->total(),
            'sucesso'      => $_GET['ok'] ?? '',
        ]);
    }

    /**
     * Processa a mudanca de status de uma ocorrencia (POST).
     */
    public function atualizarStatus(): void
    {
        if (!$this->request->isPost()) {
            header('Location: ' . baseUrl('admin/ocorrencias'));
            exit;
        }

        $id         = (int) $this->request->post('id_ocorrencia');
        $status     = (string) $this->request->post('status');
        $observacao = trim((string) $this->request->post('observacao'));
        $admin      = auth_user();

        if ($id > 0 && $admin) {
            $this->model('Ocorrencia')->atualizarStatus($id, $status, $observacao, (int) $admin['id_usuario']);
        }

        header('Location: ' . baseUrl('admin/ocorrencias') . '?ok=1');
        exit;
    }

    public function materiais(): void
    {
        $this->view('admin/materiais', [
            'tituloPagina' => 'Materiais',
            'adminMenu'    => 'materiais',
            'materiais'    => $this->model('Material')->listaCompleta(),
        ]);
    }

    public function coleta(): void
    {
        $this->view('admin/coleta', [
            'tituloPagina' => 'Coleta',
            'adminMenu'    => 'coleta',
            'bairros'      => $this->model('Coleta')->porBairro(),
        ]);
    }

    public function pontos(): void
    {
        $this->view('admin/pontos', [
            'tituloPagina' => 'Pontos de Descarte',
            'adminMenu'    => 'pontos',
            'pontos'       => $this->model('Ponto')->listaCompleta(),
        ]);
    }

    public function indicadores(): void
    {
        $ocModel = $this->model('Ocorrencia');
        $contagens = $ocModel->contagemPorStatus();
        $total = $ocModel->total();
        $resolvidas = $contagens['RESOLVIDA'] ?? 0;

        $this->view('admin/indicadores', [
            'tituloPagina' => 'Indicadores',
            'adminMenu'    => 'indicadores',
            'totalOc'      => $total,
            'resolvidas'   => $resolvidas,
            'emAndamento'  => ($contagens['EM_ANDAMENTO'] ?? 0) + ($contagens['EM_ANALISE'] ?? 0),
            'taxa'         => $total > 0 ? (int) round(($resolvidas / $total) * 100) : 0,
            'porBairro'    => $ocModel->contagemPorBairro(),
            'porTipo'      => $ocModel->contagemPorTipo(),
        ]);
    }
}
