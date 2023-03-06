<?php

use ByJG\Daemon\Daemonize;
use \PHPUnit\Framework\TestCase;

class DaemonizeTest extends TestCase
{
    protected $serviceWriter = null;

    public function setUp(): void
    {
        if (file_exists('/tmp/test.service')) {
            unlink('/tmp/test.service');
        }
        $this->assertFalse(file_exists('/tmp/test.service'));

        Daemonize::setWriter(null);

        $this->serviceWriter = new \ByJG\Daemon\ServiceWriter('/tmp');
    }

    public function testInstallMock()
    {
        Daemonize::setWriter($this->serviceWriter);
        $result = Daemonize::install('test', 'ByJG\Daemon\Sample\TryMe::saveJson', 'vendor/autoload.php', __DIR__ . '/../', "systemd", 'Custom Description', []);
        $this->assertTrue($result);
    }

    public function testInstall()
    {
        try {
            $result = Daemonize::install('test', 'ByJG\Daemon\Sample\TryMe::saveJson', 'vendor/autoload.php', __DIR__ . '/../', "systemd", 'Custom Description', []);
        } catch (\ByJG\Daemon\DaemonizeException $ex) {
            $this->markAsRisky();
            return;
        }

        $services = Daemonize::listServices();
        $this->assertEquals(["test"], $services);

        $services = Daemonize::uninstall('test');

        $services = Daemonize::listServices();
        $this->assertEquals([], $services);
    }

}