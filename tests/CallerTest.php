<?php

use PHPUnit\Framework\TestCase;

class CallerTest extends TestCase
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

    public function testCli()
    {
        /** @psalm-suppress ForbiddenCode */
        shell_exec( __DIR__ . '/../scripts/daemonize call /testclosure --http-get arg=10 --controller ' . __DIR__ . '/rest/app.php');

        $this->assertTrue(file_exists('/tmp/tryme_test.txt'));
        $this->assertEquals("{\"result\":\"OK\",\"arg\":\"10\"}\n", file_get_contents('/tmp/tryme_test.txt'));
    }

    public function testCliNoArgs()
    {
        /** @psalm-suppress ForbiddenCode */
        shell_exec( __DIR__ . '/../scripts/daemonize call /testclosure --controller ' . __DIR__ . '/rest/app.php');

        $this->assertTrue(file_exists('/tmp/tryme_test.txt'));
        $this->assertEquals("{\"result\":\"OK\",\"arg\":null}\n", file_get_contents('/tmp/tryme_test.txt'));
    }

}