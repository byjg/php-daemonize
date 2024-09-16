<?php

namespace ByJG\Daemon;

class ServiceWriter
{
    protected ?string $overridePath = null;

    public function __construct(?string $overridePath = null)
    {
        $this->overridePath = $overridePath;
    }

    /**
     * @param string $path
     * @param string $contents
     * @return void
     * @throws DaemonizeException
     */
    protected function writeFile(string $path, string $contents): void
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

    /**
     * @param string $path
     * @param string $contents
     * @param int|null $chmod
     * @throws DaemonizeException
     */
    public function writeService(string $path, string $contents, int $chmod = null): void
    {
        $this->writeFile($path, $contents);

        if (!is_null($chmod)) {
            chmod($path, $chmod);
        }
    }

    /**
     * @param string $path
     * @param array $environment
     * @throws DaemonizeException
     */
    public function writeEnvironment(string $path, array $environment): void
    {
        $contents = "";
        if (!empty($environment)) {
            $contents = "export " . http_build_query($environment, "", "\nexport ");
        }
        $this->writeFile($path, $contents);
    }
}