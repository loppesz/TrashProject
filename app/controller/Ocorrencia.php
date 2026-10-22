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
        $metricas = $model->metricasPublicas();
        $mapaOcorrencias = $model->paraMapaPublico();
        $mapaPorBairro = $model->contagemPublicaPorBairro();
        $bairrosAtivos = $this->model('Bairro')->listaAtivos();
        $usuario = auth_user();
        $minhasOcorrencias = $usuario ? $model->porUsuario((int) $usuario['id_usuario']) : [];

        $registradas = $metricas['registradas'];
        $resolvidas  = $metricas['resolvidas'];
        $taxa = $registradas > 0 ? (int) round(($resolvidas / $registradas) * 100) : 0;

        // Mensagens de retorno do POST (via query string apos redirect).
        $protocoloCriado = $_GET['protocolo'] ?? '';
        $erro            = $_GET['erro'] ?? '';

        // Regras de pontos (fonte unica: PontosModel) e quanto o usuario ja usou hoje.
        $pontosModel   = $this->model('Pontos');
        $regrasPontos  = PontosModel::regras();
        $registrosHoje = $usuario
            ? $pontosModel->creditosHoje((int) $usuario['id_usuario'], 'OCORRENCIA_REGISTRADA')
            : 0;

        // Retorno do registro: pontos realmente creditados agora (apenas exibicao).
        $retornoPontos = [
            'agora'     => max(0, min($regrasPontos['registro'], (int) ($_GET['pts'] ?? 0))),
            'foto'      => ($_GET['foto'] ?? '0') === '1',
            'vinculada' => ($_GET['vinc'] ?? '0') === '1',
        ];

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
            'bairrosAtivos'  => $bairrosAtivos,
            'mapaOcorrencias' => $mapaOcorrencias,
            'mapaPorBairro'  => $mapaPorBairro,
            'minhasOcorrencias' => $minhasOcorrencias,
            'usuarioLogado'  => $usuario !== null,
            'usaLeaflet'     => true,
            'protocoloCriado' => $protocoloCriado,
            'erroRegistro'  => $erro,
            'regrasPontos'  => $regrasPontos,
            'registrosHoje' => $registrosHoje,
            'retornoPontos' => $retornoPontos,
        ]);
    }

    /**
     * Consulta de ocorrencia por protocolo (retorno JSON, usado pela aba
     * "Consultar Status" via fetch). Traz os dados reais do banco.
     */
    public function consultarJson(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $protocolo = mb_strtoupper(trim((string) $this->request->get('protocolo')));
        if ($protocolo === '' || mb_strlen($protocolo) > 25) {
            echo json_encode(['ok' => false, 'motivo' => 'vazio']);
            exit;
        }

        // Anti-varredura: no maximo 30 consultas a cada 10 minutos por conexao
        // (impede testar todos os protocolos em sequencia).
        $chaveIp = seguranca_chave();
        if (!limite_permitido('consulta_protocolo', $chaveIp, 30, 10)) {
            http_response_code(429);
            echo json_encode(['ok' => false, 'motivo' => 'limite']);
            exit;
        }
        limite_registrar('consulta_protocolo', $chaveIp);

        $o = $this->model('Ocorrencia')->porProtocolo($protocolo);
        if (empty($o)) {
            echo json_encode(['ok' => false, 'motivo' => 'nao_encontrado']);
            exit;
        }

        $statusJs = [
            'NOVA' => 'nova', 'EM_ANALISE' => 'analise',
            'EM_ANDAMENTO' => 'andamento', 'RESOLVIDA' => 'resolvida', 'ARQUIVADA' => 'arquivada',
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

        // A descrição só volta para o próprio autor (logado), não para quem
        // apenas digitou o protocolo.
        $usuario = auth_user();
        $minha = $usuario && !empty($o['id_usuario']) && (int) $o['id_usuario'] === (int) $usuario['id_usuario'];

        echo json_encode([
            'ok'        => true,
            'minha'     => $minha,
            'descricao' => $minha ? (string) $o['descricao'] : null,
            'protocolo' => $o['protocolo'],
            // Endereco completo so para o autor; os demais veem a rua sem numero.
            'local'     => $minha ? $o['endereco_descricao'] : enderecoPublico($o['endereco_descricao']),
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
        $cidade   = trim((string) $this->request->post('cidade', 'Muriaé'));
        $descricao = trim((string) $this->request->post('descricao'));
        $data     = trim((string) $this->request->post('data_ocorrencia'));
        $nome     = trim((string) $this->request->post('nome_contato'));
        $tel      = trim((string) $this->request->post('telefone_contato'));
        $email    = trim((string) $this->request->post('email_contato'));
        $anonima  = $this->request->post('anonima', null) !== null;

        // Exibir no historico publico? (o titular escolhe; padrao: sim)
        $publico  = $this->request->post('publico', null) !== null;

        $tipo = OcorrenciaModel::TIPOS[$tipoSlug] ?? null;
        $urg  = OcorrenciaModel::URGENCIAS[$urgSlug] ?? 'MEDIA';
        $idBairro = $model->idBairroPorNome($bairro);

        // Robo preencheu o campo isca: finge sucesso sem gravar nada.
        if (honeypot_acionado()) {
            header('Location: ' . baseUrl('ocorrencia'));
            exit;
        }

        // Anti-abuso: no maximo 10 registros por hora a partir da mesma conexao.
        $chaveIp = seguranca_chave();
        if (!limite_permitido('ocorrencia', $chaveIp, 10, 60)) {
            header('Location: ' . baseUrl('ocorrencia') . '?erro=' . rawurlencode('Muitas ocorrências enviadas desta conexão. Aguarde uma hora e tente novamente.'));
            exit;
        }

        // --- Validacao ---
        $erro = '';
        if (!$tipo) {
            $erro = 'Selecione um tipo de ocorrência válido.';
        } elseif ($endereco === '' || mb_strlen($endereco) > 200) {
            $erro = 'Informe o endereço da ocorrência (até 200 caracteres).';
        } elseif (!$idBairro) {
            $erro = 'Selecione um bairro válido de Muriaé.';
        } elseif ($cidade === '' || !preg_match('/^muria[eé](?:\s*-?\s*mg)?$/iu', $cidade)) {
            $erro = 'As ocorrências desta plataforma devem estar localizadas em Muriaé-MG.';
        } elseif ($descricao === '' || mb_strlen($descricao) > 500) {
            $erro = 'Descreva a ocorrência (até 500 caracteres).';
        } elseif ($data !== '' && (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data) || strtotime($data) === false || $data > date('Y-m-d'))) {
            $erro = 'Informe uma data válida (não pode ser no futuro).';
        } elseif (!$anonima && $email !== '' && (mb_strlen($email) > 150 || !filter_var($email, FILTER_VALIDATE_EMAIL))) {
            $erro = 'O e-mail de contato não é válido.';
        } elseif (!$anonima && ($tel !== '' && !preg_match('/^[\d\s()+-]{8,20}$/', $tel))) {
            $erro = 'O telefone de contato não é válido.';
        } elseif (!$anonima && mb_strlen($nome) > 120) {
            $erro = 'O nome de contato pode ter no máximo 120 caracteres.';
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

            if ($lat !== null && !$this->coordenadaEmMuriae($lat, (float) $lng)) {
                header('Location: ' . baseUrl('ocorrencia') . '?erro=' . rawurlencode('A localização GPS informada está fora da área de Muriaé-MG. Revise o endereço.'));
                exit;
            }

            limite_registrar('ocorrencia', $chaveIp);

            $res = $model->registrar([
                'publico'          => $publico,
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
        } catch (Throwable $e) {
            // Falha ao GRAVAR a ocorrencia em si: ai sim, "tente novamente".
            header('Location: ' . baseUrl('ocorrencia') . '?erro=' . rawurlencode('Não foi possível registrar. Tente novamente.'));
            exit;
        }

        // A ESTA ALTURA A OCORRENCIA JA FOI GRAVADA COM SUCESSO.
        // A foto e os pontos sao "melhor esforco": se algo falhar aqui, o
        // registro continua valido e o usuario deve ver sucesso (nunca mais
        // "tente novamente" com a ocorrencia ja criada). Por isso cada etapa
        // tem seu proprio try/catch e nao derruba o fluxo.

        // --- Upload de foto (opcional) ---
        // A foto NAO pontua agora: os pontos dela entram quando a equipe
        // confirmar a ocorrencia (Admin::atualizarStatus -> RESOLVIDA).
        $temFoto = false;
        try {
            $temFoto = $this->salvarFoto((int) $res['id']);
        } catch (Throwable $e) {
            $temFoto = false; // foto perdida, mas a ocorrencia vale.
        }

        // --- Pontos (so para usuario logado e ocorrencia nao anonima) ---
        // Registro vale pouco e tem limite diario, para evitar spam.
        $ptsAgora = 0;
        if ($idUsuario) {
            try {
                $ptsAgora = $this->model('Pontos')->creditarRegistroOcorrencia(
                    (int) $idUsuario,
                    (int) $res['id'],
                    $res['protocolo']
                );
            } catch (Throwable $e) {
                $ptsAgora = 0; // pontuacao falhou, mas a ocorrencia vale.
            }
        }

        $destino = baseUrl('ocorrencia') . '?protocolo=' . rawurlencode($res['protocolo'])
                 . '&pts=' . $ptsAgora
                 . '&foto=' . ($temFoto ? '1' : '0')
                 . '&vinc=' . ($idUsuario ? '1' : '0');   // ocorrencia vinculada a uma conta?
        header('Location: ' . $destino);
        exit;
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

    /** Limites operacionais aproximados do município de Muriaé-MG. */
    private function coordenadaEmMuriae(float $lat, float $lng): bool
    {
        $g = MURIAE_GEO;
        return $lat >= $g['lat_min'] && $lat <= $g['lat_max']
            && $lng >= $g['lng_min'] && $lng <= $g['lng_max'];
    }

    /**
     * Processa o upload da foto da ocorrencia.
     * Valida tipo e tamanho, remove metadados (EXIF/GPS) re-encodando a imagem
     * e grava os bytes no banco (ocorrencia_foto.conteudo).
     *
     * @return bool true se uma foto valida foi salva
     */
    private function salvarFoto(int $idOcorrencia): bool
    {
        if (empty($_FILES['foto']) || ($_FILES['foto']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return false;
        }

        $arquivo = $_FILES['foto'];
        $permitidos = ['image/jpeg', 'image/png', 'image/webp'];
        $mime = $this->detectarMime($arquivo['tmp_name']);

        // Valida tipo e tamanho (max 10MB).
        if (!in_array($mime, $permitidos, true) || $arquivo['size'] > 10 * 1024 * 1024) {
            return false;
        }

        // Dimensoes plausiveis (evita "bombas" de descompressao que esgotam a memoria).
        $dim = @getimagesize($arquivo['tmp_name']);
        if (!is_array($dim) || $dim[0] < 1 || $dim[1] < 1 || $dim[0] > 8000 || $dim[1] > 8000) {
            return false;
        }

        // Privacidade (LGPD): re-encoda a imagem para DESCARTAR metadados
        // (EXIF, incluindo coordenadas GPS do celular).
        $conteudo = $this->reencodarSemMetadados($arquivo['tmp_name'], $mime);

        // Sem a extensao GD nao ha como remover os metadados com seguranca:
        // a foto NAO e guardada (melhor perder a foto do que vazar o GPS).
        if ($conteudo === null) {
            return false;
        }

        $this->model('Ocorrencia')->adicionarFoto($idOcorrencia, $conteudo, $mime);
        return true;
    }

    /**
     * Exibe uma foto de ocorrencia lida do banco (rota /ocorrencia/foto/{id}).
     * Restrita ao administrador: a foto pode conter dados pessoais.
     */
    public function foto($id = ''): void
    {
        auth_require(true);

        // /ocorrencia/foto/5 -> o "5" chega como action no Request.
        $idFoto = (int) ($id !== '' ? $id : $this->request->getAction());
        $f = $this->model('Ocorrencia')->foto($idFoto);
        if (empty($f)) {
            http_response_code(404);
            exit;
        }

        // Fotos antigas, gravadas em disco antes da migracao 03.
        if (empty($f['conteudo']) && !empty($f['url_arquivo'])) {
            $caminho = __DIR__ . '/../../' . $f['url_arquivo'];
            if (!is_file($caminho)) {
                http_response_code(404);
                exit;
            }
            $f['conteudo'] = file_get_contents($caminho);
        }

        if (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: ' . ($f['tipo_mime'] ?: 'image/jpeg'));
        header('Content-Length: ' . strlen($f['conteudo']));
        header('Cache-Control: private, max-age=86400');
        header('X-Content-Type-Options: nosniff');
        echo $f['conteudo'];
        exit;
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
     * Retorna os bytes da nova imagem, ou null se a GD nao estiver disponivel.
     */
    private function reencodarSemMetadados(string $origem, string $mime): ?string
    {
        if (!function_exists('imagecreatefromstring')) {
            return null;
        }

        $dados = @file_get_contents($origem);
        if ($dados === false) {
            return null;
        }

        $img = @imagecreatefromstring($dados);
        if ($img === false) {
            return null;
        }

        // Preserva transparencia para PNG/WEBP.
        imagealphablending($img, false);
        imagesavealpha($img, true);

        ob_start();
        $ok = false;
        switch ($mime) {
            case 'image/jpeg':
                $ok = function_exists('imagejpeg') && imagejpeg($img, null, 85);
                break;
            case 'image/png':
                $ok = function_exists('imagepng') && imagepng($img, null, 6);
                break;
            case 'image/webp':
                $ok = function_exists('imagewebp') && imagewebp($img, null, 85);
                break;
        }
        $bytes = (string) ob_get_clean();
        imagedestroy($img);

        return ($ok && $bytes !== '') ? $bytes : null;
    }
}
