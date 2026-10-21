<?php

/**
 * Model do sistema de pontos (livro-razao lancamento_pontos).
 *
 * O saldo de um usuario e SUM(quantidade): creditos positivos,
 * resgates/debitos negativos.
 */
class PontosModel extends BaseModel
{
    protected string $table = 'lancamento_pontos';
    protected string $pk = 'id_lancamento';

    /**
     * Quanto cada acao vale (creditos).
     *
     * Regra justa: o registro vale pouco (evita spam de ocorrencias falsas);
     * a maior parte dos pontos so entra quando a equipe confirma o problema
     * e marca a ocorrencia como RESOLVIDA. A foto so pontua se a ocorrencia
     * for confirmada (foto de evidencia valida).
     */
    public const VALORES = [
        'OCORRENCIA_REGISTRADA' => 10,  // ao registrar (logado e nao anonimo)
        'OCORRENCIA_RESOLVIDA'  => 40,  // quando a equipe confirma e resolve
        'FOTO_APROVADA'         => 15,  // foto de evidencia, creditada na confirmacao
    ];

    /** Maximo de ocorrencias que rendem pontos de registro por dia, por usuario. */
    public const LIMITE_REGISTROS_DIA = 3;

    /**
     * Regras de pontuacao prontas para as views (evita numeros fixos no HTML).
     */
    public static function regras(): array
    {
        $v = self::VALORES;
        return [
            'registro'   => $v['OCORRENCIA_REGISTRADA'],
            'resolvida'  => $v['OCORRENCIA_RESOLVIDA'],
            'foto'       => $v['FOTO_APROVADA'],
            'limiteDia'  => self::LIMITE_REGISTROS_DIA,
            'maximo'     => $v['OCORRENCIA_REGISTRADA'] + $v['OCORRENCIA_RESOLVIDA'] + $v['FOTO_APROVADA'],
        ];
    }

    /** Quantos lancamentos de um tipo o usuario recebeu hoje. */
    public function creditosHoje(int $idUsuario, string $tipo): int
    {
        $r = $this->db->select(
            'SELECT COUNT(*) AS n FROM lancamento_pontos
             WHERE id_usuario = ? AND tipo = ? AND quantidade > 0 AND criada_em >= CURDATE()',
            [$idUsuario, $tipo],
            'single'
        );
        return (int) ($r['n'] ?? 0);
    }

    /**
     * Credita o registro de uma ocorrencia respeitando o limite diario.
     * Retorna os pontos creditados (0 se o limite do dia ja foi atingido).
     */
    public function creditarRegistroOcorrencia(int $idUsuario, int $idOcorrencia, string $protocolo): int
    {
        if ($this->creditosHoje($idUsuario, 'OCORRENCIA_REGISTRADA') >= self::LIMITE_REGISTROS_DIA) {
            return 0;
        }
        return $this->creditarAcaoUnica($idUsuario, 'OCORRENCIA_REGISTRADA', $idOcorrencia, 'Ocorrência ' . $protocolo);
    }

    /** Saldo atual do usuario (SUM dos lancamentos). */
    public function saldo(int $idUsuario): int
    {
        $r = $this->db->select(
            'SELECT COALESCE(SUM(quantidade), 0) AS saldo
             FROM lancamento_pontos WHERE id_usuario = ?',
            [$idUsuario],
            'single'
        );
        return (int) ($r['saldo'] ?? 0);
    }

    /**
     * Registra um lancamento de pontos.
     *
     * @param int         $idUsuario
     * @param string      $tipo        ENUM de lancamento_pontos
     * @param int         $quantidade  assinado (+ credito, - debito)
     * @param string|null $descricao
     * @param array       $refs        ['id_ocorrencia'=>, 'id_resgate'=>, 'id_perfil_kids'=>]
     * @return int id do lancamento
     */
    public function lancar(int $idUsuario, string $tipo, int $quantidade, ?string $descricao = null, array $refs = []): int
    {
        return $this->db->insert(
            'INSERT INTO lancamento_pontos
               (id_usuario, id_perfil_kids, id_ocorrencia, id_resgate, tipo, quantidade, descricao)
             VALUES (?, ?, ?, ?, ?, ?, ?)',
            [
                $idUsuario,
                $refs['id_perfil_kids'] ?? null,
                $refs['id_ocorrencia'] ?? null,
                $refs['id_resgate'] ?? null,
                $tipo,
                $quantidade,
                $descricao,
            ]
        );
    }

