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
    return "<script>(function(){try{var t=localStorage.getItem('theme')||(window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light');document.documentElement.style.colorScheme=t;if(t==='dark')document.documentElement.classList.add('dark');}catch(e){}})();</script>";
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

/**
 * Texto dos horarios de coleta de um dia.
 * [['inicio'=>'18:00','fim'=>null],['inicio'=>'21:20','fim'=>null]] -> "18h e 21h20".
 * Com fim: "07h às 12h". Com $aPartir (e sem fim): "a partir das 07h30".
 */
function textoHorariosColeta(array $horarios, bool $aPartir = false): string
{
    $partes = [];
    foreach ($horarios as $h) {
        $partes[] = !empty($h['fim'])
            ? formataHora($h['inicio']) . ' às ' . formataHora($h['fim'])
            : formataHora($h['inicio']);
    }
    $txt = implode(' e ', $partes);
    $temFim = count(array_filter($horarios, static fn($h) => !empty($h['fim']))) > 0;
    return ($aPartir && !$temFim && $txt !== '') ? 'a partir das ' . $txt : $txt;
}

/**
 * Junta os dias que tem o mesmo horario, mantendo a ordem (seg..dom):
 * [['dia'=>2,...],['dia'=>4,...]] -> [['rotulo'=>'Seg e Qua','horario'=>'08h'], ...].
 */
function agrupaDiasColeta(array $dias, bool $aPartir = false): array
{
    $grupos = [];
    foreach ($dias as $d) {
        $txt = textoHorariosColeta($d['horarios'], $aPartir);
        $grupos[$txt][] = (int) $d['dia'];
    }
    $out = [];
    foreach ($grupos as $txt => $lista) {
        $out[] = ['rotulo' => rotuloDiasColeta($lista), 'horario' => (string) $txt];
    }
    return $out;
}

/**
 * Rotulo de uma lista de dias (1=dom..7=sab): "Seg a Sex", "Ter e Qui", "Seg, Qua e Sex".
 */
function rotuloDiasColeta(array $dias): string
{
    $pos = static fn(int $d): int => ($d + 5) % 7; // seg=0 ... dom=6
    usort($dias, static fn($a, $b) => $pos($a) <=> $pos($b));
    $n = count($dias);
    if ($n === 0) {
        return '';
    }
    $consecutivos = true;
    for ($i = 1; $i < $n; $i++) {
        if ($pos($dias[$i]) !== $pos($dias[$i - 1]) + 1) {
            $consecutivos = false;
            break;
        }
    }
    $nomes = array_map('abreviaDiaSemana', $dias);
    if ($n >= 3 && $consecutivos) {
        return $nomes[0] . ' a ' . $nomes[$n - 1];
    }
    if ($n === 1) {
        return $nomes[0];
    }
    return implode(', ', array_slice($nomes, 0, -1)) . ' e ' . $nomes[$n - 1];
}

/**
 * Rotulo legivel do tipo de ocorrencia (ENUM do banco -> texto).
 */
function rotuloTipoOcorrencia(string $tipo): string
{
    $map = [
        'DESCARTE_IRREGULAR'     => 'Descarte irregular',
        'COLETA_NAO_REALIZADA'   => 'Coleta não realizada',
        'MATERIAL_NAO_RECOLHIDO' => 'Material deixado',
        'PONTO_INADEQUADO'       => 'Ponto inadequado',
        'ENTULHO'                => 'Entulho/Móveis',
        'LIXO_TOXICO'            => 'Lixo tóxico',
        'OUTROS'                 => 'Outros',
    ];
    return $map[$tipo] ?? $tipo;
}

/**
 * Rotulo legivel do status de ocorrencia (ENUM do banco -> texto).
 */
function rotuloStatusOcorrencia(string $status): string
{
    $map = [
        'NOVA'         => 'Nova',
        'EM_ANALISE'   => 'Em análise',
        'EM_ANDAMENTO' => 'Em atendimento',
        'RESOLVIDA'    => 'Resolvida',
        'ARQUIVADA'    => 'Arquivada',
    ];
    return $map[$status] ?? $status;
}

/**
 * Aba do painel admin onde a ocorrencia aparece, conforme o status salvo:
 * RESOLVIDA -> resolvidas, ARQUIVADA -> arquivadas, demais -> abertas.
 */
function abaAdminOcorrencia(string $status): string
{
    if ($status === 'RESOLVIDA') {
        return 'resolvidas';
    }
    if ($status === 'ARQUIVADA') {
        return 'arquivadas';
    }
    return 'abertas';
}

/**
 * Classe CSS do badge de status (reaproveita os estilos existentes).
 */
function classeStatusOcorrencia(string $status): string
{
    $map = [
        'NOVA'         => 'nova',
        'EM_ANALISE'   => 'analise',
        'EM_ANDAMENTO' => 'andamento',
        'RESOLVIDA'    => 'resolvida',
        'ARQUIVADA'    => 'arquivada',
    ];
    return $map[$status] ?? 'nova';
}
