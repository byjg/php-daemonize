<?php

namespace ByJG\Daemon\Console;

use ByJG\Daemon\Daemonize;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('install')
            ->setDescription('Install a PHP Class as Linux Daemon')
            ->addOption(
                'template',
                't',
                InputOption::VALUE_REQUIRED,
                'Defines the default service template -- initd, upstart or systemd',
                'systemd'
            )
            ->addOption(
                'no-check',
                'nc',
                InputOption::VALUE_NONE,
                'Install without check for errors'
            )
            ->addArgument(
                'service',
                InputArgument::REQUIRED,
                'The unix service name.'
            )
            ->addOption(
                'class',
                'c',
                InputOption::VALUE_REQUIRED,
                'The PHP class and method like \\Namespace\\ClassName::Method'
            )
            ->addOption(
                'bootstrap',
                'b',
                InputOption::VALUE_OPTIONAL,
                'The relative path from root directory, like vendor/autoload.php',
                'vendor/autoload.php'
            )
            ->addOption(
                'rootdir',
                'r',
                InputOption::VALUE_OPTIONAL,
                'The root path where your application is installed',
                getcwd()
            )
            ->addOption(
                'description',
                'd',
                InputOption::VALUE_OPTIONAL,
                'is an optional service description',
                "Daemon generated by Daemonize"
            )
            ->addOption(
                'http-get',
                'g',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'is an optional arguments for your class'
            )
            ->addOption(
                'env',
                'e',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'Set environment variables for the service'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parse_str(implode("&", $input->getOption('http-get')), $httpGet);
        parse_str(implode("&", $input->getOption('env')), $env);

        Daemonize::install(
            $input->getArgument('service'),
            $input->getOption('class'),
            $input->getOption('bootstrap'),
            $input->getOption('rootdir'),
            $input->getOption('template'),
            $input->getOption('description'),
            $httpGet,
            $env,
            !$input->getOption('no-check')
        );

        return 0;
    }
}
