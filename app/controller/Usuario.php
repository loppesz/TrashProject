<?php

/**
 * Controller de autenticacao e da conta do titular:
 * login, cadastro, logout e "Minha conta" (direitos da LGPD, art. 18).
 */
class Usuario extends BaseController
{
    /** Hash usado so para igualar o tempo de resposta quando o e-mail nao existe. */
    private const HASH_FICTICIO = '$2y$10$4PMfgpAQ/Eofwo1I2wOQD.BlIaSlhkxD9jJ1Ikw8qtJsm7KoASLmC';

    /**
     * Tela de login (GET) e autenticacao (POST).
     * Limite: 5 erros por e-mail+IP e 30 por IP a cada 15 minutos.
     */
    public function login(): void
    {
        // Ja autenticado: redireciona para o destino seguro.
        if (auth_user()) {
            header('Location: ' . auth_next_path($_GET['next'] ?? null));
            exit;
        }

        $erro        = '';
        $email_input = '';
        $next        = auth_next_path($_GET['next'] ?? $_POST['next'] ?? null);

        if ($this->request->isPost()) {
            $email_input = mb_strtolower(trim((string) $this->request->post('email')));
            $senha       = (string) $this->request->post('senha');
            $chaveConta  = seguranca_chave($email_input);
            $chaveIp     = seguranca_chave();

            if ($email_input === '' || $senha === '') {
                $erro = 'Preencha o e-mail e a senha para continuar.';
            } elseif (!limite_permitido('login', $chaveConta, 5, 15) || !limite_permitido('login_ip', $chaveIp, 30, 15)) {
                $erro = 'Muitas tentativas de login. Por segurança, aguarde 15 minutos e tente novamente.';
            } else {
                try {
                    $usuarioModel = $this->model('Usuario');
                    $usuario = $usuarioModel->buscaPorEmail($email_input);

                    // Sempre roda password_verify: o tempo de resposta nao revela
                    // se o e-mail existe ou nao.
                    $hash  = !empty($usuario['senha_hash']) ? $usuario['senha_hash'] : self::HASH_FICTICIO;
                    $senhaOk = password_verify($senha, $hash);

                    if (!$usuario || !$usuario['ativo'] || !$senhaOk) {
                        limite_registrar('login', $chaveConta);
                        limite_registrar('login_ip', $chaveIp);
                        $erro = 'E-mail ou senha incorretos.';
                    } else {
                        // Atualiza o hash se o PHP passou a recomendar outro custo/algoritmo.
                        if (password_needs_rehash($usuario['senha_hash'], PASSWORD_DEFAULT)) {
                            $usuarioModel->atualizaSenha((int) $usuario['id_usuario'], password_hash($senha, PASSWORD_DEFAULT));
                        }
                        limite_limpar('login', $chaveConta);
                        auth_login($usuario);
                        header('Location: ' . $next);
                        exit;
                    }
                } catch (Throwable $e) {
                    $erro = 'Não foi possível entrar agora. Tente novamente em instantes.';
                }
            }
        }

        $this->view('login', [
            'tituloPagina' => 'Entrar – ColetaFácil',
            'cssPagina'    => ['auth.css'],
            'menu'         => '',
            'erro'         => $erro,
            'email_input'  => $email_input,
            'next'         => $next,
            'contato'      => LGPD_CONTATO,
        ]);
    }

