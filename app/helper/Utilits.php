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

/**
 * Nome do dia da semana a partir do codigo usado no banco
 * (1=domingo, 2=segunda, ... 7=sabado).
 */
function nomeDiaSemana(int $dia): string
{
    $dias = [
        1 => 'Domingo',
        2 => 'Segunda',
        3 => 'Terça',
        4 => 'Quarta',
        5 => 'Quinta',
        6 => 'Sexta',
        7 => 'Sábado',
    ];
    return $dias[$dia] ?? '';
}

/**
 * Formata um horario TIME ('08:00:00') para exibicao curta ('08h').
 * Mantem minutos quando houver ('08:30:00' -> '08h30').
 */
function formataHora(?string $hora): string
{
    if (!$hora) {
        return '–';
    }
    [$h, $m] = array_pad(explode(':', $hora), 2, '00');
    return $m === '00' ? "{$h}h" : "{$h}h{$m}";
}

/**
 * Abreviacao do dia da semana (1=domingo..7=sabado) -> Dom, Seg, Ter...
 */
function abreviaDiaSemana(int $dia): string
{
    $abrev = [1 => 'Dom', 2 => 'Seg', 3 => 'Ter', 4 => 'Qua', 5 => 'Qui', 6 => 'Sex', 7 => 'Sáb'];
    return $abrev[$dia] ?? '';
}
