<?php

/**
 * Controller da consulta de coleta seletiva por bairro.
 */
class Coleta extends BaseController
{
    public function index(): void
    {
        $model = $this->model('Coleta');

        $this->view('coleta', [
            'tituloPagina' => 'Consultar Coleta – ColetaFácil',
            'descricao'    => 'Consulte o calendário de coleta seletiva em Muriaé-MG. Veja os dias e horários de coleta por bairro.',
            'cssPagina'    => ['coleta.css'],
            'jsPagina'     => ['coleta.js'],
            'menu'         => 'coleta',
            'bairros'         => $model->porBairro(),
            'bairrosSelect'  => $model->bairrosComColeta(),
        ]);
    }

    /**
     * Consulta de coleta por bairro (retorno JSON, usado pelo form de
     * consulta e pelos botoes "Ver detalhes" da tabela via fetch).
     */
    public function consultarJson(): void
    {
        // Descarta qualquer saida espuria (BOM, warnings) antes do JSON, para
        // nao quebrar o fetch no navegador. Emite sempre JSON valido.
        if (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/json; charset=utf-8');

        try {
            $idBairro = (int) $this->request->get('id_bairro');
            if ($idBairro <= 0) {
                echo json_encode(['ok' => false, 'motivo' => 'vazio']);
                exit;
            }

            $d = $this->model('Coleta')->detalheBairro($idBairro);
            if ($d === null) {
                echo json_encode(['ok' => false, 'motivo' => 'sem_coleta']);
                exit;
            }

            echo json_encode([
                'ok'             => true,
                'id_bairro'      => $d['id_bairro'],
                'nome'           => $d['nome'],
                'dias'           => $d['dias'],
                'horario'        => formataHora($d['inicio']) . ' às ' . formataHora($d['fim']),
                'equipe'         => $d['equipe'],
                'materiais'      => array_map(static fn($m) => [
                    'nome'  => $m['nome'],
                    'icone' => $m['icone'],
                    'cor'   => strtolower((string) ($m['cor_lixeira'] ?? '')),
                ], $d['materiais']),
                'nao_recolhidos' => array_map(static fn($m) => [
                    'nome'  => $m['nome'],
                    'icone' => $m['icone'],
                ], $d['nao_recolhidos']),
            ], JSON_UNESCAPED_UNICODE);
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'motivo' => 'erro']);
        }
        exit;
    }
}