    /**
     * Tela de cadastro (GET) e criacao de conta (POST).
     * Limite: 5 tentativas por IP por hora. Registra o aceite (data + versao).
     */
    public function cadastro(): void
    {
        if (auth_user()) {
            header('Location: ' . auth_next_path($_GET['next'] ?? null));
            exit;
        }

        $erro = '';
        $form = ['nome' => '', 'email' => '', 'telefone' => ''];

        if ($this->request->isPost()) {
            $form['nome']     = trim(strip_tags((string) $this->request->post('nome')));
            $form['email']    = mb_strtolower(trim((string) $this->request->post('email')));
            $form['telefone'] = trim((string) $this->request->post('telefone'));
            $senha            = (string) $this->request->post('senha');
            $senha_conf       = (string) $this->request->post('senha_conf');
            $termos           = $this->request->post('termos', null) !== null;
            $chaveIp          = seguranca_chave();

            if (honeypot_acionado()) {
                // Robo: finge sucesso sem gravar nada.
                header('Location: ' . baseUrl('usuario/login') . '?cadastro=ok');
                exit;
            }

            if (!limite_permitido('cadastro', $chaveIp, 5, 60)) {
                $erro = 'Muitos cadastros a partir desta conexão. Aguarde uma hora e tente novamente.';
            } elseif ($form['nome'] === '' || mb_strlen($form['nome']) < 3) {
                $erro = 'Informe seu nome (pelo menos 3 caracteres).';
            } elseif (mb_strlen($form['nome']) > 120) {
                $erro = 'O nome pode ter no máximo 120 caracteres.';
            } elseif ($form['email'] === '' || mb_strlen($form['email']) > 150 || !filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
                $erro = 'Informe um e-mail válido.';
            } elseif ($form['telefone'] !== '' && !preg_match('/^[\d\s()+-]{8,20}$/', $form['telefone'])) {
                $erro = 'Telefone inválido. Use apenas números, espaços, parênteses e hífen.';
            } elseif (($erroSenha = $this->validarSenha($senha, $form['email'])) !== '') {
                $erro = $erroSenha;
            } elseif ($senha !== $senha_conf) {
                $erro = 'As senhas não coincidem. Verifique e tente novamente.';
            } elseif (!$termos) {
                $erro = 'Para criar a conta, é preciso aceitar os Termos de Uso e a Política de Privacidade.';
            } else {
                limite_registrar('cadastro', $chaveIp);
                try {
                    $usuarioModel = $this->model('Usuario');

                    if ($usuarioModel->emailExiste($form['email'])) {
                        $erro = 'Este e-mail já está cadastrado. Faça login para continuar.';
                    } else {
                        $usuarioModel->cria(
                            $form['nome'],
                            $form['email'],
                            password_hash($senha, PASSWORD_DEFAULT),
                            $form['telefone'] !== '' ? $form['telefone'] : null,
                            TERMOS_VERSAO
                        );
                        header('Location: ' . baseUrl('usuario/login') . '?cadastro=ok');
                        exit;
                    }
                } catch (Throwable $e) {
                    $erro = 'Não foi possível criar a conta agora. Tente novamente em instantes.';
                }
            }
        }

        $this->view('cadastro', [
            'tituloPagina' => 'Criar conta – ColetaFácil',
            'cssPagina'    => ['auth.css'],
            'menu'         => '',
            'erro'         => $erro,
            'form'         => $form,
        ]);
    }

    /**
     * Encerra a sessao (somente via POST com token, para que um link ou
     * imagem de outro site nao consiga deslogar ninguem).
     */
    public function logout(): void
    {
        if ($this->request->isPost()) {
            auth_logout();
        }
        header('Location: ' . baseUrl());
        exit;
    }

    // =================================================================
    // MINHA CONTA — direitos do titular (LGPD art. 18)
    // =================================================================

    /** Pagina "Minha conta e privacidade". */
    public function conta(): void
    {
        auth_require();
        seguranca_sem_cache();

        $usuario = auth_user();
        $id      = (int) $usuario['id_usuario'];
        $model   = $this->model('Usuario');

        $this->view('conta', [
            'tituloPagina' => 'Minha conta e privacidade – ColetaFácil',
            'cssPagina'    => ['legal.css'],
            'menu'         => '',
            'perfil'       => $model->perfil($id),
            'resumo'       => $model->resumoDados($id),
            'perfisKids'   => $this->model('PerfilKids')->porUsuario($id),
            'msg'          => (string) ($_GET['msg'] ?? ''),
            'erro'         => (string) ($_GET['erro'] ?? ''),
        ]);
    }

    /** Correcao de dados (art. 18, III): nome e telefone. */
    public function salvarConta(): void
    {
        auth_require();
        if (!$this->request->isPost()) {
            $this->voltarConta();
        }

        $id       = (int) auth_user()['id_usuario'];
        $nome     = trim(strip_tags((string) $this->request->post('nome')));
        $telefone = trim((string) $this->request->post('telefone'));

        if (mb_strlen($nome) < 3 || mb_strlen($nome) > 120) {
            $this->voltarConta('', 'O nome deve ter entre 3 e 120 caracteres.');
        }
        if ($telefone !== '' && !preg_match('/^[\d\s()+-]{8,20}$/', $telefone)) {
            $this->voltarConta('', 'Telefone inválido. Use apenas números, espaços, parênteses e hífen.');
        }

        $this->model('Usuario')->atualizaPerfil($id, $nome, $telefone !== '' ? $telefone : null);
        auth_atualizar_nome($nome);
        $this->voltarConta('Dados atualizados.');
    }

    /** Troca de senha (exige a senha atual). */
    public function alterarSenha(): void
    {
        auth_require();
        if (!$this->request->isPost()) {
            $this->voltarConta();
        }

        $id     = (int) auth_user()['id_usuario'];
        $model  = $this->model('Usuario');
        $atual  = (string) $this->request->post('senha_atual');
        $nova   = (string) $this->request->post('senha_nova');
        $conf   = (string) $this->request->post('senha_conf');
        $perfil = $model->perfil($id);

        if (!password_verify($atual, $model->senhaHash($id))) {
            $this->voltarConta('', 'A senha atual não confere.');
        }
        if (($erroSenha = $this->validarSenha($nova, (string) ($perfil['email'] ?? ''))) !== '') {
            $this->voltarConta('', $erroSenha);
        }
        if ($nova !== $conf) {
            $this->voltarConta('', 'A confirmação da nova senha não confere.');
        }

        $model->atualizaSenha($id, password_hash($nova, PASSWORD_DEFAULT));
        session_regenerate_id(true);
        csrf_renovar();
        $model->registrarPrivacidade('TROCA_SENHA', $id);
        $this->voltarConta('Senha alterada com sucesso.');
    }

