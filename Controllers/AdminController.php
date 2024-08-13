<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validator\ChoiseRule;
use App\Core\Validator\IntegerRule;
use App\Core\Validator\StringRule;
use App\Core\Validator\Validator;

class AdminController
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
        $service = App::getService('Admin');
        return Response::setData($service->showUser($request->getParams()));
    }

    public function addUser(Request $request): Response
    {
        (new Validator())->checkParamsValidity(
            $request->getParams(),
            [
                ['login', new StringRule(true, 1, 100)],
                ['password', new StringRule(true, 1, 100)],
                ['role', new ChoiseRule(true, ['admin', 'user'])],
                ['nickname', new StringRule(true, 1, 100)],
                ['phone', new StringRule(true, 11, 100)]
            ],
            2, 6
        );
        $service = App::getService('Admin');
        $service->addUser($request->getParams());
        return Response::setOK();
    }

    public function editUser(Request $request): Response
    {
        (new Validator())->checkParamsValidity(
            $request->getParams(),
            [
                ['user_id', new IntegerRule(true, 1, 1000)],
                ['login', new StringRule(false, 1, 100)],
                ['password', new StringRule(false, 1, 100)],
                ['role', new ChoiseRule(false, ['admin', 'user'])],
                ['nickname', new StringRule(false, 1, 100)],
                ['phone', new StringRule(false, 11, 100)]
            ],
            2, 6
        );
        $service = App::getService('Admin');
        $service->editUser($request->getParams());
        return Response::setOK();
    }

    public function deleteUser(Request $request): Response
    {
        (new Validator())->checkParamsValidity(
            $request->getParams(),
            [
                ['user_id', new IntegerRule(true, 1, 100)]
            ]
        );
        $service = App::getService('Admin');
        $service->deleteUser($request->getParams());
        return Response::setOK();
    }
}
