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

    public function testExecuteArgsWithoutRequired()
    {
        $this->expectException(ArgumentCountError::class);
        $runner = new \ByJG\Daemon\Runner(
            'ByJG\Daemon\Sample\TryMe::ping',
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