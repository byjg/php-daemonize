<?php

namespace ByJG\Daemon\Console;

use ByJG\Daemon\Daemonize;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ServicesCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('services')
            ->setDescription('List all services installed by daemonize')
            ->addOption(
                'only-names',
                null,
                InputOption::VALUE_NONE,
                'List only the services names without header'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $list = Daemonize::listServices();



        if ($input->getOption('only-names')) {
            $output->writeln($list);
            return;
        }

        $output->writeln("");
        if (count($list) == 0) {
            $output->writeln("There is no daemonize services installed.");
        } else {
            $output->writeln("List of daemonize services: ");
            foreach ($list as $filename) {
                $output->writeln(" - " . basename($filename));
            }
        }
        $output->writeln("");
    }
}
