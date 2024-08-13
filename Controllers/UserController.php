<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validator\IntegerRule;
use App\Core\Validator\StringRule;
use App\Core\Validator\Validator;

class UserController
{
    public function showUser(Request $request): Response
    {
        (new Validator())->checkParamsValidity(
            $request->getParams(),
            [
                ['user_id', new IntegerRule(false, 1, 100)],
                ['page_num', new IntegerRule(false, 1, 100)]
            ],
            1, 1
        );
        $service = App::getService('User');
        return Response::setData($service->showUser($request->getParams()));
    }

    public function editUser(Request $request): Response
    {
        (new Validator())->checkParamsValidity(
            $request->getParams(),
            [
                ['nickname', new StringRule(false, 1, 100)],
                ['phone', new StringRule(false, 11, 100)]
            ],
            1, 2
        );
        $service = App::getService('User');
        $service->editUser($request->getParams());
        return Response::setOK();
    }
}
