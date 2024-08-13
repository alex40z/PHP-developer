<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validator\ChoiseRule;
use App\Core\Validator\IntegerRule;
use App\Core\Validator\StringRule;
use App\Core\Validator\Validator;
use App\Services\DirectoryService;

class DirectoryController
{
    public function showDir(Request $request): Response
    {
        if (is_numeric($request->getParams()['directory_id'])) {
            (new Validator())->checkParamsValidity(
                $request->getParams(),
                [
                    ['directory_id', new IntegerRule(true, 1, 'max_files')],
                    ['page_num', new IntegerRule(true, 1, 1000)]
                ]
            );
        } else {
            (new Validator())->checkParamsValidity(
                $request->getParams(),
                [
                    ['directory_id', new ChoiseRule(true, [DirectoryService::SHARED_TO_ME, DirectoryService::SHARED_FROM_ME])],
                    ['page_num', new IntegerRule(true, 1, 1000)]
                ]
            );
        }
        $service = App::getService('Directory');
        return Response::setData($service->showDir($request->getParams()));
    }

    public function addDir(Request $request): Response
    {
        (new Validator())->checkParamsValidity(
            $request->getParams(),
            [
                ['parent_id', new IntegerRule(true, 1, 'max_files')],
                ['directory_name', new StringRule(true, 1, 255)]
            ]
        );
        $service = App::getService('Directory');
        $service->addDir($request->getParams());
        return Response::setOK();
    }

    public function editDir(Request $request): Response
    {
        (new Validator())->checkParamsValidity(
            $request->getParams(),
            [
                ['directory_id', new IntegerRule(true, 1, 'max_files')],
                ['directory_name', new StringRule(true, 1, 255)]
            ]
        );
        $service = App::getService('Directory');
        $service->editDir($request->getParams());
        return Response::setOK();
    }

    public function deleteDir(Request $request): Response
    {
        (new Validator())->checkParamsValidity(
            $request->getParams(),
            [
                ['directory_id', new IntegerRule(true, 1, 'max_files')]
            ]
        );
        $service = App::getService('Directory');
        $service->deleteDir($request->getParams());
        return Response::setOK();
    }
}
