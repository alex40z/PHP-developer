<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validator\StringRule;
use App\Core\Validator\Validator;

class AccessController
{
    public function loginUser(Request $request): Response
    {
        (new Validator())->checkParamsValidity(
            $request->getParams(),
            [
                ['login', new StringRule(true, 1, 100)],
                ['password', new StringRule(true, 1, 100)]
            ]
        );
        $service = App::getService('Access');
        $service->loginUser($request->getParams());
        return Response::setOK();
    }

    public function logoutUser(Request $request): Response
    {
        (new Validator())->checkParamsValidity($request->getParams(), []);
        $service = App::getService('Access');
        $service->logoutUser($request->getParams());
        return Response::setOK();
    }

    public function resetPassword(Request $request): Response
    {
        (new Validator())->checkParamsValidity(
            $request->getParams(),
            [
                [0, new StringRule(true, 1, 100)]
            ]
        );
        $service = App::getService('Access');
        $service->resetPassword($request->getParams());
        return Response::setOK();
    }

    public function updatePassword(Request $request): Response
    {
        (new Validator())->checkParamsValidity(
            $request->getParams(),
            [
                [0, new StringRule(true, 1, 100)]
            ]
        );
        $service = App::getService('Access');
        $service->updatePassword($request->getParams());
        return Response::setOK();
    }
}
