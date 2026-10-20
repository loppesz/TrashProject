<?php

/**
 * Model da coleta seletiva (agregada por bairro).
 */
class ColetaModel extends BaseModel
{
    protected string $table = 'coleta';
    protected string $pk = 'id_coleta';

    /**
     * Retorna, por bairro, os dados consolidados exibidos na tabela geral:
     *  - bairro, equipe
     *  - dias[]     (lista de dia_semana)
     *  - horario    (inicio/fim — assume o mesmo para o bairro)
     *  - materiais[] (nomes dos materiais recolhidos)
     */
    public function porBairro(): array
    {
        $bairros = $this->db->select(
            'SELECT DISTINCT b.id_bairro, b.nome
             FROM coleta c
             JOIN bairro b ON b.id_bairro = c.id_bairro
             WHERE c.ativo = 1
             ORDER BY b.nome'
        );

        foreach ($bairros as &$b) {
            $id = (int) $b['id_bairro'];

            // Dias e horario do bairro
            $linhas = $this->db->select(
                'SELECT dia_semana, horario_inicio, horario_fim, equipe_responsavel
                 FROM coleta WHERE id_bairro = ? AND ativo = 1
                 ORDER BY dia_semana',
                [$id]
            );
            $b['dias']   = array_map(static fn($l) => (int) $l['dia_semana'], $linhas);
            $b['inicio'] = $linhas[0]['horario_inicio'] ?? null;
            $b['fim']    = $linhas[0]['horario_fim'] ?? null;
            $b['equipe'] = $linhas[0]['equipe_responsavel'] ?? '';

            // Materiais recolhidos no bairro (distintos entre as coletas)
            $mats = $this->db->select(
                'SELECT DISTINCT m.nome
                 FROM coleta c
                 JOIN coleta_material cm ON cm.id_coleta = c.id_coleta
                 JOIN material m ON m.id_material = cm.id_material
                 WHERE c.id_bairro = ? AND c.ativo = 1
                 ORDER BY m.id_material',
                [$id]
            );
            $b['materiais'] = array_column($mats, 'nome');
        }
        unset($b);

        return $bairros;
    }

    /**
     * Detalhe da coleta de UM bairro (consulta publica por bairro).
     * Retorna null se o bairro nao tiver coleta cadastrada.
     *
     * @return array|null [
     *   'id_bairro', 'nome', 'dias'[], 'inicio', 'fim', 'equipe',
     *   'materiais'[] (recolhidos, com nome+icone+cor),
     *   'nao_recolhidos'[] (nao aceitos na coleta seletiva, nome+icone)
     * ]
     */
    public function detalheBairro(int $idBairro): ?array
    {
        $bairro = $this->db->select(
            'SELECT id_bairro, nome FROM bairro WHERE id_bairro = ? LIMIT 1',
            [$idBairro],
            'single'
        );
        if (empty($bairro)) {
            return null;
        }

        $linhas = $this->db->select(
            'SELECT dia_semana, horario_inicio, horario_fim, equipe_responsavel
             FROM coleta WHERE id_bairro = ? AND ativo = 1
             ORDER BY dia_semana',
            [$idBairro]
        );
        if (empty($linhas)) {
            return null;
        }

        $mats = $this->db->select(
            'SELECT DISTINCT m.id_material, m.nome, m.icone, m.cor_lixeira
             FROM coleta c
             JOIN coleta_material cm ON cm.id_coleta = c.id_coleta
             JOIN material m ON m.id_material = cm.id_material
             WHERE c.id_bairro = ? AND c.ativo = 1
             ORDER BY m.id_material',
            [$idBairro]
        );

        // Materiais NAO recolhidos na coleta seletiva (nao aceitos).
        $naoRecolhidos = $this->db->select(
            'SELECT id_material, nome, icone
             FROM material
             WHERE aceito_coleta_seletiva = 0 AND ativo = 1
             ORDER BY id_material'
        );

        return [
            'id_bairro'      => (int) $bairro['id_bairro'],
            'nome'           => $bairro['nome'],
            'dias'           => array_map(static fn($l) => (int) $l['dia_semana'], $linhas),
            'inicio'         => $linhas[0]['horario_inicio'] ?? null,
            'fim'            => $linhas[0]['horario_fim'] ?? null,
            'equipe'         => $linhas[0]['equipe_responsavel'] ?? '',
            'materiais'      => $mats,
            'nao_recolhidos' => $naoRecolhidos,
        ];
    }

    /** Lista simples de bairros com coleta ativa (para o select da consulta). */
    public function bairrosComColeta(): array
    {
        return $this->db->select(
            'SELECT DISTINCT b.id_bairro, b.nome
             FROM coleta c
             JOIN bairro b ON b.id_bairro = c.id_bairro
             WHERE c.ativo = 1
             ORDER BY b.nome'
        );
    }

    // =================================================================
    // CRUD (area administrativa) — operacao por bairro
    // =================================================================

    /**
     * Regrava a coleta de um bairro: remove as coletas atuais do bairro e
     * recria uma linha por dia marcado, com os materiais recolhidos.
     *
     * $d: id_bairro, dias (array de int 1-7), inicio, fim, equipe,
     *     materiais (array de ids).
     */
    public function salvaPorBairro(array $d): void
    {
        $idBairro = (int) $d['id_bairro'];
        $this->removePorBairro($idBairro);

        foreach ($d['dias'] as $dia) {
            $dia = (int) $dia;
            if ($dia < 1 || $dia > 7) {
                continue;
            }
            $idColeta = $this->db->insert(
                'INSERT INTO coleta
                   (id_bairro, dia_semana, horario_inicio, horario_fim, equipe_responsavel, ativo)
                 VALUES (?, ?, ?, ?, ?, 1)',
                [$idBairro, $dia, $d['inicio'], $d['fim'], $d['equipe'] ?: null]
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

    /** Remove todas as coletas (e seus materiais) de um bairro. */
    public function removePorBairro(int $idBairro): int
    {
        $ids = $this->db->select(
            'SELECT id_coleta FROM coleta WHERE id_bairro = ?',
            [$idBairro]
        );
        foreach ($ids as $row) {
            $this->db->delete('DELETE FROM coleta_material WHERE id_coleta = ?', [(int) $row['id_coleta']]);
        }
        return $this->db->delete('DELETE FROM coleta WHERE id_bairro = ?', [$idBairro]);
    }
}
