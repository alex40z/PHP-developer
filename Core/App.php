<?php

namespace App\Core;

use App\Services\BaseService;

class App
{
    public static int $userID = 0;

    public static Security $security;

    public function __construct()
    {
        self::$security = new Security();
    }

    public function handleRequest(Request $request): Response
    {
        return (new Router())->processRequest($request);
    }

    public static function getService(string $serviceName): BaseService
    {
        $serviceClass = 'App\\Services\\' . $serviceName . 'Service';
        return new $serviceClass();
    }
}
