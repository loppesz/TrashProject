<?php

/**
 * Model da tabela usuario (e dos direitos do titular — LGPD art. 18).
 */
class UsuarioModel extends BaseModel
{
    protected string $table = 'usuario';
    protected string $pk = 'id_usuario';

    /**
     * Busca um usuario pelo e-mail (para autenticacao).
     *
     * @return array Registro do usuario ou array vazio.
     */
    public function buscaPorEmail(string $email): array
    {
        return $this->db->select(
            'SELECT id_usuario, nome, email, senha_hash, tipo, ativo
             FROM usuario WHERE email = ? LIMIT 1',
            [mb_strtolower(trim($email))],
            'single'
        );
    }

    /** Dados do proprio usuario para a pagina Minha conta (sem a senha). */
    public function perfil(int $idUsuario): array
    {
        return $this->db->select(
            'SELECT id_usuario, nome, email, telefone, tipo, criado_em, atualizado_em,
                    termos_aceitos_em, termos_versao
             FROM usuario WHERE id_usuario = ? LIMIT 1',
            [$idUsuario],
            'single'
        );
    }

    /** Hash da senha atual (para confirmar acoes sensiveis). */
    public function senhaHash(int $idUsuario): string
    {
        $r = $this->db->select('SELECT senha_hash FROM usuario WHERE id_usuario = ? LIMIT 1', [$idUsuario], 'single');
        return (string) ($r['senha_hash'] ?? '');
    }

    /**
     * True se o e-mail ja estiver cadastrado.
     */
    public function emailExiste(string $email): bool
    {
        $r = $this->db->select(
            'SELECT id_usuario FROM usuario WHERE email = ? LIMIT 1',
            [mb_strtolower(trim($email))],
            'single'
        );
        return !empty($r);
    }

