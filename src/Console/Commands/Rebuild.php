<?php


namespace Mleczek\CBuilder\Console\Commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Rebuild extends Command
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('rebuild')
            ->setDescription('Clean and build package (excluding dependencies).')
            ->addOption('module', 'm', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Rebuild specified module.')
            ->addOption('nested', null, InputOption::VALUE_NONE, 'Perform operation also for all package dependencies.');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // TODO: ...
    }
}