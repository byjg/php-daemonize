<?php

namespace ByJG\Daemon;

class Caller
{
    public function call(string $endpoint, string $controller, string $queryString = ""): void
    {
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['SERVER_PORT'] = '80';
        $_SERVER['REQUEST_SCHEME'] = 'http';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = $endpoint;
        $_SERVER['QUERY_STRING'] = $queryString;
        parse_str($_SERVER['QUERY_STRING'], $_GET);
        parse_str($_SERVER['QUERY_STRING'], $_REQUEST);

        require_once $controller;
    }
}