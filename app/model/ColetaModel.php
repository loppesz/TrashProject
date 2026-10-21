<?php

/**
 * Model da coleta por bairro.
 *
 * Cada linha da tabela coleta e uma passagem do caminhao:
 *  - tipo   SELETIVA (reciclaveis) ou COMUM (lixo domestico);
 *  - trecho parte do bairro com horario proprio ('' = bairro todo);
 *  - horario_fim NULL = so o horario aproximado de passagem ("a partir de").
 *
 * Os horarios publicos vem do cronograma oficial do DEMSUR
 * (migration_06). O CRUD do painel administra a coleta SELETIVA.
 */
class ColetaModel extends BaseModel
{
    protected string $table = 'coleta';
    protected string $pk = 'id_coleta';

    /** Orgao responsavel e fonte oficial do cronograma. */
    public const FONTE = [
        'orgao'    => 'DEMSUR',
        'nome'     => 'Departamento Municipal de Saneamento Urbano de Muriaé',
        'telefone' => '0800 283 1453',
        'url'      => 'https://www.demsur.com.br/pagina/107/cronograma-coleta',
    ];

    // =================================================================
    // CONSULTA PUBLICA
    // =================================================================

    /**
     * Todos os bairros ativos com seus horarios (inclusive os que ainda nao
     * tem horario divulgado), em ordem alfabetica.
     *
     * @return array lista de [
     *   'id_bairro', 'nome',
     *   'seletiva' => [ ['trecho' => '', 'dias' => [ ['dia' => 3, 'horarios' => [['inicio' => '07:30', 'fim' => null]]] ]] ],
     *   'comum'    => (mesmo formato)
     * ]
     */
    public function quadroPublico(): array
    {
        $linhas = $this->db->select(
            'SELECT b.id_bairro, b.nome, c.tipo, c.trecho, c.dia_semana, c.horario_inicio, c.horario_fim
             FROM bairro b
             LEFT JOIN coleta c ON c.id_bairro = b.id_bairro AND c.ativo = 1
             WHERE b.ativo = 1
             ORDER BY b.nome, c.tipo, c.trecho, (c.dia_semana + 5) % 7, c.horario_inicio'
        );
        return $this->agruparPorBairro($linhas);
    }

    /**
     * Detalhe de UM bairro ativo (consulta da pagina Coleta).
     * Retorna null se o bairro nao existir ou estiver inativo; se existir mas
     * nao tiver horario cadastrado, volta com 'seletiva' e 'comum' vazios.
     */
    public function detalheBairro(int $idBairro): ?array
    {
        $linhas = $this->db->select(
            'SELECT b.id_bairro, b.nome, c.tipo, c.trecho, c.dia_semana, c.horario_inicio, c.horario_fim,
                    c.equipe_responsavel, c.telefone_contato
             FROM bairro b
             LEFT JOIN coleta c ON c.id_bairro = b.id_bairro AND c.ativo = 1
             WHERE b.id_bairro = ? AND b.ativo = 1
             ORDER BY c.tipo, c.trecho, (c.dia_semana + 5) % 7, c.horario_inicio',
            [$idBairro]
        );
        if (empty($linhas)) {
            return null;
        }

        $bairro = $this->agruparPorBairro($linhas)[0];

        // Equipe/contato: o primeiro informado (seletiva vem antes na ordenacao).
        $equipe = '';
        $telefone = '';
        foreach ($linhas as $l) {
            if ($equipe === '' && !empty($l['equipe_responsavel'])) {
                $equipe = (string) $l['equipe_responsavel'];
            }
            if ($telefone === '' && !empty($l['telefone_contato'])) {
                $telefone = (string) $l['telefone_contato'];
            }
        }

        // Materiais recolhidos pela coleta SELETIVA do bairro.
        $bairro['materiais'] = $this->db->select(
            'SELECT DISTINCT m.id_material, m.nome, m.icone, m.cor_lixeira
             FROM coleta c
             JOIN coleta_material cm ON cm.id_coleta = c.id_coleta
             JOIN material m ON m.id_material = cm.id_material
             WHERE c.id_bairro = ? AND c.ativo = 1 AND c.tipo = \'SELETIVA\'
             ORDER BY m.id_material',
            [$idBairro]
        );

        // Materiais NAO recolhidos na coleta seletiva (nao aceitos).
        $bairro['nao_recolhidos'] = $this->db->select(
            'SELECT id_material, nome, icone
             FROM material
             WHERE aceito_coleta_seletiva = 0 AND ativo = 1
             ORDER BY id_material'
        );

        $bairro['equipe']   = $equipe;
        $bairro['telefone'] = $telefone;
        return $bairro;
    }

    /** Quantos bairros ativos tem cada tipo de coleta: ['SELETIVA' => n, 'COMUM' => n]. */
    public function totalBairrosPorTipo(): array
    {
        $linhas = $this->db->select(
            'SELECT c.tipo, COUNT(DISTINCT c.id_bairro) AS total
             FROM coleta c
             JOIN bairro b ON b.id_bairro = c.id_bairro
             WHERE c.ativo = 1 AND b.ativo = 1
             GROUP BY c.tipo'
        );
        $out = ['SELETIVA' => 0, 'COMUM' => 0];
        foreach ($linhas as $l) {
            $out[$l['tipo']] = (int) $l['total'];
        }
        return $out;
    }

