<?php

namespace ByJG\Daemon\Console;

use ByJG\Daemon\Runner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('run')
            ->setDescription('Run a PHP class without become a daemon')
            ->addArgument(
                'classname',
                InputArgument::REQUIRED,
                'The PHP class and method like ClassName::Method'
            )
            ->addArgument(
                'bootstrap',
                InputArgument::OPTIONAL,
                'The relative path from root directory for the bootstrap file, like vendor/autoload.php',
                'vendor/autoload.php'
            )
            ->addArgument(
                'rootdir',
                InputArgument::OPTIONAL,
                'The root path where your application is installed',
                getcwd()
            )
            ->addArgument(
                'args',
                InputArgument::IS_ARRAY,
                'is an optional arguments for your class'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $className = $input->getArgument('classname');
        $bootstrap = $input->getArgument('bootstrap');
        $rootPath = $input->getArgument('rootdir');

        $realPathBootstrap = realpath($rootPath . $bootstrap);
        $realPathRootPath = realpath($rootPath);

        if (!file_exists($realPathRootPath)) {
            throw new \Exception("The rootpath '$rootPath' does not exists");
        }
        chdir($realPathRootPath);

        if (!file_exists($realPathBootstrap)) {
            throw new \Exception("The bootstrap file '$bootstrap' does not exists");
        }

        require_once $realPathBootstrap;
        $runner = new Runner($className, null, $input->getArgument('args'), false);
        $runner->execute();
    }
}
