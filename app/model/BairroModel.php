<?php

/**
 * Model da tabela bairro (apoio aos selects do admin).
 */
class BairroModel extends BaseModel
{
    protected string $table = 'bairro';
    protected string $pk = 'id_bairro';

    /** Lista os bairros ativos em ordem alfabetica. */
    public function listaAtivos(): array
    {
        return $this->db->select(
            'SELECT id_bairro, nome FROM bairro WHERE ativo = 1 ORDER BY nome'
        );
    }

    /** Total de bairros ativos com ao menos uma rota de coleta ativa. */
    public function totalAtendidos(): int
    {
        $r = $this->db->select(
            'SELECT COUNT(DISTINCT c.id_bairro) AS total
             FROM coleta c
             JOIN bairro b ON b.id_bairro = c.id_bairro
             WHERE c.ativo = 1 AND b.ativo = 1',
            [],
            'single'
        );
        return (int) ($r['total'] ?? 0);
    }
}
