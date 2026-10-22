<?php

/**
 * Helpers de seguranca e privacidade.
 *
 *  - Cabecalhos HTTP de seguranca (CSP, nosniff, frame, referrer, permissoes).
 *  - Token CSRF por sessao, exigido em TODO POST (validado no index.php).
 *  - Limite de tentativas por acao (login, cadastro, ocorrencias...), usando
 *    apenas o HASH do IP (pseudonimizacao, LGPD art. 13 §4º).
 *  - Campo "isca" (honeypot) contra envio automatizado por robos.
 *  - Escape para valores usados dentro de atributos JavaScript (onclick...).
 *  - Minimizacao: endereco publico sem numero da casa.
 */

// =================================================================
// CABECALHOS HTTP
// =================================================================

/**
 * Envia os cabecalhos de seguranca das respostas dinamicas (PHP).
 * A CSP lista apenas as origens que o site realmente usa.
 */
function seguranca_cabecalhos(): void
{
    if (headers_sent()) {
        return;
    }
    header_remove('X-Powered-By');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    // GPS so na propria pagina; camera/microfone/pagamento desligados.
    header('Permissions-Policy: geolocation=(self), camera=(), microphone=(), payment=(), usb=()');
    header('Cross-Origin-Opener-Policy: same-origin');

    // 'unsafe-inline' ainda e necessario porque as paginas usam scripts e
    // eventos inline (onclick). As demais diretivas limitam de onde podem
    // vir scripts, conexoes, imagens e frames.
    $csp = [
        "default-src 'self'",
        "script-src 'self' 'unsafe-inline' https://unpkg.com",
        "style-src 'self' 'unsafe-inline' https://unpkg.com https://fonts.googleapis.com",
        "font-src 'self' https://fonts.gstatic.com data:",
        "img-src 'self' data: blob: https://*.tile.openstreetmap.org https://unpkg.com",
        "connect-src 'self' https://nominatim.openstreetmap.org",
        "frame-src https://maps.google.com https://www.google.com",
        "media-src 'self'",
        "worker-src 'self'",
        "manifest-src 'self'",
        "object-src 'none'",
        "base-uri 'self'",
        "form-action 'self'",
        "frame-ancestors 'self'",
    ];
    header('Content-Security-Policy: ' . implode('; ', $csp));

    // Em HTTPS, obriga o navegador a continuar em HTTPS.
    if (seguranca_https()) {
        header('Strict-Transport-Security: max-age=31536000');
    }
}

/** True se a requisicao atual veio por HTTPS (inclusive atras de proxy). */
function seguranca_https(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
        || (($_SERVER['SERVER_PORT'] ?? '') === '443');
}

/** Impede cache de paginas com dados pessoais (conta, admin, exportacao). */
function seguranca_sem_cache(): void
{
    if (!headers_sent()) {
        header('Cache-Control: no-store, no-cache, must-revalidate, private');
        header('Pragma: no-cache');
    }
}

// =================================================================
// CSRF
// =================================================================

