<?php

namespace ByJG\Daemon\Console;

use ByJG\Daemon\Caller;
use ByJG\Daemon\Runner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CallCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('call')
            ->setDescription('Run a PHP class without become a daemon')
            ->addArgument(
                'endppoint',
                InputArgument::REQUIRED,
                'The GET endpoint to be called'
            )
            ->addOption(
                'controller',
                'c',
                InputOption::VALUE_REQUIRED,
                'The relative path from root directory for the bootstrap file, normally your controller file',
            )
            ->addOption(
                '--http-get',
                "-g",
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'is an optional arguments for your class',
                []
            )
            ->addOption(
                'daemon',
                'd',
                InputOption::VALUE_NONE,
                'Run as a daemon'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $endpoint = $input->getArgument('endppoint');
        $controller = $input->getOption('controller');

        if (!file_exists($controller)) {
            throw new \Exception("The controller file '$controller' does not exists. Use a relative path from the root path.");
        }

        $caller = new Caller();
        $caller->call($endpoint, $controller, implode('&', $input->getOption('http-get')));

        return 0;
    }
}
