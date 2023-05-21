<?php

namespace ByJG\Daemon\Console;

use ByJG\Daemon\Daemonize;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UninstallCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('uninstall')
            ->setDescription('Uninstall the Linux Daemon previously installed by daemonize')
            ->addArgument(
                'servicename',
                InputArgument::REQUIRED,
                'The unix service name.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $serviceName = $input->getArgument('servicename');
        Daemonize::uninstall($serviceName);
        $output->writeln('Service uninstalled. Maybe the service still running. ');

        return 0;
    }
}
