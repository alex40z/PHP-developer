<?php

namespace App\Services;

use App\Core\DB;
use Exception;

class MigrationService extends BaseService
{
    public function startMigration(): void
    {
        $filesList = scandir(__DIR__ . '/../Migrations');
        foreach ($filesList as $key => $value) {
            if (!in_array($value, array('.', '..'))) {
                $sql = file_get_contents(__DIR__ . '/../Migrations/' . $value);
                if (!$sql) {
                    throw new Exception('Ошибка чтения файла: ' . $value);
                }
                DB::getInstance()->executeQuery($sql);
            }
        }
    }
}
