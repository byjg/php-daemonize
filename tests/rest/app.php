<?php

use ByJG\RestServer\HttpRequestHandler;
use ByJG\RestServer\OutputProcessor\JsonOutputProcessor;
use ByJG\RestServer\Route\Route;
use ByJG\RestServer\Route\RouteList;

require_once __DIR__ . '/../../vendor/autoload.php';

$routeDefinition = new RouteList();
$routeDefinition->addRoute(Route::get("/testclosure")
    ->withOutputProcessor(JsonOutputProcessor::class)
    ->withClosure(function ($response, $request) {
        $result = [
            "result" => "OK",
            "arg" => $request->get("arg")
        ];
        $response->write($result);
        file_put_contents('/tmp/tryme_test.txt', json_encode($result, FILE_APPEND) . "\n");
    })
);

$restServer = new HttpRequestHandler();
$restServer->handle($routeDefinition);
