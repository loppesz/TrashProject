<?php

/**
 * Controller do registro e consulta de ocorrencias ambientais.
 */
class Ocorrencia extends BaseController
{
    public function index(): void
    {
        $model = $this->model('Ocorrencia');

        $historico = $model->historicoPublico();

        // Metricas a partir do banco.
        $registradas = count($historico);
        $resolvidas  = 0;
        foreach ($historico as $o) {
            if ($o['status'] === 'RESOLVIDA') {
                $resolvidas++;
            }
        }
        $taxa = $registradas > 0 ? (int) round(($resolvidas / $registradas) * 100) : 0;

        // Mensagens de retorno do POST (via query string apos redirect).
        $protocoloCriado = $_GET['protocolo'] ?? '';
        $erro            = $_GET['erro'] ?? '';

        $this->view('ocorrencias', [
            'tituloPagina'  => 'Ocorrências – ColetaFácil',
            'descricao'     => 'Registre descartes irregulares e problemas na coleta em Muriaé-MG. Acompanhe o status e ganhe pontos de recompensa.',
            'cssPagina'     => ['ocorrencias.css'],
            'jsPagina'      => ['ocorrencias.js'],
            'menu'          => 'ocorrencias',
            'historico'     => $historico,
            'ocRegistradas' => $registradas,
            'ocResolvidas'  => $resolvidas,
            'ocTaxa'        => $taxa,
            'protocoloCriado' => $protocoloCriado,
            'erroRegistro'  => $erro,
        ]);
    }

