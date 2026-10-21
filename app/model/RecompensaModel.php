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

    /**
     * Resgate completo e atomico: confere saldo, baixa o estoque (se houver),
     * grava o resgate e debita os pontos na mesma transacao.
     * Lanca DomainException com mensagem amigavel quando nao for possivel.
     *
     * @return array ['id' => int, 'codigo' => string]
     */
    public function resgatarComDebito(int $idUsuario, array $rec): array
    {
        $pdo   = $this->db->conecta();
        $idRec = (int) $rec['id_recompensa'];
        $custo = (int) $rec['custo_pontos'];

        $pdo->beginTransaction();
        try {
            // Trava a linha do usuario: dois cliques simultaneos nao gastam o saldo duas vezes.
            $this->db->select('SELECT id_usuario FROM usuario WHERE id_usuario = ? FOR UPDATE', [$idUsuario], 'single');

            $s = $this->db->select(
                'SELECT COALESCE(SUM(quantidade), 0) AS saldo FROM lancamento_pontos WHERE id_usuario = ?',
                [$idUsuario],
                'single'
            );
            if ((int) ($s['saldo'] ?? 0) < $custo) {
                throw new DomainException('Pontos insuficientes para este resgate.');
            }

            // Estoque NULL = ilimitado; senao, baixa 1 unidade so se ainda houver.
            if ($rec['estoque'] !== null) {
                $baixou = $this->db->update(
                    'UPDATE recompensa SET estoque = estoque - 1 WHERE id_recompensa = ? AND estoque > 0',
                    [$idRec]
                );
                if ($baixou === 0) {
                    throw new DomainException('Esta recompensa esgotou. Aguarde a reposição.');
                }
            }

            $resgate = $this->resgatar($idUsuario, $idRec, $custo);
            $this->db->insert(
                'INSERT INTO lancamento_pontos (id_usuario, id_resgate, tipo, quantidade, descricao)
                 VALUES (?, ?, \'RESGATE\', ?, ?)',
                [$idUsuario, $resgate['id'], -$custo, 'Resgate: ' . $rec['nome']]
            );

            $pdo->commit();
            return $resgate;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
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
