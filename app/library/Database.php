<?php

/**
 * Camada de acesso ao banco de dados via PDO.
 *
 * Mantem uma unica conexao compartilhada entre todas as instancias
 * (conexao estatica), evitando abrir multiplas conexoes por requisicao.
 * Le as configuracoes de DB_CONF_CONEXAO (app/config/Config.php).
 */
class Database
{
    private static ?PDO $conexao = null;

    /**
     * Retorna a conexao PDO ativa, criando-a na primeira chamada.
     */
    public function conecta(): object
    {
        if (self::$conexao instanceof PDO) {
            return self::$conexao;
        }

        $cfg = DB_CONF_CONEXAO;
        $dsn = sprintf(
            '%s:host=%s;port=%s;dbname=%s;charset=%s',
            $cfg['driver'],
            $cfg['host'],
            $cfg['porta'],
            $cfg['banco'],
            $cfg['charset']
        );

        $opcoes = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_CURSOR             => PDO::CURSOR_SCROLL,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$cfg['charset']}",
        ];

        self::$conexao = new PDO($dsn, $cfg['usuario'], $cfg['senha'], $opcoes);

        return self::$conexao;
    }

    /**
     * Executa um SELECT parametrizado.
     *
     * @param string $sql         Consulta com placeholders (? ou :nome).
     * @param array  $dados       Valores dos placeholders.
     * @param string $tipoRetorno 'all' para varios registros, 'single'/'first' para um.
     * @return array Lista de registros (ou registro unico quando 'single').
     */
    public function select(string $sql, array $dados = [], string $tipoRetorno = 'all'): array
    {
        $stmt = $this->conecta()->prepare($sql);
        $stmt->execute($dados);

        if ($tipoRetorno === 'single' || $tipoRetorno === 'first') {
            $registro = $stmt->fetch();
            return $registro === false ? [] : $registro;
        }

        return $stmt->fetchAll();
    }

    /**
     * Executa um INSERT parametrizado.
     *
     * @return int Id do ultimo registro inserido (lastInsertId).
     */
    public function insert(string $sql, array $dados = []): int
    {
        $stmt = $this->conecta()->prepare($sql);
        $stmt->execute($dados);

        return (int) $this->conecta()->lastInsertId();
    }

    /**
     * Executa um UPDATE parametrizado.
     *
     * @return int Numero de linhas afetadas (rowCount).
     */
    public function update(string $sql, array $dados = []): int
    {
        $stmt = $this->conecta()->prepare($sql);
        $stmt->execute($dados);

        return $stmt->rowCount();
    }

    /**
     * Executa um DELETE parametrizado.
     *
     * @return int Numero de linhas afetadas (rowCount).
     */
    public function delete(string $sql, array $dados = []): int
    {
        $stmt = $this->conecta()->prepare($sql);
        $stmt->execute($dados);

        return $stmt->rowCount();
    }
}
