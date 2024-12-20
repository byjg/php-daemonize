<?php

use ByJG\Daemon\Daemonize;
use \PHPUnit\Framework\TestCase;

class DaemonizeTest extends TestCase
{
    protected $serviceWriter = null;

    protected function clearTest()
    {
        $fileList = [
            '/tmp/test.service',
            '/tmp/test.env',
        ];

        foreach ($fileList as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
            $this->assertFalse(file_exists($file));
        }

        Daemonize::setWriter(null);
    }

    public function setUp(): void
    {
        $this->clearTest();
        $this->serviceWriter = new \ByJG\Daemon\ServiceWriter('/tmp');
    }

    public function tearDown(): void
    {
        $this->clearTest();
    }

    protected function read($file)
    {
        $contents = file_get_contents($file);

        $contents = str_replace(
            [
                PHP_BINARY,
                getcwd(),
            ],
            [
                'PHP_BINARY',
                'CURDIR',
            ],
            $contents
        );

        return $contents;
    }

    public function testInstallMock()
    {
        Daemonize::setWriter($this->serviceWriter);
        $result = Daemonize::install('test', 'ByJG\Daemon\Sample\TryMe::ping', 'vendor/autoload.php', __DIR__ . '/../', "systemd", 'Custom Description', [], []);
        $this->assertTrue($result);
        $this->assertEquals($this->read(__DIR__ . '/expected/test.env'), $this->read('/tmp/test.env'));
        $this->assertEquals($this->read(__DIR__ . '/expected/test.service'), $this->read('/tmp/test.service'));
    }

    public function testInstallMockWithEnv()
    {
        Daemonize::setWriter($this->serviceWriter);
        $result = Daemonize::install('test', 'ByJG\Daemon\Sample\TryMe::ping', 'vendor/autoload.php', __DIR__ . '/../', "systemd", 'Custom Description', ["a" => "1", "b" => 2], ['APP_ENV' => 'test', 'TEST' => 'true']);
        $this->assertTrue($result);
        $this->assertEquals($this->read(__DIR__ . '/expected/test-with-env.env'), $this->read('/tmp/test.env'));
        $this->assertEquals($this->read(__DIR__ . '/expected/test-with-env.service'), $this->read('/tmp/test.service'));
    }

    /**
     * This test will fail if you don't have root permission
     */
    public function testCommandLine()
    {
        if (posix_getuid() !== 0) {
            $this->markTestSkipped('This test will fail if you don\'t have root permission');
        }

        /** @psalm-suppress ForbiddenCode */
        $psResult = shell_exec("ps -p 1 -o comm=");
        if (empty($psResult) || trim($psResult) !== "systemd") {
            $this->markTestSkipped('This test will fail if you don\'t have systemd');
        };

        $command = __DIR__ . "/../scripts/daemonize install " .
            "--template systemd " . 
            "--description 'Custom Description' " .
            "--class 'ByJG\Daemon\Sample\TryMe::ping' " .
            "--bootstrap 'vendor/autoload.php' " .
            "--rootdir '" . __DIR__ . "/../' " .
            "--env 'APP_ENV=test' " .
            "--env 'TEST=true' " .
            "--args '1' " .
            "--args '2' " .
            "test";

        /** @psalm-suppress ForbiddenCode */
        shell_exec($command);

        $this->assertEquals($this->read(__DIR__ . '/expected/test-with-env.env'), $this->read('/etc/daemonize/test.env'));
        $this->assertEquals($this->read(__DIR__ . '/expected/test-with-env.service'), $this->read('/etc/systemd/system/test.service'));

        $services = Daemonize::listServices();
        $this->assertEquals(["test"], $services);

        Daemonize::uninstall('test');

        $services = Daemonize::listServices();
        $this->assertEquals([], $services);
    }


    /**
     * This test will fail if you don't have root permission
     */
    public function testInstall()
    {
        if (posix_getuid() !== 0) {
            $this->markTestSkipped('This test will fail if you don\'t have root permission');
        }

        /** @psalm-suppress ForbiddenCode */
        $psResult = shell_exec("ps -p 1 -o comm=");
        if (empty($psResult) || trim($psResult) !== "systemd") {
            $this->markTestSkipped('This test will fail if you don\'t have systemd');
        };

        Daemonize::install('test', 'ByJG\Daemon\Sample\TryMe::ping', 'vendor/autoload.php', __DIR__ . '/../', "systemd", 'Custom Description', ["1", 2], ['APP_ENV' => 'test', 'TEST' => 'true']);

        $this->assertEquals($this->read(__DIR__ . '/expected/test-with-env.env'), $this->read('/etc/daemonize/test.env'));
        $this->assertEquals($this->read(__DIR__ . '/expected/test-with-env.service'), $this->read('/etc/systemd/system/test.service'));

        $services = Daemonize::listServices();
        $this->assertEquals(["test"], $services);

        Daemonize::uninstall('test');

        $services = Daemonize::listServices();
        $this->assertEquals([], $services);
    }

}