<?php

namespace ByJG\Daemon;

class Daemonize
{
    protected static $writer = null;

    public static function setWriter(?ServiceWriter $writer)
    {
        self::$writer = $writer;
    }

    public static function getWriter()
    {
        if (self::$writer == null) {
            self::$writer = new ServiceWriter();
        }

        return self::$writer;
    }

    public static function  install(
        $svcName,
        $className,
        $bootstrap,
        $curdir,
        $template,
        $description,
        $consoleArgs,
        $environment,
        $check = true
    )
    {
        $targetPathAvailable = [
            'initd' => "/etc/init.d/$svcName",
            'crond' => "/etc/cron.d/$svcName",
            'upstart' => "/etc/init/$svcName.conf",
            'systemd' => "/etc/systemd/system/$svcName.service"
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

        if (!file_exists(realpath($curdir)) && $check) {
            throw new \Exception("RootPath '" . $curdir . "' not found. Use an absolute path. e.g. /projects/example");
        }

        if (!file_exists($curdir . '/' . $bootstrap) && $check) {
            throw new \Exception("Bootstrap '$bootstrap' not found. Use a relative path from root directory. e.g. vendor/autoload.php");
        }

        $autoload = realpath(__DIR__ . "/../vendor/autoload.php");
        if (!file_exists($autoload)) {
            $autoload = realpath(__DIR__ . "/../../../autoload.php");
            if (!file_exists($autoload) && $check) {
                throw new \Exception('Daemonize autoload not found. Did you run `composer dump-autload`?');
            }
        }

        $consoleArgsPrepared = '';
        if (!empty($consoleArgs)) {
            $consoleArgsPrepared = '--http-get ' . http_build_query($consoleArgs, '', ' --http-get ');
        }

        $environmentPrepared = '/etc/daemonize/' . $svcName . '.env';

        $serviceTemplatePath = __DIR__ . "/../template/_service.php.tpl";
        $daemonizeService = realpath(__DIR__ . "/../scripts/daemonize");

        $vars = [
            '#DESCRIPTION#' => $description,
            '#DAEMONBOOTSTRAP#' => $autoload,
            '#CLASS#' => str_replace("\\", "\\\\", $className),
            '#BOOTSTRAP#' => $bootstrap,
            '#SVCNAME#' => $svcName,
            '#ROOTPATH#' => realpath($curdir),
            '#CONSOLEARGS#' => $consoleArgsPrepared,
            '#ENVIRONMENT#' => $environmentPrepared,
            '#PHPPATH#' => PHP_BINARY,
            '#SERVICETEMPLATEPATH#' => $serviceTemplatePath,
            '#DAEMONIZESERVICE#' => $daemonizeService,
            "#ENVCMDLINE#" => implode(
                ' ',
                array_map(
                    function ($v, $k) {
                        return "$k=\"$v\"";
                    },
                    $environment,
                    array_keys($environment)
                )
            )
        ];

        $templateStr = Daemonize::replaceVars($vars, file_get_contents($templatePath));

        // Check if is OK
        if ($check) {
            require_once($vars['#BOOTSTRAP#']);
            $classParts = explode('::', str_replace("\\\\", "\\", $vars['#CLASS#']));
            if (!class_exists($classParts[0])) {
                throw new \Exception('Could not find class ' . $classParts[0]);
            }
            $className = $classParts[0];
            $classTest = new $className();
            if (!method_exists($classTest, $classParts[1])) {
                throw new \Exception('Could not find method ' . $vars['#CLASS#']);
            }
        }

        Daemonize::getWriter()->writeEnvironment($environmentPrepared, $environment);
        Daemonize::getWriter()->writeService($targetServicePath, $templateStr, $template == 'initd' ? 0755 : null);

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
            "/etc/systemd/system/$svcName.service",
            '/etc/daemonize/' . $svcName . '.env',
            '/etc/cron.d/' . $svcName,
        ];

        $found = false;
        foreach ($list as $service) {
            if (file_exists($service)) {
                $found = true;
                if (strpos($service, ".env") === false && !self::isDaemonizeService($service)) {
                    throw new DaemonizeException("Service '$svcName' was not created by PHP Daemonize");
                }
                unlink($service);
            }
        }

        if (!$found) {
            throw new DaemonizeException("Service '$svcName' does not exists");
        }

        restore_error_handler();
    }

    protected static function isDaemonizeService($filename)
    {
        set_error_handler(function ($number, $error) {
            throw new \Exception($error);
        });

        if (!file_exists($filename)) {
            return false;
        }
        $contents = file_get_contents($filename);

        return (strpos($contents, 'PHP_DAEMONIZE') !== false);
    }

    public static function listServices()
    {
        $list = [
            "initd" => glob("/etc/init.d/*"),
            "crond" => glob("/etc/cron.d/*"),
            "upstart" => glob("/etc/init/*.conf"),
            "systemd" => glob("/etc/systemd/system/*.service")
        ];
        $return = [];

        foreach ($list as $svcType => $filenames) {
            foreach ($filenames as $filename) {
                if (self::isDaemonizeService($filename)) {
                    $return[] = $svcType . ": " . 
                        str_replace(
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
        }

        return $return;
    }

}
