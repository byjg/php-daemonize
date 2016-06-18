<?php

namespace ByJG\Daemon;

class Daemonize
{
    public static function install($svcName, $className, $bootstrap, $curdir, $template, $description, $consoleArgs)
    {
        if (!file_exists($template)) {
            throw new \Exception("Template '$template' not found");
        }

        $bootstrap = $curdir . '/' . $bootstrap;
        if (!file_exists($bootstrap)) {
            throw new \Exception("Bootstrap '$bootstrap' not found");
        }

        $autoload = realpath(__DIR__ . "/../vendor/autoload.php");
        if (!file_exists($autoload)) {
            $autoload = realpath(__DIR__ . "/../../../autoload.php");
            if (!file_exists($autoload)) {
                throw new \Exception('Daemonize autoload not found. Did you run `composer dump-autload`?');
            }
        }

        if (!empty($consoleArgs)) {
            $consoleArgsPrepared = '[ "' . implode('", "', $consoleArgs) . '" ]';
        } else {
            $consoleArgsPrepared = "[ ]";
        }

        $templateStr = str_replace('#DESCRIPTION#', $description,
            str_replace('#DAEMONBOOTSTRAP#', $autoload,
                str_replace('#CLASS#', str_replace("\\", "\\\\", $className),
                    str_replace('#BOOTSTRAP#', realpath($bootstrap),
                        str_replace('#SVCNAME#', $svcName,
                            str_replace('#ROOTPATH#', realpath($curdir),
                                str_replace('#CONSOLEARGS#', $consoleArgsPrepared,
                                    file_get_contents($template)
                                )
                            )
                        )
                    )
                )
            )
        );

        set_error_handler(function ($number, $error) {
            throw new \Exception($error);
        });
        file_put_contents("/etc/init.d/$svcName", $templateStr);
        shell_exec("chmod a+x /etc/init.d/$svcName");
        restore_error_handler();

        return true;
    }

    public static function uninstall($svcName)
    {
        $filename = "/etc/init.d/$svcName";

        if (!file_exists($filename)) {
            throw new \Exception("Service '$svcName' does not exists");
        }

        if (!self::isDaemonizeService($filename)) {
            throw new \Exception("Service '$svcName' was not created by PHP Daemonize");
        }

        unlink($filename);

        restore_error_handler();
    }

    protected static function isDaemonizeService($filename)
    {
        set_error_handler(function ($number, $error) {
            throw new \Exception($error);
        });

        $contents = file_get_contents($filename);

        return (strpos($contents, 'PHP_DAEMONIZE') !== false);
    }

    public static function listServices()
    {
        $list = glob("/etc/init.d/*");
        $return = [];

        foreach ($list as $filename) {
            if (self::isDaemonizeService($filename)) {
                $return[] = basename($filename);
            }
        }

        return $return;
    }

}
