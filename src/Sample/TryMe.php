<?php

namespace ByJG\Daemon\Sample;

/**
 * Description of TryMe
 *
 * @author jg
 */
class TryMe
{
    public function process()
    {
        file_put_contents(sys_get_temp_dir() . '/tryme.txt', date('c') . "\n", FILE_APPEND);
        file_put_contents(sys_get_temp_dir() . '/tryme.txt', print_r($_REQUEST, true), FILE_APPEND);
        file_put_contents(sys_get_temp_dir() . '/tryme.txt', print_r(getenv('TEST'), true), FILE_APPEND);
    }

    public function ping($arg1, $arg2 = null)
    {
        $result = "pong - $arg1 - $arg2\n";
        echo $result;
        file_put_contents(sys_get_temp_dir() . '/tryme_test.txt', "$result", FILE_APPEND);
    }

    public function saveJson()
    {
        file_put_contents('/tmp/tryme_test.txt', $_SERVER['QUERY_STRING'] . "\n");
        file_put_contents('/tmp/tryme_test.txt', json_encode($_REQUEST) . "\n", FILE_APPEND);
        file_put_contents('/tmp/tryme_test.txt', json_encode($_REQUEST), FILE_APPEND);
    }
}
