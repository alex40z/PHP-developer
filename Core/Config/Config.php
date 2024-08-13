<?php

namespace App\Core\Config;

class Config
{
    private array $config;

    public function __construct()
    {
        $this->config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);
    }

    public function configParam(string $paramName): string
    {
        return $this->config[$paramName];
    }
}
