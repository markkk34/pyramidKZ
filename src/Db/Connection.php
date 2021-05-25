<?php


namespace App\Db;

use App\Db\Config;
use PDO;

class Connection
{
    /**
     * @var Config
     */
    private $dbConfig;

    public function __construct(Config $dbConfig)
    {
        $this->dbConfig = $dbConfig;
    }

    /**
     * @return PDO
     */
    public function getConnection() : PDO
    {
        $dsn = sprintf(
            "mysql:host=%s;port=%s;dbname=%s;charset=%s",
            $this->dbConfig->getHost(),
            $this->dbConfig->getPort(),
            $this->dbConfig->getDbname(),
            $this->dbConfig->getCharset()
        );
        var_dump($dsn);
        return new PDO(
            $dsn,
            $this->dbConfig->getUsername(),
            $this->dbConfig->getPassword()
        );
    }
}
