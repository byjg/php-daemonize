<?php

namespace ByJG\Daemon;

class ServiceWriter
{
    protected $overridePath = null;

    public function __construct($overridePath = null)
    {
        $this->overridePath = $overridePath;
    }

    public function writeService($path, $contents, $chmod = null)
    {
        if (!is_null($this->overridePath)) {
            $path = $this->overridePath . '/' . basename($path);
        }

        set_error_handler(function ($number, $error) {
            throw new DaemonizeException($error);
        });
        file_put_contents($path, $contents);
        if (!is_null($chmod)) {
            chmod($path, $chmod);
        }
        restore_error_handler();
    }
}