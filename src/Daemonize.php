<?php

namespace ByJG\Daemon;

class Daemonize
{
    public static function install($svcName, $className, $bootstrap, $template, $description)
    {
        if (!file_exists($template))
        {
            throw new \Exception("Template '$template' not found");
        }

        $templateStr = str_replace('#DESCRIPTION#', $description,
            str_replace('#DAEMONBOOTSTRAP#', realpath(__DIR__ . "/../vendor/autoload.php"),
                str_replace('#CLASS#', str_replace("\\", "\\\\", $className),
                    str_replace('#BOOTSTRAP#', realpath($bootstrap),
                        str_replace('#SVCNAME#', $svcName,
                            file_get_contents($template)
                        )
                    )
                )
            )
        );

		set_error_handler(function($number, $error){
			throw new \Exception($error);
		});
        file_put_contents("/etc/init.d/$svcName", $templateStr);
        shell_exec("chmod a+x /etc/init.d/$svcName");
        restore_error_handler();

        return true;
    }

    public static function uninstall($svcName)
    {
        $filename ="/etc/init.d/$svcName";

        if (!file_exists($filename))
        {
            throw new \Exception("Service '$svcName' does not exists");
        }

        if (!self::isDaemonizeService($filename))
        {
            throw new \Exception("Service '$svcName' was not created by PHP Daemonize");
        }

        unlink($filename);

        restore_error_handler();
    }

    protected static function isDaemonizeService($filename)
    {
		set_error_handler(function($number, $error){
			throw new \Exception($error);
		});

        $contents = file_get_contents($filename);

        return (strpos($contents, 'PHP_DAEMONIZE') !== false);
    }

    public static function listServices()
    {
        $list = glob("/etc/init.d/*");
        $return = [];

        foreach ($list as $filename)
        {
            if (self::isDaemonizeService($filename))
            {
                $return[] = basename($filename);
            }
        }

        return $return;
    }

}
