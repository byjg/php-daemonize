<?php

$autoload = realpath(__DIR__ . "/../vendor/autoload.php");
if (!file_exists($autoload))
{
    $autoload = realpath(__DIR__ . "/../../../autoload.php");
    if (!file_exists($autoload))
    {
        throw new \Exception('Autoload not found. Did you run `composer dump-autload`?');
    }
}

require_once $autoload;
