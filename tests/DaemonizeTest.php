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
        $result = Daemonize::install('test', 'ByJG\Daemon\Sample\TryMe::saveJson', 'vendor/autoload.php', __DIR__ . '/../', "systemd", 'Custom Description', [], []);
        $this->assertTrue($result);
        $this->assertEquals($this->read(__DIR__ . '/expected/test.env'), $this->read('/tmp/test.env'));
        $this->assertEquals($this->read(__DIR__ . '/expected/test.service'), $this->read('/tmp/test.service'));
    }

    public function testInstallMockWithEnv()
    {
        Daemonize::setWriter($this->serviceWriter);
        $result = Daemonize::install('test', 'ByJG\Daemon\Sample\TryMe::saveJson', 'vendor/autoload.php', __DIR__ . '/../', "systemd", 'Custom Description', ["a" => "1", "b" => 2], ['APP_ENV' => 'test', 'TEST' => 'true']);
        $this->assertTrue($result);
        $this->assertEquals($this->read(__DIR__ . '/expected/test-with-env.env'), $this->read('/tmp/test.env'));
        $this->assertEquals($this->read(__DIR__ . '/expected/test-with-env.service'), $this->read('/tmp/test.service'));
    }

    /**
     * This test will fail if you don't have root permission
     */
    public function testCommandLine()
    {
        $command = __DIR__ . "/../scripts/daemonize install " .
            "--template systemd " . 
            "--description 'Custom Description' " .
            "--class 'ByJG\Daemon\Sample\TryMe::saveJson' " .
            "--bootstrap 'vendor/autoload.php' " .
            "--rootdir '" . __DIR__ . "/../' " .
            "--env 'APP_ENV=test' " .
            "--env 'TEST=true' " .
            "--http-get 'a=1' " .
            "--http-get 'b=2' " .
            "test";

        shell_exec($command);


        $this->assertEquals($this->read(__DIR__ . '/expected/test-with-env.env'), $this->read('/etc/daemonize/test.env'));
        $this->assertEquals($this->read(__DIR__ . '/expected/test-with-env.service'), $this->read('/lib/systemd/system/test.service'));

        $services = Daemonize::listServices();
        $this->assertEquals(["test"], $services);

        $services = Daemonize::uninstall('test');

        $services = Daemonize::listServices();
        $this->assertEquals([], $services);
    }


    /**
     * This test will fail if you don't have root permission
     */
    public function testInstall()
    {
        $result = Daemonize::install('test', 'ByJG\Daemon\Sample\TryMe::saveJson', 'vendor/autoload.php', __DIR__ . '/../', "systemd", 'Custom Description', ["a" => "1", "b" => 2], ['APP_ENV' => 'test', 'TEST' => 'true']);

        $this->assertEquals($this->read(__DIR__ . '/expected/test-with-env.env'), $this->read('/etc/daemonize/test.env'));
        $this->assertEquals($this->read(__DIR__ . '/expected/test-with-env.service'), $this->read('/lib/systemd/system/test.service'));

        $services = Daemonize::listServices();
        $this->assertEquals(["test"], $services);

        $services = Daemonize::uninstall('test');

        $services = Daemonize::listServices();
        $this->assertEquals([], $services);
    }

}