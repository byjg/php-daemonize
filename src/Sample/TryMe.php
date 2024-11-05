<?php

namespace ByJG\Daemon\Sample;

/**
 * This is a sample class to test the Daemonize package
 */
class TryMe
{
    /**
     * This is a sample method to test the Daemonize package.
     * This will write the current date and time to the file /tmp/tryme.txt
     */
    public function process(): void
    {
        file_put_contents(sys_get_temp_dir() . '/tryme.txt', date('c') . "\n", FILE_APPEND);
        file_put_contents(sys_get_temp_dir() . '/tryme.txt', print_r($_REQUEST, true), FILE_APPEND);
        file_put_contents(sys_get_temp_dir() . '/tryme.txt', print_r(getenv('TEST'), true), FILE_APPEND);
    }

    /**
     * This will return a pong message with the arguments passed and write to the file /tmp/tryme_test.txt
     * @param string $arg1
     * @param string|null $arg2
     */
    public function ping(string $arg1, string $arg2 = null): void
    {
        $result = "pong - $arg1 - $arg2\n";
        echo $result;
        file_put_contents(sys_get_temp_dir() . '/tryme_test.txt', "$result", FILE_APPEND);
    }
}
