<?php

namespace App\Core\Middleware;

class Logger

{
    private function writeLog(string $logLevel, string $logMessage): void
    {
        file_put_contents(__DIR__ . '/../../error.log', $logLevel . ' - ' . date('Y-m-d H:i:s')
            . ' - ' . $logMessage . PHP_EOL . PHP_EOL, FILE_APPEND);
    }

    public function writeInfo(string $logMessage): void
    {
        $this->writeLog('INFO', $logMessage);
    }

    public function writeWarning(string $logMessage): void
    {
        $this->writeLog('WARNING', $logMessage);
    }

    public function writeError(string $logMessage): void
    {
        $this->writeLog('ERROR', $logMessage);
    }

    public function writeAlert(string $logMessage): void
    {
        $this->writeLog('ALERT', $logMessage);
    }

    public function writeDebug(string $logMessage)
    {
        $this->writeLog('DEBUG', $logMessage);
    }
}

