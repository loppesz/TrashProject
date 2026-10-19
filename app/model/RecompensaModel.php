<?php

/**
 * Model do catalogo de recompensas e dos resgates.
 */
class RecompensaModel extends BaseModel
{
    protected string $table = 'recompensa';
    protected string $pk = 'id_recompensa';

    /** Lista as recompensas ativas (disponiveis e em breve), ordem por custo. */
    public function listaAtivas(): array
    {
        return $this->db->select(
            'SELECT * FROM recompensa WHERE ativo = 1 ORDER BY custo_pontos'
        );
    }

    /** Busca uma recompensa ativa pelo id. */
    public function porId(int $id): array
    {
        return $this->db->select(
            'SELECT * FROM recompensa WHERE id_recompensa = ? AND ativo = 1 LIMIT 1',
            [$id],
            'single'
        );
    }

    /**
     * Efetua um resgate: grava em resgate_recompensa e retorna o id e o codigo.
     * (O debito de pontos e feito pelo controller via PontosModel.)
     *
     * @return array ['id' => int, 'codigo' => string]
     */
    public function resgatar(int $idUsuario, int $idRecompensa, int $custo): array
    {
        $codigo = $this->gerarCodigo();
        $id = $this->db->insert(
            'INSERT INTO resgate_recompensa
               (id_usuario, id_recompensa, codigo, pontos_utilizados, status, expira_em)
             VALUES (?, ?, ?, ?, \'SOLICITADO\', DATE_ADD(NOW(), INTERVAL 7 DAY))',
            [$idUsuario, $idRecompensa, $codigo, $custo]
        );
        return ['id' => $id, 'codigo' => $codigo];
    }

    /** Resgates de um usuario (mais recentes primeiro). */
    public function resgatesDoUsuario(int $idUsuario, int $limite = 20): array
    {
        return $this->db->select(
            'SELECT rr.*, r.nome AS recompensa_nome
             FROM resgate_recompensa rr
             JOIN recompensa r ON r.id_recompensa = rr.id_recompensa
             WHERE rr.id_usuario = ?
             ORDER BY rr.solicitado_em DESC
             LIMIT ' . (int) $limite,
            [$idUsuario]
        );
    }

    /** Gera um codigo unico de resgate (CF-XXXXXX). */
    private function gerarCodigo(): string
    {
        do {
            $codigo = 'CF-' . strtoupper(bin2hex(random_bytes(3)));
            $existe = $this->db->select(
                'SELECT 1 FROM resgate_recompensa WHERE codigo = ? LIMIT 1',
                [$codigo],
                'single'
            );
        } while (!empty($existe));
        return $codigo;
    }
}