/** Token CSRF da sessao (criado na primeira chamada). */
function csrf_token(): string
{
    if (empty($_SESSION['_csrf']) || !is_string($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

/** Campo oculto com o token, para colocar dentro de todo <form method="POST">. */
function csrf_campo(): string
{
    return '<input type="hidden" name="_csrf" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

/** Gera um novo token (apos login/logout, evita reaproveitamento). */
function csrf_renovar(): void
{
    $_SESSION['_csrf'] = bin2hex(random_bytes(32));
}

/** Confere o token enviado no POST (campo _csrf ou cabecalho X-CSRF-Token). */
function csrf_valido(): bool
{
    $enviado = $_POST['_csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    $sessao  = $_SESSION['_csrf'] ?? '';
    return is_string($enviado) && is_string($sessao) && $sessao !== '' && hash_equals($sessao, $enviado);
}

/**
 * Bloqueia o POST sem token valido. Responde JSON para chamadas via fetch e
 * uma pagina simples para formularios.
 */
function csrf_bloquear(): void
{
    http_response_code(403);
    $querJson = str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')
        || !empty($_SERVER['HTTP_X_CSRF_TOKEN'])
        || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch';
    if ($querJson) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'motivo' => 'csrf']);
        exit;
    }
    header('Content-Type: text/html; charset=utf-8');
    $voltar = htmlspecialchars(baseUrl(), ENT_QUOTES, 'UTF-8');
    echo '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">'
       . '<title>Formulário expirado – ColetaFácil</title>'
       . '<style>body{font-family:system-ui,Arial,sans-serif;background:#f6fbf7;color:#102019;display:grid;place-items:center;min-height:100vh;margin:0;padding:20px}'
       . 'main{max-width:460px;background:#fff;border:1px solid #d9e7dd;border-radius:16px;padding:28px;text-align:center}'
       . 'a{display:inline-block;margin-top:14px;padding:10px 18px;border-radius:10px;background:#16a34a;color:#fff;font-weight:700;text-decoration:none}</style></head>'
       . '<body><main><h1 style="font-size:1.3rem">Este formulário expirou</h1>'
       . '<p>Por segurança, não conseguimos confirmar que o envio partiu desta página. Isso acontece quando a página fica aberta por muito tempo ou a sessão termina.</p>'
       . '<p>Volte, recarregue a página e envie novamente.</p>'
       . '<a href="javascript:history.back()">Voltar</a> <a href="' . $voltar . '" style="background:#334155">Início</a></main></body></html>';
    exit;
}

// =================================================================
// LIMITE DE TENTATIVAS (anti-abuso)
// =================================================================

/** IP do cliente (sem confiar em cabecalhos de proxy, que podem ser forjados). */
function seguranca_ip(): string
{
    return (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
}

/**
 * Chave pseudonimizada: HMAC-SHA256 do IP (e complemento opcional, ex.: e-mail)
 * com a chave da aplicacao. O IP em si nunca e gravado.
 */
function seguranca_chave(string $complemento = ''): string
{
    return hash_hmac('sha256', seguranca_ip() . '|' . mb_strtolower($complemento), APP_KEY);
}

/** Conexao PDO compartilhada (mesma usada pelos models). */
function seguranca_pdo(): PDO
{
    require_once __DIR__ . '/../library/Database.php';
    return (new Database())->conecta();
}

/**
 * True se a acao ainda pode ser feita: menos de $limite registros
 * nos ultimos $minutos para esta chave.
 */
function limite_permitido(string $acao, string $chave, int $limite, int $minutos): bool
{
    try {
        $st = seguranca_pdo()->prepare(
            'SELECT COUNT(*) FROM registro_acao
             WHERE acao = ? AND chave = ? AND criada_em >= (NOW() - INTERVAL ? MINUTE)'
        );
        $st->execute([$acao, $chave, $minutos]);
        return (int) $st->fetchColumn() < $limite;
    } catch (Throwable $e) {
        return true; // falha no controle nao derruba o site
    }
}

/** Registra uma tentativa. De vez em quando apaga o que tem mais de 24h. */
function limite_registrar(string $acao, string $chave): void
{
    try {
        $pdo = seguranca_pdo();
        $pdo->prepare('INSERT INTO registro_acao (acao, chave) VALUES (?, ?)')->execute([$acao, $chave]);
        if (random_int(1, 50) === 1) {
            $pdo->exec('DELETE FROM registro_acao WHERE criada_em < (NOW() - INTERVAL 1 DAY)');
        }
    } catch (Throwable $e) {
        // sem efeito colateral para o usuario
    }
}

/** Apaga as tentativas de uma chave (ex.: apos login bem-sucedido). */
function limite_limpar(string $acao, string $chave): void
{
    try {
        seguranca_pdo()->prepare('DELETE FROM registro_acao WHERE acao = ? AND chave = ?')->execute([$acao, $chave]);
    } catch (Throwable $e) {
    }
}

// =================================================================
// HONEYPOT (campo isca)
// =================================================================

/** Campo invisivel para pessoas; robos costumam preenche-lo. */
function honeypot_campo(): string
{
    return '<div class="campo-isca" aria-hidden="true">'
         . '<label for="cf_site">Não preencha este campo</label>'
         . '<input type="text" id="cf_site" name="cf_site" tabindex="-1" autocomplete="off" value="">'
         . '</div>';
}

/** True se o campo isca veio preenchido (provavel robo). */
function honeypot_acionado(): bool
{
    return trim((string) ($_POST['cf_site'] ?? '')) !== '';
}

// =================================================================
// ESCAPE E MINIMIZACAO
// =================================================================

/**
 * Valor pronto para ir DENTRO de um atributo de evento (onclick, onsubmit).
 * Gera um literal JSON e escapa para HTML: nem aspas nem entidades
 * conseguem "fugir" da string JavaScript.
 */
function jsAttr(mixed $valor): string
{
    return htmlspecialchars(
        (string) json_encode($valor, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP),
        ENT_QUOTES,
        'UTF-8'
    );
}

/** Flags padrao para json_encode dentro de <script> ou atributos. */
const JSON_SEGURO = JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP;

/**
 * Nome para exibicao publica (ranking): primeiro nome + inicial do ultimo
 * sobrenome. "Maria da Silva" -> "Maria S."
 */
function nomePublico(?string $nome): string
{
    $partes = preg_split('/\s+/u', trim((string) $nome), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    if (empty($partes)) {
        return 'Participante';
    }
    $primeiro = $partes[0];
    if (count($partes) === 1) {
        return $primeiro;
    }
    return $primeiro . ' ' . mb_strtoupper(mb_substr(end($partes), 0, 1)) . '.';
}

/**
 * Endereco para exibicao publica: remove numeros (casa, CEP, apto),
 * deixando apenas o nome da rua/referencia. Evita expor a casa de quem
 * registrou ou de terceiros.
 */
function enderecoPublico(?string $endereco): string
{
    $e = (string) $endereco;
    $e = preg_replace('/\b(n[ºo°.]?|nº|número|numero|casa|apto|apt|ap|bloco|bl|lote|qd|quadra)\s*\.?\s*\d+[a-z]?\b/iu', '', $e);
    $e = preg_replace('/\d{5}-?\d{3}/', '', (string) $e);   // CEP
    $e = preg_replace('/\d+/', '', (string) $e);             // demais numeros
    $e = preg_replace('/\s*[,–-]\s*(?=[,–-]|$)/u', '', (string) $e);
    $e = preg_replace('/\s{2,}/', ' ', (string) $e);
    $e = trim((string) $e, " \t\n\r,–-");
    return $e !== '' ? $e : 'Local informado';
}
