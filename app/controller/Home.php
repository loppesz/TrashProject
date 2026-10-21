<?php

/**
 * Controller da area publica institucional:
 * home, sobre, materiais, recompensas e kids.
 */
class Home extends BaseController
{
    public function index(): void
    {
        $ocorrencias = $this->model('Ocorrencia');
        $materiais   = $this->model('Material');
        $pontos      = $this->model('Ponto');
        $bairros     = $this->model('Bairro');
        $programa    = $this->model('Pontos');

        $metricas = $ocorrencias->metricasPublicas();

        $this->view('home', [
            'tituloPagina' => 'ColetaFácil – Coleta Seletiva Muriaé-MG',
            'menu'         => 'home',
            'metricas'     => [
                'resolvidas'    => $metricas['resolvidas'],
                'pontos'        => $pontos->totalAtivos(),
                'participantes' => $programa->totalParticipantes(),
                'bairros'       => $bairros->totalAtendidos(),
            ],
            'totalMateriais' => $materiais->totalAtivos(),
            'rankingHome'    => $programa->ranking(3),
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
        $materiais = $this->model('Material')->listaCompleta();

        $this->view('materiais', [
            'tituloPagina' => 'Guia de Materiais – ColetaFácil',
            'menu'         => 'materiais',
            'materiais'    => $materiais,
        ]);
    }

    public function recompensas(): void
    {
        $recModel = $this->model('Recompensa');
        $pontos   = $this->model('Pontos');
        $usuario  = auth_user();

        $saldo = $usuario ? $pontos->saldo((int) $usuario['id_usuario']) : 0;

        $this->view('recompensas', [
            'tituloPagina' => 'Recompensas – ColetaFácil',
            'menu'         => 'recompensas',
            'recompensas'  => $recModel->listaAtivas(),
            'ranking'      => $pontos->ranking(10),
            'usuario'      => $usuario,
            'saldo'        => $saldo,
            'nivel'        => PontosModel::nivel($saldo),
            'regrasPontos' => PontosModel::regras(),
            'msgResgate'   => $_GET['resgate'] ?? '',
            'codigoResgate' => $_GET['codigo'] ?? '',
            'erroResgate'  => $_GET['erro'] ?? '',
        ]);
    }

    /**
     * Processa o resgate de uma recompensa (POST). Valida saldo,
     * grava o resgate e debita os pontos.
     */
    public function resgatar(): void
    {
        if (!$this->request->isPost()) {
            header('Location: ' . baseUrl('home/recompensas'));
            exit;
        }

        $usuario = auth_user();
        if (!$usuario) {
            header('Location: ' . baseUrl('usuario/login') . '?next=' . rawurlencode(baseUrl('home/recompensas')));
            exit;
        }

        $idRec   = (int) $this->request->post('id_recompensa');
        $recModel = $this->model('Recompensa');

        $rec = $recModel->porId($idRec);
        $base = baseUrl('home/recompensas');

        if (empty($rec) || !$rec['disponivel']) {
            header('Location: ' . $base . '?erro=' . rawurlencode('Recompensa indisponível.'));
            exit;
        }

        try {
            // Saldo, estoque, resgate e débito na mesma transação.
            $resgate = $recModel->resgatarComDebito((int) $usuario['id_usuario'], $rec);
            header('Location: ' . $base . '?resgate=ok&codigo=' . rawurlencode($resgate['codigo']));
            exit;
        } catch (DomainException $e) {
            header('Location: ' . $base . '?erro=' . rawurlencode($e->getMessage()));
            exit;
        } catch (Throwable $e) {
            header('Location: ' . $base . '?erro=' . rawurlencode('Não foi possível concluir o resgate.'));
            exit;
        }
    }

    public function kids(): void
    {
        $usuario = auth_user();
        $perfis  = $usuario ? $this->model('PerfilKids')->porUsuario((int) $usuario['id_usuario']) : [];

        $this->view('kids', [
            'tituloPagina' => 'Área Kids – ColetaFácil',
            'menu'         => 'kids',
            'usuario'      => $usuario,
            'perfisKids'   => $perfis,
        ]);
    }

    public function offline(): void
    {
        require_once 'app/view/offline.php';
    }

    // =================================================================
    // Area Kids — persistencia de perfis (JSON, usado pelo kids.js)
    // =================================================================

    /** Lista os perfis kids do usuario logado (JSON). */
    public function kidsPerfis(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $usuario = auth_user();
        if (!$usuario) {
            echo json_encode(['logado' => false, 'perfis' => []]);
            exit;
        }
        $perfis = $this->model('PerfilKids')->porUsuario((int) $usuario['id_usuario']);
        echo json_encode(['logado' => true, 'perfis' => $perfis], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Salva o progresso de um perfil kids do usuario logado (JSON).
     * Se nao vier id_perfil, cria um novo perfil.
     */
    public function kidsSalvar(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $usuario = auth_user();
        if (!$usuario) {
            echo json_encode(['ok' => false, 'motivo' => 'nao_logado']);
            exit;
        }
        if (!$this->request->isPost()) {
            echo json_encode(['ok' => false, 'motivo' => 'metodo']);
            exit;
        }

        $idUsuario = (int) $usuario['id_usuario'];
        $idPerfil  = (int) $this->request->post('id_perfil');
        $apelido   = (string) $this->request->post('apelido', 'Eco Herói');
        $pontos    = (int) $this->request->post('pontos');
        $jogos     = (int) $this->request->post('jogos');

        $model = $this->model('PerfilKids');

        if ($idPerfil > 0 && !empty($model->doUsuario($idPerfil, $idUsuario))) {
            $model->atualiza($idPerfil, $idUsuario, $apelido, $pontos, $jogos);
        } else {
            $idPerfil = $model->cria($idUsuario, $apelido, $pontos, $jogos);
        }

        echo json_encode(['ok' => true, 'id_perfil' => $idPerfil], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