    /**
     * Cria um novo usuario (tipo MORADOR, ativo), registrando o aceite
     * dos Termos/Politica (data + versao) como prova do consentimento.
     *
     * @return int Id do usuario criado.
     */
    public function cria(string $nome, string $email, string $senhaHash, ?string $telefone, string $versaoTermos = TERMOS_VERSAO): int
    {
        return $this->db->insert(
            'INSERT INTO usuario (nome, email, senha_hash, telefone, tipo, ativo, termos_aceitos_em, termos_versao)
             VALUES (?, ?, ?, ?, \'MORADOR\', TRUE, NOW(), ?)',
            [$nome, mb_strtolower(trim($email)), $senhaHash, $telefone, $versaoTermos]
        );
    }

    /** Correcao de dados pelo titular (nome e telefone). */
    public function atualizaPerfil(int $idUsuario, string $nome, ?string $telefone): int
    {
        return $this->db->update(
            'UPDATE usuario SET nome = ?, telefone = ? WHERE id_usuario = ?',
            [$nome, $telefone, $idUsuario]
        );
    }

    /** Grava um novo hash de senha (troca de senha ou rehash automatico). */
    public function atualizaSenha(int $idUsuario, string $senhaHash): int
    {
        return $this->db->update(
            'UPDATE usuario SET senha_hash = ? WHERE id_usuario = ?',
            [$senhaHash, $idUsuario]
        );
    }

    /**
     * Portabilidade/acesso (LGPD art. 18, II e V): todos os dados pessoais
     * do titular em um unico pacote. Nunca inclui o hash da senha.
     */
    public function dadosParaExportacao(int $idUsuario): array
    {
        $q = fn(string $sql) => $this->db->select($sql, [$idUsuario]);

        return [
            'conta' => $this->perfil($idUsuario),
            'ocorrencias' => $q(
                'SELECT o.protocolo, o.tipo, o.urgencia, o.status, o.endereco_descricao, b.nome AS bairro,
                        o.latitude, o.longitude, o.data_ocorrencia, o.descricao, o.anonima,
                        o.nome_contato, o.telefone_contato, o.email_contato, o.visivel_publicamente,
                        o.observacao_admin, o.criada_em, o.resolvida_em,
                        (SELECT COUNT(*) FROM ocorrencia_foto f WHERE f.id_ocorrencia = o.id_ocorrencia) AS fotos
                 FROM ocorrencia o JOIN bairro b ON b.id_bairro = o.id_bairro
                 WHERE o.id_usuario = ? ORDER BY o.criada_em'
            ),
            'pontos' => $q(
                'SELECT tipo, quantidade, descricao, criada_em FROM lancamento_pontos
                 WHERE id_usuario = ? ORDER BY criada_em'
            ),
            'resgates' => $q(
                'SELECT r.nome AS recompensa, rr.codigo, rr.pontos_utilizados, rr.status,
                        rr.solicitado_em, rr.expira_em, rr.utilizado_em
                 FROM resgate_recompensa rr JOIN recompensa r ON r.id_recompensa = rr.id_recompensa
                 WHERE rr.id_usuario = ? ORDER BY rr.solicitado_em'
            ),
            'perfis_kids' => $q(
                'SELECT apelido, pontos, jogos_concluidos, atualizado_em FROM perfil_kids
                 WHERE id_usuario = ? ORDER BY id_perfil_kids'
            ),
            'sugestoes_de_pontos' => $q(
                'SELECT nome_local, endereco, materiais_informados, horario_informado, observacao, status, criada_em
                 FROM sugestao_ponto_descarte WHERE id_usuario = ? ORDER BY criada_em'
            ),
        ];
    }

    /** Resumo para a pagina Minha conta (quantos registros existem). */
    public function resumoDados(int $idUsuario): array
    {
        $conta = fn(string $sql) => (int) ($this->db->select($sql, [$idUsuario], 'single')['n'] ?? 0);
        return [
            'ocorrencias' => $conta('SELECT COUNT(*) AS n FROM ocorrencia WHERE id_usuario = ?'),
            'pontos'      => $conta('SELECT COALESCE(SUM(quantidade), 0) AS n FROM lancamento_pontos WHERE id_usuario = ?'),
            'resgates'    => $conta('SELECT COUNT(*) AS n FROM resgate_recompensa WHERE id_usuario = ?'),
            'perfis_kids' => $conta('SELECT COUNT(*) AS n FROM perfil_kids WHERE id_usuario = ?'),
            'sugestoes'   => $conta('SELECT COUNT(*) AS n FROM sugestao_ponto_descarte WHERE id_usuario = ?'),
        ];
    }

    /**
     * Eliminacao (LGPD art. 18, VI): apaga a conta e os dados ligados a ela.
     *
     *  - Ocorrencias continuam existindo (sao de interesse publico e da gestao),
     *    mas ficam ANONIMIZADAS: sem vinculo com a conta e sem nome/telefone/e-mail.
     *  - Sugestoes de pontos perdem o vinculo (SET NULL).
     *  - Pontos, resgates, perfis Kids e sessoes sao apagados.
     *
     * Tudo em uma transacao: ou apaga tudo, ou nada.
     */
    public function excluirConta(int $idUsuario): void
    {
        $pdo = $this->db->conecta();
        $pdo->beginTransaction();
        try {
            $exec = function (string $sql) use ($pdo, $idUsuario): void {
                $pdo->prepare($sql)->execute([$idUsuario]);
            };
            $exec('UPDATE ocorrencia
                      SET id_usuario = NULL, anonima = 1,
                          nome_contato = NULL, telefone_contato = NULL, email_contato = NULL
                    WHERE id_usuario = ?');
            $exec('UPDATE ocorrencia_historico_status SET id_usuario_responsavel = NULL WHERE id_usuario_responsavel = ?');
            $exec('UPDATE sugestao_ponto_descarte SET id_usuario = NULL WHERE id_usuario = ?');
            $exec('DELETE FROM lancamento_pontos WHERE id_usuario = ?');
            $exec('DELETE FROM resgate_recompensa WHERE id_usuario = ?');
            $exec('DELETE FROM perfil_kids WHERE id_usuario = ?');
            $exec('DELETE FROM sessao_usuario WHERE id_usuario = ?');
            $exec('DELETE FROM usuario WHERE id_usuario = ?');
            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    /** Trilha de auditoria de acoes sobre dados pessoais (sem o conteudo dos dados). */
    public function registrarPrivacidade(string $acao, ?int $idUsuario, ?string $detalhes = null): void
    {
        try {
            $this->db->insert(
                'INSERT INTO registro_privacidade (acao, id_usuario, detalhes) VALUES (?, ?, ?)',
                [$acao, $idUsuario, $detalhes !== null ? mb_substr($detalhes, 0, 255) : null]
            );
        } catch (Throwable $e) {
            // a auditoria nao pode impedir o titular de exercer o direito
        }
    }
}
