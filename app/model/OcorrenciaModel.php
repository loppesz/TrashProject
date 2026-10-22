<?php

/**
 * Model das ocorrencias.
 *
 * Cobre a leitura (historico publico, consulta por protocolo) e a
 * escrita (registro de nova ocorrencia a partir do formulario publico).
 */
class OcorrenciaModel extends BaseModel
{
    protected string $table = 'ocorrencia';
    protected string $pk = 'id_ocorrencia';

    /** Mapas slug (form) -> ENUM (banco). */
    public const TIPOS = [
        'descarte-irregular'    => 'DESCARTE_IRREGULAR',
        'coleta-nao-realizada'  => 'COLETA_NAO_REALIZADA',
        'material-nao-recolhido' => 'MATERIAL_NAO_RECOLHIDO',
        'ponto-inadequado'      => 'PONTO_INADEQUADO',
        'entulho'               => 'ENTULHO',
        'lixo-toxico'           => 'LIXO_TOXICO',
        'outros'                => 'OUTROS',
    ];

    public const URGENCIAS = [
        'baixa' => 'BAIXA',
        'media' => 'MEDIA',
        'alta'  => 'ALTA',
    ];

    /**
     * Ocorrencias visiveis publicamente (historico publico), mais recentes primeiro.
     */
    public function historicoPublico(int $limite = 30): array
    {
        // Minimizacao de dados (LGPD): o historico publico NAO traz descricao
        // (texto livre, pode conter dados de terceiros), protocolo, coordenadas
        // nem contatos. O endereco e reduzido a rua sem numero (enderecoPublico).
        $linhas = $this->db->select(
            'SELECT o.tipo, o.urgencia, o.endereco_descricao, o.status, o.criada_em,
                    b.nome AS bairro
             FROM ocorrencia o
             JOIN bairro b ON b.id_bairro = o.id_bairro
             WHERE o.visivel_publicamente = 1
             ORDER BY o.criada_em DESC
             LIMIT ' . (int) $limite
        );
        foreach ($linhas as &$l) {
            $l['endereco_descricao'] = enderecoPublico($l['endereco_descricao']);
        }
        unset($l);
        return $linhas;
    }

    /** Ocorrências recentes vinculadas a um usuário autenticado. */
    public function porUsuario(int $idUsuario, int $limite = 5): array
    {
        return $this->db->select(
            'SELECT o.protocolo, o.tipo, o.status, o.descricao, o.criada_em, b.nome AS bairro
             FROM ocorrencia o
             JOIN bairro b ON b.id_bairro = o.id_bairro
             WHERE o.id_usuario = ?
             ORDER BY o.criada_em DESC
             LIMIT ' . (int) $limite,
            [$idUsuario]
        );
    }

    /** Consulta uma ocorrencia pelo protocolo. */
    public function porProtocolo(string $protocolo): array
    {
        return $this->db->select(
            'SELECT o.*, b.nome AS bairro
             FROM ocorrencia o
             JOIN bairro b ON b.id_bairro = o.id_bairro
             WHERE o.protocolo = ? LIMIT 1',
            [$protocolo],
            'single'
        );
    }

