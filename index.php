<?php

use App\Core\App;
use App\Core\Request;

include_once 'autoload.php';

date_default_timezone_set("Europe/Moscow");
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

$app = new App();
$request = new Request();
$request->setRequestParams();
$app->handleRequest($request)->sendResponse();
