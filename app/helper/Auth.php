<?php

/**
 * Helpers de autenticacao e sessao (migrados de auth.php).
 *
 * A autenticacao e feita via sessao PHP nativa ($_SESSION). A tabela
 * sessao_usuario existe no schema, mas o fluxo atual nao a utiliza.
 */

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/**
 * Retorna o usuario autenticado ou null.
 *
 * @return array|null ['id_usuario'=>int, 'nome'=>string, 'tipo'=>string]
 */
function auth_user(): ?array
{
    return !empty($_SESSION['id_usuario']) ? [
        'id_usuario' => (int) $_SESSION['id_usuario'],
        'nome'       => (string) ($_SESSION['nome'] ?? ''),
        'tipo'       => (string) ($_SESSION['tipo'] ?? 'MORADOR'),
    ] : null;
}

/**
 * Autentica o usuario na sessao a partir de um registro da tabela usuario.
 */
function auth_login(array $usuario): void
{
    session_regenerate_id(true);
    $_SESSION['id_usuario'] = (int) $usuario['id_usuario'];
    $_SESSION['nome']       = $usuario['nome'];
    $_SESSION['tipo']       = $usuario['tipo'];
}

/**
 * Encerra a sessao e expira o cookie de sessao.
 */
function auth_logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    session_destroy();
}

/**
 * True se houver usuario logado do tipo ADMINISTRADOR.
 */
function auth_is_admin(): bool
{
    $user = auth_user();
    return $user !== null && $user['tipo'] === 'ADMINISTRADOR';
}

/**
 * Sanitiza o destino de redirect pos-login para evitar open redirect.
 * Aceita apenas rotas internas (comecando por "/", sem "//").
 */
function auth_next_path(?string $next): string
{
    if (!$next || $next[0] !== '/' || str_contains($next, '//')) {
        return BASEURL;
    }
    return $next;
}

/**
 * Guard de rota. Redireciona para o login se nao autenticado,
 * ou se exigir admin e o usuario nao for ADMINISTRADOR.
 */
function auth_require(bool $adminOnly = false): void
{
    $user = auth_user();
    if (!$user || ($adminOnly && $user['tipo'] !== 'ADMINISTRADOR')) {
        $next = $_SERVER['REQUEST_URI'] ?? BASEURL;
        header('Location: ' . BASEURL . 'usuario/login?next=' . rawurlencode($next));
        exit;
    }
}
