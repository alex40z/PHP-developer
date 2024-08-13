<?php

namespace App\Core;

use App\Core\Exceptions\ClientException;
use App\Core\Middleware\Logger;
use Throwable;

class Router
{
    public function processRequest(Request $request): Response
    {
        foreach (Routes::URLS_LIST as $url => $methodsList) {
            if ($url != $request->getUrl()) {
                continue;
            }

            foreach ($methodsList as $httpMethod => $actionName) {
                if ($httpMethod != $request->getMethod()) {
                    continue;
                }

                $classInfo = explode('::', $actionName);
                $controllerClass = 'App\\Controllers\\' . $classInfo[0] . 'Controller';
                $controllerMethod = $classInfo[1];

                try {
                    App::$security->checkAccessRights($actionName);
                    $controller = new $controllerClass();
                    return $controller->$controllerMethod($request);
                } catch (ClientException $e) {
                    return Response::setError($e->getCode(), $e->getMessage());
                } catch (Throwable $e) {
                    $logger = new Logger();
                    $logger->writeError($e->__toString());
                    return Response::setError(500, 'Ошибка сервера');
                }
            }
        }
        return Response::setError(404, 'Страница не существует');
    }
}
