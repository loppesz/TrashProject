<?php

/**
 * Helpers de autenticacao e sessao.
 *
 * A autenticacao e feita via sessao PHP nativa ($_SESSION), com:
 *  - cookie HttpOnly + SameSite=Lax (+ Secure quando em HTTPS);
 *  - modo estrito (nao aceita ID de sessao inventado pelo cliente);
 *  - expiracao por inatividade e tempo maximo de sessao;
 *  - ID regenerado no login (contra fixacao de sessao).
 */

/** Minutos sem uso ate a sessao expirar. */
const SESSAO_INATIVIDADE_MIN = 120;
/** Duracao maxima de uma sessao logada (horas). */
const SESSAO_MAXIMA_HORAS = 12;

if (session_status() !== PHP_SESSION_ACTIVE) {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    session_name('CFSESSID');
    session_set_cookie_params([
        'lifetime' => 0,          // cookie de sessao (some ao fechar o navegador)
        'path'     => '/',
        'httponly' => true,
        'secure'   => $https,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// Expiracao: por inatividade ou por tempo maximo de sessao logada.
if (!empty($_SESSION['id_usuario'])) {
    $agora     = time();
    $ultimo    = (int) ($_SESSION['_ultimo_uso'] ?? $agora);
    $inicio    = (int) ($_SESSION['_login_em'] ?? $agora);
    $expirou   = ($agora - $ultimo) > SESSAO_INATIVIDADE_MIN * 60
              || ($agora - $inicio) > SESSAO_MAXIMA_HORAS * 3600;
    if ($expirou) {
        $_SESSION = [];
        session_regenerate_id(true);
        $_SESSION['_sessao_expirada'] = true;
    } else {
        $_SESSION['_ultimo_uso'] = $agora;
    }
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
    $_SESSION['_login_em']  = time();
    $_SESSION['_ultimo_uso'] = time();
    unset($_SESSION['_sessao_expirada']);
    if (function_exists('csrf_renovar')) {
        csrf_renovar();
    }
}

/** Atualiza o nome exibido na sessao (apos editar o perfil). */
function auth_atualizar_nome(string $nome): void
{
    if (!empty($_SESSION['id_usuario'])) {
        $_SESSION['nome'] = $nome;
    }
}

/**
 * Encerra a sessao e expira o cookie de sessao.
 */
function auth_logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires'  => time() - 42000,
            'path'     => $params['path'],
            'domain'   => $params['domain'],
            'secure'   => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => $params['samesite'] ?? 'Lax',
        ]);
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
 * Aceita apenas rotas internas (comecando por "/", sem "//" nem "\").
 */
function auth_next_path(?string $next): string
{
    if (!$next || $next[0] !== '/' || str_contains($next, '//') || str_contains($next, '\\')
        || preg_match('/[\r\n]/', $next)) {
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
        $extra = !empty($_SESSION['_sessao_expirada']) ? '&sessao=expirada' : '';
        header('Location: ' . BASEURL . 'usuario/login?next=' . rawurlencode($next) . $extra);
        exit;
    }
}
