<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validator\IntegerRule;
use App\Core\Validator\StringRule;
use App\Core\Validator\Validator;

class FileController
{
    public function downloadFile(Request $request): Response
    {
        (new Validator())->checkParamsValidity(
            $request->getParams(),
            [
                ['file_id', new IntegerRule(true, 1, 'max_files')]
            ]
        );
        $service = App::getService('File');
        $service->downloadFile($request->getParams());
        return Response::setOK();
    }

    public function uploadFile(Request $request): Response
    {
        (new Validator())->checkParamsValidity(
            $request->getParams(),
            [
                ['directory_id', new IntegerRule(true, 1, 'max_files')]
            ]
        );
        $service = App::getService('File');
        $service->uploadFile($request->getParams());
        return Response::setOK();
    }

    public function editFile(Request $request): Response
    {
        (new Validator())->checkParamsValidity(
            $request->getParams(),
            [
                ['file_id', new IntegerRule(true, 1, 'max_files')],
                ['file_name', new StringRule(true, 1, 255)]
            ]
        );
        $service = App::getService('File');
        $service->editFile($request->getParams());
        return Response::setOK();
    }

    public function deleteFile(Request $request): Response
    {
        (new Validator())->checkParamsValidity(
            $request->getParams(),
            [
                ['file_id', new IntegerRule(true, 1, 'max_files')]
            ]
        );
        $service = App::getService('File');
        $service->deleteFile($request->getParams());
        return Response::setOK();
    }
}
