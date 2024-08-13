<?php

namespace App\Core;

use App\Core\Config\Config;
use PDO;

class DB
{
    const PAGE_SIZE = 20;

    protected static $instance = null;
    private PDO $connection;

    public static function getInstance(): DB
    {
        if (self::$instance == null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct()
    {
        $config = new Config();
        $this->connection = new PDO(sprintf('mysql:host=%s;dbname=%s;charset=utf8',
            $config->configParam('db_host'),
            $config->configParam('db_name')),
            $config->configParam('db_login'),
            $config->configParam('db_password'));
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    private function implodeFields(array $fields, string $separator): string
    {
        return implode($separator, array_map(function($key, $value) { return "$key = "
            . ($value == 'null' || is_numeric($value) ? $value : "'$value'"); }, array_keys($fields), $fields));
    }

    private function implodeValues(array $fields, string $separator): string
    {
        return implode($separator, array_map(function($value) { return $value == 'null' || is_numeric($value) ? $value : "'$value'"; }, $fields));
    }

    public function selectRowBy(string $tableName, array $fieldsList, array $filtersList): array | bool
    {
        $query = $this->connection->query(sprintf('select %s from %s where %s limit 1', implode(',', $fieldsList),
            $tableName, $this->implodeFields($filtersList, ' and ')));
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function selectRowsBy(string $tableName, array $fieldsList, array $filtersList, int $pageNum): array | bool
    {
        $query = $this->connection->query(sprintf('select %s from %s where %s order by 1 limit %d offset %d',
            implode(',', $fieldsList), $tableName, $this->implodeFields($filtersList, ' and '),
            self::PAGE_SIZE, --$pageNum * self::PAGE_SIZE));
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function selectAllRows(string $tableName, array $fieldsList, int $pageNum): array | bool
    {
        $query = $this->connection->query(sprintf('select %s from %s order by 1 limit %d offset %d',
            implode(',', $fieldsList), $tableName, self::PAGE_SIZE, --$pageNum * self::PAGE_SIZE));
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertRow(string $tableName, array $fieldsValues): int
    {
        return $this->connection->exec(sprintf('insert into %s (%s) values (%s)', $tableName,
            implode(', ', array_keys($fieldsValues)), $this->implodeValues($fieldsValues, ', ')));
    }

    public function updateRows(string $tableName, array $fieldsValues, array $filtersList): int
    {
        return $this->connection->exec(sprintf('update %s set %s where %s', $tableName,
            $this->implodeFields($fieldsValues, ', '), $this->implodeFields($filtersList, ' and ')));
    }

    public function deleteRows(string $tableName, array $filtersList): int
    {
        return $this->connection->exec(sprintf('delete from %s where %s', $tableName,
            $this->implodeFields($filtersList, ' and ')));
    }

    public function executeQuery(string $sql): void
    {
        $this->connection->exec($sql);
    }
}
