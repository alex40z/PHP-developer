<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validator\Validator;

class MigrationController
{
    public function startMigration(Request $request): Response
    {
        (new Validator())->checkParamsValidity($request->getParams(), []);
        $service = App::getService('Migration');
        $service->startMigration();
        return Response::setOK();
    }
}