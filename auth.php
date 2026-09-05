<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function auth_user(): ?array
{
    return !empty($_SESSION['id_usuario']) ? [
        'id_usuario' => (int) $_SESSION['id_usuario'],
        'nome' => (string) ($_SESSION['nome'] ?? ''),
        'tipo' => (string) ($_SESSION['tipo'] ?? 'MORADOR'),
    ] : null;
}

function auth_db(): PDO
{
    static $pdo;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = getenv('COLETA_DB_HOST') ?: '127.0.0.1';
    $name = getenv('COLETA_DB_NAME') ?: 'coletafacil';
    $user = getenv('COLETA_DB_USER') ?: 'root';
    $pass = getenv('COLETA_DB_PASS') ?: '';
    $pdo = new PDO("mysql:host={$host};dbname={$name};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}

function auth_login(array $usuario): void
{
    session_regenerate_id(true);
    $_SESSION['id_usuario'] = (int) $usuario['id_usuario'];
    $_SESSION['nome'] = $usuario['nome'];
    $_SESSION['tipo'] = $usuario['tipo'];
}

function auth_logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function auth_is_admin(): bool
{
    $user = auth_user();
    return $user !== null && $user['tipo'] === 'ADMINISTRADOR';
}

function auth_next_path(?string $next): string
{
    if (!$next || $next[0] !== '/' || str_contains($next, '//')) {
        return 'index.php';
    }
    return $next;
}

function require_auth(bool $adminOnly = false): void
{
    $user = auth_user();
    if (!$user || ($adminOnly && $user['tipo'] !== 'ADMINISTRADOR')) {
        $next = $_SERVER['REQUEST_URI'] ?? '/index.php';
        $login = (str_starts_with($_SERVER['SCRIPT_NAME'] ?? '', '/admin/')) ? '../login.php' : 'login.php';
        header('Location: ' . $login . '?next=' . rawurlencode($next));
        exit;
    }
}

function site_auth_links(string $prefix = ''): string
{
    $user = auth_user();
    $links = '';
    if ($user) {
        $nome = htmlspecialchars($user['nome'], ENT_QUOTES, 'UTF-8');
        $links .= '<li><span class="nav-user">Olá, ' . $nome . '</span></li>';
        if ($user['tipo'] === 'ADMINISTRADOR') {
            $links .= '<li><a href="' . $prefix . 'admin/index.php" class="nav-admin">Admin</a></li>';
        }
        $links .= '<li><a href="' . $prefix . 'logout.php">Sair</a></li>';
    } else {
        $links .= '<li><a href="' . $prefix . 'login.php">Entrar</a></li>';
        $links .= '<li><a href="' . $prefix . 'cadastro.php">Cadastrar</a></li>';
    }
    return $links;
}

function site_auth_mobile_links(string $prefix = ''): string
{
    $user = auth_user();
    if ($user) {
        $links = '<a href="' . $prefix . 'logout.php">🚪 Sair</a>';
        if ($user['tipo'] === 'ADMINISTRADOR') {
            $links = '<a href="' . $prefix . 'admin/index.php">⚙️ Admin</a>' . $links;
        }
        return $links;
    }
    return '<a href="' . $prefix . 'login.php">🔑 Entrar</a><a href="' . $prefix . 'cadastro.php">📝 Cadastrar</a>';
}
