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
}
