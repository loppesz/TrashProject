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
}
