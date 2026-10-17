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
            $res = $model->registrar([
                'id_usuario'       => $anonima ? null : ($usuario['id_usuario'] ?? null),
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
            header('Location: ' . baseUrl('ocorrencia') . '?protocolo=' . rawurlencode($res['protocolo']));
            exit;
        } catch (Throwable $e) {
            header('Location: ' . baseUrl('ocorrencia') . '?erro=' . rawurlencode('Não foi possível registrar. Tente novamente.'));
            exit;
        }
    }
}
