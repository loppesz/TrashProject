<?php

/**
 * Model da politica de retencao e do inventario de dados pessoais (LGPD).
 *
 * Prazos em RETENCAO_DIAS (Config.php). A contagem de prazo das ocorrencias
 * comeca quando ela e encerrada (RESOLVIDA ou ARQUIVADA).
 */
class PrivacidadeModel extends BaseModel
{
    protected string $table = 'registro_privacidade';
    protected string $pk = 'id_registro';

    /** Condicao SQL: ocorrencia encerrada ha mais de :dias dias. */
    private const ENCERRADA_HA = "o.status IN ('RESOLVIDA','ARQUIVADA')
        AND COALESCE(o.resolvida_em, o.atualizada_em, o.criada_em) < (NOW() - INTERVAL ? DAY)";

    /** Quantos dados pessoais estao guardados hoje (visao do administrador). */
    public function inventario(): array
    {
        $n = fn(string $sql) => (int) ($this->db->select($sql, [], 'single')['n'] ?? 0);
        return [
            'usuarios'          => $n('SELECT COUNT(*) AS n FROM usuario'),
            'usuarios_aceite'   => $n('SELECT COUNT(*) AS n FROM usuario WHERE termos_aceitos_em IS NOT NULL'),
            'oc_com_contato'    => $n('SELECT COUNT(*) AS n FROM ocorrencia WHERE nome_contato IS NOT NULL OR telefone_contato IS NOT NULL OR email_contato IS NOT NULL'),
            'oc_com_gps'        => $n('SELECT COUNT(*) AS n FROM ocorrencia WHERE latitude IS NOT NULL'),
            'oc_anonimas'       => $n('SELECT COUNT(*) AS n FROM ocorrencia WHERE anonima = 1'),
            'oc_ocultas'        => $n('SELECT COUNT(*) AS n FROM ocorrencia WHERE visivel_publicamente = 0'),
            'fotos'             => $n('SELECT COUNT(*) AS n FROM ocorrencia_foto'),
            'perfis_kids'       => $n('SELECT COUNT(*) AS n FROM perfil_kids'),
            'tentativas'        => $n('SELECT COUNT(*) AS n FROM registro_acao'),
        ];
    }

