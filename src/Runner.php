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

    protected function extractQueryParameters($consoleArgs)
    {
        // Read parameters and convert to PHP $_GET variables
        foreach ($consoleArgs as $pair)
        {
            $arPair = explode("=", $pair);
            if (sizeof($arPair) > 1)
            {
                $_REQUEST[$arPair[0]] = $arPair[1];
                $_GET[$arPair[0]] = $arPair[1];
            }
        }
        $_SERVER['QUERY_STRING'] = implode('&', $consoleArgs);
        $_SERVER['REQUEST_URI'] = 'daemon.php';
    }

    protected function prepareLogs()
    {
        if (!file_exists(self::BASE_LOG_PATH)) {
            mkdir(self::BASE_LOG_PATH);
        }

        $logname = str_replace("\\", "_", strtolower($this->className)) . "." . strtolower($this->methodName);

        fclose(STDIN);
        fclose(STDOUT);
        fclose(STDERR);
        $this->stdIn = fopen('/dev/null', 'r');
        $this->stdOut = fopen(self::BASE_LOG_PATH . '/' . $logname . '.log', 'ab');
        $this->stdErr = fopen(self::BASE_LOG_PATH . '/' . $logname . '.error.log', 'ab');
    }

    public function __construct($object, $consoleArgs = [], $daemon = true)
    {
        $this->daemon = $daemon;

        $arr = explode("::", $object);
        $className = $this->className = $arr[0];
        $this->methodName = $arr[1];

        // Prepare environment
        if ($this->daemon) {
            $this->prepareLogs();
        }
        $this->extractQueryParameters($consoleArgs);

        // Instantiate the class
        $this->instance = new $className();
    }

    public function writeToStderr($msg)
    {
        fwrite($this->stdErr, $msg);
    }

    public function writeToStdout($msg)
    {
        fwrite($this->stdOut, $msg);
    }

    public function execute()
    {
        $instance = $this->instance;
        $method = $this->methodName;

        $this->writeToStdout("Service " . $this->className . "::" . $this->methodName . " started at " . date('c') . "\n");

        $continue = true;

        // Execute routine
        while ($continue)
        {
            try
            {
                $output = $instance->$method();
                if (!empty($output))
                {
                    $this->writeToStdout($output);
                }
            }
            catch (Exception $ex)
            {
                $this->writeToStderr(date('c') . ' [' . get_class($ex) . '] in ' . $ex->getFile() . ' at line ' . $ex->getLine() . ' -- ' . "\n");
                $this->writeToStderr('Message: '. $ex->getMessage() . "\n");
                $this->writeToStderr("Stack Trace:\n" . $ex->getTraceAsString());
                $this->writeToStderr("\n\n");
            }

            $continue = $this->daemon;

            if ($continue) {
                usleep(self::SLEEP_SERVICE * 1000);
            }
        }
        //while (true)
    }
}
