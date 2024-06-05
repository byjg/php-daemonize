<?php

namespace ByJG\Daemon;

use Exception;

class Runner
{

    const SLEEP_SERVICE = 1000;

    const BASE_LOG_PATH = "/var/log/daemonize";

    protected $stdIn = STDIN;
    protected $stdOut = STDOUT;
    protected $stdErr = STDERR;

    protected $className = null;
    protected $methodName = null;
    protected $instance = null;

    protected $daemon = true;

    protected $consoleArgs = [];

    public function __construct($object, $consoleArgs = [], $httpGet = [], $daemon = true)
    {
        $this->daemon = $daemon;

        $arr = explode("::", $object);
        $className = $this->className = $arr[0];
        $this->methodName = $arr[1];

        // Prepare environment
        $this->extractQueryParameters($httpGet);
        $this->consoleArgs = $consoleArgs;

        // Instantiate the class
        if (!class_exists($className)) {
            throw new \Exception("Could not found the class $className");
        }
        $this->instance = new $className();
    }

    protected function extractQueryParameters($httpGet)
    {
        $_SERVER['QUERY_STRING'] = http_build_query($httpGet);
        $_GET = $httpGet;
        $_REQUEST = $httpGet;
        $_SERVER['REQUEST_URI'] = 'daemon.php';
    }

    public function execute()
    {
        $instance = $this->instance;
        $method = $this->methodName;

        $continue = true;

        // Execute routine
        while ($continue) {
            call_user_func_array([$instance, $method], $this->consoleArgs);
            $continue = $this->daemon;

            if ($continue) {
                usleep(self::SLEEP_SERVICE * 1000);
            }
        }
    }
}
