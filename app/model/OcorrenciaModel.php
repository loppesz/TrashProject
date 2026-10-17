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
        return $this->db->select(
            'SELECT o.*, b.nome AS bairro
             FROM ocorrencia o
             JOIN bairro b ON b.id_bairro = o.id_bairro
             WHERE o.visivel_publicamente = 1
             ORDER BY o.criada_em DESC
             LIMIT ' . (int) $limite
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
                data_ocorrencia, descricao, anonima, nome_contato, telefone_contato,
                email_contato, visivel_publicamente, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, \'NOVA\')',
            [
                $protocolo,
                $d['id_usuario'] ?? null,
                $d['id_bairro'],
                $d['tipo'],
                $d['urgencia'],
                $d['endereco'],
                $d['data_ocorrencia'] ?: null,
                $d['descricao'],
                !empty($d['anonima']) ? 1 : 0,
                $d['nome_contato'] ?: null,
                $d['telefone_contato'] ?: null,
                $d['email_contato'] ?: null,
            ]
        );

        return ['id' => $id, 'protocolo' => $protocolo];
    }

    /** Gera um protocolo unico no formato CF-AAAA-NNNN. */
    private function gerarProtocolo(): string
    {
        do {
            $proto = 'CF-' . date('Y') . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
            $existe = $this->db->select(
                'SELECT 1 FROM ocorrencia WHERE protocolo = ? LIMIT 1',
                [$proto],
                'single'
            );
        } while (!empty($existe));

        return $proto;
    }

    /** Resolve o id do bairro pelo nome (para o form que envia nome). */
    public function idBairroPorNome(string $nome): ?int
    {
        $r = $this->db->select(
            'SELECT id_bairro FROM bairro WHERE nome = ? LIMIT 1',
            [$nome],
            'single'
        );
        return !empty($r) ? (int) $r['id_bairro'] : null;
    }
}