    /**
     * Acesso e portabilidade (art. 18, II e V): baixa todos os dados do
     * titular em JSON. POST com token para nao ser disparado por terceiros.
     */
    public function exportarDados(): void
    {
        auth_require();
        if (!$this->request->isPost()) {
            $this->voltarConta();
        }

        $id    = (int) auth_user()['id_usuario'];
        $model = $this->model('Usuario');
        $dados = [
            'gerado_em'   => date('c'),
            'plataforma'  => 'ColetaFácil – Muriaé-MG',
            'observacao'  => 'Cópia dos seus dados pessoais (LGPD, art. 18, II e V). A senha não é incluída: guardamos apenas um hash irreversível.',
            'dados'       => $model->dadosParaExportacao($id),
        ];
        $model->registrarPrivacidade('EXPORTACAO_DADOS', $id);

        if (ob_get_level() > 0) {
            ob_end_clean();
        }
        seguranca_sem_cache();
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="meus-dados-coletafacil-' . date('Y-m-d') . '.json"');
        echo json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Eliminacao (art. 18, VI): apaga a conta. Exige a senha e a palavra
     * EXCLUIR. Contas de administrador sao removidas pela equipe.
     */
    public function excluirConta(): void
    {
        auth_require();
        if (!$this->request->isPost()) {
            $this->voltarConta();
        }

        $usuario = auth_user();
        $id      = (int) $usuario['id_usuario'];
        $model   = $this->model('Usuario');

        if ($usuario['tipo'] === 'ADMINISTRADOR') {
            $this->voltarConta('', 'Contas de administrador não podem ser excluídas por aqui. Peça a remoção à equipe responsável.');
        }
        if (!password_verify((string) $this->request->post('senha'), $model->senhaHash($id))) {
            $this->voltarConta('', 'Senha incorreta. A conta não foi excluída.');
        }
        if (mb_strtoupper(trim((string) $this->request->post('confirmacao'))) !== 'EXCLUIR') {
            $this->voltarConta('', 'Digite EXCLUIR para confirmar a exclusão da conta.');
        }

        try {
            $model->excluirConta($id);
        } catch (Throwable $e) {
            $this->voltarConta('', 'Não foi possível excluir a conta agora. Tente novamente.');
        }

        $model->registrarPrivacidade('EXCLUSAO_CONTA', null, 'Conta excluída a pedido do titular; ocorrências anonimizadas.');
        auth_logout();
        header('Location: ' . baseUrl('home/privacidade') . '?conta=excluida');
        exit;
    }

    /** Apaga um perfil da Area Kids do proprio responsavel. */
    public function excluirPerfilKids(): void
    {
        auth_require();
        if ($this->request->isPost()) {
            $id = (int) auth_user()['id_usuario'];
            $n  = $this->model('PerfilKids')->exclui((int) $this->request->post('id_perfil'), $id);
            $this->voltarConta($n > 0 ? 'Perfil da Área Kids excluído.' : '', $n > 0 ? '' : 'Perfil não encontrado.');
        }
        $this->voltarConta();
    }

    // =================================================================
    // Auxiliares
    // =================================================================

    /**
     * Regras de senha: 8 a 72 bytes (limite do bcrypt), com letra e numero,
     * diferente do e-mail. Retorna a mensagem de erro ou '' se valida.
     */
    private function validarSenha(string $senha, string $email): string
    {
        if (strlen($senha) < 8) {
            return 'A senha deve ter pelo menos 8 caracteres.';
        }
        if (strlen($senha) > 72) {
            return 'A senha pode ter no máximo 72 caracteres.';
        }
        if (!preg_match('/\pL/u', $senha) || !preg_match('/\d/', $senha)) {
            return 'A senha deve misturar letras e números.';
        }
        if ($email !== '' && mb_strtolower($senha) === mb_strtolower($email)) {
            return 'A senha não pode ser igual ao e-mail.';
        }
        return '';
    }

    /** Volta para Minha conta com mensagem de sucesso ou erro. */
    private function voltarConta(string $msg = '', string $erro = ''): void
    {
        $q = $msg !== '' ? '?msg=' . rawurlencode($msg) : ($erro !== '' ? '?erro=' . rawurlencode($erro) : '');
        header('Location: ' . baseUrl('usuario/conta') . $q);
        exit;
    }
}