    /**
     * Registra uma nova ocorrencia. Recebe os dados ja validados pelo controller.
     *
     * @return array ['id' => int, 'protocolo' => string]
     */
    public function registrar(array $d): array
    {
        $protocolo = $this->gerarProtocolo();

        $id = $this->db->insert(
            'INSERT INTO ocorrencia
               (protocolo, id_usuario, id_bairro, tipo, urgencia, endereco_descricao,
                latitude, longitude, data_ocorrencia, descricao, anonima, nome_contato,
                telefone_contato, email_contato, visivel_publicamente, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, \'NOVA\')',
            [
                $protocolo,
                $d['id_usuario'] ?? null,
                $d['id_bairro'],
                $d['tipo'],
                $d['urgencia'],
                $d['endereco'],
                $d['latitude'] ?? null,
                $d['longitude'] ?? null,
                $d['data_ocorrencia'] ?: null,
                $d['descricao'],
                !empty($d['anonima']) ? 1 : 0,
                $d['nome_contato'] ?: null,
                $d['telefone_contato'] ?: null,
                $d['email_contato'] ?: null,
                array_key_exists('publico', $d) ? (!empty($d['publico']) ? 1 : 0) : 1,
            ]
        );

        // Primeiro registro do historico (status inicial NOVA).
        $this->registrarHistorico((int) $id, 'NOVA', 'Ocorrência registrada', $d['id_usuario'] ?? null);

        return ['id' => $id, 'protocolo' => $protocolo];
    }

    /**
     * Gera um protocolo unico no formato CF-AAAA-XXXXXXXX (8 caracteres
     * aleatorios, sem letras ambiguas). Com ~1 trilhao de combinacoes por ano,
     * nao da para "adivinhar" protocolos de outras pessoas.
     */
    private function gerarProtocolo(): string
    {
        $alfabeto = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        do {
            $sufixo = '';
            for ($i = 0; $i < 8; $i++) {
                $sufixo .= $alfabeto[random_int(0, strlen($alfabeto) - 1)];
            }
            $proto = 'CF-' . date('Y') . '-' . $sufixo;
            $existe = $this->db->select(
                'SELECT 1 FROM ocorrencia WHERE protocolo = ? LIMIT 1',
                [$proto],
                'single'
            );
        } while (!empty($existe));

        return $proto;
    }

    /**
     * Grava a foto de evidencia NO BANCO (coluna conteudo, LONGBLOB).
     * Os bytes ja chegam sem metadados EXIF/GPS (tratados no controller).
     */
    public function adicionarFoto(int $idOcorrencia, string $conteudo, string $mime): int
    {
        $pdo  = $this->db->conecta();
        $stmt = $pdo->prepare(
            'INSERT INTO ocorrencia_foto (id_ocorrencia, conteudo, tipo_mime, tamanho_bytes)
             VALUES (?, ?, ?, ?)'
        );
        $stmt->bindValue(1, $idOcorrencia, PDO::PARAM_INT);
        $stmt->bindValue(2, $conteudo, PDO::PARAM_LOB);
        $stmt->bindValue(3, $mime);
        $stmt->bindValue(4, strlen($conteudo), PDO::PARAM_INT);
        $stmt->execute();

        return (int) $pdo->lastInsertId();
    }

    /** Lista os ids das fotos de uma ocorrencia (sem carregar os bytes). */
    public function fotos(int $idOcorrencia): array
    {
        return $this->db->select(
            'SELECT id_ocorrencia_foto FROM ocorrencia_foto
             WHERE id_ocorrencia = ?
             ORDER BY id_ocorrencia_foto',
            [$idOcorrencia]
        );
    }

    /** Busca uma foto pelo id (bytes + mime; url_arquivo para fotos legadas em disco). */
    public function foto(int $idFoto): array
    {
        return $this->db->select(
            'SELECT conteudo, tipo_mime, url_arquivo FROM ocorrencia_foto
             WHERE id_ocorrencia_foto = ? LIMIT 1',
            [$idFoto],
            'single'
        );
    }

    /** Resolve o id do bairro pelo nome (para o form que envia nome). */
    public function idBairroPorNome(string $nome): ?int
    {
        // Só aceita bairros ativos (lista oficial de Muriaé).
        $r = $this->db->select(
            'SELECT id_bairro FROM bairro WHERE nome = ? AND ativo = 1 LIMIT 1',
            [$nome],
            'single'
        );
        return !empty($r) ? (int) $r['id_bairro'] : null;
    }

    /** Métricas públicas reais, calculadas diretamente no banco. */
    public function metricasPublicas(): array
    {
        $r = $this->db->select(
            "SELECT COUNT(*) AS registradas,
                    SUM(CASE WHEN status = 'RESOLVIDA' THEN 1 ELSE 0 END) AS resolvidas
             FROM ocorrencia
             WHERE visivel_publicamente = 1",
            [],
            'single'
        );
        return [
            'registradas' => (int) ($r['registradas'] ?? 0),
            'resolvidas' => (int) ($r['resolvidas'] ?? 0),
        ];
    }

    /**
     * Ocorrências públicas com coordenadas para o mapa.
     * A coordenada é arredondada para 3 casas (~100 m) para não apontar a casa exata.
     * Não traz protocolo nem id (evita cruzar o ponto com a consulta por protocolo).
     */
    public function paraMapaPublico(int $limite = 200): array
    {
        $g = MURIAE_GEO;
        return $this->db->select(
            "SELECT o.tipo, o.urgencia, o.status,
                    ROUND(o.latitude, 3) AS latitude, ROUND(o.longitude, 3) AS longitude,
                    DATE(o.criada_em) AS data, b.nome AS bairro
             FROM ocorrencia o
             JOIN bairro b ON b.id_bairro = o.id_bairro
             WHERE o.visivel_publicamente = 1
               AND o.latitude IS NOT NULL AND o.longitude IS NOT NULL
               AND o.latitude BETWEEN ? AND ?
               AND o.longitude BETWEEN ? AND ?
             ORDER BY o.criada_em DESC
             LIMIT " . (int) $limite,
            [$g['lat_min'], $g['lat_max'], $g['lng_min'], $g['lng_max']]
        );
    }

    /** Contagem pública real por bairro para acompanhar o mapa. */
    public function contagemPublicaPorBairro(): array
    {
        return $this->db->select(
            'SELECT b.nome AS bairro, COUNT(*) AS total
             FROM ocorrencia o
             JOIN bairro b ON b.id_bairro = o.id_bairro
             WHERE o.visivel_publicamente = 1
             GROUP BY b.id_bairro, b.nome
             ORDER BY total DESC, b.nome'
        );
    }

    /** Locais reais com mais relatos de descarte irregular. */
    public function locaisDescarteIrregular(int $limite = 10): array
    {
        return $this->db->select(
            "SELECT o.endereco_descricao AS local, b.nome AS bairro,
                    COUNT(*) AS total, MAX(o.criada_em) AS ultima
             FROM ocorrencia o
             JOIN bairro b ON b.id_bairro = o.id_bairro
             WHERE o.tipo = 'DESCARTE_IRREGULAR'
             GROUP BY o.endereco_descricao, b.id_bairro, b.nome
             ORDER BY total DESC, ultima DESC
             LIMIT " . (int) $limite
        );
    }

    // =================================================================
    // Area administrativa
    // =================================================================

    /** Lista TODAS as ocorrencias (admin), mais recentes primeiro. */
    public function listaAdmin(int $limite = 200): array
    {
        return $this->db->select(
            'SELECT o.*, b.nome AS bairro
             FROM ocorrencia o
             JOIN bairro b ON b.id_bairro = o.id_bairro
             ORDER BY o.criada_em DESC
             LIMIT ' . (int) $limite
        );
    }

    /** Conta ocorrencias por status. Retorna [status => total]. */
    public function contagemPorStatus(): array
    {
        $linhas = $this->db->select(
            'SELECT status, COUNT(*) AS total FROM ocorrencia GROUP BY status'
        );
        $out = [];
        foreach ($linhas as $l) {
            $out[$l['status']] = (int) $l['total'];
        }
        return $out;
    }

    /** Total geral de ocorrencias. */
    public function total(): int
    {
        $r = $this->db->select('SELECT COUNT(*) AS n FROM ocorrencia', [], 'single');
        return (int) ($r['n'] ?? 0);
    }

    /** Contagem de ocorrencias agrupadas por bairro (para graficos). */
    public function contagemPorBairro(): array
    {
        return $this->db->select(
            'SELECT b.nome AS bairro, COUNT(*) AS total
             FROM ocorrencia o
             JOIN bairro b ON b.id_bairro = o.id_bairro
             GROUP BY b.id_bairro, b.nome
             ORDER BY total DESC'
        );
    }

    /** Contagem por tipo (para graficos). */
    public function contagemPorTipo(): array
    {
        return $this->db->select(
            'SELECT tipo, COUNT(*) AS total
             FROM ocorrencia GROUP BY tipo ORDER BY total DESC'
        );
    }

    /**
     * Atualiza o status de uma ocorrencia (acao do admin).
     * Grava tambem a observacao e o responsavel; preenche resolvida_em quando resolve.
     *
     * @param string|null $observacao null = manter a observacao atual (troca rapida
     *                                de status pela lista, que nao envia o campo).
     * @return int linhas afetadas
     */
    public function atualizarStatus(int $id, string $status, ?string $observacao, int $idAdmin): int
    {
        $validos = ['NOVA', 'EM_ANALISE', 'EM_ANDAMENTO', 'RESOLVIDA', 'ARQUIVADA'];
        if (!in_array($status, $validos, true)) {
            return 0;
        }

        $resolvida = $status === 'RESOLVIDA' ? date('Y-m-d H:i:s') : null;

        if ($observacao === null) {
            $n = $this->db->update(
                'UPDATE ocorrencia
                 SET status = ?, id_administrador_responsavel = ?, resolvida_em = ?
                 WHERE id_ocorrencia = ?',
                [$status, $idAdmin, $resolvida, $id]
            );
        } else {
            $n = $this->db->update(
                'UPDATE ocorrencia
                 SET status = ?, observacao_admin = ?, id_administrador_responsavel = ?,
                     resolvida_em = ?
                 WHERE id_ocorrencia = ?',
                [$status, $observacao ?: null, $idAdmin, $resolvida, $id]
            );
        }

        // Registra a mudanca no historico/auditoria.
        $this->registrarHistorico($id, $status, $observacao, $idAdmin);

        return $n;
    }

    /**
     * Grava um registro no historico de status de uma ocorrencia.
     */
    public function registrarHistorico(int $idOcorrencia, string $status, ?string $observacao = null, ?int $idUsuario = null): int
    {
        return $this->db->insert(
            'INSERT INTO ocorrencia_historico_status
               (id_ocorrencia, status, observacao, id_usuario_responsavel)
             VALUES (?, ?, ?, ?)',
            [$idOcorrencia, $status, $observacao ?: null, $idUsuario]
        );
    }

    /**
     * Retorna o historico de status de uma ocorrencia (ordem cronologica).
     */
    public function historicoStatus(int $idOcorrencia): array
    {
        return $this->db->select(
            'SELECT status, observacao, criada_em
             FROM ocorrencia_historico_status
             WHERE id_ocorrencia = ?
             ORDER BY criada_em, id_historico',
            [$idOcorrencia]
        );
    }
}
