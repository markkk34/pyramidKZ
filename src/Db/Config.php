<?php
namespace App\Db;

use Exception;

class Config implements ConfigInterface
{
    const PATH = '../db.json';

    /**
     * @var string
     */
    protected $path;

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
     * @return array
     * @throws Exception
     */
    protected function getConfig() : array
    {
        if (!file_exists($this->path)) {
            throw new Exception('Specified path doesnt exist');
        }
        $content = file_get_contents($this->path);
        if (!$content) {
            throw new Exception('There is file but couldnt be read');
        }
        $config = json_decode($content, true);
        if (json_last_error() > 0) {
            throw new Exception('There was error while decoding: ' . json_last_error_msg());
        }
        return $config;
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
