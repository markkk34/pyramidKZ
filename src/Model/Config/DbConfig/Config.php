<?php

namespace App\Model\Config\DbConfig;

use App\Model\Config\Reader;
use Exception;

class Config implements ConfigInterface
{
    const PATH = '../db.json';

    /**
     * @var string
     */
    protected string $path;

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @param string $path
     * @throws Exception
     */
    public function __construct(string $path = self::PATH)
    {
        $this->path = $path;
        $this->config = $this->getConfig();
    }

    /**
     * Check for .json and extraction the data
     * @return array
     * @throws Exception
     */
    protected function getConfig(): array
    {
        $reader = new Reader();
        return $reader->readJSON(self::PATH);
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->config[self::HOST] ?? '';
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->config[self::PORT] ?? 0;
    }

    /**
     * @return string
     */
    public function getDbname(): string
    {
        return $this->config[self::DBNAME] ?? '';
    }

    /**
     * @return string
     */
    public function getCharset(): string
    {
        return $this->config[self::CHARSET] ?? '';
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->config[self::USERNAME] ?? '';
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->config[self::PASSWORD] ?? '';
    }
}
