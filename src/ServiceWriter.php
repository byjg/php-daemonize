<?php

namespace ByJG\Daemon;

class ServiceWriter
{
    protected $overridePath = null;

    public function __construct($overridePath = null)
    {
        $this->overridePath = $overridePath;
    }

    protected function writeFile($path, $contents)
    {
        if (!is_null($this->overridePath)) {
            $path = $this->overridePath . '/' . basename($path);
        }

        set_error_handler(function ($number, $error) {
            throw new DaemonizeException($error);
        });
        if (is_null($this->overridePath)) {
            if (!file_exists('/etc/daemonize')) {
                mkdir('/etc/daemonize', 0755, true);
            }
        }
        file_put_contents($path, $contents);
        restore_error_handler();
    }

    public function writeService($path, $contents, $chmod = null)
    {
        $this->writeFile($path, $contents);

        if (!is_null($chmod)) {
            chmod($path, $chmod);
        }
    }

    public function writeEnvironment($path, $environment)
    {
        $contents = "";
        if (!empty($environment)) {
            $contents = "export " . http_build_query($environment, "", "\nexport ");
        }
        $this->writeFile($path, $contents);
    }
}