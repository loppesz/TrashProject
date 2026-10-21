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
        $totalMat    = $this->model('Material')->totalAtivos();
        $totalPontos = $this->model('Ponto')->totalAtivos();
        $sugestoesPendentes = $this->model('Sugestao')->totalPendentes();

        $this->view('admin/dashboard', [
            'tituloPagina'  => 'Dashboard',
            'adminMenu'     => 'dashboard',
            'totalOc'       => $totalOc,
            'resolvidas'    => $resolvidas,
            'totalMat'      => $totalMat,
            'totalPontos'   => $totalPontos,
            'sugestoesPendentes' => $sugestoesPendentes,
            'recentes'      => $recentes,
            'porBairro'     => $ocModel->contagemPorBairro(),
            'porTipo'       => $ocModel->contagemPorTipo(),
            'porStatus'     => $contagens,
            'locaisFrequentes' => $ocModel->locaisDescarteIrregular(10),
        ]);
    }

    public function ocorrencias(): void
    {
        $ocModel = $this->model('Ocorrencia');

        // Anexa as fotos (URLs) de cada ocorrencia para exibir no painel.
        $ocorrencias = $ocModel->listaAdmin();
        foreach ($ocorrencias as &$o) {
            $o['fotos'] = array_column($ocModel->fotos((int) $o['id_ocorrencia']), 'url_arquivo');
        }
        unset($o);

        // Abas pelo status salvo no banco: o que é resolvido/arquivado sai de "Em aberto"
        // e continua fora mesmo depois de recarregar a página.
        $abasValidas = ['abertas', 'resolvidas', 'arquivadas'];
        $abaAtual    = in_array($_GET['aba'] ?? '', $abasValidas, true) ? $_GET['aba'] : 'abertas';
        $movidaPara  = in_array($_GET['para'] ?? '', $abasValidas, true) ? $_GET['para'] : '';

        $this->view('admin/ocorrencias', [
            'tituloPagina' => 'Ocorrências',
            'adminMenu'    => 'ocorrencias',
            'ocorrencias'  => $ocorrencias,
            'total'        => $ocModel->total(),
            'sucesso'      => $_GET['ok'] ?? '',
            'abaAtual'     => $abaAtual,
            'movidaPara'   => $movidaPara,
            'ocAlterada'   => (int) ($_GET['oc'] ?? 0),
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
        // A troca rápida pela lista não envia "observacao": nesse caso a nota atual é mantida.
        $observacao = array_key_exists('observacao', $_POST) ? trim((string) $_POST['observacao']) : null;
        $admin      = auth_user();

        if ($id > 0 && $admin) {
            $ocModel = $this->model('Ocorrencia');
            $anterior = $ocModel->getById($id);
            $ocModel->atualizarStatus($id, $status, $observacao, (int) $admin['id_usuario']);

            // Pontos de confirmacao: so quando a equipe resolve (ocorrencia real).
            // creditarAcaoUnica garante no maximo um credito de cada tipo por ocorrencia.
            // O admin não pontua resolvendo a própria ocorrência.
            if ($status === 'RESOLVIDA' && ($anterior['status'] ?? '') !== 'RESOLVIDA'
                && !empty($anterior['id_usuario']) && (int) $anterior['id_usuario'] !== (int) $admin['id_usuario']) {
                $pontos    = $this->model('Pontos');
                $idDono    = (int) $anterior['id_usuario'];
                $protocolo = $anterior['protocolo'] ?? '';

                $pontos->creditarAcaoUnica($idDono, 'OCORRENCIA_RESOLVIDA', $id, 'Ocorrência ' . $protocolo . ' confirmada e resolvida');

                // Foto de evidencia: pontua apenas se a ocorrencia foi confirmada.
                if (!empty($ocModel->fotos($id))) {
                    $pontos->creditarAcaoUnica($idDono, 'FOTO_APROVADA', $id, 'Foto aprovada na ocorrência ' . $protocolo);
                }
            }
        }

        // Volta para a aba em que o admin estava e informa para qual aba a ocorrência foi.
        $abasValidas = ['abertas', 'resolvidas', 'arquivadas'];
        $abaOrigem   = in_array($_POST['aba'] ?? '', $abasValidas, true) ? $_POST['aba'] : 'abertas';
        $destino = baseUrl('admin/ocorrencias') . '?ok=1&aba=' . $abaOrigem;
        if ($id > 0) {
            $destino .= '&oc=' . $id . '&para=' . abaAdminOcorrencia($status);
        }
        header('Location: ' . $destino);
        exit;
    }

    // =================================================================
    // MATERIAIS (CRUD)
    // =================================================================

    public function materiais(): void
    {
        $this->view('admin/materiais', [
            'tituloPagina' => 'Materiais',
            'adminMenu'    => 'materiais',
            'materiais'    => $this->model('Material')->listaCompleta(),
            'msg'          => $_GET['msg'] ?? '',
            'erro'         => $_GET['erro'] ?? '',
        ]);
    }

    public function salvarMaterial(): void
    {
        if (!$this->request->isPost()) {
            header('Location: ' . baseUrl('admin/materiais'));
            exit;
        }

        $model = $this->model('Material');
        $id    = (int) $this->request->post('id_material');

        $dados = [
            'nome'             => trim((string) $this->request->post('nome')),
            'descricao'        => trim((string) $this->request->post('descricao')),
            'status_coleta'    => (string) $this->request->post('status_coleta', 'ACEITO'),
            'nivel_risco'      => (string) $this->request->post('nivel_risco', 'BAIXO'),
            'icone'            => trim((string) $this->request->post('icone')),
            'cor_card'         => trim((string) $this->request->post('cor_card')),
            'preparo_label'    => trim((string) $this->request->post('preparo_label')),
            'alerta_seguranca' => trim((string) $this->request->post('alerta_seguranca')),
        ];

        if ($dados['nome'] === '') {
            header('Location: ' . baseUrl('admin/materiais') . '?erro=' . rawurlencode('Informe o nome do material.'));
            exit;
        }

        if ($id > 0) {
            $model->atualiza($id, $dados);
        } else {
            $model->cria($dados);
        }

        header('Location: ' . baseUrl('admin/materiais') . '?msg=' . rawurlencode('Material salvo com sucesso.'));
        exit;
    }

    public function excluirMaterial(): void
    {
        if ($this->request->isPost()) {
            $id = (int) $this->request->post('id_material');
            if ($id > 0) {
                $ok = $this->model('Material')->exclui($id);
                $q = $ok > 0
                    ? '?msg=' . rawurlencode('Material excluído.')
                    : '?erro=' . rawurlencode('Material em uso; não pode ser excluído.');
                header('Location: ' . baseUrl('admin/materiais') . $q);
                exit;
            }
        }
        header('Location: ' . baseUrl('admin/materiais'));
        exit;
    }

    // =================================================================
    // COLETA (CRUD por bairro)
    // =================================================================

    public function coleta(): void
    {
        $coletaModel = $this->model('Coleta');
        $bairros = $coletaModel->porBairro();
        // anexa ids de materiais de cada bairro (para marcar checkboxes na edicao)
        $matModel = $this->model('Material');
        $mapaMat = [];
        foreach ($matModel->lista() as $m) {
            $mapaMat[$m['nome']] = (int) $m['id_material'];
        }
        foreach ($bairros as &$b) {
            $ids = [];
            foreach (($b['materiais'] ?? []) as $nome) {
                if (isset($mapaMat[$nome])) {
                    $ids[] = $mapaMat[$nome];
                }
            }
            $b['materiais_ids'] = $ids;
        }
        unset($b);

        $this->view('admin/coleta', [
            'tituloPagina'  => 'Coleta',
            'adminMenu'     => 'coleta',
            'bairros'       => $bairros,
            'listaBairros'  => $this->model('Bairro')->listaAtivos(),
            'materiaisTodos' => $matModel->lista(),
            'msg'           => $_GET['msg'] ?? '',
            'erro'          => $_GET['erro'] ?? '',
        ]);
    }

    public function salvarColeta(): void
    {
        if (!$this->request->isPost()) {
            header('Location: ' . baseUrl('admin/coleta'));
            exit;
        }

        $idBairro  = (int) $this->request->post('id_bairro');
        $dias      = (array) $this->request->post('dias', []);
        $inicio    = (string) $this->request->post('inicio', '07:00');
        $fim       = (string) $this->request->post('fim', '12:00');
        $equipe    = trim((string) $this->request->post('equipe'));
        $materiais = (array) $this->request->post('materiais', []);

        if ($idBairro <= 0 || empty($dias)) {
            header('Location: ' . baseUrl('admin/coleta') . '?erro=' . rawurlencode('Selecione o bairro e ao menos um dia.'));
            exit;
        }

        $this->model('Coleta')->salvaPorBairro([
            'id_bairro' => $idBairro,
            'dias'      => $dias,
            'inicio'    => $inicio,
            'fim'       => $fim,
            'equipe'    => $equipe,
            'materiais' => $materiais,
        ]);

        header('Location: ' . baseUrl('admin/coleta') . '?msg=' . rawurlencode('Coleta salva com sucesso.'));
        exit;
    }

    public function excluirColeta(): void
    {
        if ($this->request->isPost()) {
            $idBairro = (int) $this->request->post('id_bairro');
            if ($idBairro > 0) {
                $this->model('Coleta')->removePorBairro($idBairro);
                header('Location: ' . baseUrl('admin/coleta') . '?msg=' . rawurlencode('Coleta removida.'));
                exit;
            }
        }
        header('Location: ' . baseUrl('admin/coleta'));
        exit;
    }

    // =================================================================
    // PONTOS (CRUD)
    // =================================================================

    public function pontos(): void
    {
        $pontoModel = $this->model('Ponto');
        $pontos = $pontoModel->listaCompleta();
        // anexa os ids dos materiais de cada ponto (para marcar os checkboxes na edicao)
        foreach ($pontos as &$p) {
            $p['materiais_ids'] = $pontoModel->idsMateriais((int) $p['id_ponto']);
        }
        unset($p);

        $this->view('admin/pontos', [
            'tituloPagina'   => 'Pontos de Descarte',
            'adminMenu'      => 'pontos',
            'pontos'         => $pontos,
            'listaBairros'   => $this->model('Bairro')->listaAtivos(),
            'materiaisTodos' => $this->model('Material')->lista(),
            'msg'            => $_GET['msg'] ?? '',
            'erro'           => $_GET['erro'] ?? '',
        ]);
    }

    public function salvarPonto(): void
    {
        if (!$this->request->isPost()) {
            header('Location: ' . baseUrl('admin/pontos'));
            exit;
        }

        $model = $this->model('Ponto');
        $id    = (int) $this->request->post('id_ponto');

        $dados = [
            'id_bairro'             => (int) $this->request->post('id_bairro'),
            'nome'                  => trim((string) $this->request->post('nome')),
            'endereco'              => trim((string) $this->request->post('endereco')),
            'horario_funcionamento' => trim((string) $this->request->post('horario_funcionamento')),
            'status_funcionamento'  => (string) $this->request->post('status_funcionamento', 'ABERTO'),
            'materiais'             => (array) $this->request->post('materiais', []),
        ];

        if ($dados['nome'] === '' || $dados['endereco'] === '' || $dados['id_bairro'] <= 0) {
            header('Location: ' . baseUrl('admin/pontos') . '?erro=' . rawurlencode('Preencha nome, endereço e bairro.'));
            exit;
        }

        if ($id > 0) {
            $model->atualiza($id, $dados);
        } else {
            $model->cria($dados);
        }

        header('Location: ' . baseUrl('admin/pontos') . '?msg=' . rawurlencode('Ponto salvo com sucesso.'));
        exit;
    }

    public function excluirPonto(): void
    {
        if ($this->request->isPost()) {
            $id = (int) $this->request->post('id_ponto');
            if ($id > 0) {
                $this->model('Ponto')->exclui($id);
                header('Location: ' . baseUrl('admin/pontos') . '?msg=' . rawurlencode('Ponto excluído.'));
                exit;
            }
        }
        header('Location: ' . baseUrl('admin/pontos'));
        exit;
    }

    // =================================================================
    // SUGESTOES DE PONTOS (analise)
    // =================================================================

    public function sugestoes(): void
    {
        $model = $this->model('Sugestao');

        $this->view('admin/sugestoes', [
            'tituloPagina' => 'Sugestões de Pontos',
            'adminMenu'    => 'sugestoes',
            'sugestoes'    => $model->listaTodas(),
            'contagens'    => $model->contagemPorStatus(),
            'msg'          => $_GET['msg'] ?? '',
            'erro'         => $_GET['erro'] ?? '',
        ]);
    }

    /**
     * Aprova ou recusa uma sugestao de ponto (POST).
     */
    public function atualizarSugestao(): void
    {
        if (!$this->request->isPost()) {
            header('Location: ' . baseUrl('admin/sugestoes'));
            exit;
        }

        $id     = (int) $this->request->post('id_sugestao');
        $status = (string) $this->request->post('status');
        $admin  = auth_user();

        // Decisão direta: aprovar ou recusar (não existe mais o estado "em análise").
        if ($id > 0 && $admin && in_array($status, ['APROVADA', 'RECUSADA'], true)) {
            $this->model('Sugestao')->atualizarStatus($id, $status, (int) $admin['id_usuario']);
            $rotulo = ['APROVADA' => 'aprovada', 'RECUSADA' => 'recusada'][$status];
            header('Location: ' . baseUrl('admin/sugestoes') . '?msg=' . rawurlencode('Sugestão ' . $rotulo . '.'));
            exit;
        }

        header('Location: ' . baseUrl('admin/sugestoes'));
        exit;
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
            'porStatus'    => $contagens,
            'locaisFrequentes' => $ocModel->locaisDescarteIrregular(10),
        ]);
    }
}
