<?php


namespace Mleczek\CBuilder\Console\Commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Init extends Command
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('init')
            ->setDescription('Create package file interactively.');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // TODO: ...
    }
}