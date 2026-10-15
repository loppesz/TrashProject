<?php

/**
 * Controller do registro e consulta de ocorrencias ambientais.
 */
class Ocorrencia extends BaseController
{
    public function index(): void
    {
        // Métricas do painel. O projeto ainda não persiste ocorrências,
        // então começam zeradas (estado padrão de projeto em andamento).
        // Quando houver model/banco, basta substituir estes valores pela consulta.
        $registradas = 0;
        $resolvidas  = 0;
        $taxa        = $registradas > 0 ? (int) round(($resolvidas / $registradas) * 100) : 0;

        $this->view('ocorrencias', [
            'tituloPagina' => 'Ocorrências – ColetaFácil',
            'descricao'    => 'Registre descartes irregulares e problemas na coleta em Muriaé-MG. Acompanhe o status e ganhe pontos de recompensa.',
            'cssPagina'    => ['ocorrencias.css'],
            'jsPagina'     => ['ocorrencias.js'],
            'menu'         => 'ocorrencias',
            'ocRegistradas' => $registradas,
            'ocResolvidas'  => $resolvidas,
            'ocTaxa'        => $taxa,
        ]);
    }
}
