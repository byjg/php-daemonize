<?php

namespace ByJG\Daemon\Console;

use ByJG\Daemon\Runner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('run')
            ->setDescription('Run a PHP class without become a daemon')
            ->addArgument(
                'classname',
                InputArgument::REQUIRED,
                'The PHP class and method like ClassName::Method'
            )
            ->addOption(
                'bootstrap',
                'b',
                InputOption::VALUE_OPTIONAL,
                'The relative path from root directory for the bootstrap file, like ./vendor/autoload.php',
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
                '--arg',
                "a",
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'is an optional arguments for your class',
                []
            )
            ->addOption(
                'daemon',
                'd',
                InputOption::VALUE_NONE,
                'Run as a daemon'
            )
            ->addOption(
                'showdocs',
                's',
                InputOption::VALUE_NONE,
                'Show docs and exit'
            );

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $className = $input->getArgument('classname');
        $rootPath = $input->getOption('rootdir');
        $bootstrap = $rootPath . "/" . $input->getOption('bootstrap');

        if (!file_exists($rootPath)) {
            throw new \Exception("The rootpath '$bootstrap' does not exists. Use absolute path or relative path from current directory.");
        }

        if (!file_exists($bootstrap)) {
            throw new \Exception("The bootstrap file '$bootstrap' does not exists. Use a relative path from the root path.");
        }

        chdir($rootPath);
        require_once $bootstrap;

        $runner = new Runner($className, $input->getOption("arg"), $input->getOption('daemon'));

        if ($input->getOption('showdocs')) {
            $runner->showDocs();
        } else {
            $runner->execute();
        }

        return 0;
    }
}
