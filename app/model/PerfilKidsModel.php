<?php

/**
 * Model dos perfis da Area Kids.
 *
 * Um usuario (pai/responsavel) pode ter varios perfis (filhos).
 */
class PerfilKidsModel extends BaseModel
{
    protected string $table = 'perfil_kids';
    protected string $pk = 'id_perfil_kids';

    /** Lista os perfis de um usuario. */
    public function porUsuario(int $idUsuario): array
    {
        return $this->db->select(
            'SELECT * FROM perfil_kids WHERE id_usuario = ? ORDER BY id_perfil_kids',
            [$idUsuario]
        );
    }

    /** Busca um perfil garantindo que pertence ao usuario. */
    public function doUsuario(int $idPerfil, int $idUsuario): array
    {
        return $this->db->select(
            'SELECT * FROM perfil_kids WHERE id_perfil_kids = ? AND id_usuario = ? LIMIT 1',
            [$idPerfil, $idUsuario],
            'single'
        );
    }

    /** Cria um perfil para o usuario. Retorna o id. */
    public function cria(int $idUsuario, string $apelido, int $pontos = 0, int $jogos = 0): int
    {
        $apelido = mb_substr(trim($apelido) !== '' ? trim($apelido) : 'Eco Herói', 0, 16);
        return $this->db->insert(
            'INSERT INTO perfil_kids (id_usuario, apelido, pontos, jogos_concluidos)
             VALUES (?, ?, ?, ?)',
            [$idUsuario, $apelido, max(0, $pontos), max(0, $jogos)]
        );
    }

    /** Atualiza pontos/apelido/jogos de um perfil do usuario. */
    public function atualiza(int $idPerfil, int $idUsuario, string $apelido, int $pontos, int $jogos): int
    {
        $apelido = mb_substr(trim($apelido) !== '' ? trim($apelido) : 'Eco Herói', 0, 16);
        return $this->db->update(
            'UPDATE perfil_kids SET apelido = ?, pontos = ?, jogos_concluidos = ?
             WHERE id_perfil_kids = ? AND id_usuario = ?',
            [$apelido, max(0, $pontos), max(0, $jogos), $idPerfil, $idUsuario]
        );
    }

    /** Exclui um perfil do usuario. */
    public function exclui(int $idPerfil, int $idUsuario): int
    {
        return $this->db->delete(
            'DELETE FROM perfil_kids WHERE id_perfil_kids = ? AND id_usuario = ?',
            [$idPerfil, $idUsuario]
        );
    }
}
