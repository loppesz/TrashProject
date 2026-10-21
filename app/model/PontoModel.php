<?php

/**
 * Model dos pontos de descarte.
 *
 * Monta a estrutura completa de cada ponto (dados + bairro + materiais +
 * horarios + fotos) que a tela de Pontos consome.
 */
class PontoModel extends BaseModel
{
    protected string $table = 'ponto_descarte';
    protected string $pk = 'id_ponto';

    /**
     * Lista todos os pontos ativos, cada um com:
     *  - dados da tabela ponto_descarte + nome do bairro
     *  - materiais[]  (array de nomes de material aceitos)
     *  - horarios[]   (array dia_semana => ['inicio','fim','fechado'])
     *  - fotos[]      (array de ['url','legenda'])
     */
    public function listaCompleta(): array
    {
        $pontos = $this->db->select(
            'SELECT p.*, b.nome AS bairro
             FROM ponto_descarte p
             JOIN bairro b ON b.id_bairro = p.id_bairro
             WHERE p.ativo = 1
             ORDER BY p.id_ponto'
        );

        foreach ($pontos as &$p) {
            $id = (int) $p['id_ponto'];
            $p['materiais'] = $this->materiais($id);
            $p['horarios']  = $this->horarios($id);
            $p['fotos']     = $this->fotos($id);
        }
        unset($p);

        return $pontos;
    }

    /** Nomes dos materiais aceitos em um ponto. */
    public function materiais(int $idPonto): array
    {
        $linhas = $this->db->select(
            'SELECT m.nome
             FROM ponto_material pm
             JOIN material m ON m.id_material = pm.id_material
             WHERE pm.id_ponto = ?
             ORDER BY m.nome',
            [$idPonto]
        );
        return array_column($linhas, 'nome');
    }

    /** Horarios por dia da semana de um ponto. */
    public function horarios(int $idPonto): array
    {
        return $this->db->select(
            'SELECT dia_semana, horario_inicio, horario_fim, fechado
             FROM ponto_horario
             WHERE id_ponto = ?
             ORDER BY dia_semana',
            [$idPonto]
        );
    }

    /** Fotos (galeria) de um ponto. */
    public function fotos(int $idPonto): array
    {
        return $this->db->select(
            'SELECT url_arquivo, legenda
             FROM ponto_foto
             WHERE id_ponto = ?
             ORDER BY ordem',
            [$idPonto]
        );
    }

    /** Ids dos materiais aceitos por um ponto. */
    public function idsMateriais(int $idPonto): array
    {
        $linhas = $this->db->select(
            'SELECT id_material FROM ponto_material WHERE id_ponto = ?',
            [$idPonto]
        );
        return array_map(static fn($l) => (int) $l['id_material'], $linhas);
    }

    /** Total de pontos ativos publicados no mapa. */
    public function totalAtivos(): int
    {
        $r = $this->db->select('SELECT COUNT(*) AS total FROM ponto_descarte WHERE ativo = 1', [], 'single');
        return (int) ($r['total'] ?? 0);
    }

    // =================================================================
    // CRUD (area administrativa)
    // =================================================================

    /**
     * Cria um ponto de descarte. $d: id_bairro, nome, endereco,
     * horario_funcionamento, status_funcionamento, materiais (array de ids).
     *
     * @return int id gerado
     */
    public function cria(array $d): int
    {
        $id = $this->db->insert(
            'INSERT INTO ponto_descarte
               (id_bairro, nome, endereco, horario_funcionamento, status_funcionamento, ativo)
             VALUES (?, ?, ?, ?, ?, 1)',
            [
                $d['id_bairro'],
                $d['nome'],
                $d['endereco'],
                $d['horario_funcionamento'] ?: null,
                $d['status_funcionamento'],
            ]
        );
        $this->sincronizaMateriais($id, $d['materiais'] ?? []);
        return $id;
    }

    /** Atualiza um ponto e seus materiais aceitos. */
    public function atualiza(int $id, array $d): int
    {
        $n = $this->db->update(
            'UPDATE ponto_descarte SET
               id_bairro = ?, nome = ?, endereco = ?, horario_funcionamento = ?, status_funcionamento = ?
             WHERE id_ponto = ?',
            [
                $d['id_bairro'],
                $d['nome'],
                $d['endereco'],
                $d['horario_funcionamento'] ?: null,
                $d['status_funcionamento'],
                $id,
            ]
        );
        $this->sincronizaMateriais($id, $d['materiais'] ?? []);
        return $n;
    }

    /** Regrava a lista de materiais aceitos do ponto. */
    private function sincronizaMateriais(int $idPonto, array $ids): void
    {
        $this->db->delete('DELETE FROM ponto_material WHERE id_ponto = ?', [$idPonto]);
        foreach ($ids as $idMat) {
            $idMat = (int) $idMat;
            if ($idMat > 0) {
                $this->db->insert(
                    'INSERT INTO ponto_material (id_ponto, id_material) VALUES (?, ?)',
                    [$idPonto, $idMat]
                );
            }
        }
    }

    /** Exclui um ponto e seus vinculos (materiais, horarios, fotos). */
    public function exclui(int $id): int
    {
        // ponto_material / ponto_horario / ponto_foto tem ON DELETE CASCADE,
        // mas removemos explicitamente por clareza.
        $this->db->delete('DELETE FROM ponto_material WHERE id_ponto = ?', [$id]);
        $this->db->delete('DELETE FROM ponto_horario WHERE id_ponto = ?', [$id]);
        $this->db->delete('DELETE FROM ponto_foto WHERE id_ponto = ?', [$id]);
        return $this->db->delete('DELETE FROM ponto_descarte WHERE id_ponto = ?', [$id]);
    }
}
