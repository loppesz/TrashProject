<?php

/**
 * Controller da consulta de coleta por bairro (comum e seletiva).
 * Horarios: cronograma oficial do DEMSUR (ver ColetaModel::FONTE).
 */
class Coleta extends BaseController
{
    public function index(): void
    {
        $model   = $this->model('Coleta');
        $bairros = $model->quadroPublico();
        $porTipo = $model->totalBairrosPorTipo();

        $this->view('coleta', [
            'tituloPagina' => 'Consultar Coleta – ColetaFácil',
            'descricao'    => 'Dias e horários da coleta comum e da coleta seletiva em todos os bairros de Muriaé-MG, conforme o cronograma do DEMSUR.',
            'cssPagina'    => ['coleta.css'],
            'jsPagina'     => ['coleta.js'],
            'menu'         => 'coleta',
            'bairros'       => $bairros,
            'bairrosSelect' => array_map(static fn($b) => ['id_bairro' => $b['id_bairro'], 'nome' => $b['nome']], $bairros),
            'resumoColeta'  => [
                'bairros'  => count($bairros),
                'comum'    => $porTipo['COMUM'],
                'seletiva' => $porTipo['SELETIVA'],
            ],
            'fonteColeta'   => ColetaModel::FONTE,
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
                echo json_encode(['ok' => false, 'motivo' => 'nao_encontrado']);
                exit;
            }

            $fonte = ColetaModel::FONTE;
            echo json_encode([
                'ok'             => true,
                'id_bairro'      => $d['id_bairro'],
                'nome'           => $d['nome'],
                'seletiva'       => $d['seletiva'],
                'comum'          => $d['comum'],
                'sem_cronograma' => empty($d['seletiva']) && empty($d['comum']),
                'equipe'         => $d['equipe'] ?: $fonte['orgao'],
                'telefone'       => $d['telefone'] ?: $fonte['telefone'],
                'fonte_url'      => $fonte['url'],
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
