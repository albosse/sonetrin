<?php

namespace sonetrin\DefaultBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use sonetrin\DefaultBundle\Controller\CronjobController;

class CronjobCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('sonetrin:cronjob:run')
            ->setDescription('Run Cronjobs')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}

?>