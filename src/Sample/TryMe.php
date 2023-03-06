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
    }

    public function ping()
    {
        return "pong";
    }
}
