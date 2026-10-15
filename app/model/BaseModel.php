<?php

require_once 'app/library/Database.php';

/**
 * Classe base para todos os Models.
 *
 * Fornece acesso a conexao ($db) e operacoes genericas de leitura.
 * A tabela e a chave primaria sao definidas por cada Model filho via
 * $table e $pk, ja que o schema do ColetaFacil usa PKs nomeadas
 * (id_usuario, id_ponto, id_ocorrencia, ...), nao um "id" generico.
 */
class BaseModel
{
    protected Database $db;
    protected string $table = '';
    protected string $pk = 'id';

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Lista todos os registros da tabela, ordenados por um campo.
     * Por padrao ordena pela chave primaria do Model.
     */
    public function lista(string $orderField = ''): array
    {
        $ordem = $orderField !== '' ? $orderField : $this->pk;
        return $this->db->select("SELECT * FROM {$this->table} ORDER BY {$ordem}");
    }

    /**
     * Busca um registro pela chave primaria.
     *
     * @return array Registro encontrado ou array vazio.
     */
    public function getById(int $id): array
    {
        return $this->db->select(
            "SELECT * FROM {$this->table} WHERE {$this->pk} = ?",
            [$id],
            'single'
        );
    }
}
