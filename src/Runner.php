<?php

namespace ByJG\Daemon;

use Exception;
use ReflectionClass;
use ReflectionException;

class Runner
{

    const SLEEP_SERVICE = 1000;

    const BASE_LOG_PATH = "/var/log/daemonize";

    protected $stdIn = STDIN;
    protected $stdOut = STDOUT;
    protected $stdErr = STDERR;

    protected ?string $className = null;
    protected ?string $methodName = null;
    protected mixed $instance = null;

    protected bool $daemon = true;

    protected array $consoleArgs = [];

    public function __construct(string $object, array $consoleArgs = [], bool $daemon = true)
    {
        $this->daemon = $daemon;

        $arr = explode("::", $object);
        $className = $this->className = $arr[0];
        $this->methodName = $arr[1];

        // Prepare environment
        $this->consoleArgs = $consoleArgs;

        // Instantiate the class
        if (!class_exists($className)) {
            throw new \Exception("Could not found the class $className");
        }
        $this->instance = new $className();
    }

    public function execute(): void
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

    /**
     * @throws ReflectionException
     */
    public function showDocs()
    {
        $reflection = new ReflectionClass($this->instance);
        $method = $reflection->getMethod($this->methodName);
        $docs = $method->getDocComment();

        $docs = preg_replace('/( *\/\*\*[\r\n]| *\*\/? *)/', '', $docs);

        // get the current script name
        $docs .= "\nUsage: \n";
        $docs .= $_SERVER['argv'][0] . " run \"" . str_replace('\\', '\\\\', $this->className . "::" . $this->methodName) . "\" ";

        foreach ($method->getParameters() as $param) {
            $delimiter = $param->isOptional() ? "[]" : "<>";
            $docs .= "--arg " . $delimiter[0] . $param->name .  $delimiter[1] . " ";
        }

        echo $docs;
    }
}