    /**
     * Consulta de ocorrencia por protocolo (retorno JSON, usado pela aba
     * "Consultar Status" via fetch). Traz os dados reais do banco.
     */
    public function consultarJson(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $protocolo = trim((string) $this->request->get('protocolo'));
        if ($protocolo === '') {
            echo json_encode(['ok' => false, 'motivo' => 'vazio']);
            exit;
        }

        $o = $this->model('Ocorrencia')->porProtocolo($protocolo);
        if (empty($o)) {
            echo json_encode(['ok' => false, 'motivo' => 'nao_encontrado']);
            exit;
        }

        $statusJs = [
            'NOVA' => 'nova', 'EM_ANALISE' => 'analise',
            'EM_ANDAMENTO' => 'andamento', 'RESOLVIDA' => 'resolvida', 'ARQUIVADA' => 'resolvida',
        ];

        // Historico real de status (timeline da consulta).
        $historico = $this->model('Ocorrencia')->historicoStatus((int) $o['id_ocorrencia']);
        $timeline = [];
        foreach ($historico as $h) {
            $timeline[] = [
                'status' => $statusJs[$h['status']] ?? 'nova',
                'obs'    => $h['observacao'] ?: '',
                'data'   => date('d/m/Y H:i', strtotime($h['criada_em'])),
            ];
        }

        echo json_encode([
            'ok'        => true,
            'protocolo' => $o['protocolo'],
            'local'     => $o['endereco_descricao'],
            'tipo'      => rotuloTipoOcorrencia($o['tipo']),
            'bairro'    => $o['bairro'],
            'data'      => date('d/m/Y', strtotime($o['criada_em'])),
            'status'    => $statusJs[$o['status']] ?? 'nova',
            'obs'       => $o['observacao_admin'] ?: '',
            'timeline'  => $timeline,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Processa o registro de uma nova ocorrencia (POST do formulario).
     */
    public function registrar(): void
    {
        if (!$this->request->isPost()) {
            header('Location: ' . baseUrl('ocorrencia'));
            exit;
        }

        $model = $this->model('Ocorrencia');

        // --- Coleta e normalizacao dos campos ---
        $tipoSlug = (string) $this->request->post('tipo');
        $urgSlug  = (string) $this->request->post('urgencia', 'media');
        $endereco = trim((string) $this->request->post('endereco'));
        $bairro   = trim((string) $this->request->post('bairro'));
        $descricao = trim((string) $this->request->post('descricao'));
        $data     = trim((string) $this->request->post('data_ocorrencia'));
        $nome     = trim((string) $this->request->post('nome_contato'));
        $tel      = trim((string) $this->request->post('telefone_contato'));
        $email    = trim((string) $this->request->post('email_contato'));
        $anonima  = $this->request->post('anonima', null) !== null;

        $tipo = OcorrenciaModel::TIPOS[$tipoSlug] ?? null;
        $urg  = OcorrenciaModel::URGENCIAS[$urgSlug] ?? 'MEDIA';
        $idBairro = $model->idBairroPorNome($bairro);

        // --- Validacao ---
        $erro = '';
        if (!$tipo) {
            $erro = 'Selecione um tipo de ocorrência válido.';
        } elseif ($endereco === '') {
            $erro = 'Informe o endereço da ocorrência.';
        } elseif (!$idBairro) {
            $erro = 'Selecione um bairro válido.';
        } elseif ($descricao === '') {
            $erro = 'Descreva a ocorrência.';
        }

        if ($erro !== '') {
            header('Location: ' . baseUrl('ocorrencia') . '?erro=' . rawurlencode($erro));
            exit;
        }

        // --- Persistencia ---
        try {
            $usuario = auth_user();
            $idUsuario = $anonima ? null : ($usuario['id_usuario'] ?? null);

            $res = $model->registrar([
                'id_usuario'       => $idUsuario,
                'id_bairro'        => $idBairro,
                'tipo'             => $tipo,
                'urgencia'         => $urg,
                'endereco'         => $endereco,
                'data_ocorrencia'  => $data,
                'descricao'        => $descricao,
                'anonima'          => $anonima,
                'nome_contato'     => $nome,
                'telefone_contato' => $tel,
                'email_contato'    => $email,
            ]);

            // --- Upload de foto (opcional) ---
            $temFoto = $this->salvarFoto((int) $res['id']);

            // --- Pontos (so para usuario logado e ocorrencia nao anonima) ---
            if ($idUsuario) {
                $pontos = $this->model('Pontos');
                $pontos->creditarAcao(
                    (int) $idUsuario,
                    'OCORRENCIA_REGISTRADA',
                    'Ocorrência ' . $res['protocolo'],
                    ['id_ocorrencia' => (int) $res['id']]
                );
                if ($temFoto) {
                    $pontos->creditarAcao(
                        (int) $idUsuario,
                        'FOTO_APROVADA',
                        'Foto na ocorrência ' . $res['protocolo'],
                        ['id_ocorrencia' => (int) $res['id']]
                    );
                }
            }

            header('Location: ' . baseUrl('ocorrencia') . '?protocolo=' . rawurlencode($res['protocolo']));
            exit;
        } catch (Throwable $e) {
            header('Location: ' . baseUrl('ocorrencia') . '?erro=' . rawurlencode('Não foi possível registrar. Tente novamente.'));
            exit;
        }
    }

    /**
     * Processa o upload da foto da ocorrencia (pendencia 2).
     * Valida tipo e tamanho, move para assets/uploads/ocorrencias/ e grava em ocorrencia_foto.
     *
     * @return bool true se uma foto valida foi salva
     */
    private function salvarFoto(int $idOcorrencia): bool
    {
        if (empty($_FILES['foto']) || ($_FILES['foto']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return false;
        }

        $arquivo = $_FILES['foto'];
        $permitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $mime = mime_content_type($arquivo['tmp_name']) ?: '';

        // Valida tipo e tamanho (max 10MB).
        if (!isset($permitidos[$mime]) || $arquivo['size'] > 10 * 1024 * 1024) {
            return false;
        }

        $dir = __DIR__ . '/../../assets/uploads/ocorrencias';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $nomeArquivo = 'oc' . $idOcorrencia . '_' . bin2hex(random_bytes(4)) . '.' . $permitidos[$mime];
        $destino = $dir . '/' . $nomeArquivo;

        if (!move_uploaded_file($arquivo['tmp_name'], $destino)) {
            return false;
        }

        $this->model('Ocorrencia')->adicionarFoto(
            $idOcorrencia,
            'assets/uploads/ocorrencias/' . $nomeArquivo,
            $mime,
            (int) $arquivo['size']
        );
        return true;
    }
}