    /**
     * Agrupa linhas (bairro LEFT JOIN coleta) em
     * bairro -> tipo -> trecho -> dia -> horarios, preservando a ordem do SQL.
     */
    private function agruparPorBairro(array $linhas): array
    {
        $mapa = [];
        foreach ($linhas as $l) {
            $id = (int) $l['id_bairro'];
            if (!isset($mapa[$id])) {
                $mapa[$id] = ['id_bairro' => $id, 'nome' => $l['nome'], 'seletiva' => [], 'comum' => []];
            }
            if ($l['tipo'] === null) {
                continue; // bairro sem horario cadastrado
            }
            $tipo   = $l['tipo'] === 'COMUM' ? 'comum' : 'seletiva';
            $trecho = (string) $l['trecho'];
            $dia    = (int) $l['dia_semana'];
            $mapa[$id][$tipo][$trecho][$dia][] = [
                'inicio' => substr((string) $l['horario_inicio'], 0, 5),
                'fim'    => $l['horario_fim'] !== null ? substr((string) $l['horario_fim'], 0, 5) : null,
            ];
        }

        // Converte os mapas internos em listas (formato estavel para view e JSON).
        $out = [];
        foreach ($mapa as $b) {
            foreach (['seletiva', 'comum'] as $tipo) {
                $grupos = [];
                foreach ($b[$tipo] as $trecho => $dias) {
                    $listaDias = [];
                    foreach ($dias as $dia => $horarios) {
                        $listaDias[] = ['dia' => $dia, 'horarios' => $horarios];
                    }
                    $grupos[] = ['trecho' => (string) $trecho, 'dias' => $listaDias];
                }
                $b[$tipo] = $grupos;
            }
            $out[] = $b;
        }
        return $out;
    }

    // =================================================================
    // CRUD (area administrativa) — coleta SELETIVA por bairro
    // A coleta comum vem do cronograma do DEMSUR e nao e editada aqui.
    // =================================================================

    /**
     * Bairros com coleta SELETIVA ativa, no formato da tabela do admin:
     * dias[], inicio, fim (pode ser null), equipe, trecho, materiais[].
     */
    public function porBairro(): array
    {
        $bairros = $this->db->select(
            'SELECT DISTINCT b.id_bairro, b.nome
             FROM coleta c
             JOIN bairro b ON b.id_bairro = c.id_bairro
             WHERE c.ativo = 1 AND b.ativo = 1 AND c.tipo = \'SELETIVA\'
             ORDER BY b.nome'
        );

        foreach ($bairros as &$b) {
            $id = (int) $b['id_bairro'];

            $linhas = $this->db->select(
                'SELECT dia_semana, horario_inicio, horario_fim, equipe_responsavel, trecho
                 FROM coleta WHERE id_bairro = ? AND ativo = 1 AND tipo = \'SELETIVA\'
                 ORDER BY (dia_semana + 5) % 7',
                [$id]
            );
            $b['dias']   = array_values(array_unique(array_map(static fn($l) => (int) $l['dia_semana'], $linhas)));
            $b['inicio'] = $linhas[0]['horario_inicio'] ?? null;
            $b['fim']    = $linhas[0]['horario_fim'] ?? null;
            $b['equipe'] = $linhas[0]['equipe_responsavel'] ?? '';
            $b['trecho'] = $linhas[0]['trecho'] ?? '';

            $mats = $this->db->select(
                'SELECT DISTINCT m.id_material, m.nome
                 FROM coleta c
                 JOIN coleta_material cm ON cm.id_coleta = c.id_coleta
                 JOIN material m ON m.id_material = cm.id_material
                 WHERE c.id_bairro = ? AND c.ativo = 1 AND c.tipo = \'SELETIVA\'
                 ORDER BY m.id_material',
                [$id]
            );
            $b['materiais'] = array_column($mats, 'nome');
        }
        unset($b);

        return $bairros;
    }

    /**
     * Regrava a coleta SELETIVA de um bairro: remove a atual e recria uma
     * linha por dia marcado, com os materiais recolhidos.
     *
     * $d: id_bairro, dias (array de int 1-7), inicio, fim (ou null), equipe,
     *     trecho, materiais (array de ids).
     */
    public function salvaPorBairro(array $d): void
    {
        $idBairro = (int) $d['id_bairro'];
        $this->removePorBairro($idBairro);

        foreach (array_unique(array_map('intval', $d['dias'])) as $dia) {
            if ($dia < 1 || $dia > 7) {
                continue;
            }
            $idColeta = $this->db->insert(
                'INSERT INTO coleta
                   (id_bairro, tipo, trecho, dia_semana, horario_inicio, horario_fim, equipe_responsavel, ativo)
                 VALUES (?, \'SELETIVA\', ?, ?, ?, ?, ?, 1)',
                [$idBairro, (string) ($d['trecho'] ?? ''), $dia, $d['inicio'], $d['fim'] ?: null, $d['equipe'] ?: null]
            );
            foreach (($d['materiais'] ?? []) as $idMat) {
                $idMat = (int) $idMat;
                if ($idMat > 0) {
                    $this->db->insert(
                        'INSERT INTO coleta_material (id_coleta, id_material) VALUES (?, ?)',
                        [$idColeta, $idMat]
                    );
                }
            }
        }
    }

    /** Remove a coleta SELETIVA (e seus materiais) de um bairro. A comum fica. */
    public function removePorBairro(int $idBairro): int
    {
        $ids = $this->db->select(
            'SELECT id_coleta FROM coleta WHERE id_bairro = ? AND tipo = \'SELETIVA\'',
            [$idBairro]
        );
        foreach ($ids as $row) {
            $this->db->delete('DELETE FROM coleta_material WHERE id_coleta = ?', [(int) $row['id_coleta']]);
        }
        return $this->db->delete('DELETE FROM coleta WHERE id_bairro = ? AND tipo = \'SELETIVA\'', [$idBairro]);
    }
}
