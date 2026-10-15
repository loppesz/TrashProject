<?php

/**
 * Controller de autenticacao: login, cadastro e logout.
 */
class Usuario extends BaseController
{
    /**
     * Tela de login (GET) e autenticacao (POST).
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
            $email_input = trim((string) $this->request->post('email'));
            $senha       = (string) $this->request->post('senha');

            if ($email_input === '' || $senha === '') {
                $erro = 'Preencha o e-mail e a senha para continuar.';
            } else {
                try {
                    $usuario = $this->model('Usuario')->buscaPorEmail($email_input);

                    if (!$usuario || !$usuario['ativo'] || !password_verify($senha, $usuario['senha_hash'])) {
                        $erro = 'E-mail ou senha incorretos.';
                    } else {
                        auth_login($usuario);
                        header('Location: ' . $next);
                        exit;
                    }
                } catch (Throwable $e) {
                    $erro = 'Não foi possível conectar ao banco de dados.';
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
        ]);
    }

    /**
     * Tela de cadastro (GET) e criacao de conta (POST).
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
            $form['nome']     = trim((string) $this->request->post('nome'));
            $form['email']    = trim((string) $this->request->post('email'));
            $form['telefone'] = trim((string) $this->request->post('telefone'));
            $senha            = (string) $this->request->post('senha');
            $senha_conf       = (string) $this->request->post('senha_conf');
            $termos           = $this->request->post('termos', null) !== null;

            if ($form['nome'] === '') {
                $erro = 'Informe seu nome completo.';
            } elseif (strlen($form['nome']) < 3) {
                $erro = 'O nome deve ter pelo menos 3 caracteres.';
            } elseif ($form['email'] === '' || !filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
                $erro = 'Informe um e-mail válido.';
            } elseif (strlen($senha) < 8) {
                $erro = 'A senha deve ter pelo menos 8 caracteres.';
            } elseif ($senha !== $senha_conf) {
                $erro = 'As senhas não coincidem. Verifique e tente novamente.';
            } elseif (!$termos) {
                $erro = 'Você precisa aceitar os termos de uso para continuar.';
            } else {
                try {
                    $usuarioModel = $this->model('Usuario');

                    if ($usuarioModel->emailExiste($form['email'])) {
                        $erro = 'Este e-mail já está cadastrado. Faça login ou recupere sua senha.';
                    } else {
                        $usuarioModel->cria(
                            $form['nome'],
                            $form['email'],
                            password_hash($senha, PASSWORD_DEFAULT),
                            $form['telefone'] !== '' ? $form['telefone'] : null
                        );
                        header('Location: ' . baseUrl('usuario/login') . '?cadastro=ok');
                        exit;
                    }
                } catch (Throwable $e) {
                    $erro = 'Não foi possível conectar ao banco de dados.';
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
     * Encerra a sessao e volta para a home.
     */
    public function logout(): void
    {
        auth_logout();
        header('Location: ' . baseUrl());
        exit;
    }
}
