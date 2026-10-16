<?php

/**
 * Model dos pontos de descarte.
 *
 * Monta a estrutura completa de cada ponto (dados + bairro + materiais +
 * horarios + fotos) que a tela de Pontos consome.
 */
class PontoModel extends BaseModel
{
    protected string $table = 'ponto_descarte';
    protected string $pk = 'id_ponto';

    /**
     * Lista todos os pontos ativos, cada um com:
     *  - dados da tabela ponto_descarte + nome do bairro
     *  - materiais[]  (array de nomes de material aceitos)
     *  - horarios[]   (array dia_semana => ['inicio','fim','fechado'])
     *  - fotos[]      (array de ['url','legenda'])
     */
    public function listaCompleta(): array
    {
        $pontos = $this->db->select(
            'SELECT p.*, b.nome AS bairro
             FROM ponto_descarte p
             JOIN bairro b ON b.id_bairro = p.id_bairro
             WHERE p.ativo = 1
             ORDER BY p.id_ponto'
        );

        foreach ($pontos as &$p) {
            $id = (int) $p['id_ponto'];
            $p['materiais'] = $this->materiais($id);
            $p['horarios']  = $this->horarios($id);
            $p['fotos']     = $this->fotos($id);
        }
        unset($p);

        return $pontos;
    }

    /** Nomes dos materiais aceitos em um ponto. */
    public function materiais(int $idPonto): array
    {
        $linhas = $this->db->select(
            'SELECT m.nome
             FROM ponto_material pm
             JOIN material m ON m.id_material = pm.id_material
             WHERE pm.id_ponto = ?
             ORDER BY m.nome',
            [$idPonto]
        );
        return array_column($linhas, 'nome');
    }

    /** Horarios por dia da semana de um ponto. */
    public function horarios(int $idPonto): array
    {
        return $this->db->select(
            'SELECT dia_semana, horario_inicio, horario_fim, fechado
             FROM ponto_horario
             WHERE id_ponto = ?
             ORDER BY dia_semana',
            [$idPonto]
        );
    }

    /** Fotos (galeria) de um ponto. */
    public function fotos(int $idPonto): array
    {
        return $this->db->select(
            'SELECT url_arquivo, legenda
             FROM ponto_foto
             WHERE id_ponto = ?
             ORDER BY ordem',
            [$idPonto]
        );
    }
}
