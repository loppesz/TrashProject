<?php

/**
 * Model das sugestoes de pontos de descarte enviadas pela comunidade.
 *
 * Fluxo: o formulario publico grava uma sugestao com status NOVA; o admin
 * analisa e muda o status para APROVADA ou RECUSADA. Ao aprovar, o admin
 * pode optar por criar o ponto de descarte correspondente.
 */
class SugestaoModel extends BaseModel
{
    protected string $table = 'sugestao_ponto_descarte';
    protected string $pk = 'id_sugestao';

    /**
     * Registra uma nova sugestao (status inicial NOVA).
     *
     * @param array $d nome_local, endereco, materiais, horario, observacao, id_usuario
     * @return int id da sugestao criada
     */
    public function registrar(array $d): int
    {
        return $this->db->insert(
            'INSERT INTO sugestao_ponto_descarte
               (id_usuario, nome_local, endereco, materiais_informados,
                horario_informado, observacao, status)
             VALUES (?, ?, ?, ?, ?, ?, \'NOVA\')',
            [
                $d['id_usuario'] ?? null,
                $d['nome_local'],
                $d['endereco'],
                $d['materiais'] ?: null,
                $d['horario'] ?: null,
                $d['observacao'] ?: null,
            ]
        );
    }

    /** Lista todas as sugestoes (admin), mais recentes primeiro. */
    public function listaTodas(int $limite = 200): array
    {
        return $this->db->select(
            'SELECT s.*, u.nome AS usuario_nome
             FROM sugestao_ponto_descarte s
             LEFT JOIN usuario u ON u.id_usuario = s.id_usuario
             ORDER BY s.criada_em DESC
             LIMIT ' . (int) $limite
        );
    }

    /** Busca uma sugestao pelo id. */
    public function porId(int $id): array
    {
        return $this->db->select(
            'SELECT * FROM sugestao_ponto_descarte WHERE id_sugestao = ? LIMIT 1',
            [$id],
            'single'
        ) ?: [];
    }

    /** Contagem de sugestoes por status. Retorna [status => total]. */
    public function contagemPorStatus(): array
    {
        $linhas = $this->db->select(
            'SELECT status, COUNT(*) AS total FROM sugestao_ponto_descarte GROUP BY status'
        );
        $out = [];
        foreach ($linhas as $l) {
            $out[$l['status']] = (int) $l['total'];
        }
        return $out;
    }

    /** Total de sugestoes pendentes (NOVA ou EM_ANALISE). */
    public function totalPendentes(): int
    {
        $r = $this->db->select(
            'SELECT COUNT(*) AS n FROM sugestao_ponto_descarte WHERE status IN (\'NOVA\', \'EM_ANALISE\')',
            [],
            'single'
        );
        return (int) ($r['n'] ?? 0);
    }

    /**
     * Atualiza o status de analise de uma sugestao (APROVADA / RECUSADA / EM_ANALISE).
     *
     * @return int linhas afetadas
     */
    public function atualizarStatus(int $id, string $status, int $idAdmin): int
    {
        $validos = ['NOVA', 'EM_ANALISE', 'APROVADA', 'RECUSADA'];
        if (!in_array($status, $validos, true)) {
            return 0;
        }

        return $this->db->update(
            'UPDATE sugestao_ponto_descarte
             SET status = ?, analisada_em = NOW(), id_administrador_analise = ?
             WHERE id_sugestao = ?',
            [$status, $idAdmin, $id]
        );
    }
}