    /** O que a retencao apagaria se fosse aplicada agora. */
    public function pendencias(): array
    {
        $r = RETENCAO_DIAS;
        $n = fn(string $sql, array $p) => (int) ($this->db->select($sql, $p, 'single')['n'] ?? 0);
        return [
            'contatos' => $n('SELECT COUNT(*) AS n FROM ocorrencia o WHERE ' . self::ENCERRADA_HA . '
                              AND (o.nome_contato IS NOT NULL OR o.telefone_contato IS NOT NULL OR o.email_contato IS NOT NULL)',
                             [$r['contato_ocorrencia']]),
            'gps'      => $n('SELECT COUNT(*) AS n FROM ocorrencia o WHERE ' . self::ENCERRADA_HA . '
                              AND o.latitude IS NOT NULL
                              AND (o.latitude <> ROUND(o.latitude, 3) OR o.longitude <> ROUND(o.longitude, 3))',
                             [$r['gps_ocorrencia']]),
            'fotos'    => $n('SELECT COUNT(*) AS n FROM ocorrencia_foto f JOIN ocorrencia o ON o.id_ocorrencia = f.id_ocorrencia
                              WHERE ' . self::ENCERRADA_HA, [$r['foto_ocorrencia']]),
            'tentativas' => $n('SELECT COUNT(*) AS n FROM registro_acao WHERE criada_em < (NOW() - INTERVAL ? DAY)',
                               [$r['registro_acao']]),
            'auditoria'  => $n('SELECT COUNT(*) AS n FROM registro_privacidade WHERE criada_em < (NOW() - INTERVAL ? DAY)',
                               [$r['registro_privacidade']]),
        ];
    }

    /**
     * Aplica a politica de retencao. Tudo em uma transacao.
     *  - apaga contatos de ocorrencias encerradas;
     *  - troca o GPS exato por um aproximado (~100 m) — o mapa continua util;
     *  - apaga fotos (do banco e arquivos legados em disco);
     *  - limpa controle de tentativas e auditoria antiga.
     *
     * @return array quantidade afetada em cada item
     */
    public function aplicarRetencao(int $idAdmin): array
    {
        $r   = RETENCAO_DIAS;
        $pdo = $this->db->conecta();
        $res = [];

        // Arquivos legados das fotos que serao apagadas (removidos apos o commit).
        $legados = $this->db->select(
            'SELECT f.url_arquivo FROM ocorrencia_foto f JOIN ocorrencia o ON o.id_ocorrencia = f.id_ocorrencia
             WHERE ' . self::ENCERRADA_HA . ' AND f.url_arquivo IS NOT NULL',
            [$r['foto_ocorrencia']]
        );

        $pdo->beginTransaction();
        try {
            $st = $pdo->prepare('UPDATE ocorrencia o
                SET o.nome_contato = NULL, o.telefone_contato = NULL, o.email_contato = NULL
                WHERE ' . self::ENCERRADA_HA . '
                  AND (o.nome_contato IS NOT NULL OR o.telefone_contato IS NOT NULL OR o.email_contato IS NOT NULL)');
            $st->execute([$r['contato_ocorrencia']]);
            $res['contatos'] = $st->rowCount();

            $st = $pdo->prepare('UPDATE ocorrencia o
                SET o.latitude = ROUND(o.latitude, 3), o.longitude = ROUND(o.longitude, 3)
                WHERE ' . self::ENCERRADA_HA . ' AND o.latitude IS NOT NULL
                  AND (o.latitude <> ROUND(o.latitude, 3) OR o.longitude <> ROUND(o.longitude, 3))');
            $st->execute([$r['gps_ocorrencia']]);
            $res['gps'] = $st->rowCount();

            $st = $pdo->prepare('DELETE f FROM ocorrencia_foto f JOIN ocorrencia o ON o.id_ocorrencia = f.id_ocorrencia
                WHERE ' . self::ENCERRADA_HA);
            $st->execute([$r['foto_ocorrencia']]);
            $res['fotos'] = $st->rowCount();

            $st = $pdo->prepare('DELETE FROM registro_acao WHERE criada_em < (NOW() - INTERVAL ? DAY)');
            $st->execute([$r['registro_acao']]);
            $res['tentativas'] = $st->rowCount();

            $st = $pdo->prepare('DELETE FROM registro_privacidade WHERE criada_em < (NOW() - INTERVAL ? DAY)');
            $st->execute([$r['registro_privacidade']]);
            $res['auditoria'] = $st->rowCount();

            $pdo->prepare('INSERT INTO registro_privacidade (acao, id_usuario, detalhes) VALUES (?, ?, ?)')
                ->execute(['RETENCAO_APLICADA', $idAdmin, mb_substr(json_encode($res), 0, 255)]);
            $pdo->prepare('INSERT INTO config_sistema (chave, valor) VALUES (\'retencao_ultima\', ?)
                           ON DUPLICATE KEY UPDATE valor = VALUES(valor)')
                ->execute([date('Y-m-d H:i:s')]);

            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }

        // Fotos antigas que estavam em disco (antes de irem para o banco).
        foreach ($legados as $f) {
            $caminho = realpath(__DIR__ . '/../../' . $f['url_arquivo']);
            $pasta   = realpath(__DIR__ . '/../../assets/uploads');
            if ($caminho && $pasta && str_starts_with($caminho, $pasta) && is_file($caminho)) {
                @unlink($caminho);
            }
        }

        return $res;
    }

    /** Data da ultima aplicacao da retencao (ou null). */
    public function ultimaRetencao(): ?string
    {
        $r = $this->db->select("SELECT valor FROM config_sistema WHERE chave = 'retencao_ultima' LIMIT 1", [], 'single');
        return !empty($r['valor']) ? (string) $r['valor'] : null;
    }

    /** Ultimos registros da trilha de auditoria. */
    public function auditoria(int $limite = 20): array
    {
        return $this->db->select(
            'SELECT rp.acao, rp.detalhes, rp.criada_em, u.nome AS usuario
             FROM registro_privacidade rp
             LEFT JOIN usuario u ON u.id_usuario = rp.id_usuario
             ORDER BY rp.criada_em DESC, rp.id_registro DESC
             LIMIT ' . (int) $limite
        );
    }
}
