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
        file_put_contents('/tmp/tryme.txt', date('c') . "\n", FILE_APPEND);
    }

}
