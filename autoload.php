<?php

function autoloaderFunction(string $class): void
{
    $prefix = 'App\\';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) != 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = __DIR__ . '/' . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
}

spl_autoload_register('autoloaderFunction');

include_once 'vendor/autoload.php';
