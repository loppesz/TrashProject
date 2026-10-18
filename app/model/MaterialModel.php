<?php

/**
 * Model do guia de materiais.
 */
class MaterialModel extends BaseModel
{
    protected string $table = 'material';
    protected string $pk = 'id_material';

    /**
     * Lista os materiais ativos, cada um com:
     *  - dados da tabela material
     *  - instrucoes[] (lista ordenada de preparo)
     *  - exemplos[]   (tags)
     */
    public function listaCompleta(): array
    {
        $materiais = $this->db->select(
            'SELECT * FROM material WHERE ativo = 1 ORDER BY id_material'
        );

        foreach ($materiais as &$m) {
            $id = (int) $m['id_material'];
            $m['instrucoes'] = $this->instrucoes($id);
            $m['exemplos']   = $this->exemplos($id);
        }
        unset($m);

        return $materiais;
    }

    /** Instrucoes de preparo de um material. */
    public function instrucoes(int $idMaterial): array
    {
        return $this->db->select(
            'SELECT texto, perigo FROM material_instrucao
             WHERE id_material = ? ORDER BY ordem',
            [$idMaterial]
        );
    }

    /** Exemplos (tags) de um material. */
    public function exemplos(int $idMaterial): array
    {
        return $this->db->select(
            'SELECT icone, rotulo, negativo FROM material_exemplo
             WHERE id_material = ? ORDER BY ordem',
            [$idMaterial]
        );
    }

    // =================================================================
    // CRUD (area administrativa)
    // =================================================================

    /**
     * Cria um material. $d: nome, status_coleta, nivel_risco, descricao,
     * icone, cor_card, preparo_label, alerta_seguranca.
     *
     * @return int id gerado
     */
    public function cria(array $d): int
    {
        return $this->db->insert(
            'INSERT INTO material
               (nome, descricao, aceito_coleta_seletiva, status_coleta, icone, cor_card,
                preparo_label, nivel_risco, alerta_seguranca, ativo)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)',
            [
                $d['nome'],
                $d['descricao'] ?: '',
                $d['status_coleta'] === 'NAO_ACEITO' ? 0 : 1,
                $d['status_coleta'],
                $d['icone'] ?: null,
                $d['cor_card'] ?: null,
                $d['preparo_label'] ?: 'Como preparar',
                $d['nivel_risco'],
                $d['alerta_seguranca'] ?: null,
            ]
        );
    }

    /** Atualiza um material pelos campos principais. */
    public function atualiza(int $id, array $d): int
    {
        return $this->db->update(
            'UPDATE material SET
               nome = ?, descricao = ?, aceito_coleta_seletiva = ?, status_coleta = ?,
               icone = ?, cor_card = ?, preparo_label = ?, nivel_risco = ?, alerta_seguranca = ?
             WHERE id_material = ?',
            [
                $d['nome'],
                $d['descricao'] ?: '',
                $d['status_coleta'] === 'NAO_ACEITO' ? 0 : 1,
                $d['status_coleta'],
                $d['icone'] ?: null,
                $d['cor_card'] ?: null,
                $d['preparo_label'] ?: 'Como preparar',
                $d['nivel_risco'],
                $d['alerta_seguranca'] ?: null,
                $id,
            ]
        );
    }

    /** True se o material estiver referenciado em pontos ou coletas. */
    public function emUso(int $id): bool
    {
        $p = $this->db->select('SELECT 1 FROM ponto_material WHERE id_material = ? LIMIT 1', [$id], 'single');
        if (!empty($p)) {
            return true;
        }
        $c = $this->db->select('SELECT 1 FROM coleta_material WHERE id_material = ? LIMIT 1', [$id], 'single');
        return !empty($c);
    }

    /**
     * Exclui um material. Remove antes as instrucoes/exemplos (filhos).
     * Nao exclui se estiver em uso (retorna 0).
     */
    public function exclui(int $id): int
    {
        if ($this->emUso($id)) {
            return 0;
        }
        $this->db->delete('DELETE FROM material_instrucao WHERE id_material = ?', [$id]);
        $this->db->delete('DELETE FROM material_exemplo WHERE id_material = ?', [$id]);
        return $this->db->delete('DELETE FROM material WHERE id_material = ?', [$id]);
    }
}
