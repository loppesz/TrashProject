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
            'usaLeaflet'    => true,
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

        $nome      = mb_substr(trim(strip_tags((string) $this->request->post('nome_local'))), 0, 150);
        $endereco  = mb_substr(trim(strip_tags((string) $this->request->post('endereco'))), 0, 200);
        $materiais = mb_substr(trim(strip_tags((string) $this->request->post('materiais'))), 0, 500);
        $horario   = mb_substr(trim(strip_tags((string) $this->request->post('horario'))), 0, 150);
        $obs       = mb_substr(trim(strip_tags((string) $this->request->post('observacao'))), 0, 1000);

        // Robo preencheu o campo isca: finge sucesso sem gravar nada.
        if (honeypot_acionado()) {
            header('Location: ' . baseUrl('ponto') . '?sugestao=ok#sugira');
            exit;
        }

        if ($nome === '' || $endereco === '') {
            header('Location: ' . baseUrl('ponto') . '?erro=' . rawurlencode('Informe ao menos o nome do local e o endereço.'));
            exit;
        }

        // Anti-abuso: no maximo 5 sugestoes por hora a partir da mesma conexao.
        $chaveIp = seguranca_chave();
        if (!limite_permitido('sugestao', $chaveIp, 5, 60)) {
            header('Location: ' . baseUrl('ponto') . '?erro=' . rawurlencode('Muitas sugestões enviadas desta conexão. Aguarde uma hora e tente novamente.'));
            exit;
        }
        limite_registrar('sugestao', $chaveIp);

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
