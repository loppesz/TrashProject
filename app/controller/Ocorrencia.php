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

            // Privacidade (LGPD): no modo anonimo, nenhum dado pessoal de contato
            // e gravado — o rotulo "nao vincular dados pessoais" e honrado de fato.
            if ($anonima) {
                $nome = '';
                $tel  = '';
                $email = '';
            }

            // Remove coordenadas coladas no endereco (ex.: "Lat: -21.1, Lng: -42.3 ...").
            // A geolocalizacao NAO vai para o campo publico; fica, no maximo, nas
            // colunas latitude/longitude destinadas ao uso interno do admin.
            [$enderecoLimpo, $lat, $lng] = $this->extrairCoordenadas($endereco);

            $res = $model->registrar([
                'id_usuario'       => $idUsuario,
                'id_bairro'        => $idBairro,
                'tipo'             => $tipo,
                'urgencia'         => $urg,
                'endereco'         => $enderecoLimpo,
                'latitude'         => $lat,
                'longitude'        => $lng,
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
     * Separa eventuais coordenadas do texto do endereco.
     *
     * Aceita tanto o formato legado colado no campo de endereco
     * ("Lat: -21.13, Lng: -42.36 (localizacao atual)") quanto os campos
     * ocultos lat/lng enviados pelo formulario. As coordenadas ficam fora do
     * texto publico; retornam separadas para gravar nas colunas internas.
     *
     * @return array{0:string,1:?float,2:?float} [endereco_limpo, latitude, longitude]
     */
    private function extrairCoordenadas(string $endereco): array
    {
        $lat = null;
        $lng = null;

        // 1) Campos ocultos vindos do formulario (preferencial).
        $latPost = trim((string) $this->request->post('lat'));
        $lngPost = trim((string) $this->request->post('lng'));
        if ($latPost !== '' && $lngPost !== '' && is_numeric($latPost) && is_numeric($lngPost)) {
            $lat = (float) $latPost;
            $lng = (float) $lngPost;
        }

        // 2) Formato legado colado no proprio endereco.
        if (preg_match('/lat\s*:?\s*(-?\d{1,3}\.\d+)\s*,?\s*lng\s*:?\s*(-?\d{1,3}\.\d+)/i', $endereco, $m)) {
            if ($lat === null) {
                $lat = (float) $m[1];
                $lng = (float) $m[2];
            }
            // Remove o trecho de coordenadas e marcacoes do texto publico.
            $endereco = preg_replace('/lat\s*:?\s*-?\d{1,3}\.\d+\s*,?\s*lng\s*:?\s*-?\d{1,3}\.\d+\s*/i', '', $endereco);
            $endereco = preg_replace('/\(localiza[cç][aã]o atual\)/iu', '', (string) $endereco);
            $endereco = trim((string) $endereco, " \t\n\r-–,");
        }

        // Se o endereco ficou vazio mas ha coordenadas, usa um rotulo neutro.
        if (trim($endereco) === '' && $lat !== null) {
            $endereco = 'Localização informada por GPS';
        }

        return [trim($endereco), $lat, $lng];
    }

    /**
     * Processa o upload da foto da ocorrencia (pendencia 2).
     * Valida tipo e tamanho, remove metadados (EXIF/GPS) re-encodando a imagem,
     * move para assets/uploads/ocorrencias/ e grava em ocorrencia_foto.
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
        $mime = $this->detectarMime($arquivo['tmp_name']);

        // Valida tipo e tamanho (max 10MB).
        if (!isset($permitidos[$mime]) || $arquivo['size'] > 10 * 1024 * 1024) {
            return false;
        }

        $dir = __DIR__ . '/../../assets/uploads/ocorrencias';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $ext = $permitidos[$mime];
        $nomeArquivo = 'oc' . $idOcorrencia . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $destino = $dir . '/' . $nomeArquivo;

        // Privacidade (LGPD): re-encoda a imagem para DESCARTAR metadados
        // (EXIF, incluindo coordenadas GPS do celular). A GD escreve apenas os
        // pixels, sem copiar os metadados do arquivo original.
        $salvou = $this->reencodarSemMetadados($arquivo['tmp_name'], $destino, $mime);

        if (!$salvou) {
            // Fallback: se a GD nao estiver disponivel, move o arquivo original.
            if (!move_uploaded_file($arquivo['tmp_name'], $destino)) {
                return false;
            }
        }

        $this->model('Ocorrencia')->adicionarFoto(
            $idOcorrencia,
            'assets/uploads/ocorrencias/' . $nomeArquivo,
            $mime,
            (int) (@filesize($destino) ?: $arquivo['size'])
        );
        return true;
    }

    /**
     * Detecta o MIME real da imagem sem depender exclusivamente da extensao
     * fileinfo: tenta mime_content_type(), depois finfo, e por fim getimagesize()
     * (que identifica o formato pelos bytes do proprio arquivo).
     */
    private function detectarMime(string $caminho): string
    {
        if (function_exists('mime_content_type')) {
            $m = @mime_content_type($caminho);
            if (is_string($m) && $m !== '') {
                return $m;
            }
        }
        if (class_exists('finfo')) {
            $fi = new finfo(FILEINFO_MIME_TYPE);
            $m = @$fi->file($caminho);
            if (is_string($m) && $m !== '') {
                return $m;
            }
        }
        $info = @getimagesize($caminho);
        if (is_array($info) && !empty($info['mime'])) {
            return (string) $info['mime'];
        }
        return '';
    }

    /**
     * Re-encoda a imagem via GD, descartando metadados (EXIF/GPS/etc.).
     * Retorna false se a extensao GD ou a funcao do formato nao existirem.
     */
    private function reencodarSemMetadados(string $origem, string $destino, string $mime): bool
    {
        if (!function_exists('imagecreatefromstring')) {
            return false;
        }

        $dados = @file_get_contents($origem);
        if ($dados === false) {
            return false;
        }

        $img = @imagecreatefromstring($dados);
        if ($img === false) {
            return false;
        }

        // Preserva transparencia para PNG/WEBP.
        imagealphablending($img, false);
        imagesavealpha($img, true);

        $ok = false;
        switch ($mime) {
            case 'image/jpeg':
                $ok = function_exists('imagejpeg') && imagejpeg($img, $destino, 85);
                break;
            case 'image/png':
                $ok = function_exists('imagepng') && imagepng($img, $destino, 6);
                break;
            case 'image/webp':
                $ok = function_exists('imagewebp') && imagewebp($img, $destino, 85);
                break;
        }

        imagedestroy($img);
        return $ok;
    }
}
