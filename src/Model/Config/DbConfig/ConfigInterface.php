<?php

namespace App\Model\Config\DbConfig;

interface ConfigInterface
{
    const HOST     = 'host';
    const PORT     = 'port';
    const DBNAME   = 'dbname';
    const CHARSET  = 'charset';
    const USERNAME = 'username';
    const PASSWORD = 'password';

    /**
     * @return string
     */
    public function getHost(): string;

    /**
     * @return int
     */
    public function getPort(): int;

    /**
     * @return string
     */
    public function getDbname(): string;

    /**
     * @return string
     */
    public function getCharset(): string;

    /**
     * @return string
     */
    public function getUsername(): string;

    /**
     * @return string
     */
    public function getPassword(): string;
}
