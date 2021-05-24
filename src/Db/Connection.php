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

    public function getConnection() : PDO
    {
        //var_dump($this->dbConfig->getConfig());
        $dsn = "mysql:host=%s;port=%s;dbname=%s;charset=%s";
        $dsn = sprintf($dsn, $this->dbConfig->getHost(), $this->dbConfig->getPort(), $this->dbConfig->getDbname(), $this->dbConfig->getCharset());
        return new PDO($dsn, $this->dbConfig->getUsername(), $this->dbConfig->getPassword());
    }
} //sprintf()