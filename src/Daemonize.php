<?php

namespace ByJG\Daemon;

class Daemonize
{
    public static function install(
        $svcName,
        $className,
        $bootstrap,
        $curdir,
        $template,
        $description,
        $consoleArgs
    )
    {
        $targetPathAvailable = [
            'initd' => "/etc/init.d/$svcName",
            'upstart' => "/etc/init/$svcName.conf",
            'systemd' => "/lib/systemd/system/$svcName.service"
        ];
        if (!isset($targetPathAvailable[$template])) {
            throw new \Exception(
                "Template $template does not exists. Available templates are: "
                . implode(',', array_keys($targetPathAvailable))
            );
        }
        $targetServicePath = $targetPathAvailable[$template];

        $templatePath = __DIR__ . "/../template/linux-" . $template . "-service.tpl";

        if (!file_exists($templatePath)) {
            throw new \Exception("Template '$templatePath' not found");
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

        $serviceTemplatePath = __DIR__ . "/../template/_service.php.tpl";
        $daemonizeService = __DIR__ . "/../services/$svcName.php";
        $phpPath = PHP_BINARY;

        $vars = [
            '#DESCRIPTION#' => $description,
            '#DAEMONBOOTSTRAP#' => $autoload,
            '#CLASS#' => str_replace("\\", "\\\\", $className),
            '#BOOTSTRAP#' => realpath($bootstrap),
            '#SVCNAME#' => $svcName,
            '#ROOTPATH#' => realpath($curdir),
            '#CONSOLEARGS#' => $consoleArgsPrepared,
            '#PHPPATH#' => $phpPath,
            '#SERVICETEMPLATEPATH#' => $serviceTemplatePath,
            '#DAEMONIZESERVICE#' => $daemonizeService,
        ];

        $templateStr = Daemonize::replaceVars($vars, file_get_contents($templatePath));

        $serviceStr = Daemonize::replaceVars($vars, file_get_contents($serviceTemplatePath));

        // Check if is OK
        require_once ($vars['#BOOTSTRAP#']);
        $classParts = explode('::', $vars['#CLASS#']);
        if (!class_exists($classParts[0])) {
            throw new \Exception('Could not find class ' . $classParts[0]);
        }
        $className = $classParts[0];
        $classTest = new $className();
        if (!method_exists($classTest, $classParts[1])) {
            throw new \Exception('Could not find method ' . $vars['#CLASS#']);
        }

        set_error_handler(function ($number, $error) {
            throw new \Exception($error);
        });
        file_put_contents($targetServicePath, $templateStr);
        shell_exec("chmod a+x $targetServicePath");
        file_put_contents($daemonizeService, $serviceStr);
        restore_error_handler();

        return true;
    }

    protected static function replaceVars($vars, $text)
    {
        foreach ($vars as $searchFor=>$replace) {
            $text = str_replace($searchFor, $replace, $text);
        }
        return $text;
    }

    public static function uninstall($svcName)
    {
        $list = [
            "/etc/init.d/$svcName",
            "/etc/init/$svcName.conf",
            "/lib/systemd/system/$svcName.service"
        ];

        $found = false;
        foreach ($list as $service) {
            if (file_exists($service)) {
                $found = true;
                if (!self::isDaemonizeService($service)) {
                    throw new \Exception("Service '$svcName' was not created by PHP Daemonize");
                }
                shell_exec("service $svcName stop");
                unlink($service);
            }
        }

        if (!$found) {
            throw new \Exception("Service '$svcName' does not exists");
        }

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
        $list1 = glob("/etc/init.d/*");
        $list2 = glob("/etc/init/*.conf");
        $list3 = glob("/lib/systemd/system/*.service");
        $list = array_merge($list1, $list2, $list3);
        $return = [];

        foreach ($list as $filename) {
            if (self::isDaemonizeService($filename)) {
                $return[] = str_replace(
                    '.service',
                    '',
                    str_replace(
                        '.conf',
                        '',
                        basename($filename)
                    )
                );
            }
        }

        return $return;
    }

}
