<?php

/**
 * Helpers utilitarios de view (URLs, menu, formatacao).
 */

/**
 * Monta uma URL absoluta a partir da BASEURL.
 * Ex.: baseUrl('assets/css/style.css') => '/assets/css/style.css'
 */
function baseUrl(string $path = ''): string
{
    return rtrim(BASEURL, '/') . '/' . ltrim($path, '/');
}

/**
 * Retorna a classe "active" quando a rota informada corresponde a rota atual.
 * Usado pela navbar para destacar o item vigente.
 */
function menuAtivo(string $rota): string
{
    static $atual = null;

    if ($atual === null) {
        $url   = trim(parse_url($_GET['url'] ?? '', PHP_URL_PATH) ?? '', '/');
        $parts = explode('/', $url);
        $atual = strtolower($parts[0] ?? '');
        if ($atual === '') {
            $atual = 'home';
        }
    }

    return strtolower($rota) === $atual ? 'active' : '';
}

/**
 * Gera o bloco de titulo/subtitulo padrao de uma pagina.
 */
function tituloPagina(string $titulo, string $subtitulo = ''): string
{
    $html  = '<div class="page-header"><div class="container">';
    $html .= '<h1>' . htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') . '</h1>';
    if ($subtitulo !== '') {
        $html .= '<p>' . htmlspecialchars($subtitulo, ENT_QUOTES, 'UTF-8') . '</p>';
    }
    $html .= '</div></div>';
    return $html;
}

/**
 * Formata um valor numerico no padrao brasileiro (1.234,56).
 */
function formataValor(float $valor, int $decimais = 2): string
{
    return number_format($valor, $decimais, ',', '.');
}

/**
 * Converte uma string no formato brasileiro (1.234,56) para float.
 */
function strFloat(string $valor): float
{
    $valor = str_replace('.', '', $valor);
    $valor = str_replace(',', '.', $valor);
    return (float) $valor;
}

/**
 * Script anti-flash de tema. Deve ir no topo do <head>, antes do CSS.
 */
function themeHeadScript(): string
{
    return "<script>(function(){try{var t=localStorage.getItem('theme')||(window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light');if(t==='dark')document.documentElement.classList.add('dark');}catch(e){}})();</script>";
}