    /**
     * Credita pontos de uma acao padrao (usa a tabela VALORES).
     * Retorna a quantidade creditada (0 se o tipo nao tiver valor).
     */
    public function creditarAcao(int $idUsuario, string $tipo, ?string $descricao = null, array $refs = []): int
    {
        $qtd = self::VALORES[$tipo] ?? 0;
        if ($qtd > 0) {
            $this->lancar($idUsuario, $tipo, $qtd, $descricao, $refs);
        }
        return $qtd;
    }

    /**
     * Ranking dos moradores por total de pontos (desc).
     * Administradores ficam fora (são eles que confirmam as ocorrências).
     * Inclui a contagem de ocorrencias de cada um.
     */
    public function ranking(int $limite = 10): array
    {
        return $this->db->select(
            'SELECT u.id_usuario, u.nome,
                    COALESCE(SUM(lp.quantidade), 0) AS pontos,
                    (SELECT COUNT(*) FROM ocorrencia o WHERE o.id_usuario = u.id_usuario) AS ocorrencias
             FROM usuario u
             JOIN lancamento_pontos lp ON lp.id_usuario = u.id_usuario
             WHERE u.ativo = 1 AND u.tipo <> \'ADMINISTRADOR\'
             GROUP BY u.id_usuario, u.nome
             HAVING pontos > 0
             ORDER BY pontos DESC
             LIMIT ' . (int) $limite
        );
    }

    /**
     * Credita uma ação uma única vez por ocorrência.
     * Considera o saldo líquido do tipo (créditos menos estornos): se um
     * crédito antigo foi estornado, a ação pode voltar a pontuar.
     */
    public function creditarAcaoUnica(int $idUsuario, string $tipo, int $idOcorrencia, ?string $descricao = null): int
    {
        $r = $this->db->select(
            'SELECT COALESCE(SUM(quantidade), 0) AS liquido FROM lancamento_pontos
             WHERE id_usuario = ? AND tipo = ? AND id_ocorrencia = ?',
            [$idUsuario, $tipo, $idOcorrencia],
            'single'
        );
        if ((int) ($r['liquido'] ?? 0) > 0) {
            return 0;
        }
        return $this->creditarAcao($idUsuario, $tipo, $descricao, ['id_ocorrencia' => $idOcorrencia]);
    }

    /** Total de participantes ativos com saldo positivo no programa. */
    public function totalParticipantes(): int
    {
        $r = $this->db->select(
            'SELECT COUNT(*) AS total FROM (
               SELECT u.id_usuario
               FROM usuario u
               JOIN lancamento_pontos lp ON lp.id_usuario = u.id_usuario
               WHERE u.ativo = 1 AND u.tipo <> \'ADMINISTRADOR\'
               GROUP BY u.id_usuario
               HAVING SUM(lp.quantidade) > 0
             ) participantes',
            [],
            'single'
        );
        return (int) ($r['total'] ?? 0);
    }

    /** Extrato (historico de lancamentos) de um usuario. */
    public function extrato(int $idUsuario, int $limite = 50): array
    {
        return $this->db->select(
            'SELECT tipo, quantidade, descricao, criada_em
             FROM lancamento_pontos
             WHERE id_usuario = ?
             ORDER BY criada_em DESC
             LIMIT ' . (int) $limite,
            [$idUsuario]
        );
    }

    /** Rotulo de nivel conforme o total de pontos. */
    public static function nivel(int $pontos): array
    {
        if ($pontos >= 2000) {
            return ['nome' => 'Ouro', 'icone' => '🏅', 'classe' => 'ouro'];
        }
        if ($pontos >= 1000) {
            return ['nome' => 'Prata', 'icone' => '🥈', 'classe' => 'prata'];
        }
        if ($pontos >= 300) {
            return ['nome' => 'Bronze', 'icone' => '🥉', 'classe' => 'bronze'];
        }
        return ['nome' => 'Iniciante', 'icone' => '🌱', 'classe' => 'iniciante'];
    }
}
