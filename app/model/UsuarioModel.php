<?php

/**
 * Model da tabela usuario.
 */
class UsuarioModel extends BaseModel
{
    protected string $table = 'usuario';
    protected string $pk = 'id_usuario';

    /**
     * Busca um usuario ativo pelo e-mail (para autenticacao).
     *
     * @return array Registro do usuario ou array vazio.
     */
    public function buscaPorEmail(string $email): array
    {
        return $this->db->select(
            'SELECT id_usuario, nome, email, senha_hash, tipo, ativo
             FROM usuario WHERE email = ? LIMIT 1',
            [$email],
            'single'
        );
    }

    /**
     * True se o e-mail ja estiver cadastrado.
     */
    public function emailExiste(string $email): bool
    {
        $r = $this->db->select(
            'SELECT id_usuario FROM usuario WHERE email = ? LIMIT 1',
            [$email],
            'single'
        );
        return !empty($r);
    }

    /**
     * Cria um novo usuario (tipo MORADOR, ativo).
     *
     * @return int Id do usuario criado.
     */
    public function cria(string $nome, string $email, string $senhaHash, ?string $telefone): int
    {
        return $this->db->insert(
            'INSERT INTO usuario (nome, email, senha_hash, telefone, tipo, ativo)
             VALUES (?, ?, ?, ?, \'MORADOR\', TRUE)',
            [$nome, $email, $senhaHash, $telefone]
        );
    }
}
