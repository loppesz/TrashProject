<?php

/**
 * Controller dos pontos de descarte (locais de entrega voluntaria).
 */
class Ponto extends BaseController
{
    public function index(): void
    {
        $pontos = $this->model('Ponto')->listaCompleta();

        $this->view('pontos', [
            'tituloPagina' => 'Pontos de Descarte – ColetaFácil',
            'descricao'    => 'Encontre pontos de descarte de materiais especiais em Muriaé-MG: pilhas, eletrônicos, óleo, medicamentos e mais.',
            'cssPagina'    => ['pontos.css'],
            'jsPagina'     => ['pontos.js', 'mapa.js'],
            'menu'         => 'pontos',
            'pontos'       => $pontos,
            'sugestaoOk'   => isset($_GET['sugestao']) && $_GET['sugestao'] === 'ok',
            'sugestaoErro' => $_GET['erro'] ?? '',
        ]);
    }

    /**
     * Recebe uma sugestao de novo ponto de descarte (POST do formulario publico).
     * Grava com status NOVA para posterior analise do admin.
     */
    public function sugerir(): void
    {
        if (!$this->request->isPost()) {
            header('Location: ' . baseUrl('ponto'));
            exit;
        }

        $nome     = trim((string) $this->request->post('nome_local'));
        $endereco = trim((string) $this->request->post('endereco'));
        $materiais = trim((string) $this->request->post('materiais'));
        $horario  = trim((string) $this->request->post('horario'));
        $obs      = trim((string) $this->request->post('observacao'));

        if ($nome === '' || $endereco === '') {
            header('Location: ' . baseUrl('ponto') . '?erro=' . rawurlencode('Informe ao menos o nome do local e o endereço.'));
            exit;
        }

        try {
            $usuario = auth_user();
            $this->model('Sugestao')->registrar([
                'id_usuario' => $usuario['id_usuario'] ?? null,
                'nome_local' => $nome,
                'endereco'   => $endereco,
                'materiais'  => $materiais,
                'horario'    => $horario,
                'observacao' => $obs,
            ]);
            header('Location: ' . baseUrl('ponto') . '?sugestao=ok#sugira');
            exit;
        } catch (Throwable $e) {
            header('Location: ' . baseUrl('ponto') . '?erro=' . rawurlencode('Não foi possível enviar a sugestão. Tente novamente.'));
            exit;
        }
    }
}
