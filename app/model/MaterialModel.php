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
}
