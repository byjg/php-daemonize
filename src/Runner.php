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

    public function __construct($object, $consoleArgs = [], $daemon = true)
    {
        $this->daemon = $daemon;

        $arr = explode("::", $object);
        $className = $this->className = $arr[0];
        $this->methodName = $arr[1];

        // Prepare environment
        $this->extractQueryParameters($consoleArgs);

        // Instantiate the class
        if (!class_exists($className)) {
            throw new \Exception("Could not found the class $className");
        }
        $this->instance = new $className();
    }

    protected function extractQueryParameters($consoleArgs)
    {
        $_SERVER['QUERY_STRING'] = http_build_query($consoleArgs);
        $_GET = $consoleArgs;
        $_REQUEST = $consoleArgs;
        $_SERVER['REQUEST_URI'] = 'daemon.php';
    }

    public function execute()
    {
        $instance = $this->instance;
        $method = $this->methodName;

        $continue = true;

        // Execute routine
        while ($continue) {
            $instance->$method();
            $continue = $this->daemon;

            if ($continue) {
                usleep(self::SLEEP_SERVICE * 1000);
            }
        }
    }
}
