<?php

use \PHPUnit\Framework\TestCase;

class RunnerTest extends TestCase
{
    protected function clearTest()
    {
        if (file_exists('/tmp/tryme_test.txt')) {
            unlink('/tmp/tryme_test.txt');
        }
        $this->assertFalse(file_exists('/tmp/tryme_test.txt'));
    }

    public function setUp(): void
    {
        $this->clearTest();
    }

    public function tearDown(): void
    {
        $this->clearTest();
    }

    public function testExecute()
    {
        $runner = new \ByJG\Daemon\Runner(
            'ByJG\Daemon\Sample\TryMe::saveJson',
            [],
            [],
            false
        );
        $runner->execute();

        $this->assertTrue(file_exists('/tmp/tryme_test.txt'));
        $this->assertEquals("\n[]\n[]", file_get_contents('/tmp/tryme_test.txt'));
    }

    public function testExecuteHttpGet()
    {
        $runner = new \ByJG\Daemon\Runner(
            'ByJG\Daemon\Sample\TryMe::saveJson',
            [],
            [ "a" => 1, "b" => 2 ],
            false
        );
        $runner->execute();

        $this->assertTrue(file_exists('/tmp/tryme_test.txt'));
        $this->assertEquals("a=1&b=2\n{\"a\":1,\"b\":2}\n{\"a\":1,\"b\":2}", file_get_contents('/tmp/tryme_test.txt'));
    }

    public function testExecuteHttpGetSingleParam()
    {
        $runner = new \ByJG\Daemon\Runner(
            'ByJG\Daemon\Sample\TryMe::saveJson',
            [],
            [ "a", "b" ],
            false
        );
        $runner->execute();

        $this->assertTrue(file_exists('/tmp/tryme_test.txt'));
        $this->assertEquals("0=a&1=b\n[\"a\",\"b\"]\n[\"a\",\"b\"]", file_get_contents('/tmp/tryme_test.txt'));
    }

    public function testCli()
    {
        /** @psalm-suppress ForbiddenCode */
        shell_exec( __DIR__ . '/../scripts/daemonize run \\\ByJG\\\Daemon\\\Sample\\\TryMe::saveJson --http-get a=2 --http-get b=3 --rootdir ' . __DIR__ . '/..');
        $this->assertEquals("a=2&b=3\n{\"a\":\"2\",\"b\":\"3\"}\n{\"a\":\"2\",\"b\":\"3\"}", file_get_contents('/tmp/tryme_test.txt'));
    }

    public function testCliSingleParam()
    {
        /** @psalm-suppress ForbiddenCode */
        shell_exec( __DIR__ . '/../scripts/daemonize run \\\ByJG\\\Daemon\\\Sample\\\TryMe::saveJson --http-get a --http-get b --rootdir ' . __DIR__ . '/..');
        $this->assertEquals("a=&b=\n{\"a\":\"\",\"b\":\"\"}\n{\"a\":\"\",\"b\":\"\"}", file_get_contents('/tmp/tryme_test.txt'));
    }


    public function testExecuteArgsWithoutRequired()
    {
        $this->expectException(ArgumentCountError::class);
        $runner = new \ByJG\Daemon\Runner(
            'ByJG\Daemon\Sample\TryMe::ping',
            [],
            [],
            false
        );
        $runner->execute();
    }

    public function testExecuteArgs()
    {
        $runner = new \ByJG\Daemon\Runner(
            'ByJG\Daemon\Sample\TryMe::ping',
            ["first"],
            [],
            false
        );
        $runner->execute();

        $this->assertTrue(file_exists('/tmp/tryme_test.txt'));
        $this->assertEquals("pong - first - \n", file_get_contents('/tmp/tryme_test.txt'));
    }


    public function testExecuteArgs2()
    {
        $runner = new \ByJG\Daemon\Runner(
            'ByJG\Daemon\Sample\TryMe::ping',
            ["first", "second"],
            [],
            false
        );
        $runner->execute();

        $this->assertTrue(file_exists('/tmp/tryme_test.txt'));
        $this->assertEquals("pong - first - second\n", file_get_contents('/tmp/tryme_test.txt'));
    }

    public function testCliArg()
    {
        /** @psalm-suppress ForbiddenCode */
        shell_exec( __DIR__ . '/../scripts/daemonize run \\\ByJG\\\Daemon\\\Sample\\\TryMe::ping --arg 1 --arg 2 --rootdir ' . __DIR__ . '/..');
        $this->assertEquals("pong - 1 - 2\n", file_get_contents('/tmp/tryme_test.txt'));
    }

}