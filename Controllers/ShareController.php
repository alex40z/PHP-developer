<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validator\IntegerRule;
use App\Core\Validator\Validator;

class ShareController
{
    public function showShare(Request $request): Response
    {
        (new Validator())->checkParamsValidity(
            $request->getParams(),
            [
                ['file_id', new IntegerRule(true, 1, 'max_files')],
                ['page_num', new IntegerRule(true, 1, 1000)]
            ]
        );
        $service = App::getService('Share');
        return Response::setData($service->showShare($request->getParams()));
    }

    public function addShare(Request $request): Response
    {
        (new Validator())->checkParamsValidity(
            $request->getParams(),
            [
                ['file_id', new IntegerRule(true, 1, 'max_files')],
                ['member_id', new IntegerRule(true, 1, 1000)]
            ]
        );
        $service = App::getService('Share');
        $service->addShare($request->getParams());
        return Response::setOK();
    }

    public function deleteShare(Request $request): Response
    {
        (new Validator())->checkParamsValidity(
            $request->getParams(),
            [
                ['share_id', new IntegerRule(true, 1, 'max_files')],
            ]
        );
        $service = App::getService('Share');
        $service->deleteShare($request->getParams());
        return Response::setOK();
    }
}
