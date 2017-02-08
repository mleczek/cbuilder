<?php


namespace Mleczek\CBuilder\Console\Commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Clean extends Command
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('clean')
            ->setDescription('Remove output of the build command.')
            ->addOption('module', 'm', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Clean output of the specified module.')
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